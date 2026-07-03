<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasDescriptions;
use App\Traits\HasProperties;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BannerItem extends Model
{
    use HasFactory, HasDescriptions, HasProperties;

    protected $fillable = ['banner_id', 'image', 'link', 'sort_order', 'status'];

    protected $casts = ['status' => 'boolean'];

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class);
    }
}
