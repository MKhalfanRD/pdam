<?php

namespace App\Http\Controllers\warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warga;
use Illuminate\Support\Facades\Auth;

class WargaController extends Controller
{
    public function index()
    {   $role = Auth::user()->role;
        // Ambil ID pengguna yang sedang login
        $userId = Auth::id();

        // Ambil data warga berdasarkan user_id yang sesuai
        $warga = Warga::where('user_id', $userId)->first();

        // Pastikan untuk mengembalikan data ke view
        return view('warga.index', compact('warga', 'role'));
    }
}
