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
        @include('navbar.billingdesknavbar')
    @else
        <x-logout_nav_user />
    @endif
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Billing Desk</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Transaction</li>
            </ol>
        </nav>
        <form action="/edittransactionsdate" method="get">
            <div class="row">
                <div class="col-sm-6">
                    <h4>SELECT DATES
                    </h4>
                    <div class="row">
                        <div class="col-sm-5">
                            From
                            <input type="date" class="form-control" value="{{$start_date}}" name="start_date">
                        </div>
                        <div class="col-sm-5">
                            To
                            <input type="date" class="form-control" value="{{$end_date}}" name="end_date">
                        </div>
                        <div class="col-sm-2">
                            <br>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <br>
        <!-- content -->
        <h2>Transactions</h2>

        <table id="example" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th width="10%">Transaction ID</th>
                    <th width="10%">Total Price</th>
                    <th width="8%">{{$tax}}</th>
                    <th width="10%">Grand Total<br>(including {{$tax}})</th>
                    <th width="10%">Date and Time</th>
                    <th width="10%">Customer Name</th>
                    <th width="10%">Payment Type</th>
                    <th width="5%">Edit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        {{$product->transaction_id}}
                    </td>
                    <td>
                        {{$product->sum-$product->vat}}&nbsp {{$currency}}
                    </td>
                    <td>
                        {{$product->vat}}&nbsp {{$currency}}
                    </td>
                    <td>
                        {{$product->sum}}&nbsp {{$currency}}
                    </td>
                    <td>
                        {{$product->created_at}}
                    </td>
                    <td>
                        {{$product->customer_name}}
                    </td>
                    <td>
                        {{$product->payment_type}}
                    </td>
                    <td>
                        <a href="/edittransactiondetails/{{$product->transaction_id}}" class="btn btn-primary">Edit</a>
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            order: [
                [0, 'asc']
            ]
        });
    });
</script>
