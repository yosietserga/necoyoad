<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Auditable;

use App\Traits\HasDescriptions;
use App\Traits\HasProperties;
use App\Traits\HasStoreAssignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Banner extends Model
{
    use HasFactory, HasDescriptions, HasProperties, HasStoreAssignment;

    protected $fillable = [
        'name', 'jquery_plugin', 'params',
        'publish_date_start', 'publish_date_end', 'status',
    ];

    protected $casts = [
        'params' => 'array',
        'status' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(BannerItem::class)->orderBy('sort_order');
    }
}
