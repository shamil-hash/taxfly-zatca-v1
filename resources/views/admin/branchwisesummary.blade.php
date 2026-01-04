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
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Reports</a></li>
                <li class="breadcrumb-item active" aria-current="page">Branchwise Summary</li>
            </ol>
        </nav>
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
        <h1><b>Branchwise Summary :</b></h1>
        <br>
        <h3>Account Summary</h3>
        <br>

        <table class="table">
            <thead>
                <tr>
                    <th>Branch</th>
                    {{-- <th>Total Price</th>
                    <th>Total Vat</th>
                    <th>Grand Total w/o Discount</th>
                    <th>Discount</th>
                    <th>Grand Total w/. Discount</th> --}}
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>
                            {{ $product->location }}
                        </td>
                        {{-- <td>

                            <b>{{ $currency }}</b>
                            {{ $product->total_amount + $product->discount_amount - $product->vat }}

                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $product->vat }}
                        </td>
                        <td>

                            <b>{{ $currency }}</b> {{ $product->total_amount + $product->discount_amount }}

                        </td>
                        <td>{{ $product->discount_amount }}</td>
                        <td>{{ $product->total_amount }}</td> --}}
                        <td>
                            <a href="/branchdat/{{ $product->locid }}">VIEW</a>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>

        <br>
        <h3>User Summary</h3>
        <br>

        <table class="table">
            <thead>
                <tr>
                    <th width="6%">Designation</th>
                    <th width="5%">Number of Users</th>
                    <!-- <th>Users Online</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach ($employees as $employee)
                    <tr>
                        <td>
                            {{ $employee->location }}
                        </td>
                        <td>
                            {{ $employee->count }}
                        </td>
                        <!-- <td>
        {{ $employee->online }}
        </td>    -->
                @endforeach
                </tr>
            </tbody>
        </table>

    </div>

</body>

</html>
