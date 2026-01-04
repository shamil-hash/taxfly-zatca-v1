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
use App\Models\Accountexpense;

use Carbon\Carbon;
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

use Stevebauman\Location\Facades\Location;

use Illuminate\Validation\Rule;

class AdminController extends Controller
{
   function dashBoard()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');

        $item = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();


                
    
            $adminid = Softwareuser::where('id', $userid)->pluck('admin_id')->first();
            
    
            $userdata = Softwareuser::where('id', $userid)->get();
    
            $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
    
    
    
    
            $today = Carbon::today()->format('Y-m-d');
    
            $todaysale = DB::table('buyproducts')
            ->select(DB::raw('SUM(DISTINCT COALESCE(bill_grand_total, 0)) as total_sales'))
            ->whereDate('created_at', $today)
            ->groupBy('transaction_id')
            ->get()
            ->sum('total_sales');
            
     $returnData = Returnproduct::whereDate('created_at', $today)
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
            ->whereDate('created_at', $today)
            ->sum(DB::raw('service_cost * quantity'));
    
        $servicesTotal = DB::table('service')
            ->whereDate('created_at', $today)
            ->sum('total_amount');
    
        $todayservice = $buyproductsTotal + $servicesTotal;
    
        $todaypurchase = Stockdetail::whereDate('created_at', $today)
            ->select(DB::raw('SUM(COALESCE(price, 0)) - SUM(COALESCE(discount, 0)) as total_price'))
            ->groupBy('reciept_no')
            ->get()
            ->sum('total_price');
    
            
    
            
        $transaction_count = DB::table('buyproducts')
            ->whereDate('created_at', $today)
            ->distinct()
            ->count('transaction_id');
     $lowStockItems = DB::table('products')
      ->select('product_name', 'remaining_stock')
         ->where('status', 1)
        ->where('remaining_stock', '<', 5)
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
                ->count();
        }
    
        $returned = [];
        foreach ($year as $key => $value) {
            // Compare using full date format (Y-m-d)
            $returned[] = Returnproduct::where(\DB::raw("DATE(created_at)"), Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d'))
                ->count();
        }
    
        $purchase = [];
        foreach ($year as $key => $value) {
            // Compare using full date format (Y-m-d)
            $purchase[] = Stockdetail::where(\DB::raw("DATE(created_at)"), Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d'))
                ->count();
        }
    
        $returnpurchase = [];
        foreach ($year as $key => $value) {
            // Compare using full date format (Y-m-d)
            $returnpurchase[] = Returnpurchase::where(\DB::raw("DATE(created_at)"), Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d'))
                ->count();
        }
    
        $supplier = DB::table('supplier_credits')
        ->join('suppliers', 'supplier_credits.supplier_id', '=', 'suppliers.id')
        ->select(DB::raw('COALESCE(SUM(due_amt - collected_amt), 0) as amount_difference'))
        ->value('amount_difference');
    
        $credit = DB::table('creditsummaries')
        ->join('creditusers', 'creditsummaries.credituser_id', '=', 'creditusers.id')
        ->select(DB::raw('COALESCE(SUM(due_amount - collected_amount), 0) as amount_difference'))
        ->value('amount_difference');
    
        $topProducts = DB::table('buyproducts')
        ->select('product_id', 'product_name', DB::raw('SUM(remain_quantity) as total_sales'))
        ->groupBy('product_id')
        ->orderByDesc('total_sales')
        ->limit(3)
        ->get();
    
    // Get bottom 3 performing products
        $bottomProducts = DB::table('buyproducts')
            ->select('product_id', 'product_name', DB::raw('SUM(remain_quantity) as total_sales'))
            ->groupBy('product_id')
            ->orderBy('total_sales')
            ->limit(3)
            ->get();
    
            $todayexpenses = Accountexpense::whereDate('created_at', $today)
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
            ->groupBy('credit_id')
            ->orderByDesc('transaction_count')
            ->limit(3)
            ->get();
        
        $username = Adminuser::Where('id', $adminid)
            ->pluck('username')
            ->first();
    
    
    
            return view('/admin/admindashboard', array('username'=>$username,'topCustomers'=>$topCustomers,'todayexpenses'=>$todayexpenses,'topProducts'=>$topProducts,'bottomProducts'=>$bottomProducts,'supplier'=>$supplier,'credit'=>$credit,'todayreturn'=>$todayreturn,'lowStockItems'=>$lowStockItems,'transaction_count'=>$transaction_count,'todayservice'=>$todayservice,'userid'=>$userid,'currency'=>$currency,'todaysale'=>$todaysale,'todaypurchase'=>$todaypurchase,'users' => $item,'userdatas'=>$userdata))->with('year', json_encode($dyear))->with('user', json_encode($user, JSON_NUMERIC_CHECK))->with('returned', json_encode($returned, JSON_NUMERIC_CHECK))->with('purchase', json_encode($purchase, JSON_NUMERIC_CHECK))->with('returnpurchase', json_encode($returnpurchase, JSON_NUMERIC_CHECK));
    
    }
    function createUser()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $item = DB::table('branches')
            ->get();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        return view('/admin/createuser', array('locations' => $item, 'users' => $useritem, 'shopdatas' => $shopdata));
    }
    function createUserform(Request $req)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        // $req->validate([
        //     'name' => 'required|min:5',
        //     'username' => 'required|min:5|unique:softwareusers,username',
        //     'password' => 'required|min:5',
        //     'location' => 'required',
        // ]);

        $req->validate([
            'name' => 'required|min:5',
            'username' => 'required|min:5|unique:softwareusers,username',
            'password' => 'required|min:5',
            'location' => 'required',
            'joined_date' => 'required',
        ], [
            'name.required' => 'The name field is required.',
            'name.min' => 'The name must be at least 5 characters.',
            'username.required' => 'The username field is required.',
            'username.min' => 'The username must be at least 5 characters.',
            'username.unique' => 'The username is already in use.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 5 characters.',
            'location.required' => 'The location field is required.',
        ]);

        //   Softwareuser::create($req->all());
        $user = new Softwareuser;
        $user->name = $req->input('name');
        $user->username = $req->input('username');
        $user->location = $req->input('location');
        $user->admin_id = Session('adminuser');
        $user->password = Hash::make($req->input('password'));
        $user->joined_date = $req->input('joined_date');
        $user->email = $req->input('email');
        $user->admin_status = 1;
        $user->save();

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $userid = Session('adminuser');

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " created user named " . $req->input('username');
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return back()->with('success', 'User created successfully!');
    }
    function listDesk()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('role_id', 1)
            ->get();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        return view('admin/listdesk', array('desks' => $item, 'users' => $useritem, 'shopdatas' => $shopdata));
    }
    function listInventory()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->leftJoin('branches', 'softwareusers.location', '=', 'branches.id')
            ->where('role_id', 2)
            ->get();

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        return view('/admin/listinventory', array('inventorys' => $item, 'users' => $useritem, 'shopdatas' => $shopdata));
        // return $item;
    }
    function listTeamleader()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('role_id', 7)
            ->get();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        return view('/admin/listteamleader', array('leaders' => $item, 'users' => $useritem, 'shopdatas' => $shopdata));
    }
    function listManager()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('role_id', 5)
            ->get();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        return view('/admin/listmanager', array('managers' => $item, 'users' => $useritem, 'shopdatas' => $shopdata));
    }
    function listCustomersupport()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('role_id', 4)
            ->get();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        return view('/admin/listcustomersupport', array('customers' => $item, 'users' => $useritem, 'shopdatas' => $shopdata));
    }
    function listAnalytics()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('role_id', 3)
            ->get();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        return view('/admin/listanalytics', array('analytics' => $item, 'users' => $useritem, 'shopdatas' => $shopdata));
    }
    function listAccountant()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->select(DB::raw("softwareusers.name,softwareusers.username,softwareusers.created_at,softwareusers.id"))
            ->where('user_roles.role_id', 9)
            ->get();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        $branchdata = Branch::get();


        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited list accountant page";
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/
        return view('/admin/listaccountant', array('accountants' => $item, 'branches' => $branchdata, 'users' => $useritem, 'shopdatas' => $shopdata));
    }
    function listMarketing()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('role_id', 6)
            ->get();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        return view('/admin/listmarketing', array('marketings' => $item, 'users' => $useritem, 'shopdatas' => $shopdata));
    }
    function listUser()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $item = Softwareuser::leftJoin('branches', 'softwareusers.location', '=', 'branches.id')
            ->select(DB::raw("softwareusers.name,branches.location,softwareusers.status,softwareusers.created_at,softwareusers.username,softwareusers.id,softwareusers.access,softwareusers.last_login,softwareusers.last_logout, softwareusers.login_ipaddress"))
            ->where('admin_id', Session('adminuser'))
            ->get();
        $roles = DB::table('user_roles')->get();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited list user page";
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return view('/admin/listuser', array('softusers' => $item, 'roles' => $roles, 'users' => $useritem, 'shopdatas' => $shopdata));
    }
    //
    public function addRoles(Request $request)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        if (User_role::where('user_id', $request->user_id)->exists()) {
            DB::table('user_roles')->where('user_id', $request->user_id)->delete();
        }
        if ($request->role == null) {
            return redirect('listuser');
        }
        foreach ($request->role as $key => $role) {
            $data = new User_role();
            $data->role_id = $role;
            $data->user_id = $request->user_id;
            $data->save();
        }

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $userid = Session('adminuser');
        $username = Adminuser::where('id', $userid)->pluck('username')->first();
        $usr_name = Softwareuser::where('id', $request->user_id)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " added roles to " . $usr_name;

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return redirect('listuser');
    }
    public function getRoles($id)
    {
        $roles = DB::table('user_roles')->where('user_id', $id)->pluck('role_id')->toArray();
        return response()->json([
            'roles' => $roles,
        ]);
    }
    public function getLocs($id)
    {
        $locs = DB::table('accountantlocs')->where('user_id', $id)->pluck('location_id')->toArray();
        return response()->json([
            'locs' => $locs,
        ]);
    }
    function createBranch()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        return view('/admin/createbranch', array('users' => $useritem, 'shopdatas' => $shopdata));
    }
    function listBranch()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $item = DB::table('branches')
            ->get();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited list branch page";
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return view('/admin/listbranch', array('locations' => $item, 'users' => $useritem, 'shopdatas' => $shopdata));
    }
    function branchMaindat($id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $id)
            ->pluck('location')
            ->first();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $branchid = $id;

        $buyproducts = Buyproduct::select(DB::raw("
                transaction_id,created_at,
                customer_name,
                SUM(vat_amount) as vat,
                SUM(price) as sum,
                SUM(DISTINCT COALESCE(bill_grand_total, 0)) as total_amount,
                SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
                    +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
            "))
            ->where('branch', $branchid)
            ->groupBy('transaction_id')
            ->orderBy('created_at', 'DESC')
            ->get();

        $start_date = "";
        $end_date = "";

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();
        $tax = Adminuser::where('id', $userid)->pluck('tax')->first();

        $user_type = 'webadmin';
        $message = $username . " visited " . $branchname . " branch's reports page";
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return view('/admin/branchdat', array('tax'=>$tax,'branchname' => $branchname, 'buyproducts' => $buyproducts, 'branchid' => $branchid, 'users' => $useritem, 'start_date' => $start_date, 'end_date' => $end_date, 'currency' => $currency));
        //  return view ('/admin/branchdat',array('locations'=>$item,'users'=>$useritem));
    }
    function branchwiseSummary()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');

        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

        // $products = Buyproduct::leftJoin('branches', 'branches.id', '=', 'buyproducts.branch')
        //     ->select(DB::raw("branches.location,SUM(buyproducts.price) as sum,SUM(buyproducts.vat_amount) as vat ,branches.id as locid, SUM(buyproducts.total_amount) as total_amount, SUM(buyproducts.totalamount_wo_discount) as totalamount_wo_discount,SUM(buyproducts.discount_amount) as discount_amount"))
        //     ->groupby('branches.location')
        //     ->orderBy('locid')
        //     ->get();

        $products = Branch::select(DB::raw("branches.location,branches.id as locid"))
            ->groupby('branches.location')
            ->orderBy('locid')
            ->get();

        $employee = Softwareuser::leftJoin('branches', 'branches.id', '=', 'softwareusers.location')
            ->select(DB::raw('count(*) as count,branches.location'))
            ->groupby('softwareusers.location')
            ->get();

        $shopdata = Adminuser::Where('id', $userid)
            ->get();

        $data = [
            'users' => $useritem,
            'products' => $products,
            'employees' => $employee,
            'shopdatas' => $shopdata,
            'currency' => $currency
        ];
        return view('/admin/branchwisesummary', $data);
    }
    // function createBranchform(Request $req)
    // {
    //     if (session()->missing('adminuser')) {
    //         return redirect('adminlogin');
    //     }

    //     // $req->validate([
    //     //     'location' => 'required',
    //     //     'branchname' => 'required|unique:branches,branchname',
    //     //     'mobile' => 'required',
    //     // ]);

    //     $req->validate([
    //         'location' => 'required|unique:branches,location',
    //         'branchname' => 'required|unique:branches,branchname',
    //         'mobile' => 'required',
    //     ], [
    //         'location.required' => 'Please enter a location.',
    //         'location.unique' => 'This branch location is already taken.',
    //         'branchname.required' => 'Please enter a branch name.',
    //         'branchname.unique' => 'This branch name is already taken.',
    //         'mobile.required' => 'Please enter a mobile number.',
    //     ]);

    //     //   Softwareuser::create($req->all());
    //     $branch = new Branch;
    //     $branch->location = $req->input('location');
    //     $branch->branchname = $req->input('branchname');
    //     $branch->mobile = $req->input('mobile');
    //     $branch->save();

    //     /*------------------GET IP ADDRESS---------------------------------------*/

    //     $ip = request()->ip();
    //     $uri = request()->fullUrl();

    //     $userid = Session('adminuser');
    //     $username = Adminuser::where('id', $userid)->pluck('username')->first();

    //     $user_type = 'webadmin';
    //     $message = $username . " created new branch named - " . $req->input('branchname');
    //     $locationdata = (new otherService())->get_location($ip);

    //     if ($locationdata != false) {
    //         $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
    //     }

    //     /*-----------------------------------------------------------------------*/

    //     return back()->with('success', 'Branch created successfully!');
    // }
       function createBranchform(Request $req)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        // $req->validate([
        //     'location' => 'required',
        //     'branchname' => 'required|unique:branches,branchname',
        //     'mobile' => 'required',
        // ]);

       $req->validate([
            'mobile' => 'required',
            'transaction_id' => 'required|unique:branches,transaction', // Add this line

        ], [
            'location.required' => 'Please enter a location.',
            'branchname.required' => 'Please enter a branch name.',
            'mobile.required' => 'Please enter a mobile number.',
            'transaction_id.required' => 'Transaction ID is required.',
            'transaction_id.unique' => 'This Transaction ID already exists.', // Custom error message
        ]);
        $branch = new Branch;

      if ($req->hasFile('logo')) {
    $file = $req->file('logo');

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


        // Store other inputs
        $branch->location = $req->input('location');
        $branch->branchname = $req->input('branchname');
        $branch->mobile = $req->input('mobile');
        $branch->company = $req->input('company');
        $branch->address = $req->input('address');
        $branch->email = $req->input('email');
        $branch->tr_no = $req->input('tr_no');
        $branch->file = $req->input('file');
        $branch->po_box = $req->input('po_box');
        $branch->color ='black';
        $branch->sunmilogo ='width:280px;height:90px;';
        $branch->receiptlogo ='width: 360px;height: 130px;';
        $branch->pdflogo ='width:600px;height:130px;';
        $branch->a5pdflogo ='width:450px;height:110px;';
        $branch->currency =$req->input('currency');
        $branch->transaction =$req->input('transaction_id');

        $branch->save();


        $maxBranchId = Branch::max('id'); // Query the maximum ID
        $newBranchId = $maxBranchId;

        // Insert into vat_mode table using DB facade
        DB::table('vat_mode')->insert([
            'mode' => 1,
            'exclusive' => null, // Assuming nullable in the database
            'inclusive' => 1,
            'branch' => $newBranchId, // Use the incremented branch ID
        ]);
        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $userid = Session('adminuser');
        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " created new branch named - " . $req->input('branchname');
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return back()->with('success', 'Branch created successfully!');
    }
    public function getInventorydat($id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $inventory = DB::table('softwareusers')
            ->leftJoin('products', 'softwareusers.id', '=', 'products.user_id')
            ->where('user_id', $id)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        return view('/admin/inventorydetails', array('inventorys' => $inventory, 'users' => $useritem, 'shopdatas' => $shopdata));
    }
    function purchaseReport()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        $products = DB::table('buyproducts')
            ->leftJoin('branches', 'buyproducts.branch', '=', 'branches.id')
            ->select(DB::raw("buyproducts.transaction_id,SUM(buyproducts.price) as sum,SUM(buyproducts.vat_amount) as vat,buyproducts.created_at,buyproducts.customer_name,branches.location as branch"))
            ->whereDate('buyproducts.created_at', Carbon::today())
            ->groupby('buyproducts.transaction_id')
            // ->orderBy(DB::raw("CAST(SUBSTRING(buyproducts.transaction_id FROM 3) AS UNSIGNED)"), 'desc') // The CAST() function converts a value (of any type) into a specified datatype.
            //  ->paginate(10);
            ->orderBy('buyproducts.created_at', 'DESC')
            ->get();

        $shopdata = Adminuser::Where('id', $userid)->get();
        $count = DB::table('buyproducts')
            ->whereDate('created_at', Carbon::today())
            ->distinct('transaction_id')
            ->count();
        return view('/admin/purchasereport', array('tax'=>$tax,'users' => $useritem, 'products' => $products, 'shopdatas' => $shopdata, 'count' => $count, 'currency' => $currency));
    }
    function returnReport()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        $returns = Returnproduct::select(
            DB::raw("transaction_id,SUM(price) as sum,SUM(vat_amount) as vat,created_at,branch")
        )
            ->groupby('transaction_id')
            ->orderBy('created_at', 'desc')
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        return view('/admin/returnreport', array('tax'=>$tax,'users' => $useritem, 'products' => $returns, 'shopdatas' => $shopdata, 'currency' => $currency));
    }
    public function purchaseReportdat($id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

        $buyproduct = Buyproduct::select(DB::raw("*"))
            ->where('transaction_id', $id)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        return view('/admin/purchasereportdetails', array('details' => $buyproduct, 'users' => $useritem, 'shopdatas' => $shopdata, 'currency' => $currency));
    }
    public function returnReportdat($transaction_id, $created_at)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        $returnproduct = DB::table('returnproducts')
            ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
            ->select(DB::raw('returnproducts.*'))
            ->where('returnproducts.transaction_id', $transaction_id)
            ->where('returnproducts.created_at', $created_at)
            ->get();

        $shopdata = Adminuser::Where('id', $userid)
            ->get();

        return view('/admin/returnreportdetails', array('tax'=>$tax,'details' => $returnproduct, 'users' => $useritem, 'shopdatas' => $shopdata, 'currency' => $currency));
    }

    //
    function branchPurchasedat($id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $id)
            ->pluck('location')
            ->first();
        $branchid = $id;
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();

        $buyproducts = DB::table('buyproducts')
            ->where('branch', $branchid)
            ->groupBy('transaction_id')
            ->orderBy('created_at', 'ASC')
            ->get();
        $purchases = DB::table('stockdetails')
            ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
            ->where('stockdetails.branch', $id)
            ->groupBy('stockdetails.reciept_no')
            ->orderBy('stockdetails.created_at', 'DESC')
            ->get();
        $start_date = "";
        $end_date = "";

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited " . $branchname . " branch's purchase report";
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/
        return view('/admin/branchpurchasedat', array('tax'=>$tax,'branchname' => $branchname, 'start_date' => $start_date, 'end_date' => $end_date, 'purchases' => $purchases, 'branchid' => $branchid, 'buyproducts' => $buyproducts, 'users' => $useritem, 'currency' => $currency));
    }
    function branchPurchasereturndat($id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $id)
            ->pluck('location')
            ->first();
        $branchid = $id;
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $userid)
            ->pluck('tax')
            ->first();

        $purchases = DB::table('returnpurchases')
            ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
            ->select(DB::raw('returnpurchases.id as id, returnpurchases.reciept_no as reciept_no, returnpurchases.created_at as created_at, returnpurchases.comment as comment, returnpurchases.amount as price, returnpurchases.shop_name as supplier, returnpurchases.amount_without_vat as price_without_vat, returnpurchases.vat_amount as vat_amount, products.product_name as product_name, returnpurchases.quantity as quantity'))
            ->orderBy('returnpurchases.created_at', 'DESC')
            ->where('returnpurchases.branch', $id)
            ->get();

        $start_date = "";
        $end_date = "";

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited " . $branchname . " branch's purchase return report ";
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return view('/admin/branchpurchasereturndat', array('tax'=>$tax,'branchname' => $branchname, 'start_date' => $start_date, 'end_date' => $end_date, 'purchases' => $purchases, 'branchid' => $branchid, 'users' => $useritem, 'currency' => $currency));
    }
    function branchStockdat($id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $id)
            ->pluck('location')
            ->first();
        //
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
                    ->pluck('tax')
                    ->first();
        $branchid = $id;

        // new buycost with vat and netrate (selling cost + vat amount) instead of sellingcost

        $stocks = DB::table("products")
            ->select(
                "products.*",
                DB::raw("(SELECT SUM(stockhistories.quantity) FROM stockhistories
                                WHERE stockhistories.product_id = products.id
                                GROUP BY products.id) as product_stock_total"),
                DB::raw("(SELECT SUM(stockhistories.remain_qantity) FROM stockhistories
                                WHERE stockhistories.product_id = products.id
                                GROUP BY products.id) as product_stock"),
                DB::raw("(SELECT SUM(stockdats.stock_num) FROM stockdats
                                WHERE stockdats.product_id = products.id
                              GROUP BY products.id) as product_stock_num"),
                DB::raw("(SELECT SUM(stockdats.stock_num * stockdats.netrate) FROM stockdats
                                WHERE stockdats.product_id = products.id
                              GROUP BY products.id) as product_stock_value"),
                DB::raw("(SELECT SUM(stockdats.stock_num * (stockdats.netrate - stockdats.one_pro_buycost_rate)) FROM stockdats
                                WHERE stockdats.product_id = products.id
                              GROUP BY products.id) as profit_value"),
                DB::raw("(SELECT SUM(stockhistories.quantity * stockhistories.rate) FROM stockhistories
                                WHERE stockhistories.product_id = products.id
                              GROUP BY products.id) as product_total_stock_value"),
                DB::raw("(SELECT SUM(stockhistories.remain_qantity * stockhistories.rate) FROM stockhistories
                                    WHERE stockhistories.product_id = products.id
                                  GROUP BY products.id) as product_remain_stock_amount"),
                DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                                WHERE bill_histories.product_id = products.id
                              GROUP BY products.id) as sold_buycost_value"),
                DB::raw("(SELECT SUM(buyproducts.discount_amount) FROM buyproducts
                                WHERE buyproducts.product_id = products.id
                              GROUP BY products.id) as discount_amount"),
                DB::raw("(SELECT SUM(returnproducts.discount_amount) FROM returnproducts
                                WHERE returnproducts.product_id = products.id
                              GROUP BY products.id) as return_discount_amount"),
            )
            // ->addSelect(DB::raw('IFNULL((SELECT SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) FROM buyproducts
            //             WHERE buyproducts.product_id = products.id
            //           GROUP BY products.id), 0) - IFNULL((SELECT SUM(returnproducts.discount_amount) FROM returnproducts
            //             WHERE returnproducts.product_id = products.id
            //           GROUP BY products.id), 0) AS final_discount_amount'))

            ->addSelect(DB::raw('IFNULL((SELECT SUM(COALESCE(buyproducts.discount_amount * buyproducts.remain_quantity, 0)) FROM buyproducts
                        WHERE buyproducts.product_id = products.id
                      GROUP BY products.id), 0) AS final_discount_amount'))

            ->where('products.branch', $id)
            ->where('products.status', 1)
            ->orderBy('products.id')
            ->get();


        $start_date = "";
        $end_date = "";

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited " . $branchname . " branch's stock report";
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/
        return view('/admin/branchstockdat', array('tax'=>$tax,'branchname' => $branchname, 'start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'branchid' => $branchid, 'users' => $useritem, 'location_id' => $id, 'currency' => $currency));
    }
    function stockAddHistory($location_id, $id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $location_id)
            ->pluck('location')
            ->first();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $userid)
            ->pluck('tax')
            ->first();
        // new buycost with vat - rate
        $stocks = DB::table("stock_purchase_reports")
            ->leftJoin('products', 'products.id', '=', 'stock_purchase_reports.product_id')
            ->select(
                "stock_purchase_reports.purchase_id",
                "stock_purchase_reports.receipt_no",
                "stock_purchase_reports.PBuycost",
                "stock_purchase_reports.PBuycostRate",
                "stock_purchase_reports.quantity",
                "stock_purchase_reports.remain_main_quantity",
                "stock_purchase_reports.created_at",
                "products.product_name",
                "stock_purchase_reports.product_id",
                DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity) FROM bill_histories
                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                AND bill_histories.product_id = stock_purchase_reports.product_id
                GROUP BY bill_histories.product_id) as sold_quantity_total"),

                DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                AND bill_histories.product_id = stock_purchase_reports.product_id
                GROUP BY bill_histories.product_id) as total_sold_buycost_value"),

                DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.netrate) FROM bill_histories
                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                AND bill_histories.product_id = stock_purchase_reports.product_id
                GROUP BY bill_histories.product_id) as total_sold_sellingcost_value"),

                DB::raw("(SELECT SUM(bill_histories.discount_amount) FROM bill_histories
                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                AND bill_histories.product_id = stock_purchase_reports.product_id
                GROUP BY bill_histories.product_id) as discount_amount"),

                DB::raw("(SELECT SUM(bill_histories.return_discount_amount) FROM bill_histories
                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                AND bill_histories.product_id = stock_purchase_reports.product_id
                GROUP BY bill_histories.product_id) as return_discount_amount"),
            )
            // ->addSelect(DB::raw(
            //     'IFNULL((SELECT SUM(COALESCE(bill_histories.discount_amount * bill_histories.remain_sold_quantity, 0)) FROM bill_histories
            //     WHERE bill_histories.pid = stock_purchase_reports.purchase_id
            //     AND bill_histories.product_id = stock_purchase_reports.product_id
            //     GROUP BY bill_histories.product_id), 0) - IFNULL((SELECT SUM(bill_histories.return_discount_amount) FROM bill_histories
            //     WHERE bill_histories.pid = stock_purchase_reports.purchase_id
            //     AND bill_histories.product_id = stock_purchase_reports.product_id
            //     GROUP BY bill_histories.product_id), 0) AS final_discount_amount'
            // ))

            ->addSelect(DB::raw(
                'IFNULL((SELECT SUM(bill_histories.discount_amount * bill_histories.remain_sold_quantity) FROM bill_histories
                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                AND bill_histories.product_id = stock_purchase_reports.product_id
                GROUP BY bill_histories.product_id), 0) AS final_discount_amount'
            ))

            ->where('stock_purchase_reports.product_id', $id)
            ->where('stock_purchase_reports.branch_id', $location_id)
            ->groupBy('products.id', 'stock_purchase_reports.purchase_id')
            ->get();

        $start_date = "";
        $end_date = "";
        return view('/admin/branchstockaddhistory', array('tax'=>$tax,'branchname' => $branchname, 'start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'users' => $useritem, 'branchid' => $location_id, 'product_id' => $id, 'currency' => $currency));
    }
    function stockTransactionHistory($location_id, $id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $location_id)
            ->pluck('location')
            ->first();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        $stocks = DB::table('buyproducts')
            ->select(DB::raw("product_id,transaction_id,created_at,customer_name,payment_type,total_amount,product_name,quantity, one_pro_buycost, mrp, netrate,totalamount_wo_discount,discount_amount"))
            ->where('product_id', $id)
            ->orderBy('transaction_id', 'desc')
            ->get();
        $start_date = "";
        $end_date = "";
        return view('/admin/branchstocktransactionhistory', array('tax'=>$tax,'branchname' => $branchname, 'start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'users' => $useritem, 'branchid' => $location_id, 'product_id' => $id, 'currency' => $currency));
    }

    function branchReturndat($id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $id)
            ->pluck('location')
            ->first();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $userid)
            ->pluck('tax')
            ->first();
        $branchid = $id;

        $returns = DB::table('returnproducts')
            ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
            ->select(
                DB::raw('returnproducts.id as id'),
                DB::raw('returnproducts.transaction_id as transaction_id'),
                DB::raw('returnproducts.created_at as created_at'),
                DB::raw('returnproducts.phone as phone'),
                DB::raw('SUM(returnproducts.totalamount_wo_discount) as totalamount_wo_discount'),
                DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as total_amount'),
                DB::raw('SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount'),
                DB::raw('SUM(returnproducts.vat_amount) as vat'),
                DB::raw('GROUP_CONCAT(products.product_name) as product_names'),
                DB::raw('GROUP_CONCAT(returnproducts.quantity) as quantities')
            )
            ->groupBy('returnproducts.transaction_id', 'returnproducts.created_at')
            ->orderBy('returnproducts.created_at', 'DESC')
            ->where('returnproducts.branch', $id)
            ->get();

        $start_date = "";
        $end_date = "";

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited " . $branchname . " branch's sales return report";
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/
        return view('/admin/branchreturndat', array('tax'=>$tax,'branchname' => $branchname, 'start_date' => $start_date, 'end_date' => $end_date, 'returns' => $returns, 'branchid' => $branchid, 'users' => $useritem, 'currency' => $currency));
    }

    function branchEmployeedat($id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $id)
            ->pluck('location')
            ->first();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branchid = $id;
        $employees = DB::table('softwareusers')
            ->where('location', $id)
            ->paginate(5);
        return view('/admin/branchemployeedat', array('branchname' => $branchname, 'employees' => $employees, 'branchid' => $branchid, 'users' => $useritem));
    }
    function branchdatDetails($transaction_id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $products = Buyproduct::select(DB::raw("*"))
            ->where('transaction_id', $transaction_id)
            ->paginate(5);
        $id = DB::table('buyproducts')
            ->select('branch')
            ->where('transaction_id', $transaction_id)
            ->pluck('branch')
            ->first();
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $id)
            ->pluck('location')
            ->first();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();

        $branchid = $id;
        $transaction_id=$transaction_id;
        return view('/admin/branchdatdetails', array('transaction_id'=>$transaction_id,'tax'=>$tax,'branchname' => $branchname, 'products' => $products, 'branchid' => $branchid, 'users' => $useritem, 'currency' => $currency));
    }

    public function createCredit()
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
                    $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();
        } elseif (Session::has('softwareuser')) {
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
        $shopdata = Branch::where('id', $branch)->get();
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        // Query shop data for the admin

        // Query locations
        $item = DB::table('branches')->get();


          if (Session('softwareuser')) {
                    $options = [
                        'locations' => $item,
                        'users' => $useritem,
                        'shopdatas' => $shopdata,
                        'branch'=>$branch
                    ];
                } elseif (Session('adminuser')) {
                    $options = [
                             'locations' => $item,
                                'users' => $useritem,
                                'branch'=>$branch
                    ];
                }

        return view('/admin/createcredit', $options);
    }



    function createCreditform(Request $req)
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
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        // $req->validate([
        //     'name' => 'required|min:5|unique:creditusers,name',
        //     'username' => 'required|min:5|unique:creditusers,username',
        //     'password' => 'required|min:5',
        //     'location' => 'required',
        // ]);

        // $req->validate([
        //     // 'name' => 'required|min:5|unique:creditusers,name',
        //     // 'username' => 'required|min:5|unique:creditusers,username',
        //     'name' => 'required|min:5',
        //     'username' => 'required|min:5',
        //     'password' => 'required|min:5',
        //     'location' => 'required',
        // ], [
        //     'name.required' => 'The name field is required.',
        //     'name.min' => 'The name must be at least 5 characters.',
        //     // 'name.unique' => 'The name is already in use.',
        //     'username.required' => 'The username field is required.',
        //     'username.min' => 'The username must be at least 5 characters.',
        //     // 'username.unique' => 'The username is already in use.',
        //     'password.required' => 'The password field is required.',
        //     'password.min' => 'The password must be at least 5 characters.',
        //     'location.required' => 'The location field is required.',
        // ]);

        $user = new Credituser;
        $user->name = $req->input('name');
        $user->username = $req->input('username');
        $user->location = $req->input('location');
        $user->trade_no = $req->input('trade_license_no');
        $user->l_amount = $req->input('lamount');
        $user->current_lamount = $req->input('lamount');
        $user->phone = $req->input('phone');
        $user->email = $req->input('email');
        $user->admin_id = $adminid;
        $user->admin_status = 1;
        $user->password = Hash::make($req->input('password'));

        if (Session('softwareuser')) {
            $user->user_id = $userid;
        }
        $user->business_name = $req->input('business_name');
        $user->billing_add = $req->input('billing_address');
        $user->deli_add = $req->input('delivery_address');
        $user->billing_city = $req->input('billing_city');
        $user->deli_city = $req->input('delivery_city');
        $user->billing_state = $req->input('billing_state');
        $user->deli_state = $req->input('delivery_state');
        $user->billing_postal = $req->input('billing_zip');
        $user->deli_postal = $req->input('delivery_zip');
        $user->billing_landmark = $req->input('billing_landmark');
        $user->deli_landmark = $req->input('delivery_landmark');
        $user->billing_country = $req->input('billing_country');
        $user->deli_country = $req->input('delivery_country');
        $user->b_accountname = $req->input('accountName');
        $user->b_bankname = $req->input('bank_name');
        $user->b_branch = $req->input('branch');
        $user->b_openingbalance = $req->input('openingBalance');
        $user->b_ifsc = $req->input('ifscCode');
        $user->b_iban = $req->input('ibanCode');
        $user->b_accountno = $req->input('account_number');
        $user->b_date = $req->input('date');
        $user->b_accounttype = $req->input('accountType');
        $user->b_upiid = $req->input('upiid');
        $user->b_country = $req->input('country');
        $user->trn_number = $req->input('trn_number');
        $user->delivery_default = $req->has('showDeliveryAddress') ? 1 : 0;
        $user->save();

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();


        $username = Adminuser::where('id', $adminid)->pluck('username')->first();


        $user_type = 'webadmin';
        $message = $username . " created customer named " . $req->input('username');
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($adminid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return back()->with('success', 'customer created successfully!');
    }
    function locationofAccountant(Request $request)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        if (Accountantloc::where('user_id', $request->user_id)->exists()) {
            DB::table('accountantlocs')->where('user_id', $request->user_id)->delete();
        }
        if ($request->location_id == null) {
            return redirect('listaccountant');
        }
        foreach ($request->location_id as $key => $location_id) {
            $data = new Accountantloc();
            $data->location_id = $location_id;
            $data->user_id = $request->user_id;
            $data->save();
        }
        return redirect('listaccountant');
    }
    function listCredit()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

         $item = Credituser::leftJoin('creditsummaries', 'creditusers.id', '=', 'creditsummaries.credituser_id')
         ->leftJoin('branches', 'creditusers.location', '=', 'branches.id')
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
                'branches.branchname',
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
            ->groupBy('creditusers.id', 'creditusers.name', 'creditusers.username', 'creditsummaries.due_amount', 'creditsummaries.collected_amount', 'creditsummaries.creditnote', 'cash_returns.total_returned_amount')
            ->get();

        $userid = Session('adminuser');
        $shopdata = Adminuser::Where('id', $userid)->get();

        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $location = DB::table('branches')->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited customer due summary";
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return view('/admin/listcredit', array('softusers' => $item, 'users' => $useritem, 'shopdatas' => $shopdata, 'locations' => $location, 'currency' => $currency));
    }
    function listCustomersummary($id)
    {
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        $creditdata = DB::table('fundhistories')
            ->where('credituser_id', $id)
            ->get();
        $sum = DB::table('fundhistories')
            ->select(DB::raw("SUM(amount) as sum"))
            ->where('credituser_id', $id)
            ->pluck('sum')
            ->first();
        return view('/admin/customersummary', array('users' => $useritem, 'shopdatas' => $shopdata, 'creditdatas' => $creditdata, 'sum' => $sum));
    }
    function listLocationbasedcredit($locationid)
    {
        $creditdata = DB::table('buyproducts')
            ->leftJoin('branches', 'buyproducts.branch', '=', 'branches.id')
            ->leftJoin('creditusers', 'buyproducts.credit_user_id', '=', 'creditusers.id')
            ->where('buyproducts.payment_type', 3)
            ->where('buyproducts.branch', $locationid)
            ->select(DB::raw("buyproducts.transaction_id,buyproducts.credit_user_id,branches.location,creditusers.id,creditusers.username"))
            ->groupBy('creditusers.username')
            ->get();

        return response()->json([
            'categories' => $creditdata,
        ]);
    }
    function listCustomersales()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $creditdata = DB::table('buyproducts')
            ->leftJoin('branches', 'buyproducts.branch', '=', 'branches.id')
            ->leftJoin('creditusers', 'buyproducts.credit_user_id', '=', 'creditusers.id')
            ->where('payment_type', 3)
            ->select(DB::raw("buyproducts.transaction_id,buyproducts.credit_user_id,branches.location,creditusers.id,creditusers.username"))
            ->groupBy('creditusers.username')
            ->get();

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $location = DB::table('branches')->get();
        $shopdata = Adminuser::Where('id', $userid)->get();

        return view('/admin/customersales', array('creditdatas' => $creditdata, 'users' => $useritem, 'shopdatas' => $shopdata, 'locations' => $location));
    }
    function listCustomersalesdat($customerid)
    {
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

        $shopdata = Adminuser::Where('id', $userid)->get();
        $customername = Credituser::Where('id', $customerid)
            ->pluck('name')
            ->first();

        $salesdata = DB::table('buyproducts')
            ->select(DB::raw("transaction_id,created_at,SUM(price) as price,SUM(vat_amount) as vat"))
            ->where('credit_user_id', $customerid)
            ->where('payment_type', 3)
            ->groupBy('transaction_id')
            ->get();

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited " . $customername . "'s credit sales page ";
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return view('/admin/customersalesdat', array('users' => $useritem, 'shopdatas' => $shopdata, 'salesdatas' => $salesdata, 'currency' => $currency));
    }
    function listCustomersalesdetails($transaction_id)
    {
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

        $shopdata = Adminuser::Where('id', $userid)->get();
        $salesdata = DB::table('buyproducts')
            ->select(DB::raw("product_name,created_at, price"))
            ->where('transaction_id', $transaction_id)
            ->get();
        return view('/admin/salesdetails', array('users' => $useritem, 'shopdatas' => $shopdata, 'salesdatas' => $salesdata, 'currency' => $currency));
    }
    public function disableCredit($id)
    {
        $userid = Session('adminuser');
        $username = Adminuser::where('id', $userid)->pluck('username')->first();
        $usr = Credituser::where('id', $id)->pluck('name')->first();

        $plan = Credituser::find($id);
        if ($plan->status == '1') {
            $status = '0';
        } else {
            $status = '1';
        }
        $plan->status = $status;
        $plan->save();

        $message = $username . " disabled customer named " . $usr;
        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/
        return redirect('/listcredit');
    }
    public function editUsers($id)
    {
        if (Session::has('softwareuser')) {
        } elseif (Session::has('adminuser')) {
        } else {
            return redirect('userlogin'); // or 'adminlogin' based on your logic
        }
        $adminid = null;
        if (Session::has('adminuser')) {
            $adminid = Session::get('adminuser');
            $useritem = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();
                $username = Softwareuser::Where('id', $id)
                ->pluck('username')
                ->first();
            $name = Softwareuser::Where('id', $id)
                ->pluck('name')
                ->first();
            $joined_date = Softwareuser::Where('id', $id)
                ->pluck('joined_date')
                ->first();
                $item = DB::table('branches')->get();
                $location = DB::table('softwareusers')
                ->leftJoin('branches', 'softwareusers.location', '=', 'branches.id')
                ->Where('softwareusers.id', $id)
                ->pluck('branches.location')
                ->first();
            $uid = $id;
                $shopdata = Adminuser::Where('id', $adminid)->get();

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
                $username = Softwareuser::Where('id', $id)
                ->pluck('username')
                ->first();
            $name = Softwareuser::Where('id', $id)
                ->pluck('name')
                ->first();
            $joined_date = Softwareuser::Where('id', $id)
                ->pluck('joined_date')
                ->first();
                $location = DB::table('softwareusers')
                ->leftJoin('branches', 'softwareusers.location', '=', 'branches.id')
                ->Where('softwareusers.id', $id)
                ->pluck('branches.location')
                ->first();
            $uid = $id;
                $branchId = Softwareuser::where('id', $userid)->pluck('location')->first(); // Assuming branch_id is the column in softwareusers table
                $item = DB::table('branches')->get();

        $shopdata = Branch::where('id', $branchId)->get();
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }





        return view('/admin/edituser', array('locations' => $item, 'users' => $useritem, 'shopdatas' => $shopdata, 'username' => $username, 'name' => $name, 'joined_date' => $joined_date, 'location' => $location, 'uid' => $uid));
    }
    public function changeAccess($id)
    {
        $userid = Session('adminuser');

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $usr = Softwareuser::where('id', $id)->pluck('username')->first();

        $plan = Softwareuser::find($id);
        if ($plan->access == '1') {
            $access = '0';
            $plan->access = $access;
            $plan->save();

            $message = $username . " disabled user named" . $usr;
        } elseif ($plan->access == '0') {
            $access = '1';
            $plan->access = $access;
            $plan->save();

            $message = $username . " enabled user named" . $usr;
        }

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $user_type = 'webadmin';
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/
        return redirect('/listuser');
    }
    public function adminchangePassword()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $shopdata = Adminuser::Where('id', $userid)->get();
        $username = Adminuser::Where('id', Session('adminuser'))
            ->pluck('username')
            ->first();
        return view('/admin/adminchangepassword', array('users' => $useritem, 'shopdatas' => $shopdata, 'username' => $username));
    }
    public function submitadminPassword(Request $req)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $req->validate([
            'username' => 'required',
            'old_password' => 'required',
            'password' => 'required',
            'confirmpassword' => 'required',
        ]);
        $old_pass = $req->old_password;
        $username = Adminuser::Where('id', Session('adminuser'))
            ->pluck('username')
            ->first();
        if (Auth::guard('webadmin')->attempt(['username' => $username, 'password' => $old_pass])) {
            if ($req->password == $req->confirmpassword) {
                $user = Adminuser::find(session('adminuser'));
                $user->username = $req->username;
                $user->password = Hash::make($req->password);
                $user->save();
                return back()->with('success', 'Password changed successfully!');
            } else {
                return back()->with('failed', 'Password does not match');
            }
        } else {
            return back()->with('failed', 'Old Password does not match');
        }
    }
    function userEdit(Request $req)
    {
        if (Session::has('softwareuser')) {
        } elseif (Session::has('adminuser')) {
        } else {
            return redirect('userlogin'); // or 'adminlogin' based on your logic
        }
        // $req->validate([
        //     'name' => 'required|min:5',
        //     'username' => 'required|min:5|unique:softwareusers,username',
        //     'password' => 'required',
        //     'confirmpassword' => 'required',
        //     // 'location'=>'required',
        // ]);
        if (Session('adminuser')){
            $adminid = Session('adminuser');

        }
    elseif(Session('softwareuser')){

        $userid = Session('softwareuser');
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
    }

        $req->validate([
            'name' => 'required|min:5',
            'username' => [
                'required',
                'min:5',
                Rule::unique('softwareusers', 'username')->ignore($req->id),
            ],
            'password' => 'required',
            'confirmpassword' => 'required',
        ]);

        if ($req->password == $req->confirmpassword) {
            $user = Softwareuser::find($req->id);
            $user->name = $req->name;
            $user->username = $req->username;
            //  $user->location=$req->location;
            $user->admin_id = $adminid;
            $user->password = Hash::make($req->input('password'));
            $user->joined_date = $req->input('joined_date');
            $user->save();

            /*------------------GET IP ADDRESS---------------------------------------*/

            $ip = request()->ip();
            $uri = request()->fullUrl();

            // $userid = Session('adminuser');

            $username = Adminuser::where('id', $adminid)->pluck('username')->first();

            $user_type = 'webadmin';
            $message = $username . " updated " . $req->username . " details";

            $locationdata = (new otherService())->get_location($ip);

            if ($locationdata != false) {
                $activityservice = (new activityService($adminid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
            }

            /*-----------------------------------------------------------------------*/

            return back()->with('success', 'User Data Edited Successfully!');
        }
    }
    public function accountantReports()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)->get();
        $reports = DB::table('finalreports')
            ->leftJoin('softwareusers', 'finalreports.user_id', '=', 'softwareusers.id')
            ->select(DB::raw("softwareusers.name,finalreports.file,finalreports.date,finalreports.created_at"))
            ->get();

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited accountant final report page";

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/
        return view('/admin/accountantreports', array('users' => $useritem, 'shopdatas' => $shopdata, 'reports' => $reports));
    }
    public function hrusercreationRequests()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        $userrequests = Hrusercreation::LeftJoin('branches', 'hrusercreations.location', '=', 'branches.id')
            ->select(DB::raw("hrusercreations.name,hrusercreations.username,hrusercreations.joining_date,branches.location,hrusercreations.status,hrusercreations.id"))
            ->orderBy('status', 'asc')
            ->paginate(5);
        $count = DB::table('hrusercreations')
            ->where('status', 0)
            ->count();
        return view('/admin/hrusercreationrequests', array('users' => $useritem, 'count' => $count, 'userrequests' => $userrequests, 'shopdatas' => $shopdata));
    }
    public function hrusercreation($id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $shopdata = Adminuser::Where('id', $userid)->get();
        // userdata
        $user_name = Hrusercreation::Where('id', $id)
            ->pluck('username')
            ->first();
        $full_name = Hrusercreation::Where('id', $id)
            ->pluck('name')
            ->first();
        $joining_date = Hrusercreation::Where('id', $id)
            ->pluck('joining_date')
            ->first();
        $branch = Hrusercreation::Where('id', $id)
            ->pluck('location')
            ->first();
        $branch_name = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();
        $email = Hrusercreation::Where('id', $id)
            ->pluck('email')
            ->first();
        // userdata end
        // privileges
        $privileges = DB::table('hrusercreations')
            ->leftJoin('hruserroles', 'hrusercreations.id', '=', 'hruserroles.user_id')
            ->where('hrusercreations.id', $id)
            ->get();
        $status = DB::table('hrusercreations')
            ->where('username', $user_name)
            ->update(['status' => 1]);
        $userroles = Hruserroles::Where('id', $id)
            ->get();
        $count = DB::table('hrusercreations')
            ->where('status', 0)
            ->count();
        $location = DB::table('branches')
            ->where('id', '!=', $branch)
            ->get();
        $data = [
            'users' => $useritem,
            'privileges' => $privileges,
            'count' => $count,
            'locations' => $location,
            'shopdatas' => $shopdata,
            'user_name' => $user_name,
            'full_name' => $full_name,
            'joining_date' => $joining_date,
            'branch' => $branch,
            'branch_name' => $branch_name,
            'email' => $email
        ];
        return view('/admin/hrusercreation', $data);
    }
    function hrcreateUserform(Request $req)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $req->validate([
            'name' => 'required|min:5',
            'username' => 'required|min:5|unique:softwareusers,username',
            'password' => 'required|min:5',
            'location' => 'required',
        ]);
        //   Softwareuser::create($req->all());
        $user = new Softwareuser;
        $user->name = $req->input('name');
        $user->username = $req->input('username');
        $user->location = $req->input('location');
        $user->admin_id = Session('adminuser');
        $user->password = Hash::make($req->input('password'));
        $user->joined_date = $req->input('joined_date');
        $user->email = $req->input('email');
        $user->save();
        $createduserid = $user->id;
        $user_name = $req->input('username');
        $status = DB::table('hrusercreations')
            ->where('username', $user_name)
            ->update(['status' => 2]);
        if (User_role::where('user_id', $createduserid)->exists()) {
            DB::table('user_roles')->where('user_id', $req->user_id)->delete();
        }
        if ($req->role == null) {
            return redirect('/usercreationrequests');
        }
        foreach ($req->role as $key => $role) {
            $data = new User_role();
            $data->role_id = $role;
            $data->user_id = $createduserid;
            $data->save();
        }
        return redirect('/usercreationrequests');
    }

    function customercreditdata($id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $shopdata = Adminuser::Where('id', $userid)->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

        $location = DB::table('branches')->get();

        $salesdata = DB::table('credit_transactions')
            ->select(DB::raw("*"))
            ->where('credituser_id', $id)
            ->paginate(10);

        $lastTransaction_for_due = DB::table('credit_transactions')
            ->where('credituser_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();

        $final_due = $lastTransaction_for_due->updated_balance ?? 0;

        return view('/admin/customercreditdata', array('users' => $useritem, 'salesdata' => $salesdata, 'finaldue' => $final_due, 'shopdatas' => $shopdata, 'locations' => $location, 'currency' => $currency, 'credit_id' => $id));
    }
    function listCredituser()
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
                $item = Credituser::leftJoin('branches', 'creditusers.location', '=', 'branches.id')
                ->select(DB::raw("creditusers.name, branches.location, DATE(creditusers.created_at) as created_date, creditusers.username, creditusers.l_amount, creditusers.current_lamount, creditusers.id"))
                ->where('creditusers.status', 1)
                ->orderBy('creditusers.created_at', 'DESC')
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
                $branchId = Softwareuser::where('id', $userid)->pluck('location')->first(); // Assuming branch_id is the column in softwareusers table
                // Fetch items for the specific branch for software user
               $item = Credituser::leftJoin('branches', 'creditusers.location', '=', 'branches.id')
               ->select(DB::raw("creditusers.name, branches.location, DATE(creditusers.created_at) as created_date, creditusers.username, creditusers.l_amount, creditusers.current_lamount, creditusers.id,creditusers.phone"))
               ->where('creditusers.location', $branchId) // Filter by branch
               ->where('creditusers.status', 1)
               ->orderBy('creditusers.created_at', 'DESC')
               ->get();
        $shopdata = Branch::where('id', $branchId)->get();
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }





        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $adminid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited list customer page";

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($adminid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/
          if (Session('softwareuser')) {
                    $options = [
                        'softusers' => $item,
                        'users' => $useritem,
                        'shopdatas' => $shopdata,
                    ];
                } elseif (Session('adminuser')) {
                    $options = [
                             'softusers' => $item,
                                'users' => $useritem,
                    ];
                }

        return view('/admin/listcredituser', $options);
    }
    function editCredituser($id)
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
        } elseif (Session::has('softwareuser')) {
            $userid = Session::get('softwareuser');
            $useritem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();
                $branchId = Softwareuser::where('id', $userid)->pluck('location')->first(); // Assuming branch_id is the column in softwareusers table

            // Get the admin_id from the softwareuser
            $adminid = Softwareuser::where('id', $userid)
                ->pluck('admin_id')
                ->first();
        $shopdata = Branch::where('id', $branchId)->get();
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        $item = DB::table('branches')
            ->get();



        $username = Credituser::Where('id', $id)
            ->pluck('username')
            ->first();

        $name = Credituser::Where('id', $id)
            ->pluck('name')
            ->first();

        $email = Credituser::Where('id', $id)
            ->pluck('email')
            ->first();

        $phone = Credituser::Where('id', $id)
            ->pluck('phone')
            ->first();

        $trn_number = Credituser::Where('id', $id)
            ->pluck('trn_number')
            ->first();
            $business_name = Credituser::Where('id', $id)
            ->pluck('business_name')
            ->first();

        $trade_license_no = Credituser::Where('id', $id)
            ->pluck('trade_no')
            ->first();

        $billing_address = Credituser::Where('id', $id)
            ->pluck('billing_add')
            ->first();
        $delivery_address = Credituser::Where('id', $id)
            ->pluck('deli_add')
            ->first();
        $billing_city = Credituser::Where('id', $id)
            ->pluck('billing_city')
            ->first();
        $delivery_city = Credituser::Where('id', $id)
            ->pluck('deli_city')
            ->first();
        $billing_state = Credituser::Where('id', $id)
            ->pluck('billing_state')
            ->first();
        $delivery_state = Credituser::Where('id', $id)
            ->pluck('deli_state')
            ->first();
        $billing_zip = Credituser::Where('id', $id)
            ->pluck('billing_postal')
            ->first();
        $delivery_zip = Credituser::Where('id', $id)
            ->pluck('deli_postal')
            ->first();
        $billing_landmark = Credituser::Where('id', $id)
            ->pluck('billing_landmark')
            ->first();
        $delivery_landmark = Credituser::Where('id', $id)
            ->pluck('deli_landmark')
            ->first();
        $billing_country = Credituser::Where('id', $id)
            ->pluck('billing_country')
            ->first();
        $delivery_country = Credituser::Where('id', $id)
            ->pluck('deli_country')
            ->first();
            $accountName = Credituser::Where('id', $id)
            ->pluck('b_accountname')
            ->first();
            $bank_name = Credituser::Where('id', $id)
            ->pluck('b_bankname')
            ->first();
            $branch = Credituser::Where('id', $id)
            ->pluck('b_branch')
            ->first();
            $openingBalance = Credituser::Where('id', $id)
            ->pluck('b_openingbalance')
            ->first();
            $ifscCode = Credituser::Where('id', $id)
            ->pluck('b_ifsc')
            ->first();
            $ibanCode = Credituser::Where('id', $id)
            ->pluck('b_iban')
            ->first();
            $account_number = Credituser::Where('id', $id)
            ->pluck('b_accountno')
            ->first();
            $date = Credituser::Where('id', $id)
            ->pluck('b_date')
            ->first();
            $accountType = Credituser::Where('id', $id)
            ->pluck('b_accounttype')
            ->first();
            $upiid = Credituser::Where('id', $id)
            ->pluck('b_upiid')
            ->first();
            $country = Credituser::Where('id', $id)
            ->pluck('b_country')
            ->first();
            $delivery_default = Credituser::where('id', $id)
            ->pluck('delivery_default')
            ->first();
            $current_lamount = Credituser::Where('id', $id)
            ->pluck('current_lamount')
            ->first();

        $uid = $id;


                  if (Session('softwareuser')) {
                    $options = [
                        'country'=>$country,
            'upiid'=>$upiid,
            'accountType'=>$accountType,
            'date'=>$date,
            'account_number'=>$account_number,
            'ibanCode'=>$ibanCode,
            'ifscCode'=>$ifscCode,
            'openingBalance'=>$openingBalance,
            'branch'=>$branch,
            'bank_name'=>$bank_name,
            'accountName'=>$accountName,
            'delivery_country'=>$delivery_country,
            'billing_country'=>$billing_country,
            'delivery_landmark'=>$delivery_landmark,
            'billing_landmark'=>$billing_landmark,
            'delivery_zip'=>$delivery_zip,
            'billing_zip'=>$billing_zip,
            'delivery_state'=>$delivery_state,
            'billing_state'=>$billing_state,
            'delivery_city'=>$delivery_city,
            'delivery_address'=>$delivery_address,
            'billing_city'=>$billing_city,
            'billing_address'=>$billing_address,
            'trade_license_no'=>$trade_license_no,
            'business_name'=>$business_name,
            'locations' => $item,
            'trn_number' => $trn_number,
            'email' => $email,
            'phone' => $phone,
            'users' => $useritem,
            'shopdatas' => $shopdata,
            'username' => $username,
            'name' => $name,
            'uid' => $uid,
            'current_lamount'=>$current_lamount,
            'delivery_default'=>$delivery_default
                    ];
                } elseif (Session('adminuser')) {
                    $options = [
                              'country'=>$country,
            'upiid'=>$upiid,
            'accountType'=>$accountType,
            'date'=>$date,
            'account_number'=>$account_number,
            'ibanCode'=>$ibanCode,
            'ifscCode'=>$ifscCode,
            'openingBalance'=>$openingBalance,
            'branch'=>$branch,
            'bank_name'=>$bank_name,
            'accountName'=>$accountName,
            'delivery_country'=>$delivery_country,
            'billing_country'=>$billing_country,
            'delivery_landmark'=>$delivery_landmark,
            'billing_landmark'=>$billing_landmark,
            'delivery_zip'=>$delivery_zip,
            'billing_zip'=>$billing_zip,
            'delivery_state'=>$delivery_state,
            'billing_state'=>$billing_state,
            'delivery_city'=>$delivery_city,
            'delivery_address'=>$delivery_address,
            'billing_city'=>$billing_city,
            'billing_address'=>$billing_address,
            'trade_license_no'=>$trade_license_no,
            'business_name'=>$business_name,
            'locations' => $item,
            'trn_number' => $trn_number,
            'email' => $email,
            'phone' => $phone,
            'users' => $useritem,
            'username' => $username,
            'name' => $name,
            'uid' => $uid,
            'current_lamount'=>$current_lamount,
            'delivery_default'=>$delivery_default
                    ];
                }

        return view('/admin/editcredituser', $options);
    }
    function credituserEdit(Request $req)
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
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }




        // /------------------GET IP ADDRESS---------------------------------------/


        $username = Adminuser::where('id', $adminid)->pluck('username')->first();

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $user_type = 'webadmin';
        $message = $username . " updated " . $req->username . " customer details";

        $locationdata = (new otherService())->get_location($ip);

        // /-----------------------------------------------------------------------/


            if ($locationdata != false) {
                $activityservice = (new activityService($adminid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
            }



            $user = Credituser::find($req->id);

            $existingUsername = $user->username;
            $existingPassword = $user->password;

            $user = Credituser::find($req->id);
            $user->name = $req->name;
            $user->username = $req->username;
            $user->trn_number = $req->trn_number;
            $user->phone = $req->phone;
            $user->b_country = $req->country;
            $user->b_upiid = $req->upiid;
            $user->b_accounttype = $req->accountType;
            $user->b_date = $req->date;
            $user->b_accountno = $req->account_number;
            $user->b_iban = $req->ibanCode;
            $user->b_ifsc = $req->ifscCode;
            $user->b_openingbalance = $req->openingBalance;
            $user->b_bankname = $req->bank_name;
            $user->b_accountname = $req->accountName;
            $user->deli_country = $req->delivery_country;
            $user->billing_country = $req->billing_country;
            $user->deli_landmark = $req->delivery_landmark;
            $user->deli_postal = $req->delivery_zip;
            $user->billing_postal = $req->billing_zip;
            $user->deli_state = $req->delivery_state;
            $user->billing_state = $req->billing_state;
            $user->deli_city = $req->delivery_city;
            $user->deli_add = $req->delivery_address;
            $user->billing_city = $req->billing_city;
            $user->billing_add = $req->billing_address;
            $user->trade_no = $req->trade_license_no;
            $user->business_name = $req->business_name;
            $user->trn_number = $req->trn_number;
            $user->username = $existingUsername;
            $user->password = $existingPassword;
            $user->delivery_default = $req->has('delivery_default') ? 1 : 0;  // Update based on checkbox or input
            $addAmount = $req->add_amount ?? null;

            if (!is_null($addAmount)) {
                $user->l_amount = ($user->l_amount ?? 0) + $addAmount; // Safeguard in case l_amount is null
                $user->current_lamount = ($user->current_lamount ?? 0) + $addAmount; // Safeguard in case current_lamount is null
            }
            $user->save();

            if ($locationdata != false) {
                $activityservice = (new activityService($adminid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
            }

            return redirect('/listcredituser')->with('success', 'Customer Data Edited Successfully!');

    }
    function dateSales(Request $request)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $request->branch)
            ->pluck('location')
            ->first();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $userid)
            ->pluck('tax')
            ->first();

        if ($request->start_date != $request->end_date) {
            $buyproducts = DB::table('buyproducts')
                ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
                ->select(DB::raw("
                    transaction_id,created_at,
                    customer_name,
                    SUM(vat_amount) as vat,
                    SUM(price) as sum,
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as total_amount,
                    SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
                        +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
                "))
                ->where('buyproducts.branch', $request->branch)
                ->whereBetween('buyproducts.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->groupBy('buyproducts.transaction_id')
                ->orderBy('buyproducts.created_at', 'ASC')
                ->get();
        } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {
            $buyproducts = DB::table('buyproducts')
                ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
                ->select(DB::raw("
                    transaction_id,created_at,
                    customer_name,
                    SUM(vat_amount) as vat,
                    SUM(price) as sum,
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as total_amount,
                    SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
                        +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
                "))
                ->where('buyproducts.branch', $request->branch)
                ->groupBy('buyproducts.transaction_id')
                ->orderBy('buyproducts.created_at', 'ASC')
                ->whereDate('buyproducts.created_at', $request->start_date)
                ->get();
        } else {
            $buyproducts = DB::table('buyproducts')
                ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
                ->select(DB::raw("
                    transaction_id,created_at,
                    customer_name,
                    SUM(vat_amount) as vat,
                    SUM(price) as sum,
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as total_amount,
                    SUM(buyproducts.totalamount_wo_discount) as grandtotal_without_discount,
                    SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
                        +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
                "))
                ->where('buyproducts.branch', $request->branch)
                ->groupBy('buyproducts.transaction_id')
                ->orderBy('buyproducts.created_at', 'ASC')
                ->get();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/branchdat', array('tax'=>$tax,'branchname' => $branchname, 'buyproducts' => $buyproducts, 'branchid' => $request->branch, 'users' => $useritem, 'start_date' => $start_date, 'end_date' => $end_date, 'currency' => $currency));
    }
    function datePurchases(Request $request)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $request->branch)
            ->pluck('location')
            ->first();
        $branchid = $request->branch;
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();

        $branchid = $request->branch;
        if ($request->start_date != $request->end_date) {
            $purchases = DB::table('stockdetails')
                ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->where('stockdetails.branch', $request->branch)
                ->whereBetween('stockdetails.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'ASC')
                ->get();
        } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {
            $purchases = DB::table('stockdetails')
                ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->where('stockdetails.branch', $request->branch)
                ->whereDate('stockdetails.created_at', $request->start_date)
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'ASC')
                ->get();
        } else {
            $purchases = DB::table('stockdetails')
                ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->where('stockdetails.branch', $request->branch)
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'ASC')
                ->get();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/branchpurchasedat', array('tax'=>$tax,'branchname' => $branchname, 'purchases' => $purchases, 'branchid' => $branchid, 'users' => $useritem, 'start_date' => $start_date, 'end_date' => $end_date, 'currency' => $currency));
    }
    function datePurchasesreturn(Request $request)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $request->branch)
            ->pluck('location')
            ->first();
        $branchid = $request->branch;
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $userid)
            ->pluck('tax')
            ->first();

        $branchid = $request->branch;
        if ($request->start_date != $request->end_date) {
            $purchases = DB::table('returnpurchases')
                ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
                ->select(DB::raw('returnpurchases.id as id, returnpurchases.reciept_no as reciept_no, returnpurchases.created_at as created_at, returnpurchases.comment as comment, returnpurchases.amount as price, returnpurchases.shop_name as supplier, returnpurchases.amount_without_vat as price_without_vat, returnpurchases.vat_amount as vat_amount, products.product_name as product_name, returnpurchases.quantity as quantity'))
                ->where('returnpurchases.branch', $request->branch)
                ->whereBetween('returnpurchases.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->orderBy('returnpurchases.created_at', 'ASC')
                ->get();
        } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {
            $purchases = DB::table('returnpurchases')
                ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
                ->select(DB::raw('returnpurchases.id as id, returnpurchases.reciept_no as reciept_no, returnpurchases.created_at as created_at, returnpurchases.comment as comment, returnpurchases.amount as price, returnpurchases.shop_name as supplier, returnpurchases.amount_without_vat as price_without_vat, returnpurchases.vat_amount as vat_amount, products.product_name as product_name, returnpurchases.quantity as quantity'))
                ->where('returnpurchases.branch', $request->branch)
                ->whereDate('returnpurchases.created_at', $request->start_date)
                ->orderBy('returnpurchases.created_at', 'ASC')
                ->get();
        } else {
            $purchases = DB::table('returnpurchases')
                ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
                ->select(DB::raw('returnpurchases.id as id, returnpurchases.reciept_no as reciept_no, returnpurchases.created_at as created_at, returnpurchases.comment as comment, returnpurchases.amount as price, returnpurchases.shop_name as supplier, returnpurchases.amount_without_vat as price_without_vat, returnpurchases.vat_amount as vat_amount, products.product_name as product_name, returnpurchases.quantity as quantity'))
                ->where('returnpurchases.branch', $request->branch)
                ->orderBy('returnpurchases.created_at', 'ASC')
                ->get();
        }

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/branchpurchasereturndat', array('tax'=>$tax,'branchname' => $branchname, 'purchases' => $purchases, 'branchid' => $branchid, 'users' => $useritem, 'start_date' => $start_date, 'end_date' => $end_date, 'currency' => $currency));
    }
    function dateAdminreturndate(Request $request)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $request->branch)
            ->pluck('location')
            ->first();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $userid)
            ->pluck('tax')
            ->first();

        $branchid = $request->branch;

        if ($request->start_date != $request->end_date) {

            $returns = DB::table('returnproducts')
                ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
                ->select(
                    DB::raw('returnproducts.id as id'),
                    DB::raw('returnproducts.transaction_id as transaction_id'),
                    DB::raw('returnproducts.created_at as created_at'),
                    DB::raw('returnproducts.phone as phone'),
                    DB::raw('SUM(returnproducts.totalamount_wo_discount) as totalamount_wo_discount'),
                    DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as total_amount'),
                    DB::raw('SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount'),
                    DB::raw('SUM(returnproducts.vat_amount) as vat'),
                    DB::raw('GROUP_CONCAT(products.product_name) as product_names'),
                    DB::raw('GROUP_CONCAT(returnproducts.quantity) as quantities')
                )
                ->groupBy('returnproducts.transaction_id', 'returnproducts.created_at')
                ->orderBy('returnproducts.created_at', 'DESC')
                ->where('returnproducts.branch', $request->branch)
                ->whereBetween('returnproducts.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->get();
        } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {

            $returns = DB::table('returnproducts')
                ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
                ->select(
                    DB::raw('returnproducts.id as id'),
                    DB::raw('returnproducts.transaction_id as transaction_id'),
                    DB::raw('returnproducts.created_at as created_at'),
                    DB::raw('returnproducts.phone as phone'),
                    DB::raw('SUM(returnproducts.totalamount_wo_discount) as totalamount_wo_discount'),
                    DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as total_amount'),
                    DB::raw('SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount'),
                    DB::raw('SUM(returnproducts.vat_amount) as vat'),
                    DB::raw('GROUP_CONCAT(products.product_name) as product_names'),
                    DB::raw('GROUP_CONCAT(returnproducts.quantity) as quantities')
                )
                ->groupBy('returnproducts.transaction_id', 'returnproducts.created_at')
                ->orderBy('returnproducts.created_at', 'DESC')
                ->where('returnproducts.branch', $request->branch)
                ->whereDate('returnproducts.created_at', $request->start_date)
                ->get();
        } else {
            $returns = DB::table('returnproducts')
                ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
                ->select(
                    DB::raw('returnproducts.id as id'),
                    DB::raw('returnproducts.transaction_id as transaction_id'),
                    DB::raw('returnproducts.created_at as created_at'),
                    DB::raw('returnproducts.phone as phone'),
                    DB::raw('SUM(returnproducts.totalamount_wo_discount) as totalamount_wo_discount'),
                    DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as total_amount'),
                    DB::raw('SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount'),
                    DB::raw('SUM(returnproducts.vat_amount) as vat'),
                    DB::raw('GROUP_CONCAT(products.product_name) as product_names'),
                    DB::raw('GROUP_CONCAT(returnproducts.quantity) as quantities')
                )
                ->groupBy('returnproducts.transaction_id', 'returnproducts.created_at')
                ->orderBy('returnproducts.created_at', 'DESC')
                ->where('returnproducts.branch', $request->branch)
                ->get();
        }

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/branchreturndat', array('tax'=>$tax,'branchname' => $branchname, 'returns' => $returns, 'branchid' => $branchid, 'users' => $useritem, 'start_date' => $start_date, 'end_date' => $end_date, 'currency' => $currency));
    }
    function dateAdminstockdate(Request $request)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $request->branch)
            ->pluck('location')
            ->first();
        //
          $tax = Adminuser::Where('id', $userid)
            ->pluck('tax')
            ->first();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $branchid = $request->branch;
        if ($request->start_date != $request->end_date) {
            $stocks = DB::table('products')
                ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
                ->select(DB::raw("products.product_name as product_name,products.id as product_id,products.selling_cost as selling_cost,products.buy_cost as buy_cost,products.stock as stock, SUM(stockdats.stock_num) as stock_num"),)
                ->groupBy('products.id')
                ->where('products.branch', $request->branch)
                ->whereBetween('products.created_at', [$request->start_date, $request->end_date])
                ->orderBy('products.id')
                ->get();
        } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {
            $stocks = DB::table('products')
                ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
                ->select(DB::raw("products.product_name as product_name,products.id as product_id,products.selling_cost as selling_cost,products.buy_cost as buy_cost,products.stock as stock, SUM(stockdats.stock_num) as stock_num"),)
                ->groupBy('products.id')
                ->where('products.branch', $request->branch)
                ->whereDate('products.created_at', $request->start_date)
                ->orderBy('products.id')
                ->get();
        } else {
            $stocks = DB::table('products')
                ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
                ->select(DB::raw("products.product_name as product_name,products.id as product_id,products.selling_cost as selling_cost,products.buy_cost as buy_cost,products.stock as stock, SUM(stockdats.stock_num) as stock_num"),)
                ->groupBy('products.id')
                ->where('products.branch', $request->branch)
                ->orderBy('products.id')
                ->get();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/branchstockdat', array('tax'=>$tax,'branchname' => $branchname, 'start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'branchid' => $branchid, 'users' => $useritem, 'location_id' => $request->branch));
    }
    function dateStocktransactiondate(Request $request)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $request->branch)
            ->pluck('location')
            ->first();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        if ($request->start_date != $request->end_date) {
            $stocks = DB::table('buyproducts')
                ->select(DB::raw("product_id,transaction_id,customer_name,payment_type,total_amount,product_name,quantity,created_at,one_pro_buycost,mrp,netrate,totalamount_wo_discount,discount_amount"),)
                ->where('product_id', $request->product_id)
                ->orderBy('transaction_id')
                ->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->get();
        } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {
            $stocks = DB::table('buyproducts')
                ->select(DB::raw("product_id,transaction_id,created_at,customer_name,payment_type,total_amount,product_name,quantity,one_pro_buycost,mrp,netrate,totalamount_wo_discount,discount_amount"),)
                ->where('product_id', $request->product_id)
                ->orderBy('transaction_id')
                ->whereDate('created_at', $request->start_date)
                ->get();
        } else {
            $stocks = DB::table('buyproducts')
                ->select(DB::raw("product_id,transaction_id,customer_name,payment_type,total_amount,product_name,quantity,created_at,one_pro_buycost,mrp,netrate,totalamount_wo_discount,discount_amount"),)
                ->where('product_id', $request->product_id)
                ->orderBy('transaction_id')
                ->get();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/branchstocktransactionhistory', array('tax'=>$tax,'branchname' => $branchname, 'start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'users' => $useritem, 'branchid' => $request->branch, 'product_id' => $request->product_id, 'currency' => $currency));
    }
    function dateStockadddate(Request $request)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $request->branch)
            ->pluck('location')
            ->first();
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $userid)
            ->pluck('tax')
            ->first();
        if ($request->start_date != $request->end_date) {

            $stocks = DB::table("stock_purchase_reports")
                ->leftJoin('products', 'products.id', '=', 'stock_purchase_reports.product_id')
                ->select(
                    "stock_purchase_reports.purchase_id",
                    "stock_purchase_reports.receipt_no",
                    "stock_purchase_reports.PBuycost",
                    "stock_purchase_reports.PBuycostRate",
                    "stock_purchase_reports.quantity",
                    "stock_purchase_reports.remain_main_quantity",
                    "stock_purchase_reports.created_at",
                    "products.product_name",
                    "stock_purchase_reports.product_id",
                    DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as sold_quantity_total"),
                    DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as total_sold_buycost_value"),

                    DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.netrate) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as total_sold_sellingcost_value"),

                    DB::raw("(SELECT SUM(bill_histories.discount_amount) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as discount_amount"),

                    DB::raw("(SELECT SUM(bill_histories.return_discount_amount) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as return_discount_amount"),
                )
                ->addSelect(DB::raw(
                    'IFNULL((SELECT SUM(bill_histories.discount_amount * bill_histories.remain_sold_quantity) FROM bill_histories
                    WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                    AND bill_histories.product_id = stock_purchase_reports.product_id
                    GROUP BY bill_histories.product_id), 0) AS final_discount_amount'
                ))

                ->where('stock_purchase_reports.product_id', $request->product_id)
                ->where('stock_purchase_reports.branch_id', $request->branch)
                ->whereBetween('stock_purchase_reports.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->groupBy('products.id', 'stock_purchase_reports.purchase_id')
                ->get();
        } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {

            $stocks = DB::table("stock_purchase_reports")
                ->leftJoin('products', 'products.id', '=', 'stock_purchase_reports.product_id')
                ->select(
                    "stock_purchase_reports.purchase_id",
                    "stock_purchase_reports.receipt_no",
                    "stock_purchase_reports.PBuycost",
                    "stock_purchase_reports.PBuycostRate",
                    "stock_purchase_reports.quantity",
                    "stock_purchase_reports.remain_main_quantity",
                    "stock_purchase_reports.created_at",
                    "products.product_name",
                    "stock_purchase_reports.product_id",
                    DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as sold_quantity_total"),
                    DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as total_sold_buycost_value"),

                    DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.netrate) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as total_sold_sellingcost_value"),

                    DB::raw("(SELECT SUM(bill_histories.discount_amount) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as discount_amount"),

                    DB::raw("(SELECT SUM(bill_histories.return_discount_amount) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as return_discount_amount"),
                )
                ->addSelect(DB::raw(
                    'IFNULL((SELECT SUM(bill_histories.discount_amount * bill_histories.remain_sold_quantity) FROM bill_histories
                    WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                    AND bill_histories.product_id = stock_purchase_reports.product_id
                    GROUP BY bill_histories.product_id), 0) AS final_discount_amount'
                ))

                ->where('stock_purchase_reports.product_id', $request->product_id)
                ->where('stock_purchase_reports.branch_id', $request->branch)
                ->whereDate('stock_purchase_reports.created_at', $request->start_date)
                ->groupBy('products.id', 'stock_purchase_reports.purchase_id')
                ->get();
        } else {

            $stocks = DB::table("stock_purchase_reports")
                ->leftJoin('products', 'products.id', '=', 'stock_purchase_reports.product_id')
                ->select(
                    "stock_purchase_reports.purchase_id",
                    "stock_purchase_reports.receipt_no",
                    "stock_purchase_reports.PBuycost",
                    "stock_purchase_reports.PBuycostRate",
                    "stock_purchase_reports.quantity",
                    "stock_purchase_reports.remain_main_quantity",
                    "stock_purchase_reports.created_at",
                    "products.product_name",
                    "stock_purchase_reports.product_id",
                    DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as sold_quantity_total"),
                    DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as total_sold_buycost_value"),

                    DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.netrate) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as total_sold_sellingcost_value"),

                    DB::raw("(SELECT SUM(bill_histories.discount_amount) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as discount_amount"),

                    DB::raw("(SELECT SUM(bill_histories.return_discount_amount) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as return_discount_amount"),
                )
                ->addSelect(DB::raw(
                    'IFNULL((SELECT SUM(bill_histories.discount_amount * bill_histories.remain_sold_quantity) FROM bill_histories
                    WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                    AND bill_histories.product_id = stock_purchase_reports.product_id
                    GROUP BY bill_histories.product_id), 0) AS final_discount_amount'
                ))
                ->where('stock_purchase_reports.product_id', $request->product_id)
                ->where('stock_purchase_reports.branch_id', $request->branch)
                ->groupBy(
                    'products.id',
                    'stock_purchase_reports.purchase_id'
                )
                ->get();
        }

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/branchstockaddhistory', array('tax'=>$tax,'branchname' => $branchname, 'start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'users' => $useritem, 'branchid' => $request->branch, 'product_id' => $request->product_id, 'currency' => $currency));
    }
    public function salesreport($id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        $buyproducts = Buyproduct::selectRaw("
        buyproducts.transaction_id,
        buyproducts.created_at,
        buyproducts.customer_name,
        buyproducts.vat_type,
                SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0)) as sum,
                SUM(buyproducts.vat_amount) as vat,
                SUM(buyproducts.total_amount) as total_amount,
                SUM(buyproducts.totalamount_wo_discount) as totalamount_wo_discount,
                SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) +
                SUM(DISTINCT COALESCE(buyproducts.total_discount_amount, 0)) as discount_amount,
                COALESCE(credit_sums.total_credit_note_amount) as total_credit_note
            ")
            ->leftJoin(
                DB::raw("(SELECT
                            transaction_id,
                            SUM(credit_note_amount) as total_credit_note_amount
                          FROM (
                              SELECT DISTINCT transaction_id, credit_note_id, credit_note_amount
                              FROM credit_note
                          ) as unique_credits
                          GROUP BY transaction_id) as credit_sums"),
                'buyproducts.transaction_id',
                '=',
                'credit_sums.transaction_id'
            )
            ->where('buyproducts.user_id', $id)
            ->groupBy('buyproducts.transaction_id')
            ->orderBy('buyproducts.created_at', 'DESC')
            ->get();

        $start_date = "";
        $end_date = "";

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited user reports page";
        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return view('/admin/Userreport', array('tax'=>$tax,'users' => $useritem, 'uid' => $id, 'buyproducts' => $buyproducts, 'start_date' => $start_date, 'end_date' => $end_date, 'currency' => $currency));
    }
    function userdatDetails($uid, $transaction_id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $products = Buyproduct::select([
            'product_name',
            'quantity',
            'unit',
            'created_at',
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

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();

        return view('/admin/userdatdetails', array('tax'=>$tax,'products' => $products, 'users' => $useritem, 'uid' => $uid, 'currency' => $currency));
    }

    function filtersales(Request $request, $uid)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        if ($request->start_date != $request->end_date) {
            $buyproducts = DB::table('buyproducts')
                ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
                ->select(DB::raw("
                    buyproducts.transaction_id,
                    buyproducts.created_at,
                    payment.type,
                    buyproducts.vat_type,
                    buyproducts.customer_name,
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(buyproducts.vat_amount) as vat,
                    SUM(buyproducts.total_amount) as total_amount,
                    SUM(buyproducts.totalamount_wo_discount) as totalamount_wo_discount,
                    SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
                 "))
                ->where('buyproducts.user_id', $uid)
                ->groupBy('buyproducts.transaction_id')
                ->orderBy('buyproducts.created_at', 'ASC')
                ->whereBetween('buyproducts.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->get();
        } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {
            $buyproducts = DB::table('buyproducts')
                ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
                ->select(DB::raw("
                    buyproducts.transaction_id,
                    buyproducts.created_at,
                    payment.type,
                    buyproducts.vat_type,
                    buyproducts.customer_name,
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(buyproducts.vat_amount) as vat,
                    SUM(buyproducts.total_amount) as total_amount,
                    SUM(buyproducts.totalamount_wo_discount) as totalamount_wo_discount,
                    SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
                 "))
                ->where('buyproducts.user_id', $uid)
                ->groupBy('buyproducts.transaction_id')
                ->orderBy('buyproducts.created_at', 'ASC')
                ->whereDate('buyproducts.created_at', $request->start_date)
                ->get();
        } else {
            $buyproducts = DB::table('buyproducts')
                ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
                ->select(DB::raw("
                    buyproducts.transaction_id,
                    buyproducts.created_at,
                    payment.type,
                    buyproducts.vat_type,
                    buyproducts.customer_name,
                    SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
                    SUM(buyproducts.vat_amount) as vat,
                    SUM(buyproducts.total_amount) as total_amount,
                    SUM(buyproducts.totalamount_wo_discount) as totalamount_wo_discount,
                    SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0)) +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount
                 "))
                ->where('buyproducts.user_id', $uid)
                ->groupBy('buyproducts.transaction_id')
                ->orderBy('buyproducts.created_at', 'ASC')
                ->get();
        }

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/Userreport', array('tax'=>$tax,'buyproducts' => $buyproducts, 'uid' => $uid, 'users' => $useritem, 'start_date' => $start_date, 'end_date' => $end_date, 'currency' => $currency));
    }

    public function userstockreport($id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $branch = DB::table('softwareusers')
            ->select('location')
            ->where('id', $id)
            ->pluck('location')
            ->first();

        $data = DB::table("products")
            ->select(
                "products.*",
                DB::raw("(SELECT SUM(stockhistories.quantity) FROM stockhistories
                                WHERE stockhistories.product_id = products.id
                                AND stockhistories.user_id = '$id'
                                GROUP BY products.id) as product_stock_total"),
                DB::raw("(SELECT SUM(stockhistories.remain_qantity) FROM stockhistories
                                WHERE stockhistories.product_id = products.id
                                AND stockhistories.user_id = '$id'
                                GROUP BY products.id) as product_stock"),
                DB::raw("(SELECT SUM(stockdats.stock_num) FROM stockdats
                                WHERE stockdats.product_id = products.id
                                AND stockdats.user_id = '$id'
                              GROUP BY products.id) as product_stock_num"),
                DB::raw("(SELECT SUM(stockdats.stock_num * stockdats.one_pro_sellingcost) FROM stockdats
                                WHERE stockdats.product_id = products.id
                                AND stockdats.user_id = '$id'
                              GROUP BY products.id) as product_stock_value"),
                DB::raw("(SELECT SUM(stockdats.stock_num * (stockdats.one_pro_sellingcost - stockdats.one_pro_buycost)) FROM stockdats
                                WHERE stockdats.product_id = products.id
                                AND stockdats.user_id = '$id'
                              GROUP BY products.id) as profit_value"),
                // DB::raw("(SELECT SUM(stockhistories.remain_qantity * stockhistories.buycost) FROM stockhistories
                //                 WHERE stockhistories.product_id = products.id
                //                 AND stockhistories.user_id = '$id'
                //               GROUP BY products.id) as product_total_stock_value"),
                DB::raw("(SELECT SUM(stockhistories.quantity * stockhistories.buycost) FROM stockhistories
                                WHERE stockhistories.product_id = products.id
                                AND stockhistories.user_id = '$id'
                              GROUP BY products.id) as product_total_stock_value"),
                DB::raw("(SELECT SUM(stockhistories.remain_qantity * stockhistories.buycost) FROM stockhistories
                                WHERE stockhistories.product_id = products.id
                                AND stockhistories.user_id = '$id'
                              GROUP BY products.id) as product_remain_stock_amount"),
                DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_buycost) FROM bill_histories
                                WHERE bill_histories.product_id = products.id
                                AND bill_histories.user_id = '$id'
                              GROUP BY products.id) as sold_buycost_value"),
            )
            ->where('products.user_id', $id)
            ->where('products.status', 1)
            ->get();

        $uid = $id;
        $start_date = "";
        $end_date = "";

        return view('/admin/userStockReport', array('users' => $useritem, 'uid' => $uid, 'start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $data));
    }
    function userstockAddHistory($uid, $product_id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $stocks = DB::table('stockhistories')
            ->leftJoin('products', 'products.id', '=', 'stockhistories.product_id')
            ->select(DB::raw("stockhistories.product_id,products.product_name,stockhistories.quantity,stockhistories.created_at"),)
            ->where('product_id', $product_id)
            ->get();
        $start_date = "";
        $end_date = "";
        return view('/admin/userStockaddhistory', array('start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'users' => $useritem, 'product_id' => $product_id, 'uid' => $uid));
    }
    function userstockfilter(Request $request)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        if ($request->start_date != $request->end_date) {
            $stocks = DB::table('stockhistories')
                ->leftJoin('products', 'products.id', '=', 'stockhistories.product_id')
                ->select(DB::raw("stockhistories.product_id,products.product_name,stockhistories.quantity,stockhistories.created_at"),)
                ->where('product_id', $request->product_id)
                ->whereBetween('stockhistories.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->get();
        } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {
            $stocks = DB::table('stockhistories')
                ->leftJoin('products', 'products.id', '=', 'stockhistories.product_id')
                ->select(DB::raw("stockhistories.product_id,products.product_name,stockhistories.quantity,stockhistories.created_at"),)
                ->where('product_id', $request->product_id)
                ->whereDate('stockhistories.created_at', $request->start_date)
                ->get();
        } else {
            $stocks = DB::table('stockhistories')
                ->leftJoin('products', 'products.id', '=', 'stockhistories.product_id')
                ->select(DB::raw("stockhistories.product_id,products.product_name,stockhistories.quantity,stockhistories.created_at"),)
                ->where('product_id', $request->product_id)
                ->get();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/userStockaddhistory', array('start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'users' => $useritem, 'uid' => $request->uid, 'product_id' => $request->product_id));
    }
    function userstockTransactionHistory($uid, $id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        $stocks = DB::table('buyproducts')
            ->select(DB::raw("product_id,transaction_id,created_at,customer_name,payment_type,total_amount,product_name,quantity, one_pro_buycost, mrp"),)
            ->where('product_id', $id)
            ->orderBy('transaction_id')
            ->get();
        $start_date = "";
        $end_date = "";
        return view('/admin/userstocktransactionhistory', array('start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'users' => $useritem, 'product_id' => $id, 'uid' => $uid));
    }
    function createSupplier()
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
                    $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();
        } elseif (Session::has('softwareuser')) {
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
        $shopdata = Branch::where('id', $branch)->get();
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        $item = DB::table('branches')
            ->get();

         if (Session('softwareuser')) {
                    $options = [
            'locations' => $item,
            'users' => $useritem,
            'shopdatas' => $shopdata,
            'branch'=>$branch
                    ];
        } elseif (Session('adminuser')) {
                    $options = [
            'locations' => $item,
            'users' => $useritem,
            'branch'=>$branch
                    ];
        }

        return view('/admin/createsupplier', $options);

    }
    function suppliercreateform(Request $req)
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
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        // $req->validate([
        //     'name' => 'required',
        //     // 'mobile' => 'required|numeric',
        //     // 'email' => 'required|email|unique:suppliers,email',
        //     // 'address' => 'required',
        //     'location' => 'required',
        // ]);

        $req->validate(
            [
                'name' => 'required',
                'mobile' => 'numeric',
                // 'email' => 'required|email|unique:suppliers,email',
                'trn_number' => 'required',
            ],
            [
                'name.required' => 'The name field is required.',
                // 'mobile.required' => 'The mobile field is required.',
                'mobile.numeric' => 'The mobile field must be a numeric value.',
                // 'email.required' => 'The email field is required.',
                'trn_number.required' => 'Please enter a trn number.',
            ]
        );



        // Custom validation to check for suppliers with the same name under the same branch
        $existingSupplier = Supplier::where('name', $req->input('name'))
            ->where('location', $req->input('location'))
            ->first();

        if ($existingSupplier) {
            // return back()->with('error', 'A supplier with the same name already exists under this branch.');
            return back()->withInput()->with('error', 'A supplier with the same name already exists under this branch.');
        }

        $user = new Supplier;
        $user->name = $req->input('name');
        $user->location = $req->input('location');
        $user->mobile = $req->input('mobile');
        $user->address = $req->input('address');
        $user->email = $req->input('email');
        $user->trn_number = $req->input('trn_number');
        $user->trade_no = $req->input('trade_license_no');
        $user->adminuser = $adminid;
  if (Session('softwareuser')) {

        $user->softwareuser = $userid;
        }
        $user->business_name = $req->input('business_name');
        $user->billing_add = $req->input('billing_address');
        $user->deli_add = $req->input('delivery_address');
        $user->billing_city = $req->input('billing_city');
        $user->deli_city = $req->input('delivery_city');
        $user->billing_state = $req->input('billing_state');
        $user->deli_state = $req->input('delivery_state');
        $user->billing_postal = $req->input('billing_zip');
        $user->deli_postal = $req->input('delivery_zip');
        $user->billing_landmark = $req->input('billing_landmark');
        $user->deli_landmark = $req->input('delivery_landmark');
        $user->billing_country = $req->input('billing_country');
        $user->deli_country = $req->input('delivery_country');
        $user->b_accountname = $req->input('accountName');
        $user->b_bankname = $req->input('bank_name');
        $user->b_branch = $req->input('branch');
        $user->b_openingbalance = $req->input('openingBalance');
        $user->b_ifsc = $req->input('ifscCode');
        $user->b_iban = $req->input('ibanCode');
        $user->b_accountno = $req->input('account_number');
        $user->b_date = $req->input('date');
        $user->b_accounttype = $req->input('accountType');
        $user->b_upiid = $req->input('upiid');
        $user->b_country = $req->input('country');
        $user->trn_number = $req->input('trn_number');
        $user->save();

        // /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();


        $username = Adminuser::where('id', $adminid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " created supplier named " . $req->input('name');

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($adminid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        // /*-----------------------------------------------------------------------*/
        return back()->with('success', 'Supplier created successfully!');
    }
    function listSupplier()
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

                $item = Supplier::leftJoin('branches', 'suppliers.location', '=', 'branches.id')
                ->leftJoin('supplier_credits', 'suppliers.id', '=', 'supplier_credits.supplier_id')
                ->select(DB::raw("suppliers.name,suppliers.mobile,branches.location,DATE(suppliers.created_at ) as created_date,suppliers.id, suppliers.address, supplier_credits.supplier_id, supplier_credits.due_amt, supplier_credits.collected_amt"))
                ->groupBy('suppliers.created_at')
                ->orderBy('suppliers.created_at', 'DESC')
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

                $branchId = Softwareuser::where('id', $userid)->pluck('location')->first(); // Assuming branch_id is the column in softwareusers table
                $item = Supplier::leftJoin('branches', 'suppliers.location', '=', 'branches.id')
                ->leftJoin('supplier_credits', 'suppliers.id', '=', 'supplier_credits.supplier_id')
                ->select(DB::raw("suppliers.name,suppliers.mobile,branches.location,DATE(suppliers.created_at ) as created_date,suppliers.id, suppliers.address, supplier_credits.supplier_id, supplier_credits.due_amt, supplier_credits.collected_amt"))
                ->where('suppliers.location', $branchId) // Filter by branch
                ->groupBy('suppliers.created_at')
                ->orderBy('suppliers.created_at', 'DESC')
                ->get();
        $shopdata = Branch::where('id', $branchId)->get();
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }



            $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $adminid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited list supplier page";

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($adminid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }
        /*-----------------------------------------------------------------------*/
         if (Session('softwareuser')) {
                    $options = [
            'softusers' => $item,
            'users' => $useritem,
            'shopdatas' => $shopdata,
            'currency'=>$currency
                    ];
        } elseif (Session('adminuser')) {
                    $options = [
            'softusers' => $item,
            'users' => $useritem,
            'currency'=>$currency
                    ];
        }

        return view('/admin/listsuppliers', $options);
    }
    function editSupplier($id)
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
        } elseif (Session::has('softwareuser')) {
            $userid = Session::get('softwareuser');
            $useritem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();
                $branchId = Softwareuser::where('id', $userid)->pluck('location')->first(); // Assuming branch_id is the column in softwareusers table

            // Get the admin_id from the softwareuser
            $adminid = Softwareuser::where('id', $userid)
                ->pluck('admin_id')
                ->first();
        $shopdata = Branch::where('id', $branchId)->get();
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }


        $item = DB::table('branches')->get();
        $loc = DB::table('suppliers')->find($id);

        $data = Supplier::where('id', $id)->get();
        $uid = $id;
                 if (Session('softwareuser')) {
                    $options = [
            'locations' => $item,
            'users' => $useritem,
            'shopdatas' => $shopdata,
            'data'=>$data,
            'uid'=>$uid,
            'supploc'=>$loc

                    ];
        } elseif (Session('adminuser')) {
                    $options = [
            'locations' => $item,
            'users' => $useritem,
            'data'=>$data,
            'uid'=>$uid,
            'supploc'=>$loc
                    ];
        }

        return view('/admin/editsupplier', $options);
    }
    function supplieredit(Request $req)
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
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        // $req->validate([
        //     'name' => 'required',
        //     // 'mobile' => 'required|numeric',
        //     // 'email' => 'required|email',
        //     // 'address' => 'required',
        //     'location' => 'required'
        // ]);

        $req->validate(
            [
                'name' => 'required',
                'mobile' => 'numeric',
                'email' => 'email',
                'location' => 'required',
            ],
            [
                'name.required' => 'The name field is required.',
                // 'mobile.required' => 'The mobile field is required.',
                'mobile.numeric' => 'The mobile field must be a numeric value.',
                // 'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'location.required' => 'The location field is required.',
            ]
        );

        $user = Supplier::find($req->id);

        // Check if the updated name is different from the existing name
        if ($user->name !== $req->name) {
            $existingSupplier = Supplier::where('name', $req->name)
                ->where('location', $user->location) // Same branch
                ->first();

            if ($existingSupplier) {
                return back()->withInput()->with('error', 'A supplier with the same name already exists in this branch.');
            }
        }

        if ($user->email !== $req->email) {
            $existingSupplier = Supplier::where('email', $req->email)
                ->where('location', $user->location) // Same branch
                ->first();

            if ($existingSupplier) {
                return back()->withInput()->with('error', 'A supplier with the same email already exists in this branch.');
            }
        }

        $user = Supplier::find($req->id);
        $user->name = $req->name;
        $user->mobile = $req->mobile;
        $user->address = $req->address;
        $user->email = $req->email;
        $user->location = $req->location;
        $user->business_name = $req->input('business_name');
        $user->trade_no = $req->input('trade_license_no');

    $user->billing_add = $req->input('billing_address');
    $user->deli_add = $req->input('delivery_address');
    $user->billing_city = $req->input('billing_city');
    $user->deli_city = $req->input('delivery_city');
    $user->billing_state = $req->input('billing_state');
    $user->deli_state = $req->input('delivery_state');
    $user->billing_postal = $req->input('billing_zip');
    $user->deli_postal = $req->input('delivery_zip');
    $user->billing_landmark = $req->input('billing_landmark');
    $user->deli_landmark = $req->input('delivery_landmark');
    $user->billing_country = $req->input('billing_country');
    $user->deli_country = $req->input('delivery_country');
    $user->b_accountname = $req->input('accountName');
    $user->b_bankname = $req->input('bank_name');
    $user->b_branch = $req->input('branch');
    $user->b_openingbalance = $req->input('openingBalance');
    $user->b_ifsc = $req->input('ifscCode');
    $user->b_iban = $req->input('ibanCode');
    $user->b_accountno = $req->input('account_number');
    $user->b_date = $req->input('date');
    $user->b_accounttype = $req->input('accountType');
    $user->b_upiid = $req->input('upiid');
    $user->b_country = $req->input('country');
    $user->trn_number = $req->input('trn_number');
    $user->save();
        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();


        $username = Adminuser::where('id', $adminid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " updated supplier named " . $req->name . "'s details";

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($adminid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/
        return back()->with('success', 'Supplier Data Edited Successfully!');
    }
    function userstockTransaction($uid, $product_id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $stocks = DB::table('buyproducts')
            ->select(DB::raw("product_id,transaction_id,created_at,customer_name,payment_type,total_amount,product_name,quantity"),)
            ->where('user_id', $uid)
            ->where('product_id', $product_id)
            ->orderBy('transaction_id')
            ->get();
        $start_date = "";
        $end_date = "";
        return view('/admin/userstocktransactionhistory', array('start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'users' => $useritem, 'product_id' => $product_id, 'uid' => $uid));
    }

    function userdateStocktransactiondate(Request $request, $uid, $product_id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();
        if ($request->start_date != $request->end_date) {
            $stocks = DB::table('buyproducts')
                ->select(DB::raw("product_id,transaction_id,customer_name,payment_type,total_amount,product_name,quantity, created_at"),)
                ->where('product_id', $product_id)
                ->orderBy('transaction_id')
                ->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->get();
        } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {
            $stocks = DB::table('buyproducts')
                ->select(DB::raw("product_id,transaction_id,created_at,customer_name,payment_type,total_amount,product_name,quantity, created_at"),)
                ->where('product_id', $product_id)
                ->orderBy('transaction_id')
                ->whereDate('created_at', $request->start_date)
                ->get();
        } else {
            $stocks = DB::table('buyproducts')
                ->select(DB::raw("product_id,transaction_id,customer_name,payment_type,total_amount,product_name,quantity, created_at"),)
                ->where('product_id', $product_id)
                ->orderBy('transaction_id')
                ->get();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/userstocktransactionhistory', array('start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'users' => $useritem, 'product_id' => $product_id, 'uid' => $uid));
    }
    function userpurchase($uid)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        $buyproducts = DB::table('buyproducts')
            ->where('user_id', $uid)
            ->groupBy('transaction_id')
            ->orderBy('created_at', 'ASC')
            ->get();

        $purchases = DB::table('stockdetails')
            ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
            ->where('stockdetails.user_id', $uid)
            ->groupBy('stockdetails.reciept_no')
            ->orderBy('stockdetails.created_at', 'DESC')
            ->get();

        $start_date = "";
        $end_date = "";
        return view('/admin/userpurchase', array('tax'=>$tax,'start_date' => $start_date, 'end_date' => $end_date, 'purchases' => $purchases, 'buyproducts' => $buyproducts, 'users' => $useritem, 'uid' => $uid, 'currency' => $currency));
    }

    function userpurchasedata(Request $request, $uid)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        if ($request->start_date != $request->end_date) {
            $purchases = DB::table('stockdetails')
                ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->where('stockdetails.user_id', $uid)
                ->whereBetween('stockdetails.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'ASC')
                ->get();
        } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {
            $purchases = DB::table('stockdetails')
                ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->where('stockdetails.user_id', $uid)
                ->whereDate('stockdetails.created_at', $request->start_date)
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'ASC')
                ->get();
        } else {
            $purchases = DB::table('stockdetails')
                ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.file as file,stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
                ->where('stockdetails.user_id', $uid)
                ->groupBy('stockdetails.reciept_no')
                ->orderBy('stockdetails.created_at', 'ASC')
                ->get();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/userpurchase', array('tax'=>$tax,'purchases' => $purchases, 'users' => $useritem, 'start_date' => $start_date, 'end_date' => $end_date, 'uid' => $uid, 'currency' => $currency));
    }
    function userpurchasereturn($uid)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        $purchases = DB::table('returnpurchases')
            ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
            ->select(DB::raw('returnpurchases.id as id, returnpurchases.reciept_no as reciept_no, returnpurchases.created_at as created_at, returnpurchases.comment as comment, returnpurchases.amount as price, returnpurchases.shop_name as supplier, returnpurchases.amount_without_vat as price_without_vat, returnpurchases.vat_amount as vat_amount, products.product_name as product_name, returnpurchases.quantity as quantity'))
            ->where('returnpurchases.user_id', $uid)
            ->orderBy('returnpurchases.created_at', 'DESC')
            ->paginate(10);

        $start_date = "";
        $end_date = "";
        return view('/admin/userpurchasereturn', array('tax'=>$tax,'start_date' => $start_date, 'end_date' => $end_date, 'purchases' => $purchases, 'users' => $useritem, 'uid' => $uid, 'currency' => $currency));
    }
    function userPurchasesreturnfilter(Request $request, $uid)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        if ($request->start_date != $request->end_date) {
            $purchases = DB::table('returnpurchases')
                ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
                ->select(DB::raw('returnpurchases.id as id, returnpurchases.reciept_no as reciept_no, returnpurchases.created_at as created_at, returnpurchases.comment as comment, returnpurchases.amount as price, returnpurchases.shop_name as supplier, returnpurchases.amount_without_vat as price_without_vat, returnpurchases.vat_amount as vat_amount, products.product_name as product_name, returnpurchases.quantity as quantity'))
                ->where('returnpurchases.user_id', $uid)
                ->whereBetween('returnpurchases.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->orderBy('returnpurchases.created_at', 'ASC')
                ->get();
        } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {
            $purchases = DB::table('returnpurchases')
                ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
                ->select(DB::raw('returnpurchases.id as id, returnpurchases.reciept_no as reciept_no, returnpurchases.created_at as created_at, returnpurchases.comment as comment, returnpurchases.amount as price, returnpurchases.shop_name as supplier, returnpurchases.amount_without_vat as price_without_vat, returnpurchases.vat_amount as vat_amount, products.product_name as product_name, returnpurchases.quantity as quantity'))
                ->where('returnpurchases.user_id', $uid)
                ->whereDate('returnpurchases.created_at', $request->start_date)
                ->orderBy('returnpurchases.created_at', 'ASC')
                ->get();
        } else {
            $purchases = DB::table('returnpurchases')
                ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
                ->select(DB::raw('returnpurchases.id as id, returnpurchases.reciept_no as reciept_no, returnpurchases.created_at as created_at, returnpurchases.comment as comment, returnpurchases.amount as price, returnpurchases.shop_name as supplier, returnpurchases.amount_without_vat as price_without_vat, returnpurchases.vat_amount as vat_amount, products.product_name as product_name, returnpurchases.quantity as quantity'))
                ->where('returnpurchases.user_id', $uid)
                ->orderBy('returnpurchases.created_at', 'ASC')
                ->get();
        }

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/userpurchasereturn', array('tax'=>$tax,'purchases' => $purchases, 'users' => $useritem, 'start_date' => $start_date, 'end_date' => $end_date, 'uid' => $uid, 'currency' => $currency));
    }
    function userReturndat($uid)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        $returns = DB::table('returnproducts')
            ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
            ->select(
                DB::raw('returnproducts.id as id'),
                DB::raw('returnproducts.transaction_id as transaction_id'),
                DB::raw('returnproducts.created_at as created_at'),
                DB::raw('returnproducts.phone as phone'),
                DB::raw('returnproducts.vat_type as vat_type'),
                DB::raw('returnproducts.quantity'),
                DB::raw('SUM(returnproducts.totalamount_wo_discount) as totalamount_wo_discount'),
                DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as total_amount'),
                DB::raw('SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount'),
                DB::raw('SUM(returnproducts.vat_amount) as vat'),
                DB::raw('GROUP_CONCAT(products.product_name) as product_names'),
                DB::raw('GROUP_CONCAT(returnproducts.quantity) as quantities')
            )
            ->groupBy('returnproducts.transaction_id', 'returnproducts.created_at')
            ->orderBy('returnproducts.created_at', 'DESC')
            ->where('returnproducts.user_id', $uid)
            ->get();

        $start_date = "";
        $end_date = "";
        return view('/admin/userreturndat', array('tax'=>$tax,'start_date' => $start_date, 'end_date' => $end_date, 'returns' => $returns, 'uid' => $uid, 'users' => $useritem, 'currency' => $currency));
    }
    function dateUserreturndate(Request $request, $uid)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        if ($request->start_date != $request->end_date) {

            $returns = DB::table('returnproducts')
                ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
                ->select(
                    DB::raw('returnproducts.id as id'),
                    DB::raw('returnproducts.transaction_id as transaction_id'),
                    DB::raw('returnproducts.created_at as created_at'),
                    DB::raw('returnproducts.phone as phone'),
                    DB::raw('returnproducts.vat_type as vat_type'),
                    DB::raw('returnproducts.quantity'),
                    DB::raw('SUM(returnproducts.totalamount_wo_discount) as totalamount_wo_discount'),
                    DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as total_amount'),
                    DB::raw('SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount'),
                    DB::raw('SUM(returnproducts.vat_amount) as vat'),
                    DB::raw('GROUP_CONCAT(products.product_name) as product_names'),
                    DB::raw('GROUP_CONCAT(returnproducts.quantity) as quantities')
                )
                ->groupBy('returnproducts.transaction_id', 'returnproducts.created_at')
                ->orderBy('returnproducts.created_at', 'DESC')
                ->where('returnproducts.user_id', $uid)
                ->whereBetween('returnproducts.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->get();
        } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {

            $returns = DB::table('returnproducts')
                ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
                ->select(
                    DB::raw('returnproducts.id as id'),
                    DB::raw('returnproducts.transaction_id as transaction_id'),
                    DB::raw('returnproducts.created_at as created_at'),
                    DB::raw('returnproducts.phone as phone'),
                    DB::raw('returnproducts.vat_type as vat_type'),
                    DB::raw('returnproducts.quantity'),
                    DB::raw('SUM(returnproducts.totalamount_wo_discount) as totalamount_wo_discount'),
                    DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as total_amount'),
                    DB::raw('SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount'),
                    DB::raw('SUM(returnproducts.vat_amount) as vat'),
                    DB::raw('GROUP_CONCAT(products.product_name) as product_names'),
                    DB::raw('GROUP_CONCAT(returnproducts.quantity) as quantities')
                )
                ->groupBy('returnproducts.transaction_id', 'returnproducts.created_at')
                ->orderBy('returnproducts.created_at', 'DESC')
                ->where('returnproducts.user_id', $uid)
                ->whereDate('returnproducts.created_at', $request->start_date)
                ->get();
        } else {

            $returns = DB::table('returnproducts')
                ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
                ->select(
                    DB::raw('returnproducts.id as id'),
                    DB::raw('returnproducts.transaction_id as transaction_id'),
                    DB::raw('returnproducts.created_at as created_at'),
                    DB::raw('returnproducts.phone as phone'),
                    DB::raw('returnproducts.vat_type as vat_type'),
                    DB::raw('returnproducts.quantity'),
                    DB::raw('SUM(returnproducts.totalamount_wo_discount) as totalamount_wo_discount'),
                    DB::raw('SUM(DISTINCT COALESCE(grand_total, 0)) as total_amount'),
                    DB::raw('SUM(COALESCE(returnproducts.discount_amount, 0)) + SUM(returnproducts.total_amount * (total_discount_percent / 100)) as discount_amount'),
                    DB::raw('SUM(returnproducts.vat_amount) as vat'),
                    DB::raw('GROUP_CONCAT(products.product_name) as product_names'),
                    DB::raw('GROUP_CONCAT(returnproducts.quantity) as quantities')
                )
                ->groupBy('returnproducts.transaction_id', 'returnproducts.created_at')
                ->orderBy('returnproducts.created_at', 'DESC')
                ->where('returnproducts.user_idv', $uid)
                ->get();
        }

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('/admin/userreturndat', array('tax'=>$tax,'returns' => $returns, 'uid' => $uid, 'users' => $useritem, 'start_date' => $start_date, 'end_date' => $end_date, 'currency' => $currency));
    }
    function supplier_salesreport($supplier)
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
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        $purchasedata = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, stockdetails.comment as comment, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.payment_mode as payment_mode, SUM(stockdetails.price_without_vat) as price_without_vat, SUM(stockdetails.vat_amount) as vat_amount'))
            ->where('stockdetails.supplier_id', $supplier)
            ->groupBy('stockdetails.reciept_no')
            ->orderBy('stockdetails.created_at', 'DESC')
            ->get();

        $totalPrice = $purchasedata->sum('price');

        $suppliername = Supplier::where('id', $supplier)->pluck('name')->first();

        $shopdata = Adminuser::Where('id', $adminid)->get();

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $adminid)->pluck('username')->first();
        $tax = Adminuser::where('id', $adminid)->pluck('tax')->first();

        $user_type = 'webadmin';
        $message = $username . " visited stock purchase report of supplier " . $suppliername;

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($adminid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return view('/admin/supplier_sales_report', array('tax'=>$tax,'purchasedata' => $purchasedata, 'users' => $useritem, 'shopdatas' => $shopdata, 'supplier' => $supplier, 'currency' => $currency, 'totalPrice' => $totalPrice));
    }
    function Stock()
    {
        if (Session::has('softwareuser')) {
        } elseif (Session::has('adminuser')) {
        } else {
            return redirect('userlogin'); // or 'adminlogin' based on your logic
        }

        if (Session::has('adminuser')) {
            $adminid = Session::get('adminuser');
            $useritem = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();

                $stocks = DB::table("products")
                ->select(
                    "products.*",
                    DB::raw("(SELECT SUM(stockhistories.quantity) FROM stockhistories
                                    WHERE stockhistories.product_id = products.id
                                    GROUP BY products.id) as product_stock_total"),
                    DB::raw("(SELECT SUM(stockhistories.remain_qantity) FROM stockhistories
                                    WHERE stockhistories.product_id = products.id
                                    GROUP BY products.id) as product_stock"),
                    DB::raw("(SELECT SUM(stockdats.stock_num) FROM stockdats
                                    WHERE stockdats.product_id = products.id
                                  GROUP BY products.id) as product_stock_num"),
                    DB::raw("(SELECT SUM(stockdats.stock_num * stockdats.netrate) - COALESCE(SUM(stockdats.credit_note_amount), 0)
                                  FROM stockdats
                                  WHERE stockdats.product_id = products.id
                                  GROUP BY stockdats.product_id) as product_stock_value"),
                    DB::raw("(SELECT SUM(stockdats.stock_num * (stockdats.netrate - stockdats.one_pro_buycost_rate)) FROM stockdats
                                    WHERE stockdats.product_id = products.id
                                  GROUP BY products.id) as profit_value"),
                    DB::raw("(SELECT SUM(stockhistories.quantity * stockhistories.rate) FROM stockhistories
                                    WHERE stockhistories.product_id = products.id
                                  GROUP BY products.id) as product_total_stock_value"),
                    DB::raw("(SELECT SUM(stockhistories.remain_qantity * stockhistories.rate) FROM stockhistories
                                        WHERE stockhistories.product_id = products.id
                                      GROUP BY products.id) as product_remain_stock_amount"),
                    DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                                    WHERE bill_histories.product_id = products.id
                                  GROUP BY products.id) as sold_buycost_value"),
                    DB::raw("(SELECT SUM(buyproducts.discount_amount) FROM buyproducts
                                    WHERE buyproducts.product_id = products.id
                                  GROUP BY products.id) as discount_amount"),
                    DB::raw("(SELECT SUM(returnproducts.discount_amount) FROM returnproducts
                                    WHERE returnproducts.product_id = products.id
                                  GROUP BY products.id) as return_discount_amount"),

                )
                ->addSelect(DB::raw('IFNULL((SELECT SUM(COALESCE(buyproducts.discount_amount * buyproducts.remain_quantity, 0)) FROM buyproducts
                            WHERE buyproducts.product_id = products.id
                          GROUP BY products.id), 0) AS final_discount_amount'))

                ->orderBy('products.id')
                ->where('products.status', 1)
                ->get();

        } elseif (Session::has('softwareuser')) {
            $userid = Session::get('softwareuser');
            $useritem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();

                $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

                $stocks = DB::table("products")
                ->select(
                    "products.*",
                    DB::raw("(SELECT SUM(stockhistories.quantity) FROM stockhistories
                                    WHERE stockhistories.product_id = products.id
                                    GROUP BY products.id) as product_stock_total"),
                    DB::raw("(SELECT SUM(stockhistories.remain_qantity) FROM stockhistories
                                    WHERE stockhistories.product_id = products.id
                                    GROUP BY products.id) as product_stock"),
                    DB::raw("(SELECT SUM(stockdats.stock_num) FROM stockdats
                                    WHERE stockdats.product_id = products.id
                                  GROUP BY products.id) as product_stock_num"),
                    DB::raw("(SELECT SUM(stockdats.stock_num * stockdats.netrate) - COALESCE(SUM(stockdats.credit_note_amount), 0)
                                  FROM stockdats
                                  WHERE stockdats.product_id = products.id
                                  GROUP BY stockdats.product_id) as product_stock_value"),
                    DB::raw("(SELECT SUM(stockdats.stock_num * (stockdats.netrate - stockdats.one_pro_buycost_rate)) FROM stockdats
                                    WHERE stockdats.product_id = products.id
                                  GROUP BY products.id) as profit_value"),
                    DB::raw("(SELECT SUM(stockhistories.quantity * stockhistories.rate) FROM stockhistories
                                    WHERE stockhistories.product_id = products.id
                                  GROUP BY products.id) as product_total_stock_value"),
                    DB::raw("(SELECT SUM(stockhistories.remain_qantity * stockhistories.rate) FROM stockhistories
                                        WHERE stockhistories.product_id = products.id
                                      GROUP BY products.id) as product_remain_stock_amount"),
                    DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                                    WHERE bill_histories.product_id = products.id
                                  GROUP BY products.id) as sold_buycost_value"),
                    DB::raw("(SELECT SUM(buyproducts.discount_amount) FROM buyproducts
                                    WHERE buyproducts.product_id = products.id
                                  GROUP BY products.id) as discount_amount"),
                    DB::raw("(SELECT SUM(returnproducts.discount_amount) FROM returnproducts
                                    WHERE returnproducts.product_id = products.id
                                  GROUP BY products.id) as return_discount_amount"),

                )
                ->addSelect(DB::raw('IFNULL((SELECT SUM(COALESCE(buyproducts.discount_amount * buyproducts.remain_quantity, 0)) FROM buyproducts
                            WHERE buyproducts.product_id = products.id
                          GROUP BY products.id), 0) AS final_discount_amount'))

                ->orderBy('products.id')
                ->where('products.status', 1)
                ->where('products.branch', $branch)
                ->get();

            // Get the admin_id from the softwareuser
            $adminid = Softwareuser::where('id', $userid)
                ->pluck('admin_id')
                ->first();
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        //  new buycost with vat and netrate (selling cost + vat amount) instead of sellingcost


        $start_date = "";
        $end_date = "";

        // /------------------GET IP ADDRESS---------------------------------------/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $adminid)->pluck('username')->first();
        $tax = Adminuser::where('id', $adminid)->pluck('tax')->first();

        $user_type = 'webadmin';
        $message = $username . " visited stock report page";

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($adminid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        // /-----------------------------------------------------------------------/
        return view('/admin/stock', array('tax'=>$tax,'start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'users' => $useritem, 'currency' => $currency));
    }
    function stockHistory($id)
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

                $stocks = DB::table("stock_purchase_reports")
                ->leftJoin('products', 'products.id', '=', 'stock_purchase_reports.product_id')
                ->select(
                    "stock_purchase_reports.purchase_id",
                    "stock_purchase_reports.receipt_no",
                    "stock_purchase_reports.PBuycost",
                    "stock_purchase_reports.PBuycostRate",
                    "stock_purchase_reports.quantity",
                    "stock_purchase_reports.remain_main_quantity",
                    "stock_purchase_reports.created_at",
                    "products.product_name",
                    "stock_purchase_reports.product_id",
                    DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as sold_quantity_total"),
                    DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as total_sold_buycost_value"),

                        DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.netrate)
                        FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as total_sold_sellingcost_value"),

                    DB::raw("(SELECT SUM(bill_histories.discount_amount) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as discount_amount"),

                    DB::raw("(SELECT SUM(bill_histories.return_discount_amount) FROM bill_histories
                        WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                        AND bill_histories.product_id = stock_purchase_reports.product_id
                        GROUP BY bill_histories.product_id) as return_discount_amount"),
                )
                ->addSelect(DB::raw(
                    'IFNULL((SELECT SUM(bill_histories.discount_amount * bill_histories.remain_sold_quantity) FROM bill_histories
                    WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                    AND bill_histories.product_id = stock_purchase_reports.product_id
                    GROUP BY bill_histories.product_id), 0) AS final_discount_amount'
                ))

                ->where('stock_purchase_reports.product_id', $id)
                ->groupBy('products.id', 'stock_purchase_reports.purchase_id')
                ->get();

        } elseif (Session::has('softwareuser')) {
            $userid = Session::get('softwareuser');
            $useritem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();

                $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

                $stocks = DB::table("stock_purchase_reports")
            ->leftJoin('products', 'products.id', '=', 'stock_purchase_reports.product_id')
            ->select(
                "stock_purchase_reports.purchase_id",
                "stock_purchase_reports.receipt_no",
                "stock_purchase_reports.PBuycost",
                "stock_purchase_reports.PBuycostRate",
                "stock_purchase_reports.quantity",
                "stock_purchase_reports.remain_main_quantity",
                "stock_purchase_reports.created_at",
                "products.product_name",
                "stock_purchase_reports.product_id",
                DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity) FROM bill_histories
                    WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                    AND bill_histories.product_id = stock_purchase_reports.product_id
                    GROUP BY bill_histories.product_id) as sold_quantity_total"),
                DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                    WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                    AND bill_histories.product_id = stock_purchase_reports.product_id
                    GROUP BY bill_histories.product_id) as total_sold_buycost_value"),

                    DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.netrate)
                    FROM bill_histories
                    WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                    AND bill_histories.product_id = stock_purchase_reports.product_id
                    GROUP BY bill_histories.product_id) as total_sold_sellingcost_value"),

                DB::raw("(SELECT SUM(bill_histories.discount_amount) FROM bill_histories
                    WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                    AND bill_histories.product_id = stock_purchase_reports.product_id
                    GROUP BY bill_histories.product_id) as discount_amount"),

                DB::raw("(SELECT SUM(bill_histories.return_discount_amount) FROM bill_histories
                    WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                    AND bill_histories.product_id = stock_purchase_reports.product_id
                    GROUP BY bill_histories.product_id) as return_discount_amount"),
            )
            ->addSelect(DB::raw(
                'IFNULL((SELECT SUM(bill_histories.discount_amount * bill_histories.remain_sold_quantity) FROM bill_histories
                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                AND bill_histories.product_id = stock_purchase_reports.product_id
                GROUP BY bill_histories.product_id), 0) AS final_discount_amount'
            ))

            ->where('stock_purchase_reports.product_id', $id)
            ->where('stock_purchase_reports.branch_id', $branch)
            ->groupBy('products.id', 'stock_purchase_reports.purchase_id')
            ->get();



            // Get the admin_id from the softwareuser
            $adminid = Softwareuser::where('id', $userid)
                ->pluck('admin_id')
                ->first();
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }


        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
        //new buycost with vat - rate and sellcost with vat as netrate


        $start_date = "";
        $end_date = "";
        return view('/admin/stockhistory', array('tax'=>$tax,'start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'users' => $useritem, 'product_id' => $id, 'currency' => $currency));
    }
    function dateStockdate(Request $request)
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
                if ($request->start_date != $request->end_date) {

                    $stocks = DB::table("stock_purchase_reports")
                        ->leftJoin('products', 'products.id', '=', 'stock_purchase_reports.product_id')
                        ->select(
                            "stock_purchase_reports.purchase_id",
                            "stock_purchase_reports.receipt_no",
                            "stock_purchase_reports.PBuycost",
                            "stock_purchase_reports.PBuycostRate",
                            "stock_purchase_reports.quantity",
                            "stock_purchase_reports.remain_main_quantity",
                            "stock_purchase_reports.created_at",
                            "products.product_name",
                            "stock_purchase_reports.product_id",
                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as sold_quantity_total"),
                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as total_sold_buycost_value"),

                                DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.netrate)
                                FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as total_sold_sellingcost_value"),

                            DB::raw("(SELECT SUM(bill_histories.discount_amount) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as discount_amount"),

                            DB::raw("(SELECT SUM(bill_histories.return_discount_amount) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as return_discount_amount"),
                        )
                        ->addSelect(DB::raw(
                            'IFNULL((SELECT SUM(bill_histories.discount_amount * bill_histories.remain_sold_quantity) FROM bill_histories
                            WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                            AND bill_histories.product_id = stock_purchase_reports.product_id
                            GROUP BY bill_histories.product_id), 0) AS final_discount_amount'
                        ))

                        ->where('stock_purchase_reports.product_id', $request->product_id)
                        ->whereBetween('stock_purchase_reports.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                        ->groupBy('products.id', 'stock_purchase_reports.purchase_id')
                        ->get();
                } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {

                    $stocks = DB::table("stock_purchase_reports")
                        ->leftJoin('products', 'products.id', '=', 'stock_purchase_reports.product_id')
                        ->select(
                            "stock_purchase_reports.purchase_id",
                            "stock_purchase_reports.receipt_no",
                            "stock_purchase_reports.PBuycost",
                            "stock_purchase_reports.PBuycostRate",
                            "stock_purchase_reports.quantity",
                            "stock_purchase_reports.remain_main_quantity",
                            "stock_purchase_reports.created_at",
                            "products.product_name",
                            "stock_purchase_reports.product_id",
                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as sold_quantity_total"),
                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as total_sold_buycost_value"),

                                DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.netrate)
                                FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as total_sold_sellingcost_value"),

                            DB::raw("(SELECT SUM(bill_histories.discount_amount) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as discount_amount"),

                            DB::raw("(SELECT SUM(bill_histories.return_discount_amount) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as return_discount_amount"),
                        )
                        ->addSelect(DB::raw(
                            'IFNULL((SELECT SUM(bill_histories.discount_amount * bill_histories.remain_sold_quantity) FROM bill_histories
                            WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                            AND bill_histories.product_id = stock_purchase_reports.product_id
                            GROUP BY bill_histories.product_id), 0) AS final_discount_amount'
                        ))

                        ->where('stock_purchase_reports.product_id', $request->product_id)
                        ->whereDate('stock_purchase_reports.created_at', $request->start_date)
                        ->groupBy('products.id', 'stock_purchase_reports.purchase_id')
                        ->get();
                } else {

                    $stocks = DB::table("stock_purchase_reports")
                        ->leftJoin('products', 'products.id', '=', 'stock_purchase_reports.product_id')
                        ->select(
                            "stock_purchase_reports.purchase_id",
                            "stock_purchase_reports.receipt_no",
                            "stock_purchase_reports.PBuycost",
                            "stock_purchase_reports.PBuycostRate",
                            "stock_purchase_reports.quantity",
                            "stock_purchase_reports.remain_main_quantity",
                            "stock_purchase_reports.created_at",
                            "products.product_name",
                            "stock_purchase_reports.product_id",
                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as sold_quantity_total"),
                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as total_sold_buycost_value"),

                                DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.netrate)
                                FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as total_sold_sellingcost_value"),

                            DB::raw("(SELECT SUM(bill_histories.discount_amount) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as discount_amount"),

                            DB::raw("(SELECT SUM(bill_histories.return_discount_amount) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as return_discount_amount"),
                        )
                        ->addSelect(DB::raw(
                            'IFNULL((SELECT SUM(bill_histories.discount_amount * bill_histories.remain_sold_quantity) FROM bill_histories
                            WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                            AND bill_histories.product_id = stock_purchase_reports.product_id
                            GROUP BY bill_histories.product_id), 0) AS final_discount_amount'
                        ))
                        ->where('stock_purchase_reports.product_id', $request->product_id)
                        ->groupBy(
                            'products.id',
                            'stock_purchase_reports.purchase_id'
                        )
                        ->get();
                }

        } elseif (Session::has('softwareuser')) {
            $userid = Session::get('softwareuser');
            $useritem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();

                $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

                if ($request->start_date != $request->end_date) {

                    $stocks = DB::table("stock_purchase_reports")
                        ->leftJoin('products', 'products.id', '=', 'stock_purchase_reports.product_id')
                        ->select(
                            "stock_purchase_reports.purchase_id",
                            "stock_purchase_reports.receipt_no",
                            "stock_purchase_reports.PBuycost",
                            "stock_purchase_reports.PBuycostRate",
                            "stock_purchase_reports.quantity",
                            "stock_purchase_reports.remain_main_quantity",
                            "stock_purchase_reports.created_at",
                            "products.product_name",
                            "stock_purchase_reports.product_id",
                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as sold_quantity_total"),
                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as total_sold_buycost_value"),

                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.netrate) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as total_sold_sellingcost_value"),

                            DB::raw("(SELECT SUM(bill_histories.discount_amount) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as discount_amount"),

                            DB::raw("(SELECT SUM(bill_histories.return_discount_amount) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as return_discount_amount"),
                        )
                        ->addSelect(DB::raw(
                            'IFNULL((SELECT SUM(bill_histories.discount_amount * bill_histories.remain_sold_quantity) FROM bill_histories
                            WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                            AND bill_histories.product_id = stock_purchase_reports.product_id
                            GROUP BY bill_histories.product_id), 0) AS final_discount_amount'
                        ))

                        ->where('stock_purchase_reports.product_id', $request->product_id)
                        ->where('stock_purchase_reports.branch_id', $branch)
                        ->whereBetween('stock_purchase_reports.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                        ->groupBy('products.id', 'stock_purchase_reports.purchase_id')
                        ->get();
                } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {

                    $stocks = DB::table("stock_purchase_reports")
                        ->leftJoin('products', 'products.id', '=', 'stock_purchase_reports.product_id')
                        ->select(
                            "stock_purchase_reports.purchase_id",
                            "stock_purchase_reports.receipt_no",
                            "stock_purchase_reports.PBuycost",
                            "stock_purchase_reports.PBuycostRate",
                            "stock_purchase_reports.quantity",
                            "stock_purchase_reports.remain_main_quantity",
                            "stock_purchase_reports.created_at",
                            "products.product_name",
                            "stock_purchase_reports.product_id",
                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as sold_quantity_total"),
                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as total_sold_buycost_value"),

                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.netrate) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as total_sold_sellingcost_value"),

                            DB::raw("(SELECT SUM(bill_histories.discount_amount) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as discount_amount"),

                            DB::raw("(SELECT SUM(bill_histories.return_discount_amount) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as return_discount_amount"),
                        )
                        ->addSelect(DB::raw(
                            'IFNULL((SELECT SUM(bill_histories.discount_amount * bill_histories.remain_sold_quantity) FROM bill_histories
                            WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                            AND bill_histories.product_id = stock_purchase_reports.product_id
                            GROUP BY bill_histories.product_id), 0) AS final_discount_amount'
                        ))

                        ->where('stock_purchase_reports.product_id', $request->product_id)
                        ->where('stock_purchase_reports.branch_id', $branch)
                        ->whereDate('stock_purchase_reports.created_at', $request->start_date)
                        ->groupBy('products.id', 'stock_purchase_reports.purchase_id')
                        ->get();
                } else {

                    $stocks = DB::table("stock_purchase_reports")
                        ->leftJoin('products', 'products.id', '=', 'stock_purchase_reports.product_id')
                        ->select(
                            "stock_purchase_reports.purchase_id",
                            "stock_purchase_reports.receipt_no",
                            "stock_purchase_reports.PBuycost",
                            "stock_purchase_reports.PBuycostRate",
                            "stock_purchase_reports.quantity",
                            "stock_purchase_reports.remain_main_quantity",
                            "stock_purchase_reports.created_at",
                            "products.product_name",
                            "stock_purchase_reports.product_id",
                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as sold_quantity_total"),
                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.Purchase_Buycost_Rate) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as total_sold_buycost_value"),

                            DB::raw("(SELECT SUM(bill_histories.remain_sold_quantity * bill_histories.netrate) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as total_sold_sellingcost_value"),

                            DB::raw("(SELECT SUM(bill_histories.discount_amount) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as discount_amount"),

                            DB::raw("(SELECT SUM(bill_histories.return_discount_amount) FROM bill_histories
                                WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                                AND bill_histories.product_id = stock_purchase_reports.product_id
                                GROUP BY bill_histories.product_id) as return_discount_amount"),
                        )
                        ->addSelect(DB::raw(
                            'IFNULL((SELECT SUM(bill_histories.discount_amount * bill_histories.remain_sold_quantity) FROM bill_histories
                            WHERE bill_histories.pid = stock_purchase_reports.purchase_id
                            AND bill_histories.product_id = stock_purchase_reports.product_id
                            GROUP BY bill_histories.product_id), 0) AS final_discount_amount'
                        ))
                        ->where('stock_purchase_reports.product_id', $request->product_id)
                        ->where('stock_purchase_reports.branch_id', $branch)
                        ->groupBy(
                            'products.id',
                            'stock_purchase_reports.purchase_id'
                        )
                        ->get();
                }


            // Get the admin_id from the softwareuser
            $adminid = Softwareuser::where('id', $userid)
                ->pluck('admin_id')
                ->first();
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }


        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/stockhistory', array('tax'=>$tax,'start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'users' => $useritem, 'product_id' => $request->product_id, 'currency' => $currency));
    }
    function stockTransHistory($id)
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

                $stocks = DB::table('buyproducts')
                ->select(
                    'product_id',
                    'transaction_id',
                    'created_at',
                    'customer_name',
                    'payment_type',
                    'total_amount',
                    'product_name',
                    'quantity',
                    'one_pro_buycost',
                    'mrp',
                    'netrate',
                    'totalamount_wo_discount',
                    'discount_amount',
                    DB::raw('(SELECT SUM(credit_note_amount)
                              FROM credit_note
                              WHERE credit_note.transaction_id = buyproducts.transaction_id
                                AND credit_note.product_id = buyproducts.product_id) as total_credit_note_amount')
                )
                ->where('product_id', $id)
                ->orderBy('transaction_id')
                ->get();
        } elseif (Session::has('softwareuser')) {
            $userid = Session::get('softwareuser');
            $useritem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();

                $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();
                $stocks = DB::table('buyproducts')
                ->select(
                    'product_id',
                    'transaction_id',
                    'created_at',
                    'customer_name',
                    'payment_type',
                    'total_amount',
                    'product_name',
                    'quantity',
                    'one_pro_buycost',
                    'mrp',
                    'netrate',
                    'totalamount_wo_discount',
                    'discount_amount',
                    DB::raw('(SELECT SUM(credit_note_amount)
                              FROM credit_note
                              WHERE credit_note.transaction_id = buyproducts.transaction_id
                                AND credit_note.product_id = buyproducts.product_id) as total_credit_note_amount')
                )
                ->where('product_id', $id)
                ->where('branch', $branch)
                ->orderBy('transaction_id')
                ->get();


            // Get the admin_id from the softwareuser
            $adminid = Softwareuser::where('id', $userid)
                ->pluck('admin_id')
                ->first();
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();



        $start_date = "";
        $end_date = "";
        return view('/admin/stocktranshistory', array('tax'=>$tax,'start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'users' => $useritem, 'product_id' => $id, 'currency' => $currency));
    }
    function dateStocktransdate(Request $request)
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
                if ($request->start_date != $request->end_date) {
                    $stocks = DB::table('buyproducts')
                        ->select(DB::raw("product_id,transaction_id,customer_name,payment_type,total_amount,product_name,quantity,created_at, one_pro_buycost, mrp,netrate,totalamount_wo_discount,discount_amount"))
                        ->where('product_id', $request->product_id)
                        ->orderBy('transaction_id')
                        ->whereBetween('created_at', [$request->start_date, $request->end_date])
                        ->get();
                } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {
                    $stocks = DB::table('buyproducts')
                        ->select(DB::raw("product_id,transaction_id,created_at,customer_name,payment_type,total_amount,product_name,quantity, one_pro_buycost, mrp,netrate,totalamount_wo_discount,discount_amount"))
                        ->where('product_id', $request->product_id)
                        ->orderBy('transaction_id')
                        ->whereDate('created_at', $request->start_date)
                        ->get();
                } else {
                    $stocks = DB::table('buyproducts')
                        ->select(DB::raw("product_id,transaction_id,customer_name,payment_type,total_amount,product_name,quantity,created_at, one_pro_buycost, mrp,netrate,totalamount_wo_discount,discount_amount"))
                        ->where('product_id', $request->product_id)
                        ->orderBy('transaction_id')
                        ->get();
                }
        } elseif (Session::has('softwareuser')) {
            $userid = Session::get('softwareuser');
            $useritem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();

                $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();
                if ($request->start_date != $request->end_date) {
                    $stocks = DB::table('buyproducts')
                    ->select(
                        'product_id',
                        'transaction_id',
                        'created_at',
                        'customer_name',
                        'payment_type',
                        'total_amount',
                        'product_name',
                        'quantity',
                        'one_pro_buycost',
                        'mrp',
                        'netrate',
                        'totalamount_wo_discount',
                        'discount_amount',
                        DB::raw('(SELECT SUM(credit_note_amount)
                                  FROM credit_note
                                  WHERE credit_note.transaction_id = buyproducts.transaction_id
                                    AND credit_note.product_id = buyproducts.product_id) as total_credit_note_amount')
                    )
                        ->where('product_id', $request->product_id)
                        ->where('branch', $branch)
                        ->orderBy('transaction_id')
                        ->whereBetween('created_at', [$request->start_date, $request->end_date])
                        ->get();
                } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {
                    $stocks = DB::table('buyproducts')
                    ->select(
                        'product_id',
                        'transaction_id',
                        'created_at',
                        'customer_name',
                        'payment_type',
                        'total_amount',
                        'product_name',
                        'quantity',
                        'one_pro_buycost',
                        'mrp',
                        'netrate',
                        'totalamount_wo_discount',
                        'discount_amount',
                        DB::raw('(SELECT SUM(credit_note_amount)
                                  FROM credit_note
                                  WHERE credit_note.transaction_id = buyproducts.transaction_id
                                    AND credit_note.product_id = buyproducts.product_id) as total_credit_note_amount')
                    )
                                            ->where('product_id', $request->product_id)
                        ->where('branch', $branch)
                        ->orderBy('transaction_id')
                        ->whereDate('created_at', $request->start_date)
                        ->get();
                } else {
                    $stocks = DB::table('buyproducts')
                    ->select(
                        'product_id',
                        'transaction_id',
                        'created_at',
                        'customer_name',
                        'payment_type',
                        'total_amount',
                        'product_name',
                        'quantity',
                        'one_pro_buycost',
                        'mrp',
                        'netrate',
                        'totalamount_wo_discount',
                        'discount_amount',
                        DB::raw('(SELECT SUM(credit_note_amount)
                                  FROM credit_note
                                  WHERE credit_note.transaction_id = buyproducts.transaction_id
                                    AND credit_note.product_id = buyproducts.product_id) as total_credit_note_amount')
                    )
                                            ->where('product_id', $request->product_id)
                        ->where('branch', $branch)
                        ->orderBy('transaction_id')
                        ->get();
                }


            // Get the admin_id from the softwareuser
            $adminid = Softwareuser::where('id', $userid)
                ->pluck('admin_id')
                ->first();
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();


        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/stocktranshistory', array('tax'=>$tax,'start_date' => $start_date, 'end_date' => $end_date, 'stocks' => $stocks, 'users' => $useritem, 'product_id' => $request->product_id, 'currency' => $currency));
    }
    public function adminmonthwiseExpenseReport(Request $req)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

        $branch = Branch::all();

        $today = Carbon::now();

        $month = $today->month;
        $year = $today->year;

        // $expenses = DB::table('accountexpenses')
        //     ->leftJoin('branches', 'accountexpenses.branch', '=', 'branches.id')
        //     ->select(DB::raw("
        //     accountexpenses.comment,
        //     accountexpenses.amount,
        //     accountexpenses.date,
        //     accountexpenses.branch,
        //     accountexpenses.user_id,
        //     accountexpenses.file,
        //     accountexpenses.created_at,
        //     branches.branchname as branchname
        //     "),)
        //     ->whereMonth('accountexpenses.date', $month)
        //     ->whereYear('accountexpenses.date', $year)
        //     ->get();
            $expenses = DB::table('accountexpenses')
            ->leftJoin('branches', 'accountexpenses.branch', '=', 'branches.id')
            ->select(DB::raw(
                'accountexpenses.direct_expense,
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
            // ->where('accountexpenses.user_id', $userid)
            ->whereMonth('accountexpenses.date', $month)
            ->whereYear('accountexpenses.date', $year)
            ->get();





        $start_date = "";
        $shopid = Session('adminuser');
        $shopdata = Adminuser::Where('id', $shopid)
            ->get();

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited list month-wise expense report page";

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return view('/admin/adminmonthwiseexpencereport', array('users' => $useritem, 'expenses' => $expenses, 'start_date' => $start_date, 'shopdatas' => $shopdata, 'branch' => $branch, 'currency' => $currency));
    }
    public function adminmonthwiseExpenseReportdate(Request $req)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

        $month = Carbon::parse($req->start_date)->format('m');
        $year = Carbon::parse($req->start_date)->format('Y');

        $branch = Branch::all();

        // $expenses = DB::table('accountexpenses')
        //     ->leftJoin('branches', 'accountexpenses.branch', '=', 'branches.id')
        //     ->select(DB::raw("
        //     accountexpenses.comment,
        //     accountexpenses.amount,
        //     accountexpenses.date,
        //     accountexpenses.branch,
        //     accountexpenses.user_id,
        //     accountexpenses.file,
        //     accountexpenses.created_at,
        //      branches.branchname as branchname
        //      "),)
        //     ->whereMonth('accountexpenses.date', $month)
        //     ->whereYear('accountexpenses.date', $year)
        //     ->where('accountexpenses.branch', $req->branch)
        //     ->get();

            $expenses = DB::table('accountexpenses')
            ->leftJoin('branches', 'accountexpenses.branch', '=', 'branches.id')
            ->select(DB::raw('
            accountexpenses.expense_type,
            accountexpenses.indirect_expense,
            accountexpenses.direct_expense,
            accountexpenses.details,
            accountexpenses.amount,
            accountexpenses.date,
            accountexpenses.branch,
            accountexpenses.user_id,
            accountexpenses.file,
            accountexpenses.created_at,
            branches.branchname as branchname
            '))
            // ->where('accountexpenses.user_id', $userid)
            ->whereMonth('accountexpenses.date', $month)
            ->whereYear('accountexpenses.date', $year)
            ->where('accountexpenses.branch', $req->branch)
            ->get();







        $start_date = $req->start_date;
        $shopid = Session('adminuser');
        $shopdata = Adminuser::Where('id', $shopid)
            ->get();

        return view('/admin/adminmonthwiseexpencereport', array('users' => $useritem, 'expenses' => $expenses, 'start_date' => $start_date, 'shopdatas' => $shopdata, 'branch' => $branch, 'currency' => $currency));
    }
    public function AdminEmployeeSalary()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $item = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $userdata = DB::table('accountantlocs')
            ->Join('softwareusers', 'accountantlocs.location_id', '=', 'softwareusers.location')
            ->select(DB::raw("softwareusers.name,softwareusers.joined_date,softwareusers.id"),)
            ->distinct('softwareusers.id')
            ->get();
        $shopdata = Adminuser::Where('id', $userid)->get();

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited salary report page";

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/
        return view('/admin/adminemployeesalaryreport', array('users' => $item, 'userdatas' => $userdata, 'shopdatas' => $shopdata));
    }
    public function AdminEmployeeSalarydat($user_id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $item = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

        $salarydata = Salarydata::where('user_id', '=', $user_id)->get();
        $shopdata = Adminuser::Where('id', $userid)
            ->get();
        return view('/admin/adminemployeesalaryreportdat', array('users' => $item, 'user_id' => $user_id, 'salarydatas' => $salarydata, 'shopdatas' => $shopdata, 'currency' => $currency));
    }

    function admin_supplierCreditTransactionHistory($id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $item = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', Session('adminuser'))
            ->get();

        $purchasedata = DB::table('credit_supplier_transactions')
            ->select(
                'credit_supplier_transactions.*',
                DB::raw("(SELECT created_at FROM stockdetails WHERE reciept_no COLLATE utf8mb4_general_ci = credit_supplier_transactions.reciept_no LIMIT 1) as receipt_date")
            )
            ->where('credit_supplier_id', $id)
            ->paginate(10);

        $userid = Session('adminuser');
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

        $lastTransaction_for_due = DB::table('credit_supplier_transactions')
            ->where('credit_supplier_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();

        $finaldue = $lastTransaction_for_due->updated_balance ?? 0;

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();
        $supplier = Supplier::where('id', $id)->pluck('name')->first();

        $user_type = 'webadmin';
        $message = $username . " visited credit supplier " . $supplier . "'s credit transaction page";

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }
        /*-----------------------------------------------------------------------*/
        return view('/billingdesk/suppliercreditdata', array('users' => $item, 'purchasedata' => $purchasedata, 'finaldue' => $finaldue, 'currency' => $currency, 'credit_supplier_id' => $id));
    }

    public function export_supplier_stock_purchase($supplier, $payment_mode = null)
    {
        $suppliername = Supplier::where('id', $supplier)->pluck('name')->first();
        $payment_mode = $payment_mode ?? 0;

        return Excel::download(new supplierStockPurchaseExport($supplier, $payment_mode), $suppliername . '- Stock Purchase Report.xlsx');
    }
    public function adminmonthwiseIncomeReport(Request $req)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

        $branch = Branch::all();

        $today = Carbon::now();

        $month = $today->month;
        $year = $today->year;

        // $income = DB::table('account_indirect_incomes')
        //     ->leftJoin('branches', 'account_indirect_incomes.branch', '=', 'branches.id')
        //     ->select(DB::raw("account_indirect_incomes.comment,account_indirect_incomes.amount,account_indirect_incomes.date,account_indirect_incomes.branch,account_indirect_incomes.user_id,account_indirect_incomes.file,account_indirect_incomes.created_at, branches.branchname as branchname"),)
        //     ->whereMonth('account_indirect_incomes.date', $month)
        //     ->whereYear('account_indirect_incomes.date', $year)
        //     ->get();

        $incomes = DB::table('account_indirect_incomes')
        ->leftJoin('branches', 'account_indirect_incomes.branch', '=', 'branches.id')
        ->select(
            'account_indirect_incomes.indirect_income',
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
        // ->where('account_indirect_incomes.user_id', $userid)
        ->whereMonth('account_indirect_incomes.date', $month)
        ->whereYear('account_indirect_incomes.date', $year)
        ->get();

        $start_date = "";
        $shopid = Session('adminuser');
        $shopdata = Adminuser::Where('id', $shopid)
            ->get();

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited month-wise income report page";

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return view('/admin/adminmonthwiseincomereport', array('users' => $useritem, 'income' => $incomes, 'start_date' => $start_date, 'shopdatas' => $shopdata, 'branch' => $branch, 'currency' => $currency));
    }
    public function adminmonthwiseIncomeReportdate(Request $req)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

        $month = Carbon::parse($req->start_date)->format('m');
        $year = Carbon::parse($req->start_date)->format('Y');

        $branch = Branch::all();

        // $income = DB::table('account_indirect_incomes')
        //     ->leftJoin('branches', 'account_indirect_incomes.branch', '=', 'branches.id')
        //     ->select(DB::raw("account_indirect_incomes.comment,account_indirect_incomes.amount,account_indirect_incomes.date,account_indirect_incomes.branch,account_indirect_incomes.user_id,account_indirect_incomes.file,account_indirect_incomes.created_at, branches.branchname as branchname"),)
        //     ->whereMonth('account_indirect_incomes.date', $month)
        //     ->whereYear('account_indirect_incomes.date', $year)
        //     ->where('account_indirect_incomes.branch', $req->branch)
        //     ->get();

        $incomes = DB::table('account_indirect_incomes')
        ->leftJoin('branches', 'account_indirect_incomes.branch', '=', 'branches.id')
        ->select(
            'account_indirect_incomes.indirect_income',
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
        // ->where('account_indirect_incomes.user_id', $userid)
        ->whereMonth('account_indirect_incomes.date', $month)
        ->whereYear('account_indirect_incomes.date', $year)
        ->get();

        $start_date = $req->start_date;

        $shopdata = Adminuser::Where('id', $userid)->get();

        return view('/admin/adminmonthwiseincomereport', array('users' => $useritem, 'income' => $incomes, 'start_date' => $start_date, 'shopdatas' => $shopdata, 'branch' => $branch, 'currency' => $currency));
    }

    public function p_and_l_report()
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $item = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $date = Carbon::today()->format('Y-m-d');

        /*--------------------------------------------------------------------------------------------------*/

        $remaining_stock_closing_yester = DB::table('pand_l_s')
            ->orderBy('created_at', 'desc')
            ->pluck('closingstock')
            ->first();

        /*---------------------Opening Stock--------------------------------*/
        $today_date = Carbon::today()->format('Y-m-d');

        $total_stock_opening = DB::table('pand_l_s')
            ->whereDate('created_at', $today_date)
            ->pluck('openingstock')
            ->first();

        /*-------------------------------------------------------------*/

        /*-----------------------Closing Stock-------------------------*/

        $remaining_stock_closing = DB::table('pand_l_s')
            ->whereDate('created_at', $today_date)
            ->pluck('closingstock')
            ->first();

        /*------------------------------------------------------------*/

        /*---------------------------Sold Stock ---------------------*/

        $stocks = DB::table('products')
            ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
            ->select(DB::raw("products.selling_cost as selling_cost, products.vat as vat, SUM(stockdats.stock_num) as stock_num"),)
            ->whereDate('stockdats.created_at', $date)
            ->groupBy('products.id')
            ->orderBy('products.id')
            ->get();

        $sold = 0;
        foreach ($stocks as $stock) {
            //   $sold = $sold +  (($stock->stock_num) * ($stock->selling_cost));
            $sold = $sold + (($stock->stock_num) * ($stock->selling_cost)) + ((($stock->vat) * (($stock->stock_num) * ($stock->selling_cost))) / 100);
        }

        /*-----------------------------------------------------------*/

        /*-----------------------------------------------------------*/

        $purchase = DB::table('stockdetails')
            ->select(DB::raw("stockdetails.quantity as quantity, stockdetails.price as price, stockdetails.remain_stock_quantity as remain_quan"))
            ->whereDate('stockdetails.created_at', $date)
            ->get();

        $purchaseamount = 0;

        foreach ($purchase as $purchase) {
            $one_price = ($purchase->price) / ($purchase->quantity);

            $purchaseamount = $purchaseamount + (($purchase->remain_quan) * $one_price);
        }

        /*-----------------------------------------------------------*/
        /*---------------------------Indirect Expense & income ---------------------*/

        $monthlyexpense = DB::table('accountexpenses')
            ->select(DB::raw("SUM(amount) as monthly_expense"))
            ->whereDate('date', $date)
            ->first();

        $salary_amount = DB::table('salarydatas')
            ->select(DB::raw("SUM(salary) as salary"))
            ->whereDate('date', $date)
            ->first();

        $indirect_expenses = ($monthlyexpense->monthly_expense) + ($salary_amount->salary);

        $indirect_income = DB::table('account_indirect_incomes')
            ->select(DB::raw("SUM(amount) as indirect_income"))
            ->whereDate('date', $date)
            ->first();
        /*-------------------------------------------------------------------------*/
        /*----------------------- Profit & Loss -----------------------------------*/

        $loss = 0;
        $profit = 0;
        $value_purchase = ($purchaseamount + $indirect_expenses);
        $value_sales = ($sold + ($indirect_income->indirect_income));

        $loss_value = $value_purchase - $value_sales;
        $profit_value = $value_sales - $value_purchase;

        if ($value_purchase > $value_sales) {
            $loss = $loss_value;
        } else {
            $profit = $profit_value;
        }

        /*-------------------------------------------------------------------------*/

        $shopdata = Adminuser::Where('id', $userid)->get();

        $start_date = "";
        $end_date = "";

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited P & L report";

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return view('/admin/p&l_report', array('users' => $item, 'shopdatas' => $shopdata, 'opening_stock' => (int) $total_stock_opening, 'closing_stock' => (int) $remaining_stock_closing, 'soldstock_value' => $sold, 'purchase_amount' => $purchaseamount, 'start_date' => $start_date, 'end_date' => $end_date, 'indirect_expenses' => $indirect_expenses, 'indirect_income' => $indirect_income, 'loss' => $loss, 'profit' => $profit, 'close_st' => $remaining_stock_closing_yester));
    }
    public function filter_p_and_l(Request $request)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }
        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        /*---------------------latest Opening Stock & Closing Stock--------------------------------*/

        $latest_stock_details = DB::table('stockdetails')
            ->whereDate('created_at', '<', $request->start_date)
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->first();

        $stock_details_created_date = date('Y-m-d', strtotime($latest_stock_details));

        $latest_add_stock = DB::table('addstocks')
            ->whereDate('created_at', '<', $request->start_date)
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->first();

        $add_stock_created_date = date('Y-m-d', strtotime($latest_add_stock));

        $latest_stockdats = DB::table('stockdats')
            ->whereDate('created_at', '<', $request->start_date)
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->first();

        $stockdats_created_date = date('Y-m-d', strtotime($latest_stockdats));

        /*query starts */

        $stock_purchase_yester = DB::table('stockdetails')
            ->select(DB::raw("SUM(remain_stock_quantity) as stock_purchase"))
            ->whereDate('created_at', '<=', $stock_details_created_date)
            ->first();

        $stock_add_yester = DB::table('addstocks')
            ->select((DB::raw("SUM(quantity) as stock_add")))
            ->whereDate('created_at', '<=', $add_stock_created_date)
            ->first();

        $total_stock_opening_yester =  ($stock_purchase_yester->stock_purchase) + ($stock_add_yester->stock_add);

        $soldstock_yester = DB::table('stockdats')
            ->select((DB::raw("SUM(stock_num) as soldstock")))
            ->whereDate('created_at', '<=', $stockdats_created_date)
            ->first();

        $remaining_stock_closing_yester = $total_stock_opening_yester - $soldstock_yester->soldstock; //30 march

        /*--------------------------------------------------------------------------------------------------*/

        /*---------------------Opening Stock--------------------------------*/ //1 april to 30 april

        $stock_purchase = DB::table('stockdetails')
            ->select((DB::raw("SUM(remain_stock_quantity) as stock_purchase")))
            ->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
            ->first();

        $stock_add = DB::table('addstocks')
            ->select((DB::raw("SUM(quantity) as stock_add")))
            ->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
            ->first();

        $total_stock_opening =  $remaining_stock_closing_yester + (($stock_purchase->stock_purchase) + ($stock_add->stock_add));

        /*-------------------------------------------------------------*/
        /*-----------------------Closing Stock-------------------------*/

        $soldstock = DB::table('stockdats')
            ->select((DB::raw("SUM(stock_num) as soldstock")))
            ->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
            ->first();

        $remaining_stock_closing = $total_stock_opening - $soldstock->soldstock;

        /*------------------------------------------------------------*/

        /*---------------------------Sold Stock ---------------------*/

        $stocks = DB::table('products')
            ->leftJoin('stockdats', 'products.id', '=', 'stockdats.product_id')
            ->select(DB::raw("products.selling_cost as selling_cost, products.vat as vat, SUM(stockdats.stock_num) as stock_num"),)
            ->whereBetween('stockdats.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
            ->groupBy('products.id')
            ->orderBy('products.id')
            ->get();


        $sold = 0;
        foreach ($stocks as $stock) {
            // $sold = $sold +  (($stock->stock_num) * ($stock->selling_cost));
            $sold = $sold + (($stock->stock_num) * ($stock->selling_cost)) + ((($stock->vat) * (($stock->stock_num) * ($stock->selling_cost))) / 100);
        }

        /*-----------------------------------------------------------*/
        /*-----------------------------------------------------------*/

        $purchase = DB::table('stockdetails')
            ->select(DB::raw("stockdetails.quantity as quantity, stockdetails.price as price, stockdetails.remain_stock_quantity as remain_quan"))
            ->whereBetween('stockdetails.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
            ->get();

        $purchaseamount = 0;

        foreach ($purchase as $purchase) {
            $one_price = ($purchase->price) / ($purchase->quantity);

            $purchaseamount = $purchaseamount + (($purchase->remain_quan) * $one_price);
        }

        /*--------------------------------------------------------------------------*/
        /*---------------------------Indirect Expense & income ---------------------*/

        $monthlyexpense = DB::table('accountexpenses')
            ->select(DB::raw("SUM(amount) as monthly_expense"))
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->first();

        $salary_amount = DB::table('salarydatas')
            ->select(DB::raw("SUM(salary) as salary"))
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->first();

        $indirect_expenses = ($monthlyexpense->monthly_expense) + ($salary_amount->salary);

        $indirect_income = DB::table('account_indirect_incomes')
            ->select(DB::raw("SUM(amount) as indirect_income"))
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->first();

        /*-------------------------------------------------------------------------*/

        /*----------------------- Profit & Loss -----------------------------------*/

        $loss = 0;
        $profit = 0;
        $value_purchase = ($purchaseamount + $indirect_expenses);
        $value_sales = ($sold + ($indirect_income->indirect_income));

        $loss_value = $value_purchase - $value_sales;

        $profit_value = $value_sales - $value_purchase;

        if ($value_purchase > $value_sales) {
            $loss = $loss_value;
        } else {
            $profit = $profit_value;
        }

        //  dd($profit);
        /*-------------------------------------------------------------------------*/

        $shopdata = Adminuser::Where('id', $userid)->get();

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return view('/admin/p&l_report', array('users' => $useritem, 'shopdatas' => $shopdata, 'opening_stock' => $total_stock_opening, 'closing_stock' => $remaining_stock_closing, 'soldstock_value' => $sold, 'purchase_amount' => $purchaseamount, 'start_date' => $start_date, 'end_date' => $end_date, 'indirect_expenses' => $indirect_expenses, 'indirect_income' => $indirect_income, 'loss' => $loss, 'profit' => $profit, 'close_st' => $remaining_stock_closing_yester));
    }

    public function export_p_and_l($start_date, $end_date, $branch)
    {
        $branch_name = Branch::where('id', $branch)->pluck('location')->first();

        return Excel::download(new PandLReportExport($start_date, $end_date, $branch), 'P_and_L_Report - ' . $branch_name . '.xlsx');
    }
    public function enableCredit($id)
    {
        $userid = Session('adminuser');

        $username = Adminuser::where('id', $userid)->pluck('username')->first();
        $usr = Credituser::where('id', $id)->pluck('name')->first();

        $plan = Credituser::find($id);
        if ($plan->status == '0') {
            $status = '1';
        } else {
            $status = '0';
        }
        $plan->status = $status;
        $plan->save();

        $message = $username . " enabled customer named " . $usr;
        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/
        return redirect('/listcredit');
    }

    public function store_old_stock($close_stock)
    {
        $newrow = new PandL();
        $newrow->openingstock = $close_stock;
        $newrow->closingstock = $close_stock;
        $newrow->save();

        return response()->json(['closingstock' => $close_stock]);
    }

public function new_my_p_and_l_report(Request $request)
    {
        if (Session::has('softwareuser')) {
        } elseif (Session::has('adminuser')) {
        } else {
            return redirect('userlogin'); // or 'adminlogin' based on your logic
        }
        $adminid = null;

        if (Session::has('adminuser')) {
            $adminid = Session::get('adminuser');
            $useritem = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();
                $pAndLService = new PAndLService();

                $pAndLData = $pAndLService->normalPandL();
                $branchId='';
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
                $pAndLService = new PAndLService();

                $pAndLData = $pAndLService->normalPandLbranchwise();

                $branchId = Softwareuser::where('id', $userid)->pluck('location')->first(); // Assuming branch_id is the column in softwareusers table
        $shopdata = Branch::Where('id', $branchId)->get();

        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        // $pAndLService = new PAndLService();

        // Call the method to get the data
        // $pAndLData = $pAndLService->normalPandL();


        $start_date = "";
        $end_date = "";
        $location = DB::table('branches')
        ->get();
        $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();
    // $first_location = DB::table('branches')->pluck('id')->first();

    $date = Carbon::today()->format('Y-m-d');

    $direct_expense = DB::table('accountexpenses')
    ->select(DB::raw('direct_expense,SUM(amount) as amount'))
    ->whereDate('date', $date)
    ->where('id', $branch)
    ->where('expense_type', 1)
    ->groupBy('direct_expense')
    ->get();


    $indirect_expense = DB::table('accountexpenses')
    ->select(DB::raw('indirect_expense,SUM(amount) as amount'))
    ->whereDate('date', $date)
    ->where('id', $branch)
    ->where('expense_type',2)
    ->groupBy('indirect_expense')
    ->get();

    $direct_income = DB::table('account_indirect_incomes')
    ->select(DB::raw('direct_income,SUM(amount) as amount'))
    ->whereDate('date', $date)
    ->where('id', $branch)
    ->where('income_type',1)
    ->groupBy('direct_income')
    ->get();

    $indirect_incomes = DB::table('account_indirect_incomes')
    ->select(DB::raw('indirect_income,SUM(amount) as amount'))
    ->whereDate('date', $date)
    ->where('id', $branch)
    ->where('income_type',2)
    ->groupBy('indirect_income')
    ->get();



    // Calculate total amounts
    $total_direct_expense = $direct_expense->sum('amount');
    $total_direct_income = $direct_income->sum('amount');
    $total_indirect_expense = $indirect_expense->sum('amount');
    $total_indirect_income = $indirect_incomes->sum('amount');





        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $adminid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited P & L report";

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService(
                $adminid,
                $ip,
                $uri,
                $message,
                $user_type,
                $locationdata
            ))->ipaddress_store(0);
        }



      return view('/admin/p&l_report', array_merge(
    [
        'users' => $useritem,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'currency' => $currency,
        'direct_expense' => $direct_expense,
        'indirect_expense' => $indirect_expense,
        'direct_income' => $direct_income,
        'indirect_incomes' => $indirect_incomes,
        'total_direct_expense' => $total_direct_expense,
        'total_direct_income' => $total_direct_income,
        'total_indirect_expense' => $total_indirect_expense,
        'total_indirect_income' => $total_indirect_income,
        'branchId' => $branchId,
    ],
    session('softwareuser') ? ['shopdatas' => $shopdata] : [],
    $pAndLData
));

    }
    public function optimized_new_filter_pandl(Request $request)
    {
        // Check session existence and redirect if none found
        if (Session::has('softwareuser')) {
            $userid = Session::get('softwareuser');
        } elseif (Session::has('adminuser')) {
            $adminid = Session::get('adminuser');
        } else {
            return redirect('userlogin'); // Adjust as per your application logic
        }

        // Validate input based on session type and ensure dates are validated
        // $rules = ['start_date' => 'required|date', 'end_date' => 'required|date'];
        if (Session::has('softwareuser')) {
            $rules['branches'] = 'required'; // Validate `branches` for softwareuser
        } elseif (Session::has('adminuser')) {
            $rules['branch'] = 'required'; // Validate `branch` for adminuser
        }
        $request->validate($rules);

        $filterpAndLService = new PAndLService();
        $useritem = null;
        $filterpAndLData = [];
        $branchId = null;

        // If admin user session
        if (Session::has('adminuser')) {
            // Fetch admin user's data
            $useritem = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', $adminid)
                ->get();

            // Fetch data using service class
            $filterpAndLData = $filterpAndLService->filterPandL(
                $request->input('branch'),        // Explicitly retrieve the branch
                $request->input('start_date'),   // Explicitly retrieve start_date
                $request->input('end_date')      // Explicitly retrieve end_date
            );
        } elseif (Session::has('softwareuser')) {
            // If software user session
            $useritem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();

            // Get admin_id linked to software user
            $adminid = Softwareuser::where('id', $userid)
                ->pluck('admin_id')
                ->first();

            $branchId = Softwareuser::where('id', $userid)
                ->pluck('location')
                ->first(); // Adjust "location" to your actual column name

            // Fetch data using service class
            $filterpAndLData = $filterpAndLService->filterPandLuser(
                $request->input('branches'),    // Explicitly retrieve branches
                $request->input('start_date'), // Explicitly retrieve start_date
                $request->input('end_date')    // Explicitly retrieve end_date
            );
        }

        // Ensure admin ID is valid before proceeding
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        // Get currency for the admin
        $currency = Adminuser::where('id', $adminid)
            ->pluck('currency')
            ->first();

        // Get admin shop data
        $shopdata = Adminuser::where('id', $adminid)->get();
        $selectedLocation = $request->input('location');

        // Return view with all necessary data
        return view('/admin/p&l_report', array_merge(
            [
                'users' => $useritem,
                'shopdatas' => $shopdata,
                'selectedLocation' => $selectedLocation,
                'currency' => $currency,
                'branchId' => $branchId,
            ],
            $filterpAndLData
        ));
    }
    /*------------------------------------------------------------------------*/

    //admin daily report

    public function admin_daily_report($id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $daily_datas = UserReport::select(DB::raw("created_at, trans_id"))
            ->where('user_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get();

        $start_date = "";
        $end_date = "";

        /*------------------GET IP ADDRESS---------------------------------------*/

        $ip = request()->ip();
        $uri = request()->fullUrl();

        $username = Adminuser::where('id', $userid)->pluck('username')->first();

        $user_type = 'webadmin';
        $message = $username . " visited daily user report page";

        $locationdata = (new otherService())->get_location($ip);

        if ($locationdata != false) {
            $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
        }

        /*-----------------------------------------------------------------------*/

        return view('/admin/daily_reports', array('users' => $useritem, 'uid' => $id, 'daily_datas' => $daily_datas, 'start_date' => $start_date, 'end_date' => $end_date));
    }

    //admin daily report print

   public function admin_daily_report_print($user_id, $user_report_transid,)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $user_id)
            ->get();

        $userdatas = Softwareuser::Where('id', $user_id)
            ->get();

        $opening_balance = DB::table('user_reports')
            ->where('user_id', $user_id)
            ->where('trans_id', $user_report_transid)
            ->pluck('opening_balance')
            ->first() ?? 0.000;

        $total_sales_amount = DB::table('user_reports')
            ->where('user_id', $user_id)
            ->where('trans_id', $user_report_transid)
            ->pluck('total_sales_amount')
            ->first() ?? 0.000;

        $posBankSale = DB::table('user_reports')
            ->where('user_id', $user_id)
            ->where('trans_id', $user_report_transid)
            ->pluck('posBankSale')
            ->first() ?? 0.000;

        $creditPayment = DB::table('user_reports')
            ->where('user_id', $user_id)
            ->where('trans_id', $user_report_transid)
            ->pluck('creditPayment')
            ->first() ?? 0.000;

        $creditSale = DB::table('user_reports')
            ->where('user_id', $user_id)
            ->where('trans_id', $user_report_transid)
            ->pluck('creditSale')
            ->first() ?? 0.000;

            $expense = DB::table('user_reports')
            ->where('user_id', $user_id)
            ->where('trans_id', $user_report_transid)
            ->pluck('expense')
            ->first() ?? 0.000;

            $income = DB::table('user_reports')
            ->where('user_id', $user_id)
            ->where('trans_id', $user_report_transid)
            ->pluck('income')
            ->first() ?? 0.000;
            
            $service = DB::table('user_reports')
            ->where('user_id', $user_id)
            ->where('trans_id', $user_report_transid)
            ->pluck('service')
            ->first() ?? 0.000;

        $total_amount = DB::table('user_reports')
            ->where('user_id', $user_id)
            ->where('trans_id', $user_report_transid)
            ->pluck('total_amount')
            ->first();

        $cash_details = DB::table('cash_notes')
            ->select(DB::raw("notes, quantity, note_type_total"))
            ->where('user_id', $user_id)
            ->where('trans_id', $user_report_transid)
            // ->where('branch', $branch)
            ->get();



        $data = [
            'users' => $useritem,
            'uid' => $user_id,
            'userdatas' => $userdatas,
            'cash_details' => $cash_details,
            'opening_balance' => $opening_balance,
            'posBankSale' => $posBankSale,
            'creditPayment' => $creditPayment,
            'creditSale' => $creditSale,
            'total_amount' => $total_amount,
            'tot_sales' => $total_sales_amount,
            'tot_cash_balance' => $total_amount,
            'income' => $income,
            'expense'=>$expense,
            'service'=>$service
        ];

        $pdf = PDF::loadView('/admin/admin_daily_report_print', $data);

        return $pdf->stream("UserReport_$user_report_transid.pdf");
    }

    //filter admin daily report
    public function filterDailyReport(Request $request, $uid)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        if ($request->start_date != $request->end_date) {

            $daily_datas = UserReport::select(DB::raw("created_at, trans_id"))
                ->whereBetween('user_reports.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->where('user_id', $uid)
                ->orderBy('created_at', 'DESC')
                ->get();
        } elseif ($request->start_date == $request->end_date && $request->start_date != "" && $request->end_date != "") {

            $daily_datas = UserReport::select(DB::raw("created_at, trans_id"))
                ->whereDate('user_reports.created_at', $request->start_date)
                ->where('user_id', $uid)
                ->orderBy('created_at', 'DESC')
                ->get();
        } else {
            $daily_datas = UserReport::select(DB::raw("created_at, trans_id"))
                ->where('user_id', $uid)
                ->orderBy('created_at', 'DESC')
                ->get();
        }

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('/admin/daily_reports', array('users' => $useritem, 'uid' => $uid, 'daily_datas' => $daily_datas, 'start_date' => $start_date, 'end_date' => $end_date));
    }

    public function branchPurchaseProducts($receipt_no)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $item = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->select(DB::raw('stockdetails.id, stockdetails.quantity, stockdetails.unit, stockdetails.buycost,stockdetails.price, products.product_name, stockdetails.created_at, stockdetails.price_without_vat, stockdetails.vat_amount'))
            ->where('reciept_no', $receipt_no)
            ->get();

        $id = DB::table('stockdetails')
            ->select('branch')
            ->where('reciept_no', $receipt_no)
            ->pluck('branch')
            ->first();

        $branchname = DB::table('branches')
            ->select('location')
            ->where('id', $id)
            ->pluck('location')
            ->first();

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();

        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();

        $branchid = $id;
        $receipt_no=$receipt_no;

        return view('/admin/branch_purchase_products', array('receipt_no'=>$receipt_no,'tax'=>$tax,'branchname' => $branchname, 'item' => $item, 'branchid' => $branchid, 'users' => $useritem, 'currency' => $currency));
    }
    public function userPurchaseProducts($uid, $receipt_no)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $item = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->select(DB::raw('stockdetails.id, stockdetails.quantity, stockdetails.unit, stockdetails.buycost,stockdetails.price, products.product_name, stockdetails.created_at, stockdetails.price_without_vat, stockdetails.vat_amount'))
            ->where('stockdetails.reciept_no', $receipt_no)
            ->where('stockdetails.user_id', $uid)
            ->get();

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        return view('/admin/user_purchase_products', array('tax'=>$tax,'item' => $item, 'users' => $useritem, 'uid' => $uid, 'currency' => $currency));
    }
    public function branchPurchaseWiseBills($purchase_id, $product_id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');

        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();

        $branch_id = DB::table('stock_purchase_reports')
            ->select('branch_id')
            ->where('purchase_id', $purchase_id)
            ->pluck('branch_id')
            ->first();

        $receiptno = DB::table('stock_purchase_reports')
            ->select('receipt_no')
            ->where('purchase_id', $purchase_id)
            ->pluck('receipt_no')
            ->first();

        $bills = DB::table('bill_histories')
            ->leftJoin('products', 'bill_histories.product_id', '=', 'products.id')
            ->select(DB::raw("bill_histories.trans_id, bill_histories.remain_sold_quantity, bill_histories.billing_Sellingcost, bill_histories.created_at,products.product_name, bill_histories.netrate,bill_histories.discount_amount, bill_histories.return_discount_amount"))
            ->where('pid', $purchase_id)
            ->where('product_id', $product_id)
            ->where('branch_id', $branch_id)
            ->get();

        return view('/admin/branch_purchase_wise_bills', array('tax'=>$tax,'bills' => $bills, 'users' => $useritem, 'branchid' => $branch_id, 'receiptno' => $receiptno, 'currency' => $currency));
    }

    public function purchaseWiseBills($purchase_id, $product_id)
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
                $receiptno = DB::table('stock_purchase_reports')
                ->select('receipt_no')
                ->where('purchase_id', $purchase_id)
                ->pluck('receipt_no')
                ->first();

                $bills = DB::table('bill_histories')
                ->leftJoin('products', 'bill_histories.product_id', '=', 'products.id')
                ->select(DB::raw("bill_histories.trans_id,
                 bill_histories.remain_sold_quantity,
                  bill_histories.billing_Sellingcost,
                   bill_histories.created_at,
                   products.product_name,
                   bill_histories.netrate,
                   bill_histories.discount_amount,
                   bill_histories.return_discount_amount
                   "))
                ->where('pid', $purchase_id)
                ->where('product_id', $product_id)
                ->get();
        } elseif (Session::has('softwareuser')) {
            $userid = Session::get('softwareuser');
            $useritem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();

                $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

                $receiptno = DB::table('stock_purchase_reports')
                ->select('receipt_no')
                ->where('purchase_id', $purchase_id)
                ->where('branch_id', $branch)
                ->pluck('receipt_no')
                ->first();

                $bills = DB::table('bill_histories')
                ->leftJoin('products', 'bill_histories.product_id', '=', 'products.id')
                ->select(DB::raw("bill_histories.trans_id,
                 bill_histories.remain_sold_quantity,
                  bill_histories.billing_Sellingcost,
                   bill_histories.created_at,
                   products.product_name,
                   bill_histories.netrate,
                   bill_histories.discount_amount,
                   bill_histories.return_discount_amount
                   "))
                ->where('pid', $purchase_id)
                ->where('product_id', $product_id)
                ->where('branch_id', $branch)
                ->get();

            // Get the admin_id from the softwareuser
            $adminid = Softwareuser::where('id', $userid)
                ->pluck('admin_id')
                ->first();
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();






        return view('/admin/purchase_wise_bills', array('bills' => $bills, 'users' => $useritem, 'receiptno' => $receiptno, 'currency' => $currency));
    }

    public function filterpayment_mode($supplier_id, Request $request)
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
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }


        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::where('id', $adminid)->pluck('tax')->first();

        $purchasedata = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.payment_mode as payment_mode'))
            ->where('stockdetails.supplier_id', $supplier_id)
            ->when($request->payment_mode != 0, function ($query) use ($request) {
                return $query->where('stockdetails.payment_mode', $request->payment_mode);
            })
            ->groupBy('stockdetails.reciept_no')
            ->orderBy('stockdetails.created_at', 'DESC')
            ->get();

        $totalPrice = $purchasedata->sum('price');

        $selected_payment_mode = $request->payment_mode;

        $shopdata = Adminuser::Where('id', $adminid)->get();

        return view('/admin/supplier_sales_report', array('tax'=>$tax,'purchasedata' => $purchasedata, 'users' => $useritem, 'shopdatas' => $shopdata, 'supplier' => $supplier_id, 'currency' => $currency, 'totalPrice' => $totalPrice, 'selected_payment_mode' => $selected_payment_mode));
    }

    public function viewProductsInSupplierPurchase($receipt_no)
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
        }

        // Check if $adminid is set before querying Adminuser
        if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
        $shopdata = Adminuser::Where('id', $adminid)->get();

        $view_products = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->select(DB::raw('stockdetails.id, stockdetails.quantity, stockdetails.unit, stockdetails.buycost,stockdetails.price, products.product_name, stockdetails.created_at, stockdetails.price_without_vat, stockdetails.vat'))
            ->where('reciept_no', $receipt_no)
            ->get();

        return view('/admin/supplier_purchase_products', array('tax'=>$tax,'users' => $useritem, 'shopdatas' => $shopdata, 'currency' => $currency, 'view_products' => $view_products));
    }

    public function viewUserReturns($transaction_id, $created_at, $user_id)
    {
        if (session()->missing('adminuser')) {
            return redirect('adminlogin');
        }

        $userid = Session('adminuser');
        $useritem = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $currency = Adminuser::Where('id', $userid)
            ->pluck('currency')
            ->first();
        $tax = Adminuser::Where('id', $userid)
        ->pluck('tax')
        ->first();
        $returnproduct = DB::table('returnproducts')
            ->leftJoin('products', 'returnproducts.product_id', '=', 'products.id')
            ->select(DB::raw('returnproducts.*'))
            ->where('returnproducts.transaction_id', $transaction_id)
            ->where('returnproducts.created_at', $created_at)
            ->where('returnproducts.user_id', $user_id)
            ->get();

        $shopdata = Adminuser::Where('id', $userid)
            ->get();

        return view('/admin/returnreport_product', array('tax'=>$tax,'details' => $returnproduct, 'users' => $useritem, 'currency' => $currency, 'id' => $user_id));
    }


}
