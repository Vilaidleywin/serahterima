<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        file_put_contents(storage_path('logs/test_middleware.txt'), "MASUK\n", FILE_APPEND);

        return $next($request);
    }
}
