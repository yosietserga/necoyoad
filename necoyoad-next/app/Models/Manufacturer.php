<?php
declare(strict_types=1);
namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasDescriptions;
use App\Traits\HasStoreAssignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manufacturer extends Model
{
    use HasFactory, HasDescriptions, HasStoreAssignment;
    protected $fillable = ['name', 'image', 'sort_order'];
    public function products(): HasMany { return $this->hasMany(Product::class); }
}
