<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Credituser extends Authenticatable
{
    use Notifiable;

    protected $guard = 'creditusers';

    use HasFactory;
    protected $fillable = [
        'name', 'username', 'password', 'location', 'admin_id', 'status',
    ];

    protected $hidden = [
        'password', 'remember token',

    ];

 
}
