<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OverallPurchaseExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $userid;
    protected $viewtype;
    protected $location;
    protected $fromdate;
    protected $todate;


    function __construct($userid, $viewtype, $location, $fromdate, $todate)
    {
        $this->userid = $userid;
        $this->viewtype = $viewtype;
        $this->location = $location;
        $this->fromdate = $fromdate;
        $this->todate = $todate;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->viewtype == 1) {

            if ($this->fromdate == "0" || $this->todate == "0") {
                $data = DB::table('accountantlocs')
                    ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                    ->select(DB::raw("DATE(stockdetails.created_at) as date, COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, (SUM(stockdetails.price) - SUM(stockdetails.price_without_vat)) as vat_amount,SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,(SUM(stockdetails.price) - SUM(stockdetails.discount)) AS total"))
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('stockdetails.branch', $this->location)
                    ->whereDate('stockdetails.created_at', Carbon::today())
                    ->get();

                return $data;
            } elseif ($this->fromdate == $this->todate && $this->fromdate != "0" && $this->todate != "0") {
                $date = Carbon::createFromFormat('Y-m-d', $this->fromdate);
                $data = DB::table('accountantlocs')
                    ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                    ->select(DB::raw("DATE(stockdetails.created_at) as date, COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, (SUM(stockdetails.price) - SUM(stockdetails.price_without_vat)) as vat_amount,SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,(SUM(stockdetails.price) - SUM(stockdetails.discount)) AS total"))
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('stockdetails.branch', $this->location)
                    ->whereDate('stockdetails.created_at', $date)
                    ->get();

                return $data;
            } elseif ($this->fromdate != $this->todate) {
                $data = DB::table('accountantlocs')
                    ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                    ->select(DB::raw("DATE(stockdetails.created_at) as date, COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, (SUM(stockdetails.price) - SUM(stockdetails.price_without_vat)) as vat_amount,SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,(SUM(stockdetails.price) - SUM(stockdetails.discount)) AS total"),)
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('stockdetails.branch', $this->location)
                    ->whereBetween('stockdetails.created_at', [$this->fromdate . ' 00:00:00', $this->todate . ' 23:59:59'])
                    ->get();

                return $data;
            }
        }
        if ($this->viewtype == 2) {

            if ($this->fromdate == "0" || $this->todate == "0") {
                $data = DB::table('accountantlocs')
                    ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                    ->select(DB::raw("MONTHNAME(stockdetails.created_at) as date, COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, (SUM(stockdetails.price) - SUM(stockdetails.price_without_vat)) as vat_amount,SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,(SUM(stockdetails.price) - SUM(stockdetails.discount)) AS total"),)
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('stockdetails.branch', $this->location)
                    ->whereMonth('stockdetails.created_at', Carbon::today())
                    ->get();

                return $data;
            } elseif ($this->fromdate == $this->todate && $this->fromdate != "0" && $this->todate != "0") {
                $date = Carbon::createFromFormat('Y-m-d', $this->fromdate);
                $data = DB::table('accountantlocs')
                    ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                    ->select(DB::raw("MONTHNAME(stockdetails.created_at) as date, COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, (SUM(stockdetails.price) - SUM(stockdetails.price_without_vat)) as vat_amount,SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,(SUM(stockdetails.price) - SUM(stockdetails.discount)) AS total"),)
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('stockdetails.branch', $this->location)
                    ->whereMonth('stockdetails.created_at', $date)
                    ->get();

                return $data;
            } elseif ($this->fromdate != $this->todate) {
                $data = DB::table('accountantlocs')
                    ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                    ->select(DB::raw("MONTHNAME(stockdetails.created_at) as date, COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, (SUM(stockdetails.price) - SUM(stockdetails.price_without_vat)) as vat_amount,SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,(SUM(stockdetails.price) - SUM(stockdetails.discount)) AS total"),)
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('stockdetails.branch', $this->location)
                    ->whereBetween('stockdetails.created_at', [$this->fromdate . ' 00:00:00', $this->todate . ' 23:59:59'])
                    ->get();

                return $data;
            }
        }
        if ($this->viewtype == 3) {

            if ($this->fromdate == "0" || $this->todate == "0") {
                $data = DB::table('accountantlocs')
                    ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                    ->select(DB::raw("YEAR(stockdetails.created_at) as date, COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, (SUM(stockdetails.price) - SUM(stockdetails.price_without_vat)) as vat_amount,SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,(SUM(stockdetails.price) - SUM(stockdetails.discount)) AS total"),)
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('stockdetails.branch', $this->location)
                    ->whereYear('stockdetails.created_at', Carbon::today())
                    ->get();

                return $data;
            } elseif ($this->fromdate == $this->todate && $this->fromdate != "0" && $this->todate != "0") {
                $data = DB::table('accountantlocs')
                    ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                    ->select(DB::raw("YEAR(stockdetails.created_at) as date, COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, (SUM(stockdetails.price) - SUM(stockdetails.price_without_vat)) as vat_amount,SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,(SUM(stockdetails.price) - SUM(stockdetails.discount)) AS total"),)
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('stockdetails.branch', $this->location)
                    ->whereYear('stockdetails.created_at', $this->fromdate)
                    ->get();

                return $data;
            } elseif ($this->fromdate != $this->todate) {
                $data = DB::table('accountantlocs')
                    ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                    ->select(DB::raw("YEAR(stockdetails.created_at) as date, COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, (SUM(stockdetails.price) - SUM(stockdetails.price_without_vat)) as vat_amount,SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,(SUM(stockdetails.price) - SUM(stockdetails.discount)) AS total "),)
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('stockdetails.branch', $this->location)
                    ->whereBetween('stockdetails.created_at', [$this->fromdate . ' 00:00:00', $this->todate . ' 23:59:59'])
                    ->get();

                return $data;
            }
        }
    }
    public function headings(): array
    {
        if ($this->viewtype == 1) {
            $dateheader = 'Date';
        } elseif ($this->viewtype == 2) {
            $dateheader = 'Month';
        } elseif ($this->viewtype == 3) {
            $dateheader = 'Year';
        }
        return [
            $dateheader,
            'Total Purchases',
            'Total Price',
            'Total VAT',
            'Grand Total with VAT',
            'Total Discount',
            'Grand Total with Discount'

        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:G1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true)->setSize(14);
            },
        ];
    }
}
