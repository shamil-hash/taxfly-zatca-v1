<?php

namespace App\Http\Controllers;


use App\Exports\ProductsExport;
use App\Exports\PurchaseExport;
use App\Exports\SalesExport;
use App\Exports\SalesReturnExport;
use App\Imports\ProductsImport;
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
use Illuminate\Support\Facades\Auth;
use App\Models\PandL;
use App\Models\CreditNote;
use App\Models\DebitNote;
use PDF;
use App\Services\EditTransactionService;
use App\Services\activityService;
use App\Services\otherService;
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
// Includes WebClientPrint classes
require_once app_path().'/WebClientPrint/WebClientPrint.php';

use Neodynamic\SDK\Web\WebClientPrint;

class CreditDebitController extends Controller
{
    public function creditnote()
    {

            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();
            $item = Product::select(DB::raw('*'))
                ->where('branch', $branch)
                ->get();

            $userid = Session('softwareuser');
            $useritem = DB::table('softwareusers')
                ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
                ->where('user_id', $userid)
                ->get();

            $adminid = Softwareuser::Where('id', $userid)
                ->pluck('admin_id')
                ->first();
                $shopdata = Branch::Where('id', $branch)->get();

            $sales = DB::table('buyproducts')
                ->where('branch', $branch)
                ->distinct('buyproducts.transaction_id')
                ->pluck('transaction_id');
            $tax = Adminuser::Where('id', $adminid)
                ->pluck('tax')
                ->first();

            /* ------------------GET IP ADDRESS--------------------------------------- */

            $ip = request()->ip();
            $uri = request()->fullUrl();

            $username = Softwareuser::where('id', $userid)->pluck('username')->first();

            $user_type = 'websoftware';
            $message = $username.' visited product return page';
            $locationdata = (new otherService())->get_location($ip);

            $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

            if ($locationdata != false) {
                $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
            }

            /* ----------------------------------------------------------------------- */
            return view('/billingdesk/creditnote', ['tax'=>$tax,'items' => $item, 'users' => $useritem, 'shopdatas' => $shopdata, 'sales' => $sales]);
        }

       public function creditnotesubmit(Request $request)
        {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }
            $request->validate([
                'trans_id_origin' => 'required',
            ]);

            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

                $count = DB::table('credit_note')
                ->distinct()
                ->count('credit_note_id');

            ++$count;
            $text = 'CN';

            $credit_note_id = $text.$count;


            foreach ($request->productName as $key => $productName) {
                $data = new CreditNote();

                $data->quantity = $request->quantity[$key];

                // if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {
                //     $data->mode = 'amount';
                // } else {
                //     $data->mode = 'quantity';
                // }
                // $data->quantity = $request->quantity[$key];
                if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {
                    $data->mode = 'quantity';
                }
                else{
                    $data->mode = 'amount';
                }

                $data->unit = $request->unit[$key];
                $data->product_id = $request->product_id[$key];

                $custs = DB::table('buyproducts')
                ->where('transaction_id', $request->trans[$key])
                ->pluck('customer_name')
                ->first();
                if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {
                    $data->customer = $custs;  // If checked, assign credit_note[]
                }
                else{
                    $data->customer = $request->customer_name;
                }
                $data->product_name = $productName;

                $data->transaction_id = $request->trans[$key];
                $data->credit_note_id = $credit_note_id;
                $data->email = $request->email;
                $data->trn_number = $request->trn_number;
                $data->phone = $request->phone;
                $data->price = $request->price[$key];
                $data->total_amount = $request->total_amount[$key];
                $data->payment_type = $request->ptype[$key];
                $data->user_id = Session('softwareuser');
                $data->branch = $branch;
                $data->mrp = $request->mrp[$key];
                $data->fixed_vat = $request->fixed_vat[$key];
                $data->one_pro_buycost = $request->buy_cost[$key];
                if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {
                    $data->credit_note_amount = $request->grand_total;  // If checked, assign credit_note[]
                }
                else{
                    $data->credit_note_amount = $request->credit_note[$key];
                }

                if ($request->ptype[$key] == 3) {
                    $data->creditusers_id = $request->creditusers_id[$key];
                } elseif ($request->ptype[$key] == 1 || $request->ptype[$key] == 2 || $request->ptype[$key] == 4) {
                    $data->cash_users_id = $request->creditusers_id[$key];
                }

                $data->vat_amount = $request->vat_amount[$key];

                if ($request->vat_type[$key] == 1) {
                    $data->inclusive_rate = $request->inclusive_rate_r[$key];
                    $data->discount_amount = ($request->dis_count[$key] != 0) ? ($request->total_amount_wo_discount[$key] * ($request->dis_count[$key] / 100)) : null;
                } elseif ($request->vat_type[$key] == 2) {
                    $data->discount_amount = ($request->dis_count[$key] != 0) ? ($request->total_withoutvat_wo_discount[$key] * ($request->dis_count[$key] / 100)) : null;
                }

                $data->vat_type = $request->vat_type[$key];
                $data->one_pro_buycost_rate = $request->buycost_rate[$key];
                $data->netrate = $request->net_rate[$key];

                $data->discount = $request->dis_count[$key];

                $data->totalamount_wo_discount = $request->total_amount_wo_discount[$key];
                $data->price_wo_discount = $request->total_withoutvat_wo_discount[$key];

                $data->total_discount_percent = $request->total_discount;
                // $data->total_discount_amount = $request->grand_total_wo_discount * ($request->total_discount / 100);
                $data->total_discount_amount = round($request->grand_total_wo_discount * ($request->total_discount / 100));

                $data->grand_total = $request->grand_total;
                $data->grand_total_wo_discount = $request->grand_total_wo_discount;
                $data->save();


                $inclusive_rate = $request->vat_type[$key] == 1 ? $request->inclusive_rate_r[$key] : null;
                NewBuyproduct::where('transaction_id', $request->trans[$key])
                    ->where('product_id', $request->product_id[$key])
                    ->update([
                        'mrp' => $request->mrp[$key],
                        'inclusive_rate' => $inclusive_rate,
                'total_amount' => $request->total_amount[$key],
                'netrate' => $request->net_rate[$key],
                'totalamount_wo_discount' => $request->total_amount_wo_discount[$key]

                    ]);


                    $bill_purchases = BillHistory::where('trans_id', $request->trans[$key])
                        ->where('product_id', $request->product_id[$key])
                        ->where('branch_id', $branch)
                        ->get();



                    $creditNoteAmount = isset($request->credit_note[$key]) && is_numeric($request->credit_note[$key])
                        ? $request->credit_note[$key]
                        : 0;

                        foreach ($bill_purchases as $purchase) {
                            $totalSoldQuantity = array_sum(array_map(function ($purchase) {
                                return $purchase['sold_quantity'] ?? 0; // Access sold_quantity as an array key
                            }, $bill_purchases->toArray()));


                        if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {
                            $final_credit_note=$request->grand_total / $totalSoldQuantity;
                        }
                        else{
                            $final_credit_note=$request->credit_note[$key] / $totalSoldQuantity;
                        }

                        $finalCreditNote = $final_credit_note ?? 0;
                        BillHistory::where('trans_id', $request->trans[$key])
                            ->where('product_id', $request->product_id[$key])
                            ->where('branch_id', $branch)
                            ->where('puid', $purchase->puid)
                            ->where('pid', $purchase->pid)
                            ->update([
                              'credit_note_amount' => DB::raw(
                                    $request->has('toggle_creditnote') && $request->toggle_creditnote === 'yes'
                                        ? "COALESCE(credit_note_amount, 0) + $final_credit_note"
                                        : "COALESCE(credit_note_amount, 0) + $final_credit_note"
                                ),
                            'billing_Sellingcost' => DB::raw("billing_Sellingcost - $finalCreditNote"),
                            'netrate' => DB::raw("netrate - $finalCreditNote"),

                            ]);
                    }

                        $stockdatUpdate = Stockdat::where('transaction_id', $request->trans[$key])
                                ->where('product_id', $request->product_id[$key])
                                ->update([
                                'credit_note_amount' => DB::raw(
                                    $request->has('toggle_creditnote') && $request->toggle_creditnote === 'yes'
                                        ? "COALESCE(credit_note_amount, 0) + {$request->grand_total}"
                                        : "COALESCE(credit_note_amount, 0) + $creditNoteAmount"
                                )
                            ]);

                $ids = $data->id;

                /* ----------------------- Stock Purchase Report --------------------------- */

                // $inputQuantity = $request->quantity[$key];

                // // Get the purchases from the billhistory table
                // $bill_purchases = BillHistory::where('trans_id', $request->trans[$key])
                //     ->where('product_id', $request->product_id[$key])
                //     ->where('branch_id', $branch)
                //     ->get();

                // $index = 0;
                // $buycostaddreturn = 0;
                // $buycost_rate_addreturn = 0;

                // while ($index < count($bill_purchases)) {
                //     $purchase = $bill_purchases[$index];
                //     // Get the purchase quantity and sold quantity
                //     $purchaseQuantity = $purchase->remain_sold_quantity;

                //     // Get the current sell quantity from stockpurchasereport
                //     $currentSellQuantity = DB::table('stock_purchase_reports')
                //         ->where('purchase_trans_id', $purchase->puid)
                //         ->where('purchase_id', $purchase->pid)
                //         ->pluck('sell_quantity')
                //         ->first();

                //     if ($inputQuantity <= $purchaseQuantity) {
                //         $quantityToAdd = $inputQuantity;
                //     } elseif ($inputQuantity > $purchaseQuantity) {
                //         $quantityToAdd = $purchaseQuantity;
                //     }

                //     // Update the stockpurchasereport table with the new quantity
                //     $newQuantity = $currentSellQuantity + $quantityToAdd;

                //     DB::table('stock_purchase_reports')
                //         ->where('purchase_trans_id', $purchase->puid)
                //         ->where('purchase_id', $purchase->pid)
                //         ->update(['sell_quantity' => $newQuantity]);

                //     BillHistory::where('trans_id', $request->trans[$key])
                //         ->where('product_id', $request->product_id[$key])
                //         ->where('branch_id', $branch)
                //         ->where('puid', $purchase->puid)
                //         ->where('pid', $purchase->pid)
                //         ->update([
                //             'remain_sold_quantity' => $purchaseQuantity - $quantityToAdd,
                //             'return_discount' => $request->dis_count[$key],
                //             'return_discount_amount' => ($request->dis_count[$key] != 0) ? ($request->total_amount_wo_discount[$key] * ($request->dis_count[$key] / 100)) : null,
                //             'return_total_discount_percent' => $request->total_discount,
                //             'return_total_discount_amt' => $request->grand_total_wo_discount * ($request->total_discount / 100),
                //             'return_grand_total' => $request->grand_total,
                //             'return_grand_total_wo_discount' => $request->grand_total_wo_discount,
                //         ]);

                //     $buycostaddreturn += ($quantityToAdd * $purchase->Purchase_buycost);
                //     $buycost_rate_addreturn += ($quantityToAdd * $purchase->Purchase_Buycost_Rate);

                //     Returnproduct::where('transaction_id', $request->trans[$key])
                //         ->where('id', $ids)
                //         ->where('product_id', $request->product_id[$key])
                //         ->where('branch', $branch)
                //         ->update([
                //             'buycostaddreturn' => $buycostaddreturn,
                //             'buycost_rate_addreturn' => $buycost_rate_addreturn,
                //         ]);

                //     // Deduct the allocated quantity from the current input quantity
                //     $inputQuantity -= $quantityToAdd;

                //     // If there's no input quantity left, exit the loop
                //     if ($inputQuantity <= 0) {
                //         break;
                //     }

                //     // Move to the next purchase and set the remaining input quantity as the new input quantity
                //     ++$index;
                // }

                /* -------------------------------------------------------------------------------- */
            }

