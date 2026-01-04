<?php

namespace App\Services;

use App\Models\CashSupplierTransaction;
use App\Models\CreditSupplierTransaction;
use App\Models\Product;
use App\Models\Stockdetail;
use App\Models\Stockhistory;
use App\Models\Softwareuser;
use App\Models\Bankhistory;
use Carbon\Carbon;
use App\Models\StockPurchaseReport;
use App\Repositories\Interfaces\EditPurchaseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditPurchaseService
{
    protected $editPurchaseRepository;

    public function __construct(EditPurchaseRepositoryInterface $editPurchaseRepository)
    {
        $this->editPurchaseRepository = $editPurchaseRepository;
    }

    public function getPurchaseInfo($receiptNo, $branch, $field)
    {
        return $this->editPurchaseRepository->getPurchaseDetails($receiptNo, $branch, $field);
    }

    // Example usage in your service class methods
    public function getComment($receiptNo, $branch)
    {
        return $this->getPurchaseInfo($receiptNo, $branch, 'comment');
    }
    public function getSupplier($receiptNo, $branch)
    {
        return $this->getPurchaseInfo($receiptNo, $branch, 'supplier');
    }
    public function getSupplierID($receiptNo, $branch)
    {
        return $this->getPurchaseInfo($receiptNo, $branch, 'supplier_id');
    }
    public function getPaymentType($receiptNo, $branch)
    {
        return $this->getPurchaseInfo($receiptNo, $branch, 'payment_mode');
    }
    public function getInvoiceDate($receiptNo, $branch)
    {
        return $this->getPurchaseInfo($receiptNo, $branch, 'invoice_date');
    }
    public function getBankID($receiptNo, $branch)
    {
        return $this->getPurchaseInfo($receiptNo, $branch, 'bank_id');
    }
    public function getAccountName($receiptNo, $branch)
    {
        return $this->getPurchaseInfo($receiptNo, $branch, 'account_name');
    }

    public function getProducts($branch)
    {
        return $this->editPurchaseRepository->getProducts($branch);
    }

    public function getDataPlan($receiptNo, $branch)
    {
        return $this->editPurchaseRepository->getProductsByPurchase($receiptNo, $branch);
    }

    // edit purchase submit work starts

    // old purchase stockdetails table management
    public function OldStockdetailsDataCheck($purchase_exist, $receiptNo, $branch)
    {

        if ($purchase_exist) {

            $grandtotal = Stockdetail::where('reciept_no', $receiptNo)
                ->where('branch', $branch)
                ->select(DB::raw("SUM(price) as price"))
                ->pluck('price')
                ->first();

                $totaldiscount = Stockdetail::where('reciept_no', $receiptNo)
                ->where('branch', $branch)
                ->select(DB::raw("SUM(discount) as discount"))
                ->pluck('discount')
                ->first();

            $old_purchase_data = Stockdetail::where('reciept_no', $receiptNo)
                ->get();

            // Store old quantities in an array
            $old_quantities = [];

            // Loop through the old transaction data to calculate the difference in quantity
            foreach ($old_purchase_data as $old_data) {
                $productID = $old_data->product;
                $old_quantity = $old_data->remain_stock_quantity;
                $old_method = $old_data->method;
                $old_quantities[$productID] = $old_quantity;
                $product_ids[$productID] = $productID;
            }

            $data = [
                'grandtotal' => $grandtotal,
                'old_purchase_data' => $old_purchase_data,
                'old_quantities' => $old_quantities,
                'product_ids' => $product_ids,
                'totaldiscount'=>$totaldiscount,
                'old_method'=>$old_method
            ];

            /* ------------ stock purchase reports table ---------------- */

            $stock_rep = DB::table('stock_purchase_reports')
                ->where('receipt_no', $receiptNo)
                ->where('branch_id', $branch)
                ->exists();

            if ($stock_rep) {

                $prev_pr_stock_repts_data = DB::table('stock_purchase_reports')
                    ->where('receipt_no', $receiptNo)
                    ->where('branch_id', $branch)
                    ->get();

                $data_1 = [
                    'prev_pr_stock_repts_data' => $prev_pr_stock_repts_data
                ];
            }
        }
        return [$data, $data_1];
    }

    //submit edit purchase data
    public function editPurchaseData($request, $old_purchase_data, $old_quantities, $branch, $user_id, $grandtotal,$totaldiscount,$old_method)
    {
        $receiptNo = $request->reciept_no;

        $boxDozens = $request->input('boxdozen');
        $boxCounts = $request->input('boxCount');
        $boxItems = $request->input('boxItem');
        $dozenCounts = $request->input('dozenCount');
        $dozenItems = $request->input('dozenItem');
        $units = $request->input('unit');
        $prices = $request->input('total');
        $buycosts = $request->input('buy_cost');
        $sellcosts = $request->input('sell_cost');
        $rates = $request->input('rate_r');
        $vats = $request->input('vat_r');
        $priceswithoutvat = $request->input('without_vat');
        $suppliertId = $request->input('supp_id');

        if (($request->payment_type == 1) || ($request->payment_type == 2)|| ($request->payment_type == 3)) {

            $i = 1;

            foreach ($request->product_id as $key => $productID) {

                $stockDetailsExist = $this->editPurchaseRepository->checkStockDetailsExist($receiptNo, $productID, $branch);

                if ($stockDetailsExist) {
                    // common data's updates
                    $this->editPurchaseRepository->updateCommonStockDetails($request, $receiptNo, $productID, $branch);

                    $stockPurchaseReport = StockPurchaseReport::where('product_id', $productID)
                        ->where('receipt_no', $receiptNo)
                        ->select('quantity', 'sell_quantity', 'purchase_id')
                        ->where('branch_id', $branch)
                        ->first();

                    if ($stockPurchaseReport && $stockPurchaseReport->quantity == $stockPurchaseReport->sell_quantity) {
                        // Proceed with storing/updating the product in the database
                    $totalPrice = array_sum($prices);
                    $proportionalDiscount = ($totalPrice > 0) ? ($request->discount_amount * $prices[$key]) / $totalPrice : 0;
                    $quantity = null;
                    if ($boxDozens[$key] == 1) {
                        $quantity = $boxItems[$key];
                    } elseif ($boxDozens[$key] == 2) {
                        $quantity = $dozenItems[$key];
                    } elseif ($boxDozens[$key] == 3) {
                        $quantity = $boxItems[$key];
                    }
                    
                    // Calculate discount percent only if quantity is valid
                    $discountPercent = ($quantity && $quantity > 0) ? $proportionalDiscount / $quantity : 0;
                    
                    $stockdetailsData = Stockdetail::where('reciept_no', $receiptNo)
                            ->where('product', $productID)
                            ->where('branch', $branch)
                            ->update([
                                'is_box_or_dozen' => $boxDozens[$key],
                                'price' => $prices[$key],
                                'discount' => $proportionalDiscount,
                                'discount_percent' => $discountPercent,
                                'buycost' => $buycosts[$key],
                                'sellingcost' => $sellcosts[$key],
                                'price_without_vat' => $priceswithoutvat[$key],
                                'rate' => $rates[$key],
                                'vat' => $vats[$key],
                                'box_dozen_count' => $boxDozens[$key] == 1 ? $boxCounts[$key] : ($boxDozens[$key] == 2 ? $dozenCounts[$key] : null),
                                'quantity' => $quantity,
                                'remain_stock_quantity' => $quantity,
                            ]);

                        // Check if the 'camera' file is present in the request
                        if (!empty($request->file('camera'))) {
                            $ext = $request->file('camera')->getClientOriginalExtension();
                            $filename = 'STOCK_DAT' . date('d-m-y_h-i-s') . '.' . $ext;

                            // Update the 'file' column in the stockdetailsData record
                            Stockdetail::where('reciept_no', $receiptNo)
                                ->where('product', $productID)
                                ->where('branch', $branch)
                                ->update(['file' => $filename]);

                            // Store the file in the 'stockbills' directory
                            $path = $request->file('camera')->storeAs('stockbills', $filename);
                        }

                        /* ----------- stock history table management ------------ */
                        Stockhistory::where('receipt_no', $receiptNo)
                            ->where('product_id', $productID)
                            ->update([
                                'buycost' => $buycosts[$key],
                                'sellingcost' => $sellcosts[$key],
                                'rate' => $rates[$key],
                                'vat' => $vats[$key],
                                'quantity' => $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null)),
                                'discount' => $proportionalDiscount,
                                'discount_percent' => $discountPercent,
                                'remain_qantity' => $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null)),
                                'sell_qantity' => $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null)),
                            ]);
                        /* ------------------------------------------------------ */
                        /* ----------- stock purchase reports table management ------------ */
                        StockPurchaseReport::where('receipt_no', $receiptNo)
                            ->where('purchase_id', $stockPurchaseReport->purchase_id)
                            ->where('product_id', $productID)
                            ->where('branch_id', $branch)
                            ->update([
                                'PBuycost' => $buycosts[$key],
                                'PBuycostRate' => $rates[$key],
                                'PSellcost' => $sellcosts[$key],
                                'quantity' => $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null)),
                                'remain_main_quantity' => $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null)),
                                'sell_quantity' => $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null)),
                            ]);
                        /* --------------------------------------------------------------- */
                    } else {
                        // Quantity and sell_quantity are not equal, skip or handle accordingly
                        continue;
                    }
                } else {
                    $this->createNewStockDetails($request, $receiptNo, $productID, $boxDozens, $boxCounts, $boxItems, $dozenCounts, $dozenItems, $units, $prices, $buycosts, $sellcosts, $rates, $vats, $priceswithoutvat, $suppliertId, $branch, $user_id, $key, $i);
                }

                // Update product remaining stock

                $newQuantity = $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null));
                $this->editPurchaseRepository->updateProductPurchaseStock($productID, $newQuantity, $old_purchase_data, $old_quantities, $buycosts, $sellcosts, $rates, $vats, $key);
            }
        }

        // credit purchase management
        $this->EditPurchasecreditManage($request, $branch, $grandtotal,$totaldiscount,$old_method, $user_id, $receiptNo, $productID, $prices, $key, $suppliertId);
    }

    private function createNewStockDetails($request, $receiptNo, $productID, $boxDozens, $boxCounts, $boxItems, $dozenCounts, $dozenItems, $units, $prices, $buycosts, $sellcosts, $rates, $vats, $priceswithoutvat, $suppliertId, $branch, $user_id, $key, $i)
    {
        $old_purchase_datas = Stockdetail::where('reciept_no', $receiptNo)
            ->where('branch', $branch)
            ->first();

        $oldstock_rep_dat = DB::table('stock_purchase_reports')
            ->where('receipt_no', $receiptNo)
            ->where('branch_id', $branch)
            ->first();

        $oldstockhistories_rep_dat = DB::table('stockhistories')
            ->where('receipt_no', $receiptNo)
            ->first();
            $discountAmount = $request->discount_amount; // Total discount amount
            $prices = $request->input('total');
            $totalPrice = array_sum($prices);
            $discounts = [];

            $discounts[$key] = ($discountAmount * $prices[$key]) / $totalPrice;

        $data = [
            'reciept_no' => $receiptNo,
            'comment' => $request->input('comment'),
            'supplier' => $request->input('supplier'),
            'supplier_id' => $suppliertId,
            'payment_mode' => $request->input('payment_type'),
            'user_id' => $user_id,
            'branch' => $branch,
            'product' => $productID,
            'is_box_or_dozen' => $boxDozens[$key],
            'unit' => $units[$key],
            'price' => $prices[$key],
            'buycost' => $buycosts[$key],
            'sellingcost' => $sellcosts[$key],
            'price_without_vat' => $priceswithoutvat[$key],
            'discount'=>$discounts[$key],
            'discount_percent'=>$discounts[$key] / ($boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null))),
            'rate' => $rates[$key],
            'vat' => $vats[$key],
            'box_dozen_count' => $boxDozens[$key] == 1 ? $boxCounts[$key] : ($boxDozens[$key] == 2 ? $dozenCounts[$key] : null),
            'quantity' => $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null)),
            'remain_stock_quantity' => $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null)),
            'edit' => 1,
            'edit_comment' => $request->edit_comment,
            'created_at' => $old_purchase_datas->created_at,
            'method'=>$old_purchase_datas->method
        ];

        // Save stockdetails data
        $lastInsertedId = $this->editPurchaseRepository->saveStockDetails($data, $request);

        // stock history management for new products
        $s_data = [
            'user_id' => $user_id,
            'product_id' => $productID,
            'receipt_no' => $receiptNo,
            'buycost' => $buycosts[$key],
            'sellingcost' => $sellcosts[$key],
            'discount'=>$discounts[$key],
            'discount_percent'=>$discounts[$key] / ($boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null))),
            'rate' => $rates[$key],
            'vat' => $vats[$key],
            'quantity' => $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null)),
            'remain_qantity' => $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null)),
            'sell_qantity' => $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null)),
            'created_at' => $oldstockhistories_rep_dat->created_at,
        ];

        // Save stockdetails data
        $this->editPurchaseRepository->saveStockHistories($s_data);

        // stock purchase reports management for new products

        $count = DB::table('stock_purchase_reports')
            ->distinct()
            ->count('purchase_trans_id');

        $count = $count + 1;

        $i_count = DB::table('stock_purchase_reports')
            ->where('receipt_no', $receiptNo)
            ->where('branch_id', $branch)
            ->count('receipt_no');

        $i = $i + $i_count;

        $new_pro_stock = new StockPurchaseReport();
        $new_pro_stock->purchase_id = $lastInsertedId;
        $new_pro_stock->receipt_no = $receiptNo;
        $new_pro_stock->purchase_trans_id = "PID" . $branch . $user_id . $productID . $count . $i;
        $new_pro_stock->product_id = $productID;
        $new_pro_stock->user_id = $user_id;
        $new_pro_stock->branch_id = $branch;
        $new_pro_stock->PBuycost = $buycosts[$key];
        $new_pro_stock->PSellcost = $sellcosts[$key];
        $new_pro_stock->quantity = $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null));
        $new_pro_stock->remain_main_quantity = $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null));
        $new_pro_stock->sell_quantity = $boxDozens[$key] == 1 ? $boxItems[$key] : ($boxDozens[$key] == 2 ? $dozenItems[$key] : ($boxDozens[$key] == 3 ? $boxItems[$key] : null));
        $new_pro_stock->PBuycostRate = $rates[$key];
        $new_pro_stock->created_at = $oldstock_rep_dat->created_at;
        $new_pro_stock->save();

        $i++;
    }

    private function EditPurchasecreditManage($request, $branch, $grandtotal,$totaldiscount,$old_method, $user_id, $receiptNo, $productID, $prices, $key, $suppliertId)
    {

        $creditsupp_date = DB::table('credit_supplier_transactions')
            ->where('reciept_no', $receiptNo)
            ->where('location', $branch)
            ->first();

            $discountAmount = $request->discount_amount;

                $discountDifference = $discountAmount;
                $finalgrand=$grandtotal-$totaldiscount;

            $pricetotal = 0;
            $productCount = count($request->product_id);

            foreach ($request->product_id as $key => $productID) {
                $individualDiscount = $discountDifference / $productCount;

                $pricetotal += $prices[$key] - $individualDiscount;

            }

        if ($request->payment_type == 2) {

            $dueamount =  DB::table('supplier_credits')
                ->where('supplier_id', $suppliertId)
                ->pluck('due_amt')
                ->first();

            $lastTransaction = DB::table('credit_supplier_transactions')
                ->where('credit_supplier_id', $suppliertId)
                ->where('location', $branch)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

                $updated_balance = $lastTransaction->updated_balance ?? NULL;

                $lastSameTransaction = DB::table('credit_supplier_transactions')
                ->where('credit_supplier_id', $suppliertId)
                ->where('location', $branch)
                ->where('reciept_no',$receiptNo)
                ->orderBy('updated_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

                $previous_due = $lastSameTransaction->balance_due ?? 0;


            $diff_remain = $finalgrand > $pricetotal ? $finalgrand - $pricetotal : $pricetotal - $finalgrand;
            $prieupload  = $finalgrand > $pricetotal ? $dueamount - $diff_remain : $dueamount + $diff_remain ;
            $new_due = $finalgrand > $pricetotal ? $updated_balance - $diff_remain : $updated_balance + $diff_remain;

            $suppliercredit = DB::table('supplier_credits')
                ->updateOrInsert(
                    ['supplier_id' => $suppliertId],
                    ['due_amt' => $prieupload]
                );

                $new_balance_due = $finalgrand < $pricetotal
                ? $previous_due + $diff_remain
                : $previous_due - $diff_remain;

                // dd($finalgrand,$totaldiscount, $discountDifference,$pricetotal);

                if ($prieupload != $finalgrand) {
                    Log::info('Condition passed: $prieupload is not equal to $grandtotal');

                    $credit_supp_trans = new CreditSupplierTransaction();
                    $credit_supp_trans->credit_supplier_id = $suppliertId;
                    $credit_supp_trans->credit_supplier_username = $request->supplier;
                    $credit_supp_trans->user_id = $user_id;
                    $credit_supp_trans->location = $branch;
                    $credit_supp_trans->balance_due = $new_balance_due;
                    $credit_supp_trans->reciept_no = $receiptNo;
                    $credit_supp_trans->due = $updated_balance ?? 0;
                    $credit_supp_trans->collectedamount = $finalgrand > $prieupload ? $diff_remain : NULL;
                    $credit_supp_trans->Invoice_due = $finalgrand > $prieupload ? NULL : $diff_remain;
                    $credit_supp_trans->updated_balance = $new_due;
                    $credit_supp_trans->comment = $finalgrand > $prieupload ? "Purchase Returned" : "Bill";
                    $credit_supp_trans->edit_Purchase = "1";
                    $credit_supp_trans->created_at = $creditsupp_date->created_at ?? now();

                    Log::info('Saving CreditSupplierTransaction:', $credit_supp_trans->toArray());

                    $credit_supp_trans->save();
                    Log::info('Transaction saved successfully.');
                } else {
                    Log::info('Condition failed: $prieupload is equal to $grandtotal');
                }

        } else if ($request->payment_type == 1 || $request->payment_type == 3 && ($suppliertId != "" || $suppliertId != NULL)) {

            $cashsupp_date = DB::table('cash_supplier_transactions')
                ->where('reciept_no', $receiptNo)
                ->where('location', $branch)
                ->first();

            $lastTransaction = DB::table('cash_supplier_transactions')
                ->where('cash_supplier_id', $suppliertId)
                ->where('location', $branch)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $updated_balance = $lastTransaction->updated_balance ?? NULL;

            $diff_remain = $finalgrand > $pricetotal ? $finalgrand - $pricetotal : $pricetotal - $finalgrand;
            $new_due = $finalgrand > $pricetotal ? $updated_balance - $diff_remain : $updated_balance + $diff_remain;

            if ($pricetotal != $finalgrand) {
                $cash_supp_trans = new CashSupplierTransaction();
                $cash_supp_trans->cash_supplier_id = $suppliertId;
                $cash_supp_trans->cash_supplier_username = $request->supplier;
                $cash_supp_trans->user_id = $user_id;
                $cash_supp_trans->location = $branch;
                $cash_supp_trans->reciept_no = $receiptNo;
                $cash_supp_trans->collected_amount =  $diff_remain;
                $cash_supp_trans->updated_balance = $new_due;
                $cash_supp_trans->comment = $finalgrand > $pricetotal ? "Purchase Returned" : "Bill";
                $cash_supp_trans->edit_Purchase = "1";
                $cash_supp_trans->payment_type = $request->payment_type;
                $cash_supp_trans->created_at = $cashsupp_date->created_at ?? now();
                $cash_supp_trans->save();
            }
        }
    }
}
