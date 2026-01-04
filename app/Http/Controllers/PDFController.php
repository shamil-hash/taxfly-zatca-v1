<?php

namespace App\Http\Controllers;

use App\Models\Adminuser;
use App\Models\Branch;
use App\Models\Buyproduct;
use App\Models\Credituser;
use App\Models\DeliveryNote;
use App\Models\Invoiceproduct;
use App\Models\PurchaseOrder;
use App\Models\AccountIndirectIncome;
use App\Models\Accountexpense;
use App\Models\Returnpurchase;
use App\Models\SalesOrder;
use App\Models\Service;
use App\Models\Softwareuser;
use App\Models\Returnproduct;
use App\Models\CashSupplierTransaction;
use App\Models\CreditSupplierTransaction;
use App\Models\CashTransStatement;
use App\Models\CreditTransaction;
use App\Exports\ExpenseHistoryExport;

use App\Models\Supplier;
use App\Services\salesQuotService;
use App\Services\UserService;
use App\Services\Zatca\ZatcaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use NumberFormatter;
use PDF;
use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

use Illuminate\Support\Str;
use Saleh7\Zatca\Helpers\Certificate;
use App\Services\Zatca\InvoiceXmlGenerator;
use App\Services\Zatca\InvoiceSignerService;
use App\Services\Zatca\InvoiceQrGenerator;
use App\Helpers\ZatcaHelper;

// Includes WebClientPrint classes
require_once app_path().'/WebClientPrint/WebClientPrint.php';

use Neodynamic\SDK\Web\WebClientPrint;

class PDFController extends Controller
{
    public function generatePDF($transaction_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
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
        $shopid = Session('softwareuser');
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

        $grand = Buyproduct::where('transaction_id', $transaction_id)
            ->select(DB::raw('SUM(total_amount) as total_amount'))
            ->pluck('total_amount')
            ->first();

        $discount_amt = Buyproduct::select(DB::raw('SUM(discount_amount) as discount_amount'))
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
        $supplieddate = Carbon::now()->format('d-m-Y');
        $cr_num = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();
        $po_box = Adminuser::Where('id', $adminid)
            ->pluck('po_box')
            ->first();

            $name = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();

        $admintrno = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
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

        $admin_address = Adminuser::Where('id', $adminid)
            ->pluck('address')
            ->first();
 $company = DB::table('branches')
        ->where('id', $branch)
        ->pluck('company')
        ->first();
        // $pdf = PDF::loadView('/pdf/recieptwithnotax', array('details' => $dataplan, 'grandinnumber' => $grandinnumber, 'payment_type' => $payment_type, 'totals' => $total, 'trans' => $trans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'amountinwords' => $amountinwords, 'vat' => $vat, 'admintrno' => $admintrno, 'billphone' => $billphone, 'billemail' => $billemail, 'admin_address' => $admin_address));

        $data = [
            'details' => $dataplan,
            'grandinnumber' => $grandinnumber,
            'payment_type' => $payment_type,
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
            'vat' => $vat,
            'admintrno' => $admintrno,
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
            'credit_note_amount'=>$credit_note_amount,
            'branch'=>$branch,
            'returntotal'=>$returntotal,
            'company'=>$company
        ];

        $pdf = \PDF::loadView('/pdf/recieptwithnotax', $data);

        return $pdf->download('reciept.pdf');
    }

    public function generatetaxPDF($transaction_id)
    {
        // if (session()->missing('softwareuser') && session()->missing('adminuser')) {
        //     return redirect('/');
        // }
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $buyProducts = DB::table('buyproducts')
        ->select([
            'product_name',
            'service_name',
            'service_cost',
            'product_id',
            'quantity',
            'box_count',
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
           
        $trans = $transaction_id;

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
        if (Session('softwareuser')) {
            $shopid = Session('softwareuser');
            $adminid = Softwareuser::Where('id', $shopid)
                ->pluck('admin_id')
                ->first();
        } elseif (Session('adminuser')) {
            $adminid = Session('adminuser');
        }

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
        
        $arabic_name = DB::table('branches')
        ->where('id', $branch)
        ->pluck('arabic_name')
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
            'branch'=>$branch,
            'service_cost'=>$service_cost,
            'returntotal'=>$returntotal,
            'arabic_name'=>$arabic_name

        ];

        $pdf = \PDF::loadView('/pdf/recieptwithtax', $data);

        return $pdf->download('reciept.pdf');

        // // Disable browser caching for this page
        // return response()->view('/pdf/recieptwithtax')->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function admingeneratetaxPDF($transaction_id)
    {
        if (session()->missing('softwareuser') && session()->missing('adminuser')) {
            return redirect('/');
        }
        $dataplan = DB::table('buyproducts')
            ->select(DB::raw('buyproducts.product_name as product_name,buyproducts.product_id as product_id,buyproducts.quantity as quantity,buyproducts.mrp as mrp,buyproducts.price as price,buyproducts.fixed_vat as fixed_vat,buyproducts.vat_amount as vat_amount,buyproducts.total_amount as total_amount, buyproducts.unit, buyproducts.vat_type as vat_type, buyproducts.inclusive_rate as inclusive_rate'))
            ->where('buyproducts.transaction_id', $transaction_id)
            ->get();
        $total = Buyproduct::select(
            DB::raw('SUM(price) as total'),
        )
            ->where('transaction_id', $transaction_id)
            ->get();
        $branch = DB::table('buyproducts')
            ->where('transaction_id', $transaction_id)
            ->pluck('branch')
            ->first();
        $trans = $transaction_id;
        $enctrans = Crypt::encrypt($trans);
        $custs = DB::table('buyproducts')
            ->where('transaction_id', $trans)
            ->pluck('customer_name')
            ->first();
        $branchname = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();
        $adminid = Session('adminuser');
        $shopdata = Adminuser::Where('id', $adminid)
            ->get();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $total = Buyproduct::select(
            DB::raw('SUM(price) as total'),
        )
            ->where('transaction_id', $transaction_id)
            ->pluck('total')
            ->first();
        $vat = Buyproduct::select(
            DB::raw('SUM(vat_amount) as vat'),
        )
            ->where('transaction_id', $transaction_id)
            ->pluck('vat')
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
        $grandinnumber = $grand;
        $grand = number_format($grand, 3, '.', '');
        $amountinwords = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
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

        $admintrno = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
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

        $admin_address = Adminuser::Where('id', $adminid)
            ->pluck('address')
            ->first();

        $pdf = \PDF::loadView('/pdf/recieptwithtax', ['details' => $dataplan, 'vat' => $vat, 'grandinnumber' => $grandinnumber, 'payment_type' => $payment_type, 'totals' => $total, 'trans' => $trans, 'enctrans' => $enctrans, 'custs' => $custs, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'admintrno' => $admintrno, 'emailadmin' => $emailadmin, 'billphone' => $billphone, 'billemail' => $billemail, 'vat_type' => $vat_type, 'admin_address' => $admin_address]);

        return $pdf->download('reciept.pdf');
    }

    public function invoicePDF($invoice_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $dataplan = DB::table('invoiceproducts')
            ->where('invoice_id', $invoice_id)
            ->get();
        $total = Invoiceproduct::select(
            DB::raw('SUM(price) as total'),
        )
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
        $shopid = Session('softwareuser');
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
        $total = Invoiceproduct::select(
            DB::raw('SUM(price) as total'),
        )
            ->where('invoice_id', $invoice_id)
            ->pluck('total')
            ->first();
        $vat = Invoiceproduct::select(
            DB::raw('SUM(vat_amount) as vat'),
        )
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
        $date = Carbon::parse($date)->format('d-m-Y');
        $grand = $total + $vat;
        $amountinwords = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $amountinwords = $amountinwords->format($grand);
        $amountinwords = ucwords($amountinwords);
        $supplieddate = Carbon::now()->format('d-m-Y');
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
        $pdf = \PDF::loadView('/pdf/generatedinvoice', ['tax'=>$tax,'details' => $dataplan, 'vat' => $vat, 'payment_type' => $payment_type, 'totals' => $total, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'from_name' => $from_name, 'from_address' => $from_address, 'from_trnnumber' => $from_trnnumber, 'from_number' => $from_number, 'to_name' => $to_name, 'to_address' => $to_address, 'to_trnnumber' => $to_trnnumber, 'to_number' => $to_number, 'heading' => $heading, 'footer' => $footer, 'termsandconditions' => $termsandconditions, 'invoice_type' => $invoice_type, 'due_date' => $due_date, 'invoice_number' => $invoice_number, 'invoice_num' => $invoice_num, 'to_email' => $to_email, 'from_email' => $from_email]);

        return $pdf->stream('reciept.pdf');
    }

    // credit side
    public function generatecreditPDF($transaction_id)
    {
        if (session()->missing('credituser')) {
            return redirect('/');
        }
        $dataplan = DB::table('buyproducts')
            ->select(DB::raw('buyproducts.product_name as product_name,buyproducts.product_id as product_id,buyproducts.quantity as quantity,buyproducts.mrp as mrp,buyproducts.price as price,buyproducts.fixed_vat as fixed_vat,buyproducts.vat_amount as vat_amount,buyproducts.total_amount as total_amount, buyproducts.unit as unit, buyproducts.vat_type as vat_type, buyproducts.inclusive_rate as inclusive_rate, buyproducts.netrate as netrate,buyproducts.discount, buyproducts.totalamount_wo_discount, buyproducts.price_wo_discount, buyproducts.discount_amount'))
            ->where('buyproducts.transaction_id', $transaction_id)
            ->get();
        $total = Buyproduct::select(
            DB::raw('SUM(price) as total')
        )
            ->where('transaction_id', $transaction_id)
            ->get();
        $trans = $transaction_id;
        $enctrans = Crypt::encrypt($trans);
        $custs = DB::table('buyproducts')
            ->where('transaction_id', $trans)
            ->pluck('customer_name')
            ->first();
        $branch = DB::table('buyproducts')
            ->where('transaction_id', $trans)
            ->pluck('branch')
            ->first();
        $branchname = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();
        $adminid = Credituser::Where('id', Session('credituser'))
            ->pluck('admin_id')
            ->first();
        $shopdata = Adminuser::Where('id', $adminid)
            ->get();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
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

        $grandinnumber = $grand - $Main_discount_amt;

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

        $supplieddate = Carbon::now()->format('d-m-Y');
        $cr_num = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();
        $po_box = Adminuser::Where('id', $adminid)
            ->pluck('po_box')
            ->first();
        $trn_number = DB::table('buyproducts')
            ->where('transaction_id', $trans)
            ->pluck('trn_number')
            ->first();
        $tel = Adminuser::Where('id', $adminid)
            ->pluck('phone')
            ->first();

        $admintrno = Adminuser::Where('cr_number', $adminid)
            ->pluck('cr_number')
            ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
            ->first();

        $billphone = Buyproduct::select(DB::raw('phone'))
            ->where('transaction_id', $transaction_id)
            ->pluck('phone')
            ->first();

        $billemail = Buyproduct::select(DB::raw('email'))
            ->where('transaction_id', $transaction_id)
            ->pluck('email')
            ->first();

        $admin_address = Adminuser::Where('id', $adminid)
            ->pluck('address')
            ->first();

        $vat_type = Buyproduct::select(DB::raw('vat_type'))
            ->where('transaction_id', $transaction_id)
            ->pluck('vat_type')
            ->first();

        $data = [
            'details' => $dataplan,
            'vat' => $vat,
            'grandinnumber' => $grandinnumber,
            'enctrans' => $enctrans,
            'payment_type' => $payment_type,
            'totals' => $total,
            'trans' => $trans,
            'custs' => $custs,
            'shopdatas' => $shopdata,
            'currency' => $currency,
            'date' => $date,
            'amountinwords' => $amountinwords,
            'supplieddate' => $supplieddate,
            'cr_num' => $cr_num,
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'trn_number' => $trn_number,
            'admintrno' => $admintrno,
            'emailadmin' => $emailadmin,
            'billphone' => $billphone,
            'billemail' => $billemail,
            'admin_address' => $admin_address,
            'vat_type' => $vat_type,
            'discount_amt' => $discount_amt,
            'grand_wo_dis' => $grand_wo_dis,
            'adminid' => $adminid,
            'price_wo_dis' => $price_wo_dis,
            'Main_discount_amt' => $Main_discount_amt,
            'rate' => $rate,
        ];

        $pdf = \PDF::loadView('/pdf/recieptwithtax', $data);

        return $pdf->download('reciept.pdf');
    }

    public function plexpaydownloadpdf($transaction_id)
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

        $recharge = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/rechargeInfo', [
                'transaction_id' => $transaction_id,
            ]);
        $recharges = $recharge['recharge_info'];
        $contact = $recharge['contact'];

