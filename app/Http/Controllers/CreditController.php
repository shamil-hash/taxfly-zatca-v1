<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Credituser;
use App\Models\Adminuser;

class CreditController extends Controller
{
    function dashBoard()
    {
        if (session()->missing('credituser')) {
            return redirect('/');
        }
        $userid = Session('credituser');
        $username = DB::table('creditusers')->where('id', $userid)->pluck('name')->first();
        $collected = DB::table('creditsummaries')
            ->where('credituser_id', $userid)
            ->pluck('collected_amount')
            ->first();
        $total_due = DB::table('creditsummaries')
            ->where('credituser_id', $userid)
            ->pluck('due_amount')
            ->first();

        $total_creditnote = DB::table('creditsummaries')
            ->where('credituser_id', $userid)
            ->pluck('creditnote')
            ->first();

        $due_amount = $total_due - $collected - $total_creditnote;

        $adminid = Credituser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        return view('/credit/creditdashboard', array('due' => $due_amount, 'username' => $username, 'purchase' => $total_due, 'paid' => $collected, 'currency' => $currency));
    }
    public function changePassword()
    {
        if (session()->missing('credituser')) {
            return redirect('/');
        }
        $username = Credituser::Where('id', Session('credituser'))
            ->pluck('name')
            ->first();
        return view('/credit/creditchangepassword', array('username' => $username));
    }
    public function submitcredituserPassword(Request $req)
    {
        if (session()->missing('credituser')) {
            return redirect('/');
        }
        $req->validate([
            'username' => 'required',
            'password' => 'required',
            'confirmpassword' => 'required',
        ]);
        if ($req->password == $req->confirmpassword) {
            $user = Credituser::find(Session('credituser'));
            $user->username = $req->username;
            $user->password = Hash::make($req->password);
            $user->save();
            return back()->with('success', 'Password changed successfully!');
        } else {
            return back()->with('failed', 'Password does not match');
        }
    }
    public function creditTransactions()
    {
        if (session()->missing('credituser')) {
            return redirect('/');
        }
        $username = Credituser::Where('id', Session('credituser'))
            ->pluck('name')
            ->first();

        $salesdata = DB::table('buyproducts')
            ->select(DB::raw("transaction_id,created_at,SUM(bill_grand_total) as total_amount "))
            // ->where('customer_name', $username)
            ->where('credit_user_id', Session('credituser'))
            ->where('payment_type', 3)
            ->groupBy('transaction_id')
            ->orderBy('created_at', 'DESC')
            ->get();

        $userid = Session('credituser');

        $adminid = Credituser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $start_date = "";
        $end_date = "";
        return view('/credit/credittransactions', array('salesdata' => $salesdata, 'start_date' => $start_date, 'end_date' => $end_date, 'currency' => $currency));
    }
    public function creditDuesummary()
    {
        if (session()->missing('credituser')) {
            return redirect('/');
        }
        $username = Credituser::Where('id', Session('credituser'))
            ->pluck('name')
            ->first();

        // $salesdata = DB::table('fundhistories')
        //     ->select(DB::raw("due,amount,created_at,user_id"))
        //     ->where('credituser_id', Session('credituser'))
        //     ->orderBy('created_at', 'DESC')
        //     ->get();

        $userid = Session('credituser');

        $adminid = Credituser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $salesdata = DB::table('credit_transactions')
            ->select(DB::raw("*"))
            ->where('credituser_id', $userid)
            ->get();

        $lastTransaction_for_due = DB::table('credit_transactions')
            ->where('credituser_id', $userid)
            ->orderBy('created_at', 'desc')
            ->first();

        $final_due = $lastTransaction_for_due->updated_balance ?? 0;

        $start_date = "";
        $end_date = "";
        return view('/credit/creditduesummary', array('salesdata' => $salesdata, 'start_date' => $start_date, 'end_date' => $end_date, 'currency' => $currency));
    }
    public function creditTransactiondate(Request $req)
    {
        if (session()->missing('credituser')) {
            return redirect('/');
        }
        $username = Credituser::Where('id', Session('credituser'))
            ->pluck('name')
            ->first();
        if ($req->start_date == "" && $req->end_date == "") {
            $salesdata = DB::table('buyproducts')
                ->select(DB::raw("transaction_id,created_at,SUM(total_amount ) as total_amount "))
                ->where('customer_name', $username)
                ->where('payment_type', 3)
                ->groupBy('transaction_id')
                ->orderBy('created_at', 'DESC')
                ->get();
        } else if ($req->start_date == $req->end_date) {
            $salesdata = DB::table('buyproducts')
                ->select(DB::raw("transaction_id,created_at,SUM(total_amount ) as total_amount "))
                ->where('customer_name', $username)
                ->where('payment_type', 3)
                ->groupBy('transaction_id')
                ->whereDate('created_at', '=', $req->start_date)
                ->orderBy('created_at', 'DESC')
                ->get();
        } else if ($req->start_date != $req->end_date && $req->start_date != "" && $req->end_date != "") {
            $salesdata = DB::table('buyproducts')
                ->select(DB::raw("transaction_id,created_at,SUM(total_amount ) as total_amount "))
                ->where('customer_name', $username)
                ->where('payment_type', 3)
                ->groupBy('transaction_id')
                ->whereBetween('created_at', [$req->start_date . ' 00:00:00', $req->end_date . ' 23:59:59'])
                ->orderBy('created_at', 'DESC')
                ->get();
        } else {
            $salesdata = DB::table('buyproducts')
                ->select(DB::raw("transaction_id,created_at,SUM(total_amount ) as total_amount "))
                ->where('customer_name', $username)
                ->where('payment_type', 3)
                ->groupBy('transaction_id')
                ->orderBy('created_at', 'DESC')
                ->get();
        }

        $userid = Session('credituser');

        $adminid = Credituser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        return view('/credit/credittransactions', array('salesdata' => $salesdata, 'start_date' => $req->start_date, 'end_date' => $req->end_date, 'currency' => $currency));
    }
    public function creditDuesummarydate(Request $req)
    {
        if (session()->missing('credituser')) {
            return redirect('/');
        }
        $username = Credituser::Where('id', Session('credituser'))
            ->pluck('username')
            ->first();

        // if ($req->start_date == "" && $req->end_date == "") {
        //     $salesdata = DB::table('fundhistories')
        //         ->select(DB::raw("due,amount,created_at,user_id"))
        //         ->where('credituser_id', Session('credituser'))
        //         ->orderBy('created_at', 'DESC')
        //         ->get();
        // } else if ($req->start_date == $req->end_date) {
        //     $salesdata = DB::table('fundhistories')
        //         ->select(DB::raw("due,amount,created_at,user_id"))
        //         ->where('credituser_id', Session('credituser'))
        //         ->whereDate('created_at', '=', $req->start_date)
        //         ->orderBy('created_at', 'DESC')
        //         ->get();
        // } else if ($req->start_date != $req->end_date) {
        //     $salesdata = DB::table('fundhistories')
        //         ->select(DB::raw("due,amount,created_at,user_id"))
        //         ->where('credituser_id', Session('credituser'))
        //         ->whereBetween('created_at', [$req->start_date . ' 00:00:00', $req->end_date . ' 23:59:59'])
        //         ->orderBy('created_at', 'DESC')
        //         ->get();
        // } else {
        //     $salesdata = DB::table('fundhistories')
        //         ->select(DB::raw("due,amount,created_at,user_id"))
        //         ->where('credituser_id', Session('credituser'))
        //         ->orderBy('created_at', 'DESC')
        //         ->get();
        // }

        if ($req->start_date == "" && $req->end_date == "") {

            $salesdata = DB::table('credit_transactions')
                ->select(DB::raw("*"))
                ->where('credituser_id', Session('credituser'))
                ->get();
        } else if ($req->start_date == $req->end_date) {

            $salesdata = DB::table('credit_transactions')
                ->select(DB::raw("*"))
                ->where('credituser_id', Session('credituser'))
                ->whereDate('created_at', '=', $req->start_date)
                ->get();
        } else if ($req->start_date != $req->end_date) {

            $salesdata = DB::table('credit_transactions')
                ->select(DB::raw("*"))
                ->where('credituser_id', Session('credituser'))
                ->whereBetween('created_at', [$req->start_date . ' 00:00:00', $req->end_date . ' 23:59:59'])
                ->get();
        } else {

            $salesdata = DB::table('credit_transactions')
                ->select(DB::raw("*"))
                ->where('credituser_id', Session('credituser'))
                ->get();
        }

        $userid = Session('credituser');

        $adminid = Credituser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        return view('/credit/creditduesummary', array('salesdata' => $salesdata, 'start_date' => $req->start_date, 'end_date' => $req->end_date, 'currency' => $currency));
    }
}
