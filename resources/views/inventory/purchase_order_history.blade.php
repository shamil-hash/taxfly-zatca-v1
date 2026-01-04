<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @if ($page == 'purchase_order')
        <title>Purchase Order History</title>
    @endif
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">

    @include('layouts/usersidebar')
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
 .btn-primary{
            background-color: #187f6a;
            color: white;
        }
        th,
        td {
            border: 1.5px solid black;
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
        @if ($adminroles->contains('module_id', '30'))
        <div style="margin-left:15px;margin-top:18px;">

            @include('navbar.invnavbar')
        </div>
        @else
<x-logout_nav_user />
@endif
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Inventory</a></li>
                @if ($page == 'purchase_order')
                    <li class="breadcrumb-item active" aria-current="page">Purchase Order History</li>
                @endif

            </ol>
        </nav>
        <form action="/purchaseorderhistorydate/{{ $page }}" method="get">
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

        @if ($page == 'purchase_order')
            <h2>Purchase Order History</h2>
        @endif

        <table id="example" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th width="8%">Transaction ID</th>
                    <th width="8%">Invoice No.</th>
                    <th width="10%">Comment</th>
                    <th width="8%">Total Price</th>
                    <th width="8%">{{$tax}}</th>
                    <th width="8%">Grand Total <br />(Including {{$tax}}) </th>
                    <th width="13%">Date and Time</th>
                    <th width="10%">Supplier Name</th>
                    <th width="10%">Payment Type</th>
                    <th width="10%">Action</th>
                    @if ($page == 'purchase_order')
                        @foreach ($adminroles as $adminrole)
                            @if ($adminrole->module_id == '27')
                                <th>Purchase</th>
                            @endif
                        @endforeach
                    @endif
                    <th width="5%">Download</th>
                </tr>
            </thead>
            <tbody>

                @php
                    $total = 0;
                    $vat = 0;
                    $grand_total = 0;
                @endphp

                @foreach ($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->purchase_order_id }}</td>
                        <td>{{ $purchase->reciept_no }}</td>
                        <td>{{ $purchase->comment }}</td>
                        <td><b>{{ $currency }}</b> {{ $purchase->price_without_vat }}</td>
                        <td>{{ $purchase->price - $purchase->price_without_vat }}</td>
                        <td><b>{{ $currency }}</b> {{ $purchase->price }}</td>
                        <td>{{ \Carbon\Carbon::parse($purchase->created_at)->format('d-M-Y | h:i:s A') }}</td>
                        <td>{{ $purchase->supplier }}</td>
                        <td>
                            @if ($purchase->payment_mode == '1')
                                CASH
                            @elseif ($purchase->payment_mode == '2')
                                CREDIT
                            @elseif ($purchase->payment_mode == '3')
                                BANK
                            @endif
                        </td>
                        <td>
                            <a href="/purchase_order_details/{{ $page }}/{{ $purchase->purchase_order_id }}"
                                class="btn btn-primary" title="View Products">VIEW</a>

                            <a href="/purhaseorderreceipt_print/{{ $purchase->purchase_order_id }}"
                                class="btn btn-primary">Print</a>
                        </td>
                        @if ($page == 'purchase_order')
                            @foreach ($users as $user)
                                @if ($user->role_id == '19')
                                    @php
                                        $adminroles = DB::table('adminusers')
                                            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                                            ->where('user_id', $adminid)
                                            ->get();
                                    @endphp

                                    @foreach ($adminroles as $adminrole)
                                        @if ($adminrole->module_id == '27')
                                            <td>
                                                @php
                                                    $purchase_done = DB::table('purchase_orders')
                                                        ->where('reciept_no', $purchase->reciept_no)
                                                        ->pluck('purchase_done')
                                                        ->first();
                                                @endphp

                                                @if ($purchase_done != 1)
                                                    <a href="/to_purchase/{{ $page }}/{{ $purchase->reciept_no }}"
                                                        class="btn btn-success" title="Do Purchase">To Purchase</a>
                                                @else
                                                    Purchase Done
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                        <td>
                            @if ($purchase->file != null)
                                <b><a href="{{ url('/download', $purchase->file) }}">Download</a></b>
                            @else
                                <b>No File</b>
                            @endif
                        </td>
                    </tr>
                    @php
                        $total += $purchase->price_without_vat;
                        $vat += $purchase->price - $purchase->price_without_vat ?? 0;
                        $grand_total += $purchase->price;
                    @endphp
                @endforeach
            </tbody>
            <tr style="font-weight: bold;font-size:16px;">
                <td colspan="3" class="total">Total</td>
                <td class="total"><b>{{ $currency }}</b> {{ number_format($total, 3) }}</td>
                <td class="total"><b>{{ $currency }}</b> {{ number_format($vat, 3) }}</td>
                <td class="total"><b>{{ $currency }}</b> {{ number_format($grand_total, 3) }}</td>
                <td colspan="5"></td>
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
            order: []
        });
    });
</script>
