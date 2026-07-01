<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Newsletter extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'textbody', 'htmlbody', 'status'];
    protected $casts = ['status' => 'boolean'];
    public function campaigns(): HasMany { return $this->hasMany(Campaign::class); }
}
