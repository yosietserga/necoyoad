<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Language extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'locale', 'directory', 'sort_order', 'status'];

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'store_languages');
    }
}
