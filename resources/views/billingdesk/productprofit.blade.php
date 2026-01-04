<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">

    <title>Product Profit</title>
    @include('layouts/usersidebar')
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
            background-color: #f2f2f2;
        }

        th {
            background-color: #187f6a;
            color: white;
        }

        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 1px solid #010101;
        }


        .content-wrapper {
            margin-left: 2rem;
        }

        .navbar {
            margin-bottom: 1rem;
        }

        .disabled {
            background-color: #e9ecef;
            color: #6c757d;
        }

        .btn-enable {
            background-color: green;
            color: white;
        }

        .btn-disable {
            background-color: red;
            color: white;
        }

        .btn-toggle {
            color: white;
            border: none;
            padding: 0.5em 1em;
            border-radius: 4px;
        }
    </style>
    <style>
        /* Your existing styles... */
        .date-filter {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .date-filter label {
            font-weight: bold;
            margin-right: 10px;
        }
        .date-filter input {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .date-filter button {
            padding: 5px 15px;
            background: #187f6a;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .date-filter button:hover {
            background: #15416b;
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
    </style>
</head>
@php
use App\Models\Softwareuser;
use Illuminate\Support\Facades\DB;

    $userid = Session('softwareuser');

$adminid = Softwareuser::Where('id', $userid)
    ->pluck('admin_id')
    ->first();
$adminroles = DB::table('adminusers')
->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
->where('user_id', $adminid)
->get();
@endphp
<body>
    <div id="content">
        @if ($adminroles->contains('module_id', '30'))
    <div style="margin-left:15px;margin-top:15px;">

        @include('navbar.reportnavbar')
    </div>
        @else
            <x-logout_nav_user />
        @endif
        <br><br>
        <x-admindetails_user :shopdatas="$shopdatas" />

        <div class="content-wrapper">
            <h2>Product Report</h2>

            <!-- Date Filter Form -->
            <div class="date-filter" >
                <form method="GET" action="{{ route('product.profitfilter') }}">
                    <label for="start_date">From:</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $start_date }}">

                    <label for="end_date">To:</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $end_date }}">

                    <button type="submit">Filter</button>
                </form>
            </div>
            <div class="form-group row" style="margin-bottom: 15px;">
                <div class="col-md-12 text-right">
                    <a href="{{ route('exportproductreport') }}" class="btn btn-primary">
                        <i class="fa fa-file-excel-o"></i> Export to Excel
                    </a>
                    <a href="{{ route('printproductreport') }}" class="btn btn-primary" target="_blank">
                        <i class="fa fa-print"></i> Print
                    </a>
                    <a href="{{ route('exportcategoryreport') }}" class="btn btn-primary">
                        <i class="fa fa-file-excel-o"></i> Category-wise Report
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                     <table class="table" id="table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th style="display:none;">Buycost</th>
                                <th>Total Sold Quantity</th>
                                <th style="display:none;">Total Sold Amount</th>
                                <th style="display:none;">Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalProfit = 0;
                                $totalsold = 0;
                            @endphp
                            @foreach ($products as $product)
                                @php
                                    $totalProfit += $product->profit;
                                    $totalsold += $product->total_amount;
                                @endphp
                                <tr>
                                    <td>{{ $product->product_name }}</td>
                                    <td style="display:none;">{{ $product->rate }}</td>
                                    <td>{{ $product->remain_quantity }}</td>
                                    <td style="display:none;">{{ number_format($product->total_amount, 3) }}</td>
                                    <td style="display:none;">{{ number_format($product->profit, 3) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="display:none;">
                            <tr>
                                <td colspan="3"></td>
                                <td><strong>{{$currency}} {{ number_format($totalsold, 3) }}</strong></td>
                                <td><strong>{{$currency}} {{ number_format($totalProfit, 3) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#table').DataTable({
            order: [
                [0, 'asc']
            ]
        });

        // Function to reset dates to default
        function resetDates() {
            // Set dates to current month
            const firstDay = new Date();
            firstDay.setDate(1);

            document.getElementById('start_date').valueAsDate = firstDay;
            document.getElementById('end_date').valueAsDate = new Date();

            // Submit the form
            document.forms[0].submit();
        }
    });
</script>