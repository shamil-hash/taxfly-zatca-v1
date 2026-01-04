<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class exportPurchaseReturnReport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
                ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
                ->select(DB::raw("returnpurchases.reciept_no, products.product_name, returnpurchases.quantity, returnpurchases.amount_without_vat as amount_without_vat, (returnpurchases.amount - returnpurchases.amount_without_vat) as vat_amount, returnpurchases.amount as amount,returnpurchases.discount,(returnpurchases.amount - returnpurchases.discount) AS total,returnpurchases.created_at as created_at, returnpurchases.comment, returnpurchases.shop_name"),)
                ->whereDate('returnpurchases.created_at', $this->date)
                ->where('accountantlocs.user_id', $this->userid)
                ->where('returnpurchases.branch', $this->location)
                ->orderBy('returnpurchases.created_at', 'ASC')
                ->distinct('returnpurchases.id')
                ->get();

            return $data;
        }
        if ($this->viewtype == 2) {
            $data = DB::table('accountantlocs')
                ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
                ->select(DB::raw("returnpurchases.reciept_no, products.product_name, returnpurchases.quantity, returnpurchases.amount_without_vat as amount_without_vat, (returnpurchases.amount - returnpurchases.amount_without_vat) as vat_amount, returnpurchases.amount as amount,returnpurchases.discount,(returnpurchases.amount - returnpurchases.discount) AS total,returnpurchases.created_at as created_at, returnpurchases.comment, returnpurchases.shop_name"),)
                ->whereMonth('returnpurchases.created_at', $this->date)
                ->where('accountantlocs.user_id', $this->userid)
                ->where('returnpurchases.branch', $this->location)
                ->orderBy('returnpurchases.created_at', 'ASC')
                ->distinct('returnpurchases.id')
                ->get();

            return $data;
        }
        if ($this->viewtype == 3) {
            $data = DB::table('accountantlocs')
                ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
                ->select(DB::raw("returnpurchases.reciept_no,products.product_name, returnpurchases.quantity, returnpurchases.amount_without_vat as amount_without_vat, (returnpurchases.amount - returnpurchases.amount_without_vat) as vat_amount, returnpurchases.amount as amount,returnpurchases.discount,(returnpurchases.amount - returnpurchases.discount) AS total, returnpurchases.created_at as created_at, returnpurchases.comment, returnpurchases.shop_name"),)
                ->whereYear('returnpurchases.created_at', $this->date)
                ->where('accountantlocs.user_id', $this->userid)
                ->where('returnpurchases.branch', $this->location)
                ->orderBy('returnpurchases.created_at', 'ASC')
                ->distinct('returnpurchases.id')
                ->get();

            return $data;
        }
    }
    public function headings(): array
    {
        return [
            'Reciept No',
            'Product Name',
            'Quantity',
            'Total Price',
            'Total VAT',
            'Grand Total(including VAT)',
            'Discount',
            'Grand Total with Discount',
            'Created Date',
            'Comment',
            'Supplier Name',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:K1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true)->setSize(14);
            },
        ];
    }
}
