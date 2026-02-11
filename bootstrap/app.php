<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'payment/*/webhook',
            'api/*',
        ]);

        $middleware->redirectUsersTo(function () {
            $user = Auth::user();
            
            if ($user?->role === 'admin') {
                return route('admin.dashboard');
            }
            if ($user?->role === 'customer') {
                return route('customer.dashboard');
            }
            if ($user?->role === 'staff') {
                return route('staff.dashboard');
            }

            return route('home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('common.post_too_large'),
                ], 413);
            }
            return redirect()->back()
                ->with('error', __('common.post_too_large'));
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('common.page_not_found_message'),
                ], 404);
            }
            $path = trim($request->path(), '/');
            if ($path === 'admin' || str_starts_with($path, 'admin/')) {
                return response()->view('errors.404-admin', [], 404);
            }
            return response()->view('errors.404', [], 404);
        });
    })->create();
