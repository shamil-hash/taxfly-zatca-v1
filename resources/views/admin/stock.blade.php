<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @if (Session('adminuser'))
    <title>Admin</title>
@elseif(Session('softwareuser'))
<title>Stock Report</title>
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

        .total {
            font-weight: bold;
            background-color: white;
            /*font-size: medium;*/
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
                <li class="breadcrumb-item active" aria-current="page">Stock Details</li>
            </ol>
        </nav>

        <span class="alignspan">Stock Data</span>
        <h2>Stocks</h2>

        <table id="example" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th width="10%">Products Name</th>
                    <th width="5%">Buy Cost <br /> with {{$tax}} (Rate)</th>
                    <th width="5%">Sell Cost</th>
                    <th width="3%">Total Stocks <br /> Quantity</th>
                    <th width="3%">Remaining Stocks <br /> Quantity</th>
                    <th width="3%">Sold Stocks <br /> Quantity</th>
                    <th width="6%">Total Stock Value</th>
                    <th width="6%">Remaining Stock Value</th>
                    <th width="6%">Sold Stock Value(MRP)</th>
                    <th width="5%">Discount Amount</th>
                    <th width="6%">Profit Sum</th>
                    <th width="3%">Stock History</th>
                    <th width="3%">Transaction History</th>
                </tr>
            </thead>
            @php
                $totstock = 0;
                $remainstock = 0;
                $soldstock = 0;
                $totstockamount = 0;
                $remainstockamount = 0;
                $soldstockamount = 0;
                $totdiscount = 0;
                $totprofit = 0;
            @endphp
            <tbody>
                @foreach ($stocks as $stock)
                    <tr>
                        <td>
                            {{ $stock->product_name }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $stock->rate }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $stock->selling_cost }}
                        </td>
                        <td>
                            {{ number_format($stock->product_stock_total, 3) }}
                        </td>
                        <td>
                            {{ number_format($stock->product_stock - $stock->product_stock_num, 3) }}
                        </td>
                        <td>
                            {{ number_format($stock->product_stock_num, 3) }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ number_format($stock->product_total_stock_value, 3) }}
                            <!-- total stock value -->
                        </td>
                        <td>
                            <!-- ------------- purchase wise buycost----------- -->
                            <b>{{ $currency }}</b>
                            {{ number_format($stock->product_remain_stock_amount - $stock->sold_buycost_value, 3) }}
                        </td>
                        <td>
                            <!-- sold stock -->
                            <b>{{ $currency }}</b> {{ number_format($stock->product_stock_value, 3) }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ number_format($stock->final_discount_amount, 3) }}
                        </td>
                        <td>
                            <!-- new profit purchase buycost -->
                            <b>{{ $currency }}</b>
                            {{ number_format($stock->product_stock_value - $stock->sold_buycost_value, 3) }}
                        </td>
                        <td>
                            <a href="/stockhistory/{{ $stock->id }}" class="btn btn-primary">VIEW</a>
                        </td>
                        <td>
                            <a href="/stocktranshistory/{{ $stock->id }}" class="btn btn-primary">VIEW</a>
                        </td>
                        @php
                            $totstock = $totstock + $stock->product_stock_total;
                            $remainstock = $remainstock + $stock->product_stock - $stock->product_stock_num;
                            $soldstock = $soldstock + $stock->product_stock_num;
                            $totstockamount = $totstockamount + $stock->product_total_stock_value;
                            $remainstockamount =
                                $remainstockamount + ($stock->product_remain_stock_amount - $stock->sold_buycost_value);
                            $soldstockamount = $soldstockamount + $stock->product_stock_value;
                            $totdiscount = $totdiscount + $stock->final_discount_amount;
                            $totprofit = $totprofit + ($stock->product_stock_value - $stock->sold_buycost_value);
                        @endphp
                    </tr>
                @endforeach
            </tbody>
            <tr>
                <td colspan="3" class="total">Total</td>
                <td class="total">{{ number_format($totstock, 3) }}</td>
                <td class="total">{{ number_format($remainstock, 3) }}</td>
                <td class="total">{{ number_format($soldstock, 3) }}</td>
                <td class="total"><b>{{ $currency }}</b> {{ number_format($totstockamount, 3) }}</td>
                <td class="total"><b>{{ $currency }}</b> {{ number_format($remainstockamount, 3) }}</td>
                <td class="total"><b>{{ $currency }}</b> {{ number_format($soldstockamount, 3) }}</td>
                <td class="total"><b>{{ $currency }}</b> {{ number_format($totdiscount, 3) }}</td>
                <td class="total"><b>{{ $currency }}</b> {{ number_format($totprofit, 3) }}</td>
            </tr>
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
                [0, 'asc']
            ]
        });
    });
</script>
