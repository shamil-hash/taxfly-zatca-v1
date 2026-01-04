<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\JournalEntryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillPurchaseJournals extends Command
{
    protected $signature = 'backfill:purchase-journals';
    protected $description = 'Backfill all transactional journal entries safely';

    public function handle(JournalEntryService $service)
    {
        $this->info("Starting Backfill of all flows...");

        $jobs = [];

        try {
            // ========== PURCHASES ==========
            $purchaseTables = [
                'buyproducts' => 'transaction_id',
                'new_buyproducts' => 'transaction_id',
                'stockdetails' => 'reciept_no',
                'new_stockdetails' => 'reciept_no'
            ];

            foreach ($purchaseTables as $table => $column) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $transactions = DB::table($table)->distinct()->pluck($column);
                    $count = 0;
                    foreach ($transactions as $tx) {
                        $anchorId = DB::table($table)->where($column, $tx)->min('id');
                        if (!$service->alreadyExists($table, $anchorId)) {
                            $jobs[] = ['Purchases', $tx, $table];
                            $count++;
                        }
                    }
                    $this->info("Queued {$count} new transactions from {$table}");
                }
            }

            // ========== SALES ==========
            $salesTables = [
                'performance_invoices' => 'transaction_id',
                'sales_orders' => 'transaction_id',
                'quotations' => 'transaction_id'
            ];

            foreach ($salesTables as $table => $column) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $transactions = DB::table($table)->distinct()->pluck($column);
                    $count = 0;
                    foreach ($transactions as $tx) {
                        $anchorId = DB::table($table)->where($column, $tx)->min('id');
                        if (!$service->alreadyExists($table, $anchorId)) {
                            $jobs[] = ['Sales', $tx, $table];
                            $count++;
                        }
                    }
                    $this->info("Queued {$count} new sales from {$table}");
                }
            }

            // ========== SALES RETURNS ==========
            if (DB::getSchemaBuilder()->hasTable('returnproducts')) {
                $transactions = DB::table('returnproducts')->distinct()->pluck('transaction_id');
                $count = 0;
                foreach ($transactions as $tx) {
                    $anchorId = DB::table('returnproducts')->where('transaction_id', $tx)->min('id');
                    if (!$service->alreadyExists('returnproducts', $anchorId)) {
                        $jobs[] = ['SalesReturn', $tx, 'returnproducts'];
                        $count++;
                    }
                }
                $this->info("Queued {$count} new sales returns");
            }

            // ========== PURCHASE RETURNS ==========
            if (DB::getSchemaBuilder()->hasTable('returnpurchases')) {
                $transactions = DB::table('returnpurchases')->distinct()->pluck('reciept_no');
                $count = 0;
                foreach ($transactions as $tx) {
                    $anchorId = DB::table('returnpurchases')->where('reciept_no', $tx)->min('id');
                    if (!$service->alreadyExists('returnpurchases', $anchorId)) {
                        $jobs[] = ['PurchaseReturn', $tx, 'returnpurchases'];
                        $count++;
                    }
                }
                $this->info("Queued {$count} new purchase returns");
            }

            // ========== CUSTOMER RECEIPTS ==========
            if (DB::getSchemaBuilder()->hasTable('credit_transactions')) {
                $transactions = DB::table('credit_transactions')->distinct()->pluck('transaction_id');
                $count = 0;
                foreach ($transactions as $tx) {
                    $anchorId = DB::table('credit_transactions')->where('transaction_id', $tx)->min('id');
                    if (!$service->alreadyExists('credit_transactions', $anchorId)) {
                        $jobs[] = ['CustomerReceipt', $tx, 'credit_transactions'];
                        $count++;
                    }
                }
                $this->info("Queued {$count} new customer receipts");
            }

            // ========== SUPPLIER PAYMENTS ==========
            if (DB::getSchemaBuilder()->hasTable('credit_supplier_transactions')) {
                $transactions = DB::table('credit_supplier_transactions')->distinct()->pluck('reciept_no');
                $count = 0;
                foreach ($transactions as $tx) {
                    $anchorId = DB::table('credit_supplier_transactions')->where('reciept_no', $tx)->min('id');
                    if (!$service->alreadyExists('credit_supplier_transactions', $anchorId)) {
                        $jobs[] = ['SupplierPayment', $tx, 'credit_supplier_transactions'];
                        $count++;
                    }
                }
                $this->info("Queued {$count} new supplier payments");
            }

            // ========== EXPENSES ==========
            if (DB::getSchemaBuilder()->hasTable('accountexpenses')) {
                $expenses = DB::table('accountexpenses')->pluck('id');
                $count = 0;
                foreach ($expenses as $id) {
                    if (!$service->alreadyExists('accountexpenses', $id)) {
                        $jobs[] = ['Expense', $id, 'accountexpenses'];
                        $count++;
                    }
                }
                $this->info("Queued {$count} new expenses");
            }

            // ========== INCOME ==========
            if (DB::getSchemaBuilder()->hasTable('account_indirect_incomes')) {
                $incomes = DB::table('account_indirect_incomes')->pluck('id');
                $count = 0;
                foreach ($incomes as $id) {
                    if (!$service->alreadyExists('account_indirect_incomes', $id)) {
                        $jobs[] = ['Income', $id, 'account_indirect_incomes'];
                        $count++;
                    }
                }
                $this->info("Queued {$count} new income entries");
            }

            // ========== FUND TRANSFERS ==========
            if (DB::getSchemaBuilder()->hasTable('fund_transfer')) {
                $transfers = DB::table('fund_transfer')->pluck('id');
                $count = 0;
                foreach ($transfers as $id) {
                    if (!$service->alreadyExists('fund_transfer', $id)) {
                        $jobs[] = ['FundTransfer', $id, 'fund_transfer'];
                        $count++;
                    }
                }
                $this->info("Queued {$count} new fund transfers");
            }

            // ========== CREDIT NOTES ==========
            if (DB::getSchemaBuilder()->hasTable('credit_note')) {
                $notes = DB::table('credit_note')->pluck('id');
                $count = 0;
                foreach ($notes as $id) {
                    if (!$service->alreadyExists('credit_note', $id)) {
                        $jobs[] = ['CreditNote', $id, 'credit_note'];
                        $count++;
                    }
                }
                $this->info("Queued {$count} new credit notes");
            }

            // ========== DEBIT NOTES ==========
            if (DB::getSchemaBuilder()->hasTable('debit_note')) {
                $notes = DB::table('debit_note')->pluck('id');
                $count = 0;
                foreach ($notes as $id) {
                    if (!$service->alreadyExists('debit_note', $id)) {
                        $jobs[] = ['DebitNote', $id, 'debit_note'];
                        $count++;
                    }
                }
                $this->info("Queued {$count} new debit notes");
            }

        } catch (\Exception $e) {
            $this->error("Error gathering transactions: " . $e->getMessage());
            Log::error("Error gathering transactions for backfill: " . $e->getMessage());
            return;
        }

        $total = count($jobs);
        $this->info("Total NEW journal tasks: {$total}");

        if ($total === 0) {
            $this->info("No new transactions found to process.");
            return;
        }

        $this->output->progressStart($total);

        $successCount = 0;
        $errorCount = 0;

        foreach ($jobs as [$label, $value, $table]) {
            try {
                switch ($label) {
                    case 'Purchases':
                        $service->fromPurchase($value, $table);
                        break;
                    case 'Sales':
                        $service->fromSale($value, $table);
                        break;
                    case 'SalesReturn':
                        $service->fromSalesReturn($value, $table);
                        break;
                    case 'PurchaseReturn':
                        $service->fromPurchaseReturn($value, $table);
                        break;
                    case 'CustomerReceipt':
                        $service->fromCustomerReceipt($value, $table);
                        break;
                    case 'SupplierPayment':
                        $service->fromSupplierPayment($value, $table);
                        break;
                    case 'Expense':
                        $service->fromExpense($value, $table);
                        break;
                    case 'Income':
                        $service->fromIncome($value, $table);
                        break;
                    case 'FundTransfer':
                        $service->fromFundTransfer($value, $table);
                        break;
                    case 'CreditNote':
                        $service->fromCreditNote($value, $table);
                        break;
                    case 'DebitNote':
                        $service->fromDebitNote($value, $table);
                        break;
                }
                $successCount++;
            } catch (\Throwable $e) {
                $errorCount++;
                $this->error("Error processing {$label} - {$value} from {$table}: " . $e->getMessage());
                Log::error("Backfill Error: {$label} - {$value} from {$table}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info("Backfill complete. Success: {$successCount}, Errors: {$errorCount}, Total: {$total}");
    }
}
