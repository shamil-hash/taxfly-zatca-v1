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
                <a href="/userpurchase/{{$uid}}" class="btn btn-primary active">Purchase</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchasereturn/{{$uid}}" class="btn btn-primary">Purchase Return</a>
            </div>
            <!--<div class="btn-group" role="group">-->
            <!--    <a href="/userstock/{{$uid}}" class="btn btn-primary">Stocks</a>-->
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
                <li class="breadcrumb-item active" aria-current="page">Purchase</li>
            </ol>
        </nav>
        Purchase DATA
        <h2>Purchases</h2>
        <form action="/userpurchasedate/{{$uid}}" method="get">
            <div class="row">
                <div class="col-sm-6">
                    <h4>SELECT DATES
                    </h4>
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
                    </div>
                </div>
            </div>
        </form>
        <br>

        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th width="10%">Reciept No</th>
                    <th width="10%">Created Date</th>
                    <th width="10%">Comment</th>
                    <th width="5%">Price</th>
                    <th width="5%">{{$tax}}</th>
                    <th width="5%">Grand Total <br />(Including {{$tax}})</th>
                    <th width="8%">Supplier Name</th>
                    <th width="5%">Action</th>
                    <th width="5%">Download</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->reciept_no }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($purchase->created_at)->format('d-M-Y | h:i:s A') }}
                        </td>
                        <td>{{ $purchase->comment }}</td>
                        <td><b>{{ $currency }}</b> {{ $purchase->price_without_vat }}</td>


                        <td>
                            <!--@if ($purchase->vat_amount != null)-->

                            <!--        <b>{{ $currency }}</b> {{ $purchase->vat_amount }}-->

                            <!--@elseif ($purchase->vat_amount == null)-->
                            <!--    <span class='badge badge-default'>NA</span>-->
                            <!--@endif-->

                            {{ $purchase->price - $purchase->price_without_vat }}
                        </td>
                        <td>

                                <b>{{ $currency }}</b>
                                <!--{{ $purchase->vat_amount !== null ? number_format($purchase->price_without_vat + $purchase->vat_amount, 3) : $purchase->price_without_vat }}-->
                            {{ $purchase->price }}
                        </td>
                        <td>{{ $purchase->supplier }}</td>
                        <td>
                            <a href="/user_purchase_products/{{ $uid }}/{{ $purchase->reciept_no }}"
                                class="btn btn-primary" title="View Purchased Products">VIEW</a>
                        </td>
                        @if ($purchase->file != null)
                            <td><b><a href="{{ url('/download', $purchase->file) }}" title="Download File">Download</a></b></td>
                        @else
                            <td>No file to download</td>
                        @endif
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
            order: [

            ]
        });
    });
</script>
