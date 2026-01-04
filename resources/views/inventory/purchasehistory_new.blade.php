<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">

    <title>Purchase History</title>

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
             .btn-primary{
            background-color: #187f6a;
            color: white;
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

        .custom-badge {
            background-color: #5bc0de;
            /* Light blue color */
            color: #fff;
            border: none;

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
        @if ($adminroles->contains('module_id', '30'))
        <div style="margin-left:15px;margin-top:18px;">

            @include('navbar.invnavbar')
        </div>
        @else
<x-logout_nav_user />
@endif
        @if(Session('softwareuser'))
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Inventory</a></li>
                <li class="breadcrumb-item active" aria-current="page">Purchase History</li>
            </ol>
        </nav>
        @endif
        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif

        <input type="hidden" id="page" id="page" value="edit_purchase">
        <form action="/purchasehistorydate" method="get">
            <div class="row">
                <div class="col-sm-6">
                    <h4>SELECT DATES </h4>
                    <div class="row">
                        <div class="col-sm-5">
                            From
                            <input type="date" class="form-control" value="{{ $start_date }}" name="start_date">
                        </div>
                        <div class="col-sm-5">
                            To
                            <input type="date" class="form-control" value="{{ $end_date }}" name="end_date">
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
    <h2>Purchase History</h2>
        <table id="example" class="table table-striped table-bordered" style="width:100%;">
            <thead>
                <tr>
                    <th>Bill No.</th>
                    <th>Comment</th>
                    <th>Total Price</th>
                    <th>{{$tax}}</th>
                    <th>Grand Total <br />(Including {{$tax}}) </th>
                    <th>Discount</th>
                    <th>Grand Total <br />(with Discount) </th>
                    <th>Date and Time</th>
                    <th>Supplier Name</th>
                    <th>Payment Type</th>
                    <th class="hide"></th>
                    @if (Session('adminuser'))
                    <th>Branch</th>
                    <th style="display:none;">Approve</th>
                    @endif
                    <th>Action</th>
                    <th>Print</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>

                @php
                    $total = 0;
                    $discount = 0;
                    $vat = 0;
                    $grand_total = 0;
                    $grand_total_discount=0;
                @endphp

                @foreach ($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->reciept_no }}</td>
                        <td>{{ $purchase->comment }}</td>
                        <td><b>{{ $currency }}</b> {{ $purchase->price_without_vat }}</td>
                        <td>
                            <!--{{-- @if ($purchase->vat_amount != null)-->
                            <!--    <b>{{ $currency }}</b> {{ $purchase->vat_amount }}-->
                            <!--@elseif ($purchase->vat_amount == null)-->
                            <!--    <span class='badge badge-default'>NA</span>-->
                            <!--@endif --}}-->
                            <b>{{ $currency }}</b> {{ $purchase->price - $purchase->price_without_vat }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b>

                            {{ $purchase->price }}
                        </td>
                        <td><b>{{ $currency }}</b> {{round($purchase->discount,2)}}</td>
                        <td>
                            <b>{{ $currency }}</b>

                            {{ $purchase->price - round($purchase->discount,2)}}
                        </td>
                        <td class="hide">{{ $purchase->approve }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($purchase->created_at)->format('d-M-Y | h:i:s A') }}
                        </td>
                        <td>{{ $purchase->supplier }}</td>
                        <td>
                            @if ($purchase->payment_mode == '1')
                                CASH
                            @elseif ($purchase->payment_mode == '2')
                                CREDIT
                         @elseif ($purchase->payment_mode == '3')
                                BANK
                            @endif
                        </td>
                        @if (Session('adminuser'))
                        <td> {{ $purchase->branch }}</td>
                        <td style="display:none;">
                        @if ($purchase->approve)
                            <button class="btn btn-secondary" disabled>Approved</button>
                        @else
                            <button
                                class="btn btn-success approve-button"
                                data-id="{{ $purchase->reciept_no }}"
                            >
                                Approve
                            </button>
                        @endif
                    </td>
                    @endif
                        <td>
                            @php
                                        $returnstatus = DB::table('returnpurchases')->where('reciept_no', $purchase->reciept_no)->exists();

                                        $checkCondition = DB::table('credit_supplier_transactions')
                                            ->where('reciept_no', $purchase->reciept_no)
                                            ->where('comment','=','Payment Made')
                                            ->groupBy('reciept_no')
                                            ->sum('collectedamount');

                                            $checkCondition = $checkCondition == 0 ? null : $checkCondition;

                                            $debit_note_exist = DB::table('debit_note')
                                                    ->where('reciept_no', $purchase->reciept_no)
                                                    ->exists();



                            @endphp
                            <a href="/purchasedetails/{{ $purchase->reciept_no }}" class="btn btn-primary"
                                title="View Products">VIEW</a>


                                @if (!$returnstatus && $purchase->showEditButton && $checkCondition === null && !$debit_note_exist)
                                <a class="btn btn-danger editPurLink" title="Edit Purchase"
                                    data-receipt-no="{{ $purchase->reciept_no }}">EDIT</a>

                            @else
                                <span>No Edit</span>

                                @if ($purchase->sales->isNotEmpty() || $purchase->purchase_return == true)
                                    <button type="button" class="badge badge-pill custom-badge" data-toggle="modal"
                                        data-target="#exampleModal{{ $purchase->reciept_no }}">
                                        message
                                    </button>

                                    @elseif ($checkCondition != null || $debit_note_exist)
                                    <button type="button" class="badge badge-pill custom-badge" data-toggle="modal"
                                    data-target="#exampleModal{{ $purchase->reciept_no }}">
                                    message
                                </button>
                                @endif
                            @endif
                        </td>
                         <td>
                            <a href="/barcode/{{ $purchase->reciept_no }}" class="btn btn-primary"
                                title="View Products">Barcode</a>
                        </td>
                        <td>
                            @if ($purchase->file != null)
                                <b>
                                    <a href="{{ url('/download', $purchase->file) }}">Download</a>
                                </b>
                            @else
                                <b>
                                    No File
                                </b>
                            @endif
                        </td>
                    </tr>
                    @php
                        $total += $purchase->price_without_vat;
                        $discount +=$purchase->discount;

                        $vat += $purchase->price - $purchase->price_without_vat;
                        $grand_total_discount+= $purchase->price - $purchase->discount;
                        $grand_total += $purchase->price;
                    @endphp
                @endforeach
            </tbody>
            <tr style="font-weight: bold;font-size:16px;">
                <td colspan="2" class="total">Total</td>
                <td class="total" id="ttt"><b>{{ $currency }}</b> {{ number_format($total, 3) }}</td>
                <td class="total" id="vttt"><b>{{ $currency }}</b> {{ number_format($vat, 3) }}</td>
                <td class="total" id="gttt"><b>{{ $currency }}</b> {{ number_format($grand_total, 3) }}</td>
                <td class="total" id="dttt"><b>{{ $currency }}</b> {{ number_format($discount, 3) }} </td>
                <td class="total" id="gdttt"><b>{{ $currency }}</b> {{ number_format($grand_total_discount, 3) }}</td>

                <td colspan="5"></td>
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
                var discount = 0;
                var grandTotal = 0;
                var grandTotaldiscount = 0;

                // Loop through the visible rows (filtered data)
                table.rows({
                    search: 'applied'
                }).data().each(function(d) {

                    console.log(d);

                    // Access properties directly instead of using map
                    var priceWithoutVat = parseFloat(d[2].replace(/[^0-9.-]/g, "")) || 0;
                    var totaldiscount = parseFloat(d[5].replace(/[^0-9.-]/g, "")) || 0;
                    // var vatAmount = parseFloat(d[3] ===
                    //     "<span class=\"badge badge-default\">NA</span>" ? 0 : d[3]
                    //     .replace(/[^0-9.-]/g, "")) || 0;

                    var priceWithVat = parseFloat(d[4].replace(/[^0-9.-]/g, "")) || 0;
                    var granddiscount = parseFloat(d[6].replace(/[^0-9.-]/g, "")) || 0;
                    grandTotaldiscount+=granddiscount;
                    discount+=totaldiscount;
                    total += priceWithoutVat;
                    // vat += vatAmount;
                    // grandTotal += priceWithoutVat + vatAmount;

                    vat +=priceWithVat - priceWithoutVat;
                    grandTotal += priceWithVat;
                });

                // Update the displayed totals
                $('#ttt').html('<b>{{ $currency }}</b> ' + total.toFixed(3));
                $('#dttt').html('<b>{{ $currency }}</b> ' + discount.toFixed(3));
                $('#vttt').html('<b>{{ $currency }}</b> ' + vat.toFixed(3));
                $('#gttt').html('<b>{{ $currency }}</b> ' + grandTotal.toFixed(3));
                $('#gdttt').html('<b>{{ $currency }}</b> ' + grandTotaldiscount.toFixed(3));

            });
        }
    });

        document.querySelector('tbody').addEventListener('click', function(event) {
            if (event.target.classList.contains('editPurLink')) {
                var pageName = document.getElementById('page').value;
                var receiptNo = event.target.getAttribute('data-receipt-no');
                window.location.href = "/edit_purchasedetails/" + pageName + "/" + receiptNo;
            }
        });
