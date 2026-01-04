<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Cheque Summary</title>
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
        <div style="margin-top: 15px;margin-left:15px;">
            @include('navbar.billingdesknavbar')
        </div>
        @else
            <x-logout_nav_user />
        @endif
        <br><br>
        <x-admindetails_user :shopdatas="$shopdatas" />

        <div class="content-wrapper">
            <h2>Cheque Summary</h2>
            <div class="row">
                <div class="col-md-12">
                    <table class="table" id="table">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Supplier Name</th>
                                <th>Cheque Number</th>
                                <th>Amount</th>
                                <th>Depositing Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cheques as $cheque)
                                <tr>
                                    <td>{{ $cheque->customer ?? '-' }}</td> <!-- Show customer name or '-' -->
                                    <td>{{ $cheque->supplier ?? '-' }}</td> <!-- Show supplier name or '-' -->
                                    <td>{{ $cheque->cheque }}</td>
                                    <td>{{ $cheque->amount }}</td>
                                    <td>{{ $cheque->depositing_date }}</td>
                                    <td>
                                        @php
                                            $depositDate = \Carbon\Carbon::parse($cheque->depositing_date);
                                            $today = \Carbon\Carbon::today();
                                        @endphp

                                        @if ($depositDate->isFuture())
                                            <span style="color: red; font-weight: bold;">Pending</span>
                                        @else
                                            <span style="color: green; font-weight: bold;">Completed</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
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
    });
</script>
