<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(function () {
        //
        // — RUTAS WEB (middleware "web")
        //
        Route::middleware('web')
             ->group(__DIR__.'/../routes/web.php');

        //
        // — RUTAS DE CONSOLA (artisan commands)
        //
        Route::middleware('console')
             ->group(__DIR__.'/../routes/console.php');

        //
        // — RUTAS API (middleware "api", sin CSRF)
        //
        Route::prefix('api')
             ->middleware('api')
             ->group(__DIR__.'/../routes/api.php');

    }, '/up')   // health-check URI
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
