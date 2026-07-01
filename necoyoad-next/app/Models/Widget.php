<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Widget extends Model
{
    use HasFactory;

    protected $fillable = [
        'column_id', 'name', 'module', 'store_id', 'landing_page',
        'object_type', 'object_id', 'settings', 'sort_order', 'status',
    ];

    protected $casts = ['settings' => 'array', 'status' => 'boolean'];

    public function column(): BelongsTo
    {
        return $this->belongsTo(WidgetColumn::class);
    }

    public function getComponentNameAttribute(): string
    {
        return "widgets.{$this->module}";
    }
}
