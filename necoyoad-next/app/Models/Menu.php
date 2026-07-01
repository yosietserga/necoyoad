<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['store_id', 'name', 'position', 'sort_order', 'route', 'is_default', 'status'];

    protected $casts = ['is_default' => 'boolean', 'status' => 'boolean'];

    public function links(): HasMany
    {
        return $this->hasMany(MenuLink::class)->whereNull('parent_id')->orderBy('sort_order');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
