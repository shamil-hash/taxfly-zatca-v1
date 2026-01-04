<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewBuyproduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_name',
        'product_id',
        'quantity',
        'remain_quantity',
        'unit',
        'one_pro_buycost',
        'one_pro_buycost_rate',
        'mrp',
        'inclusive_rate',
        'exclusive_rate',
        'price',
        'price_wo_discount',
        'vat_amount',
        'fixed_vat',
        'branch',
        'transaction_id',
        'customer_name',
        'trn_number',
        'phone',
        'payment_type',
        'created_at',
        'user_id',
        'email',
        'netrate',
        'total_amount',
        'totalamount_wo_discount',
        'discount_type',
        'discount',
        'discount_amount',
        'buycostadd',
        'buycost_rate_add',
        'credit_user_id',
        'vat_type',
        'total_discount_type',
        'total_discount_percent',
        'total_discount_amount',
        'bill_grand_total',
        'bill_grand_total_wo_discount',
        'edit',
        'edit_comment',
        'to_invoice',
        'sales_order_trans_ID',
        'cash_user_id',
        'quotation_trans_ID',
               'bank_id',
        'account_name',
               'employee_id',
        'employee_name',
        'credit_note',
        'approve',
        'quantity_type',
        'box_count'
    ];
}
