<?php

namespace App\Repositories\MainRepository;

use App\Models\Product;
use App\Repositories\Interfaces\QuotationRepositoryInterface;
use Illuminate\Support\Facades\DB;

class QuotationRepository implements QuotationRepositoryInterface
{
    public function getQuotationProByBranch($branch)
    {
        return Product::select(DB::raw("*"))
            ->where('branch', $branch)
            ->where('status', 1)
            ->get();
    }
    public function getQuotationByTransaction($transactionId)
    {


        $products = DB::table('quotations')
            ->join('products', 'quotations.product_id', '=', 'products.id')
            ->where('quotations.transaction_id', $transactionId)
            ->select([
                'quotations.*',
                'products.id as product_id', // Include product ID for reference
                'products.product_name',
                'products.status', // Explicitly select product status
            ])
            ->get();

        return $products; // Return an array of objects
    }
    public function getQuotationDetails($transactionId, $field)
    {
        return DB::table('quotations')
            ->where('transaction_id', $transactionId)
            ->pluck($field)
            ->first();
    }
    public function getQuotationCount($transactionId)
    {
        return DB::table('quotations')
            ->where('transaction_id', $transactionId)
            ->distinct()
            ->count('id');
    }
}
