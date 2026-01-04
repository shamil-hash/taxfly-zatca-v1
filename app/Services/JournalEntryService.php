<?php

namespace App\Services;

use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * JournalEntryService
 *
 * Responsibilities:
 *  - Create balanced DR/CR pairs in the `journal_entries` table for source transactions.
 *  - Provide backward-compatible wrappers (fromPurchase, fromSale, etc).
 *  - Provide safe idempotency checks (alreadyExists).
 *  - Provide meaningful descriptions derived from the source record (not 'Auto-generated').
 *
 * Notes:
 *  - Controllers should call wrapper methods (e.g. app(JournalEntryService::class)->fromSale($transactionId, 'performance_invoices');)
 *  - Some controllers in the project call legacy aliases like postSaleByTransaction(); we provide those aliases here.
 */
class JournalEntryService
{
    /* ==========================================================
     * WRAPPER METHODS (backward compatibility for controllers)
     * ========================================================== */

    public function fromPurchase($transactionId, $table = 'buyproducts')
    {
        return $this->recordPurchaseByTransaction($transactionId, $table);
    }

    public function fromSale($transactionId, $table = 'performance_invoices')
    {
        return $this->recordSaleByTransaction($transactionId, $table);
    }

    public function fromSalesReturn($transactionId, $table = 'returnproducts')
    {
        return $this->recordSalesReturnByTransaction($transactionId, $table);
    }

    public function fromPurchaseReturn($transactionId, $table = 'returnpurchases')
    {
        return $this->recordPurchaseReturnByTransaction($transactionId, $table);
    }

    public function fromCustomerReceipt($transactionId, $table = 'credit_transactions')
    {
        return $this->recordCustomerReceiptByTransaction($transactionId, $table);
    }

    public function fromSupplierPayment($transactionId, $table = 'credit_supplier_transactions')
    {
        return $this->recordSupplierPaymentByTransaction($transactionId, $table);
    }

    public function fromExpense($id, $table = 'accountexpenses')
    {
        return $this->recordExpense($id, $table);
    }

    public function fromIncome($id, $table = 'account_indirect_incomes')
    {
        return $this->recordIncome($id, $table);
    }

    public function fromFundTransfer($id, $table = 'fund_transfer')
    {
        return $this->recordFundTransfer($id, $table);
    }

    public function fromCreditNote($id, $table = 'credit_note')
    {
        return $this->recordCreditNote($id, $table);
    }

    public function fromDebitNote($id, $table = 'debit_note')
    {
        return $this->recordDebitNote($id, $table);
    }

    /* ==========================================================
     * LEGACY ALIASES (some controllers call these names)
     * ========================================================== */

    /**
     * Some controllers call postSaleByTransaction; keep alias for backwards compatibility.
     */
    public function postSaleByTransaction($transactionId, $table = 'performance_invoices')
    {
        return $this->recordSaleByTransaction($transactionId, $table);
    }

    /**
     * Alias for purchase posting in case a controller expects this name.
     */
    public function postPurchaseByTransaction($transactionId, $table = 'buyproducts')
    {
        return $this->recordPurchaseByTransaction($transactionId, $table);
    }

    /* ==========================================================
     * TRANSACTION-SAFE IMPLEMENTATIONS
     * ========================================================== */

