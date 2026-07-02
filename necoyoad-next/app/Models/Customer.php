<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'customer';

    protected $fillable = [
        'store_id', 'customer_group_id', 'firstname', 'lastname',
        'email', 'password', 'telephone', 'birthday', 'newsletter',
        'status', 'approved', 'visits',
    ];

    protected $hidden = ['password', 'remember_token'];
}
