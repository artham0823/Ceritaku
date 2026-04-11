<?php

namespace App\Http\Controllers;

/**
 * =====================================================
 * CONTROLLER: AuthController (Autentikasi)
 * =====================================================
 * Mengelola login, register, dan logout pengguna.
 * 
 * Login  : Semua user (username + password)
 * Register: Membuat akun member baru (publik)
 * Logout : Keluar dari akun
 * =====================================================
 */

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /** Tampilkan halaman login */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    /** Proses login */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cek apakah user ada
        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return back()->withErrors(['username' => 'Username tidak ditemukan.'])->withInput();
        }

        // Cek apakah akun diblokir
        if ($user->is_blocked) {
            return back()->withErrors(['username' => 'Akun Anda telah diblokir.'])->withInput();
        }

        // Coba login
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        return back()->withErrors(['password' => 'Password salah.'])->withInput();
    }

    /** Tampilkan halaman register (untuk member baru) */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.register');
    }

    /** Proses register member baru */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip, dan underscore.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Cegah membuat akun dengan username "artham"
        if (strtolower($request->username) === 'artham') {
            return back()->withErrors(['username' => 'Username ini tidak boleh digunakan.'])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password,
            'role' => 'member',
        ]);

        Auth::login($user);
        return redirect('/')->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user->name . '!');
    }

    /** Proses logout */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Berhasil keluar dari akun.');
    }
}
