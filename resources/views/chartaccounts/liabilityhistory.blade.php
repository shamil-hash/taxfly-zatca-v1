<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Liability History</title>
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
        .dropdown {
        position: relative; /* Keep the dropdown in position relative to the button */
        float: right; /* Keep the ☰ button on the right side of the page */
    }

   /* Ensure the dropdown appears on the left side */
.dropdown-menu {
    display: none;
    position: absolute;
    right: 100%;  /* Open the dropdown to the left of the button */
    top: 70; /* Align it with the top of the button */
    background: white;
    border: 1px solid #ddd;
    padding: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-left: -120px;
}

.dropdown:hover .dropdown-menu {
    display: block; /* Show the dropdown when hovering */
}

.dropdown-menu a {
    display: block;
    padding: 8px 12px;
    text-decoration: none;
    color: black;
    border-bottom: 1px solid #ddd;

}

.dropdown-menu a:last-child {
    border-bottom: none;
}

.dropdown-menu a:hover {
    background: #187f6a;
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
    <div id="content">
        @if ($adminroles->contains('module_id', '30'))
        <div style="margin-top: 15px;margin-left:15px;">
            @include('navbar.chartofaccounts')
        </div>
        @else
            <x-logout_nav_user />
        @endif
        <x-admindetails_user :shopdatas="$shopdatas" />
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#">Chart of Accountant</a></li>
              <li class="breadcrumb-item active" aria-current="page">Liability History</li>
            </ol>
          </nav>
          <div class="dropdown">
            <button style="background-color: #187f6a;" class="btn btn-info" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                ☰
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a href="/chartaccounts" class="dropdown-item ">Add Liability</a>
                <a href="/assethistory" class="dropdown-item ">Asset History</a>
                <a href="/capitalhistory" class="dropdown-item ">Capital History</a>
                <a href="/liabilityhistory" class="dropdown-item ">Liability History</a>

            </div>
        </div>
        <div class="content-wrapper">
            <h2>Liability History</h2>
            <div class="row">
                <div class="col-md-12">
                    <table class="table" id="table">
                        <thead>
                            <tr>
                                <th>Created</th>
                                <th>Liability Type</th>
                                <th>Liability Category</th>
                                <th>Amount</th>
                                <th>Details</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($liabilities as $liabiliti)
                            <tr>
                                <td>{{ $liabiliti->created_at }}</td>
                                <td>{{ $liabiliti->sub_type }}</td>
                                <td>{{ $liabiliti->type_category }}</td>
                                <td>{{ $liabiliti->type_amount }}</td>
                                <td>{{ $liabiliti->type_details }}</td>
                                <td>{{ $liabiliti->type_date }}</td>
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
