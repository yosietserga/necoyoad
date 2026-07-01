<?php

declare(strict_types=1);

namespace App\View\Components\Widgets;

use App\Models\Product;
use App\Services\StoreContext;
use App\Services\LanguageContext;
use App\View\Components\WidgetComponent;

/**
 * ProductList widget — displays a list of products.
 * Supports filtering by category, featured, sort order, and limit.
 */
class ProductList extends WidgetComponent
{
    public function data(): array
    {
        $storeId = app(StoreContext::class)->id();
        $langId = app(LanguageContext::class)->id();

        $query = Product::with(['descriptions' => fn($q) => $q->where('language_id', $langId)])
            ->forStore($storeId)
            ->where('status', true);

        // Filter by featured
        if (!empty($this->settings['featured'])) {
            $query->where('featured', true);
        }

        // Filter by category
        if (!empty($this->settings['category_id'])) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $this->settings['category_id']));
        }

        // Sort
        $sort = $this->settings['sort'] ?? 'sort_order';
        $order = $this->settings['order'] ?? 'ASC';
        $query->orderBy($sort, $order);

        // Limit
        $limit = $this->settings['limit'] ?? 5;
        $products = $query->limit($limit)->get();

        return [
            'products' => $products,
            'heading' => $this->settings['title'] ?? '',
            'view' => $this->settings['view'] ?? 'default',
        ];
    }
}
