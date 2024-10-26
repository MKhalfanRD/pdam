<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     return redirect()->intended(route('dashboard', absolute: false));
    // }

    public function store(LoginRequest $request)
    {
        // Autentikasi pengguna menggunakan request yang dikirim
        $request->authenticate();

        // Regenerasi sesi untuk keamanan
        $request->session()->regenerate();

        // Ambil pengguna yang sedang login
        $user = Auth::user();
        $role = $user->role;

        // Pastikan variabel $user tidak null sebelum mengakses properti
        if ($user) {
            // dd('test');
            // return redirect()->intended(route('operator.index'));
            // Ambil role dari pengguna
            $role = $user->role;
            Log::info('User logged in:', ['user' => $user->email, 'role' => $role]);
            // Debug data user dan role yang diambil
            // dd($user, $role); // Cek nilai dari user dan role

            // Arahkan pengguna berdasarkan peran
            if ($role === 'admin') {
                return redirect()->route('admin.index');
            } elseif ($role === 'operator') {
                return redirect()->route('operator.index');
            } elseif ($role === 'warga') {
                return redirect()->route('warga.index');
            }
        }

        // Jika pengguna tidak terautentikasi, lemparkan error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
