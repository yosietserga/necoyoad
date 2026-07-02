<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Banner;
use App\Models\BannerItem;

/**
 * Fired after a banner has been rendered (HTML string available).
 *
 * Listeners can use this to:
 *   - Post-process the HTML (e.g., add lazy-loading attributes)
 *   - Log the render for performance metrics
 *   - Cache the rendered HTML
 *   - Send the HTML to a CDN
 */
class BannerRendered extends BannerEvent
{
    public function __construct(
        Banner $banner,
        public readonly string $html,
        public readonly string $engine,
        public readonly float $renderTimeMs,
        ?BannerItem $slide = null,
        array $context = [],
    ) {
        parent::__construct($banner, $slide, $context);
    }
}
