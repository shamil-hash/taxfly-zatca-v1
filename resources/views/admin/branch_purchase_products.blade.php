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
        <!--        <a href="/branchdat/{{ $branchid }}" class="btn btn-primary">Sales</a>-->
        <!--    </div>-->
        <!--    <div class="btn-group" role="group">-->
        <!--        <a href="/branchdatpurchase/{{ $branchid }}" class="btn btn-primary active">Purchase</a>-->
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
        <h1><b>{{ $branchname }} {{$receipt_no}}</b></h1>
        <span class="alignspan">Purchase DATA</span>
        <h2>Items</h2>

        <table class="table" id="example">
            <thead>
                <tr>
                    <th width="10%">Product Name</th>
                    <th width="5%">Quantity</th>
                    <th width="5%">Unit</th>
                    <th width="5%">Buying Cost</th>
                    <th width="5%">Price</th>
                    <th width="5%">{{$tax}}</th>
                    <th width="8%">Grand Total<br>(including {{$tax}})</th>
                    <th width="10%">Date</th>
                    <th width="10%">Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($item as $itempro)
                    <tr>
                        <td>
                            {{ $itempro->product_name }}
                        </td>
                        <td>
                            {{ $itempro->quantity }}
                        </td>
                        <td>
                            {{ $itempro->unit }}
                        </td>

                        <td>
                            <b>{{ $currency }}</b> {{ $itempro->buycost }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $itempro->price_without_vat }}
                        </td>
                        <td>
                            <!--@if ($itempro->vat_amount != null)-->
                            <!--    <b>{{ $currency }}</b> {{ $itempro->vat_amount }}-->
                            <!--@elseif ($itempro->vat_amount == null)-->
                            <!--    <span class='badge badge-default'>NA</span>-->
                            <!--@endif-->
                            {{ $itempro->price - $itempro->price_without_vat }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b>
                            <!--{{ $itempro->vat_amount !== null ? number_format($itempro->price_without_vat + $itempro->vat_amount, 3) : $itempro->price_without_vat }}-->
                            {{ $itempro->price }}
                        </td>
                        <td>

                            {{ \Carbon\Carbon::parse($itempro->created_at)->format('D M Y') }}
                        </td>
                        <td>

                            {{ \Carbon\Carbon::parse($itempro->created_at)->format('H:i:s A') }}
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
            order: [

            ]
        });
    });
</script>
