<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Contoh pemakaian di route:
     *  - role:user
     *  - role:admin_internal,admin_komersial
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userRole = $user->role ?? null;

        // Jika role yang diminta route cocok → lanjut
        if (in_array($userRole, $roles, true)) {
            return $next($request);
        }

        /**
         * ❌ USER coba akses area ADMIN
         * contoh: role user, tapi route pakai role:admin_internal,admin_komersial
         */
        if ($userRole === 'user' && $this->rolesContainAdmin($roles)) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman admin.');
        }

        /**
         * ❌ ADMIN coba akses area USER
         * contoh: userRole = admin_internal → route role:user
         */
        if ($this->isAdmin($userRole) && in_array('user', $roles, true)) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman pengguna.');
        }

        // Default → tidak diizinkan
        abort(403, 'Unauthorized.');
    }

    /**
     * Helper: apakah roles route mengandung role admin?
     */
    private function rolesContainAdmin(array $roles): bool
    {
        return in_array('admin_internal', $roles, true)
            || in_array('admin_komersial', $roles, true);
    }

    /**
     * Helper: apakah user admin?
     */
    private function isAdmin(string $role = null): bool
    {
        return in_array($role, ['admin_internal', 'admin_komersial'], true);
    }
}
