<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerGroup extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'params', 'status'];
    protected $casts = ['params' => 'array', 'status' => 'boolean'];
    public function customers(): HasMany { return $this->hasMany(Customer::class); }
}
