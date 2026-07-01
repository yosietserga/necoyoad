<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * WidgetService — the widget data-access service.
 *
 * This is the NecoWidget equivalent from the original Necoyoad (v3, v5),
 * reimplemented with Eloquent and JSON column queries (no LIKE '%key=value%').
 *
 * Queries the widget tree (rows → columns → widgets) from the database,
 * filtered by:
 *   - store_id (multi-store)
 *   - language_id (multi-language)
 *   - position (layout position: 'main', 'featuredContent', etc.)
 *   - landing_page (route: 'common/home', 'store/product', 'all')
 *   - device (show_in_mobile, show_in_tableau, show_in_desktop)
 *   - customer_session_mode (any, logged_in, logged_out)
 *   - object_type + object_id (per-entity widget overrides)
 *
 * The per-entity override merges per-object widgets into the default tree
 * (two-query merge, same as the original).
 *
 * @see v3 (rendering pipeline — loadWidgets algorithm)
 * @see v5 (widget composition by object_type)
 * @see v8 (CMS widget composition — per-entity overrides)
 * @see v11 (widget engine preservation strategy)
 * @see v12 (widget engine API contract)
 */
class WidgetService
{
    public function __construct(
        private readonly StoreContext $storeContext,
        private readonly LanguageContext $languageContext
    ) {}

    /**
     * Get the widget tree for a position.
     *
     * @param string $position Layout position (e.g. 'main', 'featuredContent')
     * @param string|null $objectType Per-entity override type (e.g. 'product', 'post')
     * @param int|null $objectId Per-entity override ID
     * @param bool $only Only load per-object widgets (skip default tree). For embedded pages.
     * @return array Nested tree: [rows => [columns => [widgets]]]
     */
    public function getTree(
        string $position,
        ?string $objectType = null,
        ?int $objectId = null,
        bool $only = false
    ): array {
        $storeId = $this->storeContext->id();
        $languageId = $this->languageContext->id();
        $route = request()->route()?->getName() ?? 'all';
        $isMobile = $this->isMobile();
        $isTablet = $this->isTablet();
        $isDesktop = !$isMobile && !$isTablet;
        $isLoggedIn = auth('customer')->check();

        $tree = [];

        // Load default tree (skip if $only is true — for embedded pages)
        if (!$only) {
            $tree = $this->queryTree(
                storeId: $storeId,
                position: $position,
                landingPage: ['all', $route],
                isMobile: $isMobile,
                isTablet: $isTablet,
                isDesktop: $isDesktop,
                isLoggedIn: $isLoggedIn,
            );
        }

        // Load per-object override tree (merge into default)
        if ($objectType && $objectId) {
            $objectTree = $this->queryTree(
                storeId: $storeId,
                position: $position,
                landingPage: ['all', $route],
                isMobile: $isMobile,
                isTablet: $isTablet,
                isDesktop: $isDesktop,
                isLoggedIn: $isLoggedIn,
                objectType: $objectType,
                objectId: $objectId,
            );

            $tree = array_merge($tree, $objectTree);
        }

        return $tree;
    }

    /**
     * Query the widget tree from the database.
     */
    private function queryTree(
        int $storeId,
        string $position,
        array $landingPage,
        bool $isMobile,
        bool $isTablet,
        bool $isDesktop,
        bool $isLoggedIn,
        ?string $objectType = null,
        ?int $objectId = null,
    ): array {
        // Query widget rows
        $rowsQuery = \App\Models\WidgetRow::with(['columns.widgets' => function ($q) use ($storeId, $landingPage, $isMobile, $isTablet, $isDesktop, $isLoggedIn, $objectType, $objectId) {
            $q->where('status', true)
                ->where('store_id', $storeId)
                ->whereIn('landing_page', $landingPage)
                ->when($objectType, fn ($q) => $q->where('object_type', $objectType)->where('object_id', $objectId))
                ->when(!$objectType, fn ($q) => $q->whereNull('object_type'))
                // Device filtering (JSON path expressions, not LIKE)
                ->when($isMobile, fn ($q) => $q->where('settings->show_in_mobile', true))
                ->when($isTablet, fn ($q) => $q->where('settings->show_in_tablet', true))
                ->when($isDesktop, fn ($q) => $q->where('settings->show_in_desktop', true))
                // Auth filtering
                ->where(function ($q) use ($isLoggedIn) {
                    $q->where('settings->customer_session_mode', 'any')
                        ->when($isLoggedIn, fn ($q) => $q->orWhere('settings->customer_session_mode', 'logged_in'))
                        ->when(!$isLoggedIn, fn ($q) => $q->orWhere('settings->customer_session_mode', 'logged_out'));
                })
                ->orderBy('sort_order');
        }])
            ->where('store_id', $storeId)
            ->where('position', $position)
            ->where('status', true)
            ->orderBy('sort_order');

        // Cache bypass for admin users (Filament uses the 'web' guard)
        $cacheKey = "widgets:{$storeId}:{$position}";
        if (auth('web')->check()) {
            $rows = $rowsQuery->get();
        } else {
            $rows = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, fn () => $rowsQuery->get());
        }

        return $rows->map(function ($row) {
            return [
                'id' => $row->id,
                'key' => $row->key,
                'settings' => $row->settings,
                'columns' => $row->columns->map(function ($column) {
                    return [
                        'id' => $column->id,
                        'key' => $column->key,
                        'settings' => $column->settings,
                        'grid' => $column->settings['grid'] ?? ['large' => 12, 'medium' => 12, 'small' => 12],
                        'widgets' => $column->widgets->map(function ($widget) {
                            return [
                                'id' => $widget->id,
                                'name' => $widget->name,
                                'module' => $widget->module,
                                'settings' => $widget->settings,
                                'component' => $widget->component_name, // e.g. 'widgets.banner'
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
            ];
        })->toArray();
    }

    private function isMobile(): bool
    {
        $ua = request()->userAgent();
        return (bool) preg_match('/(android|iphone|ipod|ipad|windows phone|blackberry|mobile)/i', $ua);
    }

    private function isTablet(): bool
    {
        $ua = request()->userAgent();
        return (bool) preg_match('/(ipad|tablet|kindle|silk)/i', $ua);
    }
}
