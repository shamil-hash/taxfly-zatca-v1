<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">

    <title>Transactions</title>

    @if (Session('adminuser'))
        @include('layouts/adminsidebar')
    @elseif(Session('softwareuser'))
        @include('layouts/usersidebar')
    @endif

    <style>
        .btn-secondary {
    background-color: #6c757d; /* Gray color */
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: not-allowed;
}
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

        .buttnstyle {
            font-size: 12px;
        }
    </style>
    <style>
        .dropdown-menu {
            z-index: 1000;
        }

        .dropdown-menu>li>a {
            padding: 10px 20px;
            display: flex;
            align-items: center;
            color: #333;
            text-decoration: none;
        }

        .dropdown-menu>li>.no-edit {
            padding: 10px 20px;
            /* Same padding as the other items */
            display: block;
            color: #333;
        }

        .dropdown-menu>li>a>i {
            margin-right: 10px;
        }

        .dropdown-menu>li>a:hover,
        .dropdown-menu>li>.no-edit:hover {
            background-color: #f5f5f5;
            /* Change this color as needed */
            color: #333;
            /* Change this color as needed */
        }

        .dropdown-menu .divider {
            margin: 0;
        }

        .dropdown-toggle {
            padding: 5px 10px;
            /* Reduced padding for the outer button */
        }

        .dropdown-toggle .glyphicon {
            margin-right: 5px;
        }

        .dropdown-menu>li>a:hover,
        .dropdown-menu>li>.no-edit:hover {
            color: #fff;
            background-color: #187f6a;
        }

        /* Style for the red "Edit" button */
        .editLink {
            background-color: #d9534f;
            /* Bootstrap's 'btn-danger' color */
            color: #fff !important;
            /* Ensure the text color is white */
            padding: 10px 20px;
            display: block;
            text-align: center;
        }

        .editLink:hover {
            background-color: #c9302c;
            /* Darker shade of red */
            color: #fff !important;
            /* Ensure the text color remains white on hover */
        }

        .js-user {
            width: 100%;
            margin-bottom: 10px;
            /* Adjust as needed */
        }

        h4.credit {
            margin-top: -3rem;
        }

        .select2-container .select2-choice {
            height: 35px;
            line-height: 35px;
        }

        ul.select2-results {
            max-height: 100px;
        }
              .filter-section {
        margin-bottom: 20px;
    }
    .filter-header {
        margin-bottom: 1rem;
        color: #187f6a;
        font-weight: bold;
    }

    .filter-section {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 5px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .filter-group {
        display: flex;
        flex-direction: column;
    }
    .filter-label {
        margin-bottom: 5px;
    }
    .filter-control {
        width: 80%;
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
                    <li class="breadcrumb-item active" aria-current="page">Transaction History</li>
                </ol>
            </nav>
            @endif
            <input type="hidden" id="page" id="page" value="edit_bill">

        <form action="/billingsidetransactions" method="get">
            <div class="row filter-section">

                <div class="col-sm-7">
                    <h4>SELECT DATES</h4>
                    <div class="row">
                        <div class="col-sm-4">
                            From
                            <input type="date" class="form-control" value="{{ $start_date }}" name="start_date">
                        </div>
                        <div class="col-sm-4">
                            To
                            <input type="date" class="form-control" value="{{ $end_date }}" name="end_date">
                        </div>
                        @if (Session('softwareuser'))
                            @foreach ($users as $user)
                                <?php if ($user->role_id == '11') { ?>
                                <div class="col-sm-3">
                                    <h4 class="credit">Customer </h4>
                                    <br />

                                    <select class="js-user" id="credit_user_id" name="credit_user_id">
                                        <option value="">Select Customer</option>
                                        @foreach ($creditusers as $credituser)
                                            <option value="{{ $credituser->id }}"
                                                @if ($credituser->id == $credit_user_id) selected @endif>
                                                {{ $credituser->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <?php } ?>
                            @endforeach
                        @endif
                    </div>
                      </div>
              <div class="col-sm-2">
                    <br /><br /><br />
                    <button style="margin-top:6px;margin-left:-60px;background-color:#187f6a;" type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <!-- Payment Type/Export Form -->


                <form method="GET" action="{{ url('/transactions') }}">
            <div class="row filter-section align-items-end g-2">
                <!-- Payment Type Filter -->
                <div class="col-lg-2 col-md-3 col-sm-4 col-12">
                    <label for="payment_type" class="filter-label">Payment Type</label>
                    <select class="form-control" id="payment_type" name="payment_type" onchange="this.form.submit()">
                        <option value="">All Payment Types</option>
                        <option value="Cash" @if($payment_type == 'Cash') selected @endif>Cash</option>
                        <option value="Credit" @if($payment_type == 'Credit') selected @endif>Credit</option>
                        <option value="POS Card" @if($payment_type == 'POS Card') selected @endif>POS Card</option>
                        <option value="Bank" @if($payment_type == 'Bank') selected @endif>Bank</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div class="col-lg-2 col-md-3 col-sm-4 col-12">
                    <label for="date_filter" class="filter-label">Date Filter</label>
                    <select class="form-control" id="date_filter" name="date_filter" onchange="this.form.submit()">
                        <option value="all" @if($date_filter == 'all') selected @endif>All Sales</option>
                        <option value="today" @if($date_filter == 'today') selected @endif>Today</option>
                    </select>
                </div>

                <!-- Export Button -->
                <div class="col-lg-1 col-md-2 col-sm-4 col-12" style="padding-top: 2.3rem;">
                    <a href="{{ url('/transactions/export') }}?payment_type={{ $payment_type }}&date_filter={{ $date_filter }}"
                       class="btn btn-primary w-100" style="background-color:#187f6a;">
                        Export
                    </a>
                </div>
            </div>
        </form>

        <br>
        <!-- content -->
        <h2>Transactions</h2>

        <table id="example" class="table table-striped table-bordered table-responsive" style="width:100%;background-color:white;">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Date and Time</th>
                    <th>Total Price</th>
                    <th>Discount <br /> amount</th>
                    <th>Grand Total <br /> (w/. discount)</th>
                    <th>{{$tax}}</th>
                    <th>Return <br /> Amount</th>
                    @if (Session('softwareuser'))
                     @foreach ($users as $user)
                    <?php if ($user->role_id == '28') { ?>
                    <th>Credit Note</th>
                    <?php } ?>
                    @endforeach
                    @endif
                    <th>Total <br /> (Grand Total - Return
                        @if (Session('softwareuser'))
                        @foreach ($users as $user)
                        <?php if ($user->role_id == '28') { ?>
                        - Credit Note
                        <?php } ?>
                        @endforeach
                        @endif
                        )</th>
                    <th>Customer</th>
                    <th width="5%">Payment Type</th>
                    <th class="hide"></th>
                    <!--<th>Credit User</th>-->
                    <th>Phone</th>
                    @if (Session('adminuser'))
                        <th>Branch</th>
                        <th style="display:none;">Approve</th>
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
                    $total_return_amount = 0;
                    $grand_note=0;
                    $grandtotal_after_return = 0;
                @endphp
                @foreach ($products as $product)
                    @php
                        $grand_total_with_discount =
                            $product->vat_type == 1
                                ? (!is_null($product->grandtotal_without_discount)
                                    ? $product->grandtotal_without_discount - $product->discount_amount
                                    : $product->sum * ($product->quantity - $product->discount_amount))
                                : $product->sum;
                                $return_amount = $product->vat_type == 1
                                    ? (!is_null($product->return_grandtotal_without_discount)
                                        ? round($product->return_grandtotal_without_discount - $product->return_discount_amount, 2)
                                        : round($product->sum * ($product->quantity - $product->discount_amount), 2))
                                    : round($product->return_sum, 2);
                        $total_after_return = $grand_total_with_discount - $return_amount - $product->credit_note_amount;
                    @endphp
                    <tr>
                        <td>

                            {{ $product->transaction_id }}
                        </td>
                        <td>{{ date('d M Y | h:i:s A', strtotime($product->created_at)) }}</td>
                        <td>
                            @if ($product->grandtotal_without_discount != '')
                                <b>{{ $currency }}</b> {{ $product->grandtotal_without_discount }}
                            @elseif ($product->grandtotal_without_discount == '')
                                <b>{{ $currency }}</b> {{ $product->sum}}
                            @endif
                        </td>
                        <td>
                            @if ($product->discount_amount != '')
                                <b>{{ $currency }}</b> {{ number_format($product->discount_amount, 3) }}
                            @endif
                        </td>
                        <td><b>{{ $currency }}</b> {{ number_format($grand_total_with_discount, 3) }}</td>
                        <td><b>{{ $currency }}</b> {{ $product->vat }}</td>
                        <td>
                            @if ($return_amount != 0)
                                <b>{{ $currency }}</b> {{ number_format($return_amount, 3) }}
                            @endif
                        </td>
                        @if (Session('softwareuser'))
                            @foreach ($users as $user)
                           <?php if ($user->role_id == '28') { ?>
                        <td>
                            @if ($product->credit_note_amount!=null)
                            <b>{{ $currency }}</b> {{ number_format($product->credit_note_amount, 3) }}
                            @endif
                        </td>
                            <?php } ?>
                            @endforeach
                            @endif
                           <td><b>{{ $currency }}</b> {{ number_format($total_after_return, 3) }}</td>
                        <td>{{ $product->customer_name }}</td>
                        <td>{{ $product->payment_type }}</td>
                        <td class="hide">{{ $product->approve }}</td>
                        <!--<td>{{ $product->username }}</td>-->
                        <td>{{ $product->phone }}</td>
                        @if (Session('adminuser'))
                            <td> {{ $product->branch }}</td>
                            <td style="display:none;">
                                @if ($product->approve)
                                    <button class="btn btn-secondary" disabled>Approved</button>
                                @else
                                    <button
                                        class="btn btn-success approve-button"
                                        data-id="{{ $product->transaction_id }}"
                                    >
                                        Approve
                                    </button>
                                @endif
                            </td>
                        @endif
                        <td>


                            <div class="dropdown">
                                <button class="btn btn-info dropdown-toggle" style="background-color:#187f6a;" type="button" id="dropdownMenu1"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="glyphicon glyphicon-cog"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li>
                                        <a href="/transactiondetails/{{ $product->transaction_id }}"
                                            title="View Product Details"><i class="glyphicon glyphicon-eye-open"></i>
                                            VIEW
                                        </a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="/generatetax-pdf/{{ $product->transaction_id }}"
                                            title="Print Bill Invoice in PDF">
                                            <i class="glyphicon glyphicon-download-alt"></i> DOWNLOAD PDF
                                        </a>
                                        <a href="/generatetax-pdf_a4/{{ $product->transaction_id }}"
                                            title="Print Bill Invoice in PDF">
                                            <i class="glyphicon glyphicon-print"></i> A4 PRINT
                                        </a>
                                        <a href="/generatetax-pdf_a5/{{ $product->transaction_id }}"
                                            title="Print Bill Invoice in PDF">
                                            <i class="glyphicon glyphicon-print"></i> A5 PRINT
                                        </a>
                                    </li>

                                    @if (Session('softwareuser'))
                                        @php
                                            $adminroles = DB::table('adminusers')
                                                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                                                ->where('user_id', $adminid)
                                                ->get();
                                        @endphp

                                        @foreach ($adminroles as $adminrole)
                                            @if ($adminrole->module_id == '25')
                                               <li>
                                                    <a href="/sunmi_PDFPrint/{{ $product->transaction_id }}"> <i class="glyphicon glyphicon-print"></i> PRINT SUNMI</a>
                                                    <a href="/generatetax-newsunmi/{{ $product->transaction_id }}">
                                                        <i class="glyphicon glyphicon-print"></i> PRINT SUNMI MINI
                                                    </a>

                                                </li>
                                            @endif
                                            <!--@if ($adminrole->module_id == '23')-->
                                                <!-- original -->
                                            <!--    <li>-->
                                            <!--        <a href="/generatetax-newsunmi/{{ $product->transaction_id }}">-->
                                            <!--            <i class="glyphicon glyphicon-print"></i> PRINT SUNMI MINI-->
                                            <!--        </a>-->
                                            <!--<a href="/generatetax-newsunmi/{{ $product->transaction_id }}" <i class="glyphicon glyphicon-print"></i> PRINT SUNMI</a>-->

                                            <!--    </li>-->
                                            <!--@endif-->

                                        @endforeach
                                    @elseif (Session('adminuser'))
                                        @foreach ($users as $user)
                                            @if ($user->module_id == '23')
                                                <!-- original -->
                                                <li>
                                                    <a href="/sunmi_PDFPrint/{{ $product->transaction_id }}"> <i class="glyphicon glyphicon-print"></i> PRINT SUNMI</a>

                                                </li>
                                            @endif

                                            @if ($user->module_id == '25')
                                                <li>
                                                    <a href="/generatetax-newsunmi/{{ $product->transaction_id }}">
                                                        <i class="glyphicon glyphicon-print"></i> PRINT SUNMI MINI
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif

                                  <li role="separator" class="divider"></li>
                                    <li>
                                        {{-- @if (Session('softwareuser')) --}}
                                            @php
                                                $returnProductExists = DB::table('returnproducts')
                                                    ->where('transaction_id', $product->transaction_id)
                                                    ->exists();
                                                    $delivery_done = DB::table('buyproducts')
                                                ->where('transaction_id', $product->transaction_id)
                                                ->pluck('delivery_done')
                                                ->first();


                                                $checkCondition = DB::table('credit_transactions')
                                                    ->where('transaction_id', $product->transaction_id)
                                                    ->where('comment','=','Payment Received')
                                                    ->groupBy('transaction_id')
                                                    ->sum('collected_amount');

                                                // Set as null if the sum is 0
                                                $checkCondition = $checkCondition == 0 ? null : $checkCondition;

                                                  $creditnoteExists = DB::table('credit_note')
                                                    ->where('transaction_id', $product->transaction_id)
                                                    ->exists();

                                                 $checkservice = DB::table('buyproducts')
                                                    ->where('transaction_id', $product->transaction_id)
                                                    ->groupBy('transaction_id')
                                                    ->sum('service_cost');

                                                    $checkservice = $checkservice == 0 ? null : $checkservice;

                                            @endphp

                                            @if (!$returnProductExists && $delivery_done != 1 && $checkCondition===null && !$creditnoteExists && $checkservice===null )
                                           <a class="editLink btn-danger"
                                                    data-transaction-id="{{ $product->transaction_id }}">
                                                    <i class="glyphicon glyphicon-warning-sign"></i> Edit
                                                </a>
                                            @else
                                                <span class="no-edit"> No Edit</span>
                                            @endif
                                        {{-- @endif --}}
                                    </li>
                                    @if (Session('softwareuser'))
                                        <li>
                                            <a class="cloneLink" data-transaction-id="{{ $product->transaction_id }}">
                                                <i class="glyphicon glyphicon-duplicate"></i> Clone
                                            </a>
                                        </li>
                                    @endif
                                    @if (Session('softwareuser'))
                                    @foreach ($users as $user)
                                    @if ($user->role_id == '18')
                                        @php
                                            $adminroles = DB::table('adminusers')
                                                ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                                                ->where('user_id', $adminid)
                                                ->get();
                                        @endphp

                                        @foreach ($adminroles as $adminrole)
                                            @if ($adminrole->module_id == '31')


                                                    <li>
                                                        @if (!$returnProductExists && $delivery_done != 1 && !$creditnoteExists)
                                                            <a class="deliverylink" data-transaction-id="{{ $product->transaction_id }}">
                                                                <i class="glyphicon glyphicon-send"></i> To Delivery
                                                            </a>
                                                        @else
                                                        <span class="no-edit"><i class="glyphicon glyphicon-send"></i>&nbsp;&nbsp;Delivery Done</span>
                                                        @endif
                                                    </li>

                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                                @endif

                                </ul>
                            </div>
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
                        $total_return_amount += $return_amount;
                        $grandtotal_after_return += $total_after_return;
                        $grand_note+=$product->credit_note_amount;
                    @endphp
                @endforeach

            </tbody>
            <tr style="font-weight: bold;font-size:16px;">
                <td colspan="2" class="total">Total</td>
                <td class="total" id="ttt"><b>{{ $currency }}</b> {{ number_format($total, 3) }}</td>
                <td class="total" id="dddd"><b>{{ $currency }}</b> {{ number_format($discount, 3) }}</td>
                <td class="total" id="gttt"><b>{{ $currency }}</b> {{ number_format($grand_total, 3) }}
                </td>
                <td class="total" id="vttt"><b>{{ $currency }}</b> {{ number_format($vat, 3) }}</td>
                <td class="total" id="rttt"><b>{{ $currency }}</b>
                    {{ number_format($total_return_amount, 3) }}</td>
                    @if (Session('softwareuser'))
                    @foreach ($users as $user)
                    <?php if ($user->role_id == '28') { ?>
                    <td class="total" id="nttt"><b>{{ $currency }}</b>
                        {{ number_format($grand_note, 3) }}</td>
                        <?php } ?>
                        @endforeach
                        @endif
                <td class="total" id="attt"><b>{{ $currency }}</b>
                    {{ number_format($grandtotal_after_return, 3) }}</td>
                <td colspan="6"></td>
            </tr>
        </table>

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
                var totReturn = 0;
                var grand_note = 0;
                var grandTotReturn = 0;

                // Loop through the visible rows (filtered data)
                table.rows({
                    search: 'applied'
                }).data().each(function(d) {

                    // Extract numeric values from the HTML-formatted strings
                    var totalValue = parseFloat(d[2].replace(/<\/?b>/g, '').replace(
                        /[^0-9.-]/g, '')) || 0;

                    var discountValue = parseFloat(d[3].replace(/<\/?b>/g, '').replace(
                        /[^0-9.-]/g, '')) || 0;

                    var grandTotalDisValue = parseFloat(d[4].replace(/<\/?b>/g, '').replace(
                        /[^0-9.-]/g, '')) || 0;

                    var vatValue = parseFloat(d[5].replace(/<\/?b>/g, '')
                        .replace(/[^0-9.-]/g, '')) || 0;

                    var totReturnValue = parseFloat(d[6].replace(/<\/?b>/g, '').replace(
                        /[^0-9.-]/g, '')) || 0;
                        var grandnote = parseFloat(d[7].replace(/<\/?b>/g, '').replace(
                            /[^0-9.-]/g, '')) || 0;
                    var grandTotReturnValue = parseFloat(d[8].replace(/<\/?b>/g, '')
                        .replace(
                            /[^0-9.-]/g, '')) || 0;

                    total += totalValue;
                    discount += discountValue;
                    vat += vatValue;
                    grandTotal += grandTotalDisValue;
                    totReturn += totReturnValue;
                    grandTotReturn += grandTotalDisValue - totReturnValue;
                    grand_note+=grandnote;

                    console.log(grandTotalDisValue);
                });

                // Update the displayed totals
                $('#ttt').html('<b>{{ $currency }}</b> ' + total.toFixed(3));
                $('#dddd').html('<b>{{ $currency }}</b> ' + discount.toFixed(3));
                $('#gttt').html('<b>{{ $currency }}</b> ' + grandTotal.toFixed(3));
                $('#vttt').html('<b>{{ $currency }}</b> ' + vat.toFixed(3));
                $('#rttt').html('<b>{{ $currency }}</b> ' + totReturn.toFixed(3));
                $('#nttt').html('<b>{{ $currency }}</b> ' + grand_note.toFixed(3));
                $('#attt').html('<b>{{ $currency }}</b> ' + grandTotReturn.toFixed(3));
            });
        },
        drawCallback: function(settings) {
            // Recalculate dropdown menu position after each pagination
            $('.dropdown').each(function() {
                var $dropdownMenu = $(this).find('.dropdown-menu');
                var $button = $(this).find('.dropdown-toggle');

                // Get the offset of the button
                var buttonOffset = $button.offset();
                var buttonWidth = $button.outerWidth();

                // Get the height and width of the dropdown menu
                var dropdownHeight = $dropdownMenu.outerHeight();
                var dropdownWidth = $dropdownMenu.outerWidth();

                // Calculate the space available on the right side
                var spaceOnRight = $(window).width() - (buttonOffset.left + buttonWidth);

                // If there is not enough space on the right side, align dropdown to the left
                if (spaceOnRight < dropdownWidth) {
                    $dropdownMenu.css({
                        'right': 0,
                        'left': 'auto'
                    });
                } else {
                    $dropdownMenu.css({
                        'right': 'auto',
                        'left': 0
                    });
                }

                // Calculate the space available at the bottom
                var spaceAtBottom = $(window).height() - (buttonOffset.top + $button.outerHeight());

                // If there is not enough space at the bottom, drop up
                if (spaceAtBottom < dropdownHeight) {
                    $dropdownMenu.css({
                        'top': 'auto',
                        'bottom': '100%'
                    });
                } else {
                    $dropdownMenu.css({
                        'top': '100%',
                        'bottom': 'auto'
                    });
                }
            });
        }

    });

    // Event delegation for edit buttons
 document.querySelector('tbody').addEventListener('click', function(event) {
        if (event.target.classList.contains('editLink')) {
            var pageName = document.getElementById('page').value;
            var transactionId = event.target.getAttribute('data-transaction-id');
            window.location.href = "/edittransactiondetails/" + pageName + "/" + transactionId;
        }
    });

    // Event delegation for clone buttons
    document.querySelector('tbody').addEventListener('click', function(event) {
        if (event.target.classList.contains('cloneLink')) {
            var pageName = 'clone_bill';
            var transactionId = event.target.getAttribute('data-transaction-id');
            window.location.href = "/edittransactiondetails/" + pageName + "/" + transactionId;
        }
    });
            // Event delegation for to delivery buttons
            document.querySelector('tbody').addEventListener('click', function(event) {
        if (event.target.classList.contains('deliverylink')) {
            var pageName = 'to_delivery';
            var transactionId = event.target.getAttribute('data-transaction-id');
            window.location.href = "/edittransactiondetails/" + pageName + "/" + transactionId;
        }
    });
