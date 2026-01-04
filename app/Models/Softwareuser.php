<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Softwareuser extends Authenticatable
{
    use Notifiable;



    protected $guard='softwareusers';

    use HasFactory;
    protected $fillable=[
        'name','username','password','location','admin_id','status','joined_date','access'
    ];

    protected $hidden=[
                'password','remember token',

    ];
    
    public function scopeLocationById($query, $userId)
    {
        return $query->where('id', $userId)->value('location');
    }
    
}

