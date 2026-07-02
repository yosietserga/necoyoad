<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThemeFileVersion extends Model
{
    protected $table = 'theme_file_versions';

    protected $fillable = [
        'theme', 'file_path', 'content', 'user_id', 'checksum',
    ];

    protected $casts = [
        'content' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
