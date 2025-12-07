<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class LogUserLogin
{
    public function handle(Login $event)
    {
        try {
            DB::table('user_logins')->insert([
                'user_id'    => $event->user->id,
                'ip'         => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Jangan hentikan request
            \Log::error('LogUserLogin ERROR: ' . $e->getMessage());
        }
    }
}
