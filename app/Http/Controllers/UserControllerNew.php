<?php

namespace App\Http\Controllers;

use App\Exports\exportPurchaseReturnReport;
use App\Exports\OverallPurchaseExport;
use App\Exports\OverallPurchaseReturnExport;
use App\Exports\OverallSalesExport;
use App\Exports\OverallSalesReturnReport;
use App\Exports\ProductsExport;
use App\Exports\PurchaseExport;
use App\Exports\StockListExport;
use App\Exports\SalesExport;
use App\Exports\ProductSalesExport;
use App\Exports\SalesReturnExport;
use App\Imports\ProductsImport;
use App\Exports\TransactionsExport;
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
use App\Models\Chartofaccountants;
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
use App\Models\NewStockdetail;
use App\Models\Stockhistory;
use App\Models\StockPurchaseReport;
use App\Models\Supplier;
use App\Models\SupplierCredit;
use App\Models\SupplierFundHistory;
use App\Models\Termsandcondition;
use App\Models\TotalExpense;
use App\Models\TransferType;
use App\Models\ExtraProducts;
use App\Models\Unit;
use App\Models\UserReport;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use App\Models\PandL;
use App\Models\JournalEntry;
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
use Illuminate\Support\Facades\File;
use App\Exports\CategorywiseExport;
use PDF;

use App\Services\JournalEntryService;
use App\Models\JournalTransaction;


// Includes WebClientPrint classes
require_once app_path().'/WebClientPrint/WebClientPrint.php';

use Neodynamic\SDK\Web\WebClientPrint;

class UserControllerNew extends Controller
{
      public function barcode_print($purchase_id)
        {
            $userid = Session('softwareuser');
            $adminid = Softwareuser::where('id', $userid)->pluck('admin_id')->first();
            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();
        
            $currency = Adminuser::Where('id', $adminid)
                ->pluck('currency')
                ->first();
            $company = DB::table('branches')
                ->where('id', $branch)
                ->pluck('company')
                ->first();
        
            // Get selected product IDs from query parameter (if provided)
            $selectedProductIds = request()->query('products')
                ? explode(',', request()->query('products'))
                : [];
        
            // Step 1: Get product values from stockdetails (filter by selected products if provided)
            $stockDetailsQuery = DB::table('stockdetails')
                ->where('reciept_no', $purchase_id);
        
            // Apply filter only if specific products were selected
            if (!empty($selectedProductIds)) {
                $stockDetailsQuery->whereIn('product', $selectedProductIds);
            }
        
            $stockDetails = $stockDetailsQuery->select('product', 'quantity', 'sellingcost')->get();
        
            $productIds = $stockDetails->pluck('product');
        
            // Get product details
            $products = DB::table('products')
                ->whereIn('id', $productIds)
                ->select('id', 'product_code', 'selling_cost', 'barcode', 'product_name')
                ->get()
                ->keyBy('id');
        
            // Create barcode data array
            $barcodeData = [];
            foreach ($stockDetails as $stock) {
                if (isset($products[$stock->product])) {
                    $product = $products[$stock->product];
                    $barcodeData[] = [
                        'product_code' => $product->product_code,
                        'selling_cost' => $stock->sellingcost,
                        'product_name' => $product->product_name,
                        'barcode' => $product->barcode,
                        'quantity' => $stock->quantity
                    ];
                }
            }
        
            $data = compact('barcodeData', 'currency', 'company');
            return view('pdf.barcode_print', $data);
        }

 public function printBarcode(Request $request)
            {

                $receiptNo = $request->query('receipt_no');
                $quantity = (int) $request->query('quantity'); // Ensure it's an integer
                $product_name = $request->query('product_name');


                $product_code = Product::where('id', $product_name)->pluck('product_code')->first();
                $barcode = Product::where('id', $product_name)->pluck('barcode')->first();

                $userid = Session('softwareuser');


                $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

                $adminid = Softwareuser::where('id', $userid)->pluck('admin_id')->first();

                    $currency = Adminuser::Where('id', $adminid)
                    ->pluck('currency')
                    ->first();

                    $company = DB::table('branches')
                    ->where('id', $branch)
                    ->pluck('company')
                    ->first();

                     $selling_cost = DB::table('stockdetails')
                    ->where('product', $product_name)
                    ->where('reciept_no', $receiptNo)
                    ->pluck('sellingcost')
                    ->first();
                    $product_name_o = Product::where('id', $product_name)->pluck('product_name')->first();

                    $barcodes = array_fill(0, $quantity, $barcode);

                    return view('pdf.barcode_view', compact(
                        'receiptNo',
                        'quantity',
                        'product_code',
                        'currency',
                        'company',
                        'selling_cost',
                        'barcodes',
                        'barcode',
                        'product_name_o'
                    ));

    }
     public function productprofit(){
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



        $currency = Adminuser::Where('id', $adminid)
        ->pluck('currency')
        ->first();

        $products = DB::table('buyproducts')
        ->join('products', 'buyproducts.product_id', '=', 'products.id')
        ->select(
            'buyproducts.product_name',
            DB::raw('SUM(buyproducts.remain_quantity) as remain_quantity'),
            DB::raw('COALESCE(products.rate, 0) as rate'),
            DB::raw('ROUND(SUM((netrate - (netrate * (COALESCE(total_discount_percent, 0) / 100))) * remain_quantity)) as total_amount'),
            DB::raw('ROUND(SUM((netrate - (netrate * (COALESCE(total_discount_percent, 0) / 100))) * remain_quantity) - SUM(COALESCE(one_pro_buycost_rate, 0) * remain_quantity)) as profit')
            )
        ->where('buyproducts.branch', $branch)
        ->groupBy('buyproducts.product_id')
        ->get();


        $start_date='';
        $end_date='';
        return view('/billingdesk/productprofit', [
            'users' => $item,
            'shopdatas' => $shopdata,
            'currency'=>$currency,
            'products'=>$products,
            'start_date'=>$start_date,
            'end_date'=>$end_date,

        ]);
    }
     public function productprofitfilter(Request $request){
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

        $shopdata = Branch::Where('id', $branch)->get();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        // Get date filters from request or set defaults
        $startDate = $request->input('start_date', date('Y-m-01')); // Default to first day of current month
        $endDate = $request->input('end_date', date('Y-m-d'));     // Default to today



        $products = DB::table('buyproducts')
        ->join('products', 'buyproducts.product_id', '=', 'products.id')
        ->select(
            'buyproducts.product_name',
            DB::raw('SUM(buyproducts.remain_quantity) as remain_quantity'),
            DB::raw('COALESCE(products.rate, 0) as rate'),
            DB::raw('ROUND(SUM((netrate - (netrate * (COALESCE(total_discount_percent, 0) / 100))) * remain_quantity)) as total_amount'),
            DB::raw('ROUND(SUM((netrate - (netrate * (COALESCE(total_discount_percent, 0) / 100))) * remain_quantity) - SUM(COALESCE(one_pro_buycost_rate, 0) * remain_quantity)) as profit')
            )
            ->where('buyproducts.branch', $branch)
            ->whereDate('buyproducts.created_at', '>=', $startDate)
            ->whereDate('buyproducts.created_at', '<=', $endDate)
            ->groupBy('buyproducts.product_id')
            ->get();

        return view('/billingdesk/productprofit', [
            'users' => $item,
            'shopdatas' => $shopdata,
            'currency'=>$currency,
            'products'=>$products,
            'start_date'=>$startDate,
            'end_date'=>$endDate,

        ]);
    }
     public function exportproductreport()
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $products = DB::table('buyproducts')
            ->join('products', 'buyproducts.product_id', '=', 'products.id')
            ->select(
                'buyproducts.product_name',
                DB::raw('SUM(buyproducts.remain_quantity) as remain_quantity'),
                DB::raw('COALESCE(products.rate, 0) as rate'),
                DB::raw('ROUND(SUM((netrate - (netrate * (COALESCE(total_discount_percent, 0) / 100))) * remain_quantity)) as total_amount'),
                DB::raw('ROUND(SUM((netrate - (netrate * (COALESCE(total_discount_percent, 0) / 100))) * remain_quantity) - SUM(COALESCE(one_pro_buycost_rate, 0) * remain_quantity)) as profit')
            )
            ->where('buyproducts.branch', $branch)
            ->groupBy('buyproducts.product_id')
            ->get();

        $totalProfit = $products->sum('profit');
        $totalSold = $products->sum('total_amount');

