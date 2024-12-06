<?php

namespace App\Http\Controllers\admin;

use App\Exports\Pemakaian_AirExport;
use App\Http\Controllers\Controller;
use App\Models\Pemakaian_Air;
use App\Models\Pembayaran;
use App\Models\User;
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

        $data = Pemakaian_Air::leftJoin('pembayaran', 'pemakaian_air.pemakaianAir_id', '=', 'pembayaran.pemakaianAir_id')
            ->select(
                'pemakaian_air.*',
                'pembayaran.status as pembayaran_status',
                'pembayaran.komentar'
            )
            ->whereMonth('pemakaian_air.bulan', $bulan)
            ->whereYear('pemakaian_air.bulan', $tahun)
            ->orderBy($sort, $direction)
            ->get();


        // // Ambil data berdasarkan bulan dan tahun yang dipilih
        // $query = Pemakaian_Air::with('pembayaran') // Eager load pembayaran
        //     ->whereMonth('bulan', $bulan)
        //     ->whereYear('bulan', $tahun);
        //     // ->get();

        // Sorting berdasarkan status atau kolom lainnya
        // if ($sort === 'status') {
        //     $query = $query->join('pembayaran', 'pemakaian_air.pemakaianAir_id', '=', 'pembayaran.pemakaianAir_id')
        //         ->orderByRaw("
        //             CASE
        //                 WHEN pembayaran.status = 'pending' THEN 1
        //                 WHEN pembayaran.status = 'terverifikasi' THEN 2
        //                 ELSE 3
        //             END $direction
        //         ")
        //         ->select('pemakaian_air.*'); // Pastikan memilih kolom dari tabel utama
        // } else {
        //     $query = $query->orderBy($sort, $direction);
        // }

        // $data = $query->get();

        // $query = User::with('warga')->where('role', 'warga')->get();
        $data->map(function($item) {
            $carbonDate = Carbon::parse($item->bulan, $item->tahun);
            $item->bulan = $carbonDate->format('F'); // Nama bulan (contoh: November)
            $item->tahun = $carbonDate->format('Y'); // Tahun (contoh: 2024)
            $item->status = $item->pembayaran_status ?? 'belum bayar'; //jika status null
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

}
