<?php

namespace App\Repositories\MainRepository;

use App\Models\Product;
use App\Repositories\Interfaces\PurchaseOrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PurchaseOrderRepository implements PurchaseOrderRepositoryInterface
{
    public function getPurchaseOrderDetails($receiptNo, $branch, $field)
    {
        return DB::table('purchase_orders')
            ->where('reciept_no', $receiptNo)
            ->where('branch', $branch)
            ->pluck($field)
            ->first();
    }
    public function getProducts($branch)
    {
        return DB::table('products')
            ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
            ->select(DB::raw("products.product_name as product_name,products.id as id, products.unit as unit,products.buy_cost as buy_cost, products.selling_cost as selling_cost,products.rate as rate,products.purchase_vat as purchase_vat"))
            ->groupBy('products.id')
            ->where('products.branch', $branch)
            ->where('products.status', 1)
            ->orderBy('products.id')
            ->get();
    }

    public function getProductsByPurchase($receiptNo, $branch)
    {
        return DB::table('purchase_orders')
            ->leftJoin('products', 'purchase_orders.product', '=', 'products.id')
            ->select(DB::raw("purchase_orders.*, products.id as product_id,products.product_name, products.status"))
            ->where('purchase_orders.reciept_no', $receiptNo)
            ->where('purchase_orders.branch', $branch)
            ->get();
    }
}
