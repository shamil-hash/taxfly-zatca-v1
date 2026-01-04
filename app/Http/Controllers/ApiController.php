<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Adminuser;
use App\Models\Softwareuser;
use App\Models\User_role;
use App\Models\Branch;
use App\Models\Buyproduct;
use App\Models\Returnproduct;
use App\Models\Stockdat;
use App\Models\Accountantloc;
use App\Models\Credituser;
use App\Models\Supplier;
use App\Models\Hrusercreation;
use App\Models\Hruserroles;
use App\Models\Stockdetail;
use App\Models\Salarydata;
use App\Models\UserReport;
use App\Models\Returnpurchase;
use App\Models\PandL;
use App\Models\Category;
use App\Models\Unit;
use App\Models\NewBuyproduct;
use App\Models\BillHistory;
use App\Services\UserService;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Stockhistory;
use App\Models\Bankhistory;
use App\Models\CashSupplierTransaction;
use App\Models\CreditSupplierTransaction;
use App\Models\StockPurchaseReport;



use Carbon\CarbonPeriod;
use PDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use App\Exports\supplierStockPurchaseExport;
use App\Exports\PandLReportExport;

use Maatwebsite\Excel\Facades\Excel;

use App\Services\activityService;
use App\Services\otherService;
use App\Services\PAndLService;
use App\Services\JournalEntryService;

use Stevebauman\Location\Facades\Location;

