<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>credit voucher</title>
        @include('layouts/usersidebar')
    <style>
        .table-container {
            /* width: 75%; */
        }

        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
            width: 25%;
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


      
        <div align="center">
            @foreach ($shopdatas as $shopdata)
                    {{ $shopdata['name'] }}
                    <br>
                    Phone No:{{ $shopdata['phone'] }}
                    <br>
                    Email:{{ $shopdata['email'] }}
                    <br>
                    <br>
                    @endforeach
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Credit Note</a></li>
                        <li class="breadcrumb-item active" aria-current="page">History</li>
                    </ol>
                </nav>


        <br />
        <div class="table-container">
            <table class="table table-striped table-bordered" id="example">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Credit Note amount</th>
                        <th>Date</th>
                        <th>Voucher</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $number = 1; ?>
                    @foreach ($history as $purchase)
                        <tr>
                            <td>{{ $number }}</td>
                            <td><b>{{ $currency }}</b> {{ number_format($purchase->credit_note, 2) }}</td>
                            <td>{{ date('d M Y | h:i:s A', strtotime($purchase->created_at)) }}</td>
                            <td>
                        <!-- Updated Download button for the voucher -->
                        <a href="{{ route('credit.print', ['transaction_id' => $purchase->transaction_id, 'id' => $purchase->id]) }}"
                           class="btn btn-primary" title="Download Payment Voucher">Download</a>
                    </td>
                        </tr>
                        <?php $number++; ?>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            order: [
                // [0, 'desc']
            ]
        });
    });
</script>
