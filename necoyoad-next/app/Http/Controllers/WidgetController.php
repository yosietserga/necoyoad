<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Widget;
use App\Services\AssetManifest;
use App\Services\StoreContext;
use App\Services\LanguageContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\ViewException;

/**
 * WidgetController — handles async widget rendering (v3 §8).
 *
 * The async endpoint allows widgets to be loaded via AJAX after the initial
 * page render, improving perceived performance for heavy widgets (e.g.,
 * product lists that require complex queries).
 *
 * Frontend usage:
 *   <div data-async="1" data-widget-name="featured_products"
 *        data-position="main" data-settings='{"limit":4}'>
 *     Loading...
 *   </div>
 *   <script>
 *     fetch('/widget/async/featured_products?position=main&settings=...')
 *       .then(r => r.text())
 *       .then(html => div.innerHTML = html);
 *   </script>
 *
 * @see v3 §8 (Async widget rendering — ?r=module/<name>/async&w=<widget_name>)
 */
class WidgetController extends Controller
{
    public function __construct(
        private readonly StoreContext $storeContext,
        private readonly LanguageContext $languageContext,
        private readonly AssetManifest $assetManifest,
    ) {}

    /**
     * Render a single widget by name and return its HTML.
     *
     * @param string $name The widget name (e.g., 'featured_products', 'hero_banner')
     */
    public function async(Request $request, string $name): Response|JsonResponse
    {
        try {
            $position = $request->query('position', 'main');
            $settingsJson = $request->query('settings', '{}');
            $settings = json_decode($settingsJson, true) ?? [];

            // Resolve the widget component class from the name
            // Widget names use kebab-case: 'featured_products' → 'FeaturedProducts'
            $componentClass = $this->resolveWidgetComponent($name);

            if (!$componentClass) {
                Log::channel('widget')->warning('Async widget not found', [
                    'widget_name' => $name,
                    'position' => $position,
                ]);

                return response()->json([
                    'error' => "Widget '{$name}' not found",
                ], 404);
            }

            // Render the widget component to HTML
            $component = app($componentClass, [
                'settings' => $settings,
                'widgetName' => $name,
                'position' => $position,
            ]);

            $html = $component->render()->with([
                'settings' => $settings,
                'widgetName' => $name,
                'position' => $position,
            ])->render();

            // Also return the enqueued assets so the frontend can inject them
            $styles = app('view')->shared('styles', []);
            $javascripts = app('view')->shared('javascripts', []);

            return response($html, 200)->header('Content-Type', 'text/html')
                ->header('X-Widget-Styles', json_encode(array_column($styles, 'href')))
                ->header('X-Widget-Scripts', json_encode($javascripts));

        } catch (ViewException $e) {
            Log::channel('widget')->error('Async widget render failed', [
                'widget_name' => $name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Widget render failed',
                'message' => $e->getMessage(),
            ], 500);

        } catch (\Throwable $e) {
            Log::channel('widget')->error('Async widget exception', [
                'widget_name' => $name,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Internal error',
            ], 500);
        }
    }

    /**
     * Resolve a widget name to its Blade component class.
     * Looks up the Widget record by name to get its 'module' column,
     * then maps the module to the component class (e.g., 'banner' → Banner,
     * 'product-list' → ProductList, 'rich-text' → RichText).
     */
    private function resolveWidgetComponent(string $name): ?string
    {
        // First try: look up the Widget record by name → use module column
        $widget = \App\Models\Widget::where('name', $name)->first();
        if ($widget) {
            $module = $widget->module;
            // Convert module slug to StudlyCase class name: 'product-list' → 'ProductList'
            $studly = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $module)));
            $class = "App\\View\\Components\\Widgets\\{$studly}";
            if (class_exists($class)) {
                return $class;
            }
        }

        // Fallback: try direct name → class conversion (for widget names that
        // happen to match the class basename, e.g., 'search' → Search)
        $studly = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $name)));
        $class = "App\\View\\Components\\Widgets\\{$studly}";

        return class_exists($class) ? $class : null;
    }
}
