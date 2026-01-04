<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>User Sales Report</title>
    @include('layouts/adminsidebar')

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            text-align: center;
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

        tr.footer_table>td {
            text-align: right;
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
                <a href="" class="btn btn-primary active">Sales</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchase/{{ $uid }}" class="btn btn-primary">Purchase</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchasereturn/{{ $uid }}" class="btn btn-primary">Purchase Return</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userreturn/{{ $uid }}" class="btn btn-primary">Return</a>
            </div>
            <!--<div class="btn-group" role="group">-->
            <!--    <a href="/userstock/{{ $uid }}" class="btn btn-primary">Stocks</a>-->
            <!--</div>-->

            <div class="btn-group" role="group">
                <a href="/daily_report/{{ $uid }}" class="btn btn-primary">Daily Report</a>
            </div>
        </div>
        <br>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Reports</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sales</li>
            </ol>
        </nav>
        <h2>User Sales Report</h2>
        <form action="/filtersalesreport/{{ $uid }}" method="get">
            <div class="row">
                <div class="col-sm-6">
                    <h4>SELECT DATES </h4>
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
        <!-- content -->

        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Date and Time</th>
                    <th>Total Price</th>
                    <th>Discount Amount</th>
                    <th>{{$tax}}</th>
                    <th>Grand Total w/. Discount</th>
                    <th>Credit Note</th>
                    <th>Total </br>(Grand Total - Credit Note)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($buyproducts as $buyproduct)
                    <tr>
                        <td>
                            @if ($buyproduct->vat_type == 1)
                                <span class="btn btn-warning"
                                style="font-size: 8px;background-color: #766dc0;border:none">Inclusive</span>
                            @else
                                <span class="btn btn-info"
                                style="font-size: 8px;background-color:#f5a875;border:none">Exclusive</span>
                            @endif
                            &nbsp;&nbsp;
                            {{ $buyproduct->transaction_id }}
                        </td>
                        <td>
                            {{ date('d M Y | h:i:s A', strtotime($buyproduct->created_at)) }}
                        </td>

                        <td>
                            @if ($buyproduct->totalamount_wo_discount != '')
                                <b>{{ $currency }}</b> {{ $buyproduct->totalamount_wo_discount }}
                            @elseif ($buyproduct->totalamount_wo_discount == '')
                                <b>{{ $currency }}</b> {{ $buyproduct->sum + $buyproduct->vat }}
                            @endif
                        </td>
                        <td>
                            @if ($buyproduct->discount_amount != '')
                                <b>{{ $currency }}</b> {{ number_format($buyproduct->discount_amount, 3) }}
                            @endif
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $buyproduct->vat }}
                        </td>
                        <td>
                            @if ($buyproduct->vat_type == 1)
                                @if (!is_null($buyproduct->totalamount_wo_discount))
                                    <b>{{ $currency }}</b>
                                    {{ number_format($buyproduct->totalamount_wo_discount - $buyproduct->discount_amount, 3) }}
                                @else
                                    <b>{{ $currency }}</b>
                                    {{ number_format($buyproduct->sum * ($buyproduct->quantity - $buyproduct->discount_amount), 3) }}
                                @endif
                            @else
                                <b>{{ $currency }}</b> {{ $buyproduct->sum }}
                            @endif
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $buyproduct->total_credit_note }}
                        </td>
                        <td>
                            @if ($buyproduct->vat_type == 1)
                                @if (!is_null($buyproduct->totalamount_wo_discount))
                                    <b>{{ $currency }}</b>
                                    {{ number_format($buyproduct->totalamount_wo_discount - $buyproduct->discount_amount - $buyproduct->total_credit_note, 3) }}
                                @else
                                    <b>{{ $currency }}</b>
                                    {{ number_format($buyproduct->sum * ($buyproduct->quantity - $buyproduct->discount_amount - $buyproduct->total_credit_note), 3) }}
                                @endif
                            @else
                                <b>{{ $currency }}</b> {{ $buyproduct->sum - $buyproduct->total_credit_note }}
                            @endif
                        </td>
                        <td>
                            <a href="/userdatdetails/{{ $uid }}/{{ $buyproduct->transaction_id }}">VIEW</a>
                        </td>
                    </tr>
                @endforeach
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
