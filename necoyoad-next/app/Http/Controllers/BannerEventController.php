<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\BannerEventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * BannerEventController — receives banner events from the frontend.
 *
 * The browser-side banner engines dispatch events here when:
 *   - A slide changes (transition complete)
 *   - A user clicks/hovers/swipes
 *
 * These are forwarded to BannerEventService which fires Laravel events
 * so backend listeners (widgets, workflows, analytics, audit) can react.
 */
class BannerEventController extends Controller
{
    public function __construct(
        private readonly BannerEventService $bannerEventService,
    ) {}

    public function slideChanged(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'banner_id' => 'required|integer|exists:banners,id',
            'slide_index' => 'required|integer|min:0',
            'slide_id' => 'nullable|integer',
            'direction' => 'nullable|string|in:next,prev,manual',
        ]);

        $this->bannerEventService->dispatchSlideChanged(
            bannerId: $validated['banner_id'],
            slideIndex: $validated['slide_index'],
            slideId: $validated['slide_id'] ?? null,
            direction: $validated['direction'] ?? 'next',
        );

        return response()->json(['success' => true]);
    }

    public function interaction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'banner_id' => 'required|integer|exists:banners,id',
            'interaction_type' => 'required|string|in:click,hover,swipe,cta_click',
            'slide_id' => 'nullable|integer',
            'link_url' => 'nullable|string|max:500',
        ]);

        $this->bannerEventService->dispatchInteraction(
            bannerId: $validated['banner_id'],
            interactionType: $validated['interaction_type'],
            slideId: $validated['slide_id'] ?? null,
            linkUrl: $validated['link_url'] ?? null,
            userId: auth('customer')->id() ?? auth('web')->id(),
        );

        return response()->json(['success' => true]);
    }
}
