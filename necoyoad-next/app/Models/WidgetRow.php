<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WidgetRow extends Model
{
    use HasFactory;

    protected $table = 'widget_rows';

    protected $fillable = ['store_id', 'position', 'key', 'settings', 'sort_order', 'status'];

    protected $casts = ['settings' => 'array', 'status' => 'boolean'];

    public function columns(): HasMany
    {
        return $this->hasMany(WidgetColumn::class, 'row_id')->orderBy('sort_order');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
