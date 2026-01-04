<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Purchase Return History</title>
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
                <li class="breadcrumb-item active" aria-current="page">Purchase Return History</li>
            </ol>
        </nav>
        <br>
        <!-- content -->
        <h2>Purchase Return History</h2>

        <table id="example" class="table table-striped table-bordered" style="width:100%;">
            <thead>
                <tr>
                    <th>Bill No.</th>
                    {{-- <th>Comment</th> --}}
                    {{-- <th>Product Name</th>
                    <th>Quantity</th> --}}
                    <th>Total Price</th>
                    <th>{{$tax}}</th>
                    <th>Grand Total <br />(Including {{$tax}}) </th>
                    <th>Total Discount</th>
                    <th>Grand Total <br />(with discount) </th>
                    <th>Date and Time</th>
                    <th>Supplier Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                    $vat = 0;
                    $grand_total = 0;
                    $grand_totals = 0;
                    $grand_total_discount=0;
                @endphp

                @foreach ($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->reciept_no }}</td>
                        {{-- <td>{{ $purchase->comment }}</td> --}}
                        <!--{{-- <td>{{ $purchase->product_name }}</td>-->
                        <!--<td>{{ $purchase->quantity }}</td> --}}-->
                        <td><b>{{ $currency }}</b> {{ $purchase->total_price_without_vat }}</td>
                        <td>{{ $purchase->total_vat }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $purchase->grand_total }}
                        </td>
                        <td><b>{{ $currency }}</b> {{ $purchase->discount }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $purchase->grand_total-$purchase->discount }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($purchase->created_at)->format('d-M-Y | h:i:s A') }}
                        </td>
                        <td>{{ $purchase->supplier }}</td>
                        <td>
                            <a href="/returnpurchasedetails/{{ $purchase->reciept_no }}/{{ $purchase->created_at }}"
                                class="btn btn-primary">VIEW</a>

                            <!--<a href="/purchasereturn-pdf/{{ $purchase->reciept_no }}/{{ $purchase->created_at }}"-->
                            <!--    class="btn btn-primary" title="Print purchase return in PDF">PRINT</a>-->
                        </td>
                    </tr>
                    @php
                        $total += $purchase->total_price_without_vat;
                        $vat += $purchase->total_vat;
                        $grand_total += $purchase->grand_total;
                        $grand_totals += $purchase->discount;
                        $grand_total_discount+=$purchase->grand_total-$purchase->discount;
                    @endphp
                @endforeach


            </tbody>

            <tr style="font-weight: bold;font-size:16px;">
                <td colspan="1" class="total">Total</td>
                <td class="total"><b>{{ $currency }}</b> {{ number_format($total, 3) }}</td>
                <td class="total"><b>{{ $currency }}</b> {{ number_format($vat, 3) }}</td>
                <td class="total"><b>{{ $currency }}</b> {{ number_format($grand_total, 3) }}</td>
                <td class="total"><b>{{ $currency }}</b> {{ number_format($grand_totals, 3) }}</td>
                <td class="total"><b>{{ $currency }}</b> {{ number_format($grand_total_discount, 3) }}</td>

                <td colspan="2"></td>
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
