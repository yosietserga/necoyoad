<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTotal extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['order_id', 'title', 'text', 'value', 'sort_order'];
    protected $casts = ['value' => 'decimal:4'];
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
