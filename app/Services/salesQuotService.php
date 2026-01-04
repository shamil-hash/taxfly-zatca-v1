<?php

namespace App\Services;

use App\Models\Adminuser;
use App\Models\Branch;
use App\Models\PerformanceInvoice;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\Softwareuser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class salesQuotService
{
    public function __construct()
    {
    }

    public function SalesQuot($page, $transaction_id, $userService, $salesquot)
    {
        $userid = Session('softwareuser');
        $branch = Softwareuser::locationById($userid);
        $branchname = Branch::locationNameById($branch);
        $adminid = Softwareuser::Where('id', $userid)
        ->pluck('admin_id')
        ->first();
        $item = $userService->getUserDetails($userid);

        $trans = $transaction_id;
        $enctrans = Crypt::encrypt($trans);

        // Determine model and table name based on page type (use a switch statement for readability)
        switch ($page) {
            case 'sales_order':
            case 'salesorderdraft':
            case 'quot_to_salesorder':
                $model = SalesOrder::class;
                $table = 'sales_orders';

                $payment_type = DB::table('sales_orders')
                    ->leftJoin('payment', 'sales_orders.payment_type', '=', 'payment.id')
                    ->select(DB::raw('payment.type as payment_type'))
                    ->where('sales_orders.transaction_id', $trans)
                    ->pluck('payment_type')
                    ->first();

                break;
            case 'quotation':
            case 'quotationdraft':
            case 'clone_quotation':
                $model = Quotation::class;
                $table = 'quotations';
                $payment_type = null;

                break;
            case 'performance_invoice':
            case 'performadraft':
                $model = PerformanceInvoice::class;
                $table = 'performance_invoices';

                $payment_type = DB::table('performance_invoices')
                    ->leftJoin('payment', 'performance_invoices.payment_type', '=', 'payment.id')
                    ->select(DB::raw('payment.type as payment_type'))
                    ->where('performance_invoices.transaction_id', $trans)
                    ->pluck('payment_type')
                    ->first();

                break;
            default:
                // Handle invalid page type (throw an exception or redirect with error message)
                return redirect()->back()->withErrors(['Invalid page type']);
        }

        $dataplan = DB::table($table)
            ->select(DB::raw('comment as comment,product_name as product_name,product_id as product_id,quantity as quantity,mrp as mrp,price as price,fixed_vat as fixed_vat,vat_amount as vat_amount,total_amount as total_amount, unit as unit, vat_type as vat_type, inclusive_rate as inclusive_rate, netrate as netrate,discount as discount, totalamount_wo_discount as totalamount_wo_discount, price_wo_discount as price_wo_discount, discount_amount as discount_amount'))
            ->where('transaction_id', $transaction_id)
            ->get();

        $total = $model::where('transaction_id', $transaction_id)
            ->sum('price');

        $custs = DB::table($table)
            ->where('transaction_id', $trans)
            ->pluck('customer_name')
            ->first();

            $description = DB::table($table)
            ->where('transaction_id', $trans)
            ->pluck('description')
            ->first();

        $vat = $model::select(DB::raw('SUM(vat_amount) as vat'))
            ->where('transaction_id', $transaction_id)
            ->pluck('vat')
            ->first();

        $rate = $model::select(DB::raw('SUM(mrp * quantity) as mrp'))
            ->where('transaction_id', $transaction_id)
            ->pluck('mrp')
            ->first();
            $name = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();
        $date = DB::table($table)
            ->select(DB::raw('DATE(created_at) as date'))
            ->where('transaction_id', $trans)
            ->pluck('date')
            ->first();

        $grand = $model::where('transaction_id', $transaction_id)
            ->select(DB::raw('SUM(total_amount) as total_amount'))
            ->pluck('total_amount')
            ->first();

        $discount_amt = $model::select(DB::raw('SUM(discount_amount * quantity) as discount_amount'))
            ->where('transaction_id', $transaction_id)
            ->pluck('discount_amount')
            ->first();

        $Main_discount_amt = $model::where('transaction_id', $transaction_id)
            ->pluck('total_discount_amount')
            ->first();

        $trn_number = DB::table($table)
            ->where('transaction_id', $trans)
            ->pluck('trn_number')
            ->first();

        $billphone = $model::select(DB::raw('phone'))
            ->where('transaction_id', $transaction_id)
            ->pluck('phone')
            ->first();

        $billemail = $model::select(DB::raw('email'))
            ->where('transaction_id', $transaction_id)
            ->pluck('email')
            ->first();

        $vat_type = $model::select(DB::raw('vat_type'))
            ->where('transaction_id', $transaction_id)
            ->pluck('vat_type')
            ->first();

        $adminid = $userService->getAdminId($userid);

        $shopdata = Adminuser::Where('id', $adminid)
            ->get();

        $adminUser = Adminuser::find($adminid);
        $currency = $adminUser->getCurrency();

        $date = Carbon::parse($date)->format('d-m-Y');

        $grandinnumber = $grand - $Main_discount_amt;
        $grand = number_format($grandinnumber, 3, '.', '');
        $amountinwords = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $amountinwords = $amountinwords->format($grand);
        $amountinwords = ucwords($amountinwords);

        $grand_wo_dis = $model::where('transaction_id', $transaction_id)
            ->select(DB::raw('SUM(totalamount_wo_discount) as totalamount_wo_discount'))
            ->pluck('totalamount_wo_discount')
            ->first();

        $supplieddate = Carbon::now()->format('d-m-Y');

        $cr_num = $adminUser->getCRNumber();
        $po_box = $adminUser->getPOBox();
        $tel = $adminUser->getPhone();
        $admintrno = $adminUser->getCRNumber();
        $adminname = $adminUser->getAdminName();
        $emailadmin = $adminUser->getEmail();

        $admin_address = Adminuser::Where('id', $adminid)
            ->pluck('address')
            ->first();
            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

            $tel = DB::table('branches')
            ->where('id', $branch)
            ->pluck('mobile')
            ->first();
            $po_box = DB::table('branches')
            ->where('id', $branch)
            ->pluck('po_box')
            ->first();


            $CAddress = DB::table('creditusers')
            ->where('name', $custs)
            ->pluck('billing_add')
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
        return [
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
            'cr_num' => $cr_num,
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'trn_number' => $trn_number,
            'user_id' => $userid,
            'branch' => $branch,
            'admin_name' => $adminname,
            'admintrno' => $admintrno,
            'emailadmin' => $emailadmin,
            'billphone' => $billphone,
            'billemail' => $billemail,
            'vat_type' => $vat_type,
            'page' => $page,
            'admin_address' => $admin_address,
            'discount_amt' => $discount_amt,
            'grand_wo_dis' => $grand_wo_dis,
            'Main_discount_amt' => $Main_discount_amt,
            'rate' => $rate,
            'tax'=>$tax,
            'name'=>$name,
            'logo'=>$logo,
            'company'=>$company,
            'Address'=>$Address,
            'branch'=>$branch,
            'CAddress'=>$CAddress,
            'description'=>$description
        ];
    }
}
