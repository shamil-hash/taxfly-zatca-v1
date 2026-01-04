<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use App\Models\Product;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductsExport implements FromView
{
    protected $userid;

    function __construct($userid) {
            $this->userid = $userid;

    }
    public function view(): View
    {
        $product = Product::leftJoin('categories', 'categories.id', '=', 'products.category_id')
                    ->select(DB::raw("products.product_name,products.productdetails	,products.unit,products.buy_cost,products.purchase_vat,products.rate,products.inclusive_rate,products.inclusive_vat_amount,products.selling_cost,products.vat ,products.barcode ,categories.category_name "))
                    ->where('products.user_id', $this->userid)
                    // ->where('products.stock', '!=', 0)
                    // ->where('products.status', 1)
                    // ->where('products.branch', 1)
                    ->get();

        return view('exports.productexport', ['products' => $product]);
    }
}
