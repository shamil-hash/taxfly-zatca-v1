<?php

namespace App\Repositories\Interfaces;

interface EditPurchaseRepositoryInterface
{
    public function getPurchaseDetails($receiptNo, $branch, $field);
    public function getProducts($branch);
    public function getProductsByPurchase($receiptNo, $branch);
    public function updateProductPurchaseStock($productID, $newQuantity, $old_purchase_data, $old_quantities, $buycosts, $sellcosts, $rates, $vats, $key);
    public function checkStockDetailsExist($receiptNo, $productID, $branch);
    public function updateCommonStockDetails($request, $receiptNo, $productID, $branch);
    public function saveStockDetails($data, $request);
    public function saveStockHistories($data);
}
