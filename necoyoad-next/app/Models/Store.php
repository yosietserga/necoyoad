<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'folder', 'domain', 'is_default', 'status', 'settings'];

    protected $casts = [
        'settings' => 'array',
        'is_default' => 'boolean',
        'status' => 'boolean',
    ];

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'store_languages');
    }
}
