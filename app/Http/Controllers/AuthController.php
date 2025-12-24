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
        if (!Auth::check()) {
            return view('auth.login');
        }

        $user = Auth::user();

        return match ($user->role) {
            'admin_internal', 'admin_komersial' => redirect()->route('admin.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }

    public function homeRedirect()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        return match ($user->role) {
            'admin_internal', 'admin_komersial' => redirect()->route('admin.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login'    => ['required'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $data['login'])
            ->orWhere('username', $data['login'])
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return back()->withErrors([
                'login' => 'Email/Username atau password salah.'
            ])->withInput();
        }

        if (isset($user->is_active) && !$user->is_active) {
            return back()->withErrors([
                'login' => 'Akun Anda telah dinonaktifkan.'
            ])->withInput();
        }

        // ================= LOGIN =================
        Auth::login($user, $request->boolean('remember'));

        // regenerate session (WAJIB)
        $request->session()->regenerate();

        // ⚠️ TIDAK ADA LOG LOGIN DI SINI
        // ⚠️ TIDAK ADA UserLogin::create()
        // ⚠️ TIDAK ADA Cache
        // ⚠️ SEMUA LOG LOGIN DITANGANI Event Listener

        return redirect(match ($user->role) {
            'admin_internal', 'admin_komersial' => route('admin.dashboard'),
            default => route('dashboard'),
        });
    }

    public function logout(Request $request)
    {
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
