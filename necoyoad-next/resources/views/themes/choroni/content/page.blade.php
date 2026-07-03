{{--
    New Necoyoad — Static Page Template (choroni theme)
    Same as post but for pages (type = 'page').
--}}
<x-layouts.storefront>
    <div class="max-w-4xl mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6">{{ $title }}</h1>
        <div class="prose max-w-none">
            {!! $page->getDescription()?->description !!}
        </div>
    </div>

    {{-- Widget positions --}}
    @php $position = 'featuredContent'; @endphp
    <x-layouts.widget-row :position="$position" />
    <div id="mainContentContainer" nt-editable>
        <div class="row">
            <div class="large-12 medium-12 small-12">
                <div id="columnCenter" nt-editable>
                    @php $position = 'main'; @endphp
                    <x-layouts.widget-row :position="$position" />
                </div>
            </div>
        </div>
    </div>
    @php $position = 'featuredFooter'; @endphp
    <x-layouts.widget-row :position="$position" />
</x-layouts.storefront>
