{{--
    New Necoyoad — Storefront Layout

    This is the widgets-common.tpl equivalent from the original Necoyoad (v8).
    It provides the universal page layout: featured content + breadcrumbs +
    left column + center column + right column + featured footer.

    All positions are widget-driven. The WidgetComposer populates
    $widgets[$position] before this template renders.

    For manual composition, use @stack/@push in the page template.
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? '' }}">
    <meta name="keywords" content="{{ $keywords ?? '' }}">
    <meta name="author" content="Necoyoad">

    {{-- Styles (populated by AssetManifest) --}}
    @foreach (($styles ?? []) as $style)
        <link href="{{ $style['href'] }}" rel="stylesheet" media="{{ $style['media'] ?? 'all' }}">
    @endforeach

    {{-- Inline CSS (per-widget custom styles) --}}
    @if (!empty($css))
        <style>{{ $css }}</style>
    @endif

    {{-- Header JavaScript --}}
    @foreach (($headerJavascripts ?? []) as $js)
        <script src="{{ $js }}"></script>
    @endforeach
</head>
<body>
<div id="contentContainer" class="tpl-{{ $templateType ?? 'page' }}" nt-editable>

    {{-- Featured Content Position --}}
    <div id="featuredContentContainer" nt-editable>
        @php $position = 'featuredContent'; @endphp
        <x-layouts.widget-row :position="$position" />
    </div>

    {{-- Main Content Container --}}
    <div id="mainContentContainer" nt-editable>
        <div class="row">
            {{-- Breadcrumbs --}}
            @if (!empty($breadcrumbs))
                <nav class="breadcrumbs">
                    @foreach ($breadcrumbs as $crumb)
                        <a href="{{ $crumb['href'] }}">{{ $crumb['text'] }}</a>
                        @if (!$loop->last) <span class="separator">::</span> @endif
                    @endforeach
                </nav>
            @endif

            {{-- Left Column --}}
            @if (!empty($widgets['column_left']))
                <div class="large-3 medium-3 small-12">
                    <div id="columnLeft" nt-editable>
                        @php $position = 'column_left'; @endphp
                        <x-layouts.widget-row :position="$position" />
                    </div>
                </div>
            @endif

            {{-- Center Column --}}
            <div class="{{ !empty($widgets['column_left']) && !empty($widgets['column_right']) ? 'large-6' : (!empty($widgets['column_left']) || !empty($widgets['column_right']) ? 'large-9' : 'large-12') }} medium-12 small-12">
                <div id="columnCenter" nt-editable>
                    @php $position = 'main'; @endphp
                    <x-layouts.widget-row :position="$position" />

                    {{-- Manual composition stack (for hardcoded widgets) --}}
                    @stack('main-content')
                </div>
            </div>

            {{-- Right Column --}}
            @if (!empty($widgets['column_right']))
                <div class="large-3 medium-3 small-12">
                    <div id="columnRight" nt-editable>
                        @php $position = 'column_right'; @endphp
                        <x-layouts.widget-row :position="$position" />
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Featured Footer Position --}}
    <div id="featuredFooterContainer" nt-editable>
        @php $position = 'featuredFooter'; @endphp
        <x-layouts.widget-row :position="$position" />
    </div>
</div>

{{-- Footer JavaScript (populated by AssetManifest) --}}
@foreach (($javascripts ?? []) as $js)
    <script src="{{ $js }}"></script>
@endforeach

{{-- Inline scripts (per-widget JS) --}}
@if (!empty($scripts))
    @foreach ($scripts as $script)
        <script>{{ $script }}</script>
    @endforeach
@endif

{{-- Alpine.js init --}}
<script>
    document.addEventListener('alpine:init', () => {
        // ntPlugins equivalent (Alpine.js store for slider configs)
        Alpine.store('ntPlugins', []);
        Alpine.store('ntContext', {
            sid: {{ app('store.context')?->id() ?? 0 }},
            httpHome: '{{ config('app.url') }}',
            isMobile: {{ request()->userAgent() && preg_match('/(android|iphone|ipod|ipad|mobile)/i', request()->userAgent()) ? 'true' : 'false' }},
        });
    });
</script>

{{-- Page body content from child views (home, product, etc.) --}}
{{ $slot ?? '' }}

{{-- Cart drawer (Livewire) — embedded in layout so it's available on every page --}}
@livewire('cart-drawer')

</body>
</html>
