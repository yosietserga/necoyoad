{{--
    New Necoyoad — RichText Widget Template
    The simplest widget: renders rich text content.
--}}
<li id="{{ $widgetName }}" class="widget rich-text nt-editable"
    data-widget="{{ $widgetName }}" data-position="{{ $position }}">
    @if (!empty($heading))
        <div class="header"><h3>{{ $heading }}</h3></div>
    @endif
    <div class="content">
        {!! $content ?? '' !!}
    </div>
</li>
