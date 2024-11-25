<?php

use App\Http\Middleware\EnsureTwoFactorIsVerified;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
       $middleware->alias([
           'auth' => Authenticate::class,
           'role' => RoleMiddleware::class,
           'twofactor' => EnsureTwoFactorIsVerified::class,
       ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
