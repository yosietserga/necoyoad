<?php

/**
 * New Necoyoad - Digital Web Agency Platform
 * Built on Laravel 11, preserving the widget engine from the original Necoyoad.
 *
 * Architecture blueprint: docs/architecture/ (12 volumes, 265 pages)
 * Build plan: v12_new_necoyoad_project_plan.pdf
 *
 * Quick start:
 *   composer install
 *   cp .env.example .env
 *   php artisan key:generate
 *   php artisan migrate
 *   php artisan db:seed
 *   php artisan serve
 *
 * The widget engine is the core feature. See:
 *   app/Services/WidgetService.php     — the widget data-access service
 *   app/View/Composers/WidgetComposer.php — the View Composer that populates $widgets
 *   app/View/Components/WidgetComponent.php — the base Blade component for all widgets
 *   app/Traits/Has*.php                — morph traits for the polymorphic object spine
 */

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
    ->withMiddleware(function (Middleware $middleware) {
        // Multi-store resolution: detects store from domain/path/subdomain
        $middleware->append(\App\Http\Middleware\ResolveStoreContext::class);
        // Multi-language resolution: 6-level detection (GET → session → cookie → browser → config)
        $middleware->append(\App\Http\Middleware\ResolveLanguageContext::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