    /**
     * Record Purchase by transaction identifier
     * Supports tables that use transaction_id or reciept_no depending on schema
     */
    public function recordPurchaseByTransaction($identifier, $table = 'buyproducts')
    {
        try {
            $query = DB::table($table);

            // Some tables use reciept_no instead of transaction_id
            if (in_array($table, ['stockdetails', 'new_stockdetails'])) {
                $rows = $query->where('reciept_no', $identifier)->get();
            } else {
                $rows = $query->where('transaction_id', $identifier)->get();
            }

            if ($rows->isEmpty()) {
                Log::warning("JournalEntryService::recordPurchaseByTransaction - no rows found for {$identifier} in {$table}");
                return;
            }

            $purchase = $rows->first();
            $amount = (float) ($rows->sum('bill_grand_total') ?: $rows->sum('total_amount') ?: 0);

            $entity = $purchase->customer_name ?? $purchase->supplier ?? 'Supplier';
            $branch = $purchase->branch ?? null;
            $userId = $purchase->user_id ?? 1;
            $transactionId = $purchase->transaction_id ?? $purchase->reciept_no ?? $identifier;
            $anchorId = $rows->min('id') ?? ($purchase->id ?? null);

            if ($anchorId && $this->alreadyExists($table, $anchorId)) {
                Log::info("JournalEntryService::recordPurchaseByTransaction - already exists: {$table}:{$anchorId}");
                return;
            }

            // build description
            $description = $this->buildDescriptionFromRecord($table, (array) $purchase, 'Purchase Invoice');

            // determine payment method and accounts
            $paymentType = $purchase->payment_type ?? 1;
            switch ($paymentType) {
                case 1: // Cash
                    $debitAcc = 'Purchase A/c';
                    $creditAcc = 'Cash A/c';
                    $paidThrough = 'Cash';
                    break;
                case 2: // Bank
                    $bank = $this->getBankName($purchase->bank_id ?? null);
                    $debitAcc = 'Purchase A/c';
                    $creditAcc = $bank ? ($bank . ' A/c') : 'Bank A/c';
                    $paidThrough = $bank ?: 'Bank';
                    break;
                case 3: // Credit
                    $debitAcc = 'Purchase A/c';
                    $creditAcc = 'Supplier A/c';
                    $paidThrough = 'Credit';
                    break;
                default:
                    $debitAcc = 'Purchase A/c';
                    $creditAcc = 'Cash A/c';
                    $paidThrough = 'Cash';
            }

            $this->createPair(
                $transactionId,
                $purchase->created_at ?? $purchase->date ?? now(),
                $entity,
                $debitAcc,
                $creditAcc,
                $amount,
                $amount,
                $paidThrough,
                $transactionId,
                $branch,
                $userId,
                $table,
                $anchorId,
                $description
            );
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::recordPurchaseByTransaction - error: " . $e->getMessage());
        }
    }

    /**
     * Record Sale by transaction id (performance_invoices)
     */
    public function recordSaleByTransaction($transactionId, $table = 'performance_invoices')
    {
        try {
            $rows = DB::table($table)->where('transaction_id', $transactionId)->get();

            if ($rows->isEmpty()) {
                Log::warning("JournalEntryService::recordSaleByTransaction - no rows found for {$transactionId} in {$table}");
                return;
            }

            $sale = $rows->first();
            $amount = (float) ($rows->sum('bill_grand_total') ?: $rows->sum('total_amount') ?: 0);

            $entity = $sale->customer_name ?? 'Customer';
            $branch = $sale->branch ?? null;
            $userId = $sale->user_id ?? 1;
            $ref = $sale->transaction_id ?? $transactionId;
            $anchorId = $rows->min('id') ?? ($sale->id ?? null);

            if ($anchorId && $this->alreadyExists($table, $anchorId)) {
                Log::info("JournalEntryService::recordSaleByTransaction - already exists: {$table}:{$anchorId}");
                return;
            }

            $description = $this->buildDescriptionFromRecord($table, (array) $sale, 'Sales Invoice');

            switch ($sale->payment_type ?? 1) {
                case 1: // Cash
                    $debitAcc = 'Cash A/c';
                    $creditAcc = 'Sales A/c';
                    $paidThrough = 'Cash';
                    break;
                case 2: // Bank
                    $bank = $this->getBankName($sale->bank_id ?? null);
                    $debitAcc = $bank ? ($bank . ' A/c') : 'Bank A/c';
                    $creditAcc = 'Sales A/c';
                    $paidThrough = $bank ?: 'Bank';
                    break;
                case 3: // Credit
                    $debitAcc = 'Customer A/c';
                    $creditAcc = 'Sales A/c';
                    $paidThrough = 'Credit';
                    break;
                case 4: // POS
                    $bank = $this->getBankName($sale->bank_id ?? null);
                    $debitAcc = $bank ? ($bank . ' A/c') : 'Bank A/c';
                    $creditAcc = 'Sales A/c';
                    $paidThrough = 'POS';
                    break;
                default:
                    $debitAcc = 'Cash A/c';
                    $creditAcc = 'Sales A/c';
                    $paidThrough = 'Cash';
            }

            $this->createPair(
                $ref,
                $sale->created_at ?? $sale->date ?? now(),
                $entity,
                $debitAcc,
                $creditAcc,
                $amount,
                $amount,
                $paidThrough,
                $ref,
                $branch,
                $userId,
                $table,
                $anchorId,
                $description
            );
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::recordSaleByTransaction - error: " . $e->getMessage());
        }
    }

