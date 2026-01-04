<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable=[
       'product_name','quantity_enabled','box_enabled','box_count','box_sell_cost','productdetails','unit','rate', 'purchase_vat','buy_cost','selling_cost','inclusive_rate', 'inclusive_vat_amount','vat','status','user_id','branch','category_id','product_code','barcode','image',
    ];
    
}
