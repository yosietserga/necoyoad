{{--
    New Necoyoad — NivoSlider Banner Template (default fallback)
    Renders banner items as a NivoSlider. Pushes slider config to
    the Alpine.js ntPlugins store (equivalent to window.ntPlugins).
--}}
<li id="{{ $widgetName }}" class="banner nivo nt-editable"
    data-widget="{{ $widgetName }}" data-position="{{ $position }}"
    data-banner="nivoSlider">
    @if (!empty($heading))
        <div class="header"><h3>{{ $heading }}</h3></div>
    @endif
    <div class="content">
        <div class="slider-wrapper theme-default">
            <div x-data="{ init() { $($refs.slider).nivoSlider({{ json_encode($pluginConfig ?? []) }}) } }"
                 x-init="init()"
                 x-ref="slider"
                 class="nivoSlider">
                @foreach ($items as $item)
                    @if (!empty($item['image']))
                        @if (!empty($item['link']))
                            <a href="{{ $item['link'] }}" title="{{ $item['title'] ?? '' }}">
                        @endif
                        <img src="{{ asset('storage/' . $item['image']) }}"
                             alt="{{ $item['title'] ?? '' }}"
                             title="{{ $item['title'] ?? '' }}" />
                        @if (!empty($item['link']))
                            </a>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</li>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                if (!Alpine.store('ntPlugins')) {
                    Alpine.store('ntPlugins', []);
                }
                Alpine.store('ntPlugins').push({
                    id: '#{{ $widgetName }} .nivoSlider',
                    plugin: 'nivoSlider',
                    config: {
                        effect: 'random',
                        slices: 12,
                        animSpeed: 300,
                        pauseTime: 6000,
                        directionNav: false,
                        controlNav: false,
                        pauseOnHover: true,
                    }
                });
            });
        </script>
    @endpush
@endonce
