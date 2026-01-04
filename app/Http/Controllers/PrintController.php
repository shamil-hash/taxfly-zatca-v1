<?php

namespace App\Http\Controllers;

use App\Models\Adminuser;
use App\Models\Buyproduct;
use App\Models\PrinterStatus;
use App\Models\Softwareuser;
use Carbon\Carbon;
use NumberFormatter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Support\Facades\Log;

//Includes WebClientPrint classes
require_once(app_path() . '/WebClientPrint/WebClientPrint.php');

// include_once(_DIR_ . '/../WebClientPrint/WebClientPrint.php');

// require _DIR_ . '/WebClientPrint/WebClientPrint.php';


use Neodynamic\SDK\Web\WebClientPrint;

class PrintController extends Controller
{

    public function printers($trans)
    {

        $userid = Session('softwareuser');
        $item = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

        $wcppScript = WebClientPrint::createWcppDetectionScript(route('processRequest'), Session::getId());

        return view('billingdesk.printers', ['wcppScript' => $wcppScript, 'users' => $item, 'trans' => $trans]);
    }

    public function printESCPOS($trans)
    {

        /*---------------------- DB CODE -----------------------*/

        $dataplan = DB::table('buyproducts')
            ->select(DB::raw("buyproducts.product_name as product_name,buyproducts.product_id as product_id,buyproducts.quantity as quantity,buyproducts.mrp as mrp,buyproducts.price as price,buyproducts.fixed_vat as fixed_vat,buyproducts.vat_amount as vat_amount,buyproducts.total_amount as total_amount, buyproducts.unit as unit"),)
            ->where('buyproducts.transaction_id', $trans)
            ->get();

        $total = Buyproduct::select(
            DB::raw("SUM(price) as total"),
        )
            ->where('transaction_id', $trans)
            ->get();

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $enctrans = Crypt::encrypt($trans);

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

        $shopid = Session('softwareuser');

        $adminid = Softwareuser::Where('id', $shopid)
            ->pluck('admin_id')
            ->first();

        $shopdata = Adminuser::Where('id', $adminid)
            ->get();

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $total = Buyproduct::select(
            DB::raw("SUM(price) as total"),
        )
            ->where('transaction_id', $trans)
            ->pluck('total')
            ->first();

        $vat = Buyproduct::select(
            DB::raw("SUM(vat_amount) as vat"),
        )
            ->where('transaction_id', $trans)
            ->pluck('vat')
            ->first();

        $payment_type = DB::table('buyproducts')
            ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
            ->select(DB::raw("payment.type as payment_type"))
            ->where('buyproducts.transaction_id', $trans)
            ->pluck('payment_type')
            ->first();

        $date = DB::table('buyproducts')
            ->select(DB::raw("DATE(buyproducts.created_at) as date"),)
            ->where('transaction_id', $trans)
            ->pluck('date')
            ->first();

        $date = Carbon::parse($date)->format('d-m-Y');

        $grand = Buyproduct::where('transaction_id', $trans)
            ->select(DB::raw("SUM(total_amount) as total_amount"),)
            ->pluck('total_amount')
            ->first();

        $grandinnumber = $grand;
        $grand = number_format($grand, 3, '.', '');
        $amountinwords = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $amountinwords = $amountinwords->format($grand);
        $amountinwords = ucwords($amountinwords);

        $supplieddate = Carbon::now()->format('d-m-Y');

        $cr_num = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();

        $po_box = Adminuser::Where('id', $adminid)
            ->pluck('po_box')
            ->first();

        $tel = Adminuser::Where('id', $adminid)
            ->pluck('phone')
            ->first();

        $trn_number = DB::table('buyproducts')
            ->where('transaction_id', $trans)
            ->pluck('trn_number')
            ->first();

        $adminname = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();

        /*-----------------------------------------------------*/

        $trn_number = $trn_number ? $trn_number : '0';
        $dataplanJson = json_encode($dataplan);

        $wcpScript = WebClientPrint::createScript(route('processRequest'), route('printCommands', [$trans, $adminname, $po_box, $branchname, $tel, $grandinnumber, $vat, $trn_number, $custs, $date, $supplieddate, $payment_type]) . '?dataplan=' . urlencode($dataplanJson), Session::getId());

        return view('billingdesk.printESCPOS', ['wcpScript' => $wcpScript, 'users' => $item, 'trans' => $trans]);
        
        // return response()->json(['wcpScript' => $wcpScript, 'trans' => $trans]);
    }
    public function getWcpScript($trans)
    {
        $dataplan = DB::table('buyproducts')
            ->select(DB::raw("buyproducts.product_name as product_name,buyproducts.product_id as product_id,buyproducts.quantity as quantity,buyproducts.mrp as mrp,buyproducts.price as price,buyproducts.fixed_vat as fixed_vat,buyproducts.vat_amount as vat_amount,buyproducts.total_amount as total_amount, buyproducts.unit as unit"),)
            ->where('buyproducts.transaction_id', $trans)
            ->get();

        $total = Buyproduct::select(
            DB::raw("SUM(price) as total"),
        )
            ->where('transaction_id', $trans)
            ->get();

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $enctrans = Crypt::encrypt($trans);

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

        $shopid = Session('softwareuser');

        $adminid = Softwareuser::Where('id', $shopid)
            ->pluck('admin_id')
            ->first();

        $shopdata = Adminuser::Where('id', $adminid)
            ->get();

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $total = Buyproduct::select(
            DB::raw("SUM(price) as total"),
        )
            ->where('transaction_id', $trans)
            ->pluck('total')
            ->first();

        $vat = Buyproduct::select(
            DB::raw("SUM(vat_amount) as vat"),
        )
            ->where('transaction_id', $trans)
            ->pluck('vat')
            ->first();

        $payment_type = DB::table('buyproducts')
            ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
            ->select(DB::raw("payment.type as payment_type"))
            ->where('buyproducts.transaction_id', $trans)
            ->pluck('payment_type')
            ->first();

        $date = DB::table('buyproducts')
            ->select(DB::raw("DATE(buyproducts.created_at) as date"),)
            ->where('transaction_id', $trans)
            ->pluck('date')
            ->first();

        $date = Carbon::parse($date)->format('d-m-Y');

        $grand = Buyproduct::where('transaction_id', $trans)
            ->select(DB::raw("SUM(total_amount) as total_amount"),)
            ->pluck('total_amount')
            ->first();

        $grandinnumber = $grand;
        $grand = number_format($grand, 3, '.', '');
        $amountinwords = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $amountinwords = $amountinwords->format($grand);
        $amountinwords = ucwords($amountinwords);

        $supplieddate = Carbon::now()->format('d-m-Y');

        $cr_num = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();

        $po_box = Adminuser::Where('id', $adminid)
            ->pluck('po_box')
            ->first();

        $tel = Adminuser::Where('id', $adminid)
            ->pluck('phone')
            ->first();

        $trn_number = DB::table('buyproducts')
            ->where('transaction_id', $trans)
            ->pluck('trn_number')
            ->first();

        $adminname = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();

        /*-----------------------------------------------------*/

        $trn_number = $trn_number ? $trn_number : '0';
        $dataplanJson = json_encode($dataplan);


        $wcpScript = WebClientPrint::createScript(route('processRequest'), route('printCommands', [$trans, $adminname, $po_box, $branchname, $tel, $grandinnumber, $vat, $trn_number, $custs, $date, $supplieddate, $payment_type]) . '?dataplan=' . urlencode($dataplanJson), Session::getId());

        return response()->json(['wcpScript' => $wcpScript, 'trans' => $trans]);
    }
    public function savePrinterStatus()
    {
        $userid = Session('softwareuser');

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $existingData = DB::table('printer_statuses')
            ->where('user_id', $userid)
            ->where('branch', $branch)
            ->count();

        if ($existingData == 0) {

            $data = new PrinterStatus();
            $data->user_id = $userid;
            $data->branch = $branch;
            $data->printerstate = 'found';
            $data->save();
        }

        return response()->json([
            'dat' => $existingData,
        ]);
    }
    public function getPrinterStatus($user_id, $branch)
    {
        $existingData = DB::table('printer_statuses')
            ->where('user_id', $user_id)
            ->where('branch', $branch)
            ->count();

        // $existingData = DB::table('printer_statuses')->count();

        if ($existingData == 1) {

            $printstatus = DB::table('printer_statuses')
                ->where('user_id', $user_id)
                ->where('branch', $branch)
                ->pluck('printerstate');
        } else if ($existingData == 0) {

            $printstatus = NULL;
        }

        return response()->json([
            'printstatus' => $printstatus,
            'existingData' => $existingData,
            'branch' => $branch
        ]);
    }
}
