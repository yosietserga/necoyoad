<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['user_id', 'activitable_type', 'activitable_id', 'event', 'action', 'description', 'ip', 'browser', 'date_added'];
    public function activitable(): MorphTo { return $this->morphTo(); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
