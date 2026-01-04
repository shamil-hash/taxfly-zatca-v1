<?php

namespace App\Repositories\Interfaces;

interface QuotationRepositoryInterface
{
    public function getQuotationProByBranch($branch);
    public function getQuotationByTransaction($transactionId);
    public function getQuotationDetails($transactionId, $field);
    public function getQuotationCount($transactionId);
}
