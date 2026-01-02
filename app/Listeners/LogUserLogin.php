<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\UserLogin;

class LogUserLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;
        $sessionId = session()->getId();

        // tandai user online
        $user->update([
            'is_online' => true,
            'session_id' => $sessionId,
            'last_seen' => now(),
        ]);

        // SINGLE LOGIN: UPDATE kalau ada, CREATE kalau belum
        UserLogin::updateOrCreate(
            ['user_id' => $user->id], // KEY UNIK
            [
                'session_id'   => $sessionId,
                'ip'           => request()->ip(),
                'user_agent'   => request()->userAgent(),
                'is_online'    => true,
                'last_activity' => now(),
            ]
        );
    }
}
