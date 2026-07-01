<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignStat extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['campaign_id', 'contact_id', 'customer_id', 'store_url', 'server', 'session', 'request', 'ref', 'browser', 'ip', 'date_added'];
    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
}
