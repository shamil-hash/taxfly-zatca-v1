<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>List Customer</title>
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
    <div id="content">
      

        @if (Session('softwareuser'))
    <x-admindetails_user :shopdatas="$shopdatas" />
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Customer</a></li>
                <li class="breadcrumb-item active" aria-current="page">List Customer</li>
            </ol>
        </nav>
        @endif
              @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
        <div class="content-wrapper">
        <h2>List Customer</h2>
        <div class="row">
            <div class="col-md-12">
        <table class="table" id="table">
            <thead>
                <tr>
                    <th width="10%">Name</th>
                    <th width="10%">Mobile No.</th>
                    <!--<th width="20%">Username</th>-->
                    <th width="10%">Credit limit</th>
                    <th width="10%">Balance</th>
                        @if (Session('adminuser'))
                    <th width="10%">Branch</th>
                    @endif
                    <th width="10%">Date</th>
                    <th width="10%">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($softusers as $softuser)
                    <tr>
                        <td>
                            {{ $softuser->name }}
                        </td>
                        <td>
                            {{ $softuser->phone }}
                        </td>
                        <td>
                            {{ $softuser->l_amount }}
                        </td>
                        <td>
                            {{ $softuser->current_lamount }}
                        </td>
                            @if (Session('adminuser'))
                        <td>
                            {{ $softuser->location }}
                        </td>
                        @endif
                        <td>
                            {{ $softuser->created_date }}
                        </td>
                        <td>
                            <a href="editcredituser/{{ $softuser->id }}" class="btn btn-danger">EDIT</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- <span>
            {{ $softusers->links() }}
        </span> --}}
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
