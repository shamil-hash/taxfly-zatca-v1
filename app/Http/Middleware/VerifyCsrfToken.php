<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
       'WebClientPrintController',
       'loginsubmit',
       'api/billing',
       'api/purchase',
       'api/product',
       'api/submitproduct',
       'api/submitpurchase',
       'api/purchaseHistory',
       'api/submitunit',
       'api/submitcategory',
       'api/submitsupplier',
       'api/submitbill',
       'api/submitcustomer',
       'api/listCategory',
       'api/dailysalesreport',
       'api/dailysalesreportpayment',
       'api/billhistory',
       'api/billhistorydate',
       'api/purchaseHistorydate',
       'api/billhistoryview',
       'api/purchaseHistoryview'



    ];
}
