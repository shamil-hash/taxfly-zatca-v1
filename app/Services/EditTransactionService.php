<?php

namespace App\Services;

use App\Models\BillHistory;
use App\Models\Buyproduct;
use App\Models\NewBuyproduct;
use App\Models\CashTransStatement;
use App\Models\CreditTransaction;
use App\Models\StockPurchaseReport;
use App\Repositories\Interfaces\EditTransactionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EditTransactionService
{
    protected $editTransRepository;

    public function __construct(EditTransactionRepositoryInterface $editTransRepository)
    {
        $this->editTransRepository = $editTransRepository;
    }

    public function getItems($branch)
    {
        return $this->editTransRepository->getProductsByBranch($branch);
    }

    public function getDataPlan($transactionId)
    {
        return $this->editTransRepository->getBuyProductsByTransaction($transactionId);
    }

    public function getTransactionInfo($transactionId, $field)
    {
        return $this->editTransRepository->getTransactionDetails($transactionId, $field);
    }

    // Example usage in your service class methods
    public function getCustomerName($transactionId)
    {
        return $this->getTransactionInfo($transactionId, 'customer_name');
    }

    public function getTRN($transactionId)
    {
        return $this->getTransactionInfo($transactionId, 'trn_number');
    }

    public function getPhone($transactionId)
    {
        return $this->getTransactionInfo($transactionId, 'phone');
    }

    public function getPaymentType($transactionId)
    {
        return $this->getTransactionInfo($transactionId, 'payment_type');
    }

    public function getEmail($transactionId)
    {
        return $this->getTransactionInfo($transactionId, 'email');
    }

    public function getTransact_count($transactionId)
    {
        return $this->editTransRepository->getTransactionCount($transactionId);
    }

    public function getVatType($transactionId)
    {
        return $this->getTransactionInfo($transactionId, 'vat_type');
    }

    public function getCreditUserId($transactionId)
    {
        return $this->getTransactionInfo($transactionId, 'credit_user_id');
    }

    public function getCreatedAt($transactionId)
    {
        return $this->getTransactionInfo($transactionId, 'created_at');
    }

    public function getToInvoice($transactionId)
    {
        return $this->getTransactionInfo($transactionId, 'to_invoice');
    }

    public function getSalesorderTransID($transactionId)
    {
        return $this->getTransactionInfo($transactionId, 'sales_order_trans_ID');
    }

    public function getQuotationTransID($transactionId)
    {
        return $this->getTransactionInfo($transactionId, 'quotation_trans_ID');
    }

    public function getCashUserId($transactionId)
    {
        return $this->getTransactionInfo($transactionId, 'cash_user_id');
    }

    // edit transaction work

    public function ProcessBill(Request $request, $transaction_id, $branch, $user_id)
    {
        $trans_exist = DB::table('buyproducts')->where('transaction_id', $transaction_id)->exists();

        $oldBuyproductstransdata = $this->OldBuyproductsTransDataCheck($trans_exist, $transaction_id);

        $grandtotal = $oldBuyproductstransdata['grandtotal'];
        $old_created_at_date = $oldBuyproductstransdata['old_created_at_date'];
        $old_trans_data = $oldBuyproductstransdata['old_trans_data'];
        $old_quantities = $oldBuyproductstransdata['old_quantities'];
        $old_to_invoice = $oldBuyproductstransdata['old_to_invoice'];
        $old_salesordertransID = $oldBuyproductstransdata['old_salesordertransID'];
        $old_quotationtransID = $oldBuyproductstransdata['old_quotationtransID'];

        $oldStockdats_data = $this->OldStockdatsTransCheck($transaction_id);

        $prev_created_at_stockdat = $oldStockdats_data['prev_created_at_stockdat'];
        $prev_trans_stockdat = $oldStockdats_data['prev_trans_stockdat'];

        $this->editSubmitData($request, $old_trans_data, $old_quantities, $branch, $user_id, $old_created_at_date, $prev_created_at_stockdat, $old_to_invoice, $old_salesordertransID, $old_quotationtransID);

        /* ----------------------------------- credit -------------------------------------------- */

            $this->EditTranscreditManage($request, $branch, $grandtotal, $user_id, $transaction_id);
        
        /* ---------------------------- Purchase wise stock management --------------------------- */

        foreach ($request->product_id as $key => $productID) {
            $product_ids = $oldBuyproductstransdata['product_ids'];

            $inputQuantity = $request->quantity[$key];

            $buycostadd = 0;
            $buycost_rate_add = 0;

            if (in_array($productID, $product_ids)) {
                /* ----------------- if the product exist in this transaction------------------- */

                // Get the purchases from the billhistory table
                $bill_purchases = BillHistory::where('trans_id', $transaction_id)
                    ->where('product_id', $productID)
                    ->where('branch_id', $branch)
                    ->get();

                $index = 0;

                while ($index < count($bill_purchases)) {
                    $purchase = $bill_purchases[$index];

                    // Get the purchase quantity and sold quantity
                    $purchaseQuantity = $purchase->remain_sold_quantity;

                    // Get the current sell quantity from stockpurchasereport
                    $currentSellQuantity = StockPurchaseReport::getSellQuantity($purchase->puid, $purchase->pid, $branch);

                    if ($inputQuantity > $purchaseQuantity) {
                        $remainQuantity = $inputQuantity - $purchaseQuantity;
                        $inputQuantity = $remainQuantity;

                        $buycostadd += ($purchaseQuantity * $purchase->Purchase_buycost);
                        $buycost_rate_add += ($purchaseQuantity * $purchase->Purchase_Buycost_Rate);

                        BillHistory::where('trans_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch_id', $branch)
                            ->update([
                                'billing_Sellingcost' => $request->mrp[$key],
                                'billing_inclusive_rate' => ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$productID] : null,
                                'netrate' => $request->net_rate[$key],
                            ]);

                        BillHistory::where('trans_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch_id', $branch)
                            ->where('puid', $purchase->puid)
                            ->where('pid', $purchase->pid)
                            ->update([
                                // 'discount_type' => $request->dis_count_type[$key],
                                // 'discount' => $request->dis_count_type[$key] == "none" ? $request->dis_count[$key] : ($request->dis_count_type[$key] == "percentage" ? $request->dis_count[$key] : ($request->dis_count_type[$key] == "amount" ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] / (1 + ($request->fixed_vat[$key] / 100)) : $request->mrp[$key])) * 100 : 0)),
                                // 'discount_amount' => $request->dis_count_type[$key] == "percentage" ?
                                //     ($request->vat_type_value == 1 ? ($request->mrp[$key] / (1 + ($request->fixed_vat[$key] / 100))) * ($request->dis_count[$key] / 100) : $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == "amount" ? $request->dis_count[$key] : 0),

                                'discount_type' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key],

                                'discount' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                                    ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))),

                                'discount_amount' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                                    ($request->dis_count__tp_ori[$key] == 'percentage' ?
                                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0)),

                                'total_discount_type' => $request->total_discount,
                                'total_discount_percent' => $request->discount_percentage != '' || $request->discount_percentage != null ?
                                    $request->discount_percentage : (($request->bill_grand_total_wo_discount != 0) ? (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100) : 0),
                                'total_discount_amount' => $request->discount_amount != '' || $request->discount_amount != null ?
                                    $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100)),
                                'bill_grand_total' => $request->bill_grand_total,
                                'bill_grand_total_wo_discount' => $request->bill_grand_total_wo_discount,
                            ]);

                        // Check if there are more purchases
                        if ($index < count($bill_purchases) - 1) {
                            ++$index;
                            continue; // Move to the next purchase
                        } elseif ($index == count($bill_purchases) - 1) {
                            $this->newPurchaseQuantityManage($request, $productID, $branch, $transaction_id, $user_id, $inputQuantity, $buycostadd, $buycost_rate_add);
                            break;
                        }
                    } elseif ($inputQuantity < $purchaseQuantity) {
                        $remainQuantity = $purchaseQuantity - $inputQuantity;

                        StockPurchaseReport::updateSellQuantity($purchase->puid, $purchase->pid, $productID, $currentSellQuantity + $remainQuantity);

                        BillHistory::updateQuantity($transaction_id, $productID, $branch, $purchase->puid, $purchase->pid, $inputQuantity, $request);

                        $buycostadd += ($inputQuantity * $purchase->Purchase_buycost);
                        $buycost_rate_add += ($inputQuantity * $purchase->Purchase_Buycost_Rate);

                        Buyproduct::where('transaction_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch', $branch)
                            ->update([
                                'buycostadd' => $buycostadd,
                                'buycost_rate_add' => $buycost_rate_add,
                            ]);

                            NewBuyproduct::where('transaction_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch', $branch)
                            ->update([
                                'buycostadd' => $buycostadd,
                                'buycost_rate_add' => $buycost_rate_add,
                            ]);

                        BillHistory::where('trans_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch_id', $branch)
                            ->update([
                                'billing_Sellingcost' => $request->mrp[$key],
                                'billing_inclusive_rate' => ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$productID] : null,
                                'netrate' => $request->net_rate[$key],
                            ]);

                        BillHistory::where('trans_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch_id', $branch)
                            ->where('puid', $purchase->puid)
                            ->where('pid', $purchase->pid)
                            ->update([
                                // 'discount_type' => (isset($request->productStatus[$key]) && ($request->productStatus[$key] == 0)) ? $request->dis_count__typee[$key] : $request->dis_count_type[$key],
                                // 'discount' => $request->dis_count_type[$key] == "none" ? $request->dis_count[$key] : ($request->dis_count_type[$key] == "percentage" ? $request->dis_count[$key] : ($request->dis_count_type[$key] == "amount" ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] / (1 + ($request->fixed_vat[$key] / 100)) : $request->mrp[$key])) * 100 : 0)),
                                // 'discount_amount' => $request->dis_count_type[$key] == "percentage" ?
                                //     ($request->vat_type_value == 1 ? ($request->mrp[$key] / (1 + ($request->fixed_vat[$key] / 100))) * ($request->dis_count[$key] / 100) : $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == "amount" ? $request->dis_count[$key] : 0),

                                'discount_type' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key],

                                'discount' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                                    ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))),

                                'discount_amount' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                                    ($request->dis_count__tp_ori[$key] == 'percentage' ?
                                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0)),

                                'total_discount_type' => $request->total_discount,
                                'total_discount_percent' => $request->discount_percentage != '' || $request->discount_percentage != null ?
                                    $request->discount_percentage : (($request->bill_grand_total_wo_discount != 0) ? (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100) : 0),
                                'total_discount_amount' => $request->discount_amount != '' || $request->discount_amount != null ?
                                    $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100)),
                                'bill_grand_total' => $request->bill_grand_total,
                                'bill_grand_total_wo_discount' => $request->bill_grand_total_wo_discount,
                            ]);

                        $inputQuantity = 0;
                        ++$index;
                        continue;
                    } elseif ($inputQuantity == 0) {
                        StockPurchaseReport::updateSellQuantity($purchase->puid, $purchase->pid, $productID, $currentSellQuantity + $purchaseQuantity);

                        BillHistory::updateQuantity($transaction_id, $productID, $branch, $purchase->puid, $purchase->pid, 0, $request);

                        $buycostadd += ($inputQuantity * $purchase->Purchase_buycost);
                        $buycost_rate_add += ($inputQuantity * $purchase->Purchase_Buycost_Rate);

                        Buyproduct::where('transaction_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch', $branch)
                            ->update([
                                'buycostadd' => $buycostadd,
                                'buycost_rate_add' => $buycost_rate_add,
                            ]);

                            NewBuyproduct::where('transaction_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch', $branch)
                            ->update([
                                'buycostadd' => $buycostadd,
                                'buycost_rate_add' => $buycost_rate_add,
                            ]);

                        BillHistory::where('trans_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch_id', $branch)
                            ->update([
                                'billing_Sellingcost' => $request->mrp[$key],
                                'billing_inclusive_rate' => ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$productID] : null,
                                'netrate' => $request->net_rate[$key],
                            ]);

                        BillHistory::where('trans_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch_id', $branch)
                            ->where('puid', $purchase->puid)
                            ->where('pid', $purchase->pid)
                            ->update([
                                // 'discount_type' => (isset($request->productStatus[$key]) && ($request->productStatus[$key] == 0)) ? $request->dis_count__typee[$key] : $request->dis_count_type[$key],
                                // 'discount' => $request->dis_count_type[$key] == "none" ? $request->dis_count[$key] : ($request->dis_count_type[$key] == "percentage" ? $request->dis_count[$key] : ($request->dis_count_type[$key] == "amount" ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] / (1 + ($request->fixed_vat[$key] / 100)) : $request->mrp[$key])) * 100 : 0)),
                                // 'discount_amount' => $request->dis_count_type[$key] == "percentage" ?
                                //     ($request->vat_type_value == 1 ? ($request->mrp[$key] / (1 + ($request->fixed_vat[$key] / 100))) * ($request->dis_count[$key] / 100) : $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == "amount" ? $request->dis_count[$key] : 0),

                                'discount_type' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key],

                                'discount' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                                    ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))),

                                'discount_amount' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                                    ($request->dis_count__tp_ori[$key] == 'percentage' ?
                                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0)),

                                'total_discount_type' => $request->total_discount,
                                'total_discount_percent' => $request->discount_percentage != '' || $request->discount_percentage != null ?
                                    $request->discount_percentage : (($request->bill_grand_total_wo_discount != 0) ? (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100) : 0),
                                'total_discount_amount' => $request->discount_amount != '' || $request->discount_amount != null ?
                                    $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100)),
                                'bill_grand_total' => $request->bill_grand_total,
                                'bill_grand_total_wo_discount' => $request->bill_grand_total_wo_discount,
                            ]);

                        ++$index;
                        continue;
                    } elseif ($inputQuantity == $purchaseQuantity) {
                        $remainQuantity = $purchaseQuantity - $inputQuantity;
                        $inputQuantity = $remainQuantity;

                        $buycostadd += ($purchaseQuantity * $purchase->Purchase_buycost);
                        $buycost_rate_add += ($purchaseQuantity * $purchase->Purchase_Buycost_Rate);

                        Buyproduct::where('transaction_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch', $branch)
                            ->update([
                                'buycostadd' => $buycostadd,
                                'buycost_rate_add' => $buycost_rate_add,
                            ]);

                            NewBuyproduct::where('transaction_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch', $branch)
                            ->update([
                                'buycostadd' => $buycostadd,
                                'buycost_rate_add' => $buycost_rate_add,
                            ]);

                        BillHistory::where('trans_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch_id', $branch)
                            ->update([
                                'billing_Sellingcost' => $request->mrp[$key],
                                'billing_inclusive_rate' => ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$productID] : null,
                                'netrate' => $request->net_rate[$key],
                            ]);

                        BillHistory::where('trans_id', $transaction_id)
                            ->where('product_id', $productID)
                            ->where('branch_id', $branch)
                            ->where('puid', $purchase->puid)
                            ->where('pid', $purchase->pid)
                            ->update([
                                // 'discount_type' => (isset($request->productStatus[$key]) && ($request->productStatus[$key] == 0)) ? $request->dis_count__typee[$key] : $request->dis_count_type[$key],
                                // 'discount' => $request->dis_count_type[$key] == "none" ? $request->dis_count[$key] : ($request->dis_count_type[$key] == "percentage" ? $request->dis_count[$key] : ($request->dis_count_type[$key] == "amount" ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] / (1 + ($request->fixed_vat[$key] / 100)) : $request->mrp[$key])) * 100 : 0)),
                                // 'discount_amount' => $request->dis_count_type[$key] == "percentage" ?
                                //     ($request->vat_type_value == 1 ? ($request->mrp[$key] / (1 + ($request->fixed_vat[$key] / 100))) * ($request->dis_count[$key] / 100) : $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == "amount" ? $request->dis_count[$key] : 0),

                                'discount_type' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key],

                                'discount' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                                    ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))),

                                'discount_amount' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                                    ($request->dis_count__tp_ori[$key] == 'percentage' ?
                                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0)),

                                'total_discount_type' => $request->total_discount,
                                'total_discount_percent' => $request->discount_percentage != '' || $request->discount_percentage != null ?
                                    $request->discount_percentage : (($request->bill_grand_total_wo_discount != 0) ? (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100) : 0),
                                'total_discount_amount' => $request->discount_amount != '' || $request->discount_amount != null ?
                                    $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100)),
                                'bill_grand_total' => $request->bill_grand_total,
                                'bill_grand_total_wo_discount' => $request->bill_grand_total_wo_discount,
                            ]);

                        // Check if there are more purchases
                        if ($index < count($bill_purchases) - 1) {
                            ++$index;
                            continue; // Move to the next purchase
                        }
                    }
                    ++$index;
                }
                /* ----------------------------------------------------------------------------- */
            } else {
                $this->newPurchaseQuantityManage($request, $productID, $branch, $transaction_id, $user_id, $inputQuantity, $buycostadd, $buycost_rate_add);
            }
        }
        /* --------------------------------------------------------------------------------------- */
    }

    // old transaction Buyproducts table management
    public function OldBuyproductsTransDataCheck($trans_exist, $transaction_id)
    {
        if ($trans_exist) {
            // $grandtotal = Buyproduct::where('transaction_id', $transaction_id)
            //     ->select(DB::raw("SUM(total_amount) as total_amount"))
            //     ->pluck('total_amount')
            //     ->first();

            $grandtotal = Buyproduct::where('transaction_id', $transaction_id)
                ->select(DB::raw('bill_grand_total'))
                ->pluck('bill_grand_total')
                ->first();

            $old_created_at_date = $this->getCreatedAt($transaction_id);
            $old_to_invoice = $this->getToInvoice($transaction_id);
            $old_salesordertransID = $this->getSalesorderTransID($transaction_id);
            $old_quotationtransID = $this->getQuotationTransID($transaction_id);

            $old_trans_data = Buyproduct::where('transaction_id', $transaction_id)
                ->get();

            // Store old quantities in an array
            $old_quantities = [];

            // Loop through the old transaction data to calculate the difference in quantity
            foreach ($old_trans_data as $old_data) {
                $productID = $old_data->product_id;
                $old_quantity = $old_data->remain_quantity;

                $old_quantities[$productID] = $old_quantity;
                $product_ids[$productID] = $productID;
            }
            // Delete old data
            $this->editTransRepository->deleteBuyproductsByTransactionId($transaction_id);

            return [
                'grandtotal' => $grandtotal,
                'old_created_at_date' => $old_created_at_date,
                'old_trans_data' => $old_trans_data,
                'old_quantities' => $old_quantities,
                'product_ids' => $product_ids,
                'old_to_invoice' => $old_to_invoice,
                'old_salesordertransID' => $old_salesordertransID,
                'old_quotationtransID' => $old_quotationtransID,
            ];
        }
    }

    // old transaction stockdats table management
    public function OldStockdatsTransCheck($transaction_id)
    {
        if (DB::table('stockdats')->where('transaction_id', $transaction_id)->exists()) {
            $prev_created_at_stockdat = DB::table('stockdats')
                ->where('transaction_id', $transaction_id)
                ->pluck('created_at')
                ->first();

            $prev_trans_stockdat = DB::table('stockdats')
                ->where('transaction_id', $transaction_id)
                ->get();

            $this->editTransRepository->deleteStockdatsByTransactionId($transaction_id);

            return [
                'prev_created_at_stockdat' => $prev_created_at_stockdat,
                'prev_trans_stockdat' => $prev_trans_stockdat,
            ];
        }
    }

    public function editSubmitData($request, $old_trans_data, $old_quantities, $branch, $user_id, $old_created_at_date, $prev_created_at_stockdat, $old_to_invoice, $old_salesordertransID, $old_quotationtransID)
    {
        $transaction_id = $request->transaction_id;

        // Store new data
        foreach ($request->product_id as $key => $productID) {
            $data = [
                'product_name' => $request->productName[$key],
                'quantity' => $request->quantity[$key],
                'remain_quantity' => $request->quantity[$key],
                'unit' => $request->prounit[$key],
                'product_id' => $productID,
                'transaction_id' => $transaction_id,
                'customer_name' => $request->customer_name,
                'email' => $request->email,
                'trn_number' => $request->trn_number,
                'phone' => $request->phone,
                'price' => $request->price[$key],
                'total_amount' => $request->total_amount[$key],
                'payment_type' => $request->payment_type,
                'bank_id' => $request->bank_name,
                'account_name' => $request->account_name,
                'employee_id' => $request->employee_id,
                'employee_name' => $request->employee_name,
                'user_id' => $user_id,
                'branch' => $branch,
                'one_pro_buycost' => $request->buy_cost[$key],
                'mrp' => $request->mrp[$key],
                'fixed_vat' => $request->fixed_vat[$key],
                'vat_amount' => $request->vat_amount[$key],
                'credit_user_id' => ($request->payment_type == 3) ? $request->credit_user_id : null,
                'cash_user_id' => ($request->payment_type == 1 || $request->payment_type == 2 || $request->payment_type == 4) ? $request->credit_user_id : null,
                'vat_type' => $request->vat_type_value,
                'inclusive_rate' => ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$key] : null,
                'exclusive_rate' => ($request->vat_type_value == 2) ? $request->rate_discount_r[$key] : null,
                'one_pro_buycost_rate' => $request->buycost_rate[$key],
                'netrate' => $request->net_rate[$key],
                'created_at' => $old_created_at_date,
                'edit' => 1,
                'edit_comment' => $request->edit_comment,
                'to_invoice' => ($old_to_invoice != null) ? $old_to_invoice : null,
                'sales_order_trans_ID' => ($old_salesordertransID != null) ? $old_salesordertransID : null,
                'quotation_trans_ID' => ($old_quotationtransID != null) ? $old_quotationtransID : null,
                'discount_type' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ? $request->dis_count__tp_ori[$key] : $request->dis_count_type[$key],

                'discount' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                    ($request->dis_count__tp_ori[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count__tp_ori[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))) : ($request->dis_count_type[$key] == 'none' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'percentage' ? $request->dis_count[$key] : ($request->dis_count_type[$key] == 'amount' ? ($request->dis_count[$key] / ($request->vat_type_value == 1 ? $request->mrp[$key] : $request->mrp[$key])) * 100 : 0))),

                'discount_amount' => (isset($request->productStatus[$key]) && $request->productStatus[$key] == 0) ?
                    ($request->dis_count__tp_ori[$key] == 'percentage' ?
                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count__tp_ori[$key] == 'amount' ? $request->dis_count[$key] : 0)) : ($request->dis_count_type[$key] == 'percentage' ?
                        ($request->vat_type_value == 1 ? $request->mrp[$key] * ($request->dis_count[$key] / 100) :
                            $request->mrp[$key] * ($request->dis_count[$key] / 100)) : ($request->dis_count_type[$key] == 'amount' ? $request->dis_count[$key] : 0)),

                'totalamount_wo_discount' => $request->total_amount_wo_discount[$key],
                'price_wo_discount' => $request->price_withoutvat_wo_discount[$key],
                'total_discount_type' => $request->total_discount,

                'total_discount_percent' => $request->discount_percentage != '' || $request->discount_percentage != null ?
                    $request->discount_percentage : (($request->bill_grand_total_wo_discount != 0) ? (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100) : 0),

                'total_discount_amount' => $request->discount_amount != '' || $request->discount_amount != null ?
                    $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100)),
                'bill_grand_total' => $request->bill_grand_total,
                'bill_grand_total_wo_discount' => $request->bill_grand_total_wo_discount,
            ];

            // Save buyproduct data
            $this->editTransRepository->saveBuyproduct($data);

            $stockdat = [
                'product_id' => $productID,
                'stock_num' => $request->quantity[$key],
                'transaction_id' => $transaction_id,
                'user_id' => $user_id,
                'one_pro_buycost' => $request->buy_cost[$key],
                'one_pro_sellingcost' => $request->mrp[$key],
                'one_pro_inclusive_rate' => ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$key] : null,
                'one_pro_buycost_rate' => $request->buycost_rate[$key],
                'netrate' => $request->net_rate[$key],
                'created_at' => $prev_created_at_stockdat,
            ];

            // Save stockdat data
            $this->editTransRepository->saveStockdat($stockdat);

            // Update product remaining stock
            $this->editTransRepository->updateProductRemainingStock($productID, $request->quantity[$key], $old_trans_data, $old_quantities);
        }
    }

    // credit in edit transaction

    public function EditTranscreditManage($request, $branch, $grandtotal, $user_id, $transaction_id)
    {
        $due_amounttotal = 0;

        foreach ($request->product_id as $key => $productID) {
            $due_amounttotal += $request->total_amount[$key];
        }

        if ($request->payment_type == 3 && ($request->payment_mode == null || $request->payment_mode != 3)) {
            $credituserid = $request->credit_user_id;

            $due_amount = DB::table('creditsummaries')
            ->where('credituser_id', $credituserid)
            ->pluck('due_amount')
            ->first();

        // new credit_transactions table
        $credit_username = DB::table('creditusers')
            ->where('id', $credituserid)
            ->where('location', $branch)
            ->pluck('name')
            ->first();

        $lastTransaction = DB::table('credit_transactions')
            ->where('credituser_id', $credituserid)
            ->where('location', $branch)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $credit_limit = DB::table('creditusers')
            ->where('id', $credituserid)
            ->where('location', $branch)
            ->pluck('current_lamount')
            ->first();

            $creditlimit_deducted = DB::table('credit_transactions')
            ->where('credituser_id', $credituserid)
            ->where('transaction_id', $transaction_id)
            ->orderBy('id', 'desc') // Order by ID to get the most recent record
             ->pluck('credit_lose')
             ->first() ?? 0;


             $creditdue_deducted = DB::table('credit_transactions')
             ->where('credituser_id', $credituserid)
             ->where('transaction_id', $transaction_id)
             ->orderBy('id', 'desc') // Order by ID to get the most recent record
             ->pluck('credit_balance')
             ->first() ?? 0;

             $total_credit = $credit_limit + $creditlimit_deducted;
             $updated_balance = $lastTransaction->updated_balance ?? 0;
             $advance_balance = $request->advance_balance ?? 0;

             $diff_remain = abs($request->bill_grand_total - $grandtotal);
             $due_amountupload = $grandtotal > $request->bill_grand_total ? $due_amount - $diff_remain : $due_amount + $diff_remain;
        $new_due = $grandtotal > $request->bill_grand_total ? $updated_balance - $diff_remain : $updated_balance + $diff_remain;

        if ($grandtotal > $request->bill_grand_total) {
            // Case: Reduction in total amount (e.g., return or discount)
            $amount_to_reduce = $grandtotal - $request->bill_grand_total;

            // Deduct from credit_lose first
            if ($creditlimit_deducted >= $amount_to_reduce) {
                $creditlimit_deducted -= $amount_to_reduce;
                $credit_limit += $amount_to_reduce; // Add back to credit limit
                $amount_to_reduce = 0;
            } else {
                $credit_limit += $creditlimit_deducted; // Recover all credit_lose
                $amount_to_reduce -= $creditlimit_deducted;
                $creditlimit_deducted = 0;
            }

                // Now deduct the remaining amount from credit_balance if applicable
                if ($amount_to_reduce > 0) {
                    if ($creditdue_deducted >= $amount_to_reduce) {
                        $creditdue_deducted -= $amount_to_reduce;
                    } else {
                        $creditdue_deducted = 0; // All credit_balance used up
                }
            }
        } elseif ($grandtotal < $request->bill_grand_total) {
            // Case: Increase in total amount (e.g., additional purchases)
            $additional_amount = $request->bill_grand_total - $grandtotal;

            // Deduct from advance balance first
            if ($advance_balance > 0) {
                if ($additional_amount <= $advance_balance) {
                    $advance_balance -= $additional_amount;
                    $additional_amount = 0;
                } else {
                    $additional_amount -= $advance_balance;
                    $advance_balance = 0;
                }
            }

            // Deduct from credit_limit if needed
            if ($additional_amount > 0 && $credit_limit > 0) {
                if ($additional_amount <= $credit_limit) {
                    $credit_limit -= $additional_amount;
                    $creditlimit_deducted += $additional_amount; // Track credit_limit usage
                    $additional_amount = 0;
                } else {
                    $creditlimit_deducted += $credit_limit; // Use up all credit limit
                    $additional_amount -= $credit_limit;
                    $credit_limit = 0;
                }
            }

            // Update credit_balance based on remaining additional amount
            if ($additional_amount > 0 && $creditdue_deducted > 0) {
                if ($additional_amount <= $creditdue_deducted) {
                    $creditdue_deducted -= $additional_amount;
                } else {
                    $creditdue_deducted = 0; // All credit_balance used up
                }
            }
        }



         // Update the current_lamount in the creditusers table
         if (is_null($credit_limit)) {
            DB::table('creditusers')
                ->where('id', $credituserid)
                ->where('location', $branch)
                ->update(['current_lamount' => null]);
        } else {
            DB::table('creditusers')
                ->where('id', $credituserid)
                ->where('location', $branch)
                ->update(['current_lamount' => $credit_limit]);
        }

       $creditsummaries = DB::table('creditsummaries')
            ->updateOrInsert(
                ['credituser_id' => $credituserid],
                ['due_amount' => $due_amountupload]
            );

      $lastSameTransaction = DB::table('credit_transactions')
    ->where('credituser_id', $credituserid)
    ->where('location', $branch)
    ->where('transaction_id', $transaction_id)
    ->orderBy('created_at', 'desc')
    ->first();

// Get the previous 'balance_due', default to 0 if no record exists
$previous_due = $lastSameTransaction->balance_due ?? 0;

// Calculate the difference
$calculated_diff = $grandtotal > $request->bill_grand_total ? $diff_remain : null;
$cal_diff = $grandtotal <= $request->bill_grand_total ? $diff_remain : null;

// Update the new balance_due based on the condition
$new_balance_due = $grandtotal < $request->bill_grand_total
    ? $previous_due + $cal_diff
    : $previous_due - $calculated_diff;

        if ($request->bill_grand_total != $grandtotal) {
            $credit_trans = new CreditTransaction();
            $credit_trans->credituser_id = $credituserid;
            $credit_trans->credit_username = $credit_username;
            $credit_trans->user_id = $user_id;
            $credit_trans->location = $branch;
            $credit_trans->transaction_id = $transaction_id;
            $credit_trans->due = $updated_balance ?? 0;
            $credit_trans->collected_amount = $grandtotal > $request->bill_grand_total ? $diff_remain : null;
            $credit_trans->Invoice_due = $grandtotal > $request->bill_grand_total ? null : $diff_remain;
            $credit_trans->updated_balance = $new_due;
            $credit_trans->balance_due = $new_balance_due;
            $credit_trans->credit_lose = $creditlimit_deducted;
            $credit_trans->credit_balance = $creditdue_deducted;
            $credit_trans->comment = $grandtotal > $request->bill_grand_total ? 'Product Returned' : 'Invoice';
            $credit_trans->edit_tran = '1';
            $credit_trans->save();
        }
    }
        elseif (($request->payment_type == 1 || $request->payment_type == 2 || $request->payment_type == 4) && ($request->credit_user_id != null)) {
            $cash_userid = $request->credit_user_id;

            // new credit_transactions table
            $cash_username = DB::table('creditusers')
                ->where('id', $cash_userid)
                ->where('location', $branch)
                ->pluck('name')
                ->first();

            $lastTransaction = DB::table('cash_trans_statements')
                ->where('cash_user_id', $cash_userid)
                ->where('location', $branch)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $updated_balance = $lastTransaction->updated_balance ?? null;

            $diff_remain = $grandtotal > $request->bill_grand_total ? $grandtotal - $request->bill_grand_total : $request->bill_grand_total - $grandtotal;
            $new_due = $grandtotal > $request->bill_grand_total ? $updated_balance - $diff_remain : $updated_balance + $diff_remain;

            if ($request->bill_grand_total != $grandtotal) {
                $cash_trans = new CashTransStatement();
                $cash_trans->cash_user_id = $cash_userid;
                $cash_trans->cash_username = $cash_username;
                $cash_trans->user_id = Session('softwareuser');
                $cash_trans->location = $branch;
                $cash_trans->transaction_id = $transaction_id;
                $cash_trans->collected_amount = $diff_remain;
                $cash_trans->updated_balance = $new_due;
                $cash_trans->comment = $grandtotal > $request->bill_grand_total ? 'Product Returned' : 'Invoice';
                $cash_trans->payment_type = $request->payment_type;
                $cash_trans->edit_tran = '1';
                $cash_trans->save();
            }
        }
    }

    //  new Purchase Quantity Manage

    public function newPurchaseQuantityManage($request, $productID, $branch, $transaction_id, $user_id, $inputQuantity, $buycostadd, $buycost_rate_add)
    {
        /* ------------- Quantity reduce purchase wise code stock purchasereport table --------- */

        $first_purchase = $this->editTransRepository->findFirstPurchase($productID, $branch);

        $rem_sell = StockPurchaseReport::where('purchase_id', $first_purchase->purchase_id)
            ->where('product_id', $productID)
            ->where('branch_id', $branch)
            ->pluck('sell_quantity')
            ->first();

        if ($inputQuantity <= $rem_sell) {
            $balance = $rem_sell - $inputQuantity;

            StockPurchaseReport::updateBuyTimeSellQuantity($first_purchase->purchase_id, $productID, $branch, $first_purchase->receipt_no, $balance);

            $buycostadd += ($inputQuantity * $first_purchase->PBuycost);
            $buycost_rate_add += ($inputQuantity * $first_purchase->PBuycostRate);

            Buyproduct::where('transaction_id', $transaction_id)
                ->where('product_id', $productID)
                ->where('branch', $branch)
                ->update([
                    'buycostadd' => $buycostadd,
                    'buycost_rate_add' => $buycost_rate_add,
                ]);

                NewBuyproduct::where('transaction_id', $transaction_id)
                ->where('product_id', $productID)
                ->where('branch', $branch)
                ->update([
                    'buycostadd' => $buycostadd,
                    'buycost_rate_add' => $buycost_rate_add,
                ]);

            /* ----------------------------------------------------- */

            $exist_already = $this->editTransRepository->existsBillHistory($transaction_id, $productID, $branch, $first_purchase->purchase_trans_id, $first_purchase->purchase_id);

            if ($exist_already) {
                $currentSellQuantity = $this->editTransRepository->getBillsellQuantity($transaction_id, $productID, $branch, $first_purchase->purchase_trans_id, $first_purchase->purchase_id);

                $quantity = $currentSellQuantity + $inputQuantity;

                BillHistory::updateQuantity($transaction_id, $productID, $branch, $first_purchase->purchase_trans_id, $first_purchase->purchase_id, $quantity, $request);
            } else {
                $billdata = [
                    'trans_id' => $transaction_id,
                    'product_id' => $productID,
                    'puid' => $first_purchase->purchase_trans_id,
                    'pid' => $first_purchase->purchase_id,
                    'sold_quantity' => $inputQuantity,
                    'remain_sold_quantity' => $inputQuantity,
                    'branch_id' => $branch,
                    'user_id' => $user_id,
                    'Purchase_buycost' => $first_purchase->PBuycost,
                    'billing_Sellingcost' => $request->mrp[$productID],
                    'billing_inclusive_rate' => ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$productID] : null,
                    'billing_exclusive_rate' => ($request->vat_type_value == 2) ? $request->rate_discount_r[$productID] : null,
                    'Purchase_Buycost_Rate' => $first_purchase->PBuycostRate,
                    'netrate' => $request->net_rate[$productID],
                    'receipt_no' => $first_purchase->receipt_no,
                    'discount_type' => $request->dis_count_type[$productID],

                    'discount' => $request->dis_count_type[$productID] == 'none' ? $request->dis_count[$productID] : ($request->dis_count_type[$productID] == 'percentage' ? $request->dis_count[$productID] : ($request->dis_count_type[$productID] == 'amount' ? ($request->dis_count[$productID] / ($request->vat_type_value == 1 ? $request->mrp[$productID] : $request->mrp[$productID])) * 100 : 0)),
                    'discount_amount' => $request->dis_count_type[$productID] == 'percentage' ?
                        ($request->vat_type_value == 1 ? $request->mrp[$productID] * ($request->dis_count[$productID] / 100) : $request->mrp[$productID] * ($request->dis_count[$productID] / 100)) : ($request->dis_count_type[$productID] == 'amount' ? $request->dis_count[$productID] : 0),

                    'total_discount_type' => $request->total_discount,
                    'total_discount_percent' => $request->discount_percentage != '' || $request->discount_percentage != null ?
                        $request->discount_percentage : (($request->bill_grand_total_wo_discount != 0) ? (($request->discount_amount / $request->bill_grand_total_wo_discount) * 100) : 0),
                    'total_discount_amount' => $request->discount_amount != '' || $request->discount_amount != null ?
                        $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100)),
                    'bill_grand_total' => $request->bill_grand_total,
                    'bill_grand_total_wo_discount' => $request->bill_grand_total_wo_discount,
                ];

                $this->editTransRepository->saveBillHistory($billdata);
            }

            /* ----------------------------------------------------- */
        } elseif ($inputQuantity > $rem_sell) {
            $remainq = $inputQuantity - $rem_sell;

            StockPurchaseReport::updateBuyTimeSellQuantity($first_purchase->purchase_id, $productID, $branch, $first_purchase->receipt_no, 0);

            $buycostadd += ($rem_sell * $first_purchase->PBuycost);
            $buycost_rate_add += ($rem_sell * $first_purchase->PBuycostRate);

            /* ----------------------------------------------------- */

            $exist_already = $this->editTransRepository->existsBillHistory($transaction_id, $productID, $branch, $first_purchase->purchase_trans_id, $first_purchase->purchase_id);

            if ($exist_already) {
                $currentSellQuantity = $this->editTransRepository->getBillsellQuantity($transaction_id, $productID, $branch, $first_purchase->purchase_trans_id, $first_purchase->purchase_id);

                $quantity = $currentSellQuantity + $rem_sell;

                BillHistory::updateQuantity($transaction_id, $productID, $branch, $first_purchase->purchase_trans_id, $first_purchase->purchase_id, $quantity, $request);
            } else {
                $billdata = [
                    'trans_id' => $transaction_id,
                    'product_id' => $productID,
                    'puid' => $first_purchase->purchase_trans_id,
                    'pid' => $first_purchase->purchase_id,
                    'sold_quantity' => $rem_sell,
                    'remain_sold_quantity' => $rem_sell,
                    'branch_id' => $branch,
                    'user_id' => $user_id,
                    'Purchase_buycost' => $first_purchase->PBuycost,
                    'billing_Sellingcost' => $request->mrp[$productID],
                    'billing_inclusive_rate' => ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$productID] : null,
                    'billing_exclusive_rate' => ($request->vat_type_value == 2) ? $request->rate_discount_r[$productID] : null,
                    'Purchase_Buycost_Rate' => $first_purchase->PBuycostRate,
                    'netrate' => $request->net_rate[$productID],
                    'receipt_no' => $first_purchase->receipt_no,
                    'discount_type' => $request->dis_count_type[$productID], 'discount' => $request->dis_count_type[$productID] == 'none' ? $request->dis_count[$productID] : ($request->dis_count_type[$productID] == 'percentage' ? $request->dis_count[$productID] : ($request->dis_count_type[$productID] == 'amount' ? ($request->dis_count[$productID] / ($request->vat_type_value == 1 ? $request->mrp[$productID] / (1 + ($request->fixed_vat[$productID] / 100)) : $request->mrp[$productID])) * 100 : 0)),
                    'discount_amount' => $request->dis_count_type[$productID] == 'percentage' ?
                        ($request->vat_type_value == 1 ? ($request->mrp[$productID] / (1 + ($request->fixed_vat[$productID] / 100))) * ($request->dis_count[$productID] / 100) : $request->mrp[$productID] * ($request->dis_count[$productID] / 100)) : ($request->dis_count_type[$productID] == 'amount' ? $request->dis_count[$productID] : 0),
                    'total_discount_type' => $request->total_discount,
                    'total_discount_percent' => $request->discount_percentage != '' || $request->discount_percentage != null ?
                        $request->discount_percentage : ($request->discount_amount / $request->bill_grand_total_wo_discount) * 100,
                    'total_discount_amount' => $request->discount_amount != '' || $request->discount_amount != null ?
                        $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100)),
                    'bill_grand_total' => $request->bill_grand_total,
                    'bill_grand_total_wo_discount' => $request->bill_grand_total_wo_discount,
                ];

                $this->editTransRepository->saveBillHistory($billdata);
            }
            /* ----------------------------------------------------- */

            while ($remainq > 0) {
                $next_purchase = $this->editTransRepository->findFirstPurchase($productID, $branch);

                if (!$next_purchase) {
                    break;
                }

                if ($remainq <= $next_purchase->sell_quantity) {  // / next only one purchase
                    $updated_bal = $next_purchase->sell_quantity - $remainq;

                    StockPurchaseReport::updateBuyTimeSellQuantity($next_purchase->purchase_id, $productID, $branch, $next_purchase->receipt_no, $updated_bal);

                    $buycostadd += ($remainq * $next_purchase->PBuycost);

                    $buycost_rate_add += ($remainq * $next_purchase->PBuycostRate);

                    Buyproduct::where('transaction_id', $transaction_id)
                        ->where('product_id', $productID)
                        ->where('branch', $branch)
                        ->update([
                            'buycostadd' => $buycostadd,
                            'buycost_rate_add' => $buycost_rate_add,
                        ]);

                        NewBuyproduct::where('transaction_id', $transaction_id)
                        ->where('product_id', $productID)
                        ->where('branch', $branch)
                        ->update([
                            'buycostadd' => $buycostadd,
                            'buycost_rate_add' => $buycost_rate_add,
                        ]);

                    /* ----------------------------------------------------- */

                    $exist_already = $this->editTransRepository->existsBillHistory($transaction_id, $productID, $branch, $next_purchase->purchase_trans_id, $next_purchase->purchase_id);

                    if ($exist_already) {
                        $currentSellQuantity = $this->editTransRepository->getBillsellQuantity($transaction_id, $productID, $branch, $next_purchase->purchase_trans_id, $next_purchase->purchase_id);

                        $quantity = $currentSellQuantity + $remainq;

                        BillHistory::updateQuantity($transaction_id, $productID, $branch, $next_purchase->purchase_trans_id, $next_purchase->purchase_id, $quantity, $request);
                    } else {
                        $billdata = [
                            'trans_id' => $transaction_id,
                            'product_id' => $productID,
                            'puid' => $next_purchase->purchase_trans_id,
                            'pid' => $next_purchase->purchase_id,
                            'sold_quantity' => $remainq,
                            'remain_sold_quantity' => $remainq,
                            'branch_id' => $branch,
                            'user_id' => $user_id,
                            'Purchase_buycost' => $next_purchase->PBuycost,
                            'billing_Sellingcost' => $request->mrp[$productID],
                            'billing_inclusive_rate' => ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$productID] : null,
                            'billing_exclusive_rate' => ($request->vat_type_value == 2) ? $request->rate_discount_r[$productID] : null,
                            'Purchase_Buycost_Rate' => $next_purchase->PBuycostRate,
                            'netrate' => $request->net_rate[$productID],
                            'receipt_no' => $next_purchase->receipt_no,
                            'discount_type' => $request->dis_count_type[$productID], 'discount' => $request->dis_count_type[$productID] == 'none' ? $request->dis_count[$productID] : ($request->dis_count_type[$productID] == 'percentage' ? $request->dis_count[$productID] : ($request->dis_count_type[$productID] == 'amount' ? ($request->dis_count[$productID] / ($request->vat_type_value == 1 ? $request->mrp[$productID] / (1 + ($request->fixed_vat[$productID] / 100)) : $request->mrp[$productID])) * 100 : 0)),
                            'discount_amount' => $request->dis_count_type[$productID] == 'percentage' ?
                                ($request->vat_type_value == 1 ? ($request->mrp[$productID] / (1 + ($request->fixed_vat[$productID] / 100))) * ($request->dis_count[$productID] / 100) : $request->mrp[$productID] * ($request->dis_count[$productID] / 100)) : ($request->dis_count_type[$productID] == 'amount' ? $request->dis_count[$productID] : 0),
                            'total_discount_type' => $request->total_discount,
                            'total_discount_percent' => $request->discount_percentage != '' || $request->discount_percentage != null ?
                                $request->discount_percentage : ($request->discount_amount / $request->bill_grand_total_wo_discount) * 100,
                            'total_discount_amount' => $request->discount_amount != '' || $request->discount_amount != null ?
                                $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100)),
                            'bill_grand_total' => $request->bill_grand_total,
                            'bill_grand_total_wo_discount' => $request->bill_grand_total_wo_discount,
                        ];

                        $this->editTransRepository->saveBillHistory($billdata);
                    }
                    /* ----------------------------------------------------- */

                    $remainq = 0;
                } elseif ($remainq > $next_purchase->sell_quantity) { // more than 2 purchases - looping through
                    $remainq -= $next_purchase->sell_quantity;

                    StockPurchaseReport::updateBuyTimeSellQuantity($next_purchase->purchase_id, $productID, $branch, $next_purchase->receipt_no, 0);

                    $buycostadd += ($next_purchase->sell_quantity * $next_purchase->PBuycost);

                    $buycost_rate_add += ($next_purchase->sell_quantity * $next_purchase->PBuycostRate);

                    /* ----------------------------------------------------- */

                    $exist_already = $this->editTransRepository->existsBillHistory($transaction_id, $productID, $branch, $next_purchase->purchase_trans_id, $next_purchase->purchase_id);

                    if ($exist_already) {
                        $currentSellQuantity = $this->editTransRepository->getBillsellQuantity($transaction_id, $productID, $branch, $next_purchase->purchase_trans_id, $next_purchase->purchase_id);

                        $quantity = $currentSellQuantity + $next_purchase->sell_quantity;

                        BillHistory::updateQuantity($transaction_id, $productID, $branch, $next_purchase->purchase_trans_id, $next_purchase->purchase_id, $quantity, $request);
                    } else {
                        $billdata = [
                            'trans_id' => $transaction_id,
                            'product_id' => $productID,
                            'puid' => $next_purchase->purchase_trans_id,
                            'pid' => $next_purchase->purchase_id,
                            'sold_quantity' => $next_purchase->sell_quantity,
                            'remain_sold_quantity' => $next_purchase->sell_quantity,
                            'branch_id' => $branch,
                            'user_id' => $user_id,
                            'Purchase_buycost' => $next_purchase->PBuycost,
                            'billing_Sellingcost' => $request->mrp[$productID],
                            'billing_inclusive_rate' => ($request->vat_type_value == 1) ? $request->inclusive_rate_r[$productID] : null,
                            'billing_exclusive_rate' => ($request->vat_type_value == 2) ? $request->rate_discount_r[$productID] : null,
                            'Purchase_Buycost_Rate' => $next_purchase->PBuycostRate,
                            'netrate' => $request->net_rate[$productID],
                            'receipt_no' => $next_purchase->receipt_no,
                            'discount_type' => $request->dis_count_type[$productID], 'discount' => $request->dis_count_type[$productID] == 'none' ? $request->dis_count[$productID] : ($request->dis_count_type[$productID] == 'percentage' ? $request->dis_count[$productID] : ($request->dis_count_type[$productID] == 'amount' ? ($request->dis_count[$productID] / ($request->vat_type_value == 1 ? $request->mrp[$productID] / (1 + ($request->fixed_vat[$productID] / 100)) : $request->mrp[$productID])) * 100 : 0)),
                            'discount_amount' => $request->dis_count_type[$productID] == 'percentage' ?
                                ($request->vat_type_value == 1 ? ($request->mrp[$productID] / (1 + ($request->fixed_vat[$productID] / 100))) * ($request->dis_count[$productID] / 100) : $request->mrp[$productID] * ($request->dis_count[$productID] / 100)) : ($request->dis_count_type[$productID] == 'amount' ? $request->dis_count[$productID] : 0),
                            'total_discount_type' => $request->total_discount,
                            'total_discount_percent' => $request->discount_percentage != '' || $request->discount_percentage != null ?
                                $request->discount_percentage : ($request->discount_amount / $request->bill_grand_total_wo_discount) * 100,
                            'total_discount_amount' => $request->discount_amount != '' || $request->discount_amount != null ?
                                $request->discount_amount : ($request->bill_grand_total_wo_discount * ($request->discount_percentage / 100)),
                            'bill_grand_total' => $request->bill_grand_total,
                            'bill_grand_total_wo_discount' => $request->bill_grand_total_wo_discount,
                        ];

                        $this->editTransRepository->saveBillHistory($billdata);
                    }
                    /* ----------------------------------------------------- */
                }
            }
        }
    }
}
