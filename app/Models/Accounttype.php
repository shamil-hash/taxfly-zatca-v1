<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accounttype extends Model
{

    use HasFactory;
    protected $table='account_type';
    protected $fillable=[
        	'type','category','categorytype','branch'];

}
