<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'type', 'discount', 'logged', 'shipping', 'total', 'date_start', 'date_end', 'uses_total', 'status'];
    protected $casts = ['discount' => 'decimal:4', 'total' => 'decimal:4', 'logged' => 'boolean', 'shipping' => 'boolean', 'status' => 'boolean'];
}
