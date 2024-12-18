<?php

namespace App\Http\Controllers\operator;

use App\Http\Controllers\Controller;
use App\Models\Pemakaian_Air;
use App\Models\Pembayaran;
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
        // $operator_id = Auth::user()->id;
        // dd($operator_id, $role);
        $warga = Warga::findOrFail($warga_id);

        // Mendapatkan tanggal saat ini
        $tanggalHariIni = Carbon::today();
        $bulanSaatIni = $tanggalHariIni->month;
        $tahunSaatIni = $tanggalHariIni->year;

        // $pemakaianAir = Pemakaian_Air::where('warga_id', $warga_id)->first();

         // Cari data pemakaian air berdasarkan warga_id, bulan, dan tahun saat ini
        $pemakaianAir = Pemakaian_Air::where('warga_id', $warga_id)
        ->whereMonth('bulan', $bulanSaatIni)
        ->whereYear('bulan', $tahunSaatIni)
        ->first();

        // Jika tidak ada data, buat instance baru dengan default bulan dan tahun saat ini
        if (!$pemakaianAir) {
            $pemakaianAir = new Pemakaian_Air();
            $pemakaianAir->warga_id = $warga_id;
            $pemakaianAir->bulan = $bulanSaatIni; // Set bulan saat ini
            $pemakaianAir->tahun = $tahunSaatIni; // Set tahun saat ini
            $pemakaianAir->pemakaianLama = 0; // Default nilai
            $pemakaianAir->pemakaianBaru = 0; // Default nilai
            $pemakaianAir->tagihanAir = 0; // Default nilai
        }

        return view('operator.edit', compact(['warga', 'pemakaianAir', 'role']));
    }

    public function update(Request $request, $warga_id)
    {
        $pemakaianAir = Pemakaian_Air::where('warga_id', $warga_id)
        ->whereMonth('bulan', now()->month) // Hanya cari berdasarkan bulan
        ->whereYear('bulan', now()->year)  // Hanya cari berdasarkan tahun
        ->first();

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

        $operator_id = Auth::user()->id;

        // $currentMonthYear = now()->format('Y-m-d');
        $bulanSaatIni = now()->month;
        $tahunSaatIni = now()->year;

        // Format bulan dan tahun dalam format 'Y-m' (tanpa mengubah tanggal)
        $currentMonthYear = now()->format('Y-m') . '-01';

        // Perbarui data atau buat baru jika belum ada
        $pemakaianAir = Pemakaian_Air::updateOrCreate(
            [
                'warga_id' => $warga_id,
                'bulan' => $currentMonthYear // Format hanya tahun dan bulan
                // 'tahun' => $tahunSaatIni, // Pastikan hanya berlaku untuk tahun ini
            ],
            [
                'operator_id' => Auth::id(),
                'pemakaianLama' => $request->input('pemakaianLama'),
                'pemakaianBaru' => $request->input('pemakaianBaru'),
                'kubikasi' => $kubikasi,
                'tagihanAir' => $tagihanAir,
                'foto' => $fotoPath,
            ]
        );

        // // Ensure that the Pembayaran record exists for this resident, month, and year
        Pembayaran::firstOrCreate(
            [
                'warga_id' => $warga_id,
                'waktuBayar' => $currentMonthYear,
            ],
            [
                'status' => 'Belum Bayar', // Default status if not yet paid
                'pemakaianAir_id' => $pemakaianAir->pemakaianAir_id, // Link it to the Pemakaian_Air record
                'buktiBayar' => '', // Default sementara untuk kolom buktiBayar
                'tunggakan' => 0.00, // Nilai default untuk tunggakan
                'komentar' => null, // Nilai default jika tidak ada komentar
            ]
        );
        // dd($d);

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
