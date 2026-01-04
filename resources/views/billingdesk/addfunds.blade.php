<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Add Funds</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">

    @include('layouts/usersidebar')
    <style>
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
        .dropdown {
        position: relative; /* Keep the dropdown in position relative to the button */
        float: right; /* Keep the ☰ button on the right side of the page */
    }

   /* Ensure the dropdown appears on the left side */
.dropdown-menu {
    display: none;
    position: absolute;
    right: 100%;  /* Open the dropdown to the left of the button */
    top: 70; /* Align it with the top of the button */
    background: white;
    border: 1px solid #ddd;
    padding: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-left: -130px;
}

.dropdown:hover .dropdown-menu {
    display: block; /* Show the dropdown when hovering */
}

.dropdown-menu a {
    display: block;
    padding: 8px 12px;
    text-decoration: none;
    color: black;
    border-bottom: 1px solid #ddd;

}

.dropdown-menu a:last-child {
    border-bottom: none;
}

.dropdown-menu a:hover {
    background: #187f6a;
    color: white;
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

<!-- Page Content Holder -->
<div id="content">
    @if ($adminroles->contains('module_id', '30'))
    <div style="margin-left:15px;margin-top:15px;">

        @include('navbar.billingdesknavbar')
    </div>
    @else
    <x-logout_nav_user />
@endif
<!--<div align="right">-->
<!--    <a class="btn btn-info" href="/customerstatus">Customer Summary</a>-->
<!--        <a class="btn btn-info" href="/chequesummary">Cheque Summary</a>-->

<!--</div>-->
<div class="dropdown">
            <button class="btn btn-info" style="background-color:#187f6a;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                ☰
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a href="/customerstatus" class="dropdown-item ">Customer Summary</a>
                <a href="/chequesummary" class="dropdown-item ">Cheque Summary</a>

                <!--<a class="dropdown-item" href="">Refresh</a> -->
            </div>
        </div>
        <br>
<br>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Billing Desk</a></li>
            <li class="breadcrumb-item active" aria-current="page">Credit Add Fund</li>
        </ol>
    </nav>
    <!-- content -->
    <h2>Sales - Customer</h2>

    <table id="example" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th width="10%">Customer Name</th>
                <th width="8%">Gross Sale</th>
                <th width="8%">Amount Received</th>
                <th width="8%">Sales Returns</th>
                <th width="8%">Due</th>
                <th width="8%">Payment Reciepts</th>
                <th width="8%">Account Activity</th>
                {{-- <th width="8%">Report</th> --}}
                <!--<th width="8%">Reciept Voucher</th>-->
                <th width="8%">Payment History</th>


            </tr>
        </thead>
        <tbody>
            @php
    $totalsale = 0;
    $totalPayment = 0;
    $totalreturn = 0;
    $totalbalance = 0;

@endphp
            @foreach ($credits as $credit)
                <tr>
                    <td>
                        {{ $credit->name }}
                    </td>
                    <td>
                        <b>{{ $currency }}</b> {{ number_format($credit->total_invoiced_amount + $credit->credit_trans_invoice_amount, 3) }}
                    </td>
                    <td>
                        <b>{{ $currency }}</b> {{ number_format($credit->credit_trans_collected + $credit->total_invoiced_amount, 3) }}
                    </td>
                    <td>

                        <b>{{ $currency }}</b> {{ number_format($credit->credit_trans_returned + $credit->total_product_returned, 3) }}
                    </td>
                    <td>

                        <b>{{ $currency }}</b> {{ number_format($credit->due_amount - $credit->collected_amount, 3) }}
                    </td>
                    <td>
                        <button class="btn btn-primary creditfundbtn btn-sm" onclick="javascript: return false;"
                            value="{{ $credit->id }}" id="add_fund" title="Add Payment">Reciept</button>
                    </td>
                    <td>
                        <a class="btn btn-primary" href="credittransactionshistory/{{ $credit->id }}"
                            title="Payment Transaction History">VIEW</a>
                    </td>
                    {{-- <td>
                        <a class="btn btn-primary" href="creditbillshistory/{{ $credit->id }}"
                            title="Bill History">VIEW</a>
                    </td> --}}
                    <!--<td>-->
                    <!--    <a class="btn btn-primary" href="reciept_voucher/{{ $credit->id }}"-->
                    <!--        title="reciept Vouchers">VIEW</a>-->
                    <!--</td>-->
                    <td>
                        <a class="btn btn-primary" href="payment_history_customer/{{ $credit->id }}"
                            title="Payment History">VIEW</a>
                    </td>
                    @php
                    $totalsale += $credit->total_invoiced_amount + $credit->credit_trans_invoice_amount;
                    $totalPayment += $credit->credit_trans_collected + $credit->total_invoiced_amount;
                    $totalreturn += $credit->credit_trans_returned + $credit->total_product_returned;
                    $totalbalance += $credit->due_amount - $credit->collected_amount;
                   @endphp
            @endforeach
            </tr>
        </tbody>
        <tr style="font-weight: bold;font-size:16px;">
            <td colspan="1" class="total">Total</td>
            <td class="total" id="vttt"><b>{{ $currency }}</b> {{ number_format($totalsale, 3) }}</td>
            <td class="total" id="gttt"><b>{{ $currency }}</b> {{ number_format($totalPayment, 3) }}</td>
            <td class="total" id="ttt"><b>{{ $currency }}</b> {{ number_format($totalreturn, 3) }}</td>
            <td class="total" id="pttt"><b>{{ $currency }}</b> {{ number_format($totalbalance, 3) }}</td>

        </tr>
    </table>

    <!-- Supplier credit section -->
    <h2>Purchase - Supplier</h2>

    <table id="example1" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th width="10%">Supplier Name</th>
                <th width="8%">Gross Purchases</th>
                <th width="8%">Amount Paid</th>
                <th width="8%">Purchase Returns</th>
                <th width="8%">Due</th>
                <th width="8%">Make Payment</th>
                <th width="8%">Account Activity</th>
                {{-- <th width="8%">Report</th> --}}
                <!--<th width="8%">Payment Voucher</th>-->
                <th width="8%">Payment History</th>

            </tr>
        </thead>
        <tbody>
            @php
    $suppliertotalbill = 0;
    $suppliertotalpayment = 0;
    $suppliertotalreturn = 0;
    $suppliertotalbalance = 0;

@endphp
            @foreach ($suppliercredits as $suppliercredit)
                <tr>
                    <td>
                        {{ $suppliercredit->name }}
                        <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $suppliercredit->id }}">
                    </td>
                    <td><b>{{ $currency }}</b> {{ number_format($suppliercredit->total_invoiced_amount + $suppliercredit->credit_trans_invoice_amount, 3) }}</td>

                    <td><b>{{ $currency }}</b> {{ number_format($suppliercredit->credit_trans_collected + $suppliercredit->total_invoiced_amount, 3) }}</td>
                    <td>

                            <b>{{ $currency }}</b> {{ number_format($suppliercredit->credit_trans_returned + $suppliercredit->total_purchase_returned, 3) }}
                    </td>
                    <td>

                        <b>{{ $currency }}</b> {{ number_format($suppliercredit->due_amt - $suppliercredit->collected_amt, 3) }}
                </td>
                    <td>
                        <button class="btn btn-primary suppliercreditfundbtn btn-sm" onclick="javascript: return false;"
                            value="{{ $suppliercredit->id }}" id="add_supplierfund"
                            title="Add Payment">Payment</button>
                    </td>
                    <td>
                        <a class="btn btn-primary" href="suppliercredit_trans_history/{{ $suppliercredit->id }}"
                            title="Payment Transaction History">VIEW</a>
                    </td>
                    {{-- <td>
                        <a class="btn btn-primary" href="supplier_creditbillshistory/{{ $suppliercredit->id }}"
                            title="Bill History">VIEW</a>
                    </td> --}}
                    <!--<td>-->
                    <!--    <a class="btn btn-primary" href="payment_voucher/{{ $suppliercredit->id }}"-->
                    <!--        title="Payment Vouchers">VIEW</a>-->
                    <!--</td>-->
                     <td>
                        <a class="btn btn-primary" href="payment_history_supplier/{{ $suppliercredit->id }}"
                            title="Payment History">VIEW</a>
                    </td>
                    
                </tr>
                @php
                $suppliertotalbill += $suppliercredit->total_invoiced_amount + $suppliercredit->credit_trans_invoice_amount;
                $suppliertotalpayment += $suppliercredit->credit_trans_collected + $suppliercredit->total_invoiced_amount;
                $suppliertotalreturn += $suppliercredit->credit_trans_returned + $suppliercredit->total_purchase_returned;
                $suppliertotalbalance += $suppliercredit->due_amt - $suppliercredit->collected_amt;
                @endphp
            @endforeach
        </tbody>
        <tr style="font-weight: bold;font-size:16px;">
            <td colspan="1" class="total">Total</td>
            <td class="total" id="cdddd"><b>{{ $currency }}</b>{{ number_format($suppliertotalbill, 3) }}</td>
            <td class="total" id="cgttt"><b>{{ $currency }}</b>{{ number_format($suppliertotalpayment, 3) }}</td>
            <td class="total" id="cttt"><b>{{ $currency }}</b>{{ number_format($suppliertotalreturn, 3) }}</td>
            <td class="total" id="bttt"><b>{{ $currency }}</b>{{ number_format($suppliertotalbalance, 3) }}</td>


        </tr>
    </table>

    <!-- content end -->

