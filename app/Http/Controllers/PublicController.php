<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Models\Softwareuser;
use App\Models\Buyproduct;
use App\Models\Returnproduct;

use App\Models\Adminuser;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use \NumberFormatter;
use PDF;

class PublicController extends Controller
{
    public function generatePDF($transaction)
    {
        // $transaction_id = Crypt::decrypt($transaction);

        $transaction_id = $transaction;
         $branch = DB::table('buyproducts')
        ->select(DB::raw("branch,transaction_id"),)
        ->where('transaction_id', $transaction_id)
        ->pluck('branch')
        ->first();
        $buyProducts = DB::table('buyproducts')
        ->select([
            'product_name',
            'service_name',
            'service_cost',
            'product_id',
            'quantity',
            'mrp',
            'price',
            'fixed_vat',
            'vat_amount',
            'total_amount',
            'unit',
            'vat_type',
            'inclusive_rate',
            'netrate',
            'discount',
            'totalamount_wo_discount',
            'price_wo_discount',
            'discount_amount',
            DB::raw("'purchase' as record_type") // Add identifier for purchase records
        ])
         ->where('branch', $branch)
        ->where('transaction_id', $transaction_id)
        ->get();

    // Get returnproducts data
    $returnProducts = DB::table('returnproducts')
        ->select([
            'product_name',
            'product_id',
            'quantity as quantity',
            'mrp',
            'price',
            'fixed_vat',
            'vat_amount',
            'total_amount',
            'unit',
            'vat_type',
            'netrate',
            'discount_amount',
            DB::raw("'return' as record_type") // Add identifier for return records
        ])
         ->where('branch', $branch)
        ->where('transaction_id', $transaction_id)
        ->get();

    // Combine both collections with purchases first
    $dataplan = $buyProducts->merge($returnProducts);
    $total = Buyproduct::select(
        DB::raw('SUM(price) as total'),
    )
        ->where('transaction_id', $transaction_id)
        ->get();
       
    $trans = $transaction;

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
        $shopid = DB::table('buyproducts')
        ->select(DB::raw("user_id,transaction_id"),)
        ->where('transaction_id', $transaction_id)
        ->pluck('user_id')
        ->first();
        $adminid = Softwareuser::Where('id', $shopid)
            ->pluck('admin_id')
            ->first();


    $shopdata = Adminuser::Where('id', $adminid)
        ->get();
    $currency = Adminuser::Where('id', $adminid)
        ->pluck('currency')
        ->first();

        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
    $total = Buyproduct::select(
        DB::raw('SUM(price) as total'),
    )
        ->where('transaction_id', $transaction_id)
        ->pluck('total')
        ->first();
    $vat = Buyproduct::select(
        DB::raw('SUM(vat_amount) as vat')
    )
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
        $returngrand = round(Returnproduct::where('transaction_id', $transaction_id)
        ->select(DB::raw('SUM(total_amount) as total_amount'))
        ->pluck('total_amount')
        ->first(), 3);



        $returnMain_discount_amt = Returnproduct::where('transaction_id', $transaction_id)
            ->pluck('total_discount_amount')
            ->first();

            $returntotal=$returngrand - $returnMain_discount_amt;


            $remaining_after_discount = ($grand - $Main_discount_amt) - ($returngrand - $returnMain_discount_amt);

        // Determine the grand in number based on the credit note amount
        if ($remaining_after_discount <= $credit_note_amount) {
            // If credit note amount covers the remaining amount
            $grandinnumber = 0; // All of the remaining amount is covered by the credit note
        } else {
            // If credit note amount does not cover the remaining amount
            $grandinnumber = $remaining_after_discount - $credit_note_amount; // Subtract credit note amount from remaining amount
        }
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

    $emailadmin = DB::table('branches')
    ->where('id', $branch)
    ->pluck('email')
    ->first();

    $trn_number = DB::table('buyproducts')
        ->where('transaction_id', $trans)
        ->pluck('trn_number')
        ->first();


    // $emailadmin = Adminuser::Where('id', $adminid)
    //     ->pluck('email')
    //     ->first();

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

    $admin_address = Adminuser::Where('id', $adminid)
        ->pluck('address')
        ->first();

        $name = Adminuser::Where('id', $adminid)
        ->pluck('name')
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


    // $pdf = PDF::loadView('/pdf/recieptwithtax', array('details' => $dataplan, 'vat' => $vat, 'grandinnumber' => $grandinnumber, 'payment_type' => $payment_type, 'totals' => $total, 'trans' => $trans, 'enctrans' => $trans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'admintrno' => $admintrno, 'emailadmin' => $emailadmin, 'billphone' => $billphone, 'billemail' => $billemail, 'vat_type' => $vat_type, 'admin_address' => $admin_address));
    // return $pdf->stream('reciept.pdf');

    $data = [
        'deliveryAdd'=>$deliveryAdd,
        'billingAdd'=>$billingAdd,
        'bankDetails'=> $bankDetails,
        'details' => $dataplan,
        'vat' => $vat,
        'grandinnumber' => $grandinnumber,
        'payment_type' => $payment_type,
        'totals' => $total,
        'trans' => $trans,
        'enctrans' => $trans,
        'custs' => $custs,
        'users' => $item,
        'branches' => $branchname,
        'shopdatas' => $shopdata,
        'currency' => $currency,
        'date' => $date,
        'amountinwords' => $amountinwords,
        'supplieddate' => $supplieddate,
        // 'cr_num' => $cr_num,
        'branchname' => $branchname,
        'trn_number' => $trn_number,
        'emailadmin' => $emailadmin,
        'billphone' => $billphone,
        'billemail' => $billemail,
        'vat_type' => $vat_type,
        'discount_amt' => $discount_amt,
        'grand_wo_dis' => $grand_wo_dis,
        'admin_address' => $admin_address,
        'price_wo_dis' => $price_wo_dis,
        'Main_discount_amt' => $Main_discount_amt,
        'rate' => $rate,
        'tax'=>$tax,
        'name'=>$name,
        'po_box' => $po_box,
        'tel' => $tel,
        'admintrno' => $admintrno,
        'logo'=>$logo,
        'company'=>$company,
        'Address'=>$Address,
        'credit_note_amount'=>$credit_note_amount,
        'service_cost'=>$service_cost,
        'branch'=>$branch,
                    'returntotal'=>$returntotal

    ];

    $pdf = PDF::loadView('/pdf/recieptwithtax', $data);

        return $pdf->stream('reciept.pdf');
    }
}
