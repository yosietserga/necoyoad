<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use App\Services\AssetManifest;

/**
 * WidgetComponent — the base Blade component for all widget modules.
 *
 * This is the Module base class equivalent from the original Necoyoad (v3).
 * Each widget module extends this class and implements the data() method
 * to inject its data (replacing the module:settings filter).
 *
 * The constructor auto-loads the widget's CSS/JS assets via AssetManifest
 * (replacing the deps.php manifest from the original).
 *
 * Usage:
 *   class Banner extends WidgetComponent {
 *       public function data(): array {
 *           return ['banner' => Banner::find($this->settings['banner_id'])];
 *       }
 *   }
 *
 * @see v3 (Module base class)
 * @see v11 (widget engine preservation — Abstraction 5)
 * @see v12 (creating a new widget module guide)
 */
abstract class WidgetComponent extends Component
{
    public string $widgetName;
    public string $position;
    public array $settings;

    public function __construct(
        array $settings = [],
        ?string $widgetName = null,
        ?string $position = null
    ) {
        $this->settings = $settings;
        $this->widgetName = $widgetName ?? $settings['name'] ?? 'widget';
        $this->position = $position ?? 'main';

        // Auto-load this widget's assets (deps.php equivalent)
        app(AssetManifest::class)->loadForWidget(static::class);
    }

    /**
     * The injection seam — each widget module overrides this to load its data.
     * Replaces the module:settings filter from the original.
     *
     * @return array Data to pass to the Blade template
     */
    abstract public function data(): array;

    /**
     * Resolve the template to render.
     *
     * 3-level resolution (same as original, v8):
     *   1. Per-entity template override (settings['template'])
     *   2. Config default (config("defaults.{$this->moduleName}"))
     *   3. Hardcoded fallback (component default view)
     */
    public function resolveTemplate(): string
    {
        $moduleName = $this->moduleName();

        // 1. Per-entity override
        $template = $this->settings['template'] ?? null;

        // 2. Config default
        if (!$template) {
            $template = config("defaults.{$moduleName}");
        }

        // 3. Check if the template exists in the active theme
        $theme = app('store.context')->setting('config_template', 'choroni');
        if ($template && view()->exists("themes.{$theme}.{$template}")) {
            return "themes.{$theme}.{$template}";
        }

        // 4. Fallback to choroni theme
        if ($template && view()->exists("themes.choroni.{$template}")) {
            return "themes.choroni.{$template}";
        }

        // 5. Hardcoded fallback (the component's default view)
        return "components.widgets.{$moduleName}";
    }

    public function render()
    {
        return view($this->resolveTemplate(), array_merge(
            $this->data(),
            [
                'widgetName' => $this->widgetName,
                'position' => $this->position,
                'settings' => $this->settings,
            ]
        ));
    }

    /**
     * Get the module name from the class name.
     * e.g. "App\View\Components\Widgets\Banner" → "banner"
     */
    protected function moduleName(): string
    {
        $className = class_basename(static::class);
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $className));
    }
}
