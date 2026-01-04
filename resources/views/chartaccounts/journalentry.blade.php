<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Journal Entry</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
    @if (Session('adminuser'))
        @include('layouts/adminsidebar')
    @elseif(Session('softwareuser'))
        @include('layouts/usersidebar')
    @endif

    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 20px 20px 20px 280px;
            background-color: #f8f9fa;
            font-size: 14px;
        }

        @media (max-width: 1200px) {
            body { padding-left: 220px; }
        }

        @media (max-width: 768px) {
            body { padding-left: 20px; font-size: 13px; }
            .table-responsive { overflow-x: auto; }
        }

        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .report-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #187f6a;
        }

        .report-title {
            font-size: 20px;
            color: #187f6a;
            margin: 10px 0;
            font-weight: 600;
        }

        .filter-section {
            background: #f1f8f7;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        table th, table td {
            vertical-align: middle;
            text-align: center;
            font-size: 13px;
        }

        table th {
            background-color: #187f6a;
            color: white;
        }

        table input, table select {
            font-size: 13px;
            padding: 4px 6px;
            height: 30px;
        }

        .entity-other-input, .account-other-input {
            margin-top: 5px;
            display: none;
        }
        
        .amount-input {
            text-align: right;
        }
        
        .td-relative {
            position: relative;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 11px;
            position: absolute;
            bottom: -16px;
            left: 0;
            right: 0;
            text-align: center;
        }
        
        .balance-indicator {
            padding: 10px 15px;
            border-radius: 6px;
            font-weight: 500;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        
        .balance-valid {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .balance-invalid {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .transaction-summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #187f6a;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .summary-item {
            display: flex;
            flex-direction: column;
            margin: 0 15px;
        }
        
        .summary-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 4px;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: 600;
        }
        
        .summary-balance {
            color: #187f6a;
        }
        
        .summary-unbalanced {
            color: #dc3545;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        
        .btn-action {
            width: 28px;
            height: 28px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
        }

        /* Enhanced Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 25px 0;
            gap: 8px;
            flex-wrap: wrap;
        }
        .page-info {
            display: flex;
            align-items: center;
            margin: 0 10px;
        }
        .page-input-box {
            width: 50px;
            text-align: center;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 0 5px;
        }
        .go-to-page {
            background: #187f6a;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 5px;
        }
        .page-btn {
            padding: 5px 10px;
            border: 1px solid #ddd;
            background: white;
            color: #187f6a;
            border-radius: 4px;
            cursor: pointer;
        }
        .page-btn:hover {
            background: #f5f5f5;
        }
        .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .nav-tabs {
            border-bottom: 2px solid #187f6a;
            margin-top: 30px;
        }
        
        .nav-tabs > li > a {
            color: #555;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 4px 4px 0 0;
        }
        
        .nav-tabs > li.active > a, 
        .nav-tabs > li.active > a:hover, 
        .nav-tabs > li.active > a:focus {
            background-color: #187f6a;
            color: #fff !important;
            border: 1px solid #187f6a;
        }
        
        .nav-tabs > li > a:hover {
            background-color: #e0f2ef;
            color: #187f6a;
            border: 1px solid #e0f2ef;
        }
        
        .journal-book-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        
        .journal-book-table th {
            background-color: #f8f9fa;
            color: #333;
            text-align: left;
            padding: 8px;
            border: 1px solid #dee2e6;
        }
        
        .journal-book-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }
        
        .journal-book-table .date-col {
            width: 100px;
        }
        
        .journal-book-table .particulars-col {
            width: 55%;
        }
        
        .journal-book-table .amount-col {
            width: 15%;
            text-align: right;
        }
        
        .journal-entry-particulars {
            padding-left: 20px;
        }
        
        .journal-entry-particulars .debit-line {
            font-weight: bold;
            color: #115c4c;
        }
        
        .journal-entry-particulars .credit-line {
            padding-left: 40px;
            color: #555;
        }
        
        .journal-entry-narration {
            font-style: italic;
            color: #666;
            margin-top: 5px;
            padding-left: 20px;
        }
        
        .transaction-header {
            background-color: #e9ecef;
            font-weight: bold;
            padding: 8px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid #187f6a;
        }
        
        /* Fix for balance row vertical alignment */
        #balance-indicator {
            padding: 0;
        }
        
        #balance-indicator > div {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        
        /* Transaction type badges */
        .transaction-type-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 10px;
        }
        
        .transaction-sale {
            background-color: #d4edda;
            color: #155724;
        }
        
        .transaction-purchase {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .transaction-expense {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .transaction-income {
            background-color: #d4edda;
            color: #155724;
        }
        
        .transaction-transfer {
            background-color: #d6d8d9;
            color: #383d41;
        }
        
        .transaction-return {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .transaction-general {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .running-balance {
            font-weight: 600;
            background-color: #f8f9fa;
        }
        
        /* Sidebar styles */
        .sidebar {
            height: 100%;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #187f6a;
            overflow-x: hidden;
            padding-top: 20px;
            z-index: 1000;
        }
        
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 16px;
            color: white;
            display: block;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar a:hover {
            background-color: #13614f;
        }
        
        @media screen and (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .sidebar a {float: left;}
        }
    </style>
</head>

<body>
    
    <div class="report-container">
        {{-- Header --}}
        <div class="report-header">
            <div><strong>{{ $shopdatas[0]->company ?? 'Company Name' }}</strong></div>
            <div>{{ $shopdatas[0]->location ?? 'Location' }} | {{ $shopdatas[0]->mobile ?? 'Phone Number' }}</div>
            <div class="report-title">JOURNAL ENTRY</div>
            <div>As of {{ \Carbon\Carbon::now()->format('d M Y') }}</div>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Filter --}}
        <div class="filter-section">
            <form method="GET" action="{{ url()->current() }}" id="filterForm">
                <input type="hidden" name="tab" id="activeTab" value="{{ request('tab', 'manual') }}">
                <div class="row" style="margin-bottom: 5px;">
                    <div class="col-md-2"><label>From Date</label><input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}"></div>
                    <div class="col-md-2"><label>To Date</label><input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}"></div>
                    <div class="col-md-2"><label>Entity</label><input type="text" name="entity" class="form-control" value="{{ request('entity') }}" placeholder="e.g. Supplier"></div>
                    <div class="col-md-2"><label>Reference</label><input type="text" name="reference" class="form-control" value="{{ request('reference') }}"></div>
                    <div class="col-md-2">
                        <label>Method</label>
                        <select name="paid_through" class="form-control">
                            <option value="">-- Select --</option>
                            <option value="Bank" {{ request('paid_through') == 'Bank' ? 'selected' : '' }}>Bank</option>
                            <option value="Cheque" {{ request('paid_through') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="Card" {{ request('paid_through') == 'Card' ? 'selected' : '' }}>Card</option>
                        </select>
                    </div>
                    <div class="col-md-2 text-right" style="margin-top: 25px;">
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        <button type="button" onclick="window.print()" class="btn btn-default btn-sm">Print</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="{{ request('tab', 'manual') == 'manual' ? 'active' : '' }}">
                <a href="#manual" aria-controls="manual" role="tab" data-toggle="tab">
                    Manual Entry
                </a>
            </li>
            <li role="presentation" class="{{ request('tab') == 'listing' ? 'active' : '' }}">
                <a href="#listing" aria-controls="listing" role="tab" data-toggle="tab">
                    Listing View
                </a>
            </li>
            <li role="presentation" class="{{ request('tab') == 'book' ? 'active' : '' }}">
                <a href="#book" aria-controls="book" role="tab" data-toggle="tab">
                    Journal Book
                </a>
            </li>
        </ul>

        <div class="tab-content" style="margin-top:20px;">
            {{-- Manual Entry Tab --}}
            <div role="tabpanel" class="tab-pane fade {{ request('tab', 'manual') == 'manual' ? 'in active' : '' }}" id="manual">
                {{-- Entry Form --}}
                <form action="{{ route('journalentry.save') }}" method="POST" id="journalForm">
                    @csrf
                    
                    <div class="transaction-summary">
                        <div class="summary-item">
                            <span class="summary-label">Total Debit</span>
                            <span class="summary-value" id="summary-debit">0.00</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Credit</span>
                            <span class="summary-value" id="summary-credit">0.00</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Balance</span>
                            <span class="summary-value summary-balance" id="summary-balance">0.00</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Status</span>
                            <span class="summary-value" id="summary-status">
                                <span style="color: #dc3545;">Unbalanced</span>
                            </span>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="date">Date</th>
                                    <th class="entity">Entity</th>
                                    <th class="account">Account</th>
                                    <th class="description">Description</th>
                                    <th class="method">Method</th>
                                    <th class="reference">Reference</th>
                                    <th class="debit">Debit</th>
                                    <th class="credit">Credit</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody id="journal-entry-body">
                                @php $rowIndex = 0; @endphp
                                @if(old('entries'))
                                    @foreach(old('entries') as $index => $entry)
                                        <tr>
                                            <td class="td-relative">
                                                <input type="date" name="entries[{{ $index }}][entry_date]" value="{{ $entry['entry_date'] }}" required class="form-control">
                                                @if($errors->has("entries.$index.entry_date"))
                                                    <div class="error-message">{{ $errors->first("entries.$index.entry_date") }}</div>
                                                @endif
                                            </td>
                                            <td class="td-relative">
                                                <select name="entries[{{ $index }}][entity]" class="form-control {{ $errors->has("entries.$index.entity") ? 'error' : '' }}" onchange="toggleEntityOther(this)">
                                                    <option value="">-- Select Entity --</option>
                                                    @foreach ($entities as $entity)
                                                        <option value="{{ $entity }}" {{ $entry['entity'] == $entity ? 'selected' : '' }}>{{ $entity }}</option>
                                                    @endforeach
                                                    <option value="Other" {{ $entry['entity'] == 'Other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                                <input type="text" name="entries[{{ $index }}][entity_other]" value="{{ $entry['entity_other'] ?? '' }}" class="form-control entity-other-input {{ $errors->has("entries.$index.entity_other") ? 'error' : '' }}" placeholder="Enter Entity" {{ $entry['entity'] == 'Other' ? 'required' : '' }}>
                                                @if($errors->has("entries.$index.entity"))
                                                    <div class="error-message">{{ $errors->first("entries.$index.entity") }}</div>
                                                @endif
                                            </td>
                                            <td class="td-relative">
                                                <select name="entries[{{ $index }}][account]" class="form-control {{ $errors->has("entries.$index.account") ? 'error' : '' }}" required onchange="toggleAccountOther(this)">
                                                    <option value="">-- Select Account --</option>
                                                    @foreach ($accounts as $account)
                                                        <option value="{{ $account }}" {{ $entry['account'] == $account ? 'selected' : '' }}>{{ $account }}</option>
                                                    @endforeach
                                                    <option value="Other" {{ $entry['account'] == 'Other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                                <input type="text" name="entries[{{ $index }}][account_other]" value="{{ $entry['account_other'] ?? '' }}" class="form-control account-other-input {{ $errors->has("entries.$index.account_other") ? 'error' : '' }}" placeholder="Enter Account" {{ $entry['account'] == 'Other' ? 'required' : '' }}>
                                                @if($errors->has("entries.$index.account"))
                                                    <div class="error-message">{{ $errors->first("entries.$index.account") }}</div>
                                                @endif
                                            </td>
                                            <td><input type="text" name="entries[{{ $index }}][description]" value="{{ $entry['description'] ?? '' }}" class="form-control"></td>
                                            <td>
                                                <select name="entries[{{ $index }}][paid_through]" class="form-control">
                                                    <option value="">-- Select --</option>
                                                    <option value="Bank" {{ $entry['paid_through'] == 'Bank' ? 'selected' : '' }}>Bank</option>
                                                    <option value="Cheque" {{ $entry['paid_through'] == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                                    <option value="Card" {{ $entry['paid_through'] == 'Card' ? 'selected' : '' }}>Card</option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="entries[{{ $index }}][reference]" value="{{ $entry['reference'] ?? '' }}" class="form-control"></td>
                                            <td class="td-relative">
                                                <input type="number" name="entries[{{ $index }}][debit]" value="{{ $entry['debit'] ?? '' }}" step="0.01" class="form-control amount-input" oninput="updateRowTotal(this)" placeholder="0.00">
                                                @if($errors->has("entries.$index.debit"))
                                                    <div class="error-message">{{ $errors->first("entries.$index.debit") }}</div>
                                                @endif
                                            </td>
                                            <td class="td-relative">
                                                <input type="number" name="entries[{{ $index }}][credit]" value="{{ $entry['credit'] ?? '' }}" step="0.01" class="form-control amount-input" oninput="updateRowTotal(this)" placeholder="0.00">
                                                @if($errors->has("entries.$index.credit"))
                                                    <div class="error-message">{{ $errors->first("entries.$index.credit") }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    @if($index == 0)
                                                        <button type="button" class="btn btn-success btn-sm btn-action add-row">+</button>
                                                    @else
                                                        <button type="button" class="btn btn-danger btn-sm btn-action remove-row">×</button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @php $rowIndex++; @endphp
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="td-relative">
                                            <input type="date" name="entries[0][entry_date]" required class="form-control" value="{{ date('Y-m-d') }}">
                                        </td>
                                        <td class="td-relative">
                                            <select name="entries[0][entity]" class="form-control" onchange="toggleEntityOther(this)">
                                                <option value="">-- Select Entity --</option>
                                                @foreach ($entities as $entity)
                                                    <option value="{{ $entity }}">{{ $entity }}</option>
                                                @endforeach
                                                <option value="Other">Other</option>
                                            </select>
                                            <input type="text" name="entries[0][entity_other]" class="form-control entity-other-input" placeholder="Enter Entity">
                                        </td>
                                        <td class="td-relative">
                                            <select name="entries[0][account]" class="form-control" required onchange="toggleAccountOther(this)">
                                                <option value="">-- Select Account --</option>
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account }}">{{ $account }}</option>
                                                @endforeach
                                                <option value="Other">Other</option>
                                            </select>
                                            <input type="text" name="entries[0][account_other]" class="form-control account-other-input" placeholder="Enter Account">
                                        </td>
                                        <td><input type="text" name="entries[0][description]" class="form-control"></td>
                                        <td>
                                            <select name="entries[0][paid_through]" class="form-control">
                                                <option value="">-- Select --</option>
                                                <option value="Bank">Bank</option>
                                                <option value="Cheque">Cheque</option>
                                                <option value="Card">Card</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="entries[0][reference]" class="form-control"></td>
                                        <td><input type="number" name="entries[0][debit]" step="0.01" class="form-control amount-input" oninput="updateRowTotal(this)" placeholder="0.00"></td>
                                        <td><input type="number" name="entries[0][credit]" step="0.01" class="form-control amount-input" oninput="updateRowTotal(this)" placeholder="0.00"></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button type="button" class="btn btn-success btn-sm btn-action add-row">+</button>
                                            </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-right"><strong>Totals:</strong></td>
                                        <td class="text-right"><strong id="debit-total">0.00</strong></td>
                                        <td class="text-right"><strong id="credit-total">0.00</strong></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-right"><strong>Balance:</strong></td>
                                        <td colspan="3" id="balance-indicator">
                                            <div class="balance-indicator balance-invalid">
                                                Journal entries are not balanced
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="text-right" style="margin: 20px 0 40px;">
                            <button type="button" id="validateEntries" class="btn btn-info">
                                Validate
                            </button>
                            <button type="submit" class="btn btn-primary" id="saveButton" disabled title="Fill required fields and balance entries to enable">
                                Save Entries
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Listing View Tab --}}
                <div role="tabpanel" class="tab-pane fade {{ request('tab') == 'listing' ? 'in active' : '' }}" id="listing">
                    <h3>Journal Entry Records</h3>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="slno">Sl No.</th>
                                    <th class="date">Date</th>
                                    <th class="entity">Entity</th>
                                    <th class="account">Account</th>
                                    <th class="description">Description</th>
                                    <th class="method">Method</th>
                                    <th class="reference">Reference</th>
                                    <th class="debit">Debit</th>
                                    <th class="credit">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    // Get overall totals from the controller
                                    $totalDebit = $overallTotals['debit'] ?? 0;
                                    $totalCredit = $overallTotals['credit'] ?? 0;
                                    
                                    $sl = ($journalEntries->currentPage() - 1) * $journalEntries->perPage() + 1; 
                                @endphp
                                @forelse($journalEntries as $entry)
                                    <tr>
                                        <td class="slno">{{ $sl++ }}</td>
                                        <td class="date">{{ \Carbon\Carbon::parse($entry->entry_date)->format('d-m-Y') }}</td>
                                        <td class="entity">{{ $entry->entity }}</td>
                                        <td class="account">{{ $entry->account }}</td>
                                        <td class="description">{{ $entry->description }}</td>
                                        <td class="method">{{ $entry->paid_through }}</td>
                                        <td class="reference">{{ $entry->reference }}</td>
                                        <td class="debit">{{ $currency }} {{ number_format($entry->debit, 2) }}</td>
                                        <td class="credit">{{ $currency }} {{ number_format($entry->credit, 2) }}</td>
                                        </tr>
                                @empty
                                    <tr><td colspan="9" class="text-center">No journal entries found.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr style="background: #e8f4f2;">
                                    <td colspan="7" class="text-right">Overall Total</td>
                                    <td class="debit">{{ $currency }} {{ number_format($totalDebit, 2) }}</td>
                                    <td class="credit">{{ $currency }} {{ number_format($totalCredit, 2) }}</td>
                                </tr>
                                <tr style="background: #e8f4f2;">
                                    <td colspan="7" class="text-right">Balance</td>
                                    <td colspan="2" class="text-center {{ $totalDebit == $totalCredit ? 'text-success' : 'text-danger' }}">
                                        {{ $currency }} {{ number_format(abs($totalDebit - $totalCredit), 2) }} 
                                        {{ $totalDebit > $totalCredit ? 'Debit' : ($totalDebit < $totalCredit ? 'Credit' : 'Balanced') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Enhanced Pagination -->
                    <div class="pagination-container">
                        <button onclick="goToPage(1)" class="page-btn {{ $journalEntries->currentPage() == 1 ? 'disabled' : '' }}" title="First Page">
                            &laquo;&laquo;
                        </button>
                        <button onclick="goToPage({{ $journalEntries->currentPage() - 1 }})" class="page-btn {{ $journalEntries->currentPage() == 1 ? 'disabled' : '' }}" title="Previous Page">
                            &laquo;
                        </button>
                        
                        <div class="page-info">
                            <span>Page</span>
                            <input type="number" class="page-input-box" id="pageInput" 
                                   min="1" max="{{ $journalEntries->lastPage() }}" 
                                   value="{{ $journalEntries->currentPage() }}">
                            <span>of {{ $journalEntries->lastPage() }}</span>
                            <button class="go-to-page" onclick="goToPage()">Go</button>
                        </div>
                        
                        <button onclick="goToPage({{ $journalEntries->currentPage() + 1 }})" class="page-btn {{ $journalEntries->currentPage() == $journalEntries->lastPage() ? 'disabled' : '' }}" title="Next Page">
                            &raquo;
                        </button>
                        <button onclick="goToPage({{ $journalEntries->lastPage() }})" class="page-btn {{ $journalEntries->currentPage() == $journalEntries->lastPage() ? 'disabled' : '' }}" title="Last Page">
                            &laquo;&laquo;
                        </button>
                    </div>
                </div>

                {{-- Journal Book Tab --}}
                <div role="tabpanel" class="tab-pane fade {{ request('tab') == 'book' ? 'in active' : '' }}" id="book">
                    <h3>Journal Book (Chronological)</h3>
                    
                    @php
                        // Get all entries for journal book (not just current page)
                        $allEntries = \App\Models\JournalEntry::query()
                            ->when(request('start_date'), function($query) {
                                $query->where('entry_date', '>=', request('start_date'));
                            })
                            ->when(request('end_date'), function($query) {
                                $query->where('entry_date', '<=', request('end_date'));
                            })
                            ->when(request('entity'), function($query) {
                                $query->where('entity', 'like', '%' . request('entity') . '%');
                            })
                            ->when(request('reference'), function($query) {
                                $query->where('reference', 'like', '%' . request('reference') . '%');
                            })
                            ->when(request('paid_through'), function($query) {
                                $query->where('paid_through', request('paid_through'));
                            })
                            ->orderBy('entry_date', 'asc')
                            ->orderBy('created_at', 'asc')
                            ->get();
                        
                        // Group entries by transaction_id (fallback to reference or unique date-id)
$groupedEntries = [];
foreach($allEntries as $entry) {
    $key = $entry->transaction_id ?: ($entry->reference ?: ($entry->entry_date . '-' . $entry->id));
    if (!isset($groupedEntries[$key])) {
        $groupedEntries[$key] = [];
    }
    $groupedEntries[$key][] = $entry;
}
$groupedEntries[$key][] = $entry;
                        
                        
                        // Sort groups by date (oldest first for chronological order)
                        uasort($groupedEntries, function($a, $b) {
                            return strcmp($a[0]->entry_date, $b[0]->entry_date);
                        });
                        
                        // Paginate the grouped entries
                        $currentBookPage = request()->get('book_page', 1);
                        $perPage = 10;
                        $offset = ($currentBookPage - 1) * $perPage;
                        $paginatedGroups = array_slice($groupedEntries, $offset, $perPage, true);
                        $totalGroups = count($groupedEntries);
                        $lastBookPage = ceil($totalGroups / $perPage);
                    @endphp
                    
                    @forelse($paginatedGroups as $transactionRef => $entries)
                        @php 
                            $date = \Carbon\Carbon::parse($entries[0]->entry_date)->format('d-M-Y'); 
                            $totalDebit = 0;
                            $totalCredit = 0;
                            
                            // Determine transaction type based on accounts
                            $accounts = array_map(function($entry) { return $entry->account; }, $entries);
                            $transactionType = 'General';
                            
                            // Check for specific transaction patterns
                            if (in_array('Purchase', $accounts) && in_array('Supplier', $accounts)) {
                                $transactionType = 'Purchase – Credit';
                            } elseif (in_array('Purchase', $accounts) && in_array('Cash', $accounts)) {
                                $transactionType = 'Purchase – Cash';
                            } elseif (in_array('Purchase', $accounts) && in_array('Bank', $accounts)) {
                                $transactionType = 'Purchase – Bank';
                            } elseif (in_array('Cash', $accounts) && in_array('Sales', $accounts)) {
                                $transactionType = 'Sale – Cash';
                            } elseif (in_array('Customer', $accounts) && in_array('Sales', $accounts)) {
                                $transactionType = 'Sale – Credit';
                            } elseif (in_array('Bank', $accounts) && in_array('Sales', $accounts)) {
                                $transactionType = 'Sale – Bank';
                            }
                            // Add more conditions for other transaction types as needed
                        @endphp
                        
                        <div class="transaction-header">
                            <span class="transaction-type-badge transaction-{{ strtolower($transactionType) }}">{{ $transactionType }}</span>
                            @if($transactionRef != $entries[0]->entry_date) - Ref: {{ $transactionRef }} @endif
                        </div>
                        
                        <table class="journal-book-table">
                            <thead>
                                <tr>
                                    <th class="date-col">Date</th>
                                    <th class="particulars-col">Particulars</th>
                                    <th class="amount-col">Debit ({{ $currency }})</th>
                                    <th class="amount-col">Credit ({{ $currency }})</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="date-col">{{ $date }}</td>
                                    <td class="particulars-col">
                                        <div class="journal-entry-particulars">
                                            @foreach($entries as $entry)
                                                @if($entry->debit > 0)
                                                    <div class="debit-line">{{ $entry->account }} A/c Dr.</div>
                                                    @php $totalDebit += $entry->debit; @endphp
                                                @endif
                                            @endforeach
                                            @foreach($entries as $entry)
                                                @if($entry->credit > 0)
                                                    <div class="credit-line">To {{ $entry->account }} A/c</div>
                                                    @php $totalCredit += $entry->credit; @endphp
                                                @endif
                                            @endforeach
                                            <div class="journal-entry-narration">
                                                ({{ optional($entries[0]->transaction)->narration ?? ($entries[0]->description ?: 'Being transaction recorded') }})
                                            </div>
                                        </div>
                                    </td>
                                    <td class="amount-col">
                                        @foreach($entries as $entry)
                                            @if($entry->debit > 0)
                                                {{ number_format($entry->debit, 2) }}<br>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="amount-col">
                                        @foreach($entries as $entry)
                                            @if($entry->credit > 0)
                                                {{ number_format($entry->credit, 2) }}<br>
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @empty
                        <div class="alert alert-info">
                            No journal entries found.
                        </div>
                    @endforelse
                    
                    <!-- Running Balance -->
                    <table class="journal-book-table">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-center">Running Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="running-balance">
                                <td class="text-right">Total Debit:</td>
                                <td class="text-right">{{ $currency }} {{ number_format($totalDebit, 2) }}</td>
                            </tr>
                            <tr class="running-balance">
                                <td class="text-right">Total Credit:</td>
                                <td class="text-right">{{ $currency }} {{ number_format($totalCredit, 2) }}</td>
                            </tr>
                            <tr class="running-balance">
                                <td class="text-right">Balance:</td>
                                <td class="text-right">{{ $currency }} {{ number_format(abs($totalDebit - $totalCredit), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- Journal Book Pagination -->
                    <div class="pagination-container">
                        <button onclick="goToBookPage(1)" class="page-btn {{ $currentBookPage == 1 ? 'disabled' : '' }}" title="First Page">
                            &laquo;&laquo;
                        </button>
                        <button onclick="goToBookPage({{ $currentBookPage - 1 }})" class="page-btn {{ $currentBookPage == 1 ? 'disabled' : '' }}" title="Previous Page">
                            &laquo;
                        </button>
                        
                        <div class="page-info">
                            <span>Page</span>
                            <input type="number" class="page-input-box" id="bookPageInput" 
                                   min="1" max="{{ $lastBookPage }}" 
                                   value="{{ $currentBookPage }}">
                            <span>of {{ $lastBookPage }}</span>
                            <button class="go-to-page" onclick="goToBookPage()">Go</button>
                        </div>
                        
                        <button onclick="goToBookPage({{ $currentBookPage + 1 }})" class="page-btn {{ $currentBookPage == $lastBookPage ? 'disabled' : '' }}" title="Next Page">
                            &raquo;
                        </button>
                        <button onclick="goToBookPage({{ $lastBookPage }})" class="page-btn {{ $currentBookPage == $lastBookPage ? 'disabled' : '' }}" title="Last Page">
                            &raquo;&raquo;
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Initialize row index
    let rowIndex = {{ old('entries') ? count(old('entries')) : 1 }};

    // Function to toggle "Other" input fields
    function toggleEntityOther(selectElem) {
        const row = $(selectElem).closest('tr');
        const otherInput = row.find('.entity-other-input');
        if (selectElem.value === 'Other') {
            otherInput.show().prop('required', true);
        } else {
            otherInput.hide().prop('required', false).val('');
        }
    }

    function toggleAccountOther(selectElem) {
        const row = $(selectElem).closest('tr');
        const otherInput = row.find('.account-other-input');
        if (selectElem.value === 'Other') {
            otherInput.show().prop('required', true);
        } else {
            otherInput.hide().prop('required', false).val('');
        }
    }

    // Function to update row total and prevent both debit and credit
    function updateRowTotal(input) {
        const row = $(input).closest('tr');
        const debitInput = row.find('input[name*="[debit]"]');
        const creditInput = row.find('input[name*="[credit]"]');
        
        if ($(input).attr('name').includes('debit') && $(input).val() !== '') {
            creditInput.val('').prop('required', false);
        } 
        else if ($(input).attr('name').includes('credit') && $(input).val() !== '') {
            debitInput.val('').prop('required', false);
        }
        
        calculateTotals();
    }

    // Initialize any existing "Other" fields on page load
    $(document).ready(function() {
        $('select[name*="[entity]"]').each(function() {
            toggleEntityOther(this);
        });
        $('select[name*="[account]"]').each(function() {
            toggleAccountOther(this);
        });
        
        // Calculate initial totals
        calculateTotals();
        
        // Set up validation button
        $('#validateEntries').click(function() {
            validateJournalEntries();
        });
        
        // Prevent submit unless client-side validation passes
        $('#journalForm').on('submit', function(e){
            if (!validateJournalEntries()) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
        
        // Handle tab changes to update the hidden field
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('href');
            if (target === '#listing') {
                $('#activeTab').val('listing');
            } else if (target === '#book') {
                $('#activeTab').val('book');
            } else if (target === '#manual') {
                $('#activeTab').val('manual');
            }
        });
        
        // Activate the correct tab based on URL parameter
        const activeTab = getParameterByName('tab') || 'manual';
        if (activeTab === 'listing') {
            $('.nav-tabs a[href="#listing"]').tab('show');
        } else if (activeTab === 'book') {
            $('.nav-tabs a[href="#book"]').tab('show');
        } else {
            $('.nav-tabs a[href="#manual"]').tab('show');
        }
    });

    // Helper function to get URL parameters
    function getParameterByName(name, url = window.location.href) {
        name = name.replace(/[\[\]]/g, '\\$&');
        const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

    // Create a new row with the given index
    function createNewRow(index) {
        return `
            <tr>
                <td class="td-relative">
                    <input type="date" name="entries[${index}][entry_date]" required class="form-control" value="{{ date('Y-m-d') }}">
                </td>
                <td class="td-relative">
                    <select name="entries[${index}][entity]" class="form-control" onchange="toggleEntityOther(this)">
                        <option value="">-- Select Entity --</option>
                        @foreach ($entities as $entity)
                            <option value="{{ $entity }}">{{ $entity }}</option>
                        @endforeach
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" name="entries[${index}][entity_other]" class="form-control entity-other-input" placeholder="Enter Entity">
                </td>
                <td class="td-relative">
                    <select name="entries[${index}][account]" class="form-control" required onchange="toggleAccountOther(this)">
                        <option value="">-- Select Account --</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account }}">{{ $account }}</option>
                        @endforeach
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" name="entries[${index}][account_other]" class="form-control account-other-input" placeholder="Enter Account">
                </td>
                <td><input type="text" name="entries[${index}][description]" class="form-control"></td>
                <td>
                    <select name="entries[${index}][paid_through]" class="form-control">
                        <option value="">-- Select --</option>
                        <option value="Bank">Bank</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Card">Card</option>
                    </select>
                </td>
                <td><input type="text" name="entries[${index}][reference]" class="form-control"></td>
                <td><input type="number" name="entries[${index}][debit]" step="0.01" class="form-control amount-input" oninput="updateRowTotal(this)" placeholder="0.00"></td>
                <td><input type="number" name="entries[${index}][credit]" step="0.01" class="form-control amount-input" oninput="updateRowTotal(this)" placeholder="0.00"></td>
                <td>
                    <div class="action-buttons">
                        <button type="button" class="btn btn-success btn-sm btn-action add-row">+</button>
                    </div>
                </td>
            </tr>
        `;
    }

    // Fixed row addition functionality
    $(document).on('click', '.add-row', function() {
        const newRow = createNewRow(rowIndex);
        $('#journal-entry-body').append(newRow);
        $(this).removeClass('add-row btn-success').addClass('remove-row btn-danger').html('×');
        rowIndex++;
    });

    // Remove row functionality
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    // Calculate totals for all rows
    function calculateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;
        
        $('input[name*="[debit]"]').each(function() {
            totalDebit += parseFloat($(this).val() || 0);
        });
        
        $('input[name*="[credit]"]').each(function() {
            totalCredit += parseFloat($(this).val() || 0);
        });
        
        $('#debit-total').text(totalDebit.toFixed(2));
        $('#credit-total').text(totalCredit.toFixed(2));
        
        // Update summary
        $('#summary-debit').text(totalDebit.toFixed(2));
        $('#summary-credit').text(totalCredit.toFixed(2));
        
        const balance = totalDebit - totalCredit;
        $('#summary-balance').text(Math.abs(balance).toFixed(2));
        
        // Update balance indicator
        const balanceIndicator = $('#balance-indicator');
        const summaryStatus = $('#summary-status');
        
        if (balance === 0) {
            balanceIndicator.html('<div class="balance-indicator balance-valid">Journal entries are balanced</div>');
            summaryStatus.html('<span style="color: #28a745;">Balanced</span>');
            $('#summary-balance').removeClass('summary-unbalanced').addClass('summary-balance');
        } else {
            balanceIndicator.html(`<div class="balance-indicator balance-invalid">Out of Balance by: ${Math.abs(balance).toFixed(2)} (${balance > 0 ? 'Debit' : 'Credit'})</div>`);
            summaryStatus.html('<span style="color: #dc3545;">Unbalanced</span>');
            $('#summary-balance').removeClass('summary-balance').addClass('summary-unbalanced');
        }
        
        toggleSaveButton();
        return balance;
    }

    // Validate journal entries before submission
    function validateJournalEntries() {
        let isValid = true;
        const balance = calculateTotals();
        let errorMessages = [];
        
        // Check if the journal entries are balanced
        if (balance !== 0) {
            errorMessages.push('Journal entries are not balanced. Debits must equal credits.');
            isValid = false;
        }
        
        // Validate each row
        $('tr', '#journal-entry-body').each(function(index) {
            const debit = parseFloat($(this).find('input[name*="[debit]"]').val() || 0);
            const credit = parseFloat($(this).find('input[name*="[credit]"]').val() || 0);
            const account = $(this).find('select[name*="[account]"]').val();
            const entity = $(this).find('select[name*="[entity]"]').val();
            const entryDate = $(this).find('input[name*="[entry_date]"]').val();
            
            // Check if both debit and credit are filled
            if (debit > 0 && credit > 0) {
                errorMessages.push(`Row ${index + 1}: You cannot have both debit and credit amounts. Please enter only one.`);
                isValid = false;
            }
            
            // Check if neither debit nor credit is filled
            if (debit === 0 && credit === 0) {
                errorMessages.push(`Row ${index + 1}: You must enter either a debit or credit amount.`);
                isValid = false;
            }
            
            // Check if account is selected
            if (!account) {
                errorMessages.push(`Row ${index + 1}: Please select an account.`);
                isValid = false;
            }
            
            
            // If account == Other then require the text input
            const accountOtherInput = $(this).find('.account-other-input:visible').val();
            if (account === 'Other' && (!accountOtherInput || !accountOtherInput.trim())) {
                errorMessages.push(`Row ${index + 1}: Please enter the Account name for "Other".`);
                isValid = false;
            }

            // If entity == Other then require the text input
            const entityOtherInput = $(this).find('.entity-other-input:visible').val();
            if (entity === 'Other' && (!entityOtherInput || !entityOtherInput.trim())) {
                errorMessages.push(`Row ${index + 1}: Please enter the Entity name for "Other".`);
                isValid = false;
            }
    // Check if entity is selected
            if (!entity) {
                errorMessages.push(`Row ${index + 1}: Please select an entity.`);
                isValid = false;
            }
            
            // Check if date is entered
            if (!entryDate) {
                errorMessages.push(`Row ${index + 1}: Please enter a date.`);
                isValid = false;
            }
        });
        
        if (errorMessages.length > 0) {
            alert('Please fix the following errors:\n\n' + errorMessages.join('\n'));
        } else if (isValid) {
            alert('All journal entries are valid and balanced. Ready to save.');
        }
        
        return isValid;
    }

    // Set up form submission handler
    $('#journalForm').submit(function(e) {
        if (!validateJournalEntries()) {
            e.preventDefault();
            return false;
        }
        return true;
    });

    // Pagination functions for listing view
    function goToPage(page = null) {
        if (!page) {
            page = parseInt($('#pageInput').val());
            if (isNaN(page)) return;
            page = Math.max(1, Math.min(page, {{ $journalEntries->lastPage() }}));
        }
        
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        url.searchParams.set('tab', 'listing');
        window.location.href = url.toString();
    }

    // Pagination functions for journal book
    function goToBookPage(page = null) {
        if (!page) {
            page = parseInt($('#bookPageInput').val());
            if (isNaN(page)) return;
            page = Math.max(1, Math.min(page, {{ $lastBookPage ?? 1 }}));
        }
        
        const url = new URL(window.location.href);
        url.searchParams.set('book_page', page);
        url.searchParams.set('tab', 'book');
        window.location.href = url.toString();
    }

    // Handle Enter key in page inputs
    $('#pageInput').on('keypress', function(e) {
        if (e.which === 13) {
            goToPage();
        }
    });

    $('#bookPageInput').on('keypress', function(e) {
        if (e.which === 13) {
            goToBookPage();
        }
    });
    // Enable Save only if balanced and all required fields are valid
    function isFormFilledAndBalanced() {
        const balance = calculateTotals();
        if (balance !== 0) return false;
        let ok = true;
        $('#journal-entry-body tr').each(function(idx){
            const row = $(this);
            const dateVal = row.find('input[name*="[entry_date]"]').val();
            const accountSel = row.find('select[name*="[account]"]').val();
            const accountOther = row.find('.account-other-input:visible').val();
            const entitySel = row.find('select[name*="[entity]"]').val();
            const entityOther = row.find('.entity-other-input:visible').val();
            const debit = parseFloat(row.find('input[name*="[debit]"]').val() || 0);
            const credit = parseFloat(row.find('input[name*="[credit]"]').val() || 0);
            
            // date required
            if (!dateVal) { ok = false; return false; }
            // account required (either select OR visible 'Other' text)
            if (!accountSel) { ok = false; return false; }
            if (accountSel === 'Other' && (!accountOther || !accountOther.trim())) { ok = false; return false; }
            // if entity == Other, text required
            if (entitySel === 'Other' && (!entityOther || !entityOther.trim())) { ok = false; return false; }
            // exactly one of debit/credit > 0
            if (!((debit > 0 && credit === 0) || (credit > 0 && debit === 0))) { ok = false; return false; }
        });
        return ok;
    }

    function toggleSaveButton() {
        const canSave = isFormFilledAndBalanced();
        $('#saveButton').prop('disabled', !canSave);
    }

    </script>
</body>
</html>