</script>


<!-- {{-- <script>

    var editPurLinks = document.getElementsByClassName('editPurLink');

    for (var i = 0; i < editPurLinks.length; i++) {
        var pageName = document.getElementById('page').value;
        var receiptNo = editPurLinks[i].getAttribute('data-receipt-no');
        editPurLinks[i].href = "/edit_purchasedetails/" + pageName + "/" + receiptNo;
    }
</script> --}} -->

<!-- Modal -->
@foreach ($purchases as $purchase)
    <div class="modal fade" id="exampleModal{{ $purchase->reciept_no }}" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Why You Can't Edit Purchase:
                        {{ $purchase->reciept_no }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    @if ($purchase->sales->isNotEmpty())
                        <h5>Sales Done: </h5>
                        <ul>
                            @foreach ($purchase->sales as $sale)
                                <li>Transaction ID: {{ $sale->trans_id }} </li> <br />
                            @endforeach
                        </ul>

                    @else
                        No sales information available.
                    @endif

                    @if ($purchase->purchase_return == true)
                        <hr />
                        Purchase Return Done
                    @endif

                    @if($checkCondition != null)
                    <hr />
                    Payment Collected
                    @endif

                    @if($debit_note_exist)
                    <hr />
                    Debit Note Applied for this reciept
                    @endif

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
             </div>
        </div>
    </div>
@endforeach

