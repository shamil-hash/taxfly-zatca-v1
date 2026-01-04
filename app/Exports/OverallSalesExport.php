<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OverallSalesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $userid;
    protected $viewtype;
    protected $location;
    protected $fromdate;
    protected $todate;
    protected $users;



    function __construct($userid, $viewtype, $location, $fromdate, $todate, $users)
    {
        $this->userid = $userid;
        $this->viewtype = $viewtype;
        $this->location = $location;
        $this->fromdate = $fromdate;
        $this->todate = $todate;
        $this->users = $users; // Store users data

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->viewtype == 1) {

            if ($this->fromdate == "0" || $this->todate == "0") {

                $data = DB::table('accountantlocs')
                    ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                    ->select(DB::raw("
                        DATE(buyproducts.created_at) as date,

                        COUNT(distinct(buyproducts.transaction_id)) as num,

                        CASE
                            WHEN SUM(buyproducts.totalamount_wo_discount) != 0 THEN SUM(buyproducts.totalamount_wo_discount)
                            ELSE SUM(buyproducts.total_amount * buyproducts.quantity)
                        END AS grandtotal_withdiscount,

                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,

                        SUM(buyproducts.vat_amount) as vat,

                                      CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                )
            ELSE
                COALESCE(SUM(DISTINCT buyproducts.bill_grand_total), 0)
        END AS sum,
                        (
                        SELECT SUM(credit_note.credit_note_amount)
                            FROM credit_note
                            WHERE credit_note.branch = buyproducts.branch
                            AND DATE(credit_note.created_at) = DATE(NOW())
                        ) AS total_credit_note,

                         CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                COALESCE(SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) +
                SUM(DISTINCT COALESCE(total_discount_amount, 0)), 0)
            ELSE
                COALESCE(SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0)), 0)
        END  -
                        (
                            SELECT SUM(credit_note.credit_note_amount)
                            FROM credit_note
                            WHERE credit_note.branch = buyproducts.branch
                            AND DATE(credit_note.created_at) = DATE(NOW())

                    ) AS total
                    "))
                    ->groupBy('date')
                    ->where('buyproducts.branch', $this->location)
                    ->whereDate('buyproducts.created_at', Carbon::today())
                    ->where('accountantlocs.user_id', $this->userid)
                    ->get();

                return $data;
            } elseif ($this->fromdate == $this->todate && $this->fromdate != "0" && $this->todate != "0") {
                $data = DB::table('accountantlocs')
                    ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                    ->select(DB::raw("
                        DATE(buyproducts.created_at) as date,

                        COUNT(distinct(buyproducts.transaction_id)) as num,

                       CASE
                            WHEN SUM(buyproducts.totalamount_wo_discount) != 0 THEN SUM(buyproducts.totalamount_wo_discount)
                            ELSE SUM(buyproducts.total_amount * buyproducts.quantity)
                        END AS grandtotal_withdiscount,

                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,


                        SUM(buyproducts.vat_amount) as vat,

                                     CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                )
            ELSE
                COALESCE(SUM(DISTINCT buyproducts.bill_grand_total), 0)
        END AS sum,


                        (
                        SELECT SUM(credit_note.credit_note_amount)
                        FROM credit_note
                        WHERE credit_note.branch = buyproducts.branch
                        AND DATE(credit_note.created_at) = ?
                        ) AS total_credit_note,

                          CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                COALESCE(SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) +
                SUM(DISTINCT COALESCE(total_discount_amount, 0)), 0)
            ELSE
                COALESCE(SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0)), 0)
        END -
                        (
                        SELECT SUM(credit_note.credit_note_amount)
                        FROM credit_note
                        WHERE credit_note.branch = buyproducts.branch
                        AND DATE(credit_note.created_at) = ?

                    ) AS total
                    "))
                    ->groupBy('date')
                    ->where('buyproducts.branch', $this->location)
                    ->whereDate('buyproducts.created_at', $this->fromdate)
                    ->where('accountantlocs.user_id', $this->userid)
                    ->addBinding([$this->fromdate, $this->fromdate], 'select') // Add the binding for both date placeholders
                    ->get();

                return $data;
            } elseif ($this->fromdate != $this->todate) {
                $data = DB::table('accountantlocs')
                    ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                    ->select(DB::raw("
                        DATE(buyproducts.created_at) as date,

                        COUNT(distinct(buyproducts.transaction_id)) as num,

                        CASE
                            WHEN SUM(buyproducts.totalamount_wo_discount) != 0 THEN SUM(buyproducts.totalamount_wo_discount)
                            ELSE SUM(buyproducts.total_amount * buyproducts.quantity)
                        END AS grandtotal_withdiscount,


                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,


                        SUM(buyproducts.vat_amount) as vat,

                                       CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                )
            ELSE
                COALESCE(SUM(DISTINCT buyproducts.bill_grand_total), 0)
        END AS sum,

                        (
                        SELECT SUM(credit_note.credit_note_amount)
                        FROM credit_note
                        WHERE credit_note.branch = buyproducts.branch
                        AND DATE(credit_note.created_at) = DATE(buyproducts.created_at)
                    ) AS total_credit_note,

                        CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                COALESCE(SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) +
                SUM(DISTINCT COALESCE(total_discount_amount, 0)), 0)
            ELSE
                COALESCE(SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0)), 0)
        END -
                        (
                        SELECT SUM(credit_note.credit_note_amount)
                        FROM credit_note
                        WHERE credit_note.branch = buyproducts.branch
                        AND DATE(credit_note.created_at) = DATE(buyproducts.created_at)
                    ) AS total
                    "))
                    ->groupBy('date')
                    ->where('buyproducts.branch', $this->location)
                    ->whereBetween('buyproducts.created_at', [$this->fromdate . ' 00:00:00', $this->todate . ' 23:59:59'])
                    ->where('accountantlocs.user_id', $this->userid)
                    ->get();

                return $data;
            }
        }
        if ($this->viewtype == 2) {

            if ($this->fromdate == "0" || $this->todate == "0") {
                $data = DB::table('accountantlocs')
                    ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                    ->select(DB::raw("
                        MONTHNAME(buyproducts.created_at) as date,

                        COUNT(distinct(buyproducts.transaction_id)) as num,

                        CASE
                            WHEN SUM(buyproducts.totalamount_wo_discount) != 0 THEN SUM(buyproducts.totalamount_wo_discount)
                            ELSE SUM(buyproducts.total_amount * buyproducts.quantity)
                        END AS grandtotal_withdiscount,

                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,


                        SUM(buyproducts.vat_amount) as vat,

                                       CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                )
            ELSE
                COALESCE(SUM(DISTINCT buyproducts.bill_grand_total), 0)
        END AS sum,

                    (
                    SELECT SUM(credit_note.credit_note_amount)
                    FROM credit_note
                    WHERE credit_note.branch = buyproducts.branch
                AND MONTH(credit_note.created_at) = MONTH(CURRENT_DATE)
                ) AS total_credit_note,

                  CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                COALESCE(SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) +
                SUM(DISTINCT COALESCE(total_discount_amount, 0)), 0)
            ELSE
                COALESCE(SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0)), 0)
        END -
                    (
                    SELECT SUM(credit_note.credit_note_amount)
                    FROM credit_note
                    WHERE credit_note.branch = buyproducts.branch
                AND MONTH(credit_note.created_at) = MONTH(CURRENT_DATE)
                    ) AS total
                    "))
                    ->groupBy('date')
                    ->where('buyproducts.branch', $this->location)
                    ->whereMonth('buyproducts.created_at', Carbon::today())
                    ->where('accountantlocs.user_id', $this->userid)
                    ->get();

                return $data;
            } elseif ($this->fromdate == $this->todate && $this->fromdate != "0" && $this->todate != "0") {
                $date = Carbon::createFromFormat('Y-m-d', $this->fromdate);
                $data = DB::table('accountantlocs')
                    ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                    ->select(DB::raw("
                        MONTHNAME(buyproducts.created_at) as date,

                        COUNT(distinct(buyproducts.transaction_id)) as num,

                       CASE
                            WHEN SUM(buyproducts.totalamount_wo_discount) != 0 THEN SUM(buyproducts.totalamount_wo_discount)
                            ELSE SUM(buyproducts.total_amount * buyproducts.quantity)
                        END AS grandtotal_withdiscount,

                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,


                        SUM(buyproducts.vat_amount) as vat,

                                      CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                )
            ELSE
                COALESCE(SUM(DISTINCT buyproducts.bill_grand_total), 0)
        END AS sum,

                        (
                        SELECT SUM(credit_note.credit_note_amount)
                        FROM credit_note
                        WHERE credit_note.branch = buyproducts.branch
                        AND MONTH(credit_note.created_at) = ?
                    ) AS total_credit_note,

                     CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                COALESCE(SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) +
                SUM(DISTINCT COALESCE(total_discount_amount, 0)), 0)
            ELSE
                COALESCE(SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0)), 0)
        END -
                        (
                        SELECT SUM(credit_note.credit_note_amount)
                        FROM credit_note
                        WHERE credit_note.branch = buyproducts.branch
                        AND MONTH(credit_note.created_at) = ?

                    ) AS total
                    "))
                    ->groupBy('date')
                    ->where('buyproducts.branch', $this->location)
                    ->whereMonth('buyproducts.created_at', $date)
                    ->where('accountantlocs.user_id', $this->userid)
                    ->addBinding([$date->month, $date->month], 'select') // Binding the month value twice for the subqueries
                    ->get();

                return $data;
            } elseif ($this->fromdate != $this->todate) {
                $data = DB::table('accountantlocs')
                    ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                    ->select(DB::raw("
                        MONTHNAME(buyproducts.created_at) as date,

                        COUNT(distinct(buyproducts.transaction_id)) as num,

                        CASE
                            WHEN SUM(buyproducts.totalamount_wo_discount) != 0 THEN SUM(buyproducts.totalamount_wo_discount)
                            ELSE SUM(buyproducts.total_amount * buyproducts.quantity)
                        END AS grandtotal_withdiscount,


                  ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,


                        SUM(buyproducts.vat_amount) as vat,

                                        CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                )
            ELSE
                COALESCE(SUM(DISTINCT buyproducts.bill_grand_total), 0)
        END AS sum,

                        (
                        SELECT SUM(credit_note.credit_note_amount)
                        FROM credit_note
                        WHERE credit_note.branch = buyproducts.branch
                        AND MONTH(credit_note.created_at) = MONTH(buyproducts.created_at)
                    ) AS total_credit_note,

                    CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                COALESCE(SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) +
                SUM(DISTINCT COALESCE(total_discount_amount, 0)), 0)
            ELSE
                COALESCE(SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0)), 0)
        END -
                    (
                    SELECT SUM(credit_note.credit_note_amount)
                    FROM credit_note
                    WHERE credit_note.branch = buyproducts.branch
                    AND MONTH(credit_note.created_at) = MONTH(buyproducts.created_at)

                    ) AS total
                    "))
                    ->groupBy('date')
                    ->where('buyproducts.branch', $this->location)
                    ->whereBetween('buyproducts.created_at', [$this->fromdate . ' 00:00:00', $this->todate . ' 23:59:59'])
                    ->where('accountantlocs.user_id', $this->userid)
                    ->get();

                return $data;
            }
        }
        if ($this->viewtype == 3) {

            if ($this->fromdate == "0" || $this->todate == "0") {
                $data = DB::table('accountantlocs')
                    ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                    ->select(DB::raw("
                        YEAR(buyproducts.created_at) as date,

                        COUNT(distinct(buyproducts.transaction_id)) as num,

                        CASE
                            WHEN SUM(buyproducts.totalamount_wo_discount) != 0 THEN SUM(buyproducts.totalamount_wo_discount)
                            ELSE SUM(buyproducts.total_amount * buyproducts.quantity)
                        END AS grandtotal_withdiscount,


                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,

                        SUM(buyproducts.vat_amount) as vat,

                                      CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                )
            ELSE
                COALESCE(SUM(DISTINCT buyproducts.bill_grand_total), 0)
        END AS sum,

                        (
                        SELECT SUM(credit_note.credit_note_amount)
                        FROM credit_note
                        WHERE credit_note.branch = buyproducts.branch
                    AND YEAR(credit_note.created_at) = YEAR(CURRENT_DATE)
                ) AS total_credit_note,

                      CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                (
                    SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) +
                    (
                        SELECT SUM(distinct_total_discount)
                        FROM (
                            SELECT DISTINCT transaction_id, total_discount_amount as distinct_total_discount
                            FROM buyproducts WHERE buyproducts.branch = '$this->location'
                        ) as distinct_discounts
                    )
                )
            ELSE
                COALESCE(SUM(DISTINCT buyproducts.bill_grand_total), 0)
        END -
                        (
                    SELECT SUM(credit_note.credit_note_amount)
                    FROM credit_note
                    WHERE credit_note.branch = buyproducts.branch
                AND YEAR(credit_note.created_at) = YEAR(CURRENT_DATE)

                    ) AS total
                    "))
                    ->groupBy('date')
                    ->where('buyproducts.branch', $this->location)
                    ->whereYear('buyproducts.created_at', Carbon::today())
                    ->where('accountantlocs.user_id', $this->userid)
                    ->get();

                return $data;
            } elseif ($this->fromdate == $this->todate &&  $this->fromdate != "0"  &&  $this->todate != "0") {
                $data = DB::table('accountantlocs')
                    ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                    ->select(DB::raw("
                        YEAR(buyproducts.created_at) as date,

                        COUNT(distinct(buyproducts.transaction_id)) as num,

                        CASE
                            WHEN SUM(buyproducts.totalamount_wo_discount) != 0 THEN SUM(buyproducts.totalamount_wo_discount)
                            ELSE SUM(buyproducts.total_amount * buyproducts.quantity)
                        END AS grandtotal_withdiscount,

                   ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,

                        SUM(buyproducts.vat_amount) as vat,

                                       CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                )
            ELSE
                COALESCE(SUM(DISTINCT buyproducts.bill_grand_total), 0)
        END AS sum,

                        (
                            SELECT SUM(credit_note.credit_note_amount)
                            FROM credit_note
                            WHERE credit_note.branch = buyproducts.branch
                            AND YEAR(credit_note.created_at) = ?
                        ) AS total_credit_note,

                        CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                (
                    SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) +
                    (
                        SELECT SUM(distinct_total_discount)
                        FROM (
                            SELECT DISTINCT transaction_id, total_discount_amount as distinct_total_discount
                            FROM buyproducts WHERE buyproducts.branch = '$this->location'
                        ) as distinct_discounts
                    )
                )
            ELSE
                COALESCE(SUM(DISTINCT buyproducts.bill_grand_total), 0)
        END -
                        (
                        SELECT SUM(credit_note.credit_note_amount)
                        FROM credit_note
                        WHERE credit_note.branch = buyproducts.branch
                        AND YEAR(credit_note.created_at) = ?

                    ) AS total
                    "))
                    ->groupBy('date')
                    ->where('buyproducts.branch', $this->location)
                    ->whereYear('buyproducts.created_at', $this->fromdate)
                    ->where('accountantlocs.user_id', $this->userid)
                    ->addBinding([$this->fromdate, $this->fromdate], 'select') // Add the binding for both date placeholders
                    ->get();

                return $data;
            } elseif ($this->fromdate != $this->todate) {
                $data = DB::table('accountantlocs')
                    ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                    ->select(DB::raw("
                        YEAR(buyproducts.created_at) as date,

                        COUNT(distinct(buyproducts.transaction_id)) as num,

                      CASE
                            WHEN SUM(buyproducts.totalamount_wo_discount) != 0 THEN SUM(buyproducts.totalamount_wo_discount)
                            ELSE SUM(buyproducts.total_amount * buyproducts.quantity)
                        END AS grandtotal_withdiscount,

                   ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,

                        SUM(buyproducts.vat_amount) as vat,

                                        CASE
            WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                )
            ELSE
                COALESCE(SUM(DISTINCT buyproducts.bill_grand_total), 0)
        END AS sum,

                        (
                            SELECT SUM(credit_note.credit_note_amount)
                            FROM credit_note
                            WHERE credit_note.branch = buyproducts.branch
                            AND YEAR(credit_note.created_at) = YEAR(buyproducts.created_at)
                        ) AS total_credit_note,

                       CASE
             WHEN buyproducts.vat_type = 1 THEN
                COALESCE(SUM(buyproducts.totalamount_wo_discount), 0) -
                (
                    SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) +
                    (
                        SELECT SUM(distinct_total_discount)
                        FROM (
                            SELECT DISTINCT transaction_id, total_discount_amount as distinct_total_discount
                            FROM buyproducts WHERE buyproducts.branch = '$this->location'
                        ) as distinct_discounts
                    )
                )
            ELSE
                COALESCE(SUM(DISTINCT buyproducts.bill_grand_total), 0)
        END -
                        (
                        SELECT SUM(credit_note.credit_note_amount)
                        FROM credit_note
                        WHERE credit_note.branch = buyproducts.branch
                        AND YEAR(credit_note.created_at) = YEAR(buyproducts.created_at)

                    ) AS total
                    "))
                    ->groupBy('date')
                    ->where('buyproducts.branch', $this->location)
                    ->whereBetween('buyproducts.created_at', [$this->fromdate . ' 00:00:00', $this->todate . ' 23:59:59'])
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

    $headings = [
        $dateheader,
        'Total Sales',
        'Total Amount',
        'Discount Amount',
        'Total VAT',
        'Grand Total (w/. discount)',
    ];

    // Check if any user has the role_id of 28
    foreach ($this->users as $user) {
        if ($user->role_id == '28') {
            $headings[] = 'Credit Note Amount';
            $headings[] = 'Total (Grand Total - Credit Note Amount)';
            break;
        }
    }

    return $headings;
}

public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            // Default range for all headers
            $cellRange = 'A1:F1'; // Adjust based on initial headers

            // Check if any user has the role_id of 28
            $includeColumnsGH = false;
            foreach ($this->users as $user) {
                if ($user->role_id == '28') {
                    $includeColumnsGH = true;
                    break;
                }
            }

            // Extend cell range to include G and H if the role_id is 28
            if ($includeColumnsGH) {
                $cellRange = 'A1:H1'; // Extend the range to include G and H
            }

            // Apply styling to the headers
            $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true)->setSize(14);
        },
    ];
}

}
