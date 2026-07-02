<?php

declare(strict_types=1);

namespace App\View\Components\Widgets;

use App\Models\Banner;
use App\Services\StoreContext;
use App\Services\AssetManifest;
use App\View\Components\WidgetComponent;

/**
 * Banner widget — displays a slider/gallery banner.
 *
 * The most complex widget: reads the jquery_plugin column from the banner
 * and dynamically selects the slider template, JS, and CSS.
 *
 * Also supports per-item widget composition (widgets on individual slides),
 * matching the original Necoyoad's unique feature (v9).
 *
 * @see v3 (banner module — module:settings filter)
 * @see v9 (banner subsystem — jquery_plugin discriminator)
 */
class Banner extends WidgetComponent
{
    public function data(): array
    {
        $storeId = app(StoreContext::class)->id();

        $banner = Banner::with(['items.descriptions'])
            ->where('id', $this->settings['banner_id'] ?? 0)
            ->where('status', true)
            ->where('publish_date_start', '<=', now())
            ->where(function ($q) {
                $q->where('publish_date_end', '>=', now())
                    ->orWhereNull('publish_date_end');
            })
            ->first();

        if (!$banner) {
            return ['banner' => null, 'items' => []];
        }

        // Enqueue slider JS + CSS based on jquery_plugin
        $plugin = $banner->jquery_plugin;
        $manifest = app(AssetManifest::class);

        if (file_exists(public_path("js/sliders/{$plugin}/slider.js"))) {
            $manifest->enqueueAsset("js/sliders/{$plugin}/slider.js");
        }
        if (file_exists(public_path("css/sliders/{$plugin}/slider.css"))) {
            $manifest->enqueueAsset("css/sliders/{$plugin}/slider.css");
        }

        // Load per-item widgets (unique to banners)
        $items = $banner->items->where('status', true)->map(function ($item) {
            $itemData = [
                'image' => $item->image,
                'link' => $item->link,
                'sort_order' => $item->sort_order,
                'title' => $item->getDescription()?->title,
                'description' => $item->getDescription()?->description,
            ];

            // Load per-item widgets (object_type = 'banner_item')
            // This is the per-item widget composition from v9
            $widgetService = app(\App\Services\WidgetService::class);
            $itemWidgets = $widgetService->getTree(
                position: 'main',
                objectType: 'banner_item',
                objectId: $item->id,
                only: true,
            );

            $itemData['widgets'] = $itemWidgets;
            $itemData['offsetX'] = $item->getProperty('settings', 'offsetX') ?? 0;
            $itemData['offsetY'] = $item->getProperty('settings', 'offsetY') ?? 0;

            return $itemData;
        });

        return [
            'banner' => $banner,
            'items' => $items,
            'plugin' => $plugin,
            'pluginConfig' => $banner->params ?? [],
        ];
    }

    /**
     * Override resolveTemplate to use the jquery_plugin as the template name.
     */
    public function resolveTemplate(): string
    {
        $data = $this->data();

        if (!empty($data['plugin'])) {
            $plugin = $data['plugin'];
            $theme = app(StoreContext::class)->setting('config_template', 'choroni');

            // Check active theme
            if (view()->exists("themes.{$theme}.banner.{$plugin}")) {
                return "themes.{$theme}.banner.{$plugin}";
            }

            // Check choroni theme
            if (view()->exists("themes.choroni.banner.{$plugin}")) {
                return "themes.choroni.banner.{$plugin}";
            }

            // Check component default
            if (view()->exists("components.sliders.{$plugin}")) {
                return "components.sliders.{$plugin}";
            }
        }

        // Fallback to nivo-slider
        return 'components.sliders.nivo-slider';
    }
}
