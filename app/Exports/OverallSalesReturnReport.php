<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OverallSalesReturnReport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
                    ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                    ->select(DB::raw("
                        DATE(returnproducts.created_at) as date,
                        COUNT(distinct(returnproducts.id)) as num,
                        CASE
                            WHEN SUM(returnproducts.totalamount_wo_discount) != 0 THEN SUM(returnproducts.totalamount_wo_discount)
                            ELSE SUM(returnproducts.total_amount * returnproducts.quantity)
                        END AS grandtotal_withdiscount,

                        CASE
                            WHEN SUM(returnproducts.discount_amount) != 0 THEN
                                SUM(returnproducts.discount_amount) + SUM(returnproducts.total_amount * (total_discount_percent / 100))
                            ELSE
                                NULL
                        END AS discount_amount,


                        SUM( COALESCE(returnproducts.grand_total, 0)) as sum,
                         SUM(returnproducts.vat_amount) as vat
                    "))
                    ->groupBy('date')
                    ->where('returnproducts.branch', $this->location)
                    ->whereDate('returnproducts.created_at', Carbon::today())
                    ->where('accountantlocs.user_id', $this->userid)
                    ->get();

                return $data;
            } else if ($this->fromdate == $this->todate && $this->fromdate != "0" && $this->todate != "0") {
                $data = DB::table('accountantlocs')
                    ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                    ->select(DB::raw("
                        DATE(returnproducts.created_at) as date,
                        COUNT(distinct(returnproducts.id)) as num,
                        CASE
                            WHEN SUM(returnproducts.totalamount_wo_discount) != 0 THEN SUM(returnproducts.totalamount_wo_discount)
                            ELSE SUM(returnproducts.total_amount * returnproducts.quantity)
                        END AS grandtotal_withdiscount,

                        CASE
                            WHEN SUM(returnproducts.discount_amount) != 0 THEN
                                SUM(returnproducts.discount_amount) + SUM(returnproducts.total_amount * (total_discount_percent / 100))
                            ELSE
                                NULL
                        END AS discount_amount,


                        SUM( COALESCE(returnproducts.grand_total, 0)) as sum,
                         SUM(returnproducts.vat_amount) as vat
                    "))
                    ->groupBy('date')
                    ->where('returnproducts.branch', $this->location)
                    ->whereDate('returnproducts.created_at', $this->fromdate)
                    ->where('accountantlocs.user_id', $this->userid)
                    ->get();

                return $data;
            } else if ($this->fromdate != $this->todate) {
                $data = DB::table('accountantlocs')
                    ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                    ->select(DB::raw("
                    DATE(returnproducts.created_at) as date,
                    COUNT(distinct(returnproducts.id)) as num,
                    CASE
                        WHEN SUM(returnproducts.totalamount_wo_discount) != 0 THEN SUM(returnproducts.totalamount_wo_discount)
                        ELSE SUM(returnproducts.total_amount * returnproducts.quantity)
                    END AS grandtotal_withdiscount,

                    CASE
                        WHEN SUM(returnproducts.discount_amount) != 0 THEN
                            SUM(returnproducts.discount_amount) + SUM(returnproducts.total_amount * (total_discount_percent / 100))
                        ELSE
                            NULL
                    END AS discount_amount,


                    SUM( COALESCE(returnproducts.grand_total, 0)) as sum,
                     SUM(returnproducts.vat_amount) as vat
                "))
                    ->groupBy('date')
                    ->where('returnproducts.branch', $this->location)
                    ->whereBetween('returnproducts.created_at', [$this->fromdate . ' 00:00:00', $this->todate . ' 23:59:59'])
                    ->where('accountantlocs.user_id', $this->userid)
                    ->get();

                return $data;
            }
        }
        if ($this->viewtype == 2) {

            if ($this->fromdate == "0" || $this->todate == "0") {
                $data = DB::table('accountantlocs')
                    ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                    ->select(DB::raw("
                        MONTHNAME(returnproducts.created_at) as date,
                        COUNT(distinct(returnproducts.id)) as num,
                    CASE
                        WHEN SUM(returnproducts.totalamount_wo_discount) != 0 THEN SUM(returnproducts.totalamount_wo_discount)
                        ELSE SUM(returnproducts.total_amount * returnproducts.quantity)
                    END AS grandtotal_withdiscount,

                    CASE
                        WHEN SUM(returnproducts.discount_amount) != 0 THEN
                            SUM(returnproducts.discount_amount) + SUM(returnproducts.total_amount * (total_discount_percent / 100))
                        ELSE
                            NULL
                    END AS discount_amount,


                    SUM( COALESCE(returnproducts.grand_total, 0)) as sum,
                     SUM(returnproducts.vat_amount) as vat
                    "))
                    ->groupBy('date')
                    ->where('returnproducts.branch', $this->location)
                    ->whereMonth('returnproducts.created_at', Carbon::today())
                    ->where('accountantlocs.user_id', $this->userid)
                    ->get();

                return $data;
            } elseif ($this->fromdate == $this->todate && $this->fromdate != "0" && $this->todate != "0") {
                $date = Carbon::createFromFormat('Y-m-d', $this->fromdate);
                $data = DB::table('accountantlocs')
                    ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                    ->select(DB::raw("
                    MONTHNAME(returnproducts.created_at) as date,
                    COUNT(distinct(returnproducts.id)) as num,
                CASE
                    WHEN SUM(returnproducts.totalamount_wo_discount) != 0 THEN SUM(returnproducts.totalamount_wo_discount)
                    ELSE SUM(returnproducts.total_amount * returnproducts.quantity)
                END AS grandtotal_withdiscount,

                CASE
                    WHEN SUM(returnproducts.discount_amount) != 0 THEN
                        SUM(returnproducts.discount_amount) + SUM(returnproducts.total_amount * (total_discount_percent / 100))
                    ELSE
                        NULL
                END AS discount_amount,


                SUM( COALESCE(returnproducts.grand_total, 0)) as sum,
                 SUM(returnproducts.vat_amount) as vat
                "))
                    ->groupBy('date')
                    ->where('returnproducts.branch', $this->location)
                    ->whereMonth('returnproducts.created_at', $date)
                    ->where('accountantlocs.user_id', $this->userid)
                    ->get();

                return $data;
            } elseif ($this->fromdate != $this->todate) {
                $data = DB::table('accountantlocs')
                    ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                    ->select(DB::raw("
                    MONTHNAME(returnproducts.created_at) as date,
                    COUNT(distinct(returnproducts.id)) as num,
                CASE
                    WHEN SUM(returnproducts.totalamount_wo_discount) != 0 THEN SUM(returnproducts.totalamount_wo_discount)
                    ELSE SUM(returnproducts.total_amount * returnproducts.quantity)
                END AS grandtotal_withdiscount,

                CASE
                    WHEN SUM(returnproducts.discount_amount) != 0 THEN
                        SUM(returnproducts.discount_amount) + SUM(returnproducts.total_amount * (total_discount_percent / 100))
                    ELSE
                        NULL
                END AS discount_amount,


                SUM( COALESCE(returnproducts.grand_total, 0)) as sum,
                 SUM(returnproducts.vat_amount) as vat
                "))
                    ->groupBy('date')
                    ->where('returnproducts.branch', $this->location)
                    ->whereBetween('returnproducts.created_at', [$this->fromdate . ' 00:00:00', $this->todate . ' 23:59:59'])
                    ->where('accountantlocs.user_id', $this->userid)
                    ->get();

                return $data;
            }
        }
        if ($this->viewtype == 3) {

            if ($this->fromdate == "0" || $this->todate == "0") {
                $data = DB::table('accountantlocs')
                    ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                    ->select(DB::raw("
                        YEAR(returnproducts.created_at) as date,
                        COUNT(distinct(returnproducts.id)) as num,
                CASE
                    WHEN SUM(returnproducts.totalamount_wo_discount) != 0 THEN SUM(returnproducts.totalamount_wo_discount)
                    ELSE SUM(returnproducts.total_amount * returnproducts.quantity)
                END AS grandtotal_withdiscount,

                CASE
                    WHEN SUM(returnproducts.discount_amount) != 0 THEN
                        SUM(returnproducts.discount_amount) + SUM(returnproducts.total_amount * (total_discount_percent / 100))
                    ELSE
                        NULL
                END AS discount_amount,


                SUM( COALESCE(returnproducts.grand_total, 0)) as sum,
                 SUM(returnproducts.vat_amount) as vat
                    "))
                    ->groupBy('date')
                    ->where('returnproducts.branch', $this->location)
                    ->whereYear('returnproducts.created_at', Carbon::today())
                    ->where('accountantlocs.user_id', $this->userid)
                    ->get();

                return $data;
            } elseif ($this->fromdate == $this->todate && $this->fromdate != "0" && $this->todate != "0") {
                $data = DB::table('accountantlocs')
                    ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                    ->select(DB::raw("
                        YEAR(returnproducts.created_at) as date,
                        COUNT(distinct(returnproducts.id)) as num,
                CASE
                    WHEN SUM(returnproducts.totalamount_wo_discount) != 0 THEN SUM(returnproducts.totalamount_wo_discount)
                    ELSE SUM(returnproducts.total_amount * returnproducts.quantity)
                END AS grandtotal_withdiscount,

                CASE
                    WHEN SUM(returnproducts.discount_amount) != 0 THEN
                        SUM(returnproducts.discount_amount) + SUM(returnproducts.total_amount * (total_discount_percent / 100))
                    ELSE
                        NULL
                END AS discount_amount,


                SUM( COALESCE(returnproducts.grand_total, 0)) as sum,
                 SUM(returnproducts.vat_amount) as vat
                    "))
                    ->groupBy('date')
                    ->where('returnproducts.branch', $this->location)
                    ->whereYear('returnproducts.created_at', Carbon::today())
                    ->where('accountantlocs.user_id', $this->userid)
                    ->get();

                return $data;
            } elseif ($this->fromdate != $this->todate) {
                $data = DB::table('accountantlocs')
                    ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                    ->select(DB::raw("
                        YEAR(returnproducts.created_at) as date,
                        COUNT(distinct(returnproducts.id)) as num,
                CASE
                    WHEN SUM(returnproducts.totalamount_wo_discount) != 0 THEN SUM(returnproducts.totalamount_wo_discount)
                    ELSE SUM(returnproducts.total_amount * returnproducts.quantity)
                END AS grandtotal_withdiscount,

                CASE
                    WHEN SUM(returnproducts.discount_amount) != 0 THEN
                        SUM(returnproducts.discount_amount) + SUM(returnproducts.total_amount * (total_discount_percent / 100))
                    ELSE
                        NULL
                END AS discount_amount,


                SUM( COALESCE(returnproducts.grand_total, 0)) as sum,
                 SUM(returnproducts.vat_amount) as vat
                    "))
                    ->groupBy('date')
                    ->where('returnproducts.branch', $this->location)
                    ->whereBetween('returnproducts.created_at', [$this->fromdate . ' 00:00:00', $this->todate . ' 23:59:59'])
                    ->where('accountantlocs.user_id', $this->userid)
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
            'Total Sales',
            'Total Amount',
            'Discount Amount',
            'Grand Total (w/. discount)',
            'Total VAT',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:F1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true)->setSize(14);
            },
        ];
    }
}
