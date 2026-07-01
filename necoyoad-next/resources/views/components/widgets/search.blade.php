{{--
    New Necoyoad — Search Widget Template
--}}
<li id="{{ $widgetName }}" class="widget search nt-editable"
    data-widget="{{ $widgetName }}" data-position="{{ $position }}">
    <form action="{{ $action }}" method="GET" class="search-form">
        <input type="text" name="q" placeholder="{{ $placeholder }}" x-data x-model="searchTerm"
               @keydown.enter="$el.submit()">
        <button type="submit">Search</button>
    </form>
</li>
