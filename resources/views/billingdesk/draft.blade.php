<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Draft</title>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">

    @include('layouts/usersidebar')
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
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
    <div id="content">
        @if (
            $page == 'bill_draft' ||
                $page == 'salesdraft' ||
                $page == 'quotationdraft' ||
                $page == 'performadraft' ||
                $page == 'deliverydraft')

    @if ($adminroles->contains('module_id', '30'))
    <div style="margin-left:15px;margin-top:15px;">

        @include('navbar.billingdesknavbar')
    </div>
    @else
    <x-logout_nav_user />
@endif
     @elseif ($page == 'productdraft' || $page == 'purchasedraft')

    @if ($adminroles->contains('module_id', '30'))
    <div style="margin-left:15px;margin-top:18px;">

        @include('navbar.invnavbar')
    </div>
    @else
<x-logout_nav_user />
@endif

    @endif
        <x-admindetails_user :shopdatas="$shopdatas" />

            <h2>Draft</h2>

        <table class="table" id="example">
            <thead>
                <tr>
                    @if (
                        $page == 'bill_draft' ||
                            $page == 'salesdraft' ||
                            $page == 'quotationdraft' ||
                            $page == 'performadraft' ||
                            $page == 'deliverydraft')
                        <th>Transaction ID</th>
                    @elseif ($page == 'productdraft' || $page == 'purchasedraft')
                        <th>ID</th>
                    @endif
                    <th>Date and Time</th>
                    @if (
                        $page == 'bill_draft' ||
                            $page == 'salesdraft' ||
                            $page == 'quotationdraft' ||
                            $page == 'performadraft' ||
                            $page == 'deliverydraft')
                        <th>Name</th>
                        <th>Phone</th>
                    @elseif ($page == 'productdraft')
                        <th>Product Name</th>
                        <th>Product Code</th>
                    @elseif ($page == 'purchasedraft')
                        <th>Bill No</th>
                        <th>Supplier Name</th>
                        <th>Comment</th>
                    @endif
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($draft as $item)
                    <tr>
                        <td>
                            @if (
                                $page == 'bill_draft' ||
                                    $page == 'salesdraft' ||
                                    $page == 'quotationdraft' ||
                                    $page == 'performadraft' ||
                                    $page == 'deliverydraft')
                                {{ $item->transaction_id }}
                            @elseif ($page == 'productdraft')
                                {{ $item->draft_id }}
                            @elseif ($page == 'purchasedraft')
                                Draft_{{ $item->reciept_no }}
                            @endif
                        </td>
                        <td>{{ date('d M Y | h:i:s A', strtotime($item->created_at)) }}</td>
                        @if (
                            $page == 'bill_draft' ||
                                $page == 'salesdraft' ||
                                $page == 'quotationdraft' ||
                                $page == 'performadraft' ||
                                $page == 'deliverydraft')
                            <td>{{ $item->customer_name }}</td>
                            <td>{{ $item->phone }}</td>
                        @elseif ($page == 'productdraft')
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->product_code }}</td>
                        @elseif ($page == 'purchasedraft')
                            <td>{{ $item->reciept_no }}</td>
                            <td>{{ $item->supplier }}</td>
                            <td>{{ $item->comment }}</td>
                        @endif
                        <td>
                            @if ($page == 'bill_draft')
                                <a href="/editdraft/bill_draft/{{ $item->transaction_id }}" class="btn btn-success">To
                                    Billing</a>
                            @elseif ($page == 'salesdraft')
                                <a href="/sales_order_draft/salesorderdraft/{{ $item->transaction_id }}"
                                    class="btn btn-success">To Sales Order</a>
                            @elseif ($page == 'quotationdraft')
                                <a href="/editquotationdraft/quotationdraft/{{ $item->transaction_id }}"
                                    class="btn btn-success">To Quotation</a>
                            @elseif ($page == 'performadraft')
                                <a href="/editperformadraft/performadraft/{{ $item->transaction_id }}"
                                    class="btn btn-success">To Proforma</a>
                            @elseif ($page == 'deliverydraft')
                                <a href="/editdeliverydraft/deliverydraft/{{ $item->transaction_id }}"
                                    class="btn btn-success">To Delivery Note</a>
                            @elseif ($page == 'productdraft')
                                <a href="/toproduct/{{ $item->draft_id }}" class="btn btn-success">To Add Product</a>
                            @elseif ($page == 'purchasedraft')
                                <input type="hidden" name="page" id="page" value="edit_purchase_draft">
                               <a class="editLink btn btn-success" data-transaction-id="{{ $item->reciept_no }}">
                                    @php
                                    $methodText = ($item->method == 2) ? 'Service' : 'Purchase';
                                    @endphp
                                    To {{$methodText}}
                                </a>
                            @endif
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
    var page = ($('#page').val()) ?? '';

    if (page == 'edit_purchase_draft') {
        document.querySelector('tbody').addEventListener('click', function(event) {
            if (event.target.classList.contains('editLink')) {
                var pageName = document.getElementById('page').value;
                var transactionId = event.target.getAttribute('data-transaction-id');
                window.location.href = "/editpurchasedraft/" + pageName + "/" + transactionId;
            }
        });
    }
</script>
