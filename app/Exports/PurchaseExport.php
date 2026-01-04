<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PurchaseExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
                ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                ->select(DB::raw("stockdetails.reciept_no, SUM(stockdetails.price_without_vat) as price_without_vat, (SUM(stockdetails.price) - SUM(stockdetails.price_without_vat)) as vat_amount,SUM(stockdetails.price) as price, SUM(stockdetails.discount) as discount,(SUM(stockdetails.price) - SUM(stockdetails.discount)) AS total, DATE(stockdetails.created_at) as created_at, stockdetails.comment,stockdetails.supplier"),)
                ->whereDate('stockdetails.created_at', $this->date)
                ->where('accountantlocs.user_id', $this->userid)
                ->where('stockdetails.branch',  $this->location)
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'ASC')
                ->distinct('stockdetails.id')
                ->get();

            return $data;
        }
        if ($this->viewtype == 2) {

            $data = DB::table('accountantlocs')
                ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                ->select(DB::raw("stockdetails.reciept_no, SUM(stockdetails.price_without_vat) as price_without_vat, (SUM(stockdetails.price) - SUM(stockdetails.price_without_vat)) as vat_amount,SUM(stockdetails.price) as price, SUM(stockdetails.discount) as discount,(SUM(stockdetails.price) - SUM(stockdetails.discount)) AS total, DATE(stockdetails.created_at) as created_at, stockdetails.comment, stockdetails.supplier"),)
                ->whereMonth('stockdetails.created_at', $this->date)
                ->where('accountantlocs.user_id', $this->userid)
                ->where('stockdetails.branch',  $this->location)
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'ASC')
                ->distinct('stockdetails.id')
                ->get();

            return $data;
        }
        if ($this->viewtype == 3) {
            $data = DB::table('accountantlocs')
                ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                ->select(DB::raw("stockdetails.reciept_no, SUM(stockdetails.price_without_vat) as price_without_vat, (SUM(stockdetails.price) - SUM(stockdetails.price_without_vat)) as vat_amount,SUM(stockdetails.price) as price, SUM(stockdetails.discount) as discount,(SUM(stockdetails.price) - SUM(stockdetails.discount)) AS total, DATE(stockdetails.created_at) as created_at, stockdetails.comment, stockdetails.supplier"),)
                ->whereYear('stockdetails.created_at', $this->date)
                ->where('accountantlocs.user_id', $this->userid)
                ->where('stockdetails.branch',  $this->location)
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'ASC')
                ->distinct('stockdetails.id')
                ->get();

            return $data;
        }
    }
    public function headings(): array
    {
        return [
            'Reciept No',
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
                $cellRange = 'A1:I1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true)->setSize(14);
            },
        ];
    }
}
