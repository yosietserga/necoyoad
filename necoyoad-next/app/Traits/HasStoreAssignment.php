<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Store;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * HasStoreAssignment — provides multi-store scoping for any model.
 *
 * This is the object_to_store junction from the original Necoyoad,
 * reimplemented as an Eloquent morphToMany relation. Each model that
 * uses this trait gets a `stores()` relation and a global scope that
 * filters by the current store.
 *
 * Usage:
 *   $product = Product::forCurrentStore()->find($id);
 *   $product->stores()->attach($storeId);
 *
 * @see v5 (multi-store/multi-language architecture)
 */
trait HasStoreAssignment
{
    public static function bootHasStoreAssignment(): void
    {
        static::addGlobalScope('store', function ($query) {
            if (app()->bound('store.context') && app('store.context')->id()) {
                // Show entities assigned to the current store OR entities with
                // no store assignment at all (global content). orDoesntHave is
                // used instead of orWhereNull('stores.id') because the outer
                // query has no stores column — only the whereHas subquery does.
                $query->where(function ($q) {
                    $q->whereHas('stores', function ($sq) {
                        $sq->where('stores.id', app('store.context')->id());
                    })->orDoesntHave('stores');
                });
            }
        });
    }

    public function stores(): MorphToMany
    {
        return $this->morphToMany(Store::class, 'assignable', 'store_assignments');
    }

    public function scopeForStore($query, int $storeId)
    {
        return $query->whereHas('stores', function ($q) use ($storeId) {
            $q->where('stores.id', $storeId);
        });
    }

    public function scopeForCurrentStore($query)
    {
        $storeId = app('store.context')->id();

        return $this->scopeForStore($query, $storeId);
    }

    public function assignToStore(int $storeId): void
    {
        $this->stores()->syncWithoutDetaching([$storeId]);
    }

    public function removeFromStore(int $storeId): void
    {
        $this->stores()->detach([$storeId]);
    }
}
