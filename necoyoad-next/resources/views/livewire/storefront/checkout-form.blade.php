{{--
    New Necoyoad — Checkout Form (Livewire 3)
    Single-page reactive checkout: Shipping → Payment → Confirm → Success
--}}
<div class="max-w-4xl mx-auto py-8">
    {{-- Progress indicator --}}
    <div class="flex items-center justify-between mb-8">
        @foreach (['Shipping', 'Payment', 'Confirm', 'Done'] as $i => $label)
            <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold
                            {{ $step > $i ? 'bg-green-500 text-white' : ($step === $i + 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500') }}">
                    {{ $step > $i ? '✓' : $i + 1 }}
                </div>
                <span class="ml-2 text-sm font-medium {{ $step === $i + 1 ? 'text-blue-600' : 'text-gray-500' }}">{{ $label }}</span>
                @if (!$loop->last)
                    <div class="flex-1 h-1 mx-4 {{ $step > $i + 1 ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Step 1: Shipping --}}
    @if ($step === 1)
        <form wire:submit="nextStep" class="space-y-4">
            <h2 class="text-2xl font-bold mb-4">Shipping Address</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">First Name *</label>
                    <input wire:model="shipping.firstname" type="text" class="w-full border rounded-lg p-3" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Last Name *</label>
                    <input wire:model="shipping.lastname" type="text" class="w-full border rounded-lg p-3" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium mb-1">Email *</label>
                    <input wire:model="shipping.email" type="email" class="w-full border rounded-lg p-3" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Phone</label>
                    <input wire:model="shipping.telephone" type="text" class="w-full border rounded-lg p-3">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Address *</label>
                    <input wire:model="shipping.address_1" type="text" class="w-full border rounded-lg p-3" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">City *</label>
                    <input wire:model="shipping.city" type="text" class="w-full border rounded-lg p-3" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Postal Code *</label>
                    <input wire:model="shipping.postcode" type="text" class="w-full border rounded-lg p-3" required>
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700">
                Continue to Payment
            </button>
        </form>
    @endif

    {{-- Step 2: Payment --}}
    @if ($step === 2)
        <form wire:submit="nextStep" class="space-y-4">
            <h2 class="text-2xl font-bold mb-4">Payment Method</h2>
            <div class="space-y-3">
                @foreach (['bank_transfer' => 'Bank Transfer', 'paypal' => 'PayPal', 'stripe' => 'Credit Card (Stripe)'] as $method => $label)
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $paymentMethod === $method ? 'border-blue-600 bg-blue-50' : '' }}">
                        <input wire:model="paymentMethod" type="radio" value="{{ $method }}" class="mr-3">
                        <span class="font-medium">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <div class="flex gap-4">
                <button type="button" wire:click="previousStep" class="flex-1 bg-gray-200 py-3 rounded-lg font-medium hover:bg-gray-300">Back</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700">Review Order</button>
            </div>
        </form>
    @endif

    {{-- Step 3: Confirm --}}
    @if ($step === 3)
        <div class="space-y-6">
            <h2 class="text-2xl font-bold">Review Your Order</h2>

            {{-- Cart summary --}}
            <div class="border rounded-lg p-4">
                @php $cart = session()->get('cart', []); @endphp
                @foreach ($cart as $item)
                    <div class="flex justify-between py-2 border-b last:border-0">
                        <span>{{ $item['name'] }} x {{ $item['quantity'] }}</span>
                        <span>${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between font-bold pt-2">
                    <span>Total:</span>
                    <span>${{ number_format(collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']), 2) }}</span>
                </div>
            </div>

            {{-- Terms --}}
            <label class="flex items-center">
                <input wire:model="termsAccepted" type="checkbox" class="mr-2">
                <span class="text-sm">I accept the terms and conditions</span>
            </label>

            <div class="flex gap-4">
                <button wire:click="previousStep" class="flex-1 bg-gray-200 py-3 rounded-lg font-medium hover:bg-gray-300">Back</button>
                <button wire:click="placeOrder" class="flex-1 bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700">Place Order</button>
            </div>
        </div>
    @endif

    {{-- Step 4: Success --}}
    @if ($step === 4 && $orderId)
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h2 class="text-3xl font-bold mb-2">Order Placed!</h2>
            <p class="text-gray-600 mb-4">Your order number is <strong>#{{ $orderId }}</strong></p>
            <a href="{{ route('common.home') }}" class="text-blue-600 hover:underline">Continue Shopping</a>
        </div>
    @endif
</div>
