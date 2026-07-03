<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WidgetColumn extends Model
{
    use HasFactory;

    protected $table = 'widget_columns';

    protected $fillable = ['row_id', 'key', 'settings', 'sort_order'];

    protected $casts = ['settings' => 'array'];

    public function widgets(): HasMany
    {
        return $this->hasMany(Widget::class, 'column_id')->orderBy('sort_order');
    }

    public function row(): BelongsTo
    {
        return $this->belongsTo(WidgetRow::class, 'row_id');
    }
}
