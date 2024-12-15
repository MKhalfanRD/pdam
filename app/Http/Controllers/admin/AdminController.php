<?php

namespace App\Http\Controllers\admin;

use App\Exports\Pemakaian_AirExport;
use App\Http\Controllers\Controller;
use App\Models\Pemakaian_Air;
use App\Models\Pembayaran;
use App\Models\User;
use App\Models\Validasi_Pembayaran;
use App\Models\Warga;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $role = Auth::user()->role;

        // Sorting parameters
        $sort = $request->get('sort', 'status'); // Default sort by 'status'
        // $sort = $request->get('sort', 'pemakaian_air.bulan'); // Default sort by 'bulan'
        $direction = $request->get('direction', 'asc'); // Default direction 'asc'
        $search = $request->get('search', '');

        // Default filter adalah tanggal hari ini
        $tanggalHariIni = Carbon::today();
        $bulan = $request->input('bulan', $tanggalHariIni->month); // Default bulan sekarang
        $tahun = $request->input('tahun', $tanggalHariIni->year); // Default tahun sekarang
        $status = $request->input('status', ''); // Default status kosong (semua)

        $query = Pemakaian_Air::leftJoin('pembayaran', 'pemakaian_air.pemakaianAir_id', '=', 'pembayaran.pemakaianAir_id')
            ->leftJoin('validasi_pembayaran', 'pembayaran.pembayaran_id', '=', 'validasi_pembayaran.pembayaran_id')
            ->select(
                'pemakaian_air.*',
                'pembayaran.status as pembayaran_status',
                'pembayaran.komentar',
                'validasi_pembayaran.statusValidasi as validasi_status',
                'validasi_pembayaran.keterangan as validasi_keterangan',
                'validasi_pembayaran.validasi_id'
            )
            ->whereMonth('pemakaian_air.bulan', $bulan)
            ->whereYear('pemakaian_air.bulan', $tahun);
            // ->with('pembayaran.validasi');
            // Apply status filter if it's provided
            if ($status) {
                $query->whereHas('pembayaran', function($query) use ($status) {
                    $query->where('status', $status);
                });
            }
            $data = $query->orderBy($sort, $direction)->get();

        // $query = User::with('warga')->where('role', 'warga')->get();
        $data->map(function($item) {
            $carbonDate = Carbon::parse($item->bulan, $item->tahun);
            $item->bulan = $carbonDate->format('F'); // Nama bulan (contoh: November)
            $item->tahun = $carbonDate->format('Y'); // Tahun (contoh: 2024)
            $item->status = $item->pembayaran_status ?? 'Belum Bayar'; //jika status null
            return $item;
        });
        // dd($data);
        return view('admin.index', compact('role', 'data', 'sort', 'direction', 'search', 'bulan', 'tahun'));
    }

    public function show($warga_id)
    {
        $role = Auth::user()->role;
        $warga = Warga::findOrFail($warga_id);

        $pembayaran = Pembayaran::where('warga_id', $warga_id)->first(); // Ambil data pembayaran sesuai `warga_id`
        $pemakaianAir = Pemakaian_Air::where('warga_id', $warga_id)->first();

        if (!$pemakaianAir) {
            $pemakaianAir = new Pemakaian_Air(); // atau bisa juga set nilai default lainnya
        }

        // dd($pembayaran);
        return view('admin.show', compact(['warga', 'pemakaianAir', 'role', 'pembayaran']));
    }

    public function summary(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $data = Pemakaian_Air::with(['pembayaran', 'warga'])
            ->whereMonth('bulan', $bulan)
            ->whereYear('bulan', $tahun)
            ->get();

        // Ekspor ke Excel
        return Excel::download(new Pemakaian_AirExport($data), "Data_Pemakaian_Air_{$bulan}_{$tahun}.xlsx");
    }

    // public function verify($pembayaran_id)
    // {
    //     // Cari data pembayaran berdasarkan ID
    //     $pembayaran = Pembayaran::findOrFail($pembayaran_id);

    //     // Update status pembayaran menjadi "terverifikasi"
    //     $pembayaran->status = 'Terverifikasi';
    //     $pembayaran->save();

    //     // Redirect ke halaman sebelumnya dengan pesan sukses
    //     return redirect()->route('admin.show', $pembayaran->warga_id)->with('success', 'Pembayaran berhasil diverifikasi!');
    // }

    public function validasi(Request $request, $pembayaran_id)
    {
        $role = Auth::user()->role;

        // Validasi input
        $request->validate([
            'statusValidasi' => 'required|in:valid,invalid',
            'keterangan' => 'required|string|max:255',
        ]);

        // Format bulan dan tahun dalam format 'Y-m' (tanpa mengubah tanggal)
        $currentMonthYear = now()->format('Y-m') . '-01';

        $pembayaran = Pembayaran::findOrFail($pembayaran_id);

        Validasi_Pembayaran::updateOrCreate([
            'admin_id' => Auth::id(),
            'pembayaran_id' => $pembayaran_id,
            'statusValidasi' => $request->statusValidasi,
            'keterangan' => $request->keterangan,
            'waktuValidasi' => $currentMonthYear,
        ]);

        if($request->statusValidasi === 'valid'){
            $pembayaran->status = 'Terverifikasi';
        }
        else{
            $pembayaran->status = 'Pending';
        }

        $pembayaran->save();

        return redirect()->route('admin.index')->with('success', 'Pembayaran telah divalidasi');
    }
    public function edit($validasi_id)
    {
        $role = Auth::user()->role;
        $validasi = Validasi_Pembayaran::findOrFail($validasi_id);
        // dd($validasi_id);
        // Dapatkan data pembayaran terkait jika diperlukan di halaman edit
        // $pembayaran = $validasi->pembayaran;
        $pembayaran = Pembayaran::findOrFail($validasi->pembayaran_id);
        // $validasi = Validasi_Pembayaran::where('pembayaran_id', $pembayaran_id)->first();
        // dd($role);
        return view('admin.edit', compact('role','validasi', 'pembayaran'));
    }

    public function update(Request $request, $validasi_id)
    {
        // Temukan validasi yang relevan
        $validasi = Validasi_Pembayaran::findOrFail($validasi_id);

        // Validasi input
        $request->validate([
            'statusValidasi' => 'required|in:valid,invalid',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Periksa apakah ada perubahan data
        $hasChanges = false;
        if ($validasi->statusValidasi !== $request->statusValidasi) {
            $validasi->statusValidasi = $request->statusValidasi;
            $hasChanges = true;
        }
        if ($validasi->keterangan !== $request->keterangan) {
            $validasi->keterangan = $request->keterangan;
            $hasChanges = true;
        }
        if ($hasChanges) {
            $validasi->admin_id = Auth::id();
            $validasi->waktuValidasi = now();
            $validasi->save();

            // Update status pembayaran terkait
            $pembayaran = $validasi->pembayaran;
            $pembayaran->status = $request->statusValidasi === 'valid' ? 'Terverifikasi' : 'Pending';
            $pembayaran->save();
            
            return redirect()->route('admin.index')->with('success', 'Validasi telah diperbarui.');
        }
        return redirect()->route('admin.index')->with('info', 'Tidak ada perubahan pada data validasi.');
    }


    // public function update(Request $request, $validasi_id)
    // {
    //     // Temukan validasi yang relevan
    //     $validasi = Validasi_Pembayaran::findOrFail($validasi_id);

    //     $request->validate([
    //         'validasi_id' => 'required',
    //         'admin_id' => 'required',
    //         'pembayaran_id' => 'required',
    //         'statusValidasi' => 'required|in:valid,invalid',
    //         'keterangan' => 'required|string|max:255',
    //         'waktuValidasi' => 'required',
    //     ]);

    //     // Temukan data pembayaran dan validasi yang relevan
    //     $pembayaran = Pembayaran::findOrFail($pembayaran_id);
    //     $validasi = Validasi_Pembayaran::where('pembayaran_id', $pembayaran_id)->first();

    //     $validasi->statusValidasi = $request->statusValidasi;
    //     $validasi->keterangan = $request->keterangan;
    //     $validasi->admin_id = Auth::id();
    //     $validasi->save();

    //     // Update status pembayaran jika validasi diterima
    //     if ($request->statusValidasi == 'valid') {
    //         $pembayaran->status = 'Terverifikasi';
    //     } else {
    //         $pembayaran->status = 'Pending';
    //     }
    //     $pembayaran->save();

    //     return redirect()->route('admin.index')->with('success', 'Pembayaran telah divalidasi');
    // }

}
