<?php

declare(strict_types=1);

namespace App\View\Components\Widgets;

use App\Models\Banner as BannerModel;
use App\Services\StoreContext;
use App\Services\AssetManifest;
use App\View\Components\WidgetComponent;

/**
 * Banner widget — displays a slider/gallery banner.
 *
 * Uses 'BannerModel' alias for App\Models\Banner to avoid class name collision
 * with this widget component class (also named 'Banner').
 *
 * @see v3 (banner module — module:settings filter)
 * @see v9 (banner subsystem — jquery_plugin discriminator)
 */
class Banner extends WidgetComponent
{
    public function data(): array
    {
        $storeId = app(StoreContext::class)->id();

        $banner = BannerModel::with(['items.descriptions'])
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
            'config' => app(\App\Services\BannerRendererService::class)->getConfig($banner),
            'slides' => app(\App\Services\BannerRendererService::class)->getSlides($banner),
            'engine' => app(\App\Services\BannerRendererService::class)->getEngine($banner),
        ];
    }

    public function resolveTemplate(): string
    {
        $data = $this->data();
        $banner = $data['banner'] ?? null;

        if ($banner) {
            $renderer = app(\App\Services\BannerRendererService::class);
            $engine = $renderer->getEngine($banner);
            $hasEngineEav = app(\App\Services\EavService::class)->get($banner, 'banner', 'engine');
            if ($hasEngineEav) {
                $engineView = "components.banners.engines.{$engine}";
                if (view()->exists($engineView)) {
                    return $engineView;
                }
            }
        }

        if (!empty($data['plugin'])) {
            $plugin = $data['plugin'];
            $theme = app(StoreContext::class)->setting('config_template', 'choroni');

            if (view()->exists("themes.{$theme}.banner.{$plugin}")) {
                return "themes.{$theme}.banner.{$plugin}";
            }
            if (view()->exists("themes.choroni.banner.{$plugin}")) {
                return "themes.choroni.banner.{$plugin}";
            }
            if (view()->exists("components.sliders.{$plugin}")) {
                return "components.sliders.{$plugin}";
            }
        }

        if (view()->exists('components.banners.engines.swiper')) {
            return 'components.banners.engines.swiper';
        }

        return 'components.sliders.nivo-slider';
    }
}
