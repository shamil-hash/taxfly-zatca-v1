<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin</title>
    @include('layouts/adminsidebar')

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
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

        .total {
            font-weight: bold;
            background-color: white;
            font-size: large;
        }
    </style>

</head>

<body>
    <!-- Page Content Holder -->
    <div id="content">
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
                        <li><a href="/adminlogout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="btn-group btn-group-justified" role="group" aria-label="...">
            <div class="btn-group" role="group">
                <a href="/userreport/{{$uid}}" class="btn btn-primary ">Sales</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchase/{{$uid}}" class="btn btn-primary">Purchase</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchasereturn/{{$uid}}" class="btn btn-primary">Purchase Return</a>
            </div>
            <!--<div class="btn-group" role="group">-->
            <!--    <a href="/userstock/{{$uid}}" class="btn btn-primary active">Stocks</a>-->
            <!--</div>-->
            <div class="btn-group" role="group">
                <a href="/userreturn/{{$uid}}" class="btn btn-primary">Return</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/daily_report/{{ $uid }}" class="btn btn-primary">Daily Report</a>
            </div>
        </div>
        <br>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Reports</a></li>
                <li class="breadcrumb-item active" aria-current="page">Stocks</li>
            </ol>
        </nav>
        <h2>Stocks</h2>
        <!-- content -->

        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th width="20%">Products Name</th>
                    <th width="5%">Buy Cost</th>
                    <th width="5%">Sell Cost</th>
                    <th width="10%">Total Stocks Quantity</th>
                    <th width="10%">Remaining Stocks Quantity</th>
                    <th width="10%">Sold Stocks Quantity</th>
                    <th width="10%">Total Stock Value</th>
                    <th width="10%">Remaining Stock Value</th>
                    <th width="10%">Sold Stock Value(MRP)</th>
                    <th width="10%">Profit Sum</th>
                    <th width="5%">Stock History</th>
                    <th width="5%">Transaction History</th>
                </tr>
            </thead>
            @php
            $totstock=0;
            $remainstock=0;
            $soldstock=0;
            $totstockamount=0;
            $remainstockamount=0;
            $soldstockamount=0;
            $totprofit=0;
            @endphp
            <tbody>
                @foreach($stocks as $stock)
                <tr>
                        <td>
                            {{ $stock->product_name }}
                        </td>
                        <td>
                            {{ $stock->buy_cost }}
                        </td>
                        <td>
                            {{ $stock->selling_cost }}
                        </td>
                        <td>
                            {{ number_format($stock->product_stock_total, 3) }}
                        </td>
                        <td>
                            {{ number_format($stock->product_stock - $stock->product_stock_num, 3) }}
                            <!-- {{ number_format($stock->remaining_stock, 3) }} -->
                        </td>
                        <td>
                            {{ number_format($stock->product_stock_num, 3) }}
                        </td>
                        <td>
                            <!--{{ number_format($stock->product_stock * $stock->buy_cost, 3) }}-->

                            {{ number_format($stock->product_total_stock_value, 3) }}
                            <!-- total stock value -->
                        </td>
                        <td>
                            {{ number_format(($stock->product_stock - $stock->product_stock_num) * $stock->buy_cost, 3) }} <!-- new remain -->
                            <!-- {{ number_format($stock->remaining_stock * $stock->buy_cost, 3) }} -->

                            <!-- remain stock value -->

                             <!-- ------------- purchase wise buycost----------- -->
                            {{ number_format($stock->product_remain_stock_amount - $stock->sold_buycost_value, 3) }}
                        </td>
                        <td>
                            <!--{{ number_format($stock->product_stock_num * $stock->selling_cost, 3) }} -->

                            <!-- sold stock -->

                            {{ number_format($stock->product_stock_value, 3) }}
                        </td>
                        <td>
                            <!-- {{ number_format($stock->product_stock_num * ($stock->selling_cost - $stock->buy_cost), 3) }} -->

                            <!-- Profit -->

                            <!--{{ number_format($stock->profit_value, 3) }}-->

                            <!-- new profit purchase buycost -->

                            {{ number_format($stock->product_stock_value - $stock->sold_buycost_value, 3) }}
                        </td>
                        <td>
                            <a href="/userstockaddhistory/{{ $uid }}/{{ $stock->id }}"
                                class="btn btn-primary">VIEW</a>
                        </td>
                        <td>
                            <a href="/userstocktransactionhistory/{{ $uid }}/{{ $stock->id }}"
                                class="btn btn-primary">VIEW</a>
                        </td>
                        @php
                            $totstock = $totstock + $stock->product_stock_total;
                            $remainstock = $remainstock + $stock->product_stock - $stock->product_stock_num;
                            $soldstock = $soldstock + $stock->product_stock_num;
                            $totstockamount = $totstockamount + $stock->product_total_stock_value;
                            $remainstockamount = $remainstockamount + ($stock->product_remain_stock_amount - $stock->sold_buycost_value);
                            $soldstockamount = $soldstockamount + $stock->product_stock_value;
                            $totprofit = $totprofit + ($stock->product_stock_value - $stock->sold_buycost_value);
                        @endphp
                    </tr>
                @endforeach
            </tbody>
            <tr>
                <td colspan="3" class="total">Total</td>
                <td class="total">{{number_format($totstock,3)}}</td>
                <td class="total">{{number_format($remainstock,3)}}</td>
                <td class="total">{{number_format($soldstock,3)}}</td>
                <td class="total">{{number_format($totstockamount,3)}}</td>
                <td class="total">{{number_format($remainstockamount,3)}}</td>
                <td class="total">{{number_format($soldstockamount,3)}}</td>
                <td class="total">{{number_format($totprofit,3)}}</td>
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
