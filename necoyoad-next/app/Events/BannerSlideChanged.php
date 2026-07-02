<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Banner;

/**
 * Fired when a slide transition occurs (frontend JS dispatches via WebSocket).
 *
 * The browser-side banner engine emits this event when:
 *   - A slide becomes active (transition in complete)
 *   - A slide becomes inactive (transition out complete)
 *   - The user manually navigates to a slide
 *
 * Other widgets can listen to react:
 *   - A "product spotlight" widget syncs to the active banner slide
 *   - A "call to action" widget changes its text to match the slide
 *   - Analytics track which slides get the most engagement
 */
class BannerSlideChanged extends BannerEvent
{
    public function __construct(
        Banner $banner,
        public readonly int $slideIndex,
        public readonly ?int $slideId = null,
        public readonly string $direction = 'next',
        array $context = [],
    ) {
        parent::__construct($banner, null, $context);
    }
}
