<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\BannerInteraction;
use App\Events\BannerSlideChanged;
use App\Models\Banner;
use Illuminate\Support\Facades\Event;

/**
 * BannerEventService — dispatches banner events triggered by the frontend.
 *
 * The browser-side banner engines (Swiper, GSAP, Three.js) dispatch events
 * via AJAX/WebSocket when slides change or users interact. This service
 * receives those dispatches and fires the corresponding Laravel events so
 * backend listeners (widgets, workflows, analytics, audit) can react.
 *
 * Frontend → POST /api/banner/event → BannerEventService::dispatch()
 *
 * @see docs/reports/1782968369_modern_banner_module_3d_canvas_svg_composer.md
 */
class BannerEventService
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    /**
     * Dispatch a slide-changed event from the frontend.
     */
    public function dispatchSlideChanged(int $bannerId, int $slideIndex, ?int $slideId, string $direction): void
    {
        $banner = Banner::find($bannerId);
        if (!$banner) {
            return;
        }

        Event::dispatch(new BannerSlideChanged(
            banner: $banner,
            slideIndex: $slideIndex,
            slideId: $slideId,
            direction: $direction,
        ));

        $this->audit->logModel(
            event: 'banner_slide_changed',
            modelClass: Banner::class,
            modelId: $bannerId,
            changes: [
                'slide_index' => $slideIndex,
                'slide_id' => $slideId,
                'direction' => $direction,
            ],
        );
    }

    /**
     * Dispatch a user-interaction event from the frontend (click, hover, swipe).
     */
    public function dispatchInteraction(int $bannerId, string $interactionType, ?int $slideId, ?string $linkUrl, ?int $userId): void
    {
        $banner = Banner::find($bannerId);
        if (!$banner) {
            return;
        }

        Event::dispatch(new BannerInteraction(
            banner: $banner,
            interactionType: $interactionType,
            slideId: $slideId,
            linkUrl: $linkUrl,
            userId: $userId,
        ));

        $this->audit->logModel(
            event: 'banner_interaction',
            modelClass: Banner::class,
            modelId: $bannerId,
            changes: [
                'interaction' => $interactionType,
                'slide_id' => $slideId,
                'link_url' => $linkUrl,
                'user_id' => $userId,
            ],
        );
    }
}
