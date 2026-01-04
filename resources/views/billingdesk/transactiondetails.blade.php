<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Transactions</title>

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
      
        <!-- content -->
        <h2>Transactions Details</h2>

        <table class="table" id="example">
            <thead>
                <tr>
                    <th>Date and Time</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Rate</th>
                    <th>Total Price</th>
                    <th>Discount <br /> amount</th>
                    <th>{{$tax}}</th>
                    <th>Total w/. {{$tax}} <br /> (w/. discount)</th>
                        @if (Session('softwareuser'))
                    @foreach ($users as $user)
                    <?php if ($user->role_id == '28') { ?>
                    <th>credit Note</th>
                    <?php } ?>
                    @endforeach
                    @endif
                                </tr>
            </thead>
            <tbody>
                @foreach ($details as $detail)
                    <tr>
                        <td>{{ date('d M Y | h:i:s A', strtotime($detail['created_at'])) }}</td>
                        <td>{{ $detail['product_name'] }}</td>
                        <td>{{ $detail['quantity'] }}</td>
                        <td>{{ $detail['unit'] }}</td>
                        <td><b>{{ $currency }}</b> {{ $detail['netrate'] }} </td>
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
                        <td>
                            <b>{{ $currency }}</b> {{ $detail['vat_amount'] }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $detail['total_amount'] }}
                        </td>
                            @if (Session('softwareuser'))
                        @foreach ($users as $user)
                        <?php if ($user->role_id == '28') { ?>
                        <td><b>{{ $currency }}</b> {{ $detail['credit_note_amount'] }} </td>
                        <?php } ?>
                        @endforeach
                        @endif
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
