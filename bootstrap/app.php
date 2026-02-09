<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SuperAdminMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;

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
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            if ($request->is('login', 'logout', 'register', 'admin/login', 'admin/logout', 'admin/register')) {
                return null;
            }

            if ($e instanceof AuthenticationException) {
                $loginRoute = $request->is('admin/*') ? '/admin/login' : route('login');

                return redirect()->to($loginRoute);
            }

            if ($e instanceof TokenMismatchException) {
                $loginRoute = $request->is('admin/*') ? '/admin/login' : route('login');

                return redirect()->to($loginRoute)
                    ->with('error', 'Tu sesión expiró. Volvé a iniciar sesión.');
            }

            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

            $messages = [
                401 => 'No estás autenticado para acceder a esta sección.',
                403 => 'No tenés permisos para acceder a esta sección.',
                404 => 'No encontramos la página que buscás.',
                419 => 'Tu sesión expiró. Volvé a intentar.',
                429 => 'Demasiadas solicitudes. Esperá un momento y volvé a intentar.',
                500 => 'Ocurrió un error interno.',
                502 => 'Error de puerta de enlace. Intentalo más tarde.',
                503 => 'El sistema está en mantenimiento. Intentalo en unos minutos.',
            ];

            $message = $messages[$status] ?? 'Ocurrió un error.';

            return response()->view('errors.simple', [
                'status' => $status,
                'message' => $message,
            ], $status);
        });
    })->create();