use Illuminate\Validation\Rule;
class ApiController extends Controller
{
    public function listCreditusers(Request $request)
    {
        // Fetch the user ID from the request input instead of the session
        $userid = $request->input('id');

        // Check if user ID exists in the request
        if ($userid) {
            // Fetch user details for the specific user
            $useritem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();

            // Get admin_id and branch_id from softwareuser
            $adminid = Softwareuser::where('id', $userid)->pluck('admin_id')->first();
            $branchId = Softwareuser::where('id', $userid)->pluck('location')->first(); // Assuming 'location' is branch_id

            // Fetch credit users for the specific branch
            $item = Credituser::leftJoin('branches', 'creditusers.location', '=', 'branches.id')
                ->select(DB::raw("creditusers.name, branches.location, DATE(creditusers.created_at) as created_date,creditusers.trn_number, creditusers.username, creditusers.l_amount, creditusers.current_lamount, creditusers.id, creditusers.phone"))
                ->where('creditusers.location', $branchId)
                ->where('creditusers.status', 1)
                ->orderBy('creditusers.created_at', 'DESC')
                ->get();

        } elseif (Session::has('adminuser')) {
            $adminid = Session::get('adminuser');
            $useritem = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();

            // Fetch all credit users
            $item = Credituser::leftJoin('branches', 'creditusers.location', '=', 'branches.id')
                ->select(DB::raw("creditusers.name, branches.location, DATE(creditusers.created_at) as created_date,creditusers.trn_number, creditusers.username, creditusers.l_amount, creditusers.current_lamount, creditusers.id"))
                ->where('creditusers.status', 1)
                ->orderBy('creditusers.created_at', 'DESC')
                ->get();

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // If admin ID is not set, return an error
        if (!$adminid) {
            return response()->json(['error' => 'Admin ID not set. Please log in again.'], 403);
        }

        // Gather data to return
        $shopdata = Adminuser::Where('id', $adminid)->get();

        // Prepare response data
        $response = [
            'credit_users' => $item,
            'user_details' => $useritem,
            'shop_data' => $shopdata,
        ];

        // Return data as JSON
        return response()->json($response, 200);
    }

    public function billing(Request $request)
    {
        // Check if session is missing 'softwareuser'
        if (!$request->input('id')) {
            return response()->json(['message' => 'User ID is required'], 400);
        }


        // Fetch user ID from the request instead of session
        $userid = $request->input('id');

        if (!$userid) {
            return response()->json(['message' => 'User ID is required'], 400); // Return Bad Request if no user ID
        }

        $count = DB::table('buyproducts')->distinct()->count('transaction_id');
        $branch = DB::table('softwareusers')
            ->where('id', $userid)
            ->pluck('location')
            ->first();

      
        $item = Product::select(
                'products.*', 
                'categories.category_name' // Selecting the category_name from the categories table
            )
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id') // Joining with the categories table
            ->where('products.branch', $branch) // Filtering by branch
            ->where('products.status', 1) // Filtering by status
            ->get();

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

        // Validate user role
        if ($validateuser != '1') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Retrieve shop data
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $shopdata = Adminuser::Where('id', $adminid)->get();

        $user_location = DB::table('softwareusers')
            ->where('id', $userid)
            ->pluck('location')
            ->first();

        // Fetch credit users with joined summaries
        $creditusers = Credituser::where('admin_id', $adminid)
            ->where('status', 1)
            ->where('location', $user_location)
            ->leftJoin('creditsummaries', 'creditusers.id', '=', 'creditsummaries.credituser_id')
            ->select(
                'creditusers.*',
                DB::raw('COALESCE(creditsummaries.due_amount, 0) AS due_amount'),
                DB::raw('COALESCE(creditsummaries.collected_amount, 0) AS collected_amount'),
                DB::raw('CASE
                            WHEN COALESCE(creditsummaries.collected_amount, 0) > COALESCE(creditsummaries.due_amount, 0)
                            THEN COALESCE(creditsummaries.collected_amount, 0) - COALESCE(creditsummaries.due_amount, 0)
                            ELSE NULL
                        END AS balance')
            )
            ->get();

        // Fetch barcode data if provided
        $barcode = $request->barcodenumber;
        $barcodedata = [];
        if ($barcode) {
            $barcodedata = Product::select(DB::raw('*'))
                ->Where('barcode', $barcode)
                ->where('branch', $branch)
                ->where('status', 1)
                ->get();
        }

        $currency = Adminuser::Where('id', $adminid)->pluck('currency')->first();
        $tax = Adminuser::Where('id', $adminid)->pluck('tax')->first();

        $listbank = DB::table('bank')
            ->select('id', 'bank_name', 'account_name', 'status')
            ->where('status', 1)
            ->where('branch', $branch)
            ->get();

        $listemployee = DB::table('employee')
            ->select('first_name', 'id')
            ->where('branch', $branch)
            ->get();

        /* Log IP Address and User Activity */
        $ip = request()->ip();
        $uri = request()->fullUrl();
        $username = Softwareuser::where('id', $userid)->pluck('username')->first();
        $user_type = 'websoftware';
        $message = $username . ' visited billing page';
        $locationdata = (new otherService())->get_location($ip);
        $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        }

        // Return all data as JSON
        return response()->json([
            'creditusers' => $creditusers,
            'items' => $item,
            'currency' => $currency,
            'tax' => $tax,
            // 'listbank' => $listbank,
            // 'listemployee' => $listemployee
        ]);
    }

    public function purchase(Request $request)
    {
        // Fetch user ID from request input
        $userid = $request->input('id');

        // Check if the user ID is provided
        if (!$userid) {
            return response()->json(['message' => 'User ID is required'], 400); // Bad Request if no user ID is provided
        }

        // Fetch user details
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        // Fetch admin details
        $adminid = Softwareuser::where('id', $userid)
            ->pluck('admin_id')
            ->first();

        $shopdata = Adminuser::where('id', $adminid)->get();
        $branch = DB::table('softwareusers')
            ->where('id', $userid) // Use $userid instead of session
            ->pluck('location')
            ->first();

        // Fetch products
        $products = DB::table('products')
            ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
            ->select(DB::raw('products.product_name as product_name, products.id as id, products.unit as unit, products.buy_cost as buy_cost, products.selling_cost as selling_cost, products.rate as rate, products.purchase_vat as purchase_vat'))
            ->groupBy('products.id')
            ->where('products.branch', $branch)
            ->where('products.status', 1)
            ->orderBy('products.id')
            ->get();

        // Fetch suppliers
        $suppliers = DB::table('suppliers')
            ->where('location', $branch)
            ->get();

        // Fetch categories
        $categories = DB::table('categories')
            ->select(DB::raw('categories.category_name, categories.id as category_id, categories.access'))
            ->where('branch_id', $branch)
            ->where('access', 1)
            ->get();

        // Fetch units
        $units = DB::table('units')
            ->select(DB::raw('units.unit, units.id'))
            ->where('branch_id', $branch)
            ->where('status', 1)
            ->get();

        // Fetch receipt numbers
        $receipt_nos = DB::table('stockdetails')
            ->where('branch', $branch)
            ->distinct('stockdetails.reciept_no')
            ->get(['reciept_no']);

        // Fetch tax information
        $tax = Adminuser::where('id', $adminid)
            ->pluck('tax')
            ->first();

        // Fetch bank list
        $listbank = DB::table('bank')
            ->select('id', 'bank_name', 'account_name', 'status', 'current_balance')
            ->where('status', 1)
            ->where('branch', $branch)
            ->get();

        // Prepare the response data
        $response = [
            'tax' => $tax,
            'products' => $products,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'units' => $units,
            'receipt_nos' => $receipt_nos,
            'page' => 'purchase',
        ];

        return response()->json($response, 200); // Return the response in JSON format
    }

    public function product(Request $request)
    {
        // Fetch user ID from request input
        $userid = $request->input('id');

        // Check if the user ID is provided
        if (!$userid) {
            return response()->json(['message' => 'Unauthorized'], 401); // Unauthorized if no user ID
        }

        // Fetch user details
        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        // Fetch branch ID
        $branchid = DB::table('softwareusers')
            ->where('id', $userid)
            ->pluck('location')
            ->first();

        // Fetch product details
        $item = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(DB::raw('products.status, products.product_name, products.productdetails, products.unit, products.buy_cost, products.image, products.product_code, products.barcode, products.selling_cost, products.id as id, products.vat as vat, categories.category_name, categories.id as category_id, categories.access as access, products.rate, products.purchase_vat, products.inclusive_rate, products.inclusive_vat_amount'))
            ->where('branch', $branchid)
            ->paginate(10);

        // Fetch admin and tax details
        $adminid = Softwareuser::where('id', $userid)
            ->pluck('admin_id')
            ->first();

        $tax = Adminuser::where('id', $adminid)
            ->pluck('tax')
            ->first();

        $shopdata = Adminuser::where('id', $adminid)->get();

        // Fetch categories and units
        $xdetails = DB::table('categories')
            ->select(DB::raw('categories.category_name, categories.id as category_id, categories.access'))
            ->where('branch_id', $branchid)
            ->where('access', 1)
            ->get();

        $xunit = DB::table('units')
            ->select(DB::raw('units.unit, units.id'))
            ->where('branch_id', $branchid)
            ->where('status', 1)
            ->get();

        // Prepare and return response data
        return response()->json([
            'tax' => $tax,
            'details' => $item,
            'xdetails' => $xdetails,
            'users' => $useritem,
            'shopdatas' => $shopdata,
            'xunit' => $xunit
        ], 200);
    }

    public function submitproduct(Request $request)
    {
        $userid = $request->input('id');

        // Check if the user ID is provided
        if (!$userid) {
            return response()->json(['message' => 'Unauthorized'], 401); // Unauthorized if no user ID
        }

        // Validate the incoming request
        $request->validate([
            'category_id.*' => 'required',
            'productName.*' => 'required',
            'unit.*' => 'required',
            'buy_cost.*' => 'required|numeric',
            'selling_cost.*' => 'required|numeric',
            'vat.*' => 'required|numeric',
        ]);

        // Fetch branch ID from session
        $branch = DB::table('softwareusers')
        ->where('id', $userid)
        ->pluck('location')
        ->first();
        $product_code = rand(100000000, 200000000);
        $errorMessages = [];

        // Handle new products
        if (!empty($request->productName)) {
            foreach ($request->productName as $key => $productName) {
                $existingProduct = Product::where('product_name', $productName)
                    ->where('branch', $branch)
                    ->first();

                if (!$existingProduct) {
                    $data = new Product();
                    $data->product_name = $productName;
                    $data->productdetails = $request->productdetails[$key];
                    $data->unit = $request->unit[$key];
                    $data->selling_cost = $request->selling_cost[$key];
                    $data->buy_cost = $request->buy_cost[$key];
                    $data->user_id = $userid;
                    $data->branch = $branch;
                    $data->category_id = $request->category_id[$key];
                    $data->vat = $request->vat[$key];
                    $data->rate = $request->rate[$key];
                    $data->purchase_vat = $request->purchase_vat[$key];
                    $data->inclusive_rate = $request->inclusive_rate[$key];
                    $data->inclusive_vat_amount = $request->inclusive_vat_amount[$key];
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
                    $errorMessages[] = "Product '$productName' already exists for the given branch.";
                }
            }
        }

        // Handle updates
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
                    $dataupdate->productdetails = $request->pdetails[$key];
                    $dataupdate->unit = $request->punit[$key];
                    $dataupdate->buy_cost = $request->pbuy_cost[$key];
                    $dataupdate->vat = $request->pvat[$key];
                    $dataupdate->category_id = $request->pcategory_id[$key];
                    $dataupdate->rate = $request->prate[$key];
                    $dataupdate->purchase_vat = $request->ppurchase_vat[$key];
                    $dataupdate->inclusive_rate = $request->pinclusive_rate[$key];
                    $dataupdate->inclusive_vat_amount = $request->pinclusive_vat_amount[$key];
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
                            $dataupdate->image = 'images/logoimage/' . $fileName;
                        } else {
                            \Log::error('File is invalid.');
                        }
                        $dataupdate->save();

                    }


