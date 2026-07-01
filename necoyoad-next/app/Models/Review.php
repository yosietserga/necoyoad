<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasFactory;
    protected $fillable = ['reviewable_type', 'reviewable_id', 'customer_id', 'parent_id', 'author', 'text', 'rating', 'status'];
    protected $casts = ['status' => 'boolean'];
    public function reviewable(): MorphTo { return $this->morphTo(); }
    public function children(): HasMany { return $this->hasMany(Review::class, 'parent_id'); }
}
