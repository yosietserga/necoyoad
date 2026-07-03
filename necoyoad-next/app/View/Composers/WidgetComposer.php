<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Services\WidgetService;
use Illuminate\View\View;

/**
 * WidgetComposer — the loadWidgets() equivalent.
 *
 * This View Composer runs before every storefront page render.
 * It calls WidgetService to populate $widgets[$position] with the
 * widget tree, which the Blade template then iterates.
 *
 * The template iterates $widgets and renders each widget via:
 *   <x-dynamic-component :component="$widget['component']" :settings="$widget['settings']" />
 *
 * For manual composition, templates use @stack/@push instead.
 *
 * @see v3 (Controller::loadWidgets algorithm)
 * @see v11 (widget engine preservation — Abstraction 1)
 * @see v12 (widget engine API contract)
 */
class WidgetComposer
{
    public function __construct(
        private readonly WidgetService $widgetService
    ) {}

    public function compose(View $view): void
    {
        // Get the current route name for landing_page filtering
        $route = request()->route()?->getName() ?? 'all';

        // Get object_type/object_id from session (per-entity widget overrides)
        $objectType = session('object_type');
        $objectId = session('object_id');

        // Load widgets for each standard position
        $positions = ['featuredContent', 'main', 'featuredFooter', 'header', 'footer', 'column_left', 'column_right'];

        $widgets = [];
        foreach ($positions as $position) {
            $widgets[$position] = $this->widgetService->getTree(
                position: $position,
                objectType: $objectType,
                objectId: $objectId,
            );
        }

        // Use view()->share() so $widgets propagates to anonymous Blade components
        // (storefront layout, widget-row) which have isolated scope and don't
        // inherit variables from $view->with(). Share once per request.
        if (!app()->bound('widgets.shared')) {
            app()->instance('widgets.shared', true);
            view()->share('widgets', $widgets);
        }

        // Also pass to the current view (for direct @foreach usage)
        $view->with('widgets', $widgets);
    }
}
