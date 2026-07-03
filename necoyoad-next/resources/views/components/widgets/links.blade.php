{{--
    New Necoyoad — Links Widget Template (menu renderer)
--}}
<li id="{{ $widgetName }}" class="widget links nt-editable"
    data-widget="{{ $widgetName }}" data-position="{{ $position }}">
    @if (!empty($heading))
        <div class="header"><h3>{{ $heading }}</h3></div>
    @endif
    <div class="content">
        {!! $links_html !!}
    </div>
</li>
