<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Credit Note</title>
    @include('layouts/usersidebar')
    <style>
        #content {
            padding: 30px;
        }

        table {
            border: solid black 1.5px;
            border-collapse: separate;
            border-left: 0;
            border-radius: 10px;
            border-spacing: 0px;
        }

        .table>tbody>tr>td,
        .table>tbody>tr>th,
        .table>tfoot>tr>td,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>thead>tr>th {
            padding: 8 px;
            line-height: 1.42857143;
            vertical-align: top;
            border-top: 1px solid #000;
        }

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

        .select2-container .select2-choice {
            height: 35px;
            line-height: 35px;
        }

        ul.select2-results {
            max-height: 100px;
        }
    </style>
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .gap {
            margin-left: -10rem;
        }

    </style>
    <style>
/* Container style */
.input-container {
    display: flex;
    flex-wrap: wrap; /* Allow wrapping on smaller screens */
    gap: 15px; /* Space between input boxes */
    justify-content: flex-start; /* Aligns items to the start */
    margin-top: 15px;
}

/* Individual input box style */
.input-box {
    display: flex;
    flex-direction: column; /* Stacks label and input vertically */
    flex: 1; /* Allows input boxes to grow equally */
    min-width: 120px; /* Minimum width for each input box */
    max-width: 180px; /* Maximum width for each input box */
}

