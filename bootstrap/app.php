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
        // Middleware global pour CORS
        $middleware->append(\App\Http\Middleware\CorsMiddleware::class);

        // Middleware de sécurité pour masquer les routes API depuis les navigateurs
        $middleware->prependToGroup('api', \App\Http\Middleware\ApiSecurityMiddleware::class);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'jwt.auth' => \App\Http\Middleware\JwtMiddleware::class,
            'cors' => \App\Http\Middleware\CorsMiddleware::class,
            'api.security' => \App\Http\Middleware\ApiSecurityMiddleware::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
