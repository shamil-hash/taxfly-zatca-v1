<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accountexpense extends Model
{
    use HasFactory;

    protected $fillable=[
        'direct_expense','indirect_expense','details','amount','month','branch','user_id','file',
    ];
}