/* Label style */
.input-box label {
    font-size: 12px;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

/* Input style */
.input-box input {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #f9f9f9;
    color: #555;
    width: 100%; /* Full width */
    box-sizing: border-box; /* Includes padding in width */
}

/* Readonly style */
.input-box input[readonly] {
    background-color: #e9ecef;
    color: #6c757d;
    cursor: not-allowed;
}

/* Responsive layout adjustments */
@media (max-width: 600px) {
    .input-box {
        min-width: 100%; /* Stacks inputs on smaller screens */
    }
}
.credit-note-button {
    width: 100%;
    padding: 10px;
    background-color: #4CAF50; /* Change to preferred color */
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    text-align: center;
    margin-top: 20px;
    border-radius: 10px;
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
        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif
        <div align="right">
            <a href="/creditnote_history" class="btn btn-info ">Credit Note History</a>
            <a href="/customer_summary" class="btn btn-info ">Customer Summary</a>
            <a href="" class="btn btn-info">Refresh</a>
        </div>
        <x-admindetails_user :shopdatas="$shopdatas" />

        <form method="post" action="creditnotesubmit" id="myform" onsubmit="return validateForm(event)">
            @csrf
            <h2>Credit Note</h2>
<br>
<input type="hidden" id="page" name="page" value="creditnote_sellcost">

            <div class="form group row">
                <select id="transaction_id" name="transaction_id" class="product-list"
                    style="width: 250px;padding-bottom: 2rem;">
                    <option selected value="">Select Transaction ID</option>
                    @foreach ($sales as $sales)
                        <option value="{{ $sales }}">{{ $sales }}</option>
                    @endforeach
                </select>

                <input type="hidden" id="trans_id_origin" name="trans_id_origin" readonly>

                <span style="color:red">
                    @error('transaction_id')
                        {{ $message }}
                    @enderror
                </span>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        <select onclick="doSomething(this.value)" id="product" class="product-list"
                            style="width: 250px">
                            <option selected value="">Select Product</option>
                        </select>

                        {{-- <p id="soldQuantityMessage" style="color: green;display: none;margin-top:5px;margin-bottom:-10px;"></p> --}}

                    </div>
                    <div class="col-md-1">
                        <div class="input-group gap hide" id="credit">
                            @foreach ($users as $user)
                                <?php if ($user->role_id == '11') { ?>
                                <span class="input-group-addon" id="basic-addon1" style="width: 90px;display:none;">CREDIT
                                    USER</span>
                                <input type="hidden" style="width: 170px;" id="credituser" name="credituser" value=""
                                    class="form-control" readonly>

                                <input type="hidden" style="width: 170px;" id="credituser__id" name="credituser__id"
                                    value="" class="form-control" readonly>
                                <?php } ?>
                            @endforeach

                        </div>
                    </div>
                </div>


                    <br>
                    {{-- <div class="form-group">
                        <label class="pr-2">
                            <input type="radio" name="toggle_creditnote" id="show_creditnote" value="yes"> Credit Note Amount
                        </label>



                    </div> --}}







                <div class="input-container">
                    <div class="input-box">
                        <label>CUSTOMER NAME</label>
                        <input type="text" id="customer_name" name="customer_name" readonly>
                    </div>

                    <div class="input-box">
                        <label>TOTAL DUE</label>
                        <input type="text" id="total_due" name="total_due" readonly>
                    </div>

                    <div class="input-box">
                        <label>INVOICE DUE</label>
                        <input type="text" id="invoice_due" name="invoice_due" readonly>
                    </div>

                    <div class="input-box">
                        <label>COLLECTED AMOUNT</label>
                        <input type="text" id="collected_amount" name="collected_amount" readonly>
                    </div>
                    <div class="input-box">
                        <label>RETURN AMOUNT</label>
                        <input type="text" id="returnProductsAmount" name="returnProductsAmount" readonly>
                    </div>

                    <div class="input-box">
                        <label>CREDIT NOTE AMOUNT</label>
                        <input type="text" id="credit_noteamount" name="credit_noteamount" readonly>
                    </div>

                    <div class="input-box">
                        <label>BALANCE DUE</label>
                        <input type="text" id="balance_due" name="balance_due" readonly>
                    </div>
                    {{-- <div class="input-box">
                       <button id="credit_note_button" class="credit-note-button"  onclick="changesellcost()">CHANGE SELL COST</button>
                    </div> --}}
                </div>

                <br>

                <table class="table">
                    <thead>
                        <tr>
                            <th width="2%"></th>
                            <th width="7%">Total Quantity</th>
                            <th width="6%">Description</th>
                            <th width="6%">Quantity</th>
                            <th width="6%">Unit</th>
                            <th width="8%">Previous Rate</th>
                            <th width="8%">Rate</th>
                            <th width="8%" id="inclusive_heading" style="display:none">Inclusive Rate
                            </th>
                            <th width="8%" id="ratediscount_heading" style="display:none">Exclusive Rate
                            </th>
                            <th width="8%" id="vat_perc">{{$tax}}(%)</th>
                            <th width="8%" id="vat_ammi">Total {{$tax}} Amount</th>
                            <th width="8%">Net Rate</th>
                            <th width="8%">Discount</th>
                            <th width="10%">Total Amount</th>
                            <th width="8%">Total Amount<br />w/o Discount</th>
                            <th width="10%">Credit Note<br /> Amount</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr bgcolor="#187f6a">
                            <td></td>
                            <td>
                                <input type="hidden" id="trans_id" name="trans_id" class="form-control" readonly>
                                <input type="text" id="total_quantity" name="total_quantity" class="form-control" readonly>

                            </td>
                            <td>
                                <input type="text" id="product_name_name" class="form-control" tabindex="1"
                                    readonly>
                            </td>
                            <td>
                                <input type="number" id="qty" step="1" class="form-control" min="0"
                                    max="" tabindex="2">
                            </td>
                            <td><input type="text" id="units" class="form-control" tabindex="3" readonly></td>
                            <td><input type="text" id="previous_rate" class="form-control" tabindex="4" readonly></td>

                            <td>
                                <input type="number" id="mrp" class="form-control" tabindex="5" readonly>
                                <input type="hidden" step="any" id="buycost" class="form-control">
                                <input type="hidden" step="any" id="oldsellcost" class="form-control">
                                <input type="hidden" step="any" id="buycost_rate" name="buycost_rate"
                                    class="form-control">
                            </td>
                            <td id="inclusive_rate_value" name="inclusive_rate_value" style="display: none;">
                            </td>
                            <td id="rate_discount_value" name="rate_discount_value" style="display: none;">
                            </td>
                            <td>
                                <input type="number" id="fixed_vat" class="form-control" tabindex="6" readonly>
                                <input type="hidden" id="vat_type" name="vat_type" readonly>
                            </td>
                            <td><input type="number" id="vat_amount" class="form-control" readonly></td>
                            <td>
                                <input type="number" id="net_rate" class="form-control" tabindex="7" readonly>
                            </td>
                            <td>
                                <input type="number" step="any" id="discount" class="form-control" readonly>
                                <input type="hidden" id="discount_type" class="form-control" readonly>
                            </td>
                            <td>
                                <input type="number" id="price" class="form-control" readonly>
                                <input type="hidden" id="pricex" class="form-control">
                            </td>
                            <td>
                                <input type="number" step="any" id="price_wo_discount" class="form-control"
                                    readonly>

                                <input type="hidden" step="any" id="total_wo_discount" class="form-control"
                                    readonly>
                            </td>
                            <td >
                                <input type="number" id="creditnote" step="1" class="form-control" min="0" max="" tabindex="8">
                            </td>

                            <td><a href="#" class="btn btn-info addRow" title="Add Row">+</a></td>
                            <input type="hidden" id="product_id" class="form-control">
                            <input type="hidden" id="product_name" class="form-control">
                            <input type="hidden" id="buyquantity" name="buyquantity" class="form-control">
                            <input type="hidden" id="paymenttype" name="paymenttype" class="form-control">
                        </tr>
                        <tr>
                            <td colspan="13"> <i class="glyphicon glyphicon-tags"></i> &nbsp BILL
                            </td>
                        <tr>
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-10 d-flex"
                        style="margin: 1rem; display: flex; justify-content: space-between; align-items: center; background-color: #f2f2f2; margin-left:9em">
                        <div class="input-group" style="display: flex; align-items: center; margin-left: 5rem;">
                            <label for="total_discount" class="mr-2" style="margin-right: 10px;">Total Discount
                                (%):</label>
                            <input type="number" id="total_discount" name="total_discount"
                                class="form-control-plaintext border-0"
                                style="width: 70px; background: transparent; border: none; margin-bottom: 5px;"
                                readonly>
                            <input type="hidden" id="total_discount_amount" name="total_discount_amount"
                                name="total_discount" readonly>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: flex-end; margin-right: 4rem;">
                            <div class="input-group" style="display: flex; align-items: center; margin-bottom: 6px;">
                                <label for="grand_total" class="mr-2" style="margin-right: 10px;">Grand
                                    Total:</label>
                                <input type="number" id="grand_total" name="grand_total"
                                    class="form-control-plaintext border-0"
                                    style="width: 70px; background: transparent; border: none;" readonly>
                            </div>
                            <div class="input-group" style="display: flex; align-items: center;">
                                <label for="grand_total_wo_discount" class="mr-2" style="margin-right: 10px;">Grand
                                    Total without Discount:</label>
                                <input type="number" id="grand_total_wo_discount" name="grand_total_wo_discount"
                                    class="form-control-plaintext border-0"
                                    style="width: 70px; background: transparent; border: none;" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br />
            <div class="col-md-10">
                <div align="right" style="margin-right: 3rem;">
                    <input class="btn btn-primary" type="submit" value="submit" id="submitBtn">
                </div>
            </div>
        </form>
    </div>
</body>

</html>

<script type="text/javascript">
    var addedProducts = {};

    function updateGrandTotal() {
        var grandtotal = 0;

        $('.total-amount').each(function() {
            grandtotal += Number($(this).val());
        });

        grandtotal = grandtotal.toFixed(2);
        grandtotal = parseFloat(grandtotal);
        $('#grand_total_wo_discount').val(grandtotal);

        var total_dis = $('#total_discount').val();

        var main_grand_total = grandtotal - (grandtotal * (total_dis / 100));

        main_grand_total = main_grand_total.toFixed(2);
        main_grand_total = parseFloat(main_grand_total);

        $('#grand_total').val(main_grand_total);
    }

    // $('.addRow').on('click', function() {
    //     addRow();
    // });

    $('.addRow').off().on('click', addRow);

    function addRow() {

        if (($("#product_name_name").val()) == "") {
            return;
        }
        if (($("#price").val()) <= 0) {
            return;
        }
        var y = ($("#product_name_name").val());
        var w = Number($("#mrp").val());
        var z = Number($("#price").val());
        var q = Number($("#fixed_vat").val());
        var x = Number($("#qty").val());
        var p = Number($("#pricex").val());
        var vat = Number($("#vat_amount").val());
        var netrate = Number($("#net_rate").val());
        var ti = ($("#trans_id").val());
        var tq = ($("#total_quantity").val());
        var pt = ($("#paymenttype").val());
        var wv = Number($("#buycost").val());
        var buycost_rate = Number($("#buycost_rate").val());
        var unit = ($("#units").val());
        var discount = Number($("#discount").val());
        var pr = Number($("#previous_rate").val());

        discount = (discount != null) ? discount : 0;

        var price_dis = Number($("#price_wo_discount").val());
        var total_withoutvat_disc = Number($("#total_wo_discount").val());

        var credit = Number($("#creditnote").val().trim()); // Parse input value as a number

        if (!credit) {
        alert("Credit note amount is required.");
        return;
    }

    if (isNaN(credit) || credit <= 0) { // Validate if input is a valid positive number
            alert("Please enter a valid credit note amount.");
            return;
        }

        var creditNoteInput = '';
        creditNoteInput = '<td><input type="text" value="' + credit + '" name="credit_note[]" class="form-control credit-note-input" readonly></td>';


        if (($("#qty").val()) == "") {
            return;
        }
        var u = Number($("#product_id").val());
        var credituse = $('#credituser').val();
        var credituse_id = $('#credituser__id').val();
        var vat_tp = $('#vat_type').val();
        var discount_type = $('#discount_type').val();

        if (vat_tp == 1) {
            var inclu_rate = Number($("#inclusive_rate").val());
        } else if (vat_tp == 2) {
            var ratediscount = Number($("#rate_discount").val());
        }

        if (addedProducts.hasOwnProperty(ti) && addedProducts[ti].includes(u)) {
            alert("Product is already added for this transaction.");
            return;
        }

        if (!addedProducts.hasOwnProperty(ti)) {
            addedProducts[ti] = [];
        }

        var number = "";
        var tr = '<tr>' + '<td>' + number + '</td>' + '<td>' +
            '<input type="hidden" id="trans" value="' + ti +
            '" name="trans[]" class="form-control" readonly> <input type="hidden" value="' + pt +
            '" name="ptype[]" class="form-control"> <input type="text" value="' + tq +
            '" name="total_quantity[]" class="form-control">' +
            '</td>' +
            '<td><input type="text" id="productname" value="' + y +
            '" name="productName[]" class="form-control" readonly>' +
            '<input type="hidden" value=' + credituse + ' name="creditusers[]" class="form-control" >' +
            '<input type="hidden" value=' + credituse_id + ' name="creditusers_id[]" class="form-control" ></td>' +
            '<td><input type="text" value=' + x + ' name="quantity[]" class="form-control" readonly></td>' +
            '<td><input type="text" value=' + unit + ' name="unit[]" class="form-control" readonly></td>' +
            '<td><input type="text" value=' + pr + ' name="previous_rate[]" class="form-control" readonly></td>' +

            '<td><input type="text" value=' + w +
            ' name="mrp[]" class="form-control" readonly><input type="hidden" step="any" name="buy_cost[]" value=' +
            wv + ' class="form-control">' +
            '<input type="hidden" step="any" name="buycost_rate[]" value=' + buycost_rate +
            ' class="form-control"></td>';

        if (vat_tp == 1) {
            tr += '<td><input type="number" value=' + inclu_rate +
                ' name="inclusive_rate_r[]" class="form-control" readonly></td>';
        } else if (vat_tp == 2) {
            tr += '<td><input type="number" value=' + ratediscount + ' name="rate_discount_r[' + u +
                ']" class="form-control" readonly></td>';
        }

        tr += '<td><input type="text" value=' + q + ' name="fixed_vat[]" class="form-control" readonly>' +
            '<input type="hidden" value=' + vat_tp + ' name="vat_type[]"  class="form-control" readonly></td>' +
            '<td><input type="text" value=' + vat + ' name="vat_amount[]" class="form-control" readonly></td>' +
            '<td><input type="text" value=' + netrate + ' name="net_rate[]" class="form-control" readonly></td>' +
            '<td><input type="text" value=' + discount + ' name="dis_count[]" class="form-control" readonly>' +
            '<input type="hidden" value=' + discount_type +
            ' name="discount_type[]" class="form-control" readonly></td>' +
            '<td><input type="text" value=' + z +
            ' name="total_amount[]" class="form-control total-amount" readonly><input type="hidden" value=' + p +
            ' name="price[]" class="form-control" readonly></td>' +
            '<td><input type="number" value=' + price_dis +
            ' id="total_amount_wo_discount" name="total_amount_wo_discount[]" class="form-control" readonly>' +
            '<input type="hidden" value=' + total_withoutvat_disc +
            ' id="total_withoutvat_wo_discount" name="total_withoutvat_wo_discount[]" class="form-control" readonly></td>' +
            creditNoteInput + // Add the credit note column if the radio is checked

            '<td><a href="#" class="btn btn-danger remove">-</a></td>' +
            '<input type="hidden" value=' + u + ' name="product_id[]" class="form-control" >' +
            '</tr>';
        $('tbody').append(tr);
        // if (!document.getElementById('show_creditnote').checked) {
        //     $('#show_creditnote').prop('disabled', true);
        // }

        addedProducts[ti].push(u);

        var nu = "";

        $("#qty").val(nu);
        $("#price").val(nu);
        $("#units").val(nu);
        $("#product_name").val(nu);
        $("#mrp").val(nu);
        $("#buycost").val(nu);
        $("#fixed_vat").val(nu);
        $("#vat_amount").val(nu);
        $("#net_rate").val(nu);
        $("#pricex").val(nu);
        $("#number").val(number + 1);
        $("#product_name_name").val(nu);
        $("#product").val(null).trigger('change');
        $("#buyquantity").val(nu);
        $("#vat_type").val(nu);
        $("#creditnote").val(''); // Clear the field after use
        $("#previous_rate").val(''); // Clear the field after use
        $("#total_quantity").val(''); // Clear the field after use


        if (vat_tp == 1) {
            $("#inclusive_rate").val(nu);
        } else if (vat_type == 2) {
            $("#rate_discount").val(nu);
        }

        $("#buycost_rate").val(nu);
        $("#discount").val(nu);
        $("#price_wo_discount").val(nu);
        $("#total_wo_discount").val(nu);
        $("#discount_type").val(nu);

        updateGrandTotal();
        toggleCreditNoteInputVisibility();


    }
    // $('tbody').on('click', '.remove', function() {
    //     $(this).parent().parent().remove();
    // });

   // Function to remove a row
$('tbody').on('click', '.remove', function() {
    var productID = $(this).closest('tr').find('input[name="product_id[]"]').val();
    var transactionID = $(this).closest('tr').find('input[name="trans[]"]').val();

    // Check if the transactionID exists in the addedProducts object
    if (addedProducts.hasOwnProperty(transactionID)) {
        // Find the index of the productID in the array
        const index = addedProducts[transactionID].findIndex(id => id == productID);
        if (index > -1) {
            // Remove the product from the addedProducts array
            addedProducts[transactionID].splice(index, 1);
        }
    }

    // Remove the row
    $(this).closest('tr').remove();

    // After removing the row, check if there are any rows left
        // Enable the radio button if no rows remain
        $('#show_creditnote').prop('disabled', false);

    updateGrandTotal(); // Update the grand total after removal
});

</script>

<script>
    $(document).ready(function() {

        // $('#transaction_id').on('change', function() {

        $('#transaction_id').on('change', function transactionChangeHandler() {

            let trans_id = $(this).val();

            $('#trans_id_origin').val(trans_id);
            $('#trans_id').val(trans_id)

            var nu = "";
            $("#qty").val(nu);
            $("#price").val(nu);
            $("#units").val(nu);
            $("#product_name").val(nu);
            $("#mrp").val(nu);
            $("#buycost").val(nu);
            $("#fixed_vat").val(nu);
            $("#vat_amount").val(nu);
            $("#net_rate").val(nu);
            $("#pricex").val(nu);
            $("#product_name_name").val(nu);
            $("#buyquantity").val(nu);
            $("#vat_type").val(nu);
            $("#inclusive_rate").val(nu);
            $("#discount").val(nu);
            $("#discount_type").val(nu);
            $("#price_wo_discount").val(nu);
            $("#total_wo_discount").val(nu);
            $('#total_discount').val(nu);
            $('#grand_total').val(nu);
            $('#grand_total_wo_discount').val(nu);
            $("#rate_discount").val(nu);
            $('#creditnote').val(nu);

            $('#product').empty().append('<option value="" selected>Select Product</option>');

            $.ajax({
                type: 'GET',
                url: 'getproductdata/' + trans_id,
                success: function(response) {

                    response.product.forEach(element => {
                        $('#product').append(
                            `<option value="${element['id']}">${element['product_name']}</option>`
                        );
                    });

                    $('#paymenttype').val(response.payment_type);

                    if (response.payment_type == 3) {
                        $("#credit").removeClass("hide");
                        $('#credituser').val(response.credit); // Set the credit user's name
                        $('#credituser__id').val(response.credit_id);
                    } else {
                        $("#credit").addClass('hide');
                        $('#credituser').val(0);

                        if (response.cash_user_id != null) {
                            $('#credituser__id').val(response.cash_user_id);
                        } else {
                            $('#credituser__id').val(0);
                        }
                    }
                }
            });

            // Remove the event listener after the transaction is selected
            $(this).off('change', transactionChangeHandler);
            $(this).prop('disabled', true); // Disable the transaction dropdown
        });
        /*----------------------------------------------------------------------*/
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#product').on('change', function() {
            let pro_id = $(this).val();
            let trans_id = $('#transaction_id').val();

            if (pro_id && trans_id) {
                getDataProductDetails(trans_id, pro_id);
            }
        });

        function getDataProductDetails(trans_id, pro_id) {

            $.ajax({
                type: 'GET',
                url: 'getsoldquantity/' + trans_id + '/' + pro_id,
                success: function(response) {

                    var remainingQuantity = response.soldquantity;

                    $('#buyquantity').val(remainingQuantity);

                    if (remainingQuantity > 0) {
                // Set the value in the input field

                // Show the success message
                $('#soldQuantityMessage')
                    .text('Quantity Left: ' + remainingQuantity) // Set text
                    .css('color', 'green') // Optional: Set color to green
                    .show(); // Make it visible
            } else {
                // Clear the input field

                // Show the "no products left" message
                $('#soldQuantityMessage')
                    .text('No quantity left for the products.') // Set text
                    .css('color', 'red') // Optional: Set color to red
                    .show(); // Make it visible
            }
                    var soldquantity = remainingQuantity;
                    $("#qty").attr("max", soldquantity);
                    $('#total_quantity').val(soldquantity);

                    $('.addRow').prop('disabled', false);

                    var sellcost = response.sales_details;
                    $('#mrp').val(sellcost);

                    var oldsellcost = response.sales_details;
                    $('#oldsellcost').val(oldsellcost);
                    $('#previous_rate').val(oldsellcost);

                    var buyycost = response.buycost;
                    $('#buycost').val(buyycost);

                    var fix_vat = response.fixed_vat_value;
                    $('#fixed_vat').val(fix_vat);

                    var vat_type = response.vat_type;
                    $('#vat_type').val(vat_type);

                    var buycost_rate_kv = response.buycost_rate;
                    $('#buycost_rate').val(buycost_rate_kv);

                    var discount = response.discount;
                    $('#discount').val(discount);

                    var discount_type = response.discount_type;
                    $('#discount_type').val(discount_type);

                    var total_discount_percent = response.total_discount_percent;
                    $('#total_discount').val(total_discount_percent);

                    var total_disc_amount = response.total_disc_amount;
                    $('#total_discount_amount').val(total_disc_amount);

                    // net rate and total amnt calculation baased on vat type

                    var vat_type_fetch = $('#vat_type').val();

                    if (vat_type_fetch == 1) {

                        /* extra column add */

                        var inclusive_header = document.getElementById("inclusive_heading");
                        var inclusive_rate_value = document.getElementById("inclusive_rate_value");

                        inclusive_header.style.display = "table-cell";

                        inclusive_rate_value.innerHTML =
                            '<input type="number" step="any" id="inclusive_rate" name="inclusive_rate" class="form-control" readonly>';

                        inclusive_rate_value.style.display = "table-cell";

                        var inclusive_raterr = response.inclusive_rate;
                        $('#inclusive_rate').val(inclusive_raterr);

                        /*----------------------------------*/

                        $('#vat_perc').text('{{$tax}}(%)-Inclus');
                        $('#vat_ammi').text('Total {{$tax}} Amount-Inclus');

                        $("#qty").keyup(function() {

                            var total = 0;
                            var total_wo_discount = 0;
                            var grandtotal_wo_disc = 0;

                            var netrate = Number($("#net_rate").val());
                            var quantity = Number($("#qty").val());
                            var mrp = Number($("#mrp").val());
                            var fixed_vat = Number($("#fixed_vat").val());
                            var inc_rate = Number($("#inclusive_rate").val());
                            var discount_amount = Number($("#discount").val());
                            var discount_type = Number($("#discount_type").val());

                            var inclusive_rate = mrp / (1 + (fixed_vat / 100));
                            // var discount_amount = mrp * (discounts / 100);
                            var wo_disc_vat = mrp - discount_amount;

                            var wovat_wdis = wo_disc_vat / (1 + fix_vat / 100);

                            var inclu_vat = wo_disc_vat - wo_disc_vat / (1 + (fix_vat /
                                100));

                            netrate = wo_disc_vat;
                            grandvat = inclu_vat * quantity;

                            var grandtotal = netrate * quantity;
                            var grandtotal_wo_disc = mrp * quantity;
                            total_wo_discount = inc_rate * quantity;
                            total = wovat_wdis * quantity;

                            grandtotal_wo_disc = Math.round(grandtotal_wo_disc * 1000) /
                                1000;
                            grandtotal = Math.round(grandtotal * 1000) / 1000;
                            var grandvat = Math.round(grandvat * 1000) / 1000;
                            var netrate = Math.round(netrate * 1000) / 1000;

                            $("#pricex").val(total);
                            $("#price").val(grandtotal);
                            $("#vat_amount").val(grandvat);
                            $("#net_rate").val(netrate);
                            $("#price_wo_discount").val(grandtotal_wo_disc);
                            $("#total_wo_discount").val(total_wo_discount);
                        });

                    } else if (vat_type_fetch == 2) {

                        var ratediscount_header = document.getElementById(
                            "ratediscount_heading"
                        );
                        var ratediscount_value = document.getElementById("rate_discount_value");

                        ratediscount_header.style.display = "table-cell";
                        ratediscount_value.innerHTML =
                            '<input type="number" step="any" id="rate_discount" name="rate_discount" class="form-control" readonly>';
                        ratediscount_value.style.display = "table-cell";

                        var exclusive_raterr = response.exclusive_rate;
                        $('#rate_discount').val(exclusive_raterr);

                        $('#vat_perc').text('{{$tax}}(%)-Exclus');
                        $('#vat_ammi').text('Total {{$tax}} Amount-Exclus');

                        $("#qty").keyup(function() {

                            var total = 0;
                            var total_wo_discount = 0;
                            var grandtotal_wo_disc = 0;

                            var netrate = Number($("#net_rate").val());
                            var quantity = Number($("#qty").val());
                            var mrp = Number($("#mrp").val());
                            var fixed_vat = Number($("#fixed_vat").val());
                            var inc_rate = Number($("#inclusive_rate").val());
                            var discount_amount = Number($("#discount").val());
                            var discount_type = Number($("#discount_type").val());

                            // var discount_amount = mrp * (discounts / 100);
                            var wo_disc_vat = mrp - discount_amount;
                            var vat_amount = wo_disc_vat * (fix_vat / 100);

                            netrate = wo_disc_vat + vat_amount;
                            grandvat = vat_amount * quantity;

                            var grandtotal = netrate * quantity;
                            var grandtotal_wo_disc = ((mrp * (fixed_vat / 100)) + mrp) *
                                quantity;
                            total_wo_discount = mrp * quantity;
                            total = (mrp - discount_amount) * quantity;

                            grandtotal_wo_disc = Math.round(grandtotal_wo_disc * 1000) /
                                1000;
                            grandtotal = Math.round(grandtotal * 1000) / 1000;
                            var grandvat = Math.round(grandvat * 1000) / 1000;
                            var netrate = Math.round(netrate * 1000) / 1000;

                            $("#pricex").val(total);
                            $("#price").val(grandtotal);
                            $("#vat_amount").val(grandvat);
                            $("#net_rate").val(netrate);
                            $("#price_wo_discount").val(grandtotal_wo_disc);
                            $("#total_wo_discount").val(total_wo_discount);
                        });
                    }

                    $('#product').change(function() {
                        var selectedValue = $(this).val();

                        if (selectedValue === "") {
                            var nu = "";

                            $("#qty").val(nu);
                            $("#price").val(nu);
                            $("#units").val(nu);
                            $("#product_name").val(nu);
                            $("#mrp").val(nu);
                            $("#buycost").val(nu);
                            $("#fixed_vat").val(nu);
                            $("#vat_amount").val(nu);
                            $("#net_rate").val(nu);
                            $("#pricex").val(nu);
                            $("#product_name_name").val(nu);
                            $("#buyquantity").val(nu);
                            $("#vat_type").val(nu);

                            if (vat_type_fetch == 1) {
                                $("#inclusive_rate").val(nu);
                            } else if (vat_type_fetch == 2) {
                                $("#rate_discount").val(nu);
                            }

                            $("#buycost_rate").val(nu);
                            $("#discount").val(nu);
                            $("#price_wo_discount").val(nu);
                            $("#total_wo_discount").val(nu);
                        }
                    });

                    $(document).ready(function() {
                        $('#qty').on('input', function() {

                            var product = parseFloat($('#qty')
                                .val());

                            if (product > soldquantity) {

                                $('#qty').focus();
                                $('.addRow').off();
                            } else {
                                // $('.addRow').on('click',function() {
                                //         addRow();
                                // });

                                $('.addRow').off().on('click', addRow);
                            }
                        });
                    });
                }
            });
        }
    });
