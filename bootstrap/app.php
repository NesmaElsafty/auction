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
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'user' => \App\Http\Middleware\UserMiddleware::class,
            'all' => \App\Http\Middleware\AllMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle authentication exceptions (unauthenticated)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            // Check if it's an API route by multiple methods
            $path = $request->path();
            $fullUrl = $request->fullUrl();
            $isApiRoute = str_starts_with($path, 'api/') || 
                         str_contains($fullUrl, '/api/') ||
                         $request->is('api/*') ||
                         $request->route()?->getPrefix() === 'api' ||
                         $request->expectsJson() || 
                         $request->wantsJson() ||
                         $request->header('Accept') === 'application/json' ||
                         $request->header('Content-Type') === 'application/json';
            
            // Default to JSON for all non-web routes (API-first approach)
            // Only return redirect for explicit web routes
            if ($isApiRoute || !$request->is('web/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
        });
        
        // Handle authorization exceptions (unauthorized - authenticated but no permission)
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, \Illuminate\Http\Request $request) {
            $path = $request->path();
            $fullUrl = $request->fullUrl();
            $isApiRoute = str_starts_with($path, 'api/') || 
                         str_contains($fullUrl, '/api/') ||
                         $request->is('api/*') ||
                         $request->route()?->getPrefix() === 'api' ||
                         $request->expectsJson() || 
                         $request->wantsJson() ||
                         $request->header('Accept') === 'application/json' ||
                         $request->header('Content-Type') === 'application/json';
            
            if ($isApiRoute || !$request->is('web/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Unauthorized. You do not have permission to access this resource.',
                ], 403);
            }
        });
    })->create();

