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
    ->withMiddleware(function (Middleware $middleware) {
        // Rejestracja middleware dla ról i weryfikacji
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'verified.kyc' => \App\Http\Middleware\EnsureKycIsVerified::class,
            'active.subscription' => \App\Http\Middleware\EnsureSubscriptionIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
