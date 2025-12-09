<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogUserLogin
{
    public function handle(Login $event)
    {
        $user = $event->user;

        // tandai user online
        $user->update([
            'is_online' => true,
            'last_seen' => now(),
        ]);

        try {
            if (DB::getSchemaBuilder()->hasTable('user_logins')) {

                // ===== CEK ANTI DOUBLE INSERT (2 DETIK) =====
                $recent = DB::table('user_logins')
                    ->where('user_id', $user->id)
                    ->where('created_at', '>=', now()->subSeconds(2))
                    ->exists();

                if ($recent) {
                    return; // ---> SKIP agar tidak double
                }
                // ===========================================

                DB::table('user_logins')->insert([
                    'user_id'    => $user->id,
                    'ip'         => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("LogUserLogin ERROR: ".$e->getMessage());
        }
    }
}
