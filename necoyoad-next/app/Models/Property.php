<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'propertiable_type', 'propertiable_id', 'store_id',
        'group', 'key', 'value', 'sort_order',
    ];

    public function propertiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getDecodedValue(): mixed
    {
        $value = $this->value;
        $json = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }
        return $value;
    }
}
