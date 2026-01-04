<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraProducts extends Model
{
    use HasFactory;
    protected $table='extra_products';
     protected $fillable = [
        'product_id',
        'product_name',
        'buy_cost',
        'inclusive_vat_amount',
        'rate',
        'inclusive_rate',
        'purchase_vat',
        'selling_cost',
        'vat'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
