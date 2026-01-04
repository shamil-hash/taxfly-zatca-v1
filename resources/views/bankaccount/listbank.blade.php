<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>List Bank</title>
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
            @include('navbar.banknav')
        </div>
        @else
            <x-logout_nav_user />
        @endif
        <br><br>
        <x-admindetails_user :shopdatas="$shopdatas" />
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Bank</a></li>
                <li class="breadcrumb-item active" aria-current="page">List Bank</li>
            </ol>
        </nav>
        <div class="content-wrapper">
            <h2>List Bank</h2>
            <div class="row">
                <div class="col-md-12">
                    <table class="table" id="table">
                        <thead>
                            <tr>
                                <th>Account Name</th>
                                <th>Bank Name</th>
                                <th>Account Number</th>
                                <th>Current Balance</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($banks as $bank)
                                <tr class="{{ $bank->status ? '' : 'disabled' }}">
                                    <td>{{ $bank->account_name }}</td>
                                    <td>{{ $bank->bank_name }}</td>
                                    <td>{{ $bank->account_no }}</td>
                                    <td>{{ $bank->current_balance }}</td>
                                    <td>
                                        @if ($bank->status == 1)
                                            <span class="label label-success">Enabled</span>
                                        @else
                                            <span class="label label-danger">Disabled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($bank->status == 1)
                                            <a class="edit-btn btn btn-danger" href="{{ route('bank.edit', ['id' => $bank->id]) }}">Edit</a>
                                        @else
                                            <span>No Edit</span>
                                        @endif
                                        <form action="{{ route('bank.toggleStatus', ['id' => $bank->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-toggle {{ $bank->status ? 'btn-danger' : 'btn-enable' }}">
                                                {{ $bank->status ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>
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
