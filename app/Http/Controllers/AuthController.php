<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // GET /login
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    // POST /login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'login'    => ['required', 'string'], // bisa username atau email
            'password' => ['required'],
        ]);

        // Cek apakah input adalah email atau username
        $loginType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Coba login berdasarkan jenis input
        if (Auth::attempt(
            [$loginType => $credentials['login'], 'password' => $credentials['password']],
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // Jika gagal login
        return back()->withErrors([
            'login' => ucfirst($loginType) . ' atau password salah.',
        ])->onlyInput('login');
    }

    // POST /logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // GET /
    public function homeRedirect()
    {
        if (!auth()->check()) return redirect()->route('login');

        return match (auth()->user()->role) {
            'admin_internal', 'admin_komersial' => redirect()->route('admin.users.index'),
            default => redirect()->route('dashboard'),
        };
    }
}
