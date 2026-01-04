<?php

namespace App\Services;

use App\Models\Stockdetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PAndLService
{

    public function __construct()
    {
    }

    // new - buycost with vat and netrate as selling cost with vat

    public function normalPandL()
    {

        $userid = Session('adminuser');

        $location = DB::table('branches')
            ->get();

        $first_location = DB::table('branches')->pluck('id')->first();


        $first_date = Stockdetail::where('branch', $first_location)
            ->orderBy('created_at')
            ->pluck('created_at')
            ->first();

        $frst_dt = date('Y-m-d', strtotime($first_date));

        $date = Carbon::today()->format('Y-m-d');

        /*---------------------latest Opening Stock & Closing Stock--------------------------------*/

        $latest_stock_details = DB::table('stockdetails')
            ->whereDate('created_at', '<', $date)
            ->where('branch', $first_location)
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->first();


        if ($latest_stock_details) {
            $stock_details_created_date = date('Y-m-d', strtotime($latest_stock_details));
        } else {
            $stock_details_created_date = 0;
        }

        $latest_stockerturn_details = DB::table('returnpurchases')
            ->whereDate('created_at', '<', $date)
            ->where('branch', $first_location)
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->first();

        if ($latest_stockerturn_details) {
            $stock_return_details_created_date = date('Y-m-d', strtotime($latest_stockerturn_details));
        } else {
            $stock_return_details_created_date = 0;
        }

        $stock_purchase_yester_without_return = DB::table('stockdetails')
        ->select(DB::raw("SUM(price) as stock_purchase_amt"))
        ->whereDate('created_at', '<=', $stock_details_created_date)
            ->where('branch', $first_location)
            ->first();

        $stock_purchase_return_yester = DB::table('returnpurchases')
            ->select(DB::raw("SUM(amount) as stock_purchase_return_amt"))
            ->whereDate('created_at', '<=', $stock_return_details_created_date)
            ->where('branch', $first_location)
            ->first();

        $stock_purchase_yester = $stock_purchase_yester_without_return->stock_purchase_amt - $stock_purchase_return_yester->stock_purchase_return_amt;

        // dd($stock_purchase_return_yester);

        $latest_buyproducts = DB::table('buyproducts')
            ->whereDate('created_at', '<', $date)
            ->where('branch', $first_location)
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->first();

        if ($latest_buyproducts) {
            $buyproducts_created_date = date('Y-m-d', strtotime($latest_buyproducts));
        } else {
            $buyproducts_created_date = 0;
        }

        $latest_buyproducts_return = DB::table('returnproducts')
            ->whereDate('created_at', '<', $date)
            ->where('branch', $first_location)
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->first();

        if ($latest_buyproducts_return) {
            $buyproducts_return_created_date = date('Y-m-d', strtotime($latest_buyproducts_return));
        } else {
            $buyproducts_return_created_date = 0;
        }

        /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

        $soldstock_yester_without_return = DB::table('buyproducts')
            ->select(DB::raw('SUM(buycost_rate_add) as total_soldstock'))
            ->whereDate('created_at', '<=', $buyproducts_created_date)
            ->where('branch', $first_location)
            ->first();

        $soldstock__return_yester = DB::table('returnproducts')
            ->select(DB::raw('SUM(buycost_rate_addreturn) as total_returnstock'))
            ->whereDate('created_at', '<=', $buyproducts_return_created_date)
            ->where('branch', $first_location)
            ->first();

        $soldstock_yester = $soldstock_yester_without_return->total_soldstock - $soldstock__return_yester->total_returnstock;

        /*-----------------------------------------------------------------------------------------------*/

        $remaining_stock_closing_yester = $stock_purchase_yester - $soldstock_yester;

        // dd($remaining_stock_closing_yester);
        /*-------------------------------------------------------------------------------*/
        /*------------------------------TODAY open and close-----------------------------*/

        $stock_purchase_without_return = DB::table('stockdetails')
        ->select(DB::raw("SUM(price) as stock_purchase_amt"))
        ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->first();

        // dd($stock_purchase_without_return);

        $stock_purchase_return = DB::table('returnpurchases')
            ->select(DB::raw("SUM(amount) as stock_purchase_return_amt"))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->first();

            $purchase_return_discount = DB::table('returnpurchases')
            ->select(DB::raw("SUM(COALESCE(discount,0)) as purchase_return_discount"))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->value('purchase_return_discount');

        // dd($stock_purchase_return);

        $stock_purchase = $stock_purchase_without_return->stock_purchase_amt - $stock_purchase_return->stock_purchase_return_amt;

        // dd($stock_purchase);

        $total_stock_opening =  $remaining_stock_closing_yester + $stock_purchase; /* opening stockn for calculation correct */

        // dd($stock_purchase);

        /* ---------------------------------------To show opening stock ---------------------------------- */

        // $stock_purchase_amt_yester_show = DB::table('stockdetails')
        //     ->select(DB::raw("SUM(price) as st_purchase_amt"))
        //     ->whereDate('created_at', '<=', $stock_details_created_date)
        //     ->where('branch', $first_location)
        //     ->first();

        // // dd($stock_purchase_amt_yester_show);

        // $soldstock_amt_yester_show = DB::table('buyproducts')
        //     ->select(DB::raw('SUM(quantity * one_pro_buycost) as tot_soldstock'))
        //     ->whereDate('created_at', '<=', $buyproducts_created_date)
        //     ->where('branch', $first_location)
        //     ->first();

        // dd($soldstock_amt_yester_show);

        // $remaining_stock_closing_yester_show = $stock_purchase_amt_yester_show->st_purchase_amt - $soldstock_amt_yester_show->tot_soldstock;

        $show_opening_stock = $remaining_stock_closing_yester; /* opening stock for show */

        /* --------------------------------------------------------------------------------- */

        /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

        $soldstock_today_without_return = DB::table('buyproducts')
            ->select(DB::raw('SUM(buycost_rate_add) as total_soldstock'))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->first();

        $soldstock__return_today = DB::table('returnproducts')
            ->select(DB::raw('SUM(buycost_rate_addreturn) as total_returnstock'))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->first();

        $soldstock = $soldstock_today_without_return->total_soldstock - $soldstock__return_today->total_returnstock;

        /*-----------------------------------------------------------------------------------------------*/

        $remaining_stock_closing = $total_stock_opening - $soldstock;

        // dd($total_stock_opening);

        /*----------------------------------------*/

        if ($date == $frst_dt) {
            $today_open_stock = 0;
        } else {

            // $today_open_stock = $total_stock_opening;

            $today_open_stock = $show_opening_stock;
        }

        /*---------------------------Sold Stock ---------------------*/

        // $sold = DB::table('buyproducts')
        //     ->whereDate('buyproducts.created_at', $date)
        //     ->where('buyproducts.branch', $first_location)
        //     ->selectRaw('SUM(COALESCE((mrp * quantity) + vat_amount)) as total_sold')
        //     ->value('total_sold');

        $total_sold_vat_type_1 = DB::table('buyproducts')
    ->whereDate('created_at', $date)
    ->where('branch', $first_location)
    ->where('vat_type', 1)
    ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
    ->value('total_sold');

// Calculate total sold for vat_type 2
$total_sold_vat_type_2 = DB::table('buyproducts')
    ->whereDate('created_at', $date)
    ->where('branch', $first_location)
    ->where('vat_type', 2)
    ->selectRaw('SUM(COALESCE((mrp * quantity) + vat_amount)) as total_sold')
    ->value('total_sold');

// Combine results if needed
$sold = ($total_sold_vat_type_1 ?? 0) + ($total_sold_vat_type_2 ?? 0);

$total_credit_note = DB::table('credit_note')
    ->whereDate('created_at', $date)
    ->where('branch', $first_location)
    ->selectRaw('SUM(credit_note_amount) as total_credit')
    ->value('total_credit');

    // dd($total_credit_note);



        // Retrieve vat_type for a specific row or condition
                // $vat_type = DB::table('buyproducts')
                // ->whereDate('created_at', $date)
                // ->where('branch', $first_location)
                // ->value('vat_type');

                // if ($vat_type == 1) {
                // $sold = DB::table('buyproducts')
                //     ->whereDate('created_at', $date)
                //     ->where('branch', $first_location)
                //     ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
                //     ->value('total_sold');
                // } elseif ($vat_type == 2) {
                // $sold = DB::table('buyproducts')
                //     ->whereDate('created_at', $date)
                //     ->where('branch', $branch)
                //     ->selectRaw('SUM(COALESCE((mrp * quantity) + vat_amount)) as total_sold')
                //     ->value('total_sold');
                // }




            // $sold = DB::table('buyproducts')
            // ->whereDate('buyproducts.created_at', $date)
            // ->where('buyproducts.branch', $first_location)
            // // ->selectRaw('SUM(CASE WHEN totalamount_wo_discount IS NULL THEN total_amount ELSE totalamount_wo_discount END) as total_sold')
            // ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
            // ->value('total_sold');


        $salesReturn = DB::table('returnproducts')
            ->whereDate('returnproducts.created_at', $date)
            ->where('branch', $first_location)
            // ->selectRaw('SUM(CASE WHEN totalamount_wo_discount IS NULL THEN total_amount ELSE totalamount_wo_discount END) as total_sold')
            ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
            ->value('total_sold_return');
        /*-----------------------------------------------------------*/

        /* ---------------- new purchase and purchase return ---------------- */

        // $purchaseamount = DB::table('stockdetails')
        //     ->whereDate('stockdetails.created_at', $date)
        //     ->where('branch', $first_location)
        //     ->sum('price');

            $purchaseamounts = DB::table('stockdetails')
                ->whereDate('stockdetails.created_at', $date)
                ->where('branch', $first_location)
                ->select(DB::raw('SUM(stockdetails.price) as total_price'))
                ->first();

            // Access the result
            $purchaseamount = $purchaseamounts->total_price;
        // dd($purchaseamount);

        // $purchaseReturn = DB::table('returnpurchases')
        //     ->whereDate('returnpurchases.created_at', $date)
        //     ->where('branch', $first_location)
        //     ->sum('amount');

                $purchaseReturns = DB::table('returnpurchases')
                ->whereDate('returnpurchases.created_at', $date)
                ->where('branch', $first_location)
                ->select(DB::raw('SUM(returnpurchases.amount) as total_price'))
                ->first();

            $purchaseReturn = $purchaseReturns->total_price;

        // dd($purchaseReturn);
        /*-----------------------------------------------------------*/
        /*---------------------------Indirect Expense & income ---------------------*/
        $monthlyexpense = DB::table('accountexpenses')
            ->select(DB::raw("SUM(amount) as monthly_expense"))
            ->whereDate('date', $date)
            ->where('branch', $first_location)
            ->first();

        // dd($monthlyexpense);

        $salary_amount = DB::table('salarydatas')
            ->select(DB::raw("SUM(salary) as salary"))
            ->whereDate('date', $date)
            ->where('branch_id', $first_location)
            ->first();

        // $indirect_expenses = ($monthlyexpense->monthly_expense) + ($salary_amount->salary);

        // $indirect_income = DB::table('account_indirect_incomes')
        //     ->select(DB::raw("SUM(amount) as indirect_income"))
        //     ->whereDate('date', $date)
        //     ->where('branch', $first_location)
        //     ->first();

        // $indirect_income = $indirect_income->indirect_income ?? 0;

        /*-------------------------------------------------------------------------*/

        $sales_discount = DB::table('buyproducts')
            ->select(DB::raw("SUM(discount_amount * remain_quantity) as discount_amount"))
        ->whereDate('created_at', $date)
        ->where('branch', $first_location)
            ->first();

        // $return_discount = DB::table('returnproducts')
        //     ->select(DB::raw("SUM(discount_amount) as discount_amount"))
        //     ->whereDate('created_at', $date)
        //     ->where('branch', $first_location)
        //     ->first();

        $discount = DB::table('buyproducts')
        ->select(DB::raw('SUM(total_discount) as total_discount'))
        ->from(DB::raw('(SELECT transaction_id, COALESCE(SUM(buyproducts.discount_amount * buyproducts.quantity), 0) + COALESCE(SUM(total_discount_amount), 0) AS total_discount FROM buyproducts WHERE DATE(created_at) = "' . $date . '" AND branch = ' . $first_location . ' GROUP BY transaction_id) as subquery'))
        ->value('total_discount');


        $discount = DB::table('buyproducts')
            ->select(DB::raw('SUM(subquery.total_discount) as total_discount'))
            ->from(DB::raw('
                (
                    SELECT
                        transaction_id,
                        COALESCE(SUM(discount_amount * quantity), 0) AS product_discounts,
                        COALESCE(MAX(total_discount_amount), 0) AS transaction_discount,
                        COALESCE(SUM(discount_amount * quantity), 0) + COALESCE(MAX(total_discount_amount), 0) AS total_discount
                    FROM
                        buyproducts
                    WHERE
                        DATE(created_at) = "' . $date . '"
                        AND branch = ' . $first_location . '
                    GROUP BY
                        transaction_id
                ) as subquery
            '))
            ->value('total_discount');


            $return_discount = DB::table('returnproducts')
                ->select(DB::raw("ROUND(SUM(COALESCE(discount_amount,0)) + SUM(total_amount * (COALESCE(total_discount_percent,0)/100)), 2) as return_discount"))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->value('return_discount');



            $discountpurchase = DB::table('stockdetails')
            ->select(DB::raw("ROUND(SUM(COALESCE(discount, 0)), 2) as discountpurchase"))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->value('discountpurchase');

            $purchaseservice = DB::table('stockdetails')
            ->select(DB::raw("SUM(price) - ROUND(SUM(COALESCE(discount, 0)), 2) AS purchaseservice"))
            ->whereDate('created_at', $date)
            ->where('method','=',2)
            ->where('branch', $first_location)
            ->value('purchaseservice');

            $returnpurchaseservice = DB::table('returnpurchases')
            ->select(DB::raw("SUM(amount) - ROUND(SUM(COALESCE(discount, 0)), 2) AS returnpurchaseservice"))
            ->whereDate('created_at', $date)
            ->where('method','=',2)
            ->where('branch', $first_location)
            ->value('returnpurchaseservice');

            $service_cost = DB::table('buyproducts')
            ->select(DB::raw("SUM(COALESCE(service_cost, 0) * remain_quantity) as service_cost"))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->value('service_cost');


            $onlyservice_cost = DB::table('service')
            ->select(DB::raw("SUM(COALESCE(total_amount, 0)) as service_cost"))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->value('service_cost');

            $direct_expense = DB::table('accountexpenses')
            ->select(DB::raw('direct_expense,SUM(amount) as amount'))
            ->whereDate('date', $date)
            ->where('branch', $first_location)
            ->where('expense_type', 1)
            ->groupBy('direct_expense')
            ->get();


            $indirect_expense = DB::table('accountexpenses')
            ->select(DB::raw('indirect_expense,SUM(amount) as amount'))
            ->whereDate('date', $date)
            ->where('branch', $first_location)
            ->where('expense_type',2)
            ->groupBy('indirect_expense')
            ->get();

            $direct_income = DB::table('account_indirect_incomes')
            ->select(DB::raw('direct_income,SUM(amount) as amount'))
            ->whereDate('date', $date)
            ->where('branch', $first_location)
            ->where('income_type',1)
            ->groupBy('direct_income')
            ->get();


            $indirect_incomes = DB::table('account_indirect_incomes')
            ->select(DB::raw('indirect_income,SUM(amount) as amount'))
            ->whereDate('date', $date)
            ->where('branch', $first_location)
            ->where('income_type',2)
            ->groupBy('indirect_income')
            ->get();

            // dd($direct_expense);


            // Calculate total amounts
            $total_direct_expense = $direct_expense->sum('amount');
            $total_direct_income = $direct_income->sum('amount');
            $total_indirect_expense = $indirect_expense->sum('amount');
            $total_indirect_income = $indirect_incomes->sum('amount');

        return [
            'soldstock_value' => $sold,
            'opening_stock' => $today_open_stock,
            'closing_stock' => $remaining_stock_closing,
            'purchase_amount' => $purchaseamount,
            // 'indirect_expenses' => $indirect_expenses,
            // 'indirect_income' => $indirect_income,
            'locations' => $location,
            'purchaseReturn' => $purchaseReturn,
            'salesReturn' => $salesReturn,
            'discount' => $discount,
            'return_discount'=>$return_discount,
            'discountpurchase' => $discountpurchase,
            'purchaseservice' => $purchaseservice,
            'returnpurchaseservice' => $returnpurchaseservice,

            'purchase_return_discount'=>$purchase_return_discount,
            'direct_expense' => $direct_expense,
            'indirect_expense' => $indirect_expense,
            'direct_income' => $direct_income,
            'indirect_incomes' => $indirect_incomes,
            'total_direct_expense' => $total_direct_expense,
            'total_direct_income' => $total_direct_income,
            'total_indirect_expense' => $total_indirect_expense,
            'total_indirect_income' => $total_indirect_income,
            'total_credit_note'=>$total_credit_note,
            'service_cost'=>$service_cost,
            'onlyservice_cost'=>$onlyservice_cost,

        ];
    }

    public function filterPandL($branch, $start_date, $end_date)
    {

        $location = DB::table('branches')
            ->get();

        $first_date = Stockdetail::where('branch', $branch)
            ->orderBy('created_at')
            ->pluck('created_at')
            ->first();

            $userid = Session('adminuser');


        $frst_dt = date('Y-m-d', strtotime($first_date));

        /*------------------------------------ WITHOUT DATE FILTER ------------------------------------------------------*/

        if (empty($start_date) && empty($end_date) && !empty($branch)) {

            $today_date = Carbon::today()->format('Y-m-d');

            /*---------------------latest Opening Stock & Closing Stock--------------------------------*/

            $latest_stock_details = DB::table('stockdetails')
                ->whereDate('created_at', '<', $today_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $stock_details_created_date = $latest_stock_details ? date('Y-m-d', strtotime($latest_stock_details)) : 0;


            $latest_stock_return_details = DB::table('returnpurchases')
                ->whereDate('created_at', '<', $today_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $stock_return_details_created_date = $latest_stock_return_details ? date('Y-m-d', strtotime($latest_stock_return_details)) : 0;

            /*query starts */

            $stock_purchase_yester_without_return = DB::table('stockdetails')
            ->select(DB::raw("SUM(price) as stock_purchase_amt"))
            ->whereDate('created_at', '<=', $stock_details_created_date)
                ->where('branch', $branch)
                ->first();

            $stock_purchase_yester_return = DB::table('returnpurchases')
                ->select(DB::raw("SUM(amount) as stock_purchase_return_amt"))
                ->whereDate('created_at', '<=', $stock_return_details_created_date)
                ->where('branch', $branch)
                ->first();

                // $purchase_return_discount = DB::table('returnpurchases')
                // ->select(DB::raw("SUM(COALESCE(discount,0)) as purchase_return_discount"))
                // ->whereDate('created_at', '<=', $stock_return_details_created_date)
                // ->where('branch', $branch)
                // ->value('purchase_return_discount');


            $stock_purchase_yester = $stock_purchase_yester_without_return->stock_purchase_amt - $stock_purchase_yester_return->stock_purchase_return_amt;

            // dd($stock_purchase_yester);

            $latest_buyproducts = DB::table('buyproducts')
                ->whereDate('created_at', '<', $today_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $buyproducts_created_date = $latest_buyproducts ? date('Y-m-d', strtotime($latest_buyproducts)) : 0;

            $latest_buyproducts_return = DB::table('returnproducts')
                ->whereDate('created_at', '<', $today_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $buyproducts_return_created_date = $latest_buyproducts_return ? date('Y-m-d', strtotime($latest_buyproducts_return)) : 0;

            /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

            $soldstock_yester_without_return = DB::table('buyproducts')
                ->select(DB::raw('SUM(buycost_rate_add) as total_soldstock'))
                ->whereDate('created_at', '<=', $buyproducts_created_date)
                ->where('branch', $branch)
                ->first();

            $soldstock__return_yester = DB::table('returnproducts')
                ->select(DB::raw('SUM(buycost_rate_addreturn) as total_returnstock'))
                ->whereDate('created_at', '<=', $buyproducts_return_created_date)
                ->where('branch', $branch)
                ->first();

            $soldstock = $soldstock_yester_without_return->total_soldstock - $soldstock__return_yester->total_returnstock;

            /*-----------------------------------------------------------------------------------------------*/

            $remaining_stock_closing_yester = $stock_purchase_yester - $soldstock;

            /*-------------------------------------------------------------------------------*/
            /*------------------------------TODAY open and close-----------------------------*/

            $stock_purchase_today_without_return = DB::table('stockdetails')
            ->select(DB::raw("SUM(price) as stock_purchase_amt"))
            ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->first();

            $stock_purchase_return_today = DB::table('returnpurchases')
                ->select(DB::raw("SUM(amount) as stock_purchase_return_amt"))
                ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->first();

                $purchase_return_discount = DB::table('returnpurchases')
                ->select(DB::raw("SUM(COALESCE(discount,0)) as purchase_return_discount"))
                ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->value('purchase_return_discount');


            $stock_purchase = $stock_purchase_today_without_return->stock_purchase_amt - $stock_purchase_return_today->stock_purchase_return_amt;

            $total_stock_opening =  $remaining_stock_closing_yester + $stock_purchase;  /* opening stockn for calculation correct */


            /* ---------------------------------------To show opening stock ---------------------------------- */

            // $stock_purchase_amt_yester_show = DB::table('stockdetails')
            //     ->select(DB::raw("SUM(price) as st_purchase_amt"))
            //     ->whereDate('created_at', '<=', $stock_details_created_date)
            //     ->where('branch', $branch)
            //     ->first();

            // $soldstock_amt_yester_show = DB::table('buyproducts')
            //     ->select(DB::raw('SUM(quantity * one_pro_buycost) as tot_soldstock'))
            //     ->whereDate('created_at', '<=', $buyproducts_created_date)
            //     ->where('branch', $branch)
            //     ->first();


            // $remaining_stock_closing_yester_show = $stock_purchase_amt_yester_show->st_purchase_amt - $soldstock_amt_yester_show->tot_soldstock;

            // $show_opening_stock = $remaining_stock_closing_yester_show; /* opening stock for show */

            $show_opening_stock = $remaining_stock_closing_yester;

            /* --------------------------------------------------------------------------------- */

            /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

            $soldstock_today_without_return = DB::table('buyproducts')
                ->select(DB::raw('SUM(buycost_rate_add) as total_soldstock'))
                ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->first();

            $soldstock__return_today = DB::table('returnproducts')
                ->select(DB::raw('SUM(buycost_rate_addreturn) as total_returnstock'))
                ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->first();

            $soldstock = $soldstock_today_without_return->total_soldstock - $soldstock__return_today->total_returnstock;

            /*-----------------------------------------------------------------------------------------------*/

            $remaining_stock_closing = $total_stock_opening - $soldstock;

            /*----------------------------------------*/

            $today_open_stock = ($today_date == $frst_dt) ? 0 : $show_opening_stock;


            /* ---------------- new sales and sales return ---------------- */

            $sold = DB::table('buyproducts')
                ->whereDate('buyproducts.created_at', $today_date)
                ->where('branch', $branch)
                // ->selectRaw('SUM(CASE WHEN totalamount_wo_discount IS NULL THEN total_amount ELSE totalamount_wo_discount END) as total_sold')
                ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
                ->value('total_sold');

            $salesReturn = DB::table('returnproducts')
                ->whereDate('returnproducts.created_at', $today_date)
                ->where('branch', $branch)
                // ->selectRaw('SUM(CASE WHEN totalamount_wo_discount IS NULL THEN total_amount ELSE totalamount_wo_discount END) as total_sold')
                ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
                ->value('total_sold');

            /*-----------------------------------------------------------*/

            /* ---------------- new purchase and purchase return ---------------- */

            // $purchaseamount = DB::table('stockdetails')
            //     ->whereDate('stockdetails.created_at', $today_date)
            //     ->where('branch', $branch)
            //     ->sum('price');

                $purchaseamounts = DB::table('stockdetails')
                ->whereDate('stockdetails.created_at', $today_date)
                ->where('branch', $branch)
                ->select(DB::raw('SUM(stockdetails.price) as total_price'))
                ->first();

            // Access the result
            $purchaseamount = $purchaseamounts->total_price;

            // $purchaseReturn = DB::table('returnpurchases')
            //     ->whereDate('returnpurchases.created_at', $today_date)
            //     ->where('branch', $branch)
            //     ->sum('amount');

                    $purchaseReturns = DB::table('returnpurchases')
                    ->whereDate('returnpurchases.created_at', $today_date)
                    ->where('branch', $branch)
                    ->select(DB::raw('SUM(returnpurchases.amount) as total_price'))
                    ->first();

                $purchaseReturn = $purchaseReturns->total_price;

            /*-----------------------------------------------------------*/

            /*---------------------------Indirect Expense & income ---------------------*/

            $monthlyexpense = DB::table('accountexpenses')
                ->select(DB::raw("SUM(amount) as monthly_expense"))
                ->whereDate('date', $today_date)
                ->where('branch', $branch)
                ->first();

            $salary_amount = DB::table('salarydatas')
                ->select(DB::raw("SUM(salary) as salary"))
                ->whereDate('date', $today_date)
                ->where('branch_id', $branch)
                ->first();

            $indirect_expenses = ($monthlyexpense->monthly_expense) + ($salary_amount->salary);

            $indirect_income = DB::table('account_indirect_incomes')
                ->select(DB::raw("SUM(amount) as indirect_income"))
                ->whereDate('date', $today_date)
                ->where('branch', $branch)
                ->first();

            $indirect_income = $indirect_income->indirect_income ?? 0;

// -------------

            $direct_expense = DB::table('accountexpenses')
            ->select(DB::raw('direct_expense,SUM(amount) as amount'))
            ->whereDate('date', $today_date)
            ->where('branch', $branch)
            ->where('expense_type', 1)
            // ->where('user_id', $userid)
            ->groupBy('direct_expense')
            ->get();

            $indirect_expense = DB::table('accountexpenses')
            ->select(DB::raw('indirect_expense,SUM(amount) as amount'))
            ->whereDate('date', $today_date)
            ->where('branch', $branch)
            ->where('expense_type',2)
            // ->where('user_id', $userid)
            ->groupBy('indirect_expense')
            ->get();

            $direct_income = DB::table('account_indirect_incomes')
            ->select(DB::raw('direct_income,SUM(amount) as amount'))
            ->whereDate('date', $today_date)
            ->where('branch', $branch)
            ->where('income_type',1)
            // ->where('user_id', $userid)
            ->groupBy('direct_income')
            ->get();

            $indirect_incomes = DB::table('account_indirect_incomes')
            ->select(DB::raw('indirect_income,SUM(amount) as amount'))
            ->whereDate('date', $today_date)
            ->where('branch', $branch)
            ->where('income_type',2)
            // ->where('user_id', $userid)
            ->groupBy('indirect_income')
            ->get();


            $discountpurchase = DB::table('stockdetails')
            ->select(DB::raw("ROUND(SUM(COALESCE(discount, 0)), 2) as discountpurchase"))
            ->whereDate('created_at', $today_date)
            ->where('branch', $branch)
            ->value('discountpurchase');


            // Calculate total amounts
            $total_direct_expense = $direct_expense->sum('amount');
            $total_direct_income = $direct_income->sum('amount');
            $total_indirect_expense = $indirect_expense->sum('amount');
            $total_indirect_income = $indirect_incomes->sum('amount');

            /*-------------------------------------------------------------------------*/

            // $sales_discount = DB::table('buyproducts')
            //     ->select(DB::raw("SUM(discount_amount) as discount_amount"))
            //     ->whereDate('created_at', $today_date)
            //     ->where('branch', $branch)
            //     ->first();

            // $return_discount = DB::table('returnproducts')
            //     ->select(DB::raw("SUM(discount_amount) as discount_amount"))
            //     ->whereDate('created_at', $today_date)
            //     ->where('branch', $branch)
            //     ->first();

            // $discount = $sales_discount->discount_amount - $return_discount->discount_amount;


            // $discount = DB::table('buyproducts')
            //     ->select(DB::raw('SUM(total_discount) as total_discount'))
            //     ->from(DB::raw('(SELECT transaction_id, COALESCE(SUM(buyproducts.discount_amount * buyproducts.remain_quantity), 0) + COALESCE(SUM(total_discount_amount), 0) AS total_discount FROM buyproducts WHERE DATE(created_at) = "' . $today_date . '" AND branch = ' . $branch . ' GROUP BY transaction_id) as subquery'))
            //     ->value('total_discount');

            $discount = DB::table('buyproducts')
                ->select(DB::raw('SUM(subquery.total_discount) as total_discount'))
                ->from(DB::raw('
                    (
                        SELECT
                            transaction_id,
                            COALESCE(SUM(discount_amount * quantity), 0) AS product_discounts,
                            COALESCE(MAX(total_discount_amount), 0) AS transaction_discount,
                            COALESCE(SUM(discount_amount * quantity), 0) + COALESCE(MAX(total_discount_amount), 0) AS total_discount
                        FROM
                            buyproducts
                        WHERE
                            DATE(created_at) = "' . $today_date . '"
                            AND branch = ' . $branch . '
                        GROUP BY
                            transaction_id
                    ) as subquery
                '))
                ->value('total_discount');


                $return_discount = DB::table('returnproducts')
                ->select(DB::raw("ROUND(SUM(COALESCE(discount_amount,0)) + SUM(total_amount * (COALESCE(total_discount_percent,0)/100)), 2) as return_discount"))
                ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->value('return_discount');

                $service_cost = DB::table('buyproducts')
                ->select(DB::raw("SUM(COALESCE(service_cost, 0) * remain_quantity) as service_cost"))
                ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->value('service_cost');

                $onlyservice_cost = DB::table('service')
                ->select(DB::raw("SUM(COALESCE(total_amount, 0)) as service_cost"))
                ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->value('service_cost');

            $start_date = "";
            $end_date = "";

            $total_credit_note = DB::table('credit_note')
            ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->selectRaw('SUM(credit_note_amount) as total_credit')
                    ->value('total_credit');

                    $purchaseservice = DB::table('stockdetails')
                    ->select(DB::raw("SUM(price) - ROUND(SUM(COALESCE(discount, 0)), 2) AS purchaseservice"))
                    ->whereDate('created_at', $today_date)
                    ->where('method','=',2)
                    ->where('branch', $branch)
                    ->value('purchaseservice');

                    $returnpurchaseservice = DB::table('returnpurchases')
                    ->select(DB::raw("SUM(amount) - ROUND(SUM(COALESCE(discount, 0)), 2) AS returnpurchaseservice"))
                    ->whereDate('created_at', $today_date)
                    ->where('method','=',2)
                    ->where('branch', $branch)
                    ->value('returnpurchaseservice');

        } else if (!empty($start_date) && !empty($end_date) && !empty($branch)) {

            /*---------------------latest Opening Stock & Closing Stock--------------------------------*/

            $latest_stock_details = DB::table('stockdetails')
                ->whereDate('created_at', '<', $start_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $stock_details_created_date = $latest_stock_details ? date('Y-m-d', strtotime($latest_stock_details)) : 0;

            $latest_stock_return_details = DB::table('returnpurchases')
                ->whereDate('created_at', '<', $start_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $stock_return_details_created_date = $latest_stock_return_details ? date('Y-m-d', strtotime($latest_stock_return_details)) : 0;

            /*query starts */

            $stock_purchase_yester_without_return = DB::table('stockdetails')
            ->select(DB::raw("SUM(price) as stock_purchase_amt"))
            ->whereDate('created_at', '<=', $stock_details_created_date)
                ->where('branch', $branch)
                ->first();

            $stock_purchase_return_yester = DB::table('returnpurchases')
                ->select(DB::raw("SUM(amount) as stock_purchase_return_amt"))
                ->whereDate('created_at', '<=', $stock_return_details_created_date)
                ->where('branch', $branch)
                ->first();

                // $purchase_return_discount = DB::table('returnpurchases')
                // ->select(DB::raw("SUM(COALESCE(discount,0)) as purchase_return_discount"))
                // ->whereDate('created_at', '<=', $stock_return_details_created_date)
                // ->where('branch', $branch)
                // ->value('purchase_return_discount');


            $stock_purchase_yester = $stock_purchase_yester_without_return->stock_purchase_amt - $stock_purchase_return_yester->stock_purchase_return_amt;

            $latest_buyproducts = DB::table('buyproducts')
                ->whereDate('created_at', '<', $start_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $buyproducts_created_date = $latest_buyproducts ? date('Y-m-d', strtotime($latest_buyproducts)) : 0;

            $latest_buyproducts_return = DB::table('returnproducts')
                ->whereDate('created_at', '<', $start_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $buyproducts_return_created_date = $latest_buyproducts_return ? date('Y-m-d', strtotime($latest_buyproducts_return)) : 0;

            /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

            $soldstock_yester_without_return = DB::table('buyproducts')
                ->select(DB::raw('SUM(buycost_rate_add) as total_soldstock'))
                ->whereDate('created_at', '<=', $buyproducts_created_date)
                ->where('branch', $branch)
                ->first();

            $soldstock__return_yester = DB::table('returnproducts')
                ->select(DB::raw('SUM(buycost_rate_addreturn) as total_returnstock'))
                ->whereDate('created_at', '<=', $buyproducts_return_created_date)
                ->where('branch', $branch)
                ->first();

            $soldstock_yester = $soldstock_yester_without_return->total_soldstock - $soldstock__return_yester->total_returnstock;

            /*-----------------------------------------------------------------------------------------------*/

            $remaining_stock_closing_yester = $stock_purchase_yester - $soldstock_yester;

            /*----------------------------TODAY OPENING STOCK AND CLOSING STOCK---------------------------------------------------*/

            $stock_purchase_today_without_return = DB::table('stockdetails')
            ->select(DB::raw("SUM(price) as stock_purchase_amt"))
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->first();

            $stock_purchase_return_today = DB::table('returnpurchases')
                ->select(DB::raw("SUM(amount) as stock_purchase_return_amt"))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->first();


                $purchase_return_discount = DB::table('returnpurchases')
                ->select(DB::raw("SUM(COALESCE(discount,0)) as purchase_return_discount"))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->value('purchase_return_discount');

            $stock_purchase = $stock_purchase_today_without_return->stock_purchase_amt - $stock_purchase_return_today->stock_purchase_return_amt;

            $total_stock_opening =  $remaining_stock_closing_yester + $stock_purchase;

            /* ---------------------------------------To show opening stock ---------------------------------- */

            // $stock_purchase_amt_yester_show = DB::table('stockdetails')
            //     ->select(DB::raw("SUM(price) as st_purchase_amt"))
            //     ->whereDate('created_at', '<=', $stock_details_created_date)
            //     ->where('branch', $branch)
            //     ->first();

            // $soldstock_amt_yester_show = DB::table('buyproducts')
            //     ->select(DB::raw('SUM(quantity * one_pro_buycost) as tot_soldstock'))
            //     ->whereDate('created_at', '<=', $buyproducts_created_date)
            //     ->where('branch', $branch)
            //     ->first();


            // $remaining_stock_closing_yester_show = $stock_purchase_amt_yester_show->st_purchase_amt - $soldstock_amt_yester_show->tot_soldstock;

            // $show_opening_stock = $remaining_stock_closing_yester_show; /* opening stock for show */

            $show_opening_stock = $remaining_stock_closing_yester;

            /* --------------------------------------------------------------------------------- */

            /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

            $soldstock_today_without_return = DB::table('buyproducts')
                ->select(DB::raw('SUM(buycost_rate_add) as total_soldstock'))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->first();

            $soldstock__return_today = DB::table('returnproducts')
                ->select(DB::raw('SUM(buycost_rate_addreturn) as total_returnstock'))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->first();

            $soldstock = $soldstock_today_without_return->total_soldstock - $soldstock__return_today->total_returnstock;

            /*-----------------------------------------------------------------------------------------------*/

            $remaining_stock_closing = $total_stock_opening - $soldstock;

            if ($start_date == $end_date) {
                if ($start_date == $frst_dt) {
                    $today_open_stock = 0;
                } else {
                    // $today_open_stock = $total_stock_opening;

                    $today_open_stock = $show_opening_stock;
                }
            } elseif ($start_date < $frst_dt && $end_date != $frst_dt) {
                $today_open_stock = 0;
            } else {
                if ($start_date == $frst_dt) {
                    $today_open_stock = 0;
                } else if ($end_date == $frst_dt) {
                    $today_open_stock = 0;
                } else {
                    // $today_open_stock = $total_stock_opening;
                    $today_open_stock = $show_opening_stock;
                }
            }

            /*-----------------------------------------------------------*/

            /* ---------------- new sales and sales return ---------------- */

            // $sold = DB::table('buyproducts')
            // ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            // ->where('branch', $branch)
            // ->selectRaw('SUM(COALESCE((mrp * quantity) + vat_amount)) as total_sold')
            // ->value('total_sold');



            $total_sold_vat_type_1 = DB::table('buyproducts')
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->where('vat_type', 1)
            ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
            ->value('total_sold');

        // Calculate total sold for vat_type 2
            $total_sold_vat_type_2 = DB::table('buyproducts')
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->where('vat_type', 2)
            ->selectRaw('SUM(COALESCE((mrp * quantity) + vat_amount)) as total_sold')
            ->value('total_sold');

        // Combine results if needed
            $sold = ($total_sold_vat_type_1 ?? 0) + ($total_sold_vat_type_2 ?? 0);



            $salesReturn = DB::table('returnproducts')
                ->whereBetween('returnproducts.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                // ->selectRaw('SUM(CASE WHEN totalamount_wo_discount IS NULL THEN total_amount ELSE totalamount_wo_discount END) as total_sold')
                ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
                ->value('total_sold');

            /*-----------------------------------------------------------*/

            /* ---------------- new purchase and purchase return ---------------- */

            // $purchaseamount = DB::table('stockdetails')
            //     ->whereBetween('stockdetails.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            //     ->where('branch', $branch)
            //     ->sum('price');

                $purchaseamounts = DB::table('stockdetails')
                ->whereBetween('stockdetails.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->select(DB::raw('SUM(stockdetails.price) as total_price'))
                ->first();

            // Access the result
            $purchaseamount = $purchaseamounts->total_price;

            // $purchaseReturn = DB::table('returnpurchases')
            //     ->whereBetween('returnpurchases.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            //     ->where('branch', $branch)
            //     ->sum('amount');

                    $purchaseReturns = DB::table('returnpurchases')
                    ->whereBetween('returnpurchases.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                    ->where('branch', $branch)
                    ->select(DB::raw('SUM(returnpurchases.amount)  as total_price'))
                    ->first();

                $purchaseReturn = $purchaseReturns->total_price;

            /*-----------------------------------------------------------*/

            /*-----------------------------------------------------------*/
            /*---------------------------Indirect Expense & income ---------------------*/

            $monthlyexpense = DB::table('accountexpenses')
                ->select(DB::raw("SUM(amount) as monthly_expense"))
                ->whereBetween('date', [$start_date, $end_date])
                ->where('branch', $branch)
                ->first();

            $salary_amount = DB::table('salarydatas')
                ->select(DB::raw("SUM(salary) as salary"))
                ->whereBetween('date', [$start_date, $end_date])
                ->where('branch_id', $branch)
                ->first();

            $indirect_expenses = ($monthlyexpense->monthly_expense) + ($salary_amount->salary);

            $indirect_income = DB::table('account_indirect_incomes')
                ->select(DB::raw("SUM(amount) as indirect_income"))
                ->whereBetween('date', [$start_date, $end_date])
                ->where('branch', $branch)
                ->first();

            $indirect_income = $indirect_income->indirect_income ?? 0;
            // ----------------
            $direct_expense = DB::table('accountexpenses')
            ->select(DB::raw('direct_expense,SUM(amount) as amount'))
            ->whereBetween('accountexpenses.date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->where('expense_type', 1)
            // ->where('user_id', $userid)
            ->groupBy('direct_expense')
            ->get();


            $indirect_expense = DB::table('accountexpenses')
            ->select(DB::raw('indirect_expense,SUM(amount) as amount'))
            ->whereBetween('accountexpenses.date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->where('expense_type',2)
            // ->where('user_id', $userid)
            ->groupBy('indirect_expense')
            ->get();

            $direct_income = DB::table('account_indirect_incomes')
            ->select(DB::raw('direct_income,SUM(amount) as amount'))
            ->whereBetween('account_indirect_incomes.date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->where('income_type',1)
            // ->where('user_id', $userid)
            ->groupBy('direct_income')
            ->get();

            $indirect_incomes = DB::table('account_indirect_incomes')
            ->select(DB::raw('indirect_income,SUM(amount) as amount'))
            ->whereBetween('account_indirect_incomes.date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->where('income_type',2)
            // ->where('user_id', $userid)
            ->groupBy('indirect_income')
            ->get();



            // Calculate total amounts
            $total_direct_expense = $direct_expense->sum('amount');
            $total_direct_income = $direct_income->sum('amount');
            $total_indirect_expense = $indirect_expense->sum('amount');
            $total_indirect_income = $indirect_incomes->sum('amount');
            /*-------------------------------------------------------------------------*/

            // $sales_discount = DB::table('buyproducts')
            //     ->select(DB::raw("SUM(discount_amount * remain_quantity) as discount_amount"))
            //     ->whereBetween(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), [$start_date, $end_date])
            //     ->where('branch', $branch)
            //     ->first();

            // $return_discount = DB::table('returnproducts')
            //     ->select(DB::raw("SUM(discount_amount) as discount_amount"))
            //     ->whereBetween(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), [$start_date, $end_date])
            //     ->where('branch', $branch)
            //     ->first();

            // $discount = $sales_discount->discount_amount - $return_discount->discount_amount;

            // $discount = $sales_discount->discount_amount;


            $discount = DB::table('buyproducts')
                ->select(DB::raw('SUM(total_discount) as total_discount'))
                ->from(DB::raw('(SELECT transaction_id, COALESCE(SUM(buyproducts.discount_amount * buyproducts.quantity), 0) + COALESCE(SUM(total_discount_amount), 0) AS total_discount FROM buyproducts WHERE branch = ? AND DATE(created_at) BETWEEN ? AND ? GROUP BY transaction_id) as subquery'))
                ->addBinding([$branch, $start_date, $end_date], 'select')
                ->value('total_discount');


            $discount = DB::table('buyproducts')
                ->select(DB::raw('SUM(subquery.total_discount) as total_discount'))
                ->from(DB::raw('
                    (
                        SELECT
                            transaction_id,
                            COALESCE(SUM(discount_amount * quantity), 0) AS product_discounts,
                            COALESCE(MAX(total_discount_amount), 0) AS transaction_discount,
                            COALESCE(SUM(discount_amount * quantity), 0) + COALESCE(MAX(total_discount_amount), 0) AS total_discount
                        FROM
                            buyproducts
                        WHERE
                            branch = ?
                            AND DATE(created_at) BETWEEN ? AND ?
                        GROUP BY
                            transaction_id
                    ) as subquery
                '))
                ->addBinding([$branch, $start_date, $end_date], 'select')
                ->value('total_discount');

                $return_discount = DB::table('returnproducts')
                ->select(DB::raw("ROUND(SUM(COALESCE(discount_amount,0)) + SUM(total_amount * (COALESCE(total_discount_percent,0)/100)), 2) as return_discount"))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->value('return_discount');

                $discountpurchase = DB::table('stockdetails')
                ->select(DB::raw("ROUND(SUM(COALESCE(discount, 0)), 2) as discountpurchase"))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->value('discountpurchase');

                $purchaseservice = DB::table('stockdetails')
                ->select(DB::raw("SUM(price) - ROUND(SUM(COALESCE(discount, 0)), 2) AS purchaseservice"))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('method','=',2)
                ->where('branch', $branch)
                ->value('purchaseservice');

                $returnpurchaseservice = DB::table('returnpurchases')
                ->select(DB::raw("SUM(amount) - ROUND(SUM(COALESCE(discount, 0)), 2) AS returnpurchaseservice"))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('method','=',2)
                ->where('branch', $branch)
                ->value('returnpurchaseservice');

            $total_credit_note = DB::table('credit_note')
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->selectRaw('SUM(credit_note_amount) as total_credit')
                ->value('total_credit');

            $start_date = $start_date;
            $end_date = $end_date;

            $service_cost = DB::table('buyproducts')
            ->select(DB::raw("SUM(COALESCE(service_cost, 0) * remain_quantity) as service_cost"))
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->value('service_cost');

            $onlyservice_cost = DB::table('service')
            ->select(DB::raw("SUM(COALESCE(total_amount, 0)) as service_cost"))
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->value('service_cost');
        }

        return [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'soldstock_value' => $sold,
            'opening_stock' => $today_open_stock,
            'closing_stock' => $remaining_stock_closing,
            'purchase_amount' => $purchaseamount,
            'indirect_expenses' => $indirect_expenses,
            'indirect_income' => $indirect_income,
            'locations' => $location,
            'purchaseReturn' => $purchaseReturn,
            'salesReturn' => $salesReturn,
            'discount' => $discount,
            'return_discount'=>$return_discount,
            'discountpurchase' => $discountpurchase,
            'purchaseservice' => $purchaseservice,
            'returnpurchaseservice' => $returnpurchaseservice,

            'purchase_return_discount'=>$purchase_return_discount,
            'service_cost'=>$service_cost,
            'onlyservice_cost'=>$onlyservice_cost,


            'direct_expense' => $direct_expense,
            'indirect_expense' => $indirect_expense,
            'direct_income' => $direct_income,
            'indirect_incomes' => $indirect_incomes,
            'total_direct_expense' => $total_direct_expense,
            'total_direct_income' => $total_direct_income,
            'total_indirect_expense' => $total_indirect_expense,
            'total_indirect_income' => $total_indirect_income,
            'total_credit_note'=>$total_credit_note,


        ];
    }

    public function normalPandLbranchwise()
    {

        $userid = Session('softwareuser');
        $first_location = DB::table('softwareusers')
            ->where('id', $userid)
            ->pluck('location')
            ->first();


        // $first_location = DB::table('branches')->pluck('id')->first();


        $first_date = Stockdetail::where('branch', $first_location)
            ->orderBy('created_at')
            ->pluck('created_at')
            ->first();

        $frst_dt = date('Y-m-d', strtotime($first_date));

        $date = Carbon::today()->format('Y-m-d');

        /*---------------------latest Opening Stock & Closing Stock--------------------------------*/

        $latest_stock_details = DB::table('stockdetails')
            ->whereDate('created_at', '<', $date)
            ->where('branch', $first_location)
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->first();


        if ($latest_stock_details) {
            $stock_details_created_date = date('Y-m-d', strtotime($latest_stock_details));
        } else {
            $stock_details_created_date = 0;
        }

        $latest_stockerturn_details = DB::table('returnpurchases')
            ->whereDate('created_at', '<', $date)
            ->where('branch', $first_location)
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->first();

        if ($latest_stockerturn_details) {
            $stock_return_details_created_date = date('Y-m-d', strtotime($latest_stockerturn_details));
        } else {
            $stock_return_details_created_date = 0;
        }

        $stock_purchase_yester_without_return = DB::table('stockdetails')
        ->select(DB::raw("SUM(price) as stock_purchase_amt"))
        ->whereDate('created_at', '<=', $stock_details_created_date)
            ->where('branch', $first_location)
            ->first();

        $stock_purchase_return_yester = DB::table('returnpurchases')
            ->select(DB::raw("SUM(amount) as stock_purchase_return_amt"))
            ->whereDate('created_at', '<=', $stock_return_details_created_date)
            ->where('branch', $first_location)
            ->first();

            // $purchase_return_discount = DB::table('returnpurchases')
            // ->select(DB::raw("SUM(COALESCE(discount,0)) as purchase_return_discount"))
            // ->whereDate('created_at', '<=', $stock_return_details_created_date)
            // ->where('branch', $first_location)
            // ->value('purchase_return_discount');

        $stock_purchase_yester = $stock_purchase_yester_without_return->stock_purchase_amt - $stock_purchase_return_yester->stock_purchase_return_amt;

        // dd($stock_purchase_return_yester);

        $latest_buyproducts = DB::table('buyproducts')
            ->whereDate('created_at', '<', $date)
            ->where('branch', $first_location)
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->first();

        if ($latest_buyproducts) {
            $buyproducts_created_date = date('Y-m-d', strtotime($latest_buyproducts));
        } else {
            $buyproducts_created_date = 0;
        }

        $latest_buyproducts_return = DB::table('returnproducts')
            ->whereDate('created_at', '<', $date)
            ->where('branch', $first_location)
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->first();

        if ($latest_buyproducts_return) {
            $buyproducts_return_created_date = date('Y-m-d', strtotime($latest_buyproducts_return));
        } else {
            $buyproducts_return_created_date = 0;
        }

        /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

        $soldstock_yester_without_return = DB::table('buyproducts')
            ->select(DB::raw('SUM(buycost_rate_add) as total_soldstock'))
            ->whereDate('created_at', '<=', $buyproducts_created_date)
            ->where('branch', $first_location)
            ->first();

        $soldstock__return_yester = DB::table('returnproducts')
            ->select(DB::raw('SUM(buycost_rate_addreturn) as total_returnstock'))
            ->whereDate('created_at', '<=', $buyproducts_return_created_date)
            ->where('branch', $first_location)
            ->first();

        $soldstock_yester = $soldstock_yester_without_return->total_soldstock - $soldstock__return_yester->total_returnstock;

        /*-----------------------------------------------------------------------------------------------*/

        $remaining_stock_closing_yester = $stock_purchase_yester - $soldstock_yester;

        // dd($remaining_stock_closing_yester);
        /*-------------------------------------------------------------------------------*/
        /*------------------------------TODAY open and close-----------------------------*/

        $stock_purchase_without_return = DB::table('stockdetails')
        ->select(DB::raw("SUM(price) as stock_purchase_amt"))
        ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->first();

        // dd($stock_purchase_without_return);

        $stock_purchase_return = DB::table('returnpurchases')
            ->select(DB::raw("SUM(amount) as stock_purchase_return_amt"))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->first();

            $purchase_return_discount = DB::table('returnpurchases')
            ->select(DB::raw("SUM(COALESCE(discount,0)) as purchase_return_discount"))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->value('purchase_return_discount');



        // dd($stock_purchase_return);

        $stock_purchase = $stock_purchase_without_return->stock_purchase_amt - $stock_purchase_return->stock_purchase_return_amt;

        // dd($stock_purchase);

        $total_stock_opening =  $remaining_stock_closing_yester + $stock_purchase; /* opening stockn for calculation correct */

        // dd($stock_purchase);

        /* ---------------------------------------To show opening stock ---------------------------------- */

        // $stock_purchase_amt_yester_show = DB::table('stockdetails')
        //     ->select(DB::raw("SUM(price) as st_purchase_amt"))
        //     ->whereDate('created_at', '<=', $stock_details_created_date)
        //     ->where('branch', $first_location)
        //     ->first();

        // // dd($stock_purchase_amt_yester_show);

        // $soldstock_amt_yester_show = DB::table('buyproducts')
        //     ->select(DB::raw('SUM(quantity * one_pro_buycost) as tot_soldstock'))
        //     ->whereDate('created_at', '<=', $buyproducts_created_date)
        //     ->where('branch', $first_location)
        //     ->first();

        // dd($soldstock_amt_yester_show);

        // $remaining_stock_closing_yester_show = $stock_purchase_amt_yester_show->st_purchase_amt - $soldstock_amt_yester_show->tot_soldstock;

        $show_opening_stock = $remaining_stock_closing_yester; /* opening stock for show */

        /* --------------------------------------------------------------------------------- */

        /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

        $soldstock_today_without_return = DB::table('buyproducts')
            ->select(DB::raw('SUM(buycost_rate_add) as total_soldstock'))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->first();

        $soldstock__return_today = DB::table('returnproducts')
            ->select(DB::raw('SUM(buycost_rate_addreturn) as total_returnstock'))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->first();

        $soldstock = $soldstock_today_without_return->total_soldstock - $soldstock__return_today->total_returnstock;

        /*-----------------------------------------------------------------------------------------------*/

        $remaining_stock_closing = $total_stock_opening - $soldstock;

        // dd($total_stock_opening);

        /*----------------------------------------*/

        if ($date == $frst_dt) {
            $today_open_stock = 0;
        } else {

            // $today_open_stock = $total_stock_opening;

            $today_open_stock = $show_opening_stock;
        }

        /*---------------------------Sold Stock ---------------------*/

        // $sold = DB::table('buyproducts')
        //     ->whereDate('buyproducts.created_at', $date)
        //     ->where('buyproducts.branch', $first_location)
        //     ->selectRaw('SUM(COALESCE((mrp * quantity) + vat_amount)) as total_sold')
        //     ->value('total_sold');

        $total_sold_vat_type_1 = DB::table('buyproducts')
    ->whereDate('created_at', $date)
    ->where('branch', $first_location)
    ->where('vat_type', 1)
    ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
    ->value('total_sold');

// Calculate total sold for vat_type 2
$total_sold_vat_type_2 = DB::table('buyproducts')
    ->whereDate('created_at', $date)
    ->where('branch', $first_location)
    ->where('vat_type', 2)
    ->selectRaw('SUM(COALESCE((mrp * quantity) + vat_amount)) as total_sold')
    ->value('total_sold');

// Combine results if needed
$sold = ($total_sold_vat_type_1 ?? 0) + ($total_sold_vat_type_2 ?? 0);

$total_credit_note = DB::table('credit_note')
    ->whereDate('created_at', $date)
    ->where('branch', $first_location)
    ->selectRaw('SUM(credit_note_amount) as total_credit')
    ->value('total_credit');

    // dd($total_credit_note);



        // Retrieve vat_type for a specific row or condition
                // $vat_type = DB::table('buyproducts')
                // ->whereDate('created_at', $date)
                // ->where('branch', $first_location)
                // ->value('vat_type');

                // if ($vat_type == 1) {
                // $sold = DB::table('buyproducts')
                //     ->whereDate('created_at', $date)
                //     ->where('branch', $first_location)
                //     ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
                //     ->value('total_sold');
                // } elseif ($vat_type == 2) {
                // $sold = DB::table('buyproducts')
                //     ->whereDate('created_at', $date)
                //     ->where('branch', $branch)
                //     ->selectRaw('SUM(COALESCE((mrp * quantity) + vat_amount)) as total_sold')
                //     ->value('total_sold');
                // }




            // $sold = DB::table('buyproducts')
            // ->whereDate('buyproducts.created_at', $date)
            // ->where('buyproducts.branch', $first_location)
            // // ->selectRaw('SUM(CASE WHEN totalamount_wo_discount IS NULL THEN total_amount ELSE totalamount_wo_discount END) as total_sold')
            // ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
            // ->value('total_sold');


        $salesReturn = DB::table('returnproducts')
            ->whereDate('returnproducts.created_at', $date)
            ->where('branch', $first_location)
            // ->selectRaw('SUM(CASE WHEN totalamount_wo_discount IS NULL THEN total_amount ELSE totalamount_wo_discount END) as total_sold')
            ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
            ->value('total_sold_return');
        /*-----------------------------------------------------------*/

        /* ---------------- new purchase and purchase return ---------------- */

        // $purchaseamount = DB::table('stockdetails')
        //     ->whereDate('stockdetails.created_at', $date)
        //     ->where('branch', $first_location)
        //     ->sum('price');

            $purchaseamounts = DB::table('stockdetails')
            ->whereDate('stockdetails.created_at', $date)
            ->where('branch', $first_location)
            ->select(DB::raw('SUM(stockdetails.price) as total_price'))
            ->first();

        // Access the result
        $purchaseamount = $purchaseamounts->total_price;
        // dd($purchaseamount);

        // $purchaseReturn = DB::table('returnpurchases')
        //     ->whereDate('returnpurchases.created_at', $date)
        //     ->where('branch', $first_location)
        //     ->sum('amount');

                $purchaseReturns = DB::table('returnpurchases')
                ->whereDate('returnpurchases.created_at', $date)
                ->where('branch', $first_location)
                ->select(DB::raw('SUM(returnpurchases.amount) as total_price'))
                ->first();

            $purchaseReturn = $purchaseReturns->total_price;

        // dd($purchaseReturn);
        /*-----------------------------------------------------------*/
        /*---------------------------Indirect Expense & income ---------------------*/
        $monthlyexpense = DB::table('accountexpenses')
            ->select(DB::raw("SUM(amount) as monthly_expense"))
            ->whereDate('date', $date)
            ->where('branch', $first_location)
            ->first();

        // dd($monthlyexpense);

        $salary_amount = DB::table('salarydatas')
            ->select(DB::raw("SUM(salary) as salary"))
            ->whereDate('date', $date)
            ->where('branch_id', $first_location)
            ->first();

        // $indirect_expenses = ($monthlyexpense->monthly_expense) + ($salary_amount->salary);

        // $indirect_income = DB::table('account_indirect_incomes')
        //     ->select(DB::raw("SUM(amount) as indirect_income"))
        //     ->whereDate('date', $date)
        //     ->where('branch', $first_location)
        //     ->first();

        // $indirect_income = $indirect_income->indirect_income ?? 0;

        /*-------------------------------------------------------------------------*/

        $sales_discount = DB::table('buyproducts')
            ->select(DB::raw("SUM(discount_amount * remain_quantity) as discount_amount"))
        ->whereDate('created_at', $date)
        ->where('branch', $first_location)
            ->first();

//         $return_discount = DB::table('returnproducts')
//             ->select(DB::raw("SUM(discount_amount) as discount_amount"))
//             ->whereDate('created_at', $date)
//             ->where('branch', $first_location)
//             ->first();
// dd($return_discount);
        $discount = DB::table('buyproducts')
        ->select(DB::raw('SUM(total_discount) as total_discount'))
        ->from(DB::raw('(SELECT transaction_id, COALESCE(SUM(buyproducts.discount_amount * buyproducts.quantity), 0) + COALESCE(SUM(total_discount_amount), 0) AS total_discount FROM buyproducts WHERE DATE(created_at) = "' . $date . '" AND branch = ' . $first_location . ' GROUP BY transaction_id) as subquery'))
        ->value('total_discount');


        $discount = DB::table('buyproducts')
        ->select(DB::raw('SUM(subquery.total_discount) as total_discount'))
            ->from(DB::raw('
                (
                    SELECT
                    transaction_id,
                    COALESCE(SUM(discount_amount * quantity), 0) AS product_discounts,
                    COALESCE(MAX(total_discount_amount), 0) AS transaction_discount,
                    COALESCE(SUM(discount_amount * quantity), 0) + COALESCE(MAX(total_discount_amount), 0) AS total_discount
                    FROM
                        buyproducts
                    WHERE
                        DATE(created_at) = "' . $date . '"
                        AND branch = ' . $first_location . '
                        GROUP BY
                        transaction_id
                        ) as subquery
                        '))
                        ->value('total_discount');

            $return_discount = DB::table('returnproducts')
                ->select(DB::raw("ROUND(SUM(COALESCE(discount_amount,0)) + SUM(total_amount * (COALESCE(total_discount_percent,0)/100)), 2) as return_discount"))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->value('return_discount');


                                        // dd($return_discount);


            $discountpurchase = DB::table('stockdetails')
            ->select(DB::raw("ROUND(SUM(COALESCE(discount, 0)), 2) as discountpurchase"))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->value('discountpurchase');

            $purchaseservice = DB::table('stockdetails')
            ->select(DB::raw("SUM(price) - ROUND(SUM(COALESCE(discount, 0)), 2) AS purchaseservice"))
            ->whereDate('created_at', $date)
            ->where('method','=',2)
            ->where('branch', $first_location)
            ->value('purchaseservice');

            $returnpurchaseservice = DB::table('returnpurchases')
            ->select(DB::raw("SUM(amount) - ROUND(SUM(COALESCE(discount, 0)), 2) AS returnpurchaseservice"))
            ->whereDate('created_at', $date)
            ->where('method','=',2)
            ->where('branch', $first_location)
            ->value('returnpurchaseservice');


            $service_cost = DB::table('buyproducts')
            ->select(DB::raw("SUM(COALESCE(service_cost, 0) * remain_quantity) as service_cost"))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->value('service_cost');
            $onlyservice_cost = DB::table('service')
            ->select(DB::raw("SUM(COALESCE(total_amount, 0)) as service_cost"))
            ->whereDate('created_at', $date)
            ->where('branch', $first_location)
            ->value('service_cost');

            $direct_expense = DB::table('accountexpenses')
            ->select(DB::raw('direct_expense,SUM(amount) as amount'))
            ->whereDate('date', $date)
            ->where('branch', $first_location)
            ->where('expense_type', 1)
            ->groupBy('direct_expense')
            ->get();


            $indirect_expense = DB::table('accountexpenses')
            ->select(DB::raw('indirect_expense,SUM(amount) as amount'))
            ->whereDate('date', $date)
            ->where('branch', $first_location)
            ->where('expense_type',2)
            ->groupBy('indirect_expense')
            ->get();

            $direct_income = DB::table('account_indirect_incomes')
            ->select(DB::raw('direct_income,SUM(amount) as amount'))
            ->whereDate('date', $date)
            ->where('branch', $first_location)
            ->where('income_type',1)
            ->groupBy('direct_income')
            ->get();


            $indirect_incomes = DB::table('account_indirect_incomes')
            ->select(DB::raw('indirect_income,SUM(amount) as amount'))
            ->whereDate('date', $date)
            ->where('branch', $first_location)
            ->where('income_type',2)
            ->groupBy('indirect_income')
            ->get();

            // dd($direct_expense);


            // Calculate total amounts
            $total_direct_expense = $direct_expense->sum('amount');
            $total_direct_income = $direct_income->sum('amount');
            $total_indirect_expense = $indirect_expense->sum('amount');
            $total_indirect_income = $indirect_incomes->sum('amount');

        return [
            'soldstock_value' => $sold,
            'opening_stock' => $today_open_stock,
            'closing_stock' => $remaining_stock_closing,
            'purchase_amount' => $purchaseamount,
            // 'indirect_expenses' => $indirect_expenses,
            // 'indirect_income' => $indirect_income,
            'locations' => $first_location,
            'purchaseReturn' => $purchaseReturn,
            'salesReturn' => $salesReturn,
            'service_cost'=>$service_cost,
            'onlyservice_cost'=>$onlyservice_cost,

            'discount' => $discount,
            'return_discount'=>$return_discount,
            'discountpurchase' => $discountpurchase,
            'purchaseservice' => $purchaseservice,
            'returnpurchaseservice' => $returnpurchaseservice,

            'purchase_return_discount'=>$purchase_return_discount,
            'direct_expense' => $direct_expense,
            'indirect_expense' => $indirect_expense,
            'direct_income' => $direct_income,
            'indirect_incomes' => $indirect_incomes,
            'total_direct_expense' => $total_direct_expense,
            'total_direct_income' => $total_direct_income,
            'total_indirect_expense' => $total_indirect_expense,
            'total_indirect_income' => $total_indirect_income,
            'total_credit_note'=>$total_credit_note,
        ];
    }

    public function filterPandLuser($branch, $start_date, $end_date)
    {

        $location = DB::table('branches')
            ->get();

        $first_date = Stockdetail::where('branch', $branch)
            ->orderBy('created_at')
            ->pluck('created_at')
            ->first();

            $userid = Session('adminuser');


        $frst_dt = date('Y-m-d', strtotime($first_date));

        /*------------------------------------ WITHOUT DATE FILTER ------------------------------------------------------*/

        if (empty($start_date) && empty($end_date) && !empty($branch)) {

            $today_date = Carbon::today()->format('Y-m-d');

            /*---------------------latest Opening Stock & Closing Stock--------------------------------*/

            $latest_stock_details = DB::table('stockdetails')
                ->whereDate('created_at', '<', $today_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $stock_details_created_date = $latest_stock_details ? date('Y-m-d', strtotime($latest_stock_details)) : 0;


            $latest_stock_return_details = DB::table('returnpurchases')
                ->whereDate('created_at', '<', $today_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $stock_return_details_created_date = $latest_stock_return_details ? date('Y-m-d', strtotime($latest_stock_return_details)) : 0;

            /*query starts */

            $stock_purchase_yester_without_return = DB::table('stockdetails')
            ->select(DB::raw("SUM(price) as stock_purchase_amt"))
            ->whereDate('created_at', '<=', $stock_details_created_date)
                ->where('branch', $branch)
                ->first();

            $stock_purchase_yester_return = DB::table('returnpurchases')
                ->select(DB::raw("SUM(amount)  as stock_purchase_return_amt"))
                ->whereDate('created_at', '<=', $stock_return_details_created_date)
                ->where('branch', $branch)
                ->first();

                // $purchase_return_discount = DB::table('returnpurchases')
                // ->select(DB::raw("SUM(COALESCE(discount,0)) as purchase_return_discount"))
                // ->whereDate('created_at', '<=', $stock_return_details_created_date)
                // ->where('branch', $branch)
                // ->value('purchase_return_discount');


            $stock_purchase_yester = $stock_purchase_yester_without_return->stock_purchase_amt - $stock_purchase_yester_return->stock_purchase_return_amt;

            // dd($stock_purchase_yester);

            $latest_buyproducts = DB::table('buyproducts')
                ->whereDate('created_at', '<', $today_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $buyproducts_created_date = $latest_buyproducts ? date('Y-m-d', strtotime($latest_buyproducts)) : 0;

            $latest_buyproducts_return = DB::table('returnproducts')
                ->whereDate('created_at', '<', $today_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $buyproducts_return_created_date = $latest_buyproducts_return ? date('Y-m-d', strtotime($latest_buyproducts_return)) : 0;

            /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

            $soldstock_yester_without_return = DB::table('buyproducts')
                ->select(DB::raw('SUM(buycost_rate_add) as total_soldstock'))
                ->whereDate('created_at', '<=', $buyproducts_created_date)
                ->where('branch', $branch)
                ->first();

            $soldstock__return_yester = DB::table('returnproducts')
                ->select(DB::raw('SUM(buycost_rate_addreturn) as total_returnstock'))
                ->whereDate('created_at', '<=', $buyproducts_return_created_date)
                ->where('branch', $branch)
                ->first();

            $soldstock = $soldstock_yester_without_return->total_soldstock - $soldstock__return_yester->total_returnstock;

            /*-----------------------------------------------------------------------------------------------*/

            $remaining_stock_closing_yester = $stock_purchase_yester - $soldstock;

            /*-------------------------------------------------------------------------------*/
            /*------------------------------TODAY open and close-----------------------------*/

            $stock_purchase_today_without_return = DB::table('stockdetails')
            ->select(DB::raw("SUM(price) as stock_purchase_amt"))
            ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->first();

            $stock_purchase_return_today = DB::table('returnpurchases')
                ->select(DB::raw("SUM(amount) as stock_purchase_return_amt"))
                ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->first();

                $purchase_return_discount = DB::table('returnpurchases')
                ->select(DB::raw("SUM(COALESCE(discount,0)) as purchase_return_discount"))
                ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->value('purchase_return_discount');

            $stock_purchase = $stock_purchase_today_without_return->stock_purchase_amt - $stock_purchase_return_today->stock_purchase_return_amt;

            $total_stock_opening =  $remaining_stock_closing_yester + $stock_purchase;  /* opening stockn for calculation correct */


            /* ---------------------------------------To show opening stock ---------------------------------- */

            // $stock_purchase_amt_yester_show = DB::table('stockdetails')
            //     ->select(DB::raw("SUM(price) as st_purchase_amt"))
            //     ->whereDate('created_at', '<=', $stock_details_created_date)
            //     ->where('branch', $branch)
            //     ->first();

            // $soldstock_amt_yester_show = DB::table('buyproducts')
            //     ->select(DB::raw('SUM(quantity * one_pro_buycost) as tot_soldstock'))
            //     ->whereDate('created_at', '<=', $buyproducts_created_date)
            //     ->where('branch', $branch)
            //     ->first();


            // $remaining_stock_closing_yester_show = $stock_purchase_amt_yester_show->st_purchase_amt - $soldstock_amt_yester_show->tot_soldstock;

            // $show_opening_stock = $remaining_stock_closing_yester_show; /* opening stock for show */

            $show_opening_stock = $remaining_stock_closing_yester;

            /* --------------------------------------------------------------------------------- */

            /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

            $soldstock_today_without_return = DB::table('buyproducts')
                ->select(DB::raw('SUM(buycost_rate_add) as total_soldstock'))
                ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->first();

            $soldstock__return_today = DB::table('returnproducts')
                ->select(DB::raw('SUM(buycost_rate_addreturn) as total_returnstock'))
                ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->first();

            $soldstock = $soldstock_today_without_return->total_soldstock - $soldstock__return_today->total_returnstock;

            /*-----------------------------------------------------------------------------------------------*/

            $remaining_stock_closing = $total_stock_opening - $soldstock;

            /*----------------------------------------*/

            $today_open_stock = ($today_date == $frst_dt) ? 0 : $show_opening_stock;


            /* ---------------- new sales and sales return ---------------- */

            $sold = DB::table('buyproducts')
                ->whereDate('buyproducts.created_at', $today_date)
                ->where('branch', $branch)
                // ->selectRaw('SUM(CASE WHEN totalamount_wo_discount IS NULL THEN total_amount ELSE totalamount_wo_discount END) as total_sold')
                ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
                ->value('total_sold');

            $salesReturn = DB::table('returnproducts')
                ->whereDate('returnproducts.created_at', $today_date)
                ->where('branch', $branch)
                // ->selectRaw('SUM(CASE WHEN totalamount_wo_discount IS NULL THEN total_amount ELSE totalamount_wo_discount END) as total_sold')
                ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
                ->value('total_sold');

            /*-----------------------------------------------------------*/

            /* ---------------- new purchase and purchase return ---------------- */

            // $purchaseamount = DB::table('stockdetails')
            //     ->whereDate('stockdetails.created_at', $today_date)
            //     ->where('branch', $branch)
            //     ->sum('price');

                $purchaseamounts = DB::table('stockdetails')
                ->whereDate('stockdetails.created_at', $today_date)
                ->where('branch', $branch)
                ->select(DB::raw('SUM(stockdetails.price) as total_price'))
                ->first();

            // Access the result
            $purchaseamount = $purchaseamounts->total_price;

            // $purchaseReturn = DB::table('returnpurchases')
            //     ->whereDate('returnpurchases.created_at', $today_date)
            //     ->where('branch', $branch)
            //     ->sum('amount');


                    $purchaseReturns = DB::table('returnpurchases')
                    ->whereDate('returnpurchases.created_at', $today_date)
                    ->where('branch', $branch)
                    ->select(DB::raw('SUM(returnpurchases.amount) as total_price'))
                    ->first();

                $purchaseReturn = $purchaseReturns->total_price;
            /*-----------------------------------------------------------*/

            /*---------------------------Indirect Expense & income ---------------------*/

            $monthlyexpense = DB::table('accountexpenses')
                ->select(DB::raw("SUM(amount) as monthly_expense"))
                ->whereDate('date', $today_date)
                ->where('branch', $branch)
                ->first();

            $salary_amount = DB::table('salarydatas')
                ->select(DB::raw("SUM(salary) as salary"))
                ->whereDate('date', $today_date)
                ->where('branch_id', $branch)
                ->first();

            $indirect_expenses = ($monthlyexpense->monthly_expense) + ($salary_amount->salary);

            $indirect_income = DB::table('account_indirect_incomes')
                ->select(DB::raw("SUM(amount) as indirect_income"))
                ->whereDate('date', $today_date)
                ->where('branch', $branch)
                ->first();

            $indirect_income = $indirect_income->indirect_income ?? 0;

// -------------

            $direct_expense = DB::table('accountexpenses')
            ->select(DB::raw('direct_expense,SUM(amount) as amount'))
            ->whereDate('date', $today_date)
            ->where('branch', $branch)
            ->where('expense_type', 1)
            // ->where('user_id', $userid)
            ->groupBy('direct_expense')
            ->get();

            $indirect_expense = DB::table('accountexpenses')
            ->select(DB::raw('indirect_expense,SUM(amount) as amount'))
            ->whereDate('date', $today_date)
            ->where('branch', $branch)
            ->where('expense_type',2)
            // ->where('user_id', $userid)
            ->groupBy('indirect_expense')
            ->get();

            $direct_income = DB::table('account_indirect_incomes')
            ->select(DB::raw('direct_income,SUM(amount) as amount'))
            ->whereDate('date', $today_date)
            ->where('branch', $branch)
            ->where('income_type',1)
            // ->where('user_id', $userid)
            ->groupBy('direct_income')
            ->get();

            $indirect_incomes = DB::table('account_indirect_incomes')
            ->select(DB::raw('indirect_income,SUM(amount) as amount'))
            ->whereDate('date', $today_date)
            ->where('branch', $branch)
            ->where('income_type',2)
            // ->where('user_id', $userid)
            ->groupBy('indirect_income')
            ->get();



            // Calculate total amounts
            $total_direct_expense = $direct_expense->sum('amount');
            $total_direct_income = $direct_income->sum('amount');
            $total_indirect_expense = $indirect_expense->sum('amount');
            $total_indirect_income = $indirect_incomes->sum('amount');

            /*-------------------------------------------------------------------------*/

            // $sales_discount = DB::table('buyproducts')
            //     ->select(DB::raw("SUM(discount_amount) as discount_amount"))
            //     ->whereDate('created_at', $today_date)
            //     ->where('branch', $branch)
            //     ->first();

            // $return_discount = DB::table('returnproducts')
            //     ->select(DB::raw("SUM(discount_amount) as discount_amount"))
            //     ->whereDate('created_at', $today_date)
            //     ->where('branch', $branch)
            //     ->first();

            // $discount = $sales_discount->discount_amount - $return_discount->discount_amount;


            // $discount = DB::table('buyproducts')
            //     ->select(DB::raw('SUM(total_discount) as total_discount'))
            //     ->from(DB::raw('(SELECT transaction_id, COALESCE(SUM(buyproducts.discount_amount * buyproducts.remain_quantity), 0) + COALESCE(SUM(total_discount_amount), 0) AS total_discount FROM buyproducts WHERE DATE(created_at) = "' . $today_date . '" AND branch = ' . $branch . ' GROUP BY transaction_id) as subquery'))
            //     ->value('total_discount');

            $discount = DB::table('buyproducts')
                ->select(DB::raw('SUM(subquery.total_discount) as total_discount'))
                ->from(DB::raw('
                    (
                        SELECT
                            transaction_id,
                            COALESCE(SUM(discount_amount * quantity), 0) AS product_discounts,
                            COALESCE(MAX(total_discount_amount), 0) AS transaction_discount,
                            COALESCE(SUM(discount_amount * quantity), 0) + COALESCE(MAX(total_discount_amount), 0) AS total_discount
                        FROM
                            buyproducts
                        WHERE
                            DATE(created_at) = "' . $today_date . '"
                            AND branch = ' . $branch . '
                        GROUP BY
                            transaction_id
                    ) as subquery
                '))
                ->value('total_discount');

                $return_discount = DB::table('returnproducts')
                ->select(DB::raw("ROUND(SUM(COALESCE(discount_amount,0)) + SUM(total_amount * (COALESCE(total_discount_percent,0)/100)), 2) as return_discount"))
                ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->value('return_discount');

            $start_date = "";
            $end_date = "";

            $discountpurchase = DB::table('stockdetails')
            ->select(DB::raw("ROUND(SUM(COALESCE(discount, 0)), 2) as discountpurchase"))
            ->whereDate('created_at', $today_date)
            ->where('branch', $branch)
            ->value('discountpurchase');

            $purchaseservice = DB::table('stockdetails')
            ->select(DB::raw("SUM(price) - ROUND(SUM(COALESCE(discount, 0)), 2) AS purchaseservice"))
            ->whereDate('created_at', $today_date)
            ->where('method','=',2)
            ->where('branch', $branch)
            ->value('purchaseservice');

            $returnpurchaseservice = DB::table('returnpurchases')
            ->select(DB::raw("SUM(amount) - ROUND(SUM(COALESCE(discount, 0)), 2) AS returnpurchaseservice"))
            ->whereDate('created_at', $today_date)
            ->where('method','=',2)
            ->where('branch', $branch)
            ->value('returnpurchaseservice');

            $total_credit_note = DB::table('credit_note')
            ->whereDate('created_at', $today_date)
                ->where('branch', $branch)
                ->selectRaw('SUM(credit_note_amount) as total_credit')
                    ->value('total_credit');

                    $service_cost = DB::table('buyproducts')
                    ->select(DB::raw("SUM(COALESCE(service_cost, 0) * remain_quantity) as service_cost"))
                    ->whereDate('created_at', $today_date)
                    ->where('branch', $branch)
                    ->value('service_cost');

                    $onlyservice_cost = DB::table('service')
                    ->select(DB::raw("SUM(COALESCE(total_amount, 0)) as service_cost"))
                    ->whereDate('created_at', $today_date)
                    ->where('branch', $branch)
                    ->value('service_cost');

        } else if (!empty($start_date) && !empty($end_date) && !empty($branch)) {

            /*---------------------latest Opening Stock & Closing Stock--------------------------------*/

            $latest_stock_details = DB::table('stockdetails')
                ->whereDate('created_at', '<', $start_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $stock_details_created_date = $latest_stock_details ? date('Y-m-d', strtotime($latest_stock_details)) : 0;

            $latest_stock_return_details = DB::table('returnpurchases')
                ->whereDate('created_at', '<', $start_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $stock_return_details_created_date = $latest_stock_return_details ? date('Y-m-d', strtotime($latest_stock_return_details)) : 0;

            /*query starts */

            $stock_purchase_yester_without_return = DB::table('stockdetails')
            ->select(DB::raw("SUM(price) as stock_purchase_amt"))
            ->whereDate('created_at', '<=', $stock_details_created_date)
                ->where('branch', $branch)
                ->first();

            $stock_purchase_return_yester = DB::table('returnpurchases')
                ->select(DB::raw("SUM(amount)as stock_purchase_return_amt"))
                ->whereDate('created_at', '<=', $stock_return_details_created_date)
                ->where('branch', $branch)
                ->first();

                // $purchase_return_discount = DB::table('returnpurchases')
                // ->select(DB::raw("SUM(COALESCE(discount,0)) as purchase_return_discount"))
                // ->whereDate('created_at', '<=', $stock_return_details_created_date)
                // ->where('branch', $branch)
                // ->value('purchase_return_discount');


            $stock_purchase_yester = $stock_purchase_yester_without_return->stock_purchase_amt - $stock_purchase_return_yester->stock_purchase_return_amt;

            $latest_buyproducts = DB::table('buyproducts')
                ->whereDate('created_at', '<', $start_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $buyproducts_created_date = $latest_buyproducts ? date('Y-m-d', strtotime($latest_buyproducts)) : 0;

            $latest_buyproducts_return = DB::table('returnproducts')
                ->whereDate('created_at', '<', $start_date)
                ->where('branch', $branch)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->first();

            $buyproducts_return_created_date = $latest_buyproducts_return ? date('Y-m-d', strtotime($latest_buyproducts_return)) : 0;

            /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

            $soldstock_yester_without_return = DB::table('buyproducts')
                ->select(DB::raw('SUM(buycost_rate_add) as total_soldstock'))
                ->whereDate('created_at', '<=', $buyproducts_created_date)
                ->where('branch', $branch)
                ->first();

            $soldstock__return_yester = DB::table('returnproducts')
                ->select(DB::raw('SUM(buycost_rate_addreturn) as total_returnstock'))
                ->whereDate('created_at', '<=', $buyproducts_return_created_date)
                ->where('branch', $branch)
                ->first();

            $soldstock_yester = $soldstock_yester_without_return->total_soldstock - $soldstock__return_yester->total_returnstock;

            /*-----------------------------------------------------------------------------------------------*/

            $remaining_stock_closing_yester = $stock_purchase_yester - $soldstock_yester;

            /*----------------------------TODAY OPENING STOCK AND CLOSING STOCK---------------------------------------------------*/

            $stock_purchase_today_without_return = DB::table('stockdetails')
            ->select(DB::raw("SUM(price) as stock_purchase_amt"))
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->first();

            $stock_purchase_return_today = DB::table('returnpurchases')
                ->select(DB::raw("SUM(amount) as stock_purchase_return_amt"))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->first();

                $purchase_return_discount = DB::table('returnpurchases')
                ->select(DB::raw("SUM(COALESCE(discount,0)) as purchase_return_discount"))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->value('purchase_return_discount');

            $stock_purchase = $stock_purchase_today_without_return->stock_purchase_amt - $stock_purchase_return_today->stock_purchase_return_amt;

            $total_stock_opening =  $remaining_stock_closing_yester + $stock_purchase;

            /* ---------------------------------------To show opening stock ---------------------------------- */

            // $stock_purchase_amt_yester_show = DB::table('stockdetails')
            //     ->select(DB::raw("SUM(price) as st_purchase_amt"))
            //     ->whereDate('created_at', '<=', $stock_details_created_date)
            //     ->where('branch', $branch)
            //     ->first();

            // $soldstock_amt_yester_show = DB::table('buyproducts')
            //     ->select(DB::raw('SUM(quantity * one_pro_buycost) as tot_soldstock'))
            //     ->whereDate('created_at', '<=', $buyproducts_created_date)
            //     ->where('branch', $branch)
            //     ->first();


            // $remaining_stock_closing_yester_show = $stock_purchase_amt_yester_show->st_purchase_amt - $soldstock_amt_yester_show->tot_soldstock;

            // $show_opening_stock = $remaining_stock_closing_yester_show; /* opening stock for show */

            $show_opening_stock = $remaining_stock_closing_yester;

            /* --------------------------------------------------------------------------------- */

            /*-------------- Closing stock calculation using purchase wise buycostadd method ----------------*/

            $soldstock_today_without_return = DB::table('buyproducts')
                ->select(DB::raw('SUM(buycost_rate_add) as total_soldstock'))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->first();

            $soldstock__return_today = DB::table('returnproducts')
                ->select(DB::raw('SUM(buycost_rate_addreturn) as total_returnstock'))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->first();

            $soldstock = $soldstock_today_without_return->total_soldstock - $soldstock__return_today->total_returnstock;

            /*-----------------------------------------------------------------------------------------------*/

            $remaining_stock_closing = $total_stock_opening - $soldstock;

            if ($start_date == $end_date) {
                if ($start_date == $frst_dt) {
                    $today_open_stock = 0;
                } else {
                    // $today_open_stock = $total_stock_opening;

                    $today_open_stock = $show_opening_stock;
                }
            } elseif ($start_date < $frst_dt && $end_date != $frst_dt) {
                $today_open_stock = 0;
            } else {
                if ($start_date == $frst_dt) {
                    $today_open_stock = 0;
                } else if ($end_date == $frst_dt) {
                    $today_open_stock = 0;
                } else {
                    // $today_open_stock = $total_stock_opening;
                    $today_open_stock = $show_opening_stock;
                }
            }

            /*-----------------------------------------------------------*/

            /* ---------------- new sales and sales return ---------------- */

            // $sold = DB::table('buyproducts')
            // ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            // ->where('branch', $branch)
            // ->selectRaw('SUM(COALESCE((mrp * quantity) + vat_amount)) as total_sold')
            // ->value('total_sold');



            $total_sold_vat_type_1 = DB::table('buyproducts')
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->where('vat_type', 1)
            ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
            ->value('total_sold');

        // Calculate total sold for vat_type 2
            $total_sold_vat_type_2 = DB::table('buyproducts')
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->where('vat_type', 2)
            ->selectRaw('SUM(COALESCE((mrp * quantity) + vat_amount)) as total_sold')
            ->value('total_sold');

        // Combine results if needed
            $sold = ($total_sold_vat_type_1 ?? 0) + ($total_sold_vat_type_2 ?? 0);



            $salesReturn = DB::table('returnproducts')
                ->whereBetween('returnproducts.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                // ->selectRaw('SUM(CASE WHEN totalamount_wo_discount IS NULL THEN total_amount ELSE totalamount_wo_discount END) as total_sold')
                ->selectRaw('SUM(COALESCE(totalamount_wo_discount, total_amount)) as total_sold')
                ->value('total_sold');

            /*-----------------------------------------------------------*/

            /* ---------------- new purchase and purchase return ---------------- */

            // $purchaseamount = DB::table('stockdetails')
            //     ->whereBetween('stockdetails.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            //     ->where('branch', $branch)
            //     ->sum('price');

                $purchaseamounts = DB::table('stockdetails')
                ->whereBetween('stockdetails.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->select(DB::raw('SUM(stockdetails.price) as total_price'))
                ->first();

            // Access the result
            $purchaseamount = $purchaseamounts->total_price;

            // $purchaseReturn = DB::table('returnpurchases')
            //     ->whereBetween('returnpurchases.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            //     ->where('branch', $branch)
            //     ->sum('amount');


                    $purchaseReturns = DB::table('returnpurchases')
                    ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                    ->where('branch', $branch)
                    ->select(DB::raw('SUM(returnpurchases.amount) as total_price'))
                    ->first();

                $purchaseReturn = $purchaseReturns->total_price;

            /*-----------------------------------------------------------*/

            /*-----------------------------------------------------------*/
            /*---------------------------Indirect Expense & income ---------------------*/

            $monthlyexpense = DB::table('accountexpenses')
                ->select(DB::raw("SUM(amount) as monthly_expense"))
                ->whereBetween('date', [$start_date, $end_date])
                ->where('branch', $branch)
                ->first();

            $salary_amount = DB::table('salarydatas')
                ->select(DB::raw("SUM(salary) as salary"))
                ->whereBetween('date', [$start_date, $end_date])
                ->where('branch_id', $branch)
                ->first();

            $indirect_expenses = ($monthlyexpense->monthly_expense) + ($salary_amount->salary);

            $indirect_income = DB::table('account_indirect_incomes')
                ->select(DB::raw("SUM(amount) as indirect_income"))
                ->whereBetween('date', [$start_date, $end_date])
                ->where('branch', $branch)
                ->first();

            $indirect_income = $indirect_income->indirect_income ?? 0;
            // ----------------
            $direct_expense = DB::table('accountexpenses')
            ->select(DB::raw('direct_expense,SUM(amount) as amount'))
            ->whereBetween('accountexpenses.date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->where('expense_type', 1)
            // ->where('user_id', $userid)
            ->groupBy('direct_expense')
            ->get();


            $indirect_expense = DB::table('accountexpenses')
            ->select(DB::raw('indirect_expense,SUM(amount) as amount'))
            ->whereBetween('accountexpenses.date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->where('expense_type',2)
            // ->where('user_id', $userid)
            ->groupBy('indirect_expense')
            ->get();

            $direct_income = DB::table('account_indirect_incomes')
            ->select(DB::raw('direct_income,SUM(amount) as amount'))
            ->whereBetween('account_indirect_incomes.date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->where('income_type',1)
            // ->where('user_id', $userid)
            ->groupBy('direct_income')
            ->get();

            $indirect_incomes = DB::table('account_indirect_incomes')
            ->select(DB::raw('indirect_income,SUM(amount) as amount'))
            ->whereBetween('account_indirect_incomes.date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->where('income_type',2)
            // ->where('user_id', $userid)
            ->groupBy('indirect_income')
            ->get();



            // Calculate total amounts
            $total_direct_expense = $direct_expense->sum('amount');
            $total_direct_income = $direct_income->sum('amount');
            $total_indirect_expense = $indirect_expense->sum('amount');
            $total_indirect_income = $indirect_incomes->sum('amount');
            /*-------------------------------------------------------------------------*/

            // $sales_discount = DB::table('buyproducts')
            //     ->select(DB::raw("SUM(discount_amount * remain_quantity) as discount_amount"))
            //     ->whereBetween(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), [$start_date, $end_date])
            //     ->where('branch', $branch)
            //     ->first();

            // $return_discount = DB::table('returnproducts')
            //     ->select(DB::raw("SUM(discount_amount) as discount_amount"))
            //     ->whereBetween(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), [$start_date, $end_date])
            //     ->where('branch', $branch)
            //     ->first();

            // $discount = $sales_discount->discount_amount - $return_discount->discount_amount;

            // $discount = $sales_discount->discount_amount;


            $discount = DB::table('buyproducts')
                ->select(DB::raw('SUM(total_discount) as total_discount'))
                ->from(DB::raw('(SELECT transaction_id, COALESCE(SUM(buyproducts.discount_amount * buyproducts.quantity), 0) + COALESCE(SUM(total_discount_amount), 0) AS total_discount FROM buyproducts WHERE branch = ? AND DATE(created_at) BETWEEN ? AND ? GROUP BY transaction_id) as subquery'))
                ->addBinding([$branch, $start_date, $end_date], 'select')
                ->value('total_discount');


            $discount = DB::table('buyproducts')
                ->select(DB::raw('SUM(subquery.total_discount) as total_discount'))
                ->from(DB::raw('
                    (
                        SELECT
                            transaction_id,
                            COALESCE(SUM(discount_amount * quantity), 0) AS product_discounts,
                            COALESCE(MAX(total_discount_amount), 0) AS transaction_discount,
                            COALESCE(SUM(discount_amount * quantity), 0) + COALESCE(MAX(total_discount_amount), 0) AS total_discount
                        FROM
                            buyproducts
                        WHERE
                            branch = ?
                            AND DATE(created_at) BETWEEN ? AND ?
                        GROUP BY
                            transaction_id
                    ) as subquery
                '))
                ->addBinding([$branch, $start_date, $end_date], 'select')
                ->value('total_discount');

                $return_discount = DB::table('returnproducts')
                ->select(DB::raw("ROUND(SUM(COALESCE(discount_amount,0)) + SUM(total_amount * (COALESCE(total_discount_percent,0)/100)), 2) as return_discount"))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->value('return_discount');

                $discountpurchase = DB::table('stockdetails')
                ->select(DB::raw("ROUND(SUM(COALESCE(discount, 0)), 2) as discountpurchase"))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('branch', $branch)
                ->value('discountpurchase');

                $purchaseservice = DB::table('stockdetails')
                ->select(DB::raw("SUM(price) - ROUND(SUM(COALESCE(discount, 0)), 2) AS purchaseservice"))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('method','=',2)
                ->where('branch', $branch)
                ->value('purchaseservice');

                $returnpurchaseservice = DB::table('returnpurchases')
                ->select(DB::raw("SUM(amount) - ROUND(SUM(COALESCE(discount, 0)), 2) AS returnpurchaseservice"))
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->where('method','=',2)
                ->where('branch', $branch)
                ->value('returnpurchaseservice');

            $total_credit_note = DB::table('credit_note')
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->selectRaw('SUM(credit_note_amount) as total_credit')
                ->value('total_credit');

            $start_date = $start_date;
            $end_date = $end_date;


            $service_cost = DB::table('buyproducts')
            ->select(DB::raw("SUM(COALESCE(service_cost, 0) * remain_quantity) as service_cost"))
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->value('service_cost');

            $onlyservice_cost = DB::table('service')
            ->select(DB::raw("SUM(COALESCE(total_amount, 0)) as service_cost"))
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('branch', $branch)
            ->value('service_cost');
        }

        return [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'soldstock_value' => $sold,
            'opening_stock' => $today_open_stock,
            'closing_stock' => $remaining_stock_closing,
            'purchase_amount' => $purchaseamount,
            'indirect_expenses' => $indirect_expenses,
            'indirect_income' => $indirect_income,
            'locations' => $location,
            'purchaseReturn' => $purchaseReturn,
            'salesReturn' => $salesReturn,
            'discount' => $discount,
            'return_discount'=>$return_discount,
            'discountpurchase' => $discountpurchase,
            'purchaseservice' => $purchaseservice,
            'returnpurchaseservice' => $returnpurchaseservice,

            'purchase_return_discount'=>$purchase_return_discount,
            'service_cost'=>$service_cost,
            'onlyservice_cost'=>$onlyservice_cost,

            'direct_expense' => $direct_expense,
            'indirect_expense' => $indirect_expense,
            'direct_income' => $direct_income,
            'indirect_incomes' => $indirect_incomes,
            'total_direct_expense' => $total_direct_expense,
            'total_direct_income' => $total_direct_income,
            'total_indirect_expense' => $total_indirect_expense,
            'total_indirect_income' => $total_indirect_income,
            'total_credit_note'=>$total_credit_note,


        ];
    }
}
