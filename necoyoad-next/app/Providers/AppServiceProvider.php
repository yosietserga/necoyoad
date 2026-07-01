<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * AppServiceProvider — base application service provider.
 * Registers the StoreContext and LanguageContext as singletons
 * before the middleware runs.
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Pre-bind StoreContext so middleware can resolve it
        $this->app->singleton(\App\Services\StoreContext::class, function ($app) {
            return new \App\Services\StoreContext($app['request']);
        });

        $this->app->singleton(\App\Services\LanguageContext::class, function ($app) {
            return new \App\Services\LanguageContext($app['request'], $app[\App\Services\StoreContext::class]);
        });
    }

    public function boot(): void
    {
        //
    }
}
