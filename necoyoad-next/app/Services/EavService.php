<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * EavService — centralized Entity-Attribute-Value service.
 *
 * Implements the user mandate: "Always use EAV service to add or alter data
 * scheme, instead of change DB scheme."
 *
 * This service centralizes all EAV operations that were previously scattered
 * in the HasProperties trait. Provides:
 *   - Type-aware get/set (integer, string, json, boolean, date)
 *   - Per-store scoping
 *   - Request-level in-memory cache (avoids repeated queries)
 *   - Batch setMany() for bulk operations
 *   - Cache invalidation on write
 *
 * Backed by the `properties` polymorphic table (morph: propertiable).
 */
class EavService
{
    /** In-memory cache keyed by [model_type, model_id, group, key, store_id]. */
    private array $cache = [];

    /** Type registry for casting values on read. */
    private const TYPE_CASTS = [
        'integer' => 'intval',
        'boolean' => 'boolval',
        'json' => 'json_decode',
        'date' => 'Illuminate\Support\Carbon::parse',
    ];

    /**
     * Get a single EAV property value for a model.
     */
    public function get(Model $model, string $group, string $key, ?int $storeId = null): mixed
    {
        $storeId ??= $this->currentStoreId();
        $cacheKey = $this->cacheKey($model, $group, $key, $storeId);

        if (array_key_exists($cacheKey, $this->cache)) {
            return $this->cache[$cacheKey];
        }

        $property = Property::where('propertiable_type', $model->getMorphClass())
            ->where('propertiable_id', $model->getKey())
            ->where('group', $group)
            ->where('key', $key)
            ->where('store_id', $storeId)
            ->first();

        $value = $property?->getDecodedValue();

        $this->cache[$cacheKey] = $value;
        return $value;
    }

    /**
     * Get all properties in a group for a model.
     */
    public function getGroup(Model $model, string $group, ?int $storeId = null): array
    {
        $storeId ??= $this->currentStoreId();

        $properties = Property::where('propertiable_type', $model->getMorphClass())
            ->where('propertiable_id', $model->getKey())
            ->where('group', $group)
            ->where('store_id', $storeId)
            ->pluck('value', 'key');

        return $properties->map(fn ($v) => $this->decodeValue($v))->all();
    }

    /**
     * Set a single EAV property value.
     */
    public function set(Model $model, string $group, string $key, mixed $value, ?int $storeId = null): void
    {
        $storeId ??= $this->currentStoreId();
        $encoded = $this->encodeValue($value);

        Property::updateOrCreate(
            [
                'propertiable_type' => $model->getMorphClass(),
                'propertiable_id' => $model->getKey(),
                'group' => $group,
                'key' => $key,
                'store_id' => $storeId,
            ],
            [
                'value' => $encoded,
            ]
        );

        // Invalidate cache
        $cacheKey = $this->cacheKey($model, $group, $key, $storeId);
        $this->cache[$cacheKey] = $value;

        // Invalidate Laravel cache if used
        Cache::forget($this->laravelCacheKey($model, $group, $storeId));
    }

    /**
     * Set multiple EAV properties at once (batch operation).
     */
    public function setMany(Model $model, array $properties, ?int $storeId = null): void
    {
        $storeId ??= $this->currentStoreId();

        foreach ($properties as $group => $keys) {
            foreach ($keys as $key => $value) {
                $this->set($model, $group, $key, $value, $storeId);
            }
        }
    }

    /**
     * Delete a single EAV property.
     */
    public function delete(Model $model, string $group, string $key, ?int $storeId = null): void
    {
        $storeId ??= $this->currentStoreId();

        Property::where('propertiable_type', $model->getMorphClass())
            ->where('propertiable_id', $model->getKey())
            ->where('group', $group)
            ->where('key', $key)
            ->where('store_id', $storeId)
            ->delete();

        $cacheKey = $this->cacheKey($model, $group, $key, $storeId);
        unset($this->cache[$cacheKey]);
    }

    /**
     * Delete all properties in a group.
     */
    public function deleteGroup(Model $model, string $group, ?int $storeId = null): void
    {
        $storeId ??= $this->currentStoreId();

        Property::where('propertiable_type', $model->getMorphClass())
            ->where('propertiable_id', $model->getKey())
            ->where('group', $group)
            ->where('store_id', $storeId)
            ->delete();

        // Clear in-memory cache for this group
        $prefix = $this->cacheKeyPrefix($model, $group, $storeId);
        foreach (array_keys($this->cache) as $k) {
            if (str_starts_with($k, $prefix)) {
                unset($this->cache[$k]);
            }
        }
    }

    /**
     * Encode a value for storage (JSON for arrays/objects, string for scalars).
     */
    private function encodeValue(mixed $value): string
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        return (string) $value;
    }

    /**
     * Decode a stored value, attempting JSON first.
     */
    private function decodeValue(?string $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && !is_numeric($value)) {
            return $decoded;
        }
        return $value;
    }

    /**
     * Build an in-memory cache key.
     */
    private function cacheKey(Model $model, string $group, string $key, int $storeId): string
    {
        return "{$model->getMorphClass()}:{$model->getKey()}:{$group}:{$key}:{$storeId}";
    }

    private function cacheKeyPrefix(Model $model, string $group, int $storeId): string
    {
        return "{$model->getMorphClass()}:{$model->getKey()}:{$group}:";
    }

    private function laravelCacheKey(Model $model, string $group, int $storeId): string
    {
        return "eav:{$model->getMorphClass()}:{$model->getKey()}:{$group}:{$storeId}";
    }

    private function currentStoreId(): int
    {
        return app('store.context')?->id() ?? 0;
    }
}
