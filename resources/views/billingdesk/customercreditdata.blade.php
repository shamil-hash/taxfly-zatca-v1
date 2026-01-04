<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Credit Statement</title>
    @if (Session('adminuser'))
        @include('layouts/adminsidebar')
    @elseif(Session('softwareuser'))
        @include('layouts/usersidebar')
    @endif
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
            border: 1px solid black;
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
    <!-- Page Content Holder -->
    <div id="content">
       
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Ledger</a></li>
                <li class="breadcrumb-item active" aria-current="page">Transactions</li>
            </ol>
        </nav>

        <br />

        <form action="{{ url('/credittransactionshistory/' . $credit_id) }}" method="GET" onsubmit="return validateDates()">
            <div class="row">

                <div class="col-sm-7">
                    <h4>SELECT DATES</h4>
                    <div class="row">
                        <div class="col-sm-4">
                            From
                            <input type="date" class="form-control" value="{{ $startDate }}" id="start_date"
                                name="start_date">
                        </div>
                        <div class="col-sm-4">
                            To
                            <input type="date" class="form-control" value="{{ $endDate }}" id="end_date"
                                name="end_date">
                        </div>
                        <div class="col-sm-2">
                            <br />
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </div>

            </div>
        </form>

        <br />
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Date</th>
                    <th>Invoice No.</th>
                    <th>Remark</th>
                    <th>Payment</th>
                    <th>Transaction Type</th>
                    <th>Sale +</th>
                    <th>Payment -</th>
                    <th>Return -</th>
                    <th>Balance <br /> (Closing Amount)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sl = 1;
                    // Sort all transactions by date (assuming created_at exists in both)
                    usort($allTransactions, function($a, $b) {
                        return strtotime($a['created_at']) - strtotime($b['created_at']);
                    });

                    $previousDue = 0;
                    $previousBalance = 0;
                @endphp

                @foreach($allTransactions as $transaction)
                    @php
                        $isCredit = isset($transaction['credituser_id']); // Check if it's a credit transaction
                    @endphp

                    <tr>
                        <td>{{ $sl++ }}</td>

                        <td>{{ date('d-m-Y', strtotime($transaction['created_at'])) }}</td>

                        <td>{{ $transaction['transaction_id'] }}</td>

                        <td>
                            @if($isCredit)
                            {{ $transaction['note'] ?? 'N/A' }}
                            @else
                            N/A
                            @endif
                        </td>

                        <td>
                                @if($isCredit)
                                Credit
                                @else
                                {{ $transaction['payment_type'] == 1 ? 'Cash' : ($transaction['payment_type'] == 2 ? 'Bank' : ($transaction['payment_type'] == 4 ? 'POS CARD' : '')) }}
                                @endif
                            </td>

                            <td>{{ $transaction['comment'] ?? '' }} <br>
                            @if ($transaction['comment'] == 'Payment Received')
                             @if($transaction['payment_type'] == 1 || $transaction['payment_type'] == '')
                                Cash
                            @elseif($transaction['payment_type'] == 2)
                                Cheque
                                @if(isset($transaction['cheque_number'])) - {{ $transaction['cheque_number'] }} @endif
                                @if(isset($transaction['depositing_date'])) - {{ $transaction['depositing_date'] }} @endif
                            @elseif($transaction['payment_type'] == 3)
                                Bank - {{ $transaction['account_name'] ?? '' }}
                                @if(isset($transaction['reference_number'])) - {{ $transaction['reference_number'] }} @endif
                            @endif
                            @endif
                            
                            @if ($transaction['comment'] == 'Payment Cancelled')
                            {{$currency}} {{ $transaction['collected_amount'] }}
                            @endif

                            </td>

                        <td>
                            @if($isCredit)
                            @if ($transaction['comment'] == 'Invoice')
                                @if (!empty($transaction['Invoice_due']))
                                    {{$currency}} {{ $transaction['Invoice_due'] }}
                                @endif
                            @endif
                            @else
                            @if ($transaction['comment'] == 'Invoice')
                                {{$currency}} {{ $transaction['collected_amount'] ?? '' }}
                                @endif
                            @endif
                        </td>

                        <td>
                            @if($isCredit)
                            @if ($transaction['comment'] == 'Payment Received' || $transaction['comment'] == 'Invoice')
                            @if (!empty($transaction['collected_amount']))
                            {{$currency}} {{ $transaction['collected_amount'] }}
                        @endif
                        @endif
                        @else
                        @if ($transaction['comment'] == 'Invoice')
                        {{$currency}} {{ $transaction['collected_amount'] }}
                        @endif
                    @endif
                        </td>

                        <td>
                            @if($isCredit)
                            @if ($transaction['comment'] == 'Returned Product')
                            @if (!empty($transaction['collected_amount']))
                            {{$currency}} {{ $transaction['collected_amount'] }}
                        @endif
                        @endif
                        @else
                        @if ($transaction['comment'] == 'Product Returned')
                        @if (!empty($transaction['collected_amount']))
                        {{$currency}} {{ $transaction['collected_amount'] }}
                    @endif
                    @endif
                    @endif
                        </td>



                        <td>
                            @if($isCredit)
                            {{$currency}} {{ $transaction['updated_balance'] ?? 0 }}
                                @php $previousBalance = $transaction['updated_balance'] ?? 0; @endphp
                            @else
                            {{$currency}} {{ $previousBalance }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row">
            <div class="col-sm-8"></div>
            <div class="col-sm-2">

                <div align="right" class="print-button">
                    @if (empty($allTransactions))
                    <button class="btn btn-primary" disabled>PDF</button>
                    @else
                    <a href="{{ route('credit.statement.pdf', ['credit_id' => $credit_id, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                        class="btn btn-primary">PDF</a>
                    @endif
                </div>
            </div>
        </div>
    </div>


</body>

</html>
<script>
function validateDates() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (startDate && !endDate) {
        alert("Please select the 'To' date.");
        return false;
    }
    if (!startDate && endDate) {
        alert("Please select the 'From' date.");
        return false;
    }
    if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
        alert('Start date cannot be after the end date.');
        return false;
    }
    return true;
}

</script>
