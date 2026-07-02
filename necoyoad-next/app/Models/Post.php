<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Auditable;

use App\Traits\HasDescriptions;
use App\Traits\HasProperties;
use App\Traits\HasSeoUrl;
use App\Traits\HasStoreAssignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory, HasDescriptions, HasProperties, HasStoreAssignment, HasSeoUrl;

    protected $fillable = [
        'type', 'parent_id', 'author_id', 'image', 'template',
        'publish', 'allow_reviews', 'sort_order', 'status',
        'date_publish_start', 'date_publish_end', 'viewed',
    ];

    protected $casts = [
        'publish' => 'boolean',
        'allow_reviews' => 'boolean',
        'status' => 'boolean',
    ];

    public function scopePages($query)
    {
        return $query->where('type', 'page');
    }

    public function scopePosts($query)
    {
        return $query->where('type', 'post');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Post::class, 'parent_id');
    }
}
