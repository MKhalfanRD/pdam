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
        $wargaId = Auth::user()->warga->warga_id;

        $user = Auth::user();
        // Ambil data nama warga melalui relasi
        $warga = $user->warga;

        // Ambil data tagihan bulan ini
        $tagihanBulanIni = Pemakaian_Air::where('warga_id', $wargaId)
                                ->whereMonth('bulan', now()->month)
                                ->whereYear('bulan', now()->year)
                                ->first();

        // Hitung total tunggakan jika ada
        $tunggakan = DB::table('pemakaian_air')
                        ->leftJoin('pembayaran', 'pemakaian_air.pemakaianAir_id', '=', 'pembayaran.pemakaianAir_id')
                        ->where('pemakaian_air.warga_id', $wargaId)
                        ->where('pemakaian_air.bulan', '<', now()->format('Y-m'))
                        ->whereNull('pembayaran.status') // Memeriksa status dari tabel pembayaran
                        ->sum('pemakaian_air.tagihanAir');

        return view('warga.index', compact('warga', 'role', 'tagihanBulanIni', 'tunggakan'));
}


    public function history()
    {
        $wargaId = Auth::user()->warga->warga_id;
        // Ambil histori pembayaran
        $historiPembayaran = Pembayaran::where('warga_id', $wargaId)
                            ->with('pemakaianAir')
                            ->orderBy('waktuBayar', 'desc')
                            ->get();

        return view('warga.history', compact( 'historiPembayaran'));

    }
}
