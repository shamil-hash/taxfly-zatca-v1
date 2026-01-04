<?php

namespace App\Repositories\MainRepository;

use App\Models\Product;
use App\Models\Stockdetail;
use App\Models\Stockhistory;
use App\Repositories\Interfaces\EditPurchaseRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EditPurchaseRepository implements EditPurchaseRepositoryInterface
{
    public function getPurchaseDetails($receiptNo, $branch, $field)
    {
        return DB::table('stockdetails')
            ->where('reciept_no', $receiptNo)
            ->where('branch', $branch)
            ->pluck($field)
            ->first();
    }
    public function getProducts($branch)
    {
        return DB::table('products')
            ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
            ->select(DB::raw("products.product_name as product_name,products.id as id, products.unit as unit,products.buy_cost as buy_cost, products.selling_cost as selling_cost,products.rate as rate,products.purchase_vat as purchase_vat"))
            ->groupBy('products.id')
            ->where('products.branch', $branch)
            ->where('products.status', 1)
            ->orderBy('products.id')
            ->get();
    }

    public function getProductsByPurchase($receiptNo, $branch)
    {

        return DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->leftJoin('stock_purchase_reports', function ($join) {
                $join->on(DB::raw('CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_general_ci'), '=', DB::raw('CONVERT(stock_purchase_reports.receipt_no USING utf8mb4) COLLATE utf8mb4_general_ci'))
                    ->on('stockdetails.product', '=', 'stock_purchase_reports.product_id');
            })
            ->select(DB::raw("stockdetails.*, products.product_name, stock_purchase_reports.quantity, stock_purchase_reports.sell_quantity, products.status"))
            ->where('stockdetails.reciept_no', $receiptNo)
            ->where('stockdetails.branch', $branch)
            ->get();
    }
    public function checkStockDetailsExist($receiptNo, $productID, $branch)
    {
        return Stockdetail::where('reciept_no', $receiptNo)
            ->where('product', $productID)
            ->where('branch', $branch)
            ->exists();
    }
    public function updateCommonStockDetails($request, $receiptNo, $productID, $branch)
    {
        Stockdetail::where('reciept_no', $receiptNo)
            ->where('product', $productID)
            ->where('branch', $branch)
            ->update([
                'comment' => $request->comment,
                'invoice_date' => $request->invoice_date,
                'edit' => 1,
                'edit_comment' => $request->edit_comment,
            ]);
    }

    public function saveStockDetails($data, $request)
    {
        $stockdatas = new Stockdetail();
        $stockdatas->fill($data);

        // Check if the 'camera' file is present in the request
        if (!empty($request->file('camera'))) {
            $ext = $request->file('camera')->getClientOriginalExtension();
            $filename = 'STOCK_DAT' . date('d-m-y_h-i-s') . '.' . $ext;

            // Update the 'file' column in the stockdatas record
            $stockdatas->file = $filename;

            // Store the file in the 'stockbills' directory
            $path = $request->file('camera')->storeAs('stockbills', $filename);
        }

        $stockdatas->save();

        return $stockdatas->id;
    }

    public function updateProductPurchaseStock($productID, $newQuantity, $old_purchase_data, $old_quantities, $buycosts, $sellcosts, $rates, $vats, $key)
    {
        $productModel = Product::find($productID);
        // Fetch vat from the products table
        $productVat = $productModel->vat;

        $productModel->buy_cost = $buycosts[$key];
        $productModel->selling_cost = $sellcosts[$key];

        $productModel->rate = $rates[$key];
        $productModel->purchase_vat = $vats[$key];

        // Calculate inclusive rate and vat   //add
        if (($sellcosts[$key] != '' || $sellcosts[$key] != null) && ($productVat != '' || $productVat != null)) {
            $inclusive_rate = $sellcosts[$key] / (1 + ($productVat / 100));
            $inclusive_vat_amount = $sellcosts[$key] - $inclusive_rate;

            // Store inclusive rate and vat in the products table
            $productModel->inclusive_rate = $inclusive_rate;
            $productModel->inclusive_vat_amount = $inclusive_vat_amount;
        }

        if ($old_purchase_data->contains('product', $productID)) {

            /* Product ID exists in the transaction */
            $oldQuantity = $old_quantities[$productID];

            if ($newQuantity > $oldQuantity) {
                $quantity_diff = $newQuantity - $oldQuantity;

                $productModel->stock += $quantity_diff;
                $productModel->remaining_stock += $quantity_diff;
            } elseif ($oldQuantity > $newQuantity) {
                $quantity_diff = $oldQuantity - $newQuantity;

                $productModel->stock -= $quantity_diff;
                $productModel->remaining_stock -= $quantity_diff;
            }
        } else {

            /* Product ID does not exist in the transaction */
            $productModel->stock += $newQuantity;
            $productModel->remaining_stock += $newQuantity;
        }

        $productModel->save();
    }
    public function saveStockHistories($data)
    {
        $st_datas = new Stockhistory();
        $st_datas->fill($data);
        $st_datas->save();
    }
}