</script>

<script type="text/javascript">
    function doSomething(x) {
        var array = @json($items);

        function isSeries(elm) {
            return elm.id == x;
        }

        var w = array.find(isSeries).product_name;
        $('#product_name_name').val(w);

        // var k = array.find(isSeries).selling_cost;
        // $('#mrp').val(k);

        var y = array.find(isSeries).id;
        $('#product_id').val(y);

        // var t = array.find(isSeries).vat;
        // $('#fixed_vat').val(t);

        // var kv = array.find(isSeries).buy_cost;
        // $('#buycost').val(kv);

        var uni = array.find(isSeries).unit;
        $('#units').val(uni);

        var nu = "";
        $("#qty").val(nu);
        $("#unit").val(nu);
        $("#price").val(nu);
        $("#net_rate").val(nu);
        $("#vat_amount").val(nu);
        $("#discount").val(nu);
        $("#price_wo_discount").val(nu);
        $("#total_wo_discount").val(nu);
        $("#pricex").val(nu);
    }
    $(document).ready(function() {
        $('.js-user').select2({
            theme: "classic"
        });
    });

    // function validateForm() {
    //     // Validate Title
    //     var product = $("#productname").val();
    //     if (product == "" || product == null) {
    //         alert("Press the add button");
    //         return false;
    //     }
    //     return true;
    // }

    /* validate plus double submit removal */

    function validateForm(event) {
        // Prevent the form from submitting multiple times
        const form = document.getElementById("myform");
        const submitBtn = document.getElementById("submitBtn");
        const rows = document.querySelectorAll('tbody tr');

    // Filter rows with valid, visible data
    const validRows = Array.from(rows).filter(row => {
        const isVisible = row.offsetParent !== null; // Check visibility
        const productName = row.querySelector('input[name="productName[]"]')?.value;
        const quantity = row.querySelector('input[name="quantity[]"]')?.value;
        return isVisible && productName && quantity;
    });


        if (submitBtn.disabled) {
            return false; // Prevent form submission
        }

        // Disable the submit button
        submitBtn.disabled = true;
        submitBtn.innerText = "Submitting...";

        // // Validate Title
        // var product = $("#productname").val();
        // if (product == "" || product == null) {
        //     alert("Press the add button");
        //     console.log("Product validation failed.");

        //     // Re-enable the submit button after alert
        //     submitBtn.disabled = false;
        //     submitBtn.innerText = "return";

        //     return false; // Validation failed; prevent form submission
        // }


            if (validRows.length === 0) {
                alert('Press the add button.');
                submitBtn.disabled = false;
                submitBtn.innerText = "Submit";
                return false;
            }




        return true; // Validation passed; allow the form to submit
    }
    $(document).ready(function() {
        $('.product-list').select2({
            theme: "classic"
        });
    });
    $('form input:not([type="submit"])').keydown((e) => {
        if (e.keyCode === 13) {
            e.preventDefault();
            return false;
        }
        return true;
    });
