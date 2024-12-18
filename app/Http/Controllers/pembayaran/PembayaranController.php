<?php

namespace App\Http\Controllers\pembayaran;

use App\Models\Pemakaian_Air;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Support\Facades\Storage as FacadesStorage;

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


        // Jika data pemakaian air belum ada
        if (is_null($pemakaianAir)) {
            return view('pembayaran.create', [
                'message' => 'Tagihan belum tersedia. Silakan cek kembali nanti.',
                'role' => $role, // Pass role to the view
            ]);
        }

        $pembayaran = null;
        // Jika ada tagihan, periksa status pembayaran
        if (!is_null($pemakaianAir->tagihanAir)) {
            $pembayaran = Pembayaran::where('pemakaianAir_id', $pemakaianAir->pemakaianAir_id)->first();
        }

        // Tampilkan pesan jika belum ada bukti bayar
        if (!$pembayaran || is_null($pembayaran->buktiBayar)) {
            return view('pembayaran.create', [
                'pemakaianAir' => $pemakaianAir,
                'message' => 'Anda belum mengunggah bukti pembayaran. Silakan unggah bukti bayar.',
                'pembayaran' => $pembayaran, // pastikan pembayaran tetap ada
                'role' => $role, // Pass role to the view
            ]);
        }
        return view('pembayaran.create', compact('role', 'pemakaianAir', 'pembayaran' ));
    }



    // Simpan bukti pembayaran
    public function store(Request $request)
    {
        $request->validate([
            'pemakaianAir_id' => 'required|exists:pemakaian_air,pemakaianAir_id',
            'buktiBayar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'komentar' => 'nullable|string',
        ]);

        // Format bulan dan tahun dalam format 'Y-m' (tanpa mengubah tanggal)
        $currentMonthYear = now()->format('Y-m') . '-01';

        // Upload bukti pembayaran
        $buktiBayarPath = $request->file('buktiBayar')->store('bukti_pembayaran', 'public');

        $p = Pembayaran::updateOrCreate(
            [
                'warga_id' => Auth::user()->warga->warga_id,
                'pemakaianAir_id' => $request->pemakaianAir_id,
            ],
            [
                'buktiBayar' => $buktiBayarPath,
                'waktuBayar' => $currentMonthYear,
                // 'tunggakan' => $request->input('tunggakan', 0), // Sesuaikan nilai tunggakan
                'status' => 'pending', // Set status menjadi pending
                'komentar' => $request->komentar,
            ]

        );
        // dd($p);

        return redirect()->route('warga.index')->with('success', 'Pembayaran berhasil diupload.');
    }

    public function edit(Pembayaran $pembayaran)
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

        // Jika ada tagihan, periksa status pembayaran
        if ($pemakaianAir && !is_null($pemakaianAir->tagihanAir)) {
            $pembayaran = Pembayaran::where('pemakaianAir_id', $pemakaianAir->pemakaianAir_id)->first();

            // Pastikan pembayaran ada sebelum mengambil validasi
            $validasi = $pembayaran ? $pembayaran->validasi : null;

            // Tambahkan properti 'editable' untuk mengontrol apakah pembayaran bisa diubah
            if ($pembayaran && $pembayaran->status !== 'Terverifikasi') {
                $pemakaianAir->editable = true; // Jika status belum "Terverifikasi", warga boleh mengedit
            } else {
                $pemakaianAir->editable = false; // Jika sudah diverifikasi, edit tidak diperbolehkan
                $validasi = $pembayaran->validasi ?? null;
            }
        } else {
            $pemakaianAir = null; // Tidak ada tagihan untuk bulan ini
        }

        // dd($pemakaianAir);

        return view('pembayaran.edit', compact('pembayaran', 'role', 'pemakaianAir', 'validasi'));
    }

    public function update(Request $request, Pembayaran $pembayaran)
    {
        // dd($request->all());

        $validatedData = $request->validate([
            'buktiBayar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Membolehkan 'nullable' jika tidak ada perubahan gambar
            'komentar' => 'nullable|string|max:255',
        ]);

        // Simpan file baru jika ada
        if ($request->hasFile('buktiBayar')) {
            // Jika ada file baru, simpan dan ganti nilai buktiBayar
            $validatedData['buktiBayar'] = $request->file('buktiBayar')->store('bukti-bayar', 'public');
        } else {
            // Jika tidak ada file baru, pertahankan file yang lama
            $validatedData['buktiBayar'] = $pembayaran->buktiBayar;
        }

        // Pertahankan komentar lama jika tidak ada perubahan
        $validatedData['komentar'] = $request->komentar ?? $pembayaran->komentar;

        // Perbarui data pembayaran
        $pembayaran->update($validatedData);
        // dd($pembayaran);

        return redirect()->route('warga.index')->with('success', 'Pembayaran berhasil diperbarui.');
    }
}
