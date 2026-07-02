{{--
    New Necoyoad — Home Page Template (choroni theme)

    The storefront layout handles all widget positions (featuredContent,
    column_left, main, column_right, featuredFooter). The WidgetComposer
    has already populated $widgets[$position] and the layout renders them.

    This template only needs to set the template type. Entity-specific
    content (if any) can be pushed via @push('main-content').
--}}
<x-layouts.storefront>
    @php $templateType = 'home'; @endphp

    {{-- Home page has no entity-specific content outside the widget system.
         All content is widget-driven (hero_banner, featured_products, welcome_text). --}}
</x-layouts.storefront>
