{{--
    New Necoyoad — Cart Drawer (Livewire 3)
    Slide-out cart that updates reactively without page reloads.
--}}
<div x-data="{ open: @entangle('open') }"
     x-cloak
     @cart-open.window="open = true"
     @keydown.escape.window="open = false">

    {{-- Trigger button (shows item count) --}}
    <button @click="$dispatch('cart-open')" class="cart-toggle relative">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        @if ($itemCount > 0)
            <span class="cart-badge absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                {{ $itemCount }}
            </span>
        @endif
    </button>

    {{-- Slide-out drawer --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-full"
         class="fixed top-0 right-0 h-full w-96 bg-white shadow-2xl z-50 flex flex-col"
         style="display: none;">

        {{-- Header --}}
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-bold">Cart ({{ $itemCount }})</h3>
            <button @click="open = false" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Cart items --}}
        <div class="flex-1 overflow-y-auto p-4">
            @if (empty($cart))
                <div class="text-center text-gray-500 py-8">
                    <p>Your cart is empty</p>
                </div>
            @else
                @foreach ($cart as $id => $item)
                    <div class="flex items-center gap-3 py-3 border-b" wire:key="cart-{{ $id }}">
                        <div class="w-16 h-16 bg-gray-100 rounded flex-shrink-0 overflow-hidden">
                            @if (!empty($item['image']))
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm truncate">{{ $item['name'] }}</p>
                            <p class="text-gray-500 text-sm">${{ number_format($item['price'], 2) }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <button wire:click="updateQty({{ $item['product_id'] }}, {{ $item['quantity'] - 1 }})"
                                        class="px-2 py-0.5 bg-gray-200 rounded text-sm">-</button>
                                <span class="text-sm w-8 text-center">{{ $item['quantity'] }}</span>
                                <button wire:click="updateQty({{ $item['product_id'] }}, {{ $item['quantity'] + 1 }})"
                                        class="px-2 py-0.5 bg-gray-200 rounded text-sm">+</button>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-sm">${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                            <button wire:click="remove({{ $item['product_id'] }})"
                                    class="text-red-500 text-xs mt-1 hover:text-red-700">Remove</button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Footer --}}
        @if (!empty($cart))
            <div class="p-4 border-t">
                <div class="flex justify-between items-center mb-3">
                    <span class="font-bold">Total:</span>
                    <span class="font-bold text-lg">${{ $total }}</span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('checkout') }}" class="flex-1 bg-blue-600 text-white text-center py-3 rounded-lg font-medium hover:bg-blue-700">
                        Checkout
                    </a>
                    <button wire:click="clear" class="px-4 py-3 bg-gray-200 rounded-lg text-sm hover:bg-gray-300">
                        Clear
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Backdrop --}}
    <div x-show="open" @click="open = false"
         class="fixed inset-0 bg-black bg-opacity-50 z-40" style="display: none;"></div>
</div>
