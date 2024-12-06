<?php

namespace App\Http\Controllers\warga;

use App\Http\Controllers\Controller;
use App\Models\Pemakaian_Air;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Models\Warga;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WargaController extends Controller
{
    // public function index()
    // {
    //     $role = Auth::user()->role;
    //     // Ambil ID pengguna yang sedang login
    //     $userId = Auth::id();

    //     // Ambil data warga berdasarkan user_id yang sesuai
    //     $warga = Warga::where('user_id', $userId)->first();

    //     // Pastikan untuk mengembalikan data ke view
    //     return view('warga.index', compact('warga', 'role'));
    // }

// ...

    public function index()
    {
        $role = Auth::user()->role;
        $warga = Auth::user()->warga;
        $wargaId = $warga->warga_id;

        $pemakaianAir = Pemakaian_Air::where('warga_id', $wargaId)
            ->whereMonth('bulan', now()->month)
            ->whereYear('bulan', now()->year)
            ->first();

        // $pembayaran = Pembayaran::where('warga_id', $wargaId)
        //               ->where('pemakaianAir_id', $tagihanBulanIni->pemakaianAir_id ?? null)
        //               ->first();

        $pembayaran = null;
        $statusPembayaran = 'Belum Bayar';
        $jumlahTagihan = 0;

        // Cek jika ada tagihan bulan ini
        if ($pemakaianAir && !is_null($pemakaianAir->tagihanAir)) {
            $jumlahTagihan = $pemakaianAir->tagihanAir;

            // Cari data pembayaran berdasarkan pemakaianAir_id dan warga_id
            $pembayaran = Pembayaran::where('pemakaianAir_id', $pemakaianAir->pemakaianAir_id)
            ->where('warga_id', $wargaId) // pastikan warga yang sedang login yang memiliki pembayaran ini
            ->first();

            // Jika pembayaran sudah ada, ambil status pembayaran dan jumlah tagihan
            if ($pembayaran) {
                $statusPembayaran = $pembayaran->status; // "Belum Bayar", "Pending", atau "Terverifikasi"

                // Jika status pembayaran "Sudah Bayar", set jumlah tagihan menjadi 0
                if ($statusPembayaran === 'Terverifikasi') {
                    $jumlahTagihan = 0;
                }
            }
        }

        // // Hitung total tunggakan jika ada
        // $tunggakan = DB::table('pemakaian_air')
        //                 ->leftJoin('pembayaran', 'pemakaian_air.pemakaianAir_id', '=', 'pembayaran.pemakaianAir_id')
        //                 ->where('pemakaian_air.warga_id', $wargaId)
        //                 ->where('pemakaian_air.bulan', '<', now()->format('Y-m'))
        //                 ->whereNull('pembayaran.status') // Memeriksa status dari tabel pembayaran
        //                 ->sum('pemakaian_air.tagihanAir');

                        // dd($tagihanBulanIni);
        // return view('warga.index', compact('role', 'warga', 'tagihanBulanIni', 'tunggakan', 'pembayaran'));
        return view('warga.index', compact('role', 'warga', 'pemakaianAir', 'pembayaran', 'statusPembayaran', 'jumlahTagihan'));
    }



    public function history()
    {
        $role = Auth::user()->role; // Simpan role untuk penggunaan di view
        $wargaId = Auth::user()->warga->warga_id; // Ambil ID warga dari user yang login

        // Ambil histori pembayaran untuk warga terkait, diurutkan berdasarkan waktu bayar terbaru
        $historiPembayaran = Pembayaran::where('warga_id', $wargaId)
                            ->with('pemakaianAir') // Load relasi jika diperlukan
                            ->orderBy('waktuBayar', 'desc')
                            ->paginate(10); // Tambahkan paginasi untuk mengontrol jumlah data per halaman

        // dd($role);

        return view('warga.history', compact('historiPembayaran', 'role'));
    }


    public function detailHistory()
    {
        $user = Auth::user();
        $role = $user->role;
        $warga = $user->warga;
        $wargaId = $warga->warga_id;

        // Ambil tagihan bulan ini
        $pemakaianAir = Pemakaian_Air::where('warga_id', $wargaId)
                            ->whereMonth('bulan', now()->month)
                            ->whereYear('bulan', now()->year)
                            ->first();

        // Debug untuk melihat apakah tagihan dan pembayaran sudah ada
        // dd($pemakaianAir, optional($pemakaianAir)->tagihanAir);

        $pembayaran = null;

        if ($pemakaianAir && !is_null($pemakaianAir->tagihanAir)) {
            $pembayaran = Pembayaran::where('pemakaianAir_id', $pemakaianAir->pemakaianAir_id)->first();
        } else {
            $pemakaianAir = null;
        }

        return view('warga.detailHistory', compact('role', 'pemakaianAir', 'pembayaran'));
    }
}
