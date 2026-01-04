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
        <!--<div class="btn-group btn-group-justified" role="group" aria-label="...">-->
        <!--    <div class="btn-group" role="group">-->
        <!--        <a href="/branchdat/{{ $branchid }}" class="btn btn-primary active">Sales</a>-->
        <!--    </div>-->
        <!--    <div class="btn-group" role="group">-->
        <!--        <a href="/branchdatpurchase/{{ $branchid }}" class="btn btn-primary">Purchase</a>-->
        <!--    </div>-->
        <!--    <div class="btn-group" role="group">-->
        <!--        <a href="/branchdatpurchasereturn/{{ $branchid }}" class="btn btn-primary">Purchase Return</a>-->
        <!--    </div>-->
        <!--    <div class="btn-group" role="group">-->
        <!--        <a href="/branchdatstock/{{ $branchid }}" class="btn btn-primary">Stocks</a>-->
        <!--    </div>-->
        <!--    <div class="btn-group" role="group">-->
        <!--        <a href="/branchdatreturn/{{ $branchid }}" class="btn btn-primary">Return</a>-->
        <!--    </div>-->
        <!--    <div class="btn-group" role="group">-->
        <!--        <a href="/branchdatemployee/{{ $branchid }}" class="btn btn-primary">Employee</a>-->
        <!--    </div>-->
        <!--</div>-->
        <h1><b>{{ $branchname }} {{$transaction_id}}</b></h1>
        <span class="alignspan">Sales DATA</span>
        <h2>Items</h2>

        <table class="table">
            <thead>
                <tr>
                    <th width="10%">Product Name</th>
                    <th width="5%">Quantity</th>
                    <th width="5%">Date</th>
                    <th width="5%">Time</th>
                    <th width="5%">Total Price</th>
                    <th width="5%">Discount Amount</th>
                    <th width="5%">Grand Total w/. {{$tax}}<br>(w/. Discount)</th>
                    <th width="5%">{{$tax}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>
                            {{ $product->product_name }}
                        </td>
                        <td>
                            {{ $product->quantity }}
                        </td>
                        <td>
                            {{ date_format($product->created_at, 'D M Y') }}
                        </td>
                        <td>
                            {{ date('H:i:s', strtotime($product->created_at)) }}
                        </td>

                        <td>
                            @if ($product->totalamount_wo_discount != '')
                                <b>{{ $currency }}</b> {{ $product->totalamount_wo_discount }}
                            @elseif ($product->totalamount_wo_discount == '')
                                <b>{{ $currency }}</b> {{ $product->total_amount }}
                            @endif
                        </td>
                        <td>
                            @if ($product->discount_amount != '')
                                <b>{{ $currency }}</b>  {{number_format( $product->discount_amount *  $product->quantity,3 )}}
                            @endif
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $product->total_amount }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $product->vat_amount }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $products->links() }}

    </div>

</body>

</html>
