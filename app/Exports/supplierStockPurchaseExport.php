<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use App\Models\Stockdetail;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class supplierStockPurchaseExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $supplier;
    protected $payment_mode;


    function __construct($supplier, $payment_mode)
    {
        $this->supplier = $supplier;
        $this->payment_mode = $payment_mode;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        // if ($this->payment_mode == 0) {

        //     $purchasedata = Stockdetail::leftJoin('products', 'stockdetails.product', '=', 'products.id')
        //         ->select(
        //             DB::raw("TIMESTAMP(stockdetails.created_at), stockdetails.reciept_no, products.product_name, (CASE
        //                                                                                                         WHEN stockdetails.payment_mode = '1' THEN 'Cash'
        //                                                                                                         WHEN stockdetails.payment_mode = '2' THEN 'Credit'
        //                                                                                                         END) , stockdetails.price")
        //         )
        //         // ->where('stockdetails.supplier', $this->supplier)
        //         ->where('stockdetails.supplier_id', $this->supplier)
        //         ->orderBy('stockdetails.id', 'ASC')
        //         ->get();
        // } else {

        //     $purchasedata = Stockdetail::leftJoin('products', 'stockdetails.product', '=', 'products.id')
        //         ->select(DB::raw("TIMESTAMP(stockdetails.created_at), stockdetails.reciept_no, products.product_name, (CASE
        //                                                                                                         WHEN stockdetails.payment_mode = '1' THEN 'Cash'
        //                                                                                                         WHEN stockdetails.payment_mode = '2' THEN 'Credit'
        //                                                                                                         END) , stockdetails.price"))
        //         // ->where('stockdetails.supplier', $this->supplier)
        //         ->where('stockdetails.supplier_id', $this->supplier)
        //         ->where('stockdetails.payment_mode', $this->payment_mode)
        //         ->orderBy('stockdetails.id', 'ASC')
        //         ->get();
        // }

        // return $purchasedata;


        // $query = Stockdetail::leftJoin('products', 'stockdetails.product', '=', 'products.id')
        //     ->select(
        //         DB::raw("TIMESTAMP(stockdetails.created_at)"),
        //         'stockdetails.reciept_no',
        //         'products.product_name',
        //         DB::raw("(CASE WHEN stockdetails.payment_mode = '1' THEN 'Cash' WHEN stockdetails.payment_mode = '2' THEN 'Credit' END)"),
        //         'stockdetails.price'
        //     )
        //     ->where('stockdetails.supplier_id', $this->supplier)
        //     ->groupBy('stockdetails.reciept_no')
        //     ->orderBy('stockdetails.created_at', 'DESC');

        $query = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->select(
                DB::raw("TIMESTAMP(stockdetails.created_at)"),
                'stockdetails.reciept_no',
                'products.product_name',
                DB::raw("(CASE WHEN stockdetails.payment_mode = '1' THEN 'Cash' WHEN stockdetails.payment_mode = '2' THEN 'Credit' END)"),
                DB::raw("SUM(stockdetails.price) as price")
            )
            ->where('stockdetails.supplier_id', $this->supplier)
            ->groupBy('stockdetails.reciept_no');


        if ($this->payment_mode != 0) {
            $query->where('stockdetails.payment_mode', $this->payment_mode);
        }

        return $query->orderBy('stockdetails.created_at', 'DESC')->get();
    }
    public function headings(): array
    {
        // if ($this->payment_mode == 0) {
        //     $tot = Stockdetail::select(DB::raw("SUM(stockdetails.price) as totalprice"))
        //         // ->where('stockdetails.supplier', $this->supplier)
        //         ->where('stockdetails.supplier_id', $this->supplier)
        //         ->first();
        // } else {
        //     $tot = Stockdetail::select(DB::raw("SUM(stockdetails.price) as totalprice"))
        //         // ->where('stockdetails.supplier', $this->supplier)
        //         ->where('stockdetails.supplier_id', $this->supplier)
        //         ->where('stockdetails.payment_mode', $this->payment_mode)
        //         ->first();
        // }

        $totalPriceQuery = Stockdetail::select(DB::raw("SUM(stockdetails.price) as totalprice"))
            ->where('stockdetails.supplier_id', $this->supplier);

        if ($this->payment_mode != 0) {
            $totalPriceQuery->where('stockdetails.payment_mode', $this->payment_mode);
        }

        $totalPrice = $totalPriceQuery->first();


        // return [
        //     [
        //         'Date',
        //         'Bill No.',
        //         'Product',
        //         'Payment Mode',
        //         'Price',
        //     ],
        //     [
        //         'Total Price',
        //         '',
        //         '',
        //         '',
        //         $tot->totalprice,
        //     ]
        // ];

        return [
            ['Date', 'Bill No.', 'Product', 'Payment Mode', 'Price'],
            ['', '', '', '', $totalPrice->totalprice],
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:E1'; // All headers
                $cellRange2 = 'A2:E2';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true)->setSize(14);
                $event->sheet->getDelegate()->getStyle($cellRange2)->getFont()->setBold(true)->setSize(13);
            },
        ];
    }
}
