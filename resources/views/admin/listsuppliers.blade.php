<!DOCTYPE html>
<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>List Supplier</title>
    @if (Session('adminuser'))
        @include('layouts/adminsidebar')
    @elseif(Session('softwareuser'))
        @include('layouts/usersidebar')
    @endif
     <style>
        .gdot {
            height: 15px;
            width: 15px;
            background-color: #0adc0a;
            border-radius: 50%;
            display: inline-block;
            vertical-align: bottom;
        }
    </style>
    <style>
        .cdot {
            height: 15px;
            width: 15px;
            background-color: #cccccc;
            border-radius: 50%;
            display: inline-block;
            vertical-align: bottom;
        }
    </style>
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
      
    @if (Session('softwareuser'))
    <x-admindetails_user :shopdatas="$shopdatas" />

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Supplier</a></li>
                <li class="breadcrumb-item active" aria-current="page">List Supplier</li>
            </ol>
        </nav>
        @endif
        <h2>List Suppliers</h2>

        <table id="example" class="table table-striped table-bordered" width="100%">
            <thead>
                <tr>
                    <th width="20%">Name</th>
                    <th width="20%">Mobile</th>
                    @if (Session('adminuser'))
                    <th width="20%">Branch</th>
                    @endif
                    {{-- <th width="8%">Balance <br />Credit Amount</th> --}}
                    <th width="20%">Date</th>
                    <th width="20%">Action</th>
                    {{-- <th>Payment Vouchers</th> --}}
                </tr>
            </thead>
            <tbody id="listcredit">
                @foreach ($softusers as $softuser)
                    <tr>
                        <td>
                            {{ $softuser->name }}
                        </td>
                        <td>
                            {{$softuser->mobile}}
                        </td>
                        @if (Session('adminuser'))
                        <td>
                            {{ $softuser->location }}
                        </td>
                        @endif
                        {{-- <td>
                            @if ($softuser->id == $softuser->supplier_id)
                                @if ($softuser->due_amt > $softuser->collected_amt)
                                    <b>{{ $currency }}</b>
                                    {{ number_format($softuser->due_amt - $softuser->collected_amt, 3) }}
                                @else
                                    <b>{{ $currency }}</b> 0
                                @endif
                            @else
                                <span class='badge badge-default'>NA</span>
                            @endif
                        </td> --}}



                        <td>
                            {{ $softuser->created_date }}
                        </td>
                        <td>
                            <div class="btn-group btn-space">
                                <button class="btn btn-danger"><a href="editsupplier/{{ $softuser->id }}"
                                        title="Edit Supplier" style="color:white;">EDIT</a></button>
                                <!-- <button class="btn btn-secondary"><a href="suppliersales/{{ $softuser->id }}"
                                        title="Stock Purchase Report"><i
                                            class="glyphicon glyphicon-briefcase"></i></a></button> -->
                                {{-- @if ($softuser->id == $softuser->supplier_id) --}}
                                {{-- <button class="btn btn-secondary"><a
                                            href="admin_supplier_credittrans/{{ $softuser->supplier_id }}"
                                            title="Supplier Credit Summary"><i
                                                class="glyphicon glyphicon-eye-open"></i></a></button> --}}

                                <!-- <button class="btn btn-secondary"><a
                                        href="suppliercredit_trans_history/{{ $softuser->id }}"
                                        title="Supplier Statements"><i
                                            class="glyphicon glyphicon-list-alt"></i></a></button> -->
                                {{-- @endif --}}
                            </div>
                        </td>
                        {{-- <td>
                            <a class="btn btn-primary" href="payment_voucher/{{ $softuser->id }}"
                                title="Payment Vouchers">Vouchers</a>
                        </td> --}}
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
                [0, 'asc']
            ]
        });
    });
</script>
