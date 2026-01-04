<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\CreditUser;
use App\Models\JournalTransaction;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class JournalController extends Controller
{
    /**
     * Display journal entries page
     *
     * @return View
     */
    public function journalentry(): View
    {
        try {
            // Get entities for dropdowns
            $entities = $this->getEntities();
            $accounts = $this->getAccounts();
            $branches = Branch::where('status', 1)->get();
            
            // Set default date range (current month)
            $filters = [
                'start_date' => date('Y-m-01'),
                'end_date' => date('Y-m-d'),
                'account_id' => '',
                'entity_id' => '',
                'entity_type' => '',
                'branch_id' => '',
                'reference' => ''
            ];

            // Return the existing blade file with all required data
            return view('chartaccounts.journalentry', compact('entities', 'accounts', 'branches', 'filters'));
        } catch (\Exception $e) {
            Log::error('Error loading journal entry page: ' . $e->getMessage());
            
            // Fallback with empty data if there's an error
            $entities = [];
            $accounts = [];
            $branches = [];
            $filters = [
                'start_date' => date('Y-m-01'),
                'end_date' => date('Y-m-d'),
                'account_id' => '',
                'entity_id' => '',
                'entity_type' => '',
                'branch_id' => '',
                'reference' => ''
            ];
            
            return view('chartaccounts.journalentry', compact('entities', 'accounts', 'branches', 'filters'))
                ->with('error', 'Unable to load journal page.');
        }
    }

    /**
     * Save a new journal entry
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveJournalEntry(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'transaction_date' => 'required|date',
                'branch_id' => 'required|exists:branches,id',
                'reference' => 'nullable|string|max:255',
                'entries' => 'required|array|min:2',
                'entries.*.account_id' => 'required|exists:account_types,id',
                'entries.*.description' => 'required|string|max:255',
                'entries.*.amount' => 'required|numeric|min:0.01',
                'entries.*.type' => 'required|in:debit,credit'
            ], [
                'entries.min' => 'Each transaction must have at least two entries.',
                'entries.*.account_id.required' => 'Account selection is required for all entries.',
                'entries.*.description.required' => 'Description is required for all entries.',
                'entries.*.amount.required' => 'Amount is required for all entries.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check debit/credit equality
            $debitTotal = 0;
            $creditTotal = 0;
            
            foreach ($request->entries as $entry) {
                if ($entry['type'] === 'debit') {
                    $debitTotal += $entry['amount'];
                } else {
                    $creditTotal += $entry['amount'];
                }
            }
            
            if (round($debitTotal, 2) !== round($creditTotal, 2)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debit and credit totals must be equal',
                    'debit_total' => $debitTotal,
                    'credit_total' => $creditTotal
                ], 422);
            }

            DB::beginTransaction();
            
            // Generate a unique transaction ID
            $transactionId = 'TRX' . time() . rand(1000, 9999);
            
            foreach ($request->entries as $entryData) {
                $entry = new JournalTransaction();
                $entry->transaction_id = $transactionId;
                $entry->transaction_date = $request->transaction_date;
                $entry->branch_id = $request->branch_id;
                $entry->reference = $request->reference;
                
                // Set entity information if provided
                if (!empty($entryData['entity_id']) && !empty($entryData['entity_type'])) {
                    $entry->entity_id = $entryData['entity_id'];
                    $entry->entity_type = $entryData['entity_type'];
                }
                
                $entry->account_id = $entryData['account_id'];
                $entry->description = $entryData['description'];
                
                if ($entryData['type'] === 'debit') {
                    $entry->debit = $entryData['amount'];
                    $entry->credit = 0;
                } else {
                    $entry->debit = 0;
                    $entry->credit = $entryData['amount'];
                }
                
                // Mark as manual entry
                $entry->source_table = 'manual';
                $entry->source_id = $transactionId;
                
                $entry->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Journal entry created successfully',
                'transaction_id' => $transactionId
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating journal entry: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating journal entry: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filtered data for Listing view
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getListingData(Request $request): JsonResponse
    {
        try {
            $query = JournalTransaction::with(['account', 'branch']);
            
            // Apply filters
            $this->applyFilters($query, $request);
            
            // Order by date and transaction ID
            $query->orderBy('transaction_date', 'desc')
                  ->orderBy('transaction_id')
                  ->orderBy('id');
            
            // Paginate results
            $perPage = $request->input('per_page', 25);
            $entries = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'entries' => $entries,
                'pagination' => [
                    'current_page' => $entries->currentPage(),
                    'last_page' => $entries->lastPage(),
                    'per_page' => $entries->perPage(),
                    'total' => $entries->total(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching listing data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch journal entries.'
            ], 500);
        }
    }

    /**
     * Get filtered data for Journal Book view with transaction grouping
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getJournalBookData(Request $request): JsonResponse
    {
        try {
            // First get distinct transactions with filter applied
            $transactionQuery = JournalTransaction::select('transaction_id', 'source_table', 'source_id', 'transaction_date')
                ->groupBy('transaction_id', 'source_table', 'source_id', 'transaction_date');
            
            // Apply filters
            $this->applyFilters($transactionQuery, $request);
            
            // Order by date and transaction ID
            $transactionQuery->orderBy('transaction_date', 'desc')
                            ->orderBy('transaction_id');
            
            // Paginate transactions
            $perPage = $request->input('per_page', 25);
            $transactions = $transactionQuery->paginate($perPage);
            
            // For each transaction, get all entries
            $transactionIds = $transactions->pluck('transaction_id')->toArray();
            $sourceInfo = $transactions->mapWithKeys(function($item) {
                return [$item->transaction_id => ['source_table' => $item->source_table, 'source_id' => $item->source_id]];
            });
            
            $entries = JournalTransaction::with(['account', 'branch', 'entity'])
                ->whereIn('transaction_id', $transactionIds)
                ->orderBy('transaction_date', 'desc')
                ->orderBy('transaction_id')
                ->orderBy('id')
                ->get()
                ->groupBy('transaction_id');
            
            return response()->json([
                'success' => true,
                'transactions' => $transactions,
                'entries' => $entries,
                'source_info' => $sourceInfo,
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching journal book data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch journal book data.'
            ], 500);
        }
    }

    /**
     * Get entities for select2 dropdown
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getEntitiesForSelect2(Request $request): JsonResponse
    {
        try {
            $search = $request->input('q');
            
            $creditUsers = CreditUser::select(
                    DB::raw("id, name as text, 'credituser' as type")
                )
                ->where('status', 1)
                ->when($search, function($query) use ($search) {
                    return $query->where('name', 'like', "%{$search}%");
                });
            
            $suppliers = Supplier::select(
                    DB::raw("id, name as text, 'supplier' as type")
                )
                ->where('status', 1)
                ->when($search, function($query) use ($search) {
                    return $query->where('name', 'like', "%{$search}%");
                });
            
            $banks = Bank::select(
                    DB::raw("id, name as text, 'bank' as type")
                )
                ->where('status', 1)
                ->when($search, function($query) use ($search) {
                    return $query->where('name', 'like', "%{$search}%");
                });
            
            $entities = $creditUsers->union($suppliers)->union($banks)->get();
            
            return response()->json([
                'results' => $entities,
                'pagination' => ['more' => false]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching entities for select2: ' . $e->getMessage());
            
            return response()->json([
                'results' => [],
                'pagination' => ['more' => false]
            ]);
        }
    }

    /**
     * Get accounts for select2 dropdown
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAccountsForSelect2(Request $request): JsonResponse
    {
        try {
            $search = $request->input('q');
            
            $accounts = AccountType::select('id', 'name as text')
                ->where('status', 1)
                ->when($search, function($query) use ($search) {
                    return $query->where('name', 'like', "%{$search}%");
                })
                ->get();
            
            return response()->json([
                'results' => $accounts,
                'pagination' => ['more' => false]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching accounts for select2: ' . $e->getMessage());
            
            return response()->json([
                'results' => [],
                'pagination' => ['more' => false]
            ]);
        }
    }

    /**
     * Apply filters to query
     *
     * @param $query
     * @param Request $request
     * @return mixed
     */
    private function applyFilters($query, Request $request)
    {
        // Date range filter
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->where('transaction_date', '<=', $request->end_date);
        }
        
        // Account filter
        if ($request->has('account_id') && !empty($request->account_id)) {
            $query->where('account_id', $request->account_id);
        }
        
        // Entity filter
        if ($request->has('entity_id') && !empty($request->entity_id) && 
            $request->has('entity_type') && !empty($request->entity_type)) {
            $query->where('entity_id', $request->entity_id)
                  ->where('entity_type', $request->entity_type);
        }
        
        // Branch filter
        if ($request->has('branch_id') && !empty($request->branch_id)) {
            $query->where('branch_id', $request->branch_id);
        }
        
        // Reference filter
        if ($request->has('reference') && !empty($request->reference)) {
            $query->where('reference', 'like', '%' . $request->reference . '%');
        }
        
        return $query;
    }

    /**
     * Get entities from creditusers, suppliers, and bank tables
     *
     * @return array
     */
    private function getEntities(): array
    {
        try {
            $creditUsers = CreditUser::select(
                    DB::raw("id, name, 'credituser' as type")
                )
                ->where('status', 1);
            
            $suppliers = Supplier::select(
                    DB::raw("id, name, 'supplier' as type")
                )
                ->where('status', 1);
            
            $banks = Bank::select(
                    DB::raw("id, name, 'bank' as type")
                )
                ->where('status', 1);
            
            return $creditUsers->union($suppliers)->union($banks)->get()->toArray();
        } catch (\Exception $e) {
            Log::error('Error fetching entities: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get accounts from account_type table with fallback
     *
     * @return array
     */
    private function getAccounts(): array
    {
        try {
            $accounts = AccountType::where('status', 1)->get();
            
            // Fallback to default accounts if empty
            if ($accounts->isEmpty()) {
                $accounts = collect([
                    (object)['id' => 1, 'name' => 'Cash', 'code' => '1001'],
                    (object)['id' => 2, 'name' => 'Accounts Receivable', 'code' => '1002'],
                    (object)['id' => 3, 'name' => 'Accounts Payable', 'code' => '2001'],
                    (object)['id' => 4, 'name' => 'Revenue', 'code' => '3001'],
                    (object)['id' => 5, 'name' => 'Expenses', 'code' => '4001'],
                ]);
            }
            
            return $accounts->toArray();
        } catch (\Exception $e) {
            Log::error('Error fetching accounts: ' . $e->getMessage());
            
            // Return fallback accounts even if there's an error
            return [
                (object)['id' => 1, 'name' => 'Cash', 'code' => '1001'],
                (object)['id' => 2, 'name' => 'Accounts Receivable', 'code' => '1002'],
                (object)['id' => 3, 'name' => 'Accounts Payable', 'code' => '2001'],
                (object)['id' => 4, 'name' => 'Revenue', 'code' => '3001'],
                (object)['id' => 5, 'name' => 'Expenses', 'code' => '4001'],
            ];
        }
    }
}