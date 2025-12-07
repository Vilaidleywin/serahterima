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
        // Kalau belum login, tampilkan form
        if (!Auth::check()) {
            return view('auth.login');
        }

        // Kalau SUDAH login, arahkan sesuai role
        $user = Auth::user();

        switch ($user->role) {
            case 'admin_internal':
            case 'admin_komersial':
                return redirect()->route('admin.dashboard');   // <<< admin

            case 'user':
            default:
                return redirect()->route('dashboard');         // <<< user
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
                return redirect()->route('admin.dashboard');   // <<< admin ke /admin/dashboard

            case 'user':
            default:
                return redirect()->route('dashboard');         // <<< user ke /dashboard
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

        // simpan session id di tabel users (punyamu)
        $user->session_id = session()->getId();
        $user->save();

        // logout device lain
        Auth::logoutOtherDevices($data['password']);

        // ====== LOG LOGIN KE TABEL user_logins ======
        // DB::table('user_logins')->updateOrInsert(
        //     ['user_id' => $user->id],
        //     [
        //         'ip'         => $request->ip(),
        //         'user_agent' => $request->userAgent(),
        //         'updated_at' => now(),
        //         'created_at' => now(),
        //     ]
        // );

        // ============================================

        // Bedakan redirect berdasarkan role
        $defaultRedirect = match ($user->role) {
            'admin_internal', 'admin_komersial' => route('admin.dashboard'),
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
