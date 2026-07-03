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

    /**
     * Get a single EAV property value via EavService.
     * The trait is a thin facade — all logic lives in App\Services\EavService.
     */
    public function getProperty(string $group, string $key, ?int $storeId = null): mixed
    {
        return app(\App\Services\EavService::class)->get($this, $group, $key, $storeId);
    }

    /**
     * Get all properties in a group via EavService.
     */
    public function getAllProperties(?string $group = null, ?int $storeId = null): Collection
    {
        if ($group) {
            return collect(app(\App\Services\EavService::class)->getGroup($this, $group, $storeId))
                ->mapWithKeys(fn ($value, $key) => ["{$group}.{$key}" => $value]);
        }

        // No group specified — return all properties for this model
        return $this->properties()->get()->keyBy(fn ($item) => "{$item->group}.{$item->key}");
    }

    /**
     * Set a single EAV property via EavService.
     */
    public function setProperty(string $group, string $key, mixed $value, ?int $storeId = null): void
    {
        app(\App\Services\EavService::class)->set($this, $group, $key, $value, $storeId);
    }

    /**
     * Set multiple EAV properties at once via EavService.
     */
    public function setManyProperties(array $properties, ?int $storeId = null): void
    {
        app(\App\Services\EavService::class)->setMany($this, $properties, $storeId);
    }

    /**
     * Delete a single EAV property via EavService.
     */
    public function deleteProperty(string $group, string $key, ?int $storeId = null): void
    {
        app(\App\Services\EavService::class)->delete($this, $group, $key, $storeId);
    }
}
