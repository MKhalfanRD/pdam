<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Pemakaian_Air;
use App\Models\Pembayaran;
use App\Models\User;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $role = Auth::user()->role;

        // Default filter adalah tanggal hari ini
        $tanggalHariIni = Carbon::today();
        $bulan = $request->input('bulan', $tanggalHariIni->month); // Default bulan sekarang
        $tahun = $request->input('tahun', $tanggalHariIni->year); // Default tahun sekarang

        // Ambil data berdasarkan bulan dan tahun yang dipilih
        $data = Pemakaian_Air::whereMonth('bulan', $bulan)
            ->whereYear('bulan', $tahun)
            ->get();

        // $data = User::with('warga')->where('role', 'warga')->get();
        $data->map(function($item) {
            $carbonDate = Carbon::parse($item->bulan, $item->tahun);
            $item->bulan = $carbonDate->format('F'); // Nama bulan (contoh: November)
            $item->tahun = $carbonDate->format('Y'); // Tahun (contoh: 2024)
            return $item;
        });
        // dd($data);
        return view('admin.index', compact('role', 'data'));
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
}
