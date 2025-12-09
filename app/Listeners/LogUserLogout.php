<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

class LogUserLogout
{
    public function handle(Logout $event)
    {
        if ($event->user) {
            $event->user->update([
                'is_online' => false,
                'last_seen' => now(),
            ]);
        }
    }
}