        return Excel::download(new ProductSalesExport($products, $totalProfit, $totalSold), 'product_sales_report.xlsx');
    }

    public function printproductreport()
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
            $company = DB::table('branches')
            ->where('id', $branch)
            ->pluck('company')
            ->first();
        $products = DB::table('buyproducts')
            ->join('products', 'buyproducts.product_id', '=', 'products.id')
            ->select(
                'buyproducts.product_name',
                DB::raw('SUM(buyproducts.remain_quantity) as remain_quantity'),
                DB::raw('COALESCE(products.rate, 0) as rate'),
                DB::raw('ROUND(SUM((netrate - (netrate * (COALESCE(total_discount_percent, 0) / 100))) * remain_quantity)) as total_amount'),
                DB::raw('ROUND(SUM((netrate - (netrate * (COALESCE(total_discount_percent, 0) / 100))) * remain_quantity) - SUM(COALESCE(one_pro_buycost_rate, 0) * remain_quantity)) as profit')
            )
            ->where('buyproducts.branch', $branch)
            ->groupBy('buyproducts.product_id')
            ->get();

        $totalProfit = $products->sum('profit');
        $totalSold = $products->sum('total_amount');

        return view('pdf.product_profit', compact('company','products', 'totalProfit', 'totalSold'));
    }
    public function exportTransactions(Request $request)
    {
        // Validate request inputs
        $validated = $request->validate([
            'payment_type' => 'nullable|string',
            'date_filter' => 'nullable|string',
            'branch' => 'nullable|string' // allow admin to select a branch
        ]);

        // Payment type mapping
        $paymentTypeMap = [
            'Cash' => 1,
            'Bank' => 2,
            'Credit' => 3,
            'POS Card' => 4,
        ];

        // Determine branch based on input or session
        $branch = $request->filled('branch')
            ? $request->branch
            : DB::table('softwareusers')->where('id', Session('softwareuser'))->value('location');

        \Log::debug('Export Initialization:', [
            'branch' => $branch,
            'request_data' => $request->all()
        ]);

        $paymentType = $request->filled('payment_type') ? trim($request->payment_type) : null;
        $paymentTypeId = $paymentType ? ($paymentTypeMap[$paymentType] ?? null) : null;

        // Build query
        $query = DB::table('buyproducts')
            ->select([
                'buyproducts.transaction_id',
                'buyproducts.branch',
               DB::raw('
                    CASE 
                        WHEN SUM(buyproducts.totalamount_wo_discount) != 0 
                        THEN SUM(buyproducts.totalamount_wo_discount)
                        ELSE SUM(DISTINCT COALESCE(bill_grand_total, 0))
                    END AS grandtotal_withdiscount
                '),

                DB::raw('SUM(buyproducts.vat_amount) as vat'),
                DB::raw('
                    SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) +
                    SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
                '),
                DB::raw('
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum
                    '),

                DB::raw('MAX(buyproducts.created_at) as created_at'),
                DB::raw('MAX(buyproducts.customer_name) as customer_name'),
                DB::raw('MAX(buyproducts.payment_type) as payment_type'),
                DB::raw('MAX(buyproducts.vat_type) as vat_type')
            ])
            ->when($branch, function ($q) use ($branch) {
                return $q->where('buyproducts.branch', $branch);
            })
            ->when($paymentTypeId, function ($q) use ($paymentTypeId) {
                return $q->where('payment_type', $paymentTypeId);
            })
            ->when($request->date_filter && $request->date_filter !== 'all', function ($q) use ($request) {
                return $request->date_filter === 'today'
                    ? $q->whereDate('created_at', now()->toDateString())
                    : $q->whereDate('created_at', $request->date_filter);
            })
            ->groupBy('buyproducts.transaction_id', 'buyproducts.branch') // Include branch in group
            ->orderByDesc(DB::raw('MAX(buyproducts.created_at)'));

        $results = $query->get();

        $reversePaymentTypeMap = array_flip($paymentTypeMap);
        $results = $results->map(function ($item) use ($reversePaymentTypeMap) {
            $item->payment_type = $reversePaymentTypeMap[$item->payment_type] ?? $item->payment_type;
            return $item;
        });

        if ($results->isEmpty()) {
            return back()->with('error', 'No transactions found matching your criteria');
        }

        $tax = 'TAX';
        $users = DB::table('users')->get();

        return Excel::download(
            new TransactionsExport($results, $tax, $users),
            'transactions_' .
            ($branch ? 'branch_' . $branch . '_' : '') .
            ($paymentType ? strtolower(str_replace(' ', '_', $paymentType)) . '_' : '') .
            now()->format('Ymd_His') . '.xlsx'
        );
    }
    public function exportcategoryreport()
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

$products = DB::table('buyproducts')
    ->join('products', 'buyproducts.product_id', '=', 'products.id')
    ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
    ->select(
        DB::raw('COALESCE(categories.category_name, "Uncategorized") as category_name'),
        DB::raw('COALESCE(products.category_id, 0) as category_id'),
        DB::raw('SUM(buyproducts.remain_quantity) as remain_quantity'),
        DB::raw('COUNT(DISTINCT buyproducts.product_name) as product_count'),
        DB::raw('SUM(COALESCE(products.rate, 0)) as rate'),
        DB::raw('ROUND(SUM((netrate - (netrate * (COALESCE(total_discount_percent, 0) / 100))) * remain_quantity)) as total_amount'),
        DB::raw('ROUND(SUM((netrate - (netrate * (COALESCE(total_discount_percent, 0) / 100))) * remain_quantity) - SUM(COALESCE(one_pro_buycost_rate, 0) * remain_quantity)) as profit')
    )
    ->where('buyproducts.branch', $branch)
    ->groupBy(DB::raw('COALESCE(products.category_id, 0)'), DB::raw('COALESCE(categories.category_name, "Uncategorized")')) // Combined into one groupBy()
    ->get();


        $totalProfit = $products->sum('profit');
        $totalSold = $products->sum('total_amount');


        return Excel::download(new CategorywiseExport($products, $totalProfit, $totalSold), 'category_wise_report.xlsx');
    }
       public function exportStockListExcel()
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $products = DB::table('products')
            ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
            ->select(DB::raw('products.id as id,products.rate as rate,products.product_name as product_name,products.stock as stock, SUM(stockdats.stock_num) as stock_num, products.remaining_stock as remaining_stock'))
            ->groupBy('products.id')
            ->where('products.branch', $branch)
            ->where('products.status', 1)
            ->orderBy('products.id')
            ->get();

        $totalValue = 0;
        foreach($products as $product) {
            $totalValue += max(0, $product->remaining_stock) * $product->rate;
        }

        return Excel::download(new StockListExport($products, $totalValue, $branch), 'stock_list.xlsx');
    }

    public function printStockList()
    {
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

            $company = DB::table('branches')
            ->where('id', $branch)
            ->pluck('company')
            ->first();

        $products = DB::table('products')
            ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
            ->select(DB::raw('products.id as id,products.rate as rate,products.product_name as product_name,products.stock as stock, SUM(stockdats.stock_num) as stock_num, products.remaining_stock as remaining_stock'))
            ->groupBy('products.id')
            ->where('products.branch', $branch)
            ->where('products.status', 1)
            ->orderBy('products.id')
            ->get();

        $totalValue = 0;
        foreach($products as $product) {
            $totalValue += max(0, $product->remaining_stock) * $product->rate;
        }

        return view('pdf.stock-list', compact('company','products', 'totalValue', 'branch'));
    }
      public function customerstatus(){
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


        $purchases = DB::table('credit_transactions')
        ->leftJoin('creditusers', 'credit_transactions.credituser_id', '=', 'creditusers.id') // Join with creditusers
        ->leftJoin('buyproducts', DB::raw("CONVERT(buyproducts.transaction_id USING utf8mb4) COLLATE utf8mb4_unicode_ci"), '=', DB::raw("CONVERT(credit_transactions.transaction_id USING utf8mb4) COLLATE utf8mb4_unicode_ci")) // Join with buyproducts using transaction_id
        ->select(
            'credit_transactions.*', // Select all from credit_transactions
            'creditusers.phone',
            'creditusers.email',
            'credit_transactions.transaction_id',
            'credit_transactions.comment',
            'creditusers.business_name',
            'creditusers.billing_add',
            'buyproducts.quantity',
            'buyproducts.netrate',
            'buyproducts.created_at as invoice_date',
            DB::raw('DATE_ADD(credit_transactions.created_at, INTERVAL COALESCE(credit_transactions.due_days, 30) DAY) as due_date'),
            DB::raw('SUM(DISTINCT buyproducts.bill_grand_total) as total'), // Total from buyproducts
            DB::raw('(SELECT balance_due FROM credit_transactions ct WHERE ct.transaction_id = credit_transactions.transaction_id ORDER BY ct.created_at DESC LIMIT 1) as total_due'), // Subquery to get the latest balance_due for the same transaction_id
            DB::raw('(SELECT balance_due FROM credit_transactions ct WHERE ct.transaction_id = credit_transactions.transaction_id ORDER BY ct.created_at ASC LIMIT 1) as previous_invoice_due'), // Subquery to get the first balance_due for the same transaction_id
        )
        ->where('credit_transactions.location', $branch)
        ->groupBy('credit_transactions.transaction_id') // Group by the credit_transactions ID to allow aggregation (SUM) to work properly
        ->orderBy('credit_transactions.created_at', 'DESC')
        ->get();
        
        $productDetails = DB::table('buyproducts')
            ->join('products', 'buyproducts.product_id', '=', 'products.id')
            ->leftJoin('creditusers', function($join) {
                $join->on('buyproducts.credit_user_id', '=', 'creditusers.id')
                     ->orOn('buyproducts.cash_user_id', '=', 'creditusers.id');
            })
            ->where('buyproducts.branch', $branch)
            ->where(function($query) {
                $query->whereNotNull('buyproducts.credit_user_id')
                      ->orWhereNotNull('buyproducts.cash_user_id');
            })
            ->select(
                'creditusers.name as customer_name',
                'products.product_name',
                DB::raw('SUM(buyproducts.remain_quantity) as total_quantity')
            )
        ->groupBy('creditusers.id', 'products.id')
        ->orderBy('creditusers.id')
        ->orderBy('products.id')
        ->get();


        return view('/billingdesk/customerstatus', [
            'users' => $item,
            'shopdatas' => $shopdata,
            'userdatas' => $userdata,
            'currency'=>$currency,
            'purchases'=>$purchases,
            'productDetails'=>$productDetails
        ]);
    }


     public function chequesummary(){
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

        $cheques = DB::table('credit_transactions')
        ->select(
            'credit_username as customer',
            DB::raw('NULL as supplier'), // No supplier name for this table
            'collected_amount as amount',
            'cheque_number as cheque',
            'depositing_date as depositing_date',
            DB::raw("'customer' as type") // Add a type to distinguish
        )
        ->whereNotNull('collected_amount')
        ->whereNotNull('cheque_number')
        ->whereNotNull('depositing_date')
        ->where('location', $branch)

        ->union( // Union with credit_supplier_transactions
            DB::table('credit_supplier_transactions')
                ->select(
                    DB::raw('NULL as customer'), // No customer name for this table
                    'credit_supplier_username as supplier',
                    'collectedamount as amount',
                    'check_number as cheque',
                    'depositing_date as depositing_date',
                    DB::raw("'supplier' as type") // Add a type to distinguish
                )
                ->whereNotNull('collectedamount')
                ->whereNotNull('check_number')
                ->whereNotNull('depositing_date')
                ->where('location', $branch)
        )
        ->get();

        return view('/billingdesk/chequesummary', [
            'users' => $item,
            'shopdatas' => $shopdata,
            'userdatas' => $userdata,
            'currency'=>$currency,
            'cheques'=>$cheques
        ]);
    }
    public function chartaccounts()
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
            $date = Carbon::now()->format('Y-m-d');

        $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();


        $branches = Accountantloc::leftJoin('branches', 'accountantlocs.location_id', '=', 'branches.id')
            ->where('accountantlocs.user_id', $userid)
            ->get();

        return view('/chartaccounts/chartofaccount', ['branches'=>$branches,'users' => $item, 'start_date' => $date, 'branch' => $branch]);
    }

    public function storechartofaccounts(Request $request)
    {
        // Check if all inputs are empty
        if (
            !$request->has('asset_type') &&
            !$request->has('capital_type') &&
            !$request->has('liability_type')
        ) {
            return redirect()->back()->with('error', 'Please fill at least one category before submitting.');
        }
        $userid = Session('softwareuser');

        $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();

        $data = [];

        // Handle Asset Type Entries
        if ($request->has('asset_type')) {
            foreach ($request->asset_type as $key => $value) {
                if (!empty($value)) {
                    $data[] = [
                        'type' => 'Asset',
                        'sub_type' => $value,
                        'user_id' => $userid,
                        'branch' => $branch,
                        'type_category' => $request->asset_category[$key] ?? null,
                        'type_name' => $request->asset_name[$key] ?? null,
                        'type_amount' => $request->asset_amount[$key] ?? null,
                        'type_details' => $request->asset_details[$key] ?? null,
                        'type_date' => $request->asset_date[$key] ?? null,
                    ];
                }
            }
        }

        // Handle Capital Type Entries
        if ($request->has('capital_type')) {
            foreach ($request->capital_type as $key => $value) {
                if (!empty($value)) {
                    $data[] = [
                        'type' => 'Capital',
                        'sub_type' => $value,
                        'user_id' => $userid,
                        'branch' => $branch,
                        'type_category' => $request->capital_category[$key] ?? null,
                        'type_name' => $request->capital_name[$key] ?? null,
                        'type_amount' => $request->capital_amount[$key] ?? null,
                        'type_details' => $request->capital_details[$key] ?? null,
                        'type_date' => $request->capital_date[$key] ?? null,
                    ];
                }
            }
        }

        // Handle Liability Type Entries (Ensure 'type_name' is included)
        if ($request->has('liability_type')) {
            foreach ($request->liability_type as $key => $value) {
                if (!empty($value)) {
                    $data[] = [
                        'type' => 'Liability',
                        'sub_type' => $value,
                        'user_id' => $userid,
                        'branch' => $branch,
                        'type_category' => $request->liability_category[$key] ?? null,
                        'type_name' => $request->liability_name[$key] ?? null, // Ensure this column is filled
                        'type_amount' => $request->liability_amount[$key] ?? null,
                        'type_details' => $request->liability_details[$key] ?? null,
                        'type_date' => $request->liability_date[$key] ?? null,
                    ];
                }
            }
        }

        // If no valid data is found, return an error message
        if (empty($data)) {
            return redirect()->back()->with('error', 'No valid data to store.');
        }

        // Insert data into the database
        Chartofaccountants::insert($data);

        return redirect()->back()->with('success', 'Data stored successfully!');
    }

    public function assethistory()
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
        $shopdata = Branch::Where('id', $branch)->get();

        $assets = Chartofaccountants::where('type', 'Asset')->where('branch',$branch)->get();



        return view('/chartaccounts/assethistory', ['users' => $item, 'branch' => $branch,'shopdatas'=>$shopdata,'assets'=>$assets]);
    }
    public function capitalhistory()
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
        $shopdata = Branch::Where('id', $branch)->get();

        $capitals = Chartofaccountants::where('type', 'Capital')->where('branch',$branch)->get();



        return view('/chartaccounts/capitalhistory', ['users' => $item, 'branch' => $branch,'shopdatas'=>$shopdata,'capitals'=>$capitals]);
    }

    public function liabilityhistory()
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
        $shopdata = Branch::Where('id', $branch)->get();
        $liabilities = Chartofaccountants::where('type', 'Liability')->where('branch',$branch)->get();




        return view('/chartaccounts/liabilityhistory', ['users' => $item, 'branch' => $branch,'shopdatas'=>$shopdata,'liabilities'=>$liabilities]);
    }

    public function trailbalance()
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
        $shopdata = Branch::Where('id', $branch)->get();

        $adminid = Softwareuser::where('id', $userid)->pluck('admin_id')->first();

        $currency = Adminuser::Where('id', $adminid)
        ->pluck('currency')
        ->first();


        // $cashsaleTotal = Buyproduct::selectRaw('SUM(DISTINCT COALESCE(bill_grand_total,0)) as total')
        // ->where('branch', $branch)
        // ->where('payment_type', 1)
        // ->groupBy('transaction_id')
        // ->pluck('total')
        // ->sum();

        $cashsaleTotal = Buyproduct::where('branch', $branch)
            ->where('payment_type', 1)
            ->groupBy('transaction_id', 'vat_type') // Group by both transaction_id and vat_type
            ->selectRaw('
                vat_type,
                CASE
                    WHEN vat_type = 1 AND SUM(COALESCE(totalamount_wo_discount, 0)) > 0
                        THEN SUM(totalamount_wo_discount) - SUM(COALESCE(discount_amount * quantity, 0))
                    WHEN vat_type = 1
                        THEN SUM(DISTINCT COALESCE(bill_grand_total, 0)) - SUM(COALESCE(discount_amount * quantity, 0))
                    WHEN vat_type = 2
                        THEN SUM(netrate * quantity)
                END as net_total
            ')
            ->get()
            ->sum('net_total');

        $totalIncomecash = AccountIndirectIncome::selectRaw("SUM(COALESCE(amount,0)) as total_amount")
        ->where('branch', $branch)
        ->whereNull('bank_id')
        ->value('total_amount')?? 0;

        $customerpaymentcash = CreditTransaction::selectRaw('SUM(COALESCE(collected_amount, 0)) as total')
            ->where('location', $branch)
            ->where(function ($query) {
                $query->where('payment_type', 1)
                      ->orWhereNull('payment_type'); // Include NULL values
            })
             ->where('comment', '!=', 'Product Returned')
            ->groupBy('credituser_id')
            ->pluck('total')
            ->sum();

        $capitalSum = Chartofaccountants::where('type', 'Capital')
        ->where('branch', $branch)
        ->sum('type_amount');

        $liabilitySum = Chartofaccountants::where('type', 'Liability')
        ->where('branch', $branch)
        ->sum('type_amount');

        $totalexpensecash = Accountexpense::selectRaw("SUM(COALESCE(amount,0)) as total_amount")
            ->where('branch', $branch)
            ->whereNull('bank_id')
            ->value('total_amount')?? 0;

        $assetSum = Chartofaccountants::where('type', 'Asset')
            ->where('branch', $branch)
            ->sum('type_amount');

        $purchasecashcredit = CreditSupplierTransaction::selectRaw('SUM(COALESCE(collectedamount, 0)) as total')
            ->where('location', $branch)
            ->where(function ($query) {
                $query->where('payment_type', 1)
                      ->orWhereNull('payment_type'); // Include NULL values
            })
            ->where('comment', '!=', 'Purchase Returned ')
            ->groupBy('credit_supplier_id')
            ->pluck('total')
            ->sum();

        $purchasecashTotal = Stockdetail::selectRaw("
            SUM(price) as total_value")
            ->where('payment_mode', 1)
            ->where('branch',$branch)
                ->value('total_value');

        $salereturnData = Returnproduct::where('branch', $branch)
            ->select(
                'return_id',
                DB::raw('SUM(COALESCE(vat_amount, 0)) as vat'),
                DB::raw('SUM(DISTINCT COALESCE(grand_total_wo_discount, 0)) - SUM(DISTINCT COALESCE(total_discount_amount, 0)) as net_total')
            )
            ->groupBy('return_id')
            ->get();

        $salereturn = $salereturnData->sum('net_total');
        $salereturnvat = $salereturnData->sum('vat');

        $salesreturnwithoutvat=$salereturn - $salereturnvat;


        $purchasereturnData = Returnpurchase::where('branch', $branch)
            ->select(
                DB::raw('SUM(amount - amount_without_vat) as vat'),
                DB::raw('SUM(amount) - SUM(COALESCE(discount, 0)) as net_total')
            )
            ->groupBy('reciept_no', 'created_at')
            ->get();

        $purchasereturn = $purchasereturnData->sum('net_total');
        $purchasereturnvat = $purchasereturnData->sum('vat');

        $purchasesreturnwithoutvat=$purchasereturn - $purchasereturnvat;

        $paymentTypes = [1, 2, 3];
        $discountResults = [];

        foreach ($paymentTypes as $type) {
            $discountResults[$type] = Buyproduct::where('branch', $branch)
                ->where('payment_type', $type)
                ->groupBy('transaction_id')
                ->selectRaw('SUM(COALESCE(discount_amount * quantity, 0))
                    + SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount')
                ->get()
                ->sum('discount_amount');
        }

        $cashsalediscount = $discountResults[1] ?? 0;
        $banksalediscount = $discountResults[2] ?? 0;
        $creditsalediscount = $discountResults[3] ?? 0;

        $totaldiscount=$cashsalediscount+$creditsalediscount+$banksalediscount;


        $paymentmodes = [1, 2, 3];
        $purdiscountResults = [];

        foreach ($paymentmodes as $type) {
            $purdiscountResults[$type] = Stockdetail::where('branch', $branch)
                ->where('payment_mode', $type)
                ->groupBy('reciept_no')
                ->selectRaw('SUM(stockdetails.discount) as discount_amount')
                ->get()
                ->sum('discount_amount');
        }

        $cashpurchasediscount = $purdiscountResults[1] ?? 0;
        $creditpurchasediscount = $purdiscountResults[2] ?? 0;
        $bankpurchasediscount = $purdiscountResults[3] ?? 0;

        $totaldiscountpurchase=$cashpurchasediscount+$creditpurchasediscount+$bankpurchasediscount;

        $paymentTypess = [1, 2, 3, 4];
        $discountResultss = [];

        foreach ($paymentTypess as $type) {
            $discountResultss[$type] = Buyproduct::where('branch', $branch)
                ->where('payment_type', $type)
                ->groupBy('transaction_id')
                ->selectRaw('SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount')
                ->get()
                ->sum('discount_amount');
        }

        $cashsaletotaldiscount = $discountResultss[1] ?? 0;
        $banksaletotaldiscount = ($discountResultss[2] ?? 0) + ($discountResultss[4] ?? 0);
        $creditsaletotaldiscount = $discountResultss[3] ?? 0;

        $cashinhanddebit=($cashsaleTotal+$totalIncomecash+$customerpaymentcash+$capitalSum+$liabilitySum+$purchasereturn) - ($cashsaletotaldiscount);

        $cashinhandcredit=($purchasecashTotal-$cashpurchasediscount)+$purchasecashcredit+$assetSum+$totalexpensecash+$salereturn;

        $banksaleTotal = Buyproduct::where('branch', $branch)
            ->whereIn('payment_type', [2, 4])
            ->groupBy('transaction_id', 'vat_type') // Group by both transaction_id and vat_type
            ->selectRaw('
                vat_type,
                CASE
                    WHEN vat_type = 1 AND SUM(COALESCE(totalamount_wo_discount, 0)) > 0
                        THEN SUM(totalamount_wo_discount) - SUM(COALESCE(discount_amount * quantity, 0))
                    WHEN vat_type = 1
                        THEN SUM(DISTINCT COALESCE(bill_grand_total, 0)) - SUM(COALESCE(discount_amount * quantity, 0))
                    WHEN vat_type = 2
                        THEN SUM(netrate * quantity)
                END as net_total
            ')
            ->get()
            ->sum('net_total');

        $totalIncomebank = AccountIndirectIncome::selectRaw("SUM(COALESCE(amount,0)) as total_amount")
        ->where('branch', $branch)
        ->whereNotNull('bank_id') // Ensures bankid is not null
        ->value('total_amount')?? 0;


        $salebankcredit = CreditTransaction::selectRaw('SUM(COALESCE(collected_amount,0)) as total')
            ->where('location', $branch)
            ->whereIn('payment_type', [2, 3])
            ->groupBy('credituser_id')
            ->pluck('total')
            ->sum();


        $totalexpensebank = Accountexpense::selectRaw("SUM(COALESCE(amount,0)) as total_amount")
            ->where('branch', $branch)
            ->whereNotNull('bank_id') // Ensures bankid is not null
            ->value('total_amount')?? 0;

        $purchasebankcredit = CreditSupplierTransaction::selectRaw('SUM(COALESCE(collectedamount,0)) as total')
            ->where('location', $branch)
            ->whereIn('payment_type', [2, 3])
            ->groupBy('credit_supplier_id')
            ->pluck('total')
            ->sum();

        $purchasebankTotal = Stockdetail::selectRaw("SUM(price) - COALESCE(SUM(discount), 0) as total_value")
            ->where('payment_mode', 3)
            ->where('branch',$branch)
            ->value('total_value');
                

        $bankbalance = Bank::where('branch', $branch)
            ->sum('opening_balance');

        $bankdebit=($banksaleTotal+$totalIncomebank+$salebankcredit+$bankbalance)-($banksaletotaldiscount);
        $bankcredit=$totalexpensebank+$purchasebankcredit+$purchasebankTotal;

        $credit = DB::table('creditsummaries')
            ->join('creditusers', 'creditsummaries.credituser_id', '=', 'creditusers.id')
            ->where('creditusers.location', $branch)
            ->select(DB::raw('COALESCE(SUM(due_amount - collected_amount), 0) as amount_difference'))
            ->value('amount_difference');

        $supplier = DB::table('supplier_credits')
            ->join('suppliers', 'supplier_credits.supplier_id', '=', 'suppliers.id')
            ->where('suppliers.location', $branch)
            ->select(DB::raw('COALESCE(SUM(due_amt - collected_amt), 0) as amount_difference'))
            ->value('amount_difference');

        $currentassetSum = Chartofaccountants::where('type', 'Asset')
            ->where('branch', $branch)
            ->where('sub_type', 'current')
            ->sum('type_amount');


        $fixedassetSum = Chartofaccountants::where('type', 'Asset')
            ->where('branch', $branch)
            ->where('sub_type', 'fixed')
            ->sum('type_amount');


        $purchasevat = Stockdetail::where('branch', $branch)
            ->selectRaw('SUM(price) - SUM(price_without_vat) as total')
            ->value('total');

        $salevat = Buyproduct::where('branch', $branch)
            ->sum('vat_amount');

        $longliabilitySum = Chartofaccountants::where('type', 'Liability')
            ->where('branch', $branch)
            ->where('sub_type', 'Long-term Liabilities')
            ->sum('type_amount');


        $shortliabilitySum = Chartofaccountants::where('type', 'Liability')
            ->where('branch', $branch)
            ->where('sub_type', 'Short-term Liabilities')
            ->sum('type_amount');

        $ownerscapitalSum = Chartofaccountants::where('type', 'Capital')
            ->where('branch', $branch)
            ->where('sub_type', 'Owner’s & Partner’s Capital')
            ->sum('type_amount');

        $investmentcapitalSum = Chartofaccountants::where('type', 'Capital')
            ->where('branch', $branch)
            ->where('sub_type', 'Investment & Reserves')
            ->sum('type_amount');

        $salefullTotal = Buyproduct::where('branch', $branch)
            ->groupBy('transaction_id', 'vat_type') // Group by both transaction_id and vat_type
            ->selectRaw('
                vat_type,
                SUM(mrp * quantity) as subtotal,
                SUM(price) as price,
                SUM(vat_amount) as total_vat,
                SUM(COALESCE(discount_amount * quantity, 0)) as item_discounts,
                SUM(DISTINCT COALESCE(total_discount_amount, 0)) as transaction_discount')
            ->get()
            ->reduce(function ($carry, $item) {
                if ($item->vat_type == 1) {
                    return $carry + ($item->price + $item->item_discounts) ;
                } else {
                    return $carry + $item->subtotal;
                }
            }, 0);

        $totalIncome = AccountIndirectIncome::where('branch', $branch)
            ->sum('amount');

        $purchasefullTotal = Stockdetail::selectRaw("SUM(COALESCE(price_without_vat,0)) as total_value")
            ->where('branch',$branch)
            ->value('total_value');

        $totalexpenses = Accountexpense::where('branch', $branch)
            ->sum('amount');

        $data = [
            'users' => $item,
            'branch' => $branch,
            'shopdatas' => $shopdata,
            'currency' => $currency,

            // Cash related calculations
            'cashsaleTotal' => $cashsaleTotal,
            'totalIncomecash' => $totalIncomecash,
            'customerpaymentcash' => $customerpaymentcash,
            'capitalSum' => $capitalSum,
            'liabilitySum' => $liabilitySum,
            'totalexpensecash' => $totalexpensecash,
            'assetSum' => $assetSum,
            'purchasecashcredit' => $purchasecashcredit,
            'purchasecashTotal' => $purchasecashTotal,
            'cashinhandcredit' => $cashinhandcredit,
            'cashinhanddebit' => $cashinhanddebit,

            'salereturn'=>$salereturn,
            'salereturnvat'=>$salereturnvat,               
            'salesreturnwithoutvat'=>$salesreturnwithoutvat,

            'purchasereturn'=>$purchasereturn,
            'purchasereturnvat'=>$purchasereturnvat,
            'purchasesreturnwithoutvat'=>$purchasesreturnwithoutvat,
                
            'cashsalediscount'=>$cashsalediscount,
            'banksalediscount'=>$banksalediscount,
            'creditsalediscount'=>$creditsalediscount,
            'totaldiscount'=>$totaldiscount,                
            
            'cashpurchasediscount'=>$cashpurchasediscount,
            'creditpurchasediscount'=>$creditpurchasediscount,
            'bankpurchasediscount'=>$bankpurchasediscount,
            'totaldiscountpurchase'=>$totaldiscountpurchase,
                
            // Bank related calculations
            'banksaleTotal' => $banksaleTotal,
            'totalIncomebank' => $totalIncomebank,
            'salebankcredit' => $salebankcredit,                
            'totalexpensebank' => $totalexpensebank,
            'purchasebankcredit' => $purchasebankcredit,
            'purchasebankTotal' => $purchasebankTotal,
            'bankdebit' => $bankdebit,                
            'bankcredit' => $bankcredit,

            // Credit and supplier
            'credit' => $credit,
            'supplier' => $supplier,
                
            // Assets breakdown
            'currentassetSum' => $currentassetSum,
            'fixedassetSum' => $fixedassetSum,

            // VAT calculations                
            'purchasevat' => $purchasevat,
            'salevat' => $salevat,

            // Liabilities breakdown
            'longliabilitySum' => $longliabilitySum,                
            'shortliabilitySum' => $shortliabilitySum,

            // Capital breakdown
            'ownerscapitalSum' => $ownerscapitalSum,
            'investmentcapitalSum' => $investmentcapitalSum,
                
            // Full totals
            'salefullTotal' => $salefullTotal,
            'totalIncome' => $totalIncome,
            'purchasefullTotal' => $purchasefullTotal,                
            'totalexpenses' => $totalexpenses,
            'bankbalance'=>$bankbalance,
            'creditsaletotaldiscount'=>$creditsaletotaldiscount
        ];            
    
        return view('/chartaccounts/trailbalance', $data);
    }

    public function custombillingdashboardapk(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
       $item = Product::select(
                'products.id',
                'products.product_name',
                'products.selling_cost',
                'products.unit',
                'products.category_id',
                'products.buy_cost',
                'products.rate',
                'products.barcode',
                'products.inclusive_rate',
                'products.inclusive_vat_amount',
                'products.vat',
                'products.remaining_stock',
                'categories.category_name'
            )
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.branch', $branch)
            ->where('products.status', 1)
            ->where('remaining_stock', '>', 0)
            ->get();


        $extraitem  = ExtraProducts::select('extra_products.*')
        ->join('products', 'extra_products.product_id', '=', 'products.id')
        ->where('extra_products.branch', $branch)
        ->get();


// Get all categories for the filter
            $categories = DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->where('products.branch', $branch)
            ->where('products.status', 1)
            ->select('categories.id', 'categories.category_name')
            ->distinct()
            ->get();

            $allProducts = Product::select(
                'products.id',
                'products.product_name',
                'products.selling_cost',
                'products.unit',
                'products.category_id',
                'products.buy_cost',
                'products.rate',
                'products.barcode',
                'products.inclusive_rate',
                'products.inclusive_vat_amount',
                'products.vat',
                'products.remaining_stock'
            )
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







        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();




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

        return view('/billingdesk/custombillingapk', ['branch'=>$branch,'extraitem'=>$extraitem,'categories'=>$categories,'allProducts'=>$allProducts,'branchid'=>$branch,'creditusers' => $creditusers, 'items' => $item, 'users' => $useritem, 'currency' => $currency,]);
    }


    public function submitdatanew(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $userid = Session('softwareuser');
        $request->validate([
            'productName' => 'required',
        ]);

        $branch = DB::table('softwareusers')
            ->where('id', $userid)
            ->pluck('location')
            ->first();

       $count = DB::table('buyproducts')
        ->where('branch', $branch)
            ->distinct()
            ->count('transaction_id');

        ++$count;

        $admin_id = DB::table('softwareusers')
            ->where('id', $userid)
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
            $data->trn_number = $request->trn_number;


            // if ($request->dis_count[$key] === null) {
                // } else {
                    //     $data->price = ($request->vat_type_value == 1) ? (($request->mrp[$key] * $request->quantity[$key]) - ($request->dis_count[$key]  * $request->quantity[$key]) - $request->vat_amount[$key]) : ($request->mrp[$key] - $request->dis_count[$key]) * $request->quantity[$key];
                    // }

            $data->payment_type = $request->payment_type;
            $data->user_id = $userid;
            $data->branch = $branch;
            $data->one_pro_buycost = $request->buy_cost[$key];
            $data->mrp = $request->mrp[$key];
            $data->fixed_vat = $request->fixed_vat[$key];
            // $data->bank_id = $request->bank_name;
            // $data->account_name = $request->account_name;
            $data->vat_amount = $request->vat_amount[$key] * $request->quantity[$key];
            $data->price = ($request->mrp[$key] * $request->quantity[$key]) / (1 + $request->fixed_vat[$key] / 100);

            if ($request->payment_type == 3) {
                $data->credit_user_id = $request->credit_id;
            } elseif ($request->payment_type == 1 || $request->payment_type == 2 || $request->payment_type == 4) {
                $data->cash_user_id = $request->credit_id;
            }

            if ($request->vat_type_value == 1) {
            $data->total_amount = $request->total_amount[$key];
            } elseif ($request->vat_type_value == 2) {
            $data->total_amount = $request->total_amount[$key] + ($request->vat_amount[$key] * $request->quantity[$key]);
            }


            $data->vat_type = $request->vat_type_value;

            $data->one_pro_buycost_rate = $request->buycost_rate[$key];
            // if ($request->dis_count[$key] === null) {
                $data->netrate = $request->net_rate[$key];
            // } else {
                // $data->netrate = ($request->vat_type_value == 1) ? $request->mrp[$key] / (1 + ($request->fixed_vat[$key] /100)): (($request->mrp[$key] - $request->dis_count[$key]) +($request->mrp[$key] - $request->dis_count[$key]) * $request->fixed_vat[$key]/100) ;
            // }

            // if ($request->dis_count[$key] === null) {
                $data->inclusive_rate = ($request->vat_type_value == 1) ? $request->mrp[$key] / (1 + ($request->fixed_vat[$key] /100)): null;
                $data->exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;
            // } else {
            //     $data->inclusive_rate = ($request->vat_type_value == 1) ? $request->mrp[$key] - $request->dis_count[$key] : null;
            //     $data->exclusive_rate = ($request->vat_type_value == 2) ? $request->mrp[$key] - $request->dis_count[$key] : null;
            // }


            // if ($request->page == 'sales_order' || $request->page == 'quotation') {
            //     $data->discount_type = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key];

            //     $data->discount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
            //         ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0)));

            //     $data->discount_amount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
            //         ($request->dis_count__tp_ori[$key] == 'percentage' ?
            //             ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
            //                 $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
            //             ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
            //                 $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0));
            // } else {

            $data->discount_type = $request->dis_count_type[$key] ?? 'none';

            // Check if discount type exists, otherwise set discount and discount_amount to null
            if (isset($request->dis_count_type[$key]) && isset($request->dis_count[$key]) && isset($request->mrp[$key])) {
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
            } else {
                // If any of the required values are missing, store as null
                $data->discount = 0;
                $data->discount_amount = 0;
            }

            // Store other values, or set to null if missing
            $data->totalamount_wo_discount = $request->total_amount[$key];
            $data->price_wo_discount = ($request->mrp[$key] * $request->quantity[$key]) / (1 + $request->fixed_vat[$key] / 100);

            $bill_grand_total_wo_discount = $request->bill_grand_total + ($request->discount_amount ?? 0);
            if ($request->discount_amount == 0) {
                $data->total_discount_type = 0;
            } else {
                $data->total_discount_type = 2;
            }

            $data->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $bill_grand_total_wo_discount) * 100);
            $data->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($bill_grand_total_wo_discount * ($request->discount_percentage / 100));

            $data->bill_grand_total = $request->bill_grand_total;
            $data->bill_grand_total_wo_discount = $bill_grand_total_wo_discount;
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
            $credit_note_amount=$credit_note_amount-$final_credit_note;
            $credit_note_summary = DB::table('credit_note_summary')
            ->updateOrInsert(
                ['customer_name' => $request->customer_name],
                ['credit_note_amount' => $credit_note_amount]
            );

            // -----------------------------------------------------------------------//

            $stockdat = new Stockdat();
            $stockdat->product_id = $productID;
            $stockdat->stock_num = $request->quantity[$key];
            $stockdat->transaction_id = $transaction_id;
            $stockdat->user_id = $userid;
            $stockdat->one_pro_buycost = $request->buy_cost[$key];
            $stockdat->one_pro_sellingcost = $request->mrp[$key];

            if ($request->vat_type_value == 1) {
                $stockdat->one_pro_inclusive_rate = $request->mrp[$key] / (1 + ($request->fixed_vat[$key] /100));
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

                // Product::where('id', $productID)
                // ->where('branch', $branch)
                // ->update([
                //     'selling_cost' => $request->mrp[$key],
                //     'inclusive_rate' => $request->mrp[$key] / (1+($request->fixed_vat[$key]/100)),
                //     'product_name' => $request->productName[$key],
                //     'vat' => $request->fixed_vat[$key],
                // ]);



            /* ------------- Quantity reduce purchase wise code stock purchasereport table --------- */

            $buycostadd = 0;

            $buycost_rate_add = 0;
    if (!in_array($branch, [3, 14, 18, 2, 19, 23, 24, 26, 27, 25,28,29,31,32,34,35,36,37,38,39,58,63])) {

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
                $billhistory->user_id = $userid;
                $billhistory->Purchase_buycost = $first_purchase->PBuycost;
                $billhistory->billing_Sellingcost = $request->mrp[$key];
                $billhistory->Purchase_Buycost_Rate = $first_purchase->PBuycostRate;
                $billhistory->netrate = $request->net_rate[$key];
                $billhistory->receipt_no = $first_purchase->receipt_no;

                $billhistory->billing_inclusive_rate = ($request->vat_type_value == 1) ? $request->mrp[$key] / (1 + ($request->fixed_vat[$key] /100)) : null;
                $billhistory->billing_exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

                // if ($request->page == 'sales_order' || $request->page == 'quotation') {
                //     $billhistory->discount_type = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key];
                //     $billhistory->discount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                //         ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0)));

                //     $billhistory->discount_amount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                //         ($request->dis_count__tp_ori[$key] == 'percentage' ?
                //             ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                //                 $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                //             ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                //                 $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0));
                // } else {
                    $billhistory->discount_type = $request->dis_count_type[$key] ?? null;

                        // Check if discount type exists, otherwise set discount and discount_amount to null
                        if (isset($request->dis_count_type[$key]) && isset($request->dis_count[$key]) && isset($request->mrp[$key])) {
                            if ($request->vat_type_value == 1) {
                                if ($request->dis_count_type[$key] == 'none') {
                                    $billhistory->discount = $request->dis_count[$key];
                                    $billhistory->discount_amount = null;
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
                                    $billhistory->discount_amount = null;
                                } elseif ($request->dis_count_type[$key] == 'percentage') {
                                    $billhistory->discount = $request->dis_count[$key];
                                    $billhistory->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                                } elseif ($request->dis_count_type[$key] == 'amount') {
                                    $billhistory->discount_amount = $request->dis_count[$key];
                                    $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                                }
                            }
                        } else {
                            // If any required values are missing, store as null
                            $billhistory->discount = null;
                            $billhistory->discount_amount = null;
                        }
                // }

            $bill_grand_total_wo_discount = $request->bill_grand_total + ($request->discount_amount ?? 0);
            if ($request->discount_amount == 0) {
                $billhistory->total_discount_type = 0;
            } else {
                $billhistory->total_discount_type = 2;
            }

            $billhistory->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $bill_grand_total_wo_discount) * 100);
            $billhistory->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($bill_grand_total_wo_discount * ($request->discount_percentage / 100));

            $billhistory->bill_grand_total = $request->bill_grand_total;
            $billhistory->bill_grand_total_wo_discount = $bill_grand_total_wo_discount;

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
                $billhistory->user_id = $userid;
                $billhistory->Purchase_buycost = $first_purchase->PBuycost;
                $billhistory->billing_Sellingcost = $request->mrp[$key];
                $billhistory->Purchase_Buycost_Rate = $first_purchase->PBuycostRate;
                $billhistory->netrate = $request->net_rate[$key];
                $billhistory->receipt_no = $first_purchase->receipt_no;

                $billhistory->billing_inclusive_rate = ($request->vat_type_value == 1) ? $request->mrp[$key] / (1 + ($request->fixed_vat[$key] /100)) : null;
                $billhistory->billing_exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

                // if ($request->page == 'sales_order' || $request->page == 'quotation') {
                //     $billhistory->discount_type = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key];
                //     $billhistory->discount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                //         ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0)));

                //     $billhistory->discount_amount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                //         ($request->dis_count__tp_ori[$key] == 'percentage' ?
                //             ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                //                 $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                //             ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                //                 $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0));
                // } else {
               $billhistory->discount_type = $request->dis_count_type[$key] ?? null;

                    if (!empty($request->dis_count_type[$key]) && isset($request->dis_count[$key]) && isset($request->mrp[$key])) {
                        if ($request->vat_type_value == 1) {
                            if ($request->dis_count_type[$key] == 'none') {
                                $billhistory->discount = $request->dis_count[$key];
                                $billhistory->discount_amount = null;
                            } elseif ($request->dis_count_type[$key] == 'percentage') {
                                $billhistory->discount = $request->dis_count[$key];
                                $billhistory->discount_amount = $request->mrp[$key] * ($request->dis_count[$key] / 100);
                            } elseif ($request->dis_count_type[$key] == 'amount') {
                                $billhistory->discount_amount = $request->dis_count[$key];
                                $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                            }
                        } elseif ($request->vat_type_value == 2) {
                            if ($request->dis_count_type[$key] == 'none') {
                                $billhistory->discount = $request->dis_count[$key];
                                $billhistory->discount_amount = null;
                            } elseif ($request->dis_count_type[$key] == 'percentage') {
                                $billhistory->discount = $request->dis_count[$key];
                                $billhistory->discount_amount = $request->mrp[$key] * ($request->dis_count[$key] / 100);
                            } elseif ($request->dis_count_type[$key] == 'amount') {
                                $billhistory->discount_amount = $request->dis_count[$key];
                                $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                            }
                        }
                    } else {
                        // If any value is missing, store as null
                        $billhistory->discount = null;
                        $billhistory->discount_amount = null;
                    }


                // }

            $bill_grand_total_wo_discount = $request->bill_grand_total + ($request->discount_amount ?? 0);
            if ($request->discount_amount == 0) {
                $billhistory->total_discount_type = 0;
            } else {
                $billhistory->total_discount_type = 2;
            }

            $billhistory->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $bill_grand_total_wo_discount) * 100);
            $billhistory->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($bill_grand_total_wo_discount * ($request->discount_percentage / 100));

            $billhistory->bill_grand_total = $request->bill_grand_total;
            $billhistory->bill_grand_total_wo_discount = $bill_grand_total_wo_discount;
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
                        $billhistory->user_id = $userid;
                        $billhistory->Purchase_buycost = $next_purchase->PBuycost;
                        $billhistory->billing_Sellingcost = $request->mrp[$key];
                        $billhistory->Purchase_Buycost_Rate = $next_purchase->PBuycostRate;
                        $billhistory->netrate = $request->net_rate[$key];
                        $billhistory->receipt_no = $next_purchase->receipt_no;

                        $billhistory->billing_inclusive_rate = ($request->vat_type_value == 1) ? $request->mrp[$key] / (1 + ($request->fixed_vat[$key] /100)) : null;
                        $billhistory->billing_exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

                        // if ($request->page == 'sales_order' || $request->page == 'quotation') {
                        //     $billhistory->discount_type = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key];
                        //     $billhistory->discount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                        //         ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0)));

                        //     $billhistory->discount_amount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                        //         ($request->dis_count__tp_ori[$key] == 'percentage' ?
                        //             ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                        //                 $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                        //             ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                        //                 $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0));
                        // } else {
                       $billhistory->discount_type = $request->dis_count_type[$key] ?? null;

                        if (!empty($request->dis_count_type[$key]) && isset($request->dis_count[$key]) && isset($request->mrp[$key])) {
                            if ($request->vat_type_value == 1 || $request->vat_type_value == 2) {
                                if ($request->dis_count_type[$key] == 'none') {
                                    $billhistory->discount = $request->dis_count[$key];
                                    $billhistory->discount_amount = null;
                                } elseif ($request->dis_count_type[$key] == 'percentage') {
                                    $billhistory->discount = $request->dis_count[$key];
                                    $billhistory->discount_amount = $request->mrp[$key] * ($request->dis_count[$key] / 100);
                                } elseif ($request->dis_count_type[$key] == 'amount') {
                                    $billhistory->discount_amount = $request->dis_count[$key];
                                    $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                                }
                            }
                        } else {
                            // Store as NULL if any value is missing
                            $billhistory->discount = null;
                            $billhistory->discount_amount = null;
                        }


                        // }

            $bill_grand_total_wo_discount = $request->bill_grand_total + ($request->discount_amount ?? 0);
            if ($request->discount_amount == 0) {
                $billhistory->total_discount_type = 0;
            } else {
                $billhistory->total_discount_type = 2;
            }

            $billhistory->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $bill_grand_total_wo_discount) * 100);
            $billhistory->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($bill_grand_total_wo_discount * ($request->discount_percentage / 100));

            $billhistory->bill_grand_total = $request->bill_grand_total;
            $billhistory->bill_grand_total_wo_discount = $bill_grand_total_wo_discount;

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
                        $billhistory->user_id = $userid;
                        $billhistory->Purchase_buycost = $next_purchase->PBuycost;
                        $billhistory->billing_Sellingcost = $request->mrp[$key];
                        $billhistory->Purchase_Buycost_Rate = $next_purchase->PBuycostRate;
                        $billhistory->netrate = $request->net_rate[$key];
                        $billhistory->receipt_no = $next_purchase->receipt_no;

                        $billhistory->billing_inclusive_rate = ($request->vat_type_value == 1) ? $request->mrp[$key] / (1 + ($request->fixed_vat[$key] /100)) : null;
                        $billhistory->billing_exclusive_rate = ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null;

                        // if ($request->page == 'sales_order' || $request->page == 'quotation') {
                        //     $billhistory->discount_type = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key];
                        //     $billhistory->discount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                        //         ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0)));

                        //     $billhistory->discount_amount = (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                        //         ($request->dis_count__tp_ori[$key] == 'percentage' ?
                        //             ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                        //                 $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                        //             ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                        //                 $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0));
                        // } else {
          $billhistory->discount_type = $request->dis_count_type[$key] ?? null;

                // Ensure required values exist to prevent undefined index errors
                if (!empty($request->dis_count_type[$key]) && isset($request->dis_count[$key]) && isset($request->mrp[$key])) {
                    if (in_array($request->vat_type_value, [1, 2])) { // Combine common logic for both VAT types
                        if ($request->dis_count_type[$key] === 'none') {
                            $billhistory->discount = $request->dis_count[$key];
                            $billhistory->discount_amount = 0;
                        } elseif ($request->dis_count_type[$key] === 'percentage') {
                            $billhistory->discount = $request->dis_count[$key];
                            $billhistory->discount_amount = $request->mrp[$key] * ($request->dis_count[$key] / 100);
                        } elseif ($request->dis_count_type[$key] === 'amount') {
                            $billhistory->discount_amount = $request->dis_count[$key];
                            $billhistory->discount = ($request->mrp[$key] > 0) ? ($request->dis_count[$key] / $request->mrp[$key]) * 100 : 0;
                        }
                    }
                } else {
                    // Set default values if any required field is missing
                    $billhistory->discount = 0;
                    $billhistory->discount_amount = 0;
                }


                        // }

            $bill_grand_total_wo_discount = $request->bill_grand_total + ($request->discount_amount ?? 0);
            if ($request->discount_amount == 0) {
                $billhistory->total_discount_type = 0;
            } else {
                $billhistory->total_discount_type = 2;
            }

            $billhistory->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $bill_grand_total_wo_discount) * 100);
            $billhistory->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($bill_grand_total_wo_discount * ($request->discount_percentage / 100));

            $billhistory->bill_grand_total = $request->bill_grand_total;
            $billhistory->bill_grand_total_wo_discount = $bill_grand_total_wo_discount;

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
                $fund->user_id = $userid;
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
            $credit_trans->user_id = $userid;
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
            $cash_trans->user_id = $userid;
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
            $bank_history->user_id =$userid;
            $bank_history->branch = $branch;
            $bank_history->detail = 'Sales';
            $bank_history->dr_cr = 'Credit';
            $bank_history->bank_id = $request->bank_name;
            $bank_history->account_name = $request->account_name;
            $bank_history->amount = $request->bill_grand_total;
            $bank_history->date = Carbon::now(); // Store the current date and time
            $bank_history->save();
        }

        // BEGIN JournalEntry hook: Sale (New/Service)
        try {
            $transactionId = $request->input('transaction_id') ?? ($sale->transaction_id ?? null);

            if ($transactionId) {
                app(\App\Services\JournalEntryService::class)->postSaleByTransaction($transactionId);
            }
        } catch (\Exception $e) {
            \Log::error('Journal entry failed [Sale New/Service]: '.$e->getMessage());
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
        

            return redirect('/generatetax-pdfsunmi/'.$transaction);

            // return redirect('/generatetax-newsunmi/'.$transaction);


        }
                public function setting(Request $request)
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

                $branch_data = DB::table('branches')
                ->where('id', $branch)
                ->first();
                
    
            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();

            /* ------------------GET IP ADDRESS--------------------------------------- */
    
            $ip = request()->ip();
            $uri = request()->fullUrl();
    
            $username = Softwareuser::where('id', $userid)->pluck('username')->first();
    
            $user_type = 'websoftware';
            $message = $username.'edited branch details';
    
            $locationdata = (new otherService())->get_location($ip);
    
            $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();
    
            if ($locationdata != false) {
                $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
            }
       
            /* ----------------------------------------------------------------------- */
    
            return view('/user/setting', ['branch'=>$branch_data, 'users' => $useritem]);
        }

        public function updateBranch(Request $request)
        {
            // Get the current branch
            $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        
            // Handle logo upload
            $branch = Branch::findOrFail($branch);
  if ($request->hasFile('logo')) {
    $file = $request->file('logo');

    if ($file->isValid()) {
        \Log::info('File is found and valid.');

        // Define the destination path relative to the 'public' directory
        $destinationPath = public_path('images/logoimage');

        // Create a unique filename
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Move file to the destination path
        $file->move($destinationPath, $fileName);

        // Save the relative file path to the database (relative to the 'public' folder)
        $branch->logo = 'images/logoimage/' . $fileName;
    } else {
        \Log::error('File is invalid.');
    }
} else {
    \Log::error('No file found in the request.');
} 
            // Update branch data with explicit field assignments
            $branch->company = $request->company;
            $branch->branchname = $request->branch;
            $branch->location = $request->branch;
            $branch->tr_no = $request->tr_no;
            $branch->mobile = $request->mobile;
            $branch->address = $request->address;
            $branch->email = $request->email;
            $branch->po_box = $request->po_box;
            $branch->currency = $request->currency;
            
            // Save the changes
            $branch->save();
        
            // Redirect back with success message
            return redirect('/setting')->with('success', 'Branch details updated successfully!');
        }
           public function calender(Request $request)
        {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        
            // Get current date or selected month
            $today = now();
            $selectedDate = $today;
            
            if ($request->has('selected_month')) {
                $selectedDate = Carbon::createFromFormat('Y-m', $request->selected_month);
            }
        
            $currentMonth = $selectedDate->format('n');
            $currentYear = $selectedDate->format('Y');
            $monthName = $selectedDate->format('F Y');
            
            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();
            
            $userid = Session('softwareuser');
            $useritem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();
        
            // Get sales data for selected month
            $salesData = DB::table('buyproducts')
                ->select(
                    DB::raw('DAY(created_at) as day'),
                    DB::raw('COUNT(DISTINCT transaction_id) as total_sales'),
                    DB::raw('SUM(total_amount) as amount')
                )
                ->where('branch', $branch)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->groupBy(DB::raw('DAY(created_at)'))
                ->get()
                ->keyBy('day');
        
            // Generate month navigation (12 months - 6 before, 5 after)
            $monthNavigation = [];
            for ($i = -6; $i <= 5; $i++) {
                $navDate = $selectedDate->copy()->addMonths($i);
                $monthNavigation[] = [
                    'display' => $navDate->format('M Y'),
                    'value' => $navDate->format('Y-m'),
                    'is_current' => $navDate->format('Y-m') === $selectedDate->format('Y-m')
                ];
            }
        
            return view('/user/calender', [
                'salesData' => $salesData,
                'currentMonth' => $currentMonth,
                'currentYear' => $currentYear,
                'monthName' => $monthName,
                'branch' => $branch,
                'users' => $useritem,
                'today' => $today->format('Y-m-d'),
                'monthNavigation' => $monthNavigation,
                'selectedDate' => $selectedDate,
                'userid' => $userid,
                'location' => $branch,
            ]);
            
        }
        public function custombillingservicebilling(Request $request)
        {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();
                $products = DB::table('products')
                ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id') // Join with categories
                ->select(DB::raw('products.id as id,products.vat as vat,products.barcode as barcode,categories.category_name as category,products.product_name as product_name,products.id as id, products.unit as unit,products.buy_cost as buy_cost, products.selling_cost as selling_cost,products.rate as rate,products.purchase_vat as purchase_vat'))
                ->groupBy('products.id')
                ->where('products.branch', $branch)
                ->where('products.status', 1)
                ->orderBy('products.id')
                ->get();

                 $units = DB::table('units')
            ->select(DB::raw('units.unit,units.id'))
            ->where('branch_id', $branch)
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

            return view('/billingdesk/servicebilling', ['units'=>$units,'branchid'=>$branch,'mode'=>$mode,'listemployee'=>$listemployee,'listbank' => $listbank, 'creditusers' => $creditusers, 'products' => $products, 'users' => $useritem, 'shopdatas' => $shopdata, 'currency' => $currency, 'tax' => $tax]);
        }

        public function submitservicedata(Request $request)
        {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
            $userid = Session('softwareuser');


            $branch = DB::table('softwareusers')
                ->where('id', $userid)
                ->pluck('location')
                ->first();

                $count = DB::table('buyproducts')
                ->where('branch', $branch)
                    ->distinct()
                    ->count('transaction_id');
        
                ++$count;


            $admin_id = DB::table('softwareusers')
                ->where('id', $userid)
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
            $productIDs = $request->input('product_id');
            $product_code = rand(100000000, 200000000);

            foreach ($productIDs as $key => $productID) {

                $unitname = $request->unit[$key];

                // Check if unit exists within the same branch
                $UnitRecord = Unit::where('unit', $unitname)
                    ->where('branch_id', $branch)
                    ->first();

                if (!$UnitRecord) {
                    $UnitRecord = Unit::create([
                        'unit' => $unitname,
                        'user_id' => $userid,
                        'branch_id' => $branch,
                        'status' => 1
                    ]);
                }

                
                    // $categoryName = $request->category[$key] ?? 'Service';

               

                // $categoryRecord = Category::where('category_name', $categoryName)
                //     ->where('branch_id', $branch)
                //     ->first();

                // if (!$categoryRecord) {
                //     $categoryRecord = Category::create([
                //         'category_name' => $categoryName,
                //         'user_id' => $userid,
                //         'branch_id' => $branch,
                //         'access' => 1
                //     ]);
                // }


            // Get category ID
                    if (empty($productID)) {
                    $newProduct = new Product();
                    $newProduct->product_name = $request->productName[$key];
                    $newProduct->unit = $request->unit[$key]; // Use unit ID instead of name
                    $newProduct->user_id = Session('softwareuser');
                    $newProduct->branch = $branch;
                    $newProduct->category_id = 20;
                    $newProduct->buy_cost = 0;
                    $newProduct->purchase_vat = 0;
                    $newProduct->rate = 0;
                    $newProduct->selling_cost = $request->rate[$key];
                    $newProduct->vat = $request->tax_percent[$key];


                    if (!empty($barcodes[$key])) {
                        $newProduct->product_code = $barcodes[$key];
                        $newProduct->barcode = $barcodes[$key];
                    } else {
                        $newProduct->product_code = $product_code;
                        $newProduct->barcode = $product_code;
                        ++$product_code;
                    }
                    // Calculate inclusive rate and vat
                    if (!empty($request->rate[$key]) && !empty($request->tax_percent[$key])) {
                        $inclusive_rate = $request->rate[$key] / (1 + ($request->tax_percent[$key] / 100));
                        $inclusive_vat_amount = $request->rate[$key] - $inclusive_rate;

                        $newProduct->inclusive_rate = $inclusive_rate;
                        $newProduct->inclusive_vat_amount = $inclusive_vat_amount;
                    }

                    // Set initial stock values

                    $newProduct->save();

                    // Use the newly created product ID for further processing
                    $productID = $newProduct->id;
                }
                $data = new Buyproduct();
                $data->product_name = $request->productName[$key];
                $data->quantity = $request->quantity[$key];
                $data->remain_quantity = $request->quantity[$key];
                $data->unit = $request->unit[$key];
                $data->product_id = $productID;
                $data->transaction_id = $transaction_id;
                $data->customer_name = $request->customer_name;
                $data->employee_id = $request->employee_id;
                $data->employee_name = $request->employee_name;
                $data->email = $request->email;
                $data->trn_number = $request->trn_number;
                $data->phone = $request->phone;
                $data->price =($request->vat_type_value == 2) ? $request->rate[$key] * $request->quantity[$key] :(($request->rate[$key] * $request->quantity[$key]) -$request->total_tax_amount[$key]);
                $data->total_amount = $request->total_amount[$key];
                $data->payment_type = $request->payment_type;
                $data->user_id = Session('softwareuser');
                $data->branch = $branch;
                $data->one_pro_buycost = 0;
                $data->mrp = $request->rate[$key];
                $data->fixed_vat = $request->tax_percent[$key];
                $data->bank_id = $request->bank_name;
                $data->account_name = $request->account_name;

                $data->vat_amount = $request->total_tax_amount[$key];

                if ($request->payment_type == 3) {
                    $data->credit_user_id = $request->credit_id;
                } elseif ($request->payment_type == 1 || $request->payment_type == 2 || $request->payment_type == 4) {
                    $data->cash_user_id = $request->credit_id;
                }

                $data->vat_type = $request->vat_type_value;

                $data->one_pro_buycost_rate = 0;
                $data->netrate = $request->net_rate[$key];

                $data->inclusive_rate = ($request->vat_type_value == 1) ? $request->inclusive_rate[$key] : null;
                $data->exclusive_rate = ($request->vat_type_value == 2) ? $request->inclusive_rate[$key] : null;


                $data->totalamount_wo_discount = $request->total_amount[$key];
                $data->price_wo_discount =($request->vat_type_value == 2) ? $request->rate[$key] * $request->quantity[$key] :(($request->rate[$key] * $request->quantity[$key]) -$request->total_tax_amount[$key]);

                $data->total_discount_type = $request->total_discount;
                $data->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100);
                $data->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100));

                $data->bill_grand_total = $request->bill_grand_total;
                $data->bill_grand_total_wo_discount = $request->bill_grand_total_wo_discount;
                $data->credit_note = $final_credit_note;


                $data->save();
                NewBuyproduct::create($data->getAttributes());

                // -------------------------------------------------------------------//
                $credit_note_amount = DB::table('credit_note_summary')
                ->where('customer_name', $request->customer_name)
                ->pluck('credit_note_amount')
                ->first();
                $credit_note_amount=$credit_note_amount-$final_credit_note;
                $credit_note_summary = DB::table('credit_note_summary')
                ->updateOrInsert(
                    ['customer_name' => $request->customer_name],
                    ['credit_note_amount' => $credit_note_amount]
                );

                // -----------------------------------------------------------------------//

                $stockdat = new Stockdat();
            $stockdat->product_id = $productID;
            $stockdat->stock_num = $request->quantity[$key];
            $stockdat->transaction_id = $transaction_id;
            $stockdat->user_id = Session('softwareuser');
            $stockdat->one_pro_buycost = 0;
            $stockdat->one_pro_sellingcost = $request->rate[$key];

            if ($request->vat_type_value == 1) {
                $stockdat->one_pro_inclusive_rate = $request->inclusive_rate[$key];
            }

            $stockdat->one_pro_buycost_rate = 0;
            $stockdat->netrate = $request->net_rate[$key];
            $stockdat->save();

            }

            $transaction = $transaction_id;
            $due_amounttotal = 0;

            foreach ($productIDs as $key => $productID) {
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
                    $fund->user_id = $userid;
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
                $credit_trans->user_id = $userid;
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
                $cash_trans->user_id = $userid;
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
                $bank_history->user_id =$userid;
                $bank_history->branch = $branch;
                $bank_history->detail = 'Sales';
                $bank_history->dr_cr = 'Credit';
                $bank_history->bank_id = $request->bank_name;
                $bank_history->account_name = $request->account_name;
                $bank_history->amount = $request->bill_grand_total;
                $bank_history->date = Carbon::now(); // Store the current date and time
                $bank_history->save();
            }

            // BEGIN JournalEntry hook: Sale (New/Service)
            try {
                $transactionId = $request->input('transaction_id') ?? ($sale->transaction_id ?? null);

                if ($transactionId) {
                    app(\App\Services\JournalEntryService::class)->postSaleByTransaction($transactionId);
                }
            } catch (\Exception $e) {
                \Log::error('Journal entry failed [Sale New/Service]: '.$e->getMessage());
            }
            // END JournalEntry hook


            /* ------------------GET IP ADDRESS--------------------------------------- */

            $userid = Session('softwareuser');
            $ip = request()->ip();
            $uri = request()->fullUrl();

            $username = Softwareuser::where('id', $userid)->pluck('username')->first();

            $user_type = 'websoftware';
            $message = $username.' done service billing';

            $locationdata = (new otherService())->get_location($ip);

            $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

            if ($locationdata != false) {
                $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
            }

            return redirect('/billdeskfinalreciept/'.$transaction);


            }
              public function customersstore(Request $request) {

                $userid = Session::get('softwareuser');
                $useritem = DB::table('softwareusers')
                    ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                    ->where('user_id', $userid)
                    ->get();
                $branch = DB::table('softwareusers')
                    ->where('id', Session('softwareuser'))
                    ->pluck('location')
                    ->first();
                // Get the admin_id from the softwareuser
                $adminid = Softwareuser::where('id', $userid)
                    ->pluck('admin_id')
                    ->first();
    
    
                $customer = Credituser::create([
                    'name' => $request->name,
                    'l_amount' => $request->credit_limit,
                    'current_lamount' => $request->credit_limit,
                    'admin_id' => $adminid,
                    'admin_status' => 1,
                    'location' => $branch,
                    'user_id' => $userid,
    
                ]);
            
                return response()->json([
                    'success' => true,
                    'customer' => $customer
                ]);
    }

    public function supplierstore(Request $request) {

        $userid = Session::get('softwareuser');
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        // Get the admin_id from the softwareuser
        $adminid = Softwareuser::where('id', $userid)
            ->pluck('admin_id')
            ->first();


            $supplier = Supplier::create([
                'name' => $request->supplier_name,
                'adminuser' => $adminid,
                'location' => $branch,
                'softwareuser' => $userid,
            ]);

            return response()->json([
                'success' => true,
                'supplier' => $supplier // Fixed key to match AJAX
            ]);
    }
     public function balancesheet(){
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
        $fixedAssets = Chartofaccountants::where('type', 'Asset')
        ->where('sub_type', 'fixed')
        ->where('branch', $branch)
        ->get();

    $currentAssets = Chartofaccountants::where('type', 'Asset')
        ->where('sub_type', 'current')
        ->where('branch', $branch)
        ->get();

    // Fetching Liability Data
    $shortTermLiabilities = Chartofaccountants::where('type', 'Liability')
        ->where('sub_type', 'Short-term Liabilities')
        ->where('branch', $branch)
        ->get();

    $longTermLiabilities = Chartofaccountants::where('type', 'Liability')
        ->where('sub_type', 'Long-term Liabilities')
        ->where('branch', $branch)
        ->get();

          $otherAssets = Chartofaccountants::where('type', 'Asset')
        ->where('sub_type', 'other')
        ->where('branch', $branch)
        ->get();

        $capital = Chartofaccountants::where('type', 'Capital')
        ->where('branch', $branch)
        ->get();

        return view('/chartaccounts/balancesheet', [
            'users' => $item,
            'shopdatas' => $shopdata,
            'userdatas' => $userdata,
            'currency'=>$currency,
            'fixedAssets' => $fixedAssets,
            'currentAssets' => $currentAssets,
            'shortTermLiabilities' => $shortTermLiabilities,
            'longTermLiabilities'=>$longTermLiabilities,
            'otherAssets'=>$otherAssets,
            'capital'=>$capital
         ]);
    }


    public function balanceSheetfilter(Request $request)
{
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

    // Get dates from request
    $start_date = $request->start_date ?? Carbon::today()->toDateString();
    $end_date = $request->end_date ?? Carbon::today()->toDateString();

    // Fetching Asset Data
    $fixedAssets = Chartofaccountants::where('type', 'Asset')
        ->where('sub_type', 'fixed')
        ->where('branch', $branch)
        ->whereBetween('type_date', [$start_date, $end_date])
        ->get();

    $currentAssets = Chartofaccountants::where('type', 'Asset')
        ->where('sub_type', 'current')
        ->where('branch', $branch)
        ->whereBetween('type_date', [$start_date, $end_date])
        ->get();

    $otherAssets = Chartofaccountants::where('type', 'Asset')
        ->where('sub_type', 'other')
        ->where('branch', $branch)
        ->whereBetween('type_date', [$start_date, $end_date])
        ->get();

    // Fetching Liability Data
    $shortTermLiabilities = Chartofaccountants::where('type', 'Liability')
    ->where('sub_type', 'Short-term Liabilities')
    ->where('branch', $branch)
        ->whereBetween('type_date', [$start_date, $end_date])
        ->get();

    $longTermLiabilities = Chartofaccountants::where('type', 'Liability')
        ->where('sub_type', 'Long-term Liabilities')
        ->where('branch', $branch)
        ->whereBetween('type_date', [$start_date, $end_date])
        ->get();

        $capital = Chartofaccountants::where('type', 'Capital')
        ->where('branch', $branch)
        ->whereBetween('type_date', [$start_date, $end_date])
        ->get();

        return view('/chartaccounts/balancesheet', [
            'users' => $item,
            'shopdatas' => $shopdata,
            'userdatas' => $userdata,
            'currency'=>$currency,
            'fixedAssets' => $fixedAssets,
            'currentAssets' => $currentAssets,
            'shortTermLiabilities' => $shortTermLiabilities,
            'longTermLiabilities'=>$longTermLiabilities,
            'otherAssets'=>$otherAssets,
            'capital'=>$capital
         ]);
}

    public function journalEntry(Request $request)
    {
        // Authentication check
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }

        $userid = Session('softwareuser');

        // User and shop data
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $adminid = Softwareuser::where('id', $userid)->value('admin_id');
        $branch  = Softwareuser::where('id', $userid)->value('location');
        $shopdata = Branch::where('id', $branch)->get();
        $userdata = Softwareuser::where('id', $userid)->get();

        // Banks / Entities / Accounts (single query for optimization)
        $banksData = Bank::select('bank_name', 'account_name', 'account_no')->get();
        $banks     = $banksData->pluck('bank_name');
        $entities  = $banksData->pluck('account_name');
        $accounts  = $banksData->pluck('account_no');

        // Date filters
        $start_date = $request->start_date ?? Carbon::today()->toDateString();
        $end_date   = $request->end_date   ?? Carbon::today()->toDateString();

        // Overall totals
        $overallTotals = JournalEntry::query()
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('entry_date', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn($q) => $q->whereDate('entry_date', '<=', $request->end_date))
            ->when($request->filled('entity'), fn($q) => $q->where('entity', 'like', '%'.$request->entity.'%'))
            ->when($request->filled('reference'), fn($q) => $q->where('reference', 'like', '%'.$request->reference.'%'))
            ->when($request->filled('paid_through'), fn($q) => $q->where('paid_through', $request->paid_through))
            ->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')
            ->first();

        // Base query
        $query = JournalEntry::with('transaction')
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('entry_date', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn($q) => $q->whereDate('entry_date', '<=', $request->end_date))
            ->when($request->filled('entity'), fn($q) => $q->where('entity', 'like', '%'.$request->entity.'%'))
            ->when($request->filled('reference'), fn($q) => $q->where('reference', 'like', '%'.$request->reference.'%'))
            ->when($request->filled('paid_through'), fn($q) => $q->where('paid_through', $request->paid_through));

        // Ordering
        if ($request->get('view_mode') === 'book') {
            $query->orderBy('entry_date')->orderBy('created_at')->orderBy('id');
        } else {
            $query->orderByDesc('entry_date')->orderByDesc('created_at')->orderByDesc('id');
        }

        $journalEntries = $query->paginate(25);

        // Assign transaction type (cleaned up)
        foreach ($journalEntries as $entry) {
            $entry->transaction_type = match ($entry->source_table) {
                'buyproducts'            => 'Purchase',
                'performance_invoices'   => 'Sale',
                'returnproducts'         => 'Sales Return',
                'returnpurchases'        => 'Purchase Return',
                'credit_transactions'    => 'Credit Transaction',
                'fund_transfer'          => 'Fund Transfer',
                'accountexpenses'        => 'Expense',
                'account_indirect_incomes' => 'Income',
                'credit_note'            => 'Credit Note',
                default                  => 'General',
            };

            // Fallback based on accounts only if source_table is null
            if ($entry->transaction_type === 'General' && $entry->account) {
                if (str_contains($entry->account, 'Sales'))   $entry->transaction_type = 'Sale';
                if (str_contains($entry->account, 'Purchase'))$entry->transaction_type = 'Purchase';
                if (str_contains($entry->account, 'Return'))  $entry->transaction_type = 'Return';
                if (str_contains($entry->account, 'Expense')) $entry->transaction_type = 'Expense';
                if (str_contains($entry->account, 'Income'))  $entry->transaction_type = 'Income';
            }

            // Group key for book view
            $entry->group_key = $entry->reference ?: $entry->transaction_id ?: ($entry->entry_date.'-'.$entry->id);
        }

        // Grouping for book view: group by transaction_id
        $groupedEntries = $journalEntries->groupBy('transaction_id');

        return view('chartaccounts.journalentry', [
            'users'         => $item,
            'shopdatas'     => $shopdata,
            'userdatas'     => $userdata,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'journalEntries'=> $journalEntries,
            'groupedEntries'=> $groupedEntries,
            'overallTotals' => [
                'debit'  => $overallTotals->total_debit,
                'credit' => $overallTotals->total_credit,
            ],
            'currency'      => 'AED',
            'banks'         => $banks,
            'entities'      => $entities,
            'accounts'      => $accounts,
            'view_mode'     => $request->get('view_mode', 'listing'),
        ]);
    }

    public function saveJournalEntry(Request $request)
    {
        $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.entry_date' => 'required|date',
            'entries.*.account' => 'required|string',
            'entries.*.description' => 'nullable|string',
            // Must have either debit OR credit, not both, not neither
            'entries.*.debit' => 'nullable|numeric|required_without_all:entries.*.credit',
            'entries.*.credit' => 'nullable|numeric|required_without_all:entries.*.debit',
            'entries.*.entity' => 'nullable|string',
            'entries.*.paid_through' => 'nullable|string',
            'entries.*.reference' => 'nullable|string',
        ]);

        $userid   = Session('softwareuser');
        $adminid  = Softwareuser::where('id', $userid)->value('admin_id');
        $branchid = Softwareuser::where('id', $userid)->value('location');

        // Validate total debit = total credit
        $totalDebit  = collect($request->entries)->sum(fn($e) => floatval($e['debit'] ?? 0));
        $totalCredit = collect($request->entries)->sum(fn($e) => floatval($e['credit'] ?? 0));

        if (bccomp($totalDebit, $totalCredit, 2) !== 0) {
            return back()->withErrors([
                'balance' => "Total debit (AED ".number_format($totalDebit,2).") and credit (AED ".number_format($totalCredit,2).") must be equal."
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            // Create transaction record (header)
            $transaction = JournalTransaction::create([
                'transaction_date' => $request->entries[0]['entry_date'],
                'reference'        => $request->entries[0]['reference'] ?? null,
                'narration'        => $request->entries[0]['description'] ?? 'Journal entry created',
            ]);
            
            // Save each journal line
            foreach ($request->entries as $entry) {
                JournalEntry::create([
                    'transaction_id' => $transaction->id,
                    'entry_date'     => $entry['entry_date'],
                    'account'        => $entry['account'],
                    'description'    => $entry['description'] ?? null,
                    'debit'          => $entry['debit'] ?? 0,
                    'credit'         => $entry['credit'] ?? 0,
                    'entity'         => $entry['entity'] ?? null,
                    'paid_through'   => $entry['paid_through'] ?? null,
                    'reference'      => $entry['reference'] ?? null,
                    'admin_id'       => $adminid,
                    'branch_id'      => $branchid,
                    'created_by'     => $userid,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Journal entries saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['exception' => 'Error saving entries: '.$e->getMessage()])->withInput();
        }
    }


public function payment_history_customer(Request $request, $id)
{
    if (session()->missing('softwareuser')) {
        return redirect('userlogin');
    }
    
    $userid = Session('softwareuser');
    $adminid = Softwareuser::where('id', $userid)->pluck('admin_id')->first();

    // Get user roles
    $item = DB::table('softwareusers')
        ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
        ->where('user_id', $userid)
        ->get();

    // Get branch location
    $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();

    // Get currency
    $currency = Adminuser::Where('id', $adminid)
        ->pluck('currency')
        ->first();

    // Get payment transactions where comment is "Payment Received"
    $paymentTransactions = DB::table('credit_transactions')
        ->where('credituser_id',$id)
        ->where('location',$branch)
        ->where('comment', 'Payment Received')
        ->select('id','created_at','collected_amount', 'payment_type', 'cheque_number', 'depositing_date', 'account_name', 'reference_number')
        ->get();

        $customer = DB::table('creditusers')
    ->where('id', $id)
    ->first();

    return view('/billingdesk/customer_payment_history', [
        'users' => $item,
        'paymentTransactions' => $paymentTransactions,
        'currency' => $currency,
        'branch' => $branch,
        'customer'=>$customer
    ]);
}

public function payment_history_supplier(Request $request, $id)
{
    if (session()->missing('softwareuser')) {
        return redirect('userlogin');
    }
    
    $userid = Session('softwareuser');
    $adminid = Softwareuser::where('id', $userid)->pluck('admin_id')->first();

    // Get user roles
    $item = DB::table('softwareusers')
        ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
        ->where('user_id', $userid)
        ->get();

    // Get branch location
    $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();

    // Get currency
    $currency = Adminuser::Where('id', $adminid)
        ->pluck('currency')
        ->first();

    // Get payment transactions where comment is "Payment Received"
    $paymentTransactions = DB::table('credit_supplier_transactions')
        ->where('credit_supplier_id',$id)
        ->where('location',$branch)
        ->where('comment', 'Payment Made')
        ->select('id','created_at','collectedamount', 'payment_type', 'check_number', 'depositing_date', 'account_name', 'reference_number')
        ->get();

        $suppliers = DB::table('suppliers')
    ->where('id', $id)
    ->first();

    return view('/billingdesk/supplier_payment_history', [
        'users' => $item,
        'paymentTransactions' => $paymentTransactions,
        'currency' => $currency,
        'branch' => $branch,
        'suppliers'=>$suppliers
    ]);
}
public function cancelcustomerPayment(Request $request)
{
   

    DB::beginTransaction();
    
    try {
        $transaction = DB::table('credit_transactions')
            ->where('id', $request->transaction_id)
            ->where('credituser_id', $request->customer_id)
            ->first();

        if (!$transaction) {
            return back()->with('error', 'Transaction not found');
        }

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

            $lastTransaction = DB::table('credit_transactions')
            ->where('credituser_id', $request->customer_id)
            ->where('location', $branch)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    

        // Update customer balance
        DB::table('creditsummaries')
            ->where('credituser_id', $request->customer_id)
            ->decrement('collected_amount', $transaction->collected_amount);

        // Create reversal transaction
        DB::table('credit_transactions')->insert([
            'credituser_id' => $request->customer_id,
            'collected_amount' => $transaction->collected_amount,
            'payment_type' => $transaction->payment_type,
            'credit_username' => $transaction->credit_username,
            'user_id' => $transaction->user_id,
            'location' => $transaction->location,
            'transaction_id' => $transaction->transaction_id,
            'updated_balance' => $lastTransaction->updated_balance + $transaction->collected_amount,
            'balance_due' => $lastTransaction->balance_due + $transaction->collected_amount,
            'due' => $lastTransaction->updated_balance,
            'comment' => 'Payment Cancelled',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Mark original as cancelled
        DB::table('credit_transactions')
            ->where('id', $request->transaction_id)
            ->update(['location' => null]);

        DB::table('credit_transactions')
            ->where('credituser_id', $request->customer_id)
            ->where('id', $request->transaction_id)
            ->update(['status' => 'cancelled']);

        DB::commit();

        return back()->with('success', 'Payment cancelled successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Payment cancellation failed: '.$e->getMessage());
        return back()->with('error', 'Failed to cancel payment: '.$e->getMessage());
    }
}
public function cancelsupplierPayment(Request $request)
{
   

    DB::beginTransaction();
    
    try {
        $transaction = DB::table('credit_supplier_transactions')
            ->where('id', $request->transaction_id)
            ->where('credit_supplier_id', $request->supplier_id)
            ->first();

        if (!$transaction) {
            return back()->with('error', 'Transaction not found');
        }

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

            $lastTransaction = DB::table('credit_supplier_transactions')
            ->where('credit_supplier_id', $request->supplier_id)
            ->where('location', $branch)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    

        // Update customer balance
        DB::table('supplier_credits')
            ->where('supplier_id', $request->supplier_id)
            ->decrement('collected_amt', $transaction->collectedamount);

        // Create reversal transaction
        DB::table('credit_supplier_transactions')->insert([
            'credit_supplier_id' => $request->supplier_id,
            'collectedamount' => $transaction->collectedamount,
            'payment_type' => $transaction->payment_type,
            'credit_supplier_username' => $transaction->credit_supplier_username,
            'user_id' => $transaction->user_id,
            'location' => $transaction->location,
            'reciept_no' => $transaction->reciept_no,
            'updated_balance' => $lastTransaction->updated_balance + $transaction->collectedamount,
            'balance_due' => $lastTransaction->balance_due + $transaction->collectedamount,
            'due' => $lastTransaction->updated_balance,
            'comment' => 'Payment Cancelled',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Mark original as cancelled
        DB::table('credit_supplier_transactions')
            ->where('id', $request->transaction_id)
            ->update(['location' => null]);

        DB::table('credit_supplier_transactions')
            ->where('credit_supplier_id', $request->supplier_id)
            ->where('id', $request->transaction_id)
            ->update(['status' => 'cancelled']);

        DB::commit();

        return back()->with('success', 'Payment cancelled successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Payment cancellation failed: '.$e->getMessage());
        return back()->with('error', 'Failed to cancel payment: '.$e->getMessage());
    }
}
}