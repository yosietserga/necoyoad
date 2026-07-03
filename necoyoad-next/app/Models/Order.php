<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'store_id', 'customer_id', 'customer_group_id',
        'firstname', 'lastname', 'email', 'telephone',
        'shipping_address', 'payment_address',
        'shipping_method', 'payment_method',
        'total', 'order_status_id', 'language_id', 'currency_id', 'ip',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'payment_address' => 'array',
        'total' => 'decimal:4',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function totals(): HasMany
    {
        return $this->hasMany(OrderTotal::class);
    }
}