                    $dataupdate->save();
                } else {
                    $errorMessages[] = "Product '{$request->pname[$key]}' already exists for the given branch.";
                }
            }
        }

        // Return response
        if (!empty($errorMessages)) {
            return response()->json(['errors' => $errorMessages], 400); // Return errors if any
        }

        return response()->json(['message' => 'Products added or updated successfully.'], 200); // Success response
    }

    public function submitpurchase(Request $req, JournalEntryService $journal)
    {

        $userid = $req->input('id');

        // Check if the user ID is provided
        if (!$userid) {
            return response()->json(['message' => 'Unauthorized'], 401); // Unauthorized if no user ID
        }

        $branch = DB::table('softwareusers')
        ->where('id', $userid)
        ->pluck('location')
        ->first();

        $req->validate([
            'reciept_no' => 'required|unique:stockdetails,reciept_no',
            'price' => 'required',
            'supplier' => 'required',
            'payment_mode' => 'required',
        ]);

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
                $user->softwareuser = $userid;
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

            foreach ($req->input('product_id') as $key => $productID) {
                $stock = new Stockdetail();
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
                $stock->user_id = $userid;
                // $stock->price = $req->price;
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
                $stock->bank_id =$req->bank_name;
                $stock->account_name =$req->account_name;

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

                if ($req->page == 'purchase_order') {
                    $stock->to_purchase = '1';
                    $stock->purchase_order_trans_ID = $req->purchase_order_id;
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

                    if ($boxDozens[$key] == 1 || $boxDozens[$key] == 3) {
                        $datatwo->stock += $boxItems[$key];
                        $datatwo->remaining_stock += $boxItems[$key];
                    } elseif ($boxDozens[$key] == 2) {
                        $datatwo->stock += $dozenItems[$key];
                        $datatwo->remaining_stock += $dozenItems[$key];
                    }

                    $datatwo->save();
                }

                if (!empty($productID)) {
                    $data = new Stockhistory();
                    $data->user_id = $userid;
                    $data->product_id = $productID;
                    $data->receipt_no = $req->input('reciept_no');
                    $data->buycost = $buycosts[$key];
                    $data->sellingcost = $sellcosts[$key];

                    $data->rate = $rates[$key];
                    $data->vat = $vats[$key];

                    if ($boxDozens[$key] == 1 || $boxDozens[$key] == 3) {
                        $data->quantity = $boxItems[$key];
                        $data->remain_qantity = $boxItems[$key];
                        $data->sell_qantity = $boxItems[$key];
                    } elseif ($boxDozens[$key] == 2) {
                        $data->quantity = $dozenItems[$key];
                        $data->remain_qantity = $dozenItems[$key];
                        $data->sell_qantity = $dozenItems[$key];
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
                    $new_stock->purchase_trans_id = $purchase_trans_id.$branch.$userid.$purchase_insertedData->product.$count.$i;
                    $new_stock->product_id = $purchase_insertedData->product;
                    $new_stock->user_id = $userid;
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

        foreach ($req->input('product_id') as $key => $productID) {
            $pricetotal += $prices[$key];
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
            $credit_supp_trans->user_id = $userid;
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
        } elseif ($req->payment_mode == 1 && ($suppliertId != '' || $suppliertId != null)) {
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
            $cash_trans->user_id = $userid;
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
            $userid = $userid;

            $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

            DB::table('bank')
                ->where('id', $req->bank_name)
                ->where('account_name', $req->account_name)
                ->update(['current_balance' => $new_balance]);

            $bank_history = new Bankhistory();
            $bank_history->reciept_no = $req->input('reciept_no');
            $bank_history->user_id = $userid;
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

        // $userid = Session('softwareuser');
        // $ip = request()->ip();
        // $uri = request()->fullUrl();

        // $username = Softwareuser::where('id', $userid)->pluck('username')->first();

        // $user_type = 'websoftware';

        // $message = $username.' Purchased Stock';
        // $locationdata = (new otherService())->get_location($ip);

        // $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

        // if ($locationdata != false) {
        //     $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
        // }

        try {
            // Find purchase record in buyproducts by matching transaction (receipt) id
            $purchaseId = DB::table('buyproducts')
                ->where('transaction_id', $req->input('reciept_no'))
                ->value('id');

            if ($purchaseId) {
                // Prevent duplicate journal entries in case of retry:
                if (!\App\Models\JournalEntry::where('source_table', 'buyproducts')->where('source_id', $purchaseId)->exists()) {
                    $journal->fromPurchase($purchaseId, 'buyproducts');
                }
            }
        } catch (\Exception $e) {
            \Log::error('Journal entry generation failed for purchase: ' . $e->getMessage());
        }

        /* ----------------------------------------------------------------------- */

        if ($req->page == 'edit_purchase_draft') {
            DB::table('purchasedraft')->where('reciept_no', $req->receipt_no)->delete();
        }

        if ($req->page == 'purchase_order') {
            return redirect('/purchasehistory')->with('success', 'Data uploaded successfully!');
        } elseif ($req->page == 'edit_purchase_draft') {
            return redirect('/purchasestock')->with('success', 'Data uploaded successfully!');
        } else {
            return response()->json(['message' => 'Products added or updated successfully.'], 200); // Success response
        }
    }
    public function purchaseHistoryApi(Request $request)
    {
        // Check for authenticated user
        $userid = $request->input('id');

        // Check if the user ID is provided
        if (!$userid) {
            return response()->json(['message' => 'Unauthorized'], 401); // Unauthorized if no user ID
        }


        // Fetch user information
        // $useritem = DB::table('softwareusers')
        //     ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
        //     ->where('user_id', $userid)
        //     ->get();

        $branch = DB::table('softwareusers')
            ->where('id', $userid)
            ->pluck('location')
            ->first();

        // Fetch admin-related data
        $adminid = Softwareuser::where('id', $userid)->pluck('admin_id')->first();
        $currency = Adminuser::where('id', $adminid)->pluck('currency')->first();
        $tax = Adminuser::where('id', $adminid)->pluck('tax')->first();

        // Fetch purchase data
        $purchase = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->leftJoin('stock_purchase_reports', function ($join) {
                $join->on(DB::raw('CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_general_ci'), '=', DB::raw('CONVERT(stock_purchase_reports.receipt_no USING utf8mb4) COLLATE utf8mb4_general_ci'))
                    ->on('stockdetails.product', '=', 'stock_purchase_reports.product_id');
            })
            ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, stock_purchase_reports.purchase_id as purchase_id'))
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

            // Check if there is a purchase return
            $purchase_return = DB::table('returnpurchases')
                ->where('reciept_no', $purcs->reciept_no)
                ->exists();

            $purcs->purchase_return = $purchase_return;
        }

        // Define date parameters
        $start_date = '';
        $end_date = '';

        // Return JSON response
        return response()->json([
            'tax' => $tax,
            // 'users' => $useritem,
            'purchases' => $purchase,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'currency' => $currency
        ]);
    }

    public function submitunit(Request $req)
{
    // Validate the input
    $userid = $req->input('id');

    // Check if the user ID is provided
    if (!$userid) {
        return response()->json(['message' => 'Unauthorized'], 401); // Unauthorized if no user ID
    }
    $req->validate([
        'unit' => 'required|string|max:255',
    ]);


    // Fetch branch and user details
    $branch = DB::table('softwareusers')
        ->where('id', $userid)
        ->pluck('location')
        ->first();

    // Create and save the new unit
    $units = new Unit();
    $units->branch_id = $branch;
    $units->user_id = $userid;
    $units->unit = $req->unit;
    $units->save();

    // Log the activity (optional)
    $ip = $req->ip();
    $uri = $req->fullUrl();
    $username = Softwareuser::where('id', $userid)->pluck('username')->first();
    $user_type = 'websoftware';
    $message = $username . ' created new unit ' . $req->unit;
    $locationdata = (new otherService())->get_location($ip);

    if ($locationdata !== false) {
        $branch_id = $branch;
        $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
    }

    // Return success response
    return response()->json([
        'message' => 'Unit created successfully',
        'unit' => $units,
    ], 201);
}


public function submitcategory(Request $req)
{
    // Validate the input
    $userid = $req->input('id');

    // Check if the user ID is provided
    if (!$userid) {
        return response()->json(['message' => 'Unauthorized'], 401); // Unauthorized if no user ID
    }
    $req->validate([
        'categoryname' => 'required|string|max:255',
    ]);



    // Fetch branch and user details
    $branch = DB::table('softwareusers')
        ->where('id', $userid)
        ->pluck('location')
        ->first();

    // Create and save the new category
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

    // Log the activity (optional)
    $ip = $req->ip();
    $uri = $req->fullUrl();
    $username = Softwareuser::where('id', $userid)->pluck('username')->first();
    $user_type = 'websoftware';
    $message = $username . ' created category ' . $req->categoryname;
    $locationdata = (new otherService())->get_location($ip);

    if ($locationdata !== false) {
        $branch_id = $branch;
        $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
    }

    // Return success response
    return response()->json([
        'message' => 'Category created successfully',
        'category' => $categories,
    ], 201);
}
public function submitsupplier(Request $req)
{
    $userid = $req->input('id');
    $adminid = $req->input('admin_id');

    // Check if the user ID is provided
    if (!$userid) {
        return response()->json(['message' => 'no user Unauthorized'], 401); // Unauthorized if no user ID
    }elseif(!$adminid){
        return response()->json(['message' => 'no admin Unauthorized'], 401); // Unauthorized if no user ID

    }
        $userType = null;

    if ($userid) {
        $userType = 'softwareuser'; // Set userType to softwareuser if userid is present
    }

    if ($adminid) {
        $userType = 'adminuser'; // Set userType to adminuser if adminid is present
    }
    // Validate the input
    $req->validate([
        'name' => 'required',
        'mobile' => 'numeric',
        'email' => 'nullable|email',
        'location' => 'required',
    ], [
        'name.required' => 'The name field is required.',
        'mobile.numeric' => 'The mobile field must be numeric.',
        'email.email' => 'Please enter a valid email address.',
    ]);

    // Custom validation to check for existing suppliers with the same name and location
    $existingSupplier = Supplier::where('name', $req->input('name'))
        ->where('location', $req->input('location'))
        ->first();

    if ($existingSupplier) {
        return response()->json(['message' => 'A supplier with the same name already exists under this branch.'], 400);
    }

    // Create a new supplier
    $supplier = new Supplier();
    $supplier->name = $req->input('name');
    $supplier->location = $req->input('location');
    $supplier->mobile = $req->input('mobile');
    $supplier->address = $req->input('address');
    $supplier->email = $req->input('email');
    $supplier->trn_number = $req->input('trn_number');
    $supplier->trade_no = $req->input('trade_license_no');
    $supplier->adminuser = isset($adminid) ? $adminid : null;
    $supplier->softwareuser = isset($userid) ? $userid : null;
    $supplier->business_name = $req->input('business_name');
    $supplier->billing_add = $req->input('billing_address');
    $supplier->deli_add = $req->input('delivery_address');
    $supplier->billing_city = $req->input('billing_city');
    $supplier->deli_city = $req->input('delivery_city');
    $supplier->billing_state = $req->input('billing_state');
    $supplier->deli_state = $req->input('delivery_state');
    $supplier->billing_postal = $req->input('billing_zip');
    $supplier->deli_postal = $req->input('delivery_zip');
    $supplier->billing_landmark = $req->input('billing_landmark');
    $supplier->deli_landmark = $req->input('delivery_landmark');
    $supplier->billing_country = $req->input('billing_country');
    $supplier->deli_country = $req->input('delivery_country');
    $supplier->b_accountname = $req->input('accountName');
    $supplier->b_bankname = $req->input('bank_name');
    $supplier->b_branch = $req->input('branch');
    $supplier->b_openingbalance = $req->input('openingBalance');
    $supplier->b_ifsc = $req->input('ifscCode');
    $supplier->b_iban = $req->input('ibanCode');
    $supplier->b_accountno = $req->input('account_number');
    $supplier->b_date = $req->input('date');
    $supplier->b_accounttype = $req->input('accountType');
    $supplier->b_upiid = $req->input('upiid');
    $supplier->b_country = $req->input('country');
    $supplier->save();

    // Log the activity (optional)
    $ip = $req->ip();
    $uri = $req->fullUrl();
    $username = isset($adminid) ? Adminuser::where('id', $adminid)->pluck('username')->first() : Softwareuser::where('id', $userid)->pluck('username')->first();
    $user_type = $userType == 'adminuser' ? 'webadmin' : 'websoftware';
    $message = "$username created supplier named " . $req->input('name');

    // Get location information for the log
    $locationdata = (new OtherService())->get_location($ip);

    if ($locationdata !== false) {
        $activityservice = (new ActivityService($adminid ?? $userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
    }

    // Return success response
    return response()->json([
        'message' => 'Supplier created successfully!',
        'supplier' => $supplier,
    ], 201);
 }
// public function submitbill(Request $request)
//      {
//     $request->validate([
//         'product_id' => 'required|array',
//     ]);

//     // Get all product IDs
//     $productIds = $request->product_id;

  
//     $count = 0;

//     // Loop through the product IDs
//     foreach ($productIds as $key => $productID) {
//         $count++;
//         $processedProducts[] = [
//             'count' => $count,
//         ];
//     }

//     // Return the product IDs and tracking info as a JSON response
//     return response()->json([
//         'product_ids' => $productIds,
//         'total_count' => $count,
//     ]);
//  }
public function submitbill(Request $request)
{
    $userid = $request->input('id');

    // Check if the user ID is provided
    if (!$userid) {
        return response()->json(['message' => 'Unauthorized'], 401); // Unauthorized if no user ID
    }
    $request->validate([
        'productName' => 'required',
    ]);

    $branch = DB::table('softwareusers')
        ->where('id', $userid)
        ->pluck('location')
        ->first();

    $count = DB::table('buyproducts')
        ->distinct()
        ->count('transaction_id');

    ++$count;

    $admin_id = DB::table('softwareusers')
        ->where('id', $userid)
        ->pluck('admin_id')
        ->first();

    $transdefault = DB::table('adminusers')
        ->where('id', $admin_id)
        ->pluck('transpart')
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
        // $data->employee_id = $request->employee_id;
        // $data->employee_name = $request->employee_name;
        // $data->email = $request->email;
        $data->trn_number = $request->trn_number;
        // $data->phone = $request->phone;
        $data->price = $request->price[$key];
        $data->total_amount = $request->total_amount[$key];
        $data->payment_type = $request->payment_type;
        $data->user_id = $userid;
        $data->branch = $branch;
        $data->one_pro_buycost = $request->buy_cost[$key];
        $data->mrp = $request->mrp[$key];
        $data->fixed_vat = $request->fixed_vat[$key];
        // $data->bank_id = $request->bank_name;
        // $data->account_name = $request->account_name;

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
            // $data->discount_type = $request->dis_count_type[$key];

            // if ($request->vat_type_value == 1) {
            //     if ($request->dis_count_type[$key] == 'none') {
            //         $data->discount = $request->dis_count[$key];
            //     } elseif ($request->dis_count_type[$key] == 'percentage') {
            //         $data->discount = $request->dis_count[$key];
            //         $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
            //     } elseif ($request->dis_count_type[$key] == 'amount') {
            //         $data->discount_amount = $request->dis_count[$key];
            //         $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
            //     }
            // } elseif ($request->vat_type_value == 2) {
            //     if ($request->dis_count_type[$key] == 'none') {
            //         $data->discount = $request->dis_count[$key];
            //     } elseif ($request->dis_count_type[$key] == 'percentage') {
            //         $data->discount = $request->dis_count[$key];
            //         $data->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
            //     } elseif ($request->dis_count_type[$key] == 'amount') {
            //         $data->discount_amount = $request->dis_count[$key];
            //         $data->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
            //     }
            // }
        }

        // $data->totalamount_wo_discount = $request->total_amount_wo_discount[$key];
        // $data->price_wo_discount = $request->price_withoutvat_wo_discount[$key];

        // $data->total_discount_type = $request->total_discount;
        // $data->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100);
        // $data->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100));

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
                // $billhistory->discount_type = $request->dis_count_type[$key];

                // if ($request->vat_type_value == 1) {
                //     if ($request->dis_count_type[$key] == 'none') {
                //         $billhistory->discount = $request->dis_count[$key];
                //     } elseif ($request->dis_count_type[$key] == 'percentage') {
                //         $billhistory->discount = $request->dis_count[$key];
                //         $billhistory->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                //     } elseif ($request->dis_count_type[$key] == 'amount') {
                //         $billhistory->discount_amount = $request->dis_count[$key];
                //         $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                //     }
                // } elseif ($request->vat_type_value == 2) {
                //     if ($request->dis_count_type[$key] == 'none') {
                //         $billhistory->discount = $request->dis_count[$key];
                //     } elseif ($request->dis_count_type[$key] == 'percentage') {
                //         $billhistory->discount = $request->dis_count[$key];
                //         $billhistory->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                //     } elseif ($request->dis_count_type[$key] == 'amount') {
                //         $billhistory->discount_amount = $request->dis_count[$key];
                //         $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                //     }
                // }
            }

            // $billhistory->total_discount_type = $request->total_discount;
            // $billhistory->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100);
            // $billhistory->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100));
            
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
            $billhistory->user_id = $userid;
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
                // $billhistory->discount_type = $request->dis_count_type[$key];

                // if ($request->vat_type_value == 1) {
                //     if ($request->dis_count_type[$key] == 'none') {
                //         $billhistory->discount = $request->dis_count[$key];
                //     } elseif ($request->dis_count_type[$key] == 'percentage') {
                //         $billhistory->discount = $request->dis_count[$key];
                //         $billhistory->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                //     } elseif ($request->dis_count_type[$key] == 'amount') {
                //         $billhistory->discount_amount = $request->dis_count[$key];
                //         $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                //     }
                // } elseif ($request->vat_type_value == 2) {
                //     if ($request->dis_count_type[$key] == 'none') {
                //         $billhistory->discount = $request->dis_count[$key];
                //     } elseif ($request->dis_count_type[$key] == 'percentage') {
                //         $billhistory->discount = $request->dis_count[$key];
                //         $billhistory->discount_amount = ($request->mrp[$key] * ($request->dis_count[$key] / 100));
                //     } elseif ($request->dis_count_type[$key] == 'amount') {
                //         $billhistory->discount_amount = $request->dis_count[$key];
                //         $billhistory->discount = ($request->dis_count[$key] / $request->mrp[$key]) * 100;
                //     }
                // }
            }

            // $billhistory->total_discount_type = $request->total_discount;
            // $billhistory->total_discount_percent = ($request->discount_percentage != '' || $request->discount_percentage != null) ? $request->discount_percentage : (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100);
            // $billhistory->total_discount_amount = ($request->discount_amount != '' || $request->discount_amount != null) ? $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100));
            
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
                    $billhistory->user_id = $userid;
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
                    $billhistory->user_id = $userid;
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



/* ------------------GET IP ADDRESS--------------------------------------- */

    $userid = $userid;
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
        DB::table('billdraft')->where('transaction_id', $request->transaction_id)->delete();
    }

    return response()->json([
        'message' => 'Transaction completed successfully.',
        'redirect_url' => url('/billdeskfinalreciept/' . $transaction),
    ], 200);
}

public function submitcustomer(Request $req)
{
    $userid = $req->input('id');
    $adminid = $req->input('admin_id');

    // Check if the user ID or admin ID is provided
    if (!$userid && !$adminid) {
        return response()->json(['message' => 'Unauthorized access: No user or admin ID provided'], 401);
    }

    // Determine user type
    $userType = $userid ? 'softwareuser' : 'adminuser';

    // Validate input data
    // $req->validate([
    //     'name' => 'required|string|max:255',
    //     'trn_number' => 'nullable|string|max:100',
    // ], [
    //     'name.required' => 'The name field is required.',
    //     'name.string' => 'The name must be a valid string.',
    //     'trn_number.string' => 'The TRN number must be a valid string.',
    // ]);

    // Create a new Credituser instance
    $user = new Credituser();
    $user->name = $req->input('name');
    $user->trn_number = $req->input('trn_number');
    $user->location = $req->input('location');

    // Assign admin or software user IDs
    $user->admin_id = $adminid ?? null;
    $user->user_id = $userid ?? null;

    // Save the customer data
    $user->save();

    // Log the activity (optional)
    $ip = $req->ip();
    $uri = $req->fullUrl();
    $username = $adminid
        ? Adminuser::where('id', $adminid)->pluck('username')->first()
        : Softwareuser::where('id', $userid)->pluck('username')->first();
    $message = "$username created a customer named " . $req->input('name');
    $user_type = $userType == 'adminuser' ? 'webadmin' : 'websoftware';

    // Retrieve location information
    $locationdata = (new OtherService())->get_location($ip);

    if ($locationdata !== false) {
        $activityservice = new ActivityService($adminid ?? $userid, $ip, $uri, $message, $user_type, $locationdata);
        $activityservice->ipaddress_store(0);
    }

    // Return success response
    return response()->json([
        'message' => 'Customer created successfully!',
        'customer' => $user,
    ], 201);
}

public function listCategory(Request $request)
{
    $userid = $request->input('id');

    // Check if the user ID is provided
    if (!$userid) {
        return response()->json(['message' => 'User ID is required'], 400); // Bad Request if no user ID is provided
    }
    $branch = DB::table('softwareusers')
        ->where('id', $userid)
        ->pluck('location')
        ->first();
    $useritem = DB::table('softwareusers')
        ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
        ->where('user_id', $userid)
        ->get();
    $categories = DB::table('categories')
        ->where('branch_id', $branch)
        ->get();

    // $units = DB::table('units')
    //     ->where('branch_id', $branch)
    //     ->paginate(10);

        return response()->json([
            'users' => $useritem,
            'categories' => $categories,
            // 'units' => $units,
        ]);
}
public function dailysalesreport(Request $request)
{
    $userid = $request->input('id');

    // Check if the user ID is provided
    if (!$userid) {
        return response()->json(['message' => 'User ID is required'], 400); // Bad Request if no user ID is provided
    }
    $adminid = DB::table('softwareusers')
        ->where('id', $userid)
        ->pluck('admin_id')
        ->first();

    $currency = DB::table('adminusers')
        ->where('id', $adminid)
        ->pluck('currency')
        ->first();

    $date = $request->input('date'); // Accept date as a query parameter
      $branch = $request->input('location');

 if (!$date || !$branch) {
        return response()->json(['error' => 'Date and Branch are required.'], 400);
    }
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

    // Fetch sales data for the given date
    $salesData = DB::table('buyproducts')
        ->select(
            'product_name',
            'quantity',
            'total_amount',
            DB::raw('total_amount - (total_amount * (COALESCE(total_discount_percent, 0) / 100)) as final_amount')
        )
        ->whereDate('created_at', $date)
        ->where('branch', $branch)
        ->get();

    $branchname = DB::table('branches')
        ->where('id', $branch)
        ->pluck('location')
        ->first();

    // Prepare response data
    $response = [
        'salesData' => $salesData,
        'date' => $date,
        'po_box' => $po_box,
        'tel' => $tel,
        'admintrno' => $admintrno,
        'logo' => $logo,
        'company' => $company,
        'Address' => $Address,
        'branchname' => $branchname,
        'currency' => $currency,
    ];

    return response()->json($response, 200);
}
public function dailysalesreportpayment(Request $request)
{
    $userid = $request->input('id');

    // Check if the user ID is provided
    if (!$userid) {
        return response()->json(['message' => 'User ID is required'], 400); // Bad Request if no user ID is provided
    }
    $adminid = DB::table('softwareusers')
        ->where('id', $userid)
        ->pluck('admin_id')
        ->first();

    $currency = DB::table('adminusers')
        ->where('id', $adminid)
        ->pluck('currency')
        ->first();

    $date = $request->input('date'); // Accept date as a query parameter
      $branch = $request->input('location');

 if (!$date || !$branch) {
        return response()->json(['error' => 'Date and Branch are required.'], 400);
    }


    // Fetch sales data for the given date
    $cashSum = DB::table('buyproducts')
    ->whereDate('created_at', $date)
    ->where('payment_type', '1')
    ->where('branch', $branch)
    ->sum(DB::raw('total_amount - (total_amount * (COALESCE(total_discount_percent, 0) / 100))'));

    $creditSum = DB::table('buyproducts')
    ->whereDate('created_at', $date)
    ->where('payment_type', '3')
    ->where('branch', $branch)
    ->sum(DB::raw('total_amount - (total_amount * (COALESCE(total_discount_percent, 0) / 100))'));

    $posSum = DB::table('buyproducts')
    ->whereDate('created_at', $date)
    ->where('payment_type', '4')
    ->where('branch', $branch)
    ->sum(DB::raw('total_amount - (total_amount * (COALESCE(total_discount_percent, 0) / 100))'));


    $branchname = DB::table('branches')
        ->where('id', $branch)
        ->pluck('location')
        ->first();

    // Prepare response data
    $response = [
        'cashSum' => $cashSum,
        'creditSum' => $creditSum,
        'posSum' => $posSum,
        'date' => $date,
       
        'branchname' => $branchname,
        'currency' => $currency,
    ];

    return response()->json($response, 200);
}
public function billhistory(Request $req)
{
    $userid = $req->input('id');
    $adminid = $req->input('admin_id');

    // Check if the user ID is provided
    if (!$userid) {
        return response()->json(['message' => 'no user Unauthorized'], 401); // Unauthorized if no user ID
    }elseif(!$adminid){
        return response()->json(['message' => 'no admin Unauthorized'], 401); // Unauthorized if no user ID

    }


        $branch = DB::table('softwareusers')
            ->where('id', $userid)
            ->pluck('location')
            ->first();

     $data = DB::table('buyproducts')
            ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
            ->leftJoin('creditusers', 'buyproducts.credit_user_id', '=', 'creditusers.id')
            ->leftJoin(DB::raw('(SELECT
                        transaction_id COLLATE utf8mb4_unicode_ci as transaction_id,
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
            ->where('buyproducts.branch', $branch)
            ->get();

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



        $item = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $adminid)
            ->get();

        $shopdata = Adminuser::Where('id', $adminid)
            ->get();

    $currency = Adminuser::Where('id', $adminid)
        ->pluck('currency')
        ->first();

        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();

    $start_date = '';
    $end_date = '';


        return response()->json([
            'products' => $data,
            'users' => $item,
            'currency' => $currency,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'adminid' => $adminid,
            'creditusers' => $creditusers,
            'credit_user_id' => $credit_user_id,
            'tax'=>$tax,
        ]);
}
public function billhistorydate(Request $req, UserService $userservice)
{
    $userId = $req->input('id');
    $adminId = $req->input('admin_id');

    // Check if the user ID is provided
    if (!$userId) {
        return response()->json(['message' => 'no user Unauthorized'], 401); // Unauthorized if no user ID
    }elseif(!$adminId){
        return response()->json(['message' => 'no admin Unauthorized'], 401); // Unauthorized if no user ID

    }

        $branch = Softwareuser::where('id', $userId)->value('location');
        // $adminId = Softwareuser::where('id', $userId)->value('admin_id');

        $creditusers = Credituser::Where('admin_id', $adminId)
            ->where('status', 1)
            ->where('location', $branch)
            ->get();

        $credit_user_id = $req->credit_user_id;
        $start_date=$req->start_date;
        $end_date = $req->end_date;


      $query = DB::table('buyproducts')
            ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
            ->leftJoin('creditusers', 'buyproducts.credit_user_id', '=', 'creditusers.id')
            ->leftJoin(DB::raw('(SELECT
                        transaction_id COLLATE utf8mb4_unicode_ci as transaction_id,
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

        if ($req->filled('start_date') && $req->filled('end_date')) {
            $query->whereBetween('buyproducts.created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59']);
        } elseif ($req->filled('start_date')) {
            $query->whereDate('buyproducts.created_at', $start_date);
        }

        if ($req->filled('credit_user_id')) {
            $query->where('buyproducts.credit_user_id', $req->credit_user_id);
        }

        $data = $query->get();
        $item = $userservice->getUserDetails($userId);

    $currency = Adminuser::where('id', $adminId)->value('currency');
    $tax = Adminuser::Where('id', $adminId)
    ->pluck('tax')
    ->first();
    // $start_date = $req->start_date;
    // $end_date = $req->end_date;


    return response()->json([
      'products' => $data,
        'users' => $item,
        'currency' => $currency,
        'tax'=>$tax,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'adminid' => $adminId,
        'shopdatas' => isset($shopdata) ? $shopdata : null,
        'creditusers' => isset($creditusers) ? $creditusers : null,
        'credit_user_id' => isset($credit_user_id) ? $credit_user_id : null,
    ]);
}
public function purchaseHistorydate(Request $req)
{
    $userid = $req->input('id');

    // Check if the user ID is provided
    if (!$userid) {
        return response()->json(['message' => 'no user Unauthorized'], 401); // Unauthorized if no user ID
    }

    $useritem = DB::table('softwareusers')
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
        $currency = Adminuser::Where('id', $adminid)
        ->pluck('currency')
        ->first();

        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();

        $start_date = $req->start_date;
        $end_date = $req->end_date;

    if ($start_date != $end_date) {

        $purchase = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->leftJoin('stock_purchase_reports', function ($join) {
                $join->on(DB::raw('CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_general_ci'), '=', DB::raw('CONVERT(stock_purchase_reports.receipt_no USING utf8mb4) COLLATE utf8mb4_general_ci'))
                    ->on('stockdetails.product', '=', 'stock_purchase_reports.product_id');
            })
            ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, stock_purchase_reports.purchase_id as purchase_id'))
            ->groupBy('stockdetails.reciept_no')
            ->orderBy('stockdetails.created_at', 'DESC')
            ->where('stockdetails.branch', $branch)
            ->whereBetween('stockdetails.created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])
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
    } elseif ($start_date == $end_date && $start_date != '') {


        $purchase = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->leftJoin('stock_purchase_reports', function ($join) {
                $join->on(DB::raw('CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_general_ci'), '=', DB::raw('CONVERT(stock_purchase_reports.receipt_no USING utf8mb4) COLLATE utf8mb4_general_ci'))
                    ->on('stockdetails.product', '=', 'stock_purchase_reports.product_id');
            })
            ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, stock_purchase_reports.purchase_id as purchase_id'))
            ->groupBy('stockdetails.reciept_no')
            ->orderBy('stockdetails.created_at', 'DESC')
            ->where('stockdetails.branch', $branch)
            ->whereDate('stockdetails.created_at', $start_date)
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
        $purchase = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->leftJoin('stock_purchase_reports', function ($join) {
                $join->on(DB::raw('CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_general_ci'), '=', DB::raw('CONVERT(stock_purchase_reports.receipt_no USING utf8mb4) COLLATE utf8mb4_general_ci'))
                    ->on('stockdetails.product', '=', 'stock_purchase_reports.product_id');
            })
            ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount, stock_purchase_reports.purchase_id as purchase_id'))
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


    return response()->json([
        'tax' => $tax,
        'users' => $useritem,
        'purchases' => $purchase,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'currency' => $currency
    ]);
    }
    
    public function billhistoryview(Request $req)
    {
        $userid = $req->input('id');
        $transaction_id = $req->input('transaction_id');


    // Check if the user ID is provided
    if (!$userid) {
        return response()->json(['message' => 'no user Unauthorized'], 401); // Unauthorized if no user ID
    }

        $count = DB::table('buyproducts')->count();


        $item = Buyproduct::select([
                'product_name',
                'quantity',
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
            ->where('transaction_id', $transaction_id)
            ->get();



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


            return response()->json([
               'details' => $item,
                'users' => $useritem,
                'currency' => $currency,
                'tax'=>$tax,
            ]);

    }
    
    public function purchaseHistoryview(Request $req)
    {
        $userid = $req->input('id');
        $receipt_no = $req->input('receipt_no');


    // Check if the user ID is provided
    if (!$userid) {
        return response()->json(['message' => 'no user Unauthorized'], 401); // Unauthorized if no user ID
    }
        $count = DB::table('stockdetails')->count();

        $item = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->select(DB::raw('
            stockdetails.id,
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

        return response()->json([
            'tax' => $tax,
            'details' => $item,
            'users' => $useritem,
            'currency' => $currency
        ]);
            }
}
