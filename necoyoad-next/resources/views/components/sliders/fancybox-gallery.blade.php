{{--
    New Necoyoad — Fancybox Gallery Slider Template
    A lightbox gallery (not a slider). Click to open full-size image.
--}}
<li id="{{ $widgetName }}" class="banner fancybox-gallery nt-editable"
    data-widget="{{ $widgetName }}" data-position="{{ $position }}"
    data-banner="fancybox-gallery">
    @if (!empty($heading))
        <div class="header"><h3>{{ $heading }}</h3></div>
    @endif
    <div class="content">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($items as $item)
                @if (!empty($item['image']))
                    <a data-fancybox="{{ $widgetName }}-gallery"
                       href="{{ asset('storage/' . $item['image']) }}"
                       title="{{ $item['title'] ?? '' }}"
                       class="block overflow-hidden rounded-lg">
                        <img src="{{ asset('storage/' . $item['image']) }}"
                             alt="{{ $item['title'] ?? '' }}"
                             class="w-full h-32 object-cover hover:scale-110 transition">
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</li>