    /**
     * Record Sales Return
     */
    public function recordSalesReturnByTransaction($transactionId, $table = 'returnproducts')
    {
        try {
            $rows = DB::table($table)->where('transaction_id', $transactionId)->get();
            if ($rows->isEmpty()) {
                Log::warning("JournalEntryService::recordSalesReturnByTransaction - no rows for {$transactionId} in {$table}");
                return;
            }

            $ret = $rows->first();
            $amount = (float) ($ret->grand_total ?? $rows->sum('total_amount') ?? 0);
            $entity = $ret->customer_name ?? 'Customer';
            $branch = $ret->branch ?? null;
            $userId = $ret->user_id ?? 1;
            $ref = $ret->transaction_id ?? $transactionId;
            $anchorId = $rows->min('id') ?? ($ret->id ?? null);

            if ($anchorId && $this->alreadyExists($table, $anchorId)) {
                Log::info("JournalEntryService::recordSalesReturnByTransaction - already exists: {$table}:{$anchorId}");
                return;
            }

            $description = $this->buildDescriptionFromRecord($table, (array) $ret, 'Sales Return');

            $returnPayment = $ret->return_payment ?? 0;
            if ($returnPayment == 1) {
                $this->createPair($ref, $ret->created_at ?? now(), $entity, 'Sales Return A/c', 'Cash A/c', $amount, $amount, 'Cash', $ref, $branch, $userId, $table, $anchorId, $description);
            } elseif ($returnPayment == 2) {
                $bank = $this->getBankName($ret->bank_id ?? null);
                $bankAcc = $bank ? ($bank . ' A/c') : 'Bank A/c';
                $this->createPair($ref, $ret->created_at ?? now(), $entity, 'Sales Return A/c', $bankAcc, $amount, $amount, 'Bank', $ref, $branch, $userId, $table, $anchorId, $description);
            } else {
                $this->createPair($ref, $ret->created_at ?? now(), $entity, 'Sales Return A/c', 'Customer A/c', $amount, $amount, 'Credit', $ref, $branch, $userId, $table, $anchorId, $description);
            }
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::recordSalesReturnByTransaction - error: " . $e->getMessage());
        }
    }

    /**
     * Record Purchase Return
     */
    public function recordPurchaseReturnByTransaction($identifier, $table = 'returnpurchases')
    {
        try {
            // many systems store purchase returns with reciept_no
            $rows = DB::table($table)->where('reciept_no', $identifier)->get();
            if ($rows->isEmpty()) {
                Log::warning("JournalEntryService::recordPurchaseReturnByTransaction - no rows for {$identifier} in {$table}");
                return;
            }

            $ret = $rows->first();
            $amount = (float) ($ret->amount ?? $ret->debit_note_amount ?? $rows->sum('amount') ?? 0);
            $entity = $ret->shop_name ?? $ret->supplier ?? 'Supplier';
            $branch = $ret->branch ?? null;
            $userId = $ret->user_id ?? 1;
            $ref = $ret->reciept_no ?? ($ret->transaction_id ?? $identifier);
            $anchorId = $rows->min('id') ?? ($ret->id ?? null);

            if ($anchorId && $this->alreadyExists($table, $anchorId)) {
                Log::info("JournalEntryService::recordPurchaseReturnByTransaction - already exists: {$table}:{$anchorId}");
                return;
            }

            $description = $this->buildDescriptionFromRecord($table, (array) $ret, 'Purchase Return');

            $returnPayment = $ret->return_payment ?? 0;
            if ($returnPayment == 1) {
                $this->createPair($ref, $ret->created_at ?? now(), $entity, 'Cash A/c', 'Purchase Return A/c', $amount, $amount, 'Cash', $ref, $branch, $userId, $table, $anchorId, $description);
            } elseif ($returnPayment == 2) {
                $bank = $this->getBankName($ret->bank_id ?? null);
                $bankAcc = $bank ? ($bank . ' A/c') : 'Bank A/c';
                $this->createPair($ref, $ret->created_at ?? now(), $entity, $bankAcc, 'Purchase Return A/c', $amount, $amount, 'Bank', $ref, $branch, $userId, $table, $anchorId, $description);
            } else {
                $this->createPair($ref, $ret->created_at ?? now(), $entity, 'Supplier A/c', 'Purchase Return A/c', $amount, $amount, 'Credit', $ref, $branch, $userId, $table, $anchorId, $description);
            }
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::recordPurchaseReturnByTransaction - error: " . $e->getMessage());
        }
    }

