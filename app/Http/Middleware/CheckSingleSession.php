<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSingleSession
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            // Kalau session_id di DB beda dengan session sekarang → tendang
            if ($user->session_id !== session()->getId()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Akun Anda login di perangkat lain.');
            }
        }

        return $next($request);
    }
}
