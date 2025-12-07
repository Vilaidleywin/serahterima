<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userRole = $user->role ?? null;

        // Jika cocok → lanjut
        if (in_array($userRole, $roles, true)) {
            return $next($request);
        }

        // ❌ Role tidak cocok → JANGAN redirect! → cegah LOOP
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
