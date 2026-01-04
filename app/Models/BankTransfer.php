<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransfer extends Model
{
    use HasFactory;
    protected $table='bank_transfer';
    protected $fillable = [
        'account_name',
        'transfer_type',
        'transfer_name',
        'transfer_amount',
        'ref_no',
        'date',
        'image',
        'remark',
    ];
}
