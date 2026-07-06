<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Auditable, Notifiable;

    protected $fillable = ['username', 'password', 'firstname', 'lastname', 'email', 'image', 'status', 'ip'];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Filament expects a 'name' attribute for the user display name.
     * Our schema uses firstname + lastname, so we virtualize it.
     */
    public function getNameAttribute(): string
    {
        return trim($this->firstname . ' ' . $this->lastname) ?: $this->username;
    }
}
