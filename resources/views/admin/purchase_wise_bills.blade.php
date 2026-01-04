<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @if (Session('adminuser'))
    <title>Admin</title>
@elseif(Session('softwareuser'))
<title>Purchase Wise Bill</title>
@endif
    @if (Session('adminuser'))
    @include('layouts/adminsidebar')
@elseif(Session('softwareuser'))
    @include('layouts/usersidebar')
@endif
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
    <!-- Page Content Holder -->
    <div id="content">
       

        <br>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Reports</a></li>
                <li class="breadcrumb-item"><a href="/stock">Stock Report</a></li>
                <li class="breadcrumb-item active" aria-current="page">Stock Details</li>
                <li class="breadcrumb-item active" aria-current="page">Stock Purchase History</li>
                <li class="breadcrumb-item active" aria-current="page"><a href=""> Bills</a></li>
            </ol>
        </nav>
        {{-- <form class="formcss" action="/adminstockadddate" method="get">
            <input name="branch" type="hidden" value="{{ $branchid }}">
            <input name="product_id" type="hidden" value="{{ $product_id }}">
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
        </form> --}}
        <br>

        <h1><b>{{ $receiptno }}</b></h1>
        <span class="alignspan">Bills Data</span>
        <h2>Bills</h2>

        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th width="10%">Transaction ID</th>
                    <th width="15%">Date and Time</th>
                    <th width="15%">Products Name</th>
                    <th width="8%">Quantity</th>
                    <th width="8%">Selling Cost</th>
                    <th width="8%">Discount Amount</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($bills as $bill)
                    <tr>
                        <td>{{ $bill->trans_id }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($bill->created_at)->format('d-M-Y | h:i:s A') }}
                        </td>

                        <td>{{ $bill->product_name }}</td>
                        <td>{{ $bill->remain_sold_quantity }}</td>
                        <td><b>{{ $currency }}</b> {{ $bill->billing_Sellingcost }}</td>
                        <td>
                            @if ($bill->discount_amount != '')
                                <b>{{ $currency }}</b> {{ $bill->discount_amount * $bill->remain_sold_quantity }}
                            @endif
                        </td>

                        @php
                            // $stocksum = $stocksum + $stock->quantity;
                        @endphp
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
                [1, 'asc']
            ]
        });
    });
</script>
