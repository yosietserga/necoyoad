{{--
    New Necoyoad — All Categories List Template
--}}
<x-layouts.storefront>
    <div class="max-w-6xl mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6">All Categories</h1>
        @php
            $categories = \App\Models\Category::whereNull('parent_id')
                ->where('status', true)->forCurrentStore()->orderBy('sort_order')->get();
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($categories as $category)
                <a href="{{ route('store.category', $category) }}" class="border rounded-lg p-6 hover:shadow-lg transition block">
                    <h3 class="font-bold text-lg">{{ $category->getTitle() ?? 'Category' }}</h3>
                </a>
            @endforeach
        </div>
    </div>
</x-layouts.storefront>