            foreach ($request->productName as $key => $productName) {
                // $stock_num = DB::table('stockdats')
                //     ->where('transaction_id', $request->trans[$key])
                //     ->where('product_id', $request->product_id[$key])
                //     ->pluck('stock_num')
                //     ->first();

                // $one_pro_buycost = DB::table('stockdats')
                //     ->where('transaction_id', $request->trans[$key])
                //     ->where('product_id', $request->product_id[$key])
                //     ->pluck('one_pro_buycost')
                //     ->first();

                // $one_pro_sellingcost = DB::table('stockdats')
                //     ->where('transaction_id', $request->trans[$key])
                //     ->where('product_id', $request->product_id[$key])
                //     ->pluck('one_pro_sellingcost')
                //     ->first();

                // $one_pro_inclusive_rate = DB::table('stockdats')
                //     ->where('transaction_id', $request->trans[$key])
                //     ->where('product_id', $request->product_id[$key])
                //     ->pluck('one_pro_inclusive_rate')
                //     ->first();

                // $one_pro_buycost_rate = DB::table('stockdats')
                //     ->where('transaction_id', $request->trans[$key])
                //     ->where('product_id', $request->product_id[$key])
                //     ->pluck('one_pro_buycost_rate')
                //     ->first();

                // $one_pro_netrate = DB::table('stockdats')
                //     ->where('transaction_id', $request->trans[$key])
                //     ->where('product_id', $request->product_id[$key])
                //     ->pluck('netrate')
                //     ->first();

                // $newstock_num = $stock_num - $request->quantity[$key];
                // DB::table('stockdats')
                //     ->where('transaction_id', $request->trans[$key])
                //     ->where('product_id', $request->product_id[$key])
                //     ->delete();

                // $stockdat = new Stockdat();
                // $stockdat->product_id = $request->product_id[$key];
                // $stockdat->stock_num = $newstock_num;
                // $stockdat->transaction_id = $request->trans[$key];
                // $stockdat->user_id = Session('softwareuser');
                // $stockdat->one_pro_buycost = $one_pro_buycost;
                // $stockdat->one_pro_sellingcost = $one_pro_sellingcost;

                // if ($request->vat_type[$key] == 1) {
                //     $stockdat->one_pro_inclusive_rate = $one_pro_inclusive_rate;
                // }

                // $stockdat->one_pro_buycost_rate = $one_pro_buycost_rate;
                // $stockdat->netrate = $one_pro_netrate;

                // $stockdat->save();

                // $return = Product::find($request->product_id[$key]);
                // $return->remaining_stock += $request->quantity[$key];
                // $return->save();




                /* ------------------------------------------------------------ */
                if ($request->ptype[$key] != 3) {
                  // Get the customer name from the 'buyproducts' table based on the transaction ID
                    $custs = DB::table('buyproducts')
                    ->where('transaction_id', $request->trans[$key])
                    ->pluck('customer_name')
                    ->first();

                    // Default credit_note_amount to 0 if no value is found

                    // Fetch the existing credit_note_amount based on the customer
                    if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {
                    // If toggle_creditnote is checked, use the customer name from $custs
                    $credit_note_amount = DB::table('credit_note_summary')
                        ->where('customer_name', $custs)
                        ->pluck('credit_note_amount')
                        ->first();
                    } else {
                    // Otherwise, use the customer name from the request
                    $credit_note_amount = DB::table('credit_note_summary')
                        ->where('customer_name', $request->customer_name)
                        ->pluck('credit_note_amount')
                        ->first();
                    }


                    // If toggle_creditnote is checked, use grand_total
                    if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {

                        $credit_note_amount = DB::table('credit_note_summary')
                        ->where('customer_name', $custs)
                        ->pluck('credit_note_amount')
                        ->first();

                    $credit_note_amount += $request->grand_total;  // Assign grand_total to credit_note_amount
                    } else {
                    $credit_note_amount = DB::table('credit_note_summary')
                        ->where('customer_name', $request->customer_name)
                        ->pluck('credit_note_amount')
                        ->first();
                    $credit_note_amount += $request->credit_note[$key];
                    }

                    $conditions = [];
                    $values = [];

                    if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {
                        $conditions = ['customer_name' => $custs];
                        $values = ['credit_note_amount' => $credit_note_amount];
                        // $branches=['branch' => $branch];
                    } else {
                        $conditions = ['customer_name' => $request->customer_name];
                        $values = ['credit_note_amount' => $credit_note_amount];
                        // $branches=['branch' => $branch];
                    }

                    $credit_note_summary = DB::table('credit_note_summary')
                        ->updateOrInsert($conditions, $values);



                }

                if ($request->ptype[$key] == 3 && $request->creditusers_id[$key] != 0) {
                    $creditid = $request->creditusers_id[$key];

                $creditnote = DB::table('creditsummaries')
                ->where('credituser_id', $creditid)
                ->pluck('creditnote')
                ->first();

                $creditnote+=$request->grand_total;
                $creditsummaries1 = DB::table('creditsummaries')
                ->updateOrInsert(
                    ['credituser_id' => $creditid],
                    ['creditnote' => $creditnote]
                );


                    $due = DB::table('creditsummaries')
                        ->select(DB::raw('due_amount'))
                        ->where('credituser_id', $creditid)
                        ->pluck('due_amount')
                        ->first();

                    $paid = DB::table('creditsummaries')
                        ->select(DB::raw('collected_amount'))
                        ->where('credituser_id', $creditid)
                        ->pluck('collected_amount')
                        ->first();

                    $due -= $paid;

                    $fund = new Fundhistory();
                    $fund->username = $request->creditusers[$key];
                    $fund->amount = $request->total_amount[$key];
                    $fund->credituser_id = $creditid;
                    $fund->due = $due;
                    $fund->user_id = Session('softwareuser');
                    $fund->location = $branch;
                    $fund->status = '1';
                    $fund->save();
                }
            }

