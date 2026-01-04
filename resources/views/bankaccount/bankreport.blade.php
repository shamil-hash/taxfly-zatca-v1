<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bank Report</title>

    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    @include('layouts/usersidebar')
    <style>

        table {
            border-collapse: collapse;
            width: 100%;
        }
 .btn-primary{
            background-color: #187f6a;
            color: white;
        }
        th,
        td {
            border: 1.5px solid black;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2
        }

        th {
            background-color: #187f6a;
            color: white;
        }

        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 1px solid #010101;
        }

        .content-wrapper {
            padding: 20px;
        }



        .current-balance {
            font-weight: bold;
        }

        .opening-balance,
        .current-balance {
                text-align: right;
                font-weight: bold;
                font-size: 1.2em;
                background-color: #f8f9fa;
        }
    </style>

</head>
@php
use App\Models\Softwareuser;
use Illuminate\Support\Facades\DB;

    $userid = Session('softwareuser');

$adminid = Softwareuser::Where('id', $userid)
    ->pluck('admin_id')
    ->first();
$adminroles = DB::table('adminusers')
->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
->where('user_id', $adminid)
->get();
@endphp
<body>
    <div id="content">
        @if ($adminroles->contains('module_id', '30'))
        <div style="margin-top: 15px;margin-left:15px;">
            @include('navbar.banknav')
        </div>
        @else
            <x-logout_nav_user />
        @endif
        <x-admindetails_user :shopdatas="$shopdatas" />

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Bank</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bank Report</li>
            </ol>
        </nav>
        <div class="content-wrapper">
            <h2 class="text-center">Bank Report</h2>

</div>
<form id="bank-report-form" method="GET" action="{{ route('bankreport.submit') }}" onsubmit="return validateForm();">
    <div class="form-row align-items-end">
        <div class="form-group col-md-2" style="width: 200px;">
            <label for="select_account">Select Account</label>
            <select id="select_account" name="account_name" class="form-control">
                <option value="" selected disabled>Select account</option>
                @foreach ($accounts as $account)
                    @if ($account->status == 1)
                        <option value="{{ $account->id }}"
                            {{ request('account_name') == $account->id ? 'selected' : '' }}>
                            {{ $account->account_name }}
                        </option>
                    @endif
                @endforeach
            </select>
            <input type="hidden" name="bank_id" id="bank_id" value="{{ old('bank_id', request('bank_id')) }}">
        </div>

        <div class="form-group col-md-2" style="width: 200px;">
            <label for="start_date" class="required">Start Date</label>
            <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date', date('Y-m-d')) }}" required>
        </div>

        <div class="form-group col-md-2" style="width: 200px;">
            <label for="end_date" class="required">End Date</label>
            <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date', date('Y-m-d')) }}" required>
        </div>

        <div class="form-group col-md-2" style="width: 200px;">
            <label for="sortTransactionType">Transaction Type</label>
            <select id="sortTransactionType" name="transaction_type" class="form-control">
                <option value="credit&&debit" {{ request('transaction_type') == 'credit&&debit' ? 'selected' : '' }}>All</option>
                <option value="credit" {{ request('transaction_type') == 'credit' ? 'selected' : '' }}>Credit</option>
                <option value="debit" {{ request('transaction_type') == 'debit' ? 'selected' : '' }}>Debit</option>
            </select>
        </div>

        <div class="form-group d-flex align-items-center">
            <button style="margin-top: 22px;" type="submit" id="filter_button" class="btn btn-primary mr-2">Filter</button>
            <button style="margin-top: 22px;margin-left:10px" type="submit" id="download_pdf_button" class="btn btn-success" name="download_pdf" value="1">Download PDF</button>
        </div>
    </div>
</form>






            <div class="report-table">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                    <td class="opening-balance" colspan="8" style="text-align: right; font-size: 16px; padding-right: -100px;">
    Opening Balance: {{$currency}} <span id="opening_balance">{{ number_format($opening_balance, 2) }}</span>
</td>
                    </tr>
                        <tr>
                            <th>Transaction Date</th>
                            <th>Value Date</th>
                            <th>Type</th>
                            <th>Party</th>
                            <th>Description</th>
                            <th>Ref No</th>
                            <th>Amount</th>
                            <th>Dr/Cr</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody id="report_table_body">
    @forelse ($transactions as $transaction)
        <tr>
            <td>{{ $transaction->tnx_date }}</td>
            <td>{{ $transaction->value_date }}</td>
            <td>{{ $transaction->type }}</td>
            <td>{{ $transaction->party }}</td>
            <td>{{ $transaction->description }}</td>
            <td>{{ $transaction->ref_no }}</td>
            <td style="color: {{ strtolower($transaction->dr_cr) == 'credit' ? 'green' : 'red' }};">
                {{$currency}} {{ number_format(abs($transaction->amount), 2) }}
            </td>
            <td style="color: {{ strtolower($transaction->dr_cr) == 'credit' ? 'green' : 'red' }};">
                {{ ucfirst($transaction->dr_cr) }}
            </td>

            <td>{{$currency}} {{ number_format($transaction->balance, 2) }}</td>
        </tr>
    @empty
        <tr><td colspan="9" class="text-center">No transactions found.</td></tr>
    @endforelse
</tbody>
<tfoot>
    <tr>
        <td class="current-balance" colspan="9" style="text-align: right; font-size: 17px;">Closing Balance: {{$currency}} <span>{{ number_format($current_balance, 3) }}</span></td>
    </tr>
    <tr>
        @if($transactionType != 'credit')
        <td class="current-balance" colspan="9" style="text-align: right; font-size: 14px;"> Total Debit: {{$currency}} {{ number_format($totalDebit, 3) }}</td>
        @endif
    </tr>

    <tr>
        @if($transactionType != 'debit')
        <td class="current-balance"  colspan="9" style="text-align: right; font-size: 14px;"> Total Credit: {{$currency}} {{ number_format($totalCredit, 3) }}</td>
        @endif
    </tr>
</tfoot>



</table>

            </div>
        </div>
    </div>

</body>
</html>
<script>
    document.getElementById('select_account').addEventListener('change', function() {
        document.getElementById('bank_id').value = this.value;
    });
</script>

<script>
     function validateForm() {
            var accountSelect = document.getElementById('select_account');
            if (accountSelect.value === "") {
                alert("Please select an account.");
                accountSelect.focus();
                return false;
            }
            return true;
        }

        document.getElementById('select_account').addEventListener('change', function() {
            document.getElementById('bank_id').value = this.value;
        });
</script>
