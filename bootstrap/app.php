<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'require.workspace' => \App\Http\Middleware\RequireActiveWorkspace::class,
            'require.store' => \App\Http\Middleware\RequireActiveStore::class,
            'check.blocked.ip' => \App\Http\Middleware\CheckBlockedIp::class,
            'require.custom.domain' => \App\Http\Middleware\RequireCustomDomain::class,
        ]);
        
        $middleware->web(append: [
            \App\Http\Middleware\DetectCustomDomain::class,
            \App\Http\Middleware\SetActiveStore::class,
        ]);
        
        $middleware->validateCsrfTokens(except: [
            'webhook/*',
            'api/whatsapp/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
