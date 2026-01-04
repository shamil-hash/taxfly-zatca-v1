<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockdat extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'product_id', 'transaction_id', 'stock_num', 'one_pro_buycost', 'one_pro_buycost_rate', 'one_pro_sellingcost', 'one_pro_inclusive_rate','credit_note_amount', 'netrate', 'created_at'
    ];
}