</script>
<script>
    var product_name_name = document.getElementById("product_name");
    var qty = document.getElementById("qty");
    var unitt = document.getElementById("units");
    var mrp = document.getElementById("mrp");
    var fixed_vat = document.getElementById("fixed_vat");
    var vat_amount = document.getElementById("vat_amount");
    var net_rate = document.getElementById("net_rate");
    var price = document.getElementById("price");
    product_name_name.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    qty.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    unitt.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    mrp.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    fixed_vat.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    vat_amount.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    net_rate.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    price.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
</script>
<script>
    $(document).ready(function() {
        // When the invoice number is selected
        $('#transaction_id').change(function() {
            var transactionId = $(this).val();
            if (transactionId) {
                $.ajax({
                    url: '{{ route("getInvoiceDetails") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        transaction_id: transactionId
                    },
                    success: function(response) {


                        // Populate customer name, total due, invoice due
                        $('#customer_name').val(response.customer_name);
                        $('#total_due').val(response.total_due);
                        $('#invoice_due').val(response.invoice_due);
                        $('#credit_noteamount').val(response.credit_note_amount);
                        $('#balance_due').val(response.balance_due);
                        $('#collected_amount').val(response.collected_amount);
                        $('#returnProductsAmount').val(response.returnProductsAmount);


                        // Clear existing product rows
                        $('#product_table').empty();

                        // Populate product rows
                        $.each(response.products, function(index, product) {
                            // if (product.quantity === 0) {
                            //     // If the product is fully returned
                            //     $('#product_table').append(
                            //         '<tr>' +
                            //         '<td width="20%"><input type="hidden" name="product_name[]" value="' + product.product_name + '">' + product.product_name + '</td>' +
                            //         '<td width="20%">-</td>' + // No selling cost for returned products
                            //         '<td width="20%">Returned</td>' + // Mark as returned
                            //         '<td width="20%">-</td>' + // No total for returned products
                            //         '<td width="20%"><input type="text" name="credit_note_amount[]" placeholder="Enter amount" class="form-control" readonly></td>' +
                            //         '</tr>'
                            //     );
                            // } else {
                                $('#product_table').append(
                                    '<tr>' +
                                    '<td width="20%"><input type="hidden" name="product_name[]" value="' + product.product_name + '">' + product.product_name + '</td>' +
                                    '<td width="20%"><input type="hidden" name="sell_cost[]" value="' + product.bill_grand_total + '">' + product.bill_grand_total + '</td>' +
                                    '<td width="20%"><input type="hidden" name="quantity[]" value="' + product.quantity + '">' + product.quantity + '</td>' +
                                    '<td width="20%"><input type="hidden" name="total[]" value="' + product.total_amount + '">' + product.total_amount + '</td>' +
                                    // Check if quantity is numeric
                                    '<td width="20%"><input type="text" name="credit_note_amount[]" placeholder="Enter amount" class="form-control"' +
                                    (isNaN(product.quantity) || product.quantity === '' ? ' readonly' : '') +
                                    '></td>' +
                                    '</tr>'
                                );

                            // }
                        });
                    },
                    error: function(error) {
                        console.log(error);
                        alert('Error fetching details. Please try again.');
                    }
                });
            }
        });
    });
