<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Banner;
use App\Models\BannerItem;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Base class for all banner events.
 *
 * The banner module is event-driven: every lifecycle stage dispatches an event
 * that other widgets, workflows, or code listeners can subscribe to.
 *
 * Channels:
 *   - 'banners' (public) — for frontend real-time updates via WebSocket
 *   - 'admin.banners.{id}' (private) — for Filament admin live updates
 *
 * @see docs/reports/1782968369_modern_banner_module_3d_canvas_svg_composer.md
 */
abstract class BannerEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Banner $banner,
        public readonly ?BannerItem $slide = null,
        public readonly array $context = [],
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.banners.' . $this->banner->id),
        ];
    }
}
