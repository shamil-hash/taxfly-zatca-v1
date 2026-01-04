<?php

namespace App\Repositories\MainRepository;
 
use App\Models\Product;
use App\Repositories\Interfaces\SalesOrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SalesOrderRepository implements SalesOrderRepositoryInterface
{
    public function getProByBranch($branch)
    {
        return Product::select(DB::raw("*"))
            ->where('branch', $branch)
            ->where('status', 1)
            ->get();
    }
    public function getalesoderByTransaction($transactionId)
    {
        // return DB::table('sales_orders')
        //     ->where('transaction_id', $transactionId)
        //     ->get();

        $products = DB::table('sales_orders')
            ->join('products', 'sales_orders.product_id', '=', 'products.id')
            ->where('sales_orders.transaction_id', $transactionId)
            ->select([
                'sales_orders.*',
                'products.id as product_id', // Include product ID for reference
                'products.product_name',
                'products.status', // Explicitly select product status
            ])
            ->get();

        return $products; // Return an array of objects
    }
    public function getSalesorderDetails($transactionId, $field)
    {
        return DB::table('sales_orders')
            ->where('transaction_id', $transactionId)
            ->pluck($field)
            ->first();
    }
    public function getSalesorderCount($transactionId)
    {
        return DB::table('sales_orders')
            ->where('transaction_id', $transactionId)
            ->distinct()
            ->count('id');
    }
}