</script>
{{-- <script>
    function changesellcost() {
        event.preventDefault(); // Prevent page refresh

        const transactionId = document.getElementById("transaction_id").value;
        const page = document.getElementById("page").value; // Get page name from hidden input

        if (transactionId) {
            const url = `/credit/${page}/${transactionId}`; // Prepend "credit" to the URL
            window.location.href = url;
        } else {
            alert("Please select a Transaction ID.");
        }
}

</script> --}}

<script>
$(document).ready(function() {
    // Listen for changes in the creditnote, mrp, and qty input fields
    $('#creditnote,#qty').on('input', function() {
        var creditnote = parseFloat($('#creditnote').val()) || 0; // Get the creditnote value
        var mrp = parseFloat($('#mrp').val()) || 0; // Get the MRP value
        var qty = parseFloat($('#qty').val()) || 0; // Get the quantity
        var oldsellcost = parseFloat($('#oldsellcost').val()) || 0; // Default to 0 if empty
        var vatPercentage = parseFloat($('#fixed_vat').val()) || 0; // VAT percentage
        var discountAmount = parseFloat($('#discount').val()) || 0; // Discount percentage
        var vatType = $('#vat_type').val(); // VAT type (1 or 2)
        var total_quantity = parseFloat($('#total_quantity').val()) || 0; // Get the quantity
        // Validate input values (ensure all values are valid numbers)
        if (isNaN(creditnote) || isNaN(mrp) || isNaN(qty) || qty <= 0) {
            return; // If any values are invalid, exit
        }

        // Step 1: Calculate the new MRP
        var newMrp = oldsellcost - (creditnote / total_quantity); // New MRP after applying creditnote

        $('#mrp').val(newMrp.toFixed(2)); // Update MRP with the calculated new MRP


        // Handle VAT Type 1 (Inclusive VAT)
        if (vatType == '1') {
            var inclusiveRate = newMrp / (1 + vatPercentage / 100); // Calculate inclusive rate by removing VAT
            var vatAmountInclusive = inclusiveRate * (vatPercentage / 100) * qty; // VAT amount (inclusive)
            var vatAmountInclusivediscount = (inclusiveRate -(inclusiveRate/1+(vatPercentage/100))) * qty;
            var totalAmountInclusive = qty * newMrp; // Total amount with MRP
            var totalWithoutDiscountInclusive = totalAmountInclusive; // Total without discount

            // Apply discount if present
            if (discountAmount > 0) {
                var netRateWithDiscount = newMrp - discountAmount;
                console.log("netRateWithDiscount: " + netRateWithDiscount);
                var vatAmountInclusivediscount = (netRateWithDiscount -(netRateWithDiscount/(1+(vatPercentage/100)))) * qty;
                var totalAmountWithDiscount = qty * netRateWithDiscount;
                var totalWithoutDiscountWithDiscount = newMrp * qty;

                $('#inclusive_rate_value').html('<input type="number" step="any" id="inclusive_rate" class="form-control" value="' + netRateWithDiscount.toFixed(3) + '" readonly>');
                $('#price').val(totalAmountWithDiscount.toFixed(3));
                $('#price_wo_discount').val(totalWithoutDiscountWithDiscount.toFixed(3));
                $('#net_rate').val(netRateWithDiscount.toFixed(3));
                $('#vat_amount').val(vatAmountInclusivediscount.toFixed(3));
            } else {
                $('#inclusive_rate_value').html('<input type="number" step="any" id="inclusive_rate" class="form-control" value="' + inclusiveRate.toFixed(3) + '" readonly>');
                $('#price').val(totalAmountInclusive.toFixed(3));
                $('#price_wo_discount').val(totalWithoutDiscountInclusive.toFixed(3));
                $('#net_rate').val(newMrp.toFixed(3));
                $('#vat_amount').val(vatAmountInclusive.toFixed(3));
            }

            $('#inclusive_rate_value').show();
            $('#rate_discount_value').hide();
        }

        // Handle VAT Type 2 (Exclusive VAT)
        else if (vatType == '2') {
            var exclusiveRate = newMrp - discountAmount; // Set exclusive rate equal to the MRP value dynamically
            var vatAmountExclusive = (exclusiveRate * vatPercentage / 100) * qty; // VAT amount (exclusive)
            var netRateExclusive = exclusiveRate + vatAmountExclusive / qty; // Net rate (exclusive rate + VAT per unit)
            var totalAmountExclusive = qty * netRateExclusive; // Total amount (quantity * net rate including VAT)
            var totalWithoutDiscountExclusive = qty * exclusiveRate; // Total without discount (exclusive)
            // var vatAmountexclusivediscount=exclusiveRate-(exclusiveRate*(vatPercentage/100));

            // Apply discount if present
            if (discountAmount > 0) {
                var netRateWithDiscountExclusive = exclusiveRate - (exclusiveRate * discountAmount / 100);
                var totalAmountWithDiscountExclusive = qty * netRateExclusive;
                var totalWithoutDiscountWithDiscountExclusive = qty * (newMrp + newMrp * (vatPercentage / 100));

                $('#rate_discount_value').html('<input type="number" step="any" id="rate_discount" class="form-control" value="' + exclusiveRate.toFixed(3) + '" readonly>');
                $('#price').val(totalAmountWithDiscountExclusive.toFixed(3));
                $('#price_wo_discount').val(totalWithoutDiscountWithDiscountExclusive.toFixed(3));
                $('#vat_amount').val(vatAmountExclusive.toFixed(3));
                $('#net_rate').val(netRateExclusive.toFixed(3));
            } else {
                $('#rate_discount_value').html('<input type="number" step="any" id="rate_discount" class="form-control" value="' + exclusiveRate.toFixed(3) + '" readonly>');
                $('#price').val(totalAmountExclusive.toFixed(3));
                $('#price_wo_discount').val(totalAmountExclusive.toFixed(3));
                $('#vat_amount').val(vatAmountExclusive.toFixed(3));
                $('#net_rate').val(netRateExclusive.toFixed(3));
            }

            $('#rate_discount_value').show();
            $('#inclusive_rate_value').hide();
        }
    });
});



