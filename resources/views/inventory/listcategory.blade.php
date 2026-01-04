<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <title>Category</title>
    @include('layouts/usersidebar')
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 5rem;
        }
 .btn-primary{
            background-color: #187f6a;
            color: white;
        }
        th,
        td {
            border: 1px solid black;
            text-align: center;
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

        .label {
            border-radius: 10px;
        }
         .pagination {
        display: flex;
        list-style: none;
        padding: 0;
    }
    
    .pagination li {
        margin: 0 4px;
    }
    
    .pagination li a, 
    .pagination li span {
        color: #187f6a; /* Your green color */
        padding: 6px 12px;
        border: 1px solid #ddd;
        text-decoration: none;
    }
    
    .pagination li.active span {
        background-color: #187f6a;
        color: white;
        border-color: #187f6a;
    }
    
    .pagination li a:hover {
        background-color: #f0f0f0;
    }
    
    .pagination li.disabled span {
        color: #aaa;
        cursor: not-allowed;
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
                <li class="breadcrumb-item active" aria-current="page">Category</li>
            </ol>
        </nav>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <h2>Categories</h2><br>
                    <div class="category">

                        <table id="table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $category->category_name }}</td>
                                    <td>
                                        @if ($category->access == 1)
                                        <span class="label label-success">Enabled</span>
                                        @else
                                        <span class="label label-danger">Disabed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- <button value="{{ $category->id }},{{ $category->category_name }}" class="btn btn-danger settingsbtn">
                                  DELETE
                                  <i class="glyphicon glyphicon-ban-circle"></i>
                                </button> -->

                                        <a>
                                            <button type="button" value="{{ $category->id }},{{ $category->access }}" class="btn btn-primary settingsbtn btn-sm" title="Settings">
                                                <i class="glyphicon glyphicon-cog"></i>
                                            </button>
                                        </a>

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                        <span>
                            {{ $categories->links() }}
                        </span>
                    </div>

                </div>
                <div class="col-md-2">
                    <h3>
                        <button class="btn btn-primary editbtn btn-sm" title="Create New Category">Create</button>
                    </h3>
                </div>


                <div class="col-md-4">
                    <h2>Measuring Units</h2><br>
                    <div class="units">
                        <table id="table">
                            <thead>
                                <tr>
                                    <th>Unit</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($units as $ut)
                                <tr>
                                    <td>{{ $ut->unit }}</td>
                                    <td>
                                        @if ($ut->status == 1)
                                        <span class="label label-success">Enabled</span>
                                        @else
                                        <span class="label label-danger">Disabed</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- <button value="{{ $ut->id }},{{ $ut->unit }}, {{ $ut->status }}"
                                        class="btn btn-danger settingsbtn1">
                                        @if ($ut->status == 1)
                                        Disable
                                        @else
                                        Enable
                                        @endif
                                        <i class="glyphicon glyphicon-ban-circle"></i>
                                        </button> --}}

                                        @if ($ut->status == 1)
                                        <a href="/deleteunit/{{ $ut->id }}" class="btn btn-danger" title="Disable">
                                            Disable
                                        </a>
                                        @else
                                        <a href="/deleteunit/{{ $ut->id }}" class="btn btn-success" title="Enable">
                                            Enable
                                        </a>
                                        @endif

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                        <span>
                            {{ $units->links() }}
                        </span>
                    </div>
                </div>
                <div class="col-md-2">
                    <h3>
                        <button class="btn btn-primary editbtn1 btn-sm" title="Create New Unit">Create</button>
                    </h3>
                </div>

            </div>
            @include('modal.createcategory')
            <!-- @include('modal.deletecategory') -->


            @include('modal.deleteunit')
        </div>
        @include('modal.createunit')
         @include('modal.categorystatus')
    </div>

</body>

</html>
<script>
    $(document).ready(function() {
        $(document).on('click', '.editbtn', function() {
            $('#Createcategory').modal('show');
        });
    });
</script>


<script>
    $(document).ready(function() {
        $(document).on('click', '.settingsbtn', function() {
            var data = $(this).val().split(',');
            console.log(data);
            var user = data[0];
            console.log(user);
            var access = data[1];
            $('#categorystatus').modal('show');
            $('#user__id').val(user);
            var uid = $("#user__id").val();
            $("#disable").attr("href", '/changecategoryaccess/' + uid);
            $("#enable").attr("href", '/changecategoryaccess/' + uid);
            var disablediv = document.getElementById("disablediv");
            if (access === "1") {
                disablediv.style.display = "block";
            } else {
                disablediv.style.display = "none";
            }
            var enablediv = document.getElementById("enablediv");
            if (access === "0") {
                enablediv.style.display = "block";
            } else {
                enablediv.style.display = "none";
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $(document).on('click', '.editbtn1', function() {
            $('#Createunit').modal('show');
        });
    });
</script>

{{-- <script>
    $(document).ready(function() {
        $(document).on('click', '.settingsbtn1', function() {
            var data = $(this).val().split(',');
            console.log(data);
            var unit_id = data[0];
            console.log(unit_id);

            var unit = data[1];
            console.log(unit);

            $('#DeleteUnit').modal('show');
            $('#user__id').val(unit_id);
            var uid = $("#user__id").val();
            $("#edit").attr("href", '/deleteunit/' + uid);

            $("#unit").html(unit);


        });
    });
</script> --}}
