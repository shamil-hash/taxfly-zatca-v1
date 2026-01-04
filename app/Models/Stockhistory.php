<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockhistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'user_id','discount_percent','discount', 'buycost', 'rate', 'vat', 'sellingcost', 'product_name', 'receipt_no', 'receipt_no', 'quantity', 'remain_qantity', 'sell_qantity', 'created_at', 'updated_at'
    ];
}
