<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'firstname', 'lastname', 'company', 'address_1', 'address_2', 'city', 'postcode', 'country_id', 'zone_id'];
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
}