    /**
     * Record Customer Receipt
     */
    public function recordCustomerReceiptByTransaction($transactionId, $table = 'credit_transactions')
    {
        try {
            $rows = DB::table($table)->where('transaction_id', $transactionId)->get();
            if ($rows->isEmpty()) {
                Log::warning("JournalEntryService::recordCustomerReceiptByTransaction - no rows for {$transactionId} in {$table}");
                return;
            }

            $rec = $rows->first();
            $amount = (float) ($rec->collected_amount ?? $rows->sum('collected_amount') ?? 0);
            $entity = $rec->credit_username ?? 'Customer';
            $branch = $rec->location ?? $rec->branch ?? null;
            $userId = $rec->user_id ?? 1;
            $ref = $rec->transaction_id ?? $transactionId;
            $anchorId = $rows->min('id') ?? ($rec->id ?? null);

            if ($anchorId && $this->alreadyExists($table, $anchorId)) {
                Log::info("JournalEntryService::recordCustomerReceiptByTransaction - already exists: {$table}:{$anchorId}");
                return;
            }

            $description = $this->buildDescriptionFromRecord($table, (array) $rec, 'Customer Receipt');

            if (($rec->payment_type ?? 1) == 1) {
                $this->createPair($ref, $rec->created_at ?? now(), $entity, 'Cash A/c', 'Customer A/c', $amount, $amount, 'Cash', $ref, $branch, $userId, $table, $anchorId, $description);
            } else {
                $bank = $this->getBankName($rec->bank_id ?? null);
                $bankAcc = $bank ? ($bank . ' A/c') : 'Bank A/c';
                $this->createPair($ref, $rec->created_at ?? now(), $entity, $bankAcc, 'Customer A/c', $amount, $amount, 'Bank', $ref, $branch, $userId, $table, $anchorId, $description);
            }
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::recordCustomerReceiptByTransaction - error: " . $e->getMessage());
        }
    }

    /**
     * Record Supplier Payment
     */
    public function recordSupplierPaymentByTransaction($identifier, $table = 'credit_supplier_transactions')
    {
        try {
            $rows = DB::table($table)->where('reciept_no', $identifier)->get();
            if ($rows->isEmpty()) {
                Log::warning("JournalEntryService::recordSupplierPaymentByTransaction - no rows for {$identifier} in {$table}");
                return;
            }

            $pay = $rows->first();
            $amount = (float) ($pay->collectedamount ?? $rows->sum('collectedamount') ?? 0);
            $entity = $pay->credit_supplier_username ?? $pay->supplier_name ?? 'Supplier';
            $branch = $pay->location ?? $pay->branch ?? null;
            $userId = $pay->user_id ?? 1;
            $ref = $pay->reciept_no ?? $identifier;
            $anchorId = $rows->min('id') ?? ($pay->id ?? null);

            if ($anchorId && $this->alreadyExists($table, $anchorId)) {
                Log::info("JournalEntryService::recordSupplierPaymentByTransaction - already exists: {$table}:{$anchorId}");
                return;
            }

            $description = $this->buildDescriptionFromRecord($table, (array) $pay, 'Supplier Payment');

            if (($pay->payment_type ?? 1) == 1) {
                $this->createPair($ref, $pay->created_at ?? now(), $entity, 'Supplier A/c', 'Cash A/c', $amount, $amount, 'Cash', $ref, $branch, $userId, $table, $anchorId, $description);
            } else {
                $bank = $this->getBankName($pay->bank_id ?? null);
                $bankAcc = $bank ? ($bank . ' A/c') : 'Bank A/c';
                $this->createPair($ref, $pay->created_at ?? now(), $entity, 'Supplier A/c', $bankAcc, $amount, $amount, 'Bank', $ref, $branch, $userId, $table, $anchorId, $description);
            }
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::recordSupplierPaymentByTransaction - error: " . $e->getMessage());
        }
    }