</div>
</div>
</body>
@include('modal.addfund2')
@include('modal.addfund_supplier')

</html>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#example').DataTable({
            order: [
                [0, 'asc']
            ]
        });

        // Function to update totals based on the filtered data
        function updatecustomerTotals() {
            var totalsale = 0;
            var totalPayment= 0;
            var totalreturn = 0;
            var totalbalance = 0;


            // Loop through the visible rows (filtered data)
            table.rows({ search: 'applied' }).nodes().each(function(node) {
                var rowData = table.row(node).data();

                // Extract numeric values from the columns (removing HTML tags)
                totalsale += parseFloat(rowData[1].replace(/<\/?b>/g, '').replace(/[^0-9.-]/g, '')) || 0;
                totalPayment += parseFloat(rowData[2].replace(/<\/?b>/g, '').replace(/[^0-9.-]/g, '')) || 0;
                totalreturn += parseFloat(rowData[3].replace(/<\/?b>/g, '').replace(/[^0-9.-]/g, '')) || 0;
                totalbalance += parseFloat(rowData[4].replace(/<\/?b>/g, '').replace(/[^0-9.-]/g, '')) || 0;

            });

            // Update the HTML for the totals
            $('#vttt').html('<b>{{ $currency }} ' + totalsale.toFixed(3).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</b>');
            $('#gttt').html('<b>{{ $currency }} ' + totalPayment.toFixed(3).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</b>');
            $('#ttt').html('<b>{{ $currency }} ' + totalreturn.toFixed(3).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</b>');
            $('#pttt').html('<b>{{ $currency }} ' + totalbalance.toFixed(3).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</b>');

        }

        // Recalculate totals when searching or filtering
        table.on('search.dt', function() {
            updatecustomerTotals();
        });

        // Initialize totals
        updatecustomerTotals();
    });
</script>



<script>

    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#example1').DataTable({
            order: [
                [0, 'asc']
            ]
        });

        // Function to update totals based on the filtered data
        function updatesupplierTotals() {
            var suppliertotalbill = 0;
            var suppliertotalpayment = 0;
            var suppliertotalreturn = 0;
            var suppliertotalbalance = 0;


            // Loop through the visible rows (filtered data)
            table.rows({ search: 'applied' }).nodes().each(function(node) {
                var rowData = table.row(node).data();

                // Extract numeric values from the columns (removing HTML tags)
                suppliertotalbill += parseFloat(rowData[1].replace(/<\/?b>/g, '').replace(/[^0-9.-]/g, '')) || 0;
                suppliertotalpayment += parseFloat(rowData[2].replace(/<\/?b>/g, '').replace(/[^0-9.-]/g, '')) || 0;
                suppliertotalreturn += parseFloat(rowData[3].replace(/<\/?b>/g, '').replace(/[^0-9.-]/g, '')) || 0;
                suppliertotalbalance += parseFloat(rowData[4].replace(/<\/?b>/g, '').replace(/[^0-9.-]/g, '')) || 0;

            });

            // Update the HTML for the totals
            $('#cvttt').html('<b>{{ $currency }} ' + suppliertotalbill.toFixed(3).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</b>');
            $('#cgttt').html('<b>{{ $currency }} ' + suppliertotalpayment.toFixed(3).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</b>');
            $('#cttt').html('<b>{{ $currency }} ' + suppliertotalreturn.toFixed(3).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</b>');
            $('#bttt').html('<b>{{ $currency }} ' + suppliertotalbalance.toFixed(3).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</b>');

        }

        // Recalculate totals when searching or filtering
        table.on('search.dt', function() {
            updatesupplierTotals();
        });

        // Initialize totals
        updatesupplierTotals();
    });
</script>


<script>
    $(document).ready(function() {
        $(document).on('click', '.creditfundbtn', function() {
            var userid = $(this).val();
            fetchhistory();

            function fetchhistory() {
                $.ajax({
                    type: 'get',
                    url: '/gethistory/' + userid,
                    success: function(data) {
                        var creditid = data.creditid;
                        var dueamount = data.due;
                        var trans = data.trans_ids;
                        var full_name = data.full_name;

                        // Clear existing options in the dropdown
                        $('#transaction_id').empty();
                        // Add an empty option as the first option
                        $('#transaction_id').append($('<option>', {
                            value: '',
                            text: 'Select Transaction ID'
                        }));
                        // Populate the dropdown with transaction IDs
                        for (var i = 0; i < trans.length; i++) {
                            $('#transaction_id').append($('<option>', {
                                value: trans[i],
                                text: trans[i]
                            }));
                        }

                        // Clear and hide the product dropdown
                        $('#product_dropdown').empty().append($('<option>', {
                            value: '',
                            text: 'Select Product'
                        }));
                        $('#product_div').hide();

                        // Clear the Select2 selection
                        $('#transaction_id').val(null).trigger('change');
                        $('#product_dropdown').val(null).trigger('change');

                        $('#Addfund').modal('show');
                        // $('#fundusername').val(userid);
                        $('#fundusername').val(full_name);
                        $('#creditid').val(creditid);
                        $('#due').val(dueamount.toFixed(3));
                    }
                });
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $(document).on('click', '.suppliercreditfundbtn', function() {
            var userid = $(this).val();
            fetchfundhistory();

            function fetchfundhistory() {
                $.ajax({
                    type: 'get',
                    url: '/getfundhistory/' + userid,
                    success: function(data) {
                        var suppliername = data.suppliername;
                        var dueamount = data.due;
                        var trans = data.receiptnos;

                        // Clear existing options in the dropdown
                        $('#receipt_no').empty();

                        // Add an empty option as the first option
                        $('#receipt_no').append($('<option>', {
                            value: '',
                            text: 'Select Invoice No'
                        }));

                        // Populate the dropdown with transaction IDs
                        for (var i = 0; i < trans.length; i++) {
                            $('#receipt_no').append($('<option>', {
                                value: trans[i],
                                text: trans[i]
                            }));
                        }

                        // Clear and hide the product dropdown
                        $('#receipt_product').empty().append($('<option>', {
                            value: '',
                            text: 'Select Product'
                        }));
                        $('#products_div').hide();

                        // Clear the Select2 selection
                        $('#receipt_no').val(null).trigger('change');
                        $('#receipt_product').val(null).trigger('change');

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

