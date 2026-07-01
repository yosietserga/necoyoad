<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * HasCategories — provides polymorphic category assignment for any model.
 *
 * This is the object_to_category junction from the original Necoyoad,
 * reimplemented as an Eloquent morphToMany relation.
 *
 * Usage:
 *   $product->categories()->attach($categoryId);
 *   $product->categories; // collection of Category models
 *
 * @see v5 (polymorphic object spine — object_to_category)
 */
trait HasCategories
{
    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categorizable', 'categorizables');
    }
}