    /**
     * Record Income
     */
    public function recordIncome($id, $table = 'account_indirect_incomes')
    {
        try {
            $inc = DB::table($table)->find($id);
            if (!$inc) {
                Log::warning("JournalEntryService::recordIncome - record not found: {$id} in {$table}");
                return;
            }

            $amount = (float) ($inc->amount ?? 0);
            $entity = $inc->direct_income ?? $inc->indirect_income ?? 'Income';
            $branch = $inc->branch ?? null;
            $userId = $inc->user_id ?? 1;
            $ref = $inc->transaction_id ?? $inc->id ?? '';
            $anchorId = $id;

            if ($this->alreadyExists($table, $anchorId)) {
                Log::info("JournalEntryService::recordIncome - already exists: {$table}:{$anchorId}");
                return;
            }

            $description = $this->buildDescriptionFromRecord($table, (array) $inc, 'Indirect Income');

            if (($inc->income_type ?? 1) == 1) {
                $this->createPair($ref, $inc->date ?? now(), $entity, 'Cash A/c', 'Income A/c', $amount, $amount, 'Cash', $ref, $branch, $userId, $table, $anchorId, $description);
            } else {
                $bank = $this->getBankName($inc->bank_id ?? null);
                $bankAcc = $bank ? ($bank . ' A/c') : 'Bank A/c';
                $this->createPair($ref, $inc->date ?? now(), $entity, $bankAcc, 'Income A/c', $amount, $amount, 'Bank', $ref, $branch, $userId, $table, $anchorId, $description);
            }
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::recordIncome - error: " . $e->getMessage());
        }
    }

    /**
     * Record Expense
     */
    public function recordExpense($id, $table = 'accountexpenses')
    {
        try {
            $exp = DB::table($table)->find($id);
            if (!$exp) {
                Log::warning("JournalEntryService::recordExpense - record not found: {$id} in {$table}");
                return;
            }

            $amount = (float) ($exp->amount ?? 0);
            $entity = $exp->direct_expense ?? $exp->indirect_expense ?? 'Expense';
            $branch = $exp->branch ?? null;
            $userId = $exp->user_id ?? 1;
            $ref = $exp->transaction_id ?? $exp->id ?? '';
            $anchorId = $id;

            if ($this->alreadyExists($table, $anchorId)) {
                Log::info("JournalEntryService::recordExpense - already exists: {$table}:{$anchorId}");
                return;
            }

            $description = $this->buildDescriptionFromRecord($table, (array) $exp, 'Expense Entry');

            if (($exp->expense_type ?? 1) == 1) {
                $this->createPair($ref, $exp->date ?? now(), $entity, 'Expense A/c', 'Cash A/c', $amount, $amount, 'Cash', $ref, $branch, $userId, $table, $anchorId, $description);
            } else {
                $bank = $this->getBankName($exp->bank_id ?? null);
                $bankAcc = $bank ? ($bank . ' A/c') : 'Bank A/c';
                $this->createPair($ref, $exp->date ?? now(), $entity, 'Expense A/c', $bankAcc, $amount, $amount, 'Bank', $ref, $branch, $userId, $table, $anchorId, $description);
            }
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::recordExpense - error: " . $e->getMessage());
        }
    }

    /**
     * Record Fund Transfer
     */
    public function recordFundTransfer($id, $table = 'fund_transfer')
    {
        try {
            $ft = DB::table($table)->find($id);
            if (!$ft) {
                Log::warning("JournalEntryService::recordFundTransfer - record not found: {$id} in {$table}");
                return;
            }

            $amount = (float) ($ft->transfer_amount ?? 0);
            $branch = $ft->branch ?? null;
            $userId = $ft->user_id ?? 1;
            $ref = $ft->transaction_id ?? $ft->id ?? '';
            $anchorId = $id;

            if ($this->alreadyExists($table, $anchorId)) {
                Log::info("JournalEntryService::recordFundTransfer - already exists: {$table}:{$anchorId}");
                return;
            }

            $description = $this->buildDescriptionFromRecord($table, (array) $ft, 'Fund Transfer');

            $from = Str::lower($ft->transfer_from ?? '');
            $to = Str::lower($ft->transfer_to ?? '');

            if ($from == 'cash' && $to != 'cash') {
                // Cash -> Bank
                $this->createPair($ref, $ft->date ?? now(), 'Transfer', ($ft->transfer_to . ' A/c'), 'Cash A/c', $amount, $amount, 'Cash→Bank', $ref, $branch, $userId, $table, $anchorId, $description);
            } elseif ($to == 'cash' && $from != 'cash') {
                // Bank -> Cash
                $this->createPair($ref, $ft->date ?? now(), 'Transfer', 'Cash A/c', ($ft->transfer_from . ' A/c'), $amount, $amount, 'Bank→Cash', $ref, $branch, $userId, $table, $anchorId, $description);
            } else {
                // Bank -> Bank or generic
                $this->createPair($ref, $ft->date ?? now(), 'Transfer', ($ft->transfer_to . ' A/c'), ($ft->transfer_from . ' A/c'), $amount, $amount, 'Bank→Bank', $ref, $branch, $userId, $table, $anchorId, $description);
            }
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::recordFundTransfer - error: " . $e->getMessage());
        }
    }

