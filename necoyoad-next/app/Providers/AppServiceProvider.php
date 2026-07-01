<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
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
        // Morph map: stores short aliases in the polymorphic type columns
        // (aliasable_type, describable_type, propertiable_type, assignable_type,
        // categorizable_type) instead of fully-qualified class names.
        Relation::enforceMorphMap([
            'product'      => \App\Models\Product::class,
            'post'         => \App\Models\Post::class,
            'category'     => \App\Models\Category::class,
            'manufacturer' => \App\Models\Manufacturer::class,
            'banner'       => \App\Models\Banner::class,
            'banner_item'  => \App\Models\BannerItem::class,
            'menu_link'    => \App\Models\MenuLink::class,
        ]);
    }
}
