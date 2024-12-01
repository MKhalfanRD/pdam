<?php

namespace App\Http\Controllers\operator;

use App\Http\Controllers\Controller;
use App\Models\Pemakaian_Air;
use App\Models\User;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OperatorController extends Controller
{
    public function index(Request $request)
    {
        $role = Auth::user()->role;
        $sort = $request->get('sort', 'nama'); // default sort by 'nama'
        $direction = $request->get('direction', 'asc'); // default direction 'asc'
        $search = $request->get('search', '');

        // Query untuk pencarian, sorting, dan filter berdasarkan role warga
        $warga = Warga::whereHas('user', function ($query) {
            $query->where('role', 'warga'); // Filter hanya pengguna dengan role 'warga'
        });

        // Jika ada pencarian, lakukan filter berdasarkan nama atau alamat
        if (!empty($search)) {
            $warga = $warga->where(function ($query) use ($search) {
                $query->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('alamat', 'like', '%' . $search . '%');
            });
        }

        // Sorting berdasarkan sort dan direction yang diberikan
        $warga = $warga->orderBy($sort, $direction)->paginate(10);

        return view('operator.index', compact('role', 'warga', 'sort', 'direction', 'search'));
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

    public function create()
    {
        $role = Auth::user()->role;
        return view('operator.create', compact('role'));
    }

    public function store(Request $request)
    {
        $role = Auth::user()->role;
        // Validasi data
        $request->validate([
            'nama' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'alamat' => 'required|string|max:200',
            'telp' => 'required|numeric',
            'password' => 'required|string|min:8',
        ]);

        // menyimpan ke tabel 'users'
        $user = User::create([
            'username' => $request->nama,
            'email' => $request->email,
            'role' => 'warga', // Menetapkan role warga secara otomatis
            'password' => Hash::make($request->password),
        ]);
        // menyimpan ke tabel 'warga'
        $warga = Warga::create([
            'user_id' => $user->user_id, //menghubungkan warga ke user yang baru dibuat
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'telp' => $request->telp,
        ]);
        // dd($warga);

        return redirect()->route('operator.index')->with('success', 'Warga berhasil didaftarkan!');
    }

    public function edit($warga_id){
        $role = Auth::user()->role;
        $warga = Warga::findOrFail($warga_id);

        $pemakaianAir = Pemakaian_Air::where('warga_id', $warga_id)->first();

        if (!$pemakaianAir) {
            $pemakaianAir = new Pemakaian_Air(); // atau bisa juga set nilai default lainnya
        }


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

        // Menghitung kubikasi
        $kubikasi = $request->input('pemakaianBaru') - $request->input('pemakaianLama');

        // Perhitungan sesuai formula yang diberikan
        $sepuluhPertama = ($kubikasi < 10) ? $kubikasi : 10;
        $sepuluhKedua = ($kubikasi > 20) ? 10 : (($kubikasi > 10) ? $kubikasi - 10 : 0);
        $sisa = ($kubikasi > 20) ? $kubikasi - 20 : 0;

        // Menghitung tagihan berdasarkan tarif
        $tagihanAir = ($sepuluhPertama * 1500) + ($sepuluhKedua * 1750) + ($sisa * 2000) + 25000;

        // Menyimpan path foto jika sudah ada atau proses upload foto jika ada
        $fotoPath = $pemakaianAir && $pemakaianAir->foto ? $pemakaianAir->foto : null;
        if ($request->hasFile('fotoMeteran')) {
            $file = $request->file('fotoMeteran');
            $fotoPath = $file->store('fotoMeteran', 'public'); // Simpan di storage
        }

        $operator_id = Auth::user()->id;;

        $currentMonthYear = now()->format('Y-m-d');

        // Update data di pemakaian_air
        Pemakaian_Air::updateOrCreate(
            ['warga_id' => $warga_id], // Jika sudah ada warga_id, perbarui
            [
                'operator_id' => $operator_id,
                'bulan' => $currentMonthYear, // Format YYYY-MM-DD
                'pemakaianLama' => $request->input('pemakaianLama'),
                'pemakaianBaru' => $request->input('pemakaianBaru'),
                'kubikasi' => $kubikasi,
                'tagihanAir' => $tagihanAir,
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

        // Query pemakaian berdasarkan bulan dan tahun
        $pemakaianAir = Pemakaian_Air::whereYear('bulan', $selectedTahun)
            ->whereMonth('bulan', $selectedBulan)
            ->with(['warga' => function($query) use ($sort, $direction) {
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