</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const showCreditNoteRadio = document.getElementById('show_creditnote'); // Radio button for Credit Note
    const creditNoteHeader = document.getElementById('creditnote_header'); // Table Header for Credit Note
    const creditNoteCell = document.getElementById('creditnote_cell'); // Table Cell for Credit Note
    const tbody = document.querySelector('tbody'); // Table body

    // Function to toggle visibility of header and cell
    showCreditNoteRadio.addEventListener('change', function () {
        if (showCreditNoteRadio.checked) {
            // Show the header and cell for Credit Note
            creditNoteHeader.style.display = 'table-cell';
            creditNoteCell.style.display = 'table-cell';
        } else {
            // Hide the header and cell for Credit Note
            creditNoteHeader.style.display = 'none';
            creditNoteCell.style.display = 'none';
        }

        // Now toggle visibility of credit note input field in the table rows
        toggleCreditNoteInputVisibility();
    });

    // Function to handle credit note input field visibility in table rows
    function toggleCreditNoteInputVisibility() {
        if (showCreditNoteRadio.checked) {
            // Show credit note input in all rows where applicable
            tbody.querySelectorAll('.credit-note-input').forEach(input => {
                input.style.display = 'block'; // Show the input
            });
        } else {
            // Hide credit note input in all rows
            tbody.querySelectorAll('.credit-note-input').forEach(input => {
                input.style.display = 'none'; // Hide the input
            });
        }
    }

    // Initial setup if the page is loaded and the radio is checked
    toggleCreditNoteInputVisibility();
});

</script>
<script>
    $(document).ready(function() {
    // Listen for changes in the creditnote field
    $('#creditnote').on('input', function() {
        var creditNoteValue = $('#creditnote').val(); // Get the value of creditnote field
        var qtyValue = $('#qty').val(); // Get the value of qty field

        // Check if creditnote is filled but qty is empty
        if (creditNoteValue !== "" && qtyValue === "") {
            alert("Please fill in the quantity first."); // Alert message
            $('#creditnote').val(''); // Clear the creditnote field (optional)
        }
    });
});

</script>

