<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Contact extends Model
{
    use HasFactory, Auditable;

    protected $fillable = ['customer_id', 'name', 'email', 'telephone', 'is_active', 'unsubscribe_token', 'date_deleted'];

    protected $casts = ['is_active' => 'boolean'];

    /**
     * Auto-generate unsubscribe_token on creation (CAN-SPAM compliance).
     */
    protected static function booted(): void
    {
        static::creating(function (Contact $contact) {
            if (empty($contact->unsubscribe_token)) {
                $contact->unsubscribe_token = Str::random(64);
            }
        });
    }

    /**
     * Contact lists this contact is subscribed to (many-to-many via
     * contact_list_subscriptions pivot table).
     */
    public function contactLists(): BelongsToMany
    {
        return $this->belongsToMany(ContactList::class, 'contact_list_subscriptions');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
