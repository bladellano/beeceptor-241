<?php

use App\Http\Controllers\MockGatewayController;
use App\Http\Middleware\MockRateLimit;
use App\Http\Middleware\ValidateMockRequestSize;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Fora do grupo web: o caller externo não tem token CSRF.
            // Registrado depois das rotas web para não capturar /admin e /login.
            Route::middleware(['mock.public'])
                ->any('{slug}/{path?}', [MockGatewayController::class, 'handle'])
                ->where('slug', '[a-z0-9-]+')
                ->where('path', '.*');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->group('mock.public', [
            ValidateMockRequestSize::class,
            MockRateLimit::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
