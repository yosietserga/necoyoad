<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Models\Product;
use App\Services\StoreContext;
use App\Services\LanguageContext;
use Livewire\Component;
use Illuminate\Support\Collection;

/**
 * CartDrawer — reactive shopping cart component.
 *
 * Uses Livewire 3 for reactivity (no page reload needed).
 * Cart contents are persisted in session (for guests) and
 * optionally in the database (for logged-in customers).
 *
 * Features:
 *   - Add to cart (with quantity + options)
 *   - Update quantity
 *   - Remove item
 *   - Clear cart
 *   - Cart total + item count
 *   - Slide-out drawer UI
 *
 * @see v2 (cart persisted in file cache + customer.cart column + session)
 */
class CartDrawer extends Component
{
    public array $cart = [];
    public int $itemCount = 0;
    public string $total = '0.00';
    public bool $open = false;

    protected $listeners = [
        'addToCart' => 'add',
        'removeFromCart' => 'remove',
        'updateQuantity' => 'updateQty',
        'clearCart' => 'clear',
        'openCart' => 'openDrawer',
    ];

    public function mount(): void
    {
        $this->loadCart();
    }

    public function add(int $productId, int $quantity = 1): void
    {
        $product = Product::with(['descriptions' => function ($q) {
            $q->where('language_id', app(LanguageContext::class)->id());
        }])->find($productId);

        if (!$product || !$product->status) {
            $this->dispatch('notify', type: 'error', message: 'Product not available.');
            return;
        }

        $key = (string) $productId;

        if (isset($this->cart[$key])) {
            $this->cart[$key]['quantity'] += $quantity;
        } else {
            $this->cart[$key] = [
                'product_id' => $productId,
                'name' => $product->getTitle() ?? $product->sku,
                'price' => (float) $product->price,
                'quantity' => $quantity,
                'image' => $product->image,
            ];
        }

        $this->saveCart();
        $this->openDrawer();
        $this->dispatch('notify', type: 'success', message: 'Added to cart!');
    }

    public function updateQty(int $productId, int $quantity): void
    {
        $key = (string) $productId;

        if (!isset($this->cart[$key])) return;

        if ($quantity <= 0) {
            $this->remove($productId);
            return;
        }

        $this->cart[$key]['quantity'] = $quantity;
        $this->saveCart();
    }

    public function remove(int $productId): void
    {
        $key = (string) $productId;
        unset($this->cart[$key]);
        $this->saveCart();
    }

    public function clear(): void
    {
        $this->cart = [];
        $this->saveCart();
    }

    public function openDrawer(): void
    {
        $this->open = true;
    }

    public function closeDrawer(): void
    {
        $this->open = false;
    }

    private function loadCart(): void
    {
        $this->cart = session()->get('cart', []);
        $this->calculateTotals();
    }

    private function saveCart(): void
    {
        session()->put('cart', $this->cart);
        $this->calculateTotals();
    }

    private function calculateTotals(): void
    {
        $this->itemCount = collect($this->cart)->sum('quantity');
        $this->total = number_format(
            collect($this->cart)->sum(fn ($item) => $item['price'] * $item['quantity']),
            2
        );
    }

    public function render()
    {
        return view('livewire.storefront.cart-drawer');
    }
}
