<?php

namespace App\Http\Controllers;

use App\Exports\ActivityExportReport;
use App\Models\Adminuser;
use App\Models\Branch;
use App\Models\Buyproduct;
use App\Models\Credituser;
use App\Models\Module_role;
use App\Models\Returnproduct;
use App\Models\Softwareuser;
use App\Models\Superuser;
use App\Services\ActivityWorksService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class SuperuserController extends Controller
{
    public function dashBoard()
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }
        $now = [Carbon::now()->format('Y-m-d')];
        $year = [];
        $i = 7;
        while ($i > -1) {
            $today = Carbon::today();
            array_push($year, $today->subDays($i)->format('Y-m-d'));
            --$i;
        }
        $dnow = [Carbon::now()->format('Y-m-d')];
        $dyear = [];
        $di = 7;
        while ($di > -1) {
            $dtoday = Carbon::today();
            array_push($dyear, $dtoday->subDays($di)->format('d l'));
            --$di;
        }
        $user = [];
        foreach ($year as $key => $value) {
            $user[] = Buyproduct::where(DB::raw('DATE(created_at)'), $value)->count();
        }
        $return = [];
        foreach ($year as $key => $value) {
            $returned[] = Returnproduct::where(DB::raw('DATE(created_at)'), $value)->count();
        }

        return view('/superuser/superuserdashboard')->with('year', json_encode($dyear))->with('user', json_encode($user, JSON_NUMERIC_CHECK))->with('returned', json_encode($returned, JSON_NUMERIC_CHECK));
    }

    public function createAdmin()
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }

        return view('/superuser/createadmin');
    }

    public function createAdminform(Request $req)
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }

        // dd($req);
        // $req->validate([
        //     'name' => 'required|min:5',
        //     'username' => 'required|min:5|unique:adminusers,username',
        //     'password' => 'required|min:5',
        //     'phone' => 'required|min:7',
        //     'email' => 'required',
        //     'currency' => 'required',
        // ]);

        $messages = [
            'name.required' => 'Please enter a name.',
            'name.min' => 'The name must be at least 5 characters long.',
            'username.required' => 'Please enter a username.',
            'username.min' => 'The username must be at least 5 characters long.',
            'username.unique' => 'This username is already taken.',
            'password.required' => 'Please enter a password.',
            'password.min' => 'The password must be at least 5 characters long.',
            'phone.required' => 'Please enter a phone number.',
            'phone.min' => 'The phone number must be at least 7 characters long.',
            'email.required' => 'Please enter an email address.',
            'currency.required' => 'Please select a currency.',
            'tax.required' => 'Please select a tax mode.',
            'address.required' => 'Please enter an address.',
            'location.required' => 'Please select a location.',
            'transpart.required' => 'Please enter the transaction id part that you need as default.',
            'logo.image' => 'The logo must be a valid image file.',
            'logo.mimes' => 'The logo must be a file of type: jpeg, png, jpg, gif.',
            'logo.max' => 'The logo may not be greater than 2048 kilobytes.',
        ];

        $req->validate([
            'name' => 'required|min:5',
            'username' => 'required|min:5|unique:adminusers,username',
            'password' => 'required|min:5',
            'phone' => 'required|min:7',
            'email' => 'required',
            'currency' => 'required',
            'tax' => 'required',
            'location' => 'required',
            'address' => 'required',
            'transpart' => 'required',
            'logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $messages);

        $results = Adminuser::get();
        if ($results->isNotEmpty()) {
            return back()->with('failed', 'Admin Already Exists Software is designed one admin per server');
        }

        if (Adminuser::where('username', $req['username'])->exists()) {
            return redirect('/createadmin');
        } else {
            $user = new Adminuser();
            $user->name = $req->input('name');
            $user->username = $req->input('username');
            $user->password = Hash::make($req->input('password'));
            $user->email = $req->input('email');
            $user->phone = $req->input('phone');
            $user->po_box = $req->input('po_box');
            $user->postal_code = $req->input('postal_code');
            $user->cr_number = $req->input('cr_number');
            $user->currency = $req->input('currency');
            $user->tax = $req->input('tax');
            $user->location = $req->input('location');
            $user->address = $req->input('address');
            $user->transpart = $req->input('transpart');
            $user->country = $req->input('country');

            $user->save();

            if ($req->hasFile('logo')) {
                $logo = $req->file('logo');
                $logoName = 'logo.'.$logo->getClientOriginalExtension();
                $logo->move(public_path('storage/logo'), $logoName);

                $user->logo = $logoName;
                $user->save();
            }

            return back()->with('success', 'Admin successfuly created');
        }
    }

    public function listAdmin()
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }
        $item = DB::table('adminusers')
            ->get();
        $roles = DB::table('module_roles')
            ->get();

        return view('/superuser/listadmin', ['users' => $item, 'roles' => $roles]);
    }

    public function addModules(Request $request)
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }
        if (Module_role::where('user_id', $request->user_id)->exists()) {
            DB::table('module_roles')->where('user_id', $request->user_id)->delete();
        }
        foreach ($request->role as $key => $role) {
            $data = new Module_role();
            $data->module_id = $role;
            $data->user_id = $request->user_id;
            $data->save();
        }

        return redirect('listadmin');
    }

    public function getModules($id)
    {
        $roles = DB::table('module_roles')->where('user_id', $id)->pluck('module_id')->toArray();

        return response()->json([
            'roles' => $roles,
        ]);
    }

    public function listAnalytics()
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }
        $users = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_roles.role_id', 3)
            ->get();

        return view('/superuser/listanalytics', ['users' => $users]);
    }

    public function listBilldesks()
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }
        $users = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_roles.role_id', 1)
            ->get();

        return view('/superuser/listbilldesks', ['users' => $users]);
    }

    public function listInventory()
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }
        $users = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_roles.role_id', 2)
            ->get();

        return view('/superuser/listinventory', ['users' => $users]);
    }

    public function listAccountants()
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }
        $users = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_roles.role_id', 9)
            ->get();

        return view('/superuser/listaccountants', ['users' => $users]);
    }

    public function superuserchangePassword()
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }
        $username = Superuser::Where('id', 1)
            ->pluck('username')
            ->first();

        return view('/superuser/superuserchangepassword', ['username' => $username]);
    }

    public function submitsuperuserPassword(Request $req)
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }
        $req->validate([
            'username' => 'required',
            'password' => 'required',
            'confirmpassword' => 'required',
        ]);
        if ($req->password == $req->confirmpassword) {
            $user = Superuser::find(1);
            $user->username = $req->username;
            $user->password = Hash::make($req->password);
            $user->save();

            return back()->with('success', 'Password changed successfully!');
        } else {
            return back()->with('failed', 'Password does not match');
        }
    }

    public function adminEdit($id)
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }
        $username = Adminuser::Where('id', $id)
            ->pluck('username')
            ->first();
        $name = Adminuser::Where('id', $id)
            ->pluck('name')
            ->first();
        $email = Adminuser::Where('id', $id)
            ->pluck('email')
            ->first();
        $phone = Adminuser::Where('id', $id)
            ->pluck('phone')
            ->first();
        $po_box = Adminuser::Where('id', $id)
            ->pluck('po_box')
            ->first();
        $postal_code = Adminuser::Where('id', $id)
            ->pluck('postal_code')
            ->first();
        $cr_number = Adminuser::Where('id', $id)
            ->pluck('cr_number')
            ->first();
        $location = Adminuser::Where('id', $id)
            ->pluck('location')
            ->first();
        $address = Adminuser::Where('id', $id)
            ->pluck('address')
            ->first();

        $transpart = Adminuser::Where('id', $id)
            ->pluck('transpart')
            ->first();
        $country = Adminuser::Where('id', $id)
        ->pluck('country')
        ->first();
        $tax = Adminuser::Where('id', $id)
        ->pluck('tax')
        ->first();

        $uid = $id;

        return view('/superuser/editadmin', ['tax'=>$tax,'country'=>$country,'username' => $username, 'name' => $name, 'email' => $email, 'uid' => $uid, 'po_box' => $po_box, 'cr_number' => $cr_number, 'postal_code' => $postal_code, 'phone' => $phone, 'location' => $location, 'address' => $address, 'transpart' => $transpart]);
    }

    public function adminEditform(Request $req)
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }
        // $req->validate([
        //     'name' => 'required',
        //     'username' => 'required',
        //     'email' => 'required',
        //     'phone' => 'required',
        //     'po_box' => 'required',
        //     'postal_code' => 'required',
        //     'cr_number' => 'required',
        //     // 'location'=>'required',
        // ]);

        $messages = [
            'name.required' => 'Please enter a name.',
            'username.required' => 'Please enter a username.',
            'email.required' => 'Please enter an email address.',
            'phone.required' => 'Please enter a phone number.',
            'tax.required' => 'Please select a tax mode.',

        ];

        $req->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'tax' => 'required',
            'logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'location'=>'required',
        ], $messages);

        if (!empty($req->password && $req->confirmpassword)) {
            if ($req->password == $req->confirmpassword) {
                $user = Adminuser::find($req->id);
                $user->name = $req->name;
                $user->username = $req->username;
                $user->email = $req->email;
                $user->phone = $req->phone;
                $user->po_box = $req->po_box;
                $user->postal_code = $req->postal_code;
                $user->cr_number = $req->cr_number;
                $user->password = Hash::make($req->input('password'));
                $user->location = $req->location;
                $user->address = $req->address;
                $user->transpart = $req->transpart;
                $user->country = $req->country;
                $user->tax = $req->tax;

                $user->save();

                if ($req->hasFile('logo')) {
                    // Store old logo file name
                    $oldLogo = $user->logo;

                    // Store new logo
                    $logo = $req->file('logo');
                    $logoName = 'logo.'.$logo->getClientOriginalExtension();
                    $logo->move(public_path('storage/logo'), $logoName);

                    $user->logo = $logoName;

                    // Save the user with the new logo name
                    $user->save();

                    // Delete old logo if exists
                    if ($oldLogo && file_exists(public_path('storage/logo/'.$oldLogo))) {
                        unlink(public_path('storage/logo/'.$oldLogo));
                    }
                } else {
                    // Save other changes
                    $user->save();
                }
            }

            return back()->with('success', 'Admin Data Edited Successfully!');
        } else {
            $user = Adminuser::find($req->id);
            $user->name = $req->name;
            $user->username = $req->username;
            $user->email = $req->email;
            $user->phone = $req->phone;
            $user->po_box = $req->po_box;
            $user->postal_code = $req->postal_code;
            $user->cr_number = $req->cr_number;
            $user->location = $req->location;
            $user->address = $req->address;
            $user->transpart = $req->transpart;
            $user->tax = $req->tax;
            $user->country = $req->country;

            $user->save();
            if ($req->hasFile('logo')) {
                // Store old logo file name
                $oldLogo = $user->logo;

                // Store new logo
                $logo = $req->file('logo');
                $logoName = 'logo.'.$logo->getClientOriginalExtension();
                $logo->move(public_path('storage/logo'), $logoName);

                $user->logo = $logoName;

                // Save the user with the new logo name
                $user->save();

                // Delete old logo if exists
                if ($oldLogo && file_exists(public_path('storage/logo/'.$oldLogo))) {
                    unlink(public_path('storage/logo/'.$oldLogo));
                }
            } else {
                // Save other changes
                $user->save();
            }

            return back()->with('success', 'Admin Data Edited Successfully!');
        }
    }

    public function adminDelete($id)
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }
        DB::table('branches')->truncate();
        DB::table('accountantlocs')->truncate();
        DB::table('accountexpenses')->truncate();
        DB::table('buyproducts')->truncate();
        DB::table('categories')->truncate();
        DB::table('creditsummaries')->truncate();
        DB::table('creditusers')->truncate();
        DB::table('finalreports')->truncate();
        DB::table('fundhistories')->truncate();
        // DB::table('hruserroles')->truncate();
        DB::table('invoicedatas')->truncate();
        DB::table('invoiceproducts')->truncate();
        DB::table('module_roles')->truncate();
        DB::table('orders')->truncate();
        DB::table('products')->truncate();
        DB::table('returnproducts')->truncate();
        DB::table('returnpurchases')->truncate();
        DB::table('salarydatas')->truncate();
        DB::table('softwareusers')->truncate();
        DB::table('stockdats')->truncate();
        DB::table('stockdetails')->truncate();
        DB::table('stockhistories')->truncate();
        DB::table('termsandconditions')->truncate();
        DB::table('user_roles')->truncate();
        DB::table('adminusers')->truncate();
        DB::table('plexpayusers')->truncate();
        DB::table('account_indirect_incomes')->truncate();
        DB::table('addstocks')->truncate();
        DB::table('suppliers')->truncate();
        DB::table('supplier_fund_histories')->truncate();
        DB::table('supplier_credits')->truncate();
        DB::table('units')->truncate();
        DB::table('pand_l_s')->truncate();
        DB::table('pand_l_amounts')->truncate();
        DB::table('printer_statuses')->truncate();
        DB::table('rawilk_prints')->truncate();
        DB::table('activities')->truncate();
        DB::table('user_reports')->truncate();
        DB::table('cash_notes')->truncate();
        DB::table('stock_purchase_reports')->truncate();
        DB::table('bill_histories')->truncate();
        DB::table('credit_transactions')->truncate();
        DB::table('delivery_notes')->truncate();
        DB::table('purchase_orders')->truncate();
        DB::table('sales_orders')->truncate();
        DB::table('credit_supplier_transactions')->truncate();
        DB::table('performance_invoices')->truncate();
        DB::table('quotations')->truncate();
        DB::table('cash_trans_statements')->truncate();
        DB::table('billdraft')->truncate();
        DB::table('delivery_notes_draft')->truncate();
        DB::table('performance_invoices_draft')->truncate();
        DB::table('productdraft')->truncate();
        DB::table('purchasedraft')->truncate();
        DB::table('quotations_draft')->truncate();
        DB::table('sales_orders_draft')->truncate();
        DB::table('total_expenses')->truncate();
        DB::table('account_type')->truncate();
        DB::table('bank_history')->truncate();
        DB::table('bank')->truncate();
        DB::table('transfer_type')->truncate();
        DB::table('bank_transfer')->truncate();
        DB::table('employee')->truncate();
        DB::table('department_employee')->truncate();
        DB::table('credit_note')->truncate();
        DB::table('debit_note')->truncate();
        DB::table('credit_note_summary')->truncate();
        DB::table('cash_supplier_transactions')->truncate();
        DB::table('new_buyproducts')->truncate();
        DB::table('new_stockdetails')->truncate();
        DB::table('debit_note_summary')->truncate();
        DB::table('chartofaccountants')->truncate();

        return redirect('listadmin');
    }

    public function disableAdmin($id)
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }

        $plan = Adminuser::find($id);
        if ($plan->status == '1') {
            $status = '0';
        } else {
            $status = '1';
        }
        $plan->status = $status;
        $plan->save();

        $softusers = Softwareuser::where('admin_id', $id)
            ->update(['admin_status' => $status]);

        $creditusers = Credituser::where('admin_id', $id)
            ->update(['admin_status' => $status]);

        return redirect('/listadmin');
    }

    public function listActivities()
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }

        $location = DB::table('branches')
            ->get();

        $selectedLocation = null;

        $datas = (new ActivityWorksService())->getActivities($selectedLocation);

        return view('/superuser/listActivities', ['activities' => $datas, 'locations' => $location, 'selectedLocation' => $selectedLocation]);
    }

    public function filter_activities(Request $request)
    {
        if (session()->missing('superuser')) {
            return redirect('superuserlogin');
        }

        $location = DB::table('branches')
            ->get();

        $selectedLocation = $request->input('location');

        $datas = (new ActivityWorksService())->getActivities($selectedLocation);

        return view('/superuser/listActivities', ['activities' => $datas, 'locations' => $location, 'selectedLocation' => $selectedLocation]);
    }

    public function export_activities($branch = null)
    {
        if ($branch) {
            $branch_name = Branch::where('id', $branch)->pluck('location')->first();
        } else {
            $branch_name = 'All branches';
        }

        return Excel::download(new ActivityExportReport($branch), 'Activity_Report - '.$branch_name.'.xlsx');
    }
}
