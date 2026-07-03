{{--
    New Necoyoad — Blog Post Template (choroni theme)
--}}
<x-layouts.storefront>
    <div class="max-w-4xl mx-auto py-8">
        @if (!empty($breadcrumbs))
            <nav class="breadcrumbs mb-4 text-sm">
                @foreach ($breadcrumbs as $crumb)
                    <a href="{{ $crumb['href'] }}" class="text-blue-600">{{ $crumb['text'] }}</a>
                    @if (!$loop->last) <span class="mx-2 text-gray-400">::</span> @endif
                @endforeach
            </nav>
        @endif

        <article>
            @if ($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $title }}" class="w-full rounded-lg mb-6">
            @endif
            <h1 class="text-3xl font-bold mb-2">{{ $title }}</h1>
            <p class="text-gray-500 mb-6">{{ $post->date_publish_start?->format('M d, Y') }}</p>
            <div class="prose max-w-none">
                {!! $post->getDescription()?->description !!}
            </div>
        </article>
    </div>

    {{-- Widget positions --}}
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
</x-layouts.storefront>
