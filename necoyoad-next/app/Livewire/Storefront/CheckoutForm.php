<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Models\Customer;
use App\Services\StoreContext;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

/**
 * CheckoutForm — single-page checkout (Livewire 3).
 *
 * Steps: Shipping → Payment → Confirm → Success
 * All in one reactive Livewire component (no page reloads).
 *
 * Creates the order with snapshot of cart + customer + address.
 */
class CheckoutForm extends Component
{
    public int $step = 1;
    public array $shipping = [];
    public array $payment = [];
    public string $paymentMethod = 'bank_transfer';
    public bool $termsAccepted = false;
    public ?int $orderId = null;

    public function nextStep(): void
    {
        $this->validateStep();
        $this->step++;
    }

    public function previousStep(): void
    {
        $this->step--;
    }

    public function placeOrder(): void
    {
        $this->validate([
            'termsAccepted' => 'accepted',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            $this->dispatch('notify', type: 'error', message: 'Cart is empty.');
            return;
        }

        $storeId = app(StoreContext::class)->id();

        // Create order with snapshot
        $order = \App\Models\Order::create([
            'store_id' => $storeId,
            'customer_id' => auth('customer')->id(),
            'firstname' => $this->shipping['firstname'] ?? '',
            'lastname' => $this->shipping['lastname'] ?? '',
            'email' => $this->shipping['email'] ?? '',
            'telephone' => $this->shipping['telephone'] ?? '',
            'shipping_address' => $this->shipping,
            'payment_address' => $this->payment,
            'shipping_method' => $this->shipping['method'] ?? 'Standard',
            'payment_method' => $this->paymentMethod,
            'total' => collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']),
            'order_status_id' => 1, // Pending
            'language_id' => app('language.context')->id(),
        ]);

        // Create order items (snapshot)
        foreach ($cart as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity'],
                'quantity' => $item['quantity'],
            ]);
        }

        // Create order totals
        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $order->totals()->createMany([
            ['title' => 'Sub-Total', 'value' => $subtotal, 'sort_order' => 1],
            ['title' => 'Total', 'value' => $subtotal, 'sort_order' => 9],
        ]);

        // Decrement stock
        foreach ($cart as $item) {
            \App\Models\Product::where('id', $item['product_id'])
                ->where('subtract', true)
                ->decrement('quantity', $item['quantity']);
        }

        // Clear cart
        session()->forget('cart');

        $this->orderId = $order->id;
        $this->step = 4; // Success
    }

    private function validateStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'shipping.firstname' => 'required',
                'shipping.lastname' => 'required',
                'shipping.email' => 'required|email',
                'shipping.address_1' => 'required',
                'shipping.city' => 'required',
                'shipping.postcode' => 'required',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.storefront.checkout-form');
    }
}
