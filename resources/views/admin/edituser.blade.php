<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Edit User</title>
    @if (Session('adminuser'))
    @include('layouts/adminsidebar')
@elseif(Session('softwareuser'))
    @include('layouts/usersidebar')
@endif
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
  .btn-primary {
            background-color: #187f6a;
            color: #fff;
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
    </style>
    <style>
        input[type=text] {
            border-radius: 5px;
            width: 30%;
        }

        input[type=password] {
            border-radius: 5px;
            width: 30%;
        }

        select {
            border-radius: 5px;
            width: 30%;
        }

        .form-control {
            display: block;
            width: 30%;
            height: 34px;
        }
    </style>
    <!-- navbar -->
    <style>
        .navbar {
            padding: 15px 10px;
            border: none;
            border-radius: 0;
            margin-bottom: 40px;
            box-shadow: 1px 1px 3px rgb(0 0 0 / 10%);
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
        <!--@if (Session('adminuser'))-->
        <!--<nav class="navbar navbar-default">-->
        <!--    <div class="container-fluid">-->
        <!--        <div class="navbar-header">-->
        <!--            <button type="button" id="sidebarCollapse" class="btn navbar-btn">-->
        <!--                <i class="glyphicon glyphicon-chevron-left"></i>-->
        <!--                <span></span>-->
        <!--            </button>-->
        <!--        </div>-->
        <!--        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">-->
        <!--            <ul class="nav navbar-nav navbar-right">-->
        <!--                <li><a href="/adminlogout">Logout</a></li>-->
        <!--            </ul>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</nav>-->
        <!--@endif-->
        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif
        <x-admindetails_user :shopdatas="$shopdatas" />

        <form action="/useredit" method="POST" id="user_edit" name="user_edit" onsubmit="return(validateSearch());">
            @csrf
            <h2 ALIGN="CENTER">Edit User </h2>
            <div>
                <div align="center">
                    <div class="form-group">
                        <br>
                        <input type="hidden" class="form-control" name="id" value="{{ $uid }}">
                        <br>
                        <label>Name</label>
                        <input type="text" class="form-control name" name="name" placeholder="name"
                            value="{{ $name }}">
                        <span style="color:red">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <label>User Name</label>
                        <input type="text" class="form-control username" name="username" placeholder="Username"
                            value="{{ $username }}">
                        <span style="color:red">
                            @error('username')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <label>Password</label>
                        <input type="password" class="form-control password" name="password" placeholder="Password">
                        <span style="color:red">
                            @error('password')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <label>Confirm Password</label>
                        <input type="password" class="form-control confirmpassword" name="confirmpassword"
                            placeholder="Confirm Password">
                        <span style="color:red">
                            @error('confirmpassword')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <label>Joined Date</label>
                        <input type="text" class="form-control joined_date" name="joined_date"
                            placeholder="Joined Date" value="{{ $joined_date }}">
                        <span style="color:red">
                            @error('joined_date')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>

                        <button type="submit" class="btn btn-primary" id="usereditBtn">SUBMIT</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

</body>

</html>

<script type="text/javascript">
    var currentBoxNumber = 0;

    $(".name").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.username");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".username").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.password");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".password").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.confirmpassword");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".confirmpassword").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.joined_date");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
</script>
<!-- Submit form data when clicking ENTER BUTTON -->
<script>
    $(function() {
        $('#user_edit').keypress(function(e) { //use form id
            if (e.which == 13) {
                validateSearch(); //-- to validate form
                $('#user_edit').submit(); // use form id
                return false;
            }
        });
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("user_edit");
        const submitBtn = document.getElementById("usereditBtn");

        form.addEventListener("submit", function(e) {
            // Prevent the form from submitting multiple times
            submitBtn.disabled = true;
            submitBtn.innerText = "Submitting...";

            // Allow the form to submit normally
            return true;
        });
    });
</script>
