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
        // Kalau belum login, tampilkan form
        if (!Auth::check()) {
            return view('auth.login');
        }

        // Kalau SUDAH login, arahkan sesuai role
        $user = Auth::user();

        switch ($user->role) {
            case 'admin_internal':
            case 'admin_komersial':
                return redirect()->route('dashboard');

            case 'user':
            default:
                return redirect()->route('dashboard');
        }
    }

    public function homeRedirect()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        switch ($user->role) {
            case 'admin_internal':
            case 'admin_komersial':
                return redirect()->route('dashboard');

            case 'user':
            default:
                return redirect()->route('dashboard');
        }
    }

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

        // 🔐 Login
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // ⬇️⬇️ TAMBAH DI SINI ⬇️⬇️
        // Simpan session ID baru ke database (kolom session_id di tabel users)
        $user->session_id = session()->getId();
        $user->save();
        // ⬆️⬆️ SAMPAI SINI ⬆️⬆️

        // Mode “ditabrak”: tendang session lain user ini
        Auth::logoutOtherDevices($data['password']);

        // Default redirect beda tergantung role
        $defaultRedirect = match ($user->role) {
            'admin_internal', 'admin_komersial' => route('dashboard'),
            default                             => route('dashboard'),
        };

        return redirect($defaultRedirect);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
