<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Supplier Purchase Products</title>
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

        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
            height: 4.5rem;
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

        .total {
            font-weight: bold;
            background-color: white;
            font-size: large;
        }

        .pr-2 {
            padding-right: 2rem !important;
        }

        .formdrop {
            display: flex;
            justify-content: end;
            margin-right: -5rem;
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
        <div style="margin-left:15px;margin-top:15px;">

            @include('navbar.suppnav')
        </div>
        @else
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

                        @php
                            $logoutUrl = Session('adminuser')
                                ? '/adminlogout'
                                : (Session('softwareuser')
                                    ? '/userlogout'
                                    : '/');
                        @endphp

                        <li><a href="{{ $logoutUrl }}">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>@endif
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
        <h2>Stock Purchase Report</h2>
        <br />

        <table class="table" id="example">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Buying Cost</th>
                    <th>Date and Time</th>
                    <th>Price without {{$tax}}</th>
                    <th>{{$tax}} (%)</th>
                    <!-- <th>Value Added Tax</th> -->
                </tr>
            </thead>

            <tbody>

                @foreach ($view_products as $products)
                    <tr>
                        <td>{{ $products->product_name }}</td>
                        <td>{{ $products->quantity }}</td>
                        <td>{{ $products->unit }}</td>
                        <td><b>{{ $currency }}</b> {{ $products->buycost }}</td>
                        <td> {{ date('d M Y | h:i:s A', strtotime($products->created_at)) }}</td>
                        <td><b>{{ $currency }}</b> {{ $products->price_without_vat }}</td>
                        <td>
                            @if ($products->vat != null)
                                <b>{{ $currency }}</b> {{ $products->vat }}


                                <!-- {{-- {{ ($products->price_without_vat * $products->vat) / 100 }} --}} -->
                            @elseif ($products->vat == null)
                                <span class='badge badge-default'>NA</span>
                            @endif
                        </td>

                    </tr>
                @endforeach


            </tbody>
        </table>
    </div>

</body>

</html>

<!-- {{-- <script>
    function doDisplay(x) {
        var payment_mode = x;
        console.log(payment_mode)
        var supplier = document.getElementById('supplier').value;
        console.log(supplier);

        $.ajax({
            type: "GET",
            url: "/getfilterpayment/" + supplier + '/' + payment_mode,
            dataType: "json",
            success: function(response) {
                console.log(response.filtereddata);
                $part = "";
                $part2 = "";

                $.each(response.filtereddata, function(key, item) {
                    console.log(item.created_at);
                    var date = new Date(item.created_at);

                    var dateformated = date.toISOString().substring(0, 19).replace("T", " ");

                    if (item.payment_mode == 1) {

                        var x = Number(item.payment_mode);
                        var pay = x.toString().replace('1', 'Cash');


                    } else if (item.payment_mode == 2) {

                        var x = Number(item.payment_mode);
                        var pay = x.toString().replace('2', 'Credit');

                    }

                    $part = $part + '<tr><td>' + dateformated + '</td>\
                        <td>\
                        ' + item.reciept_no + '</td><td>\
                        ' + item.product_name + '</td><td><b>\
                        ' + pay + '</b></td><td>\
                        ' + item.price + '</td></tr>'
                });

                markup = "<tr><td colspan='4' class='total'>Total Price</td><td class='total'>" + response
                    .totalprice.total + "</td></tr>";

                $("#suppliersales").html($part);
                $("#suppliersales").append(markup);
            }
        });
    }
</script> --}} -->

<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            order: [

            ]
        });
    });
</script>
