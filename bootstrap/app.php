<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SuperAdminMiddleware;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
         $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'superadmin' => SuperAdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (\Throwable $e, $request) {
            if ($request->expectsJson() || $request->wantsJson() || $request->headers->has('X-Livewire')) {
                return null;
            }

            if (app()->environment('local') && config('app.debug')) {
                return null;
            }

            if (
                $e instanceof \Illuminate\Validation\ValidationException
                || $e instanceof \Illuminate\Auth\AuthenticationException
                || $e instanceof \Illuminate\Auth\Access\AuthorizationException
                || $e instanceof \Illuminate\Http\Exceptions\HttpResponseException
                || $e instanceof \Illuminate\Session\TokenMismatchException
            ) {
                return null;
            }

            $status = 500;
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $status = $e->getStatusCode();
            } elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                $status = 404;
            }

            return response()->view('errors.simple', [
                'status' => $status,
                'message' => $status === 404
                    ? 'No encontramos la página solicitada.'
                    : 'Ocurrió un error. Por favor intentá de nuevo.',
            ], $status);
        });
    })->create();

    if (! function_exists('is_superadmin')) {
    function is_superadmin(): bool
    {
        return Auth::check() && Auth::user()->role === 'superadmin';
    }
}

if (! function_exists('is_admin')) {
    function is_admin(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin']);
    }
}
