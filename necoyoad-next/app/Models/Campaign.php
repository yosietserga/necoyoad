<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'newsletter_id', 'name', 'subject', 'from_name', 'from_email',
        'replyto_email', 'trace_email', 'trace_click', 'embed_image',
        'repeat', 'date_start', 'date_end', 'status',
    ];

    protected $casts = [
        'trace_email' => 'boolean',
        'trace_click' => 'boolean',
        'embed_image' => 'boolean',
        'status' => 'boolean',
    ];

    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(CampaignLink::class);
    }
}
