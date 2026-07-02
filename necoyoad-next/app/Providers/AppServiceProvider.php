<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\AuditService;
use App\Services\EavService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // EavService — centralized EAV operations (user mandate: "Always use
        // EAV service to add or alter data scheme, instead of change DB scheme")
        $this->app->singleton(EavService::class);

        // AuditService — centralized audit logging (user mandate: "all DB queries,
        // API requests with response != 200-399 and exec process in backend with
        // errors must be listened and logged for audit")
        $this->app->singleton(AuditService::class);
    }

    public function boot(): void
    {
        // Morph map: stores short aliases in the polymorphic type columns
        // (aliasable_type, describable_type, propertiable_type, assignable_type,
        // categorizable_type) instead of fully-qualified class names.
        Relation::enforceMorphMap([
            'product'      => \App\Models\Product::class,
            'post'         => \App\Models\Post::class,
            'page'         => \App\Models\Post::class, // Post model handles both post + page types
            'category'     => \App\Models\Category::class,
            'manufacturer' => \App\Models\Manufacturer::class,
            'banner'       => \App\Models\Banner::class,
            'banner_item'  => \App\Models\BannerItem::class,
            'menu_link'    => \App\Models\MenuLink::class,
        ]);

        // Audit: listen to all DB queries and log slow ones (>100ms) or all
        // if AUDIT_ALL_QUERIES=true. Implements the user mandate for DB query auditing.
        $auditService = $this->app->make(AuditService::class);
        DB::listen(function (QueryExecuted $query) use ($auditService) {
            $auditService->logQuery($query);
        });
    }
}
