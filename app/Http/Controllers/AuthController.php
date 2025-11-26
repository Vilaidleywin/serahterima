<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    // 👉 TAMBAHKAN INI
    public function homeRedirect()
    {
        // kalau sudah login, lempar ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // kalau belum login, lempar ke halaman login
        return redirect()->route('login');
    }
    // 👉 SAMPAI SINI

    public function login(Request $request)
    {
        $data = $request->validate([
            'login'    => ['required'],
            'password' => ['required'],
        ]);

        $login = $data['login'];

        $user = User::where('email', $login)
            ->orWhere('username', $login)
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return back()
                ->withErrors(['login' => 'Email/Username atau password salah.'])
                ->withInput();
        }

        if (!$user->is_active) {
            return back()
                ->withErrors(['login' => 'Akun Anda telah dinonaktifkan.'])
                ->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
