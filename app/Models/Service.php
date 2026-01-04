<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
protected $table='service';
    protected $fillable = ['service_id','user_id','branch','customer','address','phone','service_name','payment_mode','quantity', 'total_amount'];
}
