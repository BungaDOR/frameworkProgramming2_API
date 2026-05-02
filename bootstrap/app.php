<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
<<<<<<< HEAD
        // Untuk Sanctum API (stateless)
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // Kalau butuh SPA auth (cookie + token)
        ]);
=======
        //
>>>>>>> 881f36ef3aa8a020094eae3537029ad9e6cbca8d
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
