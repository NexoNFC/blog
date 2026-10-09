<?php

use App\Exceptions\LastActiveAdministratorException;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'active' => EnsureUserIsActive::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (LastActiveAdministratorException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()
                ->withInput()
                ->with('alert', [
                    'type' => 'danger',
                    'title' => 'No se puede completar la operación',
                    'message' => $e->getMessage(),
                ]);
        });

        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'El archivo es demasiado grande para subirlo.',
                ], 413);
            }

            return back()
                ->with('alert', [
                    'type' => 'danger',
                    'title' => 'Archivo demasiado grande',
                    'message' => 'El archivo supera el tamaño máximo permitido. Usa una imagen de hasta 12 MB.',
                ]);
        });

        // TokenMismatchException se convierte a HttpException(419) antes de renderizar.
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tu sesión expiró. Recarga la página e inténtalo de nuevo.',
                ], 419);
            }

            if (! $request->isMethod('POST')) {
                return null;
            }

            $loginAttempt = $request->is('admin/login') || $request->routeIs('login');
            $target = $loginAttempt
                ? route('login')
                : (url()->previous() ?: route('home'));

            return redirect()
                ->to($target)
                ->withInput($request->except(['password', '_token']))
                ->with('alert', [
                    'type' => 'warning',
                    'title' => 'Sesión expirada',
                    'message' => 'Por seguridad, el formulario ya no es válido. Recarga e inténtalo de nuevo.',
                ]);
        });
    })->create();
