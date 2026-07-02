{{--
    New Necoyoad — Widget Row Template

    This is the widgets-rows.tpl equivalent from the original Necoyoad (v3, v8).
    It iterates the $widgets[$position] array (populated by WidgetComposer)
    and renders each widget via Laravel's dynamic Blade components.

    Three composition modes:
    1. Dynamic: this template iterates $widgets and renders <x-dynamic-component>
    2. Manual: templates use @stack/@push to hardcode widget positions
    3. Hybrid: both in the same template

    @see v3 (widgets-rows.tpl — the placeholder emitter)
    @see v8 (CMS widget composition — three modes)
    @see v12 (widget engine API contract)
--}}
@foreach ($widgets[$position] ?? [] as $row)
    @php $rowSettings = $row['settings'] ?? []; @endphp
    <div data-row="{{ $row['key'] }}"
         data-position="{{ $position }}"
         class="row {{ $rowSettings['classnames'] ?? '' }}"
         id="{{ $position }}_{{ $row['key'] }}"
         nt-editable
         @if (!empty($rowSettings['sticky'])) data-sticky="1" @endif>

        @foreach ($row['columns'] ?? [] as $column)
            @php $colSettings = $column['settings'] ?? []; @endphp
            @php $grid = $column['grid'] ?? ['large' => 12, 'medium' => 12, 'small' => 12]; @endphp
            <div data-column="{{ $column['key'] }}"
                 data-position="{{ $position }}"
                 class="large-{{ $grid['large'] }} medium-{{ $grid['medium'] }} small-{{ $grid['small'] }} {{ $colSettings['classnames'] ?? '' }}"
                 id="{{ $position }}_{{ $column['key'] }}"
                 nt-editable>
                <ul class="widgets">
                    @foreach ($column['widgets'] ?? [] as $widget)
                        @php $isAsync = !empty($widget['settings']['transition_async']); @endphp
                        @if ($isAsync)
                            {{-- Async widget: render a placeholder, JS will fetch the content --}}
                            <li id="{{ $widget['name'] }}"
                                class="widget async-widget nt-editable"
                                data-widget="{{ $widget['name'] }}"
                                data-position="{{ $position }}"
                                data-async="1"
                                data-settings='{{ json_encode($widget['settings'] ?? []) }}'>
                                <div class="async-loading" style="padding:2rem;text-align:center;color:var(--necoyoad-text-muted,#718096);">
                                    Loading...
                                </div>
                            </li>
                        @else
                            <x-dynamic-component
                                :component="$widget['component']"
                                :settings="$widget['settings']"
                                :widgetName="$widget['name']"
                                :position="$position"
                            />
                        @endif
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
@endforeach
