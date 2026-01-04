<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockPurchaseReport extends Model
{
    use HasFactory;

    protected $fillable = [
        // ... other fillable fields
        'sell_quantity',
    ];
    public static function updateSellQuantity($purchaseTransId, $purchaseId, $productID, $quantityToUpdate)
    {
        self::where('purchase_trans_id', $purchaseTransId)
            ->where('purchase_id', $purchaseId)
            ->where('product_id', $productID)
            ->update(['sell_quantity' => $quantityToUpdate]);
    }
    public static function getSellQuantity($purchaseTransId, $purchaseId, $branch)
    {
        return self::where('purchase_trans_id', $purchaseTransId)
            ->where('purchase_id', $purchaseId)
            ->where('branch_id', $branch)
            ->pluck('sell_quantity')
            ->first();
    }
    public static function updateBuyTimeSellQuantity($purchaseId, $productID, $branch, $receipt_no, $quantityToUpdate)
    {
        self::where('purchase_id', $purchaseId)
            ->where('product_id', $productID)
            ->where('branch_id', $branch)
            ->where('receipt_no', $receipt_no)
            ->update([
                'sell_quantity' => $quantityToUpdate
            ]);
    }
}
