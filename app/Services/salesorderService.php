<?php

namespace App\Services;

use App\Repositories\MainRepository\SalesOrderRepository;

class salesorderService
{
    protected $salesorderRepo;

    public function __construct(SalesOrderRepository $salesorderRepo)
    {
        $this->salesorderRepo = $salesorderRepo;
    }

    public function getProItems($branch)
    {
        return $this->salesorderRepo->getProByBranch($branch);
    }
    public function getSalesoderDatas($transactionId)
    {
        return $this->salesorderRepo->getalesoderByTransaction($transactionId);
    }
    public function getSalesorderInfo($transactionId, $field)
    {
        return $this->salesorderRepo->getSalesorderDetails($transactionId, $field);
    }

    // Example usage in your service class methods
    public function getCusName($transactionId)
    {
        return $this->getSalesorderInfo($transactionId, 'customer_name');
    }

    public function getSTRN($transactionId)
    {
        return $this->getSalesorderInfo($transactionId, 'trn_number');
    }
    public function getSPhone($transactionId)
    {
        return $this->getSalesorderInfo($transactionId, 'phone');
    }
    public function getSPaymentType($transactionId)
    {
        return $this->getSalesorderInfo($transactionId, 'payment_type');
    }
    public function getSEmail($transactionId)
    {
        return $this->getSalesorderInfo($transactionId, 'email');
    }

    public function getSalesorder_count($transactionId)
    {
        return $this->salesorderRepo->getSalesorderCount($transactionId);
    }

    public function getSVatType($transactionId)
    {
        return $this->getSalesorderInfo($transactionId, 'vat_type');
    }
    public function getSCreditUserId($transactionId)
    {
        return $this->getSalesorderInfo($transactionId, 'credit_user_id');
    }
    public function getSCreatedAt($transactionId)
    {
        return $this->getSalesorderInfo($transactionId, 'created_at');
    }
    public function getCashUserId($transactionId)
    {
        return $this->getSalesorderInfo($transactionId, 'cash_user_id');
    }
    public function getBankid($transactionId)
    {
        return $this->getSalesorderInfo($transactionId, 'bank_id');
    }
    public function getAccountname($transactionId)
    {
        return $this->getSalesorderInfo($transactionId, 'account_name');
    }
    public function getEmployeeid($transactionId)
    {
        return $this->getSalesorderInfo($transactionId, 'employee_id');
    }
    public function getEmployeename($transactionId)
    {
        return $this->getSalesorderInfo($transactionId, 'employee_name');
    }
}
