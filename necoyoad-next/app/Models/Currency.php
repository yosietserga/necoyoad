<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'symbol_left', 'symbol_right', 'decimal_place', 'value', 'status'];
    protected $casts = ['value' => 'float', 'status' => 'boolean'];
}
