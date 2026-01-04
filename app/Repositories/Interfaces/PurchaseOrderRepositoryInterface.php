<?php

namespace App\Repositories\Interfaces;

interface PurchaseOrderRepositoryInterface
{
    public function getPurchaseOrderDetails($receiptNo, $branch, $field);
    public function getProducts($branch);
    public function getProductsByPurchase($receiptNo, $branch);
}