            if ($request->paymenttype == 3 && $request->credituser__id != 0) {
                $creditid = $request->credituser__id;



                $creditcollected = DB::table('creditsummaries')
                    ->where('credituser_id', $creditid)
                    ->pluck('collected_amount')
                    ->first();


                    $credit_limit = DB::table('creditusers')
                    ->where('id', $creditid)
                    ->where('location', $branch)
                    ->pluck('current_lamount')
                    ->first();

                    if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {
                        $livecollected = $request->grand_total;  // If checked, assign credit_note[]
                    }
                    else{
                        $livecollected = $request->credit_note[$key];
                    }

                $totalcreditcollected = $creditcollected + $livecollected;

                $creditsummaries = DB::table('creditsummaries')
                    ->updateOrInsert(
                        ['credituser_id' => $creditid],
                        ['collected_amount' => $totalcreditcollected]
                    );

                // credit transaction

                $lastTransaction = DB::table('credit_transactions')
                    ->where('credituser_id', $creditid)
                    ->where('location', $branch)
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();


                    $lastSameTransaction = DB::table('credit_transactions')
                    ->where('credituser_id', $creditid)
                    ->where('location', $branch)
                    ->where('transaction_id', $request->trans_id_origin)
                    ->orderBy('created_at', 'desc')
                    ->first();

                    $previous_due = $lastSameTransaction->balance_due ?? 0;

                    $new_balance_due=$previous_due - $livecollected;

                    $credit_lose = $lastTransaction->credit_lose ?? 0;

                    $credit_deducted = $credit_lose;
                    if ($credit_deducted > 0) {
                        $amount_to_recover = min($livecollected, $credit_deducted);

                        // Update the current limit (credit limit)
                        $new_credit_limit = $credit_limit + $amount_to_recover;

                        // Save the updated credit limit back to the database
                        DB::table('creditusers')
                            ->where('id', $creditid)
                            ->where('location', $branch)
                            ->update(['current_lamount' => $new_credit_limit]);

                        // Update credit_lose and adjust livecollected
                        $credit_lose -= $amount_to_recover;
                    }

                $updated_balance = $lastTransaction->updated_balance;
                $new_due = $updated_balance;
                $new_updated_bal = $new_due - $livecollected;

                $credit_trans = new CreditTransaction();
                $credit_trans->credituser_id = $creditid;
                $credit_trans->credit_username = $request->credituser;
                $credit_trans->user_id = Session('softwareuser');
                $credit_trans->location = $branch;
                $credit_trans->due = $new_due;
                $credit_trans->balance_due = $new_balance_due;
                $credit_trans->collected_amount = $livecollected;
                $credit_trans->updated_balance = $new_updated_bal;
                $credit_trans->credit_lose = $credit_lose; // Store the updated credit_lose
                $credit_trans->comment = 'Credit Note';
                $credit_trans->transaction_id = $request->trans_id_origin;
                $credit_trans->save();
            }
            elseif (($request->paymenttype == 1 || $request->paymenttype == 2 || $request->paymenttype == 4) && ($request->credituser__id != 0)) {
                $creditid = $request->credituser__id;

                $credit_username = DB::table('creditusers')
                    ->where('id', $creditid)
                    ->where('location', $branch)
                    ->pluck('name')
                    ->first();



                $lastCashTransaction = DB::table('cash_trans_statements')
                    ->where('cash_user_id', $creditid)
                    ->where('location', $branch)
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                $updated_balance = $lastCashTransaction->updated_balance ?? null;

                $new_updated_bal = $updated_balance - $request->grand_total;

                if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {
                    $livecollectedes = $request->grand_total;  // If checked, assign credit_note[]
                }
                else{
                    $livecollectedes = $request->credit_note[$key];
                }

                $cash_trans = new CashTransStatement();
                $cash_trans->cash_user_id = $creditid;
                $cash_trans->cash_username = $credit_username;
                $cash_trans->user_id = Session('softwareuser');
                $cash_trans->location = $branch;
                $cash_trans->transaction_id = $request->trans_id_origin;
                $cash_trans->collected_amount = $livecollectedes;
                $cash_trans->updated_balance = $new_updated_bal;
                $cash_trans->comment = 'Credit Note';
                $cash_trans->payment_type = $request->paymenttype;
                $cash_trans->save();


            }

            /* ------------------GET IP ADDRESS--------------------------------------- */
            $transaction=$request->trans_id_origin;
            $ip = request()->ip();
            $uri = request()->fullUrl();

            $userid = Session('softwareuser');
            $username = Softwareuser::where('id', $userid)->pluck('username')->first();

            $user_type = 'websoftware';
            $message = $username.' Credit Note Applied';
            $locationdata = (new otherService())->get_location($ip);

            $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

            if ($locationdata != false) {
                $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
            }

            /* ----------------------------------------------------------------------- */

