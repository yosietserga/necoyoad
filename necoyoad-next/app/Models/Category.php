<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Auditable;

use App\Traits\HasDescriptions;
use App\Traits\HasProperties;
use App\Traits\HasStoreAssignment;
use App\Traits\HasSeoUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory, HasDescriptions, HasProperties, HasStoreAssignment, HasSeoUrl;

    protected $fillable = ['parent_id', 'object_type', 'image', 'sort_order', 'status'];

    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'categorizable', 'categorizables');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }
}