        if (($recharges['recharge_type'] == 'Mobile Recharge') || ($recharges['recharge_type'] == 'KSEB BILL')) {
            $pdf = \PDF::loadView('/pdf/plexpayreciept', ['recharges' => $recharges, 'contact' => $contact]);

            return $pdf->stream('reciept.pdf');
        } elseif ($recharges['recharge_type'] == 'Voucher') {
            $pdf = \PDF::loadView('/pdf/plexpayvoucherreciept', ['recharges' => $recharges, 'contact' => $contact]);

            return $pdf->stream('reciept.pdf');
        }
    }

    public function plexpaycollectiondownloadpdf($transaction_id)
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

        $account = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.comapi/dash_change', [
                'dash' => 0,
            ]);

        $wallet_amount = $account['wallet_amount'];
        $due_amount = $account['due_amount'];

        $recharge = Http::withHeaders([
            'server-sslkey' => '7f88838a7d63896460bddde5bdd8dcc39be802297cdd76aaf9e6e6b97a4f18377c',
            'access-token' => $access_token,
            'user-id' => $user_id,
        ])->asForm()
            ->post('https://plexpay.netplexsolution.com/api/duePrint', [
                'due_id' => $transaction_id,
            ]);

        $recharge = $recharge['due_info'][0];

        $pdf = \PDF::loadView('/pdf/plexpaycollectionreciept', ['recharges' => $recharge]);

        return $pdf->stream('reciept.pdf');
    }

    public function generatetaxPDFsunmi($transaction_id)
    {
        if (session()->missing('softwareuser') && session()->missing('adminuser')) {
            return redirect('/');
        }
        $dataplan = DB::table('buyproducts')
            ->select(DB::raw('buyproducts.product_name as product_name,buyproducts.product_id as product_id,buyproducts.quantity as quantity,buyproducts.mrp as mrp,buyproducts.price as price,buyproducts.fixed_vat as fixed_vat,buyproducts.vat_amount as vat_amount,buyproducts.total_amount as total_amount, buyproducts.unit as unit,buyproducts.vat_type as vat_type, buyproducts.netrate as netrate'))
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
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
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

        $grandinnumber = $grand - $Main_discount_amt;
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

        $name = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();

        $admintrno = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
            ->first();

        $billphone = Buyproduct::select(DB::raw('phone'))
            ->where('transaction_id', $transaction_id)
            ->pluck('phone')
            ->first();

        $billemail = Buyproduct::select(DB::raw('email'))
            ->where('transaction_id', $transaction_id)
            ->pluck('email')
            ->first();

        $adminroles = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $adminid)
            ->get();

        $vat_type = Buyproduct::select(DB::raw('vat_type'))
            ->where('transaction_id', $transaction_id)
            ->pluck('vat_type')
            ->first();

        $filename = uniqid('sunmi_'.$transaction_id).'.pdf';
        $tel = DB::table('branches')
        ->where('id', $branch)
        ->pluck('mobile')
        ->first();
        $po_box = DB::table('branches')
        ->where('id', $branch)
        ->pluck('po_box')
        ->first();

        $sunmilogo = DB::table('branches')
        ->where('id', $branch)
        ->pluck('sunmilogo')
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
         $arabic_name = DB::table('branches')
        ->where('id', $branch)
        ->pluck('arabic_name')
        ->first();
        $Address = DB::table('branches')
        ->where('id', $branch)
        ->pluck('address')
        ->first();

        $credit_user_id = Buyproduct::select(DB::raw('credit_user_id'))
        ->where('transaction_id', $transaction_id)
        ->pluck('credit_user_id')
        ->first();

        if($credit_user_id != null){
            $lastTransaction = CreditTransaction::select([
                    'credituser_id',
                    'collected_amount',
                    'updated_balance',
                    'invoice_due',
                    'due',
                    'comment'
                ])
                  ->where('location', $branch)
                ->where('credituser_id', $credit_user_id)
                ->where('transaction_id', $transaction_id)
                ->where('comment', '!=', 'Payment Received')
                ->orderBy('id', 'desc')
                ->first();
                
            if($lastTransaction) { // Check if transaction exists
                $creditUserId = $lastTransaction->credituser_id;
                $collectedAmount = $lastTransaction->collected_amount;
                $updatedBalance = $lastTransaction->updated_balance;
                $invoiceDue = $lastTransaction->invoice_due;
                $Due = $lastTransaction->due;
                $comment = $lastTransaction->comment;
            } else {
                // Set default values if no transaction found
                $creditUserId = null;
                $collectedAmount = 0;
                $updatedBalance = 0;
                $invoiceDue = 0;
                $Due = 0;
                $comment='';
            }
        } else {
            // Set default values if no credit user
            $creditUserId = null;
            $collectedAmount = 0;
            $updatedBalance = 0;
            $invoiceDue = 0;
            $Due = 0;
            $comment='';

        }

        $data = [
            'comment' => $comment,
            'creditUserId' => $creditUserId,
            'collectedAmount' => $collectedAmount,
            'updatedBalance' => $updatedBalance,
            'invoiceDue' => $invoiceDue,
            'Due' => $Due,
            'details' => $dataplan,
            'vat' => $vat,
            'grandinnumber' => $grandinnumber,
            'payment_type' => $payment_type,
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
            'cr_num' => $cr_num,
            // 'po_box' => $po_box,
            // 'tel' => $tel,
            'branchname' => $branchname,
            'trn_number' => $trn_number,
            'transaction_id' => $transaction_id,
            // 'admin_name' => $adminname,
            // 'admintrno' => $admintrno,
            'emailadmin' => $emailadmin,
            'billphone' => $billphone,
            'billemail' => $billemail,
            'discount_amt' => $discount_amt,
            'grand_wo_dis' => $grand_wo_dis,
            'adminroles' => $adminroles,
            'vat_type' => $vat_type,
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
            'branch'=>$branch,
            'sunmilogo'=>$sunmilogo,
            'arabic_name'=>$arabic_name
        ];

        // $wcppScript = WebClientPrint::createWcppDetectionScript(route('processRequest'), Session::getId());

        // $pdf = PDF::loadView('/pdf/sunmireceipt', array('details' => $dataplan, 'vat' => $vat, 'grandinnumber' => $grandinnumber, 'payment_type' => $payment_type, 'totals' => $total, 'trans' => $trans, 'enctrans' => $enctrans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'transaction_id' => $transaction_id, 'admin_name' => $adminname));
        // return $pdf->stream('reciept.pdf');

        foreach ($adminroles as $adminrole) {
            if ($adminrole->module_id == '23') {
                return view('/pdf/sunmireceipt', $data);
            } elseif ($adminrole->module_id == '25') {
                $pdf = \PDF::loadView('/pdf/sunmireceipt_pdf', $data);

                return $pdf->stream($filename, ['Attachment' => false]);
            }
        }
    }

    public function sunmi_PDFPrint($transaction_id)
    {
        if (session()->missing('softwareuser') && session()->missing('adminuser')) {
            return redirect('/');
        }
        $dataplan = DB::table('buyproducts')
            ->select(DB::raw('buyproducts.product_name as product_name,buyproducts.product_id as product_id,buyproducts.quantity as quantity,buyproducts.mrp as mrp,buyproducts.price as price,buyproducts.fixed_vat as fixed_vat,buyproducts.vat_amount as vat_amount,buyproducts.total_amount as total_amount, buyproducts.unit as unit'))
            ->where('buyproducts.transaction_id', $transaction_id)
            ->get();
        $total = Buyproduct::select(
            DB::raw('SUM(price) as total'),
        )
            ->where('transaction_id', $transaction_id)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $trans = $transaction_id;
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
            DB::raw('SUM(vat_amount) as vat'),
        )
            ->where('transaction_id', $transaction_id)
            ->pluck('vat')
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

        $discount_amt = Buyproduct::select(DB::raw('SUM(discount_amount) as discount_amount'))
            ->where('transaction_id', $transaction_id)
            ->pluck('discount_amount')
            ->first();

        $grandinnumber = $grand;
        $grand = number_format($grand, 3, '.', '');
        $amountinwords = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $amountinwords = $amountinwords->format($grand);
        $amountinwords = ucwords($amountinwords);

        $grand_wo_dis = Buyproduct::where('transaction_id', $transaction_id)
            ->select(DB::raw('SUM(totalamount_wo_discount) as totalamount_wo_discount'))
            ->pluck('totalamount_wo_discount')
            ->first();

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

        $admintrno = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
            ->first();

        $billphone = Buyproduct::select(DB::raw('phone'))
            ->where('transaction_id', $transaction_id)
            ->pluck('phone')
            ->first();

        $billemail = Buyproduct::select(DB::raw('email'))
            ->where('transaction_id', $transaction_id)
            ->pluck('email')
            ->first();

        $adminroles = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $adminid)
            ->get();

        $filename = uniqid('sunmi_'.$transaction_id).'.pdf';

        $data = [
            'details' => $dataplan,
            'vat' => $vat,
            'grandinnumber' => $grandinnumber,
            'payment_type' => $payment_type,
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
            'cr_num' => $cr_num,
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'trn_number' => $trn_number,
            'transaction_id' => $transaction_id,
            'admin_name' => $adminname,
            'admintrno' => $admintrno,
            'emailadmin' => $emailadmin,
            'billphone' => $billphone,
            'billemail' => $billemail,
            'discount_amt' => $discount_amt,
            'grand_wo_dis' => $grand_wo_dis,
            'adminroles' => $adminroles,
            'tax'=>$tax,
        ];

        $pdf = \PDF::loadView('/pdf/sunmireceipt_pdf', $data);

        return $pdf->stream($filename, ['Attachment' => false]);

        // $pdf = PDF::loadView('/pdf/sunmireceipt', $data);

        // return $pdf->download($filename . '.pdf');

        // $wcppScript = WebClientPrint::createWcppDetectionScript(route('processRequest'), Session::getId());

        // $pdf = PDF::loadView('/pdf/sunmireceipt', array('details' => $dataplan, 'vat' => $vat, 'grandinnumber' => $grandinnumber, 'payment_type' => $payment_type, 'totals' => $total, 'trans' => $trans, 'enctrans' => $enctrans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'transaction_id' => $transaction_id, 'admin_name' => $adminname));
        // return $pdf->stream('reciept.pdf');
    }

    public function salesorderPDF($page, $transaction_id, UserService $userService, salesQuotService $salesquot)
    {
        $salesorder_quot_data = $salesquot->SalesQuot($page, $transaction_id, $userService, $salesquot);

        if ($page == 'sales_order' || $page == 'salesorderdraft' || $page == 'quot_to_salesorder') {
            $filename = uniqid('salesorderinvoice_').'.pdf';
        } elseif ($page == 'quotation' || $page == 'quotationdraft' || $page == 'clone_quotation') {
            $filename = uniqid('quotation_invoice_').'.pdf';
        } elseif ($page == 'performance_invoice' || $page == 'performadraft') {
            $filename = uniqid('performance_invoice_').'.pdf';
        }

        $pdf = \PDF::loadView('/pdf/salesorderpdf', $salesorder_quot_data);
        // return $pdf->stream('reciept.pdf');

        return $pdf->download($filename.'.pdf');
        // return $pdf->stream($filename, array("Attachment" => false));
    }

   public function purchaseorderPDF($purchase_o_id)
    {
        // if (session()->missing('softwareuser') && session()->missing('adminuser')) {
        //     return redirect('/');
        // }
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
        $shopid = Session('softwareuser');
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

        $supplier_id = PurchaseOrder::where('purchase_order_id', $purchase_o_id)
            ->pluck('supplier_id')
            ->first();

        $trn_supp = Supplier::Where('id', $supplier_id)
            ->pluck('trn_number')
            ->first();

        $delivery_date = PurchaseOrder::where('purchase_order_id', $purchase_o_id)
            ->pluck('delivery_date')
            ->first();

        // $admin_address = Adminuser::Where('id', $adminid)
        //     ->pluck('address')
        //     ->first();
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
            
            $email = DB::table('branches')
            ->where('id', $branch)
            ->pluck('email')
            ->first();
            
        // Generate a unique filename for the PDF
        $filename = uniqid('purchaseorderinvoice_').'.pdf';

        $pdf = \PDF::loadView('/pdf/purchaseorderreceipt', ['email'=>$email,'logo'=>$logo,'company'=>$company,'Address'=>$Address,'tax'=>$tax,'details' => $dataplan, 'vat' => $vat, 'grandinnumber' => $grandinnumber, 'payment_type' => $payment_type, 'trans' => $trans, 'enctrans' => $trans, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'admintrno' => $admintrno, 'billno' => $billno, 'supplier' => $supplier, 'trn_supp' => $trn_supp, 'delivery_date' => $delivery_date,'branch'=>$branch]);

        // return $pdf->download('reciept.pdf');

        // return $pdf->stream($filename, array("Attachment" => false));

        return $pdf->download($filename.'.pdf');
    }

  public function deliveryNotePDF($transaction_id)
    {
        // if (session()->missing('softwareuser') && session()->missing('adminuser')) {
        //     return redirect('/');
        // }
        $dataplan = DB::table('delivery_notes')
            ->select(DB::raw('delivery_notes.product_name as product_name,delivery_notes.product_id as product_id,delivery_notes.quantity as quantity,delivery_notes.mrp as mrp,delivery_notes.price as price,delivery_notes.fixed_vat as fixed_vat,delivery_notes.vat_amount as vat_amount,delivery_notes.total_amount as total_amount, delivery_notes.unit as unit'))
            ->where('delivery_notes.transaction_id', $transaction_id)
            ->get();

        $total = DeliveryNote::select(
            DB::raw('SUM(price) as total')
        )
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

        $shopid = Session('softwareuser');
        $adminid = Softwareuser::Where('id', $shopid)
            ->pluck('admin_id')
            ->first();

        $shopdata = Adminuser::Where('id', $adminid)
            ->get();

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $total = DeliveryNote::select(
            DB::raw('SUM(price) as total')
        )
            ->where('transaction_id', $transaction_id)
            ->pluck('total')
            ->first();

        $vat = DeliveryNote::select(
            DB::raw('SUM(vat_amount) as vat')
        )
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

            $admin_address = Adminuser::Where('id', $adminid)
            ->pluck('address')
            ->first();
            $name = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();
            $tel = DB::table('branches')
            ->where('id', $branch)
            ->pluck('mobile')
            ->first();
            $po_box = DB::table('branches')
            ->where('id', $branch)
            ->pluck('po_box')
            ->first();

         $email = DB::table('branches')
            ->where('id', $branch)
            ->pluck('email')
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
        // Generate a unique filename for the PDF
        $filename = uniqid('deliverynoteinvoice_').'.pdf';

        $pdf = \PDF::loadView('/pdf/deliverynote_pdf', ['email'=>$email,'company'=>$company,'details' => $dataplan,'name' => $name,'logo' => $logo,'Address' => $Address, 'vat' => $vat, 'grandinnumber' => $grandinnumber, 'payment_type' => $payment_type, 'totals' => $total, 'trans' => $trans, 'enctrans' => $trans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'admintrno' => $admintrno, 'emailadmin' => $emailadmin, 'billphone' => $billphone, 'billemail' => $billemail, 'location' => $location, 'area' => $area, 'villa_no' => $villa_no, 'flat_no' => $flat_no, 'land_mark' => $land_mark, 'delivery_date' => $delivery_date, 'admin_address' => $admin_address,'branch'=>$branch]);
        // return $pdf->stream('reciept.pdf');

        // return $pdf->download('reciept.pdf');

        // return $pdf->stream($filename, array("Attachment" => false));

        return $pdf->download($filename.'.pdf');
    }

    // credit statement (transaction page)

    // public function credit_statement_pdf($credit_id)
    // {
    //     if (session()->missing('softwareuser') && session()->missing('adminuser')) {
    //         return redirect('/');
    //     }

    //     $item = DB::table('softwareusers')
    //         ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
    //         ->where('user_id', Session('softwareuser'))
    //         ->get();

    //     // $salesdata = DB::table('credit_transactions')
    //     //     ->select(DB::raw("*"))
    //     //     ->where('credituser_id', $credit_id)
    //     //     ->get();

    //     $salesdata = DB::table('credit_transactions')
    //         ->select(
    //             'credit_transactions.*',
    //             DB::raw("(SELECT created_at FROM buyproducts WHERE transaction_id COLLATE utf8mb4_general_ci = credit_transactions.transaction_id LIMIT 1) as transaction_date")
    //         )
    //         ->where('credit_transactions.credituser_id', $credit_id)
    //         ->get();

    //     $userid = Session('softwareuser');
    //     $adminid = Softwareuser::Where('id', $userid)
    //         ->pluck('admin_id')
    //         ->first();
    //     $currency = Adminuser::Where('id', $adminid)
    //         ->pluck('currency')
    //         ->first();

    //     $lastTransaction_for_due = DB::table('credit_transactions')
    //         ->where('credituser_id', $credit_id)
    //         ->orderBy('created_at', 'desc')
    //         ->first();

    //     $final_due = $lastTransaction_for_due->updated_balance;

    //     $credit_name = DB::table('creditusers')
    //         ->where('id', $credit_id)
    //         ->pluck('name')
    //         ->first();

    //     $admintrno = Adminuser::Where('id', $adminid)
    //         ->pluck('cr_number')
    //         ->first();

    //     $po_box = Adminuser::Where('id', $adminid)
    //         ->pluck('po_box')
    //         ->first();
    //     $tel = Adminuser::Where('id', $adminid)
    //         ->pluck('phone')
    //         ->first();

    //     $branch = DB::table('softwareusers')
    //         ->where('id', Session('softwareuser'))
    //         ->pluck('location')
    //         ->first();

    //     $branchname = DB::table('branches')
    //         ->where('id', $branch)
    //         ->pluck('location')
    //         ->first();

    //     $emailadmin = Adminuser::Where('id', $adminid)
    //         ->pluck('email')
    //         ->first();

    //     $adminname = Adminuser::Where('id', $adminid)
    //         ->pluck('name')
    //         ->first();

    //     $credit_location = DB::table('creditusers')
    //         ->where('id', $credit_id)
    //         ->pluck('location')
    //         ->first();

    //     $credit_branchname = DB::table('branches')
    //         ->where('id', $credit_location)
    //         ->pluck('location')
    //         ->first();

    //     $credit_trn = DB::table('creditusers')
    //         ->where('id', $credit_id)
    //         ->pluck('trn_number')
    //         ->first();

    //     $credit_phone = DB::table('creditusers')
    //         ->where('id', $credit_id)
    //         ->pluck('phone')
    //         ->first();

    //     $credit_email = DB::table('creditusers')
    //         ->where('id', $credit_id)
    //         ->pluck('email')
    //         ->first();

    //     $credit_start_row = DB::table('credit_transactions')
    //         ->where('credituser_id', $credit_id)
    //         ->orderBy('created_at', 'asc')
    //         ->first();

    //     $credit_start_date = $credit_start_row->created_at;

    //     $credit_end_date = $lastTransaction_for_due->created_at;

    //     $credit_first_due = $credit_start_row->due;

    //     $total_invoice = DB::table('credit_transactions')
    //         ->select(DB::raw(" SUM(Invoice_due) as invoice"))
    //         ->where('credituser_id', $credit_id)
    //         ->pluck('invoice')
    //         ->first();

    //     $total_payment = DB::table('credit_transactions')
    //         ->select(DB::raw(" SUM(collected_amount) as collected_amount"))
    //         ->where('credituser_id', $credit_id)
    //         ->pluck('collected_amount')
    //         ->first();

    //     $total_creditnote = DB::table('credit_transactions')
    //         ->select(DB::raw(" SUM(credit_note) as credit_note"))
    //         ->where('credituser_id', $credit_id)
    //         ->pluck('credit_note')
    //         ->first();

    //     $admin_address = Adminuser::Where('id', $adminid)
    //         ->pluck('address')
    //         ->first();

    //     $pdf = PDF::loadView('/pdf/credit_statement_transaction_pdf', array('users' => $item, 'salesdata' => $salesdata, 'finaldue' => $final_due, 'currency' => $currency, 'credit_id' => $credit_id, 'admintrno' => $admintrno, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'emailadmin' => $emailadmin, 'adminname' => $adminname, 'credit_name' => $credit_name, 'credit_branchname' => $credit_branchname, 'credit_trn' => $credit_trn, 'credit_phone' => $credit_phone, 'credit_email' => $credit_email, 'credit_start_date' => $credit_start_date, 'credit_end_date' => $credit_end_date, 'credit_first_due' => $credit_first_due, 'total_invoice' => $total_invoice, 'total_payment' => $total_payment, 'total_creditnote' => $total_creditnote, 'admin_address' => $admin_address));

    //     return $pdf->download('Credit Transaction Statement of' . $credit_name . '.pdf');
    // }
   public function credit_statement_pdf(Request $request,$credit_id)
    {
        if (Session::has('softwareuser')) {
        } elseif (Session::has('adminuser')) {
        } else {
            return redirect('userlogin'); // or 'adminlogin' based on your logic
        }

        $adminid = null;

        // Query to fetch credit transactions for PDF
        $request->validate([
    'start_date' => 'nullable|date',
    'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);

    // Initialize query builders without date conditions
    $creditQuery = CreditTransaction::where('credituser_id', $credit_id);
    $cashQuery = CashTransStatement::where('cash_user_id', $credit_id);

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



        if (Session::has('softwareuser')) {
            $userid = Session::get('softwareuser');
            $item = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', Session('softwareuser'))
                ->get();

            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();

            $branch = Softwareuser::locationById($userid);
            $branchname = DB::table('branches')
                ->where('id', $branch)
                ->pluck('location')
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
        } elseif (Session::has('adminuser')) {
            $adminid = Session::get('adminuser');
            $item = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', Session('adminuser'))
                ->get();
                $admintrno = Adminuser::Where('id', $adminid)
                ->pluck('cr_number')
                ->first();

            $po_box = Adminuser::Where('id', $adminid)
                ->pluck('po_box')
                ->first();
                $tel = Adminuser::Where('id', $adminid)
                ->pluck('phone')
                ->first();

                $adminname = Adminuser::Where('id', $adminid)
                ->pluck('name')
                ->first();
                $admin_address = Adminuser::Where('id', $adminid)
                    ->pluck('address')
                    ->first();
        }
 if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $credit_name = DB::table('creditusers')
            ->where('id', $credit_id)
            ->pluck('name')
            ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
            ->first();


        $credit_location = DB::table('creditusers')
            ->where('id', $credit_id)
            ->pluck('location')
            ->first();

        $credit_branchname = DB::table('branches')
            ->where('id', $credit_location)
            ->pluck('location')
            ->first();

        $credit_trn = DB::table('creditusers')
            ->where('id', $credit_id)
            ->pluck('trn_number')
            ->first();

        $credit_phone = DB::table('creditusers')
            ->where('id', $credit_id)
            ->pluck('phone')
            ->first();

        $credit_email = DB::table('creditusers')
            ->where('id', $credit_id)
            ->pluck('email')
            ->first();

        $credit_start_row = DB::table('credit_transactions')
            ->where('credituser_id', $credit_id)
            ->orderBy('created_at', 'asc')
            ->first();

        $credit_start_date = $credit_start_row->created_at ?? null;


        $lastTransaction_for_due = DB::table('credit_transactions')
            ->where('credituser_id', $credit_id)
            ->orderBy('created_at', 'desc')
            ->first();

        $credit_end_date = $lastTransaction_for_due->created_at ?? null;


        $credit_first_due = DB::table('credit_transactions')
            ->select(DB::raw('due'))
            ->where('credituser_id', $credit_id)
            ->whereDate('credit_transactions.created_at', $startDate.' 00:00:00')
            ->orderBy('created_at', 'asc')
            ->pluck('due')
            ->first();

        $TotalsQuery = DB::table('credit_transactions')
            ->select(
                DB::raw('SUM(Invoice_due) as invoice'),
                DB::raw('SUM(collected_amount) as collected_amount'),
                DB::raw('SUM(credit_note) as credit_note')
            )
            ->where('credituser_id', $credit_id);

        // Apply date filters if provided
        if ($startDate && $endDate) {
            $TotalsQuery->whereBetween('created_at', [
                $startDate.' 00:00:00',
                $endDate.' 23:59:59',
            ]);
        }

        $results = $TotalsQuery->first();

        $credit_first_due = $credit_first_due ?? 0;
        $total_invoice = $results->invoice ?? 0;
        $total_payment = $results->collected_amount ?? 0;
        $total_creditnote = $results->credit_note ?? 0;

        $convertedenddate = Carbon::parse($endDate);

        $final_due = DB::table('credit_transactions')
            ->select('updated_balance')
            ->where('credituser_id', $credit_id)
            ->whereDate('created_at', '<=', $convertedenddate)
            ->orderBy('created_at', 'desc')
            ->pluck('updated_balance')
            ->first();



        $shopdata = Adminuser::where('id', $adminid)->first();

        if (Session('adminuser')) {
            $branchname = DB::table('branches')
                ->where('id', $credit_location)
                ->pluck('location')
                ->first();
        }


        if (Session('softwareuser')) {
            $options = [
                'users' => $item,
            'allTransactions' => $allTransactions,
            'finaldue' => $final_due,
            'currency' => $currency,
            'credit_id' => $credit_id,
            'admintrno' => $admintrno,
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'emailadmin' => $emailadmin,
            // 'adminname' => $adminname,
            'credit_name' => $credit_name,
            'credit_branchname' => $credit_branchname,
            'credit_trn' => $credit_trn,
            'credit_phone' => $credit_phone,
            'credit_email' => $credit_email,
            'credit_start_date' => $credit_start_date,
            'credit_end_date' => $credit_end_date,
            'credit_first_due' => $credit_first_due,
            'total_invoice' => $total_invoice,
            'total_payment' => $total_payment,
            'total_creditnote' => $total_creditnote,
            // 'admin_address' => $admin_address,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'shopdata' => $shopdata,
                   'logo'=>$logo,
            'company'=>$company,
            'Address'=>$Address,
            ];
        } elseif (Session('adminuser')) {
            $options = [
                'users' => $item,
            'allTransactions' => $allTransactions,
            'finaldue' => $final_due,
            'currency' => $currency,
            'credit_id' => $credit_id,
            'admintrno' => $admintrno,
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'emailadmin' => $emailadmin,
            'adminname' => $adminname,
            'credit_name' => $credit_name,
            'credit_branchname' => $credit_branchname,
            'credit_trn' => $credit_trn,
            'credit_phone' => $credit_phone,
            'credit_email' => $credit_email,
            'credit_start_date' => $credit_start_date,
            'credit_end_date' => $credit_end_date,
            'credit_first_due' => $credit_first_due,
            'total_invoice' => $total_invoice,
            'total_payment' => $total_payment,
            'total_creditnote' => $total_creditnote,
            'admin_address' => $admin_address,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'shopdata' => $shopdata,
            //        'logo'=>$logo,
            // 'company'=>$company,
            // 'Address'=>$Address,
            ];
        }

        $pdf = \PDF::loadView('/pdf/credit_statement_transaction_pdf', $options);

        return $pdf->stream('Credit Transaction Statement of'.$credit_name.'.pdf');
    }

     public function credit_supplier_statement_pdf(Request $request,$credit_supplier_id)
    {
          if (Session::has('softwareuser')) {
        } elseif (Session::has('adminuser')) {
        } else {
            return redirect('userlogin'); // or 'adminlogin' based on your logic
        }

        // Initialize $adminid variable
        $adminid = null;

             $request->validate([
    'start_date' => 'nullable|date',
    'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);

    // Initialize query builders without date conditions
    $creditQuery = CreditSupplierTransaction::where('credit_supplier_id', $credit_supplier_id);
    $cashQuery = CashSupplierTransaction::where('cash_supplier_id', $credit_supplier_id);

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

        if (Session::has('softwareuser')) {
            $userid = Session::get('softwareuser');
            $item = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', Session('softwareuser'))
                ->get();

            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();
            $branch = Softwareuser::locationById($userid);

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

        } elseif (Session::has('adminuser')) {
            $adminid = Session::get('adminuser');
            $item = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', Session('adminuser'))
                ->get();
                $admintrno = Adminuser::Where('id', $adminid)
                ->pluck('cr_number')
                ->first();

            $po_box = Adminuser::Where('id', $adminid)
                ->pluck('po_box')
                ->first();
                $tel = Adminuser::Where('id', $adminid)
                ->pluck('phone')
                ->first();

                $adminname = Adminuser::Where('id', $adminid)
                ->pluck('name')
                ->first();
                $admin_address = Adminuser::Where('id', $adminid)
                    ->pluck('address')
                    ->first();
        }
 if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $credit_supplier_name = DB::table('suppliers')
            ->where('id', $credit_supplier_id)
            ->where('location', $branch)
            ->pluck('name')
            ->first();

        // $admintrno = Adminuser::Where('id', $adminid)
        //     ->pluck('cr_number')
        //     ->first();

        // $po_box = Adminuser::Where('id', $adminid)
        //     ->pluck('po_box')
        //     ->first();
        // $tel = Adminuser::Where('id', $adminid)
        //     ->pluck('phone')
        //     ->first();

        $branchname = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
            ->first();

        // $adminname = Adminuser::Where('id', $adminid)
        //     ->pluck('name')
        //     ->first();

        $credit_location = DB::table('suppliers')
            ->where('id', $credit_supplier_id)
            ->pluck('location')
            ->first();

        $credit_branchname = DB::table('branches')
            ->where('id', $credit_location)
            ->pluck('location')
            ->first();

        $credit_phone = DB::table('suppliers')
            ->where('id', $credit_supplier_id)
            ->pluck('mobile')
            ->first();

        $credit_email = DB::table('suppliers')
            ->where('id', $credit_supplier_id)
            ->pluck('email')
            ->first();

        // $admin_address = Adminuser::Where('id', $adminid)
        //     ->pluck('address')
        //     ->first();

        $credit_start_row = DB::table('credit_supplier_transactions')
            ->where('credit_supplier_id', $credit_supplier_id)
            ->orderBy('created_at', 'asc')
            ->first();

        $credit_start_date = $credit_start_row->created_at ?? null;;

        $lastTransaction_for_due = DB::table('credit_supplier_transactions')
            ->where('credit_supplier_id', $credit_supplier_id)
            ->orderBy('created_at', 'desc')
            ->first();

        // $final_due = $lastTransaction_for_due->updated_balance;

        $credit_end_date = $lastTransaction_for_due->created_at ?? null;;

        // $credit_first_due = $credit_start_row->due;

        $credit_first_due = DB::table('credit_supplier_transactions')
            ->select(DB::raw('due'))
            ->where('credit_supplier_id', $credit_supplier_id)
            ->whereDate('credit_supplier_transactions.created_at', $startDate.' 00:00:00')
            ->orderBy('created_at', 'asc')
            ->pluck('due')
            ->first();

        $TotalsQuery = DB::table('credit_supplier_transactions')
            ->select(
                DB::raw('SUM(Invoice_due) as invoice'),
                DB::raw('SUM(collectedamount) as collectedamount'),
                DB::raw('SUM(debitnote) as debitnote')
            )
            ->where('credit_supplier_id', $credit_supplier_id);

        // Apply date filters if provided
        if ($startDate && $endDate) {
            $TotalsQuery->whereBetween('created_at', [
                $startDate.' 00:00:00',
                $endDate.' 23:59:59',
            ]);
        }

        $results = $TotalsQuery->first();

        $credit_first_due = $credit_first_due ?? 0;
        $total_invoice = $results->invoice ?? 0;
        $total_payment = $results->collectedamount ?? 0;
        $total_debitnote = $results->debitnote ?? 0;

        $convertedenddate = Carbon::parse($endDate);

        $final_due = DB::table('credit_supplier_transactions')
            ->select('updated_balance')
            ->where('credit_supplier_id', $credit_supplier_id)
            ->whereDate('updated_at', '<=', $convertedenddate)
            ->orderBy('updated_at', 'desc')
            ->pluck('updated_balance')
            ->first();

        $shopdata = Adminuser::where('id', $adminid)->first();

        // $data = [
        //     'users' => $item,
        //     'purchasedata' => $purchasedata,
        //     'finaldue' => $final_due,
        //     'currency' => $currency,
        //     'credit_supplier_id' => $credit_supplier_id,
        //     'admintrno' => $admintrno,
        //     'po_box' => $po_box,
        //     'tel' => $tel,
        //     'branchname' => $branchname,
        //     'emailadmin' => $emailadmin,
        //     'adminname' => $adminname,
        //     'credit_supplier_name' => $credit_supplier_name,
        //     'credit_branchname' => $credit_branchname,
        //     'credit_phone' => $credit_phone,
        //     'credit_email' => $credit_email,
        //     'credit_start_date' => $credit_start_date,
        //     'credit_end_date' => $credit_end_date,
        //     'credit_first_due' => $credit_first_due,
        //     'total_invoice' => $total_invoice,
        //     'total_payment' => $total_payment,
        //     'admin_address' => $admin_address,
        //     'total_debitnote' => $total_debitnote,
        //     'shopdata' => $shopdata,
        //     'startDate' => $startDate,
        //     'endDate' => $endDate,
        //     'logo'=>$logo,
        //     'company'=>$company,
        //     'Address'=>$Address,
        // ];
        if (Session('softwareuser')) {
            $options = [
                'users' => $item,
            'allTransactions' => $allTransactions,
            'finaldue' => $final_due,
            'currency' => $currency,
            'credit_supplier_id' => $credit_supplier_id,
            'admintrno' => $admintrno,
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'emailadmin' => $emailadmin,
            // 'adminname' => $adminname,
            'credit_supplier_name' => $credit_supplier_name,
            'credit_branchname' => $credit_branchname,
            'credit_phone' => $credit_phone,
            'credit_email' => $credit_email,
            'credit_start_date' => $credit_start_date,
            'credit_end_date' => $credit_end_date,
            'credit_first_due' => $credit_first_due,
            'total_invoice' => $total_invoice,
            'total_payment' => $total_payment,
            // 'admin_address' => $admin_address,
            'total_debitnote' => $total_debitnote,
            'shopdata' => $shopdata,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'logo'=>$logo,
            'company'=>$company,
            'Address'=>$Address,
            ];
        } elseif (Session('adminuser')) {
            $options = [
                'users' => $item,
            'allTransactions' => $allTransactions,
            'finaldue' => $final_due,
            'currency' => $currency,
            'credit_supplier_id' => $credit_supplier_id,
            'admintrno' => $admintrno,
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'emailadmin' => $emailadmin,
            'adminname' => $adminname,
            'credit_supplier_name' => $credit_supplier_name,
            'credit_branchname' => $credit_branchname,
            'credit_phone' => $credit_phone,
            'credit_email' => $credit_email,
            'credit_start_date' => $credit_start_date,
            'credit_end_date' => $credit_end_date,
            'credit_first_due' => $credit_first_due,
            'total_invoice' => $total_invoice,
            'total_payment' => $total_payment,
            'admin_address' => $admin_address,
            'total_debitnote' => $total_debitnote,
            'shopdata' => $shopdata,
            'startDate' => $startDate,
            'endDate' => $endDate,
            // 'logo'=>$logo,
            // 'company'=>$company,
            // 'Address'=>$Address,
            ];
        }
        $pdf = \PDF::loadView('/pdf/credit_supplier_statement_transaction_pdf', $options);

        return $pdf->stream('Supplier_Statement_'.$credit_supplier_name.'.pdf');
    }
    
    protected ZatcaService $zatcaService;

    public function __construct(ZatcaService $zatcaService)
    {
        $this->zatcaService = $zatcaService;
    }

    /**
     * Generate ZATCA-compliant tax invoice as PDF (A4 size)
     */
    public function generatetaxPDFA4($transaction_id)
    {
        // ✅ Session check
        if (session()->missing('softwareuser') && session()->missing('adminuser')) {
            return redirect('/');
        }

        $userId   = session('softwareuser');
        $branchId = DB::table('softwareusers')->where('id', $userId)->value('location');
        $adminId  = DB::table('softwareusers')->where('id', $userId)->value('admin_id');

        // ✅ Fetch products & returns
        $buyProducts    = DB::table('buyproducts')->where('branch', $branchId)->where('transaction_id', $transaction_id)->get();
        $returnProducts = DB::table('returnproducts')->where('branch', $branchId)->where('transaction_id', $transaction_id)->get();
        $details        = $buyProducts->merge($returnProducts);

        // ✅ Customer info
        $customerName    = DB::table('buyproducts')->where('transaction_id', $transaction_id)->value('customer_name') ?? 'Walk-in Customer';
        $customerDetails = DB::table('creditusers')->where('name', $customerName)->first();
        $billingAdd      = optional($customerDetails)->billing_add ?? 'Not Provided';
        $deliveryAdd     = optional($customerDetails)->delivery_default == 1 ? $customerDetails->deli_add : null;
        $customerPostal  = optional($customerDetails)->billing_postal ?? '00000';

        // ✅ Branch & admin info
        $branch = DB::table('branches')->where('id', $branchId)->first();
        if (!$branch) {
            throw new \Exception("Branch not found for ID: $branchId");
        }

        $shopdata = DB::table('adminusers')->where('id', $adminId)->first();
        if (!$shopdata) {
            throw new \Exception("Admin user not found for ID: $adminId");
        }

        $employeeName = DB::table('softwareusers')->where('id', $userId)->value('name') ?? '';

        // ✅ Totals
        $totals = DB::table('buyproducts')
            ->selectRaw('
                SUM(price) as total_price,
                SUM(vat_amount) as total_vat,
                SUM(mrp * quantity) as total_rate,
                SUM(service_cost * quantity) as total_service_cost,
                SUM(total_amount) as total_grand,
                SUM(discount_amount * quantity) as total_discount_amt,
                SUM(totalamount_wo_discount) as total_without_discount,
                SUM(price_wo_discount) as price_without_discount
            ')
            ->where('transaction_id', $transaction_id)
            ->first();

        $totalPrice          = (float) ($totals->total_price ?? 0);
        $totalVat            = (float) ($totals->total_vat ?? 0);
        $totalRate           = (float) ($totals->total_rate ?? 0);
        $totalServiceCost    = (float) ($totals->total_service_cost ?? 0);
        $totalGrand          = (float) ($totals->total_grand ?? 0);
        $totalDiscountAmount = (float) ($totals->total_discount_amt ?? 0);
        $totalWithoutDis     = (float) ($totals->total_without_discount ?? 0);
        $priceWithoutDis     = (float) ($totals->price_without_discount ?? 0);

        // ✅ Returns / discounts
        $returnGrand        = DB::table('returnproducts')->where('transaction_id', $transaction_id)->sum('total_amount') ?? 0;
        $returnMainDiscount = DB::table('returnproducts')->where('transaction_id', $transaction_id)->value('total_discount_amount') ?? 0;
        $returnTotal        = $returnGrand - $returnMainDiscount;

        $mainDiscount     = DB::table('buyproducts')->where('transaction_id', $transaction_id)->value('total_discount_amount') ?? 0;
        $creditNoteAmount = DB::table('buyproducts')->where('transaction_id', $transaction_id)->value('credit_note') ?? 0;

        $remaining     = ($totalGrand - $mainDiscount) - $returnTotal;
        $grandInNumber = $remaining > $creditNoteAmount ? $remaining - $creditNoteAmount : 0;

        // Ensure proper rounding for ZATCA compliance
        $grandInNumber = round($grandInNumber, 2);
        $totalVat = round($totalVat, 2);
        $totalPrice = round($totalPrice, 2);

        // ✅ Convert to words (Saudi Riyals & Halalas)
        $grandFormatted = number_format($grandInNumber, 2, '.', '');
        [$riyals, $halalas] = array_pad(explode('.', $grandFormatted), 2, 0);

        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);

        $amountInWords = ucwords($formatter->format((int) $riyals)) . ' Saudi Riyals';
        $amountInWords .= (int) $halalas > 0
            ? ' and ' . ucwords($formatter->format((int) $halalas)) . ' Halalas'
            : ' Only';

        // ✅ Payment type
        $paymentType = DB::table('buyproducts')
            ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
            ->where('buyproducts.transaction_id', $transaction_id)
            ->value('payment.type') ?? '';

        // ✅ IssueDate / IssueTime (UTC)
        $createdAt = DB::table('buyproducts')->where('transaction_id', $transaction_id)->value('created_at');
        try {
            $issueDateTime = $createdAt
                ? new \DateTimeImmutable($createdAt, new \DateTimeZone('UTC'))
                : new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            Log::warning("ZATCA: Could not parse created_at for transaction $transaction_id. Using current time. Error: " . $e->getMessage());
            $issueDateTime = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        }

        // ✅ Customer TRN & transaction type
        $customerTrn = DB::table('buyproducts')->where('transaction_id', $transaction_id)->value('trn_number') ?? '';
        $transactionType = !empty($customerTrn) ? 'B2B' : 'B2C';

        // --- ZATCA Critical Data ---
        $lastInvoiceRecord = DB::table('branches')
            ->where('id', $branchId)
            ->first(['last_invoice_counter', 'last_invoice_hash']);

        $branchNumber = $branch->branch_number ?? '0000';

        if (!$lastInvoiceRecord) {
            // Initialize if not exists
            DB::table('branches')->where('id', $branchId)->update([
                'last_invoice_counter' => 0,
                'last_invoice_hash' => ''
            ]);
            $currentInvoiceCounter = 1;
            $previousInvoiceHash = '';
        } else {
            $currentInvoiceCounter = (int)($lastInvoiceRecord->last_invoice_counter ?? 0) + 1;
            $previousInvoiceHash = $lastInvoiceRecord->last_invoice_hash ?? '';
        }

        // Validate VAT number format (BR-KSA-40)
        $supplierVat = $branch->tr_no ?? '';
        if (!empty($supplierVat)) {
            // Ensure VAT number has 15 digits starting and ending with '3'
            if (strlen($supplierVat) !== 15 || substr($supplierVat, 0, 1) !== '3' || substr($supplierVat, -1) !== '3') {
                Log::channel('zatca')->warning('VAT number format invalid, attempting to format', [
                    'current_vat' => $supplierVat
                ]);
                $middle = substr($supplierVat, 0, 13);
                $middle = str_pad($middle, 13, '0', STR_PAD_RIGHT);
                $supplierVat = '3' . $middle . '3';
            }
        }

        // --- Prepare invoice data ---
        $invoiceData = [
            'id' => $transaction_id,
            // ✅ ZATCA-compliant invoice number format: [BranchNumber][5-digit Counter]
            'invoice_number' => sprintf('%s%05d', $branch->branch_number ?? '0000', $currentInvoiceCounter),
            'uuid' => (string) Str::uuid(),
            'issue_datetime' => $issueDateTime,
            'supplier_name' => $branch->company ?? 'Not Provided',
            'supplier_id' => $supplierVat,
            'supplier_crn' => $branch->commercial_registration_number ?? '',
            'supplier_branch_no'=> $branch->branch_number ?? '',
            'supplier_street' => $branch->address ?? 'Not Provided',
            'supplier_building'=> str_pad(substr($branch->supplier_building ?? '0000', 0, 4), 4, '0', STR_PAD_LEFT),
            'supplier_city' => $branch->location ?? 'Not Provided',
            'supplier_postal' => $branch->po_box ?? ($branch->supplier_postal ?? '00000'),
            'supplier_country' => $branch->supplier_country ?? 'SA',
            'buyer_name' => $customerName,
            'buyer_vat' => $customerTrn,
            'buyer_street' => $billingAdd,
            'buyer_city' => $branch->location ?? 'Not Provided',
            'buyer_postal' => $customerPostal,
            'buyer_country' => $branch->supplier_country ?? 'SA',
            'total_amount' => $grandInNumber,
            'vat_amount' => $totalVat,
            'currency' => $shopdata->currency ?? 'SAR',
            'transaction_type' => $transactionType,
            'invoice_counter' => (string) $currentInvoiceCounter,
            'previous_invoice_hash' => $previousInvoiceHash,
            'pricing_type' => 'inclusive',
            
            // ✅ ADD KSA ADDRESS FIELDS FOR ZATCA COMPLIANCE
            'supplier_plot' => $branch->plot_identification ?? 'PLOT-001',
            'supplier_province' => $branch->province ?? 'Riyadh Province',
            'supplier_province_code' => $branch->province_code ?? '01',
            'supplier_district' => $branch->district ?? 'Al Olaya',
            'supplier_additional_address_line' => $branch->additional_address_line ?? 'Building 1',
            'buyer_plot' => 'PLOT-002',
            'buyer_province' => 'Makkah Province',
            'buyer_province_code' => '02',
            'buyer_district' => 'Al Zahra',
            'buyer_additional_address_line' => 'Office 1',

            'line_items' => $details->map(function($item) use ($shopdata) {
                $quantity  = (float) ($item->quantity ?? 0);
                $unitPrice = (float) ($item->mrp ?? 0); // ← MRP IS INCLUSIVE
                $lineTotal = $quantity * $unitPrice;    // ← ALSO INCLUSIVE
                $vatPercent = (float) ($item->fixed_vat ?? $shopdata->tax ?? 0);
                $vatAmount  = $lineTotal * ($vatPercent / 100); // ← CALCULATED FROM INCLUSIVE

                $quantity = round($quantity, 2);
                $unitPrice = round($unitPrice, 2);
                $lineTotal = round($lineTotal, 2);
                $vatPercent = round($vatPercent, 2);
                $vatAmount = round($vatAmount, 2);

                return [
                    'description' => $item->product_name ?? 'Product',
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $lineTotal,
                    'vat_percent' => $vatPercent,
                    'vat_amount' => $vatAmount,
                    'unit_code' => 'PCE',
                    'vat_category_code' => 'S',
                ];
            })->toArray(),

            // ✅ REQUIRED FOR ZATCA PHASE 2 — MUST BE PRESENT
            'invoice_type_code'        => '388',
            'invoice_transaction_code' => $transactionType === 'B2B' ? '01000000' : '02000000',
            'customization_id'         => 'urn:cen.eu:en16931:2017#compliant#urn:sa.gov.zatca:invoice',
            'profile_id'               => 'reporting:1.0',
        ];

        Log::channel('zatca')->debug('Final line items before generation', [
            'line_items_count' => count($invoiceData['line_items']),
            'first_item_unit_price' => ($invoiceData['line_items'][0]['unit_price'] ?? null),
            'first_item_total' => ($invoiceData['line_items'][0]['total'] ?? null),
            'first_item_vat_percent' => ($invoiceData['line_items'][0]['vat_percent'] ?? null),
        ]);

        // --- ZATCA Compliance: Certificate + XML + Signing ---
        $qrBase64   = null;
        $signedXml  = null;
        $invoiceHash = null;
        $zatcaErrors = [];
        $zatcaWarnings = [];

        try {
            $complianceCertPath = storage_path('zatca/dev/ZATCA_certificate_data.json');
            
            if (!file_exists($complianceCertPath)) {
                throw new \Exception("ZATCA certificate JSON not found at: " . $complianceCertPath);
            }

            $jsonData = json_decode(file_get_contents($complianceCertPath), true, 512, JSON_THROW_ON_ERROR);
            
            $certificateContent = $jsonData['certificate'] ?? '';
            $privateKeyContent = $jsonData['private_key'] ?? '';
            $secret = $jsonData['secret'] ?? '';

            if (empty($certificateContent) || !str_contains($certificateContent, '-----BEGIN CERTIFICATE-----')) {
                throw new \Exception("Invalid certificate format.");
            }

            if (empty($privateKeyContent) || !str_contains($privateKeyContent, '-----BEGIN PRIVATE KEY-----')) {
                throw new \Exception("Invalid private key format.");
            }

            if (empty($secret)) {
                throw new \Exception("Certificate secret is required");
            }

            Log::channel('zatca')->debug('Certificate loaded successfully', [
                'certificate_length' => strlen($certificateContent),
                'private_key_length' => strlen($privateKeyContent),
                'has_secret' => !empty($secret)
            ]);

            $certificate = new Certificate($certificateContent, $privateKeyContent, $secret);

            // ✅ STEP 1: Create generators
            $qrGenerator = app()->make(\App\Services\Zatca\InvoiceQrGenerator::class);
            $signerService = app()->make(\App\Services\Zatca\InvoiceSignerService::class);
            $invoiceGenerator = new \App\Services\Zatca\InvoiceXmlGenerator($signerService, $qrGenerator);

            // ✅ STEP 2: USE SINGLE GENERATION FLOW (not dual generation)
            $generationResult = $invoiceGenerator->generate($invoiceData, $certificate, $branchNumber);

            if (!empty($generationResult['errors'])) {
                throw new \Exception("ZATCA generation failed: " . implode(', ', $generationResult['errors']));
            }

            // ✅ STEP 3: Extract results from single generation
            $signedXmlContent = file_get_contents($generationResult['signed_xml']);
            $qrTlvBase64 = $generationResult['qr_base64'];
            $qrPngBase64 = $generationResult['qr_image_base64'] ?? '';
            $invoiceHash = $generationResult['invoice_hash'];

            Log::channel('zatca')->info('ZATCA SINGLE GENERATION COMPLETED SUCCESSFULLY', [
                'transaction_id' => $transaction_id,
                'invoice_counter_used' => $currentInvoiceCounter,
                'has_qr_code' => !empty($qrTlvBase64),
                'has_invoice_hash' => !empty($invoiceHash),
                'generation_flow' => 'SINGLE_PASS'
            ]);

            // ✅ STEP 4: Use final signed XML and QR PNG for display
            $signedXml = $signedXmlContent;
            $qrBase64 = $qrPngBase64; // ← For PDF display (PNG), NOT TLV

            // ✅ STEP 5: Persist latest counter and hash into branch record
            DB::table('branches')->where('id', $branchId)->update([
                'last_invoice_counter' => $currentInvoiceCounter,
                'last_invoice_hash'    => $invoiceHash,
                'updated_at'           => now(),
            ]);

            Log::channel('zatca')->info('ZATCA invoice generated and data stored successfully.', [
                'transaction_id' => $transaction_id,
                'invoice_counter_used' => $currentInvoiceCounter,
                'invoice_hash_stored' => !is_null($invoiceHash),
                'branch_id' => $branchId
            ]);

        } catch (\Throwable $e) {
            $zatcaErrors[] = "ZATCA generation process failed: " . $e->getMessage();
            Log::channel('zatca')->error("ZATCA generation process failed: " . $e->getMessage(), [
                'transaction_id' => $transaction_id,
                'trace' => $e->getTraceAsString()
            ]);
        }

        // ✅ Prepare data for Blade
        $data = [
            'details'       => $details,
            'custs'         => $customerName,
            'branchname'    => $branch->location ?? '',
            'branch'        => $branch,
            'shopdata'      => $shopdata,
            'currency'      => $shopdata->currency ?? 'SAR',
            'tax'           => $shopdata->tax ?? 15,
            'date'          => $createdAt ? Carbon::parse($createdAt)->format('d-m-Y') : now()->format('d-m-Y'),
            'amountinwords' => $amountInWords,
            'trans'         => $transaction_id,
            'enctrans'      => Crypt::encrypt($transaction_id),
            'payment_type'  => $paymentType,
            'grandinnumber' => $grandInNumber,
            'vat'           => $totalVat,
            'total'         => $totalPrice,
            'discount_amt'  => $totalDiscountAmount,
            'Main_discount_amt' => $mainDiscount,
            'rate'          => $totalRate,
            'service_cost'  => $totalServiceCost,
            'grand_wo_dis'  => $totalWithoutDis,
            'price_wo_dis'  => $priceWithoutDis,
            'credit_note_amount'=> $creditNoteAmount,
            'returntotal'   => $returnTotal,
            'bankDetails'   => DB::table('bank')
                                    ->where('account_name', DB::table('buyproducts')->where('transaction_id', $transaction_id)->value('account_name'))
                                    ->where('is_default',1)->first(),
            'deliveryAdd'   => $deliveryAdd,
            'billingAdd'    => $billingAdd,
            'employeename'  => $employeeName,
            'tel'           => $branch->mobile ?? '',
            'po_box'        => $branch->po_box ?? '',
            'admintrno'     => $branch->tr_no ?? '',
            'logo'          => $branch->logo ?? '',
            'company'       => $branch->company ?? '',
            'arabic_name'   => $branch->arabic_name ?? '',
            'emailadmin'    => $branch->email ?? '',
            'Address'       => $branch->address ?? '',
            'trn_number'    => $customerTrn,
            'billphone'     => DB::table('buyproducts')->where('transaction_id', $transaction_id)->value('phone') ?? '',
            'billemail'     => DB::table('buyproducts')->where('transaction_id', $transaction_id)->value('email') ?? '',
            'vat_type'      => DB::table('buyproducts')->where('transaction_id', $transaction_id)->value('vat_type') ?? '',
            'admin_address' => $shopdata->address ?? '',
            'qrCodeBase64'  => $qrBase64, // ✅ This is the PNG image — for display on PDF
            'signedXml'     => $signedXml,
            'zatcaErrors'   => $zatcaErrors,
            'zatcaWarnings' => $zatcaWarnings,
            'invoice_counter' => $currentInvoiceCounter,

            // ✅ NEW: Pass ZATCA-compliant invoice number (not transaction_id)
            'display_invoice_number' => sprintf('%s%05d', $branch->branch_number ?? '0000', $currentInvoiceCounter),

            // ✅ NEW: Pass UUID for display
            'uuid' => $invoiceData['uuid'],

            // ✅ NEW: Pass previous invoice hash for display
            'previous_invoice_hash' => $previousInvoiceHash,

            // ✅ NEW: Pass transaction type for display
            'transactionType' => $transactionType,

            // ✅ NEW: Pass formatted issue datetime (UTC) for display
            'issue_datetime_utc' => $issueDateTime->format('Y-m-d H:i:s') . ' UTC',
        ];

        // ✅ Render PDF
        $html = View::make('pdf.recieptwithtax_A4', $data)->render();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'autoLangToFont' => true,
            'autoScriptToLang' => true,
            'setAutoBottomMargin' => 'stretch',
            'default_font' => 'amiri',
            'tempDir'     => storage_path('mpdf')
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('invoice_' . $transaction_id . '.pdf', 'I');
    }

    /**
     * Clean up temporary XML files
     */
    // private function cleanupTemporaryFiles(array $files): void
    // {
    //     foreach ($files as $file) {
    //         if (file_exists($file)) {
    //             unlink($file);
    //             Log::channel('zatca')->debug('Cleaned up temporary file', ['file' => basename($file)]);
    //         }
    //     }
    // }

    // public function salesorderPrint($transaction_id)
    // {
    //     // if (session()->missing('softwareuser') && session()->missing('adminuser')) {
    //     //     return redirect('/');
    //     // }
    //     $dataplan = DB::table('sales_orders')
    //         ->select(DB::raw("sales_orders.product_name as product_name,sales_orders.product_id as product_id,sales_orders.quantity as quantity,sales_orders.mrp as mrp,sales_orders.price as price,sales_orders.fixed_vat as fixed_vat,sales_orders.vat_amount as vat_amount,sales_orders.total_amount as total_amount, sales_orders.unit as unit"),)
    //         ->where('sales_orders.transaction_id', $transaction_id)
    //         ->get();
    //     $total = SalesOrder::select(
    //         DB::raw("SUM(price) as total")
    //     )
    //         ->where('transaction_id', $transaction_id)
    //         ->get();

    //     $branch = DB::table('softwareusers')
    //         ->where('id', Session('softwareuser'))
    //         ->pluck('location')
    //         ->first();

    //     $trans = $transaction_id;

    //     $enctrans = Crypt::encrypt($trans);

    //     $custs = DB::table('sales_orders')
    //         ->where('transaction_id', $trans)
    //         ->pluck('customer_name')
    //         ->first();
    //     $branchname = DB::table('branches')
    //         ->where('id', $branch)
    //         ->pluck('location')
    //         ->first();
    //     $userid = Session('softwareuser');
    //     $item = DB::table('softwareusers')
    //         ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
    //         ->where('user_id', $userid)
    //         ->get();
    //     $shopid = Session('softwareuser');
    //     $adminid = Softwareuser::Where('id', $shopid)
    //         ->pluck('admin_id')
    //         ->first();
    //     $shopdata = Adminuser::Where('id', $adminid)
    //         ->get();
    //     $currency = Adminuser::Where('id', $adminid)
    //         ->pluck('currency')
    //         ->first();
    //     $total = SalesOrder::select(
    //         DB::raw("SUM(price) as total"),
    //     )
    //         ->where('transaction_id', $transaction_id)
    //         ->pluck('total')
    //         ->first();
    //     $vat = SalesOrder::select(
    //         DB::raw("SUM(vat_amount) as vat")
    //     )
    //         ->where('transaction_id', $transaction_id)
    //         ->pluck('vat')
    //         ->first();

    //     $payment_type = DB::table('sales_orders')
    //         ->leftJoin('payment', 'sales_orders.payment_type', '=', 'payment.id')
    //         ->select(DB::raw("payment.type as payment_type"))
    //         ->where('sales_orders.transaction_id', $trans)
    //         ->pluck('payment_type')
    //         ->first();
    //     $date = DB::table('sales_orders')
    //         ->select(DB::raw("DATE(sales_orders.created_at) as date"),)
    //         ->where('transaction_id', $trans)
    //         ->pluck('date')
    //         ->first();
    //     $date = Carbon::parse($date)->format('d-m-Y');

    //     $grand = SalesOrder::where('transaction_id', $transaction_id)
    //         ->select(DB::raw("SUM(total_amount) as total_amount"),)
    //         ->pluck('total_amount')
    //         ->first();

    //     $grandinnumber = $grand;
    //     $grand = number_format($grand, 3, '.', '');
    //     $amountinwords = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    //     $amountinwords = $amountinwords->format($grand);
    //     $amountinwords = ucwords($amountinwords);
    //     $supplieddate = Carbon::now()->format('d-m-Y');
    //     $cr_num = Adminuser::Where('id', $adminid)
    //         ->pluck('cr_number')
    //         ->first();
    //     $po_box = Adminuser::Where('id', $adminid)
    //         ->pluck('po_box')
    //         ->first();
    //     $tel = Adminuser::Where('id', $adminid)
    //         ->pluck('phone')
    //         ->first();
    //     $trn_number = DB::table('sales_orders')
    //         ->where('transaction_id', $trans)
    //         ->pluck('trn_number')
    //         ->first();

    //     $admintrno = Adminuser::Where('id', $adminid)
    //         ->pluck('cr_number')
    //         ->first();

    //     $emailadmin = Adminuser::Where('id', $adminid)
    //         ->pluck('email')
    //         ->first();

    //     $billphone = SalesOrder::select(DB::raw("phone"))
    //         ->where('transaction_id', $transaction_id)
    //         ->pluck('phone')
    //         ->first();

    //     $billemail = SalesOrder::select(DB::raw("email"))
    //         ->where('transaction_id', $transaction_id)
    //         ->pluck('email')
    //         ->first();

    //     // Generate a unique filename for the PDF
    //     $filename = uniqid('salesorderinvoice_print' . $transaction_id) . '.pdf';

    //     $pdf = PDF::loadView('/pdf/salesorderpdf', array('details' => $dataplan, 'vat' => $vat, 'grandinnumber' => $grandinnumber, 'payment_type' => $payment_type, 'totals' => $total, 'trans' => $trans, 'enctrans' => $trans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'admintrno' => $admintrno, 'emailadmin' => $emailadmin, 'billphone' => $billphone, 'billemail' => $billemail));
    //     // return $pdf->stream('reciept.pdf');

    //     // return $pdf->download('reciept.pdf');

    //     return $pdf->stream($filename, array("Attachment" => false));
    // }

 public function salesorderPrint($page, $transaction_id, UserService $userService, salesQuotService $salesquot)
    {
        $salesorder_quot_data = $salesquot->SalesQuot($page, $transaction_id, $userService, $salesquot);

        if ($page == 'sales_order' || $page == 'salesorderdraft' || $page == 'quot_to_salesorder') {
            $filename = uniqid('salesorderinvoice_print'.$transaction_id).'.pdf';
        } elseif ($page == 'quotation' || $page == 'quotationdraft' || $page == 'clone_quotation') {
            $filename = uniqid('quotationinvoice_print'.$transaction_id).'.pdf';
        } elseif ($page == 'performance_invoice' || $page == 'performadraft') {
            $filename = uniqid('performance_invoice_print'.$transaction_id).'.pdf';
        }

        $pdf = \PDF::loadView('/pdf/salesorderpdf', $salesorder_quot_data);
        // return $pdf->stream('reciept.pdf');

        // return $pdf->download('reciept.pdf');

        return $pdf->stream($filename, ['Attachment' => false]);
    }

    public function deliverynotePrint($transaction_id)
    {
        // if (session()->missing('softwareuser') && session()->missing('adminuser')) {
        //     return redirect('/');
        // }
        $dataplan = DB::table('delivery_notes')
            ->select(DB::raw('delivery_notes.product_name as product_name,delivery_notes.product_id as product_id,delivery_notes.quantity as quantity,delivery_notes.mrp as mrp,delivery_notes.price as price,delivery_notes.fixed_vat as fixed_vat,delivery_notes.vat_amount as vat_amount,delivery_notes.total_amount as total_amount, delivery_notes.unit as unit'))
            ->where('delivery_notes.transaction_id', $transaction_id)
            ->get();

        $total = DeliveryNote::select(
            DB::raw('SUM(price) as total')
        )
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

        $shopid = Session('softwareuser');
        $adminid = Softwareuser::Where('id', $shopid)
            ->pluck('admin_id')
            ->first();

        $shopdata = Adminuser::Where('id', $adminid)
            ->get();

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $total = DeliveryNote::select(
            DB::raw('SUM(price) as total')
        )
            ->where('transaction_id', $transaction_id)
            ->pluck('total')
            ->first();

        $vat = DeliveryNote::select(
            DB::raw('SUM(vat_amount) as vat')
        )
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

        $admin_address = Adminuser::Where('id', $adminid)
            ->pluck('address')
            ->first();
            $name = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();
            $tel = DB::table('branches')
            ->where('id', $branch)
            ->pluck('mobile')
            ->first();
            $po_box = DB::table('branches')
            ->where('id', $branch)
            ->pluck('po_box')
            ->first();
            
             $email = DB::table('branches')
            ->where('id', $branch)
            ->pluck('email')
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
        // Generate a unique filename for the PDF
        $filename = uniqid('deliverynoteinvoiceprint_').'.pdf';

        $pdf = \PDF::loadView('/pdf/deliverynote_pdf', ['email'=>$email,'details' => $dataplan,'logo' => $logo,'company' => $company,'Address' => $Address,'name' => $name, 'vat' => $vat, 'grandinnumber' => $grandinnumber, 'payment_type' => $payment_type, 'totals' => $total, 'trans' => $trans, 'enctrans' => $trans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'admintrno' => $admintrno, 'emailadmin' => $emailadmin, 'billphone' => $billphone, 'billemail' => $billemail, 'location' => $location, 'area' => $area, 'villa_no' => $villa_no, 'flat_no' => $flat_no, 'land_mark' => $land_mark, 'delivery_date' => $delivery_date, 'admin_address' => $admin_address,'branch'=>$branch]);
        // return $pdf->stream('reciept.pdf');

        // return $pdf->download('reciept.pdf');

        return $pdf->stream($filename, ['Attachment' => false]);
    }

      public function purchaseOrderPrint($purchase_o_id)
    {
        // if (session()->missing('softwareuser') && session()->missing('adminuser')) {
        //     return redirect('/');
        // }
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
        $shopid = Session('softwareuser');
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

        $supplier_id = PurchaseOrder::where('purchase_order_id', $purchase_o_id)
            ->pluck('supplier_id')
            ->first();

        $trn_supp = Supplier::Where('id', $supplier_id)
            ->pluck('trn_number')
            ->first();

        $delivery_date = PurchaseOrder::where('purchase_order_id', $purchase_o_id)
            ->pluck('delivery_date')
            ->first();

        // $admin_address = Adminuser::Where('id', $adminid)
        //     ->pluck('address')
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
         $email = DB::table('branches')
            ->where('id', $branch)
            ->pluck('email')
            ->first();
        // Generate a unique filename for the PDF
        $filename = uniqid('purchaseorderinvoiceprint_').'.pdf';

        $pdf = \PDF::loadView('/pdf/purchaseorderreceipt', ['email'=>$email,'logo'=>$logo,'company'=>$company,'Address'=>$Address,'tax'=>$tax,'details' => $dataplan, 'vat' => $vat, 'grandinnumber' => $grandinnumber, 'payment_type' => $payment_type, 'trans' => $trans, 'enctrans' => $trans, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'admintrno' => $admintrno, 'billno' => $billno, 'supplier' => $supplier, 'trn_supp' => $trn_supp, 'delivery_date' => $delivery_date,'branch'=>$branch]);

        // return $pdf->download('reciept.pdf');

        return $pdf->stream($filename, ['Attachment' => false]);
    }

         public function generatetaxPDFA5($transaction_id)
    {
        if (session()->missing('softwareuser') && session()->missing('adminuser')) {
            return redirect('/');
        }
         $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $buyProducts = DB::table('buyproducts')
        ->select([
            'product_name',
            'service_name',
            'service_cost',
            'product_id',
            'quantity',
            'box_count',
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
        $total = Buyproduct::select(DB::raw('SUM(price) as total'))
            ->where('transaction_id', $transaction_id)
            ->get();
       
        $trans = $transaction_id;

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

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
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
        
         $arabic_name = DB::table('branches')
        ->where('id', $branch)
        ->pluck('arabic_name')
        ->first();
        
        
        $emailadmin = DB::table('branches')
        ->where('id', $branch)
        ->pluck('email')
        ->first();

        $Address = DB::table('branches')
        ->where('id', $branch)
        ->pluck('address')
        ->first();

        $trn_number = DB::table('buyproducts')
            ->where('transaction_id', $trans)
            ->pluck('trn_number')
            ->first();

        // $admintrno = Adminuser::Where('id', $adminid)
        //     ->pluck('cr_number')
        //     ->first();

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
     $account_name = DB::table('buyproducts')
            ->where('transaction_id', $transaction_id)
            ->pluck('account_name')
            ->first();

            $bankDetails = DB::table('bank')
            ->where('account_name', $account_name)
            ->where('is_default', 1)
            ->first();


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
        // Generate a unique filename for the PDF
        $filename = uniqid('invoice_'.$transaction_id).'.pdf';

        // $pdf = PDF::loadView('/pdf/recieptwithtax_A4', array('details' => $dataplan, 'vat' => $vat, 'grandinnumber' => $grandinnumber, 'payment_type' => $payment_type, 'totals' => $total, 'trans' => $trans, 'enctrans' => $trans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'admintrno' => $admintrno, 'emailadmin' => $emailadmin, 'billphone' => $billphone, 'billemail' => $billemail, 'vat_type' => $vat_type, 'admin_address' => $admin_address));

        // return $pdf->stream('reciept.pdf');

        $data = [
            'deliveryAdd'=>$deliveryAdd,
            'billingAdd'=>$billingAdd,
            'bankDetails'=>$bankDetails,
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
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'trn_number' => $trn_number,
            // 'admintrno' => $admintrno,
            'emailadmin' => $emailadmin,
            'billphone' => $billphone,
            'billemail' => $billemail,
            'vat_type' => $vat_type,
            'discount_amt' => $discount_amt,
            'admin_address' => $admin_address,
            'grand_wo_dis' => $grand_wo_dis,
            'price_wo_dis' => $price_wo_dis,
            'Main_discount_amt' => $Main_discount_amt,
            'rate' => $rate,
            'tax'=>$tax,
              'po_box' => $po_box,
            'tel' => $tel,
            'admintrno' => $admintrno,
            'logo'=>$logo,
            'company'=>$company,
            'Address'=>$Address,
            'credit_note_amount'=>$credit_note_amount,
            'branch'=>$branch,
            'service_cost'=>$service_cost,
            'returntotal'=>$returntotal,
            'arabic_name'=>$arabic_name

        ];

        $pdf = \PDF::loadView('/pdf/recieptwithtax_A5', $data);

        return $pdf->stream($filename, ['Attachment' => false]);
    }

    // admin supplier stock purchase report pdf

    public function PDFSupplierPurchaseReport($supplier, $payment_mode = null)
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


        if (Session('adminuser')) {
            $adminid = Session('adminuser');
        } elseif (Session('softwareuser')) {
            $adminid = Softwareuser::Where('id', Session('softwareuser'))->pluck('admin_id')->first();
        }
            $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        $credit_supplier_name = DB::table('suppliers')
            ->where('id', $supplier)
            ->pluck('name')
            ->first();

        $admintrno = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();

        $po_box = Adminuser::Where('id', $adminid)
            ->pluck('po_box')
            ->first();
        $tel = Adminuser::Where('id', $adminid)
            ->pluck('phone')
            ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
            ->first();

        $adminname = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();

        $credit_location = DB::table('suppliers')
            ->where('id', $supplier)
            ->pluck('location')
            ->first();

        $credit_branchname = DB::table('branches')
            ->where('id', $credit_location)
            ->pluck('location')
            ->first();

        $credit_phone = DB::table('suppliers')
            ->where('id', $supplier)
            ->pluck('mobile')
            ->first();

        $credit_email = DB::table('suppliers')
            ->where('id', $supplier)
            ->pluck('email')
            ->first();

        $credit_start_row = DB::table('credit_supplier_transactions')
            ->where('credit_supplier_id', $supplier)
            ->orderBy('created_at', 'asc')
            ->first();

        $purchase_pdf_data = DB::table('stockdetails')
            ->leftJoin('products', 'stockdetails.product', '=', 'products.id')
            ->select(DB::raw('stockdetails.id as id, stockdetails.reciept_no as reciept_no, stockdetails.created_at as created_at, SUM(stockdetails.price) as price, stockdetails.supplier as supplier, stockdetails.payment_mode as payment_mode'))
            ->where('stockdetails.supplier_id', $supplier)
            ->when($payment_mode != 0, function ($query) use ($payment_mode) {
                return $query->where('stockdetails.payment_mode', $payment_mode);
            })
            ->groupBy('stockdetails.reciept_no')
            ->orderBy('stockdetails.created_at', 'DESC')
            ->get();


        $totalPrice = $purchase_pdf_data->sum('price');

        $supplier_credit_start_row = DB::table('credit_supplier_transactions')
            ->where('credit_supplier_id', $supplier)
            ->orderBy('created_at', 'asc')
            ->first();

        $credit_start_date = $credit_start_row->created_at ?? null;

        $supplier_lastTransaction_for_due = DB::table('credit_supplier_transactions')
            ->where('credit_supplier_id', $supplier)
            ->orderBy('created_at', 'desc')
            ->first();

        $credit_end_date = $supplier_lastTransaction_for_due->created_at ?? null;

        $admin_address = Adminuser::Where('id', $adminid)
            ->pluck('address')
            ->first();

            $shopdata = Adminuser::Where('id', $adminid)
            ->get();

        $pdf = \PDF::loadView('/pdf/pdf_supplier_purchase_report', ['shopdatas'=>$shopdata,'tax'=>$tax,'purchase_pdf_data' => $purchase_pdf_data, 'totalPrice' => $totalPrice, 'currency' => $currency, 'credit_supplier_id' => $supplier, 'admintrno' => $admintrno, 'po_box' => $po_box, 'tel' => $tel, 'emailadmin' => $emailadmin, 'adminname' => $adminname, 'credit_supplier_name' => $credit_supplier_name, 'credit_branchname' => $credit_branchname, 'credit_phone' => $credit_phone, 'credit_email' => $credit_email, 'credit_start_date' => $credit_start_date, 'credit_end_date' => $credit_end_date, 'admin_address' => $admin_address]);

        return $pdf->download('Supplier_Statement_'.$credit_supplier_name.'.pdf');
    }

   public function VoucherPaymentDownload($supplier_id, $id)
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



            $purchases = DB::table('credit_supplier_transactions')
            ->leftJoin('suppliers', 'credit_supplier_transactions.credit_supplier_id', '=', 'suppliers.id') // Join with creditusers
            ->leftJoin('stockdetails', DB::raw("CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_unicode_ci"), '=', DB::raw("CONVERT(credit_supplier_transactions.reciept_no USING utf8mb4) COLLATE utf8mb4_unicode_ci")) // Join with buyproducts using transaction_id
            ->select(
                'credit_supplier_transactions.*', // Select all from credit_transactions
                'suppliers.mobile',
                'suppliers.email',
                'credit_supplier_transactions.reciept_no',
                'credit_supplier_transactions.comment',
                'suppliers.business_name',
                'suppliers.billing_add',
                'stockdetails.created_at as invoice_date',
                DB::raw('SUM(DISTINCT stockdetails.price) as total'), // Total from buyproducts
                DB::raw('credit_supplier_transactions.balance_due as total_due'), // Current invoice_due
                DB::raw('(SELECT balance_due FROM credit_supplier_transactions ct WHERE ct.reciept_no = credit_supplier_transactions.reciept_no ORDER BY ct.created_at ASC LIMIT 1) as previous_invoice_due') // Subquery to get the first invoice_due for the same transaction_id

            )
            ->where('credit_supplier_transactions.id', $id) // Assuming $id relates to credit_transactions
            // ->where('credit_supplier_transactions.location', $branch)
            ->where('credit_supplier_transactions.credit_supplier_id', $supplier_id)
            ->orderBy('credit_supplier_transactions.created_at', 'DESC')
            ->get();

            $shopdata = Adminuser::Where('id', $adminid)
            ->get();

            $suppfundHistory = DB::table('credit_supplier_transactions')
            ->where('id', $id)
            ->where('credit_supplier_id', $supplier_id)
            ->first();

        // Check if product_id is null
        if ($suppfundHistory && is_null($suppfundHistory->product_id)) {

            $products = DB::table('stockdetails')
                ->where('reciept_no', $suppfundHistory->reciept_no)
                ->get();
        } else {
            // If product_id is not null, you might want to fetch specific product details
            $products = DB::table('stockdetails')
                ->where('product', $suppfundHistory->product_id)
                ->where('reciept_no', $suppfundHistory->reciept_no)
                ->get();
        }
        } elseif (Session('softwareuser')) {
            $userid = Session('softwareuser');

            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

            $branchname = DB::table('branches')
                ->where('id', $branch)
                ->pluck('location')
                ->first();

                $company = DB::table('branches')
                ->where('id', $branch)
                ->pluck('company')
                ->first();

                  $address = DB::table('branches')
                ->where('id', $branch)
                ->pluck('address')
                ->first();

              $emailadmin = DB::table('branches')
                ->where('id', $branch)
                ->pluck('email')
                ->first();

              $tel = DB::table('branches')
                ->where('id', $branch)
                ->pluck('mobile')
                ->first();

              $admintrno = DB::table('branches')
                ->where('id', $branch)
                ->pluck('tr_no')
                ->first();




            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();
                $shopdata = Adminuser::Where('id', $adminid)
                ->get();

                $purchases = DB::table('credit_supplier_transactions')
                ->leftJoin('suppliers', 'credit_supplier_transactions.credit_supplier_id', '=', 'suppliers.id') // Join with suppliers
                ->leftJoin('stockdetails', DB::raw("CONVERT(stockdetails.reciept_no USING utf8mb4) COLLATE utf8mb4_unicode_ci"), '=', DB::raw("CONVERT(credit_supplier_transactions.reciept_no USING utf8mb4) COLLATE utf8mb4_unicode_ci")) // Join with stockdetails using reciept_no
                ->leftJoin('debit_note', DB::raw("CONVERT(debit_note.reciept_no USING utf8mb4) COLLATE utf8mb4_unicode_ci"), '=', DB::raw("CONVERT(credit_supplier_transactions.reciept_no USING utf8mb4) COLLATE utf8mb4_unicode_ci")) // Join with debit_note using reciept_no
                ->select(
                    'credit_supplier_transactions.*', // Select all from credit_supplier_transactions
                    'suppliers.mobile',
                    'suppliers.email',
                    'credit_supplier_transactions.reciept_no',
                    'credit_supplier_transactions.comment',
                    'suppliers.business_name',
                    'suppliers.billing_add',
                    'stockdetails.created_at as invoice_date',
                    DB::raw('SUM(DISTINCT stockdetails.price) as total'), // Total from stockdetails
                    DB::raw('credit_supplier_transactions.balance_due as total_due'), // Current invoice_due
                    DB::raw('(SELECT balance_due FROM credit_supplier_transactions ct WHERE ct.reciept_no = credit_supplier_transactions.reciept_no ORDER BY ct.created_at ASC LIMIT 1) as previous_invoice_due'), // Subquery to get the first invoice_due for the same reciept_no
                    DB::raw('SUM(debit_note.debit_note_amount) as total_debit_note') // Sum the debit_note amount
                )
                ->where('credit_supplier_transactions.id', $id) // Assuming $id relates to credit_supplier_transactions
                ->where('credit_supplier_transactions.location', $branch)
                ->where('credit_supplier_transactions.credit_supplier_id', $supplier_id)
                ->groupBy('credit_supplier_transactions.id') // Group by the credit_supplier_transactions ID to allow aggregation (SUM) to work properly
                ->orderBy('credit_supplier_transactions.created_at', 'DESC')
                ->get();

            }

            $amount = $purchases->first()->collectedamount;

            $totalamount = number_format($amount, 3, '.', '');
            $amountinwords = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
            $amountinwords = $amountinwords->format($totalamount);
            $amountinwords = ucwords($amountinwords);

            $suppfundHistory = DB::table('credit_supplier_transactions')
            ->where('id', $id)
            ->where('credit_supplier_id', $supplier_id)
            ->first();
            // Check if product_id is null
            if ($suppfundHistory && is_null($suppfundHistory->product_id)) {
                // Fetch all products under the trans_id from stockdetails, joining with products to get product names
                $products = DB::table('stockdetails')
                    ->join('products', 'stockdetails.product', '=', 'products.id') // Join with products table
                    ->where('stockdetails.reciept_no', $suppfundHistory->reciept_no)
                    ->select(
                        'stockdetails.*',
                        'products.product_name',
                        'stockdetails.rate',
                        'stockdetails.quantity as final_quantity' // No returnpurchases adjustment, use stock quantity directly
                    )
                    ->groupBy('stockdetails.id', 'products.product_name', 'stockdetails.rate') // Grouping based on product details
                    ->havingRaw('final_quantity > 0') // Only include products where final quantity is greater than 0
                    ->get();
            } else {
                // If product_id is not null, fetch specific product details
                $products = DB::table('stockdetails')
                    ->join('products', 'stockdetails.product', '=', 'products.id') // Join with products table
                    ->where('stockdetails.product', $suppfundHistory->product_id)
                    ->where('stockdetails.reciept_no', $suppfundHistory->reciept_no)
                    ->select(
                        'stockdetails.*',
                        'products.product_name',
                        'stockdetails.rate',
                        'stockdetails.quantity as final_quantity' // No returnpurchases adjustment, use stock quantity directly
                    )
                    ->groupBy('stockdetails.id', 'products.product_name', 'stockdetails.rate') // Grouping based on product details
                    ->havingRaw('final_quantity > 0') // Only include products where final quantity is greater than 0
                    ->get();
            }


            if ($suppfundHistory && is_null($suppfundHistory->product_id)) {
                // Fetch all products under the trans_id from stockdetails, joining with products to get product names
                $returnpurchases = DB::table('returnpurchases')
                    ->join('products', 'returnpurchases.product_id', '=', 'products.id') // Join with products table
                    ->where('returnpurchases.reciept_no', $suppfundHistory->reciept_no)
                    ->where('returnpurchases.created_at', $suppfundHistory->created_at) // Add created_at filter
                    ->select(
                        'returnpurchases.*',
                        'products.product_name',
                        'returnpurchases.rate',
                        'returnpurchases.quantity as final_quantity' // No returnpurchases adjustment, use stock quantity directly
                    )
                    ->groupBy('returnpurchases.id', 'products.product_name', 'returnpurchases.rate') // Grouping based on product details
                    ->havingRaw('final_quantity > 0') // Only include products where final quantity is greater than 0
                    ->get();
            } else {
                // If product_id is not null, fetch specific product details
                $returnpurchases = DB::table('returnpurchases')
                    ->join('products', 'returnpurchases.product_id', '=', 'products.id') // Join with products table
                    ->where('returnpurchases.product_id', $suppfundHistory->product_id)
                    ->where('returnpurchases.reciept_no', $suppfundHistory->reciept_no)
                    ->where('returnpurchases.created_at', $suppfundHistory->created_at) // Add created_at filter
                    ->select(
                        'returnpurchases.*',
                        'products.product_name',
                        'returnpurchases.rate',
                        'returnpurchases.quantity as final_quantity' // No returnpurchases adjustment, use stock quantity directly
                    )
                    ->groupBy('returnpurchases.id', 'products.product_name', 'returnpurchases.rate') // Grouping based on product details
                    ->havingRaw('final_quantity > 0') // Only include products where final quantity is greater than 0
                    ->get();
            }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $cr_num = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();

        $po_box = Adminuser::Where('id', $adminid)
            ->pluck('po_box')
            ->first();

        $tel = Adminuser::Where('id', $adminid)
            ->pluck('phone')
            ->first();

        $admintrno = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
            ->first();

        $admin_name = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();

        $admin_address = Adminuser::Where('id', $adminid)
            ->pluck('address')
            ->first();

        // Generate a unique filename for the PDF
        $filename = uniqid('payment_Voucher_'.$id).'.pdf';

        if (Session('adminuser')) {
            $data = [
                'id' => $id,
                'currency' => $currency,
                'cr_num' => $cr_num,
                'po_box' => $po_box,
                'tel' => $tel,
                'admintrno' => $admintrno,
                'emailadmin' => $emailadmin,
                'admin_name' => $admin_name,
                'shopdatas' => $shopdata,
                'purchases' => $purchases,
                'admin_address' => $admin_address,
                 'products' => $products,
                 'amountinwords'=>$amountinwords,
                 'returnpurchases'=>$returnpurchases,
                'company'=>$company,


            ];
        } elseif (Session('softwareuser')) {
            $data = [
                'id' => $id,
                'currency' => $currency,
                'cr_num' => $cr_num,
                'po_box' => $po_box,
                'tel' => $tel,
                'admintrno' => $admintrno,
                'emailadmin' => $emailadmin,
                'shopdatas' => $shopdata,
                'admin_name' => $admin_name,
                'branchname' => $branchname,
                'purchases' => $purchases,
                'admin_address' => $admin_address,
                'products' => $products,
                'amountinwords'=>$amountinwords,
                'returnpurchases'=>$returnpurchases,
                'company'=>$company,
                'address'=>$address,



            ];
        }

        $pdf = \PDF::loadView('/pdf/voucher_payment_download', $data);

        return $pdf->stream($filename, ['Attachment' => false]);
    }

    public function generatePurchaseReturnPrint($receipt_no, $created_at, UserService $userService)
    {
        if (session()->missing('softwareuser') && session()->missing('adminuser')) {
            return redirect('/');
        }

        $branch = Softwareuser::locationById(Session('softwareuser'));

        $branchname = Branch::locationNameById($branch);

        $dataplan = DB::table('returnpurchases')
            ->leftJoin('products', 'returnpurchases.product_id', '=', 'products.id')
            ->select(DB::raw('returnpurchases.id as id, returnpurchases.reciept_no as reciept_no, returnpurchases.created_at as created_at,returnpurchases.amount as amount, products.product_name,returnpurchases.quantity, returnpurchases.unit, returnpurchases.buycost,returnpurchases.amount_without_vat as amount_without_vat, returnpurchases.vat_amount as vat_amount'))
            ->where('returnpurchases.reciept_no', $receipt_no)
            ->where('returnpurchases.created_at', $created_at)
            ->where('returnpurchases.branch', $branch)
            ->get();

        $trans = $receipt_no;

        $enctrans = Crypt::encrypt($trans);

        $userid = Session('softwareuser');
        $item = $userService->getUserDetails($userid);

        $adminid = $userService->getAdminId($userid);

        $shopdata = Adminuser::Where('id', $adminid)
            ->get();

        $vat = Returnpurchase::select(DB::raw('SUM(vat) as vat'))
            ->where('returnpurchases.reciept_no', $receipt_no)
            ->where('returnpurchases.created_at', $created_at)
            ->where('returnpurchases.branch', $branch)
            ->pluck('vat')
            ->first();

        $date = DB::table('returnpurchases')
            ->select(DB::raw('DATE(returnpurchases.created_at) as date'))
            ->where('returnpurchases.reciept_no', $trans)
            ->where('returnpurchases.created_at', $created_at)
            ->where('returnpurchases.branch', $branch)
            ->pluck('date')
            ->first();

        $date = Carbon::parse($date)->format('d-m-Y');

        $supplier = Returnpurchase::where('returnpurchases.reciept_no', $trans)
            ->where('returnpurchases.created_at', $created_at)
            ->pluck('shop_name')
            ->first();

        $grand = Returnpurchase::where('returnpurchases.reciept_no', $trans)
            ->where('returnpurchases.created_at', $created_at)
            ->select(DB::raw('SUM(amount) as amount'))
            ->pluck('amount')
            ->first();

        $grandinnumber = $grand;
        $grand = number_format($grand, 3, '.', '');
        $amountinwords = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $amountinwords = $amountinwords->format($grand);
        $amountinwords = ucwords($amountinwords);
        $supplieddate = Carbon::now()->format('d-m-Y');

        $adminUser = Adminuser::find($adminid);

        $cr_num = $adminUser->getCRNumber();
        $po_box = $adminUser->getPOBox();
        $tel = $adminUser->getPhone();
        $admintrno = $adminUser->getCRNumber();
        $adminname = $adminUser->getAdminName();
        $emailadmin = $adminUser->getEmail();

        $supplier_id = Returnpurchase::where('returnpurchases.reciept_no', $trans)
            ->where('returnpurchases.created_at', $created_at)
            ->pluck('suppplierid')
            ->first();

        $suppUser = Supplier::find($supplier_id);

        $trn_supp = $suppUser->getSuppTRNumber();
        $mobile_supp = $suppUser->getSuppMobile();
        $email_supp = $suppUser->getSuppEmail();
        $address_supp = $suppUser->getSuppAddress();

        $currency = $adminUser->getCurrency($adminid);
        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
        $admin_address = Adminuser::Where('id', $adminid)
            ->pluck('address')
            ->first();

        // Generate a unique filename for the PDF
        $filename = uniqid('purchase_return_print').'.pdf';

        $data = [
            'details' => $dataplan,
            'vat' => $vat,
            'grandinnumber' => $grandinnumber,
            'trans' => $trans,
            'enctrans' => $trans,
            'users' => $item,
            'branches' => $branchname,
            'shopdatas' => $shopdata,
            'currency' => $currency,
            'date' => $date,
            'amountinwords' => $amountinwords,
            'supplieddate' => $supplieddate,
            'cr_num' => $cr_num,
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'admintrno' => $admintrno,
            'emailadmin' => $emailadmin,
            'supplier' => $supplier,
            'trn_supp' => $trn_supp,
            'adminname' => $adminname,
            'mobile_supp' => $mobile_supp,
            'address_supp' => $address_supp,
            'email_supp' => $email_supp,
            'admin_address' => $admin_address,
            'tax'=>$tax,
        ];

        $pdf = \PDF::loadView('/pdf/purchasereturnPrint', $data);

        return $pdf->stream($filename, ['Attachment' => false]);
    }

    // cash statement
    public function cash_statement_pdf($customer_id, $startDate = null, $endDate = null)
    {
          if (Session::has('softwareuser')) {
        } elseif (Session::has('adminuser')) {
        } else {
            return redirect('userlogin'); // or 'adminlogin' based on your logic
        }

        // Initialize $adminid variable
        $adminid = null;

        // original
        // $salesdata = DB::table('cash_trans_statements')
        //     ->select(
        //         'cash_trans_statements.*',
        //         DB::raw("(SELECT created_at FROM buyproducts WHERE transaction_id COLLATE utf8mb4_general_ci = cash_trans_statements.transaction_id LIMIT 1) as transaction_date")
        //     )
        //     ->where('cash_trans_statements.cash_user_id', $customer_id)
        //     ->get();

        // Query to fetch credit transactions for PDF
        $query = DB::table('cash_trans_statements')
            ->select(
                'cash_trans_statements.*',
                DB::raw('(SELECT created_at FROM buyproducts WHERE transaction_id COLLATE utf8mb4_general_ci = cash_trans_statements.transaction_id LIMIT 1) as transaction_date')
            )
            ->where('cash_trans_statements.cash_user_id', $customer_id);

        // Apply date filters if provided
        if ($startDate && $endDate) {
            $query->whereBetween(
                'cash_trans_statements.created_at',
                [
                    $startDate.' 00:00:00',
                    $endDate.' 23:59:59',
                ]
            );
        }
        $salesdata = $query->get();

        if (Session::has('softwareuser')) {
            $userid = Session::get('softwareuser');
            $item = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', Session('softwareuser'))
                ->get();

            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();

            $branch = Softwareuser::locationById($userid);

            $branchname = DB::table('branches')
                ->where('id', $branch)
                ->pluck('location')
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

        } elseif (Session::has('adminuser')) {
            $adminid = Session::get('adminuser');
            $item = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', Session('adminuser'))
                ->get();

                $admintrno = Adminuser::Where('id', $adminid)
                ->pluck('cr_number')
                ->first();

            $po_box = Adminuser::Where('id', $adminid)
                ->pluck('po_box')
                ->first();
                $tel = Adminuser::Where('id', $adminid)
                ->pluck('phone')
                ->first();

                $adminname = Adminuser::Where('id', $adminid)
                ->pluck('name')
                ->first();
                $admin_address = Adminuser::Where('id', $adminid)
                    ->pluck('address')
                    ->first();

        }
      if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $credit_name = DB::table('creditusers')
            ->where('id', $customer_id)
            ->pluck('name')
            ->first();

        // $admintrno = Adminuser::Where('id', $adminid)
        //     ->pluck('cr_number')
        //     ->first();

        // $po_box = Adminuser::Where('id', $adminid)
        //     ->pluck('po_box')
        //     ->first();
        // $tel = Adminuser::Where('id', $adminid)
        //     ->pluck('phone')
        //     ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
            ->first();

        // $adminname = Adminuser::Where('id', $adminid)
        //     ->pluck('name')
        //     ->first();

        $credit_location = DB::table('creditusers')
            ->where('id', $customer_id)
            ->pluck('location')
            ->first();

        $credit_branchname = DB::table('branches')
            ->where('id', $credit_location)
            ->pluck('location')
            ->first();

        $credit_trn = DB::table('creditusers')
            ->where('id', $customer_id)
            ->pluck('trn_number')
            ->first();

        $credit_phone = DB::table('creditusers')
            ->where('id', $customer_id)
            ->pluck('phone')
            ->first();

        $credit_email = DB::table('creditusers')
            ->where('id', $customer_id)
            ->pluck('email')
            ->first();

        $cash_start_row = DB::table('cash_trans_statements')
            ->where('cash_user_id', $customer_id)
            ->orderBy('created_at', 'asc')
            ->first();

        $cash_start_date = $cash_start_row->created_at ?? null;

        $lastTransaction = DB::table('cash_trans_statements')
            ->where('cash_user_id', $customer_id)
            ->orderBy('created_at', 'desc')
            ->first();

        $cash_end_date = $lastTransaction->created_at ?? null;

        $convertedenddate = Carbon::parse($endDate);

        $updatedBalanceQuery = DB::table('cash_trans_statements')
            ->select('updated_balance')
            ->where('cash_user_id', $customer_id)
            ->whereDate('created_at', '<=', $convertedenddate)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        // Check if the user is a normal user and apply the branch filter
        if (session('user_role') == 'softwareuser') {
            $updatedBalanceQuery->where('location', $branch);
        }

        $updated_balance = $updatedBalanceQuery->first();
        $updated_balance = $updated_balance ? $updated_balance->updated_balance : 0;

        // $admin_address = Adminuser::Where('id', $adminid)
        //     ->pluck('address')
        //     ->first();

        $shopdata = Adminuser::where('id', $adminid)->first();

        if (Session('adminuser')) {
            $branchname = DB::table('branches')
                ->where('id', $credit_location)
                ->pluck('location')
                ->first();
        }

        // $data = [
        //     'users' => $item,
        //     'salesdata' => $salesdata,
        //     'currency' => $currency,
        //     'credit_id' => $customer_id,
        //     'admintrno' => $admintrno,
        //     'po_box' => $po_box,
        //     'tel' => $tel,
        //     'branchname' => $branchname,
        //     'emailadmin' => $emailadmin,
        //     'adminname' => $adminname,
        //     'credit_name' => $credit_name,
        //     'credit_branchname' => $credit_branchname,
        //     'credit_trn' => $credit_trn,
        //     'credit_phone' => $credit_phone,
        //     'credit_email' => $credit_email,
        //     'cash_start_date' => $cash_start_date,
        //     'cash_end_date' => $cash_end_date,
        //     'updated_balance' => $updated_balance,
        //     'admin_address' => $admin_address,
        //     'startDate' => $startDate,
        //     'endDate' => $endDate,
        //     'shopdata' => $shopdata,
        //      'logo'=>$logo,
        //     'company'=>$company,
        //     'Address'=>$Address,

        // ];
        if (Session('softwareuser')) {
            $options = [
                'users' => $item,
                'salesdata' => $salesdata,
                'currency' => $currency,
                'credit_id' => $customer_id,
                'admintrno' => $admintrno,
                'po_box' => $po_box,
                'tel' => $tel,
                'branchname' => $branchname,
                'emailadmin' => $emailadmin,
                // 'adminname' => $adminname,
                'credit_name' => $credit_name,
                'credit_branchname' => $credit_branchname,
                'credit_trn' => $credit_trn,
                'credit_phone' => $credit_phone,
                'credit_email' => $credit_email,
                'cash_start_date' => $cash_start_date,
                'cash_end_date' => $cash_end_date,
                'updated_balance' => $updated_balance,
                // 'admin_address' => $admin_address,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'shopdata' => $shopdata,
                 'logo'=>$logo,
                'company'=>$company,
                'Address'=>$Address,
            ];
        } elseif (Session('adminuser')) {
            $options = [
                'users' => $item,
            'salesdata' => $salesdata,
            'currency' => $currency,
            'credit_id' => $customer_id,
            'admintrno' => $admintrno,
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'emailadmin' => $emailadmin,
            'adminname' => $adminname,
            'credit_name' => $credit_name,
            'credit_branchname' => $credit_branchname,
            'credit_trn' => $credit_trn,
            'credit_phone' => $credit_phone,
            'credit_email' => $credit_email,
            'cash_start_date' => $cash_start_date,
            'cash_end_date' => $cash_end_date,
            'updated_balance' => $updated_balance,
            'admin_address' => $admin_address,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'shopdata' => $shopdata,
            //  'logo'=>$logo,
            // 'company'=>$company,
            // 'Address'=>$Address,
            ];
        }
        $pdf = \PDF::loadView('/pdf/cash_statement_transaction_pdf', $options);

        return $pdf->download('Cash Transaction Statement of'.$credit_name.'.pdf');
    }

    // supplier cash statement
    public function cash_supplier_statement_pdf($supplier_cash_id, $startDate = null, $endDate = null)
    {
           if (Session::has('softwareuser')) {
        } elseif (Session::has('adminuser')) {
        } else {
            return redirect('userlogin'); // or 'adminlogin' based on your logic
        }

        // Initialize $adminid variable
        $adminid = null;

        // Query to fetch credit transactions for PDF
        $query = DB::table('cash_supplier_transactions')
            ->select(
                'cash_supplier_transactions.*',
                DB::raw('(SELECT created_at FROM stockdetails WHERE reciept_no COLLATE utf8mb4_general_ci = cash_supplier_transactions.reciept_no LIMIT 1) as receipt_date')
            )
            ->where('cash_supplier_transactions.cash_supplier_id', $supplier_cash_id);

        // Apply date filters if provided
        if ($startDate && $endDate) {
            $query->whereBetween(
                'cash_supplier_transactions.created_at',
                [
                    $startDate.' 00:00:00',
                    $endDate.' 23:59:59',
                ]
            );
        }

        $purchasedata = $query->get();

        if (Session::has('softwareuser')) {
            $userid = Session::get('softwareuser');
            $item = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', Session('softwareuser'))
                ->get();

            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();

            $branch = Softwareuser::locationById($userid);

            $branchname = DB::table('branches')
                ->where('id', $branch)
                ->pluck('location')
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

        } elseif (Session::has('adminuser')) {
            $adminid = Session::get('adminuser');
            $item = DB::table('adminusers')
                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                ->where('user_id', Session('adminuser'))
                ->get();
                $admintrno = Adminuser::Where('id', $adminid)
                ->pluck('cr_number')
                ->first();

            $po_box = Adminuser::Where('id', $adminid)
                ->pluck('po_box')
                ->first();
                $tel = Adminuser::Where('id', $adminid)
                ->pluck('phone')
                ->first();

                $adminname = Adminuser::Where('id', $adminid)
                ->pluck('name')
                ->first();
                $admin_address = Adminuser::Where('id', $adminid)
                    ->pluck('address')
                    ->first();

        }
 if (!$adminid) {
            return redirect()->back()->withErrors(['error' => 'Admin ID is not set. Please log in again.']);
        }

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        // $admintrno = Adminuser::Where('id', $adminid)
        //     ->pluck('cr_number')
        //     ->first();

        // $po_box = Adminuser::Where('id', $adminid)
        //     ->pluck('po_box')
        //     ->first();
        // $tel = Adminuser::Where('id', $adminid)
        //     ->pluck('phone')
        //     ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
            ->first();

        // $adminname = Adminuser::Where('id', $adminid)
        //     ->pluck('name')
        //     ->first();

        $creditSupplierNameQuery = DB::table('suppliers')
            ->where('id', $supplier_cash_id);

        // Check if the user is a normal user and apply the branch filter
        if (session('user_role') == 'softwareuser') {
            $creditSupplierNameQuery->where('location', $branch);
        }

        $credit_supplier_name = $creditSupplierNameQuery->pluck('name')->first();

        $credit_location = DB::table('suppliers')
            ->where('id', $supplier_cash_id)
            ->pluck('location')
            ->first();

        $credit_branchname = DB::table('branches')
            ->where('id', $credit_location)
            ->pluck('location')
            ->first();

        $credit_phone = DB::table('suppliers')
            ->where('id', $supplier_cash_id)
            ->pluck('mobile')
            ->first();

        $credit_email = DB::table('suppliers')
            ->where('id', $supplier_cash_id)
            ->pluck('email')
            ->first();

        $cash_start_row = DB::table('cash_supplier_transactions')
            ->where('cash_supplier_id', $supplier_cash_id)
            ->orderBy('created_at', 'asc')
            ->first();

        $cash_start_date = $cash_start_row->created_at ?? null;

        $lastTransaction = DB::table('cash_supplier_transactions')
            ->where('cash_supplier_id', $supplier_cash_id)
            ->orderBy('created_at', 'desc')
            ->first();

        $cash_end_date = $lastTransaction->created_at ?? null;

        // Controller method

        $convertedenddate = Carbon::parse($endDate);

        $updatedBalanceQuery = DB::table('cash_supplier_transactions')
            ->select('updated_balance')
            ->where('cash_supplier_id', $supplier_cash_id)
            ->whereDate('created_at', '<=', $convertedenddate)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        // Check if the user is a normal user and apply the branch filter
        if (session('user_role') == 'softwareuser') {
            $updatedBalanceQuery->where('location', $branch);
        }

        $updated_balance = $updatedBalanceQuery->first();

        $updated_balance = $updated_balance ? $updated_balance->updated_balance : 0;

        // $admin_address = Adminuser::Where('id', $adminid)
        //     ->pluck('address')
        //     ->first();

        $shopdata = Adminuser::where('id', $adminid)->first();

        if (Session('adminuser')) {
            $branchname = DB::table('branches')
                ->where('id', $credit_location)
                ->pluck('location')
                ->first();
        }

        // $data = [
        //     'users' => $item,
        //     'purchasedata' => $purchasedata,
        //     'currency' => $currency,
        //     'credit_id' => $supplier_cash_id,
        //     'admintrno' => $admintrno,
        //     'po_box' => $po_box,
        //     'tel' => $tel,
        //     'branchname' => $branchname,
        //     'emailadmin' => $emailadmin,
        //     'adminname' => $adminname,
        //     'credit_supplier_name' => $credit_supplier_name,
        //     'credit_branchname' => $credit_branchname,
        //     'credit_phone' => $credit_phone,
        //     'credit_email' => $credit_email,
        //     'cash_start_date' => $cash_start_date,
        //     'cash_end_date' => $cash_end_date,
        //     'updated_balance' => $updated_balance,
        //     'admin_address' => $admin_address,
        //     'startDate' => $startDate,
        //     'endDate' => $endDate,
        //     'shopdata' => $shopdata,
        //     'logo'=>$logo,
        //     'company'=>$company,
        //     'Address'=>$Address,
        // ];
        if (Session('softwareuser')) {
            $options = [
                'users' => $item,
                'purchasedata' => $purchasedata,
                'currency' => $currency,
                'credit_id' => $supplier_cash_id,
                'admintrno' => $admintrno,
                'po_box' => $po_box,
                'tel' => $tel,
                'branchname' => $branchname,
                'emailadmin' => $emailadmin,
                // 'adminname' => $adminname,
                'credit_supplier_name' => $credit_supplier_name,
                'credit_branchname' => $credit_branchname,
                'credit_phone' => $credit_phone,
                'credit_email' => $credit_email,
                'cash_start_date' => $cash_start_date,
                'cash_end_date' => $cash_end_date,
                'updated_balance' => $updated_balance,
                // 'admin_address' => $admin_address,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'shopdata' => $shopdata,
                'logo'=>$logo,
                'company'=>$company,
                'Address'=>$Address,
            ];
        } elseif (Session('adminuser')) {
            $options = [
                'users' => $item,
            'purchasedata' => $purchasedata,
            'currency' => $currency,
            'credit_id' => $supplier_cash_id,
            'admintrno' => $admintrno,
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'emailadmin' => $emailadmin,
            'adminname' => $adminname,
            'credit_supplier_name' => $credit_supplier_name,
            'credit_branchname' => $credit_branchname,
            'credit_phone' => $credit_phone,
            'credit_email' => $credit_email,
            'cash_start_date' => $cash_start_date,
            'cash_end_date' => $cash_end_date,
            'updated_balance' => $updated_balance,
            'admin_address' => $admin_address,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'shopdata' => $shopdata,
            // 'logo'=>$logo,
            // 'company'=>$company,
            // 'Address'=>$Address,
            ];
        }
        $pdf = \PDF::loadView('/pdf/cash_supplier_statement_transaction_pdf', $options);

        return $pdf->download('Cash Supplier Transaction Statement of'.$credit_supplier_name.'.pdf');
    }

   public function userReportPrintPDF($trans_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $Reportgenerated = DB::table('user_reports')
            // ->whereDate('user_reports.created_at', $today)
            ->where('trans_id', $trans_id)
            ->pluck('created_at')
            ->first();

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $userid = Session('softwareuser');

        $userdatas = Softwareuser::Where('id', $userid)
            ->get();

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

        $data = DB::table('user_reports')
            ->select(DB::raw('opening_balance,total_sales_amount,total_amount'))
            ->where('user_id', $userid)
            ->where('trans_id', $trans_id)
            ->where('branch', $branch)
            ->get();

        $company = DB::table('branches')
            ->where('id', $branch)
            ->pluck('company')
            ->first();

        $data = [
            'trans_id' => $trans_id,
            'open_balance' => $open_balance,
            'total_sales_amount' => $total_sales_amount,
            'creditPayment' => $creditPayment,
            'posBankSale' => $posBankSale,
            'creditSale' => $creditSale,
            'total_amount' => $total_amount,
            'cash_details' => $cash_details,
            'expense' => $expense,
            'income' => $income,
            'userdatas' => $userdatas,
            'service'=>$service,
            'Reportgenerated' => $Reportgenerated,
             'company'=>$company,

        ];

        // $filename = uniqid('user_report' . $trans_id) . '.pdf';
        // $pdf = PDF::loadView('/pdf/user-report-print', $data);
        // return $pdf->stream($filename, array("Attachment" => false));

        $pdf = \PDF::loadView('/pdf/user-report-print', $data);

        // $pdf = PDF::loadView('/pdf/user-report-print', $data)
        //     ->setPaper([0, 0, 80 * 2.83465, 210 * 2.83465], 'portrait');

        return $pdf->stream('user_report_'.$trans_id.'.pdf', ['Attachment' => false]);
    }
    public function bankReportSubmit(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $bank_id = $request->input('bank_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $transactionType = $request->input('transaction_type');
        $downloadPDF = $request->input('download_pdf', false);

        $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();

        $userid = Session('softwareuser');
        $adminid = Softwareuser::where('id', $userid)->pluck('admin_id')->first();
        $shopdata = Adminuser::where('id', $adminid)->get();
        $currency = Adminuser::where('id', $adminid)->pluck('currency')->first();

        $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();
        $cr_num = DB::table('branches')
            ->where('id', $branch)
            ->pluck('tr_no')
            ->first();

        $po_box = DB::table('branches')
            ->where('id', $branch)
            ->pluck('po_box')
            ->first();
        $tel = DB::table('branches')
            ->where('id', $branch)
            ->pluck('mobile')
            ->first();

        $admin_address = DB::table('branches')
            ->where('id', $branch)
            ->pluck('address')
            ->first();



        $branchname = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();


        $accountName = DB::table('bank')
        ->where('id', $bank_id)
        ->value('account_name');

        $accountNo = DB::table('bank')
        ->where('id', $bank_id)
        ->value('account_no');

        $bank_name = DB::table('bank')
        ->where('id', $bank_id)
        ->value('bank_name');

        $branch_name = DB::table('bank')
        ->where('id', $bank_id)
        ->value('branch_name');

        $bankDetails = DB::table('bank')
        ->where('id', $bank_id)
        ->select('ifsc_code', 'iban_code')
        ->first();

        $ifsc_code = $bankDetails->ifsc_code ?? null;
        $iban_code = $bankDetails->iban_code ?? null;

        if ($ifsc_code) {
            $codeToUse = $ifsc_code;
            $codeType = 'IFSC';
        } elseif ($iban_code) {
            $codeToUse = $iban_code;
            $codeType = 'IBAN';
        } else {
            $codeToUse = 'N/A';
            $codeType = '';
        }


        $useritem = DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userid)
            ->get();

            $accounts = DB::table('bank')
            ->select('id', 'status', 'account_name')
            ->where('branch',$branch)
            ->get();

            $openingBalance = DB::table('bank')
            ->where('id', $bank_id)
            ->value('opening_balance');

        if ($openingBalance === null) {
            return response()->json([
                'error' => 'Account not found or opening balance not set.',
            ], 404);
        }


        $openingBalance = DB::table('bank_history')
            ->where('bank_id', $bank_id)
            ->where('date', '<', $startDate)
            ->when($transactionType === 'credit&&debit', function ($query) {
                return $query;
            }, function ($query) use ($transactionType) {
                return $query->where('dr_cr', $transactionType);
            })
            ->where('branch', $branch)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->reduce(function ($balance, $transaction) {
                $amount = $transaction->amount;
                $drCr = strtolower($transaction->dr_cr);
                if ($drCr === 'credit' || $drCr === 'cr') {
                    return $balance + $amount;
                } elseif ($drCr === 'debit' || $drCr === 'dr') {
                    return $balance - $amount;
                }
                return $balance;
            }, $openingBalance);

        $transactions = DB::table('bank_history')
            ->select('id', 'detail as type', 'dr_cr', 'amount', 'created_at as value_date', 'date as tnx_date', 'remark as description', 'ref_no', 'party')
            ->where('bank_id', $bank_id)
            ->when($transactionType === 'credit&&debit', function ($query) {
                return $query;
            }, function ($query) use ($transactionType) {
                return $query->where('dr_cr', $transactionType);
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->where('branch', $branch)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

            $totalDebit = 0;
            $totalCredit = 0;

        $currentBalance = $openingBalance;
        $shopdata = Adminuser::Where('id', $adminid)
        ->get();

        $formattedTransactions = $transactions->map(function ($transaction) use (&$currentBalance, &$totalDebit, &$totalCredit) {
            $amount = (float) $transaction->amount;
            $drCr = strtolower($transaction->dr_cr);

            if ($drCr === 'credit' || $drCr === 'cr') {
                $totalCredit += $amount;
                $currentBalance += $amount;
            } elseif ($drCr === 'debit' || $drCr === 'dr') {
                $totalDebit += $amount;
                $currentBalance -= $amount;

            }

            return (object) [
                'tnx_date' => $transaction->tnx_date,
                'value_date' => $transaction->value_date,
                'type' => ucfirst($transaction->type),
                'description' => $transaction->description,
                'ref_no' => $transaction->ref_no,
                'party' => $transaction->party,
                'amount' => $amount,
                'dr_cr' => $transaction->dr_cr,
                'balance' => $currentBalance,
            ];
        });

        if ($downloadPDF) {
            $pdf = PDF::loadView('pdf.bankreportpdf', [
                'userid' => $userid,
                'users' => $useritem,
                'accounts' => $accounts,
                'opening_balance' => $openingBalance,
                'current_balance' => $currentBalance,
                'transactions' => $formattedTransactions,
                'currency' => $currency,
                'accountName' => $accountName,
                'accountNo' => $accountNo,
                'bank_name' => $bank_name,
                'branch_name' => $branch_name,
                'codeToUse' => $codeToUse,
                'codeType'=>$codeType,
                'branchname' => $branchname,
                'cr_num' => $cr_num,
                'po_box' => $po_box,
                'tel' => $tel,
                'admin_address'=>$admin_address,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalDebit' => $totalDebit,
                'totalCredit' => $totalCredit,
                'transactionType' => $transactionType,


            ]);

            return $pdf->download('bank_report.pdf');
        }

        return view('bankaccount.bankreport', [
            'userid' => $userid,
            'users' => $useritem,
            'accounts' => $accounts,
            'opening_balance' => $openingBalance,
            'current_balance' => $currentBalance,
            'transactions' => $formattedTransactions,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'transactionType' => $transactionType,
            'shopdatas' => $shopdata,


        ]);
    }

    // voucher_reciept_download............................................

   public function VoucherrecieptDownload($credituser_id, $id)
    {

            $userid = Session('softwareuser');

            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

            $branchname = DB::table('branches')
                ->where('id', $branch)
                ->pluck('location')
                ->first();

            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();
                $shopdata = Adminuser::Where('id', $adminid)
                ->get();
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
                    DB::raw('SUM(DISTINCT buyproducts.bill_grand_total) as total'), // Total from buyproducts
                    DB::raw('credit_transactions.balance_due as total_due'), // Current invoice_due
                    DB::raw('(SELECT balance_due FROM credit_transactions ct WHERE ct.transaction_id = credit_transactions.transaction_id ORDER BY ct.created_at ASC LIMIT 1) as previous_invoice_due'), // Subquery to get the first invoice_due for the same transaction_id
                )
                ->where('credit_transactions.id', $id) // Assuming $id relates to credit_transactions
                ->where('credit_transactions.location', $branch)
                ->where('credit_transactions.credituser_id', $credituser_id)
                ->groupBy('credit_transactions.id') // Group by the credit_transactions ID to allow aggregation (SUM) to work properly
                ->orderBy('credit_transactions.created_at', 'DESC')
                ->get();






            $amount = $purchases->first()->collected_amount;

            $totalamount = number_format($amount, 3, '.', '');
            $amountinwords = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
            $amountinwords = $amountinwords->format($totalamount);
            $amountinwords = ucwords($amountinwords);


        $fundHistory = DB::table('credit_transactions')
        ->where('id', $id)
        ->where('credituser_id', $credituser_id)
        ->first();


        if ($fundHistory && is_null($fundHistory->product_id)) {
            $products = DB::table('buyproducts')
                ->select(
                    'buyproducts.product_name',
                    'buyproducts.quantity',
                    'buyproducts.netrate',
                    'buyproducts.total_amount',
                    'buyproducts.total_discount_percent',
                    DB::raw('buyproducts.netrate - (buyproducts.total_discount_percent * buyproducts.netrate / 100) as total') // Calculate total after discount
                )
                ->where('buyproducts.transaction_id', $fundHistory->transaction_id)
                ->groupBy('buyproducts.product_name', 'buyproducts.quantity', 'buyproducts.netrate', 'buyproducts.total_amount', 'buyproducts.total_discount_percent') // Group by necessary fields
                ->get();
        } else {
            $products = DB::table('buyproducts')
                ->select(
                    'buyproducts.product_name',
                    'buyproducts.quantity',
                    'buyproducts.netrate',
                    'buyproducts.total_amount',
                    'buyproducts.total_discount_percent',
                    DB::raw('buyproducts.netrate - (buyproducts.total_discount_percent * buyproducts.netrate / 100) as total') // Calculate total after discount
                )
                ->where('buyproducts.product_name', $fundHistory->product_id)
                ->where('buyproducts.transaction_id', $fundHistory->transaction_id)
                ->groupBy('buyproducts.product_name', 'buyproducts.quantity', 'buyproducts.netrate', 'buyproducts.total_amount', 'buyproducts.total_discount_percent') // Group by necessary fields
                ->get();
        }

        if ($fundHistory && is_null($fundHistory->product_id)) {
            $returnproducts = DB::table('returnproducts')
                ->select(
                    'returnproducts.product_name',
                    'returnproducts.quantity',
                    'returnproducts.netrate',
                    'returnproducts.total_amount',
                    'returnproducts.total_discount_percent',
                    DB::raw('returnproducts.netrate - (returnproducts.total_discount_percent * returnproducts.netrate / 100) as total') // Calculate total after discount
                )
                ->where('returnproducts.created_at', $fundHistory->created_at) // Add created_at filter
                ->where('returnproducts.transaction_id', $fundHistory->transaction_id)
                ->groupBy('returnproducts.product_name', 'returnproducts.quantity', 'returnproducts.netrate', 'returnproducts.total_amount', 'returnproducts.total_discount_percent') // Group by necessary fields
                ->get();
        } else {
            $returnproducts = DB::table('returnproducts')
                ->select(
                    'returnproducts.product_name',
                    'returnproducts.quantity',
                    'returnproducts.netrate',
                    'returnproducts.total_amount',
                    'returnproducts.total_discount_percent',
                    DB::raw('returnproducts.netrate - (returnproducts.total_discount_percent * returnproducts.netrate / 100) as total') // Calculate total after discount
                )
                ->where('returnproducts.created_at', $fundHistory->created_at) // Add created_at filter
                ->where('returnproducts.product_name', $fundHistory->product_id)
                ->where('returnproducts.transaction_id', $fundHistory->transaction_id)
                ->groupBy('returnproducts.product_name', 'returnproducts.quantity', 'returnproducts.netrate', 'returnproducts.total_amount', 'returnproducts.total_discount_percent') // Group by necessary fields
                ->get();
        }

        // Now $finalProducts contains the products with adjusted quantities


        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

        $cr_num = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();

        $po_box = Adminuser::Where('id', $adminid)
            ->pluck('po_box')
            ->first();

        $tel = Adminuser::Where('id', $adminid)
            ->pluck('phone')
            ->first();

        $admintrno = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
            ->first();

        $admin_name = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();

        $admin_address = Adminuser::Where('id', $adminid)
            ->pluck('address')
            ->first();

        // Generate a unique filename for the PDF
        $filename = uniqid('reciept_Voucher_'.$id).'.pdf';

            $company = DB::table('branches')
                ->where('id', $branch)
                ->pluck('company')
                ->first();

          $address = DB::table('branches')
                ->where('id', $branch)
                ->pluck('address')
                ->first();

          $emailadmin = DB::table('branches')
                ->where('id', $branch)
                ->pluck('email')
                ->first();

          $tel = DB::table('branches')
                ->where('id', $branch)
                ->pluck('mobile')
                ->first();

          $admintrno = DB::table('branches')
                ->where('id', $branch)
                ->pluck('tr_no')
                ->first();


        if (Session('adminuser')) {
            $data = [
                'id' => $id,
                'currency' => $currency,
                'cr_num' => $cr_num,
                'po_box' => $po_box,
                'tel' => $tel,
                'admintrno' => $admintrno,
                'emailadmin' => $emailadmin,
                'admin_name' => $admin_name,
                'shopdatas' => $shopdata,
                'purchases' => $purchases,
                'admin_address' => $admin_address,
                'products' => $products,
                'amountinwords'=>$amountinwords,
                'returnproducts'=>$returnproducts

            ];
        } elseif (Session('softwareuser')) {
            $data = [
                'id' => $id,
                'currency' => $currency,
                'cr_num' => $cr_num,
                'po_box' => $po_box,
                'tel' => $tel,
                'admintrno' => $admintrno,
                'emailadmin' => $emailadmin,
                'shopdatas' => $shopdata,
                'admin_name' => $admin_name,
                'branchname' => $branchname,
                'purchases' => $purchases,
                'admin_address' => $admin_address,
                'products' => $products,
                'amountinwords'=>$amountinwords,
                'returnproducts'=>$returnproducts,
                'company'=>$company,
                'address'=>$address,

            ];
        }

        $pdf = \PDF::loadView('/pdf/voucher_reciept_download', $data);

        return $pdf->stream($filename, ['Attachment' => false]);
    }
    public function creditPrintPDF(Request $request)
    {
        // Fetch transaction_id from the request (from query parameters or form data)
        $transaction_id = $request->input('transaction_id'); // or use $request->get('transaction_id')

        if (!$transaction_id) {
            return redirect()->back()->with('error', 'No transaction ID found.');
        }
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

        // Prepare data to pass to the view
        $data = compact('creditNote', 'buyProduct', 'creditNoteDetails');

        // Generate PDF
        $pdf = \PDF::loadView('pdf.credit_pdf_print', $data);

        return $pdf->download('creditnote.pdf');
    }
    public function debititPrintPDF(Request $request)
    {
        // Fetch transaction_id from the request (from query parameters or form data)
        $reciept_no = $request->input('reciept_no');

        if (!$reciept_no) {
            return redirect()->back()->with('error', 'No transaction ID found.');
        }
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


 $creditNoteDetails = DB::table('debit_note')
    ->where('reciept_id', $reciept_no)
    ->where('created_at', $latestSubmissionTime)  // Only fetch details for the latest submission time
    ->distinct()  // Ensure distinct results if required
    ->get();
        // Prepare data to pass to the view
        $data = compact('creditNote', 'buyProduct', 'creditNoteDetails');

        // Generate PDF
        $pdf = \PDF::loadView('pdf.debit_pdf_print', $data);

        return $pdf->download('debitnote.pdf');
    }
     public function generatetaxNEWsunmi($transaction_id)
    {
        if (session()->missing('softwareuser') && session()->missing('adminuser')) {
            return redirect('/');
        }
    $dataplan = DB::table('buyproducts')
            ->select(DB::raw('buyproducts.product_name as product_name,buyproducts.unit as unit,buyproducts.product_id as product_id,buyproducts.quantity as quantity,buyproducts.mrp as mrp,buyproducts.price as price,buyproducts.fixed_vat as fixed_vat,buyproducts.vat_amount as vat_amount,buyproducts.total_amount as total_amount, buyproducts.unit as unit,buyproducts.vat_type as vat_type, buyproducts.netrate as netrate'))
            ->where('buyproducts.transaction_id', $transaction_id)
            ->get();
        $total = Buyproduct::select(DB::raw('SUM(price) as total'))
            ->where('transaction_id', $transaction_id)
            ->get();
            $branch = DB::table('buyproducts')
            ->where('transaction_id', $transaction_id)
            ->pluck('branch')
            ->first();
        $trans = $transaction_id;
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

        $grandinnumber = $grand - $Main_discount_amt;
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

        $supplieddate = Carbon::now()->format('d-m-Y');

        $cr_num = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();
        $po_box =  DB::table('branches')
        ->where('id', $branch)
        ->pluck('po_box')
        ->first();
        $tel = Adminuser::Where('id', $adminid)
            ->pluck('phone')
            ->first();
        $trn_number = DB::table('branches')
        ->where('id', $branch)
        ->pluck('tr_no')
        ->first();

        $adminname = DB::table('branches')
        ->where('id', $branch)
        ->pluck('company')
        ->first();


        $admintrno = Adminuser::Where('id', $adminid)
            ->pluck('cr_number')
            ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
            ->first();

       $billphone =  DB::table('branches')
            ->where('id', $branch)
            ->pluck('mobile')
            ->first();

        $billemail = Buyproduct::select(DB::raw('email'))
            ->where('transaction_id', $transaction_id)
            ->pluck('email')
            ->first();

        $adminroles = DB::table('adminusers')
            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
            ->where('user_id', $adminid)
            ->get();

        $vat_type = Buyproduct::select(DB::raw('vat_type'))
            ->where('transaction_id', $transaction_id)
            ->pluck('vat_type')
            ->first();

        $filename = uniqid('sunmi_'.$transaction_id).'.pdf';

        $data = [
            'details' => $dataplan,
            'vat' => $vat,
            'grandinnumber' => $grandinnumber,
            'payment_type' => $payment_type,
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
            'cr_num' => $cr_num,
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'trn_number' => $trn_number,
            'transaction_id' => $transaction_id,
            'admin_name' => $adminname,
            'admintrno' => $admintrno,
            'emailadmin' => $emailadmin,
            'billphone' => $billphone,
            'billemail' => $billemail,
            'discount_amt' => $discount_amt,
            'grand_wo_dis' => $grand_wo_dis,
            'adminroles' => $adminroles,
            'vat_type' => $vat_type,
            'price_wo_dis' => $price_wo_dis,
            'Main_discount_amt' => $Main_discount_amt,
            'rate' => $rate,
            'branch'=>$branch,
        ];

        // $wcppScript = WebClientPrint::createWcppDetectionScript(route('processRequest'), Session::getId());

        // $pdf = PDF::loadView('/pdf/sunmireceipt', array('details' => $dataplan, 'vat' => $vat, 'grandinnumber' => $grandinnumber, 'payment_type' => $payment_type, 'totals' => $total, 'trans' => $trans, 'enctrans' => $enctrans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'transaction_id' => $transaction_id, 'admin_name' => $adminname));
        // return $pdf->stream('reciept.pdf');

        if(Session('softwareuser')){
            foreach ($adminroles as $adminrole) {
                if ($adminrole->module_id == '23') {
                    return view('/pdf/newsunmireceipt', $data);
                } elseif ($adminrole->module_id == '25') {
                    $pdf = \PDF::loadView('/pdf/sunmireceipt_pdf', $data);

                    return $pdf->stream($filename, ['Attachment' => false]);
                }
            }
        }elseif(Session('adminuser')){
            return view('/pdf/newsunmireceipt', $data);

        }
    }
    public function returnPDF($transaction_id,$return_id)
    {
        // if (session()->missing('softwareuser') && session()->missing('adminuser')) {
        //     return redirect('/');
        // }
        if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        } elseif (Session('adminuser')) {
            if (session()->missing('adminuser')) {
                return redirect('adminlogin');
            }
        }

        $dataplan = DB::table('returnproducts')
            ->select(DB::raw('returnproducts.product_name as product_name,returnproducts.product_id as product_id,returnproducts.quantity as quantity,returnproducts.mrp as mrp,returnproducts.price as price,returnproducts.fixed_vat as fixed_vat,returnproducts.vat_amount as vat_amount,returnproducts.total_amount as total_amount, returnproducts.unit as unit, returnproducts.vat_type as vat_type, returnproducts.inclusive_rate as inclusive_rate, returnproducts.netrate as netrate,returnproducts.discount, returnproducts.totalamount_wo_discount, returnproducts.price_wo_discount, returnproducts.discount_amount'))
            ->where('returnproducts.transaction_id', $transaction_id)
            ->where('returnproducts.return_id', $return_id)
            ->get();
        $total = Returnproduct::select(
            DB::raw('SUM(price) as total'),
        )
            ->where('transaction_id', $transaction_id)
            ->where('returnproducts.return_id', $return_id)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $trans = $transaction_id;
        $return_id=$return_id;


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
        if (Session('softwareuser')) {
            $shopid = Session('softwareuser');
            $adminid = Softwareuser::Where('id', $shopid)
                ->pluck('admin_id')
                ->first();
        } elseif (Session('adminuser')) {
            $adminid = Session('adminuser');
        }

        $shopdata = Adminuser::Where('id', $adminid)
            ->get();
        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        $total = Returnproduct::select(
            DB::raw('SUM(price) as total'),
        )
            ->where('transaction_id', $transaction_id)
            ->where('returnproducts.return_id', $return_id)
            ->pluck('total')
            ->first();
        $vat = Returnproduct::select(
            DB::raw('SUM(vat_amount) as vat')
        )
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

        $grand = round(Returnproduct::where('transaction_id', $transaction_id)
        ->where('returnproducts.return_id', $return_id)
        ->select(DB::raw('SUM(total_amount) as total_amount'))
        ->pluck('total_amount')
        ->first(), 3);


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
        $tel = DB::table('branches')
        ->where('id', $branch)
        ->pluck('mobile')
        ->first();
        $po_box = DB::table('branches')
        ->where('id', $branch)
        ->pluck('po_box')
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

        $trn_number = DB::table('returnproducts')
            ->where('transaction_id', $trans)
            ->where('returnproducts.return_id', $return_id)
            ->pluck('trn_number')
            ->first();

            $admintrno = DB::table('branches')
            ->where('id', $branch)
            ->pluck('tr_no')
            ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
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

        $admin_address = Adminuser::Where('id', $adminid)
            ->pluck('address')
            ->first();

            $name = Adminuser::Where('id', $adminid)
            ->pluck('name')
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
            'bankDetails'=>$bankDetails,
            'billingAdd'=>$billingAdd,
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
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'trn_number' => $trn_number,
            'admintrno' => $admintrno,
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
            'logo'=>$logo,
            'company'=>$company,
            'Address'=>$Address,
            'return_id'=>$return_id,
            'branch'=>$branch

        ];


        $pdf = \PDF::loadView('/pdf/returnpdf', $data);

        return $pdf->download('return_reciept.pdf');

        // // Disable browser caching for this page
        // return response()->view('/pdf/recieptwithtax')->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

      public function generateSalesPdfmini(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
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
        $date = $request->query('date'); // Get the date from the query string
        $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
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
        // Fetch sales data for the given date
        $salesData = DB::table('buyproducts')
        ->select(
            'product_name',
            'quantity',
            'total_amount',
            'vat_amount',
            DB::raw('total_amount - (total_amount * (COALESCE(total_discount_percent, 0) / 100)) as final_amount')
        )
        ->whereDate('created_at', $date)
        ->where('branch', $branch)
        ->get();

        $totalDiscountAmount = DB::table('buyproducts')
    ->select(DB::raw('
        SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
        + SUM(DISTINCT COALESCE(buyproducts.total_discount_amount, 0)) as total_discount_amount
    '))
    ->whereDate('buyproducts.created_at', $date)
    ->where('buyproducts.branch', $branch)
    ->first();
            $branchname = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();


        return view('pdf.dasboardsalereport', [
            'salesData' => $salesData,
            'date' => $date,
            'po_box' => $po_box,
            'tel' => $tel,
            'admintrno' => $admintrno,
            'logo' => $logo,
            'company' => $company,
            'Address' => $Address,
            'branchname' => $branchname,
            'currency'=>$currency,
            'branch'=>$branch,
            'tax'=>$tax,
            'totalDiscountAmount'=>$totalDiscountAmount

        ]);
    }
    public function generateSalesPdf(Request $request)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
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
        $date = $request->query('date'); // Get the date from the query string
        $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
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
        // Fetch sales data for the given date
        $salesData = DB::table('buyproducts')
        ->select(
            'product_name',
            'quantity',
            'total_amount',
            'vat_amount',
            DB::raw('total_amount - (total_amount * (COALESCE(total_discount_percent, 0) / 100)) as final_amount')
        )
        ->whereDate('created_at', $date)
        ->where('branch', $branch)
        ->get();

        $totalDiscountAmount = DB::table('buyproducts')
    ->select(DB::raw('
        SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
        + SUM(DISTINCT COALESCE(buyproducts.total_discount_amount, 0)) as total_discount_amount
    '))
    ->whereDate('buyproducts.created_at', $date)
    ->where('buyproducts.branch', $branch)
    ->first();




    $vatSum = DB::table('buyproducts')
    ->whereDate('created_at', $date)
    ->where('branch', $branch)
    ->sum('vat_amount');
            $branchname = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();


        return view('pdf.dasboardsalereportsunmi', [
            'salesData' => $salesData,
            'date' => $date,
            'po_box' => $po_box,
            'tel' => $tel,
            'admintrno' => $admintrno,
            'logo' => $logo,
            'company' => $company,
            'Address' => $Address,
            'branchname' => $branchname,
            'currency'=>$currency,
            'branch'=>$branch,
            'tax'=>$tax,
            'totalDiscountAmount'=>$totalDiscountAmount

        ]);
    }

        public function downloadRow($service_id)
    {
         if (Session('softwareuser')) {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
        }
        // $service = Service::findOrFail($service_id); // Ensure service exists
        $services = Service::where('service_id', $service_id)->get();

        // $services = collect([$service]);
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

        $customer_name = DB::table('service')
        ->where('service_id', $service_id)
        ->pluck('customer')
        ->first();

        $customer_address = DB::table('service')
        ->where('service_id', $service_id)
        ->pluck('address')
        ->first();

        $customer_phone = DB::table('service')
        ->where('service_id', $service_id)
        ->pluck('phone')
        ->first();

         $payment_mode = DB::table('service')
        ->where('service_id', $service_id)
        ->pluck('payment_mode')
        ->first();

        $data = [
            'currency' => $currency,
            'po_box' => $po_box,
            'tel' => $tel,
            'admintrno' => $admintrno,
            'logo'=>$logo,
            'company'=>$company,
            'Address'=>$Address,
            'customer_name'=>$customer_name,
            'customer_address'=>$customer_address,
            'customer_phone'=>$customer_phone,
            'services'=>$services,
            'payment_mode'=>$payment_mode,
        ];

        $pdf = \PDF::loadView('/pdf/servicepdf', $data);
        return $pdf->download("service-{$service_id}.pdf");
    }
        public function downloadPDF(Request $request)
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
        ->groupBy('returnproducts.transaction_id')
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

        $pdf = PDF::loadView('pdf.daybook_pdf', compact(
            'sales', 'sales_return', 'purchases', 'purchases_return', 'expenses', 'incomes',
            'total_sales', 'total_return_sales', 'total_purchases', 'total_return_purchase', 'total_expenses', 'total_incomes',
            'start_date', 'end_date','company_name','receiptcustomer','total_receiptcustomer','paymentcustomer','total_paymentcustomer','service','total_service'
        ));

        return $pdf->stream('daybook_' . $start_date . '_to_' . $end_date . '.pdf');
    }
    public function exportExpenseHistory(Request $request)
        {
            $userid = Session('softwareuser');
        
            $branchId = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
                $startDate = $request->input('start_date');
        
            return Excel::download(new ExpenseHistoryExport($branchId, $startDate), 'expense_history.xlsx');
        }
 public function generateVoucher_customer($customer_id, $transaction_id)
        {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
            
            // Get payment transaction details
            $paymentTransaction = DB::table('credit_transactions')
                ->leftJoin('bank', 'credit_transactions.bank_id', '=', 'bank.id')
                ->where('credit_transactions.credituser_id', $customer_id)
                ->where('credit_transactions.id', $transaction_id)
                ->where('credit_transactions.comment', 'Payment Received')
                ->select(
                    'credit_transactions.id',
                    'credit_transactions.created_at',
                    'credit_transactions.collected_amount',
                    'credit_transactions.payment_type',
                    'credit_transactions.cheque_number',
                    'credit_transactions.depositing_date',
                    'credit_transactions.account_name',
                    'credit_transactions.reference_number',
                    'credit_transactions.bank_id',
                    'credit_transactions.transfer_date',
                    'bank.bank_name as bank_name'
                )
                ->first();
        
            if (!$paymentTransaction) {
                abort(404, 'Payment transaction not found');
            }
        
            // Get customer details
            $customer = DB::table('creditusers')
                ->where('id', $customer_id)
                ->select('name', 'phone', 'email', 'trn_number', 'billing_add', 'location')
                ->first();
        
            if (!$customer) {
                abort(404, 'Customer not found');
            }
            $customerBalance = DB::table('creditsummaries')
            ->where('credituser_id', $customer_id)
            ->selectRaw('due_amount - collected_amount as balance')
            ->first();
            // Get company/branch details
            $companyDetails = DB::table('branches')
                ->where('id', $customer->location)
                ->select('company', 'email as emailadmin', 'address', 'mobile as tel', 'tr_no as admintrno')
                ->first();
        
            // Format payment type
            $paymentType = [
                1 => 'Cash',
                2 => 'Cheque',
                3 => 'Bank Transfer'
            ][$paymentTransaction->payment_type] ?? 'Cash';

            $shopid = Session('softwareuser');
            $adminid = Softwareuser::Where('id', $shopid)
                ->pluck('admin_id')
                ->first();

            $currency = Adminuser::Where('id', $adminid)
                ->pluck('currency')
                ->first();
        
            $data = [
                'transaction' => $paymentTransaction,
                'customer' => $customer,
                'company' => $companyDetails,
                'paymentType' => $paymentType,
                'date' => \Carbon\Carbon::parse($paymentTransaction->created_at)->format('d/m/Y'),
                'time' => \Carbon\Carbon::parse($paymentTransaction->created_at)->format('h:i A'),
                'voucherNumber' => 'VOUCH-'.str_pad($transaction_id, 6, '0', STR_PAD_LEFT),
                'balance' => $customerBalance->balance ?? 0,
                'currency'=>$currency
            ];
        
            $pdf = PDF::loadView('pdf.customer_receipt_voucher', $data);
            return $pdf->stream('receipt-voucher-'.$transaction_id.'.pdf');
        }

        public function generateVoucher_supplier($supplier_id, $transaction_id)
        {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
            
            // Get payment transaction details
            $paymentTransaction = DB::table('credit_supplier_transactions')
                ->leftJoin('bank', 'credit_supplier_transactions.bank_id', '=', 'bank.id')
                ->where('credit_supplier_transactions.credit_supplier_id', $supplier_id)
                ->where('credit_supplier_transactions.id', $transaction_id)
                ->where('credit_supplier_transactions.comment', 'Payment Made')
                ->select(
                    'credit_supplier_transactions.id',
                    'credit_supplier_transactions.created_at',
                    'credit_supplier_transactions.collectedamount',
                    'credit_supplier_transactions.payment_type',
                    'credit_supplier_transactions.check_number',
                    'credit_supplier_transactions.depositing_date',
                    'credit_supplier_transactions.account_name',
                    'credit_supplier_transactions.reference_number',
                    'credit_supplier_transactions.bank_id',
                    'credit_supplier_transactions.transfer_date',
                    'bank.bank_name as bank_name'
                )
                ->first();
        
            if (!$paymentTransaction) {
                abort(404, 'Payment transaction not found');
            }
        
            // Get customer details
            $suppliers = DB::table('suppliers')
                ->where('id', $supplier_id)
                ->select('name', 'mobile', 'email', 'trn_number', 'billing_add', 'location')
                ->first();
        
            if (!$suppliers) {
                abort(404, 'Customer not found');
            }
            $supplierBalance = DB::table('supplier_credits')
            ->where('supplier_id', $supplier_id)
            ->selectRaw('due_amt - collected_amt as balance')
            ->first();
            // Get company/branch details
            $companyDetails = DB::table('branches')
                ->where('id', $suppliers->location)
                ->select('company', 'email as emailadmin', 'address', 'mobile as tel', 'tr_no as admintrno')
                ->first();
        
            // Format payment type
            $paymentType = [
                1 => 'Cash',
                2 => 'Cheque',
                3 => 'Bank Transfer'
            ][$paymentTransaction->payment_type] ?? 'Cash';

            $shopid = Session('softwareuser');
            $adminid = Softwareuser::Where('id', $shopid)
                ->pluck('admin_id')
                ->first();

            $currency = Adminuser::Where('id', $adminid)
                ->pluck('currency')
                ->first();
        
            $data = [
                'transaction' => $paymentTransaction,
                'suppliers' => $suppliers,
                'company' => $companyDetails,
                'paymentType' => $paymentType,
                'date' => \Carbon\Carbon::parse($paymentTransaction->created_at)->format('d/m/Y'),
                'time' => \Carbon\Carbon::parse($paymentTransaction->created_at)->format('h:i A'),
                'voucherNumber' => 'VOUCH-'.str_pad($transaction_id, 6, '0', STR_PAD_LEFT),
                'balance' => $supplierBalance->balance ?? 0,
                'currency'=>$currency
            ];
        
            $pdf = PDF::loadView('pdf.supplier_receipt_voucher', $data);
            return $pdf->stream('receipt-voucher-'.$transaction_id.'.pdf');
        }
}