    /**
     * Record Credit Note
     */
    public function recordCreditNote($id, $table = 'credit_note')
    {
        try {
            $note = DB::table($table)->find($id);
            if (!$note) {
                Log::warning("JournalEntryService::recordCreditNote - record not found: {$id} in {$table}");
                return;
            }

            $amount = (float) ($note->credit_note_amount ?? $note->total_amount ?? $note->amount ?? 0);
            $entity = $note->customer_name ?? $note->customer ?? 'Customer';
            $branch = $note->branch ?? null;
            $userId = $note->user_id ?? 1;
            $ref = $note->note_number ?? $note->credit_note_id ?? $note->transaction_id ?? $id;
            $anchorId = $id;

            if ($this->alreadyExists($table, $anchorId)) {
                Log::info("JournalEntryService::recordCreditNote - already exists: {$table}:{$anchorId}");
                return;
            }

            $description = $this->buildDescriptionFromRecord($table, (array) $note, 'Credit Note');

            $this->createPair($ref, $note->date ?? $note->created_at ?? now(), $entity, 'Credit Note A/c', 'Customer A/c', $amount, $amount, 'Credit Note', $ref, $branch, $userId, $table, $anchorId, $description);
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::recordCreditNote - error: " . $e->getMessage());
        }
    }

    /**
     * Record Debit Note
     */
    public function recordDebitNote($id, $table = 'debit_note')
    {
        try {
            $note = DB::table($table)->find($id);
            if (!$note) {
                Log::warning("JournalEntryService::recordDebitNote - record not found: {$id} in {$table}");
                return;
            }

            $amount = (float) ($note->debit_note_amount ?? $note->amount ?? 0);
            $entity = $note->shop_name ?? $note->supplier_name ?? 'Supplier';
            $branch = $note->branch ?? null;
            $userId = $note->user_id ?? 1;
            $ref = $note->note_number ?? $note->debit_note_id ?? $note->reciept_no ?? $id;
            $anchorId = $id;

            if ($this->alreadyExists($table, $anchorId)) {
                Log::info("JournalEntryService::recordDebitNote - already exists: {$table}:{$anchorId}");
                return;
            }

            $description = $this->buildDescriptionFromRecord($table, (array) $note, 'Debit Note');

            $this->createPair($ref, $note->date ?? $note->created_at ?? now(), $entity, 'Supplier A/c', 'Debit Note A/c', $amount, $amount, 'Debit Note', $ref, $branch, $userId, $table, $anchorId, $description);
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::recordDebitNote - error: " . $e->getMessage());
        }
    }

    /* ==========================================================
     * SHARED HELPERS
     * ========================================================== */

    /**
     * Check whether journal entries already exist for a given source_table and source_id.
     */
    public function alreadyExists($table, $sourceId): bool
    {
        try {
            if (!$table || !$sourceId) return false;
            return JournalEntry::where('source_table', $table)->where('source_id', $sourceId)->exists();
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::alreadyExists - DB check failed for {$table}:{$sourceId} - " . $e->getMessage());
            // Fail-safe: return false so callers can attempt to create; duplicates later are less likely but logged.
            return false;
        }
    }

