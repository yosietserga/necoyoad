<?php

declare(strict_types=1);

namespace App\Services;

/**
 * AssetManifest — the deps.php manifest equivalent.
 *
 * Reads the Vite manifest and loads matching CSS/JS for the current
 * route + widget modules. Replaces the deps.php manifest from the
 * original Necoyoad (v3).
 *
 * Widget modules register their assets in WidgetServiceProvider::boot().
 * The AssetManifest service loads matching assets for the current route
 * and for each widget module that renders on the page.
 *
 * @see v3 (deps.php manifest)
 * @see v11 (widget engine preservation — Abstraction 6)
 */
class AssetManifest
{
    private array $widgets = [];
    private array $loaded = [];

    /**
     * Register a widget's CSS/JS assets.
     */
    public function registerWidget(string $widgetName, array $assets): void
    {
        $this->widgets[$widgetName] = $assets;
    }

    /**
     * Load assets for a widget module (called by WidgetComponent constructor).
     */
    public function loadForWidget(string $widgetClass): void
    {
        $widgetName = $this->extractWidgetName($widgetClass);

        if (!isset($this->widgets[$widgetName]) || in_array($widgetName, $this->loaded)) {
            return;
        }

        $assets = $this->widgets[$widgetName];

        // Load CSS
        foreach ($assets['css'] ?? [] as $css) {
            $this->enqueueCss($css, $assets['routes'] ?? ['*']);
        }

        // Load JS
        foreach ($assets['js'] ?? [] as $js) {
            $this->enqueueJs($js, $assets['routes'] ?? ['*']);
        }

        $this->loaded[] = $widgetName;
    }

    /**
     * Load assets for the current route (called by the middleware).
     */
    public function loadForRoute(string $route): void
    {
        foreach ($this->widgets as $widgetName => $assets) {
            $routes = $assets['routes'] ?? ['*'];
            if ($routes === '*' || in_array($route, (array) $routes)) {
                if (!in_array($widgetName, $this->loaded)) {
                    foreach ($assets['css'] ?? [] as $css) {
                        $this->enqueueCss($css);
                    }
                    foreach ($assets['js'] ?? [] as $js) {
                        $this->enqueueJs($js);
                    }
                    $this->loaded[] = $widgetName;
                }
            }
        }
    }

    private function enqueueCss(string $path, array $routes = ['*']): void
    {
        // In production, this would use Vite's manifest to resolve the path
        // For now, push to a shared collection
        if (!collect(app('view')->shared('styles', []))->contains('href', $path)) {
            app('view')->share('styles', array_merge(
                app('view')->shared('styles', []),
                [['href' => $path, 'media' => 'all']]
            ));
        }
    }

    private function enqueueJs(string $path, array $routes = ['*']): void
    {
        if (!collect(app('view')->shared('javascripts', []))->contains($path)) {
            app('view')->share('javascripts', array_merge(
                app('view')->shared('javascripts', []),
                [$path]
            ));
        }
    }

    private function extractWidgetName(string $class): string
    {
        $basename = class_basename($class);
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $basename));
    }
}
