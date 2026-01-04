<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Supplier Cash Statement</title>
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
        <x-logout_nav_user />
    @endif
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Ledger</a></li>
                <li class="breadcrumb-item active" aria-current="page">Transactions</li>
            </ol>
        </nav>

        <div class="btn-group btn-group-justified" role="group" aria-label="...">
            <div class="btn-group" role="group">
                <a href="/suppliercredit_trans_history/{{ $cash_supplier_id }} }}" class="btn btn-primary">Credit
                    Statement</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/suppliercash_trans_history/{{ $cash_supplier_id }} }}" class="btn btn-primary active">Cash
                    Statement</a>
            </div>
        </div>
        <br />

        <form action="{{ url('/suppliercash_trans_history/' . $cash_supplier_id) }}" method="GET" onsubmit="return validateDates()">
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
                    <th>Desk User ID</th>
                </tr>
            </thead>
            <tbody>
                <?php $number = 1; ?>
                <?php $collectiontotal = 0; ?>
                @foreach ($purchasedata as $purchasedat)
                    <tr>
                        <td>{{ $number }}</td>
                        <td>
                            {{ date('d M Y | h:i:s A', strtotime($purchasedat->created_at)) }}
                            @if ($purchasedat->receipt_date != '')
                                <br /><br />
                                Invoice Date: <br />{{ $purchasedat->receipt_date }}
                            @endif
                        </td>
                        <td>{{ $purchasedat->comment }}</td>
                        <td>

                            @if ($purchasedat->comment == 'Bill')
                                <b>Invoice No.:</b> {{ $purchasedat->reciept_no }} <br />
                            @elseif ($purchasedat->comment == 'Payment Made')
                                @if ($purchasedat->reciept_no != '')
                                    {{ $currency }} {{ $purchasedat->collected_amount }} for Payment of
                                    {{ $purchasedat->reciept_no }} <br /><br />
                                @endif

                                @if ($purchasedat->payment_type == 1)
                                    Payment - CASH <br />
                                @elseif ($purchasedat->payment_type == 2)
                                    Payment - CHECK <br /> <br />

                                    @if ($purchasedat->check_number != '')
                                        Check Number - {{ $purchasedat->check_number }} <br /><br />
                                    @endif

                                    @if ($purchasedat->depositing_date != '')
                                        Depositing Date - {{ date('d M Y', strtotime($purchasedat->depositing_date)) }}
                                        <br />
                                    @endif

                                    @if ($purchasedat->bank_name != '')
                                        Bank - {{ $purchasedat->bank_name }} <br />
                                    @endif

                                    @if ($purchasedat->reference_number != '')
                                        Reference No. - {{ $purchasedat->reference_number }}
                                    @endif
                                @endif
                            @elseif ($purchasedat->comment == 'Purchase Returned')
                                @if ($purchasedat->reciept_no != '')
                                    <b>Invoice No.:</b> {{ $purchasedat->reciept_no }}
                                @endif
                            @elseif ($purchasedat->comment == '')
                                @if ($purchasedat->reciept_no != '')
                                    <b>Invoice No.:</b> {{ $purchasedat->reciept_no }} <br />
                                @endif

                                @if ($purchasedat->payment_type == 1)
                                    Payment - CASH <br />
                                @elseif ($purchasedat->payment_type == 2)
                                    Payment - CHECK <br /> <br />

                                    @if ($purchasedat->check_number != '')
                                        Check Number - {{ $purchasedat->check_number }} <br /><br />
                                    @endif

                                    @if ($purchasedat->depositing_date != '')
                                        Depositing Date - {{ date('d M Y', strtotime($purchasedat->depositing_date)) }}
                                        <br />
                                    @endif

                                    @if ($purchasedat->bank_name != '')
                                        Bank - {{ $purchasedat->bank_name }} <br />
                                    @endif

                                    @if ($purchasedat->reference_number != '')
                                        Reference No. - {{ $purchasedat->reference_number }}
                                    @endif
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($purchasedat->collected_amount != null || $purchasedat->collected_amount != 0)
                                <b>{{ $currency }}</b> {{ $purchasedat->collected_amount }}
                            @endif
                        </td>
                        <td>UID{{ $purchasedat->user_id }}</td>
                    </tr>
                    <?php $number++; ?>
                    <?php $collectiontotal = $collectiontotal + $purchasedat->collected_amount; ?>
                @endforeach
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td>Total Invoice Amount</td>
                    <td colspan="2"><b>{{ $currency }}</b> {{ $updated_balance }}</td>
                </tr>
            </tbody>
        </table>
        {{ $purchasedata->links() }}

        <div class="row">
            <div class="col-sm-8"></div>
            <div class="col-sm-2">

                <div align="right" class="print-button">
                    @if ($purchasedata->isEmpty())
                        <button class="btn btn-primary" disabled>PDF</button>
                    @else
                    <a href="{{ route('supplier_cash.statement.pdf', ['supplier_cash_id' => $cash_supplier_id, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
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

