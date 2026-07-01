<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'name', 'email', 'telephone', 'is_active', 'unsubscribe_token', 'date_deleted'];

    protected $casts = ['is_active' => 'boolean'];
}
