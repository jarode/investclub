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
        // Middleware dla obsługi języka musi być w grupie web, NIE jako globalne middleware
        $middleware->web(append: [\App\Http\Middleware\SetLocale::class]);
        
        // Rejestracja middleware dla ról i weryfikacji
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'verified.kyc' => \App\Http\Middleware\EnsureKycIsVerified::class,
            'active.subscription' => \App\Http\Middleware\EnsureSubscriptionIsActive::class,
            'subscription.plan' => \App\Http\Middleware\CheckSubscriptionPlan::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
