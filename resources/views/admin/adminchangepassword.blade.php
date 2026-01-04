<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin</title>
    @include('layouts/adminsidebar')
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

<body>
    <!-- Page Content Holder -->
    <div id="content">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" id="sidebarCollapse" class="btn navbar-btn">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                        <span></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="/adminlogout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::has('failed'))
            <div class="alert alert-danger">
                {{ Session::get('failed') }}
            </div>
        @endif
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
        <form action="submitadminpassword" method="POST" id="changepassword" name="changepassword"
            onsubmit="return(validateSearch());">
            @csrf
            <h2 ALIGN="CENTER">Change Password </h2>
            <div>
                <div align="center">
                    <div class="form-group">
                        <br>
                        <input type="text" class="form-control username" name="username" placeholder="Username"
                            value="{{ $username }}">
                        <span style="color:red">
                            @error('username')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <input type="password" class="form-control old_password" name="old_password"
                            placeholder="Old Password">
                        <span style="color:red">
                            @error('old_password')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <input type="password" class="form-control password" name="password" placeholder="New Password">
                        <span style="color:red">
                            @error('password')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <input type="password" class="form-control confirmpassword" name="confirmpassword"
                            placeholder="Confirm Password">
                        <span style="color:red">
                            @error('confirmpassword')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <button type="submit" class="btn btn-primary submitpassword">Change Password</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

</body>

</html>

<script type="text/javascript">
    var currentBoxNumber = 0;

    $(".username").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.old_password");
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
    $(".old_password").keydown(function(event) {
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
            textboxes = $("button.submitpassword");
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

<script>
    $(function() {
        $('#changepassword').keypress(function(e) { //use form id
            if (e.which == 13) {
                validateSearch(); //-- to validate form
                $('#changepassword').submit(); // use form id
                return false;
            }
        });
    });
</script>
