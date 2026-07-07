<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        apiPrefix: 'api',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'is_admin'       => \App\Http\Middleware\IsAdmin::class,
            'cliente.guest'  => \App\Http\Middleware\RedirectIfClienteAuthenticated::class,
            'auth.zona'      => \App\Http\Middleware\IsZonaUser::class,
            'mantenimiento'  => \App\Http\Middleware\CheckMantenimiento::class,
            'erp.api_key'    => \App\Http\Middleware\EnsureErpApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
