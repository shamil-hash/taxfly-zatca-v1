<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bills</title>
    @include('layouts/usersidebar')
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
        <div style="margin-top:15px;margin-left:15px;">

            @include('navbar.billingdesknavbar')
            </div>
                @else
        <x-logout_nav_user />
    @endif
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Ledger</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bills</li>
            </ol>
        </nav>
        <h2>
            {{$supplier_name}}
        </h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Bill No</th>
                    <th width="20%">Created At</th>
                    <th>Total Amount</th>
                    <th>Desk User ID</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php $number = 1; ?>
                <?php $collectiontotal = 0; ?>
                @foreach($purchases as $purchase)
                <tr>
                        <td>{{ $number }}</td>
                        <td>{{ $purchase->reciept_no }}</td>
                        <!-- {{-- <td>{{ $purchase->product_name }}</td>
                        <td>
                            @if ($purchase->is_box_or_dozen == 1)
                                Box
                            @else
                                Dozen
                            @endif
                        </td>
                        <td>{{ $purchase->box_dozen_count }}</td>
                        <td>{{ $purchase->quantity }}</td> --}} -->
                        <td> {{ date('d M Y | h:i:s A', strtotime($purchase->created_at)) }}</td>
                        <td><b>{{ $currency }}</b> {{ $purchase->price - $purchase->discount }}</td>
                        <td>UID{{ $purchase->user_id }}</td>
                        <td>
                            <a href="/purchasedetails/{{ $purchase->reciept_no }}" class="btn btn-primary"
                                title="View Purchased Products">View</a>
                        </td>
                    </tr>
                <?php $number++; ?>
                @endforeach
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td>Total Purchase Credit Amount</td>
                    <td><b>{{ $currency }}</b> {{ $total_due }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td>Total Given Credit Amount</td>
                    <td><b>{{ $currency }}</b> {{ $paid }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td>Balance Credit Amount</td>
                    <td> <b>{{ $currency }}</b> {{ number_format($total_due - $paid, 3) }}</td>
                </tr>
                {{-- <tr>
                    <td colspan="4"></td>
                    <td>Given Amount</td>
                    <td>
                        <button class="btn btn-primary suppliercreditfundbtn btn-sm" onclick="javascript: return false;" value="{{$supplier_id}}" id="add_supplierfund">PAYMENT</button>
                    </td>
                </tr> --}}
            </tbody>
        </table>
    </div>

</body>
@include('modal.addfund_supplier')

</html>
<script>
    $(document).ready(function() {
        $(document).on('click', '.suppliercreditfundbtn', function() {
            var userid = $(this).val();
            console.log(userid);
            fetchfundhistory();

            function fetchfundhistory() {
                $.ajax({
                    type: 'get',
                    url: '/getfundhistory/' + userid,
                    success: function(data) {
                        var suppliername = (data.suppliername);
                        // console.log(data.suppliername);
                        var dueamount = (data.due);
                        console.log(data.due);
                        $('#AddSupplierfund').modal('show');
                        $('#fundsuppliername').val(suppliername);
                        $('#supplierid').val(userid);
                        $('#dueamount').val(dueamount.toFixed(3));
                    }
                });
            }
        });
    });
</script>
