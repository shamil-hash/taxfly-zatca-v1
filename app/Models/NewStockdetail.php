<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewStockdetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id','method','approve', 'branch','discount_percent','discount', 'reciept_no', 'comment', 'product', 'buycost', 'rate', 'vat', 'sellingcost', 'is_box_or_dozen', 'unit', 'box_dozen_count', 'quantity', 'remain_stock_quantity', 'price', 'price_without_vat', 'payment_mode', 'supplier', 'supplier_id', 'invoice_date', 'file', 'created_at', 'updated_at'
    ];
}