</script>

<script>
    $(document).ready(function() {

        @if (Session('softwareuser'))
            $('.js-user').select2({
                theme: "classic",
                placeholder: "Select Customer",
                allowClear: true // Optional: allows clearing the selected option
            });
        @endif

        $('.dropdown').on('show.bs.dropdown', function() {

            var $dropdownMenu = $(this).find('.dropdown-menu');
            var $button = $(this).find('.dropdown-toggle');

            // Get the offset of the button
            var buttonOffset = $button.offset();
            var buttonWidth = $button.outerWidth();

            // Get the height and width of the dropdown menu
            var dropdownHeight = $dropdownMenu.outerHeight();
            var dropdownWidth = $dropdownMenu.outerWidth();

            // Calculate the space available on the right side
            var spaceOnRight = $(window).width() - (buttonOffset.left + buttonWidth);

            // If there is not enough space on the right side, align dropdown to the left
            if (spaceOnRight < dropdownWidth) {
                $dropdownMenu.css({
                    'right': 0,
                    'left': 'auto'
                });
            } else {
                $dropdownMenu.css({
                    'right': 'auto',
                    'left': 0
                });
            }

            // Calculate the space available at the bottom
            var spaceAtBottom = $(window).height() - (buttonOffset.top + $button.outerHeight());

            // If there is not enough space at the bottom, drop up
            if (spaceAtBottom < dropdownHeight) {
                $dropdownMenu.css({
                    'top': 'auto',
                    'bottom': '100%'
                });
            } else {
                $dropdownMenu.css({
                    'top': '100%',
                    'bottom': 'auto'
                });
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
    $('.js-user').select2({
        placeholder: "Select Customer",
        allowClear: true
    });
});
</script>
