<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">

    @if ($page == 'sales_order')
        <title>Sales Order History</title>
    @elseif($page == 'deliverynote')
        <title>Delivery Note History</title>
    @elseif($page == 'quotation')
        <title>Quotation History</title>
    @elseif($page == 'performance_invoice')
        <title>Proforma Invoice History</title>
    @endif

    @include('layouts/usersidebar')
    <style>
              table {
    border-collapse: collapse;
    width: 100%;
    font-family: Arial, sans-serif;
    
}

th, td {
    border: 1px solid #e0e0e0;
    padding: 10px 12px;
    
}

th {
    background: #187f6a;
    color: white;
}

tr:hover {
    background: #f0faf8; /* Very light teal */
}

        .btn {
            font-size: 12px;
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
        @if ($adminroles->contains('module_id', '30'))
        <div style="margin-left:15px;margin-top:15px;">

            @include('navbar.billingdesknavbar')
        </div>
            @else
        <x-logout_nav_user />
    @endif

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Billing Desk</a></li>
                @if ($page == 'sales_order')
                    <li class="breadcrumb-item active" aria-current="page">Sales Order History</li>
                @elseif($page == 'deliverynote')
                    <li class="breadcrumb-item active" aria-current="page">Delivery Note History</li>
                @elseif($page == 'quotation')
                    <li class="breadcrumb-item active" aria-current="page">Quotation History</li>
                @elseif($page == 'performance_invoice')
                    <li class="breadcrumb-item active" aria-current="page">Proforma Invoice History</li>
                @endif
            </ol>
        </nav>

        <form action="/historyfilter_sales_delivery/{{ $page }}" method="get">
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
        @if ($page == 'sales_order')
            <h2>Sales Order History</h2>
        @elseif($page == 'deliverynote')
            <h2>Delivery Note History</h2>
        @elseif($page == 'quotation')
            <h2>Quotation History</h2>
        @elseif($page == 'performance_invoice')
            <h2>Proforma Invoice History</h2>
        @endif

        <table id="example" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Total Price</th>
                    <th>Discount <br /> amount</th>
                    <th>Grand Total <br /> (w/. discount)</th>
                    <th>{{$tax}}</th>
                    <th>Date and Time</th>
                    <th>Customer Name</th>
                    @if ($page == 'sales_order' || $page == 'deliverynote' || $page == 'performance_invoice')
                        <th>Payment Type</th>
                    @endif
                    <!--<th>Credit User</th>-->
                    <th>Action</th>
                    @if ($page == 'sales_order')
                        @foreach ($adminroles as $adminrole)
                            @if ($adminrole->module_id == '22')
                                <th>Billing</th>
                                <th>Edit</th>
                            @endif
                        @endforeach
                    @endif
                    @if ($page == 'quotation')
                        @foreach ($adminroles as $adminrole)
                            @if ($adminrole->module_id == '26')
                                <th>Billing</th>
                            @endif
                            @if ($adminrole->module_id == '28')
                                <th>To Sales Order</th>
                            @endif
                        @endforeach
                    @endif
                    <th>PDF</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>
                            {{-- @if ($product->vat_type == 1)
                                <span class="btn btn-warning"
                                    style="font-size: 8px;background-color: #766dc0;border:none">Inclusive</span>
                            @else
                                <span class="btn btn-info"
                                    style="font-size: 8px;background-color:#f5a875;border:none">Exclusive</span>
                            @endif
                            &nbsp;&nbsp; --}}
                            {{ $product->transaction_id }}
                        </td>
                        <td>
                            @if ($product->grandtotal_without_discount != '')
                                <b>{{ $currency }}</b> {{ $product->grandtotal_without_discount }}
                            @elseif ($product->grandtotal_without_discount == '')
                                <b>{{ $currency }}</b> {{ $product->sum }} + {{ $product->vat }}
                            @endif
                        </td>
                        <td>
                            @if ($product->discount_amount != '')
                                <b>{{ $currency }}</b> {{ number_format($product->discount_amount, 3) }}
                            @endif
                        </td>
                        <td>
                            @if ($product->vat_type == 1)
                                @if (!is_null($product->grandtotal_without_discount))
                                    <b>{{ $currency }}</b>
                                    {{ number_format($product->grandtotal_without_discount - $product->discount_amount, 3) }}
                                @else
                                    <b>{{ $currency }}</b>
                                    {{ number_format($product->sum * ($product->quantity - $product->discount_amount), 3) }}
                                @endif
                            @else
                                <b>{{ $currency }}</b> {{ $product->sum }}
                            @endif
                        </td>
                        <td><b>{{ $currency }}</b> {{ $product->vat }}</td>
                        <td>{{ date('d M Y | h:i:s A', strtotime($product->created_at)) }}</td>
                        <td>{{ $product->customer_name }}</td>
                        @if ($page == 'sales_order' || $page == 'deliverynote' || $page == 'performance_invoice')
                            <td>{{ $product->payment_type }}</td>
                        @endif
                        <!--<td> {{ $product->username }}</td>-->
                        <td>
                            <a href="/historydetails/{{ $page }}/{{ $product->transaction_id }}"
                                class="btn btn-primary" title="View Product Details">VIEW</a>
                            @if ($page == 'quotation')
                                <a href="/to_quotation/clone_quotation/{{ $product->transaction_id }}"
                                    class="btn btn-success" title="Clone Quotation">Clone</a>
                            @endif
                        </td>
                        @if ($page == 'sales_order')
                            @foreach ($users as $user)
                                @if ($user->role_id == '17')
                                    @php
                                        $adminroles = DB::table('adminusers')
                                            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                                            ->where('user_id', $adminid)
                                            ->get();
                                    @endphp

                                    @foreach ($adminroles as $adminrole)
                                        @if ($adminrole->module_id == '22')
                                            <td>
                                                @php
                                                    $invoice_done = DB::table('sales_orders')
                                                        ->where('transaction_id', $product->transaction_id)
                                                        ->pluck('invoice_done')
                                                        ->first();
                                                @endphp

                                                @if ($invoice_done != 1)
                                                    <a href="/to_invoice/{{ $page }}/{{ $product->transaction_id }}"
                                                        class="btn btn-success" title="Do Billing">To Invoice</a>
                                                @else
                                                    Invoice Done
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach

                            <td><a href="/salesorder_edit/editsalesorder/{{ $product->transaction_id }}"
                                    class="btn btn-danger">EDIT</a></td>
                        @elseif ($page == 'quotation')
                            @foreach ($users as $user)
                                @if ($user->role_id == '20')
                                    @php
                                        $adminroles = DB::table('adminusers')
                                            ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                                            ->where('user_id', $adminid)
                                            ->get();
                                    @endphp

                                    @foreach ($adminroles as $adminrole)
                                        @if ($adminrole->module_id == '26')
                                            <td>
                                                @php
                                                    $invoice_done = DB::table('quotations')
                                                        ->where('transaction_id', $product->transaction_id)
                                                        ->pluck('invoice_done')
                                                        ->first();
                                                @endphp

                                                @if ($invoice_done != 1)
                                                    <a href="/to_invoice/{{ $page }}/{{ $product->transaction_id }}"
                                                        class="btn btn-success" title="Do Billing">To Invoice</a>
                                                @else
                                                    Invoice Done
                                                @endif
                                            </td>
                                        @endif
                                        @if ($adminrole->module_id == '28')
                                            <td>
                                                <a href="/to_salesorder/quot_to_salesorder/{{ $product->transaction_id }}"
                                                    class="btn btn-success" title="Do Sales Order">To Sales Order</a>
                                            </td>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                        <td>
                            @if ($page == 'sales_order' || $page == 'quotation' || $page == 'performance_invoice')
                                <a href="/salesorderreceipt_print/{{ $page }}/{{ $product->transaction_id }}"
                                    class="btn btn-primary">Print</a>
                            @elseif($page == 'deliverynote')
                                <a href="/deliverynote_print/{{ $product->transaction_id }}"
                                    class="btn btn-primary">Print</a>
                            @endif
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
            order: []
        });
    });
</script>
