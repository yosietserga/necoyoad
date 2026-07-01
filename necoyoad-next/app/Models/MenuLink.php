<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasDescriptions;
use App\Traits\HasProperties;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuLink extends Model
{
    use HasFactory, HasDescriptions, HasProperties;

    protected $fillable = ['menu_id', 'parent_id', 'link', 'tag', 'sort_order'];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuLink::class, 'parent_id')->orderBy('sort_order');
    }
}
