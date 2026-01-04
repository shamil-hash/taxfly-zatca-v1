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
                    <li class="breadcrumb-item active" aria-current="page">Credit Note History</li>
                </ol>
            </nav>
        @endif

        <!-- content -->
        <h2>Credit Note History</h2>

        <table id="example" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Credit Note No.</th>
                    <th>Transaction ID</th>
                    <th>Date and Time</th>
                    {{-- <th>Total Price</th> --}}
                    {{-- <th>Discount <br /> amount</th> --}}
                    {{-- <th>Return <br /> amount</th> --}}
                    <th>Credit Note Amount</th>
                    {{-- <th>{{$tax}}</th> --}}
                    {{-- <th>Grand Total <br /> (w/. discount)</th> --}}
                    <th>Customer</th>
                    {{-- <th>Payment</th> --}}
                    <th>Phone</th>
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
                    $credit_note_amount = 0;
                    @endphp
                @foreach ($products as $product)
                {{-- @php
                     $return_amount = $product->vat_type == 1
                                    ? (!is_null($product->return_grandtotal_without_discount)
                                        ? round($product->return_grandtotal_without_discount - $product->return_discount_amount, 2)
                                        : round($product->sum * ($product->quantity - $product->discount_amount), 2))
                                    : round($product->return_sum, 2);
                @endphp --}}
                <tr>
                    <td> {{ $product->credit_note_id }} </td>
                        <td>

                            {{ $product->transaction_id }}
                        </td>
                        <td>
                            {{ date('d M Y | h:i:s A', strtotime($product->created_at)) }}
                        </td>
                        {{-- <td>
                            @if ($product->grandtotal_without_discount != '')
                            <b>{{ $currency }}</b> {{ $product->grandtotal_without_discount }}
                            @elseif ($product->grandtotal_without_discount == '')
                            <b>{{ $currency }}</b> {{ $product->sum }} + {{ $product->vat }}
                            @endif
                        </td> --}}
                        {{-- <td>
                            <b>{{ $currency }}</b> {{ $product->bill_grand_total }}

                        </td> --}}
                        {{-- <td>
                            @if ($product->discount_amount != '')
                            <b>{{ $currency }}</b> {{ number_format($product->discount_amount, 3) }}
                            @endif
                        </td> --}}
                        {{-- <td>
                            @if ($return_amount != 0)
                                <b>{{ $currency }}</b> {{ number_format($return_amount, 3) }}
                            @endif
                        </td> --}}
                        <td>
                            <b>{{ $currency }}</b> {{ $product->credit_note_amount }}
                        </td>
                        {{-- <td>
                            <b>{{ $currency }}</b> {{ $product->vat }}
                        </td> --}}
                        {{-- <td>
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

                        </td> --}}
                        <td>{{ $product->customer }}</td>
                        {{-- <td>
                            @if ($product->payment_type == '1')
                                CASH
                            @elseif ($product->payment_type == '2')
                                BANK
                         @elseif ($product->payment_type == '3')
                                CREDIT
                                @elseif ($product->payment_type == '4')
                                POS CARD
                            @endif
                        </td> --}}
                        <!-- {{-- <td>{{ $product->phone }}</td> --}} -->
                        <td>{{ $product->phone }}</td>


                        <td>
                                <a href="/creditnoteviewdetails/{{ $product->credit_note_id }}"
                                    class="btn btn-primary">VIEW</a>
                                    <a href="/creditnote-pdf/{{ $product->transaction_id }}/{{$product->credit_note_id}}" class="btn btn-primary">Print</a>

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
                        $credit_note_amount += $product->credit_note_amount?? 0;

                    @endphp
                @endforeach

            </tbody>

            {{-- <tr style="font-weight: bold;font-size:16px;">
                <td colspan="3" class="total">Total</td>
                <td class="total" id="ttt"><b>{{ $currency }}</b> {{ number_format($total, 3) }}</td>
                <td class="total" id="dddd"><b>{{ $currency }}</b> {{ number_format($discount, 3) }}</td>
                <td class="total" id="vttt"><b>{{ $currency }}</b> {{ number_format($vat, 3) }}</td>
                <td class="total" id="cttt"><b>{{ $currency }}</b> {{ number_format($credit_note_amount, 3) }}</td>
                <td class="total" id="gttt"><b>{{ $currency }}</b> {{ number_format($grand_total, 3) }}
                </td>
                <td colspan="4"></td>
            </tr> --}}
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
