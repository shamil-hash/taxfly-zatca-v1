<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @if (Session('adminuser'))
    <title>Admin</title>
@elseif(Session('softwareuser'))
<title>Stock Trans History</title>
@endif
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
          .btn-primary{
            background-color: #187f6a;
            color: white;
        }
                   div.dataTables_wrapper div.dataTables_paginate ul.pagination li a {
            color: #187f6a !important;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.active a,
        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.active a:focus,
        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.active a:hover {
            background-color: #187f6a !important;
            border-color: #187f6a !important;
            color: white !important;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination li a:hover {
            background-color: #187f6a !important;
            border-color: #187f6a !important;
            color: white !important;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.disabled a {
            color: #6c757d !important;
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
       
        <br>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Reports</a></li>
                <li class="breadcrumb-item"><a href="/stock">Stock Reports</a></li>
                <li class="breadcrumb-item active" aria-current="page">Stock Details</li>
            </ol>
        </nav>
        <form class="formcss" action="/adminstocktransdate" method="get">
            <input name="product_id" type="hidden" value="{{ $product_id }}"></input>
            <div class="row">
                <div class="col-sm-6">
                    <h4>SELECT DATES
                    </h4>
                    <div class="row">
                        <div class="col-sm-5">
                            From
                            <input type="date" class="form-control" value="{{ $start_date }}" name="start_date">
                        </div>
                        <div class="col-sm-5">
                            To
                            <input type="date" class="form-control" value="{{ $end_date }}" name="end_date">
                        </div>
                        <div class="col-sm-2">
                            <br>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <br>

        <span class="alignspan">Stock Data</span>
        <h2>Stocks</h2>

        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th width="10%">Products Name</th>
                    <th width="10%">Customer Name</th>
                    <th width="8%">Transaction ID</th>
                    <th width="10%">Created At</th>
                    <th width="8%">Payment Type</th>
                    <th width="5%">Quantity</th>
                    <th width="5%">Selling Cost</th>
                    <th width="5%">Selling Cost <br /> with {{$tax}}</th>
                    <th width="5%">Total Amount</th>
                    <th width="5%">Discount Amount</th>
                    @if (Session('softwareuser'))
                    @foreach ($users as $user)
                    @if ($user->role_id == '28')
                    <th width="5%">Credit Note</th>
                    @endif
                    @endforeach
                    @endif
                    <th width="5%">Total Amount<br /> w/. Discount</th>
                    @if (Session('softwareuser'))
                    @foreach ($users as $user)
                    @if ($user->role_id == '28')
                    <th width="5%">Total <br />(Total Amount - Credit Note)</th>
                    @endif
                    @endforeach
                    @endif
                </tr>
            </thead>
            @php
                $stocksum = 0;
                $stockamount = 0;
                $discount = 0;
                $credit_note=0;
                $stockamount_w_discount = 0;
                $final=0;
            @endphp
            <tbody>
                @foreach ($stocks as $stock)
                    <tr>
                        <td>
                            {{ $stock->product_name }}
                        </td>
                        <td>
                            {{ $stock->customer_name }}
                        </td>
                        <td>
                            {{ $stock->transaction_id }}
                        </td>
                        <td>
                            {{ date('d M Y | h:i:s A', strtotime($stock->created_at)) }}

                        </td>
                        @if ($stock->payment_type == 1)
                            <td>
                                CASH
                            </td>
                        @elseif($stock->payment_type == 2)
                            <td>
                                BANK
                            </td>
                        @elseif($stock->payment_type == 3)
                            <td>
                                CREDIT
                            </td>
                        @elseif($stock->payment_type == 4)
                            <td>
                                POS CARD
                            </td>
                        @else
                            <td>
                            </td>
                        @endif
                        <td>
                            {{ $stock->quantity }}
                        </td>
                        <td><b>{{ $currency }}</b> {{ $stock->mrp }}</td>
                        <td><b>{{ $currency }}</b> {{ $stock->netrate }}</td>
                        <td>
                            @if ($stock->totalamount_wo_discount != '' || $stock->totalamount_wo_discount != null)
                                <b>{{ $currency }}</b> {{ $stock->totalamount_wo_discount }}
                            @elseif ($stock->totalamount_wo_discount != '' || $stock->totalamount_wo_discount == null)
                                <b>{{ $currency }}</b> {{ $stock->total_amount }}
                            @endif
                        </td>
                        <td>
                            @if ($stock->discount_amount != '')
                                <b>{{ $currency }}</b> {{ $stock->discount_amount }}
                            @endif
                        </td>
                        @if (Session('softwareuser'))
                        @foreach ($users as $user)
                        @if ($user->role_id == '28')
                        <td>
                            <b>{{ $currency }}</b> {{ $stock->total_credit_note_amount }}
                        </td>
                        @endif
                        @endforeach
                        @endif
                        <td>
                            <b>{{ $currency }}</b> {{ $stock->total_amount }}
                        </td>
                        @if (Session('softwareuser'))
                        @foreach ($users as $user)
                        @if ($user->role_id == '28')
                        <td>
                            <b>{{ $currency }}</b> {{ number_format($stock->total_amount-$stock->total_credit_note_amount,3) }}
                        </td>
                        @endif
                        @endforeach
                        @endif
                        {{-- <!--<td><b>{{ $currency }}</b> {{ $stock->one_pro_buycost }}</td>--> --}}
                        @php
                            $stocksum = $stocksum + $stock->quantity;
                            $stockamount +=
                                $stock->totalamount_wo_discount != null
                                    ? $stock->totalamount_wo_discount
                                    : $stock->total_amount;
                            $discount += $stock->discount_amount;
                            $credit_note += $stock->total_credit_note_amount;

                            $stockamount_w_discount += $stock->total_amount;

                            $final+=$stock->total_amount-$stock->total_credit_note_amount
                        @endphp
                    </tr>
                @endforeach

            </tbody>

        </table>

        <table id="example2" class="table table-striped table-bordered" style="width:100%">
            <tr>
                <td colspan="2">Quantity</td>
                <td>{{ $stocksum }}</td>
            </tr>
            <tr>
                <td colspan="2">Total Amount</td>
                <td><b>{{ $currency }}</b> {{ $stockamount }}</td>
            </tr>
            <tr>
                <td colspan="2">Discount Amount</td>
                <td><b>{{ $currency }}</b> {{ $discount }}</td>
            </tr>
            @if (Session('softwareuser'))
            @foreach ($users as $user)
            @if ($user->role_id == '28')
            <tr>
                <td colspan="2">Credit Note</td>
                <td><b>{{ $currency }}</b> {{ $credit_note }}</td>
            </tr>
            @endif
            @endforeach
            @endif
            <tr>
                <td colspan="2">Total Amount w/. Discount</td>
                <td><b>{{ $currency }}</b> {{ $stockamount_w_discount }}</td>
            </tr>
            @if (Session('softwareuser'))
            @foreach ($users as $user)
            @if ($user->role_id == '28')
            <tr>
                <td colspan="2">Total (Total Amount - Credit Note)</td>
                <td><b>{{ $currency }}</b> {{ $final }}</td>
            </tr>
            @endif
            @endforeach
            @endif
        </table>

    </div>

</body>

</html>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            order: [
                [3, 'asc']
            ]
        });
    });
</script>
