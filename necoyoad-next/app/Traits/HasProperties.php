<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Property;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * HasProperties — provides EAV key-value metadata for any model.
 *
 * This is the polymorphic property table from the original Necoyoad,
 * reimplemented as an Eloquent morph relation. Each model that uses this
 * trait gets a `properties()` relation with group + key accessors.
 *
 * Usage:
 *   $product->getProperty('style', 'view');           // per-entity template override
 *   $banner->getProperty('settings', 'autoplay');      // slider config
 *   $product->getAllProperties('style');               // all style properties
 *   $product->setProperty('style', 'view', 'custom');  // set a property
 *
 * @see v3 (rendering pipeline — property EAV table)
 * @see v5 (multi-store/multi-language — polymorphic spine)
 * @see v8 (CMS — per-entity template override via property('style', 'view'))
 */
trait HasProperties
{
    public function properties(): MorphMany
    {
        return $this->morphMany(Property::class, 'propertiable');
    }

    public function getProperty(string $group, string $key, ?int $storeId = null): mixed
    {
        $storeId ??= app('store.context')->id();

        $property = $this->properties()
            ->where('group', $group)
            ->where('key', $key)
            ->where(function ($query) use ($storeId) {
                $query->where('store_id', $storeId)
                    ->orWhereNull('store_id');
            })
            ->orderByRaw('store_id IS NULL') // store-specific first, then global
            ->first();

        return $property?->getDecodedValue();
    }

    public function getAllProperties(?string $group = null, ?int $storeId = null): Collection
    {
        $storeId ??= app('store.context')->id();

        $query = $this->properties();

        if ($group) {
            $query->where('group', $group);
        }

        return $query->where(function ($q) use ($storeId) {
            $q->where('store_id', $storeId)
                ->orWhereNull('store_id');
        })->get()->keyBy(fn ($item) => "{$item->group}.{$item->key}");
    }

    public function setProperty(string $group, string $key, mixed $value, ?int $storeId = null): void
    {
        $storeId ??= app('store.context')->id();

        $this->properties()->updateOrCreate(
            [
                'group' => $group,
                'key' => $key,
                'store_id' => $storeId,
            ],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
            ]
        );
    }

    public function deleteProperty(string $group, string $key, ?int $storeId = null): void
    {
        $storeId ??= app('store.context')->id();

        $this->properties()
            ->where('group', $group)
            ->where('key', $key)
            ->where('store_id', $storeId)
            ->delete();
    }
}
