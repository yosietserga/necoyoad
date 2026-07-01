<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\StoreContext;
use App\Services\LanguageContext;
use App\Services\WidgetService;
use App\Services\AssetManifest;
use App\Filters\FilterPipeline;
use App\View\Composers\WidgetComposer;
use Illuminate\Support\ServiceProvider;

class NecoyoadServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // WidgetService depends on StoreContext + LanguageContext
        // (registered in AppServiceProvider)
        $this->app->singleton(WidgetService::class, function ($app) {
            return new WidgetService($app[StoreContext::class], $app[LanguageContext::class]);
        });

        $this->app->singleton(AssetManifest::class);

        // Filter system (Hooks)
        $this->app->singleton('filter', FilterPipeline::class);
    }

    public function boot(): void
    {
        // Register the WidgetComposer for all storefront views
        view()->composer('themes.*', WidgetComposer::class);

        // Register widget assets (the deps.php equivalent)
        $this->registerWidgetAssets();
    }

    /**
     * Register all widget modules' CSS/JS assets.
     * This is the deps.php manifest equivalent.
     */
    private function registerWidgetAssets(): void
    {
        $manifest = app(AssetManifest::class);

        // Core widget modules
        $manifest->registerWidget('rich-text', [
            'css' => ['css/widgets/rich-text.css'],
            'js' => [],
            'routes' => ['*'],
        ]);

        $manifest->registerWidget('product-list', [
            'css' => ['css/widgets/product-list.css'],
            'js' => [],
            'routes' => ['*', 'store/product', 'store/category', 'common/home'],
        ]);

        $manifest->registerWidget('category-list', [
            'css' => ['css/widgets/category-list.css'],
            'js' => [],
            'routes' => ['*', 'store/category', 'common/home'],
        ]);

        $manifest->registerWidget('contact-form', [
            'css' => ['css/widgets/contact-form.css'],
            'js' => ['js/widgets/contact-form.js'],
            'routes' => ['*'],
        ]);

        $manifest->registerWidget('search', [
            'css' => [],
            'js' => ['js/widgets/search.js'],
            'routes' => ['*'],
        ]);

        $manifest->registerWidget('banner', [
            'css' => [],
            'js' => [],
            'routes' => ['*'],
            // Banner assets are loaded dynamically based on jquery_plugin
        ]);
    }
}
