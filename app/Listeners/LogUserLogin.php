<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\UserLogin;

class LogUserLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        // tandai user online
        $user->update([
            'is_online' => true,
            'last_seen' => now(),
        ]);

        // ===== FINAL ANTI DOUBLE (DATABASE LEVEL) =====
        UserLogin::firstOrCreate(
            [
                'user_id'    => $user->id,
                'session_id' => session()->getId(),
            ],
            [
                'ip'         => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]
        );
    }
}
