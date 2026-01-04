<?php

namespace App\Http\Controllers;

use App\Exports\exportPurchaseReturnReport;
use App\Exports\OverallPurchaseExport;
use App\Exports\OverallPurchaseReturnExport;
use App\Exports\OverallSalesExport;
use App\Exports\OverallSalesReturnReport;
use App\Exports\ProductsExport;
use App\Exports\PurchaseExport;
use App\Exports\SalesExport;
use App\Exports\SalesReturnExport;
use App\Imports\ProductsImport;
use App\Models\Accountantloc;
use App\Models\Accountexpense;
use App\Models\AccountIndirectIncome;
use App\Models\Accounttype;
use App\Models\Addstock;
use App\Models\Adminuser;
use App\Models\bank;
use App\Models\Bankhistory;
use App\Models\BankTransfer;
use App\Models\BillDraft;
use App\Models\BillHistory;
use App\Models\Branch;
use App\Models\Buyproduct;
use App\Models\NewBuyproduct;
use App\Models\CashNotes;
use App\Models\CashSupplierTransaction;
use App\Models\CashTransStatement;
use App\Models\Category;
use App\Models\CreditSupplierTransaction;
use App\Models\CreditTransaction;
use App\Models\Credituser;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteDraft;
use App\Models\Finalreport;
use App\Models\Fundhistory;
use App\Models\Hrusercreation;
use App\Models\Hruserroles;
use App\Models\Invoicedata;
use App\Models\Invoiceproduct;
use App\Models\PerformanceInvoice;
use App\Models\PerformanceInvoiceDraft;
use App\Models\Product;
use App\Models\ProductDraft;
use App\Models\PurchaseDraft;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Models\QuotationDraft;
use App\Models\Returnproduct;
use App\Models\Returnpurchase;
use App\Models\Salarydata;
use App\Models\SalesOrder;
use App\Models\SalesOrderDraft;
use App\Models\Softwareuser;
use App\Models\Stockdat;
use App\Models\Stockdetail;
use App\Models\Service;
use App\Models\NewStockdetail;
use App\Models\Stockhistory;
use App\Models\StockPurchaseReport;
use App\Models\Supplier;
use App\Models\SupplierCredit;
use App\Models\SupplierFundHistory;
use App\Models\Termsandcondition;
use App\Models\TotalExpense;
use App\Models\Chartofaccountants;
use App\Models\TransferType;
use App\Models\Unit;
use App\Models\UserReport;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use App\Models\PandL;
use App\Repositories\MainRepository\EditPurchaseRepository;
use App\Repositories\MainRepository\EditTransactionRepository;
use App\Services\activityService;
use App\Services\EditPurchaseService;
use App\Services\EditTransactionService;
use App\Services\otherService;
use App\Services\purchaseorderService;
use App\Services\QuotationService;
use App\Services\salesorderService;
use App\Services\salesQuotService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Log;

use App\Services\JournalEntryService;

// Includes WebClientPrint classes
require_once app_path().'/WebClientPrint/WebClientPrint.php';

use Neodynamic\SDK\Web\WebClientPrint;

class UserController extends Controller
{
    // user
   public function dashBoard()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');

        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
            
            $servicerole = $item->where('role_id', 31)->isNotEmpty() ? 1 : 0;
            

        $adminid = Softwareuser::where('id', $userid)->pluck('admin_id')->first();
        $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();
                        $shopdata = Branch::Where('id', $branch)->get();

        $userdata = Softwareuser::where('id', $userid)->get();

        $currency = Adminuser::Where('id', $adminid)
        ->pluck('currency')
        ->first();




        $today = Carbon::today()->format('Y-m-d');

        $todaysale = DB::table('buyproducts')
        ->select(DB::raw('SUM(DISTINCT COALESCE(bill_grand_total, 0)) as total_sales'))
        ->where('branch', $branch)
        ->whereDate('created_at', $today)
        ->groupBy('transaction_id')
        ->get()
        ->sum('total_sales');
        
 $returnData = Returnproduct::where('branch', $branch)
            ->whereDate('created_at', $today)
            ->select(
                'transaction_id',
                DB::raw('SUM(DISTINCT COALESCE(grand_total_wo_discount, 0)) as grand_total'),
                DB::raw('SUM(DISTINCT COALESCE(total_discount_amount, 0)) as total_discount')
            )
            ->groupBy('return_id')
            ->get();

        $todayreturn = $returnData->sum(function($item) {
            return $item->grand_total - $item->total_discount;
        });
        
        $buyproductsTotal = DB::table('buyproducts')
        ->where('branch', $branch)
        ->whereDate('created_at', $today)
        ->sum(DB::raw('service_cost * quantity'));

    $servicesTotal = DB::table('service')
        ->where('branch', $branch)
        ->whereDate('created_at', $today)
        ->sum('total_amount');

    $todayservice = $buyproductsTotal + $servicesTotal;

    $todaypurchase = Stockdetail::whereDate('created_at', $today)
        ->where('branch', $branch)
        ->select(DB::raw('SUM(COALESCE(price, 0)) - SUM(COALESCE(discount, 0)) as total_price'))
        ->groupBy('reciept_no')
        ->get()
        ->sum('total_price');

        

        
    $transaction_count = DB::table('buyproducts')
        ->where('branch', $branch)
        ->whereDate('created_at', $today)
        ->distinct()
        ->count('transaction_id');
 $lowStockItems = DB::table('products')
  ->select('product_name', 'remaining_stock')
     ->where('status', 1)
    ->where('remaining_stock', '<', 5)
    ->where('branch', $branch)
    ->orderBy('remaining_stock', 'asc')
    ->get();
    

    $now = [Carbon::now()->format('Y-m-d')];
    $year = [];
    $i = 7;
    while ($i > -1) {
        // Adjusting to get the correct date format including month and day
        $today = Carbon::today()->subDays($i);
        array_push($year, $today->format('Y-m-d'));  // Store full date (year-month-day)
        $i--;
    }

    $dnow = [Carbon::now()->format('Y-m-d')];
    $dyear = [];
    $di = 7;
    while ($di > -1) {
        $dtoday = Carbon::today()->subDays($di);
        array_push($dyear, $dtoday->format('Y-m-d'));  // Store full date (year-month-day)
        $di--;
    }

    $user = [];
    foreach ($year as $key => $value) {
        // Compare using full date format (Y-m-d)
        $user[] = Buyproduct::where(\DB::raw("DATE(created_at)"), '=', Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d'))
            ->where('branch', $branch)
            ->count();
    }

    $returned = [];
    foreach ($year as $key => $value) {
        // Compare using full date format (Y-m-d)
        $returned[] = Returnproduct::where(\DB::raw("DATE(created_at)"), Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d'))
            ->where('branch', $branch)
            ->count();
    }

    $purchase = [];
    foreach ($year as $key => $value) {
        // Compare using full date format (Y-m-d)
        $purchase[] = Stockdetail::where(\DB::raw("DATE(created_at)"), Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d'))
            ->where('branch', $branch)
            ->count();
    }

    $returnpurchase = [];
    foreach ($year as $key => $value) {
        // Compare using full date format (Y-m-d)
        $returnpurchase[] = Returnpurchase::where(\DB::raw("DATE(created_at)"), Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d'))
            ->where('branch', $branch)
            ->count();
    }

        $supplier = DB::table('supplier_credits')
    ->join('suppliers', 'supplier_credits.supplier_id', '=', 'suppliers.id')
    ->where('suppliers.location', $branch)
    ->select(DB::raw('COALESCE(SUM(due_amt - collected_amt), 0) as amount_difference'))
    ->value('amount_difference');

    $credit = DB::table('creditsummaries')
    ->join('creditusers', 'creditsummaries.credituser_id', '=', 'creditusers.id')
    ->where('creditusers.location', $branch)
    ->select(DB::raw('COALESCE(SUM(due_amount - collected_amount), 0) as amount_difference'))
    ->value('amount_difference');

    $topProducts = DB::table('buyproducts')
    ->select('product_id', 'product_name', DB::raw('SUM(remain_quantity) as total_sales'))
    ->where('branch', $branch)
    ->groupBy('product_id')
    ->orderByDesc('total_sales')
    ->limit(3)
    ->get();

    // Get bottom 3 performing products
    $bottomProducts = DB::table('buyproducts')
        ->select('product_id', 'product_name', DB::raw('SUM(remain_quantity) as total_sales'))
        ->where('branch', $branch)
        ->groupBy('product_id')
        ->orderBy('total_sales')
        ->limit(3)
        ->get();

        $todayexpenses = Accountexpense::where('branch', $branch)
        ->whereDate('created_at', $today)
        ->sum('amount');

        $topCustomers = DB::table('buyproducts')
        ->join('creditusers', function($join) {
            $join->on('buyproducts.credit_user_id', '=', 'creditusers.id')
                 ->orOn('buyproducts.cash_user_id', '=', 'creditusers.id');
        })
        ->select(
            DB::raw('COALESCE(buyproducts.credit_user_id, buyproducts.cash_user_id) as credit_id'),
            'creditusers.name as customer_name',
            DB::raw('COUNT(DISTINCT buyproducts.transaction_id) as transaction_count')
        )
        ->where('branch', $branch)
        ->groupBy('credit_id')
        ->orderByDesc('transaction_count')
        ->limit(3)
        ->get();
    
    


        return view('/user/dashboard', array('topCustomers'=>$topCustomers,'todayexpenses'=>$todayexpenses,'topProducts'=>$topProducts,'bottomProducts'=>$bottomProducts,'supplier'=>$supplier,'credit'=>$credit,'servicerole'=>$servicerole,'todayreturn'=>$todayreturn,'lowStockItems'=>$lowStockItems,'transaction_count'=>$transaction_count,'todayservice'=>$todayservice,'userid'=>$userid,'currency'=>$currency,'todaysale'=>$todaysale,'todaypurchase'=>$todaypurchase,'users' => $item, 'shopdatas' => $shopdata,'userdatas'=>$userdata))->with('year', json_encode($dyear))->with('user', json_encode($user, JSON_NUMERIC_CHECK))->with('returned', json_encode($returned, JSON_NUMERIC_CHECK))->with('purchase', json_encode($purchase, JSON_NUMERIC_CHECK))->with('returnpurchase', json_encode($returnpurchase, JSON_NUMERIC_CHECK));


    }

    public function submitData(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'productName' => 'required',
        ]);

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $count = DB::table('buyproducts')
        ->where('branch', $branch)
            ->distinct()
            ->count('transaction_id');

        ++$count;

        $admin_id = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('admin_id')
            ->first();

          $transdefault = DB::table('branches')
            ->where('id', $branch)
            ->pluck('transaction')
            ->first();

        $text = ($request->vat_type_value == 1) ? 'IN' : 'EX';

        $transaction_id = $transdefault.$count.$text;

        $credit_note_amount=$request->credit_note_amount;
        $bill_grand_total = $request->bill_grand_total;
        if ($credit_note_amount > $bill_grand_total) {
            $final_credit_note = $bill_grand_total; // Use the grand total
        } else {
            $final_credit_note = $credit_note_amount; // Use the credit note amount
        }

        /* --------------------new code with product id-------------------------- */

        foreach ($request->product_id as $key => $productID) {
            $data = new Buyproduct();
            $data->product_name = $request->productName[$key];
            $data->quantity = $request->quantity[$key];
            $data->remain_quantity = $request->quantity[$key];
            $data->unit = $request->prounit[$key];
            $data->product_id = $productID;
            $data->transaction_id = $transaction_id;
            $data->customer_name = $request->customer_name;
            $data->employee_id = $request->employee_id;
            $data->employee_name = $request->employee_name;
            $data->email = $request->email;
            $data->trn_number = $request->trn_number;
            $data->phone = $request->phone;
            $data->price = $request->price[$key];
            $data->total_amount = $request->total_amount[$key];
            $data->payment_type = $request->payment_type;
            $data->user_id = Session('softwareuser');
            $data->branch = $branch;
            $data->one_pro_buycost = $request->buy_cost[$key];
            $data->mrp = $request->mrp[$key];
            $data->fixed_vat = $request->fixed_vat[$key];
            $data->bank_id = $request->bank_name;
            $data->account_name = $request->account_name;
            
             if($branch==4 || $branch==2 || $branch==5){
            $data->quantity_type = $request->quantityType[$key] ?? null;
            $data->box_count = $request->boxInput[$key] ?? null;
             }


            $data->vat_amount = $request->vat_amount[$key];
            $data->service_name = $request->servicename[$key] ?? null;
            $data->service_cost = $request->serviceprice[$key] ?? null;

            if ($request->payment_type == 3) {
                $data->credit_user_id = $request->credit_id;
            } elseif ($request->payment_type == 1 || $request->payment_type == 2 || $request->payment_type == 4) {
                $data->cash_user_id = $request->credit_id;
            }

            $data->vat_type = $request->vat_type_value;

            $data->one_pro_buycost_rate = $request->buycost_rate[$key];
            $data->netrate = $request->net_rate[$key];

            $data->inclusive_rate = ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$key] : null;
            $data->exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

            if ($request->page == 'sales_order' || $request->page == 'quotation') {
                $data->discount_type = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key];

                $data->discount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                    ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0)));

                $data->discount_amount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                    ($request->dis_count__tp_ori[$key] == 'percentage' ?
                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0));
            } else {
                $data->discount_type = $request->dis_count_type[$key];

                if ($request->vat_type_value == 1) {
                    if ($request->dis_count_type[$key] == 'none') {
                        $data->discount = $request->dis_count[$key];
                    } elseif ($request->dis_count_type[$key] == 'percentage') {
                        $data->discount = $request->dis_count[$key];
                        $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                    } elseif ($request->dis_count_type[$key] == 'amount') {
                        $data->discount_amount = $request->dis_count[$key];
                        $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                    }
                } elseif ($request->vat_type_value == 2) {
                    if ($request->dis_count_type[$key] == 'none') {
                        $data->discount = $request->dis_count[$key];
                    } elseif ($request->dis_count_type[$key] == 'percentage') {
                        $data->discount = $request->dis_count[$key];
                        $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                    } elseif ($request->dis_count_type[$key] == 'amount') {
                        $data->discount_amount = $request->dis_count[$key];
                        $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                    }
                }
            }

            $data->totalamount_wo_discount = $request->total_amount_wo_discount[$key];
            $data->price_wo_discount = $request->price_withoutvat_wo_discount[$key];

            $data->total_discount_type = $request->total_discount;
            $data->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100);
            $data->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100));

            $data->bill_grand_total = $request->bill_grand_total;
            $data->bill_grand_total_wo_discount = $request->bill_grand_total_wo_discount;
            $data->credit_note = $final_credit_note;

            if ($request->page == 'sales_order') {
                $data->to_invoice = '1';
                $data->sales_order_trans_ID = $request->transaction_id;
            } elseif ($request->page == 'quotation') {
                $data->to_invoice = '2';
                $data->quotation_trans_ID = $request->transaction_id;
            }
            $data->save();
            NewBuyproduct::create($data->getAttributes());
            // -------------------------------------------------------------------//
            $credit_note_amount = DB::table('credit_note_summary')
            ->where('customer_name', $request->customer_name)
            ->pluck('credit_note_amount')
            ->first();

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $credit_note_amount = $credit_note_amount - $final_credit_note;

        $credit_note_summary = DB::table('credit_note_summary')
            ->updateOrInsert(
                ['customer_name' => $request->customer_name],
                [
                    'credit_note_amount' => $credit_note_amount,
                    'branch' => $branch
                ]
            );


            // -----------------------------------------------------------------------//

            $stockdat = new Stockdat();
            $stockdat->product_id = $productID;
            $stockdat->stock_num = $request->quantity[$key];
            $stockdat->transaction_id = $transaction_id;
            $stockdat->user_id = Session('softwareuser');
            $stockdat->one_pro_buycost = $request->buy_cost[$key];
            $stockdat->one_pro_sellingcost = $request->mrp[$key];

            if ($request->vat_type_value == 1) {
                $stockdat->one_pro_inclusive_rate = $request->inclusive_rate_r[$key];
            }

            $stockdat->one_pro_buycost_rate = $request->buycost_rate[$key];
            $stockdat->netrate = $request->net_rate[$key];
            $stockdat->save();

            $remainingstock = Product::find($productID);
            $remainingstock->remaining_stock -= $request->quantity[$key];
            $remainingstock->save();

            if ($request->page == 'sales_order') {
                DB::table('sales_orders')
                    ->where('transaction_id', $request->transaction_id)
                    ->update([
                        'invoice_done' => 1,
                        'invoice_trans' => $transaction_id,
                    ]);
            } elseif ($request->page == 'quotation') {
                DB::table('quotations')
                    ->where('transaction_id', $request->transaction_id)
                    ->update([
                        'invoice_done' => 1,
                        'invoice_trans' => $transaction_id,
                    ]);
            }

            /* ------------- Quantity reduce purchase wise code stock purchasereport table --------- */

            $buycostadd = 0;

            $buycost_rate_add = 0;
        if($branch!=0){

            $first_purchase = DB::table('stock_purchase_reports')
                ->select(DB::raw('*'))
                ->where('sell_quantity', '>', 0)
                ->where('product_id', $productID)
                ->where('branch_id', $branch)
                ->orderBy('created_at', 'ASC')
                ->first();

            if (!$first_purchase) {
                break;
            }

            $rem_sell = StockPurchaseReport::where('purchase_id', $first_purchase->purchase_id)
                ->where('product_id', $productID)
                ->where('branch_id', $branch)
                ->pluck('sell_quantity')
                ->first();

            if ($request->quantity[$key] <= $rem_sell) {
                $balance = $rem_sell - $request->quantity[$key];

                StockPurchaseReport::where('purchase_id', $first_purchase->purchase_id)
                    ->where('product_id', $productID)
                    ->where('branch_id', $branch)
                    ->where('receipt_no', $first_purchase->receipt_no)
                    ->update([
                        'sell_quantity' => $balance,
                    ]);

                $buycostadd += ($request->quantity[$key] * $first_purchase->PBuycost);
                $buycost_rate_add += ($request->quantity[$key] * $first_purchase->PBuycostRate);

                Buyproduct::where('transaction_id', $transaction_id)
                    ->where('product_id', $productID)
                    ->where('branch', $branch)
                    ->update([
                        'buycostadd' => $buycostadd,
                        'buycost_rate_add' => $buycost_rate_add,
                    ]);

                    NewBuyproduct::where('transaction_id', $transaction_id)
                    ->where('product_id', $productID)
                    ->where('branch', $branch)
                    ->update([
                        'buycostadd' => $buycostadd,
                        'buycost_rate_add' => $buycost_rate_add,
                    ]);

                /* ----------------------------------------------------- */

                $billhistory = new BillHistory();
                $billhistory->trans_id = $transaction_id;
                $billhistory->product_id = $productID;
                $billhistory->puid = $first_purchase->purchase_trans_id;
                $billhistory->pid = $first_purchase->purchase_id;
                $billhistory->sold_quantity = $request->quantity[$key];
                $billhistory->remain_sold_quantity = $request->quantity[$key];
                $billhistory->branch_id = $branch;
                $billhistory->user_id = Session('softwareuser');
                $billhistory->Purchase_buycost = $first_purchase->PBuycost;
                $billhistory->billing_Sellingcost = $request->mrp[$key];
                $billhistory->Purchase_Buycost_Rate = $first_purchase->PBuycostRate;
                $billhistory->netrate = $request->net_rate[$key];
                $billhistory->receipt_no = $first_purchase->receipt_no;

                $billhistory->billing_inclusive_rate = ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$key] : null;
                $billhistory->billing_exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

                if ($request->page == 'sales_order' || $request->page == 'quotation') {
                    $billhistory->discount_type = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key];
                    $billhistory->discount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                        ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0)));

                    $billhistory->discount_amount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                        ($request->dis_count__tp_ori[$key] == 'percentage' ?
                            ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                            ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0));
                } else {
                    $billhistory->discount_type = $request->dis_count_type[$key];

                    if ($request->vat_type_value == 1) {
                        if ($request->dis_count_type[$key] == 'none') {
                            $billhistory->discount = $request->dis_count[$key];
                        } elseif ($request->dis_count_type[$key] == 'percentage') {
                            $billhistory->discount = $request->dis_count[$key];
                            $billhistory->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                        } elseif ($request->dis_count_type[$key] == 'amount') {
                            $billhistory->discount_amount = $request->dis_count[$key];
                            $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                        }
                    } elseif ($request->vat_type_value == 2) {
                        if ($request->dis_count_type[$key] == 'none') {
                            $billhistory->discount = $request->dis_count[$key];
                        } elseif ($request->dis_count_type[$key] == 'percentage') {
                            $billhistory->discount = $request->dis_count[$key];
                            $billhistory->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                        } elseif ($request->dis_count_type[$key] == 'amount') {
                            $billhistory->discount_amount = $request->dis_count[$key];
                            $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                        }
                    }
                }

                $billhistory->total_discount_type = $request->total_discount;
                $billhistory->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100);
                $billhistory->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100));
                $billhistory->bill_grand_total = $request->bill_grand_total;
                $billhistory->bill_grand_total_wo_discount = $request->bill_grand_total_wo_discount;

                $billhistory->save();
                /* ----------------------------------------------------- */
            } elseif ($request->quantity[$key] > $rem_sell) {
                $remainq = $request->quantity[$key] - $rem_sell;

                StockPurchaseReport::where('purchase_id', $first_purchase->purchase_id)
                    ->where('product_id', $productID)
                    ->where('branch_id', $branch)
                    ->where('receipt_no', $first_purchase->receipt_no)
                    ->update([
                        'sell_quantity' => 0,
                    ]);

                $buycostadd += ($rem_sell * $first_purchase->PBuycost);

                $buycost_rate_add += ($rem_sell * $first_purchase->PBuycostRate);

                /* ----------------------------------------------------- */

                $billhistory = new BillHistory();
                $billhistory->trans_id = $transaction_id;
                $billhistory->product_id = $productID;
                $billhistory->puid = $first_purchase->purchase_trans_id;
                $billhistory->pid = $first_purchase->purchase_id;
                $billhistory->sold_quantity = $rem_sell;
                $billhistory->remain_sold_quantity = $rem_sell;
                $billhistory->branch_id = $branch;
                $billhistory->user_id = Session('softwareuser');
                $billhistory->Purchase_buycost = $first_purchase->PBuycost;
                $billhistory->billing_Sellingcost = $request->mrp[$key];
                $billhistory->Purchase_Buycost_Rate = $first_purchase->PBuycostRate;
                $billhistory->netrate = $request->net_rate[$key];
                $billhistory->receipt_no = $first_purchase->receipt_no;

                $billhistory->billing_inclusive_rate = ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$key] : null;
                $billhistory->billing_exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

                if ($request->page == 'sales_order' || $request->page == 'quotation') {
                    $billhistory->discount_type = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key];
                    $billhistory->discount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                        ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0)));

                    $billhistory->discount_amount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                        ($request->dis_count__tp_ori[$key] == 'percentage' ?
                            ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                            ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0));
                } else {
                    $billhistory->discount_type = $request->dis_count_type[$key];

                    if ($request->vat_type_value == 1) {
                        if ($request->dis_count_type[$key] == 'none') {
                            $billhistory->discount = $request->dis_count[$key];
                        } elseif ($request->dis_count_type[$key] == 'percentage') {
                            $billhistory->discount = $request->dis_count[$key];
                            $billhistory->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                        } elseif ($request->dis_count_type[$key] == 'amount') {
                            $billhistory->discount_amount = $request->dis_count[$key];
                            $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                        }
                    } elseif ($request->vat_type_value == 2) {
                        if ($request->dis_count_type[$key] == 'none') {
                            $billhistory->discount = $request->dis_count[$key];
                        } elseif ($request->dis_count_type[$key] == 'percentage') {
                            $billhistory->discount = $request->dis_count[$key];
                            $billhistory->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                        } elseif ($request->dis_count_type[$key] == 'amount') {
                            $billhistory->discount_amount = $request->dis_count[$key];
                            $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                        }
                    }
                }

                $billhistory->total_discount_type = $request->total_discount;
                $billhistory->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100);
                $billhistory->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100));
                $billhistory->bill_grand_total = $request->bill_grand_total;
                $billhistory->bill_grand_total_wo_discount = $request->bill_grand_total_wo_discount;
                $billhistory->save();
                /* ----------------------------------------------------- */

                while ($remainq > 0) {
                    $next_purchase = DB::table('stock_purchase_reports')
                        ->select(DB::raw('*'))
                        ->where('sell_quantity', '>', 0)
                        ->where('product_id', $productID)
                        ->where('branch_id', $branch)
                        ->orderBy('created_at', 'ASC')
                        ->first();

                    if (!$next_purchase) {
                        break;
                    }

                    if ($remainq <= $next_purchase->sell_quantity) {  // / next only one purchase
                        $updated_bal = $next_purchase->sell_quantity - $remainq;

                        StockPurchaseReport::where('purchase_id', $next_purchase->purchase_id)
                            ->where('product_id', $productID)
                            ->where('branch_id', $branch)
                            ->where('receipt_no', $next_purchase->receipt_no)
                            ->update([
                                'sell_quantity' => $updated_bal,
                            ]);

                        $buycostadd += ($remainq * $next_purchase->PBuycost);

                        $buycost_rate_add += ($remainq * $next_purchase->PBuycostRate);

                        Buyproduct::where('transaction_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch', $branch)
                            ->update([
                                'buycostadd' => $buycostadd,
                                'buycost_rate_add' => $buycost_rate_add,
                            ]);

                            NewBuyproduct::where('transaction_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch', $branch)
                            ->update([
                                'buycostadd' => $buycostadd,
                                'buycost_rate_add' => $buycost_rate_add,
                            ]);

                        /* ----------------------------------------------------- */

                        $billhistory = new BillHistory();
                        $billhistory->trans_id = $transaction_id;
                        $billhistory->product_id = $productID;
                        $billhistory->puid = $next_purchase->purchase_trans_id;
                        $billhistory->pid = $next_purchase->purchase_id;
                        $billhistory->sold_quantity = $remainq;
                        $billhistory->remain_sold_quantity = $remainq;
                        $billhistory->branch_id = $branch;
                        $billhistory->user_id = Session('softwareuser');
                        $billhistory->Purchase_buycost = $next_purchase->PBuycost;
                        $billhistory->billing_Sellingcost = $request->mrp[$key];
                        $billhistory->Purchase_Buycost_Rate = $next_purchase->PBuycostRate;
                        $billhistory->netrate = $request->net_rate[$key];
                        $billhistory->receipt_no = $next_purchase->receipt_no;

                        $billhistory->billing_inclusive_rate = ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$key] : null;
                        $billhistory->billing_exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

                        if ($request->page == 'sales_order' || $request->page == 'quotation') {
                            $billhistory->discount_type = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key];
                            $billhistory->discount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                                ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0)));

                            $billhistory->discount_amount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                                ($request->dis_count__tp_ori[$key] == 'percentage' ?
                                    ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                        $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                                    ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                        $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0));
                        } else {
                            $billhistory->discount_type = $request->dis_count_type[$key];

                            if ($request->vat_type_value == 1) {
                                if ($request->dis_count_type[$key] == 'none') {
                                    $billhistory->discount = $request->dis_count[$key];
                                } elseif ($request->dis_count_type[$key] == 'percentage') {
                                    $billhistory->discount = $request->dis_count[$key];
                                    $billhistory->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                                } elseif ($request->dis_count_type[$key] == 'amount') {
                                    $billhistory->discount_amount = $request->dis_count[$key];
                                    $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                                }
                            } elseif ($request->vat_type_value == 2) {
                                if ($request->dis_count_type[$key] == 'none') {
                                    $billhistory->discount = $request->dis_count[$key];
                                } elseif ($request->dis_count_type[$key] == 'percentage') {
                                    $billhistory->discount = $request->dis_count[$key];
                                    $billhistory->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                                } elseif ($request->dis_count_type[$key] == 'amount') {
                                    $billhistory->discount_amount = $request->dis_count[$key];
                                    $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                                }
                            }
                        }

                        $billhistory->total_discount_type = $request->total_discount;
                        $billhistory->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100);
                        $billhistory->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100));
                        $billhistory->bill_grand_total = $request->bill_grand_total;
                        $billhistory->bill_grand_total_wo_discount = $request->bill_grand_total_wo_discount;

                        $billhistory->save();
                        /* ----------------------------------------------------- */

                        $remainq = 0;
                    } elseif ($remainq > $next_purchase->sell_quantity) { // more than 2 purchases - looping through
                        $remainq -= $next_purchase->sell_quantity;

                        StockPurchaseReport::where('purchase_id', $next_purchase->purchase_id)
                            ->where('product_id', $productID)
                            ->where('branch_id', $branch)
                            ->where('receipt_no', $next_purchase->receipt_no)
                            ->update([
                                'sell_quantity' => 0,
                            ]);

                        $buycostadd += ($next_purchase->sell_quantity * $next_purchase->PBuycost);

                        $buycost_rate_add += ($next_purchase->sell_quantity * $next_purchase->PBuycostRate);

                        /* ----------------------------------------------------- */

                        $billhistory = new BillHistory();
                        $billhistory->trans_id = $transaction_id;
                        $billhistory->product_id = $productID;
                        $billhistory->puid = $next_purchase->purchase_trans_id;
                        $billhistory->pid = $next_purchase->purchase_id;
                        $billhistory->sold_quantity = $next_purchase->sell_quantity;
                        $billhistory->remain_sold_quantity = $next_purchase->sell_quantity;
                        $billhistory->branch_id = $branch;
                        $billhistory->user_id = Session('softwareuser');
                        $billhistory->Purchase_buycost = $next_purchase->PBuycost;
                        $billhistory->billing_Sellingcost = $request->mrp[$key];
                        $billhistory->Purchase_Buycost_Rate = $next_purchase->PBuycostRate;
                        $billhistory->netrate = $request->net_rate[$key];
                        $billhistory->receipt_no = $next_purchase->receipt_no;

                        $billhistory->billing_inclusive_rate = ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$key] : null;
                        $billhistory->billing_exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

                        if ($request->page == 'sales_order' || $request->page == 'quotation') {
                            $billhistory->discount_type = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key];
                            $billhistory->discount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                                ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0)));

                            $billhistory->discount_amount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                                ($request->dis_count__tp_ori[$key] == 'percentage' ?
                                    ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                        $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                                    ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                        $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0));
                        } else {
                            $billhistory->discount_type = $request->dis_count_type[$key];

                            if ($request->vat_type_value == 1) {
                                if ($request->dis_count_type[$key] == 'none') {
                                    $billhistory->discount = $request->dis_count[$key];
                                } elseif ($request->dis_count_type[$key] == 'percentage') {
                                    $billhistory->discount = $request->dis_count[$key];
                                    $billhistory->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                                } elseif ($request->dis_count_type[$key] == 'amount') {
                                    $billhistory->discount_amount = $request->dis_count[$key];
                                    $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                                }
                            } elseif ($request->vat_type_value == 2) {
                                if ($request->dis_count_type[$key] == 'none') {
                                    $billhistory->discount = $request->dis_count[$key];
                                } elseif ($request->dis_count_type[$key] == 'percentage') {
                                    $billhistory->discount = $request->dis_count[$key];
                                    $billhistory->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                                } elseif ($request->dis_count_type[$key] == 'amount') {
                                    $billhistory->discount_amount = $request->dis_count[$key];
                                    $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                                }
                            }
                        }

                        $billhistory->total_discount_type = $request->total_discount;
                        $billhistory->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100);
                        $billhistory->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100));
                        $billhistory->bill_grand_total = $request->bill_grand_total;
                        $billhistory->bill_grand_total_wo_discount = $request->bill_grand_total_wo_discount;

                        $billhistory->save();
                        /* ----------------------------------------------------- */
                    }
                }
            }
        }
}
        /* ---------------------------------------------------------------------- */


// -------------------------------------------------------------------------


        $transaction = $transaction_id;
        $due_amounttotal = 0;

        foreach ($request->product_id as $key => $productID) {
            $due_amounttotal += $request->total_amount[$key];
        }

        $credituserid = $request->credit_id;

        $credit_username = DB::table('creditusers')
            ->where('id', $credituserid)
            ->where('location', $branch)
            ->pluck('name')
            ->first();



        if ($request->payment_type == 3) {
            $due_amount = DB::table('creditsummaries')
                ->where('credituser_id', $credituserid)
                ->pluck('due_amount')
                ->first();

            $due_amountupload = $request->bill_grand_total + $due_amount;

            $paid = DB::table('creditsummaries')
                ->where('credituser_id', $credituserid)
                ->pluck('collected_amount')
                ->first();

            $crediit_note = DB::table('creditsummaries')
                ->select(DB::raw('creditnote'))
                ->where('credituser_id', $credituserid)
                ->pluck('creditnote')
                ->first();

                $due = $due_amount - $paid - $crediit_note;

            $credit_limit = DB::table('creditusers')
            ->where('id', $credituserid)
            ->where('location', $branch)
            ->pluck('current_lamount')
            ->first();

            $remaining_bill_amount = $request->bill_grand_total;

              // Step 0: Deduct from advance balance if available
              $deducted_from_advance = 0;
        if ($request->advance_balance !== null) {
            $deduct_from_advance = min($request->advance_balance, $remaining_bill_amount);
            $remaining_bill_amount -= $deduct_from_advance;
            $deducted_from_advance = $deduct_from_advance; // Amount deducted from advance balance

            // Update advance balance in the database if needed
            $new_advance_balance = $request->advance_balance - $deduct_from_advance;
            // Save the updated advance balance back to the database if necessary
        }

        $deducted_from_credit = 0;
        if ($remaining_bill_amount > 0 && $credit_limit !== null) {
            if ($credit_limit >= $remaining_bill_amount) {
                // Deduct the entire remaining bill amount from credit limit
                $deducted_from_credit = $remaining_bill_amount; // Amount deducted
                $remaining_credit_limit = $credit_limit - $remaining_bill_amount;
                $remaining_bill_amount = 0;

                // Update the current_lamount column in creditusers table
                DB::table('creditusers')
                    ->where('id', $credituserid)
                    ->where('location', $branch)
                    ->update(['current_lamount' => $remaining_credit_limit]);
            } else {
                // If credit limit is less than the remaining bill amount, deduct whatever is available and update the credit limit to zero
                $deducted_from_credit = $credit_limit; // Amount deducted
                $remaining_bill_amount -= $credit_limit;
                $remaining_credit_limit = 0;

                // Update the current_lamount column in creditusers table
                DB::table('creditusers')
                    ->where('id', $credituserid)
                    ->where('location', $branch)
                    ->update(['current_lamount' => $remaining_credit_limit]);
            }

        }



            $collect = $paid + $request->advance;

            if ($request->advance) {
                $fund = new Fundhistory();
                $fund->username = $credit_username;
                $fund->amount = $request->advance;
                $fund->credituser_id = $credituserid;
                $fund->due = ($due + $request->bill_grand_total);
                $fund->user_id = Session('softwareuser');
                $fund->location = $branch;
                $fund->trans_id = $transaction_id;
                $fund->save();
            }

            $creditsummaries = DB::table('creditsummaries')
                ->updateOrInsert(
                    ['credituser_id' => $credituserid],
                    [
                        'due_amount' => $due_amountupload,
                        'collected_amount' => $collect,
                    ]
                );

            // new transaction_creditnote table

            $lastTransaction = DB::table('credit_transactions')
                ->where('credituser_id', $credituserid)
                ->where('location', $branch)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

                $updated_balance = $lastTransaction->updated_balance ?? null;
                $last_invoice_due = $lastTransaction->balance_due ?? null;


                // $new_due = $updated_balance + $due_amounttotal;
                $new_due = $updated_balance + $request->bill_grand_total;

                $advanced_bal = $new_due - $request->advance;
                // For the invoice_due, subtract the collected amount from the last invoice_due
        if ($lastTransaction && $lastTransaction->transaction_id === $transaction) {
        $new_invoice_due = $last_invoice_due - $request->advance;
        } else {
        $new_invoice_due = $request->bill_grand_total - $request->advance;
        }
            $credit_trans = new CreditTransaction();
            $credit_trans->credituser_id = $credituserid;
            $credit_trans->credit_username = $credit_username;
            $credit_trans->user_id = Session('softwareuser');
            $credit_trans->location = $branch;
            $credit_trans->transaction_id = $transaction;
            if ($updated_balance == null) {
                $credit_trans->due = 0;
            } else {
                $credit_trans->due = $updated_balance;
            }
            $credit_trans->updated_balance = $advanced_bal;
            $credit_trans->collected_amount = $request->advance ?? null;
            $credit_trans->balance_due = $new_invoice_due;
            $credit_trans->Invoice_due = $request->bill_grand_total;
            $credit_trans->due_days = $request->due_days;
            $credit_trans->comment = 'Invoice';
            if ($deducted_from_advance > 0) {
                $credit_trans->credit_balance = $deducted_from_advance;
            }
            if ($deducted_from_credit > 0) {
                $credit_trans->credit_lose = $deducted_from_credit;
            }
            $credit_trans->save();
        } elseif (($request->payment_type == 1 || $request->payment_type == 2 || $request->payment_type == 4) && ($request->credit_id != '' || $request->credit_id != null)) {
            $lastTransaction = DB::table('cash_trans_statements')
                ->where('cash_user_id', $credituserid)
                ->where('location', $branch)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $updated_balance = $lastTransaction->updated_balance ?? null;

            // $new_due = $updated_balance + $due_amounttotal;
            $new_bal = $updated_balance + $request->bill_grand_total;

            $cash_trans = new CashTransStatement();
            $cash_trans->cash_user_id = $credituserid;
            $cash_trans->cash_username = $credit_username;
            $cash_trans->user_id = Session('softwareuser');
            $cash_trans->location = $branch;
            $cash_trans->transaction_id = $transaction;
            $cash_trans->collected_amount = $request->bill_grand_total;
            $cash_trans->updated_balance = $new_bal;
            $cash_trans->comment = 'Invoice';
            $cash_trans->payment_type = $request->payment_type;
            $cash_trans->save();
        }
             // ----------bank-------

        if ($request->bank_name && $request->account_name) {
            $current_balance = DB::table('bank')
                        ->where('id', $request->bank_name)
                        ->where('account_name', $request->account_name)
                        ->pluck('current_balance')
                        ->first();

            $new_balance = $current_balance + $request->bill_grand_total;

            DB::table('bank')
                ->where('id', $request->bank_name)
                ->where('account_name', $request->account_name)
                ->update(['current_balance' => $new_balance]);

            $bank_history = new Bankhistory();
            $bank_history->transaction_id = $transaction_id;
            $bank_history->user_id = Session('softwareuser');
            $bank_history->branch = $branch;
            $bank_history->detail = 'Sales';
            $bank_history->dr_cr = 'Credit';
            $bank_history->bank_id = $request->bank_name;
            $bank_history->account_name = $request->account_name;
            $bank_history->amount = $request->bill_grand_total;
            $bank_history->date = Carbon::now(); // Store the current date and time
            $bank_history->save();
        }

        // BEGIN JournalEntry hook: Sale (Web)
        try {
            $transactionId = $request->input('transaction_id') ?? ($sale->transaction_id ?? null);

            if ($transactionId) {
                app(\App\Services\JournalEntryService::class)->postSaleByTransaction($transactionId);
            }
        } catch (\Exception $e) {
            \Log::error('Journal entry failed [Sale Web]: '.$e->getMessage());
        }
        // END JournalEntry hook


/* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' done product billing';

        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        if ($request->page == 'bill_draft') {
            DB::table('billdraft')
                ->where('transaction_id', $request->transaction_id)
                ->update(['branch' => null]);
        }

        return redirect('/billdeskfinalreciept/'.$transaction);
    }


    public function updateData(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'productName' => 'required',
        ]);
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        foreach ($request->productName as $key => $productName) {
            $data = new Buyproduct();
            $data->product_name = $productName;
            $data->quantity = $request->quantity[$key];
            $data->product_id = $request->product_id[$key];
            $data->transaction_id = $request->transaction_id;
            $data->customer_name = $request->customer_name;
            $data->trn_number = $request->trn_number;
            $data->price = $request->price[$key];
            $data->payment_type = $request->payment_type;
            $data->user_id = Session('softwareuser');
            $data->branch = $branch;
            $data->mrp = $request->mrp[$key];
            $data->fixed_vat = $request->fixed_vat[$key];
            $data->vat_amount = (($request->price[$key] * $request->fixed_vat[$key]) / 100);
            $data->save();
            $stockdat = new Stockdat();
            $stockdat->product_id = $request->product_id[$key];
            $stockdat->stock_num = $request->quantity[$key];
            $stockdat->transaction_id = $request->transaction_id;
            $stockdat->user_id = Session('softwareuser');
            $stockdat->save();
        }
        $transaction = $request->transaction_id;
        session()->pull('payment_type');
        session()->pull('billdata');
        session()->pull('customer_name');

        return redirect('/billdeskfinalreciept/'.$transaction);
    }

  public function listTransaction()
    {
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }
        else {
            return redirect('userlogin');
        }
        $payment_type = request()->input('payment_type');
        $date_filter = request()->input('date_filter', 'all');
        if (Session('softwareuser')) {
            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();
            // original//////

            // $data = DB::table('buyproducts')
            //     ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
            //     ->leftJoin('creditusers', 'buyproducts.credit_user_id', '=', 'creditusers.id')
            //     ->select(DB::raw("
            //         buyproducts.transaction_id,
            //         buyproducts.created_at,
            //         buyproducts.customer_name,
            //         buyproducts.vat_type,
            //         buyproducts.quantity,
            //         SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
            //         SUM(buyproducts.vat_amount) as vat,
            //         payment.type as payment_type,
            //         creditusers.username,
            //         buyproducts.phone,
            //         SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,

            //         SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
            //         +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
            //     "))
            //     ->groupBy('buyproducts.transaction_id')
            //     ->orderBy('buyproducts.created_at', 'DESC')
            //     ->where('branch', $branch)
            //     ->get();

         $query = DB::table('buyproducts')
                ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
                ->leftJoin('creditusers', 'buyproducts.credit_user_id', '=', 'creditusers.id')
                ->leftJoin(DB::raw('(SELECT
                            transaction_id COLLATE utf8mb4_unicode_ci as transaction_id,
                                    SUM(totalamount_wo_discount) as return_grandtotal_without_discount,
                                    SUM(COALESCE(discount_amount, 0)) + SUM(total_amount * (total_discount_percent / 100)) as return_discount_amount,
                                    SUM(COALESCE(grand_total, 0)) as return_sum
                                FROM returnproducts
                                GROUP BY transaction_id) as returns'), 'buyproducts.transaction_id', '=', 'returns.transaction_id')
                ->leftJoin(DB::raw('(SELECT
                                transaction_id,
                                SUM(credit_note_amount) as total_credit_note_amount
                            FROM (
                                SELECT DISTINCT transaction_id, credit_note_id, credit_note_amount
                                FROM credit_note
                            ) as unique_credits
                            GROUP BY transaction_id) as credit_sums'), 'buyproducts.transaction_id', '=', 'credit_sums.transaction_id')
                ->select(DB::raw('
                    buyproducts.transaction_id,
                    buyproducts.created_at,
                    buyproducts.customer_name,
                    buyproducts.vat_type,
                    buyproducts.quantity,
                     buyproducts.approve,
                    SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0)) as sum,
                    SUM(buyproducts.vat_amount) as vat,
                    payment.type as payment_type,
                    creditusers.username,
                    buyproducts.phone,
                    SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
                    + SUM(DISTINCT COALESCE(buyproducts.total_discount_amount, 0)) as discount_amount,
                    COALESCE(returns.return_grandtotal_without_discount, 0) as return_grandtotal_without_discount,
                    COALESCE(returns.return_discount_amount, 0) as return_discount_amount,
                    COALESCE(returns.return_sum, 0) as return_sum,
                COALESCE(credit_sums.total_credit_note_amount) as credit_note_amount
                '))
                ->groupBy('buyproducts.transaction_id')
                ->orderBy('buyproducts.created_at', 'DESC')
                ->where('buyproducts.branch', $branch);

                if ($payment_type) {
                    $query->where('payment.type', $payment_type);
                }

                // Apply date filter
                if ($date_filter == 'today') {
                    $query->whereDate('buyproducts.created_at', Carbon::today());
                }

                $data = $query->get();
            $userid = Session('softwareuser');
            $item = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();

            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();

            $user_location = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

            $creditusers = Credituser::Where('admin_id', $adminid)
                ->where('status', 1)
                ->where('location', $user_location)
                ->get();

            $credit_user_id = null;
        } elseif (Session('adminuser')) {
           $query = DB::table('buyproducts')
             ->leftJoin('branches', 'buyproducts.branch', '=', 'branches.id')
                ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
                ->leftJoin('creditusers', 'buyproducts.credit_user_id', '=', 'creditusers.id')
                ->leftJoin(DB::raw('(SELECT
                                    transaction_id,
                                    SUM(totalamount_wo_discount) as return_grandtotal_without_discount,
                                    SUM(COALESCE(discount_amount, 0)) + SUM(total_amount * (total_discount_percent / 100)) as return_discount_amount,
                                    SUM(DISTINCT COALESCE(grand_total, 0)) as return_sum
                                FROM returnproducts
                                GROUP BY transaction_id) as returns'), 'buyproducts.transaction_id', '=', 'returns.transaction_id')
        ->leftJoin(DB::raw('(SELECT
                                transaction_id,
                                SUM(credit_note_amount) as total_credit_note_amount
                            FROM (
                                SELECT DISTINCT transaction_id, credit_note_id, credit_note_amount
                                FROM credit_note
                            ) as unique_credits
                            GROUP BY transaction_id) as credit_sums'), 'buyproducts.transaction_id', '=', 'credit_sums.transaction_id')
                ->select(DB::raw('
                    buyproducts.transaction_id,
                    buyproducts.created_at,
                    buyproducts.customer_name,
                    buyproducts.vat_type,
                    buyproducts.quantity,
                    buyproducts.approve,
                     branches.location as branch,
                    SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0)) as sum,
                    SUM(buyproducts.vat_amount) as vat,
                    payment.type as payment_type,
                    creditusers.username,
                    buyproducts.phone,
                    SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
                    + SUM(DISTINCT COALESCE(buyproducts.total_discount_amount, 0)) as discount_amount,
                    COALESCE(returns.return_grandtotal_without_discount, 0) as return_grandtotal_without_discount,
                    COALESCE(returns.return_discount_amount, 0) as return_discount_amount,
                    COALESCE(returns.return_sum, 0) as return_sum,
                COALESCE(credit_sums.total_credit_note_amount) as credit_note_amount
                '))
                ->groupBy('buyproducts.transaction_id')
                ->orderBy('buyproducts.created_at', 'DESC');

                // Apply payment type filter if selected
                if ($payment_type) {
                    $query->where('payment.type', $payment_type);
                }

                // Apply date filter
                if ($date_filter == 'today') {
                    $query->whereDate('buyproducts.created_at', Carbon::today());
                }

                $data = $query->get();

            $adminid = Session('adminuser');

            $item = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();

            $shopdata = Adminuser::Where('id', $adminid)
                ->get();
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        $start_date = '';
        $end_date = '';


        if (Session('softwareuser')) {
            $options = [
                'products' => $data,
                'users' => $item,
                'currency' => $currency,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'adminid' => $adminid,
                'creditusers' => $creditusers,
                'credit_user_id' => $credit_user_id,
                'tax'=>$tax,
                'payment_type' => $payment_type,
                'date_filter' => $date_filter,
            ];
        } elseif (Session('adminuser')) {
            $options = [
                'products' => $data,
                'users' => $item,
                'currency' => $currency,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'shopdatas' => $shopdata,
                'tax' => $tax,
                'payment_type' => $payment_type,
                'date_filter' => $date_filter,
            ];
        }

        return view('/billingdesk/transactions', $options);
    }

    public function editTransaction()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $data = DB::table('buyproducts')
            ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
            ->select(DB::raw('buyproducts.transaction_id,buyproducts.created_at,buyproducts.customer_name,SUM(buyproducts.total_amount) as sum,SUM(buyproducts.vat_amount) as vat,payment.type as payment_type'))
            ->groupBy('transaction_id')
            ->orderBy('created_at', 'DESC')
            ->where('branch', $branch)
            ->get();
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        $start_date = '';
        $end_date = '';

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' visited edit transaction page';

        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }
        /* ----------------------------------------------------------------------- */

        return view('/billingdesk/edittransactions', ['tax'=>$tax,'products' => $data, 'users' => $item, 'currency' => $currency, 'start_date' => $start_date, 'end_date' => $end_date]);
    }

    public function billingdashBoard()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $count = DB::table('buyproducts')->distinct()->count('transaction_id');
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $item = Product::select(DB::raw('*'))
            ->where('branch', $branch)
            ->where('status', 1)
            ->get();
        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $validateuser = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->where('role_id', 1)
            ->pluck('role_id')
            ->first();
        if ($validateuser != '1') {
            return redirect('userlogin');
        }

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $shopdata = Branch::Where('id', $branch)->get();
        $user_location = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $creditusers = Credituser::Where('admin_id', $adminid)
            ->where('status', 1)
            ->where('location', $user_location)
            ->get();

        return view('/billingdesk/dashboard', ['counts' => $count, 'creditusers' => $creditusers, 'items' => $item, 'users' => $useritem, 'shopdatas' => $shopdata]);
    }

     public function custombillingdashboard(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $item = Product::select([
                'id',
                'product_name',
                'unit',
                'buy_cost',
                'rate',
                'selling_cost',
                'vat',
                'remaining_stock',
                'box_count',
                'box_enabled'
            ])
            ->where('branch', $branch)
            ->where('status', 1)
            ->where('remaining_stock', '>', 0)
            ->get();

        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $validateuser = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->where('role_id', 1)
            ->pluck('role_id')
            ->first();
        if ($validateuser != '1') {
            return redirect('userlogin');
        }
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();

        $creditusers = Credituser::where('admin_id', $adminid)
            ->where('status', 1)
            ->where('location', $branch)
            ->leftJoin('creditsummaries', 'creditusers.id', '=', 'creditsummaries.credituser_id') // Use LEFT JOIN to include all creditusers
            ->select(
                'creditusers.*', // Select all creditusers columns
                DB::raw('COALESCE(creditsummaries.due_amount, 0) AS due_amount'), // Treat NULL as 0 for due_amount
                DB::raw('COALESCE(creditsummaries.collected_amount, 0) AS collected_amount'), // Treat NULL as 0 for collected_amount
                DB::raw('CASE
                            WHEN COALESCE(creditsummaries.collected_amount, 0) > COALESCE(creditsummaries.due_amount, 0)
                            THEN COALESCE(creditsummaries.collected_amount, 0) - COALESCE(creditsummaries.due_amount, 0)
                            ELSE NULL
                         END AS balance') // Only show balance when collected_amount > due_amount
            )
            ->get();


        $shopdata='';

      

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        $listbank = DB::table('bank')
            ->select('id','bank_name', 'account_name', 'status') // Include status if you need it
            ->where('status', 1)
            ->where('branch', $branch)
            ->get();

        $listemployee=DB::table('employee')
            ->select('first_name', 'id')
            ->where('branch', $branch)
            ->get();
        $vat = DB::table('vat_mode')->where('branch',$branch)->first(); // Get the first row from the vat_mode table
        $mode = $vat->mode;

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' visited billing page';

        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */
        if($branch==4 || $branch==2 || $branch==5){
         return view('/billingdesk/billing_box', ['branchid'=>$branch,'mode'=>$mode,'listemployee'=>$listemployee,'listbank' => $listbank, 'creditusers' => $creditusers, 'items' => $item, 'users' => $useritem, 'shopdatas' => $shopdata, 'currency' => $currency, 'tax' => $tax]);

        }else{
            
        return view('/billingdesk/custombilling', ['branchid'=>$branch,'mode'=>$mode,'listemployee'=>$listemployee,'listbank' => $listbank, 'creditusers' => $creditusers, 'items' => $item, 'users' => $useritem, 'shopdatas' => $shopdata, 'currency' => $currency, 'tax' => $tax]);
        }
    }
    public function recieptFinal($transaction_id)
    {
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }
        else {
            return redirect('userlogin');
        }
        $dataplan = DB::table('buyproducts')
        ->select(DB::raw('buyproducts.box_count as box_count,buyproducts.product_name as product_name,buyproducts.service_name as service_name,buyproducts.service_cost as service_cost,buyproducts.product_id as product_id,buyproducts.quantity as quantity,buyproducts.mrp as mrp,buyproducts.price as price,buyproducts.fixed_vat as fixed_vat,buyproducts.vat_amount as vat_amount,buyproducts.total_amount as total_amount, buyproducts.unit as unit, buyproducts.vat_type as vat_type, buyproducts.inclusive_rate as inclusive_rate, buyproducts.netrate as netrate,buyproducts.discount, buyproducts.totalamount_wo_discount, buyproducts.price_wo_discount, buyproducts.discount_amount'))
        ->where('buyproducts.transaction_id', $transaction_id)
            ->get();
        $total = Buyproduct::select(DB::raw('SUM(price) as total'))
            ->where('transaction_id', $transaction_id)
            ->get();

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $trans = $transaction_id;
        // $enctrans = Crypt::encrypt($trans);

        $enctrans = $trans;

        $custs = DB::table('buyproducts')
            ->where('transaction_id', $trans)
            ->pluck('customer_name')
            ->first();

        $branchname = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();
            if (Session('softwareuser')) {
                $userid = Session('softwareuser');
                $item = DB::table('softwareusers')
                    ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                    ->where('user_id', $userid)
                    ->get();
                    $adminid = Softwareuser::Where('id', $userid)
                        ->pluck('admin_id')
                    ->first();
                    $branch = DB::table('softwareusers')
                    ->where('id', Session('softwareuser'))
                    ->pluck('location')
                    ->first();
                $shopdata = Branch::Where('id', $branch)->get();
            } elseif (Session('adminuser')) {
                $adminid = Session('adminuser');
                $item = DB::table('adminusers')
                    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                    ->where('user_id', $adminid)
                    ->get();

                    $shopdata = Adminuser::Where('id', $adminid)->get();
                    $branch = DB::table('buyproducts')
                    ->where('transaction_id', $transaction_id)
                    ->pluck('branch')
                    ->first();
                }
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $name = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();
        $total = Buyproduct::select(DB::raw('SUM(price) as total'))
            ->where('transaction_id', $transaction_id)
            ->pluck('total')
            ->first();
        $vat = Buyproduct::select(DB::raw('SUM(vat_amount) as vat'))
            ->where('transaction_id', $transaction_id)
            ->pluck('vat')
            ->first();

        $rate = Buyproduct::select(DB::raw('SUM(mrp * quantity) as mrp'))
            ->where('transaction_id', $transaction_id)
            ->pluck('mrp')
            ->first();
            $service_cost = Buyproduct::select(DB::raw('SUM(service_cost * quantity) as service_cost'))
            ->where('transaction_id', $transaction_id)
            ->pluck('service_cost')
            ->first();

        $payment_type = DB::table('buyproducts')
            ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
            ->select(DB::raw('payment.type as payment_type'))
            ->where('buyproducts.transaction_id', $trans)
            ->pluck('payment_type')
            ->first();

        $date = DB::table('buyproducts')
            ->select(DB::raw('DATE(buyproducts.created_at) as date'))
            ->where('transaction_id', $trans)
            ->pluck('date')
            ->first();
        $date = Carbon::parse($date)->format('d-m-Y');

        $grand = Buyproduct::where('transaction_id', $transaction_id)
            ->select(DB::raw('SUM(total_amount) as total_amount'))
            ->pluck('total_amount')
            ->first();

        $discount_amt = Buyproduct::select(DB::raw('SUM(discount_amount * quantity) as discount_amount'))
            ->where('transaction_id', $transaction_id)
            ->pluck('discount_amount')
            ->first();

        $Main_discount_amt = Buyproduct::where('transaction_id', $transaction_id)
            ->pluck('total_discount_amount')
            ->first();

            $credit_note_amount=DB::table('buyproducts')
            ->where('transaction_id', $transaction_id)
            ->pluck('credit_note')
            ->first();


            $remaining_after_discount = $grand - $Main_discount_amt;

            // Determine the grand in number based on the credit note amount
            if ($remaining_after_discount <= $credit_note_amount) {
                // If credit note amount covers the remaining amount
                $grandinnumber = 0; // All of the remaining amount is covered by the credit note
            } else {
                // If credit note amount does not cover the remaining amount
                $grandinnumber = $remaining_after_discount - $credit_note_amount; // Subtract credit note amount from remaining amount
            }

        // $grandinnumber = $grand - $Main_discount_amt -$credit_note_amount;
      $grand = number_format($grandinnumber, 3, '.', '');

        // Split into dirhams and fils
        $parts = explode('.', $grand);
        $dirhams = (int)$parts[0];
        $fils = isset($parts[1]) ? (int)substr($parts[1], 0, 3) : 0; // Take up to 3 decimal places
        
        // Format dirhams part
        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $amountinwords = ucwords($formatter->format($dirhams)) . ' Dirham';
        
        // Add fils part if exists
        if ($fils > 0) {
            $amountinwords .= ' and ' . ucwords($formatter->format($fils)) . ' Fils';
        }
        
        // Handle special case for 0 fils (e.g., "10.000")
        if (strpos($grand, '.') !== false && $fils == 0) {
            $amountinwords .= ' Only';
        }

        $grand_wo_dis = Buyproduct::where('transaction_id', $transaction_id)
            ->select(DB::raw('SUM(totalamount_wo_discount) as totalamount_wo_discount'))
            ->pluck('totalamount_wo_discount')
            ->first();

        $price_wo_dis = Buyproduct::where('transaction_id', $transaction_id)
            ->select(DB::raw('SUM(price_wo_discount) as price_wo_discount'))
            ->pluck('price_wo_discount')
            ->first();

        $supplieddate = Carbon::now()->format('d-m-Y');
        // $cr_num = Adminuser::Where('id', $adminid)
        //     ->pluck('cr_number')
        //     ->first();
        // $po_box = Adminuser::Where('id', $adminid)
        //     ->pluck('po_box')
        //     ->first();
        // $tel = Adminuser::Where('id', $adminid)
        //     ->pluck('phone')
        //     ->first();

        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
        $trn_number = DB::table('buyproducts')
            ->where('transaction_id', $trans)
            ->pluck('trn_number')
            ->first();

            $tel = DB::table('branches')
            ->where('id', $branch)
            ->pluck('mobile')
            ->first();
            $po_box = DB::table('branches')
            ->where('id', $branch)
            ->pluck('po_box')
            ->first();




            $admintrno = DB::table('branches')
            ->where('id', $branch)
            ->pluck('tr_no')
            ->first();

            $logo = DB::table('branches')
            ->where('id', $branch)
            ->pluck('logo')
            ->first();
            $company = DB::table('branches')
            ->where('id', $branch)
            ->pluck('company')
            ->first();

            $Address = DB::table('branches')
            ->where('id', $branch)
            ->pluck('address')
            ->first();

        $billphone = Buyproduct::select(DB::raw('phone'))
            ->where('transaction_id', $transaction_id)
            ->pluck('phone')
            ->first();

        $billemail = Buyproduct::select(DB::raw('email'))
            ->where('transaction_id', $transaction_id)
            ->pluck('email')
            ->first();

        $vat_type = Buyproduct::select(DB::raw('vat_type'))
            ->where('transaction_id', $transaction_id)
            ->pluck('vat_type')
            ->first();

        $employeename = Buyproduct::select(DB::raw('employee_name'))
            ->where('transaction_id', $transaction_id)
            ->pluck('employee_name')
            ->first();
   $account_name = DB::table('buyproducts')
            ->where('transaction_id', $transaction_id)
            ->pluck('account_name')
            ->first();

            $bankDetails = DB::table('bank')
            ->where('account_name', $account_name)
            ->where('is_default', 1)
            ->first();

         // Check if bankDetails is null and redirect without error message


        // Fetch customer details from the credituser table based on the customer name
        $customerDetails = DB::table('creditusers')
            ->where('name', $custs)
            ->first();
        // Prepare the additional details
        $billingAdd = optional($customerDetails)->billing_add;

        if ($billingAdd) {
            // Only display the billing address if it exists
            // echo $billingAdd;
        }
        $deliveryAdd = optional($customerDetails)->delivery_default == 1
        ? $customerDetails->deli_add
        : null;
        $wcppScript = WebClientPrint::createWcppDetectionScript(route('processRequest'), Session::getId());

        // return view('/billingdesk/recieptfinal', array('details' => $dataplan, 'vat' => $vat, 'payment_type' => $payment_type, 'grandinnumber' => $grandinnumber, 'totals' => $total, 'trans' => $trans, 'enctrans' => $enctrans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'user_id' => $userid, 'branch' => $branch, 'admin_name' => $adminname,'admintrno'=> $admintrno,'billphone' => $billphone, 'billemail' => $billemail, 'wcppScript' => $wcppScript));

        // return view('/billingdesk/recieptfinalbro', array('details' => $dataplan, 'vat' => $vat, 'payment_type' => $payment_type, 'grandinnumber' => $grandinnumber, 'totals' => $total, 'trans' => $trans, 'enctrans' => $enctrans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'user_id' => $userid, 'branch' => $branch, 'admin_name' => $adminname, 'admintrno' => $admintrno, 'emailadmin' => $emailadmin, 'billphone' => $billphone, 'billemail' => $billemail, 'vat_type' => $vat_type, 'wcppScript' => ''));
        if (Session('softwareuser')) {
            $data = [
                'deliveryAdd'=>$deliveryAdd,
                'billingAdd'=>$billingAdd,
                'bankDetails'=>$bankDetails,
                'details' => $dataplan,
                'vat' => $vat,
                'payment_type' => $payment_type,
                'grandinnumber' => $grandinnumber,
                'totals' => $total, 'trans' => $trans,
                'enctrans' => $enctrans,
                'custs' => $custs,
                'users' => $item,
                'branches' => $branchname,
                'shopdatas' => $shopdata,
                'currency' => $currency,
                'date' => $date,
                'amountinwords' => $amountinwords,
                'supplieddate' => $supplieddate,
                // 'cr_num' => $cr_num,
                'po_box' => $po_box,
                'tel' => $tel,
                'branchname' => $branchname,
                'trn_number' => $trn_number,
                'user_id' => $userid,
                'branch' => $branch,
                // 'admin_name' => $adminname,
                'admintrno' => $admintrno,
                'billphone' => $billphone,
                'billemail' => $billemail,
                'vat_type' => $vat_type,
                'wcppScript' => '',
                'discount_amt' => $discount_amt,
                'grand_wo_dis' => $grand_wo_dis,
                'adminid' => $adminid,
                'price_wo_dis' => $price_wo_dis,
                'Main_discount_amt' => $Main_discount_amt,
                'rate' => $rate,
                'tax'=>$tax,
                'employeename'=>$employeename,
                'name'=>$name,
                'logo'=>$logo,
                'company'=>$company,
                'Address'=>$Address,
                'credit_note_amount'=>$credit_note_amount,
                'service_cost'=>$service_cost

            ];
        } elseif (Session('adminuser')) {
            $data = [
                'deliveryAdd'=>$deliveryAdd,
                'billingAdd'=>$billingAdd,
                'bankDetails'=>$bankDetails,
                'details' => $dataplan,
                'vat' => $vat,
                'payment_type' => $payment_type,
                'grandinnumber' => $grandinnumber,
                'totals' => $total, 'trans' => $trans,
                'enctrans' => $enctrans,
                'custs' => $custs,
                'users' => $item,
                'branches' => $branchname,
                'shopdatas' => $shopdata,
                'currency' => $currency,
                'date' => $date,
                'amountinwords' => $amountinwords,
                'supplieddate' => $supplieddate,
                // 'cr_num' => $cr_num,
                'po_box' => $po_box,
                'tel' => $tel,
                'branchname' => $branchname,
                'trn_number' => $trn_number,
                // 'user_id' => $userid,
                'branch' => $branch,
                // 'admin_name' => $adminname,
                'admintrno' => $admintrno,
                'billphone' => $billphone,
                'billemail' => $billemail,
                'vat_type' => $vat_type,
                'wcppScript' => '',
                'discount_amt' => $discount_amt,
                'grand_wo_dis' => $grand_wo_dis,
                'adminid' => $adminid,
                'price_wo_dis' => $price_wo_dis,
                'Main_discount_amt' => $Main_discount_amt,
                'rate' => $rate,
                'tax'=>$tax,
                'employeename'=>$employeename,
                'name'=>$name,
                'logo'=>$logo,
                'company'=>$company,
                'Address'=>$Address,
                'credit_note_amount'=>$credit_note_amount,
                'service_cost'=>$service_cost

            ];
        }


        return view('/billingdesk/recieptfinalbro', $data);
    }

    public function recieptwithouttaxFinal($transaction_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $dataplan = Buyproduct::where('transaction_id', $transaction_id)->get();
        $total = Buyproduct::select(DB::raw('SUM(price) as total'))
            ->where('transaction_id', $transaction_id)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $trans = $transaction_id;
        $custs = DB::table('buyproducts')
            ->where('transaction_id', $trans)
            ->pluck('customer_name')
            ->first();
        $branchname = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
                        $shopdata = Branch::Where('id', $branch)->get();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $total = Buyproduct::select(DB::raw('SUM(price) as total'))
            ->where('transaction_id', $transaction_id)
            ->pluck('total')
            ->first();
        $vat = Buyproduct::select(DB::raw('SUM(vat_amount) as vat'))
            ->where('transaction_id', $transaction_id)
            ->pluck('vat')
            ->first();

        $rate = Buyproduct::select(DB::raw('SUM(mrp * quantity) as mrp'))
            ->where('transaction_id', $transaction_id)
            ->pluck('mrp')
            ->first();

        $payment_type = DB::table('buyproducts')
            ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
            ->select(DB::raw('payment.type as payment_type'))
            ->where('buyproducts.transaction_id', $trans)
            ->pluck('payment_type')
            ->first();
        $date = DB::table('buyproducts')
            ->select(DB::raw('DATE(buyproducts.created_at) as date'))
            ->where('transaction_id', $trans)
            ->pluck('date')
            ->first();
        $supplieddate = Carbon::now()->format('Y-m-d');
        $cr_num = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();
        $po_box = Adminuser::Where('id', $adminid)
            ->pluck('po_box')
            ->first();

        $grand = Buyproduct::where('transaction_id', $transaction_id)
            ->select(DB::raw('SUM(total_amount) as total_amount'))
            ->pluck('total_amount')
            ->first();

        $discount_amt = Buyproduct::select(DB::raw('SUM(discount_amount * quantity) as discount_amount'))
            ->where('transaction_id', $transaction_id)
            ->pluck('discount_amount')
            ->first();

        $Main_discount_amt = Buyproduct::where('transaction_id', $transaction_id)
            ->pluck('total_discount_amount')
            ->first();
             $credit_note_amount=DB::table('buyproducts')
            ->where('transaction_id', $transaction_id)
            ->pluck('credit_note')
            ->first();


            $remaining_after_discount = $grand - $Main_discount_amt;

            // Determine the grand in number based on the credit note amount
            if ($remaining_after_discount <= $credit_note_amount) {
                // If credit note amount covers the remaining amount
                $grandinnumber = 0; // All of the remaining amount is covered by the credit note
            } else {
                // If credit note amount does not cover the remaining amount
                $grandinnumber = $remaining_after_discount - $credit_note_amount; // Subtract credit note amount from remaining amount
            }



        // $grandinnumber = $grand - $Main_discount_amt;
        $grand = number_format($grandinnumber, 3, '.', '');
        $amountinwords = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $amountinwords = $amountinwords->format($grand);
        $amountinwords = ucwords($amountinwords);

        $grand_wo_dis = Buyproduct::where('transaction_id', $transaction_id)
            ->select(DB::raw('SUM(totalamount_wo_discount) as totalamount_wo_discount'))
            ->pluck('totalamount_wo_discount')
            ->first();

        $price_wo_dis = Buyproduct::where('transaction_id', $transaction_id)
            ->select(DB::raw('SUM(price_wo_discount) as price_wo_discount'))
            ->pluck('price_wo_discount')
            ->first();

        $trn_number = DB::table('buyproducts')
            ->where('transaction_id', $trans)
            ->pluck('trn_number')
            ->first();

        $billphone = Buyproduct::select(DB::raw('phone'))
            ->where('transaction_id', $transaction_id)
            ->pluck('phone')
            ->first();

        $billemail = Buyproduct::select(DB::raw('email'))
            ->where('transaction_id', $transaction_id)
            ->pluck('email')
            ->first();

        $vat_type = Buyproduct::select(DB::raw('vat_type'))
            ->where('transaction_id', $transaction_id)
            ->pluck('vat_type')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
     $account_name = DB::table('buyproducts')
            ->where('transaction_id', $trans)
            ->pluck('account_name')
            ->first();

            $bankDetails = DB::table('bank')
            ->where('account_name', $account_name)
            ->where('is_default', 1)
            ->first();

         // Check if bankDetails is null and redirect without error message

        // Fetch customer details from the credituser table based on the customer name
        $customerDetails = DB::table('creditusers')
            ->where('name', $custs)
            ->first();
        // Prepare the additional details
        $billingAdd = optional($customerDetails)->billing_add;

        if (!empty($billingAdd)) {
            // Only display the billing address if it exists and is not empty
            // echo $billingAdd;
        }
        $deliveryAdd = optional($customerDetails)->delivery_default == 1
        ? $customerDetails->deli_add
        : null;
        $data = [
            'deliveryAdd'=>$deliveryAdd,
            'billingAdd'=>$billingAdd,
            'bankDetails'=>$bankDetails,
            'details' => $dataplan,
            'vat' => $vat,
            'payment_type' => $payment_type,
            'grandinnumber' => $grandinnumber,
            'totals' => $total,
            'trans' => $trans,
            'custs' => $custs,
            'users' => $item,
            'branches' => $branchname,
            'shopdatas' => $shopdata,
            'currency' => $currency,
            'date' => $date,
            'supplieddate' => $supplieddate,
            'cr_num' => $cr_num,
            'po_box' => $po_box,
            'amountinwords' => $amountinwords,
            'trn_number' => $trn_number,
            'billphone' => $billphone,
            'billemail' => $billemail,
            'vat_type' => $vat_type,
            'discount_amt' => $discount_amt,
            'grand_wo_dis' => $grand_wo_dis,
            'price_wo_dis' => $price_wo_dis,
            'Main_discount_amt' => $Main_discount_amt,
            'rate' => $rate,
            'tax'=>$tax,
            'credit_note_amount'=>$credit_note_amount
        ];

        return view('/billingdesk/recieptfinalnotax', $data);

        // return view('/billingdesk/recieptfinalnotax', array('details' => $dataplan, 'vat' => $vat, 'payment_type' => $payment_type, 'grandinnumber' => $grandinnumber, 'totals' => $total, 'trans' => $trans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'amountinwords' => $amountinwords, 'trn_number' => $trn_number, 'billphone' => $billphone, 'billemail' => $billemail));
    }

    public function viewTrans($name)
    {
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }

        $count = DB::table('buyproducts')->count();


        $item = Buyproduct::select([
                'product_name',
                'quantity',
                'created_at',
                'unit',
                'netrate',
                'totalamount_wo_discount',
                'total_amount',
                'vat_amount',
                'discount_amount',
                DB::raw('(SELECT SUM(credit_note.credit_note_amount)
                          FROM credit_note
                          WHERE credit_note.product_name = buyproducts.product_name
                          AND credit_note.transaction_id = buyproducts.transaction_id
                          LIMIT 1) AS credit_note_amount')
            ])
            ->where('transaction_id', $name)
            ->get();



        if (Session('softwareuser')) {
            $userid = Session('softwareuser');
            $useritem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();

            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();
        } elseif (Session('adminuser')) {
            $adminid = Session('adminuser');
            $useritem = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();

            $shopdata = Adminuser::Where('id', $adminid)->get();
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if (Session('softwareuser')) {
            $options = [
                'details' => $item,
                'users' => $useritem,
                'currency' => $currency,
                'tax'=>$tax,
            ];
        } elseif (Session('adminuser')) {
            $options = [
                'details' => $item,
                'users' => $useritem,
                'currency' => $currency,
                'shopdatas' => $shopdata,
                'tax'=>$tax,
            ];
        }

        return view('billingdesk/transactiondetails', $options);
    }

    public function returnProduct()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $item = Product::select(DB::raw('*'))
            ->where('branch', $branch)
            ->get();

        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
                        $shopdata = Branch::Where('id', $branch)->get();

        $sales = DB::table('buyproducts')
            ->where('branch', $branch)
            ->distinct('buyproducts.transaction_id')
            ->pluck('transaction_id');
        $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
            $listbank = DB::table('bank')
            ->select('id','bank_name', 'account_name','status','current_balance')
            ->where('status', 1)
            ->where('branch', $branch)
            ->get();
        /* ------------------GET IP ADDRESS--------------------------------------- */

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' visited product return page';
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */
        return view('/billingdesk/return', ['listbank'=>$listbank,'tax'=>$tax,'items' => $item, 'users' => $useritem, 'shopdatas' => $shopdata, 'sales' => $sales]);
    }

    // original function
    public function returnAction(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'trans_id_origin' => 'required',
        ]);

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

              $count = DB::table('returnproducts')
              ->where('branch', $branch)
            ->distinct()
            ->count('return_id');

        ++$count;
        $text = 'R';

        $return_id = $text.$count;

        foreach ($request->productName as $key => $productName) {
            $data = new Returnproduct();
            $data->product_name = $productName;
            $data->quantity = $request->quantity[$key];
            $data->unit = $request->unit[$key];
            $data->product_id = $request->product_id[$key];
            $data->transaction_id = $request->trans[$key];
            $data->email = $request->email;
            $data->trn_number = $request->trn_number;
            $data->return_id = $return_id;
            $data->phone = $request->phone;
            $data->price = $request->price[$key];
            $data->total_amount = $request->total_amount[$key];
            $data->payment_type = $request->ptype[$key];
            $data->user_id = Session('softwareuser');
            $data->branch = $branch;
            $data->mrp = $request->mrp[$key];
            $data->fixed_vat = $request->fixed_vat[$key];
            $data->one_pro_buycost = $request->buy_cost[$key];
            $data->return_payment = $request->payment_mode;
            $data->bank_id = $request->bank_name;
            $data->account_name = $request->account_name;

            if ($request->ptype[$key] == 3) {
                $data->creditusers_id = $request->creditusers_id[$key];
            } elseif ($request->ptype[$key] == 1 || $request->ptype[$key] == 2 || $request->ptype[$key] == 4) {
                $data->cash_users_id = $request->creditusers_id[$key];
            }

            $data->vat_amount = $request->vat_amount[$key];

            if ($request->vat_type[$key] == 1) {
                $data->inclusive_rate = $request->inclusive_rate_r[$key];
                $data->discount_amount = ($request->dis_count[$key] != 0) ? ($request->total_amount_wo_discount[$key] * ($request->dis_count[$key] / 100)) : null;
            } elseif ($request->vat_type[$key] == 2) {
                $data->discount_amount = ($request->dis_count[$key] != 0) ? ($request->total_withoutvat_wo_discount[$key] * ($request->dis_count[$key] / 100)) : null;
            }

            $data->vat_type = $request->vat_type[$key];
            $data->one_pro_buycost_rate = $request->buycost_rate[$key];
            $data->netrate = $request->net_rate[$key];

            $data->discount = $request->dis_count[$key];

            $data->totalamount_wo_discount = $request->total_amount_wo_discount[$key];
            $data->price_wo_discount = $request->total_withoutvat_wo_discount[$key];

            $data->total_discount_percent = $request->total_discount;
            // $data->total_discount_amount = $request->grand_total_wo_discount * ($request->total_discount / 100);
            $data->total_discount_amount = round($request->grand_total_wo_discount * ($request->total_discount / 100));
            $data->grand_total = $request->grand_total;
            $data->grand_total_wo_discount = $request->grand_total_wo_discount;
            $data->save();

            $ids = $data->id;

            /* ----------------------- Stock Purchase Report --------------------------- */

            $inputQuantity = $request->quantity[$key];

            // Get the purchases from the billhistory table
            $bill_purchases = BillHistory::where('trans_id', $request->trans[$key])
                ->where('product_id', $request->product_id[$key])
                ->where('branch_id', $branch)
                ->get();

            $index = 0;
            $buycostaddreturn = 0;
            $buycost_rate_addreturn = 0;

            while ($index < count($bill_purchases)) {
                $purchase = $bill_purchases[$index];
                // Get the purchase quantity and sold quantity
                $purchaseQuantity = $purchase->remain_sold_quantity;

                // Get the current sell quantity from stockpurchasereport
                $currentSellQuantity = DB::table('stock_purchase_reports')
                    ->where('purchase_trans_id', $purchase->puid)
                    ->where('purchase_id', $purchase->pid)
                    ->pluck('sell_quantity')
                    ->first();

                if ($inputQuantity <= $purchaseQuantity) {
                    $quantityToAdd = $inputQuantity;
                } elseif ($inputQuantity > $purchaseQuantity) {
                    $quantityToAdd = $purchaseQuantity;
                }

                // Update the stockpurchasereport table with the new quantity
                $newQuantity = $currentSellQuantity + $quantityToAdd;

                DB::table('stock_purchase_reports')
                    ->where('purchase_trans_id', $purchase->puid)
                    ->where('purchase_id', $purchase->pid)
                    ->update(['sell_quantity' => $newQuantity]);

                BillHistory::where('trans_id', $request->trans[$key])
                    ->where('product_id', $request->product_id[$key])
                    ->where('branch_id', $branch)
                    ->where('puid', $purchase->puid)
                    ->where('pid', $purchase->pid)
                    ->update([
                        'remain_sold_quantity' => $purchaseQuantity - $quantityToAdd,
                        'return_discount' => $request->dis_count[$key],
                        'return_discount_amount' => ($request->dis_count[$key] != 0) ? ($request->total_amount_wo_discount[$key] * ($request->dis_count[$key] / 100)) : null,
                        'return_total_discount_percent' => $request->total_discount,
                        'return_total_discount_amt' => $request->grand_total_wo_discount * ($request->total_discount / 100),
                        'return_grand_total' => $request->grand_total,
                        'return_grand_total_wo_discount' => $request->grand_total_wo_discount,
                    ]);

                $buycostaddreturn += ($quantityToAdd * $purchase->Purchase_buycost);
                $buycost_rate_addreturn += ($quantityToAdd * $purchase->Purchase_Buycost_Rate);

                Returnproduct::where('transaction_id', $request->trans[$key])
                    ->where('id', $ids)
                    ->where('product_id', $request->product_id[$key])
                    ->where('branch', $branch)
                    ->update([
                        'buycostaddreturn' => $buycostaddreturn,
                        'buycost_rate_addreturn' => $buycost_rate_addreturn,
                    ]);

                // Deduct the allocated quantity from the current input quantity
                $inputQuantity -= $quantityToAdd;

                // If there's no input quantity left, exit the loop
                if ($inputQuantity <= 0) {
                    break;
                }

                // Move to the next purchase and set the remaining input quantity as the new input quantity
                ++$index;
            }

            /* -------------------------------------------------------------------------------- */
        }

        foreach ($request->productName as $key => $productName) {
            $stock_num = DB::table('stockdats')
                ->where('transaction_id', $request->trans[$key])
                ->where('product_id', $request->product_id[$key])
                ->pluck('stock_num')
                ->first();

            $one_pro_buycost = DB::table('stockdats')
                ->where('transaction_id', $request->trans[$key])
                ->where('product_id', $request->product_id[$key])
                ->pluck('one_pro_buycost')
                ->first();

            $one_pro_sellingcost = DB::table('stockdats')
                ->where('transaction_id', $request->trans[$key])
                ->where('product_id', $request->product_id[$key])
                ->pluck('one_pro_sellingcost')
                ->first();

            $one_pro_inclusive_rate = DB::table('stockdats')
                ->where('transaction_id', $request->trans[$key])
                ->where('product_id', $request->product_id[$key])
                ->pluck('one_pro_inclusive_rate')
                ->first();

            $one_pro_buycost_rate = DB::table('stockdats')
                ->where('transaction_id', $request->trans[$key])
                ->where('product_id', $request->product_id[$key])
                ->pluck('one_pro_buycost_rate')
                ->first();

            $one_pro_netrate = DB::table('stockdats')
                ->where('transaction_id', $request->trans[$key])
                ->where('product_id', $request->product_id[$key])
                ->pluck('netrate')
                ->first();

            $newstock_num = $stock_num - $request->quantity[$key];
            DB::table('stockdats')
                ->where('transaction_id', $request->trans[$key])
                ->where('product_id', $request->product_id[$key])
                ->delete();

            $stockdat = new Stockdat();
            $stockdat->product_id = $request->product_id[$key];
            $stockdat->stock_num = $newstock_num;
            $stockdat->transaction_id = $request->trans[$key];
            $stockdat->user_id = Session('softwareuser');
            $stockdat->one_pro_buycost = $one_pro_buycost;
            $stockdat->one_pro_sellingcost = $one_pro_sellingcost;

            if ($request->vat_type[$key] == 1) {
                $stockdat->one_pro_inclusive_rate = $one_pro_inclusive_rate;
            }

            $stockdat->one_pro_buycost_rate = $one_pro_buycost_rate;
            $stockdat->netrate = $one_pro_netrate;

            $stockdat->save();

            $return = Product::find($request->product_id[$key]);
            $return->remaining_stock += $request->quantity[$key];
            $return->save();

            $remain_quan = Buyproduct::where('transaction_id', $request->trans[$key])
                ->where('product_id', $request->product_id[$key])
                // ->where('user_id', Session('softwareuser'))
                ->where('branch', $branch)
                ->pluck('remain_quantity')
                ->first();

            $soldquantity_reduce = Buyproduct::where('transaction_id', $request->trans[$key])
                ->where('product_id', $request->product_id[$key])
                // ->where('user_id', Session('softwareuser'))
                ->where('branch', $branch)
                ->update(['remain_quantity' => ($remain_quan - $request->quantity[$key])]);

            /* ------------------------------------------------------------ */

            if ($request->ptype[$key] == 3 && $request->creditusers_id[$key] != 0) {
                $creditid = $request->creditusers_id[$key];

                $due = DB::table('creditsummaries')
                    ->select(DB::raw('due_amount'))
                    ->where('credituser_id', $creditid)
                    ->pluck('due_amount')
                    ->first();

                $paid = DB::table('creditsummaries')
                    ->select(DB::raw('collected_amount'))
                    ->where('credituser_id', $creditid)
                    ->pluck('collected_amount')
                    ->first();

                $due -= $paid;

                $fund = new Fundhistory();
                $fund->username = $request->creditusers[$key];
                $fund->amount = $request->total_amount[$key];
                $fund->credituser_id = $creditid;
                $fund->due = $due;
                $fund->user_id = Session('softwareuser');
                $fund->location = $branch;
                $fund->status = '1';
                $fund->save();
            }
        }

        if ($request->paymenttype == 3 && $request->credituser__id != 0) {
            $creditid = $request->credituser__id;

            $creditcollected = DB::table('creditsummaries')
                ->where('credituser_id', $creditid)
                ->pluck('collected_amount')
                ->first();


                $credit_limit = DB::table('creditusers')
                ->where('id', $creditid)
                ->where('location', $branch)
                ->pluck('current_lamount')
                ->first();

            $livecollected = $request->grand_total;
            $totalcreditcollected = $creditcollected + $livecollected;

            $creditsummaries = DB::table('creditsummaries')
                ->updateOrInsert(
                    ['credituser_id' => $creditid],
                    ['collected_amount' => $totalcreditcollected]
                );

            // credit transaction

            $lastTransaction = DB::table('credit_transactions')
                ->where('credituser_id', $creditid)
                ->where('location', $branch)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();


                $lastSameTransaction = DB::table('credit_transactions')
                ->where('credituser_id', $creditid)
                ->where('location', $branch)
                ->where('transaction_id', $request->trans_id_origin)
                ->orderBy('created_at', 'desc')
                ->first();

                $previous_due = $lastSameTransaction->balance_due ?? 0;

                $new_balance_due=$previous_due - $livecollected;

                $credit_lose = $lastTransaction->credit_lose ?? 0;

                $credit_deducted = $credit_lose;
                if ($credit_deducted > 0) {
                    $amount_to_recover = min($livecollected, $credit_deducted);

                    // Update the current limit (credit limit)
                    $new_credit_limit = $credit_limit + $amount_to_recover;

                    // Save the updated credit limit back to the database
                    DB::table('creditusers')
                        ->where('id', $creditid)
                        ->where('location', $branch)
                        ->update(['current_lamount' => $new_credit_limit]);

                    // Update credit_lose and adjust livecollected
                    $credit_lose -= $amount_to_recover;
                    // $livecollected -= $amount_to_recover; // Deduct recovered amount from livecollected
                }

            $updated_balance = $lastTransaction->updated_balance;
            $new_due = $updated_balance;
            $new_updated_bal = $new_due - $livecollected;

            $credit_trans = new CreditTransaction();
            $credit_trans->credituser_id = $creditid;
            $credit_trans->credit_username = $request->credituser;
            $credit_trans->user_id = Session('softwareuser');
            $credit_trans->location = $branch;
            $credit_trans->due = $new_due;
            $credit_trans->balance_due = $new_balance_due;
            $credit_trans->collected_amount = $livecollected;
            $credit_trans->updated_balance = $new_updated_bal;
            $credit_trans->credit_lose = $credit_lose; // Store the updated credit_lose
            $credit_trans->comment = 'Returned Product';
            $credit_trans->transaction_id = $request->trans_id_origin;
            $credit_trans->save();
        }
        elseif (($request->paymenttype == 1 || $request->paymenttype == 2 || $request->paymenttype == 4) && ($request->credituser__id != 0)) {
            $creditid = $request->credituser__id;

            $credit_username = DB::table('creditusers')
                ->where('id', $creditid)
                ->where('location', $branch)
                ->pluck('name')
                ->first();

            $lastCashTransaction = DB::table('cash_trans_statements')
                ->where('cash_user_id', $creditid)
                ->where('location', $branch)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $updated_balance = $lastCashTransaction->updated_balance;
            $new_updated_bal = $updated_balance - $request->grand_total;

            $cash_trans = new CashTransStatement();
            $cash_trans->cash_user_id = $creditid;
            $cash_trans->cash_username = $credit_username;
            $cash_trans->user_id = Session('softwareuser');
            $cash_trans->location = $branch;
            $cash_trans->transaction_id = $request->trans_id_origin;
            $cash_trans->collected_amount = $request->grand_total;
            $cash_trans->updated_balance = $new_updated_bal;
            $cash_trans->comment = 'Product Returned';
            $cash_trans->payment_type = $request->paymenttype;
            $cash_trans->save();
        }
        // bank...............................
        if ($request->bank_name && $request->account_name) {
            $current_balance = DB::table('bank')
                ->where('id', $request->bank_name)
                ->where('account_name', $request->account_name)
                ->pluck('current_balance')
                ->first();

            $new_balance = $current_balance - $request->grand_total;
            $userid = Session('softwareuser');

            $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

            DB::table('bank')
                ->where('id', $request->bank_name)
                ->where('account_name', $request->account_name)
                ->update(['current_balance' => $new_balance]);

            $bank_history = new Bankhistory();
            $bank_history->transaction_id = $request->trans_id_origin;
            $bank_history->user_id = Session('softwareuser');
            $bank_history->bank_id = $request->bank_name;
            $bank_history->account_name = $request->account_name;
            $bank_history->branch = $branch_id;
            $bank_history->detail = 'Sale Return';
            $bank_history->dr_cr = 'Debit';
            $bank_history->date = Carbon::now(); // Store the current date and time
            $bank_history->amount = $request->grand_total;
            $bank_history->save();
        }

        /* ------------------GET IP ADDRESS--------------------------------------- */
        $transaction=$request->trans_id_origin;
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $userid = Session('softwareuser');
        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' product returned';
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return redirect('/returnfinalreciept/'.$transaction.'/'.$return_id);
    }

      public function returnfinalreciept($transaction_id,$return_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }



        $created_at = DB::table('returnproducts')
            ->where('transaction_id', $transaction_id)
            ->where('returnproducts.return_id', $return_id)
            ->pluck('created_at')
            ->first();


        $dataplan = DB::table('returnproducts')
            ->select(DB::raw('returnproducts.product_name as product_name,returnproducts.product_id as product_id,returnproducts.quantity as quantity,returnproducts.mrp as mrp,returnproducts.price as price,returnproducts.fixed_vat as fixed_vat,returnproducts.vat_amount as vat_amount,returnproducts.total_amount as total_amount, returnproducts.unit as unit, returnproducts.vat_type as vat_type, returnproducts.inclusive_rate as inclusive_rate, returnproducts.netrate as netrate,returnproducts.discount, returnproducts.totalamount_wo_discount, returnproducts.price_wo_discount, returnproducts.discount_amount'))
            ->where('returnproducts.transaction_id', $transaction_id)
            ->where('returnproducts.return_id', $return_id)
            ->get();

        $total = Returnproduct::select(DB::raw('SUM(price) as total'))
            ->where('transaction_id', $transaction_id)
            ->where('returnproducts.return_id', $return_id)
            ->get();

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $trans = $transaction_id;

        $return_id=$return_id;
        // $enctrans = Crypt::encrypt($trans);

        $enctrans = $trans;

        $custs = DB::table('buyproducts')
            ->where('transaction_id', $trans)
            ->pluck('customer_name')
            ->first();

        $branchname = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();

                        $shopdata = Branch::Where('id', $branch)->get();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $name = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();
        $total = Returnproduct::select(DB::raw('SUM(price) as total'))
            ->where('transaction_id', $transaction_id)
            ->where('returnproducts.return_id', $return_id)
            ->pluck('total')
            ->first();
        $vat = Returnproduct::select(DB::raw('SUM(vat_amount) as vat'))
            ->where('transaction_id', $transaction_id)
            ->where('returnproducts.return_id', $return_id)
            ->pluck('vat')
            ->first();

        $rate = Returnproduct::select(DB::raw('SUM(mrp * quantity) as mrp'))
            ->where('transaction_id', $transaction_id)
            ->where('returnproducts.return_id', $return_id)
            ->pluck('mrp')
            ->first();

        $payment_type = DB::table('returnproducts')
            ->leftJoin('payment', 'returnproducts.payment_type', '=', 'payment.id')
            ->select(DB::raw('payment.type as payment_type'))
            ->where('returnproducts.transaction_id', $trans)
            ->where('returnproducts.return_id', $return_id)
            ->pluck('payment_type')
            ->first();

        $date = DB::table('returnproducts')
            ->select(DB::raw('DATE(returnproducts.created_at) as date'))
            ->where('transaction_id', $trans)
            ->where('returnproducts.return_id', $return_id)
            ->pluck('date')
            ->first();
        $date = Carbon::parse($date)->format('d-m-Y');

        $grand = Returnproduct::where('transaction_id', $transaction_id)
        ->where('returnproducts.return_id', $return_id)
            ->select(DB::raw('SUM(total_amount) as total_amount'))
            ->pluck('total_amount')
            ->first();

        $discount_amt = Returnproduct::select(DB::raw('SUM(discount_amount) as discount_amount'))
            ->where('transaction_id', $transaction_id)
            ->where('returnproducts.return_id', $return_id)
            ->pluck('discount_amount')
            ->first();

        $Main_discount_amt = Returnproduct::where('transaction_id', $transaction_id)
        ->where('returnproducts.return_id', $return_id)
            ->pluck('total_discount_amount')
            ->first();

        $grandinnumber = $grand - $Main_discount_amt;
        $grand = number_format($grandinnumber, 3, '.', '');
        $amountinwords = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $amountinwords = $amountinwords->format($grand);
        $amountinwords = ucwords($amountinwords);

        $grand_wo_dis = Returnproduct::where('transaction_id', $transaction_id)
        ->where('returnproducts.return_id', $return_id)
            ->select(DB::raw('SUM(totalamount_wo_discount) as totalamount_wo_discount'))
            ->pluck('totalamount_wo_discount')
            ->first();

        $price_wo_dis = Returnproduct::where('transaction_id', $transaction_id)
        ->where('returnproducts.return_id', $return_id)
            ->select(DB::raw('SUM(price_wo_discount) as price_wo_discount'))
            ->pluck('price_wo_discount')
            ->first();

        $supplieddate = Carbon::now()->format('d-m-Y');
        // $cr_num = Adminuser::Where('id', $adminid)
        //     ->pluck('cr_number')
        //     ->first();
        // $po_box = Adminuser::Where('id', $adminid)
        //     ->pluck('po_box')
        //     ->first();
        // $tel = Adminuser::Where('id', $adminid)
        //     ->pluck('phone')
        //     ->first();

        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
        $trn_number = DB::table('returnproducts')
            ->where('transaction_id', $trans)
            ->where('returnproducts.return_id', $return_id)
            ->pluck('trn_number')
            ->first();

            $tel = DB::table('branches')
            ->where('id', $branch)
            ->pluck('mobile')
            ->first();
            $po_box = DB::table('branches')
            ->where('id', $branch)
            ->pluck('po_box')
            ->first();




            $admintrno = DB::table('branches')
            ->where('id', $branch)
            ->pluck('tr_no')
            ->first();

            $logo = DB::table('branches')
            ->where('id', $branch)
            ->pluck('logo')
            ->first();
            $company = DB::table('branches')
            ->where('id', $branch)
            ->pluck('company')
            ->first();

            $Address = DB::table('branches')
            ->where('id', $branch)
            ->pluck('address')
            ->first();

        $billphone = Returnproduct::select(DB::raw('phone'))
            ->where('transaction_id', $transaction_id)
            ->where('returnproducts.return_id', $return_id)
            ->pluck('phone')
            ->first();

        $billemail = Returnproduct::select(DB::raw('email'))
            ->where('transaction_id', $transaction_id)
            ->where('returnproducts.return_id', $return_id)
            ->pluck('email')
            ->first();

        $vat_type = Returnproduct::select(DB::raw('vat_type'))
            ->where('transaction_id', $transaction_id)
            ->where('returnproducts.return_id', $return_id)
            ->pluck('vat_type')
            ->first();

              $account_name = DB::table('returnproducts')
            ->where('transaction_id', $transaction_id)
            ->where('returnproducts.return_id', $return_id)
            ->pluck('account_name')
            ->first();

            $bankDetails = DB::table('bank')
            ->where('account_name', $account_name)
            ->where('is_default', 1)
            ->first();







         // Check if bankDetails is null and redirect without error message


        // Fetch customer details from the credituser table based on the customer name
        $customerDetails = DB::table('creditusers')
            ->where('name', $custs)
            ->first();
        // Prepare the additional details
        $billingAdd = optional($customerDetails)->billing_add;

        if ($billingAdd) {
            // Only display the billing address if it exists
            // echo $billingAdd;
        }
        $deliveryAdd = optional($customerDetails)->delivery_default == 1
        ? $customerDetails->deli_add
        : null;
        $wcppScript = WebClientPrint::createWcppDetectionScript(route('processRequest'), Session::getId());

        // return view('/billingdesk/recieptfinal', array('details' => $dataplan, 'vat' => $vat, 'payment_type' => $payment_type, 'grandinnumber' => $grandinnumber, 'totals' => $total, 'trans' => $trans, 'enctrans' => $enctrans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'user_id' => $userid, 'branch' => $branch, 'admin_name' => $adminname,'admintrno'=> $admintrno,'billphone' => $billphone, 'billemail' => $billemail, 'wcppScript' => $wcppScript));

        // return view('/billingdesk/recieptfinalbro', array('details' => $dataplan, 'vat' => $vat, 'payment_type' => $payment_type, 'grandinnumber' => $grandinnumber, 'totals' => $total, 'trans' => $trans, 'enctrans' => $enctrans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'user_id' => $userid, 'branch' => $branch, 'admin_name' => $adminname, 'admintrno' => $admintrno, 'emailadmin' => $emailadmin, 'billphone' => $billphone, 'billemail' => $billemail, 'vat_type' => $vat_type, 'wcppScript' => ''));
        $data = [
            'deliveryAdd'=>$deliveryAdd,
            'bankDetails'=>$bankDetails,
            'billingAdd'=>$billingAdd,
            'details' => $dataplan,
            'vat' => $vat,
            'payment_type' => $payment_type,
            'grandinnumber' => $grandinnumber,
            'totals' => $total,
             'trans' => $trans,
            'enctrans' => $enctrans,
            'custs' => $custs,
            'users' => $item,
            'branches' => $branchname,
            'shopdatas' => $shopdata,
            'currency' => $currency,
            'date' => $date,
            'amountinwords' => $amountinwords,
            'supplieddate' => $supplieddate,
            // 'cr_num' => $cr_num,
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'trn_number' => $trn_number,
            'user_id' => $userid,
            'branch' => $branch,
            // 'admin_name' => $adminname,
            'admintrno' => $admintrno,
            'billphone' => $billphone,
            'billemail' => $billemail,
            'vat_type' => $vat_type,
            'wcppScript' => '',
            'discount_amt' => $discount_amt,
            'grand_wo_dis' => $grand_wo_dis,
            'adminid' => $adminid,
            'price_wo_dis' => $price_wo_dis,
            'Main_discount_amt' => $Main_discount_amt,
            'rate' => $rate,
            'tax'=>$tax,
            // 'employeename'=>$employeename,
            'name'=>$name,
            'logo'=>$logo,
            'company'=>$company,
            'Address'=>$Address,
            'return_id'=>$return_id,

        ];

        return view('/billingdesk/returnreciept', $data);
    }

    public function listReturn()
    {
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }

        if (Session('softwareuser')) {
            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

            $data = DB::table('returnproducts')
                ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
                ->select(
                    DB::raw('returnproducts.id as id'),
                    DB::raw('returnproducts.transaction_id as transaction_id'),
                    DB::raw('returnproducts.return_id as return_id'),
                    DB::raw('returnproducts.created_at as created_at'),
                    DB::raw('returnproducts.phone as phone'),
                    DB::raw('returnproducts.vat_type as vat_type'),
                    DB::raw('returnproducts.quantity'),

                    DB::raw('SUM(returnproducts.totalamount_wo_discount) as grandtotal_without_discount'),
                    DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as sum'),

                    DB::raw('SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount'),

                    DB::raw('SUM(returnproducts.vat_amount) as vat'),
                    DB::raw('GROUP_CONCAT(products.product_name) as product_names'),
                    DB::raw('GROUP_CONCAT(returnproducts.quantity) as quantities')
                )
                ->groupBy('returnproducts.transaction_id', 'returnproducts.created_at')
                ->orderBy('returnproducts.created_at', 'DESC')
                ->where('returnproducts.branch', $branch)
                ->get();

            $userid = Session('softwareuser');

            $item = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();

            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();
        } elseif (Session('adminuser')) {
            $data = DB::table('returnproducts')
                ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
                ->leftJoin('branches', 'returnproducts.branch', '=', 'branches.id')
                ->select(
                    DB::raw('returnproducts.id as id'),
                    DB::raw('returnproducts.transaction_id as transaction_id'),
                    DB::raw('returnproducts.created_at as created_at'),
                    DB::raw('returnproducts.phone as phone'),
                    DB::raw('returnproducts.vat_type as vat_type'),
                    DB::raw('returnproducts.quantity'),

                    DB::raw('SUM(returnproducts.totalamount_wo_discount) as grandtotal_without_discount'),
                    DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as sum'),

                    DB::raw('SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount'),

                    DB::raw('SUM(returnproducts.vat_amount) as vat'),
                    DB::raw('GROUP_CONCAT(products.product_name) as product_names'),
                    DB::raw('GROUP_CONCAT(returnproducts.quantity) as quantities'),
                    DB::raw('branches.location as branch'),
                )
                ->groupBy('returnproducts.transaction_id', 'returnproducts.created_at')
                ->orderBy('returnproducts.created_at', 'DESC')
                ->get();

            $adminid = Session('adminuser');

            $item = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();

            $shopdata = Adminuser::Where('id', $adminid)
                ->get();
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if (Session('softwareuser')) {
            $options = [
                'products' => $data,
                'users' => $item,
                'currency' => $currency,
                'tax'=>$tax,
            ];
        } elseif (Session('adminuser')) {
            $options = [
                'products' => $data,
                'users' => $item,
                'currency' => $currency,
                'shopdatas' => $shopdata,
                'tax'=>$tax,
            ];
        }

        return view('/billingdesk/returnhistory', $options);
    }

    public function viewReturns($transaction_id, $created_at, $branch_id = null)
    {
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }

        if (Session('softwareuser')) {
            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

            $userid = Session('softwareuser');
            $useritem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();

            $shopid = Session('softwareuser');
            $adminid = Softwareuser::Where('id', $shopid)
                ->pluck('admin_id')
                ->first();
        } elseif (Session('adminuser')) {
            $branch = $branch_id;

            $adminid = Session('adminuser');
            $useritem = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();
        }

        $count = DB::table('returnproducts')->count();

        $item = DB::table('returnproducts')
            ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
            ->select(DB::raw('returnproducts.*'))
            ->where('returnproducts.transaction_id', $transaction_id)
            ->where('returnproducts.created_at', $created_at)
            ->where('returnproducts.branch', $branch)
            ->get();

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if (Session('softwareuser')) {
            return view('billingdesk/returndetails', ['tax'=>$tax,'details' => $item, 'users' => $useritem, 'currency' => $currency]);
        } elseif (Session('adminuser')) {
            return view('/admin/returnreport_product', ['tax'=>$tax,'details' => $item, 'users' => $useritem, 'currency' => $currency, 'id' => $branch]);
        }
    }

    // inventory
    public function inventorydashBoard()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $branchid = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
  $item = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(DB::raw('products.box_count,products.quantity_enabled,products.box_enabled,products.box_sell_cost,products.status,products.product_name,products.productdetails,products.unit,products.buy_cost,products.image,products.product_code,products.barcode,products.selling_cost,products.id as id,products.vat as vat,categories.category_name,categories.id as category_id, categories.access as access,products.rate,products.purchase_vat, products.inclusive_rate, products.inclusive_vat_amount, products.inclusive_rate, products.inclusive_vat_amount'))
            ->where('branch', $branchid)
            //  ->where('products.user_id', $userid)
            ->paginate(10);
        //  ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();

        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
                        $shopdata = Branch::Where('id', $branch)->get();
        $xdetails = DB::table('categories')
            ->select(DB::raw('categories.category_name,categories.id as category_id,categories.access '))
            ->where('branch_id', $branch)
            ->where('access', 1)
            ->get();
        $xunit = DB::table('units')
            ->select(DB::raw('units.unit,units.id'))
            ->where('branch_id', $branchid)
            ->where('status', 1)
            ->get();
        if($branch==4 || $branch==2 || $branch==5){
         return view('/inventory/product_box', ['tax'=>$tax,'details' => $item, 'xdetails' => $xdetails, 'users' => $useritem, 'shopdatas' => $shopdata, 'xunit' => $xunit]);

        }else{
            
        return view('/inventory/inventorydashboard', ['tax'=>$tax,'details' => $item, 'xdetails' => $xdetails, 'users' => $useritem, 'shopdatas' => $shopdata, 'xunit' => $xunit]);
        }
    }

  public function productData(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'category_id.*' => 'required|',
            'productName.*' => 'required',
            'unit.*' => 'required',
            'buy_cost.*' => 'required',
            'selling_cost.*' => 'required',
            'vat.*' => 'required',
        ]);
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $product_code = rand(100000000, 200000000);

        if (!empty($request->productName)) {
            foreach ($request->productName as $key => $productName) {
                $existingProduct = Product::where('product_name', $productName)
                    ->where('branch', $branch)
                    ->first();
                if (!$existingProduct) {
                    $data = new Product();
                    $data->product_name = $productName;
                    // $data->productdetails = $request->productdetails[$key];
                    $data->unit = $request->unit[$key];
                    $data->selling_cost = $request->selling_cost[$key];
                    $data->buy_cost = $request->buy_cost[$key];
                    $data->user_id = Session('softwareuser');
                    $data->branch = $branch;
                    $data->category_id = $request->category_id[$key];
                    $data->vat = $request->vat[$key];
                    
                     if($branch==4 || $branch==2 || $branch==5){
                    $data->quantity_enabled = isset($request->quantity_enabled[$key]) ? 1 : 0;
                    $data->box_enabled = isset($request->box_enabled[$key]) ? 1 : 0;
                    
                    // Handle box options if box is enabled
                    if ($data->box_enabled) {
                        $data->box_count = $request->box_count[$key] ?? 1;
                    }
                     }

                    $data->rate = $request->rate[$key];
                    $data->purchase_vat = $request->purchase_vat[$key];

                    $data->inclusive_rate = $request->inclusive_rate[$key];
                    $data->inclusive_vat_amount = $request->inclusive_vat_amount[$key];

                    if (!empty($request->exist_barcode[$key])) {
                        $data->product_code = $request->exist_barcode[$key];
                        $data->barcode = $request->exist_barcode[$key];
                    } else {
                        $data->product_code = $product_code;
                        $data->barcode = $product_code;
                        ++$product_code;
                    }


                    if (!empty($request->image[$key])) {
                        $file = $request->image[$key];

                        if ($file->isValid()) {
                            \Log::info('File is found and valid.');

                            // Define the destination path relative to the 'public' directory
                            $destinationPath = public_path('images/logoimage');

                            // Create a unique filename
                            $fileName = time() . '_' . $file->getClientOriginalName();

                            // Move file to the destination path
                            $file->move($destinationPath, $fileName);

                            // Save the relative file path to the database (relative to the 'public' folder)
                            $data->image = 'images/logoimage/' . $fileName;
                        } else {
                            \Log::error('File is invalid.');
                        }
                    } else {
                        \Log::error('No file found in the request.');
                    }
                    $data->save();

                } else {
                    // Flash an error message
                    // return redirect('/inventorydashboard')->withErrors("Product '$productName' already exists for the given branch.");

                    // Collect the error message
                    $errorMessages[] = "Product '$productName' already exists for the given branch.";
                }
            }
        }
        if (!empty($request->pid)) {
            foreach ($request->pid as $key => $value) {
                $alreadyexistingProduct = Product::where('product_name', $request->pname[$key])
                    ->where('branch', $branch)
                    ->where('id', '<>', $value) // Exclude the current product being updated
                    ->first();

                if (!$alreadyexistingProduct) {
                    $dataupdate = Product::find($request->pid[$key]);
                    $dataupdate->selling_cost = $request->pselling_cost[$key];
                    $dataupdate->product_name = $request->pname[$key];
                    // $dataupdate->productdetails = $request->pdetails[$key];
                    $dataupdate->unit = $request->punit[$key];
                    $dataupdate->buy_cost = $request->pbuy_cost[$key];
                    $dataupdate->vat = $request->pvat[$key];
                    $dataupdate->category_id = $request->pcategory_id[$key];
                    
                     if($branch==4 || $branch==2 || $branch==5){
                    $dataupdate->quantity_enabled = isset($request->pquantity_enabled[$key]) ? 1 : 0;
                    $dataupdate->box_enabled = isset($request->pbox_enabled[$key]) ? 1 : 0;
                    
                    // Handle box options if box is enabled
                    if ($dataupdate->box_enabled) {
                        $dataupdate->box_count = $request->pbox_count[$key] ?? null;
                    }
                     }
                    $dataupdate->rate = $request->prate[$key];
                    $dataupdate->purchase_vat = $request->ppurchase_vat[$key];

                    $dataupdate->inclusive_rate = $request->pinclusive_rate[$key];
                    $dataupdate->inclusive_vat_amount = $request->pinclusive_vat_amount[$key];

                    // $dataupdate->save();
                    if (!empty($request->pexist_barcode[$key])) {
                        $dataupdate->product_code = $request->pexist_barcode[$key];
                        $dataupdate->barcode = $request->pexist_barcode[$key];
                        $dataupdate->save();
                    }
                    else if (!empty($request->image[$key])) {
                        $file = $request->image[$key];

                        if ($file->isValid()) {
                            \Log::info('File is found and valid.');

                            // Define the destination path relative to the 'public' directory
                            $destinationPath = public_path('images/logoimage');

                            // Create a unique filename
                            $fileName = time() . '_' . $file->getClientOriginalName();

                            // Move file to the destination path
                            $file->move($destinationPath, $fileName);

                            // Save the relative file path to the database (relative to the 'public' folder)
                            $dataupdate->image = 'images/logoimage/' . $fileName;
                        } else {
                            \Log::error('File is invalid.');
                        }
                        $dataupdate->save();

                    }
                     else {
                        $dataupdate->save();
                    }
                }
                else {
                    // Flash an error message
                    // return redirect('/inventorydashboard')->withErrors("Product '{$request->pname[$key]}' already exists for the given branch.");

                    // Collect the error message
                    $errorMessages[] = "Product '{$request->pname[$key]}' already exists for the given branch.";
                }
            }
        }

        if (!empty($errorMessages)) {
            // Redirect with all error messages
            return redirect('/inventorydashboard')->withErrors($errorMessages);
        }

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' added or edited products';
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return redirect('/inventorydashboard')->with('success', 'Product added successfully!');
    }

    public function changeStatus($id)
    {
        $productname = Product::where('id', $id)->pluck('product_name')->first();

        $plan = Product::find($id);
        if ($plan->status == '0') {
            $status = '1';
        } else {
            $status = '0';
        }
        $plan->status = $status;
        $plan->save();

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        if ($status == '1') {
            $text = 'enabled';
        } else {
            $text = 'disabled';
        }

        $user_type = 'websoftware';
        $message = $username.' '.$text.' '.$productname;
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return redirect('/inventorydashboard');
    }

    public function deleteProduct($id)
    {
        $productname = Product::where('id', $id)->pluck('product_name')->first();

        $plan = Product::find($id)->delete();

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' deleted product '.$productname;
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();
        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */
        return redirect('/inventorydashboard');
    }

    public function deleteCategory($id)
    {
        $delete = Category::find($id)->delete();
        $status = DB::table('products')
            ->where('category_id', $id)
            ->update(['category_id' => null]);

        return redirect('/listcategory');
    }

    public function deleteUnit($id)
    {
        $unitname = Unit::where('id', $id)->pluck('unit')->first();

        // $delete = Unit::find($id)->delete();
        // $status = DB::table('units')
        //     ->where('id', $id)
        //     ->update(['id' => NULL]);

        $plan = Unit::find($id);
        if ($plan->status == '1') {
            $status = '0';
            $text = 'disabled';

            $plan->status = $status;
            $plan->save();
        } elseif ($plan->status == '0') {
            $status = '1';
            $text = 'enabled';

            $plan->status = $status;
            $plan->save();
        }

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';

        $message = $username.' '.$text.' unit '.$unitname;

        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();
        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return redirect('/listcategory');
    }

    public function searchProduct($search_text)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $branchid = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $item = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(DB::raw('products.status,products.product_name,products.productdetails,products.unit,products.buy_cost,products.image,products.product_code,products.barcode,products.selling_cost,products.id as id,products.vat as vat,categories.category_name,categories.id as category_id, categories.access as access,products.rate as rate,products.purchase_vat as purchase_vat, products.inclusive_rate, products.inclusive_vat_amount, products.inclusive_rate, products.inclusive_vat_amount'))
            ->where('branch', $branchid)
            ->where('product_name', 'LIKE', '%'.$search_text.'%')
            ->get();

        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
                        $shopdata = Branch::Where('id', $branchid)->get();

        $xdetails = DB::table('categories')
            ->select(DB::raw('categories.category_name,categories.id as category_id, categories.access'))
            ->where('branch_id', $branchid)
            ->where('access', 1)
            ->get();

        $xunit = DB::table('units')
            ->select(DB::raw('units.unit,units.id'))
            ->where('branch_id', $branchid)
            ->where('status', 1)
            ->get();

        return view('/inventory/inventorySearch_NewSystem', ['tax'=>$tax,'details' => $item, 'users' => $useritem, 'shopdatas' => $shopdata, 'xdetails' => $xdetails, 'xunit' => $xunit]);
    }

    public function stockData()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $item = DB::table('products')
            ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
            ->select(DB::raw('products.product_name as product_name,products.stock as stock, SUM(stockdats.stock_num) as stock_num,products.id as id, products.remaining_stock as remaining_stock'))
            ->groupBy('products.id')
            ->where('products.branch', $branch)
            ->where('products.status', 1)
            ->orderBy('products.id')
            ->get();
        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
                        $shopdata = Branch::Where('id', $branch)->get();
        $purchasecount = StockDetail::Where('branch', $branch)
            ->Where('status', 1)
            ->distinct()->count('id');

        return view('/inventory/inventorystock', ['stocks' => $item, 'users' => $useritem, 'count' => $purchasecount, 'shopdatas' => $shopdata, 'userid' => $userid]);
    }

    public function newstockPurchases()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $item = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->where('stockdetails.branch', $branch)
            ->where('stockdetails.status', 1)
            ->get();
        $products = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->where('stockdetails.branch', $branch)
            ->whereDate('stockdetails.created_at', Carbon::today())
            ->get();
        $status = DB::table('stockdetails')
            ->where('branch', $branch)
            ->update(['status' => 0]);
        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
                               $shopdata = Branch::Where('id', $branch)->get();


        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

        return view('/inventory/newstockpurchases', ['newproducts' => $item, 'products' => $products, 'users' => $useritem, 'shopdatas' => $shopdata, 'currency' => $currency]);
    }

    public function listCategory()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $categories = DB::table('categories')
            ->where('branch_id', $branch)
            ->paginate(10);

        $units = DB::table('units')
            ->where('branch_id', $branch)
            ->paginate(10);

        return view('/inventory/listcategory', ['users' => $useritem, 'categories' => $categories, 'units' => $units]);
    }

    public function changecategoryAccess($id)
    {
        $categoryname = Category::where('id', $id)->pluck('category_name')->first();

        $plan = Category::find($id);
        if ($plan->access == '1') {
            $access = '0';
            $text = 'disabled';

            $plan->access = $access;
            $plan->save();
        } elseif ($plan->access == '0') {
            $access = '1';
            $text = 'enabled';

            $plan->access = $access;
            $plan->save();
        }
        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';

        $message = $username.' '.$text.' category '.$categoryname;
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();
        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return redirect('/listcategory');
    }

    public function createCategory(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $req->validate([
                'categoryname' => 'required',
            ]);

            $userid = Session('softwareuser');
            $categories = new Category();
            $categories->branch_id = $branch;
            $categories->user_id = $userid;
            $categories->category_name = $req->categoryname;

            if ($req->hasFile('image')) {
                $file = $req->file('image');
                if ($file->isValid()) {
                    \Log::info('File is found and valid.');

                    $destinationPath = 'images/logoimage'; // Relative path within public directory
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path($destinationPath), $fileName);

                    $categories->image = $destinationPath . '/' . $fileName;
                } else {
                    \Log::error('File upload failed. Error: ' . $file->getErrorMessage());
                }
            } else {
                \Log::error('No file uploaded in the request.');
            }

            $categories->save();

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' created category '.$req->categoryname;
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();
        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return redirect('/listcategory');
    }

    public function stockAdd(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $req->validate([
            'productName' => 'required',
        ]);
        foreach ($req->productName as $key => $productName) {
            $data = Product::find($req->id[$key]);
            $data->stock += $req->stock[$key];
            $data->remaining_stock += $req->stock[$key];
            $data->save();
        }

        foreach ($req->productName as $key => $productName) {
            $data = new Stockhistory();
            $data->product_id = $req->id[$key];
            $data->user_id = $req->uid;
            $data->quantity = $req->stock[$key];
            $data->remain_qantity = $req->stock[$key];
            $data->save();
        }

        foreach ($req->productName as $key => $productName) {
            $data = new AddStock();
            $data->product_id = $req->id[$key];
            $data->user_id = $req->uid;
            $data->quantity = $req->stock[$key];
            $data->save();
        }

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' added products stocks';

        $locationdata = (new otherService())->get_location($ip);
        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return redirect('/inventorystock');
    }

    public function purchaseData()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
                        $shopdata = Branch::Where('id', $branch)->get();

        $products = DB::table('products')
            ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
            ->select(DB::raw('products.quantity_enabled as quantity_enabled,products.box_enabled as box_enabled,products.box_count as box_count,products.product_name as product_name,products.id as id, products.unit as unit,products.buy_cost as buy_cost, products.selling_cost as selling_cost,products.rate as rate,products.purchase_vat as purchase_vat'))
            ->groupBy('products.id')
            ->where('products.branch', $branch)
            ->where('products.status', 1)
            ->orderBy('products.id')
            ->get();

        $suppliers = DB::table('suppliers')
            ->where('location', $branch)
            ->get();

        $categories = DB::table('categories')
            ->select(DB::raw('categories.category_name,categories.id as category_id,categories.access '))
            ->where('branch_id', $branch)
            ->where('access', 1)
            ->get();
        $units = DB::table('units')
            ->select(DB::raw('units.unit,units.id'))
            ->where('branch_id', $branch)
            ->where('status', 1)
            ->get();

        $receipt_nos = DB::table('stockdetails')
            ->where('branch', $branch)
            ->distinct('stockdetails.reciept_no')
            ->get(['reciept_no']);

        $page = 'purchase';
        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
        $listbank = DB::table('bank')
        ->select('id','bank_name', 'account_name','status','current_balance')
        ->where('status', 1)
        ->where('branch', $branch)
        ->get();
        
        if($branch==4 || $branch==2 || $branch==5){
        return view('/inventory/purchase_box', ['listbank' => $listbank,'tax'=>$tax,'users' => $useritem, 'products' => $products, 'shopdatas' => $shopdata, 'suppliers' => $suppliers, 'categories' => $categories, 'units' => $units, 'receipt_nos' => $receipt_nos, 'page' => $page]);

        }else{
            
        return view('/inventory/purchase', ['listbank' => $listbank,'tax'=>$tax,'users' => $useritem, 'products' => $products, 'shopdatas' => $shopdata, 'suppliers' => $suppliers, 'categories' => $categories, 'units' => $units, 'receipt_nos' => $receipt_nos, 'page' => $page]);
        }
    }

    public function StockDetails(Request $req)
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        if ($req->mode == 1) {
            $req->validate(
                [
                    'boxselect' => 'required|numeric',
                    'boxselectenter' => 'required|numeric',
                ],
                [
                    'boxselect.required' => '*Please Enter No. of Boxes',
                    'boxselectenter.required' => ' *Please Enter total no. of items in boxes',
                ]
            );
        } elseif ($req->mode == 2) {
            $req->validate(
                [
                    'dozenselect' => 'required|numeric',
                    'dozenselectenter' => 'required|numeric',
                ],
                [
                    'dozenselect.required' => '*Please Enter No. of Dozens',
                    'dozenselectenter.required' => ' *Please Enter total no. of items in dozen',
                ]
            );
        }

        $req->validate([
            'reciept_no' => 'required',
            'comment' => 'required',
            'mode' => 'required',
            'price' => 'required',
            'supplier' => 'required',
            'payment_mode' => 'required',
        ]);

        if (($req->payment_mode == 1) || ($req->payment_mode == 2 && $req->supp_id != null)) {
            $data = new StockDetail();
            $data->user_id = Session('softwareuser');
            $data->reciept_no = $req->reciept_no;
            $data->comment = $req->comment;
            $data->product = $req->product;
            $data->is_box_or_dozen = $req->mode;
            $data->unit = $req->unit;

            if ($req->mode == 1) {
                $data->box_dozen_count = $req->boxselect;
                $data->quantity = $req->boxselectenter;
                $data->remain_stock_quantity = $req->boxselectenter;
            } elseif ($req->mode == 2) {
                $data->box_dozen_count = $req->dozenselect;
                $data->quantity = $req->dozenselectenter;
                $data->remain_stock_quantity = $req->dozenselectenter;
            }

            $data->price = $req->price;
            $data->payment_mode = $req->payment_mode;
            // $data->shopname = $req->shopname;
            $data->supplier = $req->supplier;
            $data->supplier_id = $req->supp_id;

            $data->branch = $branch;
            if (!empty($req->file('camera'))) {
                $ext = $req->file('camera')->getClientOriginalExtension();
                $data->file = 'STOCK_DAT'.date('d-m-y_h-i-s').'.'.$ext;
                $data->save();
                $path = $req->file('camera')->storeAs('stockbills', $data->file);
            } else {
                $data->save();
            }

            if (!empty($req->product)) {
                $datatwo = Product::find($req->product);
                // $datatwo->stock = $datatwo->stock + $req->quantity;

                if ($req->mode == 1) {
                    $datatwo->stock += $req->boxselectenter;
                    $datatwo->remaining_stock += $req->boxselectenter;
                } elseif ($req->mode == 2) {
                    $datatwo->stock += $req->dozenselectenter;
                    $datatwo->remaining_stock += $req->dozenselectenter;
                }

                $datatwo->save();
            }

            if (!empty($req->product)) {
                $data = new Stockhistory();
                $data->user_id = Session('softwareuser');
                $data->product_id = $req->product;
                $data->receipt_no = $req->reciept_no;
                // $data->quantity = $req->quantity;

                if ($req->mode == 1) {
                    $data->quantity = $req->boxselectenter;
                    $data->remain_qantity = $req->boxselectenter;
                } elseif ($req->mode == 2) {
                    $data->quantity = $req->dozenselectenter;
                    $data->remain_qantity = $req->dozenselectenter;
                }

                $data->save();
            }
        } else {
            $req->validate(
                [
                    'supp_id' => 'required',
                ],
                [
                    'supp_id.required' => 'Supplier not exist. Create the supplier named '.$req->supplier.' to give credit',
                ]
            );
        }

        $dueamount = DB::table('supplier_credits')
            ->where('supplier_id', $req->supp_id)
            ->pluck('due_amt')
            ->first();

        if ($req->payment_mode == 2) {
            if ($req->supp_id != null) {
                $suppliercredit = DB::table('supplier_credits')
                    ->updateOrInsert(
                        ['supplier_id' => $req->supp_id],
                        ['due_amt' => $dueamount + $req->price],
                    );
            } else {
                $req->validate(
                    [
                        'supp_id' => 'required',
                    ],
                    [
                        'supp_id.required' => 'Supplier not exist. Create the supplier named '.$req->supplier.' to give credit',
                    ]
                );
            }
        }

        /* ------------------------------------------------------------------------- */

        $supply = Supplier::where('name', $req->supplier)
            ->where('location', $branch)
            ->first();

        if ($supply == null) {
            $user = new Supplier();
            $user->name = $req->supplier;
            $user->location = $branch;
            $user->softwareuser = Session('softwareuser');
            $user->save();
        }

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $productname = Product::where('id', $req->product)->pluck('product_name')->first();

        $user_type = 'websoftware';

        $message = $username.' purchased stock of '.$productname;
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();
        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return back()->with('success', 'Data uploaded successfully!');
    }

    public function download(Request $request, $file)
    {
        return response()->download(storage_path('app/stockbills/'.$file));
    }

    public function expensedownload(Request $request, $file)
    {
        return response()->download(storage_path('app/monthlybills/'.$file));
    }

    // analytics
    public function returnAnalytics()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $data = Returnproduct::select(
            DB::raw('transaction_id,created_at,SUM(price) as sum,SUM(vat) as vat')
        )
            ->groupBy('transaction_id')
            ->orderBy('created_at', 'ASC')
            ->get();

        $userid = Session('softwareuser');
        $count = DB::table('returnproducts')->distinct()->count('transaction_id');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $grand = Returnproduct::select(DB::raw('SUM(price+vat) as grand'))
            ->get();

        return view('/analytics/returnanalytics', ['products' => $data, 'users' => $item, 'counts' => $count, 'grands' => $grand]);
    }

    public function transactionAnalytics()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $data = Buyproduct::select(
            DB::raw('transaction_id,created_at,customer_name,SUM(price) as sum,SUM(vat) as vat,user_id')
        )
            ->groupBy('transaction_id')
            ->orderBy('created_at', 'ASC')
            ->get();
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $count = DB::table('buyproducts')->distinct()->count('transaction_id');
        $totalsum = Buyproduct::select(DB::raw('SUM(price) as totalsum'))
            ->get();
        $grand = Buyproduct::select(DB::raw('SUM(price+vat) as grand'))
            ->get();

        return view('/analytics/transactionanalytics', ['products' => $data, 'users' => $item, 'counts' => $count, 'totalsums' => $totalsum, 'grands' => $grand]);
    }

    // manager
    public function taskManagement()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $items = DB::table('softwareusers')
            ->get();

        return view('/manager/taskmanagement', ['users' => $item, 'datas' => $items]);
    }

    public function timeTracking()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $items = DB::table('softwareusers')
            ->get();

        return view('/manager/timetracking', ['users' => $item, 'datas' => $items]);
    }

    public function issueTracking()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $items = DB::table('softwareusers')
            ->get();

        return view('/manager/issuetracking', ['users' => $item, 'datas' => $items]);
    }

    public function employeeManagement()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $items = DB::table('softwareusers')
            ->get();

        return view('/manager/employeemanagement', ['users' => $item, 'datas' => $items]);
    }

    public function liveEmployees()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $employees = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->get();

        return view('/manager/liveemployees', ['employees' => $employees, 'users' => $item]);
    }

    public function staffCreation()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $designation = DB::table('roles')
            ->leftJoin('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->select(DB::raw('roles.role_name,roles.id'))
            ->where('user_id', $userid)
            // ->where('roles.id', '!=' ,11)
            ->distinct('roles.id')
            ->get();
        $locations = DB::table('branches')
            ->get();
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
              $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
                               $shopdata = Branch::Where('id', $branch)->get();


        return view('/hr/staffcreation', ['users' => $item, 'locations' => $locations, 'designations' => $designation, 'shopdatas' => $shopdata]);
    }

    public function salaryInfo()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $items = DB::table('softwareusers')
            ->get();
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
              $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
                                $shopdata = Branch::Where('id', $branch)->get();


        return view('/hr/salaryinfo', ['users' => $item, 'datas' => $items, 'shopdatas' => $shopdata]);
    }

    public function legal()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
              $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
  $shopdata = Branch::Where('id', $branch)->get();


        return view('/hr/legal', ['users' => $item, 'shopdatas' => $shopdata]);
    }

    public function companyExpenses()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $date = Carbon::now()->format('Y-m-d H:i:s');

        $branch = Accountantloc::leftJoin('branches', 'accountantlocs.location_id', '=', 'branches.id')
            ->where('accountantlocs.user_id', $userid)
            ->get();

        return view('/accountant/companyexpenses', ['users' => $item, 'start_date' => $date, 'branch' => $branch]);
    }

    public function salesReport()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $count = DB::table('accountantlocs')
            ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
            ->whereDate('buyproducts.created_at', Carbon::today())
            ->where('buyproducts.branch', $branch)
            ->distinct('buyproducts.transaction_id')
            ->count();
        $location = DB::table('accountantlocs')
            ->Join('branches', 'accountantlocs.location_id', '=', 'branches.id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/accountant/salesreport', ['users' => $item, 'count' => $count, 'locations' => $location]);
    }

    public function dateSales($date, $location_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        $data = DB::table('accountantlocs')
            ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
            ->select(DB::raw('
                buyproducts.transaction_id,
                buyproducts.vat_type,
                buyproducts.created_at,
                buyproducts.quantity,
                buyproducts.customer_name,
                SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                SUM(buyproducts.price) as price,
                SUM(buyproducts.vat_amount) as vat,
                SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
                +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount,
                        (
            SELECT SUM(credit_note.credit_note_amount)
            FROM credit_note
            WHERE credit_note.transaction_id = buyproducts.transaction_id
        ) AS total_credit_note
            '))
            ->groupBy('buyproducts.transaction_id')
            ->orderBy('buyproducts.created_at', 'ASC')
            ->whereDate('buyproducts.created_at', $date)
            ->where('accountantlocs.user_id', $userid)
            ->where('buyproducts.branch', $location_id)
            ->get();


        return view('/accountant/datesales', ['tax'=>$tax,'users' => $item, 'products' => $data, 'currency' => $currency]);
    }

    public function monthSales($month, $location_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        $data = DB::table('accountantlocs')
            ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
            ->select(DB::raw('buyproducts.transaction_id,
                buyproducts.created_at,
                buyproducts.quantity,
                buyproducts.customer_name,
                SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                SUM(buyproducts.price) as price,
                SUM(buyproducts.vat_amount) as vat,
                SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
                +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount,
        (
            SELECT SUM(credit_note.credit_note_amount)
            FROM credit_note
            WHERE credit_note.transaction_id = buyproducts.transaction_id
        ) AS total_credit_note
            '))
            ->groupBy('buyproducts.transaction_id')
            ->orderBy('buyproducts.created_at', 'ASC')
            ->whereMonth('buyproducts.created_at', $month)
            ->where('accountantlocs.user_id', $userid)
            ->where('buyproducts.branch', $location_id)
            ->get();

        return view('/accountant/monthsales', ['tax'=>$tax,'users' => $item, 'products' => $data, 'currency' => $currency]);
    }

    public function yearSales($year, $location_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        $data = DB::table('accountantlocs')
            ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
            ->select(DB::raw('
                buyproducts.transaction_id,
                buyproducts.created_at,
                buyproducts.customer_name,
                buyproducts.quantity,
                SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                SUM(buyproducts.price) as price,
                SUM(buyproducts.vat_amount) as vat,
                SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
                +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount,
                        (
            SELECT SUM(credit_note.credit_note_amount)
            FROM credit_note
            WHERE credit_note.transaction_id = buyproducts.transaction_id
        ) AS total_credit_note
            '))
            ->groupBy('buyproducts.transaction_id')
            ->orderBy('buyproducts.created_at', 'ASC')
            ->whereYear('buyproducts.created_at', $year)
            ->where('accountantlocs.user_id', $userid)
            ->where('buyproducts.branch', $location_id)
            ->get();

        return view('/accountant/yearsales', ['tax'=>$tax,'users' => $item, 'products' => $data, 'currency' => $currency]);
    }

    // sales report2
    public function salesReportfinal($transaction)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
            $data = Buyproduct::select([
                'product_name',
                'quantity',
                'created_at',
                'unit',
                'netrate',
                'totalamount_wo_discount',
                'total_amount',
                'vat_amount',
                'discount_amount',
                DB::raw('(SELECT SUM(credit_note.credit_note_amount)
                          FROM credit_note
                          WHERE credit_note.product_name = buyproducts.product_name
                          AND credit_note.transaction_id = buyproducts.transaction_id
                          LIMIT 1) AS credit_note_amount')
            ])
            ->where('transaction_id', $transaction)
            ->get();

        return view('/accountant/salesreportfinal', ['tax'=>$tax,'users' => $item, 'products' => $data, 'currency' => $currency]);
    }

    public function finalReport()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $date = Carbon::now()->format('d-M-Y');

        return view('/accountant/finalreport', ['users' => $item, 'date' => $date]);
    }

    // Accountant
    public function companyExpensessubmit(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'branch' => 'required',
            // 'comment' => 'required',
            'amount' => 'required',
            'start_date' => 'required',
        ]);

        $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();

        foreach ($request->comment as $key => $comment) {
            $data = new Accountexpense();
            $data->comment = $comment;
            $data->amount = $request->amount[$key];
            $data->date = $request->start_date[$key];
            $data->user_id = Session('softwareuser');
            $data->branch = $branch;
            if (!isset($request->image[$key]) || $request->image[$key] == '' || is_null($request->image[$key])) {
                $data->save();
            } else {
                $image = $request->image[$key];
                $ext = $image->getClientOriginalExtension();
                $name = 'MONTHLY_DATA'.date('d-m-y_h-i-s').'.'.$ext;
                $data->file = $name;
                $path = $image->storeAs('monthlybills', $name);
                $timeInSeconds = 1;
                sleep($timeInSeconds);
                $data->save();
            }
        }

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $branchname = Branch::where('id', $branch)->pluck('branchname')->first();

        $user_type = 'websoftware';
        $message = $username.' added company expenses of branch '.$branchname;
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */
        return redirect('/companyexpenses');
        // return $request;
    }

    public function companyExpenseshistory(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $date = Carbon::now()->format('Y-m-d');

        $branch = Accountantloc::leftJoin('branches', 'accountantlocs.location_id', '=', 'branches.id')
            ->where('accountantlocs.user_id', $userid)
            ->get();

        $expenses = DB::table('accountexpenses')
            ->leftJoin('branches', 'accountexpenses.branch', '=', 'branches.id')
            ->select(DB::raw('accountexpenses.details,accountexpenses.amount,accountexpenses.date,accountexpenses.branch,accountexpenses.user_id,accountexpenses.file,accountexpenses.created_at, branches.branchname'))
            ->where('accountexpenses.user_id', $userid)
            ->where('accountexpenses.date', $date)
            ->get();

        return view('/accountant/expenseshistory', ['users' => $item, 'expenses' => $expenses, 'branch' => $branch, 'currency' => $currency]);
    }

    public function fetchData($id)
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $item = Product::select(DB::raw('*'))
            ->where('branch', $branch)
            ->where('category_id', $id)
            ->get();
        if ($id == 'all') {
            $item = Product::select(DB::raw('*'))
                ->where('branch', $branch)
                ->get();
        }

        return response()->json([
            'categories' => $item,
        ]);
    }

    public function addFundCredit(Request $req)
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $fund = new Fundhistory();
        $fund->username = $req->input('fundusername');
        $fund->amount = $req->input('addedfund');
        $fund->credituser_id = $req->input('creditid');
        $fund->due = $req->input('due');
        $fund->user_id = Session('softwareuser');
        $fund->location = $branch;
        $fund->save();

        return redirect('/dashboardv2');
    }

    public function addFundCredit2(Request $req)
    {
        // $req->validate([
        //     'addedfund' => 'required',
        // ]);

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        if ($req->input('addedfund')) {
            $fund = new Fundhistory();
            $fund->username = $req->input('fundusername');
            $fund->amount = $req->input('addedfund');
            $fund->credituser_id = $req->input('creditid');
            $fund->due = $req->input('due');
            $fund->user_id = Session('softwareuser');
            $fund->location = $branch;
            $fund->trans_id = $req->input('transaction_id');
            $fund->product_id = $req->input('product_dropdown');
            $fund->credit_note = $req->input('creditnote');
            $fund->note = $req->input('note');
            $fund->payment_type = $req->input('credit_payment_type');
            $fund->cheque_number = $req->input('cheque_no');
            $fund->depositing_date = $req->input('cheque_date');
            $fund->reference_number = $req->input('bank_reference_no');
            $fund->bank_id = $req->input('bank_name');
            $fund->account_name =$req->input('account_name');
            $fund->save();
        }

        $creditcollected = DB::table('creditsummaries')
            ->where('credituser_id', $req->creditid)
            ->pluck('collected_amount')
            ->first();

        $credit_note_collected = DB::table('creditsummaries')
            ->where('credituser_id', $req->creditid)
            ->pluck('creditnote')
            ->first();

        $livecollected = $req->input('addedfund') ?? null;
        $live_creditnote_collected = $req->input('creditnote') ?? null;

        $totalcreditcollected = ($creditcollected + $livecollected);

        $total_credit_note = ($credit_note_collected + $live_creditnote_collected);

        $creditsummaries = DB::table('creditsummaries')
            ->updateOrInsert(
                ['credituser_id' => $req->input('creditid')],
                ['collected_amount' => $totalcreditcollected, 'creditnote' => $total_credit_note],
            );

        // new transaction_creditnote table

        $credit_username = DB::table('creditusers')
            ->where('id', $req->input('creditid'))
            ->where('location', $branch)
            ->pluck('name')
            ->first();

        $lastTransaction = DB::table('credit_transactions')
            ->where('credituser_id', $req->input('creditid'))
            ->where('location', $branch)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();


            $credit_limit = DB::table('creditusers')
            ->where('id', $req->input('creditid'))
            ->where('location', $branch)
            ->pluck('current_lamount')
            ->first();


        $lastSameTransaction = DB::table('credit_transactions')
            ->where('credituser_id', $req->input('creditid'))
            ->where('location', $branch)
            ->where('transaction_id', $req->input('transaction_id'))  // Only get the last record with the same transaction ID
            ->orderBy('created_at', 'desc')
            ->first();


        $previous_due = $lastSameTransaction->balance_due ?? 0;  // Get the 'due' of the last transaction with the same transaction ID

            $credit_lose = $lastTransaction->credit_lose ?? 0;

            $credit_deducted = $credit_lose;

        $updated_balance = $lastTransaction->updated_balance ?? 0;
        $previous_invoice_due = $lastSameTransaction->balance_due ?? 0;

        $new_due = $updated_balance;
        $collected_amount = $req->input('addedfund') ?? 0;
        $credit_note_collected = $req->input('creditnote') ?? 0;

        $transaction_id = $req->input('transaction_id');
        if ($lastTransaction === null) {
            // No previous transaction exists, so you can set the new invoice_due accordingly
            $new_invoice_due = $previous_due - $collected_amount;
        } else {
            // Check if the last transaction ID matches the current transaction ID
            if ($lastTransaction->transaction_id !== $transaction_id) {
                // Reset invoice_due for a new transaction
                $new_invoice_due = $previous_due - $collected_amount; // New invoice starts from last due
            } else {
                // Continue reducing the invoice_due for the current transaction
                $new_invoice_due = $previous_invoice_due - $collected_amount;
            }
        }



        $new_updated_bal = $new_due - $livecollected - $live_creditnote_collected;

        $credit_trans = new CreditTransaction();
        $credit_trans->credituser_id = $req->input('creditid');
        $credit_trans->credit_username = $credit_username;
        $credit_trans->user_id = Session('softwareuser');
        $credit_trans->location = $branch;
        $credit_trans->due = $new_due;
        $credit_trans->collected_amount = $req->input('addedfund');
        $credit_trans->transaction_id = $req->input('transaction_id');
        $credit_trans->product_id = $req->input('product_dropdown');
        $credit_trans->credit_note = $req->input('creditnote');
        $credit_trans->note = $req->input('note');
        $credit_trans->updated_balance = $new_updated_bal;
        $credit_trans->balance_due = $new_invoice_due; // Add the new invoice_due
        $credit_trans->transfer_date =$req->input('bank_transfer_date');

        if ($req->input('addedfund') > 0) {
            if ($credit_deducted > 0) {
                $amount_to_recover = min($credit_deducted, $req->input('addedfund'));

                // Update the current limit (credit limit)
                $new_credit_limit = $credit_limit + $amount_to_recover;

                // Save updated credit limit back to the database
                DB::table('creditusers')
                    ->where('id', $req->input('creditid'))
                    ->where('location', $branch)
                    ->update(['current_lamount' => $new_credit_limit]);

                // Update credit_lose for the transaction
                $credit_trans->credit_lose = $credit_deducted - $amount_to_recover;
            } else {
                // If there are no credit losses to recover
                $credit_trans->credit_lose = 0;
            }
        } else {
            // Handle the case where addedfund is not greater than 0
            $credit_trans->credit_lose = $credit_deducted; // Retain current credit_lose
        }
        if (($req->input('addedfund') != '' || $req->input('addedfund') != null) && ($req->input('creditnote') == '' || $req->input('creditnote') == null)) {
            $credit_trans->comment = 'Payment Received';
        } elseif (($req->input('addedfund') == '' || $req->input('addedfund') == null) && ($req->input('creditnote') != '' || $req->input('creditnote') != null)) {
            $credit_trans->comment = 'Credit Note';
        } elseif (($req->input('addedfund') != '' || $req->input('addedfund') != null) && ($req->input('creditnote') != '' || $req->input('creditnote') != null)) {
            $credit_trans->comment = 'Payment & Credit Note';
        }
        $credit_trans->payment_type = $req->input('credit_payment_type');
        $credit_trans->cheque_number = $req->input('cheque_no');
        $credit_trans->depositing_date = $req->input('cheque_date');
        $credit_trans->reference_number = $req->input('bank_reference_no');
        $credit_trans->bank_id = $req->input('bank_name');
        $credit_trans->account_name =$req->input('account_name');
        $credit_trans->save();
        if ($req->bank_name && $req->account_name) {
            $current_balance = DB::table('bank')
                ->where('id', $req->bank_name)
                ->where('account_name', $req->account_name)
                ->pluck('current_balance')
                ->first();

            $new_balance = $current_balance + $req->addedfund;
            $userid = Session('softwareuser');

            $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

            DB::table('bank')
                ->where('id', $req->bank_name)
                ->where('account_name', $req->account_name)
                ->update(['current_balance' => $new_balance]);

            $bank_history = new Bankhistory();
            $bank_history->transaction_id =$req->input('transaction_id');
            $bank_history->user_id = Session('softwareuser');
            $bank_history->bank_id = $req->bank_name;
            $bank_history->account_name = $req->account_name;
            $bank_history->branch = $branch_id;
            $bank_history->detail = 'Amount recievable';
            $bank_history->party = $req->fundusername;
            $bank_history->dr_cr = 'Credit';
            $bank_history->date = $req->bank_transfer_date ?? now();
            $bank_history->ref_no = $req->bank_ref_no;
            $bank_history->amount = $req->addedfund;
            $bank_history->save();
        }




        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' added collection payment to credit user '.$req->input('fundusername');
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();
        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return redirect('/addfunds');
    }

    public function getTransactionajax()
    {
        $count = DB::table('buyproducts')
            ->distinct()
            ->count('transaction_id');

        return response()->json([
            'transaction_id' => $count,
        ]);
    }

    public function getHistory($userid)
    {
        $id = $userid;

        $due = DB::table('creditsummaries')
            ->select(DB::raw('due_amount'))
            ->where('credituser_id', $id)
            ->pluck('due_amount')
            ->first();

        $paid = DB::table('creditsummaries')
            ->select(DB::raw('collected_amount'))
            ->where('credituser_id', $id)
            ->pluck('collected_amount')
            ->first();

        $crediit_note = DB::table('creditsummaries')
            ->select(DB::raw('creditnote'))
            ->where('credituser_id', $id)
            ->pluck('creditnote')
            ->first();

        $due = $due - $paid - $crediit_note;

        $trn_number = DB::table('creditusers')->where('id', $id)->pluck('trn_number')->first();
        $phone = DB::table('creditusers')->where('id', $id)->pluck('phone')->first();
        $email = DB::table('creditusers')->where('id', $id)->pluck('email')->first();

        $full_name = DB::table('creditusers')->where('id', $id)->pluck('name')->first();

        $trans_ids = DB::table('buyproducts')
            ->where('payment_type', 3)
            ->where('credit_user_id', $id)
            ->distinct('transaction_id')
            ->pluck('transaction_id');

        return response()->json([
            'creditid' => $id,
            'due' => $due,
            'trn_number' => $trn_number,
            'phone' => $phone,
            'email' => $email,
            'trans_ids' => $trans_ids,
            'full_name' => $full_name,
        ]);
    }

    public function accountantSalesview(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        // perday
        if ($req->view_type == 1) {
            if (is_null($req->start_date) || is_null($req->end_date)) {
                $location_id = $req->location_id;
                $fromdate = '0';
                $todate = '0';
                $viewtype = $req->view_type;

                return redirect('/salesreportperday/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            } else {
                $location_id = $req->location_id;
                $fromdate = $req->start_date;
                $todate = $req->end_date;
                $viewtype = $req->view_type;

                return redirect('/salesreportperday/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            }
        }
        // permonth
        if ($req->view_type == 2) {
            if (is_null($req->start_date) || is_null($req->end_date)) {
                $location_id = $req->location_id;
                $fromdate = '0';
                $todate = '0';
                $viewtype = $req->view_type;

                return redirect('/salesreportpermonth/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            } else {
                $location_id = $req->location_id;
                $fromdate = $req->start_date;
                $todate = $req->end_date;
                $viewtype = $req->view_type;

                return redirect('/salesreportpermonth/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            }
        }
        // peryear
        if ($req->view_type == 3) {
            if (is_null($req->start_date) || is_null($req->end_date)) {
                $location_id = $req->location_id;
                $fromdate = '0';
                $todate = '0';
                $viewtype = $req->view_type;

                return redirect('/salesreportperyear/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            } else {
                $location_id = $req->location_id;
                $fromdate = $req->start_date;
                $todate = $req->end_date;
                $viewtype = $req->view_type;

                return redirect('/salesreportperyear/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            }
        }

        return redirect('salesreport');
    }

    public function accountantperdayview($viewtype, $location_id, $fromdate, $todate)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if ($fromdate == '0' || $todate == '0') {
            $data = DB::table('accountantlocs')
                ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                ->select(DB::raw("
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(buyproducts.price) as price,
                    buyproducts.vat_type,
                    buyproducts.quantity,
                    DATE(buyproducts.created_at) as date,
                    COUNT(distinct(buyproducts.transaction_id)) as num,
                    SUM(buyproducts.vat_amount) as vat,
                    SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,
                     (
                    SELECT SUM(credit_note.credit_note_amount)
                    FROM credit_note
                    WHERE credit_note.branch = buyproducts.branch
                    AND DATE(credit_note.created_at) = DATE(NOW())
                ) AS total_credit_note
                "))
                ->groupBy('date')
                ->where('buyproducts.branch', $location_id)
                ->whereDate('buyproducts.created_at', Carbon::today())
                ->where('accountantlocs.user_id', $userid)
                ->get();
            $location = $location_id;

            return view('/accountant/salesreportperday', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate == $todate && $fromdate != '0' && $todate != '0') {
            $data = DB::table('accountantlocs')
                ->join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                ->select(DB::raw("
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(buyproducts.price) as price,
                    buyproducts.vat_type,
                    buyproducts.quantity,
                    DATE(buyproducts.created_at) as date,
                    COUNT(distinct(buyproducts.transaction_id)) as num,
                    SUM(buyproducts.vat_amount) as vat,
                    SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                   ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,
                    (
                    SELECT SUM(credit_note.credit_note_amount)
                    FROM credit_note
                    WHERE credit_note.branch = buyproducts.branch
                    AND DATE(credit_note.created_at) = ?
                        ) AS total_credit_note
                "))
                ->groupBy('date')
                ->where('buyproducts.branch', $location_id)
                ->whereDate('buyproducts.created_at', $fromdate)
                ->where('accountantlocs.user_id', $userid)
                ->addBinding([$fromdate], 'select')
                ->get();

            $location = $location_id;

            return view('/accountant/salesreportperday', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate != $todate) {
            $data = DB::table('accountantlocs')
                ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                ->select(DB::raw("
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(buyproducts.price) as price,
                    buyproducts.vat_type,
                    buyproducts.quantity,
                    DATE(buyproducts.created_at) as date,
                    COUNT(distinct(buyproducts.transaction_id)) as num,
                    SUM(buyproducts.vat_amount) as vat,
                    SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,
            (
            SELECT SUM(credit_note.credit_note_amount)
            FROM credit_note
            WHERE credit_note.branch = buyproducts.branch
            AND DATE(credit_note.created_at) = DATE(buyproducts.created_at)
        ) AS total_credit_note
                "))
                ->groupBy('date')
                ->where('buyproducts.branch', $location_id)
                ->whereBetween('buyproducts.created_at', [$fromdate.' 00:00:00', $todate.' 23:59:59'])
                ->where('accountantlocs.user_id', $userid)
                ->get();
            $location = $location_id;


            return view('/accountant/salesreportperday', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        }
    }

    public function accountantpermonthview($viewtype, $location_id, $fromdate, $todate)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if ($fromdate == '0' || $todate == '0') {
            $data = DB::table('accountantlocs')
                ->join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                ->select(
                    DB::raw("
                        SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                        SUM(buyproducts.price) as price,
                        buyproducts.vat_type,
                        MONTHNAME(buyproducts.created_at) as date,
                        MONTH(buyproducts.created_at) as month_id,
                        COUNT(DISTINCT buyproducts.transaction_id) as num,
                        SUM(buyproducts.vat_amount) as vat,
                        SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,
                  (
                    SELECT SUM(credit_note.credit_note_amount)
                    FROM credit_note
                    WHERE credit_note.branch = buyproducts.branch
                AND MONTH(credit_note.created_at) = MONTH(CURRENT_DATE)
                ) AS total_credit_note
                    ")
                )
                ->groupBy('date')
                ->where('buyproducts.branch', $location_id)
                ->whereMonth('buyproducts.created_at', Carbon::today())
                ->where('accountantlocs.user_id', $userid)
                ->get();


            $location = $location_id;

            return view('/accountant/salesreportpermonth', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate == $todate && $fromdate != '0' && $todate != '0') {
            $date = Carbon::createFromFormat('Y-m-d', $fromdate);
            $data = DB::table('accountantlocs')
                ->join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                ->select(
                    DB::raw("
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(buyproducts.price) as price,
                    buyproducts.vat_type,
                    MONTHNAME(buyproducts.created_at) as date,
                    MONTH(buyproducts.created_at) as month_id,
                    COUNT(DISTINCT buyproducts.transaction_id) as num,
                    SUM(buyproducts.vat_amount) as vat,
                    SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                   ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,
                  (
                SELECT SUM(credit_note.credit_note_amount)
                FROM credit_note
                WHERE credit_note.branch = buyproducts.branch
                AND MONTH(credit_note.created_at) = ?
            ) AS total_credit_note
                ")
                )
                ->groupBy('date')
                ->where('buyproducts.branch', $location_id)
                ->whereMonth('buyproducts.created_at', $date)
                ->where('accountantlocs.user_id', $userid)
                ->addBinding([$date->month], 'select') // Add the month as binding
                ->get();


            $location = $location_id;

            return view('/accountant/salesreportpermonth', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate != $todate) {
            $data = DB::table('accountantlocs')
                ->join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                ->select(
                    DB::raw("
                        SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                        SUM(buyproducts.price) as price,
                        buyproducts.vat_type,
                        MONTHNAME(buyproducts.created_at) as date,
                        MONTH(buyproducts.created_at) as month_id,
                        COUNT(DISTINCT buyproducts.transaction_id) as num,
                        SUM(buyproducts.vat_amount) as vat,
                        SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                   ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,
                (
                SELECT SUM(credit_note.credit_note_amount)
                FROM credit_note
                WHERE credit_note.branch = buyproducts.branch
                AND MONTH(credit_note.created_at) = MONTH(buyproducts.created_at)
            ) AS total_credit_note
                    ")
                )
                ->groupBy('date')
                ->where('buyproducts.branch', $location_id)
                ->whereBetween('buyproducts.created_at', [$fromdate . ' 00:00:00', $todate . ' 23:59:59'])
                ->where('accountantlocs.user_id', $userid)
                ->get();


            $location = $location_id;

            return view('/accountant/salesreportpermonth', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        }
    }

    public function accountantperyearview($viewtype, $location_id, $fromdate, $todate)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if ($fromdate == '0' || $todate == '0') {
            $data = DB::table('accountantlocs')
                ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                ->select(DB::raw("
                SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                SUM(buyproducts.price) as price,
                buyproducts.vat_type,
                YEAR(buyproducts.created_at) as date,
                COUNT(distinct(buyproducts.transaction_id)) as num,
                SUM(buyproducts.vat_amount) as vat,
                SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
               ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,
                                                                   (
                    SELECT SUM(credit_note.credit_note_amount)
                    FROM credit_note
                    WHERE credit_note.branch = buyproducts.branch
                AND YEAR(credit_note.created_at) = YEAR(CURRENT_DATE)
                ) AS total_credit_note
                "))
                ->groupBy('date')
                ->where('buyproducts.branch', $location_id)
                ->whereYear('buyproducts.created_at', Carbon::today())
                ->where('accountantlocs.user_id', $userid)
                ->get();

            $location = $location_id;

            return view('/accountant/salesreportperyear', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate == $todate && $fromdate != '0' && $todate != '0') {
            $data = DB::table('accountantlocs')
                ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                ->select(DB::raw("
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(buyproducts.price) as price,
                    buyproducts.vat_type,
                    YEAR(buyproducts.created_at) as date,
                    COUNT(distinct(buyproducts.transaction_id)) as num,
                    SUM(buyproducts.vat_amount) as vat,
                    SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,
        (
            SELECT SUM(credit_note.credit_note_amount)
            FROM credit_note
            WHERE credit_note.branch = buyproducts.branch
            AND YEAR(credit_note.created_at) = ?
        ) AS total_credit_note
                "))
                ->groupBy('date')
                ->where('buyproducts.branch', $location_id)
                ->whereYear('buyproducts.created_at', $fromdate)
                ->where('accountantlocs.user_id', $userid)
                ->addBinding([$fromdate], 'select') // Add the month as binding
                ->get();
            $location = $location_id;

            return view('/accountant/salesreportperyear', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate != $todate) {
            $data = DB::table('accountantlocs')
                ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                ->select(DB::raw("
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(buyproducts.price) as price,
                    buyproducts.vat_type,
                    YEAR(buyproducts.created_at) as date,
                    COUNT(distinct(buyproducts.transaction_id)) as num,
                    SUM(buyproducts.vat_amount) as vat,
                    SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                   ROUND(
                        SUM(
                            COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)
                            + COALESCE(buyproducts.total_amount * (buyproducts.total_discount_percent / 100), 0)
                        ), 2
                    ) AS discount_amount,
            (
                SELECT SUM(credit_note.credit_note_amount)
                FROM credit_note
                WHERE credit_note.branch = buyproducts.branch
                AND YEAR(credit_note.created_at) = YEAR(buyproducts.created_at)
            ) AS total_credit_note
                "))
                ->groupBy('date')
                ->where('buyproducts.branch', $location_id)
                ->whereBetween('buyproducts.created_at', [$fromdate.' 00:00:00', $todate.' 23:59:59'])
                ->where('accountantlocs.user_id', $userid)
                ->get();
            $location = $location_id;

            return view('/accountant/salesreportperyear', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        }
    }

    public function accountantReturnview(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        // perday
        if ($req->view_type == 1) {
            if (is_null($req->start_date) || is_null($req->end_date)) {
                $location_id = $req->location_id;
                $fromdate = '0';
                $todate = '0';
                $viewtype = $req->view_type;

                return redirect('/returnreportperday/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            } else {
                $location_id = $req->location_id;
                $fromdate = $req->start_date;
                $todate = $req->end_date;
                $viewtype = $req->view_type;

                return redirect('/returnreportperday/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            }
        }
        // permonth
        if ($req->view_type == 2) {
            if (is_null($req->start_date) || is_null($req->end_date)) {
                $location_id = $req->location_id;
                $fromdate = '0';
                $todate = '0';
                $viewtype = $req->view_type;

                return redirect('/returnreportpermonth/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            } else {
                $location_id = $req->location_id;
                $fromdate = $req->start_date;
                $todate = $req->end_date;
                $viewtype = $req->view_type;

                return redirect('/returnreportpermonth/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            }
        }
        // peryear
        if ($req->view_type == 3) {
            if (is_null($req->start_date) || is_null($req->end_date)) {
                $location_id = $req->location_id;
                $fromdate = '0';
                $todate = '0';
                $viewtype = $req->view_type;

                return redirect('/returnreportperyear/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            } else {
                $location_id = $req->location_id;
                $fromdate = $req->start_date;
                $todate = $req->end_date;
                $viewtype = $req->view_type;

                return redirect('/returnreportperyear/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            }
        }

        return redirect('accountreturnreport');
    }

    public function accountantreturnperdayview($viewtype, $location_id, $fromdate, $todate)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if ($fromdate == '0' || $todate == '0') {
            $data = DB::table('accountantlocs')
                ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                ->select(DB::raw('
                    SUM(COALESCE(grand_total, 0)) as sum,
                    SUM(returnproducts.price) as price,
                    returnproducts.vat_type,
                    DATE(returnproducts.created_at) as date,
                    COUNT(distinct(returnproducts.id)) as num,
                    SUM(returnproducts.vat_amount) as vat,
                    SUM(returnproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount
                '))
                ->groupBy('date')
                ->where('returnproducts.branch', $location_id)
                ->whereDate('returnproducts.created_at', Carbon::today())
                ->where('accountantlocs.user_id', $userid)
                ->get();
            $location = $location_id;

            return view('/accountant/returnreportperday', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate == $todate && $fromdate != '0' && $todate != '0') {
            $data = DB::table('accountantlocs')
                ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                ->select(DB::raw('
                    SUM(COALESCE(grand_total, 0)) as sum,
                    SUM(returnproducts.price) as price,
                    returnproducts.vat_type,
                    DATE(returnproducts.created_at) as date,
                    COUNT(distinct(returnproducts.id)) as num,
                    SUM(returnproducts.vat_amount) as vat,
                    SUM(returnproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount
                '))
                ->groupBy('date')
                ->where('returnproducts.branch', $location_id)
                ->whereDate('returnproducts.created_at', $fromdate)
                ->where('accountantlocs.user_id', $userid)
                ->get();
            $location = $location_id;

            return view('/accountant/returnreportperday', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate != $todate) {
            $data = DB::table('accountantlocs')
                ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                ->select(DB::raw('
                    SUM(COALESCE(grand_total, 0)) as sum,
                    SUM(returnproducts.price) as price,
                    returnproducts.vat_type,
                    DATE(returnproducts.created_at) as date,
                    COUNT(distinct(returnproducts.id)) as num,
                    SUM(returnproducts.vat_amount) as vat,
                    SUM(returnproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount
                '))
                ->groupBy('date')
                ->where('returnproducts.branch', $location_id)
                ->whereBetween('returnproducts.created_at', [$fromdate.' 00:00:00', $todate.' 23:59:59'])
                ->where('accountantlocs.user_id', $userid)
                ->get();
            $location = $location_id;

            return view('/accountant/returnreportperday', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        }
    }

    public function accountantreturnpermonthview($viewtype, $location_id, $fromdate, $todate)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if ($fromdate == '0' || $todate == '0') {
            $data = DB::table('accountantlocs')
                ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                ->select(DB::raw('
                    SUM(COALESCE(grand_total, 0)) as sum,
                    SUM(returnproducts.price) as price,
                    returnproducts.vat_type,
                    MONTHNAME(returnproducts.created_at) as date,
                    MONTH(returnproducts.created_at) as month_id,
                    COUNT(distinct(returnproducts.id)) as num,
                    SUM(returnproducts.vat_amount) as vat,
                    SUM(returnproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount
                '))
                ->groupBy('date')
                ->where('returnproducts.branch', $location_id)
                ->whereMonth('returnproducts.created_at', Carbon::today())
                ->where('accountantlocs.user_id', $userid)
                ->get();
            $location = $location_id;

            return view('/accountant/returnreportpermonth', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate == $todate && $fromdate != '0' && $todate != '0') {
            $date = Carbon::createFromFormat('Y-m-d', $fromdate);
            $data = DB::table('accountantlocs')
                ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                ->select(DB::raw('
                    SUM(COALESCE(grand_total, 0)) as sum,
                    SUM(returnproducts.price) as price,
                    returnproducts.vat_type,
                    MONTHNAME(returnproducts.created_at) as date,
                    MONTH(returnproducts.created_at) as month_id,
                    COUNT(distinct(returnproducts.id)) as num,
                    SUM(returnproducts.vat_amount) as vat,
                    SUM(returnproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount
                '))
                ->groupBy('date')
                ->where('returnproducts.branch', $location_id)
                ->whereMonth('returnproducts.created_at', $date)
                ->where('accountantlocs.user_id', $userid)
                ->get();
            $location = $location_id;

            return view('/accountant/returnreportpermonth', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate != $todate) {
            $data = DB::table('accountantlocs')
                ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                ->select(DB::raw('
                    SUM(COALESCE(grand_total, 0)) as sum,
                    SUM(returnproducts.price) as price,
                    returnproducts.vat_type,
                    MONTHNAME(returnproducts.created_at) as date,
                    MONTH(returnproducts.created_at) as month_id,
                    COUNT(distinct(returnproducts.id)) as num,
                    SUM(returnproducts.vat_amount) as vat,
                    SUM(returnproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount
               '))
                ->groupBy('date')
                ->where('returnproducts.branch', $location_id)
                ->whereBetween('returnproducts.created_at', [$fromdate.' 00:00:00', $todate.' 23:59:59'])
                ->where('accountantlocs.user_id', $userid)
                ->get();
            $location = $location_id;

            return view('/accountant/returnreportpermonth', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        }
    }

    public function accountantreturnperyearview($viewtype, $location_id, $fromdate, $todate)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if ($fromdate == '0' || $todate == '0') {
            $data = DB::table('accountantlocs')
                ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                ->select(DB::raw('
                    SUM(COALESCE(grand_total, 0)) as sum,
                    SUM(returnproducts.price) as price,
                    returnproducts.vat_type,
                    YEAR(returnproducts.created_at) as date,
                    COUNT(distinct(returnproducts.id)) as num,
                    SUM(returnproducts.vat_amount) as vat,
                    SUM(returnproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount
                '))
                ->groupBy('date')
                ->where('returnproducts.branch', $location_id)
                ->whereYear('returnproducts.created_at', Carbon::today())
                ->where('accountantlocs.user_id', $userid)
                ->get();
            $location = $location_id;

            return view('/accountant/returnreportperyear', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate == $todate && $fromdate != '0' && $todate != '0') {
            $data = DB::table('accountantlocs')
                ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                ->select(DB::raw('
                    SUM(COALESCE(grand_total, 0)) as sum,
                    SUM(returnproducts.price) as price,
                    returnproducts.vat_type,
                    YEAR(returnproducts.created_at) as date,
                    COUNT(distinct(returnproducts.id)) as num,
                    SUM(returnproducts.vat_amount) as vat,
                    SUM(returnproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount
               '))
                ->groupBy('date')
                ->where('returnproducts.branch', $location_id)
                ->whereYear('returnproducts.created_at', Carbon::today())
                ->where('accountantlocs.user_id', $userid)
                ->get();
            $location = $location_id;

            return view('/accountant/returnreportperyear', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate != $todate) {
            $data = DB::table('accountantlocs')
                ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
                ->select(DB::raw('
                    SUM(COALESCE(grand_total, 0)) as sum,
                    SUM(returnproducts.price) as price,
                    returnproducts.vat_type,
                    YEAR(returnproducts.created_at) as date,
                    COUNT(distinct(returnproducts.id)) as num,
                    SUM(returnproducts.vat_amount) as vat,
                    SUM(returnproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount
                '))
                ->groupBy('date')
                ->where('returnproducts.branch', $location_id)
                ->whereBetween('returnproducts.created_at', [$fromdate.' 00:00:00', $todate.' 23:59:59'])
                ->where('accountantlocs.user_id', $userid)
                ->get();
            $location = $location_id;

            return view('/accountant/returnreportperyear', ['tax'=>$tax,'users' => $item, 'productdays' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        }
    }

    public function accountreturnReport()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $data = Returnproduct::select(DB::raw('transaction_id,created_at,SUM(price) as sum,SUM(vat) as vat'))
            ->groupBy('transaction_id')
            ->orderBy('created_at', 'ASC')
            ->where('branch', $branch)
            ->get();
        $count = DB::table('accountantlocs')
            ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
            ->whereDate('returnproducts.created_at', Carbon::today())
            ->where('returnproducts.branch', $branch)
            ->distinct('returnproducts.id')
            ->count();
        $location = DB::table('accountantlocs')
            ->Join('branches', 'accountantlocs.location_id', '=', 'branches.id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/accountant/accountantreturnreport', ['users' => $item, 'products' => $data, 'locations' => $location, 'count' => $count]);
    }

    public function returnReportfinal($transaction)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $return = Returnproduct::select(DB::raw('*'))
            ->where('transaction_id', $transaction)
            ->get();

        return view('/accountant/accountantreturnreportfinal', ['users' => $item, 'returns' => $return]);
    }

    public function dateReturn($date, $location_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        $data = DB::table('accountantlocs')
            ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
            ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
            ->select(DB::raw('returnproducts.transaction_id,
                returnproducts.created_at,
                returnproducts.total_amount as sum,
                returnproducts.price as price,
                returnproducts.quantity,
                products.product_name,
                returnproducts.vat_amount as vat,
                returnproducts.totalamount_wo_discount as grandtotal_without_discount,
                returnproducts.discount_amount as discount_amount
            '))
            // ->groupBy('returnproducts.transaction_id')
            ->orderBy('returnproducts.created_at', 'ASC')
            ->whereDate('returnproducts.created_at', $date)
            ->where('accountantlocs.user_id', $userid)
            ->where('returnproducts.branch', $location_id)
            ->get();

        return view('/accountant/datereturn', ['tax'=>$tax,'users' => $item, 'products' => $data, 'currency' => $currency]);
    }

    public function monthReturn($month, $location_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        $data = DB::table('accountantlocs')
            ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
            ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
            ->select(DB::raw('returnproducts.transaction_id,returnproducts.created_at,returnproducts.total_amount as sum,returnproducts.price as price,returnproducts.quantity, products.product_name,returnproducts.vat_amount as vat,returnproducts.totalamount_wo_discount as grandtotal_without_discount,returnproducts.discount_amount as discount_amount'))
            // ->groupBy('returnproducts.transaction_id')
            ->orderBy('returnproducts.created_at', 'ASC')
            ->whereMonth('returnproducts.created_at', $month)
            ->where('accountantlocs.user_id', $userid)
            ->where('returnproducts.branch', $location_id)
            ->get();

        return view('/accountant/monthreturn', ['tax'=>$tax,'users' => $item, 'products' => $data, 'currency' => $currency]);
    }

    public function yearReturn($year, $location_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        $data = DB::table('accountantlocs')
            ->Join('returnproducts', 'accountantlocs.location_id', '=', 'returnproducts.branch')
            ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
            ->select(DB::raw('returnproducts.transaction_id,returnproducts.created_at,returnproducts.total_amount as sum,returnproducts.price as price,returnproducts.quantity, products.product_name,returnproducts.vat_amount as vat,returnproducts.totalamount_wo_discount as grandtotal_without_discount,returnproducts.discount_amount as discount_amount'))
            // ->groupBy('returnproducts.transaction_id')
            ->orderBy('returnproducts.created_at', 'ASC')
            ->whereYear('returnproducts.created_at', $year)
            ->where('accountantlocs.user_id', $userid)
            ->where('returnproducts.branch', $location_id)
            ->get();

        return view('/accountant/yearreturn', ['tax'=>$tax,'users' => $item, 'products' => $data, 'currency' => $currency]);
    }

    public function accountReport()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $purchase = DB::table('stockdetails')
            ->where('branch', $branch)
            ->get();
        $location = DB::table('accountantlocs')
            ->Join('branches', 'accountantlocs.location_id', '=', 'branches.id')
            ->where('user_id', Session('softwareuser'))
            ->get();
        $count = DB::table('accountantlocs')
            ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
            ->whereDate('stockdetails.created_at', Carbon::today())
            ->where('stockdetails.branch', $branch)
            ->distinct('stockdetails.reciept_no')
            ->count();

        return view('/accountant/accountsreport', ['users' => $item, 'count' => $count, 'purchases' => $purchase, 'locations' => $location]);
    }

    public function accountantPurchaseview(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        // perday
        if ($req->view_type == 1) {
            if (is_null($req->start_date) || is_null($req->end_date)) {
                $location_id = $req->location_id;
                $fromdate = '0';
                $todate = '0';
                $viewtype = $req->view_type;

                return redirect('/purchaseperday/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            } else {
                $location_id = $req->location_id;
                $fromdate = $req->start_date;
                $todate = $req->end_date;
                $viewtype = $req->view_type;

                return redirect('/purchaseperday/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            }
        }
        // permonth
        if ($req->view_type == 2) {
            if (is_null($req->start_date) || is_null($req->end_date)) {
                $location_id = $req->location_id;
                $fromdate = '0';
                $todate = '0';
                $viewtype = $req->view_type;

                return redirect('/purchasepermonth/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            } else {
                $location_id = $req->location_id;
                $fromdate = $req->start_date;
                $todate = $req->end_date;
                $viewtype = $req->view_type;

                return redirect('/purchasepermonth/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            }
        }
        // peryear
        if ($req->view_type == 3) {
            if (is_null($req->start_date) || is_null($req->end_date)) {
                $location_id = $req->location_id;
                $fromdate = '0';
                $todate = '0';
                $viewtype = $req->view_type;

                return redirect('/purchaseperyear/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            } else {
                $location_id = $req->location_id;
                $fromdate = $req->start_date;
                $todate = $req->end_date;
                $viewtype = $req->view_type;

                return redirect('/purchaseperyear/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            }
        }

        return redirect('accountreport');
    }

    public function accountantpurchaseperyearview($viewtype, $location_id, $fromdate, $todate)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if ($fromdate == '0' || $todate == '0') {
            $data = DB::table('accountantlocs')
                ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                ->select(DB::raw('SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,YEAR(stockdetails.created_at) as date,COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('stockdetails.branch', $location_id)
                ->whereYear('stockdetails.created_at', Carbon::today())
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereportperyear', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate == $todate && $fromdate != '0' && $todate != '0') {
            $data = DB::table('accountantlocs')
                ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                ->select(DB::raw('SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,YEAR(stockdetails.created_at) as date,COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('stockdetails.branch', $location_id)
                ->whereYear('stockdetails.created_at', $fromdate)
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereportperyear', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate != $todate) {
            $data = DB::table('accountantlocs')
                ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                ->select(DB::raw('SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,YEAR(stockdetails.created_at) as date,COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('stockdetails.branch', $location_id)
                ->whereBetween('stockdetails.created_at', [$fromdate.' 00:00:00', $todate.' 23:59:59'])
                ->get();

            $location = $location_id;

            return view('/accountant/purchasereportperyear', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        }
    }

    public function accountantpurchasepermonthview($viewtype, $location_id, $fromdate, $todate)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if ($fromdate == '0' || $todate == '0') {
            $data = DB::table('accountantlocs')
                ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                ->select(DB::raw('SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,MONTHNAME(stockdetails.created_at) as date,MONTH(stockdetails.created_at) as month_id,COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('stockdetails.branch', $location_id)
                ->whereMonth('stockdetails.created_at', Carbon::today())
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereportpermonth', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate == $todate && $fromdate != '0' && $todate != '0') {
            $date = Carbon::createFromFormat('Y-m-d', $fromdate);
            $data = DB::table('accountantlocs')
                ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                ->select(DB::raw('SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,MONTHNAME(stockdetails.created_at) as date,MONTH(stockdetails.created_at) as month_id,COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('stockdetails.branch', $location_id)
                ->whereMonth('stockdetails.created_at', $date)
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereportpermonth', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate != $todate) {
            $data = DB::table('accountantlocs')
                ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                ->select(DB::raw('SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,MONTHNAME(stockdetails.created_at) as date,MONTH(stockdetails.created_at) as month_id,COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('stockdetails.branch', $location_id)
                ->whereBetween('stockdetails.created_at', [$fromdate.' 00:00:00', $todate.' 23:59:59'])
                ->get();

            $location = $location_id;

            return view('/accountant/purchasereportpermonth', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        }
    }

    public function accountantpurchaseperdayview($viewtype, $location_id, $fromdate, $todate)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if ($fromdate == '0' || $todate == '0') {
            $data = DB::table('accountantlocs')
                ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                ->select(DB::raw('SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,DATE(stockdetails.created_at) as date,COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('stockdetails.branch', $location_id)
                ->whereDate('stockdetails.created_at', Carbon::today())
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereportperday', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate == $todate && $fromdate != '0' && $todate != '0') {
            $date = Carbon::createFromFormat('Y-m-d', $fromdate);
            $data = DB::table('accountantlocs')
                ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                ->select(DB::raw('SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,DATE(stockdetails.created_at) as date,COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('stockdetails.branch', $location_id)
                ->whereDate('stockdetails.created_at', $date)
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereportperday', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate != $todate) {
            $data = DB::table('accountantlocs')
                ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
                ->select(DB::raw('SUM(stockdetails.price) as price,SUM(stockdetails.discount) as discount,DATE(stockdetails.created_at) as date,COUNT(distinct(stockdetails.reciept_no)) as purchase, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('stockdetails.branch', $location_id)
                ->whereBetween('stockdetails.created_at', [$fromdate.' 00:00:00', $todate.' 23:59:59'])
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereportperday', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        }
    }

    public function datePurchase($date, $location_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        // $data = DB::table('accountantlocs')
        //     ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
        //     ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
        //     ->select(DB::raw("stockdetails.reciept_no,stockdetails.comment,stockdetails.quantity,stockdetails.price,stockdetails.supplier,stockdetails.file,DATE(stockdetails.created_at) as created_at, products.product_name"),)
        //     ->whereDate('stockdetails.created_at', $date)
        //     ->where('stockdetails.branch', $location_id)
        //     ->distinct('stockdetails.id')
        //     ->get();

        $data = DB::table('accountantlocs')
            ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
            ->select(DB::raw('stockdetails.reciept_no,stockdetails.comment,stockdetails.supplier,stockdetails.file,stockdetails.created_at as created_at, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, SUM(stockdetails.price) as price, SUM(stockdetails.discount) as discount'))
            ->whereDate('stockdetails.created_at', $date)
            ->where('stockdetails.branch', $location_id)
            ->where('accountantlocs.user_id', $userid)
            ->groupBy('stockdetails.reciept_no')
            ->orderBy('stockdetails.created_at', 'ASC')
            ->distinct('stockdetails.id')
            ->get();

        return view('/accountant/datepurchase', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'currency' => $currency]);
    }

    public function monthPurchase($date, $location_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        // $data = DB::table('accountantlocs')
        //     ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
        //     ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
        //     ->select(DB::raw("stockdetails.reciept_no,stockdetails.comment,stockdetails.quantity,stockdetails.price,stockdetails.supplier,stockdetails.file,DATE(stockdetails.created_at) as created_at, products.product_name"),)
        //     ->whereMonth('stockdetails.created_at', $date)
        //     ->where('stockdetails.branch', $location_id)
        //     ->distinct('stockdetails.id')
        //     ->get();

        $data = DB::table('accountantlocs')
            ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
            ->select(DB::raw('stockdetails.reciept_no,stockdetails.comment,stockdetails.supplier,stockdetails.file,stockdetails.created_at as created_at, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, SUM(stockdetails.price) as price, SUM(stockdetails.discount) as discount'))
            ->whereMonth('stockdetails.created_at', $date)
            ->where('stockdetails.branch', $location_id)
            ->where('accountantlocs.user_id', $userid)
            ->groupBy('stockdetails.reciept_no')
            ->orderBy('stockdetails.created_at', 'ASC')
            ->distinct('stockdetails.id')
            ->get();

        return view('/accountant/monthpurchase', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'currency' => $currency]);
    }

    public function yearPurchase($date, $location_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        // $data = DB::table('accountantlocs')
        //     ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
        //     ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
        //     ->select(DB::raw("stockdetails.reciept_no,stockdetails.comment,stockdetails.quantity,stockdetails.price,stockdetails.supplier,stockdetails.file,DATE(stockdetails.created_at) as created_at, products.product_name"),)
        //     ->whereYear('stockdetails.created_at', $date)
        //     ->where('stockdetails.branch', $location_id)
        //     ->distinct('stockdetails.id')
        //     ->get();

        $data = DB::table('accountantlocs')
            ->Join('stockdetails', 'accountantlocs.location_id', '=', 'stockdetails.branch')
            ->select(DB::raw('stockdetails.reciept_no,stockdetails.comment, stockdetails.supplier,stockdetails.file,stockdetails.created_at as created_at, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, SUM(stockdetails.price) as price, SUM(stockdetails.discount) as discount'))
            ->whereYear('stockdetails.created_at', $date)
            ->where('stockdetails.branch', $location_id)
            ->where('accountantlocs.user_id', $userid)
            ->groupBy('stockdetails.reciept_no')
            ->orderBy('stockdetails.created_at', 'ASC')
            ->distinct('stockdetails.id')
            ->get();

        return view('/accountant/yearpurchase', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'currency' => $currency]);
    }

   public function EmployeeSalary()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
            $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
            $userdata = DB::table('employee')
            ->select('employee.first_name', 'employee.date', 'employee.id')
            ->where('branch',$branch)
            ->get();


        return view('/accountant/employeesalary', ['users' => $item, 'userdatas' => $userdata]);
    }

    public function EmployeeSalarydat($user_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
            $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $joined_date = Salarydata::where('branch_id', '=', $branch)
            ->orderBy('id', 'DESC')
            ->pluck('date')
            ->first();


        if ($joined_date != null) {
            $date = Carbon::create($joined_date);
            $joined_date = $date->addMonths(1);
            $joined_date = $joined_date->toDateString();
        } else {
            $joined_date = Employee::where('id', '=', $user_id)
                ->pluck('date')
                ->first();
            $date = Carbon::create($joined_date);
            $joined_date = $date->addMonths(1);
            $joined_date = $joined_date->toDateString();
        }
        $userid = $user_id;

        $employee = Employee::where('id', $userid)
        ->select('first_name', 'last_name')
        ->first();

            $full_name = $employee->first_name . ' ' . $employee->last_name;



        $salarydata = Salarydata::where('branch_id', '=', $branch)->where('employee_id', '=', $userid)
            ->get();


        return view('/accountant/employeesalarydat', ['full_name'=>$full_name,'users' => $item, 'joined_date' => $joined_date, 'user_id' => $userid, 'salarydatas' => $salarydata, 'currency' => $currency]);
    }

    public function addsalaryemployeeSubmit(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid=Session('softwareuser');
        $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();

        $user = new Salarydata();
        $user->user_id = $userid;
        $user->branch_id = $branch;
        $user->date = $req->date;
        $user->salary = $req->salary;
        $user->employee_id = $req->employee_id;
        $user->save();

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $salry_user_name = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' added salary of '.$salry_user_name;
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch);
        }

        /* ----------------------------------------------------------------------- */
        return redirect('/employeesalary');
    }


    public function purchaseReturn()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $receiptnos = DB::table('stockdetails')
            ->where('branch', $branch)
            ->distinct('stockdetails.reciept_no')
            ->pluck('reciept_no');

        // $purchases = DB::table('stockdetails')
        //     ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
        //     ->select(DB::raw("stockdetails.id, stockdetails.reciept_no, stockdetails.product, stockdetails.supplier, products.product_name, stockdetails.price, stockdetails.quantity, stockdetails.payment_mode"))
        //     ->where('stockdetails.branch', $branch)
        //     ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
  $shopdata = Branch::Where('id', $branch)->get();


        $suppliers = DB::table('suppliers')
            ->where('location', $branch)
            ->get();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
            $listbank = DB::table('bank')
            ->select('id','bank_name', 'account_name','status','current_balance')
            ->where('status', 1)
            ->where('branch', $branch)
            ->get();

        // new vat system
        return view('/inventory/purchasereturn_newvatsystem', ['listbank'=>$listbank,'tax'=>$tax,'users' => $item, 'shopdatas' => $shopdata, 'receiptnos' => $receiptnos]);
    }

    public function purchaseReturnhistory()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();

        // original

        // $purchases = DB::table('returnpurchases')
        //     ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
        //     ->select(DB::raw('returnpurchases.id as id, returnpurchases.reciept_no as reciept_no, returnpurchases.created_at as created_at, returnpurchases.comment as comment, returnpurchases.amount as price, returnpurchases.shop_name as supplier, returnpurchases.amount_without_vat as price_without_vat, returnpurchases.vat_amount as vat_amount, products.product_name as product_name, returnpurchases.quantity as quantity'))
        //     ->groupBy('returnpurchases.reciept_no')
        //     ->orderBy('returnpurchases.created_at', 'DESC')
        //     ->where('returnpurchases.branch', $branch)
        //     ->get();

        $purchases = DB::table('returnpurchases')
            ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
            ->select(
                DB::raw('returnpurchases.id as id'),
                DB::raw('returnpurchases.reciept_no as reciept_no'),
                DB::raw('returnpurchases.created_at as created_at'),
                DB::raw('returnpurchases.comment as comment'),
                DB::raw('SUM(returnpurchases.discount) as discount'),
                DB::raw('returnpurchases.shop_name as supplier'),
                DB::raw('SUM(returnpurchases.amount_without_vat) as total_price_without_vat'),
                DB::raw('SUM(returnpurchases.amount - returnpurchases.amount_without_vat) as total_vat'),
                DB::raw('SUM(returnpurchases.amount) as grand_total'),
                DB::raw('GROUP_CONCAT(products.product_name) as product_names'),
                DB::raw('GROUP_CONCAT(returnpurchases.quantity) as quantities')
            )
            ->groupBy('returnpurchases.reciept_no', 'returnpurchases.created_at')
            ->orderBy('returnpurchases.created_at', 'DESC')
            ->where('returnpurchases.branch', $branch)
            ->get();

        // return view('/inventory/purchasereturnhistory', array('users' => $item, 'purchases' => $purchases));

        return view('/inventory/purchasereturnhistory_new', ['tax'=>$tax,'users' => $item, 'purchases' => $purchases, 'currency' => $currency]);
    }

  public function returnsubmitData(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'reciept_no' => 'required|array',
            'reciept_no.*' => 'required|',
            'quantity' => 'required|array',
            'quantity.*' => 'required',
            'comment' => 'array',
            'amount' => 'required|array',
            'shop_name.*' => 'required',
            'product' => 'required',
        ]);
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        foreach ($request->p_main_id as $key => $p_main_id) {
            $data = new Returnpurchase();
            $data->reciept_no = $request->reciept_no[$key];
            $data->quantity = $request->quantity[$key];
            $data->unit = $request->units[$key];
            $data->buycost = $request->buycosts[$key];
            // $data->comment = $request->comment[$key];
            $data->product_id = $request->p_id[$key];
            $data->amount = $request->amount[$key];
            $data->discount = $request->quantity[$key] * $request->discount_percent[$key];
            $data->shop_name = $request->shop_name[$key];
            $data->return_payment = $request->payment_mode;
            $data->bank_id = $request->bank_name;
            $data->account_name = $request->account_name;

            $oldmethod = DB::table('stockdetails')
            ->where('reciept_no',$request->reciept_no[$key])
            ->pluck('method')
            ->first();
            $data->method = $oldmethod;
            $data->suppplierid = $request->supplid[$key];

            $data->user_id = Session('softwareuser');
            $data->branch = $branch;
            $data->amount_without_vat = $request->withoutvat[$key];

            $data->rate = $request->rate_r[$key];
            $data->vat = $request->vat_r[$key];

            $data->save();

            $return = Product::find($request->p_id[$key]);
            $return->remaining_stock -= $request->quantity[$key];
            $return->save();

            $remain_stock = StockDetail::where('reciept_no', $request->reciept_no[$key])
                ->where('product', $request->p_id[$key])
                // ->where('user_id', Session('softwareuser'))
                ->where('branch', $branch)
                ->pluck('remain_stock_quantity')
                ->first();

            $purchase_reduce = StockDetail::where('reciept_no', $request->reciept_no[$key])
                ->where('product', $request->p_id[$key])
                // ->where('user_id', Session('softwareuser'))
                ->where('branch', $branch)
                ->update(['remain_stock_quantity' => ($remain_stock - $request->quantity[$key])]);

            /* ----- purchase return reduce from stock purchase reports table ---- */

            $remain_main_quantity_sr = StockPurchaseReport::where('purchase_trans_id', $request->p_unique_trans_id[$key])
                ->where('purchase_id', $request->p_main_id[$key])
                ->where('receipt_no', $request->reciept_no[$key])
                ->where('product_id', $request->p_id[$key])
                // ->where('user_id', Session('softwareuser'))
                ->where('branch_id', $branch)
                ->pluck('remain_main_quantity')
                ->first();

            $sell_quantity_sr = StockPurchaseReport::where('purchase_trans_id', $request->p_unique_trans_id[$key])
                ->where('purchase_id', $request->p_main_id[$key])
                ->where('receipt_no', $request->reciept_no[$key])
                ->where('product_id', $request->p_id[$key])
                // ->where('user_id', Session('softwareuser'))
                ->where('branch_id', $branch)
                ->pluck('sell_quantity')
                ->first();

            $p_return_reduce_sreport = StockPurchaseReport::where('purchase_trans_id', $request->p_unique_trans_id[$key])
                ->where('purchase_id', $request->p_main_id[$key])
                ->where('receipt_no', $request->reciept_no[$key])
                ->where('product_id', $request->p_id[$key])
                // ->where('user_id', Session('softwareuser'))
                ->where('branch_id', $branch)
                ->update([
                    'remain_main_quantity' => ($remain_main_quantity_sr - $request->quantity[$key]),
                    'sell_quantity' => ($sell_quantity_sr - $request->quantity[$key]),
                ]);

            /* ------------------------------------------------------------------ */

            $remain_history_stock = Stockhistory::where('receipt_no', $request->reciept_no[$key])
                ->where('product_id', $request->p_id[$key])
                ->where('user_id', Session('softwareuser'))
                ->pluck('remain_qantity')
                ->first();

            $purchase_histry_reduce = Stockhistory::where('receipt_no', $request->reciept_no[$key])
                ->where('product_id', $request->p_id[$key])
                ->where('user_id', Session('softwareuser'))
                ->update(['remain_qantity' => ($remain_history_stock - $request->quantity[$key])]);

            $supplier_id = $request->supplid[$key];

            if ($request->ptype[$key] == 2) {
                // $supplier_id =  DB::table('suppliers')
                //     ->where('name', $request->shop_name[$key])
                //     ->where('location', $branch)
                //     ->pluck('id')
                //     ->first();

                $supplier_id = $request->supplid[$key];

                $credit = DB::table('supplier_credits')
                    ->select(DB::raw('due_amt'))
                    ->where('supplier_id', $supplier_id)
                    ->pluck('due_amt')
                    ->first();

                $paid = DB::table('supplier_credits')
                    ->select(DB::raw('collected_amt'))
                    ->where('supplier_id', $supplier_id)
                    ->pluck('collected_amt')
                    ->first();

                $credit -= $paid;

                $fund = new SupplierFundHistory();
                $fund->suppliername = $request->shop_name[$key];
                $fund->collectedamount = ($request->amount[$key])-($request->quantity[$key] * $request->discount_percent[$key]);
                $fund->supplierid = $supplier_id;
                $fund->due = $credit;
                $fund->userid = Session('softwareuser');
                $fund->branch = $branch;
                $fund->status = '1';
                $fund->save();

                $creditcollected = DB::table('supplier_credits')
                    ->where('supplier_id', $supplier_id)
                    ->pluck('collected_amt')
                    ->first();

                    $livecollected = ($request->amount[$key])-($request->quantity[$key] * $request->discount_percent[$key]);
                $totalcreditcollected = $creditcollected + $livecollected;
                $creditsummaries = DB::table('supplier_credits')
                    ->updateOrInsert(
                        ['supplier_id' => $supplier_id],
                        ['collected_amt' => $totalcreditcollected]
                    );

                // credit credit_supplier_transactions

                $lastTransaction = DB::table('credit_supplier_transactions')
                    ->where('credit_supplier_id', $supplier_id)
                    ->where('location', $branch)
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                    $lastSameTransaction = DB::table('credit_supplier_transactions')
                    ->where('credit_supplier_id', $supplier_id)
                    ->where('location', $branch)
                    ->where('reciept_no', $request->reciept_no[$key])
                    ->orderBy('created_at', 'desc')
                    ->first();
                    $previous_due = $lastSameTransaction->balance_due ?? 0;


                $updated_balance = $lastTransaction->updated_balance;
                $new_due = $updated_balance;
                $new_updated_bal = $new_due - $livecollected;
                $new_balance_due=$previous_due - $livecollected;

                $credit_trans_suppr = new CreditSupplierTransaction();
                $credit_trans_suppr->credit_supplier_id = $supplier_id;
                $credit_trans_suppr->credit_supplier_username = $request->shop_name[$key];
                $credit_trans_suppr->user_id = Session('softwareuser');
                $credit_trans_suppr->location = $branch;
                $credit_trans_suppr->due = $new_due;
                $credit_trans_suppr->collectedamount = $livecollected;
                $credit_trans_suppr->updated_balance = $new_updated_bal;
                $credit_trans_suppr->balance_due = $new_balance_due;
                $credit_trans_suppr->comment = 'Purchase Returned';
                $credit_trans_suppr->reciept_no = $request->reciept_no[$key];
                $credit_trans_suppr->save();
            } elseif ($request->ptype[$key] == 1 || $request->ptype[$key] == 3 && ($supplier_id != '' || $supplier_id != null)) {
                $supplier_id = $request->supplid[$key];

                $lastTransaction = DB::table('cash_supplier_transactions')
                    ->where('cash_supplier_id', $supplier_id)
                    ->where('location', $branch)
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                    $livecollected = ($request->amount[$key])-($request->quantity[$key] * $request->discount_percent[$key]);

                $updated_balance = $lastTransaction->updated_balance ?? null;
                $new_due = $updated_balance - $livecollected;

                $cash_trans = new CashSupplierTransaction();
                $cash_trans->cash_supplier_id = $supplier_id;
                $cash_trans->cash_supplier_username = $request->shop_name[$key];
                $cash_trans->user_id = Session('softwareuser');
                $cash_trans->location = $branch;
                $cash_trans->reciept_no = $request->reciept_no[$key];
                $cash_trans->collected_amount = $livecollected;
                $cash_trans->updated_balance = $new_due;
                $cash_trans->comment = 'Purchase Returned';
                $cash_trans->payment_type = $request->ptype[$key];
                $cash_trans->save();
            }
             // bank---------------------------------------------------------------------------

             if ($request->bank_name && $request->account_name) {
                $current_balance = DB::table('bank')
                            ->where('id', $request->bank_name)
                            ->where('account_name', $request->account_name)
                            ->pluck('current_balance')
                            ->first();
            $bankamount = ($request->amount[$key])-($request->quantity[$key] * $request->discount_percent[$key]);
                $new_balance = $current_balance + $bankamount;

                DB::table('bank')
                    ->where('id', $request->bank_name)
                    ->where('account_name', $request->account_name)
                    ->update(['current_balance' => $new_balance]);

                $bank_history = new Bankhistory();
                $bank_history->reciept_no = $request->reciept_no[$key];
                $bank_history->user_id = Session('softwareuser');
                $bank_history->branch = $branch;
                $bank_history->detail = 'Purchase Return';
                $bank_history->dr_cr = 'Credit';
                $bank_history->bank_id = $request->bank_name;
                $bank_history->account_name = $request->account_name;
                $bank_history->amount = $bankamount;
                $bank_history->date = Carbon::now();
                $bank_history->save();
            }

        }

        // ================= Journal Entry Hook =====================
        try {
            // Deduplicate: only journalize once per receipt number (not per row)
            $uniqueReceipts = array_unique($request->reciept_no);

            $journal = app(\App\Services\JournalEntryService::class);

            foreach ($uniqueReceipts as $receiptNo) {
                $journal->postPurchaseReturnByReceipt($receiptNo);
            }
        } catch (\Exception $e) {
            \Log::error('Journal entry failed [Purchase Return]: '.$e->getMessage());
        }
        // ===========================================================


        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';

        $message = $username.' purchase returned';
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */
        session()->flash('success', 'Purchase Returned Successfully');
        return redirect('/purchasereturn');
    }

    public function accountPurchasereturn()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $location = DB::table('accountantlocs')
            ->Join('branches', 'accountantlocs.location_id', '=', 'branches.id')
            ->where('user_id', Session('softwareuser'))
            ->get();
        $count = DB::table('accountantlocs')
            ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
            ->whereDate('returnpurchases.created_at', Carbon::today())
            ->where('returnpurchases.branch', $branch)
            ->distinct('returnpurchases.id')
            ->count();

        return view('/accountant/accountpurchasereturn', ['users' => $item, 'count' => $count, 'locations' => $location]);
    }

    public function accountantPurchasereturnview(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        // perday
        if ($req->view_type == 1) {
            if (is_null($req->start_date) || is_null($req->end_date)) {
                $location_id = $req->location_id;
                $fromdate = '0';
                $todate = '0';
                $viewtype = $req->view_type;

                return redirect('/purchasereturnperday/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            } else {
                $location_id = $req->location_id;
                $fromdate = $req->start_date;
                $todate = $req->end_date;
                $viewtype = $req->view_type;

                return redirect('/purchasereturnperday/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            }
        }
        // permonth
        if ($req->view_type == 2) {
            if (is_null($req->start_date) || is_null($req->end_date)) {
                $location_id = $req->location_id;
                $fromdate = '0';
                $todate = '0';
                $viewtype = $req->view_type;

                return redirect('/purchasereturnpermonth/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            } else {
                $location_id = $req->location_id;
                $fromdate = $req->start_date;
                $todate = $req->end_date;
                $viewtype = $req->view_type;

                return redirect('/purchasereturnpermonth/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            }
        }
        // peryear
        if ($req->view_type == 3) {
            if (is_null($req->start_date) || is_null($req->end_date)) {
                $location_id = $req->location_id;
                $fromdate = '0';
                $todate = '0';
                $viewtype = $req->view_type;

                return redirect('/purchasereturnperyear/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            } else {
                $location_id = $req->location_id;
                $fromdate = $req->start_date;
                $todate = $req->end_date;
                $viewtype = $req->view_type;

                return redirect('/purchasereturnperyear/'.$viewtype.'/'.$location_id.'/'.$fromdate.'/'.$todate);
            }
        }

        return redirect('accountpurchasereturn');
    }

    public function accountantpurchasereturnperyearview($viewtype, $location_id, $fromdate, $todate)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        if ($fromdate == '0' || $todate == '0') {
            $data = DB::table('accountantlocs')
                ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                ->select(DB::raw('SUM(returnpurchases.amount) as amount,SUM(returnpurchases.discount) as discount,YEAR(returnpurchases.created_at) as date,COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, SUM(returnpurchases.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('returnpurchases.branch', $location_id)
                ->whereYear('returnpurchases.created_at', Carbon::today())
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereturnreportperyear', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate == $todate && $fromdate != '0' && $todate != '0') {
            $data = DB::table('accountantlocs')
                ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                ->select(DB::raw('SUM(returnpurchases.amount) as amount,SUM(returnpurchases.discount) as discount,YEAR(returnpurchases.created_at) as date,COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, SUM(returnpurchases.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('returnpurchases.branch', $location_id)
                ->whereYear('returnpurchases.created_at', $fromdate)
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereturnreportperyear', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate != $todate) {
            $data = DB::table('accountantlocs')
                ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                ->select(DB::raw('SUM(returnpurchases.amount) as amount,SUM(returnpurchases.discount) as discount,YEAR(returnpurchases.created_at) as date,COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, SUM(returnpurchases.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('returnpurchases.branch', $location_id)
                ->whereBetween('returnpurchases.created_at', [$fromdate.' 00:00:00', $todate.' 23:59:59'])
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereturnreportperyear', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        }
    }

    public function accountantpurchasereturnpermonthview($viewtype, $location_id, $fromdate, $todate)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if ($fromdate == '0' || $todate == '0') {
            $data = DB::table('accountantlocs')
                ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                ->select(DB::raw('SUM(returnpurchases.amount) as amount,SUM(returnpurchases.discount) as discount,MONTHNAME(returnpurchases.created_at) as date,MONTH(returnpurchases.created_at) as month_id,COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, SUM(returnpurchases.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('returnpurchases.branch', $location_id)
                ->whereMonth('returnpurchases.created_at', Carbon::today())
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereturnreportpermonth', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate == $todate && $fromdate != '0' && $todate != '0') {
            $date = Carbon::createFromFormat('Y-m-d', $fromdate);
            $data = DB::table('accountantlocs')
                ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                ->select(DB::raw('SUM(returnpurchases.amount) as amount,SUM(returnpurchases.discount) as discount,MONTHNAME(returnpurchases.created_at) as date,MONTH(returnpurchases.created_at) as month_id,COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, SUM(returnpurchases.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('returnpurchases.branch', $location_id)
                ->whereMonth('returnpurchases.created_at', $date)
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereturnreportpermonth', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate != $todate) {
            $data = DB::table('accountantlocs')
                ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                ->select(DB::raw('SUM(returnpurchases.amount) as amount,SUM(returnpurchases.discount) as discount,MONTHNAME(returnpurchases.created_at) as date,MONTH(returnpurchases.created_at) as month_id,COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, SUM(returnpurchases.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('returnpurchases.branch', $location_id)
                ->whereBetween('returnpurchases.created_at', [$fromdate.' 00:00:00', $todate.' 23:59:59'])
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereturnreportpermonth', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        }
    }

    public function accountantpurchasereturnperdayview($viewtype, $location_id, $fromdate, $todate)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if ($fromdate == '0' || $todate == '0') {
            $data = DB::table('accountantlocs')
                ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                ->select(DB::raw('SUM(returnpurchases.amount) as amount,SUM(returnpurchases.discount) as discount,DATE(returnpurchases.created_at) as date,COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, SUM(returnpurchases.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('returnpurchases.branch', $location_id)
                ->whereDate('returnpurchases.created_at', Carbon::today())
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereturnreportperday', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        } elseif ($fromdate == $todate && $fromdate != '0' && $todate != '0') {
            $data = DB::table('accountantlocs')
                ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                ->select(DB::raw('SUM(returnpurchases.amount) as amount,SUM(returnpurchases.discount) as discount,DATE(returnpurchases.created_at) as date,COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, SUM(returnpurchases.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('returnpurchases.branch', $location_id)
                ->whereDate('returnpurchases.created_at', $fromdate)
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereturnreportperday', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        }
        if ($fromdate != $todate) {
            $data = DB::table('accountantlocs')
                ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
                ->select(DB::raw('SUM(returnpurchases.amount) as amount,SUM(returnpurchases.discount) as discount,DATE(returnpurchases.created_at) as date,COUNT(distinct(returnpurchases.id)) as purchase, SUM(returnpurchases.amount_without_vat) as price_without_vat, SUM(returnpurchases.vat_amount) as vat_amount'))
                ->groupBy('date')
                ->where('accountantlocs.user_id', $userid)
                ->where('returnpurchases.branch', $location_id)
                ->whereBetween('returnpurchases.created_at', [$fromdate.' 00:00:00', $todate.' 23:59:59'])
                ->get();
            $location = $location_id;

            return view('/accountant/purchasereturnreportperday', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'location' => $location, 'userid' => $userid, 'viewtype' => $viewtype, 'fromdate' => $fromdate, 'todate' => $todate, 'currency' => $currency]);
        }
    }

    public function datePurchasereturn($date, $location_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        $data = DB::table('accountantlocs')
            ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
            ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
            ->select(DB::raw('returnpurchases.reciept_no,returnpurchases.discount,returnpurchases.comment,returnpurchases.shop_name,returnpurchases.created_at as created_at, returnpurchases.amount_without_vat as price_without_vat, returnpurchases.vat_amount as vat_amount, products.product_name, returnpurchases.quantity, returnpurchases.amount as price'))
            ->whereDate('returnpurchases.created_at', $date)
            ->where('returnpurchases.branch', $location_id)
            ->where('accountantlocs.user_id', $userid)
            ->orderBy('returnpurchases.created_at', 'ASC')
            ->distinct('returnpurchases.id')
            ->get();

        return view('/accountant/datepurchasereturn', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'currency' => $currency]);
    }

    public function monthPurchasereturn($date, $location_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        $data = DB::table('accountantlocs')
            ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
            ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
            ->select(DB::raw('returnpurchases.reciept_no,returnpurchases.discount,returnpurchases.comment,returnpurchases.shop_name,returnpurchases.created_at as created_at, returnpurchases.amount_without_vat as price_without_vat, returnpurchases.vat_amount as vat_amount, products.product_name, returnpurchases.quantity, returnpurchases.amount as price'))
            ->whereMonth('returnpurchases.created_at', $date)
            ->where('returnpurchases.branch', $location_id)
            ->where('accountantlocs.user_id', $userid)
            ->orderBy('returnpurchases.created_at', 'ASC')
            ->distinct('returnpurchases.id')
            ->get();

        return view('/accountant/monthpurchasereturn', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'currency' => $currency]);
    }

    public function yearPurchasereturn($date, $location_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        $data = DB::table('accountantlocs')
            ->Join('returnpurchases', 'accountantlocs.location_id', '=', 'returnpurchases.branch')
            ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
            ->select(DB::raw('returnpurchases.reciept_no,returnpurchases.discount,returnpurchases.comment,returnpurchases.shop_name,returnpurchases.created_at as created_at, returnpurchases.amount_without_vat as price_without_vat, returnpurchases.vat_amount as vat_amount, products.product_name, returnpurchases.quantity, returnpurchases.amount as price'))
            ->whereYear('returnpurchases.created_at', $date)
            ->where('returnpurchases.branch', $location_id)
            ->where('accountantlocs.user_id', $userid)
            ->orderBy('returnpurchases.created_at', 'ASC')
            ->distinct('returnpurchases.id')
            ->get();

        return view('/accountant/yearpurchasereturn', ['tax'=>$tax,'users' => $item, 'purchases' => $data, 'currency' => $currency]);
    }

    public function accountantFinalreport(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'finalreportfile' => 'required',
            'date' => 'required',
        ]);
        $data = new Finalreport();
        $data->user_id = Session('softwareuser');
        $data->date = $request->date;
        $ext = $request->file('finalreportfile')->getClientOriginalExtension();
        $data->file = 'FINAL_REPORT'.date('d-m-y_h-i-s').'.'.$ext;
        $data->save();
        $path = $request->file('finalreportfile')->storeAs('public/finalreport', $data->file);

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' uploaded final accountant report';
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */
        return back()->with('success', 'Data uploaded successfully!');
    }

    public function finalReporthistory()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();
        $reports = DB::table('finalreports')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/accountant/finalreporthistory', ['users' => $item, 'reports' => $reports]);
    }

    public function downloadReport(Request $request, $file)
    {
        return response()->download(storage_path('app/public/finalreport/'.$file));
    }

    public function hrAnalytics()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/hranalytics', ['users' => $item]);
    }

    public function hrTime()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/hrtime', ['users' => $item]);
    }

    public function hrserviceRequests()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/servicerequests', ['users' => $item]);
    }

    public function hrtalentAquisition()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/talentaquisition', ['users' => $item]);
    }

    public function backgroundCheck()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/backgroundcheck', ['users' => $item]);
    }

    public function hrAppraisals()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/appraisals', ['users' => $item]);
    }

    public function hrApprovedrequisitions()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/approvedrequisitions', ['users' => $item]);
    }

    public function hrRejectedrequisitions()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/rejectedrequisitions', ['users' => $item]);
    }

    public function hrCandidates()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/hrcandidates', ['users' => $item]);
    }

    public function hrInterviews()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/hrinterviews', ['users' => $item]);
    }

    public function hrShortlisted()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/hrshortlisted', ['users' => $item]);
    }

    public function backgroundCheckconf()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/backgroundconf', ['users' => $item]);
    }

    public function hrmanagerAppraisals()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/managerappraisals', ['users' => $item]);
    }

    public function hrmanagerAppraisalsstatus()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/managerstatus', ['users' => $item]);
    }

    public function hrEmployeestatus()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/employeestatus', ['users' => $item]);
    }

    public function hrSelfappraisals()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/selfappraisal', ['users' => $item]);
    }

    public function hrMyteamappraisals()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/myteamappraisal', ['users' => $item]);
    }

    public function hrAppraisalhistory()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/appraisalhistory', ['users' => $item]);
    }

    public function hruserAnalytics()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/hruseranalytics', ['users' => $item]);
    }

    public function hremployeeAnalytics()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/hremployeeanalytics', ['users' => $item]);
    }

    public function hrrecruitmentAnalytics()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/hrrecruitmentanalytics', ['users' => $item]);
    }

    public function hremployeeleaveAnalytics()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/hremployeeleaveanalytics', ['users' => $item]);
    }

    public function hrholidayAnalytics()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/hrholidayanalytics', ['users' => $item]);
    }

    public function hrbackgroundcheckAnalytics()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/hrbackgroundcheckanalytics', ['users' => $item]);
    }

    public function hrlogsAnalytics()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/hrlogsanalytics', ['users' => $item]);
    }

    public function hrservicerequestsAnalytics()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/hrservicerequestsanalytics', ['users' => $item]);
    }

    public function userCreatehr(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $req->validate(
            [
                'full_name' => 'required',
                'username' => 'required|unique:softwareusers,username',
                'location' => 'required',
            ],
            [
                'full_name.required' => "Enter Employee's Full Name", // custom message
                'location.required' => 'Specify the branch of the employee', // custom message
            ]
        );
        $adminid = Softwareuser::Where('id', Session('softwareuser'))
            ->pluck('admin_id')
            ->first();
        $user = new Hrusercreation();
        $user->name = $req->full_name;
        $user->username = $req->username;
        $user->location = $req->location;
        $user->admin_id = $adminid;
        $user->user_id = Session('softwareuser');
        $user->joining_date = $req->joining_date;
        $user->email = $req->email;
        $user->save();
        $createduserid = $user->id;
        if ($req->privileges == null) {
            return back()->with('success', 'User data sent successfully!');
        }
        foreach ($req->privileges as $key => $privileges) {
            $data = new Hruserroles();
            $data->role_id = $privileges;
            $data->user_id = $createduserid;
            $data->save();
        }

        return back()->with('success', 'User data sent successfully!');
    }

    public function createTalentaquisition()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        return view('/hr/createtalentaquisition', ['users' => $item]);
    }

    public function generateInvoice()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();
        $adminid = Softwareuser::Where('id', Session('softwareuser'))
            ->pluck('admin_id')
            ->first();
        $shopname = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        $shopnumber = Adminuser::Where('id', $adminid)
            ->pluck('phone')
            ->first();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $products = Product::select(DB::raw('*'))
            ->where('branch', $branch)
            ->get();

        return view('/accountant/generateinvoice', ['tax'=>$tax,'users' => $item, 'shopname' => $shopname, 'shopnumber' => $shopnumber, 'products' => $products]);
    }

    public function generateInvoiceform(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $request->validate([
            'productName' => 'required|array',
            'productName.*' => 'required|',
            'from_name' => 'required',
            'from_number' => 'required',
            'from_trnnumber' => 'required',
            'from_address' => 'required',
            'invoicetype' => 'required',
            'to_name' => 'required',
            'to_number' => 'required',
            'to_trnnumber' => 'required',
            'to_address' => 'required',
            'heading' => 'required',
            'quantity' => 'required|array',
            'quantity.*' => 'required',
            'mrp' => 'required|array',
            'mrp.*' => 'required',
            'fixed_vat' => 'required|array',
            'fixed_vat.*' => 'required',
            'price' => 'required|array',
            'price.*' => 'required',
        ]);
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $invoice = new Invoicedata();
        $invoice->from_name = $request->from_name;
        $invoice->from_number = $request->from_number;
        $invoice->from_trnnumber = $request->from_trnnumber;
        $invoice->from_address = $request->from_address;
        $invoice->from_email = $request->from_email;
        $invoice->invoice_type = $request->invoicetype;
        $invoice->to_email = $request->to_email;
        $invoice->to_name = $request->to_name;
        $invoice->to_number = $request->to_number;
        $invoice->to_trnnumber = $request->to_trnnumber;
        $invoice->to_address = $request->to_address;
        $invoice->heading = $request->heading;
        $invoice->footer = $request->footer;
        $invoice->due_date = $request->due_date;
        $invoice->save();
        $invoice_id = $invoice->id;
        foreach ($request->productName as $key => $productName) {
            $data = new Invoiceproduct();
            $data->product_name = $productName;
            $data->quantity = $request->quantity[$key];
            $data->product_id = $request->product_id[$key];
            $data->price = $request->price[$key];
            $data->payment_type = $request->payment_type;
            $data->user_id = Session('softwareuser');
            $data->branch = $branch;
            $data->mrp = $request->mrp[$key];
            $data->fixed_vat = $request->fixed_vat[$key];
            $data->invoice_id = $invoice_id;
            $data->vat_amount = (($request->price[$key] * $request->fixed_vat[$key]) / 100);
            $data->save();
        }
        if (is_null($request->termsandcondition)) {
            return redirect('/invoicegenerated/'.$invoice_id);
        } else {
            foreach ($request->termsandcondition as $key => $termsandcondition) {
                $datainvoice = new Termsandcondition();
                $datainvoice->termsandcondition = $termsandcondition;
                $datainvoice->invoice_id = $invoice_id;
                $datainvoice->save();
            }
        }

        return redirect('/invoicegenerated/'.$invoice_id);
    }

    public function invoiceGenerated($invoice_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $dataplan = DB::table('invoiceproducts')
            ->where('invoice_id', $invoice_id)
            ->get();
        $total = Invoiceproduct::select(DB::raw('SUM(price) as total'))
            ->where('invoice_id', $invoice_id)
            ->pluck('total')
            ->first();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $branchname = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
          $shopdata = Branch::Where('id', $branch)->get();

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        $total = Invoiceproduct::select(DB::raw('SUM(price) as total'))
            ->where('invoice_id', $invoice_id)
            ->pluck('total')
            ->first();
        $vat = Invoiceproduct::select(DB::raw('SUM(vat_amount) as vat'))
            ->where('invoice_id', $invoice_id)
            ->pluck('vat')
            ->first();
        $payment_type = DB::table('invoiceproducts')
            ->leftJoin('payment', 'invoiceproducts.payment_type', '=', 'payment.id')
            ->select(DB::raw('payment.type as payment_type'))
            ->where('invoiceproducts.invoice_id', $invoice_id)
            ->pluck('payment_type')
            ->first();
        $date = DB::table('invoiceproducts')
            ->select(DB::raw('DATE(invoiceproducts.created_at) as date'))
            ->where('invoice_id', $invoice_id)
            ->pluck('date')
            ->first();
        $grand = $total + $vat;
        $amountinwords = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $amountinwords = $amountinwords->format($grand);
        $amountinwords = ucwords($amountinwords);
        $supplieddate = Carbon::now()->format('Y-m-d');
        $from_name = DB::table('invoicedatas')
            ->where('id', $invoice_id)
            ->pluck('from_name')
            ->first();
        $from_trnnumber = DB::table('invoicedatas')
            ->where('id', $invoice_id)
            ->pluck('from_trnnumber')
            ->first();
        $from_email = DB::table('invoicedatas')
            ->where('id', $invoice_id)
            ->pluck('from_email')
            ->first();
        $from_address = DB::table('invoicedatas')
            ->where('id', $invoice_id)
            ->pluck('from_address')
            ->first();
        $from_number = DB::table('invoicedatas')
            ->where('id', $invoice_id)
            ->pluck('from_number')
            ->first();
        $to_name = DB::table('invoicedatas')
            ->where('id', $invoice_id)
            ->pluck('to_name')
            ->first();
        $to_trnnumber = DB::table('invoicedatas')
            ->where('id', $invoice_id)
            ->pluck('to_trnnumber')
            ->first();
        $to_email = DB::table('invoicedatas')
            ->where('id', $invoice_id)
            ->pluck('to_email')
            ->first();
        $to_address = DB::table('invoicedatas')
            ->where('id', $invoice_id)
            ->pluck('to_address')
            ->first();
        $to_number = DB::table('invoicedatas')
            ->where('id', $invoice_id)
            ->pluck('to_number')
            ->first();
        $heading = DB::table('invoicedatas')
            ->where('id', $invoice_id)
            ->pluck('heading')
            ->first();
        $footer = DB::table('invoicedatas')
            ->where('id', $invoice_id)
            ->pluck('footer')
            ->first();
        $invoice_type_id = DB::table('invoicedatas')
            ->where('id', $invoice_id)
            ->pluck('invoice_type')
            ->first();
        $due_date = DB::table('invoicedatas')
            ->where('id', $invoice_id)
            ->pluck('due_date')
            ->first();
        if ($invoice_type_id == 1) {
            $invoice_type = 'LPO';
        } elseif ($invoice_type_id == 2) {
            $invoice_type = 'Invoice';
        } elseif ($invoice_type_id == 3) {
            $invoice_type = 'Quotation';
        }
        $termsandconditions = DB::table('termsandconditions')
            ->where('invoice_id', $invoice_id)
            ->get();
        $invoice_number = 'INV'.$invoice_id;
        $invoice_num = $invoice_id;

        return view('/accountant/generatedinvoice', ['tax'=>$tax,'details' => $dataplan, 'vat' => $vat, 'payment_type' => $payment_type, 'totals' => $total, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'from_name' => $from_name, 'from_address' => $from_address, 'from_trnnumber' => $from_trnnumber, 'from_number' => $from_number, 'to_name' => $to_name, 'to_address' => $to_address, 'to_trnnumber' => $to_trnnumber, 'to_number' => $to_number, 'heading' => $heading, 'footer' => $footer, 'termsandconditions' => $termsandconditions, 'invoice_type' => $invoice_type, 'due_date' => $due_date, 'invoice_number' => $invoice_number, 'invoice_num' => $invoice_num, 'to_email' => $to_email, 'from_email' => $from_email]);
    }

     public function billingsideTransactions(Request $req, UserService $userservice)
    {
        if ((Session('softwareuser') && session()->missing('softwareuser')) ||
            (Session('adminuser') && session()->missing('adminuser')) ||
            (!Session('softwareuser') && !Session('adminuser'))) {
            return redirect('userlogin');
        }

        $filters = [
            'payment_type' => $req->input('payment_type'),
            'date_filter' => $req->input('date_filter', 'all'),
            'start_date' => $req->input('start_date'),
            'end_date' => $req->input('end_date'),
            'credit_user_id' => $req->input('credit_user_id'),
        ];

        $paymentTypeMap = [
            'Cash' => 1,
            'Bank' => 2,
            'Credit' => 3,
            'POS Card' => 4,
        ];

        $query = DB::table('buyproducts')
            ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
            ->leftJoin('creditusers', 'buyproducts.credit_user_id', '=', 'creditusers.id')
            ->leftJoin(DB::raw('(SELECT transaction_id COLLATE utf8mb4_unicode_ci as transaction_id,
                                SUM(totalamount_wo_discount) as return_grandtotal_without_discount,
                                SUM(COALESCE(discount_amount, 0)) + SUM(total_amount * (total_discount_percent / 100)) as return_discount_amount,
                                SUM(DISTINCT COALESCE(grand_total, 0)) as return_sum
                                FROM returnproducts GROUP BY transaction_id) as returns'),
                                'buyproducts.transaction_id', '=', 'returns.transaction_id')
            ->leftJoin(DB::raw('(SELECT transaction_id, SUM(credit_note_amount) as total_credit_note_amount
                                FROM (SELECT DISTINCT transaction_id, credit_note_id, credit_note_amount
                                      FROM credit_note) as unique_credits
                                GROUP BY transaction_id) as credit_sums'),
                                'buyproducts.transaction_id', '=', 'credit_sums.transaction_id');

        $selectFields = [
            'buyproducts.transaction_id',
            'buyproducts.created_at',
            'buyproducts.customer_name',
            'buyproducts.vat_type',
            'buyproducts.quantity',
            'buyproducts.approve',
            DB::raw('SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0)) as sum'),
            DB::raw('SUM(buyproducts.vat_amount) as vat'),
            'payment.type as payment_type',
            'creditusers.username',
            'buyproducts.phone',
            DB::raw('SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount'),
            DB::raw('SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
                   + SUM(DISTINCT COALESCE(buyproducts.total_discount_amount, 0)) as discount_amount'),
            DB::raw('COALESCE(returns.return_grandtotal_without_discount, 0) as return_grandtotal_without_discount'),
            DB::raw('COALESCE(returns.return_discount_amount, 0) as return_discount_amount'),
            DB::raw('COALESCE(returns.return_sum, 0) as return_sum'),
            DB::raw('COALESCE(credit_sums.total_credit_note_amount) as credit_note_amount')
        ];

        if (Session('softwareuser')) {
            $userId = Session('softwareuser');
            $branch = Softwareuser::find($userId)->location;
            $adminId = Softwareuser::find($userId)->admin_id;

            $query->where('buyproducts.branch', $branch);
            $creditusers = Credituser::where('admin_id', $adminId)
                ->where('status', 1)
                ->where('location', $branch)
                ->get();
        } else {
            $adminId = Session('adminuser');
            $query->leftJoin('branches', 'buyproducts.branch', '=', 'branches.id')
                  ->addSelect('branches.location as branch');
        }

        if ($filters['start_date'] && $filters['end_date']) {
            $query->whereBetween('buyproducts.created_at', [
                $filters['start_date'] . ' 00:00:00',
                $filters['end_date'] . ' 23:59:59'
            ]);
        } elseif ($filters['start_date']) {
            $query->whereDate('buyproducts.created_at', $filters['start_date']);
        }

        if ($filters['payment_type'] && isset($paymentTypeMap[$filters['payment_type']])) {
            $query->where('buyproducts.payment_type', $paymentTypeMap[$filters['payment_type']]);
        }

        if ($filters['credit_user_id']) {
            $query->where('buyproducts.credit_user_id', $filters['credit_user_id']);
        }

        if ($filters['date_filter'] === 'today') {
            $query->whereDate('buyproducts.created_at', now()->format('Y-m-d'));
        }

        $data = $query->select($selectFields)
            ->groupBy('buyproducts.transaction_id')
            ->orderByDesc('buyproducts.created_at')
            ->get();

        return view('/billingdesk/transactions', [
            'products' => $data,
            'currency' => Adminuser::find($adminId)->currency,
            'tax' => Adminuser::find($adminId)->tax,
            'start_date' => $filters['start_date'],
            'end_date' => $filters['end_date'],
            'payment_type' => $filters['payment_type'],
            'date_filter' => $filters['date_filter'],
            'credit_user_id' => $filters['credit_user_id'],
            'creditusers' => $creditusers ?? null,
            'adminid' => $adminId,
            'users' => Session('softwareuser') ? $userservice->getUserDetails($userId) : null,
            'shopdatas' => Session('adminuser') ? Adminuser::find($adminId) : null,
        ]);
    }
    public function addFunds()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

       $credit = Credituser::leftJoin('creditsummaries', 'creditusers.id', '=', 'creditsummaries.credituser_id')
            ->leftJoin(DB::raw("(SELECT credit_user_id, cash_user_id, transaction_id, MAX(bill_grand_total) as bill_grand_total
                                 FROM buyproducts
                                 GROUP BY credit_user_id, cash_user_id, transaction_id) as bp_summary"), function($join) {
                $join->on('creditusers.id', '=', 'bp_summary.credit_user_id')
                     ->orOn('creditusers.id', '=', 'bp_summary.cash_user_id');
            })
            ->leftJoin(DB::raw("(SELECT SUM(collected_amount) as total_returned_amount, cash_user_id
                                 FROM cash_trans_statements
                                 WHERE comment = 'Product Returned'
                                 GROUP BY cash_user_id) as cash_returns"), 'creditusers.id', '=', 'cash_returns.cash_user_id')

            ->leftJoin(DB::raw("(SELECT SUM(collected_amount) as total_invoiced_amount, cash_user_id
                    FROM cash_trans_statements
                    WHERE comment = 'Invoice'
                    GROUP BY cash_user_id) as cash_invoices"), 'creditusers.id', '=', 'cash_invoices.cash_user_id')

            ->leftJoin(DB::raw("(SELECT credituser_id,
                                 SUM(CASE 
                                    WHEN comment IN ('Invoice', 'Payment Received') 
                                    AND (status IS NULL OR status != 'cancelled') 
                                    THEN collected_amount 
                                    ELSE 0 
                                END) as credit_trans_collected,
                                 SUM(CASE WHEN comment = 'Invoice' THEN collected_amount ELSE 0 END) as credit_trans_invoice,
                                 SUM(CASE WHEN comment = 'Invoice' THEN Invoice_due ELSE 0 END) as credit_trans_invoice_amount,
                                 SUM(CASE WHEN comment = 'Returned Product' THEN collected_amount ELSE 0 END) as credit_trans_returned
                                 FROM credit_transactions
                                 GROUP BY credituser_id) as credit_trans"), 'creditusers.id', '=', 'credit_trans.credituser_id')
            ->select(
                'creditusers.name',
                'creditusers.id',
                'creditusers.username',
                DB::raw('COALESCE(creditsummaries.due_amount, 0) as due_amount'),
                DB::raw('COALESCE(creditsummaries.collected_amount, 0) as collected_amount'),
                DB::raw('COALESCE(creditsummaries.creditnote, 0) as creditnote'),
                DB::raw('SUM(bp_summary.bill_grand_total) as total_bill_grand_total'),
                DB::raw('SUM(CASE WHEN bp_summary.cash_user_id = creditusers.id THEN bp_summary.bill_grand_total ELSE 0 END) as total_cash_user_grand_total'),
                DB::raw('COALESCE(cash_returns.total_returned_amount, 0) as total_product_returned'),
                DB::raw('COALESCE(cash_invoices.total_invoiced_amount, 0) as total_invoiced_amount'),
                DB::raw('COALESCE(credit_trans.credit_trans_collected, 0) as credit_trans_collected'),
                DB::raw('COALESCE(credit_trans.credit_trans_invoice, 0) as credit_trans_invoice'),
                DB::raw('COALESCE(credit_trans.credit_trans_invoice_amount, 0) as credit_trans_invoice_amount'),
                DB::raw('COALESCE(credit_trans.credit_trans_returned, 0) as credit_trans_returned')
            )
            ->where('creditusers.status', 1)
            ->where('creditusers.location', $branch)
            ->groupBy('creditusers.id', 'creditusers.name', 'creditusers.username', 'creditsummaries.due_amount', 'creditsummaries.collected_amount', 'creditsummaries.creditnote', 'cash_returns.total_returned_amount')
            ->get();




            $suppliercredit = Supplier::leftJoin('supplier_credits', 'suppliers.id', '=', 'supplier_credits.supplier_id')
            ->leftJoin('stockdetails', 'suppliers.id', '=', 'stockdetails.supplier_id') // Join with stockdetails table
            ->leftJoin(DB::raw("(SELECT SUM(collected_amount) as total_returned_amount, cash_supplier_id
                                 FROM cash_supplier_transactions
                                 WHERE comment = 'Purchase Returned'
                                 GROUP BY cash_supplier_id) as cash_returns"), 'suppliers.id', '=', 'cash_returns.cash_supplier_id')
             ->leftJoin(DB::raw("(SELECT SUM(collected_amount) as total_invoiced_amount, cash_supplier_id
                                 FROM cash_supplier_transactions
                                 WHERE comment = 'Bill'
                                 GROUP BY cash_supplier_id) as cash_invoices"), 'suppliers.id', '=', 'cash_invoices.cash_supplier_id')
            ->leftJoin(DB::raw("(SELECT credit_supplier_id,
                                  SUM(CASE 
                                    WHEN comment IN ('Bill', 'Payment Made') 
                                    AND (status IS NULL OR status != 'cancelled') 
                                    THEN collectedamount 
                                    ELSE 0 
                                END) as credit_trans_collected,
                                 SUM(CASE WHEN comment = 'Bill' THEN collectedamount ELSE 0 END) as credit_trans_invoice,
                                 SUM(CASE WHEN comment = 'Bill' THEN Invoice_due ELSE 0 END) as credit_trans_invoice_amount,
                                 SUM(CASE WHEN comment = 'Purchase Returned' THEN collectedamount ELSE 0 END) as credit_trans_returned
                                 FROM credit_supplier_transactions
                                 GROUP BY credit_supplier_id) as credit_trans"), 'suppliers.id', '=', 'credit_trans.credit_supplier_id')
            ->select(
                'suppliers.id',
                'suppliers.name',
                DB::raw('COALESCE(supplier_credits.due_amt, 0) as due_amt'), // Handle NULL as 0
                DB::raw('COALESCE(supplier_credits.collected_amt, 0) as collected_amt'), // Handle NULL as 0
                DB::raw('COALESCE(supplier_credits.debitnote, 0) as debitnote'),
                DB::raw('SUM(stockdetails.price) - SUM(COALESCE(stockdetails.discount,0)) as total_price'), // Sum of all prices from stockdetails
                DB::raw('
                SUM(CASE WHEN stockdetails.payment_mode IN (1, 3) THEN stockdetails.price ELSE 0 END) -
                SUM(CASE WHEN stockdetails.payment_mode IN (1, 3) THEN COALESCE( stockdetails.discount,0) ELSE 0 END) as price_with_payment_mode'),
                DB::raw('COALESCE(cash_returns.total_returned_amount, 0) as total_purchase_returned'), // Sum of returned amounts from cash_supplier_transactions
                DB::raw('COALESCE(cash_invoices.total_invoiced_amount, 0) as total_invoiced_amount'),
                DB::raw('COALESCE(credit_trans.credit_trans_collected, 0) as credit_trans_collected'),
                DB::raw('COALESCE(credit_trans.credit_trans_invoice, 0) as credit_trans_invoice'),
                DB::raw('COALESCE(credit_trans.credit_trans_invoice_amount, 0) as credit_trans_invoice_amount'),
                DB::raw('COALESCE(credit_trans.credit_trans_returned, 0) as credit_trans_returned')
            )
            ->where('suppliers.location', $branch)
            ->groupBy('suppliers.id', 'suppliers.name', 'supplier_credits.due_amt', 'supplier_credits.collected_amt', 'supplier_credits.debitnote', 'cash_returns.total_returned_amount') // Updated to include total_returned_amount
            ->get();

            $listbank = DB::table('bank')
            ->select('id','bank_name', 'account_name', 'status','current_balance')
            ->where('status', 1)
            ->where('branch', $branch)
            ->get();

        return view('/billingdesk/addfunds', ['listbank'=>$listbank,'users' => $item, 'credits' => $credit, 'suppliercredits' => $suppliercredit, 'currency' => $currency]);
    }

    // inventory list stock
    public function inventoryStocklist()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
          $shopdata = Branch::Where('id', $branch)->get();
        $products = DB::table('products')
            ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
            ->select(DB::raw('products.product_name as product_name,products.stock as stock, SUM(stockdats.stock_num) as stock_num, products.remaining_stock as remaining_stock'))
            ->groupBy('products.id')
            ->where('products.branch', $branch)
            ->where('products.status', 1)
            ->orderBy('products.id')
            ->get();

        $categories = Category::where('branch_id', $branch)
            ->select(DB::raw('category_name, id'))
            ->get();

        return view('/inventory/inventorystocklist', ['users' => $useritem, 'shopdatas' => $shopdata, 'products' => $products, 'categories' => $categories]);
    }

    public function purchaseHistory()
    {
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }
        else {
            return redirect('userlogin');
        }
        if (Session('softwareuser')) {
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();

        // original

        // $purchase = DB::table('stockdetails')
        //     ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
        //     ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
        //     ->groupBy('stockdetails.reciept_no')
        //     ->orderBy('stockdetails.created_at', 'DESC')
        //     ->where('stockdetails.branch', $branch)
        //     ->get();

        // new

        $purchase = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->leftJoin('stock_purchase_reports', function ($join) {
                $join->on(DB::raw('CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_general_ci'), '=', DB::raw('CONVERT(stock_purchase_reports.receipt_no USING utf8mb4) COLLATE utf8mb4_general_ci'))
                    ->on('stockdetails.product', '=', 'stock_purchase_reports.product_id');
            })
            ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no,stockdetails.approve as approve, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.discount) as discount, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, stock_purchase_reports.purchase_id as purchase_id'))
            ->groupBy('stockdetails.reciept_no')
            ->orderBy('stockdetails.created_at', 'DESC')
            ->where('stockdetails.branch', $branch)
            ->get();

        foreach ($purchase as $purcs) {
            $hasEqualQuantities = DB::table('stock_purchase_reports')
                ->where('receipt_no', $purcs->reciept_no)
                ->whereColumn('quantity', 'sell_quantity')
                ->exists();

            $purcs->showEditButton = $hasEqualQuantities;

            // Fetch sales information related to the purchase
            $sales = DB::table('bill_histories')
                ->where('receipt_no', $purcs->reciept_no)
                ->distinct('trans_id')
                ->get('trans_id');

            $purcs->sales = $sales;

            $purchase_return = DB::table('returnpurchases')
                ->where('reciept_no', $purcs->reciept_no)
                ->exists();

            $purcs->purchase_return = $purchase_return;
        }
    } elseif (Session('adminuser')) {
        $adminid = Session('adminuser');

        $item = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $adminid)
            ->get();

        $purchase = DB::table('stockdetails')
        ->leftJoin('branches', 'stockdetails.branch', '=', 'branches.id')
        ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
        ->leftJoin('stock_purchase_reports', function ($join) {
            $join->on(DB::raw('CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_general_ci'), '=', DB::raw('CONVERT(stock_purchase_reports.receipt_no USING utf8mb4) COLLATE utf8mb4_general_ci'))
            ->on('stockdetails.product', '=', 'stock_purchase_reports.product_id');
        })
        ->select(DB::raw('branches.location as branch,stockdetails.id as id,stockdetails.approve as approve, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment,SUM(stockdetails.discount) as discount, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, stock_purchase_reports.purchase_id as purchase_id'))
        ->groupBy('stockdetails.reciept_no')
        ->orderBy('stockdetails.created_at', 'DESC')
        ->get();

        foreach ($purchase as $purcs) {
            $hasEqualQuantities = DB::table('stock_purchase_reports')
            ->where('receipt_no', $purcs->reciept_no)
            ->whereColumn('quantity', 'sell_quantity')
            ->exists();

            $purcs->showEditButton = $hasEqualQuantities;

            // Fetch sales information related to the purchase
        $sales = DB::table('bill_histories')
            ->where('receipt_no', $purcs->reciept_no)
            ->distinct('trans_id')
            ->get('trans_id');

            $purcs->sales = $sales;

            $purchase_return = DB::table('returnpurchases')
            ->where('reciept_no', $purcs->reciept_no)
            ->exists();

            $purcs->purchase_return = $purchase_return;
        }
    }
        $start_date = '';
        $end_date = '';

        $currency = Adminuser::Where('id', $adminid)
        ->pluck('currency')
        ->first();

    $tax = Adminuser::Where('id', $adminid)
    ->pluck('tax')
    ->first();
    if (Session('softwareuser')) {
        $options = [
            'purchases' => $purchase,
            'users' => $item,
            'currency' => $currency,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'tax'=>$tax,
        ];
    } elseif (Session('adminuser')) {
        $options = [
            'purchases' => $purchase,
            'users' => $item,
            'currency' => $currency,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'tax'=>$tax,
        ];
    }
    return view('/inventory/purchasehistory_new', $options);

    // return view('/inventory/purchasehistory_new', ['tax'=>$tax,'users' => $useritem, 'purchases' => $purchase, 'start_date' => $start_date, 'end_date' => $end_date, 'currency' => $currency]);
}

    public function purchaseHistorydate(Request $req)
    {
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }
        else {
            return redirect('userlogin');
        }

        if (Session('softwareuser')) {
        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();


        if ($req->start_date != $req->end_date) {
            // $purchase = DB::table('stockdetails')
            //     ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            //     ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
            //     ->groupBy('stockdetails.reciept_no')
            //     ->orderBy('stockdetails.created_at', 'DESC')
            //     ->where('stockdetails.branch', $branch)
            //     ->whereBetween('stockdetails.created_at', [$req->start_date . ' 00:00:00', $req->end_date . ' 23:59:59'])
            //     ->get();

            $purchase = DB::table('stockdetails')
                ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
                ->leftJoin('stock_purchase_reports', function ($join) {
                    $join->on(DB::raw('CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_general_ci'), '=', DB::raw('CONVERT(stock_purchase_reports.receipt_no USING utf8mb4) COLLATE utf8mb4_general_ci'))
                        ->on('stockdetails.product', '=', 'stock_purchase_reports.product_id');
                })
                ->select(DB::raw('stockdetails.id as id,stockdetails.approve as approve, stockdetails.reciept_no as reciept_no,SUM(stockdetails.discount) as discount, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, stock_purchase_reports.purchase_id as purchase_id'))
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'DESC')
                ->where('stockdetails.branch', $branch)
                ->whereBetween('stockdetails.created_at', [$req->start_date.' 00:00:00', $req->end_date.' 23:59:59'])
                ->get();

            foreach ($purchase as $purcs) {
                $hasEqualQuantities = DB::table('stock_purchase_reports')
                    ->where('receipt_no', $purcs->reciept_no)
                    ->whereColumn('quantity', 'sell_quantity')
                    ->exists();

                $purcs->showEditButton = $hasEqualQuantities;

                // Fetch sales information related to the purchase
                $sales = DB::table('bill_histories')
                    ->where('receipt_no', $purcs->reciept_no)
                    ->distinct('trans_id')
                    ->get('trans_id');

                $purcs->sales = $sales;

                $purchase_return = DB::table('returnpurchases')
                    ->where('reciept_no', $purcs->reciept_no)
                    ->exists();

                $purcs->purchase_return = $purchase_return;
            }
        } elseif ($req->start_date == $req->end_date && $req->start_date != '') {
            // $purchase = DB::table('stockdetails')
            //     ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            //     ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
            //     ->groupBy('stockdetails.reciept_no')
            //     ->orderBy('stockdetails.created_at', 'DESC')
            //     ->where('stockdetails.branch', $branch)
            //     ->whereDate('stockdetails.created_at', $req->start_date)
            //     ->get();

            $purchase = DB::table('stockdetails')
                ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
                ->leftJoin('stock_purchase_reports', function ($join) {
                    $join->on(DB::raw('CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_general_ci'), '=', DB::raw('CONVERT(stock_purchase_reports.receipt_no USING utf8mb4) COLLATE utf8mb4_general_ci'))
                        ->on('stockdetails.product', '=', 'stock_purchase_reports.product_id');
                })
                ->select(DB::raw('stockdetails.id as id,stockdetails.approve as approve, stockdetails.reciept_no as reciept_no,SUM(stockdetails.discount) as discount, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, stock_purchase_reports.purchase_id as purchase_id'))
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'DESC')
                ->where('stockdetails.branch', $branch)
                ->whereDate('stockdetails.created_at', $req->start_date)
                ->get();

            foreach ($purchase as $purcs) {
                $hasEqualQuantities = DB::table('stock_purchase_reports')
                    ->where('receipt_no', $purcs->reciept_no)
                    ->whereColumn('quantity', 'sell_quantity')
                    ->exists();

                $purcs->showEditButton = $hasEqualQuantities;

                // Fetch sales information related to the purchase
                $sales = DB::table('bill_histories')
                    ->where('receipt_no', $purcs->reciept_no)
                    ->distinct('trans_id')
                    ->get('trans_id');

                $purcs->sales = $sales;

                $purchase_return = DB::table('returnpurchases')
                    ->where('reciept_no', $purcs->reciept_no)
                    ->exists();

                $purcs->purchase_return = $purchase_return;
            }
        } else {
            // $purchase = DB::table('stockdetails')
            //     ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            //     ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
            //     ->groupBy('stockdetails.reciept_no')
            //     ->orderBy('stockdetails.created_at', 'DESC')
            //     ->where('stockdetails.branch', $branch)
            //     ->whereDate('stockdetails.created_at', Carbon::today())
            //     ->get();

            $purchase = DB::table('stockdetails')
                ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
                ->leftJoin('stock_purchase_reports', function ($join) {
                    $join->on(DB::raw('CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_general_ci'), '=', DB::raw('CONVERT(stock_purchase_reports.receipt_no USING utf8mb4) COLLATE utf8mb4_general_ci'))
                        ->on('stockdetails.product', '=', 'stock_purchase_reports.product_id');
                })
                ->select(DB::raw('stockdetails.id as id,stockdetails.approve as approve, stockdetails.reciept_no as reciept_no,SUM(stockdetails.discount) as discount, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, stock_purchase_reports.purchase_id as purchase_id'))
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'DESC')
                ->where('stockdetails.branch', $branch)
                ->whereDate('stockdetails.created_at', Carbon::today())
                ->get();

            foreach ($purchase as $purcs) {
                $hasEqualQuantities = DB::table('stock_purchase_reports')
                    ->where('receipt_no', $purcs->reciept_no)
                    ->whereColumn('quantity', 'sell_quantity')
                    ->exists();

                $purcs->showEditButton = $hasEqualQuantities;

                // Fetch sales information related to the purchase
                $sales = DB::table('bill_histories')
                    ->where('receipt_no', $purcs->reciept_no)
                    ->distinct('trans_id')
                    ->get('trans_id');

                $purcs->sales = $sales;

                $purchase_return = DB::table('returnpurchases')
                    ->where('reciept_no', $purcs->reciept_no)
                    ->exists();

                $purcs->purchase_return = $purchase_return;
            }
        }
    } elseif (Session('adminuser')) {
        $adminId = Session('adminuser');
        if ($req->start_date != $req->end_date) {
            // $purchase = DB::table('stockdetails')
            //     ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            //     ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
            //     ->groupBy('stockdetails.reciept_no')
            //     ->orderBy('stockdetails.created_at', 'DESC')
            //     ->where('stockdetails.branch', $branch)
            //     ->whereBetween('stockdetails.created_at', [$req->start_date . ' 00:00:00', $req->end_date . ' 23:59:59'])
            //     ->get();

            $purchase = DB::table('stockdetails')
            ->leftJoin('branches', 'stockdetails.branch', '=', 'branches.id')
                ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
                ->leftJoin('stock_purchase_reports', function ($join) {
                    $join->on(DB::raw('CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_general_ci'), '=', DB::raw('CONVERT(stock_purchase_reports.receipt_no USING utf8mb4) COLLATE utf8mb4_general_ci'))
                        ->on('stockdetails.product', '=', 'stock_purchase_reports.product_id');
                })
                ->select(DB::raw('branches.location as branch,stockdetails.approve as approve,stockdetails.id as id,SUM(stockdetails.discount) as discount, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, stock_purchase_reports.purchase_id as purchase_id'))
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'DESC')
                ->whereBetween('stockdetails.created_at', [$req->start_date.' 00:00:00', $req->end_date.' 23:59:59'])
                ->get();

            foreach ($purchase as $purcs) {
                $hasEqualQuantities = DB::table('stock_purchase_reports')
                    ->where('receipt_no', $purcs->reciept_no)
                    ->whereColumn('quantity', 'sell_quantity')
                    ->exists();

                $purcs->showEditButton = $hasEqualQuantities;

                // Fetch sales information related to the purchase
                $sales = DB::table('bill_histories')
                    ->where('receipt_no', $purcs->reciept_no)
                    ->distinct('trans_id')
                    ->get('trans_id');

                $purcs->sales = $sales;

                $purchase_return = DB::table('returnpurchases')
                    ->where('reciept_no', $purcs->reciept_no)
                    ->exists();

                $purcs->purchase_return = $purchase_return;
            }
        } elseif ($req->start_date == $req->end_date && $req->start_date != '') {
            // $purchase = DB::table('stockdetails')
            //     ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            //     ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
            //     ->groupBy('stockdetails.reciept_no')
            //     ->orderBy('stockdetails.created_at', 'DESC')
            //     ->where('stockdetails.branch', $branch)
            //     ->whereDate('stockdetails.created_at', $req->start_date)
            //     ->get();

            $purchase = DB::table('stockdetails')
            ->leftJoin('branches', 'stockdetails.branch', '=', 'branches.id')
                ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
                ->leftJoin('stock_purchase_reports', function ($join) {
                    $join->on(DB::raw('CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_general_ci'), '=', DB::raw('CONVERT(stock_purchase_reports.receipt_no USING utf8mb4) COLLATE utf8mb4_general_ci'))
                        ->on('stockdetails.product', '=', 'stock_purchase_reports.product_id');
                })
                ->select(DB::raw('branches.location as branch,stockdetails.approve as approve,stockdetails.id as id,SUM(stockdetails.discount) as discount, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, stock_purchase_reports.purchase_id as purchase_id'))
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'DESC')
                ->whereDate('stockdetails.created_at', $req->start_date)
                ->get();

            foreach ($purchase as $purcs) {
                $hasEqualQuantities = DB::table('stock_purchase_reports')
                    ->where('receipt_no', $purcs->reciept_no)
                    ->whereColumn('quantity', 'sell_quantity')
                    ->exists();

                $purcs->showEditButton = $hasEqualQuantities;

                // Fetch sales information related to the purchase
                $sales = DB::table('bill_histories')
                    ->where('receipt_no', $purcs->reciept_no)
                    ->distinct('trans_id')
                    ->get('trans_id');

                $purcs->sales = $sales;

                $purchase_return = DB::table('returnpurchases')
                    ->where('reciept_no', $purcs->reciept_no)
                    ->exists();

                $purcs->purchase_return = $purchase_return;
            }
        } else {
            // $purchase = DB::table('stockdetails')
            //     ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            //     ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
            //     ->groupBy('stockdetails.reciept_no')
            //     ->orderBy('stockdetails.created_at', 'DESC')
            //     ->where('stockdetails.branch', $branch)
            //     ->whereDate('stockdetails.created_at', Carbon::today())
            //     ->get();

            $purchase = DB::table('stockdetails')
            ->leftJoin('branches', 'stockdetails.branch', '=', 'branches.id')
                ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
                ->leftJoin('stock_purchase_reports', function ($join) {
                    $join->on(DB::raw('CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_general_ci'), '=', DB::raw('CONVERT(stock_purchase_reports.receipt_no USING utf8mb4) COLLATE utf8mb4_general_ci'))
                        ->on('stockdetails.product', '=', 'stock_purchase_reports.product_id');
                })
                ->select(DB::raw('branches.location as branch,stockdetails.approve as approve,stockdetails.id as id,SUM(stockdetails.discount) as discount, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, stock_purchase_reports.purchase_id as purchase_id'))
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'DESC')
                ->whereDate('stockdetails.created_at', Carbon::today())
                ->get();

            foreach ($purchase as $purcs) {
                $hasEqualQuantities = DB::table('stock_purchase_reports')
                    ->where('receipt_no', $purcs->reciept_no)
                    ->whereColumn('quantity', 'sell_quantity')
                    ->exists();

                $purcs->showEditButton = $hasEqualQuantities;

                // Fetch sales information related to the purchase
                $sales = DB::table('bill_histories')
                    ->where('receipt_no', $purcs->reciept_no)
                    ->distinct('trans_id')
                    ->get('trans_id');

                $purcs->sales = $sales;

                $purchase_return = DB::table('returnpurchases')
                    ->where('reciept_no', $purcs->reciept_no)
                    ->exists();

                $purcs->purchase_return = $purchase_return;
            }
        }

        $useritem = DB::table('adminusers')
        ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
        ->where('user_id', $adminId)
        ->get();

    $shopdata = Adminuser::where('id', $adminId)->get();
    }

    $currency = Adminuser::Where('id', $adminid)
    ->pluck('currency')
    ->first();

    $tax = Adminuser::Where('id', $adminid)
    ->pluck('tax')
    ->first();

        $start_date = $req->start_date;
        $end_date = $req->end_date;

        return view('/inventory/purchasehistory_new', ['tax'=>$tax,'users' => $useritem, 'purchases' => $purchase, 'start_date' => $start_date, 'end_date' => $end_date, 'currency' => $currency]);
    }

    public function edittransactionsDate(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        if ($req->start_date != $req->end_date) {
            $data = DB::table('buyproducts')
                ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
                ->select(DB::raw('buyproducts.transaction_id,buyproducts.created_at,buyproducts.customer_name,SUM(buyproducts.total_amount) as sum,SUM(buyproducts.vat_amount) as vat,payment.type as payment_type'))
                ->groupBy('transaction_id')
                ->orderBy('created_at', 'DESC')
                ->where('branch', $branch)
                ->whereBetween('buyproducts.created_at', [$req->start_date, $req->end_date])
                ->get();
        } elseif ($req->start_date == $req->end_date && $req->start_date != '') {
            $data = DB::table('buyproducts')
                ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
                ->select(DB::raw('buyproducts.transaction_id,buyproducts.created_at,buyproducts.customer_name,SUM(buyproducts.total_amount) as sum,SUM(buyproducts.vat_amount) as vat,payment.type as payment_type'))
                ->groupBy('transaction_id')
                ->orderBy('created_at', 'DESC')
                ->where('branch', $branch)
                ->whereDate('buyproducts.created_at', $req->start_date)
                ->get();
        } else {
            $data = DB::table('buyproducts')
                ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
                ->select(DB::raw('buyproducts.transaction_id,buyproducts.created_at,buyproducts.customer_name,SUM(buyproducts.total_amount) as sum,SUM(buyproducts.vat_amount) as vat,payment.type as payment_type'))
                ->groupBy('transaction_id')
                ->orderBy('created_at', 'DESC')
                ->where('branch', $branch)
                ->get();
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $start_date = $req->start_date;
        $end_date = $req->end_date;

        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();

        return view('/billingdesk/edittransactions', ['tax'=>$tax,'products' => $data, 'users' => $item, 'currency' => $currency, 'start_date' => $start_date, 'end_date' => $end_date]);
    }

    public function creditTransactionsHistory(Request $request, $id)
        {
            if (Session('softwareuser')) {
                if (session()->missing('softwareuser')) {
                    return redirect('userlogin');
                }
            } elseif (Session('adminuser')) {
                if (session()->missing('adminuser')) {
                    return redirect('adminlogin');
                }
            }
    
    
    
          $request->validate([
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        // Initialize query builders without date conditions
        $creditQuery = CreditTransaction::where('credituser_id', $id);
        $cashQuery = CashTransStatement::where('cash_user_id', $id);
        
        // Only apply date filters if dates are provided in the request
        if ($request->has('start_date') && $request->start_date) {
            $creditQuery->whereDate('created_at', '>=', $request->start_date);
            $cashQuery->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $creditQuery->whereDate('created_at', '<=', $request->end_date);
            $cashQuery->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Execute the queries
        $creditTransactions = $creditQuery->get()->toArray();
        $cashTransactions = $cashQuery->get()->toArray();
        
        // Combine both collections
        $allTransactions = array_merge($creditTransactions, $cashTransactions);
            $startDate = request('start_date') ?: now()->format('Y-m-d');
            $endDate = request('end_date') ?: now()->format('Y-m-d');
    
    
            if (Session('softwareuser')) {
                $item = DB::table('softwareusers')
                    ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                    ->where('user_id', Session('softwareuser'))
                    ->get();
    
                $userid = Session('softwareuser');
                $adminid = Softwareuser::Where('id', $userid)
                    ->pluck('admin_id')
                    ->first();
            } elseif (Session('adminuser')) {
                $item = DB::table('adminusers')
                    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                    ->where('user_id', Session('adminuser'))
                    ->get();
    
                $adminid = Session('adminuser');
            }
    
            $currency = Adminuser::Where('id', $adminid)
                ->pluck('currency')
                ->first();
    
            $lastTransaction_for_due = DB::table('credit_transactions')
                ->where('credituser_id', $id)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();
    
            $final_due = $lastTransaction_for_due->updated_balance ?? 0;
    
            return view('/billingdesk/customercreditdata', [
                'users' => $item,
                'allTransactions' => $allTransactions,
                'finaldue' => $final_due,
                'currency' => $currency,
                'credit_id' => $id,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);
        }

    public function creditBillsHistory($id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();
        $collected = DB::table('creditsummaries')
            ->where('credituser_id', $id)
            ->pluck('collected_amount')
            ->first();
        $total_due = DB::table('creditsummaries')
            ->where('credituser_id', $id)
            ->pluck('due_amount')
            ->first();
        $username = DB::table('creditusers')->where('id', $id)->pluck('name')->first();

        $transactions = DB::table('buyproducts')
            ->select(DB::raw('transaction_id,created_at,SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount ,user_id,vat_type,SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
                    +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount, SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum, quantity'))
            // ->where('customer_name', $username)
            ->where('credit_user_id', $id)
            ->where('payment_type', 3)
            ->groupBy('transaction_id')
            ->orderBy('created_at', 'DESC')
            ->get();

        $userid = Session('softwareuser');

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

            $listbank = DB::table('bank')
            ->select('id','bank_name', 'account_name', 'status')
            ->where('status', 1)
            ->where('branch', $branch)
            ->get();

        return view('/billingdesk/customerbilldata', ['listbank'=>$listbank,'users' => $item, 'transactions' => $transactions, 'purchase' => $total_due, 'paid' => $collected, 'credit_id' => $username, 'currency' => $currency]);
    }

    public function PurchaseEdit($id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
          $shopdata = Branch::Where('id', $branch)->get();


        $products = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->select(DB::raw('products.product_name as product_name,stockdetails.product as id'))
            ->where('stockdetails.id', $id)
            ->first();

        $reciept_no = DB::table('stockdetails')
            ->where('id', $id)
            ->pluck('reciept_no')
            ->first();
        $comment = DB::table('stockdetails')
            ->where('id', $id)
            ->pluck('comment')
            ->first();
        $price = DB::table('stockdetails')
            ->where('id', $id)
            ->pluck('price')
            ->first();
        $supplier = DB::table('stockdetails')
            ->where('id', $id)
            ->pluck('supplier')
            ->first();

        $supp_id = DB::table('stockdetails')
            ->where('id', $id)
            ->pluck('supplier_id')
            ->first();

        $unit = DB::table('stockdetails')
            ->where('id', $id)
            ->pluck('unit')
            ->first();

        $datas = DB::table('stockdetails')
            ->select(DB::raw('is_box_or_dozen, box_dozen_count'))
            ->where('id', $id)
            ->first();

        $quan = DB::table('stockdetails')
            ->where('id', $id)
            ->pluck('quantity')
            ->first();

        $quantity = number_format($quan);

        $suppliers = DB::table('suppliers')
            ->where('location', $branch)
            ->get();

        $payment_type = DB::table('stockdetails')
            ->where('id', $id)
            ->pluck('payment_mode')
            ->first();

        return view('/inventory/purchaseedit', ['users' => $useritem, 'reciept_no' => $reciept_no, 'comment' => $comment, 'price' => $price, 'supplier' => $supplier, 'purchase_id' => $id, 'products' => $products, 'shopdatas' => $shopdata, 'unit' => $unit, 'datas' => $datas, 'quantity' => $quantity, 'suppliers' => $suppliers, 'payment_type' => $payment_type, 'supplier_id' => $supp_id]);
    }

    public function purchaseEditform(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $purchaseedit = DB::table('stockdetails')
            ->updateOrInsert(
                ['id' => $request->purchase_id],
                ['reciept_no' => $request->reciept_no],
                ['comment' => $request->comment],
                // ['quantity' => $request->quantity],
                ['price' => $request->price],
                ['shopname' => $request->supplier]
            );
        $purchaseedit = DB::table('stockdetails')
            ->updateOrInsert(
                ['id' => $request->purchase_id],
                ['comment' => $request->comment]
            );

        $purchaseedit = DB::table('stockdetails')
            ->updateOrInsert(
                ['id' => $request->purchase_id],
                ['price' => $request->price]
            );
        $purchaseedit = DB::table('stockdetails')
            ->updateOrInsert(
                ['id' => $request->purchase_id],
                ['supplier' => $request->supplier]
            );

        $dueamount = DB::table('supplier_credits')
            ->where('supplier_id', $request->supp_id)
            ->pluck('due_amt')
            ->first();

        if ($request->payment_type == 2) {
            $remove_old_price = DB::table('supplier_credits')
                ->updateOrInsert(
                    ['supplier_id' => $request->supp_id],
                    ['due_amt' => $dueamount - $request->prevprice],
                );

            $dueamountafter = DB::table('supplier_credits')
                ->where('supplier_id', $request->supp_id)
                ->pluck('due_amt')
                ->first();

            $addnewprice = DB::table('supplier_credits')
                ->updateOrInsert(
                    ['supplier_id' => $request->supp_id],
                    ['due_amt' => $dueamountafter + $request->afterprice],
                );
        }

        return redirect('/editpurchase/'.$request->purchase_id);
    }

    public function Plexpayrecharge()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $phonenumber = '';

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $response = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $response['wallet_amount'];
        $due_amount = $response['due_amount'];

        $provider_info['ProviderLogo'] = '';
        $provider_info['ProviderName'] = '';

        $plans = [];

        return view('/plexpay/recharge', ['users' => $item, 'plans' => $plans, 'phonenumber' => $phonenumber, 'provider_info' => $provider_info, 'due_amount' => $due_amount, 'wallet_amount' => $wallet_amount]);
    }

    public function Plexpayregister()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $phonenumber = '';
        $user_id = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $register = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        // $password = Crypt::decrypt($password);
        if ($register == '' || $password == '') {
            $register = 'Not Registered';
        } else {
            $register = 'Registered';
        }

        return view('/plexpay/plexpayregister', ['users' => $item, 'user_id' => $user_id, 'phonenumber' => $phonenumber, 'register' => $register]);
    }

    public function Plexpayunregister(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $password = Crypt::encrypt('password');
        $plexpayuserdata = DB::table('plexpayusers')
            ->updateOrInsert(
                ['id' => 1],
                ['user_id' => '', 'password' => $password, 'plexbill_userid' => '']
            );

        return back()->with('success', 'Un-Registered successfully!');
    }

    public function Plexpayuserregisterpost(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $plexbill_userid = Session('softwareuser');

        if ($req->user_id == '' || $req->password == '') {
            return back()->with('success', 'Registeration Details not updated!');
        }

        $encpass = Crypt::encrypt($req->password);

        $plexpayuserdata = DB::table('plexpayusers')
            ->updateOrInsert(
                ['id' => 1],
                ['user_id' => $req->user_id, 'password' => $encpass, 'plexbill_userid' => $plexbill_userid]
            );

        return back()->with('success', 'Registered successfully!');
    }

    public function Plexpayregisterpost(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $response = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $method = $req->method;
        $auth = json_decode($auth, true);
        $dash = $req->dash;
        $logo = $req->ProviderLogo;
        $Expiry_Date = $req->Expiry_Date;
        $ProviderName = $req->ProviderName;
        $ProviderCode = $req->ProviderCode;
        $AccountNumber = $req->phone;
        $SkuCode = $req->SkuCode;
        $SendValue = $req->SendValue;
        $ReceiveValue = $req->ReceiveValue;
        $Our_SendValue = $req->Our_SendValue;
        $Country_Iso = $req->Country_Iso;
        $CoupenTitle = $req->CoupenTitle;
        $amount = $req->amount;
        $SendCurrencyIso = $req->SendCurrencyIso;
        $minValue = $req->minValue;
        $maxValue = $req->maxValue;
        if ($req->Our_SendValue == 0 || $req->ReceiveValue == 0 || $req->SendValue == 0) {
            return view('/plexpay/internationalamountconversion', ['users' => $item, 'minValue' => $minValue, 'maxValue' => $maxValue, 'id' => 0, 'SendCurrencyIso' => $SendCurrencyIso, 'CoupenTitle' => $CoupenTitle, 'amount' => $amount, 'method' => $method, 'dash' => $dash, 'ProviderLogo' => $logo, 'Expiry_Date' => $Expiry_Date, 'CoupenTitle' => $CoupenTitle, 'ProviderName' => $ProviderName, 'Country_Iso' => $Country_Iso, 'ReceiveValue' => $ReceiveValue, 'SendValue' => $SendValue, 'SkuCode' => $SkuCode, 'Our_SendValue' => $Our_SendValue, 'phonenumber' => $AccountNumber, 'ProviderCode' => $ProviderCode]);
        } else {
            return view('/plexpay/rechargeconfirm', ['users' => $item, 'SendCurrencyIso' => $SendCurrencyIso, 'CoupenTitle' => $CoupenTitle, 'amount' => $amount, 'method' => $method, 'dash' => $dash, 'ProviderLogo' => $logo, 'Expiry_Date' => $Expiry_Date, 'CoupenTitle' => $CoupenTitle, 'ProviderName' => $ProviderName, 'Country_Iso' => $Country_Iso, 'ReceiveValue' => $ReceiveValue, 'SendValue' => $SendValue, 'SkuCode' => $SkuCode, 'Our_SendValue' => $Our_SendValue, 'phonenumber' => $AccountNumber, 'ProviderCode' => $ProviderCode]);
        }
    }

    public function Plexpayrechargesearch(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $phonenumber = $req->phonenumber;

        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $phonenumber = $req->phone;
        $method = $req->method;
        $amount = $req->amount;
        $accoNumber = substr($phonenumber, 1);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $response = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $response['wallet_amount'];
        $due_amount = $response['due_amount'];

        $reports = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/typeAndPlanByNumber', [
                'account_number' => $accoNumber,
                'dash' => 1,
            ]);
        $provider_info = $reports['provider_info'];

        $plans = $reports['plans'];
        $dash = '1';

        return view('/plexpay/recharge', ['users' => $item, 'method' => $method, 'amount' => $amount, 'dash' => $dash, 'phonenumber' => $phonenumber, 'plans' => $plans, 'provider_info' => $provider_info, 'due_amount' => $due_amount, 'wallet_amount' => $wallet_amount]);
    }

    public function Customrechargepost(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];
        $id = $req->id;
        $method = $req->method;
        if ($id == 1) {
            $ReceiveValue = $req->ReceiveValue;
            $SkuCode = $req->SkuCode;
            $ProviderCode = $req->ProviderCode;
            $AccountNumber = $req->AccountNumber;
            $amount = $req->Amount;
            $ReceiveCurrencyIso = $req->ReceiveCurrencyIso;
            $Our_SendValue = $req->Our_SendValue;
            $Country_Iso = $req->Country_Iso;
            $SendValue = $req->SendValue;
            $SendCurrencyIso = $req->SendCurrencyIso;
            if ($Our_SendValue == 0) {
                $Our_SendValue = $amount * 0.055;
            } else {
                $Our_SendValue = $Our_SendValue;
            }
            $ReceivedValue = $Our_SendValue;
        } elseif ($id == 2) {
            $products = Http::withHeaders([
                'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
                'access-token' => $access_token,
                'user-id' => $user_id,
            ])->asForm()
                ->post('https://plexpay.netplexsolution.com/api/getCustomAmount_one', [
                    'amount' => $req->Amount,
                ]);
            $ReceiveValue = $req->ReceiveValue;
            $SkuCode = $req->SkuCode;
            $ProviderCode = $req->ProviderCode;
            $AccountNumber = $req->AccountNumber;
            $amount = $req->Amount;
            $ReceiveCurrencyIso = '';
            if ($req->Country_Iso != 'AE') {
                $ReceivedValue = $products['SendValue'];
            } else {
                $ReceivedValue = $amount;
            }
            $Our_SendValue = 0;
            $Country_Iso = $req->Country_Iso;
            $SendValue = 0;
            $SendCurrencyIso = 0;
        }

        return view('/plexpay/giftcardrecharge', ['users' => $item, 'Our_SendValue' => $Our_SendValue, 'Country_Iso' => $Country_Iso, 'SendValue' => $SendValue, 'SendCurrencyIso' => $SendCurrencyIso, 'ReceivedValue' => $ReceivedValue, 'id' => $id, 'ReceiveCurrencyIso' => $ReceiveCurrencyIso, 'method' => $method, 'amount' => $amount, 'id' => $id, 'ReceiveValue' => $ReceiveValue, 'SkuCode' => $SkuCode, 'ProviderCode' => $ProviderCode, 'AccountNumber' => $AccountNumber]);
    }

    public function localCustomrechargepost(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];

        $response = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/customAmountRecharge', [
                'Dash' => 1,
                'AccountNumber' => $req->AccountNumber,
                'Amount' => $req->Amount,
            ]);

        if ($response['status'] == false) {
            return redirect('rechargefailed');
        } else {
            $transaction_id = $response['transaction_id'];
        }

        return redirect('/rechargesuccessful/'.$transaction_id);
    }

    public function plexpayrechargelocalnumber(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];

        $ProviderCode = $req->ProviderCode;
        $AccountNumber = $req->AccountNumber;
        $SkuCode = $req->SkuCode;
        $SendValue = $req->SendValue;
        $ReceiveValue = $req->ReceiveValue;
        $Our_SendValue = $req->Our_SendValue;
        $Country_Iso = $req->Country_Iso;
        $AccountNumber = substr($AccountNumber, 1);

        $response = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/rechargeNow', [
                'Dash' => 0,
                'ProviderCode' => $ProviderCode,
                'AccountNumber' => $AccountNumber,
                'SkuCode' => $SkuCode,
                'SendValue' => $SendValue,
                'ReceiveValue' => $ReceiveValue,
                'Our_SendValue' => $Our_SendValue,
                'Country_Iso' => $Country_Iso,
            ]);

        if ($response['status'] == false) {
            return redirect('rechargefailed');
        } else {
            $transaction_id = $response['transaction_id'];
        }

        return redirect('/rechargesuccessful/'.$transaction_id);
    }

    public function Plexpayrechargeinternationalnumber(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $method = $req->method;
        if ($method == 1) {
            $access_token = $auth['access_token'];
            $user_id = $auth['user_id'];
            $dash = $req->dash;
            $ProviderCode = $req->ProviderCode;
            $AccountNumber = $req->AccountNumber;
            $SkuCode = $req->SkuCode;
            $SendValue = $req->SendValue;
            $ReceiveValue = $req->ReceiveValue;
            $Our_SendValue = $req->Our_SendValue;
            $Country_Iso = $req->Country_Iso;
            $CoupenTitle = $req->CoupenTitle;

            if ($dash == 0) {
                $AccountNumber = substr($AccountNumber, 1);
            } else {
                $AccountNumber = substr($AccountNumber, 1);
            }
            $response1 = Http::withHeaders([
                'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
                'access-token' => $access_token,
                'user-id' => $user_id,
            ])->asForm()
                ->post('https://plexpay.netplexsolution.com/api/rechargeNow', [
                    'Dash' => $dash,
                    'ProviderCode' => $ProviderCode,
                    'AccountNumber' => $AccountNumber,
                    'SkuCode' => $SkuCode,
                    'SendValue' => $SendValue,
                    'ReceiveValue' => $ReceiveValue,
                    'Our_SendValue' => $Our_SendValue,
                    'Country_Iso' => $Country_Iso,
                    'CoupenTitle' => $CoupenTitle,
                ]);
        } elseif ($method == 0) {
            $access_token = $auth['access_token'];
            $user_id = $auth['user_id'];

            $response1 = Http::withHeaders([
                'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
                'access-token' => $access_token,
                'user-id' => $user_id,
            ])->asForm()
                ->post('https://plexpay.netplexsolution.com/api/customAmountRecharge', [
                    'Dash' => 0,
                    'AccountNumber' => $req->AccountNumber,
                    'Amount' => $req->amount,
                ]);
        } elseif ($method == 2) {
            $access_token = $auth['access_token'];
            $user_id = $auth['user_id'];
            $ProviderCode = $req->ProviderCode;
            $SkuCode = $req->SkuCode;
            $Our_SendValue = $req->Our_SendValue;
            $SendValue = $req->SendValue;
            $ReceiveValue = $req->ReceiveValue;
            $response1 = Http::withHeaders([
                'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
                'access-token' => $access_token,
                'user-id' => $user_id,
            ])->asForm()
                ->post('https://plexpay.netplexsolution.com/api/voucherRecharge_test', [
                    'Dash' => 1,
                    'ProviderCode' => $ProviderCode,
                    'SkuCode' => $SkuCode,
                    'SendValue' => $SendValue,
                    'Our_SendValue' => $Our_SendValue,
                    'ReceiveValue' => $ReceiveValue,
                ]);
        } elseif ($method == 3) {
            $access_token = $auth['access_token'];
            $user_id = $auth['user_id'];
            $ProviderCode = $req->ProviderCode;
            $SkuCode = $req->SkuCode;
            $amount = $req->amount;
            $ReceiveValue = $req->ReceiveValue;
            $ReceiveValue = $req->ReceiveValue;
            $Our_SendValue = $req->Our_SendValue;
            $Country_Iso = $req->Country_Iso;
            $CoupenTitle = $req->CoupenTitle;
            $SendValue = $req->SendValue;
            $AccountNumber = $req->AccountNumber;
            $providers = Http::withHeaders([
                'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
                'access-token' => $access_token,
                'user-id' => $user_id,
            ])->asForm()
                ->post('https://plexpay.netplexsolution.com/api/fetchCountryfornumber', [
                    'countryIso' => $Country_Iso,
                ]);

            $country_code = $providers['country'];
            $AccountNumber = $country_code.$AccountNumber;
            $response1 = Http::withHeaders([
                'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
                'access-token' => $access_token,
                'user-id' => $user_id,
            ])->asForm()
                ->post('https://plexpay.netplexsolution.com/api/rechargeNow', [
                    'Dash' => 1,
                    'ProviderCode' => $ProviderCode,
                    'AccountNumber' => $AccountNumber,
                    'SkuCode' => $SkuCode,
                    'SendValue' => $SendValue,
                    'ReceiveValue' => $amount,
                    'Our_SendValue' => $Our_SendValue,
                    'Country_Iso' => $Country_Iso,
                    'CoupenTitle' => $CoupenTitle,
                ]);
        } elseif ($method == 4) {
            $access_token = $auth['access_token'];
            $user_id = $auth['user_id'];
            $ProviderCode = $req->ProviderCode;
            $SkuCode = $req->SkuCode;
            $Our_SendValue = $req->Our_SendValue;
            $amount = $req->amount;
            $consumer_number = $req->AccountNumber;
            $response1 = Http::withHeaders([
                'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
                'access-token' => $access_token,
                'user-id' => $user_id,
            ])->asForm()
                ->post('https://plexpay.netplexsolution.com/api/rechargeNow_Two', [
                    'consumer_number' => $consumer_number,
                    'amount' => $amount,
                    'CountryIso' => $req->Country_Iso,
                    'ProviderCode' => $ProviderCode,
                ]);
        }
        if ($response1['status'] == false) {
            return redirect('rechargefailed');
        } else {
            $transaction_id = $response1['transaction_id'];
        }

        return redirect('/rechargesuccessful/'.$transaction_id);
    }

    public function Plexpayinternationalnumber(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $Our_SendValue = $req->Our_SendValue;
        $SkuCode = $req->SkuCode;
        $SendValue = $req->SendValue;
        $ReceiveValue = $req->ReceiveValue;
        $Country_Iso = $req->Country_Iso;
        $ProviderCode = $req->ProviderCode;
        $ProviderLogo = $req->providerlogo;
        $providername = $req->providername;
        $CoupenTitle = $req->CoupenTitle;
        $SendCurrencyIso = $req->SendCurrencyIso;
        $method = $req->method;
        $phonenumber = 0;

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];

        $response = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 1,
            ]);

        $wallet_amount = $response['wallet_amount'];
        $due_amount = $response['due_amount'];

        return view('/plexpay/rechargeconfirm', ['users' => $item, 'SendCurrencyIso' => $SendCurrencyIso, 'Expiry_Date' => '', 'amount' => 0, 'dash' => 0, 'phonenumber' => $phonenumber, 'method' => $method, 'CoupenTitle' => $CoupenTitle, 'due_amount' => $due_amount, 'ProviderName' => $providername, 'ProviderLogo' => $ProviderLogo, 'wallet_amount' => $wallet_amount, 'Country_Iso' => $Country_Iso, 'ProviderCode' => $ProviderCode, 'Our_SendValue' => $Our_SendValue, 'SkuCode' => $SkuCode, 'SendValue' => $SendValue, 'ReceiveValue' => $ReceiveValue, 'SendCurrencyIso' => $SendCurrencyIso, 'Expiry_Date' => '', 'amount' => 0, 'dash' => 0, 'phonenumber' => $phonenumber, 'method' => $method, 'CoupenTitle' => $CoupenTitle, 'due_amount' => $due_amount, 'ProviderName' => $providername, 'ProviderLogo' => $ProviderLogo, 'wallet_amount' => $wallet_amount, 'Country_Iso' => $Country_Iso, 'ProviderCode' => $ProviderCode, 'Our_SendValue' => $Our_SendValue, 'SkuCode' => $SkuCode, 'SendValue' => $SendValue, 'ReceiveValue' => $ReceiveValue, 'SendCurrencyIso' => $SendCurrencyIso, 'Expiry_Date' => '', 'amount' => 0, 'dash' => 0, 'phonenumber' => $phonenumber, 'method' => $method, 'CoupenTitle' => $CoupenTitle, 'due_amount' => $due_amount, 'ProviderName' => $providername, 'ProviderLogo' => $ProviderLogo, 'wallet_amount' => $wallet_amount, 'Country_Iso' => $Country_Iso, 'ProviderCode' => $ProviderCode, 'Our_SendValue' => $Our_SendValue, 'SkuCode' => $SkuCode, 'SendValue' => $SendValue, 'ReceiveValue' => $ReceiveValue, 'SendCurrencyIso' => $SendCurrencyIso, 'Expiry_Date' => '', 'amount' => 0, 'dash' => 0, 'phonenumber' => $phonenumber, 'method' => $method, 'CoupenTitle' => $CoupenTitle, 'due_amount' => $due_amount, 'ProviderName' => $providername, 'ProviderLogo' => $ProviderLogo, 'wallet_amount' => $wallet_amount, 'Country_Iso' => $Country_Iso, 'ProviderCode' => $ProviderCode, 'Our_SendValue' => $Our_SendValue, 'SkuCode' => $SkuCode, 'SendValue' => $SendValue, 'ReceiveValue' => $ReceiveValue]);
    }

    public function plexpaylocalnumber(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);
        $method = $req->method;
        $id = $req->carrier_code;
        $phone = $req->phone;
        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $userid = Session('softwareuser');
        $products = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/plansBy_Provider', [
                'dash' => '0',
                'providerCode' => $id,
            ]);

        $plans = $products['products']['PlanInfo'];
        $providers = $products['products']['ProviderInfo'];
        $dash = 0;

        return view('/plexpay/localproviderconfirm', ['users' => $item, 'method' => $method, 'dash' => $dash, 'phone' => $phone, 'carrier_code' => $id, 'providers' => $providers, 'plans' => $plans, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount]);
    }

    public function Plexpaybalance()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $response = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $cat = 'prepaid';
        $reports = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/fetchsubcategory', []);
        $category = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/fetchcategory', []);
        $categories = $category['category'];

        $provider_info = $reports['provider'];

        $wallet_amount = $response['wallet_amount'];
        $due_amount = $response['due_amount'];
        $local_providers = $response['local_providers'];

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        return view('/plexpay/balance', ['users' => $item, 'categories' => $categories, 'due_amount' => $due_amount, 'wallet_amount' => $wallet_amount, 'local_providers' => $provider_info]);
    }

    public function Plexpayreports()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $start_date = '';
        $end_date = '';

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $reports = [];

        $point_name = '';
        $Duration = '';
        $Balance = '';
        $total_amount = '';
        $total_commission = '';

        return view('/plexpay/reports', ['users' => $item, 'start_date' => $start_date, 'total_commission' => $total_commission, 'total_amount' => $total_amount, 'Balance' => $Balance, 'point_name' => $point_name, 'Duration' => $Duration, 'wallet_amount' => $wallet_amount, 'reports' => $reports, 'due_amount' => $due_amount, 'end_date' => $end_date]);
    }

    public function Plexpayreportsearch(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $start_date = $req->start_date;
        $end_date = $req->end_date;

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $reports = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/rechargeReport', [
                'start_date' => $req->start_date,
                'end_date' => $req->end_date,
            ]);

        $point_name = $reports['point_name'];
        $Duration = $reports['Duration'];
        $Balance = $reports['Balance'];
        $total_amount = $reports['total_amount'];
        $total_commission = $reports['total_commission'];

        $reports = $reports['result'];

        return view('/plexpay/reports', ['users' => $item, 'start_date' => $start_date, 'total_commission' => $total_commission, 'total_amount' => $total_amount, 'Balance' => $Balance, 'point_name' => $point_name, 'Duration' => $Duration, 'wallet_amount' => $wallet_amount, 'reports' => $reports, 'due_amount' => $due_amount, 'end_date' => $end_date]);
    }

    public function Plexpayrechargeinternational()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $countries = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/fetchCountryfordisplay', []);
        $countries = $countries['country'];

        return view('/plexpay/plexpayinternational', ['users' => $item, 'countires' => $countries, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount]);
    }

    public function Internationalbycountry($id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $providers = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/fetchCountryfornumber', [
                'countryIso' => $id,
            ]);

        $country_code = $providers['country'];

        return view('/plexpay/internationalfetchplanbynumber', ['users' => $item, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'dial_code' => $country_code]);
    }

    public function Internationalbyprovider($id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com//auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $products = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/plansBy_Provider', [
                'dash' => '1',
                'providerCode' => $id,
            ]);

        $plans = $products['products']['PlanInfo'];
        $providers = $products['products']['ProviderInfo'];
        $id = 1;

        return view('/plexpay/dthrechargeplans', ['users' => $item, 'id' => $id, 'providers' => $providers, 'plans' => $plans, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount]);
    }

    public function localprovider($id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        return view('/plexpay/localbyprovider', ['users' => $item, 'carrier_code' => $id, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount]);
    }

    public function Plexpayfunds()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $start_date = '';
        $end_date = '';
        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $today = Carbon::now()->format('Y-m-d');

        $funds = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/fundings', [
                'start_date' => $today,
                'end_date' => $today,
            ]);

        if (($funds['status'] == false) || ($funds['result'] == 'Empty')) {
            $funds = [];
        } else {
            $funds = $funds['result']['all_data'];
        }

        return view('/plexpay/funds', ['users' => $item, 'funds' => $funds, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'start_date' => $start_date, 'end_date' => $end_date]);
    }

    public function Plexpaytransactions()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $start_date = '';
        $end_date = '';

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $today = Carbon::now()->format('Y-m-d');

        $response = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/get_transactions', [
                'start_date' => $today,
                'end_date' => $today,
            ]);

        if ($response['status'] == false) {
            $transaction = [];
        } else {
            $transaction = $response['transactions'];
        }

        return view('/plexpay/transactions', ['users' => $item, 'start_date' => $start_date, 'transactions' => $transaction, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'end_date' => $end_date]);
    }

    public function Plexpaycollection()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $start_date = '';
        $end_date = '';

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $today = Carbon::now()->format('Y-m-d');

        $response = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/collections', [
                'start_date' => $today,
                'end_date' => $today,
            ]);

        if (($response['status'] == false) || ($response['result'] == 'Empty')) {
            $collection = [];
        } else {
            $collection = $response['result'];
        }

        return view('/plexpay/collection', ['users' => $item, 'start_date' => $start_date, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'end_date' => $end_date, 'collections' => $collection]);
    }

    public function Plexpayduesummary()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $start_date = '';
        $end_date = '';

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $today = Carbon::now()->format('Y-m-d');

        $due_summary = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/due_summary', [
                'start_date' => $today,
                'end_date' => $today,
            ]);

        if (($due_summary['status'] == false) || ($due_summary['result'] == 'Empty')) {
            $due_summary = [];
        } else {
            $due_summary = $due_summary['result'];
        }

        return view('/plexpay/duesummary', ['users' => $item, 'start_date' => $start_date, 'end_date' => $end_date, 'due_summarys' => $due_summary, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount]);
    }

    public function Plexpaysummary()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $start_date = '';
        $end_date = '';

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];
        $today = Carbon::now()->format('Y-m-d');

        $summary = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/profit_report', [
                'start_date' => $today,
                'end_date' => $today,
            ]);

        if (($summary['status'] == false) || ($summary['result'] == 'Empty')) {
            $summary = [];
        } else {
            $summary = $summary['result'];
        }

        return view('/plexpay/summary', ['users' => $item, 'start_date' => $start_date, 'summarys' => $summary, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'end_date' => $end_date]);
    }

    public function PlexpayVoucherrechargeconfirm(Request $req, $id1)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('userlogout');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        if ($id1 == 1) {
            $name = $req->name;
            $voucher_id = $req->voucher_id;
            $providerCode = $req->providerCode;
            $mrp = $req->mrp;
            $providerinfo = Http::withHeaders([
                'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
                'access-token' => $access_token,
                'user-id' => $user_id,
            ])->asForm()
                ->post('https://plexpay.netplexsolution.com/api/getVoucherInfo', [
                    'voucher_ID' => $voucher_id,
                    'categoryName' => $providerCode,
                ]);

            $provider_code = $providerinfo['voucher_info']['providerCode'];

            $planId = $providerinfo['voucher_info']['planId'];

            $mrp = $providerinfo['voucher_info']['mrp'];
            $response = Http::withHeaders([
                'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
                'access-token' => $access_token,
                'user-id' => $user_id,
            ])->asForm()
                ->post('https://plexpay.netplexsolution.com/api/voucherRecharge', [
                    'Dash' => 0,
                    'planId' => $planId,
                    'providerCode' => $provider_code,
                    'mrp' => $mrp,
                ]);
        } elseif ($id1 == 2) {
            $ProviderCode = $req->ProviderCode;
            $SkuCode = $req->SkuCode;
            $Our_SendValue = $req->Our_SendValue;
            $SendValue = $req->SendValue;
            $response = Http::withHeaders([
                'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
                'access-token' => $access_token,
                'user-id' => $user_id,
            ])->asForm()
                ->post('https://plexpay.netplexsolution.com/api/voucherRecharge_test', [
                    'Dash' => 1,
                    'ProviderCode' => $ProviderCode,
                    'SkuCode' => $SkuCode,
                    'SendValue' => $SendValue,
                    'Our_SendValue' => $Our_SendValue,
                ]);
        }
        if ($response['status'] == false) {
            return redirect('rechargefailed');
        } else {
            $transaction_id = $response['transaction_id'];
        }

        return redirect('/rechargesuccessful/'.$transaction_id);
    }

    public function Plexpaycustomrecharge($id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('userlogout');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        return view('/plexpay/customrecharge', ['users' => $item, 'carrier_code' => $id, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount]);
    }

    public function localCustom($id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('userlogout');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];
        $dash = 0;

        return view('/plexpay/localcustom', ['users' => $item, 'dash' => $dash, 'carrier_code' => $id, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount]);
    }

    public function Plexpaylocalvoucherrecharge($id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('userlogout');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $vouchers = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/getVouchers', [
                'Dash' => 0,
            ]);

        $vouchers = $vouchers['result']['data'];
        $vouchers = $vouchers[0];
        $plans = $vouchers['categories'];
        if ($id == 'DU UAE') {
            $plans = $plans[0];
            $plans = $plans['serviceCategoryList'];
            $plans = $plans[0]['myServiceDataList'];
            $providerCode = $id;
        } elseif ($id == 'Etisalat UAE') {
            $plans = $plans[1];
            $plans = $plans['serviceCategoryList'];
            $plans = $plans[0]['myServiceDataList'];
            $providerCode = $id;
        } elseif ($id == 'Salik UAE') {
            $plans = $plans[2];
            $plans = $plans['serviceCategoryList'];
            $plans = $plans[0]['myServiceDataList'];
            $providerCode = $id;
        } elseif ($id == 'Hello! Intl VOIP Calling Card') {
            $plans = $plans[3];
            $plans = $plans['serviceCategoryList'];
            $plans = $plans[0]['myServiceDataList'];
            $providerCode = $id;
        } elseif ($id == 'Five Intl VOIP Calling Card') {
            $plans = $plans[4];
            $plans = $plans['serviceCategoryList'];
            $plans = $plans[0]['myServiceDataList'];
            $providerCode = $id;
        }

        return view('/plexpay/localvoucherrecharge', ['users' => $item, 'providerCode' => $providerCode, 'vouchers' => $vouchers, 'plans' => $plans, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount]);
    }

    public function Plexpaygiftcardrecharge($id, Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('userlogout');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $userid = Session('softwareuser');
        $products = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/getCustomAmount_one', [
                'amount' => $req->Amount,
            ]);

        $ReceiveValue = $req->ReceiveValue;
        $SkuCode = $req->SkuCode;
        $ProviderCode = $req->ProviderCode;
        $amount = $req->Amount;
        $ReceiveCurrencyIso = $req->ReceiveCurrencyIso;
        $Our_SendValue = $products['Our_SendValue'];
        $Country_Iso = $req->Country_Iso;
        $SendValue = $products['SendValue'];
        $SendCurrencyIso = $req->SendCurrencyIso;
        $Account = $req->AccountNumber;

        return view('/plexpay/plexpayinternationalprovider', ['users' => $item, 'Account' => $Account, 'SendCurrencyIso' => $SendCurrencyIso, 'SendValue' => $SendValue, 'Country_Iso' => $Country_Iso, 'Our_SendValue' => $Our_SendValue, 'wallet_amount' => $wallet_amount, 'AccountNumber' => '', 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'ReceiveCurrencyIso' => $ReceiveCurrencyIso, 'amount' => $amount, 'ReceiveValue' => $ReceiveValue, 'SkuCode' => $SkuCode, 'ProviderCode' => $ProviderCode, 'id' => $id]);
    }

    public function localvoucherconfirm(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $auth = json_decode($auth, true);
        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];

        $name = $req->name;
        $voucher_id = $req->voucher_id;
        $providerCode = $req->providerCode;
        $mrp = $req->mrp;
        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        return view('/plexpay/voucherrechargeconfirm', ['users' => $item, 'mrp' => $mrp, 'name' => $name, 'voucher_id' => $voucher_id, 'providerCode' => $providerCode, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'mrp' => $mrp]);
    }

    public function plexpayFundssearch(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $start_date = $req->start_date;
        $end_date = $req->end_date;

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $funds = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/fundings', [
                'start_date' => $req->start_date,
                'end_date' => $req->end_date,
            ]);

        $funds = $funds['result']['all_data'];

        return view('/plexpay/funds', ['users' => $item, 'start_date' => $start_date, 'end_date' => $end_date, 'funds' => $funds, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount]);
    }

    public function plexpayTransactionsearch(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $start_date = $req->start_date;
        $end_date = $req->end_date;

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $response = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/get_transactions', [
                'start_date' => $req->start_date,
                'end_date' => $req->end_date,
            ]);

        if ($response['status'] == false) {
            $transaction = [];
        } else {
            $transaction = $response['transactions'];
        }

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        return view('/plexpay/transactions', ['users' => $item, 'start_date' => $start_date, 'end_date' => $end_date, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'transactions' => $transaction]);
    }

    public function plexpayCollectionsearch(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $response = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/collections', [
                'start_date' => $req->start_date,
                'end_date' => $req->end_date,
            ]);

        if (($response['status'] == false) || ($response['result'] == 'Empty')) {
            $collection = [];
        } else {
            $collection = $response['result'];
        }

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $start_date = $req->start_date;
        $end_date = $req->end_date;

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        return view('/plexpay/collection', ['users' => $item, 'start_date' => $start_date, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'end_date' => $end_date, 'collections' => $collection]);
    }

    public function plexpayDuesummarysearch(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $start_date = $req->start_date;
        $end_date = $req->end_date;

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $due_summary = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/due_summary', [
                'start_date' => $req->start_date,
                'end_date' => $req->end_date,
            ]);

        $due_summary = $due_summary['result'];

        return view('/plexpay/duesummary', ['users' => $item, 'start_date' => $start_date, 'due_summarys' => $due_summary, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'end_date' => $end_date]);
    }

    public function plexpaySummarysearch(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $start_date = $req->start_date;
        $end_date = $req->end_date;

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $summary = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/profit_report', [
                'start_date' => $req->start_date,
                'end_date' => $req->end_date,
            ]);

        $summary = $summary['result'];

        return view('/plexpay/summary', ['users' => $item, 'start_date' => $start_date, 'summarys' => $summary, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'end_date' => $end_date]);
    }

    public function PlexpayrechargeInternationalProvider($id, $cid, $pcode, Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('userlogout');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com//api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        if ($id == 1) {
            $ReceiveValue = $req->ReceiveValue;
            $SkuCode = $req->SkuCode;
            $ProviderCode = $req->ProviderCode;
            $amount = $req->amount;
            $ReceiveCurrencyIso = $req->ReceiveCurrencyIso;
            $Our_SendValue = $req->Our_SendValue;
            $Country_Iso = $req->Country_Iso;
            $SendValue = $req->SendValue;
            $SendCurrencyIso = $req->SendCurrencyIso;
            $Account = '';
        } else {
            $ReceiveValue = 0;
            $SkuCode = 0;
            $ProviderCode = $pcode;
            $ReceiveCurrencyIso = '';
            $amount = 0;
            $Our_SendValue = 0;
            $Country_Iso = $cid;
            $SendValue = 0;
            $SendCurrencyIso = 0;
            $Account = '';
        }

        return view('/plexpay/plexpayinternationalprovider', ['users' => $item, 'Account' => $Account, 'SendCurrencyIso' => $SendCurrencyIso, 'SendValue' => $SendValue, 'Country_Iso' => $Country_Iso, 'Our_SendValue' => $Our_SendValue, 'wallet_amount' => $wallet_amount, 'AccountNumber' => '', 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'ReceiveCurrencyIso' => $ReceiveCurrencyIso, 'amount' => $amount, 'ReceiveValue' => $ReceiveValue, 'SkuCode' => $SkuCode, 'ProviderCode' => $ProviderCode, 'id' => $id]);
    }

    public function PlexpayrechargeInternationalPlans($country, $provider)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $phonenumber = '';

        return view('/plexpay/plexpayinternationalplans', ['users' => $item, 'phonenumber' => $phonenumber, 'countrycode' => $country, 'providercode' => $provider]);
    }

    public function PlexpayrechargeInternationalCustom($country, $provider, Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $phonenumber = $req->AccountNumber;
        $phonenumber = substr($phonenumber, 1);
        $Amount = $req->Amount;
        $maxValue = $req->maxValue;
        $minValue = $req->minValue;
        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/getCustomAmount', [
                'mobile' => $phonenumber,
                'amount' => $Amount,
            ]);
        $DenominationInfo = $account['DenominationInfo'];
        // var_dump($DenominationInfo);
        $number = '+';
        $number .= $phonenumber;
        $id = 1;

        return view('/plexpay/internationalamountconversion', ['users' => $item, 'maxValue' => $maxValue, 'minValue' => $minValue, 'id' => $id, 'phonenumber' => $number, 'Amount' => $Amount, 'DenominationInfo' => $DenominationInfo]);
    }

    public function plexpayrechargesuccessful($transaction_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('userlogout');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        return view('/plexpay/rechargesuccess', ['users' => $item, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'transaction_id' => $transaction_id]);
    }

    public function rechargesuccessfulvoucher($transaction_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('userlogout');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        return view('/plexpay/rechargesuccess', ['users' => $item, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'transaction_id' => $transaction_id]);
    }

    public function plexpayrechargefailed()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('userlogout');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        return view('/plexpay/rechargefailed', ['users' => $item, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount]);
    }

    public function plexpaypasswordchangepost(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('plexpayregister');
            }
        }

        $auth = json_decode($auth, true);
        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];

        $old_password = $req->old_password;
        $new_password = $req->new_password;
        $conf_password = $req->conf_password;

        $response = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/password_update', [
                'old_password' => $old_password,
                'new_password' => $new_password,
                'conf_password' => $conf_password,
            ]);

        if ($response['success'] == true) {
            $plexpayuserdata = DB::table('plexpayusers')
                ->updateOrInsert(
                    ['id' => 1],
                    ['password' => $conf_password]
                );

            return back()->with('success', 'Password Change Successfully!');
        } else {
            return back()->with('success', 'Password Change Failed!');
        }
    }

    public function plexpaychangepassword()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('userlogout');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        return view('/plexpay/changepassword', ['users' => $item, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount]);
    }

    public function giftcardrecharge1($id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('userlogout');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $userid = Session('softwareuser');
        $products = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/plansBy_Provider', [
                'dash' => '1',
                'providerCode' => $id,
            ]);

        $plans = $products['products']['PlanInfo'];
        $providers = $products['products']['ProviderInfo'];

        return view('/plexpay/internationalbyprovider', ['users' => $item, 'carrier_code' => $id, 'providers' => $providers, 'plans' => $plans, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount]);
    }

    public function ElectricityGETpayment(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $username = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('user_id')
            ->first();
        $password = DB::table('plexpayusers')
            ->where('id', 1)
            ->pluck('password')
            ->first();
        if (empty($password)) {
            return redirect('plexpayregister');
        }
        $password = Crypt::decrypt($password);
        if ($username == '' || $password == '') {
            return redirect('plexpayregister');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $auth = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/login_action', [
                'username' => $username,
                'password' => $password,
            ]);

        if (!empty($auth['error'])) {
            if ($auth['error'] == true) {
                return redirect('userlogout');
            }
        }

        $auth = json_decode($auth, true);

        $access_token = $auth['access_token'];
        $user_id = $auth['user_id'];
        $name = $auth['name'];

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $summary = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/KsebBill', [
                'consumer_number' => $req->AccountNumber,
                'CountryIso' => $req->Country_Iso,
                'ProviderCode' => $req->ProviderCode,
            ]);
        // var_dump($summary['message']);
        if (!empty($summary['plan_info'])) {
            $plan_info = $summary['plan_info'];
        } else {
            $plan_info = $summary['message'];
        }

        $ReceiveValue = 0;
        $SkuCode = 0;
        $ReceiveCurrencyIso = '';
        $amount = '';
        $id = 2;
        $Country_Iso = $req->Country_Iso;
        $ProviderCode = $req->ProviderCode;
        $ReceiveValue = 0;
        $SkuCode = 0;
        $ReceiveCurrencyIso = '';
        $amount = 0;
        $Our_SendValue = 0;
        $SendValue = 0;
        $SendCurrencyIso = 0;

        return view('/plexpay/plexpayinternationalprovider', ['users' => $item, 'SendCurrencyIso' => $SendCurrencyIso, 'Country_Iso' => $Country_Iso, 'Our_SendValue' => $Our_SendValue, 'SendValue' => $SendValue, 'plan_info' => $plan_info, 'AccountNumber' => $req->AccountNumber, 'id' => $id, 'wallet_amount' => $wallet_amount, 'due_amount' => $due_amount, 'ReceiveCurrencyIso' => $ReceiveCurrencyIso, 'amount' => $amount, 'ReceiveValue' => $ReceiveValue, 'SkuCode' => $SkuCode, 'ProviderCode' => $ProviderCode, 'id' => $id]);
    }

    public function createUnit(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $req->validate([
            'unit' => 'required',
        ]);
        $userid = Session('softwareuser');
        $units = new Unit();
        $units->branch_id = $branch;
        $units->user_id = $userid;
        $units->unit = $req->unit;
        $units->save();

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' created new unit '.$req->unit;
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */
        return redirect('/listcategory');
        // return $req;
    }

    public function getUnit($pro_id)
    {
        echo json_encode(DB::table('products')->where('id', $pro_id)->get());
    }

    public function getbarcodedata($barcode)
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $items = DB::table('products')
            ->select(DB::raw('id, product_name, selling_cost, buy_cost, vat, round((((vat * selling_cost)/ 100 ) + selling_cost), 2) as netrate, remaining_stock, unit, inclusive_rate, rate'))
            ->where('barcode', $barcode)
            ->where('branch', $branch)
            ->where('status', 1)
            ->get();
        echo json_encode($items);
    }

    public function exportExcel()
    {
        $userid = Session('softwareuser');

        return Excel::download(new ProductsExport($userid), 'Products.xlsx');
    }

  public function monthwiseExpenseHistory(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::where('id', $adminid)
            ->pluck('currency')
            ->first();

        $today = Carbon::now();
        $month = $today->month;
        $year = $today->year;

        $branchid = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $branch = Accountantloc::leftJoin('branches', 'accountantlocs.location_id', '=', 'branches.id')
            ->where('accountantlocs.user_id', $userid)
            ->get();


        $expenses = DB::table('accountexpenses')
            ->leftJoin('branches', 'accountexpenses.branch', '=', 'branches.id')
            ->select(DB::raw(
                'accountexpenses.direct_expense,
                accountexpenses.id,
                accountexpenses.indirect_expense,
                accountexpenses.details,
                accountexpenses.amount,
                accountexpenses.date,
                accountexpenses.branch,
                accountexpenses.user_id,
                accountexpenses.file,
                accountexpenses.created_at,
                accountexpenses.expense_type,
                branches.branchname as branchname'
            ))
            ->where('accountexpenses.branch', $branchid)
            ->orderBy('accountexpenses.created_at', 'DESC')
            ->get();
            
         $start_date = '';
            $end_date = '';
        return view('/accountant/monthwiseexpencehistory', [
            'users' => $useritem,
            'expenses' => $expenses,
            'start_date' => $start_date,
            'end_date'=>$end_date,
            'branch' => $branch,
            'currency' => $currency
        ]);
    }


    public function monthwiseExpenseHistoryDate(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $userid = session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::where('id', $adminid)
            ->pluck('currency')
            ->first();

        $branchid = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $branch = Accountantloc::leftJoin('branches', 'accountantlocs.location_id', '=', 'branches.id')
            ->where('accountantlocs.user_id', $userid)
            ->get();

        // Get the start and end dates from the request
        $start_date = $req->input('start_date');
        $end_date = $req->input('end_date');

        // Base query
        $query = DB::table('accountexpenses')
            ->leftJoin('branches', 'accountexpenses.branch', '=', 'branches.id')
            ->select(DB::raw(
                'accountexpenses.direct_expense,
                accountexpenses.id,
                accountexpenses.indirect_expense,
                accountexpenses.details,
                accountexpenses.amount,
                accountexpenses.date,
                accountexpenses.branch,
                accountexpenses.user_id,
                accountexpenses.file,
                accountexpenses.created_at,
                accountexpenses.expense_type,
                branches.branchname as branchname'
            ))
            ->where('accountexpenses.branch', $branchid)
            ->orderBy('accountexpenses.created_at', 'DESC');

        // Apply date filter if both dates are provided
        if ($start_date && $end_date) {
            // Convert end_date to include the entire day
            $end_date_adjusted = date('Y-m-d', strtotime($end_date . ' +1 day'));
            $start_date_formatted = date('Y-m-d 00:00:00', strtotime($start_date));
            $end_date_formatted = date('Y-m-d 23:59:59', strtotime($end_date));

            $query->whereDate('accountexpenses.created_at', '>=', $start_date_formatted)
                  ->whereDate('accountexpenses.created_at', '<=', $end_date_formatted);
        }

        $expenses = $query->get();

        return view('/accountant/monthwiseexpencehistory', [
            'users' => $useritem,
            'expenses' => $expenses,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'branch' => $branch,
            'currency' => $currency
        ]);
    }
    public function getproductdata($trans_id)
    {
        $product = DB::table('buyproducts')
            ->leftJoin('products', 'buyproducts.product_id', '=', 'products.id')
            ->select(DB::raw('*'))
            ->where('buyproducts.transaction_id', $trans_id)
            ->get();

        $user_location = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $creditid = DB::table('buyproducts')
            ->select(DB::raw('credit_user_id'))
            ->where('buyproducts.transaction_id', $trans_id)
            ->distinct('buyproducts.transaction_id', $trans_id)
            ->pluck('credit_user_id') // Pluck the credit_user_id directly
            ->first(); // Get the first result

        $credit = DB::table('creditusers')
            ->select(DB::raw('name'))
            ->where('id', $creditid)
            ->where('status', 1)
            ->where('location', $user_location)
            ->pluck('name')
            ->first();

        $payment_type = DB::table('buyproducts')
            ->where('buyproducts.transaction_id', $trans_id)
            ->distinct('buyproducts.transaction_id', $trans_id)
            ->pluck('payment_type');

        $cash_user_id = DB::table('buyproducts')
            ->select(DB::raw('cash_user_id'))
            ->where('buyproducts.transaction_id', $trans_id)
            ->distinct('buyproducts.transaction_id', $trans_id)
            ->pluck('cash_user_id')
            ->first();

        return response()->json([
            'product' => $product,
            'credit' => $credit,
            'credit_id' => $creditid,
            'payment_type' => $payment_type,
            'cash_user_id' => $cash_user_id,
        ]);
    }

    public function getsoldquantity($trans_id, $pro_id)
    {
        $soldquantity = Buyproduct::where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            // ->value('remain_quantity');
            ->pluck('remain_quantity')
            ->first();


        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $sales_details = DB::table('buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('mrp');

        $buycost = DB::table('buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('one_pro_buycost');

        $fixed_vat_value = DB::table('buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('fixed_vat')
            ->first();

        $vat_type = DB::table('buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('vat_type')
            ->first();

        $buycost_rate = DB::table('buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('one_pro_buycost_rate');

        $discount = DB::table('buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('discount');

        $discount_type = DB::table('buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('discount_type');

        $total_discount_percent = DB::table('buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('total_discount_percent');

        $total_disc_amount = DB::table('buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('total_discount_amount');

        if ($vat_type == 1) {
            $inclusive_rate = DB::table('buyproducts')
                ->where('transaction_id', $trans_id)
                ->where('product_id', $pro_id)
                ->where('branch', $branch)
                ->pluck('inclusive_rate')
                ->first();

            return response()->json([
                'soldquantity' => $soldquantity,
                'sales_details' => $sales_details,
                'buycost' => $buycost,
                'fixed_vat_value' => $fixed_vat_value,
                'vat_type' => $vat_type,
                'inclusive_rate' => $inclusive_rate,
                'buycost_rate' => $buycost_rate,
                'discount' => $discount,
                'discount_type' => $discount_type,
                'total_discount_percent' => $total_discount_percent,
                'total_disc_amount' => $total_disc_amount,
            ]);
        }
        if ($vat_type == 2) {
            $exclusive_rate = DB::table('buyproducts')
                ->where('transaction_id', $trans_id)
                ->where('product_id', $pro_id)
                ->where('branch', $branch)
                ->pluck('exclusive_rate')
                ->first();

            return response()->json([
                'soldquantity' => $soldquantity,
                'sales_details' => $sales_details,
                'buycost' => $buycost,
                'fixed_vat_value' => $fixed_vat_value,
                'vat_type' => $vat_type,
                'buycost_rate' => $buycost_rate,
                'discount' => $discount,
                'discount_type' => $discount_type,
                'total_discount_percent' => $total_discount_percent,
                'total_disc_amount' => $total_disc_amount,
                'exclusive_rate' => $exclusive_rate,
            ]);
        }
    }

    public function getremainstock_purchase($receiptno, $pro_id)
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        /* purchase wise including bill quantity reducing - new */

        $purchase_remain = DB::table('stock_purchase_reports')
            ->select(DB::raw('sell_quantity'))
            ->where('receipt_no', $receiptno)
            ->where('product_id', $pro_id)
            ->where('branch_id', $branch)
            ->get();

    //  $retunquantity =DB::table('returnpurchases')
    //     ->select(DB::raw('quantity'))
    //     ->where('receipt_no', $receiptno)
    //     ->where('product_id', $pro_id)
    //     ->where('branch_id', $branch)
    //     ->get();
       $purchase_details = DB::table('stockdetails')
        ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
        ->leftJoin('stock_purchase_reports', 'stockdetails.id', '=', 'stock_purchase_reports.purchase_id')
        ->leftJoin('returnpurchases', function($join) {
            $join->on('stockdetails.reciept_no', '=', 'returnpurchases.reciept_no')
                 ->on('stockdetails.product', '=', 'returnpurchases.product_id');
        })
        ->select(DB::raw('stockdetails.discount,stockdetails.discount_percent,stockdetails.quantity,stockdetails.id, stockdetails.reciept_no, stockdetails.product, stockdetails.supplier, products.product_name, stockdetails.price, stockdetails.quantity, stockdetails.payment_mode, stockdetails.buycost, stockdetails.unit, stockdetails.vat_amount, stockdetails.vat_percentage, stock_purchase_reports.purchase_trans_id, stockdetails.supplier_id, stockdetails.rate,stockdetails.vat'))
        ->where('stockdetails.reciept_no', $receiptno)
        ->where('stockdetails.product', $pro_id)
        ->where('stockdetails.branch', $branch)
        ->get();

        $returnPrice = DB::table('returnpurchases')
            ->select(DB::raw('amount'))
            ->where('reciept_no', $receiptno)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->get();

        // echo json_encode($purchase_remain);
        return response()->json([
            'purchase_Data' => $purchase_details,
            'purchase_remain' => $purchase_remain,
            'returnPrice' => $returnPrice,
        ]);
    }

    public function exportExcelSalesReport($userid, $viewtype, $location, $date)
    {
        $userid = Session('softwareuser');

        $users = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        if ($viewtype == 1) {
            return Excel::download(new SalesExport($userid, $viewtype, $location, $date, $users), $date.'- SalesReport-Date wise.xlsx');
        } elseif ($viewtype == 2) {
            $monthname = date('F', mktime($date));

            return Excel::download(new SalesExport($userid, $viewtype, $location, $date, $users), $monthname.'- SalesReport - Month Wise.xlsx');
        } elseif ($viewtype == 3) {
            return Excel::download(new SalesExport($userid, $viewtype, $location, $date, $users), $date.'- SalesReport - Year wise.xlsx');
        }
    }

    public function getFundHistory($userid)
    {
        $due = DB::table('supplier_credits')
            ->select(DB::raw('due_amt'))
            ->where('supplier_id', $userid)
            ->pluck('due_amt')
            ->first();
        $paid = DB::table('supplier_credits')
            ->select(DB::raw('collected_amt'))
            ->where('supplier_id', $userid)
            ->pluck('collected_amt')
            ->first();

        $due -= $paid;

        $suppliername = DB::table('suppliers')->where('id', $userid)->pluck('name')->first();

        $receiptnos = DB::table('stockdetails')
            ->where('payment_mode', 2)
            ->where('supplier_id', $userid)
            ->distinct('reciept_no')
            ->pluck('reciept_no');

        return response()->json([
            'supplierid' => $userid,
            'due' => $due,
            'suppliername' => $suppliername,
            'receiptnos' => $receiptnos,
        ]);
    }

    public function addSupplierCreditFund(Request $req)
    {
        // $req->validate([
        //     'addedcollectfund' => 'required',
        // ]);

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

    $bankName = $req->input('bank_name');

    $bank = DB::table('bank')->where('bank_name', $bankName)->first();

    if ($bank) {
        $accountName = $bank->account_name;
    } else {
        $accountName = null;
    }
        if ($req->input('addedcollectfund')) {
            $supplierfund = new SupplierFundhistory();
            $supplierfund->suppliername = $req->input('fundsuppliername');
            $supplierfund->collectedamount = $req->input('addedcollectfund');
            $supplierfund->supplierid = $req->input('supplierid');
            $supplierfund->due = $req->input('dueamount');
            $supplierfund->userid = Session('softwareuser');
            $supplierfund->branch = $branch;
            $supplierfund->reciept_no = $req->input('receipt_no');
            $supplierfund->product_id = $req->input('receipt_product');
            $supplierfund->debit_note = $req->input('debitnote');

            $supplierfund->payment_type = $req->input('payment_option');
            $supplierfund->check_number = $req->input('check_number');
            $supplierfund->depositing_date = $req->input('check_date');
            $supplierfund->reference_number = $req->input('reference_no');
            $supplierfund->bank_id = $req->input('bank_name');
            $supplierfund->account_name = $accountName;
            $supplierfund->save();
        }

        $supplier_creditcollected = DB::table('supplier_credits')
            ->where('supplier_id', $req->supplierid)
            ->pluck('collected_amt')
            ->first();

        $supplier_debit_note_collected = DB::table('supplier_credits')
            ->where('supplier_id', $req->supplierid)
            ->pluck('debitnote')
            ->first();

        $supplier_livecollected = $req->input('addedcollectfund') ?? null;
        $supplier_live_debitnote_collected = $req->input('debitnote') ?? null;

        $supplier_totcreditcollected = $supplier_creditcollected + $supplier_livecollected;
        $supplier_total_debit_note = ($supplier_debit_note_collected + $supplier_live_debitnote_collected);

        $supplier_credits = DB::table('supplier_credits')
            ->updateOrInsert(
                ['supplier_id' => $req->input('supplierid')],
                [
                    'collected_amt' => $supplier_totcreditcollected,
                    'debitnote' => $supplier_total_debit_note,
                ]
            );

        // new credit_supplier_transactions table

        $lastTransaction = DB::table('credit_supplier_transactions')
            ->where('credit_supplier_id', $req->input('supplierid'))
            ->where('location', $branch)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

$lastSameTransaction = DB::table('credit_supplier_transactions')
    ->where('credit_supplier_id', $req->input('supplierid'))
    ->where('location', $branch)
    ->where('reciept_no', $req->input('receipt_no'))  // Only get the last record with the same transaction ID
    ->orderBy('updated_at', 'desc')
    ->orderBy('id', 'desc')
    ->first();
    $previous_due = $lastSameTransaction->balance_due ?? 0;  // Get the 'due' of the last transaction with the same transaction ID
    $previous_invoice_due = $lastSameTransaction->balance_due ?? 0;

    $reciept_no = $req->input('reciept_no');

    if ($lastTransaction === null) {
        // No previous transaction exists, so you can reset the invoice_due for a new transaction
        $new_invoice_due = $previous_due - $req->input('addedcollectfund'); // Start from previous due
    } else {
        // Check if the last transaction's receipt number differs from the current one
        if ($lastTransaction->reciept_no !== $reciept_no) {
            // Reset invoice_due for a new transaction
            $new_invoice_due = $previous_due - $req->input('addedcollectfund'); // New invoice starts from last due
        } else {
            // Continue reducing the invoice_due for the current transaction
            $new_invoice_due = $previous_invoice_due - $req->input('addedcollectfund');
        }
    }



        $updated_balance = $lastTransaction->updated_balance ?? null; // get the last updated balance
        $new_due = $updated_balance;

        $new_updated_bal_supp = $new_due - $supplier_livecollected - $supplier_live_debitnote_collected;
        $bankName = $req->input('bank_name');
        $bank = DB::table('bank')->where('id', $bankName)->first();

        if ($bank) {
            $accountName = $bank->account_name;
        } else {
            $accountName = null;
        }

        $credit_supp_trans = new CreditSupplierTransaction();
        $credit_supp_trans->credit_supplier_id = $req->input('supplierid');
        $credit_supp_trans->credit_supplier_username = $req->input('fundsuppliername');
        $credit_supp_trans->user_id = Session('softwareuser');
        $credit_supp_trans->location = $branch;
        $credit_supp_trans->reciept_no = $req->input('receipt_no');
        $credit_supp_trans->product_id = $req->input('receipt_product');
        $credit_supp_trans->due = $new_due;
        $credit_supp_trans->collectedamount = $req->input('addedcollectfund');
        $credit_supp_trans->debitnote = $req->input('debitnote');
        $credit_supp_trans->updated_balance = $new_updated_bal_supp;
        $credit_supp_trans->balance_due = $new_invoice_due; // Add the new invoice_due
        $credit_supp_trans->transfer_date =$req->input('bank_transfer_date');


        // if (($req->input('addedcollectfund') != '' || $req->input('addedcollectfund') != null)) {
        //     $credit_supp_trans->comment = "Payment Made";
        // }

        if (($req->input('addedcollectfund') != '' || $req->input('addedcollectfund') != null) && ($req->input('debitnote') == '' || $req->input('debitnote') == null)) {
            $credit_supp_trans->comment = 'Payment Made';
        } elseif (($req->input('addedcollectfund') == '' || $req->input('addedcollectfund') == null) && ($req->input('debitnote') != '' || $req->input('debitnote') != null)) {
            $credit_supp_trans->comment = 'Debit Note';
        } elseif (($req->input('addedcollectfund') != '' || $req->input('addedcollectfund') != null) && ($req->input('debitnote') != '' || $req->input('debitnote') != null)) {
            $credit_supp_trans->comment = 'Payment & Debit Note';
        }

        $credit_supp_trans->payment_type = $req->input('payment_option');
        $credit_supp_trans->check_number = $req->input('check_number');
        $credit_supp_trans->depositing_date = $req->input('check_date');
        $credit_supp_trans->reference_number = $req->input('reference_no');
        $credit_supp_trans->bank_id = $req->input('bank_name');
        $credit_supp_trans->account_name = $accountName;
        $credit_supp_trans->save();
          if ($req->bank_name && $req->addedcollectfund) {
            $bank = DB::table('bank')
                ->where('id', $req->bank_name)
                ->first();

            if ($bank) {
                $accountName = $bank->account_name;
                $current_balance = $bank->current_balance;
                $new_balance = $current_balance - $req->addedcollectfund;

                $userid = Session('softwareuser');
                $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

                DB::table('bank')
                    ->where('id', $req->bank_name)
                    ->where('account_name', $accountName)
                    ->update(['current_balance' => $new_balance]);

                $bank_history = new Bankhistory();
                $bank_history->reciept_no = $req->input('receipt_no');
                $bank_history->user_id = $userid;
                $bank_history->bank_id = $req->bank_name;
                $bank_history->account_name = $accountName;
                $bank_history->branch = $branch_id;
                $bank_history->detail = 'Amount payable';
                $bank_history->party =  $req->fundsuppliername;
                $bank_history->dr_cr = 'Debit';
                $bank_history->ref_no = $req->reference_num;
                $bank_history->date = $req->bank_transfer_date ?? now();
                $bank_history->amount = $req->addedcollectfund;
                $bank_history->save();
            }
        }


        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' added collection payment to credit supplier '.$req->input('fundsuppliername');
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */
        return redirect('/addfunds');
    }

  public function supplierCreditTransactionHistory(Request $request, $id)
    {
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }

        // Validate and sanitize input
      $request->validate([
    'start_date' => 'nullable|date',
    'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);
    
    // Initialize query builders without date conditions
    $creditQuery = CreditSupplierTransaction::where('credit_supplier_id', $id);
    $cashQuery = CashSupplierTransaction::where('cash_supplier_id', $id);
    
    // Only apply date filters if dates are provided in the request
    if ($request->has('start_date') && $request->start_date) {
        $creditQuery->whereDate('created_at', '>=', $request->start_date);
        $cashQuery->whereDate('created_at', '>=', $request->start_date);
    }
    
    if ($request->has('end_date') && $request->end_date) {
        $creditQuery->whereDate('created_at', '<=', $request->end_date);
        $cashQuery->whereDate('created_at', '<=', $request->end_date);
    }
    
    // Execute the queries
    $creditTransactions = $creditQuery->get()->toArray();
    $cashTransactions = $cashQuery->get()->toArray();
    
    // Combine both collections
    $allTransactions = array_merge($creditTransactions, $cashTransactions);
        $startDate = request('start_date') ?: now()->format('Y-m-d');
        $endDate = request('end_date') ?: now()->format('Y-m-d');


        if (Session('softwareuser')) {
            $item = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', Session('softwareuser'))
                ->get();

            $userid = Session('softwareuser');
            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();
        } elseif (Session('adminuser')) {
            $item = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', Session('adminuser'))
                ->get();

            $adminid = Session('adminuser');
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $lastTransaction_for_due = DB::table('credit_supplier_transactions')
            ->where('credit_supplier_id', $id)
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $finaldue = $lastTransaction_for_due->updated_balance ?? 0;

        $data = [
            'users' => $item,
            'allTransactions' => $allTransactions,
            'finaldue' => $finaldue,
            'currency' => $currency,
            'credit_supplier_id' => $id,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        return view('/billingdesk/suppliercreditdata', $data);
    }

    public function supplierCreditBillsHistory($id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        $collected = DB::table('supplier_credits')
            ->where('supplier_id', $id)
            ->pluck('collected_amt')
            ->first();

        $total_due = DB::table('supplier_credits')
            ->where('supplier_id', $id)
            ->pluck('due_amt')
            ->first();

        $username = DB::table('suppliers')->where('id', $id)->pluck('name')->first();

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $userid = Session('softwareuser');
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $purchases = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->select(DB::raw('stockdetails.id, stockdetails.reciept_no,SUM(COALESCE(stockdetails.discount, 0)) as discount, stockdetails.created_at, SUM(stockdetails.price) as price, stockdetails.user_id'))
            ->where('stockdetails.payment_mode', 2)
            ->where('stockdetails.branch', $branch)
            ->where('stockdetails.supplier_id', $id)
            ->groupBy('stockdetails.reciept_no')
            ->orderBy('created_at', 'DESC')
            ->get();
            $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

            $listbank = DB::table('bank')
            ->select('id','bank_name', 'account_name', 'status','current_balance')
            ->where('status', 1)
            ->where('branch', $branch)
            ->get();

        return view('/billingdesk/supplierbilldata', ['listbank'=>$listbank,'users' => $item, 'purchases' => $purchases, 'total_due' => $total_due, 'paid' => $collected, 'supplier_name' => $username, 'supplier_id' => $id, 'currency' => $currency]);
    }

    public function completeSalesExport($userid, $viewtype, $location, $fromdate, $todate)
    {
        $userid = Session('softwareuser');

        $users = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        if ($viewtype == 1) {
            return Excel::download(new OverallSalesExport($userid, $viewtype, $location, $fromdate, $todate, $users), 'SalesReport-Date wise.xlsx');
        } elseif ($viewtype == 2) {
            return Excel::download(new OverallSalesExport($userid, $viewtype, $location, $fromdate, $todate, $users), 'SalesReport-Month wise.xlsx');
        } elseif ($viewtype == 3) {
            return Excel::download(new OverallSalesExport($userid, $viewtype, $location, $fromdate, $todate, $users), 'SalesReport-Year wise.xlsx');
        }
    }

    public function exportExcelPurchaseReport($userid, $viewtype, $location, $date)
    {
        if ($viewtype == 1) {
            return Excel::download(new PurchaseExport($userid, $viewtype, $location, $date), $date.'- PurchaseReport-Date wise.xlsx');
        } elseif ($viewtype == 2) {
            $monthname = date('F', mktime($date));

            return Excel::download(new PurchaseExport($userid, $viewtype, $location, $date), $monthname.'- PurchaseReport - Month Wise.xlsx');
        } elseif ($viewtype == 3) {
            return Excel::download(new PurchaseExport($userid, $viewtype, $location, $date), $date.'- PurchaseReport - Year wise.xlsx');
        }
    }

    public function completePurchaseExport($userid, $viewtype, $location, $fromdate, $todate)
    {
        if ($viewtype == 1) {
            return Excel::download(new OverallPurchaseExport($userid, $viewtype, $location, $fromdate, $todate), 'PurchaseReport-Date wise.xlsx');
        } elseif ($viewtype == 2) {
            return Excel::download(new OverallPurchaseExport($userid, $viewtype, $location, $fromdate, $todate), 'PurchaseReport-Month wise.xlsx');
        } elseif ($viewtype == 3) {
            return Excel::download(new OverallPurchaseExport($userid, $viewtype, $location, $fromdate, $todate), 'PurchaseReport-Year wise.xlsx');
        }
    }

    public function exportSalesReturnReport($userid, $viewtype, $location, $date)
    {
        if ($viewtype == 1) {
            return Excel::download(new SalesReturnExport($userid, $viewtype, $location, $date), $date.'- Sales Return Report-Date wise.xlsx');
        } elseif ($viewtype == 2) {
            $monthno = date('m', strtotime($date));

            return Excel::download(new SalesReturnExport($userid, $viewtype, $location, $monthno), $date.'- Sales Return Report - Month Wise.xlsx');
        } elseif ($viewtype == 3) {
            return Excel::download(new SalesReturnExport($userid, $viewtype, $location, $date), $date.'- Sales Return Report - Year wise.xlsx');
        }
    }

    public function completeSalesReturnExport($userid, $viewtype, $location, $fromdate, $todate)
    {
        if ($viewtype == 1) {
            return Excel::download(new OverallSalesReturnReport($userid, $viewtype, $location, $fromdate, $todate), 'Sales Return Report-Date wise.xlsx');
        } elseif ($viewtype == 2) {
            return Excel::download(new OverallSalesReturnReport($userid, $viewtype, $location, $fromdate, $todate), 'Sales Return Report-Month wise.xlsx');
        } elseif ($viewtype == 3) {
            return Excel::download(new OverallSalesReturnReport($userid, $viewtype, $location, $fromdate, $todate), 'Sales Return Report-Year wise.xlsx');
        }
    }

    public function exportPurchaseReturnReport($userid, $viewtype, $location, $date)
    {
        if ($viewtype == 1) {
            return Excel::download(new exportPurchaseReturnReport($userid, $viewtype, $location, $date), $date.'- Purchase Return Report-Date wise.xlsx');
        } elseif ($viewtype == 2) {
            $monthno = date('m', strtotime($date));

            return Excel::download(new exportPurchaseReturnReport($userid, $viewtype, $location, $monthno), $date.'- Purchase Return Report - Month Wise.xlsx');
        } elseif ($viewtype == 3) {
            return Excel::download(new exportPurchaseReturnReport($userid, $viewtype, $location, $date), $date.'- Purchase Return Report - Year wise.xlsx');
        }
    }

    public function completePurchaseReturnExport($userid, $viewtype, $location, $fromdate, $todate)
    {
        if ($viewtype == 1) {
            return Excel::download(new OverallPurchaseReturnExport($userid, $viewtype, $location, $fromdate, $todate), 'Purchase Return Report-Date wise.xlsx');
        } elseif ($viewtype == 2) {
            return Excel::download(new OverallPurchaseReturnExport($userid, $viewtype, $location, $fromdate, $todate), 'Purchase Return Report-Month wise.xlsx');
        } elseif ($viewtype == 3) {
            return Excel::download(new OverallPurchaseReturnExport($userid, $viewtype, $location, $fromdate, $todate), 'Purchase Return Report-Year wise.xlsx');
        }
    }

    public function upload_products(Request $request)
    {
        request()->validate([
            'products' => 'required|mimes:xlx,xls,xlsx|max:2048',
        ]);
        $product_code = rand(1000000000000, 9999999999999);
        $userid = Session('softwareuser');

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        Excel::import(new ProductsImport($product_code, $userid, $branch), $request->file('products'));

        /* ------------------GET IP ADDRESS--------------------------------------- */
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' imported products';
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return back()->with('massage', 'Products Imported Successfully');
    }

    public function IndirectIncome()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $date = Carbon::now()->format('Y-m-d H:i:s');

        $branch = Accountantloc::leftJoin('branches', 'accountantlocs.location_id', '=', 'branches.id')
            ->where('accountantlocs.user_id', $userid)
            ->get();

        return view('/accountant/indirectincome', ['users' => $item, 'start_date' => $date, 'branch' => $branch]);
    }

    public function indirectincomesubmit(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'branch' => 'required',
            'amount' => 'required',
            'start_date' => 'required',
        ]);
        $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();
        foreach ($request->comment as $key => $comment) {
            $data = new AccountIndirectIncome();
            $data->comment = $comment;
            $data->amount = $request->amount[$key];
            $data->date = $request->start_date[$key];
            $data->user_id = Session('softwareuser');
            $data->branch = $branch;
            if (!isset($request->image[$key]) || $request->image[$key] == '' || is_null($request->image[$key])) {
                $data->save();
            } else {
                $image = $request->image[$key];
                $ext = $image->getClientOriginalExtension();
                $name = 'INDIRECT_INCOME'.date('d-m-y_h-i-s').'.'.$ext;
                $data->file = $name;
                $path = $image->storeAs('monthlybills', $name);
                $timeInSeconds = 1;
                sleep($timeInSeconds);
                $data->save();
            }
        }

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();
        $branch_name = Branch::where('id', $branch)->pluck('branchname')->first();

        $user_type = 'websoftware';
        $message = $username.' added indirect income of branch '.$branch_name;
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */
        return redirect('/indirectincome');
    }

  public function searchMonthwiseIncome(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $userid = session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::where('id', $adminid)
            ->pluck('currency')
            ->first();
            $branchid = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $branch = Accountantloc::leftJoin('branches', 'accountantlocs.location_id', '=', 'branches.id')
            ->where('accountantlocs.user_id', $userid)
            ->get();

            $today = Carbon::now();
            $month = $today->month;
            $year = $today->year;


        $incomes = DB::table('account_indirect_incomes')
            ->leftJoin('branches', 'account_indirect_incomes.branch', '=', 'branches.id')
            ->select(
                'account_indirect_incomes.indirect_income',
                'account_indirect_incomes.direct_income',
                'account_indirect_incomes.details',
                'account_indirect_incomes.id',
                'account_indirect_incomes.amount',
                'account_indirect_incomes.date',
                'account_indirect_incomes.branch',
                'account_indirect_incomes.user_id',
                'account_indirect_incomes.file',
                'account_indirect_incomes.created_at',
                'account_indirect_incomes.income_type',
                'branches.branchname as branchname'
            )
            ->where('account_indirect_incomes.branch', $branchid)
            ->get();


            $start_date = '';

        return view('/accountant/monthwiseincomehistory', [
            'users' => $useritem,
            'incomes' => $incomes,
            'start_date' => $start_date,
            'branch' => $branch,
            'currency' => $currency
        ]);
    }

   public function monthwiseIncomeHistory(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $month = Carbon::parse($req->start_date)->format('m');
            $year = Carbon::parse($req->start_date)->format('Y');

            $branchid = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $branch = Accountantloc::leftJoin('branches', 'accountantlocs.location_id', '=', 'branches.id')
            ->where('accountantlocs.user_id', $userid)
            ->get();

            $incomes = DB::table('account_indirect_incomes')
            ->leftJoin('branches', 'account_indirect_incomes.branch', '=', 'branches.id')
            ->select(
                'account_indirect_incomes.indirect_income',
                'account_indirect_incomes.id',
                'account_indirect_incomes.direct_income',
                'account_indirect_incomes.details',
                'account_indirect_incomes.amount',
                'account_indirect_incomes.date',
                'account_indirect_incomes.branch',
                'account_indirect_incomes.user_id',
                'account_indirect_incomes.file',
                'account_indirect_incomes.created_at',
                'account_indirect_incomes.income_type',
                'branches.branchname as branchname'
            )
            ->whereMonth('account_indirect_incomes.date', $month)
            ->whereYear('account_indirect_incomes.date', $year)
            ->where('account_indirect_incomes.branch', $branchid)
            ->get();



        $start_date = $req->start_date;
        return view('/accountant/monthwiseincomehistory', ['users' => $useritem, 'incomes' => $incomes, 'start_date' => $start_date, 'branch' => $branch, 'currency' => $currency]);
    }

    public function expensesHistorydate(Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $date = Carbon::now()->format('Y-m-d');

        $branch = Accountantloc::leftJoin('branches', 'accountantlocs.location_id', '=', 'branches.id')
            ->where('accountantlocs.user_id', $userid)
            ->get();

        $expenses = DB::table('accountexpenses')
            ->leftJoin('branches', 'accountexpenses.branch', '=', 'branches.id')
            ->select(DB::raw('accountexpenses.comment,accountexpenses.amount,accountexpenses.date,accountexpenses.branch,accountexpenses.user_id,accountexpenses.file,accountexpenses.created_at, branches.branchname'))
            ->where('accountexpenses.user_id', $userid)
            ->where('accountexpenses.date', $date)
            ->where('accountexpenses.branch', $req->branch)
            ->get();

        return view('/accountant/expenseshistory', ['users' => $useritem, 'expenses' => $expenses, 'branch' => $branch, 'currency' => $currency]);
    }

    public function getcreditsupplier($receiptno)
    {
        $credit = Stockdetail::select('supplier')
            ->where('id', $receiptno)
            ->get();

        $payment_type = Stockdetail::select('payment_mode')
            ->where('id', $receiptno)
            ->pluck('payment_mode');

        return response()->json([
            'creditsupplier' => $credit,
            'payment_type' => $payment_type,
        ]);
    }

   public function userReport(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $userid = Session('softwareuser');
        $users = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $branch = Softwareuser::where('id', $userid)->pluck('location')->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();

          $shopdata = Branch::Where('id', $branch)->get();
        $userdatas = Softwareuser::Where('id', $userid)->get();

        $customers = DB::table('buyproducts')
            ->pluck('customer_name')
            ->all();

        $lastTransaction = DB::table('user_reports')
            ->where('user_id', $userid)
            ->where('branch', $branch)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTransaction) {
            $last_userreport_date = $lastTransaction->created_at;

            $totalsaless = DB::table('buyproducts')
                ->select(DB::raw('SUM(DISTINCT COALESCE(bill_grand_total, 0)) as total_sales'))
                ->where('user_id', $userid)
                ->where('branch', $branch)
                ->where('created_at', '>=', $last_userreport_date)
                ->groupBy('transaction_id')
                ->get();

            $totalsales = $totalsaless->sum('total_sales');

            $totalreturnsaless = DB::table('returnproducts')
            ->select(DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as total_sales'))
            ->where('user_id', $userid)
            ->where('branch', $branch)
            ->where('created_at', '>=', $last_userreport_date)
            ->groupBy('transaction_id')
            ->get();

        $totalretunsales = $totalreturnsaless->sum('total_sales');

            $creditpaymentt = DB::table('credit_transactions')
                ->select(DB::raw('SUM( COALESCE(collected_amount, 0)) as creditpayment'))
                ->where('user_id', $userid)
                ->where('location', $branch)
                ->where('created_at', '>=', $last_userreport_date)
                ->whereIn('comment', ['Payment Received', 'Invoice'])
                ->groupBy('transaction_id')
                ->get();
            $creditpayment = $creditpaymentt->sum('creditpayment');

            $returncreditpaymentt = DB::table('credit_transactions')
            ->select(DB::raw('SUM( COALESCE(collected_amount, 0)) as creditpayment'))
            ->where('user_id', $userid)
            ->where('location', $branch)
            ->where('created_at', '>=', $last_userreport_date)
            ->whereIn('comment',['Returned Product'])
            ->groupBy('transaction_id')
            ->get();
        $returncreditpayment = $returncreditpaymentt->sum('creditpayment');



            $poss = DB::table('buyproducts')
                ->select(DB::raw('SUM(DISTINCT COALESCE(bill_grand_total, 0)) as pos'))
                ->where('user_id', $userid)
                ->where('branch', $branch)
                ->where('created_at', '>=', $last_userreport_date)
                ->whereIn('payment_type', [4, 2])
                ->whereIn('customer_name', $customers)
                ->groupBy('transaction_id')
                ->get();
            $pos = $poss->sum('pos');

            $possreturn = DB::table('returnproducts')
            ->select(DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as pos'))
            ->where('user_id', $userid)
            ->where('branch', $branch)
            ->where('created_at', '>=', $last_userreport_date)
            ->whereIn('payment_type', [4, 2])
            ->groupBy('transaction_id')
            ->get();
        $posreturn = $possreturn->sum('pos');

            $creditsalebuyproductss = DB::table('buyproducts')
                ->select(DB::raw('SUM(DISTINCT COALESCE(bill_grand_total, 0)) as creditsalebuyproducts'))
                ->where('user_id', $userid)
                ->where('branch', $branch)
                ->where('created_at', '>=', $last_userreport_date)
                ->where('payment_type', 3)
                ->groupBy('transaction_id')
                ->get();
            $creditsalebuyproducts = $creditsalebuyproductss->sum('creditsalebuyproducts');

            $creditsalereturn = DB::table('returnproducts')
            ->select(DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as creditsalebuyproducts'))
            ->where('user_id', $userid)
            ->where('branch', $branch)
            ->where('created_at', '>=', $last_userreport_date)
            ->where('payment_type', 3)
            ->groupBy('transaction_id')
            ->get();
        $creditsalereturnfinal = $creditsalereturn->sum('creditsalebuyproducts');


        $totalIncomecash = AccountIndirectIncome::selectRaw("SUM(COALESCE(amount,0)) as total_amount")
        ->where('branch', $branch)
        ->where('user_id', $userid)
        ->where('created_at', '>=', $last_userreport_date)
        ->whereNull('bank_id')
        ->value('total_amount')?? 0;

        $totalIncomebank = AccountIndirectIncome::selectRaw("SUM(COALESCE(amount,0)) as total_amount")
        ->where('branch', $branch)
        ->where('user_id', $userid)
        ->where('created_at', '>=', $last_userreport_date)
        // ->whereNotNull('bank_id') // Ensures bankid is not null
        ->value('total_amount')?? 0;


        $totalexpensecash = Accountexpense::selectRaw("SUM(COALESCE(amount,0)) as total_amount")
        ->where('branch', $branch)
        ->where('user_id', $userid)
        ->where('created_at', '>=', $last_userreport_date)
        ->whereNull('bank_id')
        ->value('total_amount')?? 0;

        $totalexpensebank = Accountexpense::selectRaw("SUM(COALESCE(amount,0)) as total_amount")
        ->where('branch', $branch)
        ->where('user_id', $userid)
        ->where('created_at', '>=', $last_userreport_date)
        // ->whereNotNull('bank_id') // Ensures bankid is not null
        ->value('total_amount')?? 0;


        $service = DB::table('service')
        ->select(DB::raw("SUM(COALESCE(total_amount, 0)) as service_cost"))
        ->where('branch', $branch)
        ->where('user_id', $userid)
        ->where('payment_mode', '!=', 2)
        ->where('created_at', '>=', $last_userreport_date)
        ->value('total_amount')?? 0;
        
    $servicepos = DB::table('service')
        ->select(DB::raw("SUM(COALESCE(total_amount, 0)) as service_cost"))
        ->where('branch', $branch)
        ->where('user_id', $userid)
        ->where('payment_mode', '=', 2)
        ->where('created_at', '>=', $last_userreport_date)
        ->value('total_amount')?? 0;


        } else {
            $totalsaless = DB::table('buyproducts')
                ->select(DB::raw('SUM(DISTINCT COALESCE(bill_grand_total, 0)) as total_sales'))
                ->where('user_id', $userid)
                ->where('branch', $branch)
                ->groupBy('transaction_id')
                ->get();

            $totalsales = $totalsaless->sum('total_sales');

            $totalreturnsaless = DB::table('returnproducts')
            ->select(DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as total_sales'))
            ->where('user_id', $userid)
            ->where('branch', $branch)
            ->groupBy('transaction_id')
            ->get();

        $totalretunsales = $totalreturnsaless->sum('total_sales');

            $creditpaymentt = DB::table('credit_transactions')
                ->select(DB::raw('SUM( COALESCE(collected_amount, 0)) as creditpayment'))
                ->where('user_id', $userid)
                ->where('location', $branch)
                ->whereIn('comment', ['Payment Received', 'Invoice'])
                ->groupBy('transaction_id')
                ->get();
            $creditpayment = $creditpaymentt->sum('creditpayment');


            $returncreditpaymentt = DB::table('credit_transactions')
            ->select(DB::raw('SUM(COALESCE(collected_amount, 0)) as creditpayment'))
            ->where('user_id', $userid)
            ->where('location', $branch)
            ->whereIn('comment',['Returned Product'])
            ->groupBy('transaction_id')
            ->get();
        $returncreditpayment = $returncreditpaymentt->sum('creditpayment');

            $poss = DB::table('buyproducts')
                ->select(DB::raw('SUM(DISTINCT COALESCE(bill_grand_total, 0)) as pos'))
                ->where('user_id', $userid)
                ->where('branch', $branch)
                ->whereIn('payment_type', [4, 2])
                ->whereIn('customer_name', $customers)
                ->groupBy('transaction_id')
                ->get();
            $pos = $poss->sum('pos');

            $possreturn = DB::table('returnproducts')
            ->select(DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as pos'))
            ->where('user_id', $userid)
            ->where('branch', $branch)
            ->whereIn('payment_type', [4, 2])
            ->groupBy('transaction_id')
            ->get();
        $posreturn = $possreturn->sum('pos');

            // Query for buyproducts
            $creditsalebuyproductss = DB::table('buyproducts')
                ->select(DB::raw('SUM(DISTINCT COALESCE(bill_grand_total, 0)) as creditsalebuyproducts'))
                ->where('user_id', $userid)
                ->where('branch', $branch)
                ->where('payment_type', 3)
                ->groupBy('transaction_id')
                ->get();
            $creditsalebuyproducts = $creditsalebuyproductss->sum('creditsalebuyproducts');

            $creditsalereturn = DB::table('returnproducts')
            ->select(DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as creditsalebuyproducts'))
            ->where('user_id', $userid)
            ->where('branch', $branch)
            ->where('payment_type', 3)
            ->groupBy('transaction_id')
            ->get();
        $creditsalereturnfinal = $creditsalereturn->sum('creditsalebuyproducts');

        $totalIncomecash = AccountIndirectIncome::selectRaw("SUM(COALESCE(amount,0)) as total_amount")
        ->where('branch', $branch)
        ->where('user_id', $userid)
        // ->whereNull('bank_id')
        ->value('total_amount')?? 0;

        $totalIncomebank = AccountIndirectIncome::selectRaw("SUM(COALESCE(amount,0)) as total_amount")
        ->where('branch', $branch)
        ->where('user_id', $userid)
        // ->whereNotNull('bank_id') // Ensures bankid is not null
        ->value('total_amount')?? 0;


        $totalexpensecash = Accountexpense::selectRaw("SUM(COALESCE(amount,0)) as total_amount")
        ->where('branch', $branch)
        ->where('user_id', $userid)
        // ->whereNull('bank_id')
        ->value('total_amount')?? 0;

        $totalexpensebank = Accountexpense::selectRaw("SUM(COALESCE(amount,0)) as total_amount")
        ->where('branch', $branch)
        ->where('user_id', $userid)
        // ->whereNotNull('bank_id') // Ensures bankid is not null
        ->value('total_amount')?? 0;

        $service = DB::table('service')
        ->select(DB::raw("SUM(COALESCE(total_amount, 0)) as service_cost"))
        ->where('branch', $branch)
        ->where('user_id', $userid)
        ->where('payment_mode', '!=', 2)
        ->value('total_amount')?? 0;
        
    $servicepos = DB::table('service')
        ->select(DB::raw("SUM(COALESCE(total_amount, 0)) as service_cost"))
        ->where('branch', $branch)
        ->where('user_id', $userid)
        ->where('payment_mode', '=', 2)
        ->value('total_amount')?? 0;

        }

        return view('/user/user-report', compact('servicepos','service','totalexpensebank','totalIncomecash','totalIncomebank','totalexpensecash','returncreditpayment','posreturn','creditsalereturnfinal','totalretunsales','users', 'totalsales', 'creditpayment', 'pos', 'creditsalebuyproducts', 'shopdata', 'userdatas'));
    }

   public function submitUserReport(Request $req)
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();



        $userid = Session('softwareuser');

        $count = DB::table('user_reports')->distinct()->count('trans_id');
        ++$count;
        $trans_id = 'UR'.$count;


        DB::transaction(function () use ($req, $userid, $branch, $trans_id) {
            $data = new UserReport();
            $data->user_id = $userid;
            $data->branch = $branch;
            $data->trans_id = $trans_id;
            $data->opening_balance = $req->input('openingBalance');
            $data->total_sales_amount = $req->totalSales;
            $data->creditPayment = $req->creditPayment;
            $data->posBankSale = $req->posBankSale;
            $data->creditSale = $req->creditSale;
            $data->expense = $req->expense;
            $data->income = $req->income;
            $data->service = $req->service;
            
            $data->total_amount = $req->totalCashInDraw;
            $data->save();



        $productIds = $req->product_id ?? []; // Default to an empty array if null
        $expenseIds = $req->expense_id ?? []; // Default to an empty array if null


           foreach ($productIds as $key => $productID) {
            $datas = new CashNotes();
            $datas->user_id = $userid;
            $datas->branch = $branch;
            $datas->trans_id = $trans_id;
            $datas->notes = $req->cashnote[$key] ?? ''; // Default to an empty string if null
            $datas->quantity = $req->note_quantity[$key] ?? 0; // Default to 0 if null
            $datas->note_type_total = $req->total_cash_amt[$key] ?? 0; // Default to 0 if null
            $datas->save();
        }


        });

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';

        $message = $username.' user report submitted by '.$username;
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();
        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */
        return redirect('/userreportreciept/'.$trans_id);
    }

    public function userReportReceipt($trans_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $userid = Session('softwareuser');
        $userdatas = Softwareuser::Where('id', $userid)->get();

        $users = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $Reportgenerated = DB::table('user_reports')
            // ->whereDate('user_reports.created_at', $today)
            ->where('trans_id', $trans_id)
            ->pluck('created_at')
            ->first();

        $datas = DB::table('user_reports')
            ->select(DB::raw('service,expense,income,opening_balance, total_sales_amount, posBankSale, creditPayment, creditSale, total_amount'))
            ->where('user_id', $userid)
            ->where('trans_id', $trans_id)
            ->where('branch', $branch)
            ->first();

        $open_balance = $datas->opening_balance ?? 0;
        $total_sales_amount = $datas->total_sales_amount ?? 0;
        $creditPayment = $datas->creditPayment ?? 0;
        $posBankSale = $datas->posBankSale ?? 0;
        $creditSale = $datas->creditSale ?? 0;
        $expense = $datas->expense ?? 0;
        $income = $datas->income ?? 0;
        $service = $datas->service ?? 0;


        $total_amount = $open_balance + $service + $income + $total_sales_amount + $creditPayment - $posBankSale - $creditSale - $expense;

        $cash_details = DB::table('cash_notes')
            ->select(DB::raw('notes, quantity, note_type_total'))
            ->where('user_id', $userid)
            ->where('trans_id', $trans_id)
            ->where('branch', $branch)
            ->get();



        $data = [
            'trans_id' => $trans_id,
            'open_balance' => $open_balance,
            'total_sales_amount' => $total_sales_amount,
            'creditPayment' => $creditPayment,
            'posBankSale' => $posBankSale,
            'creditSale' => $creditSale,
            'expense' => $expense,
            'income' => $income,
            'service'=>$service,

            'total_amount' => $total_amount,
            'cash_details' => $cash_details,
            'userdatas' => $userdatas,
            'Reportgenerated' => $Reportgenerated,
            'users' => $users,
        ];

        return view('/user/user-report-receipt', $data);
    }

    // new

    public function purchaseTable(Request $req)
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $req->validate([
            'reciept_no' => 'required|unique:stockdetails,reciept_no',
            'price' => 'required',
            'supplier' => 'required',
            'payment_mode' => 'required',
        ]);

        if($branch==4 || $branch==2 || $branch==5){
            $boxDozens = 3;
        }else{
            $boxDozens = $req->input('boxdozen');
        }
        $boxCounts = $req->input('boxCount');
        $boxItems = $req->input('boxItem');
        $dozenCounts = $req->input('dozenCount');
        $dozenItems = $req->input('dozenItem');
        $units = $req->input('unit');

        $prices = $req->input('total');
        $discountAmount = $req->discount_amount; // Total discount amount
        $totalPrice = array_sum($prices);
        $discounts = [];
        $buycosts = $req->input('buy_cost');
        $sellcosts = $req->input('sell_cost');

        $rates = $req->input('rate_r');
        $vats = $req->input('vat_r');

        $priceswithoutvat = $req->input('without_vat');

        $suppliertId = $req->input('supp_id');

        if (empty($suppliertId)) {
            // Supplier ID is not provided, create a new supplier
            $supply = Supplier::where('name', $req->supplier)
                ->where('location', $branch)
                ->first();

            if ($supply == null) {
                $user = new Supplier();
                $user->name = $req->supplier;
                $user->location = $branch;
                $user->softwareuser = Session('softwareuser');
                $user->save();

                $suppliertId = $user->id;
            }
        }

        if (($req->payment_mode == 1) || ($req->payment_mode == 2) || ($req->payment_mode == 3)) {
            $count = DB::table('stock_purchase_reports')
                ->distinct()
                ->count('purchase_trans_id');

            ++$count;
            $purchase_trans_id = 'PID';

            $i = 1;
            $pricetotals = 0;


            foreach ($req->input('product_id') as $key => $productID) {
                $pricetotals += $prices[$key];
                $stock = new Stockdetail();
                $stock->reciept_no = $req->input('reciept_no');
                $stock->comment = $req->input('comment');
                $stock->supplier = $req->input('supplier');
                $stock->invoice_date = $req->input('invoice_date');

                // $stock->supplier_id = $req->input('supp_id');

                if ($req->input('supp_id') != '' || $req->input('supp_id') != null) {
                    $stock->supplier_id = $req->input('supp_id');
                } elseif ($req->input('supp_id') == '' || $req->input('supp_id') == null) {
                    $stock->supplier_id = $suppliertId;
                }

                $stock->payment_mode = $req->input('payment_mode');
                $stock->user_id = Session('softwareuser');
                // $stock->price = $req->price;
                $stock->branch = $branch;

                $stock->product = $productID;
                if($branch==4 || $branch==2 || $branch==5){
                $stock->is_box_or_dozen = 3;
                }else{
                    $stock->is_box_or_dozen = $boxDozens[$key];
                }
                $stock->unit = $units[$key];
                $stock->price = $prices[$key];
                $stock->buycost = $buycosts[$key];
                $stock->sellingcost = $sellcosts[$key];
                $stock->price_without_vat = $priceswithoutvat[$key];

                $stock->rate = $rates[$key];
                $stock->vat = $vats[$key];
                $stock->bank_id =$req->bank_name;
                $stock->account_name =$req->account_name;
                $discounts[$key] = ($totalPrice != 0)
                    ? ($discountAmount * $prices[$key]) / $totalPrice
                    : 0;
                $stock->discount = $discounts[$key];
                $method = trim(strtolower($req->input('method'))); // Normalize input
                $stock->method = ($method === 'service') ? 2 : 1;
                if($branch==4 || $branch==2 || $branch==5){
                    $stock->quantity = $req->actual_quantity[$key];
                    $stock->remain_stock_quantity = $req->actual_quantity[$key];
                    $stock->discount_percent = $discounts[$key]/$req->actual_quantity[$key];
                }else{
                    if ($boxDozens[$key] == 1) {
                        $stock->box_dozen_count = $boxCounts[$key];
                        $stock->quantity = $boxItems[$key];
                        $stock->remain_stock_quantity = $boxItems[$key];
                        $stock->discount_percent = $discounts[$key]/$boxItems[$key];
                    } elseif ($boxDozens[$key] == 2) {
                        $stock->box_dozen_count = $dozenCounts[$key];
                        $stock->quantity = $dozenItems[$key];
                        $stock->remain_stock_quantity = $dozenItems[$key];
                        $stock->discount_percent = $discounts[$key]/$dozenItems[$key];
                    } elseif ($boxDozens[$key] == 3) {
                        $stock->quantity = $boxItems[$key];
                        $stock->remain_stock_quantity = $boxItems[$key];
                        $stock->discount_percent = $discounts[$key]/$boxItems[$key];
                    }
                }


                if ($req->page == 'purchase_order') {
                    $stock->to_purchase = '1';
                    $stock->purchase_order_trans_ID = $req->purchase_order_id;
                }

                if (!empty($req->file('camera'))) {
                    $ext = $req->file('camera')->getClientOriginalExtension();
                    $stock->file = 'STOCK_DAT'.date('d-m-y_h-i-s').'.'.$ext;
                    $stock->save();
                    NewStockdetail::create($stock->getAttributes());

                    $path = $req->file('camera')->storeAs('stockbills', $stock->file);

                    $stockId = $stock->id;
                } else {
                    $stock->save();
                    NewStockdetail::create($stock->getAttributes());


                    $stockId = $stock->id;
                }

                if ($req->page == 'purchase_order') {
                    DB::table('purchase_orders')
                        ->where('purchase_order_id', $req->purchase_order_id)
                        ->update([
                            'purchase_done' => 1,
                            'purchase_trans' => $req->input('reciept_no'),
                        ]);
                }

                if (!empty($productID)) {
                    $datatwo = Product::find($productID);

                    // Fetch vat from the products table
                    $productVat = $datatwo->vat;

                    $datatwo->buy_cost = $buycosts[$key];
                    $datatwo->selling_cost = $sellcosts[$key];

                    $datatwo->rate = $rates[$key];
                    $datatwo->purchase_vat = $vats[$key];

                    // Calculate inclusive rate and vat   //add
                    if (($sellcosts[$key] != '' || $sellcosts[$key] != null) && ($productVat != '' || $productVat != null)) {
                        $inclusive_rate = $sellcosts[$key] / (1 + ($productVat / 100));
                        $inclusive_vat_amount = $sellcosts[$key] - $inclusive_rate;

                        // Store inclusive rate and vat in the products table
                        $datatwo->inclusive_rate = $inclusive_rate;
                        $datatwo->inclusive_vat_amount = $inclusive_vat_amount;
                    }
                    if($branch==4 || $branch==2 || $branch==5){
                        $datatwo->stock += $req->actual_quantity[$key];
                        $datatwo->remaining_stock += $req->actual_quantity[$key];
                    }else{
                        if ($boxDozens[$key] == 1 || $boxDozens[$key] == 3) {
                            $datatwo->stock += $boxItems[$key];
                            $datatwo->remaining_stock += $boxItems[$key];
                        } elseif ($boxDozens[$key] == 2) {
                            $datatwo->stock += $dozenItems[$key];
                            $datatwo->remaining_stock += $dozenItems[$key];
                        }
                    }

                    $datatwo->save();
                }

                if (!empty($productID)) {
                    $data = new Stockhistory();
                    $data->user_id = Session('softwareuser');
                    $data->product_id = $productID;
                    $data->receipt_no = $req->input('reciept_no');
                    $data->buycost = $buycosts[$key];
                    $data->sellingcost = $sellcosts[$key];
                    $data->rate = $rates[$key];
                    $data->vat = $vats[$key];
                    $discounts[$key] = ($totalPrice != 0)
                    ? ($discountAmount * $prices[$key]) / $totalPrice
                    : 0;
                    $data->discount = $discounts[$key];
                    if($branch==4 || $branch==2 || $branch==5){
                         $data->quantity = $req->actual_quantity[$key];
                        $data->remain_qantity = $req->actual_quantity[$key];
                        $data->sell_qantity = $req->actual_quantity[$key];
                        $data->discount_percent = $discounts[$key]/$req->actual_quantity[$key];
                    }else{
                        if ($boxDozens[$key] == 1 || $boxDozens[$key] == 3) {
                            $data->quantity = $boxItems[$key];
                            $data->remain_qantity = $boxItems[$key];
                            $data->sell_qantity = $boxItems[$key];
                            $data->discount_percent = $discounts[$key]/$boxItems[$key];
                        } elseif ($boxDozens[$key] == 2) {
                            $data->quantity = $dozenItems[$key];
                            $data->remain_qantity = $dozenItems[$key];
                            $data->sell_qantity = $dozenItems[$key];
                            $data->discount_percent = $discounts[$key]/$dozenItems[$key];
    
                        }
                    }
                   

                    $data->save();
                }


                /* --------------------- stock reports table --------------------------- */

                $purchase_insertedData = Stockdetail::find($stockId);

                /* --------------------------- another method -------------------- */

                if ($purchase_insertedData) {
                    $new_stock = new StockPurchaseReport();
                    $new_stock->purchase_id = $stockId;
                    $new_stock->receipt_no = $purchase_insertedData->reciept_no;
                    $new_stock->purchase_trans_id = $purchase_trans_id.$branch.Session('softwareuser').$purchase_insertedData->product.$count.$i;
                    $new_stock->product_id = $purchase_insertedData->product;
                    $new_stock->user_id = Session('softwareuser');
                    $new_stock->branch_id = $branch;
                    $new_stock->PBuycost = $purchase_insertedData->buycost;
                    $new_stock->PSellcost = $purchase_insertedData->sellingcost;
                    $new_stock->quantity = $purchase_insertedData->quantity;
                    $new_stock->remain_main_quantity = $purchase_insertedData->quantity;
                    $new_stock->sell_quantity = $purchase_insertedData->quantity;
                    $new_stock->PBuycostRate = $purchase_insertedData->rate;
                    $new_stock->save();

                    ++$i;
                }

                /* --------------------------------------------------------------- */
            }
        }

        $pricetotal = 0;
        $totalPrice = array_sum($prices); // Total price from all products (or another logic)
        foreach ($req->input('product_id') as $key => $productID) {
            $discounts[$key] = ($totalPrice != 0)
                    ? ($discountAmount * $prices[$key]) / $totalPrice
                    : 0;
            $pricetotal += $prices[$key] -$discounts[$key];
        }

        $dueamount = DB::table('supplier_credits')
            ->where('supplier_id', $suppliertId)
            ->pluck('due_amt')
            ->first();

        if ($req->payment_mode == 2) {
            $prieupload = $pricetotal + $dueamount;

            $suppliercredit = DB::table('supplier_credits')
                ->updateOrInsert(
                    ['supplier_id' => $suppliertId],
                    // ['due_amt' => $dueamount + $prices[$key]],
                    ['due_amt' => $prieupload],
                );

            // new credit_supplier_transactions table

            $lastTransaction = DB::table('credit_supplier_transactions')
                ->where('credit_supplier_id', $suppliertId)
                ->where('location', $branch)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $updated_balance = $lastTransaction->updated_balance ?? null;
            $last_invoice_due = $lastTransaction->balance_due ?? null;

            // $new_due = $updated_balance + $prices[$key];

            $new_due = $updated_balance + $pricetotal;

            if ($lastTransaction && $lastTransaction->reciept_no === $req->input('reciept_no')) {
                $new_invoice_due = $last_invoice_due;
                } else {
                $new_invoice_due = $req->price;
                }

            $credit_supp_trans = new CreditSupplierTransaction();
            $credit_supp_trans->credit_supplier_id = $suppliertId;
            $credit_supp_trans->credit_supplier_username = $req->supplier;
            $credit_supp_trans->user_id = Session('softwareuser');
            $credit_supp_trans->location = $branch;
            $credit_supp_trans->balance_due = $new_invoice_due;
            $credit_supp_trans->reciept_no = $req->input('reciept_no');
            $credit_supp_trans->Invoice_due = $pricetotal;
            if ($updated_balance == null) {
                $credit_supp_trans->due = 0;
            } else {
                $credit_supp_trans->due = $updated_balance;
            }
            // $credit_supp_trans->Invoice_due = $prices[$key];

            $credit_supp_trans->Invoice_due = $pricetotal;
            $credit_supp_trans->updated_balance = $new_due;
            $credit_supp_trans->comment = 'Bill';
            $credit_supp_trans->save();
        } elseif ($req->payment_mode == 1 || $req->payment_mode == 3 && ($suppliertId != '' || $suppliertId != null)) {
            $lastTransaction = DB::table('cash_supplier_transactions')
                ->where('cash_supplier_id', $suppliertId)
                ->where('location', $branch)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $updated_balance = $lastTransaction->updated_balance ?? null;
            $new_due = $updated_balance + $pricetotal;

            $cash_trans = new CashSupplierTransaction();
            $cash_trans->cash_supplier_id = $suppliertId;
            $cash_trans->cash_supplier_username = $req->supplier;
            $cash_trans->user_id = Session('softwareuser');
            $cash_trans->location = $branch;
            $cash_trans->reciept_no = $req->input('reciept_no');
            $cash_trans->collected_amount = $pricetotal;
            $cash_trans->updated_balance = $new_due;
            $cash_trans->comment = 'Bill';
            $cash_trans->payment_type = $req->payment_mode;
            $cash_trans->save();
        }
        if ($req->bank_name && $req->account_name) {
            $current_balance = DB::table('bank')
                ->where('id', $req->bank_name)
                ->where('account_name', $req->account_name)
                ->pluck('current_balance')
                ->first();

            $new_balance = $current_balance - $req->price;
            $userid = Session('softwareuser');

            $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

            DB::table('bank')
                ->where('id', $req->bank_name)
                ->where('account_name', $req->account_name)
                ->update(['current_balance' => $new_balance]);

            $bank_history = new Bankhistory();
            $bank_history->reciept_no = $req->input('reciept_no');
            $bank_history->user_id = Session('softwareuser');
            $bank_history->bank_id = $req->bank_name;
            $bank_history->account_name = $req->account_name;
            $bank_history->branch = $branch_id;
            $bank_history->detail = 'Purchase';
            $bank_history->dr_cr = 'Debit';
            $bank_history->date = Carbon::now(); // Store the current date and time
            $bank_history->amount = $req->price;
            $bank_history->save();
        }


        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';

        $message = $username.' Purchased Stock';
        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

                /* ---------------- Journal Entry Hook ---------------- */
        try {
            $receiptNo = $req->input('reciept_no');
            if ($receiptNo) {
                app(\App\Services\JournalEntryService::class)
                    ->postPurchaseByReceipt($receiptNo);
            }
        } catch (\Exception $e) {
            \Log::error('Journal entry failed [purchaseTable]: '.$e->getMessage());
        }
        /* ---------------------------------------------------- */


          if ($req->page == 'edit_purchase_draft') {
            DB::table('purchasedraft')
            ->where('reciept_no', $req->receipt_no)
            ->update(['branch' => null]);
        }


        if ($req->page == 'purchase_order') {
            return redirect('/purchasehistory')->with('success', 'Data uploaded successfully!');
        } elseif ($req->page == 'edit_purchase_draft') {
            return redirect('/purchasestock')->with('success', 'Data uploaded successfully!');
        } else {
             $purchase_id=$req->input('reciept_no');
           if ($req->print_option == 1) {
            return redirect('/barcode/'.$purchase_id);
            }

           return back()->with('success', 'Purchase added successfully');
        }
        
        
    }


    // purchase details view

    public function viewPurchasedetails($receipt_no)
    {
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }
        $count = DB::table('stockdetails')->count();

        $item = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->select(DB::raw('
            stockdetails.id,
             stockdetails.quantity,
              stockdetails.unit,
              stockdetails.buycost,
              stockdetails.sellingcost,
              stockdetails.price,
              products.product_name,
              products.id as product_id,
              products.product_code,
              products.barcode,
              stockdetails.created_at,
              stockdetails.price_without_vat,
              stockdetails.vat'))
            ->where('reciept_no', $receipt_no)
            ->get();

            if (Session('softwareuser')) {
                $userid = Session('softwareuser');
                $useritem = DB::table('softwareusers')
                    ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                    ->where('user_id', $userid)
                    ->get();

                $adminid = Softwareuser::Where('id', $userid)
                    ->pluck('admin_id')
                    ->first();
            } elseif (Session('adminuser')) {
                $adminid = Session('adminuser');
                $useritem = DB::table('adminusers')
                    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                    ->where('user_id', $adminid)
                    ->get();

                $shopdata = Adminuser::Where('id', $adminid)->get();
            }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
        $receipt_no=$receipt_no;
        if (Session('softwareuser')) {
            $options = [
                'details' => $item,
                'users' => $useritem,
                'currency' => $currency,
                'tax'=>$tax,
                'receipt_no'=>$receipt_no
            ];
        } elseif (Session('adminuser')) {
            $options = [
                'details' => $item,
                'users' => $useritem,
                'currency' => $currency,
                'shopdatas' => $shopdata,
                'tax'=>$tax,
                 'receipt_no'=>$receipt_no
            ];
        }

        return view('inventory/purchaseDetails', $options);

// return view('inventory/purchaseDetails', ['tax'=>$tax,'details' => $item, 'users' => $useritem, 'currency' => $currency]);
}

    public function getPurchaseProduct($receipt_no)
    {
        $purchasedata = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->select(DB::raw('stockdetails.id, stockdetails.reciept_no, stockdetails.product, stockdetails.supplier, products.product_name, stockdetails.price, stockdetails.quantity, stockdetails.payment_mode'))
            ->where('stockdetails.reciept_no', $receipt_no)
            ->get();

        $credit = DB::table('stockdetails')
            ->select(DB::raw('supplier'))
            ->where('stockdetails.reciept_no', $receipt_no)
            ->distinct('stockdetails.reciept_no', $receipt_no)
            ->get();

        $payment_type = Stockdetail::select('payment_mode')
            ->where('stockdetails.reciept_no', $receipt_no)
            ->distinct('stockdetails.reciept_no', $receipt_no)
            ->pluck('payment_mode');

        return response()->json([
            'productdatas' => $purchasedata,
            'creditsupplier' => $credit,
            'payment_type' => $payment_type,
        ]);
    }

    public function returnPurchaseDetails($receipt_no, $created_at)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $count = DB::table('returnpurchases')->count();

        $item = DB::table('returnpurchases')
            ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
            ->select(DB::raw('returnpurchases.id as id, returnpurchases.reciept_no as reciept_no, returnpurchases.created_at as created_at,returnpurchases.amount as amount, products.product_name,returnpurchases.quantity, returnpurchases.unit, returnpurchases.buycost,returnpurchases.amount_without_vat as amount_without_vat, returnpurchases.vat_amount as vat_amount'))
            ->where('returnpurchases.reciept_no', $receipt_no)
            ->where('returnpurchases.created_at', $created_at)
            ->where('returnpurchases.branch', $branch)
            ->get();

        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopid = Session('softwareuser');
        $adminid = Softwareuser::Where('id', $shopid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
        return view('inventory/ReturnPurchaseDetails', ['tax'=>$tax,'details' => $item, 'users' => $useritem, 'currency' => $currency]);
    }

    // check receipt no

    public function receiptno_check(Request $request)
    {
        $receiptNo = $request->input('reciept_no');

        $existsInStockDetails = DB::table('stockdetails')
            ->where('reciept_no', $receiptNo)
            ->exists();

        $existsInPurchaseDraft = DB::table('purchasedraft')
            ->where('reciept_no', $receiptNo)
            ->exists();

        $exists = $existsInStockDetails || $existsInPurchaseDraft;

        return response()->json(['exists' => $exists]);
    }
    // list stock category filter

    public function categoryFilter($categoryid)
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $liststockata = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
            ->select(DB::raw('products.product_name as product_name,products.stock as stock, SUM(stockdats.stock_num) as stock_num, products.remaining_stock as remaining_stock'))
            ->groupBy('products.id')
            ->where('products.branch', $branch)
            ->where('products.status', 1)
            ->where('products.category_id', $categoryid)
            ->orderBy('products.id')
            ->get();

        return response()->json([
            'liststockata' => $liststockata,
        ]);
    }

    public function purchasedetailsproduct($receipt_no)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $data = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->select(DB::raw('stockdetails.id,
            stockdetails.quantity,
            stockdetails.unit,
            stockdetails.buycost,
            stockdetails.price,
            products.product_name,
            stockdetails.created_at,
            stockdetails.price_without_vat,
             stockdetails.vat'))
            ->where('reciept_no', $receipt_no)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        return view('/accountant/purchaseproducts', ['users' => $item, 'details' => $data, 'currency' => $currency]);
    }

    public function sales_order_quot(Request $request, $page, UserService $userService)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $count = DB::table('buyproducts')->distinct()->count('transaction_id');

        $userid = Session('softwareuser');
        $useritem = $userService->getUserDetails($userid);

        $branch = Softwareuser::locationById($userid);

        $item = Product::select(DB::raw('*'))
            ->where('branch', $branch)
            ->where('status', 1)
            ->get();

        $validateuser = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->where('role_id', 1)
            ->pluck('role_id')
            ->first();



        if ($validateuser != '1') {
            return redirect('userlogin');
        }
        $adminid = $userService->getAdminId($userid);
        $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
          $shopdata = Branch::Where('id', $branch)->get();

        $user_location = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $creditusers = Credituser::Where('admin_id', $adminid)
            ->where('status', 1)
            ->where('location', $user_location)
            ->get();

        $barcode = $request->barcodenumber;

        $barcodeselected_product = DB::table('products')
            ->Where('barcode', $request->barcodenumber)
            ->where('branch', $branch)
            ->where('status', 1)
            ->pluck('id')
            ->first();

        $barcodedata = Product::select(DB::raw('*'))
            ->Where('barcode', $request->barcodenumber)
            ->where('branch', $branch)
            ->where('status', 1)
            ->get();

        if ($page == 'quotation') {
            $categories = DB::table('categories')
                ->select(DB::raw('categories.category_name,categories.id as category_id,categories.access '))
                ->where('branch_id', $branch)
                ->where('access', 1)
                ->get();

            $units = DB::table('units')
                ->select(DB::raw('units.unit,units.id'))
                ->where('branch_id', $branch)
                ->where('status', 1)
                ->get();
        }

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';

        $blade_file = 'salesorder';

        if ($page == 'sales_order') {
            $pa_name = 'sales order';
        } elseif ($page == 'quotation') {
            $pa_name = 'Quotation';
        } elseif ($page == 'performance_invoice') {
            $pa_name = 'performance_invoice';
        }

        $message = $username.' visited '.$pa_name.' page';

        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }
        $listbank = DB::table('bank')
        ->select('id','bank_name', 'account_name', 'status')
        ->where('status', 1)
        ->where('branch', $branch)
        ->get();

        $listemployee=DB::table('employee')
        ->select('first_name', 'id')
        ->where('branch', $branch)
        ->get();
        $vat = DB::table('vat_mode')->where('branch',$branch)->first(); // Get the first row from the vat_mode table
                $mode = $vat->mode;
        /* ----------------------------------------------------------------------- */

        $data = [
            'counts' => $count,
            'creditusers' => $creditusers,
            'items' => $item,
            'users' => $useritem,
            'shopdatas' => $shopdata,
            'barcodedata' => $barcodedata,
            'page' => $page,
            'tax'=>$tax,
            'listbank'=>$listbank,
            'listemployee'=>$listemployee,
            'mode'=>$mode

        ];

        if ($page == 'quotation') {
            $data['categories'] = $categories;
            $data['units'] = $units;
        }

        return view('/billingdesk/'.$blade_file)->with($data);
    }

    public function salesorderSubmit(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'productName' => 'required',
        ]);

        $userid = Session('softwareuser');
        $branch = Softwareuser::locationById($userid);
        $admin_id = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('admin_id')
            ->first();

        $transdefault = DB::table('adminusers')
            ->where('id', $admin_id)
            ->pluck('transpart')
            ->first();

        // Determine model and table name based on page type (use a switch statement for readability)
        $page = $request->page;
        switch ($page) {
            case 'sales_order':
            case 'salesorderdraft':
            case 'quot_to_salesorder':
                $modelClass = SalesOrder::class;
                $table = 'sales_orders';
                $shrt = 'SLS';
                $pa_name = 'sales order';
                break;
            case 'quotation':
            case 'quotationdraft':
            case 'clone_quotation':
                $modelClass = Quotation::class;
                $table = 'quotations';
                $shrt = 'QUOT';
                $pa_name = 'Quotation';
                break;
            case 'performance_invoice':
            case 'performadraft':
                $modelClass = PerformanceInvoice::class;
                $table = 'performance_invoices';
                $shrt = 'PI';
                $pa_name = 'performance_invoice';
                break;
            default:
                // Handle invalid page type (throw an exception or redirect with error message)
                return redirect()->back()->withErrors(['Invalid page type']);
        }

        // Generate transaction ID
        $count = DB::table($table)->where('branch', $branch)->distinct()->count('transaction_id');
        ++$count;

        $text = ($request->vat_type_value == 1) ? 'IN' : 'EX';
        $transaction_id = $transdefault.$shrt.$count.$text;

        // Loop through products and save data
        foreach ($request->product_id as $key => $productId) {
            $data = new $modelClass();
            $data->product_name = $request->productName[$key];
            $data->quantity = $request->quantity[$key];
            $data->remain_quantity = $request->quantity[$key];
            $data->unit = $request->prounit[$key];
            $data->product_id = $productId;
            $data->transaction_id = $transaction_id;
            $data->customer_name = $request->customer_name;
            $data->email = $request->email;
            $data->description = $request->has('description') ? 1 : 0;
            $data->trn_number = $request->trn_number;
            $data->comment = isset($request->pcomment[$key]) && !empty($request->pcomment[$key]) ? $request->pcomment[$key] : null;
            $data->phone = $request->phone;
            $data->price = $request->price[$key];
            $data->total_amount = $request->total_amount[$key];
            $data->user_id = $userid;
            $data->branch = $branch;
            $data->one_pro_buycost = $request->buy_cost[$key];
            $data->mrp = $request->mrp[$key];
            $data->fixed_vat = $request->fixed_vat[$key];
            $data->bank_id = $request->bank_name;
            $data->account_name = $request->account_name;
            $data->employee_id = $request->employee_id;
            $data->employee_name = $request->employee_name;

            if ($request->payment_type == 3) {
                $data->credit_user_id = $request->credit_id;
            } elseif ($request->payment_type == 1 || $request->payment_type == 2 || $request->payment_type == 4) {
                $data->cash_user_id = $request->credit_id;
            }

            $data->vat_amount = $request->vat_amount[$key];
            $data->vat_type = $request->vat_type_value;
            $data->payment_type = $request->payment_type;

            $data->one_pro_buycost_rate = $request->buycost_rate[$key];
            $data->netrate = $request->net_rate[$key];

            $data->inclusive_rate = ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$key] : null;
            $data->exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

            if ($request->page == 'salesorderdraft' || $request->page == 'quotationdraft' || $request->page == 'performadraft' || $request->page == 'quot_to_salesorder' || $request->page == 'clone_quotation') {
                $data->discount_type = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key];

                $data->discount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                    ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0)));

                $data->discount_amount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                    ($request->dis_count__tp_ori[$key] == 'percentage' ?
                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0));
            } else {
                $data->discount_type = $request->dis_count_type[$key];

                if ($request->vat_type_value == 1) {
                    if ($request->dis_count_type[$key] == 'none') {
                        $data->discount = $request->dis_count[$key];
                    } elseif ($request->dis_count_type[$key] == 'percentage') {
                        $data->discount = $request->dis_count[$key];
                        $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                    } elseif ($request->dis_count_type[$key] == 'amount') {
                        $data->discount_amount = $request->dis_count[$key];
                        $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                    }
                } elseif ($request->vat_type_value == 2) {
                    if ($request->dis_count_type[$key] == 'none') {
                        $data->discount = $request->dis_count[$key];
                    } elseif ($request->dis_count_type[$key] == 'percentage') {
                        $data->discount = $request->dis_count[$key];
                        $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                    } elseif ($request->dis_count_type[$key] == 'amount') {
                        $data->discount_amount = $request->dis_count[$key];
                        $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                    }
                }
            }

            $data->totalamount_wo_discount = $request->total_amount_wo_discount[$key];
            $data->price_wo_discount = $request->price_withoutvat_wo_discount[$key];

            $data->total_discount_type = $request->total_discount;

            if ($request->page == 'salesorderdraft' || $request->page == 'quotationdraft' || $request->page == 'performadraft' || $request->page == 'quot_to_salesorder' || $request->page == 'clone_quotation') {
                $data->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100);
                $data->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100));

                $data->bill_grand_total = $request->bill_grand_total;
                $data->bill_grand_total_wo_discount = $request->bill_grand_total_wo_discount;
            } else {
                $data->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->sales_grand_total_wo_discount) * 100);
                $data->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->sales_grand_total_wo_discount * ($request->discount_percentage / 100));

                $data->bill_grand_total = $request->sales_grand_total;
                $data->bill_grand_total_wo_discount = $request->sales_grand_total_wo_discount;
            }

            if ($page == 'quot_to_salesorder') {
                $data->quotationID = $request->transaction_id;
            }

            $data->save();
        }

        /* ---------------------------------------------------------------------- */

        $transaction = $transaction_id;

        $tableName = '';
        if ($page == 'salesorderdraft') {
            $tableName = 'sales_orders_draft';
        } elseif ($page == 'quotationdraft') {
            $tableName = 'quotations_draft';
        } elseif ($page == 'performadraft') {
            $tableName = 'performance_invoices_draft';
        }

         if ($tableName) {
            DB::table($tableName)
            ->where('transaction_id', $request->transaction_id)
            ->update(['branch' => null]);
        }

        if ($page == 'quot_to_salesorder') {
            DB::table('quotations')
                ->where('transaction_id', $request->transaction_id)
                ->update([
                    'Salesorder_ID' => $transaction,
                ]);
        }

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' done '.$pa_name.' '.$transaction;

        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return redirect('/receipt/'.$request->page.'/'.$transaction);
    }

    public function salesOrderReceipt($page, $transaction_id, UserService $userService, salesQuotService $salesquot)
    {
        if (session()->missing('softwareuser') && session()->missing('adminuser')) {
            return redirect('/');
        }

        $salesorder_quot_data = $salesquot->SalesQuot($page, $transaction_id, $userService, $salesquot);

        return view('/billingdesk/sales_order_receipt', $salesorder_quot_data);
    }

    public function gethistorySales($userid)
    {
        $id = $userid;

        $trn_number = DB::table('creditusers')->where('id', $id)->pluck('trn_number')->first();
        $phone = DB::table('creditusers')->where('id', $id)->pluck('phone')->first();
        $email = DB::table('creditusers')->where('id', $id)->pluck('email')->first();

        $full_name = DB::table('creditusers')->where('id', $id)->pluck('name')->first();

        return response()->json([
            'creditid' => $id,
            'trn_number' => $trn_number,
            'phone' => $phone,
            'email' => $email,
            'full_name' => $full_name,
        ]);
    }

    // purchase order
    public function purchaseOrder()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
          $shopdata = Branch::Where('id', $branch)->get();

        $products = DB::table('products')
            ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
            ->select(DB::raw('products.product_name as product_name,products.id as id, products.unit as unit,products.buy_cost as buy_cost, products.selling_cost as selling_cost,products.rate as rate,products.purchase_vat as purchase_vat'))
            ->groupBy('products.id')
            ->where('products.branch', $branch)
            ->where('products.status', 1)
            ->orderBy('products.id')
            ->get();

        $suppliers = DB::table('suppliers')
            ->where('location', $branch)
            ->get();

        $receipt_nos = DB::table('purchase_orders')
            ->where('branch', $branch)
            ->distinct('purchase_orders.reciept_no')
            ->get(['reciept_no']);

        $categories = DB::table('categories')
            ->select(DB::raw('categories.category_name,categories.id as category_id,categories.access '))
            ->where('branch_id', $branch)
            ->where('access', 1)
            ->get();
        $units = DB::table('units')
            ->select(DB::raw('units.unit,units.id'))
            ->where('branch_id', $branch)
            ->where('status', 1)
            ->get();

        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
        $listbank = DB::table('bank')
        ->select('id','bank_name', 'account_name','status','current_balance')
        ->where('status', 1)
        ->where('branch', $branch)
        ->get();

        $data = [
            'users' => $useritem,
            'products' => $products,
            'shopdatas' => $shopdata,
            'suppliers' => $suppliers,
            'receipt_nos' => $receipt_nos,
            'categories' => $categories,
            'units' => $units,
            'tax'=>$tax,
            'listbank'=>$listbank,

        ];

        return view('/inventory/purchase_order')->with($data);
    }

    public function purchaseOrderSubmit(Request $req)
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $req->validate([
            // 'reciept_no' => 'required|unique:stockdetails,reciept_no',
            // 'reciept_no' => 'required',
            'reciept_no' => 'required|unique:purchase_orders,reciept_no',
            'price' => 'required',
            'supplier' => 'required',
            'payment_mode' => 'required',
        ]);

        $count = DB::table('purchase_orders')
            ->distinct()
            ->count('purchase_order_id');
        ++$count;

        $admin_id = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('admin_id')
            ->first();

        $transdefault = DB::table('adminusers')
            ->where('id', $admin_id)
            ->pluck('transpart')
            ->first();

        $purchase_o_id = $transdefault.'PRSO'.$count;

        $boxDozens = $req->input('boxdozen');
        $boxCounts = $req->input('boxCount');
        $boxItems = $req->input('boxItem');
        $dozenCounts = $req->input('dozenCount');
        $dozenItems = $req->input('dozenItem');
        $units = $req->input('unit');
        $prices = $req->input('total');
        $buycosts = $req->input('buy_cost');
        $sellcosts = $req->input('sell_cost');

        $rates = $req->input('rate_r');

        $vats = $req->input('vat_r');

        $priceswithoutvat = $req->input('without_vat');

        $suppliertId = $req->input('supp_id');

        if (empty($suppliertId)) {
            // Supplier ID is not provided, create a new supplier
            $supply = Supplier::where('name', $req->supplier)
                ->where('location', $branch)
                ->first();

            if ($supply == null) {
                $user = new Supplier();
                $user->name = $req->supplier;
                $user->location = $branch;
                $user->softwareuser = Session('softwareuser');
                $user->save();

                $suppliertId = $user->id;
            }
        }

        if (($req->payment_mode == 1) || ($req->payment_mode == 2)||($req->payment_mode == 3)) {
            // $suppliertId = $req->supp_id;

            foreach ($req->input('product_id') as $key => $productID) {
                $stock = new PurchaseOrder();
                $stock->purchase_order_id = $purchase_o_id;
                $stock->reciept_no = $req->input('reciept_no');
                $stock->comment = $req->input('comment');
                $stock->supplier = $req->input('supplier');

                $stock->delivery_date = $req->input('delivery_date');

                if ($req->input('supp_id') != '' || $req->input('supp_id') != null) {
                    $stock->supplier_id = $suppliertId;
                } elseif ($req->input('supp_id') == '' || $req->input('supp_id') == null) {
                    $stock->supplier_id = $suppliertId;
                }

                $stock->payment_mode = $req->input('payment_mode');
                $stock->bank_id = $req->bank_name;
                $stock->account_name = $req->account_name;
                $stock->user_id = Session('softwareuser');
                $stock->branch = $branch;

                $stock->product = $productID;
                $stock->is_box_or_dozen = $boxDozens[$key];
                $stock->unit = $units[$key];
                $stock->price = $prices[$key];
                $stock->buycost = $buycosts[$key];
                $stock->sellingcost = $sellcosts[$key];
                $stock->price_without_vat = $priceswithoutvat[$key];

                $stock->rate = $rates[$key];
                $stock->vat = $vats[$key];

                if ($boxDozens[$key] == 1) {
                    $stock->box_dozen_count = $boxCounts[$key];
                    $stock->quantity = $boxItems[$key];
                    $stock->remain_stock_quantity = $boxItems[$key];
                } elseif ($boxDozens[$key] == 2) {
                    $stock->box_dozen_count = $dozenCounts[$key];
                    $stock->quantity = $dozenItems[$key];
                    $stock->remain_stock_quantity = $dozenItems[$key];
                } elseif ($boxDozens[$key] == 3) {
                    $stock->quantity = $boxItems[$key];
                    $stock->remain_stock_quantity = $boxItems[$key];
                }

                if (!empty($req->file('camera'))) {
                    $ext = $req->file('camera')->getClientOriginalExtension();
                    $stock->file = 'STOCK_DAT'.date('d-m-y_h-i-s').'.'.$ext;
                    $stock->save();
                    $path = $req->file('camera')->storeAs('stockbills', $stock->file);
                } else {
                    $stock->save();
                }
            }

            $purchase_o_id = $purchase_o_id;
        }
        // else {
        //     $req->validate(
        //         [
        //             'supp_id' => 'required',
        //         ],
        //         [
        //             'supp_id.required' => 'Supplier not exist. Create the supplier named ' . $req->supplier . ' to give credit',
        //         ]
        //     );
        // }

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' done purchase order';

        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return redirect('/purchaseorderreceipt/'.$purchase_o_id);
    }

  public function purchaseorderReceipt($purchase_o_id)
    {
        if (session()->missing('softwareuser') && session()->missing('adminuser')) {
            return redirect('/');
        }
        $dataplan = DB::table('purchase_orders')
            ->leftJoin('products', 'purchase_orders.product', '=', 'products.id')
            ->select(DB::raw('products.product_name as product_name,purchase_orders.*'))
            ->where('purchase_orders.purchase_order_id', $purchase_o_id)
            ->get();

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $trans = $purchase_o_id;
        $enctrans = Crypt::encrypt($trans);

        $branchname = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
          $shopdata = Branch::Where('id', $branch)->get();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $vat = PurchaseOrder::select(DB::raw('SUM(vat_amount) as vat'))
            ->where('purchase_order_id', $purchase_o_id)
            ->pluck('vat')
            ->first();

        $payment_type = DB::table('purchase_orders')
            ->where('purchase_orders.purchase_order_id', $trans)
            ->pluck('payment_mode')
            ->first();

        $date = DB::table('purchase_orders')
            ->select(DB::raw('DATE(purchase_orders.created_at) as date'))
            ->where('purchase_orders.purchase_order_id', $trans)
            ->pluck('date')
            ->first();

        $date = Carbon::parse($date)->format('d-m-Y');

        $billno = PurchaseOrder::where('purchase_order_id', $purchase_o_id)
            ->pluck('reciept_no')
            ->first();

        $supplier = PurchaseOrder::where('purchase_order_id', $purchase_o_id)
            ->pluck('supplier')
            ->first();

        // $grand = PurchaseOrder::where('purchase_order_id', $purchase_o_id)
        //     ->select(DB::raw("SUM(price_without_vat) as price"),)
        //     ->pluck('price')
        //     ->first();

        $grand = PurchaseOrder::where('purchase_order_id', $purchase_o_id)
            ->select(DB::raw('SUM(price) as price'))
            ->pluck('price')
            ->first();

        $grandinnumber = $grand;
        $grand = number_format($grand, 3, '.', '');
        $amountinwords = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $amountinwords = $amountinwords->format($grand);
        $amountinwords = ucwords($amountinwords);
        $supplieddate = Carbon::now()->format('d-m-Y');

        // $cr_num = Adminuser::Where('id', $adminid)
        //     ->pluck('cr_number')
        //     ->first();
        // $po_box = Adminuser::Where('id', $adminid)
        //     ->pluck('po_box')
        //     ->first();
        // $tel = Adminuser::Where('id', $adminid)
        //     ->pluck('phone')
        //     ->first();

        // $admintrno = Adminuser::Where('id', $adminid)
        //     ->pluck('cr_number')
        //     ->first();

        // $emailadmin = Adminuser::Where('id', $adminid)
        //     ->pluck('email')
        //     ->first();

        // $adminname = Adminuser::Where('id', $adminid)
        //     ->pluck('name')
        //     ->first();
        $tel = DB::table('branches')
        ->where('id', $branch)
        ->pluck('mobile')
        ->first();
        $po_box = DB::table('branches')
        ->where('id', $branch)
        ->pluck('po_box')
        ->first();

        $admintrno = DB::table('branches')
        ->where('id', $branch)
        ->pluck('tr_no')
        ->first();
        $name = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();
        $logo = DB::table('branches')
        ->where('id', $branch)
        ->pluck('logo')
        ->first();
        $company = DB::table('branches')
        ->where('id', $branch)
        ->pluck('company')
        ->first();

        $Address = DB::table('branches')
        ->where('id', $branch)
        ->pluck('address')
        ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        $supplier_id = PurchaseOrder::where('purchase_order_id', $purchase_o_id)
            ->pluck('supplier_id')
            ->first();

        $trn_supp = Supplier::Where('id', $supplier_id)
            ->pluck('trn_number')
            ->first();

        $delivery_date = PurchaseOrder::where('purchase_order_id', $purchase_o_id)
            ->pluck('delivery_date')
            ->first();

        return view('/inventory/purchase_order_receipt', ['name'=>$name,'logo'=>$logo,'company'=>$company,'Address'=>$Address,'tax'=>$tax,'details' => $dataplan, 'vat' => $vat, 'payment_type' => $payment_type, 'grandinnumber' => $grandinnumber,  'trans' => $trans, 'enctrans' => $enctrans, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate,'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'user_id' => $userid, 'branch' => $branch,'admintrno' => $admintrno,'billno' => $billno, 'supplier' => $supplier, 'trn_supp' => $trn_supp, 'delivery_date' => $delivery_date]);
    }
    // delivery note

    public function deliveryNote(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $count = DB::table('buyproducts')->distinct()->count('transaction_id');
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $item = Product::select(DB::raw('*'))
            ->where('branch', $branch)
            ->where('status', 1)
            ->get();

        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $validateuser = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->where('role_id', 1)
            ->pluck('role_id')
            ->first();
        if ($validateuser != '1') {
            return redirect('userlogin');
        }

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
          $shopdata = Branch::Where('id', $branch)->get();
        $user_location = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $creditusers = Credituser::Where('admin_id', $adminid)
            ->where('status', 1)
            ->where('location', $user_location)
            ->get();

        $barcode = $request->barcodenumber;

        $barcodeselected_product = DB::table('products')
            ->Where('barcode', $request->barcodenumber)
            ->where('branch', $branch)
            ->where('status', 1)
            ->pluck('id')
            ->first();

        $barcodedata = Product::select(DB::raw('*'))
            ->Where('barcode', $request->barcodenumber)
            ->where('branch', $branch)
            ->where('status', 1)
            ->get();
            $listbank = DB::table('bank')
            ->select('id','bank_name', 'account_name', 'status') // Include status if you need it
            ->where('status', 1)
            ->where('branch', $branch)
            ->get();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        /* ------------------GET IP ADDRESS--------------------------------------- */

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();
        $user_type = 'websoftware';
        $message = $username.' visited delivery note page';

        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();
        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }
        $vat = DB::table('vat_mode')->where('branch',$branch)->first(); // Get the first row from the vat_mode table
                $mode = $vat->mode;
        /* ----------------------------------------------------------------------- */

        $data = [
            'counts' => $count,
            'creditusers' => $creditusers,
            'items' => $item,
            'users' => $useritem,
            'shopdatas' => $shopdata,
            'barcodedata' => $barcodedata,
            'tax'=>$tax,
            'listbank'=>$listbank,
            'mode'=>$mode

        ];

        return view('/billingdesk/delivery_note')->with($data);
    }

    public function deliveryNoteSubmit(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'productName' => 'required',
        ]);
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $count = DB::table('delivery_notes')
        ->where('branch', $branch)
            ->distinct()
            ->count('transaction_id');

        ++$count;

        $admin_id = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('admin_id')
            ->first();

        $transdefault = DB::table('adminusers')
            ->where('id', $admin_id)
            ->pluck('transpart')
            ->first();

        $text = ($request->vat_type_value == 1) ? 'IN' : 'EX';

        $transaction_id = $transdefault.'DN'.$count.$text;

        foreach ($request->product_id as $key => $productID) {
            $data = new DeliveryNote();
            $data->product_name = $request->productName[$key];
            $data->quantity = $request->quantity[$key];
            $data->remain_quantity = $request->quantity[$key];
            $data->unit = $request->prounit[$key];
            $data->product_id = $productID;
            $data->transaction_id = $transaction_id;
            $data->customer_name = $request->customer_name;
            $data->email = $request->email;
            $data->trn_number = $request->trn_number;
            $data->phone = $request->phone;
            $data->price = $request->price[$key];
            $data->total_amount = $request->total_amount[$key];
            $data->payment_type = $request->payment_type;
            $data->user_id = Session('softwareuser');
            $data->branch = $branch;
            $data->one_pro_buycost = $request->buy_cost[$key];
            $data->mrp = $request->mrp[$key];
            $data->fixed_vat = $request->fixed_vat[$key];
            $data->bank_id = $request->bank_name;
            $data->account_name = $request->account_name;

            $data->credit_user_id = $request->credit_id;
            $data->vat_amount = $request->vat_amount[$key];
            $data->vat_type = $request->vat_type_value;

            $data->one_pro_buycost_rate = $request->buycost_rate[$key];
            $data->netrate = $request->net_rate[$key];

            $data->inclusive_rate = ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$key] : null;
            $data->exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

            if ($request->page == 'deliverydraft' ||$request->page == 'to_delivery') {
                $data->discount_type = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key];

                $data->discount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                    ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0)));

                $data->discount_amount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                    ($request->dis_count__tp_ori[$key] == 'percentage' ?
                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0));
            } else {
                $data->discount_type = $request->dis_count_type[$key];

                if ($request->vat_type_value == 1) {
                    if ($request->dis_count_type[$key] == 'none') {
                        $data->discount = $request->dis_count[$key];
                    } elseif ($request->dis_count_type[$key] == 'percentage') {
                        $data->discount = $request->dis_count[$key];
                        $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                    } elseif ($request->dis_count_type[$key] == 'amount') {
                        $data->discount_amount = $request->dis_count[$key];
                        $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                    }
                } elseif ($request->vat_type_value == 2) {
                    if ($request->dis_count_type[$key] == 'none') {
                        $data->discount = $request->dis_count[$key];
                    } elseif ($request->dis_count_type[$key] == 'percentage') {
                        $data->discount = $request->dis_count[$key];
                        $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                    } elseif ($request->dis_count_type[$key] == 'amount') {
                        $data->discount_amount = $request->dis_count[$key];
                        $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                    }
                }
            }

            $data->totalamount_wo_discount = $request->total_amount_wo_discount[$key];
            $data->price_wo_discount = $request->price_withoutvat_wo_discount[$key];
            $data->total_discount_type = $request->total_discount;

            if ($request->page == 'deliverydraft'||$request->page == 'to_delivery') {
                $data->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100);
                $data->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100));

                $data->bill_grand_total = $request->bill_grand_total;
                $data->bill_grand_total_wo_discount = $request->bill_grand_total_wo_discount;
            } else {
                $data->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->delivery_grand_total_wo_discount) * 100);
                $data->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->delivery_grand_total_wo_discount * ($request->discount_percentage / 100));

                $data->bill_grand_total = $request->delivery_grand_total;
                $data->bill_grand_total_wo_discount = $request->delivery_grand_total_wo_discount;
            }

            $data->location_delivery = $request->location;
            $data->area = $request->area;
            $data->villa_no = $request->villa_no;
            $data->flat_no = $request->flat_no;
            $data->land_mark = $request->land_mark;
            $data->delivery_date = $request->delivery_date;
            $data->save();
        }
        if ($request->page == 'to_delivery') {
            DB::table('buyproducts')
                ->where('transaction_id', $request->transaction_id)
                ->update([
                    'delivery_done' => 1,
                ]);
        }
        /* ---------------------------------------------------------------------- */

        $transaction = $transaction_id;

         if ($request->page == 'deliverydraft') {
            DB::table('delivery_notes_draft')
            ->where('transaction_id', $request->transaction_id)
            ->update(['branch' => null]);
        }


        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' done delivery note.';

        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return redirect('/deliverynotereceipt/'.$transaction);
    }

     public function deliveryNoteReceipt($transaction_id)
    {
        if (session()->missing('softwareuser') && session()->missing('adminuser')) {
            return redirect('/');
        }
        $dataplan = DB::table('delivery_notes')
            ->select(DB::raw('delivery_notes.product_name as product_name,delivery_notes.product_id as product_id,delivery_notes.quantity as quantity,delivery_notes.mrp as mrp,delivery_notes.price as price,delivery_notes.fixed_vat as fixed_vat,delivery_notes.vat_amount as vat_amount,delivery_notes.total_amount as total_amount, delivery_notes.unit as unit'))
            ->where('delivery_notes.transaction_id', $transaction_id)
            ->get();

        $total = DeliveryNote::select(DB::raw('SUM(price) as total'))
            ->where('transaction_id', $transaction_id)
            ->get();

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $trans = $transaction_id;

        $enctrans = Crypt::encrypt($trans);

        $custs = DB::table('delivery_notes')
            ->where('transaction_id', $trans)
            ->pluck('customer_name')
            ->first();

        $branchname = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();

          $shopdata = Branch::Where('id', $branch)->get();

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $total = DeliveryNote::select(DB::raw('SUM(price) as total'))
            ->where('transaction_id', $transaction_id)
            ->pluck('total')
            ->first();

        $vat = DeliveryNote::select(DB::raw('SUM(vat_amount) as vat'))
            ->where('transaction_id', $transaction_id)
            ->pluck('vat')
            ->first();

        $payment_type = DB::table('delivery_notes')
            ->leftJoin('payment', 'delivery_notes.payment_type', '=', 'payment.id')
            ->select(DB::raw('payment.type as payment_type'))
            ->where('delivery_notes.transaction_id', $trans)
            ->pluck('payment_type')
            ->first();

        $date = DB::table('delivery_notes')
            ->select(DB::raw('DATE(delivery_notes.created_at) as date'))
            ->where('transaction_id', $trans)
            ->pluck('date')
            ->first();

        $date = Carbon::parse($date)->format('d-m-Y');

        $grand = DeliveryNote::where('transaction_id', $transaction_id)
            ->select(DB::raw('SUM(total_amount) as total_amount'))
            ->pluck('total_amount')
            ->first();

        $grandinnumber = $grand;
        $grand = number_format($grand, 3, '.', '');
        $amountinwords = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $amountinwords = $amountinwords->format($grand);
        $amountinwords = ucwords($amountinwords);
        $supplieddate = Carbon::now()->format('d-m-Y');

        $cr_num = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        $po_box = Adminuser::Where('id', $adminid)
            ->pluck('po_box')
            ->first();

        $tel = Adminuser::Where('id', $adminid)
            ->pluck('phone')
            ->first();

        $trn_number = DB::table('delivery_notes')
            ->where('transaction_id', $trans)
            ->pluck('trn_number')
            ->first();

        $admintrno = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();

        $name = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
            ->first();

        $billphone = DeliveryNote::select(DB::raw('phone'))
            ->where('transaction_id', $transaction_id)
            ->pluck('phone')
            ->first();

        $billemail = DeliveryNote::select(DB::raw('email'))
            ->where('transaction_id', $transaction_id)
            ->pluck('email')
            ->first();

        $location = DB::table('delivery_notes')
            ->where('transaction_id', $trans)
            ->pluck('location_delivery')
            ->first();

        $area = DB::table('delivery_notes')
            ->where('transaction_id', $trans)
            ->pluck('area')
            ->first();

        $villa_no = DB::table('delivery_notes')
            ->where('transaction_id', $trans)
            ->pluck('villa_no')
            ->first();

        $flat_no = DB::table('delivery_notes')
            ->where('transaction_id', $trans)
            ->pluck('flat_no')
            ->first();

        $land_mark = DB::table('delivery_notes')
            ->where('transaction_id', $trans)
            ->pluck('land_mark')
            ->first();

        $delivery_date = DB::table('delivery_notes')
            ->where('transaction_id', $trans)
            ->pluck('delivery_date')
            ->first();

            $tel = DB::table('branches')
            ->where('id', $branch)
            ->pluck('mobile')
            ->first();
            $po_box = DB::table('branches')
            ->where('id', $branch)
            ->pluck('po_box')
            ->first();




            $admintrno = DB::table('branches')
            ->where('id', $branch)
            ->pluck('tr_no')
            ->first();

            $logo = DB::table('branches')
            ->where('id', $branch)
            ->pluck('logo')
            ->first();
            $company = DB::table('branches')
            ->where('id', $branch)
            ->pluck('company')
            ->first();

            $Address = DB::table('branches')
            ->where('id', $branch)
            ->pluck('address')
            ->first();


        return view('/billingdesk/delivery_note_receipt', ['Address'=>$Address,'logo'=>$logo,'company'=>$company,'tax'=>$tax,'details' => $dataplan, 'vat' => $vat, 'payment_type' => $payment_type, 'grandinnumber' => $grandinnumber, 'totals' => $total, 'trans' => $trans, 'enctrans' => $enctrans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'user_id' => $userid, 'branch' => $branch, 'name' => $name, 'admintrno' => $admintrno, 'emailadmin' => $emailadmin, 'billphone' => $billphone, 'billemail' => $billemail, 'location' => $location, 'area' => $area, 'villa_no' => $villa_no, 'flat_no' => $flat_no, 'land_mark' => $land_mark, 'delivery_date' => $delivery_date]);
    }

    // sales order history
    public function salesOrder_DeliveryNoteHistory($page, UserService $userService)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $userid = Session('softwareuser');
        $item = $userService->getUserDetails($userid);
        $branch = Softwareuser::locationById($userid);
        $adminid = $userService->getAdminId($userid);

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if ($page == 'sales_order') {
            $data = DB::table('sales_orders')
                ->leftJoin('payment', 'sales_orders.payment_type', '=', 'payment.id')
                ->leftJoin('creditusers', 'sales_orders.credit_user_id', '=', 'creditusers.id')
                ->select(DB::raw('sales_orders.transaction_id,
                    sales_orders.vat_type,
                    sales_orders.created_at,
                    sales_orders.customer_name,
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(sales_orders.vat_amount) as vat,
                    payment.type as payment_type,
                    creditusers.username,
                    SUM(sales_orders.totalamount_wo_discount) as grandtotal_without_discount,

                    SUM(COALESCE(sales_orders.discount_amount * sales_orders.quantity, 0))
                    +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
                '))
                ->groupBy('sales_orders.transaction_id')
                ->orderBy('sales_orders.created_at', 'DESC')
                ->where('branch', $branch)
                ->get();

            $adminroles = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();

            $extra_options = [
                'adminroles' => $adminroles,
            ];
        } elseif ($page == 'deliverynote') {
            $data = DB::table('delivery_notes')
                ->leftJoin('payment', 'delivery_notes.payment_type', '=', 'payment.id')
                ->leftJoin('creditusers', 'delivery_notes.credit_user_id', '=', 'creditusers.id')
                ->select(DB::raw('
                    delivery_notes.transaction_id,
                    delivery_notes.vat_type,
                    delivery_notes.created_at,
                    delivery_notes.customer_name,
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(delivery_notes.vat_amount) as vat,
                    payment.type as payment_type,
                    creditusers.username,
                    SUM(delivery_notes.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(delivery_notes.discount_amount * delivery_notes.quantity, 0))
                    +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
                '))
                ->groupBy('delivery_notes.transaction_id')
                ->orderBy('delivery_notes.created_at', 'DESC')
                ->where('branch', $branch)
                ->get();

            $extra_options = [];
        } elseif ($page == 'quotation') {
            $data = DB::table('quotations')
                ->leftJoin('creditusers', 'quotations.credit_user_id', '=', 'creditusers.id')
                ->select(DB::raw('
                    quotations.transaction_id,
                    quotations.vat_type,quotations.created_at,
                    quotations.customer_name,
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(quotations.vat_amount) as vat,
                    creditusers.username,
                    SUM(quotations.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(quotations.discount_amount * quotations.quantity, 0))
                    +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
                '))
                ->groupBy('quotations.transaction_id')
                ->orderBy('quotations.created_at', 'DESC')
                ->where('branch', $branch)
                ->get();
            $adminroles = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();

            $extra_options = [
                'adminroles' => $adminroles,
            ];
        } elseif ($page == 'performance_invoice') {
            $data = DB::table('performance_invoices')
                ->leftJoin('payment', 'performance_invoices.payment_type', '=', 'payment.id')
                ->leftJoin('creditusers', 'performance_invoices.credit_user_id', '=', 'creditusers.id')
                ->select(DB::raw('
                    performance_invoices.transaction_id,
                    performance_invoices.vat_type,
                    performance_invoices.created_at,
                    performance_invoices.customer_name,
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(performance_invoices.vat_amount) as vat,
                    payment.type as payment_type,creditusers.username,
                    SUM(performance_invoices.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(performance_invoices.discount_amount * performance_invoices.quantity, 0))
                    +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
                '))
                ->groupBy('performance_invoices.transaction_id')
                ->orderBy('performance_invoices.created_at', 'DESC')
                ->where('branch', $branch)
                ->get();

            $extra_options = [];
        }

        $start_date = '';
        $end_date = '';

        $options = [
            'products' => $data,
            'users' => $item,
            'currency' => $currency,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'page' => $page,
            'adminid' => $adminid,
            'tax'=>$tax,
        ];

        return view('/billingdesk/history_sales_delivery', array_merge(
            $options,
            $extra_options
        ));
    }

    public function viewSalesDelivery($page, $transaction_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if ($page == 'sales_order') {
            $table = 'sales_orders';
            $modelClass = SalesOrder::class;
        } elseif ($page == 'deliverynote') {
            $table = 'delivery_notes';
            $modelClass = DeliveryNote::class;
        } elseif ($page == 'quotation') {
            $table = 'quotations';
            $modelClass = Quotation::class;
        } elseif ($page == 'performance_invoice') {
            $table = 'performance_invoices';
            $modelClass = PerformanceInvoice::class;
        }

        $count = DB::table($table)->count();

        $item = $modelClass::select(DB::raw('*'))
            ->where('transaction_id', $transaction_id)
            ->get();

        return view('billingdesk/historyProductDetails', ['tax'=>$tax,'details' => $item, 'users' => $useritem, 'currency' => $currency]);
    }

    public function historyFilterSalesDelivery($page, Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();

        if ($page == 'sales_order') {
            $table = 'sales_orders';

            $adminroles = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();
        } elseif ($page == 'deliverynote') {
            $table = 'delivery_notes';
        } elseif ($page == 'performance_invoice') {
            $table = 'performance_invoices';
        } elseif ($page == 'quotation') {
            $adminroles = DB::table('adminusers')
               ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
               ->where('user_id', $adminid)
               ->get();
        }

        if ($req->start_date != $req->end_date) {
            if ($page == 'sales_order' || $page == 'deliverynote' || $page == 'performance_invoice') {
                $data = DB::table($table)
                    ->leftJoin('payment', $table.'.payment_type', '=', 'payment.id')
                    ->leftJoin('creditusers', $table.'.credit_user_id', '=', 'creditusers.id')
                    ->select(
                        $table.'.transaction_id',
                        $table.'.created_at',
                        $table.'.customer_name',
                        $table.'.vat_type',
                        DB::raw('SUM(DISTINCT '.$table.'.bill_grand_total) as sum'),
                        DB::raw('SUM('.$table.'.vat_amount) as vat'),
                        DB::raw('SUM('.$table.'.totalamount_wo_discount) as grandtotal_without_discount'),
                        DB::raw("SUM(COALESCE($table.discount_amount * $table.quantity, 0))
                        +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount"),
                        'payment.type as payment_type',
                        'creditusers.username'
                    )
                    ->groupBy('transaction_id')
                    ->orderBy($table.'.created_at', 'DESC')
                    ->where('branch', $branch)
                    ->whereBetween($table.'.created_at', [$req->start_date.' 00:00:00', $req->end_date.' 23:59:59'])
                    ->get();
            } elseif ($page == 'quotation') {
                $data = DB::table('quotations')
                    ->leftJoin('creditusers', 'quotations.credit_user_id', '=', 'creditusers.id')
                    ->select(DB::raw('
                    quotations.transaction_id,
                    quotations.vat_type,
                    quotations.created_at,
                    quotations.customer_name,
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(quotations.vat_amount) as vat,
                    creditusers.username,
                    SUM(quotations.totalamount_wo_discount) as grandtotal_without_discount,
                      SUM(COALESCE(quotations.discount_amount * quotations.quantity, 0))
                    +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
                    '))
                    ->groupBy('transaction_id')
                    ->orderBy('created_at', 'DESC')
                    ->where('branch', $branch)
                    ->whereBetween('quotations.created_at', [$req->start_date.' 00:00:00', $req->end_date.' 23:59:59'])
                    ->get();
            }
        } elseif ($req->start_date == $req->end_date && $req->start_date != '') {
            if ($page == 'sales_order' || $page == 'deliverynote' || $page == 'performance_invoice') {
                $data = DB::table($table)
                    ->leftJoin('payment', $table.'.payment_type', '=', 'payment.id')
                    ->leftJoin('creditusers', $table.'.credit_user_id', '=', 'creditusers.id')
                    ->select(
                        $table.'.transaction_id',
                        $table.'.created_at',
                        $table.'.customer_name',
                        $table.'.vat_type',
                        DB::raw('SUM(DISTINCT '.$table.'.bill_grand_total) as sum'),
                        DB::raw('SUM('.$table.'.vat_amount) as vat'),
                        DB::raw('SUM('.$table.'.totalamount_wo_discount) as grandtotal_without_discount'),
                        DB::raw("SUM(COALESCE($table.discount_amount * $table.quantity, 0))
                        +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount"),
                        'payment.type as payment_type',
                        'creditusers.username'
                    )
                    ->groupBy('transaction_id')
                    ->orderBy($table.'.created_at', 'DESC')
                    ->where('branch', $branch)
                    ->whereDate($table.'.created_at', $req->start_date)
                    ->get();
            } elseif ($page == 'quotation') {
                $data = DB::table('quotations')
                    ->leftJoin('creditusers', 'quotations.credit_user_id', '=', 'creditusers.id')
                    ->select(DB::raw('
                    quotations.transaction_id,
                    quotations.vat_type,
                    quotations.created_at,
                    quotations.customer_name,
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(quotations.vat_amount) as vat,
                    creditusers.username,
                    SUM(quotations.totalamount_wo_discount) as grandtotal_without_discount,
                      SUM(COALESCE(quotations.discount_amount * quotations.quantity, 0))
                    +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
                    '))
                    ->groupBy('transaction_id')
                    ->orderBy('created_at', 'DESC')
                    ->where('branch', $branch)
                    ->whereDate('quotations.created_at', $req->start_date)
                    ->get();
            }
        } else {
            if ($page == 'sales_order' || $page == 'deliverynote' || $page == 'performance_invoice') {
                $data = DB::table($table)
                    ->leftJoin('payment', $table.'.payment_type', '=', 'payment.id')
                    ->leftJoin('creditusers', $table.'.credit_user_id', '=', 'creditusers.id')
                    ->select(
                        $table.'.transaction_id',
                        $table.'.created_at',
                        $table.'.customer_name',
                        $table.'.vat_type',
                        DB::raw('SUM(DISTINCT '.$table.'.bill_grand_total) as sum'),
                        DB::raw('SUM('.$table.'.vat_amount) as vat'),
                        DB::raw('SUM('.$table.'.totalamount_wo_discount) as grandtotal_without_discount'),
                        DB::raw("SUM(COALESCE($table.discount_amount * $table.quantity, 0))
                        +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount"),
                        'payment.type as payment_type',
                        'creditusers.username'
                    )
                    ->groupBy('transaction_id')
                    ->orderBy($table.'.created_at', 'DESC')
                    ->where('branch', $branch)
                    ->whereDate($table.'.created_at', Carbon::today())
                    ->get();
            } elseif ($page == 'quotation') {
                $data = DB::table('quotations')
                    ->leftJoin('creditusers', 'quotations.credit_user_id', '=', 'creditusers.id')
                    ->select(DB::raw('
                    quotations.transaction_id,
                    quotations.vat_type,
                    quotations.created_at,
                    quotations.customer_name,
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(quotations.vat_amount) as vat,
                    creditusers.username,
                    SUM(quotations.totalamount_wo_discount) as grandtotal_without_discount,
                      SUM(COALESCE(quotations.discount_amount * quotations.quantity, 0))
                    +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
                    '))
                    ->groupBy('transaction_id')
                    ->orderBy('created_at', 'DESC')
                    ->where('branch', $branch)
                    ->whereDate('quotations.created_at', Carbon::today())
                    ->get();
            }
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        $start_date = $req->start_date;
        $end_date = $req->end_date;

        if ($page == 'sales_order' || $page == 'quotation') {
            $data_s = [
                'products' => $data,
                'users' => $item,
                'currency' => $currency,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'page' => $page,
                'adminroles' => $adminroles,
                'adminid' => $adminid,
                'tax'=>$tax,
            ];
        } else {
            $data_s = [
                'products' => $data,
                'users' => $item,
                'currency' => $currency,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'page' => $page,
                'tax'=>$tax,
            ];
        }

        return view('/billingdesk/history_sales_delivery', $data_s);
    }

    // purchase order history
    public function purchaseOrderHistory($page)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
        if ($page == 'purchase_order') {
            $purchase = DB::table('purchase_orders')
                ->leftJoin('products', 'purchase_orders.product', '=', 'products.id')
                ->select(DB::raw('purchase_orders.id as id, purchase_orders.reciept_no as reciept_no, purchase_orders.created_at as created_at, purchase_orders.comment as comment, SUM(purchase_orders.price) as price, purchase_orders.supplier as supplier, purchase_orders.file as file,purchase_orders.payment_mode as payment_mode, SUM(purchase_orders.price_without_vat) as price_without_vat, SUM(purchase_orders.vat_amount) as vat_amount, purchase_orders.purchase_order_id as purchase_order_id'))
                ->groupBy('purchase_orders.purchase_order_id')
                ->orderBy('purchase_orders.created_at', 'DESC')
                ->where('purchase_orders.branch', $branch)
                ->get();

            $adminroles = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();
        }

        $start_date = '';
        $end_date = '';

        $data = [
            'users' => $useritem,
            'purchases' => $purchase,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'currency' => $currency,
            'page' => $page,
            'adminroles' => $adminroles,
            'adminid' => $adminid,
            'tax'=>$tax,
        ];

        return view('/inventory/purchase_order_history', $data);
    }

    // purchase order details view

    public function viewPurchaseOrderdetails($page, $purchase_orderid)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        if ($page == 'purchase_order') {
            $count = DB::table('purchase_orders')->count();

            $item = DB::table('purchase_orders')
                ->leftJoin('products', 'purchase_orders.product', '=', 'products.id')
                ->select(DB::raw('purchase_orders.id, purchase_orders.quantity, purchase_orders.unit, purchase_orders.buycost,purchase_orders.price, products.product_name, purchase_orders.created_at, purchase_orders.price_without_vat, purchase_orders.vat_amount'))
                ->where('purchase_orders.purchase_order_id', $purchase_orderid)
                ->get();
        }

        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();

        return view('inventory/purchaseOrderDetails', ['tax'=>$tax,'details' => $item, 'users' => $useritem, 'currency' => $currency, 'page' => $page]);
    }

    public function downloadPurchaseOrder(Request $request, $file)
    {
        return response()->download(storage_path('app/stockbills/'.$file));
    }

    public function purchaseOrderHistorydate($page, Request $req)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();

        if ($req->start_date != $req->end_date) {
            if ($page == 'purchase_order') {
                $purchase = DB::table('purchase_orders')
                    ->leftJoin('products', 'purchase_orders.product', '=', 'products.id')
                    ->select(DB::raw('purchase_orders.id as id, purchase_orders.reciept_no as reciept_no, purchase_orders.created_at as created_at, purchase_orders.comment as comment, SUM(purchase_orders.price) as price, purchase_orders.supplier as supplier, purchase_orders.file as file,purchase_orders.payment_mode as payment_mode, SUM(purchase_orders.price_without_vat) as price_without_vat, SUM(purchase_orders.vat_amount) as vat_amount, purchase_orders.purchase_order_id as purchase_order_id'))
                    ->groupBy('purchase_orders.reciept_no')
                    ->orderBy('purchase_orders.created_at', 'DESC')
                    ->where('purchase_orders.branch', $branch)
                    ->whereBetween('purchase_orders.created_at', [$req->start_date.' 00:00:00', $req->end_date.' 23:59:59'])
                    ->get();

                $adminroles = DB::table('adminusers')
                    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                    ->where('user_id', $adminid)
                    ->get();
            }
        } elseif ($req->start_date == $req->end_date && $req->start_date != '') {
            if ($page == 'purchase_order') {
                $purchase = DB::table('purchase_orders')
                    ->leftJoin('products', 'purchase_orders.product', '=', 'products.id')
                    ->select(DB::raw('purchase_orders.id as id, purchase_orders.reciept_no as reciept_no, purchase_orders.created_at as created_at, purchase_orders.comment as comment, SUM(purchase_orders.price) as price, purchase_orders.supplier as supplier, purchase_orders.file as file,purchase_orders.payment_mode as payment_mode, SUM(purchase_orders.price_without_vat) as price_without_vat, SUM(purchase_orders.vat_amount) as vat_amount, purchase_orders.purchase_order_id as purchase_order_id'))
                    ->groupBy('purchase_orders.reciept_no')
                    ->orderBy('purchase_orders.created_at', 'DESC')
                    ->where('purchase_orders.branch', $branch)
                    ->whereDate('purchase_orders.created_at', $req->start_date)
                    ->get();

                $adminroles = DB::table('adminusers')
                    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                    ->where('user_id', $adminid)
                    ->get();
            }
        } else {
            if ($page == 'purchase_order') {
                $purchase = DB::table('purchase_orders')
                    ->leftJoin('products', 'purchase_orders.product', '=', 'products.id')
                    ->select(DB::raw('purchase_orders.id as id, purchase_orders.reciept_no as reciept_no, purchase_orders.created_at as created_at, purchase_orders.comment as comment, SUM(purchase_orders.price) as price, purchase_orders.supplier as supplier, purchase_orders.file as file,purchase_orders.payment_mode as payment_mode, SUM(purchase_orders.price_without_vat) as price_without_vat, SUM(purchase_orders.vat_amount) as vat_amount, purchase_orders.purchase_order_id as purchase_order_id'))
                    ->groupBy('purchase_orders.reciept_no')
                    ->orderBy('purchase_orders.created_at', 'DESC')
                    ->where('purchase_orders.branch', $branch)
                    ->whereDate('purchase_orders.created_at', Carbon::today())
                    ->get();

                $adminroles = DB::table('adminusers')
                    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                    ->where('user_id', $adminid)
                    ->get();
            }
        }

        $start_date = $req->start_date;
        $end_date = $req->end_date;

        $data = [
            'users' => $useritem,
            'purchases' => $purchase,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'currency' => $currency,
            'page' => $page,
            'adminroles' => $adminroles,
            'adminid' => $adminid,
            'tax'=>$tax,
        ];

        return view('/inventory/purchase_order_history', $data);
    }

    public function addProductModal(Request $request)
    {
        $request->validate([
            'category_new.*' => 'required|',
            'product_name_new.*' => 'required',
            'unit_new.*' => 'required',
            'buycost_new.*' => 'required',
            'sellcost_new.*' => 'required',
            'vat_new.*' => 'required',
        ]);

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $existingProduct_chk = Product::where('product_name', $request->input('product_name_new'))
            ->where('branch', $branch)
            ->first();

        if ($existingProduct_chk) {
            // Product already exists, return an error or handle accordingly
            return response()->json(['success' => false, 'message' => 'Product with the same name already exists']);
        } else {
            if (!empty($request->input('product_name_new'))) {
                $product = new Product();
                $product->product_name = $request->input('product_name_new');
                $product->productdetails = $request->input('product_description');
                $product->unit = $request->input('unit_new');
                $product->rate = $request->input('rate_new');
                $product->purchase_vat = $request->input('purchase_vat_new');
                $product->buy_cost = $request->input('buycost_new');
                $product->selling_cost = $request->input('sellcost_new');
                $product->vat = $request->input('vat_new');
                $product->category_id = $request->input('category_new');
                $product->branch = $branch;
                $product->user_id = Session('softwareuser');

                $product->inclusive_rate = $request->input('inlclusive_rate_new');
                $product->inclusive_vat_amount = $request->input('inlclusive_vat_new');

                // Generate a UUID and use it as the barcode
                $uuid = (string) Str::uuid();

                $barcode = $barcode = (string) $request->input('barcode_newww') ? (string) $request->input('barcode_newww') : substr(hash('crc32', $uuid), 0, 10);

                // $barcode = substr(hash('crc32', $uuid), 0, 10);

                $product->barcode = $barcode;
                $product->save();
            }

            return response()->json(['success' => true, 'message' => 'Product added successfully', 'product' => $product]);
        }
    }

    public function getProductDetails($id)
    {
        $product = Product::find($id);

        if ($product) {
            return response()->json(['success' => true, 'product' => $product]);
        } else {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }
    }

    public function checkProductName(Request $request)
    {
        $data = $request->all();
        $new_product = $data['new_pro_name'];

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $check_product_name = Product::where('product_name', $new_product)
            ->where('branch', $branch)
            ->first();

        if ($check_product_name) {
            $status = true;
        } else {
            $status = false;
        }

        return response()->json(['status' => $status, 'new_product' => $new_product]);
    }

    public function AddcheckProductName(Request $request)
    {
        $data = $request->all();
        $new_product = $data['new_pro_name'];

        $product_id = $data['product_id'];

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        if (empty($product_id)) {
            $check_product_name = Product::where('product_name', $new_product)
                ->where('branch', $branch)
                ->first();

            if ($check_product_name) {
                $status = true;
            } else {
                $status = false;
            }
        } else {
            // Check if any other product with the same name exists, excluding the current product
            $check_product_name = Product::where('product_name', $new_product)
                ->where('branch', $branch)
                ->where('id', '!=', $product_id)
                ->first();

            if ($check_product_name) {
                $status = true;
            } else {
                $status = false;
            }
        }

        return response()->json(['status' => $status, 'new_product' => $new_product]);
    }

    public function paymentVoucher($id)
    {
        if (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        } elseif (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        }
        else {
            return redirect('userlogin'); // or 'adminlogin' based on your logic
        }

        if (Session('adminuser')) {
            $adminid = Session('adminuser');
            $shopdata = Adminuser::Where('id', $adminid)
                ->get();

            $item = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();

            $purchases = DB::table('credit_supplier_transactions')
                ->select(DB::raw('*'))
                ->where('credit_supplier_transactions.credit_supplier_id', $id)
                ->whereNotNull('collectedamount') // New condition for collected_amount
                ->orderBy('created_at', 'DESC')
                ->get();
        } elseif (Session('softwareuser')) {
            $item = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', Session('softwareuser'))
                ->get();

            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

            $userid = Session('softwareuser');
            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();

            $purchases = DB::table('credit_supplier_transactions')
                ->select(DB::raw('*'))
                ->where('credit_supplier_transactions.location', $branch)
                ->where('credit_supplier_transactions.credit_supplier_id', $id)
                ->whereNotNull('collectedamount') // New condition for collected_amount
                ->orderBy('created_at', 'DESC')
                ->get();
        }

        $username = DB::table('suppliers')->where('id', $id)->pluck('name')->first();

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        if (Session('adminuser')) {
            $data = [
                'users' => $item,
                'purchases' => $purchases,
                'supplier_name' => $username,
                'supplier_id' => $id,
                'currency' => $currency,
                'shopdatas' => $shopdata,
            ];
        } elseif (Session('softwareuser')) {
            $data = [
                'users' => $item,
                'purchases' => $purchases,
                'supplier_name' => $username,
                'supplier_id' => $id,
                'currency' => $currency,
            ];
        }

        return view('/billingdesk/paymentvoucher', $data);
    }

    // purchase order receipt check
    public function receiptno_purchaserder(Request $request)
    {
        $exists = DB::table('purchase_orders')
            ->where('reciept_no', $request->input('reciept_no'))
            ->distinct('reciept_no')
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    // edit transaction page
    public function editviewTrans($page, $transactionId, UserService $userService, EditTransactionService $edittransService)
    {
        // Authentication check
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }
        else {
            return redirect('userlogin');
        }

        if ($page == 'edit_bill') {
            $returnProductExists = DB::table('returnproducts')
                ->where('transaction_id', $transactionId)
                ->exists();

            if ($returnProductExists) {
                // Redirect back to the transaction history page or show a message
                return redirect('/transactions')->with('error', 'Cannot edit transaction with sales return.');
            }
        }

        // Fetch user details using the injected service
                // $userId = session('softwareuser');
        $userId = DB::table('buyproducts')
        ->where('transaction_id', $transactionId)
        ->pluck('user_id')
        ->first();
        $branch = DB::table('buyproducts')
        ->where('transaction_id', $transactionId)
        ->pluck('branch')
        ->first();
        // $userItem = $userService->getUserDetails($userId);
        if (Session('softwareuser')) {
            $userId = Session('softwareuser');
            $userItem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userId)
                ->get();

            $adminid = Softwareuser::Where('id', $userId)
                ->pluck('admin_id')
                ->first();
        } elseif (Session('adminuser')) {
            $adminid = Session('adminuser');
            $userItem = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();

            $shopdata = Adminuser::Where('id', $adminid)->get();
        }
        // $branch = Softwareuser::locationById($userId);
        // $branchName = Branch::locationNameById($branch);
        $branchName = DB::table('branches')
        ->where('id',$branch)
        ->pluck('location')
        ->first();
        $adminid = $userService->getAdminId($userId);
          $shopdata = Branch::Where('id', $branch)->get();
        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();


        if ($page == 'edit_bill' || $page == 'clone_bill'|| $page == 'to_delivery') {
            // Fetch item details using the injected product service
            $item = $edittransService->getItems($branch);

            // Fetch other data using the injected product service
            $dataplan = $edittransService->getDataPlan($transactionId);

            $custs = $edittransService->getCustomerName($transactionId);
            $trn_number = $edittransService->getTRN($transactionId);
            $phone = $edittransService->getPhone($transactionId);
            $payment_type = $edittransService->getPaymentType($transactionId);
            $email = $edittransService->getEmail($transactionId);
            $vattype = $edittransService->getVatType($transactionId);
            $credit_user_id = $edittransService->getCreditUserId($transactionId);
            $cash_user_id = $edittransService->getCashUserId($transactionId);

            $product_count = $edittransService->getTransact_count($transactionId);
            ++$product_count;

            $total_discount_type = Buyproduct::Where('transaction_id', $transactionId)
                ->pluck('total_discount_type')
                ->first();

            $total_discount_percent = Buyproduct::Where('transaction_id', $transactionId)
                ->pluck('total_discount_percent')
                ->first();


            $prev_grand_total = Buyproduct::Where('transaction_id', $transactionId)
            ->pluck('bill_grand_total')
            ->first();

            $total_discount_amount = Buyproduct::Where('transaction_id', $transactionId)
                ->pluck('total_discount_amount')
                ->first();
                $bank_id = Buyproduct::Where('transaction_id', $transactionId)
                ->pluck('bank_id')
                ->first();
                $account_name = Buyproduct::Where('transaction_id', $transactionId)
                ->pluck('account_name')
                ->first();
                $employee_id = Buyproduct::Where('transaction_id', $transactionId)
                ->pluck('employee_id')
                ->first();
                $employee_name = Buyproduct::Where('transaction_id', $transactionId)
                ->pluck('employee_name')
                ->first();

                $current_balance = Bank::where('id',$bank_id)
                ->value('current_balance');

                $advance_balance = DB::table('creditsummaries')
                ->where('credituser_id', $credit_user_id)
                ->selectRaw('CASE
                                WHEN COALESCE(collected_amount, 0) > COALESCE(due_amount, 0)
                                THEN COALESCE(collected_amount, 0) - COALESCE(due_amount, 0)
                                ELSE NULL
                             END AS advance_balance')
                ->first();



                $current_lamount = DB::table('creditusers')
                ->where('status', 1)
                ->where(function ($query) use ($credit_user_id, $cash_user_id) {
                    $query->where('id', $credit_user_id)
                          ->orWhere('id', $cash_user_id);
                })
                ->value('current_lamount');

            $credit_user_name = Credituser::Where('id', $credit_user_id)
                ->pluck('name')
                ->first();

            $creditusers = Credituser::Where('admin_id', $adminid)
                ->where('status', 1)
                ->where('location', $branch)
                ->leftJoin('creditsummaries', 'creditusers.id', '=', 'creditsummaries.credituser_id') // Use LEFT JOIN to include all creditusers
                ->select(
                    'creditusers.*', // Select all creditusers columns
                    DB::raw('COALESCE(creditsummaries.due_amount, 0) AS due_amount'), // Treat NULL as 0 for due_amount
                    DB::raw('COALESCE(creditsummaries.collected_amount, 0) AS collected_amount'), // Treat NULL as 0 for collected_amount
                    DB::raw('CASE
                                WHEN COALESCE(creditsummaries.collected_amount, 0) > COALESCE(creditsummaries.due_amount, 0)
                                THEN COALESCE(creditsummaries.collected_amount, 0) - COALESCE(creditsummaries.due_amount, 0)
                                ELSE NULL
                             END AS balance') // Only show balance when collected_amount > due_amount
                )
                ->get();


                $listbank = DB::table('bank')
                ->select('id','bank_name', 'account_name','current_balance','status')
                ->where('status', 1)
                ->where('branch', $branch)
                ->get();
                $tax = Adminuser::Where('id', $adminid)
                ->pluck('tax')
                ->first();

                $listemployee=DB::table('employee')
                ->select('first_name', 'id')
                ->where('branch', $branch)
                ->get();

                $creditlimit_deducted = DB::table('credit_transactions')
                ->where('credituser_id', $credit_user_id)
                ->where('transaction_id', $transactionId)
                ->orderBy('id', 'desc') // Order by ID to get the most recent record
                 ->pluck('credit_lose')
                 ->first();


                 $creditdue_deducted = DB::table('credit_transactions')
                 ->where('credituser_id', $credit_user_id)
                 ->where('transaction_id', $transactionId)
                 ->orderBy('id', 'desc') // Order by ID to get the most recent record
                 ->pluck('credit_balance')
                 ->first();

            // Combine data
            $data = [
                'details' => $dataplan,
                'email' => $email,
                'transaction_id' => $transactionId,
                'productcount' => $product_count,
                'payment_type' => $payment_type,
                'items' => $item,
                'customer_name' => $custs,
                'trn_number' => $trn_number,
                'phone' => $phone,
                'vattype' => $vattype,
                'credit_user_id' => $credit_user_id,
                'total_discount_type' => $total_discount_type,
                'total_discount_percent' => $total_discount_percent,
                'total_discount_amount' => $total_discount_amount,
                'cash_user_id' => $cash_user_id,
                'credit_user_name' => $credit_user_name,
                'creditusers' => $creditusers,
                'tax'=>$tax,
                'bank_id' => $bank_id,
                'account_name' => $account_name,
                'current_balance' => $current_balance,
                'listbank'=>$listbank,
                'employee_id'=>$employee_id,
                'employee_name'=>$employee_name,
                'listemployee'=>$listemployee,
                'current_lamount'=>$current_lamount,
                'advance_balance'=>$advance_balance,
                'prev_grand_total'=>$prev_grand_total,
                'creditlimit_deducted'=>$creditlimit_deducted,
                'creditdue_deducted'=>$creditdue_deducted,
                'branch'=>$branch,


            ];
        }

        return view('/billingdesk/edit_bill', array_merge(
            [
                'users' => $userItem,
                'branches' => $branchName,
                'shopdatas' => $shopdata,
                'page' => $page,
                'tax'=>$tax,
            ],
            $data
        ));
    }

    public function editsubmitData(Request $request, EditTransactionService $edittransService, EditTransactionRepository $repository)
    {
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }
        else {
            return redirect('userlogin');
        }

        $request->validate([
            'productName' => 'required',
        ]);

        $user_id = DB::table('buyproducts')
        ->where('transaction_id', $request->transaction_id)
        ->pluck('user_id')
        ->first();

        $branch = DB::table('buyproducts')
        ->where('transaction_id', $request->transaction_id)
        ->pluck('branch')
        ->first();

        // $user_id = Session('softwareuser');
        // $branch = Softwareuser::locationById($user_id);

        $edit_comment = $request->input('edit_comment');

        $transaction_id = $request->transaction_id;

        if ($request->page == 'edit_bill') {
            $edittransService->ProcessBill($request, $transaction_id, $branch, $user_id);
        }
         $payment_mode = $request->input('payment_mode');
            if ($payment_mode == 3) {
                $transaction = $transaction_id;
            $due_amounttotal = 0;

            foreach ($request->product_id as $key => $productID) {
                $due_amounttotal += $request->bill_grand_total;
            }

            $credituserid = $request->credit_user_id;

            $credit_username = DB::table('creditusers')
                ->where('id', $credituserid)
                ->where('location', $branch)
                ->pluck('name')
                ->first();



            if ($request->payment_type == 3) {
                $due_amount = DB::table('creditsummaries')
                    ->where('credituser_id', $credituserid)
                    ->pluck('due_amount')
                    ->first();

                $due_amountupload = $request->bill_grand_total + $due_amount;

                $paid = DB::table('creditsummaries')
                    ->where('credituser_id', $credituserid)
                    ->pluck('collected_amount')
                    ->first();

                $crediit_note = DB::table('creditsummaries')
                    ->select(DB::raw('creditnote'))
                    ->where('credituser_id', $credituserid)
                    ->pluck('creditnote')
                    ->first();

                    $due = $due_amount - $paid - $crediit_note;

                $credit_limit = DB::table('creditusers')
                ->where('id', $credituserid)
                ->where('location', $branch)
                ->pluck('current_lamount')
                ->first();

                $remaining_bill_amount = $request->bill_grand_total;

                  // Step 0: Deduct from advance balance if available
                  $deducted_from_advance = 0;
            if ($request->advance_balance !== null) {
                $deduct_from_advance = min($request->advance_balance, $remaining_bill_amount);
                $remaining_bill_amount -= $deduct_from_advance;
                $deducted_from_advance = $deduct_from_advance; // Amount deducted from advance balance

                // Update advance balance in the database if needed
                $new_advance_balance = $request->advance_balance - $deduct_from_advance;
                // Save the updated advance balance back to the database if necessary
            }

            $deducted_from_credit = 0;
            if ($remaining_bill_amount > 0 && $credit_limit !== null) {
                if ($credit_limit >= $remaining_bill_amount) {
                    // Deduct the entire remaining bill amount from credit limit
                    $deducted_from_credit = $remaining_bill_amount; // Amount deducted
                    $remaining_credit_limit = $credit_limit - $remaining_bill_amount;
                    $remaining_bill_amount = 0;

                    // Update the current_lamount column in creditusers table
                    DB::table('creditusers')
                        ->where('id', $credituserid)
                        ->where('location', $branch)
                        ->update(['current_lamount' => $remaining_credit_limit]);
                } else {
                    // If credit limit is less than the remaining bill amount, deduct whatever is available and update the credit limit to zero
                    $deducted_from_credit = $credit_limit; // Amount deducted
                    $remaining_bill_amount -= $credit_limit;
                    $remaining_credit_limit = 0;

                    // Update the current_lamount column in creditusers table
                    DB::table('creditusers')
                        ->where('id', $credituserid)
                        ->where('location', $branch)
                        ->update(['current_lamount' => $remaining_credit_limit]);
                }

            }



                $collect = $paid + $request->advance;

                if ($request->advance) {
                    $fund = new Fundhistory();
                    $fund->username = $credit_username;
                    $fund->amount = $request->advance;
                    $fund->credituser_id = $credituserid;
                    $fund->due = ($due + $request->bill_grand_total);
                    $fund->user_id = Session('softwareuser');
                    $fund->location = $branch;
                    $fund->trans_id = $transaction_id;
                    $fund->save();
                }

                $creditsummaries = DB::table('creditsummaries')
                    ->updateOrInsert(
                        ['credituser_id' => $credituserid],
                        [
                            'due_amount' => $due_amountupload,
                            'collected_amount' => $collect,
                        ]
                    );

                // new transaction_creditnote table

                $lastTransaction = DB::table('credit_transactions')
                    ->where('credituser_id', $credituserid)
                    ->where('location', $branch)
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                    $updated_balance = $lastTransaction->updated_balance ?? null;
                    $last_invoice_due = $lastTransaction->balance_due ?? null;


                    // $new_due = $updated_balance + $due_amounttotal;
                    $new_due = $updated_balance + $request->bill_grand_total;

                    $advanced_bal = $new_due - $request->advance;
                    // For the invoice_due, subtract the collected amount from the last invoice_due
            if ($lastTransaction && $lastTransaction->transaction_id === $transaction) {
            $new_invoice_due = $last_invoice_due - $request->advance;
            } else {
            $new_invoice_due = $request->bill_grand_total - $request->advance;
            }
                $credit_trans = new CreditTransaction();
                $credit_trans->credituser_id = $credituserid;
                $credit_trans->credit_username = $credit_username;
                $credit_trans->user_id = Session('softwareuser');
                $credit_trans->location = $branch;
                $credit_trans->transaction_id = $transaction;
                if ($updated_balance == null) {
                    $credit_trans->due = 0;
                } else {
                    $credit_trans->due = $updated_balance;
                }
                $credit_trans->updated_balance = $advanced_bal;
                $credit_trans->collected_amount = $request->advance ?? null;
                $credit_trans->balance_due = $new_invoice_due;
                $credit_trans->Invoice_due = $request->bill_grand_total;
                $credit_trans->due_days = $request->due_days;
                $credit_trans->comment = 'Invoice';
                if ($deducted_from_advance > 0) {
                    $credit_trans->credit_balance = $deducted_from_advance;
                }
                if ($deducted_from_credit > 0) {
                    $credit_trans->credit_lose = $deducted_from_credit;
                }
                $credit_trans->save();
            } elseif (($request->payment_type == 1 || $request->payment_type == 2 || $request->payment_type == 4) && ($request->credit_id != '' || $request->credit_id != null)) {
                $lastTransaction = DB::table('cash_trans_statements')
                    ->where('cash_user_id', $credituserid)
                    ->where('location', $branch)
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                $updated_balance = $lastTransaction->updated_balance ?? null;

                // $new_due = $updated_balance + $due_amounttotal;
                $new_bal = $updated_balance + $request->bill_grand_total;

                $cash_trans = new CashTransStatement();
                $cash_trans->cash_user_id = $credituserid;
                $cash_trans->cash_username = $credit_username;
                $cash_trans->user_id = Session('softwareuser');
                $cash_trans->location = $branch;
                $cash_trans->transaction_id = $transaction;
                $cash_trans->collected_amount = $request->bill_grand_total;
                $cash_trans->updated_balance = $new_bal;
                $cash_trans->comment = 'Invoice';
                $cash_trans->payment_type = $request->payment_type;
                $cash_trans->save();
            }
            DB::table('cash_trans_statements')
            ->where('cash_user_id', $credituserid)
            ->where('location', $branch)
            ->where('transaction_id', $transaction_id) // Add this line to filter by transaction_id
            ->delete();

            }
          // bank history...........................

          if ($request->bank_name && $request->account_name) {
            $current_balance = DB::table('bank')
                ->where('id', $request->bank_name)
                ->where('account_name', $request->account_name)
                ->pluck('current_balance')
                ->first();

            $bank_history = Bankhistory::where('transaction_id', $transaction_id)
                ->where('user_id', $user_id)
                ->where('branch', $branch)
                ->where('detail', 'Sales')
                ->where('dr_cr', 'Credit')
                ->where('bank_id', $request->bank_name)
                ->where('account_name', $request->account_name)
                ->whereDate('date', Carbon::today())
                ->first();

            if ($bank_history) {
                $existing_amount = $bank_history->amount;
                $new_amount = $request->bill_grand_total;
                $difference = $new_amount - $existing_amount;

                $new_balance = $current_balance + $difference;

                DB::table('bank')
                    ->where('id', $request->bank_name)
                    ->where('account_name', $request->account_name)
                    ->update(['current_balance' => $new_balance]);

                $bank_history->amount = $new_amount;
                $bank_history->save();
            } else {
                // Handle the case where the record does not exist, if needed
            }
        }

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $user_id)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' done '.$transaction_id.' editing - '.$edit_comment;

        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $user_id)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($user_id, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return redirect('/billdeskfinalreciept/'.$transaction_id);
    }

    // edit purchase page
    public function editPurchase($page, $receipt_no, UserService $userService, EditPurchaseService $editpurchaseService)
    {
        // Authentication check
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }
        else {
            return redirect('userlogin');
        }

        // Check if quantities are equal
        $hasEqualQuantities = DB::table('stock_purchase_reports')
            ->where('receipt_no', $receipt_no)
            ->whereColumn('quantity', 'sell_quantity')
            ->exists();

        if (!$hasEqualQuantities) {
            // Redirect back to the purchase history page or show an error message
            return redirect('/purchasehistory')->with('error', 'Cannot edit purchase with unequal quantities.');
        }

        // Fetch user details using the injected service
        $userId = DB::table('stockdetails')
        ->where('reciept_no', $receipt_no)
        ->pluck('user_id')
        ->first();
        $branch = DB::table('stockdetails')
        ->where('reciept_no', $receipt_no)
        ->pluck('branch')
        ->first();
        // $userId = session('softwareuser');
        // $userItem = $userService->getUserDetails($userId);

        if (Session('softwareuser')) {
            $userid = Session('softwareuser');
            $userItem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();
                $adminid = Softwareuser::Where('id', $userid)
                    ->pluck('admin_id')
                ->first();
            $shopdata = Branch::Where('id', $branch)->get();
        } elseif (Session('adminuser')) {
            $adminid = Session('adminuser');
            $userItem = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();

                $shopdata = Adminuser::Where('id', $adminid)->get();
            }


        // Fetch branch details using the injected service
        // $branch = Softwareuser::locationById($userId);
        $branchName = DB::table('branches')
        ->where('id',$branch)
        ->pluck('location')
        ->first();

        // $branchName = Branch::locationNameById($branch);

        // $adminid = $userService->getAdminId($userId);





        if ($page == 'edit_purchase') {
            // // Fetch other data using the injected product service
            $dataplan = $editpurchaseService->getDataPlan($receipt_no, $branch);

            $comment = $editpurchaseService->getComment($receipt_no, $branch);
            $supplier = $editpurchaseService->getSupplier($receipt_no, $branch);
            $supplier_id = $editpurchaseService->getSupplierID($receipt_no, $branch);
            $payment_type = $editpurchaseService->getPaymentType($receipt_no, $branch);
            $invoice_date = $editpurchaseService->getInvoiceDate($receipt_no, $branch);

            $products = $editpurchaseService->getProducts($branch);
            $bank_id = $editpurchaseService->getBankID($receipt_no, $branch);
            $account_name = $editpurchaseService->getAccountName($receipt_no, $branch);

            $current_balance = DB::table('bank')
            ->where('id',$bank_id)
            ->value('current_balance');
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
            $prev_grand_total = Stockdetail::where('reciept_no', $receipt_no)
            ->sum('price');
            $discount = Stockdetail::Where('reciept_no', $receipt_no)
            ->pluck('discount')
            ->sum();
            $method = Stockdetail::Where('reciept_no', $receipt_no)
            ->pluck('method')
            ->first();
             $returnstatus = Returnpurchase::where('reciept_no', $receipt_no)->exists() ? 1 : 0;

            // Combine data
            $data = [
                'details' => $dataplan,
                'receipt_no' => $receipt_no,
                'comment' => $comment,
                'supplier' => $supplier,
                'supplier_id' => $supplier_id,
                'payment_type' => $payment_type,
                'invoice_date' => $invoice_date,
                'products' => $products,
                'tax'=>$tax,
                'bank_id' => $bank_id,
                'account_name' => $account_name,
                'current_balance' => $current_balance,
                'prev_grand_total'=>$prev_grand_total,
                'discount'=>$discount,
                'returnstatus'=>$returnstatus,
                'method'=>$method
                        ];
        }

        return view('/inventory/edit_purchase', array_merge(
            [
                'users' => $userItem,
                'branches' => $branchName,
                'shopdatas' => $shopdata,
                'page' => $page,
            ],
            $data
        ));
    }

    // submit edit purchase
    public function submitEditPurchase(Request $request, EditPurchaseService $editprservice, EditPurchaseRepository $prrepo)
    {
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }
        else {
            return redirect('userlogin');
        }

        $request->validate([
            'productName' => 'required',
        ]);

        // $user_id = Session('softwareuser');
        $user_id = DB::table('stockdetails')
        ->where('reciept_no', $request->reciept_no)
        ->pluck('user_id')
        ->first();

        $branch = DB::table('stockdetails')
        ->where('reciept_no', $request->reciept_no)
        ->pluck('branch')
        ->first();

        // $branch = Softwareuser::locationById($user_id);

        if ($request->page == 'edit_purchase') {
            $edit_comment = $request->input('edit_comment');

            $receiptNo = $request->reciept_no;

            $purchase_exist = DB::table('stockdetails')
                ->where('reciept_no', $receiptNo)
                ->where('branch', $branch)
                ->exists();

            $oldStockdetailsdata = $editprservice->OldStockdetailsDataCheck($purchase_exist, $receiptNo, $branch);

            $data = $oldStockdetailsdata[0];
            $data_1 = $oldStockdetailsdata[1];
            $grandtotal = $data['grandtotal'];
            $old_method = $data['old_method'];
            $totaldiscount = $data['totaldiscount'];
            $old_purchase_data = $data['old_purchase_data'];
            $old_quantities = $data['old_quantities'];
            $prev_pr_stock_repts_data = $data_1['prev_pr_stock_repts_data'];

            $editprservice->editPurchaseData($request, $old_purchase_data, $old_quantities, $branch, $user_id, $grandtotal,$totaldiscount,$old_method);
        }


        if ($request->bank_name && $request->account_name) {
            $current_balance = DB::table('bank')
                ->where('id', $request->bank_name)
                ->where('account_name', $request->account_name)
                ->pluck('current_balance')
                ->first();


                $bank_history = Bankhistory::where('reciept_no', $receiptNo)
                ->where('user_id', $user_id)
                ->where('branch', $branch)
                ->where('detail', 'Purchase')
                ->where('dr_cr', 'Debit')
                ->where('bank_id', $request->bank_name)
                ->where('account_name', $request->account_name)
                ->whereDate('date', Carbon::today())
                ->first();

                if ($bank_history) {
                    $existing_amount = $bank_history->amount;
                    $new_amount = $request->price;
                    $difference = $new_amount - $existing_amount;

                    $new_balance = $current_balance - $difference;

                    DB::table('bank')
                        ->where('id', $request->bank_name)
                        ->where('account_name', $request->account_name)
                        ->update(['current_balance' => $new_balance]);

                    $bank_history->amount = $new_amount;
                    $bank_history->save();
                } else {
                    // Handle the case where the record does not exist, if needed
                }
        }

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' done Purchase '.$receiptNo.' editing - '.$edit_comment;

        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();
        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */

        return redirect('/purchasehistory')->with('success', 'Purchase data Edited successfully!');
    }

    public function getPreviousSellingCost(Request $request)
    {
        // Retrieve data based on phone number and selected products
        $cust_id = $request->input('cust_id');
        $selectedProducts = $request->input('selectedProducts');
        $barcodeProducts = $request->input('barcodeProducts');

        $previousSellingCost = [];

        if (($cust_id != '' || $cust_id != null) && ($selectedProducts != '' || $selectedProducts != null) || ($barcodeProducts != '' || $barcodeProducts != null)) {
            if ($selectedProducts != '' || $selectedProducts != null) {
                $product_id = $selectedProducts;
            } elseif ($barcodeProducts != '' || $barcodeProducts != null) {
                $product_id = $barcodeProducts;
            }

            $latestTransaction = Buyproduct::where('customer_name', $cust_id)
                ->where('product_id', $selectedProducts)
                ->select('mrp', 'product_name', 'product_id')
                ->orderBy('created_at', 'desc')
                ->first();
            // ->keyBy('product_id')
            // ->toArray();

            if ($latestTransaction) {
                $previousSellingCost = [
                    $latestTransaction->product_id => [
                        'mrp' => $latestTransaction->mrp,
                        'product_name' => $latestTransaction->product_name,
                        'product_id' => $latestTransaction->product_id,
                    ],
                ];
            }
        }

        return response()->json($previousSellingCost);
    }

    // convert to invoice billing page
    public function toInvoiceBillPage($page, $transactionId, UserService $userService, salesorderService $sales_order_Service, QuotationService $quotation_Service)
    {
        // Authentication check
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        // Fetch user details using the injected service
        $userId = session('softwareuser');
        $userItem = $userService->getUserDetails($userId);

        // Fetch branch details using the injected service
        $branch = Softwareuser::locationById($userId);

        $branchName = Branch::locationNameById($branch);

        $adminid = $userService->getAdminId($userId);

        // Fetch shop data using the injected service
          $shopdata = Branch::Where('id', $branch)->get();


            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        if ($page == 'sales_order') {
            $item = $sales_order_Service->getProItems($branch);
            $dataplan = $sales_order_Service->getSalesoderDatas($transactionId);

            $custs = $sales_order_Service->getCusName($transactionId);

            $trn_number = $sales_order_Service->getSTRN($transactionId);
            $phone = $sales_order_Service->getSPhone($transactionId);
            $payment_type = $sales_order_Service->getSPaymentType($transactionId);
            $email = $sales_order_Service->getSEmail($transactionId);
            $vattype = $sales_order_Service->getSVatType($transactionId);
            $credit_user_id = $sales_order_Service->getSCreditUserId($transactionId);
            $cash_user_id = $sales_order_Service->getCashUserId($transactionId);
            $bank_id = $sales_order_Service->getBankid($transactionId);
            $account_name = $sales_order_Service->getAccountname($transactionId);
            $employee_id = $sales_order_Service->getEmployeeid($transactionId);
            $employee_name = $sales_order_Service->getEmployeename($transactionId);
            $current_balance = DB::table('bank')
            ->where('id',$bank_id)
            ->value('current_balance');
            $listbank = DB::table('bank')
            ->select('id','bank_name', 'account_name', 'status') // Include status if you need it
            ->where('status', 1)
            ->where('branch', $branch)
            ->get();
            $listemployee=DB::table('employee')
            ->select('first_name', 'id')
            ->where('branch', $branch)
            ->get();
            $product_count = $sales_order_Service->getSalesorder_count($transactionId);
            ++$product_count;

            $creditusers = Credituser::Where('admin_id', $adminid)
                ->where('status', 1)
                ->where('location', $branch)
                ->get();
            $current_lamount= DB::table('creditusers')
                ->where('status', 1)
                ->where('id', $credit_user_id)
                ->value('current_lamount');

                $advance_balance = DB::table('creditsummaries')
                ->where('credituser_id', $credit_user_id)
                ->selectRaw('CASE
                                WHEN COALESCE(collected_amount, 0) > COALESCE(due_amount, 0)
                                THEN COALESCE(collected_amount, 0) - COALESCE(due_amount, 0)
                                ELSE NULL
                             END AS advance_balance')
                ->first();

            $credit_user_name = Credituser::Where('admin_id', $adminid)
                ->where('id', $credit_user_id)
                ->where('status', 1)
                ->where('location', $branch)
                ->pluck('name')
                ->first();

            $total_discount_type = DB::table('sales_orders')
                ->where('transaction_id', $transactionId)
                ->pluck('total_discount_type')
                ->first();

            $total_discount_percent = DB::table('sales_orders')
                ->where('transaction_id', $transactionId)
                ->pluck('total_discount_percent')
                ->first();

            $total_discount_amount = DB::table('sales_orders')
                ->where('transaction_id', $transactionId)
                ->pluck('total_discount_amount')
                ->first();
        } elseif ($page == 'quotation' || $page == 'clone_quotation') {
            $item = $quotation_Service->getQuotationProItems($branch);
            $dataplan = $quotation_Service->getQuotationDatas($transactionId);

            $custs = $quotation_Service->getQuotationCusName($transactionId);
            $trn_number = $quotation_Service->getQuotationTRN($transactionId);
            $phone = $quotation_Service->getQuotationPhone($transactionId);
            $payment_type = $quotation_Service->getQuotationPaymentType($transactionId);
            $email = $quotation_Service->getQuotationEmail($transactionId);
            $vattype = $quotation_Service->getQuotationVatType($transactionId);
            $credit_user_id = $quotation_Service->getQuotationCreditUserId($transactionId);
            $cash_user_id = $quotation_Service->getQuotationCashUserId($transactionId);
            $bank_id = $quotation_Service->getQuotationBankid($transactionId);
            $account_name = $quotation_Service->getQuotationAccountname($transactionId);
            $employee_id = $quotation_Service->getQuotationEmployeeid($transactionId);
            $employee_name = $quotation_Service->getQuotationEmployeename($transactionId);
            $current_balance = DB::table('bank')
            ->where('id',$bank_id)
            ->value('current_balance');

            $product_count = $quotation_Service->getQuotation_count($transactionId);
            ++$product_count;

            $creditusers = Credituser::Where('admin_id', $adminid)
                ->where('status', 1)
                ->where('location', $branch)
                ->get();

            $credit_user_name = Credituser::Where('admin_id', $adminid)
                ->where('id', $credit_user_id)
                ->where('status', 1)
                ->where('location', $branch)
                ->pluck('name')
                ->first();

            $total_discount_type = DB::table('quotations')
                ->where('transaction_id', $transactionId)
                ->pluck('total_discount_type')
                ->first();

            $total_discount_percent = DB::table('quotations')
                ->where('transaction_id', $transactionId)
                ->pluck('total_discount_percent')
                ->first();

            $total_discount_amount = DB::table('quotations')
                ->where('transaction_id', $transactionId)
                ->pluck('total_discount_amount')
                ->first();
                $listbank = DB::table('bank')
                ->select('id','bank_name', 'account_name', 'status') // Include status if you need it
                ->where('status', 1)
                ->where('branch', $branch)
                ->get();
                $listemployee=DB::table('employee')
                ->select('first_name', 'id')
                ->where('branch', $branch)
                ->get();
                $current_lamount= DB::table('creditusers')
                ->where('status', 1)
                ->where('id', $credit_user_id)
                ->value('current_lamount');

                $advance_balance = DB::table('creditsummaries')
        ->where('credituser_id', $credit_user_id)
        ->selectRaw('CASE
                        WHEN COALESCE(collected_amount, 0) > COALESCE(due_amount, 0)
                        THEN COALESCE(collected_amount, 0) - COALESCE(due_amount, 0)
                        ELSE NULL
                     END AS advance_balance')
        ->first();

        }
        $data = [
            'details' => $dataplan,
            'email' => $email,
            'transaction_id' => $transactionId,
            'productcount' => $product_count,
            'payment_type' => $payment_type,
            'items' => $item,
            'customer_name' => $custs,
            'trn_number' => $trn_number,
            'phone' => $phone,
            'vattype' => $vattype,
            'credit_user_id' => $credit_user_id,
            'creditusers' => $creditusers,
            'credit_user_name' => $credit_user_name,
            'total_discount_type' => $total_discount_type,
            'total_discount_percent' => $total_discount_percent,
            'total_discount_amount' => $total_discount_amount,
            'cash_user_id' => $cash_user_id,
            'tax'=>$tax,
            'bank_id' => $bank_id,
            'account_name' => $account_name,
            'current_balance' => $current_balance,
            'listbank'=>$listbank,
            'listemployee'=>$listemployee,
            'employee_id'=>$employee_id,
            'employee_name'=>$employee_name,
            'current_lamount'=>$current_lamount,
            'advance_balance'=>$advance_balance,
            'branch'=>$branch,
        ];

        return view('/billingdesk/edit_bill', array_merge(
            [
                'users' => $userItem,
                'branches' => $branchName,
                'shopdatas' => $shopdata,
                'page' => $page,
                'tax'=>$tax,
            ],
            $data
        ));
    }

    // cash statement transactions

    public function cashStatementTransactions(Request $request, $id)
    {
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }
        // Validate and sanitize input
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // original
        // $salesdata = DB::table('cash_trans_statements')
        //     ->select(
        //         'cash_trans_statements.*',
        //         DB::raw("(SELECT created_at FROM buyproducts WHERE transaction_id COLLATE utf8mb4_general_ci = cash_trans_statements.transaction_id LIMIT 1) as transaction_date")
        //     )
        //     ->where('cash_trans_statements.cash_user_id', $id)
        //     ->paginate(20);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = DB::table('cash_trans_statements')
            ->select(
                'cash_trans_statements.*',
                DB::raw('(SELECT created_at FROM buyproducts WHERE transaction_id COLLATE utf8mb4_general_ci = cash_trans_statements.transaction_id LIMIT 1) as transaction_date')
            )
            ->where('cash_trans_statements.cash_user_id', $id);

        // Apply date filters if provided
        if ($startDate && $endDate) {
            $query->whereBetween('cash_trans_statements.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }
        $salesdata = $query->paginate(20);

        if (Session('softwareuser')) {
            $item = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', Session('softwareuser'))
                ->get();

            $userid = Session('softwareuser');
            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();

            $branch = Softwareuser::locationById($userid);
        } elseif (Session('adminuser')) {
            $item = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', Session('adminuser'))
                ->get();

            $adminid = Session('adminuser');
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $lastTransactionQuery = DB::table('cash_trans_statements')
            ->where('cash_user_id', $id)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        // Check if the user is a normal user and apply the branch filter
        if (session('user_role') == 'softwareuser') {
            $lastTransactionQuery->where('location', $branch);
        }

        $lastTransaction = $lastTransactionQuery->first();
        $updated_balance = $lastTransaction->updated_balance ?? null;

        $data = [
            'users' => $item,
            'salesdata' => $salesdata,
            'currency' => $currency,
            'cash_customer_id' => $id,
            'updated_balance' => $updated_balance,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        return view('/billingdesk/customercashdata', $data);
    }

    // edit sales order
    public function editedsales($page, $transaction_id, UserService $userService, salesorderService $sales_order_Service)
    {
        $userid = Session('softwareuser');
        $adminid = $userService->getAdminId($userid);
        $branch = Softwareuser::locationById($userid);
        $adminid = $userService->getAdminId($userid);
        $useritem = $userService->getUserDetails($userid);

          $shopdata = Branch::Where('id', $branch)->get();


        $items = $sales_order_Service->getProItems($branch);

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        $customer_name = $sales_order_Service->getCusName($transaction_id);
        $phone = $sales_order_Service->getSPhone($transaction_id);
        $email = $sales_order_Service->getSEmail($transaction_id);
        $trn_number = $sales_order_Service->getSTRN($transaction_id);
        $credit_user_id = $sales_order_Service->getSCreditUserId($transaction_id);
        $cash_user_id = $sales_order_Service->getCashUserId($transaction_id);
        $bank_id = $sales_order_Service->getBankid($transaction_id);
        $account_name = $sales_order_Service->getAccountname($transaction_id);
        $employee_id = $sales_order_Service->getEmployeeid($transaction_id);
        $employee_name = $sales_order_Service->getEmployeename($transaction_id);

        $vattype = DB::table('sales_orders')
            ->Where('transaction_id', $transaction_id)
            ->pluck('vat_type')
            ->first();

        $dataplan = DB::table('sales_orders')
            ->join('products', 'sales_orders.product_id', '=', 'products.id')
            ->where('sales_orders.transaction_id', $transaction_id)
            ->select([
                'sales_orders.*',
                'products.id as product_id', // Include product ID for reference
                'products.product_name',
                'products.status', // Explicitly select product status
            ])
            ->get();

        $payment_type = DB::table('sales_orders')
            ->Where('transaction_id', $transaction_id)
            ->pluck('payment_type')
            ->first();

        $total_discount_type = DB::table('sales_orders')
            ->Where('transaction_id', $transaction_id)
            ->pluck('total_discount_type')
            ->first();

        $total_discount_percent = DB::table('sales_orders')
            ->Where('transaction_id', $transaction_id)
            ->pluck('total_discount_percent')
            ->first();

        $total_discount_amount = DB::table('sales_orders')
            ->Where('transaction_id', $transaction_id)
            ->pluck('total_discount_amount')
            ->first();

        $data = [
            'details' => $dataplan,
            'email' => $email,
            'transaction_id' => $transaction_id,
            'payment_type' => $payment_type,
            'items' => $items,
            'customer_name' => $customer_name,
            'trn_number' => $trn_number,
            'phone' => $phone,
            'vattype' => $vattype,
            'credit_user_id' => $credit_user_id,
            'total_discount_type' => $total_discount_type,
            'total_discount_percent' => $total_discount_percent,
            'total_discount_amount' => $total_discount_amount,
            'cash_user_id' => $cash_user_id,
            'users' => $useritem,
            'currency' => $currency,
            'shopdatas' => $shopdata,
            'page' => $page,
            'tax'=>$tax,
            'bank_id' => $bank_id,
            'account_name' => $account_name,
            'employee_id' => $employee_id,
            'employee_name' => $employee_name,
            'branch'=>$branch,


        ];

        return view('billingdesk.edit_bill', $data);
    }

    public function editedsalesorder(Request $request, $transaction_id)
    {
        // Retrieve the existing sales order
        $salesOrder = DB::table('sales_orders')->where('transaction_id', $transaction_id)->first();

        if (!$salesOrder) {
            return redirect('/history/sales_order')->with('error', 'Sales Order not found.');
        }

        foreach ($request->input('productId') as $productID => $value) {
            $existingProduct = DB::table('sales_orders')
                ->where('transaction_id', $transaction_id)
                ->where('product_id', $productID)
                ->first();

            $updatedData = [
                'customer_name' => $request->input('customer_name'),
                'trn_number' => $request->input('trn_number'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'vat_type' => $request->input('vat_type_value'),
                'unit' => $request->input("prounit.$productID"),
                'product_name' => $request->input("productName.$productID"),
                'product_id' => $productID,
                'quantity' => $request->input("quantity.$productID"),
                'remain_quantity' => $request->input("quantity.$productID"),
                'mrp' => $request->input("mrp.$productID"),
                'one_pro_buycost' => $request->input("buy_cost.$productID"),
                'one_pro_buycost_rate' => $request->input("buycost_rate.$productID"),
                'inclusive_rate' => ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$productID] : null,
                'exclusive_rate' => ($request->vat_type_value == 2) ? $request->rate_discount_r[$productID] : null,
                'price' => $request->input("price.$productID"),
                'vat_amount' => $request->input("vat_amount.$productID"),
                'total_amount' => $request->input("total_amount.$productID"),
                'fixed_vat' => $request->input("fixed_vat.$productID"),
                'netrate' => $request->input("net_rate.$productID"),

                'totalamount_wo_discount' => $request->input("total_amount_wo_discount.$productID"),

                'discount' => (isset($request->productStatus[$productID]) && $request->productStatus[$productID] == 0) ?
                    ($request->dis_count_tp_ori[$productID] == 'none' ? $request->dis_count[$productID] : ($request->dis_counttp_ori[$productID] == 'percentage' ? $request->dis_count[$productID] : ($request->dis_count_tp_ori[$productID] == 'amount' ? ($request->dis_count[$productID] / ($request->vat_type_value == 1 ? $request->mrp[$productID] : $request->mrp[$productID])) * 100 : 0))) : ($request->dis_count_type[$productID] == 'none' ? $request->dis_count[$productID] : ($request->dis_count_type[$productID] == 'percentage' ? $request->dis_count[$productID] : ($request->dis_count_type[$productID] == 'amount' ? ($request->dis_count[$productID] / ($request->vat_type_value == 1 ? $request->mrp[$productID] : $request->mrp[$productID])) * 100 : 0))),

                'discount_amount' => (isset($request->productStatus[$productID]) && $request->productStatus[$productID] == 0) ?
                    ($request->dis_count__tp_ori[$productID] == 'percentage' ?
                        ($request->vat_type_value == 1 ? $request->mrp[$productID] * ($request->dis_count[$productID] / 100) :
                            $request->mrp[$productID] * ($request->dis_count[$productID] / 100)) : ($request->dis_count__tp_ori[$productID] == 'amount' ? $request->dis_count[$productID] : 0)) : ($request->dis_count_type[$productID] == 'percentage' ?
                        ($request->vat_type_value == 1 ? $request->mrp[$productID] * ($request->dis_count[$productID] / 100) :
                            $request->mrp[$productID] * ($request->dis_count[$productID] / 100)) : ($request->dis_count_type[$productID] == 'amount' ? $request->dis_count[$productID] : 0)),

                'bill_grand_total' => $request->input('bill_grand_total'),
                'discount_type' => (isset($request->productStatus[$productID]) && $request->productStatus[$productID] == 0) ? $request->dis_count__tp_ori[$productID] : $request->dis_count_type[$productID],
                'price_wo_discount' => $request->input("price_withoutvat_wo_discount.$productID"),
                'bill_grand_total_wo_discount' => $request->input('bill_grand_total_wo_discount'),
                'total_discount_type' => $request->input('total_discount'),
                'total_discount_percent' => $request->discount_percentage != '' || $request->discount_percentage != null ?
                    $request->discount_percentage : ($request->discount_amount / $request->bill_grand_total_wo_discount) * 100,
                'total_discount_amount' => $request->discount_amount != '' || $request->discount_amount != null ?
                    $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100)),
            ];

            $finalData = array_merge($updatedData, [
                'transaction_id' => $transaction_id,
                'branch' => $salesOrder->branch,
                'bank_id' => $salesOrder->bank_id,
                'account_name' => $salesOrder->account_name,
                'employee_id' => $salesOrder->employee_id,
                'employee_name' => $salesOrder->employee_name,
                'user_id' => $salesOrder->user_id,
                'credit_user_id' => $salesOrder->credit_user_id,
                'payment_type' => $salesOrder->payment_type,
                'cash_user_id' => $salesOrder->cash_user_id,
            ]);

            if ($existingProduct) {
                DB::table('sales_orders')
                    ->where('transaction_id', $transaction_id)
                    ->where('product_id', $productID)
                    ->update($finalData);
            } else {
                DB::table('sales_orders')->insert($finalData);
            }
        }

        return redirect('/history/sales_order')->with('success', 'Sales Order updated successfully.');
    }

    // billing draft........................
    public function draft($page)
    {
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $branch = DB::table('softwareusers')
            ->where('id', $userid)
            ->pluck('location')
            ->first();
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
          $shopdata = Branch::Where('id', $branch)->get();
        switch ($page) {
            case 'bill_draft':
                $table = 'billdraft';
                break;
            case 'salesdraft':
                $table = 'sales_orders_draft';
                break;
            case 'quotationdraft':
                $table = 'quotations_draft';
                break;
            case 'performadraft':
                $table = 'performance_invoices_draft';
                break;
            case 'deliverydraft':
                $table = 'delivery_notes_draft';
                break;
        }

        if ($page == 'bill_draft' || $page == 'salesdraft' || $page == 'quotationdraft' || $page == 'performadraft' || $page == 'deliverydraft') {
            $draft = DB::table($table)
                ->select("$table.transaction_id", "$table.created_at", "$table.customer_name", "$table.phone")
                ->groupBy("$table.transaction_id")
                ->orderBy("$table.created_at", 'DESC')
                ->where('branch', $branch)
                ->get();
        } elseif ($page == 'productdraft') {
            $draft = DB::table('productdraft')
                ->select(DB::raw("
                    productdraft.draft_id,
                    productdraft.created_at,
                    GROUP_CONCAT(productdraft.product_name SEPARATOR ', ') AS product_name,
                    GROUP_CONCAT(productdraft.product_code SEPARATOR ', ') AS product_code
                "))
                ->groupBy('productdraft.draft_id')
                ->orderBy('productdraft.created_at', 'DESC')
                ->where('branch', $branch)
                ->get();
        } elseif ($page == 'purchasedraft') {
            $draft = DB::table('purchasedraft')
                ->select(DB::raw('
                    purchasedraft.reciept_no,
                    purchasedraft.created_at,
                    purchasedraft.reciept_no,
                    purchasedraft.supplier,
                    purchasedraft.comment,
                    purchasedraft.method

                '))
                ->groupBy('purchasedraft.reciept_no')
                ->orderBy('purchasedraft.created_at', 'DESC')
                ->where('branch', $branch)
                ->get();
        }

        $data = [
            'users' => $item,
            'draft' => $draft,
            'page' => $page,
            'shopdatas'=>$shopdata
        ];

        return view('billingdesk.draft', $data);
    }

    public function savedraft(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $branch = DB::table('softwareusers')
            ->where('id', session('softwareuser'))
            ->pluck('location')
            ->first();

        $count = DB::table('billdraft')
            ->distinct()
            ->count('transaction_id');

         ++$count;

        $transaction_id = 'Draft'.($count + 1);

        foreach ($request->product_id as $key => $productID) {
            $data = new BillDraft();
            $data->product_name = $request->productName[$key];
            $data->quantity = $request->quantity[$key];
            $data->remain_quantity = $request->quantity[$key];
            $data->unit = $request->prounit[$key];
            $data->product_id = $productID;
            $data->transaction_id = $transaction_id;
            $data->customer_name = $request->customer_name;
            $data->email = $request->email;
            $data->trn_number = $request->trn_number;
            $data->phone = $request->phone;
            $data->price = $request->price[$key];
            $data->total_amount = $request->total_amount[$key];
            $data->payment_type = $request->payment_type;
            $data->user_id = Session('softwareuser');
            $data->branch = $branch;
            $data->one_pro_buycost = $request->buy_cost[$key];
            $data->mrp = $request->mrp[$key];
            $data->fixed_vat = $request->fixed_vat[$key];
            $data->bank_id = $request->bank_name;
            $data->account_name = $request->account_name;
            $data->employee_id = $request->employee_id;
            $data->employee_name = $request->employee_name;

            $data->vat_amount = $request->vat_amount[$key];

            if ($request->payment_type == 3) {
                $data->credit_user_id = $request->credit_id;
            } elseif ($request->payment_type == 1 || $request->payment_type == 2 || $request->payment_type == 4) {
                $data->cash_user_id = $request->credit_id;
            }

            $data->vat_type = $request->vat_type_value;

            $data->one_pro_buycost_rate = $request->buycost_rate[$key];
            $data->netrate = $request->net_rate[$key];

            $data->inclusive_rate = ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$key] : null;
            $data->exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

            $data->discount_type = $request->dis_count_type[$key];

            if ($request->vat_type_value == 1) {
                if ($request->dis_count_type[$key] == 'none') {
                    $data->discount = $request->dis_count[$key];
                } elseif ($request->dis_count_type[$key] == 'percentage') {
                    $data->discount = $request->dis_count[$key];
                    $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                } elseif ($request->dis_count_type[$key] == 'amount') {
                    $data->discount_amount = $request->dis_count[$key];
                    $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                }
            } elseif ($request->vat_type_value == 2) {
                if ($request->dis_count_type[$key] == 'none') {
                    $data->discount = $request->dis_count[$key];
                } elseif ($request->dis_count_type[$key] == 'percentage') {
                    $data->discount = $request->dis_count[$key];
                    $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                } elseif ($request->dis_count_type[$key] == 'amount') {
                    $data->discount_amount = $request->dis_count[$key];
                    $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                }
            }

            $data->totalamount_wo_discount = $request->total_amount_wo_discount[$key];
            $data->price_wo_discount = $request->price_withoutvat_wo_discount[$key];

            $data->total_discount_type = $request->total_discount;
            $data->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100);
            $data->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100));

            $data->bill_grand_total = $request->bill_grand_total;
            $data->bill_grand_total_wo_discount = $request->bill_grand_total_wo_discount;
            $data->amount = $request->advance;

            $data->save();
        }
        session()->flash('success', 'Invoice Successfully Drafted');
        return redirect('/dashboard');
    }

    public function editdraft($page, $transaction_id, UserService $userService, EditTransactionService $edittransService)
    {
        $userid = Session('softwareuser');
        $adminid = $userService->getAdminId($userid);
        $useritem = $userService->getUserDetails($userid);
        $branch = Softwareuser::locationById($userid);

        $items = $edittransService->getItems($branch);

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        $dataplan = DB::table('billdraft')
            ->join('products', 'billdraft.product_id', '=', 'products.id')
            ->where('billdraft.transaction_id', $transaction_id)
            ->select([
                'billdraft.*',
                'products.id as product_id',
                'products.product_name',
                'products.status',
            ])
            ->get();

        $billdraft = DB::table('billdraft')
        ->select('employee_id','employee_name','bank_id','account_name','customer_name', 'phone', 'email', 'trn_number', 'credit_user_id', 'cash_user_id', 'vat_type', 'payment_type', 'total_discount_type', 'total_discount_percent', 'total_discount_amount', 'amount')
        ->where('transaction_id', $transaction_id)
            ->first();

        $customer_name = $billdraft->customer_name;
        $phone = $billdraft->phone;
        $email = $billdraft->email;
        $trn_number = $billdraft->trn_number;
        $credit_user_id = $billdraft->credit_user_id;
        $cash_user_id = $billdraft->cash_user_id;
        $vattype = $billdraft->vat_type;
        $payment_type = $billdraft->payment_type;
        $total_discount_type = $billdraft->total_discount_type;
        $total_discount_percent = $billdraft->total_discount_percent;
        $total_discount_amount = $billdraft->total_discount_amount;
        $advance = $billdraft->amount;
        $bank_id = $billdraft->bank_id;
        $account_name = $billdraft->account_name;
        $employee_id = $billdraft->employee_id;
        $employee_name = $billdraft->employee_name;


        $current_balance = DB::table('bank')
        ->where('id',$bank_id)
        ->value('current_balance');

        $current_lamount= DB::table('creditusers')
        ->where('status', 1)
        ->where('id', $credit_user_id)
        ->value('current_lamount');

        $advance_balance = DB::table('creditsummaries')
        ->where('credituser_id', $credit_user_id)
        ->selectRaw('CASE
                        WHEN COALESCE(collected_amount, 0) > COALESCE(due_amount, 0)
                        THEN COALESCE(collected_amount, 0) - COALESCE(due_amount, 0)
                        ELSE NULL
                     END AS advance_balance')
        ->first();


          $shopdata = Branch::Where('id', $branch)->get();



        $credit_user_name = Credituser::Where('admin_id', $adminid)
            ->where('id', $credit_user_id)
            ->where('status', 1)
            ->where('location', $branch)
            ->pluck('name')
            ->first();



        $data = [
            'details' => $dataplan,
            'items' => $items,
            'transaction_id' => $transaction_id,
            'payment_type' => $payment_type,
            'customer_name' => $customer_name,
            'trn_number' => $trn_number,
            'phone' => $phone,
            'email' => $email,
            'vattype' => $vattype,
            'credit_user_id' => $credit_user_id,
            'cash_user_id' => $cash_user_id,
            'credit_user_name' => $credit_user_name,
            'total_discount_type' => $total_discount_type,
            'total_discount_percent' => $total_discount_percent,
            'total_discount_amount' => $total_discount_amount,
            'users' => $useritem,
            'currency' => $currency,
            'shopdatas' => $shopdata,
            'advance' => $advance,
            'page' => $page,
            'tax'=>$tax,
            'bank_id' => $bank_id,
            'account_name' => $account_name,
            'current_balance' => $current_balance,
            'employee_name' => $employee_name,
            'employee_id' => $employee_id,
            'current_lamount'=>$current_lamount,
            'advance_balance'=>$advance_balance,
            'branch'=>$branch,

        ];
        // return view('billingdesk.editdraft', $data);

        return view('/billingdesk/edit_bill', $data);
    }

    public function draftproductsubmit(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $product_code = rand(100000000, 200000000);
        $errorMessages = [];

        $count = DB::table('productdraft')
            ->distinct()
            ->count('draft_id');
        ++$count;

        $draft_id = 'Draft'.$count;

        if (!empty($request->productName)) {
            foreach ($request->productName as $key => $productName) {
                // $existingProduct = ProductDraft::where('product_name', $productName)
                //     ->where('branch', $branch)
                //     ->first();

                // if (!$existingProduct) {

                // Check if the product exists in the products table
                $existingProductInProducts = DB::table('products')
                    ->where('product_name', $productName)
                    ->where('branch', $branch)
                    ->first();

                // Check if the product exists in the drafts table if it's not in the products table
                $existingProductInDrafts = null;
                if (!$existingProductInProducts) {
                    $existingProductInDrafts = ProductDraft::where('product_name', $productName)
                        ->where('branch', $branch)
                        ->first();
                }

                if (!$existingProductInProducts && !$existingProductInDrafts) {
                    $data = new ProductDraft();
                    $data->product_name = $productName;
                    // $data->productdetails = $request->productdetails[$key];
                    $data->unit = $request->unit[$key];
                    $data->selling_cost = $request->selling_cost[$key];
                    $data->buy_cost = $request->buy_cost[$key];
                    $data->user_id = Session('softwareuser');
                    $data->branch = $branch;
                    $data->category_id = $request->category_id[$key];
                    $data->vat = $request->vat[$key];
                    $data->draft_id = $draft_id; // Use the single generated draft_id
                    $data->rate = $request->rate[$key];
                    $data->purchase_vat = $request->purchase_vat[$key];
                    $data->inclusive_rate = $request->inclusive_rate[$key];
                    $data->inclusive_vat_amount = $request->inclusive_vat_amount[$key];

                    if (!empty($request->exist_barcode[$key])) {
                        $data->product_code = $request->exist_barcode[$key];
                        $data->barcode = $request->exist_barcode[$key];
                    } else {
                        $data->product_code = $product_code;
                        $data->barcode = $product_code;
                        ++$product_code;
                    }

                    if (!isset($request->image[$key]) || $request->image[$key] == '' || is_null($request->image[$key])) {
                        $data->save();
                    } else {
                        $image = $request->image[$key];
                        $ext = $image->getClientOriginalExtension();
                        $name = 'PRODUCT'.date('d-m-y_h-i-s').'.'.$ext;
                        $data->image = $name;
                        $path = $image->storeAs('public/productimages', $name);
                        $timeInSeconds = 1;
                        sleep($timeInSeconds);
                        $data->save();
                    }
                } else {
                    // $errorMessages[] = "Product '$productName' already exists for the given branch.";

                    if ($existingProductInProducts) {
                        $errorMessages[] = "Product '$productName' already exists in products for the given branch.";
                    } else {
                        $errorMessages[] = "Product '$productName' already exists in drafts for the given branch.";
                    }
                }
            }
        }

        if (!empty($request->pid)) {
            foreach ($request->pid as $key => $value) {
                $alreadyexistingProduct = ProductDraft::where('product_name', $request->pname[$key])
                    ->where('branch', $branch)
                    ->where('id', '<>', $value) // Exclude the current product being updated
                    ->first();
            }
        }

        if (!empty($errorMessages)) {
            // Redirect with all error messages
            return redirect('/inventorydashboard')->withErrors($errorMessages);
        }
        session()->flash('success', 'Product Successfully Drafted');
        return redirect('/inventorydashboard');
    }

    public function DraftToProduct($draft_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $branchid = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $item = DB::table('productdraft')
            ->leftJoin('categories', 'productdraft.category_id', '=', 'categories.id')
            ->where('productdraft.draft_id', $draft_id)
            ->where('branch', $branchid)
            ->select(DB::raw('productdraft.status,productdraft.product_name,productdraft.productdetails,productdraft.unit,productdraft.buy_cost,productdraft.image,productdraft.product_code,productdraft.barcode,productdraft.selling_cost,productdraft.id as id,productdraft.vat as vat,categories.category_name,categories.id as category_id, categories.access as access,productdraft.rate,productdraft.purchase_vat, productdraft.inclusive_rate, productdraft.inclusive_vat_amount, productdraft.inclusive_rate, productdraft.inclusive_vat_amount'))
            ->get();

        $shopid = Session('softwareuser');
        $adminid = Softwareuser::Where('id', $shopid)
            ->pluck('admin_id')
            ->first();
          $shopdata = Branch::Where('id', $branchid)->get();

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $xdetails = DB::table('categories')
            ->select(DB::raw('categories.category_name,categories.id as category_id,categories.access '))
            ->where('branch_id', $branch)
            ->where('access', 1)
            ->get();
        $xunit = DB::table('units')
            ->select(DB::raw('units.unit,units.id'))
            ->where('branch_id', $branchid)
            ->where('status', 1)
            ->get();

        $draft_id = $draft_id;

        $productdraft = DB::table('productdraft')
            ->select('product_name', 'productdetails', 'unit', 'buy_cost', 'purchase_vat', 'rate', 'inclusive_rate', 'inclusive_vat_amount', 'selling_cost', 'vat', 'category_id', 'barcode')
            ->where('draft_id', $draft_id)
            ->first();

        $product_name = $productdraft->product_name;
        $productdetails = $productdraft->productdetails;
        $unit = $productdraft->unit;
        $buy_cost = $productdraft->buy_cost;
        $purchase_vat = $productdraft->purchase_vat;
        $rate = $productdraft->rate;
        $inclusive_rate = $productdraft->inclusive_rate;
        $inclusive_vat_amount = $productdraft->inclusive_vat_amount;
        $selling_cost = $productdraft->selling_cost;
        $vat = $productdraft->vat;
        $category_id = $productdraft->category_id;
        $barcode = $productdraft->barcode;
        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
        $data = [
            'details' => $item,
            'xdetails' => $xdetails,
            'users' => $useritem,
            'shopdatas' => $shopdata,
            'xunit' => $xunit,
            'product_name' => $product_name,
            'productdetails' => $productdetails,
            'unit' => $unit,
            'buy_cost' => $buy_cost,
            'purchase_vat' => $purchase_vat,
            'rate' => $rate,
            'inclusive_rate' => $inclusive_rate,
            'inclusive_vat_amount' => $inclusive_vat_amount,
            'selling_cost' => $selling_cost,
            'vat' => $vat,
            'category_id' => $category_id,
            'draft_id' => $draft_id,
            'barcode' => $barcode,
            'tax'=>$tax,
        ];

        return view('inventory.editproduct', $data);
    }

    public function submitproductDataDraft(Request $request, $draft_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'category_id.*' => 'required|',
            'productName.*' => 'required',
            // 'productdetails.*' => "required",
            'unit.*' => 'required',
            'buy_cost.*' => 'required',
            'selling_cost.*' => 'required',
            'vat.*' => 'required',
        ]);
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $product_code = rand(100000000, 200000000);

        if (!empty($request->productName)) {
            foreach ($request->productName as $key => $productName) {
                $existingProduct = Product::where('product_name', $request->productName[$key])
                    ->where('branch', $branch)
                    ->where('id', '<>', $productName) // Exclude the current product being updated
                    ->first();

                if (!$existingProduct) {
                    $data = new Product();
                    $data->product_name = $productName;
                    // $data->productdetails = $request->productdetails[$key];
                    $data->unit = $request->unit[$key];
                    $data->selling_cost = $request->selling_cost[$key];
                    $data->buy_cost = $request->buy_cost[$key];
                    $data->user_id = Session('softwareuser');
                    $data->branch = $branch;
                    $data->category_id = $request->category_id[$key];
                    $data->vat = $request->vat[$key];

                    $data->rate = $request->rate[$key];
                    $data->purchase_vat = $request->purchase_vat[$key];

                    $data->inclusive_rate = $request->inclusive_rate[$key];
                    $data->inclusive_vat_amount = $request->inclusive_vat_amount[$key];

                    if (!empty($request->exist_barcode[$key])) {
                        $data->product_code = $request->exist_barcode[$key];
                        $data->barcode = $request->exist_barcode[$key];
                    } else {
                        $data->product_code = $product_code;
                        $data->barcode = $product_code;
                        ++$product_code;
                    }

                    if (!isset($request->image[$key]) || $request->image[$key] == '' || is_null($request->image[$key])) {
                        $data->save();
                    } else {
                        $image = $request->image[$key];
                        $ext = $image->getClientOriginalExtension();
                        $name = 'PRODUCT'.date('d-m-y_h-i-s').'.'.$ext;
                        $data->image = $name;
                        $path = $image->storeAs('public/productimages', $name);
                        $timeInSeconds = 1;
                        sleep($timeInSeconds);
                        $data->save();
                    }
                } else {
                    // Flash an error message
                    // return redirect('/inventorydashboard')->withErrors("Product '$productName' already exists for the given branch.");

                    // Collect the error message
                    $errorMessages[] = "Product '$productName' already exists for the given branch.";
                }
            }
        }

        if (!empty($errorMessages)) {
            return redirect('/inventorydashboard')->withErrors($errorMessages);
        }

        /* ------------------GET IP ADDRESS--------------------------------------- */

        $userid = Session('softwareuser');
        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'websoftware';
        $message = $username.' added or edited products';

        $locationdata = (new otherService())->get_location($ip);

        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        /* ----------------------------------------------------------------------- */
         DB::table('productdraft')
        ->where('draft_id', $draft_id)
        ->update(['branch' => null]);

        return redirect('/inventorydashboard');
    }

    public function deleteid($id)
    {
        $productname = ProductDraft::where('id', $id)->pluck('product_name')->first();

          $plan=ProductDraft::where('id', $id)
        ->update(['branch' => null]);

        return redirect()->back()->with('success', 'Product deleted successfully.');
    }

    public function draftpurchasesubmit(Request $req)
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $req->validate([
            'reciept_no' => 'required|unique:purchasedraft,reciept_no',
        ]);

        $boxDozens = $req->input('boxdozen');
        $boxCounts = $req->input('boxCount');
        $boxItems = $req->input('boxItem');
        $dozenCounts = $req->input('dozenCount');
        $dozenItems = $req->input('dozenItem');
        $units = $req->input('unit');
        $prices = $req->input('total');
        $discountAmount = $req->discount_amount; // Total discount amount
        $totalPrice = array_sum($prices);
        $discounts = [];
        $buycosts = $req->input('buy_cost');
        $sellcosts = $req->input('sell_cost');
        $rates = $req->input('rate_r');
        $vats = $req->input('vat_r');
        $priceswithoutvat = $req->input('without_vat');
        $suppliertId = $req->input('supp_id');

        if (($req->payment_mode == 1) || ($req->payment_mode == 2)|| ($req->payment_mode == 3)) {
            $count = DB::table('stock_purchase_reports')
                ->distinct()
                ->count('purchase_trans_id');

            ++$count;
            $purchase_trans_id = 'PID';

            $i = 1;
            $pricetotals = 0;


            foreach ($req->input('product_id') as $key => $productID) {
                $pricetotals += $prices[$key];
                $stock = new PurchaseDraft();
                $stock->reciept_no = $req->input('reciept_no');
                $stock->comment = $req->input('comment');
                $stock->supplier = $req->input('supplier');

                // $stock->supplier_id = $req->input('supp_id');

                if ($req->input('supp_id') != '' || $req->input('supp_id') != null) {
                    $stock->supplier_id = $req->input('supp_id');
                } elseif ($req->input('supp_id') == '' || $req->input('supp_id') == null) {
                    $stock->supplier_id = $suppliertId;
                }

                $stock->payment_mode = $req->input('payment_mode');
                $stock->user_id = Session('softwareuser');
                // $stock->price = $req->price;
                $stock->branch = $branch;

                $stock->product = $productID;
                $stock->is_box_or_dozen = $boxDozens[$key];
                $stock->unit = $units[$key];
                $stock->price = $prices[$key];
                $stock->buycost = $buycosts[$key];
                $stock->sellingcost = $sellcosts[$key];
                $stock->price_without_vat = $priceswithoutvat[$key];
                $stock->bank_id = $req->bank_name;
                $stock->account_name = $req->account_name;
                $discounts[$key] = ($discountAmount * $prices[$key]) / $totalPrice;
                $stock->discount = $discounts[$key];
                $stock->rate = $rates[$key];
                $stock->vat = $vats[$key];
                 $method = trim(strtolower($req->input('method'))); // Normalize input
                $stock->method = ($method === 'service') ? 2 : 1;

                if ($boxDozens[$key] == 1) {
                    $stock->box_dozen_count = $boxCounts[$key];
                    $stock->quantity = $boxItems[$key];
                    $stock->remain_stock_quantity = $boxItems[$key];
                } elseif ($boxDozens[$key] == 2) {
                    $stock->box_dozen_count = $dozenCounts[$key];
                    $stock->quantity = $dozenItems[$key];
                    $stock->remain_stock_quantity = $dozenItems[$key];
                } elseif ($boxDozens[$key] == 3) {
                    $stock->quantity = $boxItems[$key];
                    $stock->remain_stock_quantity = $boxItems[$key];
                }

                if (!empty($req->file('camera'))) {
                    $ext = $req->file('camera')->getClientOriginalExtension();
                    $stock->file = 'STOCK_DAT'.date('d-m-y_h-i-s').'.'.$ext;
                    $stock->save();
                    $path = $req->file('camera')->storeAs('stockbills', $stock->file);

                    $stockId = $stock->id;
                } else {
                    $stock->save();

                    $stockId = $stock->id;
                }
            }
        }
        session()->flash('success', 'Purchase Successfully Drafted');
        return redirect()->back();
    }

    public function editpurchasedraft($page, $reciept_no, EditPurchaseService $editpurchaseService)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopid = Session('softwareuser');
        $adminid = Softwareuser::Where('id', $shopid)
            ->pluck('admin_id')
            ->first();

        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
          $shopdata = Branch::Where('id', $branch)->get();


        if ($page == 'edit_purchase_draft') {
            $products = DB::table('products')
                ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
                ->select(DB::raw('products.product_name as product_name,products.id as id, products.unit as unit,products.buy_cost as buy_cost, products.selling_cost as selling_cost,products.rate as rate,products.purchase_vat as purchase_vat'))
                ->groupBy('products.id')
                ->where('products.branch', $branch)
                ->where('products.status', 1)
                ->orderBy('products.id')
                ->get();

            $details = DB::table('purchasedraft')
                ->leftJoin('products', 'purchasedraft.product', '=', 'products.id')

                ->select(DB::raw('purchasedraft.*, products.product_name,products.status'))
                ->where('purchasedraft.reciept_no', $reciept_no)
                ->where('purchasedraft.branch', $branch)
                ->get();

            $payment_type = DB::table('purchasedraft')
                ->where('reciept_no', $reciept_no)
                ->pluck('payment_mode')
                ->first();

            $comment = DB::table('purchasedraft')
                ->where('reciept_no', $reciept_no)
                ->pluck('comment')
                ->first();
            $price = DB::table('purchasedraft')
                ->where('reciept_no', $reciept_no)
                ->pluck('price')
                ->first();
            $supplier = DB::table('purchasedraft')
                ->where('reciept_no', $reciept_no)
                ->pluck('supplier')
                ->first();

            $supp_id = DB::table('purchasedraft')
                ->where('reciept_no', $reciept_no)
                ->pluck('supplier_id')
                ->first();

            $unit = DB::table('purchasedraft')
                ->where('reciept_no', $reciept_no)
                ->pluck('unit')
                ->first();

            $datas = DB::table('purchasedraft')
                ->select(DB::raw('is_box_or_dozen, box_dozen_count'))
                ->where('reciept_no', $reciept_no)
                ->first();

            $quan = DB::table('purchasedraft')
                ->where('reciept_no', $reciept_no)
                ->pluck('quantity')
                ->first();
                $bank_id = DB::table('purchasedraft')
                ->where('reciept_no', $reciept_no)
                ->pluck('bank_id')
                ->first();

                $account_name = DB::table('purchasedraft')
                ->where('reciept_no', $reciept_no)
                ->pluck('account_name')
                ->first();
                $discount = PurchaseDraft::Where('reciept_no', $reciept_no)
                ->pluck('discount')
                ->sum();

                $current_balance = DB::table('bank')
                ->where('id',$bank_id)
                ->value('current_balance');
            $quantity = number_format($quan);

            $suppliers = DB::table('suppliers')
                ->where('location', $branch)
                ->get();
                  $method = PurchaseDraft::Where('reciept_no', $reciept_no)
                ->pluck('method')
                ->first();

            $invoice_date = $editpurchaseService->getInvoiceDate($reciept_no, $branch);

            $receipt_no = $reciept_no;

            $data = [
                'page' => $page,
                'details' => $details,
                'products' => $products,
                'users' => $useritem,
                'receipt_no' => $receipt_no,
                'comment' => $comment,
                'price' => $price,
                'supplier' => $supplier,
                'shopdatas' => $shopdata,
                'unit' => $unit,
                'datas' => $datas,
                'quantity' => $quantity,
                'suppliers' => $suppliers,
                'payment_type' => $payment_type,
                'supplier_id' => $supp_id,
                'invoice_date' => $invoice_date,
                'tax'=>$tax,
                'bank_id' => $bank_id,
                'account_name' => $account_name,
                'current_balance'=>$current_balance,
                'discount'=>$discount,
                'method'=>$method

            ];
        }

        return view('inventory/edit_purchase', $data);
    }

    public function draftsalessubmit(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'productName' => 'required',
        ]);

        $userid = Session('softwareuser');

        $branch = Softwareuser::locationById($userid);

        $admin_id = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('admin_id')
            ->first();

        $transdefault = DB::table('adminusers')
            ->where('id', $admin_id)
            ->pluck('transpart')
            ->first();

        // Generate transaction ID
        $count = DB::table('sales_orders_draft')->distinct()->count('transaction_id');
        ++$count;

        $text = ($request->vat_type_value == 1) ? 'IN' : 'EX';

        $transaction_id = $transdefault.'SLS'.$count.$text;

        // Loop through products and save data
        foreach ($request->product_id as $key => $productId) {
            $data = new SalesOrderDraft();
            $data->product_name = $request->productName[$key];
            $data->quantity = $request->quantity[$key];
            $data->remain_quantity = $request->quantity[$key];
            $data->unit = $request->prounit[$key];
            $data->product_id = $productId;
            $data->transaction_id = $transaction_id;
            $data->customer_name = $request->customer_name;
            $data->email = $request->email;
            $data->trn_number = $request->trn_number;
            $data->phone = $request->phone;
            $data->price = $request->price[$key];
            $data->total_amount = $request->total_amount[$key];
            $data->user_id = $userid;
            $data->branch = $branch;
            $data->one_pro_buycost = $request->buy_cost[$key];
            $data->mrp = $request->mrp[$key];
            $data->fixed_vat = $request->fixed_vat[$key];
            $data->bank_id = $request->bank_name;
            $data->account_name = $request->account_name;
            $data->employee_id = $request->employee_id;
            $data->employee_name = $request->employee_name;

            if ($request->payment_type == 3) {
                $data->credit_user_id = $request->credit_id;
            } elseif ($request->payment_type == 1 || $request->payment_type == 2 || $request->payment_type == 4) {
                $data->cash_user_id = $request->credit_id;
            }

            $data->vat_amount = $request->vat_amount[$key];
            $data->vat_type = $request->vat_type_value;
            $data->payment_type = $request->payment_type;

            $data->one_pro_buycost_rate = $request->buycost_rate[$key];
            $data->netrate = $request->net_rate[$key];

            $data->inclusive_rate = ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$key] : null;
            $data->exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

            $data->discount_type = $request->dis_count_type[$key];

            if ($request->vat_type_value == 1) {
                if ($request->dis_count_type[$key] == 'none') {
                    $data->discount = $request->dis_count[$key];
                } elseif ($request->dis_count_type[$key] == 'percentage') {
                    $data->discount = $request->dis_count[$key];
                    $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                } elseif ($request->dis_count_type[$key] == 'amount') {
                    $data->discount_amount = $request->dis_count[$key];
                    $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                }
            } elseif ($request->vat_type_value == 2) {
                if ($request->dis_count_type[$key] == 'none') {
                    $data->discount = $request->dis_count[$key];
                } elseif ($request->dis_count_type[$key] == 'percentage') {
                    $data->discount = $request->dis_count[$key];
                    $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                } elseif ($request->dis_count_type[$key] == 'amount') {
                    $data->discount_amount = $request->dis_count[$key];
                    $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                }
            }

            $data->totalamount_wo_discount = $request->total_amount_wo_discount[$key];
            $data->price_wo_discount = $request->price_withoutvat_wo_discount[$key];

            $data->total_discount_type = $request->total_discount;
            $data->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->sales_grand_total_wo_discount) * 100);
            $data->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->sales_grand_total_wo_discount * ($request->discount_percentage / 100));

            $data->bill_grand_total = $request->sales_grand_total;
            $data->bill_grand_total_wo_discount = $request->sales_grand_total_wo_discount;

            $data->save();
        }
        session()->flash('success', 'Sales Order Successfully Drafted');
        return redirect()->back();
    }

    public function salesorderdraft($page, $transaction_id, UserService $userService, salesorderService $sales_order_Service)
    {
        $userid = Session('softwareuser');
        $adminid = $userService->getAdminId($userid);
        $branch = Softwareuser::locationById($userid);
        $adminid = $userService->getAdminId($userid);
        $useritem = $userService->getUserDetails($userid);

          $shopdata = Branch::Where('id', $branch)->get();


        $items = $sales_order_Service->getProItems($branch);

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

        switch ($page) {
            case 'salesorderdraft':
                $table = 'sales_orders_draft';
                break;
            case 'quotationdraft':
                $table = 'quotations_draft';
                break;
            case 'performadraft':
                $table = 'performance_invoices_draft';
                break;
            case 'deliverydraft':
                $table = 'delivery_notes_draft';
                break;
            case 'quot_to_salesorder':
                $table = 'quotations';
                break;
            default:
                // Handle invalid page type (throw an exception or redirect with error message)
                return redirect()->back()->withErrors(['Invalid page type']);
        }
        $employee_id = DB::table($table)
        ->Where('transaction_id', $transaction_id)
        ->pluck('employee_id')
        ->first();
        $employee_name = DB::table($table)
        ->Where('transaction_id', $transaction_id)
        ->pluck('employee_name')
        ->first();
        $bank_id = DB::table($table)
        ->Where('transaction_id', $transaction_id)
        ->pluck('bank_id')
        ->first();
        $account_name = DB::table($table)
        ->Where('transaction_id', $transaction_id)
        ->pluck('account_name')
        ->first();
        $current_balance = DB::table('bank')
        ->where('id',$bank_id)
        ->value('current_balance');

        $customer_name = DB::table($table)
            ->Where('transaction_id', $transaction_id)
            ->pluck('customer_name')
            ->first();
        $phone = DB::table($table)
            ->Where('transaction_id', $transaction_id)
            ->pluck('phone')
            ->first();
        $email = DB::table($table)
            ->Where('transaction_id', $transaction_id)
            ->pluck('email')
            ->first();
        $trn_number = DB::table($table)
            ->Where('transaction_id', $transaction_id)
            ->pluck('trn_number')
            ->first();
        $credit_user_id = DB::table($table)
            ->Where('transaction_id', $transaction_id)
            ->pluck('credit_user_id')
            ->first();
        $cash_user_id = DB::table($table)
            ->Where('transaction_id', $transaction_id)
            ->pluck('cash_user_id')
            ->first();

        $vattype = DB::table($table)
            ->Where('transaction_id', $transaction_id)
            ->pluck('vat_type')
            ->first();

        $dataplan = DB::table($table)
            ->join('products', "{$table}.product_id", '=', 'products.id')
            ->where("{$table}.transaction_id", $transaction_id)
            ->select([
                "{$table}.*",
                'products.id as product_id',
                'products.product_name',
                'products.status',
            ])
            ->get();

        $payment_type = DB::table($table)
            ->Where('transaction_id', $transaction_id)
            ->pluck('payment_type')
            ->first();

        $total_discount_type = DB::table($table)
            ->Where('transaction_id', $transaction_id)
            ->pluck('total_discount_type')
            ->first();

        $total_discount_percent = DB::table($table)
            ->Where('transaction_id', $transaction_id)
            ->pluck('total_discount_percent')
            ->first();

        $total_discount_amount = DB::table($table)
            ->Where('transaction_id', $transaction_id)
            ->pluck('total_discount_amount')
            ->first();

        if (!($page == 'deliverydraft')) {
            $credit_user_name = Credituser::Where('id', $credit_user_id)
                ->pluck('name')
                ->first();
        }

        if ($page == 'deliverydraft') {
            $location_delivery = DB::table('delivery_notes_draft')
                ->Where('transaction_id', $transaction_id)
                ->pluck('location_delivery')
                ->first();
            $area = DB::table('delivery_notes_draft')
                ->Where('transaction_id', $transaction_id)
                ->pluck('area')
                ->first();
            $villa_no = DB::table('delivery_notes_draft')
                ->Where('transaction_id', $transaction_id)
                ->pluck('villa_no')
                ->first();
            $flat_no = DB::table('delivery_notes_draft')
                ->Where('transaction_id', $transaction_id)
                ->pluck('flat_no')
                ->first();
            $land_mark = DB::table('delivery_notes_draft')
                ->Where('transaction_id', $transaction_id)
                ->pluck('land_mark')
                ->first();
            $delivery_date = DB::table('delivery_notes_draft')
                ->Where('transaction_id', $transaction_id)
                ->pluck('delivery_date')
                ->first();

            $data_1 = [
                'delivery_date' => $delivery_date,
                'land_mark' => $land_mark,
                'flat_no' => $flat_no,
                'villa_no' => $villa_no,
                'area' => $area,
                'location_delivery' => $location_delivery,
            ];
        } else {
            $data_1 = [
                'credit_user_name' => $credit_user_name,
            ];
        }

        $data = [
            'details' => $dataplan,
            'email' => $email,
            'transaction_id' => $transaction_id,
            'payment_type' => $payment_type,
            'items' => $items,
            'customer_name' => $customer_name,
            'trn_number' => $trn_number,
            'phone' => $phone,
            'vattype' => $vattype,
            'credit_user_id' => $credit_user_id,
            'total_discount_type' => $total_discount_type,
            'total_discount_percent' => $total_discount_percent,
            'total_discount_amount' => $total_discount_amount,
            'cash_user_id' => $cash_user_id,
            'users' => $useritem,
            'currency' => $currency,
            'shopdatas' => $shopdata,
            'page' => $page,
            'tax'=>$tax,
            'bank_id' => $bank_id,
            'account_name' => $account_name,
            'current_balance' => $current_balance,
            'employee_id'=>$employee_id,
            'employee_name'=>$employee_name,
            'branch'=>$branch,

        ];

        return view('billingdesk.edit_bill', array_merge($data, $data_1));
    }

    public function draftquotationsubmit(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'productName' => 'required',
        ]);

        $userid = Session('softwareuser');

        $branch = Softwareuser::locationById($userid);

        $admin_id = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('admin_id')
            ->first();

        $transdefault = DB::table('adminusers')
            ->where('id', $admin_id)
            ->pluck('transpart')
            ->first();

        // Generate transaction ID
        $count = DB::table('quotations_draft')->distinct()->count('transaction_id');
        ++$count;

        $text = ($request->vat_type_value == 1) ? 'IN' : 'EX';

        $transaction_id = $transdefault.'QUOT'.$count.$text;

        // Loop through products and save data
        foreach ($request->product_id as $key => $productId) {
            $data = new QuotationDraft();
            $data->product_name = $request->productName[$key];
            $data->quantity = $request->quantity[$key];
            $data->remain_quantity = $request->quantity[$key];
            $data->unit = $request->prounit[$key];
            $data->product_id = $productId;
            $data->transaction_id = $transaction_id;
            $data->customer_name = $request->customer_name;
            $data->email = $request->email;
            $data->trn_number = $request->trn_number;
            $data->phone = $request->phone;
            $data->price = $request->price[$key];
            $data->total_amount = $request->total_amount[$key];
            $data->user_id = $userid;
            $data->branch = $branch;
            $data->one_pro_buycost = $request->buy_cost[$key];
            $data->mrp = $request->mrp[$key];
            $data->fixed_vat = $request->fixed_vat[$key];
            $data->bank_id = $request->bank_name;
            $data->account_name = $request->account_name;
            $data->employee_id = $request->employee_id;
            $data->employee_name = $request->employee_name;

            if ($request->payment_type == 3) {
                $data->credit_user_id = $request->credit_id;
            } elseif ($request->payment_type == 1 || $request->payment_type == 2 || $request->payment_type == 4) {
                $data->cash_user_id = $request->credit_id;
            }

            $data->vat_amount = $request->vat_amount[$key];
            $data->vat_type = $request->vat_type_value;
            $data->payment_type = $request->payment_type;

            $data->one_pro_buycost_rate = $request->buycost_rate[$key];
            $data->netrate = $request->net_rate[$key];

            $data->inclusive_rate = ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$key] : null;
            $data->exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

            $data->discount_type = $request->dis_count_type[$key];

            if ($request->vat_type_value == 1) {
                if ($request->dis_count_type[$key] == 'none') {
                    $data->discount = $request->dis_count[$key];
                } elseif ($request->dis_count_type[$key] == 'percentage') {
                    $data->discount = $request->dis_count[$key];
                    $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                } elseif ($request->dis_count_type[$key] == 'amount') {
                    $data->discount_amount = $request->dis_count[$key];
                    $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                }
            } elseif ($request->vat_type_value == 2) {
                if ($request->dis_count_type[$key] == 'none') {
                    $data->discount = $request->dis_count[$key];
                } elseif ($request->dis_count_type[$key] == 'percentage') {
                    $data->discount = $request->dis_count[$key];
                    $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                } elseif ($request->dis_count_type[$key] == 'amount') {
                    $data->discount_amount = $request->dis_count[$key];
                    $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                }
            }

            $data->totalamount_wo_discount = $request->total_amount_wo_discount[$key];
            $data->price_wo_discount = $request->price_withoutvat_wo_discount[$key];

            $data->total_discount_type = $request->total_discount;
            $data->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->sales_grand_total_wo_discount) * 100);
            $data->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->sales_grand_total_wo_discount * ($request->discount_percentage / 100));

            $data->bill_grand_total = $request->sales_grand_total;
            $data->bill_grand_total_wo_discount = $request->sales_grand_total_wo_discount;

            $data->save();
        }
        session()->flash('success', 'Quotation Successfully Drafted');
        return redirect()->back();
    }

    public function draftperformasubmit(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'productName' => 'required',
        ]);

        $userid = Session('softwareuser');

        $branch = Softwareuser::locationById($userid);

        $admin_id = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('admin_id')
            ->first();

        $transdefault = DB::table('adminusers')
            ->where('id', $admin_id)
            ->pluck('transpart')
            ->first();

        // Generate transaction ID
        $count = DB::table('performance_invoices_draft')->distinct()->count('transaction_id');
        ++$count;

        $text = ($request->vat_type_value == 1) ? 'IN' : 'EX';

        $transaction_id = $transdefault.'PI'.$count.$text;

        // Loop through products and save data
        foreach ($request->product_id as $key => $productId) {
            $data = new PerformanceInvoiceDraft();
            $data->product_name = $request->productName[$key];
            $data->quantity = $request->quantity[$key];
            $data->remain_quantity = $request->quantity[$key];
            $data->unit = $request->prounit[$key];
            $data->product_id = $productId;
            $data->transaction_id = $transaction_id;
            $data->customer_name = $request->customer_name;
            $data->email = $request->email;
            $data->trn_number = $request->trn_number;
            $data->phone = $request->phone;
            $data->price = $request->price[$key];
            $data->total_amount = $request->total_amount[$key];
            $data->user_id = $userid;
            $data->branch = $branch;
            $data->one_pro_buycost = $request->buy_cost[$key];
            $data->mrp = $request->mrp[$key];
            $data->fixed_vat = $request->fixed_vat[$key];
            $data->bank_id = $request->bank_name;
            $data->account_name = $request->account_name;
            $data->employee_id = $request->employee_id;
            $data->employee_name = $request->employee_name;

            if ($request->payment_type == 3) {
                $data->credit_user_id = $request->credit_id;
            } elseif ($request->payment_type == 1 || $request->payment_type == 2 || $request->payment_type == 4) {
                $data->cash_user_id = $request->credit_id;
            }

            $data->vat_amount = $request->vat_amount[$key];
            $data->vat_type = $request->vat_type_value;
            $data->payment_type = $request->payment_type;

            $data->one_pro_buycost_rate = $request->buycost_rate[$key];
            $data->netrate = $request->net_rate[$key];

            $data->inclusive_rate = ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$key] : null;
            $data->exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

            $data->discount_type = $request->dis_count_type[$key];

            if ($request->vat_type_value == 1) {
                if ($request->dis_count_type[$key] == 'none') {
                    $data->discount = $request->dis_count[$key];
                } elseif ($request->dis_count_type[$key] == 'percentage') {
                    $data->discount = $request->dis_count[$key];
                    $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                } elseif ($request->dis_count_type[$key] == 'amount') {
                    $data->discount_amount = $request->dis_count[$key];
                    $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                }
            } elseif ($request->vat_type_value == 2) {
                if ($request->dis_count_type[$key] == 'none') {
                    $data->discount = $request->dis_count[$key];
                } elseif ($request->dis_count_type[$key] == 'percentage') {
                    $data->discount = $request->dis_count[$key];
                    $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                } elseif ($request->dis_count_type[$key] == 'amount') {
                    $data->discount_amount = $request->dis_count[$key];
                    $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                }
            }

            $data->totalamount_wo_discount = $request->total_amount_wo_discount[$key];
            $data->price_wo_discount = $request->price_withoutvat_wo_discount[$key];

            $data->total_discount_type = $request->total_discount;
            $data->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->sales_grand_total_wo_discount) * 100);
            $data->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->sales_grand_total_wo_discount * ($request->discount_percentage / 100));

            $data->bill_grand_total = $request->sales_grand_total;
            $data->bill_grand_total_wo_discount = $request->sales_grand_total_wo_discount;

            $data->save();
        }
        session()->flash('success', 'Proforma Invoice Successfully Drafted');
        return redirect()->back();
    }

    public function draftdeliverysubmit(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $request->validate([
            'productName' => 'required',
        ]);
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $count = DB::table('delivery_notes_draft')
            ->distinct()
            ->count('transaction_id');

        ++$count;

        $admin_id = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('admin_id')
            ->first();

        $transdefault = DB::table('adminusers')
            ->where('id', $admin_id)
            ->pluck('transpart')
            ->first();

        $text = ($request->vat_type_value == 1) ? 'IN' : 'EX';

        $transaction_id = $transdefault.'DN'.$count.$text;

        foreach ($request->product_id as $key => $productID) {
            $data = new DeliveryNoteDraft();
            $data->product_name = $request->productName[$key];
            $data->quantity = $request->quantity[$key];
            $data->remain_quantity = $request->quantity[$key];
            $data->unit = $request->prounit[$key];
            $data->product_id = $productID;
            $data->transaction_id = $transaction_id;
            $data->customer_name = $request->customer_name;
            $data->email = $request->email;
            $data->trn_number = $request->trn_number;
            $data->phone = $request->phone;
            $data->price = $request->price[$key];
            $data->total_amount = $request->total_amount[$key];
            $data->payment_type = $request->payment_type;
            $data->user_id = Session('softwareuser');
            $data->branch = $branch;
            $data->one_pro_buycost = $request->buy_cost[$key];
            $data->mrp = $request->mrp[$key];
            $data->fixed_vat = $request->fixed_vat[$key];
            $data->bank_id = $request->bank_name;
            $data->account_name = $request->account_name;

            $data->credit_user_id = $request->credit_id;
            $data->vat_amount = $request->vat_amount[$key];
            $data->vat_type = $request->vat_type_value;

            $data->one_pro_buycost_rate = $request->buycost_rate[$key];
            $data->netrate = $request->net_rate[$key];

            $data->inclusive_rate = ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$key] : null;
            $data->exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

            $data->discount_type = $request->dis_count_type[$key];

            if ($request->vat_type_value == 1) {
                if ($request->dis_count_type[$key] == 'none') {
                    $data->discount = $request->dis_count[$key];
                } elseif ($request->dis_count_type[$key] == 'percentage') {
                    $data->discount = $request->dis_count[$key];
                    $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                } elseif ($request->dis_count_type[$key] == 'amount') {
                    $data->discount_amount = $request->dis_count[$key];
                    $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                }
            } elseif ($request->vat_type_value == 2) {
                if ($request->dis_count_type[$key] == 'none') {
                    $data->discount = $request->dis_count[$key];
                } elseif ($request->dis_count_type[$key] == 'percentage') {
                    $data->discount = $request->dis_count[$key];
                    $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                } elseif ($request->dis_count_type[$key] == 'amount') {
                    $data->discount_amount = $request->dis_count[$key];
                    $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                }
            }

            $data->totalamount_wo_discount = $request->total_amount_wo_discount[$key];
            $data->price_wo_discount = $request->price_withoutvat_wo_discount[$key];

            $data->total_discount_type = $request->total_discount;
            $data->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->delivery_grand_total_wo_discount) * 100);
            $data->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->delivery_grand_total_wo_discount * ($request->discount_percentage / 100));

            $data->bill_grand_total = $request->delivery_grand_total;
            $data->bill_grand_total_wo_discount = $request->delivery_grand_total_wo_discount;

            $data->location_delivery = $request->location;
            $data->area = $request->area;
            $data->villa_no = $request->villa_no;
            $data->flat_no = $request->flat_no;
            $data->land_mark = $request->land_mark;
            $data->delivery_date = $request->delivery_date;
            $data->save();
        }
        session()->flash('success', 'Delivery Note Successfully Drafted');
        return redirect()->back();
    }

    // to purcahse page
    public function toPurchasePage($page, $receipt_no, UserService $userService, purchaseorderService $purchase_order_Service)
    {
        // Authentication check
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        // Fetch user details using the injected service
        $userId = session('softwareuser');
        $userItem = $userService->getUserDetails($userId);

        // Fetch branch details using the injected service
        $branch = Softwareuser::locationById($userId);

        $branchName = Branch::locationNameById($branch);

        $adminid = $userService->getAdminId($userId);

        // Fetch shop data using the injected service
          $shopdata = Branch::Where('id', $branch)->get();

         $tax = Adminuser::where('id', $adminid)
            ->pluck('tax')
            ->first();
        if ($page == 'purchase_order') {
            // // Fetch other data using the injected product service
            $dataplan = $purchase_order_Service->getDataPlan($receipt_no, $branch);

            $comment = $purchase_order_Service->getComment($receipt_no, $branch);
            $supplier = $purchase_order_Service->getSupplier($receipt_no, $branch);
            $supplier_id = $purchase_order_Service->getSupplierID($receipt_no, $branch);
            $payment_type = $purchase_order_Service->getPaymentType($receipt_no, $branch);
            $invoice_date = $purchase_order_Service->getDeliveryDate($receipt_no, $branch);
            $purchase_order_id = $purchase_order_Service->getPurchaseOrderID($receipt_no, $branch);
            $bank_id = $purchase_order_Service->getBankID($receipt_no, $branch);
            $account_name = $purchase_order_Service->getAccountName($receipt_no, $branch);

            $current_balance = DB::table('bank')
            ->where('id',$bank_id)
            ->value('current_balance');

            $discount='';
            $products = $purchase_order_Service->getProducts($branch);

            // Combine data
            $data = [
                'details' => $dataplan,
                'receipt_no' => $receipt_no,
                'comment' => $comment,
                'supplier' => $supplier,
                'supplier_id' => $supplier_id,
                'payment_type' => $payment_type,
                'invoice_date' => $invoice_date,
                'products' => $products,
                'purchase_order_id' => $purchase_order_id,
                'tax'=>$tax,
                'bank_id' => $bank_id,
                'account_name' => $account_name,
                'current_balance'=>$current_balance,
                'discount'=>$discount,
            ];
        }

        return view('/inventory/edit_purchase', array_merge(
            [
                'users' => $userItem,
                'branches' => $branchName,
                'shopdatas' => $shopdata,
                'page' => $page,
            ],
            $data
        ));
    }

    // credit note

    public function getTotalAmount($transactionId)
    {
        // Fetch the total amount for the selected transaction ID
        $totalAmount = DB::table('buyproducts')
            ->where('buyproducts.transaction_id', $transactionId)
            ->select(
                DB::raw('SUM(buyproducts.total_amount) as totalPAmount'),
                DB::raw('GROUP_CONCAT(buyproducts.total_amount SEPARATOR ",") as productPrices')
            )
            ->first();

        return response()->json(['totalPAmount' => $totalAmount->totalPAmount, 'productPrices' => $totalAmount->productPrices]);
    }

    // fetch products by transaction id
    public function getProductsByTransactionId($transactionId)
    {
        $products = DB::table('buyproducts')
            ->where('transaction_id', $transactionId)
            ->get();
            $invoiceDue = DB::table('credit_transactions')
            ->where('transaction_id', $transactionId)
            ->orderBy('id', 'desc') // Assuming 'id' is the primary key in the credit_transactions table
            ->value('balance_due');

            return response()->json([
                'products' => $products,
                'invoice_due' => $invoiceDue
            ]);
            }

    // for debite note
    public function getPurchaseTotalAmount($receipt_no)
    {
        // Fetch the total amount and products prices for the selected receipt number
        $data = DB::table('stockdetails')
            ->where('stockdetails.reciept_no', $receipt_no)
            ->select(
                DB::raw('SUM(stockdetails.price) as totalPAmount'),
                DB::raw('GROUP_CONCAT(stockdetails.price SEPARATOR ",") as productPrices')
            )
            ->first();

        return response()->json(['totalPAmount' => $data->totalPAmount, 'productPrices' => $data->productPrices]);
    }

    // fetch products by receipt No
    public function getProductsByReceiptNo($receipt_no)
    {
        $Reproducts = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->where('reciept_no', $receipt_no)
            ->get();

            $invoiceDue = DB::table('credit_supplier_transactions')
            ->where('reciept_no', $receipt_no)
            ->orderBy('id', 'desc') // Assuming 'id' is the primary key in the credit_transactions table
            ->value('balance_due');

        return response()->json(['re_products' => $Reproducts,
        'invoice_due' => $invoiceDue]);
        }

    // supplier cash transactions
    public function supplierCashTransactionHistory(Request $request, $id)
    {
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }

        // Validate and sanitize input
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = DB::table('cash_supplier_transactions')
            ->select(
                'cash_supplier_transactions.*',
                DB::raw('(SELECT created_at FROM stockdetails WHERE reciept_no COLLATE utf8mb4_general_ci = cash_supplier_transactions.reciept_no LIMIT 1) as receipt_date')
            )
            ->where('cash_supplier_transactions.cash_supplier_id', $id);

        // Apply date filters if provided
        if ($startDate && $endDate) {
            $query->whereBetween('cash_supplier_transactions.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }

        $purchasedata = $query->paginate(20);

        if (Session('softwareuser')) {
            $item = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', Session('softwareuser'))
                ->get();

            $userid = Session('softwareuser');
            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();

            $branch = Softwareuser::locationById($userid);
        } elseif (Session('adminuser')) {
            $item = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', Session('adminuser'))
                ->get();

            $adminid = Session('adminuser');
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $lastTransactionQuery = DB::table('cash_supplier_transactions')
            ->where('cash_supplier_id', $id)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        // Check if the user is a normal user and apply the branch filter
        if (session('user_role') == 'softwareuser') {
            $lastTransactionQuery->where('location', $branch);
        }

        $lastTransaction = $lastTransactionQuery->first();

        $updated_balance = $lastTransaction->updated_balance ?? 0;

        $data = [
            'users' => $item,
            'purchasedata' => $purchasedata,
            'updated_balance' => $updated_balance,
            'currency' => $currency,
            'cash_supplier_id' => $id,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        return view('/billingdesk/suppliercashdata', $data);
    }


    // income and expense..........................................

    public function incomeReport()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $date = Carbon::now()->format('Y-m-d H:i:s');


        $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();
        $expense_category = DB::table('account_type')
        ->select('category')
        ->where('categorytype','expense')
        ->where('branch', $branch)
        ->distinct()
        ->get();

        $income_category = DB::table('account_type')
        ->select('category')
        ->where('categorytype','income')
        ->where('branch', $branch)
        ->distinct()
        ->get();




        $branches = Accountantloc::leftJoin('branches', 'accountantlocs.location_id', '=', 'branches.id')
            ->where('accountantlocs.user_id', $userid)
            ->get();



        $listbank = DB::table('bank')
        ->select('id','bank_name', 'account_name','current_balance','status') // Include status if you need it
        ->where('status', 1)
        ->where('branch', $branch)
        ->get();

        return view('/accountant/income_and_expense', ['branches'=>$branches,'listbank'=>$listbank,'users' => $item, 'start_date' => $date, 'branch' => $branch, 'expense_category' => $expense_category, 'income_category' => $income_category]);
    }


    public function expensesubmit(Request $request)
    {
        // Check if the user is logged in
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        // Log request data for debugging
        \Log::info('Form Data:', $request->all());

        // Validate the incoming request data
        $request->validate([
            'expense_comment.*' => 'required|string',
            'expense_amount.*' => 'required|numeric',
            'income_comment.*' => 'required|string',
            'income_amount.*' => 'required|numeric',
        ]);

        $user_id = Session::get('softwareuser');
        $branch_id = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();
        // Handle Expenses
        if ($request->has('expense_comment')) {
            foreach ($request->expense_comment as $key => $comment) {
                $expense = new AccountExpense();

                $category = DB::table('account_type')
                    ->where('category', $comment)
                    ->first();

                if ($category) {
                    if ($category->type == 'direct') {
                        $expense->direct_expense = $comment;
                    } else if ($category->type == 'indirect') {
                        $expense->indirect_expense = $comment;
                    }
                }
                if ($category) {
                    if ($category->type == 'direct') {
                        $expense->expense_type = 1;
                    } else if ($category->type == 'indirect') {
                        $expense->expense_type = 2;
                    }
                }
                $expense->bank_id =$request->expense_bank_id[$key] ?? null;;
                $expense->account_name =$request->expense_account_name[$key] ?? null;;
                $expense->amount = $request->expense_amount[$key];
                $expense->details = $request->expense_details[$key];
                $expense->date = $request->expense_start_date[$key];
                $expense->branch = $branch_id;
                $expense->user_id = $user_id;

                if (!isset($request->expense_image[$key]) || $request->expense_image[$key] == '' || is_null($request->expense_image[$key])) {
                    $expense->save();
                } else {
                    $image = $request->expense_image[$key];
                    $ext = $image->getClientOriginalExtension();
                    $name = 'EXPENSE_'.date('d-m-y_h-i-s').'.'.$ext;
                    $expense->file = $name;
                    $path = $image->storeAs('monthlybills', $name);
                    $timeInSeconds = 1;
                    sleep($timeInSeconds);
                    $expense->save();
                }

                \Log::info('Saving Expense:', $expense->toArray());

                $expense->save();
                if ($request->expense_bank_id[$key] && $request->expense_account_name[$key]) {
                    $current_balance = DB::table('bank')
                        ->where('id', $request->expense_bank_id[$key])
                        ->where('account_name', $request->expense_account_name[$key])
                        ->pluck('current_balance')
                        ->first();

                        $new_balance = $current_balance - $request->expense_amount[$key];

                    DB::table('bank')
                        ->where('id', $request->expense_bank_id[$key])
                        ->where('account_name', $request->expense_account_name[$key])
                        ->update(['current_balance' => $new_balance]);

                    $bank_history = new Bankhistory();
                    $bank_history->user_id = $user_id;
                    $bank_history->bank_id = $request->expense_bank_id[$key];
                    $bank_history->account_name = $request->expense_account_name[$key];
                    $bank_history->branch = $branch_id;
                    $bank_history->detail = 'Expense';
                    $bank_history->dr_cr = 'Debit';
                    $bank_history->date = $request->expense_start_date[$key];
                    $bank_history->amount = $request->expense_amount[$key];
                    $bank_history->save();
                }
            }
        }

        if ($request->has('income_comment')) {
            foreach ($request->income_comment as $key => $comment) {
                $income = new AccountIndirectIncome(); // Assuming you have an AccountIndirectIncome model

                // Retrieve the category type from account_type table
                $category = DB::table('account_type')
                    ->where('category', $comment)
                    ->first();

                if ($category) {
                    if ($category->type == 'direct') {
                        $income->direct_income = $comment;
                    } else if ($category->type == 'indirect') {
                        $income->indirect_income = $comment;
                    }
                }
                if ($category) {
                    if ($category->type == 'direct') {
                        $income->income_type = 1;
                    } else if ($category->type == 'indirect') {
                        $income->income_type = 2;
                    }
                }
                $income->bank_id = $request->income_bank_id[$key] ?? null; // Use null coalescing
                $income->account_name = $request->income_account_name[$key] ?? null; // Correctly assign to income
                $income->amount = $request->income_amount[$key];
                $income->details = $request->income_details[$key];
                $income->date = $request->income_start_date[$key];
                $income->user_id = $user_id;
                $income->branch = $branch_id;


                if (!isset($request->income_image[$key]) || $request->income_image[$key] == '' || is_null($request->income_image[$key])) {
                    $income->save();
                } else {
                    $image = $request->income_image[$key];
                    $ext = $image->getClientOriginalExtension();
                    $name = 'INCOME_'.date('d-m-y_h-i-s').'.'.$ext;
                    $income->file = $name;
                    $path = $image->storeAs('monthlybills', $name);
                    $timeInSeconds = 1;
                    sleep($timeInSeconds);
                    $income->save();
                }

                \Log::info('Saving Income:', $income->toArray());

                $income->save();
                if ($request->income_bank_id[$key] && $request->income_account_name[$key]) {
                    $current_balance = DB::table('bank')
                        ->where('id', $request->income_bank_id[$key])
                        ->where('account_name', $request->income_account_name[$key])
                        ->pluck('current_balance')
                        ->first();

                        $new_balance = $current_balance + $request->income_amount[$key];



                    DB::table('bank')
                        ->where('id', $request->income_bank_id[$key])
                        ->where('account_name', $request->income_account_name[$key])
                        ->update(['current_balance' => $new_balance]);

                    $bank_history = new Bankhistory();
                    $bank_history->user_id = $user_id;
                    $bank_history->bank_id = $request->income_bank_id[$key];
                    $bank_history->account_name = $request->income_account_name[$key];
                    $bank_history->branch = $branch_id;
                    $bank_history->detail = 'Income';
                    $bank_history->dr_cr = 'Credit';
                    $bank_history->date = $request->income_start_date[$key];
                    $bank_history->amount = $request->income_amount[$key];
                    $bank_history->save();
                }
            }

        }

        // Log Activity
        $ip = request()->ip();
        $uri = request()->fullUrl();
        $username = Softwareuser::where('id', $user_id)->pluck('username')->first();
        $branchname = Branch::where('id', $branch_id)->pluck('branchname')->first();
        $user_type = 'websoftware';
        $message = "$username added company expenses and income for branch $branchname";
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = new activityService($user_id, $ip, $uri, $message, $user_type, $locationdata);
            $activityservice->ipaddress_store($branch_id);
        }

    session()->flash('success', 'Submitted successfully');
    return redirect()->back();
}

    public function submittype(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'newCategoryName' => 'required|string',
            'categoryType' => 'required|string',
        ]);
        $userid = Session('softwareuser');
        $branch = DB::table('softwareusers')
       ->where('id', $userid)
       ->pluck('location')
       ->first();

        // Create a new Accounttype instance and save it
        $type = new  Accounttype();
        $type->type = $request->input('type');
        $type->branch = $branch;
        $type->category = $request->input('newCategoryName');
        $type->categorytype = $request->input('categoryType');
        $type->save();

        // Return a JSON response with the new category data
        return response()->json([
            'success' => true,
            'category' => $type
        ]);
    }
    public function loginsubmit(Request $req)
    {
        if (Auth::guard('websoftware')->attempt([
            'username' => $req->username,
            'password' => $req->password,
            'admin_status' => '1'
        ])) {
            // Authentication passed...
            $id = Auth::guard('websoftware')->user()->id;

            $access = DB::table('softwareusers')
                ->where('id', $id)
                ->pluck('access')
                ->first();

            if ($access == "0") {
                return redirect("/");
            }

            $req->session()->put('softwareuser', $id);

            // Update user status
            DB::table('softwareusers')
                ->where('id', $id)
                ->update([
                    'status' => 1,
                    'last_login' => Carbon::now()->toDateTimeString(),
                    'login_ipaddress' => request()->ip()
                ]);

            // Manage opening and closing stock
            $date = Carbon::today()->format('Y-m-d');
            $closestock = DB::table('pand_l_s')
                ->orderBy('created_at', 'desc')
                ->pluck('closingstock')
                ->first();

            $rowExists = DB::table('pand_l_s')
                ->whereDate('created_at', '=', $date)
                ->exists();

            if (!$rowExists) {
                $newrow = new PandL();
                $newrow->openingstock = $closestock;
                $newrow->closingstock = $closestock;
                $newrow->save();
            }

            // Log user activity
            $ip = request()->ip();
            $uri = $req->fullUrl();
            $user_type = 'websoftware';
            $message = $req->username . " logged in";
            $locationdata = (new otherService())->get_location($ip);
            $branch_id = Softwareuser::where('id', $id)->pluck('location')->first();

            if ($locationdata != false) {
                $activityservice = (new activityService($id, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
            }

            // Retrieve user-specific data for dashboard
            $userid = Session('softwareuser');
            $item = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();

            $adminid = Softwareuser::where('id', $userid)->pluck('admin_id')->first();
            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();
                     $company = DB::table('branches')
                ->where('id', $branch)
                ->pluck('company')
                ->first();
                $mobile = DB::table('branches')
                ->where('id', $branch)
                ->pluck('mobile')
                ->first();
          $shopdata = Branch::Where('id', $branch)->get();
            $userdata = Softwareuser::where('id', $userid)->get();

            $currency = Adminuser::where('id', $adminid)
                ->pluck('currency')
                ->first();


            $customers = DB::table('buyproducts')
                ->pluck('customer_name')
                ->all();

            $today = Carbon::today()->format('Y-m-d');

            $todaysale = DB::table('buyproducts')
                ->select(DB::raw('SUM(DISTINCT COALESCE(bill_grand_total, 0)) as total_sales'))
                ->where('branch', $branch)
                ->whereDate('created_at', $today)
                ->groupBy('transaction_id')
                ->get()
                ->sum('total_sales');

            $todayreturn = Returnproduct::whereDate('created_at', $today)
                ->where('branch', $branch)
                ->select(DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as total_return'))
                ->groupBy('transaction_id')
                ->get()
                ->sum('total_return');

            $todaypayment = DB::table('supplier_fund_histories')
                ->whereDate('created_at', $today)
                ->where('branch', $branch)
                ->sum('collectedamount');

            $todayreceipt = DB::table('fundhistories')
                ->whereDate('created_at', $today)
                ->where('location', $branch)
                ->sum('amount');

            // Redirect to dashboard with user data
            return response()->json([

                'userdatas' => $userdata,
                'currency' => $currency,
                'todaysale' => $todaysale,
                'todayreturn' => $todayreturn,
                'todaypayment' => $todaypayment,
                'todayreceipt' => $todayreceipt,
                'company'=>$company,
                'mobile'=>$mobile,
            ]);
        } else {
            return redirect('userlogin');
        }
    }


     // Bank
     public function bank()
     {
         if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
         $userid = Session('softwareuser');
         $useritem = DB::table('softwareusers')
             ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
             ->where('user_id', $userid)
             ->get();

             $adminid = Softwareuser::Where('id', $userid)
             ->pluck('admin_id')
             ->first();
                 $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();
          $shopdata = Branch::Where('id', $branch)->get();

         $data = [
             'users' => $useritem,
             'shopdatas' => $shopdata,

         ];

         return view('/bankaccount/bank', $data);
     }

     public function banksubmit(Request $req)
     {
         // Validate the request data
         $validatedData = $req->validate([
             'accountName' => 'required|string|max:255',
             'accountNo' => 'required|string|max:255',
             'bankName' => 'required|string|max:255',
             'openingBalance' => 'required|numeric',
             // 'branchName' => 'nullable|string|max:255',
             'ifscCode' => 'nullable|numeric',
             'accountType' => 'nullable|string|in:savings,current,fixed',
             'accountHolderName' => 'nullable|string|max:255',
             'upiid' => 'nullable|numeric',
             'country' => 'nullable|string|max:255',
         ]);

         // Map the type to 1 or 2

         // Map account type to corresponding integer value
         $accountTypeMapping = [
             'savings' => 1,
             'current' => 2,
             'fixed' => 3,
         ];
         $accountTypeValue = isset($accountTypeMapping[$req->input('accountType')]) ? $accountTypeMapping[$req->input('accountType')] : null;
         // $branch = DB::table('softwareusers')
         // ->where('id', Session('softwareuser'))
         // ->pluck('location')
         // ->first();
         $userid = Session('softwareuser');
         $branch = Softwareuser::locationById($userid);

         // Create a new bank instance
         $bank = new bank();
         $bank->account_name = $req->input('accountName');
         $bank->account_no = $req->input('accountNo');
         $bank->user_id = Session('softwareuser');
         $bank->branch = $branch;
         $bank->opening_balance = $req->input('openingBalance');
         $bank->current_balance = $req->input('openingBalance'); // Set current balance equal to opening balance
         $bank->bank_name = $req->input('bankName');
         $bank->branch_name = $req->input('branchName');
         $bank->ifsc_code = $req->input('ifscCode');
         $bank->iban_code = $req->input('ibanCode');
         $bank->account_type = $accountTypeValue;
         $bank->upi_id = $req->input('upiid');
         $bank->country = $req->input('country');
         $bank->status = 1; // Enable the bank by default
         $bank->date = $req->input('date');
  // Check if the 'Set as Default Bank' checkbox was selected
  if ($req->has('defaultBank')) {
    $bank->is_default = 1; // Set the current bank as default
} else {
    $bank->is_default = 0; // Default bank is not selected
}

         // Save the new bank record
         $bank->save();

         //  // Insert a record into the bank_history table
         //  DB::table('bank_history')->insert([
         //     'bank_name' => $req->input('bankName'),
         //     'account_name' => $req->input('accountName'),
         //     'user_id' => Session('softwareuser'),
         //     'branch' => $branch,
         //     'amount' => $req->input('openingBalance'),
         //     'dr_cr' => 'Opening_stock', // Store as 'Credit'
         //     'detail' =>'Opening Balance',
         //     'date' => now(), // Use the current date and time
         // ]);
        //  if ($req->has('defaultBank')) {
        //     DB::table('bank')
        //         ->where('id', '!=', $bank->id)
        //         ->update(['is_default' => 0]); // Update other records to remove default
        // }
         // Redirect back to the form with a success message
         session()->flash('success', 'Bank added successfully');

         return redirect()->back();
     }

     public function list(Request $request)
     {
         if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
         $userid = Session('softwareuser');
         $useritem = DB::table('softwareusers')
             ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
             ->where('user_id', $userid)
             ->get();

         $branch = DB::table('softwareusers')
         ->where('id', Session('softwareuser'))
         ->pluck('location')
         ->first();

         $banks = Bank::where('branch', $branch)
         ->orderBy('created_at', 'DESC')
         ->get();
         $adminid = Softwareuser::Where('id', $userid)
         ->pluck('admin_id')
         ->first();
          $shopdata = Branch::Where('id', $branch)->get();

         $data = [
             'users' => $useritem,
             'banks' => $banks,
             'shopdatas' => $shopdata,

         ];

         return view('/bankaccount/listbank', $data);
     }

     public function editbank($id)
     {
         if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
         $editbank = bank::find($id);

         // Redirect back if the bank is disabled
         if ($editbank->status == 0) {
             return redirect('/listbank')->with('error', 'Cannot edit disabled bank.');
         }

         $userid = Session('softwareuser');
         $useritem = DB::table('softwareusers')
             ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
             ->where('user_id', $userid)
             ->get();
               $adminid = Softwareuser::Where('id', $userid)
             ->pluck('admin_id')
             ->first();
                      $branch = DB::table('softwareusers')
         ->where('id', Session('softwareuser'))
         ->pluck('location')
         ->first();
          $shopdata = Branch::Where('id', $branch)->get();

             $data = [
                 'users' => $useritem,
                 'id' => $id,
                 'editbank' => $editbank,
                 'shopdatas'=>$shopdata,
             ];

         return view('/bankaccount/editbank', $data);
     }

     public function updatebank(Request $req, $id)
     {
         // Find the bank record by its ID
         $bank = bank::find($id);

         // Validate the request
         $validatedData = $req->validate([
             'accountName' => 'required|string|max:255',
             'accountNo' => 'required|string|max:255',
             'bankName' => 'required|string|max:255',
             'openingBalance' => 'required|numeric',
             // 'type' => 'required|string|in:credit,debit',
             // 'enterAmount' => 'required|numeric',
             'branchName' => 'nullable|string|max:255',
             'ifscCode' => 'nullable|string|max:255',
             'accountType' => 'nullable|string|in:savings,current,fixed',
             'country' => 'nullable|string|max:255',
             'upiid' => 'nullable|string|max:255',
             'accountHolderName' => 'nullable|string|max:255',
         ]);

         // Retrieve the enter amount and type from the request
         // $enterAmount = (float) $req->input('enterAmount');
         // $type = $req->input('type');

         // // Calculate the new current balance and set the edit amount with sign
         // if ($type === 'credit') {
         //     $newBalance = $bank->current_balance + $enterAmount;
         //     $editAmountEntry = '+'.$enterAmount; // positive value with sign for credit
         // } elseif ($type === 'debit') {
         //     $newBalance = $bank->current_balance - $enterAmount;
         //     $editAmountEntry = '-'.$enterAmount; // negative value with sign for debit
         // } else {
         //     return redirect()->back()->with('error', 'Invalid transaction type.');
         // }
         // Map the account type to an integer value if needed
         $accountTypeValue = null;
         if ($req->input('accountType')) {
             switch ($req->input('accountType')) {
                 case 'savings':
                     $accountTypeValue = 1;
                     break;
                 case 'current':
                     $accountTypeValue = 2;
                     break;
                 case 'fixed':
                     $accountTypeValue = 3;
                     break;
             }
         }
         // Update the bank record with new values
        $newBalance=$req->input('openingBalance')+$req->input('add_amount');
         $bank->update([
             'account_name' => $req->input('accountName'),
             'account_no' => $req->input('accountNo'),
             'opening_balance' => $req->input('currentbalance'), // Update opening balance if needed
             'current_balance' => $newBalance,
             'bank_name' => $req->input('bankName'),
             // 'type' => $type === 'credit' ? 1 : 2, // Store the type as 1 or 2
             // 'edit_amount' => $editAmountEntry, // Update the edit_amount with sign
             'branch_name' => $req->input('branchName'),
             'ifsc_code' => $req->input('ifscCode'),
             'iban_code' => $req->input('ibanCode'),
             'account_type' => $accountTypeValue, // Storing the mapped value
             'country' => $req->input('country'),
             'upi_id' => $req->input('upiid'),
         ]);


         $branch = DB::table('softwareusers')
         ->where('id', Session('softwareuser'))
         ->pluck('location')
         ->first();

            $bank_history = new Bankhistory();
            $bank_history->user_id = Session('softwareuser');
            $bank_history->branch = $branch;
            $bank_history->detail = 'Amount Added';
            $bank_history->dr_cr = 'Credit';
            $bank_history->bank_id = $id;
            $bank_history->account_name = $req->accountName;
            $bank_history->amount = $req->add_amount;
            $bank_history->date = $req->depositing_date; // Store the current date and time
            $bank_history->save();

         // Redirect to the bank list with a success message
         return redirect('/listbank')->with('success', 'Bank details updated successfully!');
     }

     public function toggleBankStatus($id)
     {
         $bank = bank::find($id);
         if ($bank) {
             $bank->status = !$bank->status;
             $bank->save();
         }

         return redirect()->back()->with('status', 'Bank status updated successfully!');
     }

     public function fundtransfer()
     {
         if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
         $userid = Session('softwareuser');
         $useritem = DB::table('softwareusers')
             ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
             ->where('user_id', $userid)
             ->get();

             $branch = DB::table('softwareusers')
             ->where('id', $userid)
             ->pluck('location')
             ->first();
             $accounts = bank::select('account_name', 'current_balance', 'id')
             ->where('branch', $branch)
             ->where('status',1)
             ->whereIn('id', function ($query) {
                 $query->select(DB::raw('MAX(id)'))
                     ->from('bank')
                     ->groupBy('account_name');
             })
             ->get();

    // Define the branch based on the user session
    // Fetch debit types based on user_id and branch
    $debitTypes = TransferType::where('transfer_type', 2)
    ->where('user_id', $userid)    // Add this line to filter by user_id
    ->where('branch', $branch)      // Add this line to filter by branch
    ->pluck('transfer_name', 'id');

    // Fetch credit types based on user_id and branch
    $creditTypes = TransferType::where('transfer_type', 1)
    ->where('user_id', $userid)    // Add this line to filter by user_id
    ->where('branch', $branch)      // Add this line to filter by branch
    ->pluck('transfer_name', 'id');

         $supp = DB::table('suppliers')
         ->select('b_accountname', 'b_bankname')
         ->where('location',$branch)
         ->whereNotNull('b_accountname')
         ->get();

         $customer = DB::table('creditusers')
         ->select('b_accountname', 'b_bankname')
         ->where('location',$branch)
         ->whereNotNull('b_accountname')
         ->get();



       $currency = Adminuser::Where('id', $userid)
         ->pluck('currency')
         ->first();
         $adminid = Softwareuser::Where('id', $userid)
         ->pluck('admin_id')
         ->first();
          $shopdata = Branch::Where('id', $branch)->get();


         return view('bankaccount/fundtransfer', [
             'customer'=>$customer,
             'supp'=> $supp,
             'accounts' => $accounts,
             'userid' => $userid,
             'useritem' => $useritem,
             'debitTypes' => $debitTypes,
             'creditTypes' => $creditTypes,
             'users' => $useritem,
             'currency'=>$currency,
             'shopdatas'=>$shopdata,

         ]);
     }

     public function storetransfertype(Request $request)
     {
         $request->validate([
             'newOptionName' => 'required|string|max:255',
             'dropdownType' => 'required|string|in:debit,credit',
         ]);
         $userid = Session('softwareuser');
         $branch = DB::table('softwareusers')
        ->where('id', $userid)
        ->pluck('location')
        ->first();
         $transferType = $request->input('dropdownType') === 'credit' ? 1 : 2;

         $type = new TransferType();
         $type->transfer_type = $transferType;
         $type->transfer_name = $request->newOptionName;
         $type->branch = $branch;
         $type->user_id = $userid;
         $type->save();

       // Return the ID and name of the newly created transfer type
    return response()->json([
        'success' => true,
        'id' => $type->id, // Get the newly created ID
        'name' => $type->transfer_name // Get the name of the transfer type
    ]);
     }

     public function fundTransferSubmit(Request $request)
     {
         $request->validate([
             'account_name' => 'required|string|max:255',
             'date' => 'required|date',
             'debit_type' => 'nullable|int',
             'credit_type' => 'nullable|int',
             'amount' => 'required|numeric',
             'ref_no' => 'nullable|int',
             'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
             'remarks' => 'nullable|string|max:255',
         ]);

         $userid = Session('softwareuser');
         $useritem = DB::table('softwareusers')
             ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
             ->where('user_id', $userid)
             ->get();

         $branch = DB::table('softwareusers')
         ->where('id', Session('softwareuser'))
         ->pluck('location')
         ->first();

         if ($request->debit_type) {
             $transfer_type = 2;
             $transferNameRecord = DB::table('transfer_type')->where('id', $request->debit_type)->first();
             $transfer_name = $transferNameRecord->transfer_name;

             if ($request->input('supplierAccountName')) {
                 $detail = 'Debited to supplier ';
             } elseif ($request->input('customerAccountName')) {
                 $detail = 'Debited to customer ';
             } else {
                 $detail = 'Debit Transaction';
             }

             $drCr = 'Debit';
         } elseif ($request->credit_type) {
             $transfer_type = 1;
             $transferNameRecord = DB::table('transfer_type')->where('id', $request->credit_type)->first();
             $transfer_name = $transferNameRecord->transfer_name;

             if ($request->input('supplierAccountName')) {
                 $detail = 'Credited from supplier';
             } elseif ($request->input('customerAccountName')) {
                 $detail = 'Credited from customer ';
             } else {
                 $detail = 'Credit Transaction';
             }

             $drCr = 'Credit';
         } else {
             return back()->withErrors(['Please select a transaction type (debit or credit).']);
         }

         $image_name = null;
         if ($request->hasFile('receipt_image')) {
             $image = $request->file('receipt_image');
             $ext = $image->getClientOriginalExtension();
             $image_name = 'RECEIPT_'.date('d-m-y_h-i-s').'.'.$ext;
             $image->storeAs('uploads/receipts', $image_name);

             sleep(1);
         }

         $account = DB::table('bank')->where('id', $request->account_name)->first();
         $accountName = $account->account_name;
         $bankName = $account->id;



         $new_balance = $account->current_balance;
         if ($transfer_type == 2) {
             $new_balance -= $request->amount;
         } elseif ($transfer_type == 1) {
             $new_balance += $request->amount;
         }

         DB::table('bank')
             ->where('id', $account->id)
             ->update(['current_balance' => $new_balance]);

         $bankTransfer = new BankTransfer();
         $bankTransfer->account_name = $accountName;
         $bankTransfer->bank_id = $bankName;
         $bankTransfer->transfer_type = $transfer_type;
         $bankTransfer->transfer_name = $transfer_name;
         $bankTransfer->transfer_amount = $request->amount;
         $bankTransfer->ref_no = $request->ref_no;
         $bankTransfer->date = $request->date;
         $bankTransfer->image = $image_name;
         $bankTransfer->remark = $request->remarks;
         $bankTransfer->s_accountname = $request->input('supplierAccountName');
         $bankTransfer->c_accountname = $request->input('customerAccountName');
         $bankTransfer->save();


         DB::table('bank_history')->insert([
         'bank_id' => $account->id,
         'account_name' => $accountName,
         'user_id' => $userid,
         'branch' => $branch,
         'detail' => $detail,
         'party' => $request->input('supplierAccountName') ?? $request->input('customerAccountName'),
         'dr_cr' => $drCr,
         'amount' => $request->amount,
         'ref_no' => $request->ref_no,
         'remark' => $request->remarks,
         'date' => $request->date,

     ]);
         return redirect()->back()->with('success', 'Transfer has been successfully recorded.');
     }

     public function bankreport(Request $request)
     {
         if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
         $userid = Session('softwareuser');
         $useritem = DB::table('softwareusers')
             ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
             ->where('user_id', $userid)
             ->get();

         $accountName = $request->input('account_name');

         $branch = DB::table('softwareusers')
         ->where('id', Session('softwareuser'))
         ->pluck('location')
         ->first();

         $accounts = DB::table('bank')
         ->select('id', 'status', 'account_name')
         ->where('branch',$branch)
         ->get();
         $adminid = Softwareuser::Where('id', $userid)
         ->pluck('admin_id')
         ->first();
          $shopdata = Branch::Where('id', $branch)->get();

     $currency = Adminuser::Where('id', $adminid)
     ->pluck('currency')
     ->first();

         $opening_balance= DB::table('bank')
         ->where('account_name', $accountName)
         ->value('opening_balance');

         // Initialize empty values for transactions, totals, and balance
         $transactions = [];
         $currentBalance = 0;
         $start_date='';
         $end_date='';
         $totalDebit = 0;
         $totalCredit = 0;
         $transactionType=0;

         return view('bankaccount.bankreport', [
             'opening_balance' => $opening_balance, // Corrected line
             'users' => $useritem,
             'accounts' => $accounts,
             'userid' => $userid,
             'useritem' => $useritem,
             'transactions' => $transactions,
             'current_balance' => $currentBalance,
             'start_date' => $start_date,
             'end_date' => $end_date,
             'totalDebit' => $totalDebit,
             'totalCredit' => $totalCredit,
             'transactionType' => $transactionType,
             'shopdatas' => $shopdata,
             'currency'=>$currency,




         ]);
     }


     // employeeeeeeeeeee.....................................................................

             public function employee()
         {
            if (Session::has('softwareuser')) {
            } elseif (Session::has('adminuser')) {
            } else {
                return redirect('userlogin'); // or 'adminlogin' based on your logic
            }

            // Initialize $adminid variable
            $adminid = null;

            // Check for adminuser session
            if (Session::has('adminuser')) {
                $adminid = Session::get('adminuser');
                $useritem = DB::table('adminusers')
                    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                    ->where('user_id', $adminid)
                    ->get();
                    $departments = DB::table('department_employee')
                    ->select('id', 'department as name')
                    ->get();
            } elseif (Session::has('softwareuser')) {
                $userid = Session::get('softwareuser');
                $useritem = DB::table('softwareusers')
                    ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                    ->where('user_id', $userid)
                    ->get();

                // Get the admin_id from the softwareuser
                $adminid = Softwareuser::where('id', $userid)
                    ->pluck('admin_id')
                    ->first();
       // Retrieve the branch based on the user ID
       $branch = DB::table('softwareusers')
       ->where('id', $userid)
       ->pluck('location')
       ->first();

   // Get departments filtered by user_id and branch
   $departments = DB::table('department_employee')
       ->where('user_id', $userid) // Assuming user_id is in department_employee
       ->where('branch', $branch)   // Assuming branch is in department_employee
       ->select('id', 'department as name')
       ->get();
       $shopdata = Branch::Where('id', $branch)->get();
                }

            // Check if $adminid is set before querying Adminuser
            if (!$adminid) {
                return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
            }

            $item = DB::table('branches')
                ->get();

                if (Session('softwareuser')) {
                    $options = [
                        'departments' => $departments,
                        'locations' => $item,
                        'users' => $useritem,
                        'shopdatas' => $shopdata,
                        'branch'=>$branch,
                    ];
                } elseif (Session('adminuser')) {
                    $options = [
                        'departments' => $departments,
                        'locations' => $item,
                        'users' => $useritem,
                    ];
                }


             return view('/employee/employee', $options);
         }


         public function employeesubmit(Request $request)
         {
            if (Session::has('softwareuser')) {
            } elseif (Session::has('adminuser')) {
            } else {
                return redirect('userlogin'); // or 'adminlogin' based on your logic
            }

            // Initialize $adminid variable
            $adminid = null;

            // Check for adminuser session
            if (Session::has('adminuser')) {
                $adminid = Session::get('adminuser');

            } elseif (Session::has('softwareuser')) {
                $userid = Session::get('softwareuser');


                // Get the admin_id from the softwareuser
                $adminid = Softwareuser::where('id', $userid)
                    ->pluck('admin_id')
                    ->first();
            }

            // Check if $adminid is set before querying Adminuser
            if (!$adminid) {
                return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
            }

             // Create new employee entry
             $employee = new Employee();
             $employee->admin_id = $adminid;
             $employee->first_name = $request->input('first_name');
             $employee->last_name = $request->input('last_name');
             $employee->email = $request->input('email');
             $employee->phone = $request->input('phone');
             $employee->branch = $request->input('branch');
             $employee->department = $request->input('department');
             $employee->employee_id = $request->input('Employeeid');
             $employee->salary = $request->input('salary');
             $employee->date = $request->input('date_of_joining');
             if (Session('softwareuser')) {
                $employee->user_id = $userid;
            }
             $employee->save();

             // Redirect back with success message
             return redirect()->back()->with('success', 'Employee added successfully.');
         }

         public function store(Request $request)
         {
             $validatedData = $request->validate([
                 'name' => 'required|string|max:255',
             ]);

          // Initialize variables
          $adminid = null;
          $userid = null;
          $branch = null;

          // Check if 'adminuser' session exists
          if (Session::has('adminuser')) {
              $adminid = Session::get('adminuser');

              // Create the department with admin_id
              $department = Department::create([
                  'department' => $validatedData['name'],
                  'admin_id' => $adminid,
              ]);

             return response()->json([
                 'success' => true,
                 'department' => $department,
             ]);
         }
         elseif (Session::has('softwareuser')) {
            $userid = Session::get('softwareuser');

            // Retrieve the admin_id associated with the software user
            $adminid = Softwareuser::where('id', $userid)
                ->pluck('admin_id')
                ->first();

            // Retrieve the branch based on the user ID
            $branch = DB::table('softwareusers')
                ->where('id', $userid)
                ->pluck('location')
                ->first();

            // Create the department with user_id and branch
            $department = Department::create([
                'department' => $validatedData['name'],
                'user_id' => $userid,
                'admin_id' => $adminid,
                'branch' => $branch,
            ]);

            return response()->json([
                'success' => true,
                'department' => $department,
            ]);
        }

        // Return an error response if neither session exists
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated.'
        ], 401);
    }

         public function listemployee(Request $request)
         {
            if (Session::has('softwareuser')) {
            } elseif (Session::has('adminuser')) {
            } else {
                return redirect('userlogin'); // or 'adminlogin' based on your logic
            }

            // Initialize $adminid variable
            $adminid = null;

            // Check for adminuser session
            if (Session::has('adminuser')) {
                $adminid = Session::get('adminuser');
                $useritem = DB::table('adminusers')
                    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                    ->where('user_id', $adminid)
                    ->get();
                         $departments = DB::table('department_employee')
                 ->select('id', 'department as name')
                 ->get();

            $employees = DB::table('employee')
            ->where('admin_id', $adminid)
                 ->get();
             $shopdata = Adminuser::where('id', $adminid)->get();
            } elseif (Session::has('softwareuser')) {
                $userid = Session::get('softwareuser');
                $useritem = DB::table('softwareusers')
                    ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                    ->where('user_id', $userid)
                    ->get();

                // Get the admin_id from the softwareuser
                $adminid = Softwareuser::where('id', $userid)
                    ->pluck('admin_id')
                    ->first();
                         $departments = DB::table('department_employee')
                 ->select('id', 'department as name')
                 ->get();
                 $branch = DB::table('softwareusers')
                 ->where('id', $userid)
                 ->pluck('location')
                 ->first();
            $employees = DB::table('employee')
            ->where('admin_id', $adminid)
            ->where('branch',$branch)
                 ->get();
                           $shopdata = Branch::Where('id', $branch)->get();

            }

            // Check if $adminid is set before querying Adminuser
            if (!$adminid) {
                return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
            }





             $data=[
                 'users' => $useritem,
                 'shopdatas' => $shopdata,
                 'employees' => $employees
             ];

             return view('/employee/listemployee',$data);
         }

         public function editemployee($id) {
            if (Session::has('softwareuser')) {
            } elseif (Session::has('adminuser')) {
            } else {
                return redirect('userlogin'); // or 'adminlogin' based on your logic
            }

            // Initialize $adminid variable
            $adminid = null;

            // Check for adminuser session
            if (Session::has('adminuser')) {
                $adminid = Session::get('adminuser');
                $useritem = DB::table('adminusers')
                    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                    ->where('user_id', $adminid)
                    ->get();
             $shopdata = Adminuser::where('id', $adminid)->get();
            } elseif (Session::has('softwareuser')) {
                $userid = Session::get('softwareuser');
                $useritem = DB::table('softwareusers')
                    ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                    ->where('user_id', $userid)
                    ->get();

                // Get the admin_id from the softwareuser
                $adminid = Softwareuser::where('id', $userid)
                    ->pluck('admin_id')
                    ->first();
                                     $branch = DB::table('softwareusers')
                 ->where('id', $userid)
                 ->pluck('location')
                 ->first();
                $shopdata = Branch::Where('id', $branch)->get();

            }

            // Check if $adminid is set before querying Adminuser
            if (!$adminid) {
                return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
            }


             $item = DB::table('branches')->get();

                 // Fetching the department column from the department_employee table
                 $departments = DB::table('department_employee')
                 ->select('id', 'department as name')
                 ->get();

             // Find the employee by ID
             $employee = Employee::find($id);

             // Fetch branch locations from the branches table
             $locations = DB::table('branches')->get();

             // Fetch departments from the department_employee table
             $departments = DB::table('department_employee')
                 ->select('id', 'department as name')
                 ->get();


             // Prepare the data for the view
             $data = [
                 'locations' => $item,
                 'users' => $useritem,
                 'shopdatas' => $shopdata,
                 'departments' => $departments,
                 'locations' => $locations,
                 'users' => $useritem,
                 'employee' => $employee,
             ];

             // Return the edit view with the employee data, locations, and departments
             return view('employee.editemployee', $data);
         }


    public function updateemployee(Request $request, $id)
    {

        // Find the employee by ID
        $employee = Employee::find($id);

        // Update employee data
        $employee->first_name = $request->input('first_name');
        $employee->last_name = $request->input('last_name');
        $employee->email = $request->input('email');
        $employee->phone = $request->input('phone');
        $employee->salary = $request->input('salary');
        $employee->date = $request->input('date_of_joining'); // Assuming 'date_of_joining' exists in the form

        // Save the updated employee data
        $employee->save();

        // Flash a success message
        session()->flash('success', 'Employee updated successfully!');

        return redirect('/listemployee');
    }

        //  recieptVoucher...................

    public function recieptVoucher($id)
    {
        if (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        } elseif (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        }
        else {
            return redirect('userlogin'); // or 'adminlogin' based on your logic
        }

        if (Session('adminuser')) {
            $adminid = Session('adminuser');
            $shopdata = Adminuser::Where('id', $adminid)
                ->get();

            $item = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();

            $purchases = DB::table('credit_transactions')
                ->select(DB::raw('*'))
                ->where('credit_transactions.credituser_id', $id)
                ->whereNotNull('collected_amount') // New condition for collected_amount
                ->orderBy('created_at', 'DESC')
                ->get();
        } elseif (Session('softwareuser')) {
            $item = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', Session('softwareuser'))
                ->get();

            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

            $userid = Session('softwareuser');
            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();

            $purchases = DB::table('credit_transactions')
                ->select(DB::raw('*'))
                ->where('credit_transactions.location', $branch)
                ->where('credit_transactions.credituser_id', $id)
                ->whereNotNull('collected_amount') // New condition for collected_amount
                ->orderBy('created_at', 'DESC')
                ->get();
            $shopdata = Branch::Where('id', $branch)->get();

        }

        $username = DB::table('creditusers')->where('id', $id)->pluck('name')->first();

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        if (Session('adminuser')) {
            $data = [
                'users' => $item,
                'purchases' => $purchases,
                'credit_name' => $username,
                'credit_id' => $id,
                'currency' => $currency,
                'shopdatas' => $shopdata,
            ];
        } elseif (Session('softwareuser')) {
            $data = [
                'users' => $item,
                'purchases' => $purchases,
                'credit_name' => $username,
                'credit_id' => $id,
                'currency' => $currency,
                'shopdatas' => $shopdata,
            ];
        }

        return view('/billingdesk/recieptvoucher', $data);
    }
    public function creditnote()
    {
         if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', Session('softwareuser'))
            ->get();

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $userid = Session('softwareuser');
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();


        $shopdata = Branch::Where('id', $branch)->get();
        $transactionIds = BuyProduct::select('transaction_id')
            ->where('branch', $branch) // Assuming the 'branch' column exists in BuyProduct
            // ->where('payment_type', 3) // Add the condition for payment_type
            ->distinct()
            ->get();
        $data=[
            'transactionIds'=>$transactionIds,
            'users'=>$item,
            'userid'=>$userid,
            'shopdatas'=>$shopdata
        ];

        // Return the view with the data
        return view('billingdesk.creditnote', $data);
    }

    public function getInvoiceDetails(Request $request)
    {
        $transactionId = $request->input('transaction_id');

        // Fetch customer name
        $customerName = BuyProduct::where('transaction_id', $transactionId)
                        ->pluck('customer_name')
                        ->first();
        // Fetch credit_user_id from BuyProduct based on transaction_id
        $creditUserId = BuyProduct::where('transaction_id', $transactionId)
                            ->pluck('credit_user_id')
                            ->first();

        // Fetch total due as due_amount - collected_amount from creditsummaries for the fetched credituser_id
        $totalDue = DB::table('creditsummaries')
                        ->where('credituser_id', $creditUserId)  // Use the credituser_id from BuyProduct
                        ->select(DB::raw('due_amount - collected_amount as total_due'))
                        ->value('total_due');  // Fetch the calculated total due

        // $invoiceDue = DB::table('credit_transactions')
        // ->where('transaction_id', $transactionId)
        // ->sum('Invoice_due');

        $invoiceDue = DB::table('buyproducts')
        ->select(DB::raw('SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0)) as sum'))
        ->where('transaction_id', $transactionId)
        ->groupBy('transaction_id')
        ->value('sum');

        $credit_note_amount=DB::table('credit_note')
        ->where('transaction_id', $transactionId)
        ->sum('credit_note');

        $collected_amount = DB::table('credit_transactions')
        ->where('transaction_id', $transactionId)
        ->sum('collected_amount');


        $balance_due=$invoiceDue - $collected_amount - $credit_note_amount;

        // Fetch product details with netrate - (total_discount_percent * netrate / 100) as total
      // Fetch products from the BuyProduct table
        $products = BuyProduct::where('transaction_id', $transactionId)
        ->select('product_name', 'netrate', 'total_discount_percent', 'quantity', 'total_amount',
            DB::raw('ROUND(netrate - (total_discount_percent * netrate / 100), 2) as bill_grand_total'))
        ->get();

        // Fetch return quantities from the returnproducts table
        $returnQuantities = DB::table('returnproducts')
        ->select('product_name', DB::raw('SUM(quantity) as total_return_quantity'))
        ->where('transaction_id', $transactionId)
        ->groupBy('product_name')
        ->get()
        ->keyBy('product_name'); // Use product_name as key for easy access

        // Prepare the final result
        $product = $products->map(function ($product) use ($returnQuantities) {
        // Check if product exists in return quantities
        $returnedQuantity = $returnQuantities->get($product->product_name)->total_return_quantity ?? 0;

        // Calculate remaining quantity
        $remainingQuantity = $product->quantity - $returnedQuantity;

        // If remaining quantity is greater than zero, return the new product details
        if ($remainingQuantity > 0) {
            return [
                'product_name' => $product->product_name,
                'quantity' => $remainingQuantity,
                'total_amount' => $product->total_amount / $product->quantity * $remainingQuantity, // Calculate new total_amount
                'bill_grand_total' => $product->bill_grand_total
            ];
        }
        // Return null for products with no remaining quantity
        return [
           'product_name' => $product->product_name,
            'quantity' => 'Returned',
            'total_amount' => '-',
            'bill_grand_total' => '-'
        ];
        });

        // Filter out null values
        $product = $product->filter()->values(); // Reset keys after filtering

// Return or use the $result as needed


        // Prepare response data
        $response = [
            'customer_name' => $customerName,
            'total_due' => $totalDue,
            'invoice_due' => $invoiceDue,
            'products' => $product,
            'credit_note_amount'=>$credit_note_amount,
            'balance_due'=>$balance_due,
            'collected_amount'=>$collected_amount,
        ];

        return response()->json($response);
    }

    public function storeInvoiceDetails(Request $request)
    {
        // Validate incoming data
        // $validated = $request->validate([
        //     'transaction_id' => 'required|string',  // Ensure transaction_id is valid
        //     'customer_name' => 'required|string',
        //     'total_due' => 'required|numeric',
        //     'invoice_due' => 'required|numeric',
        //     'credit_note_amount' => 'array',
        //     'remarks' => 'nullable|string',
        // ]);
        $transactionId = $request->input('transaction_id');

        // Fetch customer name based on the transaction_id
        $customerName = BuyProduct::where('transaction_id', $transactionId)
                        ->pluck('customer_name')
                        ->first();

        // Fetch credit_user_id based on the transaction_id
        $creditUserId = BuyProduct::where('transaction_id', $transactionId)
                        ->pluck('credit_user_id')
                        ->first();
        $userid = Session('softwareuser');
        $branch = Softwareuser::locationById($userid);
        // Initialize totalCreditNote to accumulate credit note amounts
  // Initialize a variable to store the sum of credit notes
        $totalCreditNote = 0;

        foreach ($request->product_name as $index => $productID) {
            // Add the current credit note amount to the total sum
            $creditNoteAmount = $request->input('credit_note_amount')[$index];
            if (is_numeric($creditNoteAmount)) {
            $totalCreditNote += $creditNoteAmount; // Sum the credit_note_amount

            // Insert data into the credit_note table
            DB::table('credit_note')->insert([
                'user_id' => $userid,
                'branch' => $branch,
                'transaction_id' => $request->input('transaction_id'),
                'customer_name' => $request->input('customer_name'),
                'total_due' => $request->input('total_due'),
                'invoice_due' => $request->input('invoice_due'),
                'product_name' => $productID,
                'sell_cost' => $request->input('sell_cost')[$index],
                'quantity' => $request->input('quantity')[$index],
                'total' => $request->input('total')[$index],
                'credit_note' => $creditNoteAmount,
                'remark' => $request->input('remarks'),
            ]);

        }
    }

        // Update the `creditsummaries` table using `credituser_id`
        DB::table('creditsummaries')
        ->where('credituser_id', $creditUserId)  // Use `credituser_id` to identify the customer
        ->update([
            'creditnote' => DB::raw('creditnote + ' . $totalCreditNote),  // Add the total credit note amount
            'due_amount' => DB::raw('due_amount - ' . $totalCreditNote),
        ]);



   // Redirect to the new view with the transaction ID
   return redirect()->route('viewCreditNote', ['transaction_id' => $request->input('transaction_id')])
   ->with('success', 'Invoice details stored successfully.');
}


public function viewCreditNote($transaction_id)
{
     if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
    $item = DB::table('softwareusers')
    ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
    ->where('user_id', Session('softwareuser'))
    ->get();

    $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();

    $userid = Session('softwareuser');
    $adminid = Softwareuser::Where('id', $userid)
        ->pluck('admin_id')
        ->first();


        $shopdata = Branch::Where('id', $branch)->get();
        // Fetch credit note details by transaction_id
        $creditNote = DB::table('credit_note')
            ->where('transaction_id', $transaction_id)
            ->first();

        if (!$creditNote) {
            return redirect()->back()->with('error', 'No credit note found for the specified transaction ID.');
        }

        // Fetch transaction details from buyproducts table
        $buyProduct = DB::table('buyproducts')
            ->where('transaction_id', $transaction_id)
            ->first();

        // Fetch all related credit note details (products, amounts, etc.)
        $latestSubmissionTime = DB::table('credit_note')
        ->where('transaction_id', $transaction_id)
        ->orderBy('created_at', 'desc')  // Ensure the latest submission is captured
        ->value('created_at');  // Fetch the latest 'created_at' time

        $creditNoteDetails = DB::table('credit_note')
            ->where('transaction_id', $transaction_id)
            ->where('created_at', $latestSubmissionTime)  // Only fetch details for the latest submission time
            ->distinct()  // Ensure distinct results if required
            ->get();


    // Pass the data to the view
    return view('billingdesk.creditnote_print', [
        'creditNote' => $creditNote,
        'buyProduct' => $buyProduct,
        'creditNoteDetails' => $creditNoteDetails,
        'users' => $item,  // Passing $item as 'users'
        'userid' => $userid,
        'shopdatas' => $shopdata
    ]);
}
public function creditNoteHistory(Request $request)
{
     if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
    // $customers = DB::table('credit_note')
    //     ->select('customer_name')
    //     ->distinct()
    //     ->get();

    $customers = DB::table('creditusers')
        ->select('name')
        ->distinct()
        ->get();

    // Get the currently logged-in user's details
    $item = DB::table('softwareusers')
        ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
        ->where('user_id', Session('softwareuser'))
        ->get();

    $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();

    $userid = Session('softwareuser');
    $adminid = Softwareuser::Where('id', $userid)
        ->pluck('admin_id')
        ->first();

        $shopdata = Branch::Where('id', $branch)->get();

    // Get the filters from the request
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $customerName = $request->input('customer_name');

    // Base query to get all credit notes
    $query = DB::table('credit_note')
    ->leftJoin(DB::raw('(SELECT
                            transaction_id COLLATE utf8mb4_unicode_ci as transaction_id,
                            SUM(COALESCE(collected_amount, 0)) as collected_amount,
                            GROUP_CONCAT(comment SEPARATOR ", ") as comments -- Concatenate comments
                        FROM credit_transactions
                        WHERE comment != "Returned Product"  -- Exclude returned products
                        GROUP BY transaction_id) as collectedamount'), 'credit_note.transaction_id', '=', 'collectedamount.transaction_id')
    ->leftJoin(DB::raw('(SELECT
                        transaction_id COLLATE utf8mb4_unicode_ci as transaction_id,
                        SUM(DISTINCT(grand_total)) as return_grand_total
                    FROM returnproducts
                    GROUP BY transaction_id) as returnproducts'), 'credit_note.transaction_id', '=', 'returnproducts.transaction_id')
    ->select(
        'credit_note.id',
        'credit_note.customer_name',
        'credit_note.transaction_id',
        'credit_note.invoice_due',
        DB::raw('COALESCE(collectedamount.collected_amount, 0) as collected_amount'),
        DB::raw('GROUP_CONCAT(collectedamount.comments SEPARATOR ", ") as comments'), // Add concatenated comments
        DB::raw('SUM(credit_note.credit_note) as total_amount'),
        DB::raw('COALESCE(returnproducts.return_grand_total, 0) as return_grand_total') // Add returnproducts grand total
    )
    ->where('credit_note.branch', $branch)
    ->groupBy(
        'credit_note.transaction_id'
    );


    // Apply filters if provided
    if (!empty($startDate) && !empty($endDate)) {
        // Convert the dates to proper format using Carbon
        $formattedStartDate = Carbon::parse($startDate)->startOfDay(); // Start of the day
        $formattedEndDate = Carbon::parse($endDate)->endOfDay(); // End of the day

        // Apply the date filter
        $query->whereBetween('created_at', [$formattedStartDate, $formattedEndDate]);
    }

    // Apply customer name filter if provided
    if (!empty($customerName)) {
        $query->where('customer_name', 'like', '%' . $customerName . '%');
    }

    // Execute the query and get the results
    $history = $query->get();

    // Pass the data to the view
    return view('billingdesk.creditnote_history', [
        'start_date' => $startDate,
        'end_date' => $endDate,
        'customer_name' => $customerName,
        'history' => $history,
        'users' => $item,
        'userid' => $userid,
        'shopdatas' => $shopdata,
        'customers' => $customers
    ]);
}



public function debitnote()
{
 if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
    $item = DB::table('softwareusers')
        ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
        ->where('user_id', Session('softwareuser'))
        ->get();

    $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();

    $userid = Session('softwareuser');
    $adminid = Softwareuser::Where('id', $userid)
        ->pluck('admin_id')
        ->first();


        $shopdata = Branch::Where('id', $branch)->get();
    $recieptIds = stockdetail::select('reciept_no')
        ->where('branch', $branch) // Assuming the 'branch' column exists in BuyProduct
        // ->where('payment_mode', 2)  // Filter by payment mode = 2
        ->distinct()
        ->get();
    $data=[
        'recieptIds'=>$recieptIds,
        'users'=>$item,
        'userid'=>$userid,
        'shopdatas'=>$shopdata
    ];

    // Return the view with the data
    return view('inventory.debitnote', $data);
}

public function getDebitInvoiceDetails(Request $request)
{
    $recieptIds = $request->input('reciept_no');

    // Fetch customer name
    $customerName = stockdetail::where('reciept_no', $recieptIds)
                    ->pluck('supplier')
                    ->first();
    // Fetch credit_user_id from BuyProduct based on transaction_id
    $creditUserId = stockdetail::where('reciept_no', $recieptIds)
                    ->pluck('supplier_id')
                    ->first();

    // Fetch total due as due_amount - collected_amount from creditsummaries for the fetched credituser_id
    $totalDue = DB::table('supplier_credits')
                    ->where('supplier_id', $creditUserId)  // Use the credituser_id from BuyProduct
                    ->select(DB::raw('due_amt - collected_amt as total_due'))
                    ->value('total_due');  // Fetch the calculated total due

    // $invoiceDue = DB::table('credit_supplier_transactions')
    //                 ->where('reciept_no', $recieptIds)
    //                 ->sum('Invoice_due');
        $invoiceDue = DB::table('stockdetails')
            ->select(DB::raw('SUM(stockdetails.price) as sum'))
            ->where('reciept_no', $recieptIds)
            ->value('sum');

            $collected_amount = DB::table('credit_supplier_transactions')
            ->where('reciept_no', $recieptIds)
            ->sum('collectedamount');

        $debit_note_amount=DB::table('debit_note')
        ->where('reciept_id', $recieptIds)
        ->sum('debit_note');


        $balance_due=$invoiceDue - $collected_amount - $debit_note_amount;

        $latestSubmissionTime = DB::table('stockdetails')
        ->where('reciept_no', $recieptIds)
        ->orderBy('created_at', 'desc')  // Ensure the latest submission is captured
        ->value('created_at');


        // Fetch products from stockdetails table along with the product name from products table
        $products = DB::table('stockdetails as dn')
        ->join('products as p', 'dn.product', '=', 'p.id') // Join with products table to get the product name
        ->where('dn.reciept_no', $recieptIds)
        ->select('p.product_name', 'dn.product', 'dn.rate', 'dn.quantity', 'dn.price')
        ->get();

        // Fetch returned quantities from returnpurchases table
        $returnQuantities = DB::table('returnpurchases as rp')
        ->join('products as p', 'rp.product_id', '=', 'p.id') // Join to convert product_id to product name
        ->where('rp.reciept_no', $recieptIds)
        ->select('rp.product_id', DB::raw('SUM(rp.quantity) as total_return_quantity'))
        ->groupBy('rp.product_id')
        ->get()
        ->keyBy('product_id'); // Use product_id as the key for easy access

        // Prepare the final result
        $result = $products->map(function ($product) use ($returnQuantities) {
        // Check if product exists in the return quantities
        $returnedQuantity = $returnQuantities->get($product->product)->total_return_quantity ?? 0;
        if ($returnedQuantity == $product->quantity) {
            return [
                'product_name' => $product->product_name,
                'product' => $product->product, // The product ID
                'rate' => $product->rate,
                'quantity' => 'returned', // Mark as returned
                'price' => 0, // No price if fully returned
            ];
        }
        // Calculate the remaining quantity
        $remainingQuantity = $product->quantity - $returnedQuantity;

        // If remaining quantity is greater than zero, return the new product details
        if ($remainingQuantity > 0) {
            return [
                'product_name' => $product->product_name,
                'product' => $product->product, // The product ID
                'rate' => $product->rate,
                'quantity' => $remainingQuantity, // Remaining quantity
                'price' => $product->price / $product->quantity * $remainingQuantity, // Adjusted price based on remaining quantity
            ];
        }
        // Return null if no remaining quantity
        return [
            'product_name' => $product->product_name,
            'product' => $product->product, // The product ID
            'rate' => $product->rate,
            'quantity' => 'returned', // Mark as returned
            'price' => 0, // No price if fully returned
        ];
            });

        // Filter out null values (products with no remaining quantities)
        $result = $result->filter()->values(); // Reset the keys after filtering

        // Return or use the $result as needed




    // Prepare response data
    $response = [
        'customer_name' => $customerName,
        'total_due' => $totalDue,
        'invoice_due' => $invoiceDue,
        'products' => $result,
        'debit_note_amount'=>$debit_note_amount,
        'balance_due'=>$balance_due,
        'collected_amount'=>$collected_amount,
    ];

    return response()->json($response);
}
public function storeDebitInvoiceDetails(Request $request)
{
    // // Validate incoming data
    // $validated = $request->validate([
    //     'reciept_no' => 'required|string',  // Changed from 'reciept_id' to 'reciept_no'
    //     'supplier' => 'required|string',
    //     'total_due' => 'required|numeric',
    //     'invoice_due' => 'required|numeric',
    //     'debit_note_amount' => 'required|array',
    //     'product' => 'required|array',      // Added validation for product array
    //     'sellingcost' => 'required|array',  // Added validation for selling cost array
    //     'quantity' => 'required|array',     // Added validation for quantity array
    //     'total' => 'required|array',        // Added validation for total array
    //     'remarks' => 'nullable|string',
    // ]);
    $reciept_no = $request->input('reciept_no');

    $supplier_id = Stockdetail::where('reciept_no', $reciept_no)
    ->pluck('supplier_id')
    ->first();

    $totalDebitNote = 0;
    $userid = Session('softwareuser');
    $branch = Softwareuser::locationById($userid);
    // Store each product along with credit note amount
    foreach ($request->product_id as $index => $productID) {

        $debitNoteAmount = $request->input('debit_note_amount')[$index];
        if (is_numeric($debitNoteAmount)) {

        $totalDebitNote += $debitNoteAmount; // Sum the credit_note_amount

        DB::table('debit_note')->insert([
            'user_id' => $userid,
             'branch' => $branch,
            'reciept_id' => $request->input('reciept_no'),  // Corrected field name
            'supplier' => $request->input('supplier'),
            'total_due' => $request->input('total_due'),
            'bill_due' => $request->input('invoice_due'),
            'product_name' => $request->input('product')[$index],  // Corrected indexing
            'buy_cost' => $request->input('sellingcost')[$index],
            'quantity' => $request->input('quantity')[$index],
            'total' => $request->input('total')[$index],
            'debit_note' => $debitNoteAmount,
            'remark' => $request->input('remarks'),

        ]);


    }
}
    DB::table('supplier_credits')
    ->where('supplier_id', $supplier_id)  // Use `credituser_id` to identify the customer
    ->update([
        'debitnote' => DB::raw('debitnote + ' . $totalDebitNote),  // Add the total credit note amount
        'due_amt' => DB::raw('due_amt - ' . $totalDebitNote),

    ]);


    // Redirect to the new view with the transaction ID
    return redirect()->route('viewDebitNote', ['reciept_no' => $request->input('reciept_no')])
        ->with('success', 'Invoice details stored successfully.');
}

public function viewDebitNote($reciept_no)
{
     if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
    $item = DB::table('softwareusers')
    ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
    ->where('user_id', Session('softwareuser'))
    ->get();

    $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();

    $userid = Session('softwareuser');
    $adminid = Softwareuser::Where('id', $userid)
        ->pluck('admin_id')
        ->first();


        $shopdata = Branch::Where('id', $branch)->get();
    // Fetch credit note details by transaction_id
    $creditNote = DB::table('debit_note')
        ->where('reciept_id', $reciept_no)
        ->first();

    if (!$creditNote) {
        return redirect()->back()->with('error', 'No credit note found for the specified transaction ID.');
    }

    // Fetch transaction details from buyproducts table
    $buyProduct = DB::table('stockdetails')
        ->where('reciept_no', $reciept_no)
        ->first();

    // Fetch all related credit note details (products, amounts, etc.)
    $latestSubmissionTime = DB::table('debit_note')
    ->where('reciept_id', $reciept_no)
    ->orderBy('created_at', 'desc')  // Ensure the latest submission is captured
    ->value('created_at');  // Fetch the latest 'created_at' time


    // Fetch the product ID based on receipt number and submission time
    $creditNoteDetails = DB::table('debit_note')
    ->where('reciept_id', $reciept_no)
    ->where('created_at', $latestSubmissionTime)  // Only fetch details for the latest submission time
    ->distinct()  // Ensure distinct results if required
    ->get();

    // Pass the data to the view
    return view('inventory.debitnote_print' , [
        'creditNote' => $creditNote,
        'buyProduct' => $buyProduct,
        'creditNoteDetails' => $creditNoteDetails,
        'users' => $item,  // Passing $item as 'users'
        'userid' => $userid,
        'shopdatas' => $shopdata
    ]);
}

public function debitNoteHistory(Request $request)
{
     if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
    $suppliers = DB::table('suppliers')
    ->select('name')
    ->distinct()
    ->get();


    // Get the currently logged-in user's details
    $item = DB::table('softwareusers')
        ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
        ->where('user_id', Session('softwareuser'))
        ->get();

    $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();

    $userid = Session('softwareuser');
    $adminid = Softwareuser::Where('id', $userid)
        ->pluck('admin_id')
        ->first();

        $shopdata = Branch::Where('id', $branch)->get();

    // Get the filters from the request
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $supplier = $request->input('supplier');

    // Base query to get all credit notes
    $query = DB::table('debit_note')
    ->leftJoin(DB::raw('(SELECT
                            reciept_no COLLATE utf8mb4_unicode_ci as reciept_no,
                            SUM(COALESCE(collectedamount, 0)) as collected_amount,
                            MAX(comment) as comment  -- Use MAX to get one comment per receipt
                        FROM credit_supplier_transactions
                        WHERE comment != "Purchase Returned"  -- Exclude returned products
                        GROUP BY reciept_no) as collected_amount'), 'debit_note.reciept_id', '=', 'collected_amount.reciept_no')
    ->leftJoin(DB::raw('(SELECT
                        reciept_no COLLATE utf8mb4_unicode_ci as reciept_no,
                        SUM(amount) as return_grand_total
                    FROM returnpurchases
                    GROUP BY reciept_no) as returnpurchases'), 'debit_note.reciept_id', '=', 'returnpurchases.reciept_no')
    ->select(
        'debit_note.id',
        'debit_note.supplier',
        'debit_note.reciept_id',
        'debit_note.bill_due',
        DB::raw('COALESCE(collected_amount.collected_amount, 0) as collected_amount'), // Corrected alias reference
        DB::raw('SUM(debit_note.debit_note) as total_amount'), // This line is fine, but check if it needs to be grouped
        DB::raw('COALESCE(returnpurchases.return_grand_total, 0) as return_grand_total'), // Corrected alias reference
        DB::raw('MAX(collected_amount.comment) as comments') // Adding the comment column
    )
    ->where('debit_note.branch', $branch)
    ->groupBy(
        'debit_note.reciept_id'
    );



    // Apply filters if provided
    if (!empty($startDate) && !empty($endDate)) {
        // Convert the dates to proper format using Carbon
        $formattedStartDate = Carbon::parse($startDate)->startOfDay(); // Start of the day
        $formattedEndDate = Carbon::parse($endDate)->endOfDay(); // End of the day

        // Apply the date filter
        $query->whereBetween('created_at', [$formattedStartDate, $formattedEndDate]);
    }

    // Apply customer name filter if provided
    if (!empty($supplier)) {
        $query->where('supplier', 'like', '%' . $supplier . '%');
    }

    // Execute the query and get the results
    $history = $query->get();

    // Pass the data to the view
    return view('inventory.debitnote_history', [
        'start_date' => $startDate,
        'end_date' => $endDate,
        'supplier' => $supplier,
        'history' => $history,
        'users' => $item,
        'userid' => $userid,
        'shopdatas' => $shopdata,
        'suppliers' => $suppliers
    ]);
}
public function creditVoucher($transaction_id)
{
     if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $customers = DB::table('credit_note')
        ->select('customer_name', 'transaction_id') // Include transaction_id
        ->distinct()
        ->get();

        // Get the currently logged-in user's details
        $item = DB::table('softwareusers')
        ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
        ->where('user_id', Session('softwareuser'))
        ->get();

        $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();

        $userid = Session('softwareuser');
        $adminid = Softwareuser::Where('id', $userid)
        ->pluck('admin_id')
        ->first();

        $shopdata = Branch::Where('id', $branch)->get();
        // Base query to get all credit notes
        $query = DB::table('credit_note')
        ->select(
            'credit_note.id',
            'credit_note.customer_name',
            'credit_note.transaction_id',
            'credit_note.credit_note',
            'credit_note.created_at'

        )

        ->where('transaction_id', $transaction_id);
        // Execute the query and get the results
        $history = $query->get();

        // Pass the data to the view
        return view('/billingdesk/creditvoucher', [
            'history' => $history,
            'users' => $item,
            'userid' => $userid,
            'shopdatas' => $shopdata,
            'customers' => $customers
        ]);
   }
public function debitVoucher($reciept_id)
   {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
           $customers = DB::table('debit_note')
           ->select('supplier', 'reciept_id') // Include transaction_id
           ->distinct()
           ->get();

           // Get the currently logged-in user's details
           $item = DB::table('softwareusers')
           ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
           ->where('user_id', Session('softwareuser'))
           ->get();

           $branch = DB::table('softwareusers')
           ->where('id', Session('softwareuser'))
           ->pluck('location')
           ->first();

           $userid = Session('softwareuser');
           $adminid = Softwareuser::Where('id', $userid)
           ->pluck('admin_id')
           ->first();

        $shopdata = Branch::Where('id', $branch)->get();
           // Base query to get all credit notes
           $query = DB::table('debit_note')
           ->select(
               'debit_note.id',
               'debit_note.supplier',
               'debit_note.reciept_id',
               'debit_note.debit_note',
               'debit_note.created_at'

           )

           ->where('reciept_id', $reciept_id);
           // Execute the query and get the results
           $history = $query->get();

           // Pass the data to the view
           return view('/inventory/debitvoucher', [
               'history' => $history,
               'users' => $item,
               'userid' => $userid,
               'shopdatas' => $shopdata,
               'customers' => $customers
           ]);
     }
     public function updateVatMode(Request $request)
     {
        $validated = $request->validate([
            'vat_mode' => 'required|in:1,2',
        ]);

        // Retrieve the branch ID of the logged-in user from the session
        $branchId = DB::table('softwareusers')
            ->where('id', session('softwareuser'))
            ->pluck('location') // Assuming 'location' stores the branch ID
            ->first();

        if (!$branchId) {
            return response()->json(['success' => false, 'message' => 'Branch not found'], 404);
        }

        Log::info('VAT Mode request received', ['vat_mode' => $validated['vat_mode'], 'branch_id' => $branchId]);

        $vatMode = $validated['vat_mode'];

        // Update the existing VAT mode record only for the corresponding branch
        $updated = DB::table('vat_mode')
            ->where('branch', $branchId)
            ->update([
                'mode' => $vatMode,
                'inclusive' => $vatMode == 1 ? 1 : null,
                'exclusive' => $vatMode == 2 ? 2 : null,
            ]);

        if ($updated) {
            Log::info('VAT Mode record updated successfully for branch', ['branch' => $branchId]);
            return response()->json(['success' => true, 'message' => 'VAT mode updated successfully']);
        }
    }

     public function editbillapprove(Request $request)
    {
        $transactionId = $request->transaction_id;

        // Update the `buyproducts` table
        $updated = DB::table('buyproducts')
            ->where('transaction_id', $transactionId)
            ->update(['approve' => 1]);

        if ($updated) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }


    public function editpurchaseapprove(Request $request)
    {
        $reciept_no = $request->reciept_no;

        // Update the `buyproducts` table
        $updated = DB::table('stockdetails')
            ->where('reciept_no', $reciept_no)
            ->update(['approve' => 1]);

        if ($updated) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

   

    public function updateexpense(Request $request)
    {

    $expense = Accountexpense::find($request->id);
    if (!$expense) {
        return redirect()->back()->with('error', 'Expense not found');
    }

    if ($request->expense_type == 1) {
        $expense->direct_expense = $request->expense;
        $expense->indirect_expense = null;
    } else {
        $expense->indirect_expense = $request->expense;
        $expense->direct_expense = null;
    }

    $expense->expense_type = $request->expense_type;
    $expense->details = $request->details;
    $expense->amount = $request->amount;
    $expense->date = $request->date;

    $expense->save();

    return redirect()->back()->with('success', 'Expense updated successfully');
    }

    public function updateincome(Request $request)
    {


    $income = AccountIndirectIncome::find($request->id);
    if (!$income) {
        return redirect()->back()->with('error', 'Income not found');
    }

    if ($request->income_type == 1) {
        $income->direct_income = $request->income;
        $income->indirect_income = null;
    } else {
        $income->indirect_income = $request->income;
        $income->direct_income = null;
    }

    $income->income_type = $request->income_type;
    $income->details = $request->details;
    $income->amount = $request->amount;
    $income->date = $request->date;

    $income->save();

    return redirect()->back()->with('success', 'Income updated successfully');
    }


     public function service(){
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
         $userid = Session('softwareuser');
         $useritem = DB::table('softwareusers')
             ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
             ->where('user_id', $userid)
             ->get();

             $adminid = Softwareuser::Where('id', $userid)
             ->pluck('admin_id')
             ->first();
                 $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();
          $shopdata = Branch::Where('id', $branch)->get();
          $services = Service::select('payment_mode','customer','created_at','address', 'phone', 'service_id', DB::raw('SUM(total_amount) as total_amount'))
          ->where('branch', $branch)
          ->groupBy('service_id')
          ->orderBy('created_at', 'DESC')
          ->get();
               $data = [
             'users' => $useritem,
             'shopdatas' => $shopdata,
             'services'=>$services

         ];

         return view('/user/service', $data);
            }



    // Store a new service
    public function stores(Request $request)
    {
        // Get the user ID and branch location
        $userid = Session('softwareuser');
        $branch = DB::table('softwareusers')
            ->where('id', $userid)
            ->pluck('location')
            ->first();
            $count = DB::table('service')
            ->distinct()
            ->count('service_id');

        ++$count;

        $text = 'Service';

        $serviceid = $text.$count;
        // Loop through each service entry and create a new Service record
        foreach ($request->service_name as $index => $serviceName) {
            Service::create([
                'service_id'=>$serviceid,
                'user_id' => $userid,
                'branch' => $branch,
                'service_name' => $serviceName,
                'quantity' => $request->quantity[$index],
                'total_amount' => $request->total_amount[$index],
                'customer' => $request->customer_name,
                'payment_mode' => $request->payment, // Add payment mode here
                'address' => $request->address,
                'phone' => $request->mobile,
            ]);
        }

        // Redirect back with success message
        return redirect('service')->with('success', 'Services added successfully.');
    }
    
    

 

    public function daybook(){
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
            $userid = Session('softwareuser');
    
            $item = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();
    
            $adminid = Softwareuser::where('id', $userid)->pluck('admin_id')->first();
            $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
            // $shopdata = Adminuser::where('id', $adminid)->get();
            $shopdata = Branch::Where('id', $branch)->get();
    
            $userdata = Softwareuser::where('id', $userid)->get();
    
            $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
           
            $todaydate = \Carbon\Carbon::today()->toDateString();
            $company_name = DB::table('branches')
            ->where('id', $branch)
            ->pluck('company')
            ->first();
            // Sample Sales Data
            $sales = DB::table('buyproducts')
            ->select('customer_name as id', DB::raw('SUM(DISTINCT bill_grand_total) as total_amount'), 'created_at')
            ->where('branch', $branch)
            ->whereDate(DB::raw('DATE(created_at)'), $todaydate)
            ->groupBy('transaction_id')
            ->distinct()
            ->get();
        
            
 

            $sales_return = DB::table('returnproducts')
            ->select(
                DB::raw('SUM(DISTINCT returnproducts.grand_total) as total_amount'),
                'returnproducts.created_at',
                'buyproducts.customer_name as id'
            )
            ->join('buyproducts', 'buyproducts.transaction_id', '=', 'returnproducts.transaction_id')
            ->where('returnproducts.branch', $branch)
            ->whereDate(DB::raw('DATE(returnproducts.created_at)'), $todaydate)
            ->groupBy('returnproducts.transaction_id', 'returnproducts.created_at')
            ->distinct()
            ->get();
            
            // Sample Purchases Data
            $purchases = DB::table('stockdetails')
            ->select('supplier as id', DB::raw('SUM(price) - COALESCE(SUM(discount), 0) as total_amount'), 'created_at')
            ->where('branch', $branch)
            ->whereDate(DB::raw('DATE(created_at)'), $todaydate)
            ->groupBy('reciept_no')
            ->get();

            $purchases_return = DB::table('returnpurchases')
            ->select('shop_name as id', DB::raw('SUM(amount) - COALESCE(SUM(discount), 0) as total_amount'), 'created_at')
            ->where('branch', $branch)
            ->whereDate(DB::raw('DATE(created_at)'), $todaydate)
            ->groupBy('reciept_no')
            ->get();

            // Sample Expenses Data
            $expenses = Accountexpense::selectRaw("
            COALESCE(NULLIF(direct_expense, ''), NULLIF(indirect_expense, '')) as expense_name,amount as amount,date")
            ->where('branch', $branch)
            ->whereDate('date', $todaydate)
            ->get();

            // Sample Payments Data
            $incomes = AccountIndirectIncome::selectRaw("
            COALESCE(NULLIF(direct_income, ''), NULLIF(indirect_income, '')) as income_name,amount,date")
            ->where('branch', $branch)
            ->whereDate('date', $todaydate)
            ->get();


            $receiptcustomer = DB::table('credit_transactions')
                    ->select(
                        'credit_username as id','created_at',
                        DB::raw('collected_amount as amount')
                    )
                    ->whereIn('comment', ['invoice', 'Payment Received'])
                    ->where('collected_amount', '>', 0)
                     ->where('location', $branch)
                    ->whereDate(DB::raw('DATE(created_at)'), $todaydate)
                    ->groupBy('credituser_id')
                    ->get();
        
            $paymentcustomer = DB::table('credit_supplier_transactions')
                    ->select(
                        'credit_supplier_username as id','created_at',
                        DB::raw('collectedamount as amount')
                    )
                    ->where('comment', 'Payment Made')
                    ->where('collectedamount', '>', 0) 
                     ->where('location', $branch)
                    ->whereDate(DB::raw('DATE(created_at)'), $todaydate)
                    ->groupBy('credit_supplier_id')
                    ->get();
                    
        $service = DB::table('service')
            ->select('service_name as id', DB::raw('total_amount as total_amount'), 'created_at')
            ->where('branch', $branch)
            ->whereDate(DB::raw('DATE(created_at)'), $todaydate)
            ->get();
            
            // Calculate Totals
            $total_sales = $sales->sum('total_amount'); // 350.50
            $total_purchases = $purchases->sum('total_amount'); // 351.00
            $total_expenses = $expenses->sum('amount'); // 125.50
            $total_incomes = $incomes->sum('amount'); // 700.50
            $total_return_sales = $sales_return->sum('total_amount'); // 700.50
            $total_return_purchase = $purchases_return->sum('total_amount'); // 700.50
            $total_receiptcustomer = $receiptcustomer->sum('amount'); // 700.50
            $total_paymentcustomer = $paymentcustomer->sum('amount'); // 700.50
            $total_service = $service->sum('total_amount');


            return view('/user/daybook', [
                'users' => $item,
                'shopdatas' => $shopdata,
                'userdatas' => $userdata,
                'currency' => $currency,
                'sales' => $sales,
                'purchases' => $purchases,
                'expenses' => $expenses,
                'incomes' => $incomes,
                'total_sales' => $total_sales,
                'total_purchases' => $total_purchases,
                'total_expenses' => $total_expenses,
                'total_incomes' => $total_incomes,
                'todaydate' => $todaydate,
                'company_name' => $company_name,
                'sales_return'=>$sales_return,
                'total_return_sales'=>$total_return_sales,
                'purchases_return'=>$purchases_return,
                'total_return_purchase'=>$total_return_purchase,
                'receiptcustomer'=>$receiptcustomer,
                'total_receiptcustomer'=>$total_receiptcustomer,
                'paymentcustomer'=>$paymentcustomer,
                'total_paymentcustomer'=>$total_paymentcustomer,
                'service'=>$service,
                'total_service'=>$total_service
                
            ]);
            
        }
        public function daybookfilter(Request $request){
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
            $userid = Session('softwareuser');
    
            $item = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();
    
            $adminid = Softwareuser::where('id', $userid)->pluck('admin_id')->first();
            $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
            // $shopdata = Adminuser::where('id', $adminid)->get();
            $shopdata = Branch::Where('id', $branch)->get();
    
            $userdata = Softwareuser::where('id', $userid)->get();
    
            $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
           
            $start_date = $request->input('start_date', now()->toDateString());
            $end_date = $request->input('end_date', now()->toDateString());

            $company_name = DB::table('branches')
            ->where('id', $branch)
            ->pluck('company')
            ->first();
            // Sample Sales Data
            $sales = DB::table('buyproducts')
            ->select('customer_name as id', DB::raw('SUM(DISTINCT bill_grand_total) as total_amount'), 'created_at')
            ->where('branch', $branch)
            ->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date])
            ->groupBy('transaction_id')
            ->distinct()
            ->get();

            $sales_return = DB::table('returnproducts')
            ->select(
                DB::raw('SUM(DISTINCT returnproducts.grand_total) as total_amount'),
                'returnproducts.created_at',
                'buyproducts.customer_name as id'
            )
            ->join('buyproducts', 'buyproducts.transaction_id', '=', 'returnproducts.transaction_id')
            ->where('returnproducts.branch', $branch)
            ->whereBetween(DB::raw('DATE(returnproducts.created_at)'), [$start_date, $end_date])
            ->groupBy('returnproducts.transaction_id', 'returnproducts.created_at')
            ->distinct()
            ->get();
            
        
            
            // Sample Purchases Data
            $purchases = DB::table('stockdetails')
            ->select('supplier as id', DB::raw('SUM(price) - COALESCE(SUM(discount), 0) as total_amount'), 'created_at')
            ->where('branch', $branch)
            ->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date])
            ->groupBy('reciept_no')
            ->get();
        
            $purchases_return = DB::table('returnpurchases')
            ->select('shop_name as id', DB::raw('SUM(amount) - COALESCE(SUM(discount), 0) as total_amount'), 'created_at')
            ->where('branch', $branch)
            ->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date])
            ->groupBy('reciept_no')
            ->get();

            // Sample Expenses Data
            $expenses = Accountexpense::selectRaw("
            COALESCE(NULLIF(direct_expense, ''), NULLIF(indirect_expense, '')) as expense_name,amount as amount,date")
            ->where('branch', $branch)
            ->whereBetween('date', [$start_date, $end_date])
            ->get();
        
            // Sample Payments Data
            $incomes = AccountIndirectIncome::selectRaw("
            COALESCE(NULLIF(direct_income, ''), NULLIF(indirect_income, '')) as income_name,amount,date")
            ->where('branch', $branch)
            ->whereBetween('date', [$start_date, $end_date])
            ->get();

            $receiptcustomer = DB::table('credit_transactions')
                ->select(
                    'credit_username as id','created_at',
                    DB::raw('collected_amount as amount')
                )
                ->whereIn('comment', ['invoice', 'Payment Received'])
                ->where('collected_amount', '>', 0) 
                 ->where('location', $branch)
                ->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date])
                ->groupBy('credituser_id')
                ->get();

            $paymentcustomer = DB::table('credit_supplier_transactions')
            ->select(
                'credit_supplier_username as id','created_at',
                DB::raw('collectedamount as amount')
            )
            ->where('comment', 'Payment Made')
            ->where('collectedamount', '>', 0) 
             ->where('location', $branch)
            ->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date])
            ->groupBy('credit_supplier_id')
            ->get();

            $service = DB::table('service')
            ->select('service_name as id', DB::raw('total_amount as total_amount'), 'created_at')
            ->where('branch', $branch)
            ->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date])
            ->get();
                    
            // Calculate Totals
            $total_sales = $sales->sum('total_amount'); 
            $total_purchases = $purchases->sum('total_amount'); 
            $total_expenses = $expenses->sum('amount'); 
            $total_incomes = $incomes->sum('amount'); 
            $total_return_sales = $sales_return->sum('total_amount'); 
            $total_return_purchase = $purchases_return->sum('total_amount');
            $total_receiptcustomer = $receiptcustomer->sum('amount'); 
            $total_paymentcustomer = $paymentcustomer->sum('amount'); 
            $total_service = $service->sum('total_amount');

            return view('/user/daybook', [
                'users' => $item,
                'shopdatas' => $shopdata,
                'userdatas' => $userdata,
                'currency' => $currency,
                'sales' => $sales,
                'purchases' => $purchases,
                'expenses' => $expenses,
                'incomes' => $incomes,
                'total_sales' => $total_sales,
                'total_purchases' => $total_purchases,
                'total_expenses' => $total_expenses,
                'total_incomes' => $total_incomes,
                'company_name' => $company_name,
                'sales_return'=>$sales_return,
                'total_return_sales'=>$total_return_sales,
                'purchases_return'=>$purchases_return,
                'total_return_purchase'=>$total_return_purchase,
                'receiptcustomer'=>$receiptcustomer,
                'total_receiptcustomer'=>$total_receiptcustomer,
                'paymentcustomer'=>$paymentcustomer,
                'total_paymentcustomer'=>$total_paymentcustomer,
                'service'=>$service,
                'total_service'=>$total_service
                
            ]);
            
        }

}
