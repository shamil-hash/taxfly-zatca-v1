<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Returns - Products</title>
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
                <a href="/userreport/{{ $id }}" class="btn btn-primary">Sales</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchase/{{ $id }}" class="btn btn-primary">Purchase</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchasereturn/{{ $id }}" class="btn btn-primary">Purchase Return</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userreturn/{{ $id }}" class="btn btn-primary active">Return</a>
            </div>
            <!--<div class="btn-group" role="group">-->
            <!--    <a href="/userstock/{{ $id }}" class="btn btn-primary">Stocks</a>-->
            <!--</div>-->
            <div class="btn-group" role="group">
                <a href="/daily_report/{{ $id }}" class="btn btn-primary">Daily Report</a>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Reports</a></li>
                <li class="breadcrumb-item active" aria-current="page">Return</li>
            </ol>
        </nav>

        <h2>Return History</h2>

        <table id="example" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Rate</th>
                    <th>Date and Time</th>
                    <th>Total Price</th>
                    <th>Discount <br /> amount</th>
                    <th>Total w/.{{$tax}}  <br /> (w/. discount)</th>
                    <th>{{$tax}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details as $detail)
                    <tr>
                        <td>
                            {{ $detail->product_name }}
                        </td>
                        <td>
                            {{ $detail->quantity }}
                        </td>
                        <td>
                            {{ $detail->unit }}
                        </td>
                        <td>
                            {{ $detail->netrate }}
                        </td>
                        <td>
                            {{ date('d M Y | h:i:s A', strtotime($detail->created_at)) }}
                        </td>

                        <td>
                            @if ($detail->totalamount_wo_discount != '')
                            <b>{{ $currency }}</b> {{ $detail->totalamount_wo_discount }}
                            @elseif ($detail->totalamount_wo_discount == '')
                            <b>{{ $currency }}</b> {{ $detail->total_amount }}
                            @endif
                        </td>
                        <td>
                            {{ $detail->discount_amount }}
                        </td>
                        <td>
                            {{ $detail->total_amount }}
                        </td>
                        <td>
                            {{ $detail->vat_amount }}
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
