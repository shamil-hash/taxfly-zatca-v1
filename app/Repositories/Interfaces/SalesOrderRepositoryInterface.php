<?php

namespace App\Repositories\Interfaces;

interface SalesOrderRepositoryInterface
{
    public function getProByBranch($branch);
    public function getalesoderByTransaction($transactionId);
    public function getSalesorderDetails($transactionId, $field);
    public function getSalesorderCount($transactionId);
}
