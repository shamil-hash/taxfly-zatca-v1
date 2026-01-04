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
                <a href="/branchdat/{{ $branchid }}" class="btn btn-primary">Sales</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/branchdatpurchase/{{ $branchid }}" class="btn btn-primary">Purchase</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/branchdatpurchasereturn/{{ $branchid }}" class="btn btn-primary">Purchase Return</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/branchdatstock/{{ $branchid }}" class="btn btn-primary active">Stocks</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/branchdatreturn/{{ $branchid }}" class="btn btn-primary">Return</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/branchdatemployee/{{ $branchid }}" class="btn btn-primary">Employee</a>
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
        <form class="formcss" action="/adminstocktransactiondate" method="get">
            <input name="branch" type="hidden" value="{{ $branchid }}">
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
                        <input type="hidden" name="product_id" id="product_id" value="{{ $product_id }}">
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
                    <th width="10%">Products Name</th>
                    <th width="10%">Customer Name</th>
                    <th width="8%">Transaction ID</th>
                    <th width="10%">Created At</th>
                    <th width="8%">Payment Type</th>
                    <th width="5%">Quantity</th>
                    <th width="5%">Selling Cost</th>
                    <th width="5%">Selling Cost <br /> with {{$tax}}</th>
                    <th width="5%">Total Amount<br /> (w/o Discount)</th>
                    <th width="5%">Discount Amount</th>
                    <th width="5%">Total Amount<br /> (w/. Discount)</th>
                    <!--<th width="5%">Buying Cost</th>-->

                </tr>
            </thead>
            @php
                $stocksum = 0;
                $stockamount = 0;
                $discount = 0;
                $stockamount_w_discount = 0;
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
                        <td>
                            <b>{{ $currency }}</b> {{ $stock->total_amount }}
                        </td>
                        {{-- <!--<td><b>{{ $currency }}</b> {{ $stock->one_pro_buycost }}</td>--> --}}
                        @php
                            $stocksum = $stocksum + $stock->quantity;
                            $stockamount +=
                                $stock->totalamount_wo_discount != null
                                    ? $stock->totalamount_wo_discount
                                    : $stock->total_amount;
                            $discount += $stock->discount_amount;
                            $stockamount_w_discount += $stock->total_amount;
                        @endphp
                @endforeach
                </tr>
            </tbody>

        </table>

        <table id="example2" class="table table-striped table-bordered" style="width:100%">
            <tr>
                <td colspan="2">Quantity</td>
                <td>{{ $stocksum }}</td>
            </tr>
            <tr>
                <td colspan="2">Total Amount w/o Discount</td>
                <td><b>{{ $currency }}</b> {{ $stockamount }}</td>
            </tr>
            <tr>
                <td colspan="2">Discount Amount</td>
                <td><b>{{ $currency }}</b> {{ $discount }}</td>
            </tr>
            <tr>
                <td colspan="2">Total Amount w/. Discount</td>
                <td><b>{{ $currency }}</b> {{ $stockamount_w_discount }}</td>
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
