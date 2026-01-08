<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LogUserLogin
{
    public function handle(Login $event): void
    {
        if (!Schema::hasTable('user_logins')) {
            return;
        }

        $userId = $event->user->id;

        // Cegah keitung 2x: kalau ada log login user ini dalam 15 detik terakhir, skip
        $recent = DB::table('user_logins')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subSeconds(15))
            ->exists();

        if ($recent) {
            return;
        }

        DB::table('user_logins')->insert([
            'user_id'    => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
