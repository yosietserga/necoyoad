<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\BannerRendered;
use App\Events\BannerRendering;
use App\Exceptions\BannerRenderException;
use App\Models\Banner;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * BannerRendererService — resolves the banner engine + renders + fires events.
 *
 * This is the single entry point for rendering a banner. It:
 *   1. Fires BannerRendering event (listeners can inject slides or override engine)
 *   2. Resolves the engine from EAV (defaults to 'swiper')
 *   3. Loads slide data (with EAV layers + transitions)
 *   4. Renders the engine Blade template
 *   5. Fires BannerRendered event (listeners can post-process HTML)
 *   6. Logs to audit channel
 *
 * The event-driven architecture allows other widgets, workflows, or code
 * listeners to hook into the render lifecycle without modifying this service.
 *
 * @see docs/reports/1782968369_modern_banner_module_3d_canvas_svg_composer.md
 */
class BannerRendererService
{
    /** Engines that have a Blade template + JS module. */
    public const ENGINES = [
        'swiper' => 'Swiper (standard carousel)',
        'gsap-cube' => 'GSAP 3D Cube',
        'gsap-coverflow' => 'GSAP 3D Coverflow',
        'gsap-flip' => 'GSAP 3D Flip',
        'three-distort' => 'Three.js WebGL Distortion',
        'canvas-particles' => 'Canvas 2D Particle Dissolve',
        'svg-morph' => 'SVG Path Morph',
        'ken-burns' => 'Ken Burns Cinematic',
    ];

    public function __construct(
        private readonly EavService $eav,
        private readonly AuditService $audit,
    ) {}

    /**
     * Get the engine name for a banner (EAV property, defaults to 'swiper').
     */
    public function getEngine(Banner $banner): string
    {
        return $this->eav->get($banner, 'banner', 'engine') ?? 'swiper';
    }

    /**
     * Get all banner config (EAV properties in the 'banner' group).
     */
    public function getConfig(Banner $banner): array
    {
        $defaults = [
            'engine' => 'swiper',
            'autoplay' => true,
            'autoplay_speed' => 5000,
            'transition_speed' => 800,
            'loop' => true,
            'show_navigation' => true,
            'show_pagination' => true,
            'parallax_depth' => 0,
            'ken_burns_intensity' => 50,
        ];

        return array_merge($defaults, $this->eav->getGroup($banner, 'banner'));
    }

    /**
     * Get all slides with their layers + transitions resolved from EAV.
     *
     * @return array<int, array>
     */
    public function getSlides(Banner $banner): array
    {
        return $banner->items()
            ->where('status', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($item) {
                $slideEav = $this->eav->getGroup($item, 'slide');
                return [
                    'id' => $item->id,
                    'title' => $item->getTitle(),
                    'image' => $item->image,
                    'link' => $slideEav['link_url'] ?? null,
                    'link_target' => $slideEav['link_target'] ?? '_self',
                    'background_type' => $slideEav['background_type'] ?? 'image',
                    'background_video' => $slideEav['background_video_url'] ?? null,
                    'background_gradient' => $slideEav['background_gradient'] ?? null,
                    'transition_in' => $slideEav['transition_in'] ?? 'fade',
                    'transition_out' => $slideEav['transition_out'] ?? 'fade',
                    'ken_burns' => $slideEav['ken_burns'] ?? 'none',
                    'layers' => is_string($slideEav['layers'] ?? null)
                        ? json_decode($slideEav['layers'], true) ?? []
                        : ($slideEav['layers'] ?? []),
                ];
            })->toArray();
    }

    /**
     * Render a banner by resolving its engine + delegating to the engine blade.
     * Fires BannerRendering before render + BannerRendered after.
     */
    public function render(Banner $banner): string
    {
        $startTime = microtime(true);

        try {
            // 1. Fire BannerRendering event — listeners can inject slides or override engine
            $renderingEvent = new BannerRendering($banner);
            Event::dispatch($renderingEvent);

            // 2. Resolve engine (event can override)
            $engine = $renderingEvent->getOverrideEngine() ?? $this->getEngine($banner);

            // Validate engine exists
            if (!array_key_exists($engine, self::ENGINES)) {
                Log::channel('audit')->warning('Unknown banner engine, falling back to swiper', [
                    'banner_id' => $banner->id,
                    'requested_engine' => $engine,
                ]);
                $engine = 'swiper';
            }

            // 3. Load config + slides (merge injected slides)
            $config = $this->getConfig($banner);
            $slides = $this->getSlides($banner);

            // Merge listener-injected slides
            foreach ($renderingEvent->getInjectedSlides() as $injectedSlide) {
                $slides[] = $injectedSlide;
            }

            if (empty($slides)) {
                throw new BannerRenderException($banner->name ?? "ID:{$banner->id}", 'no slides available');
            }

            // 4. Render the engine Blade template
            $viewName = "components.banners.engines.{$engine}";
            if (!view()->exists($viewName)) {
                // Fallback to swiper if the engine view doesn't exist yet
                Log::channel('audit')->warning('Banner engine view not found, falling back to swiper', [
                    'banner_id' => $banner->id,
                    'engine' => $engine,
                ]);
                $viewName = 'components.banners.engines.swiper';
                $engine = 'swiper';
            }

            $html = view($viewName, [
                'banner' => $banner,
                'config' => $config,
                'slides' => $slides,
                'engine' => $engine,
            ])->render();

            $renderTimeMs = (microtime(true) - $startTime) * 1000;

            // 5. Fire BannerRendered event — listeners can post-process HTML
            Event::dispatch(new BannerRendered(
                banner: $banner,
                html: $html,
                engine: $engine,
                renderTimeMs: $renderTimeMs,
                context: ['slide_count' => count($slides)],
            ));

            // 6. Audit log
            $this->audit->logModel(
                event: 'banner_rendered',
                modelClass: Banner::class,
                modelId: $banner->id,
                changes: [
                    'engine' => $engine,
                    'slide_count' => count($slides),
                    'render_time_ms' => round($renderTimeMs, 2),
                ],
            );

            return $html;

        } catch (BannerRenderException $e) {
            $this->audit->logException($e, ['banner_id' => $banner->id]);
            throw;
        } catch (\Throwable $e) {
            $this->audit->logException($e, ['banner_id' => $banner->id]);
            throw new BannerRenderException($banner->name ?? "ID:{$banner->id}", $e->getMessage(), 0, $e);
        }
    }
}
