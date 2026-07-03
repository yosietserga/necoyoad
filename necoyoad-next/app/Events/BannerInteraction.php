<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Banner;

/**
 * Fired when a user interacts with a banner (click, hover, swipe).
 *
 * Listeners can use this to:
 *   - Track CTA clicks for analytics
 *   - Trigger a workflow (e.g., "user clicked banner → add to cart")
 *   - Fire a remarketing pixel
 *   - Log to audit for engagement metrics
 */
class BannerInteraction extends BannerEvent
{
    public function __construct(
        Banner $banner,
        public readonly string $interactionType,
        public readonly ?int $slideId = null,
        public readonly ?string $linkUrl = null,
        public readonly ?int $userId = null,
        array $context = [],
    ) {
        parent::__construct($banner, null, $context);
    }
}