    /**
     * Create a pair of JournalEntry rows (DR and CR) in a single DB transaction.
     *
     * IMPORTANT: This method is the single place that writes to journal_entries.
     *
     * @param mixed  $transactionId  Unique transaction reference (string/int)
     * @param string $date
     * @param string $entity
     * @param string $debitAcc
     * @param string $creditAcc
     * @param float  $debitAmt
     * @param float  $creditAmt
     * @param string $paidThrough
     * @param string $ref
     * @param mixed  $branch
     * @param int    $userId
     * @param string $sourceTable
     * @param mixed  $sourceId
     * @param string|null $description
     */
    private function createPair($transactionId, $date, $entity, $debitAcc, $creditAcc, $debitAmt, $creditAmt, $paidThrough, $ref, $branch, $userId, $sourceTable, $sourceId, $description = null)
    {
        try {
            // Dedup key - use sourceId if present, otherwise fallback to transactionId
            $dedupeId = $sourceId ?: $transactionId ?: null;

            // If an existing pair exists for this source, skip
            if ($dedupeId && $sourceTable && $this->alreadyExists($sourceTable, $dedupeId)) {
                Log::info("JournalEntryService::createPair - skipping (exists) {$sourceTable}:{$dedupeId}");
                return;
            }

            // Build description - prefer explicit, then record-aware fallback
            $desc = $description ?: $this->buildDescriptionFromSource($sourceTable, $ref, $dedupeId);

            DB::transaction(function () use ($transactionId, $date, $entity, $debitAcc, $creditAcc, $debitAmt, $creditAmt, $paidThrough, $ref, $branch, $userId, $sourceTable, $dedupeId, $desc) {
                // DR entry
                JournalEntry::create([
                    'transaction_id' => $transactionId,
                    'entry_date'     => $date ?? now(),
                    'account'        => $debitAcc,
                    'description'    => $desc,
                    'debit'          => $debitAmt ?? 0,
                    'credit'         => 0,
                    'entity'         => $entity,
                    'paid_through'   => $paidThrough,
                    'reference'      => $ref,
                    'admin_id'       => $this->resolveAdminId($userId),
                    'branch'         => $branch,
                    'user_id'        => $userId,
                    'source_table'   => $sourceTable,
                    'source_id'      => $dedupeId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                // CR entry
                JournalEntry::create([
                    'transaction_id' => $transactionId,
                    'entry_date'     => $date ?? now(),
                    'account'        => $creditAcc,
                    'description'    => $desc,
                    'debit'          => 0,
                    'credit'         => $creditAmt ?? 0,
                    'entity'         => $entity,
                    'paid_through'   => $paidThrough,
                    'reference'      => $ref,
                    'admin_id'       => $this->resolveAdminId($userId),
                    'branch'         => $branch,
                    'user_id'        => $userId,
                    'source_table'   => $sourceTable,
                    'source_id'      => $dedupeId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            });

            Log::info("JournalEntryService::createPair - created pair for {$sourceTable}:{$dedupeId} ({$desc})");
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::createPair - failed for {$sourceTable}:{$sourceId} - " . $e->getMessage());
        }
    }

    /**
     * Build a fallback friendly description using source_table + reference|id.
     */
    protected function buildDescriptionFromSource($sourceTable, $ref = null, $sourceId = null)
    {
        if (!$sourceTable) {
            return 'Journal Entry';
        }

        $friendly = ucwords(str_replace(['_', '-'], ' ', $sourceTable));

        if ($ref) {
            return "{$friendly} #{$ref}";
        }
        if ($sourceId) {
            return "{$friendly} #{$sourceId}";
        }
        return $friendly;
    }

    /**
     * Build a context-rich description when we have a record array pulled from source table.
     * Prioritises common identifiers: invoice_no, invoice_number, transaction_id, note_number, reciept_no, id.
     */
    protected function buildDescriptionFromRecord($table, array $record, $fallback = null)
    {
        // common keys to look for
        $keys = [
            'invoice_no', 'invoice_number', 'transaction_id', 'transaction', 'note_number', 'reciept_no',
            'receipt_no', 'credit_note_id', 'debit_note_id', 'id', 'reference', 'note', 'note_no'
        ];

        foreach ($keys as $k) {
            if (!empty($record[$k])) {
                return ucwords(str_replace(['_', '-'], ' ', $table)) . ' #' . $record[$k];
            }
        }

        // Some tables store description/details
        if (!empty($record['details'])) {
            return ucwords(str_replace(['_', '-'], ' ', $table)) . ' - ' . Str::limit($record['details'], 80);
        }

        if (!empty($record['description'])) {
            return ucwords(str_replace(['_', '-'], ' ', $table)) . ' - ' . Str::limit($record['description'], 80);
        }

        // fallback to friendly source build
        return $fallback ?: $this->buildDescriptionFromSource($table, $record['transaction_id'] ?? null, $record['id'] ?? null);
    }

    /**
     * Resolve admin_id from softwareusers table for the given user id.
     * Returns admin_id or null.
     */
    protected function resolveAdminId($userId)
    {
        try {
            if (!$userId) return null;
            $adminId = DB::table('softwareusers')->where('id', $userId)->value('admin_id');
            return $adminId ?: null;
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::resolveAdminId - failed for user {$userId} - " . $e->getMessage());
            return null;
        }
    }

    /**
     * Resolve a bank's display name. Be tolerant: check 'banks' then 'bank' table names.
     */
    private function getBankName($bankId)
    {
        if (!$bankId) return 'Bank';
        try {
            if (Schema::hasTable('banks')) {
                $name = DB::table('banks')->where('id', $bankId)->value('bank_name');
                if ($name) return $name;
            }
            if (Schema::hasTable('bank')) {
                $name = DB::table('bank')->where('id', $bankId)->value('bank_name');
                if ($name) return $name;
            }
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::getBankName - error fetching bank name for id {$bankId} - " . $e->getMessage());
        }
        return 'Bank';
    }

    /* ==========================================================
     * OPTIONAL: Backfill helper you can call manually from tinker/artisan
     * (Not invoked automatically by controllers)
     * ========================================================== */

    /**
     * Backfill missing journal entries for a given source table.
     * Use with caution - run in tinker or artisan command.
     *
     * Example usage in tinker:
     *   app(\App\Services\JournalEntryService::class)->backfillTable('performance_invoices');
     */
    public function backfillTable(string $table, int $limit = 0)
    {
        try {
            if (!Schema::hasTable($table)) {
                Log::warning("JournalEntryService::backfillTable - table {$table} does not exist.");
                return;
            }

            $query = DB::table($table);

            // Try to identify a primary id column
            $rows = $query->select('*')->get();

            $count = 0;
            foreach ($rows as $row) {
                // Determine transaction id or fallback to id
                $row = (array) $row;
                $sourceId = $row['id'] ?? null;
                $transactionId = $row['transaction_id'] ?? ($row['reciept_no'] ?? ($row['invoice_no'] ?? $sourceId));

                if ($sourceId && !JournalEntry::where('source_table', $table)->where('source_id', $sourceId)->exists()) {
                    // Route to appropriate method based on table name
                    switch ($table) {
                        case 'buyproducts':
                            $this->recordPurchaseByTransaction($transactionId, $table);
                            break;
                        case 'performance_invoices':
                            $this->recordSaleByTransaction($transactionId, $table);
                            break;
                        case 'returnproducts':
                            $this->recordSalesReturnByTransaction($transactionId, $table);
                            break;
                        case 'returnpurchases':
                            $this->recordPurchaseReturnByTransaction($transactionId, $table);
                            break;
                        case 'credit_transactions':
                            $this->recordCustomerReceiptByTransaction($transactionId, $table);
                            break;
                        case 'credit_supplier_transactions':
                            $this->recordSupplierPaymentByTransaction($transactionId, $table);
                            break;
                        case 'accountexpenses':
                            $this->recordExpense($sourceId, $table);
                            break;
                        case 'account_indirect_incomes':
                            $this->recordIncome($sourceId, $table);
                            break;
                        case 'fund_transfer':
                            $this->recordFundTransfer($sourceId, $table);
                            break;
                        case 'credit_note':
                            $this->recordCreditNote($sourceId, $table);
                            break;
                        case 'debit_note':
                            $this->recordDebitNote($sourceId, $table);
                            break;
                        default:
                            Log::info("JournalEntryService::backfillTable - no backfill handler for {$table}");
                    }

                    $count++;
                    if ($limit > 0 && $count >= $limit) {
                        Log::info("JournalEntryService::backfillTable - reached limit {$limit}, stopping.");
                        break;
                    }
                }
            }

            Log::info("JournalEntryService::backfillTable - completed for {$table}. Entries processed: {$count}");
        } catch (\Throwable $e) {
            Log::error("JournalEntryService::backfillTable - error: " . $e->getMessage());
        }
    }
}

