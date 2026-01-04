<?php

namespace App\Services;

use App\Repositories\MainRepository\PurchaseOrderRepository;

class purchaseorderService
{
    protected $purchaseorderRepo;

    public function __construct(PurchaseOrderRepository $purchaseorderRepo)
    {
        $this->purchaseorderRepo = $purchaseorderRepo;
    }
    public function getPurchaseOrderInfo($receiptNo, $branch, $field)
    {
        return $this->purchaseorderRepo->getPurchaseOrderDetails($receiptNo, $branch, $field);
    }

    // Example usage in your service class methods
    public function getComment($receiptNo, $branch)
    {
        return $this->getPurchaseOrderInfo($receiptNo, $branch, 'comment');
    }
    public function getSupplier($receiptNo, $branch)
    {
        return $this->getPurchaseOrderInfo($receiptNo, $branch, 'supplier');
    }
    public function getSupplierID($receiptNo, $branch)
    {
        return $this->getPurchaseOrderInfo($receiptNo, $branch, 'supplier_id');
    }
    public function getPaymentType($receiptNo, $branch)
    {
        return $this->getPurchaseOrderInfo($receiptNo, $branch, 'payment_mode');
    }
    public function getDeliveryDate($receiptNo, $branch)
    {
        return $this->getPurchaseOrderInfo($receiptNo, $branch, 'delivery_date');
    }
    public function getPurchaseOrderID($receiptNo, $branch)
    {
        return $this->getPurchaseOrderInfo($receiptNo, $branch, 'purchase_order_id');
    }
    public function getBankID($receiptNo, $branch)
    {
        return $this->getPurchaseOrderInfo($receiptNo, $branch, 'bank_id');
    }
    public function getAccountName($receiptNo, $branch)
    {
        return $this->getPurchaseOrderInfo($receiptNo, $branch, 'account_name');
    }

    public function getProducts($branch)
    {
        return $this->purchaseorderRepo->getProducts($branch);
    }

    public function getDataPlan($receiptNo, $branch)
    {
        return $this->purchaseorderRepo->getProductsByPurchase($receiptNo, $branch);
    }
}
