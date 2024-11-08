<?php

namespace App\Http\Controllers\pembayaran;

use App\Models\Pemakaian_Air;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class PembayaranController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $role = Auth::user()->role;

        // Pastikan user yang login memiliki role 'warga'
        if ($user->role !== 'warga') {
            abort(403, 'Unauthorized action.');
        }

        // Ambil data pembayaran yang sesuai dengan `warga_id` user yang login
        $pembayaranList = Pembayaran::where('warga_id', $user->warga->warga_id)
                            ->with('pemakaianAir')
                            ->orderBy('waktuBayar', 'desc')
                            ->paginate(10); // Pagination untuk 10 pembayaran per halaman

        return view('pembayaran.index', compact('role', 'pembayaranList'));
    }
    // public function create()
    // {
    //     $user = Auth::user();
    //     $role = $user->role;

    //     // Ambil bulan dan tahun saat ini
    //     $currentYear = now()->year;
    //     $currentMonth = now()->month;

    //     // Ambil tagihan bulan ini untuk ditampilkan di view
    //     $pemakaianAir = Pemakaian_Air::whereYear('bulan', $currentYear)
    //         ->whereMonth('bulan', $currentMonth)
    //         ->where('warga_id', $user->id)
    //         ->first();

    //     // Debug untuk melihat apakah tagihan sudah ada
    //     dd($pemakaianAir, optional($pemakaianAir)->tagihanAir);

    //     $pembayaran = null;

    //     if ($pemakaianAir && !is_null($pemakaianAir->tagihanAir)) {
    //         $pembayaran = Pembayaran::where('pemakaianAir_id', $pemakaianAir->pemakaianAir_id)->first();
    //     } else {
    //         $pemakaianAir = null;
    //     }

    //     return view('pembayaran.create', compact('role', 'pemakaianAir', 'pembayaran'));
    // }

    public function create(Request $request)
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

        return view('pembayaran.create', compact('role', 'pemakaianAir', 'pembayaran'));
    }



    // Simpan bukti pembayaran
    public function store(Request $request)
    {
        $request->validate([
            'pemakaianAir_id' => 'required|exists:pemakaian_air,pemakaianAir_id',
            'buktiBayar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'komentar' => 'nullable|string',
        ]);

        // Upload bukti pembayaran
        $buktiBayarPath = $request->file('buktiBayar')->store('bukti_pembayaran', 'public');

        $p = Pembayaran::create([
            'warga_id' => Auth::user()->warga->warga_id,
            'pemakaianAir_id' => $request->pemakaianAir_id,
            'buktiBayar' => $buktiBayarPath,
            'waktuBayar' => now(),
            // 'tunggakan' => $request->input('tunggakan', 0), // Sesuaikan nilai tunggakan
            'komentar' => $request->komentar,
        ]);
        // dd($p);

        return redirect()->route('warga.index')->with('success', 'Bukti pembayaran berhasil diupload.');
    }
}
