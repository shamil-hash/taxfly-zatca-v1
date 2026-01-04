<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bank extends Model
{

    use HasFactory;
    protected $table='bank';
    protected $fillable = [
        'date',
        'account_name',
        'account_no',
        'opening_balance',
        'current_balance',
        'bank_name',
        'branch_name',
        'ifsc_code',
        'iban_code',
        'account_type',
        'country',
        'upi_id',
    ];

}