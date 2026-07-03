<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Models\Product;
use App\Services\LanguageContext;
use Livewire\Component;

/**
 * ProductPage — reactive product detail page.
 *
 * Uses Livewire 3 for: add to cart, quantity selector,
 * option selection, and reactive price updates.
 */
class ProductPage extends Component
{
    public Product $product;
    public int $quantity = 1;
    public array $selectedOptions = [];
    public float $currentPrice;

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->currentPrice = (float) $product->price;
    }

    public function addToCart(): void
    {
        $this->dispatch('addToCart', productId: $this->product->id, quantity: $this->quantity)->to(CartDrawer::class);
    }

    public function incrementQty(): void
    {
        $this->quantity++;
    }

    public function decrementQty(): void
    {
        if ($this->quantity > 1) $this->quantity--;
    }

    public function render()
    {
        $langId = app(LanguageContext::class)->id();
        $description = $this->product->getDescription($langId);

        return view('livewire.storefront.product-page', [
            'title' => $description?->title ?? $this->product->sku,
            'description' => $description?->description ?? '',
        ]);
    }
}
