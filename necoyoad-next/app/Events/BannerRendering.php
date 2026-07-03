<?php

declare(strict_types=1);

namespace App\Events;

/**
 * Fired when a banner is about to be rendered on the storefront.
 *
 * Listeners can use this to:
 *   - Inject dynamic slides (e.g., from a "featured products" widget)
 *   - Override the engine based on context (e.g., mobile → simpler engine)
 *   - Track impressions for analytics
 *   - A/B test different banner variants
 *
 * Example listener:
 *   Event::listen(BannerRendering::class, function (BannerRendering $e) {
 *       if ($e->banner->name === 'Home Hero') {
 *           $e->addSlide(['image' => 'banners/flash-sale.jpg', 'title' => 'Flash Sale!']);
 *       }
 *   });
 */
class BannerRendering extends BannerEvent
{
    protected array $injectedSlides = [];
    protected ?string $overrideEngine = null;

    public function addSlide(array $slide): void
    {
        $this->injectedSlides[] = $slide;
    }

    public function getInjectedSlides(): array
    {
        return $this->injectedSlides;
    }

    public function overrideEngine(string $engine): void
    {
        $this->overrideEngine = $engine;
    }

    public function getOverrideEngine(): ?string
    {
        return $this->overrideEngine;
    }
}
