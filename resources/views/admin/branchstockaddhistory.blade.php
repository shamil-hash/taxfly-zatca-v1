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
         .btn-group{
            padding:6px;
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
                <a href="/branchdat/{{ $branchid }}" class="btn btn-primary" style="display: block; padding: 10px 5px; background-color: #187f6a; color: white; text-align: center; text-decoration: none; border: 1px solid #146354; border-radius: 4px; transition: all 0.3s ease;">Sales</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/branchdatpurchase/{{ $branchid }}" class="btn btn-primary" style="display: block; padding: 10px 5px; background-color: #187f6a; color: white; text-align: center; text-decoration: none; border: 1px solid #146354; border-radius: 4px; transition: all 0.3s ease;">Purchase</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/branchdatpurchasereturn/{{ $branchid }}" class="btn btn-primary" style="display: block; padding: 10px 5px; background-color: #187f6a; color: white; text-align: center; text-decoration: none; border: 1px solid #146354; border-radius: 4px; transition: all 0.3s ease;">Purchase Return</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/branchdatstock/{{ $branchid }}" class="btn btn-primary active" style="display: block; padding: 10px 5px; background-color: #115c4c; color: white; text-align: center; text-decoration: none; border: 1px solid #0d4539; border-radius: 4px; font-weight: bold; box-shadow: inset 0 2px 4px rgba(0,0,0,0.15);">Stocks</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/branchdatreturn/{{ $branchid }}" class="btn btn-primary" style="display: block; padding: 10px 5px; background-color: #187f6a; color: white; text-align: center; text-decoration: none; border: 1px solid #146354; border-radius: 4px; transition: all 0.3s ease;">Return</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/branchdatemployee/{{ $branchid }}" class="btn btn-primary" style="display: block; padding: 10px 5px; background-color: #187f6a; color: white; text-align: center; text-decoration: none; border: 1px solid #146354; border-radius: 4px; transition: all 0.3s ease;">User</a>
            </div>
        </div>
        <br>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Reports</a></li>
                <li class="breadcrumb-item"><a href="/branchwisesummary">Branchwise Summary</a></li>
                <li class="breadcrumb-item active" aria-current="page">Stock Details</li>
            </ol>
        </nav>
        <form class="formcss" action="/adminstockadddate" method="get">
            <input name="branch" type="hidden" value="{{ $branchid }}"></input>
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
        <h1><b>{{ $branchname }}</b></h1>
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
                    <th width="10%">Total Sellingcost value</th>
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
                        <td> <b>{{ $currency }} </b>{{ $stock->PBuycost }}</td>
                        <td> <b>{{ $currency }} </b>{{ $stock->PBuycostRate }}</td>
                        <td><b>{{ $currency }} </b> {{ number_format($stock->total_sold_buycost_value, 3) }} </td>
                        <td><b>{{ $currency }} </b>
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
                            <a href="/branch_purchasewise_bills/{{ $stock->purchase_id }}/{{ $stock->product_id }}"
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
