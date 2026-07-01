{{--
    New Necoyoad — Home Page Template (choroni theme)
    Uses the storefront layout (which includes the widget row renderer).
    The WidgetComposer has already populated $widgets[$position].
--}}
<x-layouts.storefront>
    @php $templateType = 'home'; @endphp
    @php $position = 'featuredContent'; @endphp
    <x-layouts.widget-row :position="$position" />
    <div id="mainContentContainer" nt-editable>
        <div class="row">
            @php $position = 'column_left'; @endphp
            @if (!empty($widgets['column_left']))
                <div class="large-3 medium-3 small-12">
                    <div id="columnLeft" nt-editable>
                        <x-layouts.widget-row :position="$position" />
                    </div>
                </div>
            @endif
            <div class="large-{{ !empty($widgets['column_left']) && !empty($widgets['column_right']) ? '6' : (!empty($widgets['column_left']) || !empty($widgets['column_right']) ? '9' : '12') }} medium-12 small-12">
                <div id="columnCenter" nt-editable>
                    @php $position = 'main'; @endphp
                    <x-layouts.widget-row :position="$position" />
                    @stack('main-content')
                </div>
            </div>
            @php $position = 'column_right'; @endphp
            @if (!empty($widgets['column_right']))
                <div class="large-3 medium-3 small-12">
                    <div id="columnRight" nt-editable>
                        <x-layouts.widget-row :position="$position" />
                    </div>
                </div>
            @endif
        </div>
    </div>
    @php $position = 'featuredFooter'; @endphp
    <x-layouts.widget-row :position="$position" />
</x-layouts.storefront>
