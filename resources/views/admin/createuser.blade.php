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
        .btn-primary{
            background-color: #187f6a;
            color: white;
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
    <!-- navbar -->
    <style>
        .navbar {
            padding: 15px 10px;
            border: none;
            border-radius: 0;
            margin-bottom: 40px;
            box-shadow: 1px 1px 3px rgb(0 0 0 / 10%);
        }

        form {
            width: 90%;
            margin: auto;
            background-color: #ebe9e9;
            padding: 30px 40px;
            border-color: #ddd;
            border-width: 1px;
            border-radius: 10px;
            -webkit-box-shadow: none;
            box-shadow: none;
        }

        h2 {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .csssalign {
            margin: 10px;

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
        <!--<div align="center">-->
        <!--    @foreach ($shopdatas as $shopdata)-->
        <!--        {{ $shopdata['name'] }}-->
        <!--        <br>-->
        <!--        Phone No:{{ $shopdata['phone'] }}-->
        <!--        <br>-->
        <!--        Email:{{ $shopdata['email'] }}-->
        <!--        <br>-->
        <!--        <br>-->
        <!--    @endforeach-->
        <!--</div>-->
        <form action="usercreate" method="POST" id="createuser" name="createuser" onsubmit="return(validateSearch());">
            @csrf
            <h2>Create User </h2>
            <div>
                <div align="center" class="csssalign">
                    <div class="form-group">
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">@</span>
                            <input type="text" name="username" class="form-control Username" placeholder="Username"
                                aria-describedby="basic-addon1" value="{{ old('username') }}">
                        </div>
                        <span style="color:red">
                            @error('username')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <input type="text" name="name" class="form-control name" placeholder="Employee Name"
                                aria-describedby="basic-addon2" value="{{ old('name') }}">
                            <span class="input-group-addon" id="basic-addon2">Full Name <span
                                    style="color: red;">*</span></span>
                        </div>
                        <span style="color:red">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control password" placeholder="Password"
                                aria-describedby="basic-addon2">
                            <span class="input-group-addon" id="basic-addon2">Password <span
                                    style="color: red;">*</span></span>
                        </div>
                        <span style="color:red">
                            @error('password')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <input type="date" name="joined_date" class="form-control joined_date"
                                aria-describedby="basic-addon5" value="{{ old('joined_date') }}">
                            <span class="input-group-addon" id="basic-addon5">Joining Date <span
                                    style="color: red;">*</span></span>
                        </div>
                        <span style="color:red">
                            @error('joined_date')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <input type="text" name="email" class="form-control email" placeholder="Email"
                                aria-describedby="basic-addon5" value="{{ old('email') }}">
                            <span class="input-group-addon" id="basic-addon5">Email</span>
                        </div>
                        <span style="color:red">
                            @error('email')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <select name="location" class="form-control location">
                                <option selected value="">Branch</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->location }}({{ $location->company }})</option>
                                @endforeach
                            </select>
                            <span class="input-group-addon" id="basic-addon5">Location <span
                                    style="color: red;">*</span></span>
                            <?php if ($locations == '[]') { ?>
                            <span style="color:red">Create a branch first</span>
                            <br>
                            <?php } ?>
                        </div>

                        <span style="color:red">
                            @error('location')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div ALIGN="RIGHT">
                            <button type="submit" class="btn btn-primary submituser" id="usersubmitBtn">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

</body>

</html>

<script type="text/javascript">
    var currentBoxNumber = 0;

    $(".Username").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.name");
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
    $(".name").keydown(function(event) {
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
    $(".joined_date").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.email");
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
    $(".email").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("select.location");
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
    $(".location").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("button.submituser");
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
        $('#createuser').keypress(function(e) { //use form id
            if (e.which == 13) {
                validateSearch(); //-- to validate form
                $('#createuser').submit(); // use form id
                return false;
            }
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("createuser");
        const submitBtn = document.getElementById("usersubmitBtn");

        form.addEventListener("submit", function(e) {
            // Prevent the form from submitting multiple times
            submitBtn.disabled = true;
            submitBtn.innerText = "Submitting...";

            // Allow the form to submit normally
            return true;
        });
    });
</script>
