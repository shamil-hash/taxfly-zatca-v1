<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>User Sales - Products</title>
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
                <a href="/userreport/{{ $uid }}" class="btn btn-primary active">Sales</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchase/{{ $uid }}" class="btn btn-primary">Purchase</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchasereturn/{{ $uid }}" class="btn btn-primary">Purchase Return</a>
            </div>
            <!--<div class="btn-group" role="group">-->
            <!--    <a href="/userstock/{{ $uid }}" class="btn btn-primary">Stocks</a>-->
            <!--</div>-->
            <div class="btn-group" role="group">
                <a href="/userreturn/{{ $uid }}" class="btn btn-primary">Return</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/daily_report/{{ $uid }}" class="btn btn-primary">Daily Report</a>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Reports</a></li>
                <li class="breadcrumb-item"><a href="/userreport/{{ $uid }}">Sales</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sales Details</li>
            </ol>
        </nav>
        Sales DATA
        <h2>Items</h2>

        <table id="example" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Total Price</th>
                    <th>Discount Amount</th>
                    <th>{{$tax}}</th>
                    <th>Grand Total w/. {{$tax}}<br>(w/. Discount)</th>
                    <th>Credit Note</th>
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

                            <b>{{ $currency }}</b> {{ $product->totalamount_wo_discount - $product->vat_amount}}
                            @elseif ($product->totalamount_wo_discount == '')

                            <b>{{ $currency }}</b> {{ $product->total_amount + $product->vat_amount}}
                            @endif

                        </td>
                        <td>
                            @if ($product->discount_amount != '')
                            <b>{{ $currency }}</b> {{number_format( $product->discount_amount * $product->quantity,3)}}
                            @else
                            <b>{{ $currency }}</b>&nbsp;0
                            @endif
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $product->vat_amount }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $product->total_amount }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $product->credit_note_amount }}
                        </td>
                        @endforeach
                    </tr>
                </tbody>
        </table>
    </div>
</body>

</html>

<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            order: []
        });
    });
</script>
