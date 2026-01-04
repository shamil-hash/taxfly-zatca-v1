<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Return Transactions</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">

    @if (Session('adminuser'))
        @include('layouts/adminsidebar')
    @elseif(Session('softwareuser'))
        @include('layouts/usersidebar')
    @endif

    <style>
            table {
    border-collapse: collapse;
    width: 100%;
    font-family: Arial, sans-serif;
    
}

th, td {
    border: 1px solid #e0e0e0;
    padding: 10px 12px;
    
}

th {
    background: #187f6a;
    color: white;
}

tr:hover {
    background: #f0faf8; /* Very light teal */
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
         .btn-primary{
            background-color: #187f6a;
            color: white;
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
      

        @if (Session('adminuser'))
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
        @elseif(Session('softwareuser'))
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Billing Desk</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Return History</li>
                </ol>
            </nav>
        @endif

        <!-- content -->
        <h2>Sales Return History</h2>

        <table id="example" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Total Price</th>
                    <th>Discount <br /> amount</th>
                    <th>Grand Total <br /> (w/. discount)</th>
                    <th>{{$tax}}</th>
                    <th>Date and Time</th>
                    @if (Session('adminuser'))
                        <th>Branch</th>
                    @endif
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                    $vat = 0;
                    $grand_total = 0;
                    $discount = 0;
                    $grand_with_discount = 0;
                @endphp
                @foreach ($products as $product)
                    <tr>
                        <td>
                            {{-- @if ($product->vat_type == 1)
                                <span class="btn btn-warning"
                                    style="font-size: 8px;background-color: #766dc0;border:none;">Inclusive</span>&nbsp;{{ $product->transaction_id }}
                            @else
                                <span class="btn btn-info"
                                    style="font-size: 8px;background-color: #f5a875;border:none;">Exclusive</span>&nbsp;{{ $product->transaction_id }}
                            @endif --}}

                            {{ $product->transaction_id }}
                        </td>
                        <td>
                            @if ($product->grandtotal_without_discount != '')
                                <b>{{ $currency }}</b> {{ $product->grandtotal_without_discount }}
                            @elseif ($product->grandtotal_without_discount == '')
                                <b>{{ $currency }}</b> {{ $product->sum }} + {{ $product->vat }}
                            @endif
                        </td>
                        <td>
                            @if ($product->discount_amount != '')
                                <b>{{ $currency }}</b> {{ number_format($product->discount_amount, 3) }}
                            @endif
                        </td>
                        <td>
                            @if ($product->vat_type == 1)
                                @if (!is_null($product->grandtotal_without_discount))
                                    <b>{{ $currency }}</b>
                                    {{ number_format($product->grandtotal_without_discount - $product->discount_amount, 3) }}
                                @else
                                    <b>{{ $currency }}</b>
                                    {{ number_format($product->sum * ($product->quantity - $product->discount_amount), 3) }}
                                @endif
                            @else
                                <b>{{ $currency }}</b> {{ $product->sum }}
                            @endif

                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $product->vat }}
                        </td>
                        <td>
                            {{ date('d M Y | h:i:s A', strtotime($product->created_at)) }}
                        </td>
                        <!-- {{-- <td>{{ $product->phone }}</td> --}} -->
                        @if (Session('adminuser'))
                            <td> {{ $product->branch }}</td>
                        @endif
                        <td>
                            @if (Session('softwareuser'))
                                <a href="/return_transaction/{{ $product->transaction_id }}/{{ $product->created_at }}"
                                    class="btn btn-primary">VIEW</a>
                                <a href="/return-pdf/{{ $product->transaction_id }}/{{ $product->return_id }}"
                                    class="btn btn-primary">Download PDF</a>
                            @endif

                            @if (Session('adminuser'))
                                <a href="/returnreportdetails/{{ $product->transaction_id }}/{{ $product->created_at }}"
                                    class="btn btn-primary">VIEW</a>
                            @endif
                        </td>
                    </tr>

                    @php
                        $total +=
                            $product->grandtotal_without_discount != ''
                                ? $product->grandtotal_without_discount
                                : $product->sum + $product->vat;

                        $discount += $product->discount_amount;
                        $grand_total +=
                            $product->vat_type == 1
                                ? ($product->grandtotal_without_discount != ''
                                    ? $product->grandtotal_without_discount - $product->discount_amount
                                    : $product->sum * ($product->quantity - $product->discount_amount))
                                : $product->sum;

                        $vat += $product->vat ?? 0;
                    @endphp
                @endforeach

            </tbody>

            <tr style="font-weight: bold;font-size:16px;">
                <td colspan="1" class="total">Total</td>
                <td class="total" id="ttt"><b>{{ $currency }}</b> {{ number_format($total, 3) }}</td>
                <td class="total" id="dddd"><b>{{ $currency }}</b> {{ number_format($discount, 3) }}</td>
                <td class="total" id="gttt"><b>{{ $currency }}</b> {{ number_format($grand_total, 3) }}
                </td>
                <td class="total" id="vttt"><b>{{ $currency }}</b> {{ number_format($vat, 3) }}</td>
                <td colspan="2"></td>
            </tr>
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

                    var grandTotalDisValue = parseFloat(d[3].replace(/<\/?b>/g, '').replace(
                        /[^0-9.-]/g, '')) || 0;

                    var vatValue = parseFloat(d[4].replace(/<\/?b>/g, '')
                        .replace(/[^0-9.-]/g, '')) || 0;

                    total += totalValue;
                    discount += discountValue;
                    vat += vatValue;
                    grandTotal += grandTotalDisValue;
                });

                // Update the displayed totals
                $('#ttt').html('<b>{{ $currency }}</b> ' + total.toFixed(3));
                $('#dddd').html('<b>{{ $currency }}</b> ' + discount.toFixed(3));
                $('#gttt').html('<b>{{ $currency }}</b> ' + grandTotal.toFixed(3));
                $('#vttt').html('<b>{{ $currency }}</b> ' + vat.toFixed(3));
            });
        }
    });
</script>
