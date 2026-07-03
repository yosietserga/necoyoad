{{--
    New Necoyoad — Product Page (Livewire 3)
    Reactive: quantity selector + add to cart without page reload.
--}}
<div x-data="{}">
    {{-- Product details (rendered within the storefront layout) --}}
    <div class="product-detail grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Product image --}}
        <div class="product-image">
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}"
                     alt="{{ $title }}"
                     class="w-full rounded-lg shadow-lg">
            @endif
        </div>

        {{-- Product info --}}
        <div class="product-info">
            <h1 class="text-3xl font-bold mb-2">{{ $title }}</h1>
            <p class="text-2xl text-blue-600 font-semibold mb-4">${{ number_format($product->price, 2) }}</p>

            @if (!empty($product->model))
                <p class="text-gray-500 mb-2">Model: {{ $product->model }}</p>
            @endif

            @if (!empty($product->sku))
                <p class="text-gray-500 mb-2">SKU: {{ $product->sku }}</p>
            @endif

            @if ($product->quantity > 0)
                <p class="text-green-600 mb-4">In Stock ({{ $product->quantity }} available)</p>
            @else
                <p class="text-red-600 mb-4">Out of Stock</p>
            @endif

            {{-- Description --}}
            <div class="prose max-w-none mb-6">
                {!! $description !!}
            </div>

            {{-- Quantity selector + Add to cart --}}
            <div class="flex items-center gap-4 mb-4">
                <div class="flex items-center border rounded-lg">
                    <button wire:click="decrementQty" class="px-4 py-2 text-lg hover:bg-gray-100">-</button>
                    <input type="number" wire:model="quantity" class="w-16 text-center border-0 py-2" min="1">
                    <button wire:click="incrementQty" class="px-4 py-2 text-lg hover:bg-gray-100">+</button>
                </div>

                <button wire:click="addToCart"
                        @if ($product->quantity <= 0) disabled @endif
                        class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    Add to Cart
                </button>
            </div>

            {{-- Category links --}}
            @foreach ($product->categories as $category)
                @php $catDesc = $category->getDescription(); @endphp
                <a href="{{ route('store.category', $category) }}"
                   class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm mr-2 hover:bg-gray-200">
                    {{ $catDesc?->title ?? 'Category' }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Widget positions (per-entity: object_type = 'product') --}}
    @php $position = 'featuredContent'; @endphp
    <x-layouts.widget-row :position="$position" />

    <div id="mainContentContainer" nt-editable>
        <div class="row">
            <div class="large-12 medium-12 small-12">
                <div id="columnCenter" nt-editable>
                    @php $position = 'main'; @endphp
                    <x-layouts.widget-row :position="$position" />
                </div>
            </div>
        </div>
    </div>

    @php $position = 'featuredFooter'; @endphp
    <x-layouts.widget-row :position="$position" />
</div>