            // return redirect('/creditnote')->with('success', 'Transaction Returned successfully!');
            return redirect('/creditnotefinalreciept/'.$transaction.'/'.$credit_note_id);

        }


        public function getInvoiceDetails(Request $request)
        {
            $transactionId = $request->input('transaction_id');

            // Fetch customer name
            $customerName = BuyProduct::where('transaction_id', $transactionId)
                            ->pluck('customer_name')
                            ->first();
            // Fetch credit_user_id from BuyProduct based on transaction_id
            $creditUserId = BuyProduct::where('transaction_id', $transactionId)
                                ->pluck('credit_user_id')
                                ->first();

            $cashUserId = BuyProduct::where('transaction_id', $transactionId)
            ->pluck('cash_user_id')
            ->first();

            // Fetch total due as due_amount - collected_amount from creditsummaries for the fetched credituser_id
            $totalDue = DB::table('creditsummaries')
                            ->where('credituser_id', $creditUserId)  // Use the credituser_id from BuyProduct
                            ->select(DB::raw('due_amount - collected_amount as total_due'))
                            ->value('total_due') ?? 0.00; // Fallback to 0.00 if value is null


            $invoiceDue = DB::table('buyproducts')
            ->select(DB::raw('SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0)) as sum'))
            ->where('transaction_id', $transactionId)
            ->groupBy('transaction_id')
            ->value('sum');



            $credit_note_amount =  DB::table('credit_note')
            ->where('transaction_id', $transactionId)
            ->sum('credit_note_amount');

            $credit_note_amount = number_format($credit_note_amount, 3, '.', '');


            if ($creditUserId) {
                // If $creditUserId is available, get the collected amount from credit_transactions
                $collected_amount = DB::table('credit_transactions')
                    ->where('transaction_id', $transactionId)
                    ->where('comment', '!=', 'Returned Product')
                    ->where('comment', '!=', 'Credit Note') // Exclude rows with 'Return product' comment
                    ->sum('collected_amount');
            } elseif ($cashUserId) {
                // If $cashUserId is available, get the collected amount from cash_trans_statements
                $collected_amount = DB::table('cash_trans_statements')
                    ->where('transaction_id', $transactionId)
                    ->where('comment', '!=', 'Product Returned') // Exclude rows with 'Return product' comment
                    ->where('comment', '!=', 'Credit Note') // Exclude rows with 'Return product' comment
                    ->sum('collected_amount');
            } else {
                // If neither user ID is available, get the bill grand total from buyproducts
                $collected_amount = DB::table('buyproducts')
                    ->where('transaction_id', $transactionId)
                    ->pluck('bill_grand_total')
                    ->first();
            }


            $returnProductsAmount = DB::table('returnproducts')
            ->selectRaw("
                CASE
                    WHEN vat_type = 1 THEN
                        COALESCE(SUM(totalamount_wo_discount), 0) -
                        (COALESCE(SUM(discount_amount), 0) + SUM(total_amount * (total_discount_percent / 100)))
                    ELSE
                        COALESCE(SUM(grand_total), 0)
                END AS return_amount
            ")
            ->where('transaction_id', $transactionId)
            ->value('return_amount') ?? 0;

        // Format the value to three decimal places
        $returnProductsAmount = number_format($returnProductsAmount, 3, '.', '');


            $balance_due=number_format($invoiceDue - $collected_amount - $returnProductsAmount - $credit_note_amount,3);



            // Prepare response data
            $response = [
                'customer_name' => $customerName,
                'total_due' => $totalDue,
                'invoice_due' => $invoiceDue,
                'credit_note_amount'=>$credit_note_amount,
                'balance_due'=>$balance_due,
                'collected_amount'=>$collected_amount,
                'returnProductsAmount'=>$returnProductsAmount,
            ];

            return response()->json($response);
        }

         public function getsoldcreditquantity($trans_id, $pro_id)
    {
        // Fetch the sold quantity from the Buyproduct table
        $buyproductsquantity = Buyproduct::where('transaction_id', $trans_id)
        ->where('product_id', $pro_id)
        ->pluck('quantity')
        ->first() ?? 0;

        $retunquantity = Returnproduct::where('transaction_id', $trans_id)
        ->where('product_id', $pro_id)
        ->pluck('quantity')
        ->first() ?? 0;

        // Fetch the quantity from the CreditNote table
        $creditNoteQuantity = CreditNote::where('transaction_id', $trans_id)
        ->where('product_id', $pro_id)
        ->where('mode', '!=', 'amount') // Exclude rows where mode is 'amount'
        ->sum('quantity'); // This will sum up all matching quantities

        // Calculate the final answer
        $finalAnswer = $buyproductsquantity - $retunquantity - $creditNoteQuantity;
        // If you want to ensure it's not negative
        $soldquantity  = max(0, $finalAnswer); // Set to 0 if final answer is negative
        // Now $finalAnswer contains the desired result

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $sales_details = DB::table('new_buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('mrp');

        $buycost = DB::table('new_buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('one_pro_buycost');

        $fixed_vat_value = DB::table('new_buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('fixed_vat')
            ->first();

        $vat_type = DB::table('new_buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('vat_type')
            ->first();

        $buycost_rate = DB::table('new_buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('one_pro_buycost_rate');

        $discount = DB::table('new_buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('discount');

        $discount_type = DB::table('new_buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('discount_type');

        $total_discount_percent = DB::table('new_buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('total_discount_percent');

        $total_disc_amount = DB::table('new_buyproducts')
            ->where('transaction_id', $trans_id)
            ->where('product_id', $pro_id)
            ->where('branch', $branch)
            ->pluck('total_discount_amount');

        if ($vat_type == 1) {
            $inclusive_rate = DB::table('new_buyproducts')
                ->where('transaction_id', $trans_id)
                ->where('product_id', $pro_id)
                ->where('branch', $branch)
                ->pluck('inclusive_rate')
                ->first();

            return response()->json([
                'soldquantity' => $soldquantity,
                'sales_details' => $sales_details,
                'buycost' => $buycost,
                'fixed_vat_value' => $fixed_vat_value,
                'vat_type' => $vat_type,
                'inclusive_rate' => $inclusive_rate,
                'buycost_rate' => $buycost_rate,
                'discount' => $discount,
                'discount_type' => $discount_type,
                'total_discount_percent' => $total_discount_percent,
                'total_disc_amount' => $total_disc_amount,
            ]);
        }
        if ($vat_type == 2) {
            $exclusive_rate = DB::table('new_buyproducts')
                ->where('transaction_id', $trans_id)
                ->where('product_id', $pro_id)
                ->where('branch', $branch)
                ->pluck('exclusive_rate')
                ->first();

            return response()->json([
                'soldquantity' => $soldquantity,
                'sales_details' => $sales_details,
                'buycost' => $buycost,
                'fixed_vat_value' => $fixed_vat_value,
                'vat_type' => $vat_type,
                'buycost_rate' => $buycost_rate,
                'discount' => $discount,
                'discount_type' => $discount_type,
                'total_discount_percent' => $total_discount_percent,
                'total_disc_amount' => $total_disc_amount,
                'exclusive_rate' => $exclusive_rate,
            ]);
        }
    }

    public function creditnotereciept($transaction_id,$credit_note_id)
    {
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }
        $dataplan = DB::table('credit_note')
            ->select(DB::raw('credit_note.product_name as product_name,credit_note.credit_note_amount as credit_note_amount,credit_note.product_id as product_id,credit_note.quantity as quantity,credit_note.mrp as mrp,credit_note.price as price,credit_note.fixed_vat as fixed_vat,credit_note.vat_amount as vat_amount,credit_note.total_amount as total_amount, credit_note.unit as unit, credit_note.vat_type as vat_type, credit_note.inclusive_rate as inclusive_rate, credit_note.netrate as netrate,credit_note.discount, credit_note.totalamount_wo_discount, credit_note.price_wo_discount, credit_note.discount_amount'))
            ->where('credit_note.transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->get();

        $total = CreditNote::select(DB::raw('SUM(price) as total'))
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->get();

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $trans = $transaction_id;

        $creditnote=$credit_note_id;
        // $enctrans = Crypt::encrypt($trans);

        $enctrans = $trans;

        $custs = DB::table('credit_note')
            ->where('transaction_id', $trans)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('customer')
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
        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();

            $shopdata = Branch::Where('id', $branch)->get();
            $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();
        $name = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();
        $total = CreditNote::select(DB::raw('SUM(price) as total'))
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('total')
            ->first();
        $vat = CreditNote::select(DB::raw('SUM(vat_amount) as vat'))
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('vat')
            ->first();

        $rate = CreditNote::select(DB::raw('SUM(mrp * quantity) as mrp'))
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('mrp')
            ->first();


        $payment_type = DB::table('credit_note')
            ->leftJoin('payment', 'credit_note.payment_type', '=', 'payment.id')
            ->select(DB::raw('payment.type as payment_type'))
            ->where('credit_note.transaction_id', $trans)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('payment_type')
            ->first();

        $date = DB::table('credit_note')
            ->select(DB::raw('DATE(credit_note.created_at) as date'))
            ->where('transaction_id', $trans)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('date')
            ->first();
        $date = Carbon::parse($date)->format('d-m-Y');

        $grand = CreditNote::where('transaction_id', $transaction_id)
        ->where('credit_note.credit_note_id', $credit_note_id)
            ->select(DB::raw('SUM(total_amount) as total_amount'))
            ->pluck('total_amount')
            ->first();

        $discount_amt = CreditNote::select(DB::raw('SUM(discount) as discount_amount'))
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('discount_amount')
            ->first();

        $Main_discount_amt = CreditNote::where('transaction_id', $transaction_id)
        ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('total_discount_amount')
            ->first();

        $grandinnumber = $grand - $Main_discount_amt;
        $grand = number_format($grandinnumber, 3, '.', '');
        $amountinwords = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $amountinwords = $amountinwords->format($grand);
        $amountinwords = ucwords($amountinwords);

        $grand_wo_dis = CreditNote::where('transaction_id', $transaction_id)
        ->where('credit_note.credit_note_id', $credit_note_id)
            ->select(DB::raw('SUM(totalamount_wo_discount) as totalamount_wo_discount'))
            ->pluck('totalamount_wo_discount')
            ->first();

        $price_wo_dis = CreditNote::where('transaction_id', $transaction_id)
        ->where('credit_note.credit_note_id', $credit_note_id)
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

        $tax = Adminuser::Where('id', $adminid)
        ->pluck('tax')
        ->first();
        $trn_number = DB::table('credit_note')
            ->where('transaction_id', $trans)
            ->where('credit_note.credit_note_id', $credit_note_id)
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

        $billphone = CreditNote::select(DB::raw('phone'))
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('phone')
            ->first();

        $billemail = CreditNote::select(DB::raw('email'))
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('email')
            ->first();

        $vat_type = CreditNote::select(DB::raw('vat_type'))
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('vat_type')
            ->first();






         // Check if bankDetails is null and redirect without error message


        // Fetch customer details from the credituser table based on the customer name
        $customerDetails = DB::table('creditusers')
            ->where('name', $custs)
            ->first();
        // Prepare the additional details
        $billingAdd = optional($customerDetails)->billing_add;

        if ($billingAdd) {
            // Only display the billing address if it exists
            // echo $billingAdd;
        }
        $deliveryAdd = optional($customerDetails)->delivery_default == 1
        ? $customerDetails->deli_add
        : null;
        $wcppScript = WebClientPrint::createWcppDetectionScript(route('processRequest'), Session::getId());

        // return view('/billingdesk/recieptfinal', array('details' => $dataplan, 'vat' => $vat, 'payment_type' => $payment_type, 'grandinnumber' => $grandinnumber, 'totals' => $total, 'trans' => $trans, 'enctrans' => $enctrans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'user_id' => $userid, 'branch' => $branch, 'admin_name' => $adminname,'admintrno'=> $admintrno,'billphone' => $billphone, 'billemail' => $billemail, 'wcppScript' => $wcppScript));

        // return view('/billingdesk/recieptfinalbro', array('details' => $dataplan, 'vat' => $vat, 'payment_type' => $payment_type, 'grandinnumber' => $grandinnumber, 'totals' => $total, 'trans' => $trans, 'enctrans' => $enctrans, 'custs' => $custs, 'users' => $item, 'branches' => $branchname, 'shopdatas' => $shopdata, 'currency' => $currency, 'date' => $date, 'amountinwords' => $amountinwords, 'supplieddate' => $supplieddate, 'cr_num' => $cr_num, 'po_box' => $po_box, 'tel' => $tel, 'branchname' => $branchname, 'trn_number' => $trn_number, 'user_id' => $userid, 'branch' => $branch, 'admin_name' => $adminname, 'admintrno' => $admintrno, 'emailadmin' => $emailadmin, 'billphone' => $billphone, 'billemail' => $billemail, 'vat_type' => $vat_type, 'wcppScript' => ''));
        $data = [
            'deliveryAdd'=>$deliveryAdd,
            'billingAdd'=>$billingAdd,
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
            // 'cr_num' => $cr_num,
            'po_box' => $po_box,
            'tel' => $tel,
            'branchname' => $branchname,
            'trn_number' => $trn_number,
            'user_id' => $userid,
            'branch' => $branch,
            // 'admin_name' => $adminname,
            'admintrno' => $admintrno,
            'billphone' => $billphone,
            'billemail' => $billemail,
            'vat_type' => $vat_type,
            'wcppScript' => '',
            'discount_amt' => $discount_amt,
            'grand_wo_dis' => $grand_wo_dis,
            'adminid' => $adminid,
            'price_wo_dis' => $price_wo_dis,
            'Main_discount_amt' => $Main_discount_amt,
            'rate' => $rate,
            'tax'=>$tax,
            // 'employeename'=>$employeename,
            'name'=>$name,
            'logo'=>$logo,
            'company'=>$company,
            'Address'=>$Address,
            'creditnote'=>$creditnote,

        ];

        return view('/billingdesk/creditnotereciept', $data);
    }

    public function creditnotePDF($transaction_id,$credit_note_id)
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

    $dataplan = DB::table('credit_note')
            ->select(DB::raw('credit_note.product_name as product_name,credit_note.credit_note_amount as credit_note_amount,credit_note.product_id as product_id,credit_note.quantity as quantity,credit_note.mrp as mrp,credit_note.price as price,credit_note.fixed_vat as fixed_vat,credit_note.vat_amount as vat_amount,credit_note.total_amount as total_amount, credit_note.unit as unit, credit_note.vat_type as vat_type, credit_note.inclusive_rate as inclusive_rate, credit_note.netrate as netrate,credit_note.discount, credit_note.totalamount_wo_discount, credit_note.price_wo_discount, credit_note.discount_amount'))
            ->where('credit_note.transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->get();

        $total = CreditNote::select(
            DB::raw('SUM(price) as total'),
        )
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->get();
        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
        $trans = $transaction_id;
        $creditnote=$credit_note_id;


        $enctrans = Crypt::encrypt($trans);

        $custs = DB::table('credit_note')
            ->where('transaction_id', $trans)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('customer')
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

        $shopdata = Branch::Where('id', $branch)->get();

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
        $total = CreditNote::select(
            DB::raw('SUM(price) as total'),
        )
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('total')
            ->first();
        $vat = CreditNote::select(
            DB::raw('SUM(vat_amount) as vat')
        )
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('vat')
            ->first();

        $rate = CreditNote::select(DB::raw('SUM(mrp * quantity) as mrp'))
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('mrp')
            ->first();

        $payment_type = DB::table('credit_note')
            ->leftJoin('payment', 'credit_note.payment_type', '=', 'payment.id')
            ->select(DB::raw('payment.type as payment_type'))
            ->where('credit_note.transaction_id', $trans)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('payment_type')
            ->first();
        $date = DB::table('credit_note')
            ->select(DB::raw('DATE(credit_note.created_at) as date'))
            ->where('transaction_id', $trans)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('date')
            ->first();
        $date = Carbon::parse($date)->format('d-m-Y');

        $grand = round(CreditNote::where('transaction_id', $transaction_id)
        ->where('credit_note.credit_note_id', $credit_note_id)
        ->select(DB::raw('SUM(total_amount) as total_amount'))
        ->pluck('total_amount')
        ->first(), 3);


        $discount_amt = CreditNote::select(DB::raw('SUM(discount) as discount_amount'))
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('discount_amount')
            ->first();

        $Main_discount_amt = CreditNote::where('transaction_id', $transaction_id)
        ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('total_discount_amount')
            ->first();

        $grandinnumber = $grand - $Main_discount_amt;
        $grand = number_format($grandinnumber, 3, '.', '');
        $amountinwords = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $amountinwords = $amountinwords->format($grand);
        $amountinwords = ucwords($amountinwords);

        $grand_wo_dis = CreditNote::where('transaction_id', $transaction_id)
        ->where('credit_note.credit_note_id', $credit_note_id)
            ->select(DB::raw('SUM(totalamount_wo_discount) as totalamount_wo_discount'))
            ->pluck('totalamount_wo_discount')
            ->first();

        $price_wo_dis = CreditNote::where('transaction_id', $transaction_id)
        ->where('credit_note.credit_note_id', $credit_note_id)
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

        $trn_number = DB::table('credit_note')
            ->where('transaction_id', $trans)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('trn_number')
            ->first();

            $admintrno = DB::table('branches')
            ->where('id', $branch)
            ->pluck('tr_no')
            ->first();

        $emailadmin = Adminuser::Where('id', $adminid)
            ->pluck('email')
            ->first();

        $billphone = CreditNote::select(DB::raw('phone'))
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('phone')
            ->first();

        $billemail = CreditNote::select(DB::raw('email'))
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('email')
            ->first();

        $vat_type = CreditNote::select(DB::raw('vat_type'))
            ->where('transaction_id', $transaction_id)
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->pluck('vat_type')
            ->first();

        $admin_address = Adminuser::Where('id', $adminid)
            ->pluck('address')
            ->first();

            $name = Adminuser::Where('id', $adminid)
            ->pluck('name')
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
            'creditnote'=>$creditnote,

        ];


        $pdf = \PDF::loadView('/pdf/creditnotepdf', $data);

        return $pdf->download('creditnote_reciept.pdf');

        // // Disable browser caching for this page
        // return response()->view('/pdf/recieptwithtax')->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }
    public function fetchCustomers(Request $request) {
        $name = $request->input('name');
        $customers = DB::table('credit_note_summary')
        ->where('customer_name', 'LIKE', "%{$name}%")
        ->where('credit_note_amount', '>', 0) // Add condition for credit note amount
        ->select('customer_name AS name', 'credit_note_amount') // Select the credit note amount
        ->get();


        return response()->json($customers);
    }


    public function creditNoteHistory()
    {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }


            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

                $data = DB::table('credit_note')
                ->leftJoin('products', 'credit_note.product_id', '=', 'products.id')
                ->leftJoin('buyproducts', 'credit_note.transaction_id', '=', 'buyproducts.transaction_id')
                // ->leftJoin(DB::raw('(SELECT
                //                         transaction_id COLLATE utf8mb4_unicode_ci as transaction_id,
                //                         SUM(totalamount_wo_discount) as return_grandtotal_without_discount,
                //                         SUM(COALESCE(discount_amount, 0)) + SUM(total_amount * (total_discount_percent / 100)) as return_discount_amount,
                //                         SUM(DISTINCT COALESCE(grand_total, 0)) as return_sum
                //                     FROM returnproducts
                //                     GROUP BY transaction_id) as returns'), 'buyproducts.transaction_id', '=', 'returns.transaction_id')
                // ->leftJoin(DB::raw('(SELECT transaction_id, SUM(credit_note_amount) as total_credit_note_amount
                //                      FROM (SELECT DISTINCT transaction_id, credit_note_id, credit_note_amount
                //                            FROM credit_note) as unique_credits
                //                      GROUP BY transaction_id) as credit_sums'), 'credit_note.transaction_id', '=', 'credit_sums.transaction_id')
                ->select(
                    'credit_note.id as id',
                    'credit_note.customer as customer',
                    'credit_note.credit_note_id as credit_note_id',
                    'credit_note.transaction_id as transaction_id',
                    'credit_note.created_at as created_at',
                    'buyproducts.phone as phone',
                    'credit_note.vat_type as vat_type',
                    'credit_note.quantity',
                    'credit_note.payment_type as payment_type',
                    DB::raw('SUM(credit_note.totalamount_wo_discount) as grandtotal_without_discount'),
                    DB::raw('SUM(COALESCE(buyproducts.discount_amount, 0))
                              + SUM(buyproducts.total_amount * (buyproducts.total_discount_percent / 100)) as discount_amount'),
                    DB::raw('SUM(credit_note.vat_amount) as vat'),
                    DB::raw('COALESCE(credit_note.credit_note_amount, 0) as credit_note_amount'), // Corrected calculation
                    DB::raw('GROUP_CONCAT(DISTINCT products.product_name) as product_names'),
                    DB::raw('GROUP_CONCAT(credit_note.quantity) as quantities'),
                    // DB::raw('COALESCE(returns.return_grandtotal_without_discount, 0) as return_grandtotal_without_discount'),
                    // DB::raw('COALESCE(returns.return_discount_amount, 0) as return_discount_amount'),
                    // DB::raw('COALESCE(returns.return_sum, 0) as return_sum'),
                    DB::raw('SUM(DISTINCT COALESCE(credit_note.grand_total, 0)) as sum'),
                    // DB::raw('SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0)) as bill_grand_total')
                )
                ->groupBy('credit_note.credit_note_id')
                ->orderBy('credit_note.created_at', 'DESC')
                ->where('credit_note.branch', $branch)
                ->get();

            $userid = Session('softwareuser');

            $item = DB::table('softwareusers')
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

            $options = [
                'products' => $data,
                'users' => $item,
                'currency' => $currency,
                'tax'=>$tax,
            ];


        return view('/billingdesk/creditnote_history', $options);
    }


    public function viewcreditnote($credit_note_id, $branch_id = null)
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

            $shopid = Session('softwareuser');
            $adminid = Softwareuser::Where('id', $shopid)
                ->pluck('admin_id')
                ->first();


        $count = DB::table('credit_note')->count();

        $item = DB::table('credit_note')
            ->leftJoin('products', 'credit_note.product_id', '=', 'products.id')
            ->select(DB::raw('credit_note.*'))
            ->where('credit_note.credit_note_id', $credit_note_id)
            ->where('credit_note.branch', $branch)
            ->get();

        $currency = Adminuser::Where('id', $adminid)
            ->pluck('currency')
            ->first();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();

            return view('billingdesk/creditnotedetails', ['tax'=>$tax,'details' => $item, 'users' => $useritem, 'currency' => $currency]);

    }
    // -------------------------------------------------------------------------//
     public function customerSummary()
    {
            if (session()->missing('softwareuser')) {
                return redirect('userlogin');
            }


            $branch = DB::table('softwareusers')
                ->where('id', Session('softwareuser'))
                ->pluck('location')
                ->first();

                $data = DB::table('credit_note_summary')
                    ->select('customer_name', 'credit_note_amount')
                    ->where('credit_note_amount', '>', 0)
                    ->where('branch',$branch)
                    ->get();

            $userid = Session('softwareuser');

            $item = DB::table('softwareusers')
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

            $options = [
                'products' => $data,
                'users' => $item,
                'currency' => $currency,
                'tax'=>$tax,
            ];


        return view('/billingdesk/customer_summary', $options);
    }

    // ---------------------------------------------------------------------------------//

    // Debit Note

    public function debitnote()
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

        $receiptnos = DB::table('stockdetails')
            ->where('branch', $branch)
            ->distinct('stockdetails.reciept_no')
            ->pluck('reciept_no');


        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
            $shopdata = Branch::Where('id', $branch)->get();


        $suppliers = DB::table('suppliers')
            ->where('location', $branch)
            ->get();

            $tax = Adminuser::Where('id', $adminid)
            ->pluck('tax')
            ->first();
            $listbank = DB::table('bank')
            ->select('id','bank_name', 'account_name','status','current_balance')
            ->where('status', 1)
            ->where('branch', $branch)
            ->get();

        // new vat system
        return view('/inventory/debitnote', ['listbank'=>$listbank,'tax'=>$tax,'users' => $item, 'shopdatas' => $shopdata, 'receiptnos' => $receiptnos]);
    }

    public function getDebitInvoiceDetails(Request $request)
{
    $recieptIds = $request->input('reciept_no');

    $customerName = stockdetail::where('reciept_no', $recieptIds)
                    ->pluck('supplier')
                    ->first();
    $creditUserId = stockdetail::where('reciept_no', $recieptIds)
                    ->pluck('supplier_id')
                    ->first();

    $totalDue = DB::table('supplier_credits')
                    ->where('supplier_id', $creditUserId)  // Use the credituser_id from BuyProduct
                    ->select(DB::raw('due_amt - collected_amt as total_due'))
                    ->value('total_due') ?? 0.00; // Fallback to 0.00 if value is null

        $invoiceDue = DB::table('stockdetails')
            ->select(DB::raw('SUM(stockdetails.price) as sum'))
            ->where('reciept_no', $recieptIds)
            ->value('sum');

        $debit_note_amount =DB::table('debit_note')
        ->where('reciept_no', $recieptIds)
        ->sum('debit_note_amount');
        $debit_note_amount = number_format($debit_note_amount, 3, '.', '');

        if ($creditUserId) {
            $collected_amount = DB::table('credit_supplier_transactions')
            ->where('reciept_no', $recieptIds)
            // ->where('comment', '!=', 'Returned Product')
                ->where('comment', '!=', 'Debit Note') // Exclude rows with 'Return product' comment
                ->sum('collectedamount');
        }
        // elseif ($cashUserId) {
        //     $collected_amount = DB::table('cash_supplier_transactions')
        //     ->where('reciept_no', $recieptIds)
        //         // ->where('comment', '!=', 'Product Returned') // Exclude rows with 'Return product' comment
        //         ->where('comment', '!=', 'Debit Note') // Exclude rows with 'Return product' comment
        //         ->sum('collectedamount');
        // }
        else {
            $collected_amount = DB::table('stockdetails')
            ->where('reciept_no', $recieptIds)
            ->sum('price');

        }

        // $collected_amount = DB::table('credit_supplier_transactions')
        // ->where('reciept_no', $recieptIds)
        // ->sum('collectedamount');

        $balance_due=$invoiceDue - $collected_amount - $debit_note_amount;


    $response = [
        'customer_name' => $customerName,
        'total_due' => $totalDue,
        'invoice_due' => $invoiceDue,
        'debit_note_amount'=>$debit_note_amount,
        'balance_due'=>$balance_due,
        'collected_amount'=>$collected_amount,
    ];


    return response()->json($response);
}

public function getremainstock_purchase($receiptno, $pro_id)
{
    $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();

    /* purchase wise including bill quantity reducing - new */

    $purchase_remain = DB::table('stock_purchase_reports')
        ->select(DB::raw('sell_quantity'))
        ->where('receipt_no', $receiptno)
        ->where('product_id', $pro_id)
        ->where('branch_id', $branch)
        ->get();


        $purchase_details = DB::table('new_stockdetails')
        ->leftJoin('products', 'new_stockdetails.product', '=', 'products.id')
        ->leftJoin('stock_purchase_reports', 'new_stockdetails.id', '=', 'stock_purchase_reports.purchase_id')
        ->leftJoin('returnpurchases', function($join) {
            $join->on('new_stockdetails.reciept_no', '=', 'returnpurchases.reciept_no')
                ->on('new_stockdetails.product', '=', 'returnpurchases.product_id');
        })
        ->leftJoin('debit_note', function($join) {
            $join->on('new_stockdetails.reciept_no', '=', 'debit_note.reciept_no')
                ->on('new_stockdetails.product', '=', 'debit_note.product_id');
        })
        ->select(DB::raw('new_stockdetails.id,
                          new_stockdetails.reciept_no,
                          new_stockdetails.product,
                          new_stockdetails.supplier,
                          products.product_name,
                          new_stockdetails.price,
                          new_stockdetails.quantity - IFNULL(returnpurchases.quantity, 0) - IFNULL(debit_note.quantity, 0) as quantity,
                          new_stockdetails.payment_mode,
                          new_stockdetails.buycost,
                          new_stockdetails.unit,
                          new_stockdetails.vat_amount,
                          new_stockdetails.vat_percentage,
                          stock_purchase_reports.purchase_trans_id,
                          new_stockdetails.supplier_id,
                          new_stockdetails.rate,
                          new_stockdetails.vat'))
        ->where('new_stockdetails.reciept_no', $receiptno)
        ->where('new_stockdetails.product', $pro_id)
        ->where('new_stockdetails.branch', $branch)
        ->get();


    $returnPrice = DB::table('returnpurchases')
        ->select(DB::raw('amount'))
        ->where('reciept_no', $receiptno)
        ->where('product_id', $pro_id)
        ->where('branch', $branch)
        ->get();

    // echo json_encode($purchase_remain);
    return response()->json([
        'purchase_Data' => $purchase_details,
        'purchase_remain' => $purchase_remain,
        'returnPrice' => $returnPrice,
    ]);
}


public function debitnotesubmit(Request $request)
{
    if (session()->missing('softwareuser')) {
        return redirect('userlogin');
    }
    // $request->validate([
    //     'reciept_no' => 'required|array',
    //     'reciept_no.*' => 'required|',
    //     'quantity' => 'required|array',
    //     'quantity.*' => 'required',
    //     'comment' => 'array',
    //     'amount' => 'required|array',
    //     'shop_name.*' => 'required',
    //     'product' => 'required',
    // ]);
    $branch = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();

        $count = DB::table('debit_note')
        ->distinct()
        ->count('debit_note_id');

    ++$count;
    $text = 'DN';

    $debit_note_id = $text.$count;

    foreach ($request->p_main_id as $key => $p_main_id) {
        $data = new DebitNote();
        $data->reciept_no = $request->reciept_no[$key];
        $data->quantity = $request->quantity[$key];
        $data->unit = $request->units[$key];
        $data->buycost = $request->buycosts[$key];
        if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {
            $data->mode = 'quantity';
        }
        else{
            $data->mode = 'amount';
        }
        $data->product_id = $request->p_id[$key];
        $data->amount = $request->amount[$key];
        $data->shop_name = $request->shop_name[$key];
        $data->return_payment = $request->payment_mode;
        $data->bank_id = $request->bank_name;
        $data->account_name = $request->account_name;
        if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {
            $data->debit_note_amount = $request->amount[$key];  // If checked, assign credit_note[]
        }
        else{
            $data->debit_note_amount = $request->credit_note[$key];
        }
        $data->debit_note_id = $debit_note_id;


        $data->suppplierid = $request->supplid[$key];

        $data->user_id = Session('softwareuser');
        $data->branch = $branch;
        $data->amount_without_vat = $request->withoutvat[$key];

        $data->rate = $request->rate_r[$key];
        $data->vat = $request->vat_r[$key];

        $data->save();

            NewStockdetail::where('reciept_no', $request->reciept_no[$key])
                ->where('product', $request->p_id[$key])
                ->update([
                    'buycost' => $request->buycosts[$key],
                    'rate' => $request->rate_r[$key],
                    'price' => $request->amount[$key],
                ]);


        // $return = Product::find($request->p_id[$key]);
        // $return->remaining_stock -= $request->quantity[$key];
        // $return->save();

        // $remain_stock = StockDetail::where('reciept_no', $request->reciept_no[$key])
        //     ->where('product', $request->p_id[$key])
        //     // ->where('user_id', Session('softwareuser'))
        //     ->where('branch', $branch)
        //     ->pluck('remain_stock_quantity')
        //     ->first();

        // $purchase_reduce = StockDetail::where('reciept_no', $request->reciept_no[$key])
        //     ->where('product', $request->p_id[$key])
        //     // ->where('user_id', Session('softwareuser'))
        //     ->where('branch', $branch)
        //     ->update(['remain_stock_quantity' => ($remain_stock - $request->quantity[$key])]);

        /* ----- purchase return reduce from stock purchase reports table ---- */

        // $remain_main_quantity_sr = StockPurchaseReport::where('purchase_trans_id', $request->p_unique_trans_id[$key])
        //     ->where('purchase_id', $request->p_main_id[$key])
        //     ->where('receipt_no', $request->reciept_no[$key])
        //     ->where('product_id', $request->p_id[$key])
        //     // ->where('user_id', Session('softwareuser'))
        //     ->where('branch_id', $branch)
        //     ->pluck('remain_main_quantity')
        //     ->first();

        // $sell_quantity_sr = StockPurchaseReport::where('purchase_trans_id', $request->p_unique_trans_id[$key])
        //     ->where('purchase_id', $request->p_main_id[$key])
        //     ->where('receipt_no', $request->reciept_no[$key])
        //     ->where('product_id', $request->p_id[$key])
        //     // ->where('user_id', Session('softwareuser'))
        //     ->where('branch_id', $branch)
        //     ->pluck('sell_quantity')
        //     ->first();

        // $p_return_reduce_sreport = StockPurchaseReport::where('purchase_trans_id', $request->p_unique_trans_id[$key])
        //     ->where('purchase_id', $request->p_main_id[$key])
        //     ->where('receipt_no', $request->reciept_no[$key])
        //     ->where('product_id', $request->p_id[$key])
        //     // ->where('user_id', Session('softwareuser'))
        //     ->where('branch_id', $branch)
        //     ->update([
        //         'remain_main_quantity' => ($remain_main_quantity_sr - $request->quantity[$key]),
        //         'sell_quantity' => ($sell_quantity_sr - $request->quantity[$key]),
        //     ]);

        /* ------------------------------------------------------------------ */

        // $remain_history_stock = Stockhistory::where('receipt_no', $request->reciept_no[$key])
        //     ->where('product_id', $request->p_id[$key])
        //     ->where('user_id', Session('softwareuser'))
        //     ->pluck('remain_qantity')
        //     ->first();

        // $purchase_histry_reduce = Stockhistory::where('receipt_no', $request->reciept_no[$key])
        //     ->where('product_id', $request->p_id[$key])
        //     ->where('user_id', Session('softwareuser'))
        //     ->update(['remain_qantity' => ($remain_history_stock - $request->quantity[$key])]);



                          if ($request->ptype[$key] != 2) {

                              // If toggle_creditnote is checked, use grand_total
                              if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {

                                $credit_note_amount = DB::table('debit_note_summary')
                                ->where('supplier', $request->shop_name[$key])
                                ->pluck('debit_note_amount')
                                ->first();

                              $credit_note_amount += $request->amount[$key];  // Assign grand_total to credit_note_amount
                              } else {
                                $credit_note_amount = DB::table('debit_note_summary')
                                ->where('supplier', $request->shop_name[$key])
                                ->pluck('debit_note_amount')
                                ->first();
                              $credit_note_amount += $request->credit_note[$key];
                              }

                              $conditions = [];
                              $values = [];

                              if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {
                                  $conditions = ['supplier' => $request->shop_name[$key]];
                                  $values = ['debit_note_amount' => $credit_note_amount];
                                  // $branches=['branch' => $branch];
                              } else {
                                  $conditions = ['supplier' => $request->shop_name[$key]];
                                  $values = ['debit_note_amount' => $credit_note_amount];
                                  // $branches=['branch' => $branch];
                              }

                              $credit_note_summary = DB::table('debit_note_summary')
                                  ->updateOrInsert($conditions, $values);



                          }


        $supplier_id = $request->supplid[$key];

        if ($request->ptype[$key] == 2) {
            // $supplier_id =  DB::table('suppliers')
            //     ->where('name', $request->shop_name[$key])
            //     ->where('location', $branch)
            //     ->pluck('id')
            //     ->first();

            $supplier_id = $request->supplid[$key];

            $credit = DB::table('supplier_credits')
                ->select(DB::raw('due_amt'))
                ->where('supplier_id', $supplier_id)
                ->pluck('due_amt')
                ->first();

            $paid = DB::table('supplier_credits')
                ->select(DB::raw('collected_amt'))
                ->where('supplier_id', $supplier_id)
                ->pluck('collected_amt')
                ->first();

            $credit -= $paid;

            $fund = new SupplierFundHistory();
            $fund->suppliername = $request->shop_name[$key];
            $fund->collectedamount = $request->amount[$key];
            $fund->supplierid = $supplier_id;
            $fund->due = $credit;
            $fund->userid = Session('softwareuser');
            $fund->branch = $branch;
            $fund->status = '1';
            $fund->save();

            $creditcollected = DB::table('supplier_credits')
                ->where('supplier_id', $supplier_id)
                ->pluck('collected_amt')
                ->first();

            if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {
                $livecollected = $request->amount[$key];  // If checked, assign credit_note[]
            }
            else{
                $livecollected = $request->credit_note[$key];
            }
            $totalcreditcollected = $creditcollected + $livecollected;
            $creditsummaries = DB::table('supplier_credits')
                ->updateOrInsert(
                    ['supplier_id' => $supplier_id],
                    ['collected_amt' => $totalcreditcollected]
                );

            // credit credit_supplier_transactions

            $lastTransaction = DB::table('credit_supplier_transactions')
                ->where('credit_supplier_id', $supplier_id)
                ->where('location', $branch)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

                $lastSameTransaction = DB::table('credit_supplier_transactions')
                ->where('credit_supplier_id', $supplier_id)
                ->where('location', $branch)
                ->where('reciept_no', $request->reciept_no[$key])
                ->orderBy('created_at', 'desc')
                ->first();
                $previous_due = $lastSameTransaction->balance_due ?? 0;


            $updated_balance = $lastTransaction->updated_balance;
            $new_due = $updated_balance;
            $new_updated_bal = $new_due - $livecollected;
            $new_balance_due=$previous_due - $livecollected;

            $credit_trans_suppr = new CreditSupplierTransaction();
            $credit_trans_suppr->credit_supplier_id = $supplier_id;
            $credit_trans_suppr->credit_supplier_username = $request->shop_name[$key];
            $credit_trans_suppr->user_id = Session('softwareuser');
            $credit_trans_suppr->location = $branch;
            $credit_trans_suppr->due = $new_due;
            $credit_trans_suppr->collectedamount = $livecollected;
            $credit_trans_suppr->updated_balance = $new_updated_bal;
            $credit_trans_suppr->balance_due = $new_balance_due;
            $credit_trans_suppr->comment = 'Debit Note';
            $credit_trans_suppr->reciept_no = $request->reciept_no[$key];
            $credit_trans_suppr->save();
        } elseif ($request->ptype[$key] == 1 && ($supplier_id != '' || $supplier_id != null)) {
            $supplier_id = $request->supplid[$key];

            $lastTransaction = DB::table('cash_supplier_transactions')
                ->where('cash_supplier_id', $supplier_id)
                ->where('location', $branch)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if ($request->has('toggle_creditnote') && $request->toggle_creditnote == 'yes') {
                $livecollecteds = $request->amount[$key];  // If checked, assign credit_note[]
            }
            else{
                $livecollecteds = $request->credit_note[$key];
            }
            $updated_balance = $lastTransaction->updated_balance ?? null;
            $new_due = $updated_balance - $livecollecteds;

            $cash_trans = new CashSupplierTransaction();
            $cash_trans->cash_supplier_id = $supplier_id;
            $cash_trans->cash_supplier_username = $request->shop_name[$key];
            $cash_trans->user_id = Session('softwareuser');
            $cash_trans->location = $branch;
            $cash_trans->reciept_no = $request->reciept_no[$key];
            $cash_trans->collected_amount = $livecollecteds;
            $cash_trans->updated_balance = $new_due;
            $cash_trans->comment = 'Debit Note';
            $cash_trans->payment_type = $request->ptype[$key];
            $cash_trans->save();
        }
         // bank---------------------------------------------------------------------------


    }


    /* ------------------GET IP ADDRESS--------------------------------------- */

    $userid = Session('softwareuser');
    $ip = request()->ip();
    $uri = request()->fullUrl();

    $username = Softwareuser::where('id', $userid)->pluck('username')->first();

    $user_type = 'websoftware';

    $message = $username.' Debit Note Applied';
    $locationdata = (new otherService())->get_location($ip);

    $branch_id = Softwareuser::where('id', $userid)->pluck('location')->first();

    if ($locationdata != false) {
        $activityservice = (new activityService($userid, $ip, $uri, $message, $user_type, $locationdata))->ipaddress_store($branch_id);
    }

    /* ----------------------------------------------------------------------- */
    session()->flash('success', 'Debit Note Applied Successfully');
    return redirect('/debitnote');
}

public function DebitNotseHistory()
{
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }


        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

            $data = DB::table('debit_note')
            ->leftJoin('products', 'credit_note.product_id', '=', 'products.id')
            ->leftJoin('buyproducts', 'credit_note.transaction_id', '=', 'buyproducts.transaction_id')
            // ->leftJoin(DB::raw('(SELECT
            //                         transaction_id COLLATE utf8mb4_unicode_ci as transaction_id,
            //                         SUM(totalamount_wo_discount) as return_grandtotal_without_discount,
            //                         SUM(COALESCE(discount_amount, 0)) + SUM(total_amount * (total_discount_percent / 100)) as return_discount_amount,
            //                         SUM(DISTINCT COALESCE(grand_total, 0)) as return_sum
            //                     FROM returnproducts
            //                     GROUP BY transaction_id) as returns'), 'buyproducts.transaction_id', '=', 'returns.transaction_id')
            // ->leftJoin(DB::raw('(SELECT transaction_id, SUM(credit_note_amount) as total_credit_note_amount
            //                      FROM (SELECT DISTINCT transaction_id, credit_note_id, credit_note_amount
            //                            FROM credit_note) as unique_credits
            //                      GROUP BY transaction_id) as credit_sums'), 'credit_note.transaction_id', '=', 'credit_sums.transaction_id')
            ->select(
                'credit_note.id as id',
                'credit_note.customer as customer',
                'credit_note.credit_note_id as credit_note_id',
                'credit_note.transaction_id as transaction_id',
                'credit_note.created_at as created_at',
                'buyproducts.phone as phone',
                'credit_note.vat_type as vat_type',
                'credit_note.quantity',
                'credit_note.payment_type as payment_type',
                DB::raw('SUM(credit_note.totalamount_wo_discount) as grandtotal_without_discount'),
                DB::raw('SUM(COALESCE(buyproducts.discount_amount, 0))
                          + SUM(buyproducts.total_amount * (buyproducts.total_discount_percent / 100)) as discount_amount'),
                DB::raw('SUM(credit_note.vat_amount) as vat'),
                DB::raw('COALESCE(credit_note.credit_note_amount, 0) as credit_note_amount'), // Corrected calculation
                DB::raw('GROUP_CONCAT(DISTINCT products.product_name) as product_names'),
                DB::raw('GROUP_CONCAT(credit_note.quantity) as quantities'),
                // DB::raw('COALESCE(returns.return_grandtotal_without_discount, 0) as return_grandtotal_without_discount'),
                // DB::raw('COALESCE(returns.return_discount_amount, 0) as return_discount_amount'),
                // DB::raw('COALESCE(returns.return_sum, 0) as return_sum'),
                DB::raw('SUM(DISTINCT COALESCE(credit_note.grand_total, 0)) as sum'),
                // DB::raw('SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0)) as bill_grand_total')
            )
            ->groupBy('credit_note.credit_note_id')
            ->orderBy('credit_note.created_at', 'DESC')
            ->where('credit_note.branch', $branch)
            ->get();

        $userid = Session('softwareuser');

        $item = DB::table('softwareusers')
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

        $options = [
            'products' => $data,
            'users' => $item,
            'currency' => $currency,
            'tax'=>$tax,
        ];


    return view('/billingdesk/creditnote_history', $options);
}

public function DebitNoteHistory()
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

    $adminid = Softwareuser::Where('id', $userid)
        ->pluck('admin_id')
        ->first();
    $currency = Adminuser::Where('id', $adminid)
        ->pluck('currency')
        ->first();

    $tax = Adminuser::Where('id', $adminid)
    ->pluck('tax')
    ->first();



    $purchases = DB::table('debit_note')
        ->leftJoin('products', 'debit_note.product_id', '=', 'products.id')
        ->select(
            DB::raw('debit_note.id as id'),
            DB::raw('debit_note.reciept_no as reciept_no'),
            DB::raw('debit_note.created_at as created_at'),
            DB::raw('debit_note.debit_note_id as debit_note_id'),
            DB::raw('debit_note.shop_name as supplier'),
            DB::raw('SUM(debit_note.amount_without_vat) as total_price_without_vat'),
            DB::raw('SUM(debit_note.amount - debit_note.amount_without_vat) as total_vat'),
            DB::raw('SUM(debit_note.amount) as grand_total'),
            DB::raw('GROUP_CONCAT(products.product_name) as product_names'),
            DB::raw('GROUP_CONCAT(debit_note.quantity) as quantities'),
            DB::raw('COALESCE(debit_note.debit_note_amount, 0) as debit_note_amount'), // Corrected calculation

        )
        ->groupBy('debit_note.debit_note_id')
        ->orderBy('debit_note.created_at', 'DESC')
        ->where('debit_note.branch', $branch)
        ->get();

    // return view('/inventory/purchasereturnhistory', array('users' => $item, 'purchases' => $purchases));

    return view('/inventory/debitnote_history', ['tax'=>$tax,'users' => $item, 'purchases' => $purchases, 'currency' => $currency]);
}

public function supplierSummary()
{
        if (session()->missing('softwareuser')) {
            return redirect('userlogin');
        }


        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

            $data = DB::table('debit_note_summary')
                ->select('supplier', 'debit_note_amount')
                ->where('debit_note_amount', '>', 0)
                // ->where('branch', $branch)
                ->get();

        $userid = Session('softwareuser');

        $item = DB::table('softwareusers')
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

        $options = [
            'products' => $data,
            'users' => $item,
            'currency' => $currency,
            'tax'=>$tax,
        ];


    return view('/inventory/supplier_summary', $options);
}

}
