<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth', 'admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'setlocale' => \App\Http\Middleware\SetLocale::class,
        ]);

        // Programa "Recomienda y gana": captura ?ref=XXX en sesión + cookie 30 días.
        // Se agrega al grupo web para que corra en todas las páginas públicas.
        // SetLocale también corre en todas las públicas para respetar sesión/cookie
        // aunque la ruta no lleve prefijo /en/ (así la elección persiste al navegar).
        $middleware->web(append: [
            \App\Http\Middleware\CaptureReferral::class,
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Cualquier invitado (a /cuenta o a /admin) va al login unificado.
        $middleware->redirectGuestsTo(fn () => route('login'));

        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
            'epayco/confirmacion',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
