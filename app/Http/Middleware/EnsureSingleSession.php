<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnsureSingleSession
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $valid = DB::table('sessions')
                ->where('id', session()->getId())
                ->exists();

            if (! $valid) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('login')
                    ->withErrors([
                        'login' => 'Akun Anda login di perangkat lain.'
                    ]);
            }
        }

        return $next($request);
    }
}
