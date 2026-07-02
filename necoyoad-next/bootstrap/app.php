<?php

use App\Exceptions\StorefrontException;
use App\Services\AuditService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\ResolveStoreContext::class);
        $middleware->append(\App\Http\Middleware\ResolveLanguageContext::class);
        // Audit: log all HTTP responses with status outside 200-399
        $middleware->append(\App\Http\Middleware\LogHttpResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Render StorefrontException subclasses as user-friendly error pages
        $exceptions->render(function (StorefrontException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'type' => class_basename($e),
                ], $e->getStatusCode());
            }
            return response()->view('errors.storefront', [
                'message' => $e->getMessage(),
                'status' => $e->getStatusCode(),
                'type' => class_basename($e),
            ], $e->getStatusCode());
        });

        // Report all exceptions to the audit log
        $exceptions->report(function (\Throwable $e) {
            try {
                app(AuditService::class)->logException($e);
            } catch (\Throwable) {
                // Don't let audit logging failure crash the error handler
            }
        });
    })->create();
