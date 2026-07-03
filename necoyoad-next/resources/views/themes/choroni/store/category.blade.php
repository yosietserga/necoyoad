{{--
    New Necoyoad — Category Page Template (choroni theme)
--}}
<x-layouts.storefront>
    <div class="max-w-6xl mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6">{{ $title }}</h1>

        {{-- Products in this category --}}
        @php
            $products = \App\Models\Product::whereHas('categories', fn($q) =>
                $q->where('categories.id', $category->id)
            )->where('status', true)->forCurrentStore()->paginate(12);
        @endphp

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
            {{ $products->links() }}
        @else
            <p class="text-gray-500">No products in this category.</p>
        @endif
    </div>

    {{-- Widget positions --}}
    @php $position = 'featuredContent'; @endphp
    <x-layouts.widget-row :position="$position" />
    @php $position = 'featuredFooter'; @endphp
    <x-layouts.widget-row :position="$position" />
</x-layouts.storefront>
