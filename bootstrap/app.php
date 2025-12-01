<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// tambahkan use untuk middleware custom kamu
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\CheckSingleSession;
use Illuminate\Session\Middleware\AuthenticateSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // alias middleware yang bisa dipakai di routes
        $middleware->alias([
            'role'           => RoleMiddleware::class,
            'single.session' => CheckSingleSession::class,
            'auth.session'   => AuthenticateSession::class,
        ]);

        // OPTIONAL: kalau mau AuthenticateSession selalu jalan di semua route "web"
        // tinggal uncomment ini:
        /*
        $middleware->web(append: [
            AuthenticateSession::class,
        ]);
        */
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
