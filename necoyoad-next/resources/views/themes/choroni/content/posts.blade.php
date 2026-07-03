{{--
    New Necoyoad — All Blog Posts List Template
--}}
<x-layouts.storefront>
    <div class="max-w-4xl mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6">Blog</h1>
        @php
            $posts = \App\Models\Post::where('type', 'post')
                ->where('status', true)->where('publish', true)
                ->where('date_publish_start', '<=', now())
                ->forCurrentStore()->orderBy('date_publish_start', 'desc')->paginate(10);
        @endphp
        <div class="space-y-6">
            @foreach ($posts as $post)
                <article class="border-b pb-6">
                    @if ($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->getTitle() }}" class="w-full h-48 object-cover rounded-lg mb-4">
                    @endif
                    <h2 class="text-xl font-bold">
                        <a href="{{ route('content.post', $post) }}" class="hover:text-blue-600">{{ $post->getTitle() }}</a>
                    </h2>
                    <p class="text-gray-500 text-sm mb-2">{{ $post->date_publish_start?->format('M d, Y') }}</p>
                    <p class="text-gray-600">{{ \Illuminate\Support\Str::limit(strip_tags($post->getDescription()?->description ?? ''), 200) }}</p>
                    <a href="{{ route('content.post', $post) }}" class="text-blue-600 hover:underline text-sm mt-2 inline-block">Read more →</a>
                </article>
            @endforeach
        </div>
        {{ $posts->links() }}
    </div>
</x-layouts.storefront>
