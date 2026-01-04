<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Transactions</title>

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
            background-color: #20639B;
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
        <div style="margin-left:15px;margin-top:15px;">

            @include('navbar.billingdesknavbar')
        </div>
            @else
        <x-logout_nav_user />
    @endif
        <!-- content -->
        <h2>Transactions</h2>

        <table class="table" id="example">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Rate</th>
                    <th>Date and Time</th>
                    <th>Total Price</th>
                    <th>Discount <br /> amount</th>
                    <th>Total w/. {{$tax}} <br /> (w/. discount)</th>
                    <th>{{$tax}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details as $detail)
                    <tr>
                        <td>
                            {{ $detail['product_name'] }}
                        </td>
                        <td>
                            {{ $detail['quantity'] }}
                        </td>
                        <td>
                            {{ $detail['unit'] }}
                        </td>
                        <td>{{ $detail['netrate'] }} </td>
                        <td>
                            {{ date('d M Y | h:i:s A', strtotime($detail['created_at'])) }}
                        </td>
                        <td>
                            @if ($detail['totalamount_wo_discount'] != '')
                                <b>{{ $currency }}</b> {{ $detail['totalamount_wo_discount'] }}
                            @elseif ($detail['totalamount_wo_discount'] == '')
                                <b>{{ $currency }}</b> {{ $detail['total_amount'] }}
                            @endif
                        </td>
                        <td>
                            @if ($detail['discount_amount'] != '')
                                <b>{{ $currency }}</b> {{ $detail['discount_amount'] * $detail['quantity'] }}
                            @endif
                        </td>
                        <td><b>{{ $currency }}</b> {{ $detail['total_amount'] }}</td>
                        <td>
                            <b>{{ $currency }}</b> {{ $detail['vat_amount'] }}
                        </td>
                @endforeach
                </tr>
            </tbody>
        </table>
        <!-- content end -->

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
