<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateLastActivity
{
    public function handle(Request $request, Closure $next)
    {
        // ❌ JANGAN update last_activity dari polling realtime
        if ($request->routeIs('admin.online-users-count')) {
            return $next($request);
        }

        if (Auth::check()) {
            Auth::user()->update([
                'last_activity' => now(),
            ]);
        }

        return $next($request);
    }
}
