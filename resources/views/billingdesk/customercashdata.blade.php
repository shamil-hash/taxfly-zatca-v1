<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Cash Statement</title>
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
        @if ($adminroles->contains('module_id', '30'))
        <div style="margin-top:15px;margin-left:15px;">

            @include('navbar.billingdesknavbar')
            </div>
                @else
                <nav class="navbar navbar-default">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <button type="button" id="sidebarCollapse" class="btn navbar-btn">
                                <i class="glyphicon glyphicon-chevron-left"></i>
                                <span></span>
                            </button>
                        </div>
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav navbar-right">
                                @php
                                    $logoutUrl = Session('adminuser')
                                        ? '/adminlogout'
                                        : (Session('softwareuser')
                                            ? '/userlogout'
                                            : '/');
                                @endphp
                                <li><a href="{{ $logoutUrl }}">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
        @endif

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Ledger</a></li>
                <li class="breadcrumb-item active" aria-current="page">Transactions</li>
            </ol>
        </nav>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
            <div class="btn-group" role="group">
                <a href="/credittransactionshistory/{{ $cash_customer_id }}" class="btn btn-primary">Credit
                    Statement</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/cash_statement_transactions/{{ $cash_customer_id }} }}" class="btn btn-primary active">Cash
                    Statement</a>
            </div>
        </div>
        <br />
        <form action="{{ url('/cash_statement_transactions/' . $cash_customer_id) }}" method="GET" onsubmit="return validateDates()">
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
        <table class="table">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Date</th>
                    <th>Transaction Type</th>
                    <th>Details</th>
                    <th>Payments</th>
                    <!-- {{-- <th>Final Balance</th> --}} -->
                    <th>Desk User ID</th>
                </tr>
            </thead>
            <tbody>
                <?php $number = 1; ?>
                <?php $collectiontotal = 0; ?>
                @foreach ($salesdata as $salesdat)
                    <tr>
                        <td>{{ $number }}</td>
                        <td>
                            {{ date('d M Y | h:i:s A', strtotime($salesdat->created_at)) }}

                            @if ($salesdat->transaction_id != '')
                                <br /><br />
                                Invoice Date: <br />{{ $salesdat->transaction_date }}
                            @endif

                        </td>
                        <td>{{ $salesdat->comment }}</td>
                        <td>
                            @if ($salesdat->comment == 'Payment Received')
                                @if ($salesdat->transaction_id != '')
                                    {{ $currency }} {{ $salesdat->collected_amount }} <br /> for payment of
                                    {{ $salesdat->transaction_id }}
                                @else
                                    {{ $currency }} {{ $salesdat->collected_amount }} paid.
                                @endif

                                @if ($salesdat->payment_type == 1)
                                    Payment - CASH <br />
                                @elseif ($salesdat->payment_type == 2)
                                    Payment - BANK <br /> <br />
                                @elseif ($salesdat->payment_type == 4)
                                    Payment - POSCARD <br /> <br />
                                @endif
                            @elseif ($salesdat->comment == 'Invoice')
                                @if ($salesdat->transaction_id != '')
                                    Transaction: <b>{{ $salesdat->transaction_id }} </b><br />
                                @endif
                            @elseif ($salesdat->comment == 'Product Returned')
                                @if ($salesdat->transaction_id != '')
                                    {{ $currency }} {{ $salesdat->collected_amount }} <br /> returned on
                                    transaction
                                    {{ $salesdat->transaction_id }} <br /> <br />
                                @else
                                    {{ $currency }} {{ $salesdat->collected_amount }} returned. <br /> <br />
                                @endif
                            @elseif ($salesdat->comment == 'Credit Note')
                                @if ($salesdat->transaction_id != '')
                                    {{ $currency }} {{ $salesdat->collected_amount }} <br /> credit note on
                                    transaction
                                    {{ $salesdat->transaction_id }} <br /> <br />
                                @else
                                    {{ $currency }} {{ $salesdat->collected_amount }} credit note. <br /> <br />
                                @endif
                            @elseif ($salesdat->comment == '')
                                @if ($salesdat->transaction_id != '')
                                    Transaction {{ $salesdat->transaction_id }} <br />
                                @endif

                                @if ($salesdat->payment_type == 1)
                                    Payment - CASH <br />
                                @elseif ($salesdat->payment_type == 2)
                                    Payment - BANK <br /> <br />
                                @elseif ($salesdat->payment_type == 4)
                                    Payment - POSCARD <br /> <br />
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($salesdat->collected_amount != null || $salesdat->collected_amount != 0)
                                <b>{{ $currency }}</b> {{ $salesdat->collected_amount }}
                            @endif
                        </td>
                        <!-- {{-- <td><b>{{ $currency }}</b> {{ $salesdat->updated_balance }}</td> --}} -->
                        <td>UID{{ $salesdat->user_id }}</td>
                    </tr>
                    <?php $number++; ?>
                    <?php $collectiontotal = $collectiontotal + $salesdat->updated_balance; ?>
                @endforeach
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td>Total Bill Amount</td>
                    <td><b>{{ $currency }}</b> {{ $updated_balance }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        {{ $salesdata->links() }}

        <div class="row">
            <div class="col-sm-8"></div>
            <div class="col-sm-2">

                <div align="right" class="print-button">
                    @if ($salesdata->isEmpty())
                        <button class="btn btn-primary" disabled>PDF</button>
                    @else
                    <a href="{{ route('cash.statement.pdf', ['customer_id' => $cash_customer_id, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
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

        if (!startDate && endDate) {
            alert("Please select a From date.");
            return false;
        }

        if (startDate && !endDate) {
            alert("Please select a To date.");
            return false;
        }
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);

            if (start > end) {
                alert("The From date cannot be greater than the To date.");
                return false;
            }


        }
        return true; // Allow form submission if both dates are valid or both are empty
    }
</script>
