<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferType extends Model
{
    use HasFactory;
    protected $table = 'transfer_type';

    protected $fillable = [
        'transfer_type',
        'transfer_name',
    ];

}
