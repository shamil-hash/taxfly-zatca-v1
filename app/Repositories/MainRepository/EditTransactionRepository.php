<?php

namespace App\Repositories\MainRepository;

use App\Models\BillHistory;
use App\Models\Product;
use App\Models\Buyproduct;
use App\Models\NewBuyproduct;

use App\Models\Stockdat;

use App\Repositories\Interfaces\EditTransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EditTransactionRepository implements EditTransactionRepositoryInterface
{
    public function getProductsByBranch($branch)
    {
        return Product::select(DB::raw("*"))
            ->where('branch', $branch)
            ->where('status', 1)
            ->get();
    }

    public function getBuyProductsByTransaction($transactionId)
    {
        // return DB::table('buyproducts')
        //     ->where('transaction_id', $transactionId)
        //     ->get();

        // Join buyproducts and products tables
        $products = DB::table('buyproducts')
            ->join('products', 'buyproducts.product_id', '=', 'products.id')
            ->where('buyproducts.transaction_id', $transactionId)
            ->select([
                'buyproducts.*',
                'products.id as product_id', // Include product ID for reference
                'products.product_name',
                'products.status', // Explicitly select product status
            ])
            ->get();

        return $products; // Return an array of objects
    }

    public function getTransactionDetails($transactionId, $field)
    {
        return DB::table('buyproducts')
            ->where('transaction_id', $transactionId)
            ->pluck($field)
            ->first();
    }
    public function getTransactionCount($transactionId)
    {
        return DB::table('buyproducts')
            ->where('transaction_id', $transactionId)
            ->distinct()
            ->count('id');
    }

    // EDIT TRANSACTION WORKS

    public function deleteBuyproductsByTransactionId($transactionId)
    {
        Buyproduct::where('transaction_id', $transactionId)->delete();
        NewBuyproduct::where('transaction_id', $transactionId)->delete();

    }
    public function deleteStockdatsByTransactionId($transactionId)
    {
        Stockdat::where('transaction_id', $transactionId)->delete();
    }

    public function saveBuyproduct($data)
    {
        $buyproduct = new Buyproduct();
        $buyproduct->fill($data);
        $buyproduct->save();

        $buyproduct = new NewBuyproduct();
        $buyproduct->fill($data);
        $buyproduct->save();
    }

    public function saveStockdat($data)
    {
        $stockdat = new Stockdat();
        $stockdat->fill($data);
        $stockdat->save();
    }

    public function updateProductRemainingStock($productID, $newQuantity, $old_trans_data, $old_quantities)
    {
        $productModel = Product::find($productID);

        if ($old_trans_data->contains('product_id', $productID)) {
            /* Product ID exists in the transaction */
            $oldQuantity = $old_quantities[$productID];

            if ($newQuantity > $oldQuantity) {
                $quantity_diff = $newQuantity - $oldQuantity;

                $productModel->remaining_stock -= $quantity_diff;
            } elseif ($oldQuantity > $newQuantity) {
                $quantity_diff = $oldQuantity - $newQuantity;

                $productModel->remaining_stock += $quantity_diff;
            }
        } else {
            /* Product ID does not exist in the transaction */
            $productModel->remaining_stock -= $newQuantity;
        }

        $productModel->save();
    }

    public function findFirstPurchase($productID, $branch)
    {
        return DB::table('stock_purchase_reports')
            ->select(DB::raw('*'))
            ->where('sell_quantity', '>', 0)
            ->where('product_id', $productID)
            ->where('branch_id', $branch)
            ->orderBy('created_at', 'ASC')
            ->first();
    }
    public function existsBillHistory($transaction_id, $productID, $branch, $puid, $pid)
    {
        return BillHistory::where('trans_id', $transaction_id)
            ->where('product_id', $productID)
            ->where('branch_id', $branch)
            ->where('puid', $puid)
            ->where('pid', $pid)
            ->exists();
    }

    public function getBillsellQuantity($transaction_id, $productID, $branch, $purchaseUID, $purchaseID)
    {
        return BillHistory::where('trans_id', $transaction_id)
            ->where('product_id', $productID)
            ->where('branch_id', $branch)
            ->where('puid', $purchaseUID)
            ->where('pid', $purchaseID)
            ->pluck('remain_sold_quantity')
            ->first();
    }

    public function saveBillHistory($data)
    {
        $billstore = new BillHistory();
        $billstore->fill($data);
        $billstore->save();
    }
}
