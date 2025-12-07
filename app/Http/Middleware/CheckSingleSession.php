<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSingleSession
{
    public function handle($request, Closure $next)
    {
        // ... kode yang sudah ada di middleware lu (JANGAN DIHAPUS)

        // === Tambahkan ini sebelum return $next($request); ===
        if (Auth::check()) {
            $user = Auth::user();
            $user->last_seen = now();
            $user->save();
        }

        return $next($request);
    }
}
