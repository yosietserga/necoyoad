{{--
    New Necoyoad — All Products List Template
--}}
<x-layouts.storefront>
    <div class="max-w-6xl mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6">All Products</h1>
        @php
            $products = \App\Models\Product::where('status', true)
                ->forCurrentStore()
                ->orderBy('sort_order')
                ->paginate(12);
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class="product-card border rounded-lg p-4 hover:shadow-lg transition">
                    @if ($product->image)
                        <a href="{{ route('store.product', $product) }}">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->getTitle() }}" class="w-full h-48 object-cover rounded mb-3">
                        </a>
                    @endif
                    <a href="{{ route('store.product', $product) }}" class="font-medium hover:text-blue-600">{{ $product->getTitle() ?? $product->sku }}</a>
                    <p class="text-blue-600 font-semibold mt-1">${{ number_format($product->price, 2) }}</p>
                </div>
            @endforeach
        </div>
        {{ $products->links() }}
    </div>
</x-layouts.storefront>
