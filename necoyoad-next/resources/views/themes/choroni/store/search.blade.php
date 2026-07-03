{{--
    New Necoyoad — Search Results Template
--}}
<x-layouts.storefront>
    <div class="max-w-6xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Search: "{{ $query }}"</h1>

        @if ($products->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <div class="product-card border rounded-lg p-4 hover:shadow-lg transition">
                        @if ($product->image)
                            <a href="{{ route('store.product', $product) }}">
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     alt="{{ $product->getTitle() }}"
                                     class="w-full h-48 object-cover rounded mb-3">
                            </a>
                        @endif
                        <a href="{{ route('store.product', $product) }}" class="font-medium hover:text-blue-600">
                            {{ $product->getTitle() ?? $product->sku }}
                        </a>
                        <p class="text-blue-600 font-semibold mt-1">${{ number_format($product->price, 2) }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <p class="text-lg">No products found for "{{ $query }}"</p>
                <a href="{{ route('common.home') }}" class="text-blue-600 hover:underline mt-4 inline-block">Back to Home</a>
            </div>
        @endif
    </div>
</x-layouts.storefront>
