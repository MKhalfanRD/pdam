<?php

namespace App\Http\Controllers\operator;

use App\Http\Controllers\Controller;
use App\Models\Pemakaian_Air;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperatorController extends Controller
{
    public function index(Request $request){

        $role = Auth::user()->role;
        $sort = $request->get('sort', 'nama'); // default sort by 'nama'
        $direction = $request->get('direction', 'asc'); // default direction 'asc'
        $search = $request->get('search', '');

        // Query untuk pencarian dan sorting
        $warga = Warga::query();

        // Jika ada pencarian, lakukan filter berdasarkan nama atau alamat
        if (!empty($search)) {
            $warga = $warga->where('nama', 'like', '%' . $search . '%')
                           ->orWhere('alamat', 'like', '%' . $search . '%');
        }

        // Sorting berdasarkan sort dan direction yang diberikan
        $warga = $warga->orderBy($sort, $direction)->paginate(10);

        return view('operator.index', compact( 'role','warga', 'sort', 'direction', 'search'));
    }

    // public function search(Request $request){
    //     $search = $request->search;
    //     $warga = Warga::query();

    //     if ($search) {
    //         $warga->where(function($query) use ($search){
    //             $query->where('nama', 'like', "%$search%")
    //                 ->orWhere('alamat', 'like', "%$search%");
    //         });
    //     } else {
    //         return redirect()->route('operator.index');
    //     }

    //     $warga = $warga->paginate(10);

    //     return view('operator.index', compact('search', 'warga'));
    // }


    public function edit($warga_id){
        $role = 'operator';
        $warga = Warga::findOrFail($warga_id);

        $pemakaianAir = Pemakaian_Air::where('warga_id', $warga_id)->first();

        if (!$pemakaianAir) {
            $pemakaianAir = new Pemakaian_Air(); // atau bisa juga set nilai default lainnya
        }

        // $bulan = [
        //     '01' => 'January',
        //     '02' => 'February',
        //     '03' => 'March',
        //     '04' => 'April',
        //     '05' => 'May',
        //     '06' => 'June',
        //     '07' => 'July',
        //     '08' => 'August',
        //     '09' => 'September',
        //     '10' => 'October',
        //     '11' => 'November',
        //     '12' => 'December',
        // ];

        // $tahun = range(date('Y'), date('Y') - 5); // Tahun 5 tahun terakhir

        return view('operator.edit', compact(['warga', 'pemakaianAir', 'role']));
    }

    public function update(Request $request, $warga_id)
    {
        $pemakaianAir = Pemakaian_Air::where('warga_id', $warga_id)->first();

        $request->validate([
            'pemakaianBaru' => 'required|numeric',
            'pemakaianLama' => 'required|numeric',
            'fotoMeteran' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi file foto
        ]);

        $fotoPath = $pemakaianAir && $pemakaianAir->foto ? $pemakaianAir->foto : null;

        // Proses upload foto jika ada
        // $fotoPath = $pemakaianAir->foto;
        if ($request->hasFile('fotoMeteran')) {
            $file = $request->file('fotoMeteran');
            $fotoPath = $file->store('fotoMeteran', 'public'); // Simpan di storage
        }

        $currentMonthYear = now()->format('Y-m');

        // Update data di pemakaian_air
        Pemakaian_Air::updateOrCreate(
            ['warga_id' => $warga_id], // Jika sudah ada warga_id, perbarui
            [
                'operator_id' => 1,
                'bulan' => $currentMonthYear, // Format YYYY-MM
                'pemakaianLama' => $request->input('pemakaianLama'),
                'pemakaianBaru' => $request->input('pemakaianBaru'),
                'foto' => $fotoPath,
            ]
        );

        return redirect()->route('operator.index')->with('success', 'Data pemakaian berhasil diperbarui.');
    }

    public function history(Request $request) {
        $role = 'operator';
        // Ambil bulan dan tahun dari request, jika tidak ada gunakan bulan dan tahun sekarang
        $selectedBulan = $request->get('bulan', date('m'));
        $selectedTahun = $request->get('tahun', date('Y'));

        // Ambil parameter sorting
        $sort = $request->get('sort', 'pemakaianAir_id'); // Kolom default
        $direction = $request->get('direction', 'asc'); // Arah default: ascending

        // Ambil keyword pencarian
        $search = $request->get('search', '');

        // Format bulan dan tahun untuk query
        $bulanTahun = $selectedTahun . '-' . str_pad($selectedBulan, 2, '0', STR_PAD_LEFT);

        // Query pemakaian berdasarkan bulan dan tahun
        $pemakaianAir = Pemakaian_Air::where('bulan', $bulanTahun)->with(['warga' => function($query) use ($sort, $direction) {
            // Sorting berdasarkan kolom di tabel warga (relasi)
            if ($sort == 'nama' || $sort == 'alamat') {
                $query->orderBy($sort, $direction);
            }
        }]);

        // Pencarian
        if (!empty($search)) {
            $pemakaianAir->whereHas('warga', function($query) use ($search) {
                $query->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('alamat', 'like', '%' . $search . '%'); // Gunakan orWhere
            });
        }

        // Sorting berdasarkan kolom di tabel pemakaian_air jika tidak diatur sebelumnya
        if ($sort != 'nama' && $sort != 'alamat') {
            $pemakaianAir->orderBy($sort, $direction);
        }

        $pemakaianAir = $pemakaianAir->get();

        // List bulan dan tahun untuk dropdown
        $bulanList = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];

        $tahunList = range(date('Y'), date('Y') - 5); // 5 tahun terakhir

        return view('operator.history', compact('role', 'pemakaianAir', 'selectedBulan', 'selectedTahun', 'bulanList', 'tahunList', 'sort', 'direction', 'search'));
    }
}
