<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Models\Adminuser;
use App\Models\Credituser;
use App\Models\Superuser;
use App\Models\Softwareuser;
use App\Models\PandL;
use App\Models\PandLAmount;
use App\Services\activityService;

use App\Services\otherService;

use Stevebauman\Location\Facades\Location;

use Carbon\Carbon;

class UserAuth extends Controller
{
    function adminLogin(Request $req)
    {
        $credentials = $req->only('username', 'password');
        if (Auth::guard('webadmin')->attempt(['username' => $req->username, 'password' => $req->password, 'status' => '1'])) {
            // Authentication passed...
            $id = Auth::guard('webadmin')->user()->id;
            $req->session()->put('adminuser', $id);
            return redirect()->intended('admindashboard');
        } else {
            return redirect('/')->with('alert', 'Contact Your Administrator');
        }
    }
    function superuserLogin(Request $req)
    {
        $req->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $credentials = $req->only('username', 'password');
        if (Auth::guard('websuperuser')->attempt($credentials)) {
            // Authentication passed...
            $id = Auth::guard('websuperuser')->user()->id;
            $req->session()->put('superuser', $id);
            return redirect()->intended('superuserdashboard');
        } else if (Auth::guard('webadmin')->attempt(['username' => $req->username, 'password' => $req->password, 'status' => '1'])) {

            // Authentication passed...
            $id = Auth::guard('webadmin')->user()->id;
            $req->session()->put('adminuser', $id);

            /*------------------GET IP ADDRESS---------------------------------------*/

            //get ipaddress
            // $ip = $req->getClientIp();

            $ip = request()->ip();
            $uri = $req->fullUrl();

            $user_type = 'webadmin';
            $message = $req->username . " logged in";
            // $locationdata = Location::get($ip);


            $locationdata = (new otherService())->get_location($ip);


            if ($locationdata != false) {
                $activityservice = (new activityService($id, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
            }
            /*-----------------------------------------------------------------------*/


            return redirect()->intended('admindashboard');
        } else if (Auth::guard('websoftware')->attempt(['username' => $req->username, 'password' => $req->password, 'admin_status' => '1'])) {
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

            //live employees
            $status = DB::table('softwareusers')
                ->where('id', $id)
                ->update(['status' => 1, 'last_login' => Carbon::now()->toDateTimeString(), 'login_ipaddress' => request()->ip()]);

            /*------------     -----------------------------*/

            $date = Carbon::today()->format('Y-m-d');

            $closestock = DB::table('pand_l_s')
                ->orderBy('created_at', 'desc')
                ->pluck('closingstock')
                ->first();

            $rowExists = DB::table('pand_l_s')
                ->whereDate('created_at', '=', $date)
                ->exists();


            if (!$rowExists) {
                // a row with today's date exists in the table

                $newrow = new PandL();
                $newrow->openingstock = $closestock;
                $newrow->closingstock = $closestock;
                $newrow->save();
            }

            // /*---------------- p and l amount ---------------*/

            // $closestock_amount = DB::table('pand_l_amounts')
            //     ->orderBy('created_at', 'desc')
            //     ->pluck('closing_stock_amt')
            //     ->first();

            // $rowExists = DB::table('pand_l_amounts')
            //     ->whereDate('created_at', '=', $date)
            //     ->exists();


            // if (!$rowExists) {
            //     // a row with today's date exists in the table

            //     $newrow = new PandLAmount();
            //     $newrow->opening_stock_amt = $closestock_amount;
            //     $newrow->closing_stock_amt = $closestock_amount;
            //     $newrow->save();
            // }

            // /*-----------------------------------------------*/

            //login info save
            /*------------------GET IP ADDRESS---------------------------------------*/
            //get ipaddress

            $ip = request()->ip();
            $uri = $req->fullUrl();


            $user_type = 'websoftware';
            $message = $req->username . " logged in";
            // $locationdata = Location::get($ip);

            $locationdata = (new otherService())->get_location($ip);

            $branch_id = Softwareuser::where('id', $id)->pluck('location')->first();

            if ($locationdata != false) {
                $activityservice = (new activityService($id, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
            }
            /*-----------------------------------------------------------------------*/

            return redirect()->intended('userdashboard');
        } else if (Auth::guard('webcredituser')->attempt(['username' => $req->username, 'password' => $req->password, 'admin_status' => '1'])) {
            // Authentication passed...
            $id = Auth::guard('webcredituser')->user()->id;
            $req->session()->put('credituser', $id);

            /*------------------GET IP ADDRESS---------------------------------------*/

            //get ipaddress
            // $ip = $req->getClientIp();
            $ip = request()->ip();
            $uri = $req->fullUrl();
            // dd($uri);

            $user_type = 'webcredituser';
            $message = $req->username . " logged in";
            // $locationdata = Location::get($ip);
            $locationdata = (new otherService())->get_location($ip);

            $branch_id = Credituser::where('id', $id)->pluck('location')->first();

            if ($locationdata != false) {
                $activityservice = (new activityService($id, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
            }

            /*-----------------------------------------------------------------------*/

            return redirect()->intended('creditdashboard');
        } else {
            return redirect('/')->with('alert', 'Contact Your Administrator');
        }
    }
    function userLogin(Request $req)
    {
        $credentials = $req->only('username', 'password');
        if (Auth::guard('websoftware')->attempt(['username' => $req->username, 'password' => $req->password, 'admin_status' => '1'])) {
            // Authentication passed...
            $id = Auth::guard('websoftware')->user()->id;
            $req->session()->put('softwareuser', $id);
            //live employees
            $status = DB::table('softwareusers')
                ->where('id', $id)
                ->update(['status' => 1, 'last_login' => Carbon::now()->toDateTimeString(), 'login_ipaddress' => request()->ip()]);
            //login info save

            return redirect()->intended('userdashboard');
        } else {


            return redirect('/')->with('fail', 'Contact Your Administrator');
        }
    }
    function userLogout()
    {
        $id = Session('softwareuser');
        $status = DB::table('softwareusers')
            ->where('id', $id)
            ->update(['status' => 0, 'last_logout' => Carbon::now()->toDateTimeString()]);

        if (Session('softwareuser')) {

            $username = Softwareuser::where('id', $id)->pluck('username')->first();

            $ip = request()->ip();
            $uri = request()->fullUrl();

            $user_type = 'websoftware';
            $message = $username . " logged out";
            // $locationdata = Location::get($ip);

            $locationdata = (new otherService())->get_location($ip);

            $branch_id = Softwareuser::where('id', $id)->pluck('location')->first();

            if ($locationdata != false) {
                $activityservice = (new activityService($id, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
            }
        }

        if (session()->has('softwareuser')) {
            session()->pull('softwareuser');
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
            return redirect('/');
        }
        $password = Crypt::decrypt($password);
        if ($username == "" || $password == "") {
            return redirect('/');
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
                return redirect('/');
            }
        }

        $auth = json_decode($auth, true);
        $user_id = $auth['user_id'];

        $logout = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/auth/logout', [
                'user_id' => $user_id,
            ]);

        return redirect('/');
    }
    function adminLogout()
    {
        $id = Session('adminuser');
        if (Session('adminuser')) {

            $username = Adminuser::where('id', $id)->pluck('username')->first();

            $ip = request()->ip();
            $uri = request()->fullUrl();

            $user_type = 'webadmin';
            $message = $username . " logged out";
            // $locationdata = Location::get($ip);

            $locationdata = (new otherService())->get_location($ip);
            if ($locationdata != false) {
                $activityservice = (new activityService($id, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store(0);
            }
        }

        if (session()->has('adminuser')) {

            session()->pull('adminuser');
        }

        return redirect('/');
    }
    function creditLogout()
    {
        $id = Session('credituser');
        if (Session('credituser')) {

            $username = Credituser::where('id', $id)->pluck('name')->first();

            $ip = request()->ip();
            $uri = request()->fullUrl();

            $user_type = 'webcredituser';
            $message = $username . " logged out";
            // $locationdata = Location::get($ip);

            $locationdata = (new otherService())->get_location($ip);

            $branch_id = Credituser::where('id', $id)->pluck('location')->first();

            if ($locationdata != false) {
                $activityservice = (new activityService($id, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
            }
        }


        if (session()->has('credituser')) {
            session()->pull('credituser');
        }
        return redirect('/');
    }
}
