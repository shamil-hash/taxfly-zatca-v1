<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Credit Note History</title>

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
        <div style="margin-left:15px;margin-top:15px;">
            @include('navbar.billingdesknavbar')
        </div>
            @else
            <x-logout_nav_user />

                @endif

        @if (Session('adminuser'))
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
        @elseif(Session('softwareuser'))
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Billing Desk</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Credit Note Custmer Summary</li>
                </ol>
            </nav>
        @endif

        <!-- content -->
        <h2>Credit Note Customer Summary</h2>

        <table id="example" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th width="50%">Customer Name</th>
                    <th width="50%">Total Credit Note Amount</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->customer_name }}</td>




                        <td>
                            <b>{{ $currency }}</b> {{ $product->credit_note_amount }}
                        </td>


                    </tr>


                @endforeach

            </tbody>


        </table>
        <!-- content end -->
    </div>
</body>

</html>

<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
    // $(document).ready(function() {
    //     $('#example').DataTable({
    //         order: []
    //     });
    // });

    $('#example').DataTable({
        order: [],
        initComplete: function() {
            var table = this.api();

            table.on('search.done', function() {
                // Calculate the new totals based on the filtered data
                var total = 0;
                var vat = 0;
                var grandTotal = 0;
                var discount = 0;
                var creditnote = 0;


                // Loop through the visible rows (filtered data)
                table.rows({
                    search: 'applied'
                }).data().each(function(d) {
                    console.log(d);

                    // Extract numeric values from the HTML-formatted strings
                    var totalValue = parseFloat(d[1].replace(/<\/?b>/g, '').replace(
                        /[^0-9.-]/g, '')) || 0;

                    var discountValue = parseFloat(d[2].replace(/<\/?b>/g, '').replace(
                        /[^0-9.-]/g, '')) || 0;

                    var grandTotalDisValue = parseFloat(d[5].replace(/<\/?b>/g, '').replace(
                        /[^0-9.-]/g, '')) || 0;

                    var vatValue = parseFloat(d[3].replace(/<\/?b>/g, '')
                        .replace(/[^0-9.-]/g, '')) || 0;

                    var creditValue = parseFloat(d[4].replace(/<\/?b>/g, '')
                        .replace(/[^0-9.-]/g, '')) || 0;

                    total += totalValue;
                    discount += discountValue;
                    vat += vatValue;
                    grandTotal += grandTotalDisValue;
                    creditnote += creditValue;

                });

                // Update the displayed totals
                $('#ttt').html('<b>{{ $currency }}</b> ' + total.toFixed(3));
                $('#dddd').html('<b>{{ $currency }}</b> ' + discount.toFixed(3));
                $('#gttt').html('<b>{{ $currency }}</b> ' + grandTotal.toFixed(3));
                $('#vttt').html('<b>{{ $currency }}</b> ' + vat.toFixed(3));
                $('#cttt').html('<b>{{ $currency }}</b> ' + creditnote.toFixed(3));

            });
        }
    });
</script>
