<?php

namespace App\Services;

use App\Repositories\MainRepository\QuotationRepository;

class QuotationService
{
    protected $quotationRepo;

    public function __construct(QuotationRepository $quotationRepo)
    {
        $this->quotationRepo = $quotationRepo;
    }

    public function getQuotationProItems($branch)
    {
        return $this->quotationRepo->getQuotationProByBranch($branch);
    }
    public function getQuotationDatas($transactionId)
    {
        return $this->quotationRepo->getQuotationByTransaction($transactionId);
    }
    public function getQuotationInfo($transactionId, $field)
    {
        return $this->quotationRepo->getQuotationDetails($transactionId, $field);
    }

    // Example usage in your service class methods
    public function getQuotationCusName($transactionId)
    {
        return $this->getQuotationInfo($transactionId, 'customer_name');
    }

    public function getQuotationTRN($transactionId)
    {
        return $this->getQuotationInfo($transactionId, 'trn_number');
    }
    public function getQuotationPhone($transactionId)
    {
        return $this->getQuotationInfo($transactionId, 'phone');
    }
    public function getQuotationPaymentType($transactionId)
    {
        return $this->getQuotationInfo($transactionId, 'payment_type');
    }
    public function getQuotationEmail($transactionId)
    {
        return $this->getQuotationInfo($transactionId, 'email');
    }

    public function getQuotation_count($transactionId)
    {
        return $this->quotationRepo->getQuotationCount($transactionId);
    }

    public function getQuotationVatType($transactionId)
    {
        return $this->getQuotationInfo($transactionId, 'vat_type');
    }
    public function getQuotationCreditUserId($transactionId)
    {
        return $this->getQuotationInfo($transactionId, 'credit_user_id');
    }
    public function getQuotationCreatedAt($transactionId)
    {
        return $this->getQuotationInfo($transactionId, 'created_at');
    }
    public function getQuotationCashUserId($transactionId)
    {
        return $this->getQuotationInfo($transactionId, 'cash_user_id');
    }
    public function getQuotationBankid($transactionId)
    {
        return $this->getQuotationInfo($transactionId, 'bank_id');
    }
    public function getQuotationAccountname($transactionId)
    {
        return $this->getQuotationInfo($transactionId, 'account_name');
    }
    public function getQuotationEmployeeid($transactionId)
    {
        return $this->getQuotationInfo($transactionId, 'employee_id');
    }
    public function getQuotationEmployeename($transactionId)
    {
        return $this->getQuotationInfo($transactionId, 'employee_name');
    }
}
