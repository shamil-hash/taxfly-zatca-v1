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
        <div align="center">
            @foreach($shopdatas as $shopdata)
            {{$shopdata['name']}}
            <br>
            Phone No:{{$shopdata['phone']}}
            <br>
            Email:{{$shopdata['email']}}
            <br>
            <br>
            @endforeach
        </div>
        <h2>Return Report</h2>
        <div class="row">
            <div class="col-md-12">
                <table id="example" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Total Price</th>
                            <th>{{$tax}}</th>
                            <th>Grand Total<br>(including {{$tax}})</th>
                            <th>Date and Time</th>
                            <th>Branch</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                {{$product['transaction_id']}}
                            </td>
                            <td>
                                    <b>{{ $currency }}</b> {{ $product['sum'] }}
                                </td>
                                <td>
                                    <b>{{ $currency }}</b> {{ $product['vat'] }}
                                </td>
                                <td>
                                    <b>{{ $currency }}</b> {{ $product['vat'] + $product['sum'] }}
                                </td>
                                <td>
                                    {{ date('d M Y | h:i:s A', strtotime($product['created_at'])) }}

                                </td>
                            <td>
                                {{$product['branch']}}
                            </td>
                            <td>
                                <a href="/returnreportdetails/{{$product['transaction_id']}}">VIEW</a>
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>

</html>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            order: []
        });
    });
</script>
