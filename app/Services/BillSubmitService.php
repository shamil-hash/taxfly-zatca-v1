<?php

namespace App\Services;

use App\Models\BillHistory;
use App\Models\Buyproduct;
use App\Models\CreditTransaction;
use App\Models\StockPurchaseReport;
use App\Repositories\Interfaces\EditTransactionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillSubmitService
{
    // bill purchase wise logic

    public function billPurchaseWiseLogic($request, $productID, $branch, $transaction_id, $mrp_wo_vat)
    {
        $buycostadd = 0;
        $buycost_rate_add = 0;

        $first_purchase = DB::table('stock_purchase_reports')
            ->select(DB::raw('*'))
            ->where('sell_quantity', '>', 0)
            ->where('product_id', $productID)
            ->where('branch_id', $branch)
            ->orderBy('created_at', 'ASC')
            ->first();

        if (!$first_purchase) {
            return;
        }

        $rem_sell = StockPurchaseReport::where('purchase_id', $first_purchase->purchase_id)
            ->where('product_id', $productID)
            ->where('branch_id', $branch)
            ->pluck('sell_quantity')
            ->first();

        if ($request->quantity[$productID] <= $rem_sell) {

            $balance = $rem_sell - $request->quantity[$productID];

            $this->updateStockQuantity($first_purchase, $productID, $branch, $balance);

            $buycostadd += ($request->quantity[$productID] * $first_purchase->PBuycost);
            $buycost_rate_add += ($request->quantity[$productID] * $first_purchase->PBuycostRate);

            $this->updateBuyProduct($transaction_id, $productID, $branch, $buycostadd, $buycost_rate_add);
            $this->createBillHistory($first_purchase, $transaction_id, $productID, $branch, $request,  $request->quantity[$productID], $mrp_wo_vat);
        } else if ($request->quantity[$productID] > $rem_sell) {

            $remainq = $request->quantity[$productID] - $rem_sell;

            $this->updateStockQuantity($first_purchase, $productID, $branch, 0);

            $buycostadd += ($rem_sell * $first_purchase->PBuycost);
            $buycost_rate_add += ($rem_sell * $first_purchase->PBuycostRate);

            $this->updateBuyProduct($transaction_id, $productID, $branch, $buycostadd, $buycost_rate_add);
            $this->createBillHistory($first_purchase, $transaction_id, $productID, $branch, $request,  $rem_sell, $mrp_wo_vat);
            $this->processRemainingQuantity($remainq, $productID, $branch, $transaction_id, $request, $mrp_wo_vat, $buycostadd, $buycost_rate_add);
        }
    }


    private function updateStockQuantity($purchase, $productID, $branch, $quantity)
    {
        StockPurchaseReport::where('purchase_id', $purchase->purchase_id)
            ->where('product_id', $productID)
            ->where('branch_id', $branch)
            ->where('receipt_no', $purchase->receipt_no)
            ->update(['sell_quantity' => $quantity]);
    }

    private function updateBuyProduct($transaction_id, $productID, $branch, $buycostadd, $buycost_rate_add)
    {
        Buyproduct::where('transaction_id', $transaction_id)
            ->where('product_id', $productID)
            ->where('branch', $branch)
            ->update([
                'buycostadd' => $buycostadd,
                'buycost_rate_add' => $buycost_rate_add
            ]);
    }

    private function createBillHistory($purchase, $transaction_id, $productID, $branch, $request, $quantity, $mrp_wo_vat)
    {
        $billhistory = new BillHistory();
        $billhistory->trans_id = $transaction_id;
        $billhistory->product_id = $productID;
        $billhistory->puid = $purchase->purchase_trans_id;
        $billhistory->pid = $purchase->purchase_id;
        $billhistory->sold_quantity = $quantity;
        $billhistory->remain_sold_quantity = $quantity;
        $billhistory->branch_id = $branch;
        $billhistory->user_id = Session('softwareuser');
        $billhistory->Purchase_buycost = $purchase->PBuycost;
        $billhistory->billing_Sellingcost = $request->mrp[$productID];
        $billhistory->Purchase_Buycost_Rate = $purchase->PBuycostRate;
        $billhistory->netrate = $request->net_rate[$productID];
        $billhistory->receipt_no = $purchase->receipt_no;
        $billhistory->discount_type = $request->dis_count_type[$productID];

        $this->setBillHistoryDiscount($billhistory, $request, $productID);
        $billhistory->save();
    }

    private function setBillHistoryDiscount($billhistory, $request, $productID)
    {
        if ($request->vat_type_value == 1) {
            $mrp_wo_vat = $request->mrp[$productID] / (1 + ($request->fixed_vat[$productID] / 100));

            $billhistory->billing_inclusive_rate = $request->inclusive_rate_r[$productID];
            $this->setDiscount($billhistory, $request, $productID, $mrp_wo_vat);
        } else if ($request->vat_type_value == 2) {

            $mrp_wo_vat = $request->mrp[$productID];

            $billhistory->billing_exclusive_rate = $request->rate_discount_r[$productID];
            $this->setDiscount($billhistory, $request, $productID, $mrp_wo_vat);
        }
    }

    private function setDiscount($billhistory, $request, $productID, $mrp_wo_vat)
    {
        if ($request->dis_count_type[$productID] == "none") {
            $billhistory->discount = $request->dis_count[$productID];
        } else if ($request->dis_count_type[$productID] == "percentage") {
            $billhistory->discount = $request->dis_count[$productID];
            $billhistory->discount_amount = ($mrp_wo_vat * ($request->dis_count[$productID] / 100)) * $request->quantity[$productID];
        } else if ($request->dis_count_type[$productID] == "amount") {
            $billhistory->discount_amount = $request->dis_count[$productID];

            if ($request->vat_type_value == 1) {
                $billhistory->discount = ($request->dis_count[$productID] / $mrp_wo_vat) * 100;
            } else if ($request->vat_type_value == 2) {
                $billhistory->discount = $request->mrp[$productID] - (($request->mrp[$productID] * $request->fixed_vat[$productID]) / 100);
            }
        }
    }

    private function processRemainingQuantity(&$remainq, $productID, $branch, $transaction_id, $request, $mrp_wo_vat, &$buycostadd, &$buycost_rate_add)
    {
        while ($remainq > 0) {
            $next_purchase  = DB::table('stock_purchase_reports')
                ->select(DB::raw('*'))
                ->where('sell_quantity', '>', 0)
                ->where('product_id', $productID)
                ->where('branch_id', $branch)
                ->orderBy('created_at', 'ASC')
                ->first();

            if (!$next_purchase) {
                break;
            }

            if ($remainq <= $next_purchase->sell_quantity) {
                $updated_bal = $next_purchase->sell_quantity - $remainq;
                $this->updateStockQuantity($next_purchase, $productID, $branch, $updated_bal);
                $buycostadd += $remainq * $next_purchase->PBuycost;
                $buycost_rate_add += $remainq * $next_purchase->PBuycostRate;
                $this->updateBuyProduct($transaction_id, $productID, $branch, $buycostadd, $buycost_rate_add);

                $this->createBillHistory($next_purchase, $transaction_id, $productID, $branch, $request,  $remainq, $mrp_wo_vat);
                $remainq = 0;
            } else {
                $remainq -= $next_purchase->sell_quantity;
                $this->updateStockQuantity($next_purchase, $productID, $branch, 0);
                $buycostadd += $next_purchase->sell_quantity * $next_purchase->PBuycost;
                $buycost_rate_add += $next_purchase->sell_quantity * $next_purchase->PBuycostRate;

                $this->updateBuyProduct($transaction_id, $productID, $branch, $buycostadd, $buycost_rate_add);

                $this->createBillHistory($next_purchase, $transaction_id, $productID, $branch, $request,  $next_purchase->sell_quantity, $mrp_wo_vat);
            }
        }
    }
}
