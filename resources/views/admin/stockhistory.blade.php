<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @if (Session('adminuser'))
    <title>Admin</title>
@elseif(Session('softwareuser'))
<title>Stock History</title>
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
                <li class="breadcrumb-item"><a href="/stock">Stock Report</a></li>
                <li class="breadcrumb-item active" aria-current="page">Stock Details</li>
            </ol>
        </nav>
        <form class="formcss" action="/adminstockdateall" method="get">
            <input name="product_id" type="hidden" value="{{ $product_id }}">
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
                    <th width="10%">Invoice No.</th>
                    <th width="15%">Date and Time</th>
                    <th width="15%">Products Name</th>
                    <th width="8%">Added Stocks</th>
                    <th width="8%">Sold Quantity</th>
                    <th width="8%">Purchase Buycost</th>
                    <th width="8%">Purchase Rate <br />(Buycost with {{$tax}})</th>
                    <th width="10%">Total Sold Buycost Value <br /> (with {{$tax}})</th>
                    <th width="10%">Total Sellingcost value <br /> (with {{$tax}})</th>
                    <th width="5%">Discount Amount</th>
                    <th width="10%">Stock Profit</th>
                    <th width="8%">Stock Sold Bill</th>

                </tr>
            </thead>
            @php
                $soldqaun = 0;
                $tot_buy = 0;
                $tot_sell = 0;
                $tot_disc = 0;
                $tot_pro = 0;
            @endphp
            <tbody>
                @foreach ($stocks as $stock)
                    <tr>
                        <td>{{ $stock->receipt_no }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($stock->created_at)->format('d-M-Y | h:i:s A') }}
                        </td>

                        <td>{{ $stock->product_name }}</td>
                        <td>{{ $stock->remain_main_quantity }}</td>
                        <td>{{ $stock->sold_quantity_total }}</td>
                        <td><b>{{ $currency }}</b> {{ $stock->PBuycost }} </td>
                        <td> <b>{{ $currency }} </b>{{ $stock->PBuycostRate }}</td>
                        <td><b>{{ $currency }}</b> {{ number_format($stock->total_sold_buycost_value, 3) }} </td>
                        <td><b>{{ $currency }}</b>
                            {{ number_format($stock->total_sold_sellingcost_value, 3) }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b>
                            {{ number_format($stock->final_discount_amount, 3) }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b>
                            {{ number_format($stock->total_sold_sellingcost_value - $stock->total_sold_buycost_value, 3) }}

                        </td>
                        <td>
                            <a href="/purchasewise_bills/{{ $stock->purchase_id }}/{{ $stock->product_id }}"
                                class="btn btn-primary">Bills</a>
                        </td>
                        @php
                            $soldqaun = $soldqaun + $stock->sold_quantity_total;
                            $tot_buy = $tot_buy + $stock->total_sold_buycost_value;
                            $tot_sell = $tot_sell + $stock->total_sold_sellingcost_value;
                            $tot_disc = $tot_disc + $stock->final_discount_amount;
                            $tot_pro =
                                $tot_pro + ($stock->total_sold_sellingcost_value - $stock->total_sold_buycost_value);
                        @endphp
                    </tr>
                @endforeach

            </tbody>
            <tr style="font-weight: bold;font-size:16px;">
                <td colspan="4">Total</td>
                <td>{{ number_format($soldqaun, 3) }}</td>
                <td></td>
                <td></td>
                <td><b>{{ $currency }}</b> {{ number_format($tot_buy, 3) }}</td>
                <td><b>{{ $currency }}</b> {{ number_format($tot_sell, 3) }}</td>
                <td><b>{{ $currency }}</b> {{ number_format($tot_disc, 3) }}</td>
                <td><b>{{ $currency }}</b> {{ number_format($tot_pro, 3) }}</td>
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
                [1, 'asc']
            ]
        });
    });
</script>
