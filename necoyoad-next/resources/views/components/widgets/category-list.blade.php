{{--
    New Necoyoad — CategoryList Widget Template
--}}
<li id="{{ $widgetName }}" class="widget category-list nt-editable"
    data-widget="{{ $widgetName }}" data-position="{{ $position }}">
    @if (!empty($heading))
        <div class="header"><h3>{{ $heading }}</h3></div>
    @endif
    <ul class="category-list">
        @foreach ($categories as $category)
            <li class="category-item">
                <a href="{{ route('store.category', $category) }}">
                    {{ $category->getTitle() ?? 'Category ' . $category->id }}
                </a>
            </li>
        @endforeach
    </ul>
</li>
