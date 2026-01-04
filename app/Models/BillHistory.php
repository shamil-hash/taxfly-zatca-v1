<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillHistory extends Model
{
    use HasFactory;

    protected $fillable = [

        'trans_id',
        'product_id',
        'puid',
        'pid',
        'receipt_no',
        'sold_quantity',
        'remain_sold_quantity',
        'branch_id',
        'user_id',
        'Purchase_buycost',
        'Purchase_Buycost_Rate',
        'billing_Sellingcost',
        'billing_inclusive_rate',
        'billing_exclusive_rate',
        'netrate',
        'discount_type',
        'discount',
        'discount_amount',
        'return_discount',
        'return_discount_amount',
        'total_discount_type',
        'total_discount_percent',
        'total_discount_amount',
        'bill_grand_total',
        'bill_grand_total_wo_discount',
        'return_total_discount_percent',
        'return_total_discount_amt',
        'return_grand_total',
        'return_grand_total_wo_discount',
        'credit_note_amount'
    ];

    public static function updateQuantity($transaction_id, $productID, $branch, $purchaseUID, $purchaseID, $quantity, $request)
    {
        self::where('trans_id', $transaction_id)
            ->where('product_id', $productID)
            ->where('branch_id', $branch)
            ->where('puid', $purchaseUID)
            ->where('pid', $purchaseID)
            ->update([
                'remain_sold_quantity' => $quantity,
                'sold_quantity' => $quantity,
                // 'discount' => $request->dis_count[$productID],
                // 'discount_amount' => ($request->dis_count[$productID] != 0) ? (($quantity * $request->net_rate[$productID]) * ($request->dis_count[$productID] / 100)) : NULL,
                'discount' => $request->dis_count_type[$productID] == "none" ? $request->dis_count[$productID] : ($request->dis_count_type[$productID] == "percentage" ? $request->dis_count[$productID] : ($request->dis_count_type[$productID] == "amount" ? ($request->dis_count[$productID] / ($request->vat_type_value == 1 ? $request->mrp[$productID] : $request->mrp[$productID])) * 100 : 0)),
                'discount_amount' => $request->dis_count_type[$productID] == "percentage" ?
                    ($request->vat_type_value == 1 ? $request->mrp[$productID] * ($request->dis_count[$productID] / 100) : $request->mrp[$productID] * ($request->dis_count[$productID] / 100)) : ($request->dis_count_type[$productID] == "amount" ? $request->dis_count[$productID] : 0),
            ]);
    }
}
