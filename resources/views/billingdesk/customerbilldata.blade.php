<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bills</title>
    @include('layouts/usersidebar')
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
        <div style="margin-left:15px;margin-top:15px;">

            @include('navbar.billingdesknavbar')
            </div>
                @else
        <x-logout_nav_user />
    @endif
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Ledger</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bills</li>
            </ol>
        </nav>
        <h2>
            {{ $credit_id }}
        </h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Reciept No</th>
                    <th>Desk User ID</th>
                    <th>Created At</th>
                    <th>Total Amount</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php $number = 1; ?>
                <?php $collectiontotal = 0; ?>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $number }}</td>
                        <td>{{ $transaction->transaction_id }}</td>
                        <td>UID{{ $transaction->user_id }}</td>
                        <td>{{ date('d M Y | h:i:s A', strtotime($transaction->created_at)) }}</td>
                        <td>
                            @if ($transaction->vat_type == 1)
                                @if (!is_null($transaction->grandtotal_without_discount))
                                    <b>{{ $currency }}</b>
                                    {{ number_format($transaction->grandtotal_without_discount - $transaction->discount_amount, 3) }}
                                @else
                                    <b>{{ $currency }}</b>
                                    {{ number_format($transaction->sum * ($transaction->quantity - $transaction->discount_amount), 3) }}
                                @endif
                            @else
                                <b>{{ $currency }}</b> {{ $transaction->sum }}
                            @endif
                        </td>
                        <td> <a class="btn btn-primary"
                                href="/transactiondetails/{{ $transaction->transaction_id }}">VIEW</a></td>
                    </tr>
                    <?php $number++; ?>
                @endforeach
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td>Total Due Amount</td>
                    <td><b>{{ $currency }}</b> {{ $purchase }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td>Total Collection Amount</td>
                    <td><b>{{ $currency }}</b> {{ $paid }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td>Remaining Due</td>
                    <td><b>{{ $currency }}</b> {{ number_format($purchase - $paid, 3) }}</td>
                </tr>
                {{-- <tr>
                    <td colspan="4"></td>
                    <td>Collect Amount</td>
                    <td>
                        <button class="btn btn-primary creditfundbtn btn-sm" onclick="javascript: return false;"
                            value="{{ $credit_id }}" id="add_fund">PAYMENT</button>
                    </td>
                </tr> --}}
            </tbody>
        </table>
    </div>
    </div>
</body>
@include('modal.addfund2')

</html>
<script>
    $(document).ready(function() {
        $(document).on('click', '.creditfundbtn', function() {
            var userid = $(this).val();
            console.log(userid);
            fetchhistory();

            function fetchhistory() {
                $.ajax({
                    type: 'get',
                    url: '/gethistory/' + userid,
                    success: function(data) {
                        var creditid = (data.creditid);
                        console.log(data.creditid);
                        var dueamount = (data.due);
                        console.log(data.due);
                        $('#Addfund').modal('show');
                        $('#fundusername').val(userid);
                        $('#creditid').val(creditid);
                        $('#due').val(dueamount.toFixed(3));
                    }
                });
            }
        });
    });
</script>
