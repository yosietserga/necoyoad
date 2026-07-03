<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContactList extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'status'];
    protected $casts = ['status' => 'boolean'];
    public function contacts(): BelongsToMany { return $this->belongsToMany(Contact::class, 'contact_list_subscriptions'); }
}
