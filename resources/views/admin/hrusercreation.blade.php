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
            margin-top: 30px;
            margin-right: 70px;
            margin-left: 70px;
            background-color: #ebe9e9;
            padding: 30px;
            border-color: #ddd;
            border-width: 1px;
            border-radius: 4px 4px 0 0;
            -webkit-box-shadow: none;
            box-shadow: none;
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
        <ul class="nav nav-tabs">
            <li role="presentation"><a href="/usercreationrequests">User Creation Requests<span
                        class="badge">{{ $count }}</span></a></li>
            <li role="presentation" class="active"><a href="#">User Create</a></li>
        </ul>
        <br>
        <form action="/hrusercreate" method="POST">
            @csrf
            <h2>Create User </h2>
            <div>
                <div>
                    <div class="form-group">
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">@</span>
                            <input type="text" name="username" class="form-control" placeholder="Username"
                                aria-describedby="basic-addon1" value="{{ $user_name }}">
                        </div>
                        <span style="color:red">
                            @error('username')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" placeholder="Employee Name"
                                aria-describedby="basic-addon2" value="{{ $full_name }}">
                            <span class="input-group-addon" id="basic-addon2">Full Name</span>
                        </div>
                        <span style="color:red">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" placeholder="Password"
                                aria-describedby="basic-addon2">
                            <span class="input-group-addon" id="basic-addon2">Password</span>
                        </div>
                        <span style="color:red">
                            @error('password')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <input type="text" name="joined_date" class="form-control"
                                aria-describedby="basic-addon5" value="{{ $joining_date }}">
                            <span class="input-group-addon" id="basic-addon5">Joining Date</span>
                        </div>
                        <span style="color:red">
                            @error('joined_date')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <input type="text" name="email" class="form-control" placeholder="Email"
                                aria-describedby="basic-addon5" value="{{ $email }}">
                            <span class="input-group-addon" id="basic-addon5">Email</span>
                        </div>
                        <span style="color:red">
                            @error('email')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <select name="location" class="form-control">
                            <option selected value="{{ $branch }}">{{ $branch_name }}</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->location }}</option>
                            @endforeach
                        </select>
                        <br>
                        <h4>Privileges</h4>
                        <!-- Billing  -->
                        @foreach ($privileges as $privilege)
                            <?php if ($privilege->role_id == '1') { ?>
                            <input type="checkbox" id="bill" name="role[]" value="1" checked>
                            <label for="check">Billing Desk</label>
                            <br>
                            Billing
                            <br>
                            Return
                            <br>
                            Return History
                            <br>
                            Transaction History
                            <br>
                            <?php } ?>
                        @endforeach
                        <!-- inventory -->
                        @foreach ($privileges as $privilege)
                            <?php if ($privilege->role_id == '2') { ?>
                            <input type="checkbox" id="inventory" name="role[]" value="2" checked>
                            <label for="inventory">Inventory</label>
                            <br>
                            Add Product
                            <br>
                            Stock
                            <br>
                            Purchase
                            <br>
                            Purchase History
                            <br>
                            <?php } ?>
                        @endforeach
                        <!-- Analytics -->
                        @foreach ($privileges as $privilege)
                            <?php if ($privilege->role_id == '3') { ?>
                            <br>
                            <input type="checkbox" id="analytics" name="role[]" value="3" checked>
                            <label for="analytics">Analytics</label>
                            <?php } ?>
                        @endforeach
                        <!-- Customer Support  -->
                        @foreach ($privileges as $privilege)
                            <?php if ($privilege->role_id == '4') { ?>
                            <br>
                            <input type="checkbox" id="customersupport" name="role[]" value="4" checked>
                            <label for="customer support">Customer Support</label>
                            <?php } ?>
                        @endforeach
                        <!-- Manager  -->
                        @foreach ($privileges as $privilege)
                            <?php if ($privilege->role_id == '5') { ?>
                            <br>
                            <input type="checkbox" id="manager" name="role[]" value="5">
                            <label for="manager">Manager</label>
                            <?php } ?>
                        @endforeach
                        <!-- Marketing  -->
                        @foreach ($privileges as $privilege)
                            <?php if ($privilege->role_id == '6') { ?>
                            <br>
                            <input type="checkbox" id="marketing" name="role[]" value="6" checked>
                            <label for="marketing">Marketing</label>
                            <?php } ?>
                        @endforeach
                        <!-- Team Leader  -->
                        @foreach ($privileges as $privilege)
                            <?php if ($privilege->role_id == '7') { ?>
                            <br>
                            <input type="checkbox" id="teamleader" name="role[]" value="7" checked>
                            <label for="teamleader">Team Leader</label>
                            <?php } ?>
                        @endforeach
                        <!-- HR  -->
                        @foreach ($privileges as $privilege)
                            <?php if ($privilege->role_id == '8') { ?>
                            <input type="checkbox" id="hr" name="role[]" value="8" checked>
                            <label for="hr">hr</label>
                            <br>
                            <?php } ?>
                        @endforeach
                        <!-- ACCOUNTANT -->
                        @foreach ($privileges as $privilege)
                            <?php if ($privilege->role_id == '9') { ?>
                            <input type="checkbox" id="accountant" name="role[]" value="9" checked>
                            <label for="accountant">Accountant</label>
                            <br>
                            <?php } ?>
                        @endforeach
                        <!-- BILLING TOUCH -->
                        @foreach ($privileges as $privilege)
                            <?php if ($privilege->role_id == '10') { ?>
                            <input type="checkbox" id="billingtouch" name="role[]" value="10" checked>
                            <label for="billingtouch">Billing Cafteria</label>
                            <br>
                            <?php } ?>
                        @endforeach
                        <!-- CREDIT -->
                        @foreach ($privileges as $privilege)
                            <?php if ($privilege->role_id == '11') { ?>
                            <input type="checkbox" id="credit" name="role[]" value="11" checked>
                            <label for="credit">CREDIT</label>
                            <br>
                            <?php } ?>
                        @endforeach
                        <div ALIGN="RIGHT">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

</body>

</html>
