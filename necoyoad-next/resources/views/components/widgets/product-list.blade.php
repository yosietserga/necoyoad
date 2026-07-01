{{--
    New Necoyoad — ProductList Widget Template
    Displays products in a grid or list layout.
--}}
<li id="{{ $widgetName }}" class="widget product-list nt-editable"
    data-widget="{{ $widgetName }}" data-position="{{ $position }}">
    @if (!empty($heading))
        <div class="header"><h3>{{ $heading }}</h3></div>
    @endif
    <div class="content">
        <div class="product-grid grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($products as $product)
                <div class="product-card">
                    @if ($product->image)
                        <a href="{{ route('store.product', $product) }}">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->getTitle() }}" class="w-full">
                        </a>
                    @endif
                    <a href="{{ route('store.product', $product) }}" class="product-name">
                        {{ $product->getTitle() ?? $product->sku }}
                    </a>
                    <span class="price">{{ config('app.currency') }} {{ number_format($product->price, 2) }}</span>
                </div>
            @endforeach
        </div>
    </div>
</li>
