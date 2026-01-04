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
                <a href="/userreport/{{$uid}}" class="btn btn-primary ">Sales</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchase/{{$uid}}" class="btn btn-primary">Purchase</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchasereturn/{{$uid}}" class="btn btn-primary">Purchase Return</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userstock/{{$uid}}" class="btn btn-primary active">Stocks</a>
            </div>
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
                <li class="breadcrumb-item"><a href="/userstock/{{$uid}}">Stocks</a></li>
                <li class="breadcrumb-item active" aria-current="page">Transaction Details</li>
            </ol>
        </nav>
        <br>
        <h2>Stocks</h2>
        <br>
        <form class="formcss" action="/userstocktransactiondate/{{$uid}}/{{$product_id}}" method="get">
            <div class="row">
                <div class="col-sm-6">
                    <h4>SELECT DATES </h4>
                    <div class="row">
                        <div class="col-sm-5">
                            From
                            <input type="date" class="form-control" value="{{$start_date}}" name="start_date">
                        </div>
                        <div class="col-sm-5">
                            To
                            <input type="date" class="form-control" value="{{$end_date}}" name="end_date">
                        </div>
                        <div class="col-sm-2">
                            <br>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                        <input type="hidden" name="product_id" id="product_id" value="{{$product_id}}">
                    </div>
                </div>
            </div>
        </form>

        <table id="example" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th width="20%">Products Name</th>
                    <th width="20%">Customer Name</th>
                    <th width="10%">Transaction ID</th>
                    <th width="20%">Created At</th>
                    <th width="10%">Payment Type</th>
                    <th width="10%">Quantity</th>
                    <th width="10%">Total Amount</th>
                    <th width="5%">Buying Cost</th>
                    <th width="5%">Selling Cost</th>
                </tr>
            </thead>
            @php
            $stocksum=0;
            $stockamount=0;
            @endphp
            <tbody>
                @foreach($stocks as $stock)
                <tr>
                    <td>
                        {{$stock->product_name}}
                    </td>
                    <td>
                        {{$stock->customer_name}}
                    </td>
                    <td>
                        {{$stock->transaction_id}}
                    </td>
                    <td>
                        {{$stock->created_at}}
                    </td>
                    @if($stock->payment_type==1)
                    <td>
                        CASH
                    </td>
                    @elseif($stock->payment_type==2)
                    <td>
                        BANK
                    </td>
                    @elseif($stock->payment_type==3)
                    <td>
                        CREDIT
                    </td>
                    @else
                    <td>
                    </td>
                    @endif
                    <td>
                        {{$stock->quantity}}
                    </td>
                    <td>
                        {{$stock->total_amount}}
                    </td>
                     <td>{{ $stock->one_pro_buycost }}</td>
                    <td>{{ $stock->mrp }}</td>
                    @php
                    $stocksum=$stocksum+$stock->quantity;
                    $stockamount=$stockamount+$stock->total_amount;
                    @endphp
                </tr>
                @endforeach

            </tbody>
        </table>


        <table id="example2" class="table table-striped table-bordered" style="width:100%">
            <tr>
                <td colspan="2">Quantity</td>
                <td>{{$stocksum}}</td>
            </tr>
            <tr>
                <td colspan="2">Total Amount</td>
                <td>{{$stockamount}}</td>
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
