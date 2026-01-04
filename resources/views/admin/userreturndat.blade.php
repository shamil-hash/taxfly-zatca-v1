<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>User Return Report</title>
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
                <a href="/userreport/{{ $uid }}" class="btn btn-primary">Sales</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchase/{{ $uid }}" class="btn btn-primary">Purchase</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchasereturn/{{ $uid }}" class="btn btn-primary">Purchase Return</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userreturn/{{ $uid }}" class="btn btn-primary active">Return</a>
            </div>
            <!--<div class="btn-group" role="group">-->
            <!--    <a href="/userstock/{{ $uid }}" class="btn btn-primary">Stocks</a>-->
            <!--</div>-->
            <div class="btn-group" role="group">
                <a href="/daily_report/{{ $uid }}" class="btn btn-primary">Daily Report</a>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Reports</a></li>
                <li class="breadcrumb-item active" aria-current="page">Return</li>
            </ol>
        </nav>
        Return Data
        <h2>Returns</h2>
        <form action="/userreturndate/{{ $uid }}" method="get">
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

        <table id="example" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Total Price</th>
                    <th>Discount <br /> amount</th>
                    <th>Grand Total <br /> (w/. discount)</th>
                    <th>{{$tax}}</th>
                    <th>Date and Time</th>
                    <th>Phone Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                    $vat = 0;
                    $grand_total = 0;
                    $discount = 0;
                    $grand_with_discount = 0;
                @endphp
                @foreach ($returns as $return)
                    <tr>
                        <td>
                            @if ($return->vat_type == 1)
                                <span class="btn btn-warning"
                                style="font-size: 8px;background-color: #766dc0;border:none">Inclusive</span>
                            @else
                                <span class="btn btn-info"
                                style="font-size: 8px;background-color:#f5a875;border:none">Exclusive</span>
                            @endif
                            &nbsp;&nbsp;
                            {{ $return->transaction_id }}
                        </td>                        <td>
                            @if ($return->totalamount_wo_discount != '')
                                <b>{{ $currency }}</b> {{ $return->totalamount_wo_discount }}
                            @elseif ($return->totalamount_wo_discount == '')
                                <b>{{ $currency }}</b> {{ $return->total_amount * $return->quantity }}
                            @endif
                        </td>

                        <td>
                            @if ($return->discount_amount != '')
                                <b>{{ $currency }}</b> {{ number_format($return->discount_amount, 3) }}


                            @endif
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $return->total_amount }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $return->vat }}
                        </td>
                        <td>
                            {{ date('d M Y | h:i:s A', strtotime($return->created_at)) }}
                        </td>
                        <td>{{ $return->phone }}</td>
                        <td>
                            <a href="/return_user_dat/{{ $return->transaction_id }}/{{ $return->created_at }}/{{ $uid }}"
                                class="btn btn-primary">VIEW</a>

                        </td>
                    </tr>

                    @php
                        $total +=
                            $return->vat !== null
                                ? $return->totalamount_wo_discount - $return->vat
                                : $return->totalamount_wo_discount;
                        $vat += $return->vat ?? 0;
                        $grand_total += $return->totalamount_wo_discount;
                        $discount += $return->discount_amount;
                        $grand_with_discount += $return->total_amount;
                    @endphp
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
