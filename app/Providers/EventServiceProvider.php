<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\LogUserLogin::class,
        ],

        \Illuminate\Auth\Events\Logout::class => [
            \App\Listeners\LogUserLogout::class,
        ],
    ];


    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
