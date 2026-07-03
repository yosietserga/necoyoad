{{--
    New Necoyoad — Slick Carousel Slider Template
    Uses Alpine.js + Slick carousel. Pushes config to ntPlugins store.
--}}
<li id="{{ $widgetName }}" class="banner slick nt-editable"
    data-widget="{{ $widgetName }}" data-position="{{ $position }}"
    data-banner="slick">
    @if (!empty($heading))
        <div class="header"><h3>{{ $heading }}</h3></div>
    @endif
    <div class="content">
        <div x-data="{
            init() {
                $(this.$refs.slider).slick({
                    slidesToShow: {{ $settings['slides_to_show'] ?? 1 }},
                    slidesToScroll: {{ $settings['slides_to_scroll'] ?? 1 }},
                    infinite: true,
                    autoplay: {{ !empty($settings['autoplay']) ? 'true' : 'false' }},
                    autoplaySpeed: {{ $settings['autoplay_speed'] ?? 4000 }},
                    dots: {{ !empty($settings['dots']) ? 'true' : 'false' }},
                    arrows: {{ !empty($settings['arrows']) ? 'true' : 'false' }},
                    fade: {{ !empty($settings['fade']) ? 'true' : 'false' }},
                });
            }
        }" x-init="init()" x-ref="slider" class="slick-slider">
            @foreach ($items as $item)
                @if (!empty($item['image']))
                    <div>
                        @if (!empty($item['link']))
                            <a href="{{ $item['link'] }}">
                        @endif
                        <img src="{{ asset('storage/' . $item['image']) }}"
                             alt="{{ $item['title'] ?? '' }}"
                             class="w-full">
                        @if (!empty($item['link']))
                            </a>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</li>
