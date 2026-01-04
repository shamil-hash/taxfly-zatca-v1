<?php

namespace App\Repositories\Interfaces;

interface EditTransactionRepositoryInterface
{

    public function getProductsByBranch($branch);
    public function getBuyProductsByTransaction($transactionId);

    public function getTransactionDetails($transactionId, $field);
    public function getTransactionCount($transactionId);

    //edit transaction work

    public function deleteBuyproductsByTransactionId($transactionId);
    public function deleteStockdatsByTransactionId($transactionId);
    public function saveBuyproduct($data);
    public function saveStockdat($data);
    public function updateProductRemainingStock($productID, $newQuantity, $old_trans_data, $old_quantities);

    // new purchase wise

    public function findFirstPurchase($productID, $branch);
    public function existsBillHistory($transaction_id, $productID, $branch, $puid, $pid);
    public function getBillsellQuantity($transaction_id, $productID, $branch, $purchaseUID, $purchaseID);
    public function saveBillHistory($data);
}
