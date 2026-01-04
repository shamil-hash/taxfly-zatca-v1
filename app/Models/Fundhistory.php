<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fundhistory extends Model
{
    use HasFactory;
    protected $fillable=[
        'username','credituser_id','location','user_id','due'
    ];

}

