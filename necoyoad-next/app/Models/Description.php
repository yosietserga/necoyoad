<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Description extends Model
{
    use HasFactory;

    protected $fillable = [
        'describable_type', 'describable_id', 'language_id',
        'title', 'description', 'seo_title', 'meta_description', 'meta_keywords', 'params',
    ];

    protected $casts = ['params' => 'array'];

    public function describable(): MorphTo
    {
        return $this->morphTo();
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
