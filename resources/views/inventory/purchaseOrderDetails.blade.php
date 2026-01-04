<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    @if ($page == 'purchase_order')
        <title>Purchase Order Details</title>
    @endif

    @include('layouts/usersidebar')
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1.5px solid black;
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
        <!-- content -->

        @if ($page == 'purchase_order')
            <h2>Purchase Order Details</h2>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Buying Cost</th>
                    <th>Date and Time</th>
                    <th>Price without {{$tax}}</th>
                    <th>Value Added Tax</th>
                    <th>Price with {{$tax}}</th>
                    <!--<th>Edit</th>-->

                </tr>
            </thead>
            <tbody>
                @foreach ($details as $detail)
                    <tr>
                        <td>
                            {{ $detail->product_name }}
                        </td>
                        <td>
                            {{ $detail->quantity }}
                        </td>
                        <td>
                            {{ $detail->unit }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $detail->buycost }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($detail->created_at)->format('d-M-Y | h:i:s A') }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $detail->price_without_vat }}
                        </td>
                        <td>



                            {{ $detail->price - $detail->price_without_vat }}

                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $detail->price }}
                        </td>
                @endforeach
                </tr>
            </tbody>
        </table>
        <!-- content end -->
    </div>
</body>

</html>
