<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (!Auth::check()) {
            return view('auth.login');
        }

        $user = Auth::user();

        switch ($user->role) {
            case 'admin_internal':
            case 'admin_komersial':
                return redirect()->route('admin.dashboard');

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
                return redirect()->route('admin.dashboard');

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

        if (isset($user->is_active) && !$user->is_active) {
            return back()
                ->withErrors(['login' => 'Akun Anda telah dinonaktifkan.'])
                ->withInput();
        }

        // === LOGIN SUKSES ===
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // === SET USER ONLINE ===
       

        // Logout device lain
        Auth::logoutOtherDevices($data['password']);

        // Redirect berdasarkan role
        $defaultRedirect = match ($user->role) {
            'admin_internal', 'admin_komersial' => route('admin.dashboard'),
            default => route('dashboard'),
        };

        return redirect($defaultRedirect);
    }

    public function logout(Request $request)
    {
        // === SET OFFLINE SAAT LOGOUT ===
        if (Auth::check()) {
            Auth::user()->update([
                'is_online' => false,
                'last_seen' => now(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
