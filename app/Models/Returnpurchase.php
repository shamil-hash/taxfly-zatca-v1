<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Returnpurchase extends Model
{
    use HasFactory;
    protected $fillable=[
        'reciept_no','discount','comment','quantity','amount','shop_name',
    ];
}
