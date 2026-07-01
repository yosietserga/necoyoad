<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasDescriptions;
use App\Traits\HasProperties;
use App\Traits\HasStoreAssignment;
use App\Traits\HasSeoUrl;
use App\Traits\HasCategories;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory, HasDescriptions, HasProperties, HasStoreAssignment, HasSeoUrl, HasCategories;

    protected $fillable = [
        'sku', 'model', 'price', 'cost', 'quantity', 'subtract',
        'minimum', 'image', 'weight', 'length', 'width', 'height',
        'manufacturer_id', 'shipping', 'featured', 'viewed',
        'sort_order', 'status', 'date_available',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'cost' => 'decimal:4',
        'featured' => 'boolean',
        'subtract' => 'boolean',
        'shipping' => 'boolean',
        'status' => 'boolean',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }
}
