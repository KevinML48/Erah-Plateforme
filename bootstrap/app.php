<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\EnsureAdminRole;
use App\Http\Middleware\EnsureSupporterActive;
use App\Http\Middleware\ApplyPlatformSecurityHeaders;
use App\Http\Middleware\LocalOnly;
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
        $middleware->web(append: [
            HandleInertiaRequests::class,
            ApplyPlatformSecurityHeaders::class,
        ]);

        $middleware->api(append: [
            ApplyPlatformSecurityHeaders::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login', [
            'required' => 'participation',
        ]));

        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);

        $middleware->alias([
            'admin' => EnsureAdminRole::class,
            'supporter.active' => EnsureSupporterActive::class,
            'local.only' => LocalOnly::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
