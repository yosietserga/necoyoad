<?php

declare(strict_types=1);

namespace App\View\Components\Widgets;

use App\Models\Category;
use App\Services\StoreContext;
use App\Services\LanguageContext;
use App\View\Components\WidgetComponent;

/**
 * CategoryList widget — displays a list of categories.
 * Supports tree rendering (parent_id) and grid/default views.
 */
class CategoryList extends WidgetComponent
{
    public function data(): array
    {
        $storeId = app(StoreContext::class)->id();
        $langId = app(LanguageContext::class)->id();

        $parentId = $this->settings['parent_id'] ?? 0;

        $categories = Category::with(['descriptions' => fn($q) => $q->where('language_id', $langId)])
            ->forStore($storeId)
            ->where('parent_id', $parentId)
            ->where('status', true)
            ->orderBy('sort_order')
            ->get();

        return [
            'categories' => $categories,
            'heading' => $this->settings['title'] ?? '',
            'view' => $this->settings['view'] ?? 'default',
        ];
    }
}
