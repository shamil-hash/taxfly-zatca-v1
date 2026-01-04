<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Returnproduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_name',
        'quantity',
        'unit',
        'one_pro_buycost',
        'one_pro_buycost_rate',
        'inclusive_rate',
        'mrp',
        'transaction_id',
        'price',
        'price_wo_discount',
        'vat',
        'branch',
        'user_id',
        'product_id',
        'email',
        'trn_number',
        'phone',
        'netrate',
        'total_amount',
        'totalamount_wo_discount',
        'discount',
        'discount_amount',
        'payment_type',
        'fixed_vat',
        'vat_amount',
        'buycostaddreturn',
        'buycost_rate_addreturn',
        'creditusers_id',
        'vat_type',
        'total_discount_percent',
        'total_discount_amount',
        'grand_total',
'grand_total_wo_discount',
        'return_id'
        ];
}
