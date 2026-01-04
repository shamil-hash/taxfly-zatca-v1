<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salarydata extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id','date','salary','employee_id','branch_id',
    ];
}
