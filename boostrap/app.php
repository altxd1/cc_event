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
        //
    })
    ->withMiddleware(function (Illuminate\Foundation\Configuration\Middleware $middleware) {
        // Alias our custom authentication middlewares. The "login" middleware
        // ensures a user is authenticated and the "admin" middleware
        // enforces administrative privileges. We also register a locale
        // middleware alias to handle language switching based on query
        // parameters or session values.
        $middleware->alias([
            'login'  => \App\Middleware\RequireLogin::class,
            'admin'  => \App\Middleware\RequireAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
