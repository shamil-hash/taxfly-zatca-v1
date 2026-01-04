<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table='department_employee';
    protected $fillable = [
        'department',
        'user_id',
        'branch',
        'admin_id'
    ];

}
