<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OverallPurchaseReturnExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
                    ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                    ->select(DB::raw("DATE(returnpurchases.created_at) as date,COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, (SUM(returnpurchases.amount) - SUM(returnpurchases.amount_without_vat)) as vat_amount, SUM(returnpurchases.amount) as price,SUM(returnpurchases.discount) as discount,(SUM(returnpurchases.amount) - SUM(returnpurchases.discount)) AS total"))
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('returnpurchases.branch', $this->location)
                    ->whereDate('returnpurchases.created_at', Carbon::today())
                    ->get();

                return $data;
            } elseif ($this->fromdate == $this->todate && $this->fromdate != "0" && $this->todate != "0") {
                $data = DB::table('accountantlocs')
                    ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                    ->select(DB::raw("DATE(returnpurchases.created_at) as date, COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, (SUM(returnpurchases.amount) - SUM(returnpurchases.amount_without_vat)) as vat_amount, SUM(returnpurchases.amount) as price,SUM(returnpurchases.discount) as discount,(SUM(returnpurchases.amount) - SUM(returnpurchases.discount)) AS total"))
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('returnpurchases.branch', $this->location)
                    ->whereDate('returnpurchases.created_at', $this->fromdate)
                    ->get();

                return $data;
            }
            if ($this->fromdate != $this->todate) {
                $data = DB::table('accountantlocs')
                    ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                    ->select(DB::raw("DATE(returnpurchases.created_at) as date, COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, (SUM(returnpurchases.amount) - SUM(returnpurchases.amount_without_vat)) as vat_amount, SUM(returnpurchases.amount) as price,SUM(returnpurchases.discount) as discount,(SUM(returnpurchases.amount) - SUM(returnpurchases.discount)) AS total"))
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('returnpurchases.branch', $this->location)
                    ->whereBetween('returnpurchases.created_at', [$this->fromdate . ' 00:00:00', $this->todate . ' 23:59:59'])
                    ->get();

                return $data;
            }
        }
        if ($this->viewtype == 2) {

            if ($this->fromdate == "0" || $this->todate == "0") {
                $data = DB::table('accountantlocs')
                    ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                    ->select(DB::raw("MONTHNAME(returnpurchases.created_at) as date, COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, (SUM(returnpurchases.amount) - SUM(returnpurchases.amount_without_vat)) as vat_amount, SUM(returnpurchases.amount) as price,SUM(returnpurchases.discount) as discount,(SUM(returnpurchases.amount) - SUM(returnpurchases.discount)) AS total"))
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('returnpurchases.branch', $this->location)
                    ->whereMonth('returnpurchases.created_at', Carbon::today())
                    ->get();

                return $data;
            } elseif ($this->fromdate == $this->todate && $this->fromdate != "0" && $this->todate != "0") {
                $date = Carbon::createFromFormat('Y-m-d', $this->fromdate);
                $data = DB::table('accountantlocs')
                    ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                    ->select(DB::raw("MONTHNAME(returnpurchases.created_at) as date, COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, (SUM(returnpurchases.amount) - SUM(returnpurchases.amount_without_vat)) as vat_amount, SUM(returnpurchases.amount) as price,SUM(returnpurchases.discount) as discount,(SUM(returnpurchases.amount) - SUM(returnpurchases.discount)) AS total"))
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('returnpurchases.branch', $$this->location)
                    ->whereMonth('returnpurchases.created_at', $date)
                    ->get();

                return $data;
            } elseif ($this->fromdate != $this->todate) {
                $data = DB::table('accountantlocs')
                    ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                    ->select(DB::raw("MONTHNAME(returnpurchases.created_at) as date, COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, (SUM(returnpurchases.amount) - SUM(returnpurchases.amount_without_vat)) as vat_amount, SUM(returnpurchases.amount) as price,SUM(returnpurchases.discount) as discount,(SUM(returnpurchases.amount) - SUM(returnpurchases.discount)) AS total"))
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('returnpurchases.branch', $this->location)
                    ->whereBetween('returnpurchases.created_at', [$this->fromdate . ' 00:00:00', $this->todate . ' 23:59:59'])
                    ->get();

                return $data;
            }
        }
        if ($this->viewtype == 3) {

            if ($this->fromdate == "0" || $this->todate == "0") {
                $data = DB::table('accountantlocs')
                    ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                    ->select(DB::raw("YEAR(returnpurchases.created_at) as date, COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, (SUM(returnpurchases.amount) - SUM(returnpurchases.amount_without_vat)) as vat_amount, SUM(returnpurchases.amount) as price,SUM(returnpurchases.discount) as discount,(SUM(returnpurchases.amount) - SUM(returnpurchases.discount)) AS total"))
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('returnpurchases.branch', $this->location)
                    ->whereYear('returnpurchases.created_at', Carbon::today())
                    ->get();

                return $data;
            } elseif ($this->fromdate == $this->todate && $this->fromdate != "0" && $this->todate != "0") {
                $data = DB::table('accountantlocs')
                    ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                    ->select(DB::raw("YEAR(returnpurchases.created_at) as date, COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, (SUM(returnpurchases.amount) - SUM(returnpurchases.amount_without_vat)) as vat_amount, SUM(returnpurchases.amount) as price,SUM(returnpurchases.discount) as discount,(SUM(returnpurchases.amount) - SUM(returnpurchases.discount)) AS total"))
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('returnpurchases.branch', $this->location)
                    ->whereYear('returnpurchases.created_at', $this->fromdate)
                    ->get();

                return $data;
            } elseif ($this->fromdate != $this->todate) {
                $data = DB::table('accountantlocs')
                    ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                    ->select(DB::raw("YEAR(returnpurchases.created_at) as date, COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, (SUM(returnpurchases.amount) - SUM(returnpurchases.amount_without_vat)) as vat_amount, SUM(returnpurchases.amount) as price,SUM(returnpurchases.discount) as discount,(SUM(returnpurchases.amount) - SUM(returnpurchases.discount)) AS total"))
                    ->groupBy('date')
                    ->where('accountantlocs.user_id', $this->userid)
                    ->where('returnpurchases.branch', $this->location)
                    ->whereBetween('returnpurchases.created_at', [$this->fromdate . ' 00:00:00', $this->todate . ' 23:59:59'])
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
            'Total Amount',
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
