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
    public function create()
    {
        $user = Auth::user();
        $role = Auth::user()->role;

        // Ambil tagihan bulan ini untuk ditampilkan di view
        $currentMonthYear = now()->format('Y-m'); // Format YYYY-MM
        $pemakaianAir = Pemakaian_Air::where('bulan', $currentMonthYear)
            ->where('warga_id', Auth::id()) // Assuming the `warga_id` is the same as the logged-in user ID
            ->get();

        return view('pembayaran.create', compact('role', 'pemakaianAir'));
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

        Pembayaran::create([
            'warga_id' => Auth::user()->warga->warga_id,
            'pemakaianAir_id' => $request->pemakaianAir_id,
            'buktiBayar' => $buktiBayarPath,
            'waktuBayar' => now(),
            'tunggakan' => $request->input('tunggakan', 0), // Sesuaikan nilai tunggakan
            'komentar' => $request->komentar,
        ]);

        return redirect()->route('pembayaran.create')->with('success', 'Bukti pembayaran berhasil diupload.');
    }
}
