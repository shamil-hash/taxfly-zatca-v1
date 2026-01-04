<?php

namespace App\Exports;

use App\Models\Stockdetail;
use App\Services\PAndLService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Sheet;

class PandLReportExport implements FromView, WithEvents, WithStyles
{
    // /**
    // * @return \Illuminate\Support\Collection
    // */

    protected $start_date;
    protected $end_date;
    protected $branch;

    function __construct($start_date, $end_date, $branch)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->branch = $branch;
    }

    public function view(): View
    {
        $first_date = Stockdetail::where('branch', $this->branch)
            ->orderBy('created_at')
            ->pluck('created_at')
            ->first();

        $frst_dt = date('Y-m-d', strtotime($first_date));


        $filterpAndLService = new PAndLService();

        // Call the method to get the data
        $filterpAndLData = $filterpAndLService->filterPandL(
            $this->branch,
            $this->start_date,
            $this->end_date
        );

        return view('exports.pandlexport', array_merge(

            $filterpAndLData
        ));


        /*--------------------------------------------------------------*/
    }

    //original - buycost without vat and selling cost without vat in exclusive case

    // public function view(): View
    // {
    //     $first_date = Stockdetail::where('branch', $this->branch)
    //         ->orderBy('created_at')
    //         ->pluck('created_at')
    //         ->first();

    //     $frst_dt = date('Y-m-d', strtotime($first_date));

    //     /*------------------------------------ WITHOUT DATE FILTER ------------------------------------------------------*/

    //     if (empty($this->start_date) && empty($this->end_date) && !empty($this->branch)) {
    //         // dd('no date given');

    //         $today_date = Carbon::today()->format('Y-m-d');

    //         /*---------------------latest Opening Stock & Closing Stock--------------------------------*/

    //         $latest_stock_details = DB::table('stockdetails')
    //             ->whereDate('created_at', '<', $today_date)
    //             ->where('branch', $this->branch)
    //             ->orderBy('created_at', 'desc')
    //             ->pluck('created_at')
    //             ->first();

    //         $stock_details_created_date = $latest_stock_details ? date('Y-m-d', strtotime($latest_stock_details)) : 0;


    //         $latest_stock_return_details = DB::table('returnpurchases')
    //             ->whereDate('created_at', '<', $today_date)
    //             ->where('branch', $this->branch)
    //             ->orderBy('created_at', 'desc')
    //             ->pluck('created_at')
    //             ->first();

    //         $stock_return_details_created_date = $latest_stock_return_details ? date('Y-m-d', strtotime($latest_stock_return_details)) : 0;

    //         /*query starts */


    //         $stock_purchase_yester_without_return = DB::table('stockdetails')
    //             ->select(DB::raw("SUM(price_without_vat) as stock_purchase_amt"))
    //             ->whereDate('created_at', '<=', $stock_details_created_date)
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $stock_purchase_yester_return = DB::table('returnpurchases')
    //             ->select(DB::raw("SUM(amount_without_vat) as stock_purchase_return_amt"))
    //             ->whereDate('created_at', '<=', $stock_return_details_created_date)
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $stock_purchase_yester = $stock_purchase_yester_without_return->stock_purchase_amt - $stock_purchase_yester_return->stock_purchase_return_amt;


    //         $latest_buyproducts = DB::table('buyproducts')
    //             ->whereDate('created_at', '<', $today_date)
    //             ->where('branch', $this->branch)
    //             ->orderBy('created_at', 'desc')
    //             ->pluck('created_at')
    //             ->first();

    //         $buyproducts_created_date = $latest_buyproducts ? date('Y-m-d', strtotime($latest_buyproducts)) : 0;

    //         $latest_buyproducts_return = DB::table('returnproducts')
    //             ->whereDate('created_at', '<', $today_date)
    //             ->where('branch', $this->branch)
    //             ->orderBy('created_at', 'desc')
    //             ->pluck('created_at')
    //             ->first();

    //         $buyproducts_return_created_date = $latest_buyproducts_return ? date('Y-m-d', strtotime($latest_buyproducts_return)) : 0;

    //         /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

    //         $soldstock_yester_without_return = DB::table('buyproducts')
    //             ->select(DB::raw('SUM(buycostadd) as total_soldstock'))
    //             ->whereDate('created_at', '<=', $buyproducts_created_date)
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $soldstock__return_yester = DB::table('returnproducts')
    //             ->select(DB::raw('SUM(buycostaddreturn) as total_returnstock'))
    //             ->whereDate('created_at', '<=', $buyproducts_return_created_date)
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $soldstock = $soldstock_yester_without_return->total_soldstock - $soldstock__return_yester->total_returnstock;

    //         /*-----------------------------------------------------------------------------------------------*/

    //         $remaining_stock_closing_yester = $stock_purchase_yester - $soldstock;

    //         /*-------------------------------------------------------------------------------*/
    //         /*------------------------------TODAY open and close-----------------------------*/

    //         $stock_purchase_today_without_return = DB::table('stockdetails')
    //             ->select(DB::raw("SUM(price_without_vat) as stock_purchase_amt"))
    //             ->whereDate('created_at', $today_date)
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $stock_purchase_return_today = DB::table('returnpurchases')
    //             ->select(DB::raw("SUM(amount_without_vat) as stock_purchase_return_amt"))
    //             ->whereDate('created_at', $today_date)
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $stock_purchase = $stock_purchase_today_without_return->stock_purchase_amt - $stock_purchase_return_today->stock_purchase_return_amt;

    //         $total_stock_opening =  $remaining_stock_closing_yester + $stock_purchase;  /* opening stockn for calculation correct */


    //         /* ---------------------------------------To show opening stock ---------------------------------- */

    //         $show_opening_stock = $remaining_stock_closing_yester;

    //         /* --------------------------------------------------------------------------------- */

    //         /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

    //         $soldstock_today_without_return = DB::table('buyproducts')
    //             ->select(DB::raw('SUM(buycostadd) as total_soldstock'))
    //             ->whereDate('created_at', $today_date)
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $soldstock__return_today = DB::table('returnproducts')
    //             ->select(DB::raw('SUM(buycostaddreturn) as total_returnstock'))
    //             ->whereDate('created_at', $today_date)
    //             ->where('branch', $this->branch)
    //             ->first();

    //         // dd($soldstock__return_today);

    //         $soldstock = $soldstock_today_without_return->total_soldstock - $soldstock__return_today->total_returnstock;

    //         // dd($soldstock);

    //         /*-----------------------------------------------------------------------------------------------*/

    //         $remaining_stock_closing = $total_stock_opening - $soldstock;

    //         /*----------------------------------------*/

    //         $today_open_stock = ($today_date == $frst_dt) ? 0 : $show_opening_stock;

    //         /* ---------------- new sales and sales return ---------------- */


    //         $sold = DB::table('buyproducts')
    //             ->whereDate('buyproducts.created_at', $today_date)
    //             ->where('branch', $this->branch)
    //             ->sum('price');


    //         $salesReturn = DB::table('returnproducts')
    //             ->whereDate('returnproducts.created_at', $today_date)
    //             ->where('branch', $this->branch)
    //             ->sum('price');


    //         /*-----------------------------------------------------------*/

    //         /* ---------------- new purchase and purchase return ---------------- */

    //         $purchaseamount = DB::table('stockdetails')
    //             ->whereDate('stockdetails.created_at', $today_date)
    //             ->where('branch', $this->branch)
    //             ->sum('price_without_vat');


    //         $purchaseReturn = DB::table('returnpurchases')
    //             ->whereDate('returnpurchases.created_at', $today_date)
    //             ->where('branch', $this->branch)
    //             ->sum('amount_without_vat');

    //         /*-----------------------------------------------------------*/

    //         /*---------------------------Indirect Expense & income ---------------------*/

    //         $monthlyexpense = DB::table('accountexpenses')
    //             ->select(DB::raw("SUM(amount) as monthly_expense"))
    //             ->whereDate('date', $today_date)
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $salary_amount = DB::table('salarydatas')
    //             ->select(DB::raw("SUM(salary) as salary"))
    //             ->whereDate('date', $today_date)
    //             ->where('branch_id', $this->branch)
    //             ->first();

    //         $indirect_expenses = ($monthlyexpense->monthly_expense) + ($salary_amount->salary);

    //         $indirect_income = DB::table('account_indirect_incomes')
    //             ->select(DB::raw("SUM(amount) as indirect_income"))
    //             ->whereDate('date', $today_date)
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $indirect_income = $indirect_income->indirect_income ?? 0;

    //         /*-------------------------------------------------------------------------*/

    //         $start_date = "";
    //         $end_date = "";
    //     } else if (!empty($this->start_date) && !empty($this->end_date) && !empty($this->branch)) {


    //         /*---------------------latest Opening Stock & Closing Stock--------------------------------*/

    //         $latest_stock_details = DB::table('stockdetails')
    //             ->whereDate('created_at', '<', $this->start_date)
    //             ->where('branch', $this->branch)
    //             ->orderBy('created_at', 'desc')
    //             ->pluck('created_at')
    //             ->first();

    //         $stock_details_created_date = $latest_stock_details ? date('Y-m-d', strtotime($latest_stock_details)) : 0;


    //         $latest_stock_return_details = DB::table('returnpurchases')
    //             ->whereDate('created_at', '<', $this->start_date)
    //             ->where('branch', $this->branch)
    //             ->orderBy('created_at', 'desc')
    //             ->pluck('created_at')
    //             ->first();


    //         $stock_return_details_created_date = $latest_stock_return_details ? date('Y-m-d', strtotime($latest_stock_return_details)) : 0;

    //         /*query starts */

    //         $stock_purchase_yester_without_return = DB::table('stockdetails')
    //             ->select(DB::raw("SUM(price_without_vat) as stock_purchase_amt"))
    //             ->whereDate('created_at', '<=', $stock_details_created_date)
    //             ->where('branch', $this->branch)
    //             ->first();



    //         $stock_purchase_return_yester = DB::table('returnpurchases')
    //             ->select(DB::raw("SUM(amount_without_vat) as stock_purchase_return_amt"))
    //             ->whereDate('created_at', '<=', $stock_return_details_created_date)
    //             ->where('branch', $this->branch)
    //             ->first();



    //         $stock_purchase_yester = $stock_purchase_yester_without_return->stock_purchase_amt - $stock_purchase_return_yester->stock_purchase_return_amt;



    //         $latest_buyproducts = DB::table('buyproducts')
    //             ->whereDate('created_at', '<', $this->start_date)
    //             ->where('branch', $this->branch)
    //             ->orderBy('created_at', 'desc')
    //             ->pluck('created_at')
    //             ->first();

    //         $buyproducts_created_date = $latest_buyproducts ? date('Y-m-d', strtotime($latest_buyproducts)) : 0;

    //         $latest_buyproducts_return = DB::table('returnproducts')
    //             ->whereDate('created_at', '<', $this->start_date)
    //             ->where('branch', $this->branch)
    //             ->orderBy('created_at', 'desc')
    //             ->pluck('created_at')
    //             ->first();

    //         $buyproducts_return_created_date = $latest_buyproducts_return ? date('Y-m-d', strtotime($latest_buyproducts_return)) : 0;



    //         /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

    //         $soldstock_yester_without_return = DB::table('buyproducts')
    //             ->select(DB::raw('SUM(buycostadd) as total_soldstock'))
    //             ->whereDate('created_at', '<=', $buyproducts_created_date)
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $soldstock__return_yester = DB::table('returnproducts')
    //             ->select(DB::raw('SUM(buycostaddreturn) as total_returnstock'))
    //             ->whereDate(
    //                 'created_at',
    //                 '<=',
    //                 $buyproducts_return_created_date
    //             )
    //             ->where('branch', $this->branch)
    //             ->first();


    //         $soldstock_yester = $soldstock_yester_without_return->total_soldstock - $soldstock__return_yester->total_returnstock;

    //         /*-----------------------------------------------------------------------------------------------*/
    //         $remaining_stock_closing_yester = $stock_purchase_yester - $soldstock_yester;


    //         /*----------------------------TODAY OPENING STOCK AND CLOSING STOCK---------------------------------------------------*/

    //         $stock_purchase_today_without_return = DB::table('stockdetails')
    //             ->select(DB::raw("SUM(price_without_vat) as stock_purchase_amt"))
    //             ->whereBetween('created_at', [$this->start_date . ' 00:00:00', $this->end_date . ' 23:59:59'])
    //             ->where('branch', $this->branch)
    //             ->first();


    //         $stock_purchase_return_today = DB::table('returnpurchases')
    //             ->select(DB::raw("SUM(amount_without_vat) as stock_purchase_return_amt"))
    //             ->whereBetween('created_at', [$this->start_date . ' 00:00:00', $this->end_date . ' 23:59:59'])
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $stock_purchase = $stock_purchase_today_without_return->stock_purchase_amt - $stock_purchase_return_today->stock_purchase_return_amt;


    //         $total_stock_opening =  $remaining_stock_closing_yester + $stock_purchase;


    //         /* ---------------------------------------To show opening stock ---------------------------------- */

    //         $show_opening_stock = $remaining_stock_closing_yester;

    //         /* --------------------------------------------------------------------------------- */


    //         /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

    //         $soldstock_today_without_return = DB::table('buyproducts')
    //             ->select(DB::raw('SUM(buycostadd) as total_soldstock'))
    //             ->whereBetween('created_at', [$this->start_date . ' 00:00:00', $this->end_date . ' 23:59:59'])
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $soldstock__return_today = DB::table('returnproducts')
    //             ->select(DB::raw('SUM(buycostaddreturn) as total_returnstock'))
    //             ->whereBetween('created_at', [$this->start_date . ' 00:00:00', $this->end_date . ' 23:59:59'])
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $soldstock = $soldstock_today_without_return->total_soldstock - $soldstock__return_today->total_returnstock;

    //         /*-----------------------------------------------------------------------------------------------*/

    //         $remaining_stock_closing = $total_stock_opening - $soldstock;


    //         if ($this->start_date == $this->end_date) {
    //             if ($this->start_date == $frst_dt) {
    //                 $today_open_stock = 0;
    //             } else {


    //                 $today_open_stock = $show_opening_stock;
    //             }
    //         } elseif ($this->start_date < $frst_dt && $this->end_date != $frst_dt) {
    //             $today_open_stock = 0;
    //         } else {
    //             if ($this->start_date == $frst_dt) {
    //                 $today_open_stock = 0;
    //             } else if ($this->end_date == $frst_dt) {
    //                 $today_open_stock = 0;
    //             } else {

    //                 $today_open_stock = $show_opening_stock;
    //             }
    //         }


    //         /*-----------------------------------------------------------*/

    //         /* ---------------- new sales and sales return ---------------- */

    //         $sold = DB::table('buyproducts')
    //             ->whereBetween('created_at', [$this->start_date . ' 00:00:00', $this->end_date . ' 23:59:59'])
    //             ->where('branch', $this->branch)
    //             ->sum('price');



    //         $salesReturn = DB::table('returnproducts')
    //             ->whereBetween('returnproducts.created_at', [$this->start_date . ' 00:00:00', $this->end_date . ' 23:59:59'])
    //             ->where('branch', $this->branch)
    //             ->sum('price');



    //         /*-----------------------------------------------------------*/

    //         /* ---------------- new purchase and purchase return ---------------- */

    //         $purchaseamount = DB::table('stockdetails')
    //             ->whereBetween('stockdetails.created_at', [$this->start_date . ' 00:00:00', $this->end_date . ' 23:59:59'])
    //             ->where('branch', $this->branch)
    //             ->sum('price_without_vat');

    //         $purchaseReturn = DB::table('returnpurchases')
    //             ->whereBetween('returnpurchases.created_at', [$this->start_date . ' 00:00:00', $this->end_date . ' 23:59:59'])
    //             ->where('branch', $this->branch)
    //             ->sum('amount_without_vat');

    //         /*-----------------------------------------------------------*/

    //         /*-----------------------------------------------------------*/
    //         /*---------------------------Indirect Expense & income ---------------------*/

    //         $monthlyexpense = DB::table('accountexpenses')
    //             ->select(DB::raw("SUM(amount) as monthly_expense"))
    //             ->whereBetween('date', [$this->start_date, $this->end_date])
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $salary_amount = DB::table('salarydatas')
    //             ->select(DB::raw("SUM(salary) as salary"))
    //             ->whereBetween('date', [$this->start_date, $this->end_date])
    //             ->where('branch_id', $this->branch)
    //             ->first();

    //         $indirect_expenses = ($monthlyexpense->monthly_expense) + ($salary_amount->salary);

    //         $indirect_income = DB::table('account_indirect_incomes')
    //             ->select(DB::raw("SUM(amount) as indirect_income"))
    //             ->whereBetween('date', [$this->start_date, $this->end_date])
    //             ->where('branch', $this->branch)
    //             ->first();

    //         $indirect_income = $indirect_income->indirect_income ?? 0;

    //         /*-------------------------------------------------------------------------*/

    //         $start_date = $this->start_date;
    //         $end_date = $this->end_date;
    //     }

    //     /*--------------------------------------------------------------*/

    //     return view('exports.pandlexport', ['opening_stock' => $today_open_stock, 'closing_stock' => $remaining_stock_closing, 'soldstock_value' => $sold, 'purchase_amount' => $purchaseamount, 'start_date' => $start_date, 'end_date' => $end_date, 'indirect_expenses' => $indirect_expenses, 'indirect_income' => $indirect_income, 'purchaseReturn' => $purchaseReturn, 'salesReturn' => $salesReturn]);
    //     //return view('exports.pandlexport', ['opening_stock' => $total_stock_opening, 'closing_stock' => $remaining_stock_closing, 'soldstock_value' => $sold, 'purchase_amount' => $purchaseamount, 'start_date' => $start_date, 'end_date' => $end_date, 'indirect_expenses' => $indirect_expenses, 'indirect_income' => $indirect_income, 'loss' => $loss, 'profit' => $profit]);
    // }
    public function registerEvents(): array
    {
        return [

            AfterSheet::class    => function (AfterSheet $event) {

                $event->sheet->getColumnDimension('A')->setAutoSize(false);
                $event->sheet->getColumnDimension('B')->setAutoSize(false);
                $event->sheet->getColumnDimension('C')->setAutoSize(false);
                $event->sheet->getColumnDimension('D')->setAutoSize(false);
                $event->sheet->getColumnDimension('E')->setAutoSize(false);
                $event->sheet->getColumnDimension('F')->setAutoSize(false);

                $event->sheet->getColumnDimension('A')->setWidth(32);
                $event->sheet->getColumnDimension('B')->setWidth(32);
                $event->sheet->getColumnDimension('C')->setWidth(32);
                $event->sheet->getColumnDimension('D')->setWidth(32);
                $event->sheet->getColumnDimension('E')->setWidth(32);
                $event->sheet->getColumnDimension('F')->setWidth(32);

                Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
                    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
                });

                $event->sheet->styleCells('B:B', [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
                $event->sheet->styleCells('C:C', [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
                $event->sheet->styleCells('E:E', [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
                $event->sheet->styleCells('F:F', [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
            },
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            'A'  => ['font' => ['bold' => true, 'size' => 14]],
            'B'  => ['font' => ['bold' => true, 'size' => 14]],
            'C'  => ['font' => ['bold' => true, 'size' => 14]],
            'D'  => ['font' => ['bold' => true, 'size' => 14]],
            'E'  => ['font' => ['bold' => true, 'size' => 14]],
            'F'  => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}
