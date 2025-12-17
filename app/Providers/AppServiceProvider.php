<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- TAMBAH BARIS INI

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Paksa https kalau APP_URL mengandung ngrok atau lagi production
        if (app()->environment('production') || str_contains(config('app.url'), 'ngrok-free.dev')) {
            URL::forceScheme('http');
        }
    }
}
