<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesReturnExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $userid;
    protected $location;
    protected $date;
    protected $viewtype;

    function __construct($userid, $viewtype, $location, $date)
    {
        $this->userid = $userid;
        $this->location = $location;
        $this->date = $date;
        $this->viewtype = $viewtype;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->viewtype == 1) {

            $data = DB::table('accountantlocs')
                ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
                ->selectRaw("
                    returnproducts.transaction_id as transaction_id,
                    products.product_name as product_name,
                    returnproducts.quantity as quantity,

                    CASE
                    WHEN returnproducts.discount_amount != 0 THEN (returnproducts.total_amount + returnproducts.discount_amount)
                    ELSE returnproducts.total_amount
                    END as grandtotal_withoutdiscount,
                    CASE
                    WHEN returnproducts.discount_amount != 0 THEN returnproducts.discount_amount
                    ELSE NULL
                    END as discount_amount,
                    returnproducts.total_amount as sum,
                    returnproducts.vat_amount as vat,
                    returnproducts.created_at as created_at
                ")
                // ->select(DB::raw("returnproducts.transaction_id, products.product_name, returnproducts.quantity, returnproducts.price as price, ((returnproducts.total_amount) - (returnproducts.price)) as vat, returnproducts.total_amount as sum, returnproducts.created_at"),)
                // ->groupBy('returnproducts.transaction_id')
                ->orderBy('returnproducts.created_at', 'ASC')
                ->whereDate('returnproducts.created_at', $this->date)
                ->where('accountantlocs.user_id', $this->userid)
                ->where('returnproducts.branch', $this->location)
                ->get();

            return $data;
        }
        if ($this->viewtype == 2) {

            $data = DB::table('accountantlocs')
                ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
                ->selectRaw("
                    returnproducts.transaction_id as transaction_id,
                    products.product_name as product_name,
                    returnproducts.quantity as quantity,

                    CASE
                    WHEN returnproducts.discount_amount != 0 THEN (returnproducts.total_amount + returnproducts.discount_amount)
                    ELSE returnproducts.total_amount
                    END as grandtotal_withoutdiscount,
                    CASE
                    WHEN returnproducts.discount_amount != 0 THEN returnproducts.discount_amount
                    ELSE NULL
                    END as discount_amount,
                    returnproducts.total_amount as sum,
                    returnproducts.vat_amount as vat,
                    returnproducts.created_at as created_at
                ")
                // ->select(DB::raw("returnproducts.transaction_id, products.product_name, returnproducts.quantity, returnproducts.price as price, ((returnproducts.total_amount) - (returnproducts.price)) as vat, returnproducts.total_amount as sum, returnproducts.created_at"),)
                // ->groupBy('returnproducts.transaction_id')
                ->orderBy('returnproducts.created_at', 'ASC')
                ->whereMonth('returnproducts.created_at', $this->date)
                ->where('accountantlocs.user_id', $this->userid)
                ->where('returnproducts.branch', $this->location)
                ->get();

            return $data;
        }
        if ($this->viewtype == 3) {

            $data = DB::table('accountantlocs')
                ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
                ->selectRaw("
                    returnproducts.transaction_id as transaction_id,
                    products.product_name as product_name,
                    returnproducts.quantity as quantity,

                    CASE
                    WHEN returnproducts.discount_amount != 0 THEN (returnproducts.total_amount + returnproducts.discount_amount)
                    ELSE returnproducts.total_amount
                    END as grandtotal_withoutdiscount,
                    CASE
                    WHEN returnproducts.discount_amount != 0 THEN returnproducts.discount_amount
                    ELSE NULL
                    END as discount_amount,
                    returnproducts.total_amount as sum,
                    returnproducts.vat_amount as vat,
                    returnproducts.created_at as created_at
                ")
                // ->select(DB::raw("returnproducts.transaction_id, products.product_name, returnproducts.quantity, returnproducts.price as price, ((returnproducts.total_amount) - (returnproducts.price)) as vat, returnproducts.total_amount as sum, returnproducts.created_at"),)
                // ->groupBy('returnproducts.transaction_id')
                ->orderBy('returnproducts.created_at', 'ASC')
                ->whereYear('returnproducts.created_at', $this->date)
                ->where('accountantlocs.user_id', $this->userid)
                ->where('returnproducts.branch', $this->location)
                ->get();

            return $data;
        }
    }
    public function headings(): array
    {
        return [
            'Transaction ID',
            'Product Name',
            'Quantity',
            'Total Price',
            'Discount Amount',
            'Grand Total (w/. discount)',
            'VAT',
            'Date and Time',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:H1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true)->setSize(14);
            },
        ];
    }
}
