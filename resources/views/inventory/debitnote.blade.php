<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Debit Note</title>
    @include('layouts/usersidebar')
    <style>
        table {
            border: solid black 1px;
            border-collapse: separate;
            border-left: solid black 1px;
            border-radius: 10px;
            border-spacing: 0px;
        }

        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
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

        #content {
            padding: 30px;
        }

        .select2-container .select2-choice {
            height: 35px;
            line-height: 35px;
        }

        ul.select2-results {
            max-height: 100px;
        }

        h2 {
            margin-bottom: 2rem;
        }
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
        <div style="margin-left:-15px;margin-top:-18px;">

            @include('navbar.invnavbar')
        </div>
        @else
<x-logout_nav_user />
@endif
<div align="right">
    <a href="/debitnote_history" class="btn btn-info ">Debit Note History</a>
    <a href="/supplier_summary" class="btn btn-info ">Supplier Summary</a>
    <a href="" class="btn btn-info">Refresh</a>
</div>
<x-admindetails_user :shopdatas="$shopdatas" />

        @if (session('success'))
        <div class="alert alert-success" style="text-align: center;">
            {{ session('success') }}
        </div>
        @endif
        <form method="post" action="debitnotesubmit" id="myform" onsubmit="return validateForm()">
            @csrf
            <h2>Debit Note</h2>

            <div class="row">
                <div class="form group col-md-1">
                    <select id="reciept_no" name="reciept_no" class="js-user" style="width: 250px;">
                        <option selected value="">Select Receipt No.</option>
                        @foreach ($receiptnos as $receiptno)
                            <option value="{{ $receiptno }}">{{ $receiptno }}</option>
                        @endforeach
                    </select>
                    <span style="color:red">
                        @error('transaction_id')
                            {{ $message }}
                        @enderror
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <select id="product" class="js-user" style="width: 250px">
                        <option selected value="">Select Product</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <div class="input-group gap hide" id="credit">
                        <span class="input-group-addon" id="basic-addon1" style="width: 90px;display:none;">CREDIT SUPPLIER</span>
                        <input type="hidden" style="width: 170px;" id="creditsupplier" name="creditsupplier" value=""
                            class="form-control" readonly>
                    </div>
                </div>
            </div>
            <br>

            <div class="input-container">
                <div class="input-box">
                    <label>SUPPLIER NAME</label>
                    <input type="text" id="supplier" name="supplier" readonly>
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
                {{-- <div class="input-box">
                    <label>RETURN AMOUNT</label>
                    <input type="text" id="returnProductsAmount" name="returnProductsAmount" readonly>
                </div> --}}

                <div class="input-box">
                    <label>DEBIT NOTE AMOUNT</label>
                    <input type="text" id="debit_noteamount" name="credit_noteamount" readonly>
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
                        <th width="8%">Reciept No</th>
                        <th width="7%">Total Quantity</th>
                        {{-- <th width="10%">Comment</th> --}}
                        <th width="7%">Supplier Name</th>
                        <th width="10%">Product</th>
                        <th width="6%">Quantity</th>
                        <th width="5%">Unit</th>
                        <th width="8%">Previous Buycost</th>
                        <th width="8%">Buycost</th>
                        <th width="8%">{{$tax}}(%)</th>
                        <th width="8%">Rate</th>
                        <!--{{-- <th width="9%">VAT (%)</th>-->
                        <!--<th width="9%">VAT amount</th> --}}-->
                        <th width="10%">AMOUNT <br />(without {{$tax}})</th>
                        <th width="10%">AMOUNT <br />(with {{$tax}})</th>
                        <th width="11%">Debit Note<br /> Amount</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr bgcolor="#187f6a">
                        <td>
                            <input type="text" id="reciept_name" name="reciept_name" class="form-control" readonly>

                            <input type="hidden" id="purchase_main_id" name="purchase_main_id" class="form-control"
                            readonly>

                            <input type="hidden" id="purchase_unique_trans_id" name="purchase_unique_trans_id"
                            class="form-control" readonly>
                        </td>
                        <td>
                            <input type="text" id="total_quantity" name="total_quantity" class="form-control" readonly>

                        </td>
                        <td><input type="text" id="shopname" class="form-control shopname" readonly>
                            <input type="hidden" id="suppid" name="suppid" readonly>
                        </td>
                        {{-- <td><input type="text" id="comment" class="form-control comment"></td> --}}
                        <td><input type="text" id="product_name" class="form-control product" readonly></td>
                        <td><input type="text" id="quantity" step="0.001" class="form-control quantity"
                                min="0" max="">
                        </td>
                        <td> <input type="text" id="unit" name="unit" class="form-control" readonly></td>
                        <td><input type="text" id="previous_rate" class="form-control" tabindex="4" readonly></td>

                        <td> <input type="text" id="buycost" name="buycost" class="form-control" readonly></td>
                        <td><input type="text" name="vat" id="vat" class="form-control" readonly></td>
                        <td><input type="text" name="rate" id="rate" class="form-control" readonly></td>
                        <!-- -->

                        <!--{{-- <td> <input type="text" id="vat_percen" name="vat_percen" class="form-control" readonly></td>-->
                        <!--<td> <input type="text" id="vat_amount" name="vat_amount" class="form-control" readonly>-->
                        <!--</td> --}}-->

                        <!-- -->
                        <td><input type="number" id="withoutvat" name="withoutvat" class="form-control amount"
                                readonly></td>
                        <td><input type="number" id="amount" class="form-control amount" readonly></td>
                        <td>
                            <input type="text" id="creditnote" step="1" class="form-control" min="0" max="" tabindex="5">
                        </td>
                        <td><a href="#" class="btn btn-info addRow">+</a></td>
                        <input type="hidden" id="p_id" class="form-control">
                        <input type="hidden" id="remain_purchase_stock" name="remain_purchase_stock"
                            class="form-control">
                        <input type="hidden" id="paymenttype" name="paymenttype" class="form-control">

                    </tr>
                    <tr>
                        <td colspan="11"> <i class="glyphicon glyphicon-tags"></i> &nbsp Return Purchase
                        </td>
                    <tr>
                </tbody>
            </table>

            <input class="btn btn-primary" type="submit" value="submit" id="submitBtn">
        </form>
    </div>
</body>

</html>

<!-- Load Product in receipt number -->
<script>
    $(document).ready(function() {

        $('#reciept_no').on('change', function() {

            let reciept_no = $(this).val();

            $('#reciept_name').val(reciept_no);

            var nu = "";
            $("#quantity").val(nu);
            $("#unit").val(nu);

            $("#buycost").val(nu);
            $("#amount").val(nu);
            $("#product_name").val(nu);
            $("#comment").val(nu);

            $("#rate").val(nu);
            $("#vat").val(nu);

            // $("#vat_percen").val(nu);
            // $("#vat_amount").val(nu);

            $("#withoutvat").val(nu);

            $("#purchase_main_id").val(nu);
            $("#purchase_unique_trans_id").val(nu);

            $("#suppid").val(nu);
            $("#shopname").val(nu);

            $('#product').empty().append('<option value="" selected>Select Product</option>');

            $.ajax({
                type: 'GET',
                url: 'get_purchase_product/' + reciept_no,
                success: function(response) {

                    response.productdatas.forEach(element => {

                        $('#product').append(
                            `<option value="${element['product']}">${element['product_name']}</option>`
                        );
                    });

                    $('#paymenttype').val(response.payment_type);

                    //credit

                    if (response.payment_type == 2) {
                        response.creditsupplier.forEach(creditsupplier => {

                            $("#credit").removeClass("hide");

                            $('#creditsupplier').val(creditsupplier['supplier']);

                        });
                    } else if (response.payment_type !== 2) {

                        $("#credit").addClass('hide');
                    }
                }
            });
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#product').on('change', function() {
            let pro_id = $(this).val();
            let receipt_No = $('#reciept_no').val();

            if (pro_id && receipt_No) {

                getPurchaseProdetails(pro_id, receipt_No);
            }
        });

        $('#reciept_no').on('change', function() {
            let receipt_No = $(this).val();

            $('#product').val('').trigger('change'); // reset product dropdown

            let pro_id = $('#product').val();

            if (pro_id && receipt_No) {

                getPurchaseProdetails(pro_id, receipt_No);

            } else {
                // If either transaction or product is not selected, disable the addRow button
                $('.addRow').off();
            }

        });

        function getPurchaseProdetails(pro_id, receipt_No) {

            $.ajax({
                type: 'GET',
                // url: 'getremainstock_purchase/' + receipt_No + '/' + pro_id,
                success: function(response) {

                    response.purchase_Data.forEach(element => {
                        console.log(response.purchase_Data); // Logs the entire purchase_Data array

                        var pp_id = element['product'];
                        $('#p_id').val(pp_id);

                        var p_name = element['product_name'];
                        $('#product_name').val(p_name);

                        var k = element['supplier'];
                        $('#shopname').val(k);

                        var kid = element['supplier_id'];
                        $('#suppid').val(kid);

                        var paymode = element['payment_mode'];
                        $('#paymenttype').val(paymode);

                        var buycost = element['buycost'];
                        $('#buycost').val(buycost);
                        $('#previous_rate').val(buycost);

                        var unit = element['unit'];
                        $('#unit').val(unit);

                        var rate = element['rate'];
                        $('#rate').val(rate);

                        var vat = element['vat'];
                        $('#vat').val(vat);

                        var p_main_id = element['id'];
                        $('#purchase_main_id').val(p_main_id);

                        var p_unique_trans_ID = element['purchase_trans_id'];
                        $('#purchase_unique_trans_id').val(p_unique_trans_ID);

                        /*---------------------------------------------------------------*/

                        var quanty = element['quantity'];
                        if (quanty !== undefined && quanty !== null) {
                            $('#total_quantity').val(Math.round(quanty)); // This will round to the nearest integer
                        } else {
                            console.log('Quantity is not available');
                        }
                        var purchaseamt = element['price'];

                        var price_1 = (purchaseamt / quanty);

                        $('#quantity').on('change', function() {

                            var return_quantity = $('#quantity').val();

                            var current_price = (rate *
                                return_quantity);

                            var current_price = parseFloat(current_price
                                .toFixed(3));

                            $('#amount').val(current_price);

                            /*---------- without vat --------------*/


                            var withoutvat = (buycost * return_quantity)

                            var withoutvat = parseFloat(withoutvat
                                .toFixed(3));

                            $('#withoutvat').val(withoutvat);
                            /*-------------------------------------*/
                        });

                        $('#quantity').on('keyup', function() {

                            var return_quantity = $('#quantity').val();

                            // vat

                            // var vatam = (vat_percen * buycost *
                            //     return_quantity) / 100;
                            // $('#vat_amount').val(vatam.toFixed(5));

                            //

                            // var vatamf = parseFloat(vatam.toFixed(5));

                            // var current_price = (buycost *
                            //     return_quantity) + vatamf;
                            // // price of returning products

                            var current_price = (rate *
                                return_quantity);

                            var current_price = parseFloat(current_price
                                .toFixed(3));

                            $('#amount').val(current_price);

                            /*---------- without vat --------------*/

                            var withoutvat = (buycost * return_quantity)

                            var withoutvat = parseFloat(withoutvat
                                .toFixed(3));

                            $('#withoutvat').val(withoutvat);
                            /*-------------------------------------*/
                        });

                        /*---------------------------------------------------------------*/

                    });

                    response.purchase_remain.forEach(element => {

                        $('#remain_purchase_stock').val(element[
                            'sell_quantity']);

                        var purchase_remain = $('#remain_purchase_stock').val();

                        $("#quantity").attr("max", purchase_remain);

                        $(document).ready(function() {
                            $('#quantity').on('input', function() {

                                var product = parseFloat($(
                                    '#quantity').val());

                                if (product > purchase_remain) {
                                    $('.addRow').off();
                                } else {
                                    // $('.addRow').on('click',function() {
                                    //         addRow();
                                    // });

                                    $('.addRow').off().on(
                                        'click', addRow);
                                }
                            });
                        });
                    });

                    var nu = "";
                    $("#quantity").val(nu);
                    $("#comment").val(nu);
                    $("#amount").val(nu);
                    $("#withoutvat").val(nu);
                    $('#comment').focus();
                }
            });

        }
    });
</script>

<script type="text/javascript">
    var addedProductsByReceipt = {};

    // $('.addRow').on('click', function() {
    //     addRow();
    // });

    $('.addRow').off().on('click', addRow);

    function addRow() {
        var y = ($("#reciept_no").val());
        if (($("#reciept_no").val()) == "") {
            return;
        }
        var z = ($("#comment").val());
        // if (($("#comment").val()) == "") {
        //     return;
        // }
        var p = ($("#product_name").val());
        if (($("#product_name").val()) == "") {
            return;
        }
        var q = Number($("#quantity").val());
        if (($("#quantity").val()) == "") {
            return;
        }
        var w = Number($("#amount").val());
        if (($("#amount").val()) == "") {
            return;
        }
        var t = ($("#shopname").val());
        if (($("#shopname").val()) == "") {
            return;
        }
        var name = ($("#reciept_name").val());
        if (($("#reciept_name").val()) == "") {
            return;
        }

        var pid = ($("#p_id").val());

        var pt = ($("#paymenttype").val());

        var Buycost = ($("#buycost").val());

        var unit = ($("#unit").val());

        // var vatperc = ($("#vat_percen").val());

        // var vatamo = ($("#vat_amount").val());

        var rate_value = ($("#rate").val()) || 0;
        var vat_value = ($("#vat").val()) || 0;

        var withoutvat_amount = Number($("#withoutvat").val());

        var pid_main = $("#purchase_main_id").val();

        var ptransid_unique = $("#purchase_unique_trans_id").val();

        var suppli_id = $("#suppid").val();
        var pr = Number($("#previous_rate").val());
        var tq = ($("#total_quantity").val());

        // var credit = Number($("#creditnote").val());
        // if (($("#creditnote").val()) == "") {
        //     return;
        // }
        // if (!credit) {
        // alert("Debit note amount is required.");
        // return;
        // }
        var credit = Number($("#creditnote").val().trim()); // Parse input value as a number
        if (!$("#creditnote").val().trim()) { // Check for empty input
            alert("Debit note amount is required.");
            return;
        }
        if (isNaN(credit) || credit <= 0) { // Validate if input is a valid positive number
            alert("Please enter a valid debit note amount.");
            return;
        }

        if (!addedProductsByReceipt[y]) {
            addedProductsByReceipt[y] = [];
        }

        // Check if the product is already added for this receipt number
        if (addedProductsByReceipt[y].includes(pid)) {
            // Product already added for this receipt number, show an alert message
            alert("Product is already added for this receipt number.");
            return;
        }
        var creditNoteInput = '';
        creditNoteInput = '<td><input type="text" value="' + credit + '" name="credit_note['+ pid_main +']" class="form-control credit-note-input" readonly></td>';



        var tr = '<tr>' + '<td>' +
            '<input type="text" value="' + name + '" name="reciept_no[' + pid_main +
            ']" class="form-control" readonly><input type="hidden" value="' + pt + '" name="ptype[' + pid_main +
            ']" class="form-control">' +
            '<input type="hidden" value="' + pid_main + '" id="p_main_id" name="p_main_id[' + pid_main +
            ']" class="form-control" readonly>' +
            '<input type="hidden" value="' + ptransid_unique + '" id="p_unique_trans_id" name="p_unique_trans_id[' +
            pid_main + ']" class="form-control" readonly>' +
            '</td>' +
            '<td><input type="text" value=' + tq + ' name="total_quantity[]" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + t +
            '" name="shop_name[' + pid_main + ']" class="form-control" readonly><input type="hidden" value="' +
            suppli_id +
            '" name="supplid[' + pid_main + ']" class="form-control" readonly></td>' +

            '<td><input type="text" value="' + p + '" name="product[' + pid_main +
            ']" id="productname" class="form-control" readonly><input type="hidden" value="' + pid +
            '" name="p_id[' + pid_main + ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + q + '"  name="quantity[' + pid_main +
            ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + unit + '" name="units[' + pid_main +
            ']" class="form-control" readonly></td>' +
            '<td><input type="text" value=' + pr + ' name="previous_rate[]" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + Buycost + '" name="buycosts[' + pid_main +
            ']" class="form-control" readonly></td>' +
            '<td><input type="text" value=' + vat_value + ' id="vat_r" name="vat_r[' + pid_main +
            ']" class="form-control" readonly> </td>' +
            '<td><input type="text" value=' + rate_value + ' id="rate_r" name="rate_r[' + pid_main +
            ']" class="form-control" readonly> </td>' +
            // '<td><input type="text" value="' + vatperc +
            // '" name="vatpercentages[' + pid_main + ']" class="form-control" readonly></td>' +
            // '<td><input type="text" value="' + vatamo + '" name="vatamounts[' + pid_main +
            // ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + withoutvat_amount +
            '" name="withoutvat[' + pid_main + ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + w + '" name="amount[' + pid_main +
            ']" class="form-control" readonly></td>' +
            creditNoteInput + // Add the credit note column if the radio is checked

            '<td><a href="#" class="btn btn-danger remove">-</a></td>' +
            '</tr>';
        $('tbody').append(tr);

        addedProductsByReceipt[y].push(pid);

        var nu = "";
        $("#quantity").val(nu);
        $("#unit").val(nu);
        $("#buycost").val(nu);
        $("#amount").val(nu);
        $("#product_name").val(nu);
        $("#comment").val(nu);
        // $("#reciept_no").val(null).trigger('change');
        $("#product").val(null).trigger('change');

        // $("#vat_percen").val(nu);
        // $("#vat_amount").val(nu);

        $("#rate").val(nu);
        $("#vat").val(nu);

        $("#withoutvat").val(nu);

        $("#purchase_main_id").val(nu);
        $("#purchase_unique_trans_id").val(nu);
        $("#creditnote").val(''); // Clear the field after use
        $("#previous_rate").val(''); // Clear the field after use
        $("#total_quantity").val(''); // Clear the field after use


    };

    // $('tbody').on('click', '.remove', function() {
    //     $(this).parent().parent().remove();
    // });

    $('tbody').on('click', '.remove', function() {
        var row = $(this).closest("tr");
        var receiptNo = row.find('[name^="reciept_no"]').val();
        var productId = row.find('[name^="p_id"]').val();

        // Check if the receipt number is in the dictionary
        if (addedProductsByReceipt[receiptNo]) {
            var index = addedProductsByReceipt[receiptNo].indexOf(productId);
            if (index !== -1) {
                addedProductsByReceipt[receiptNo].splice(index, 1);
                console.log(addedProductsByReceipt);
            }
        }

        // Remove the row
        row.remove();
    });
</script>


<script type="text/javascript">
    /* validate plus double submit removal */

    function validateForm() {
        // Prevent the form from submitting multiple times
        const form = document.getElementById("myform");
        const submitBtn = document.getElementById("submitBtn");
        var payment_mode_val = $('input[name="payment_mode"]:checked').val();
        var bankRadio = document.querySelector('input[name="payment_mode"][value="2"]');
        var accountSelect = document.getElementById('bank-dropdown');


        if (submitBtn.disabled) {
            return false; // Prevent form submission
        }

        // Disable the submit button
        submitBtn.disabled = true;
        submitBtn.innerText = "Submitting...";

        // Validate Title
        var product = $("#productname").val();
        if (product == "" || product == null) {
            alert("Press the add button");

            // Re-enable the submit button after alert
            submitBtn.disabled = false;
            submitBtn.innerText = "submit";

            return false; // Validation failed; prevent form submission
        }



        return true; // Validation passed; allow the form to submit
    }
</script>

<script>
    $(document).ready(function() {
        $('.js-user').select2({
            theme: "classic"
        });
    });

    /* To submit form data by clicking ENTER BUTTON */
    $('form input:not([type="submit"])').keydown((e) => {
        if (e.keyCode === 13) {
            e.preventDefault();
            return false;
        }
        return true;
    });
</script>

<script type="text/javascript">
    /* To Move pointer to next input field by clicking ENTER BUTTON */

    var currentBoxNumber = 0;

    $(".comment").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.product");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".product").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.quantity");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".quantity").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.amount");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".amount").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.shopname");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
</script>

<script>
    /* To submit form data by clicking ENTER BUTTON */

    var reciept_name = document.getElementById("reciept_name");
    var comment = document.getElementById("comment");
    var productp = document.getElementById("product_name");
    var quantity = document.getElementById("quantity");
    var amount = document.getElementById("amount");
    var shopname = document.getElementById("shopname");
    reciept_name.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    comment.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    productp.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    quantity.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    amount.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    shopname.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function toggleDropdown() {
            var bankRadio = document.querySelector('input[name="payment_mode"][value="2"]');
            var dropdown = document.getElementById('bank-dropdown');
            var accountNameInput = document.getElementById('account_name');

            if (bankRadio.checked) {
                dropdown.style.display = 'inline-block';
            } else {
                dropdown.style.display = 'none';
                accountNameInput.value = '';
            }
        }
        var paymentModeRadios = document.querySelectorAll('input[name="payment_mode"]');
        paymentModeRadios.forEach(function(radio) {
            radio.addEventListener('change', toggleDropdown);
        });
        document.getElementById('bank-dropdown').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var accountNameInput = document.getElementById('account_name');
        var bankIdInput = document.getElementById('bank_id');
        accountNameInput.value = selectedOption.text.split('(')[1]?.replace(')', '') || '';
        bankIdInput.value = selectedOption.value || '';
        });
            toggleDropdown();
        });
</script>

<script>
    document.getElementById('bank-dropdown').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const currentBalance = selectedOption.getAttribute('data-current-balance');
        document.getElementById('current_balance').value = currentBalance;
    });
</script>
<script>
        $(document).ready(function() {
            $('#reciept_no').on('change', function() {
                var recieptNo = $(this).val();

                if (recieptNo) {
                    $.ajax({
                        url: "{{ route('getDebitInvoiceDetails') }}",  // Replace with your route
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            reciept_no: recieptNo
                        },
                        success: function(response) {

                            // Populate the fields with response data
                            $('#supplier').val(response.customer_name);
                            $('#total_due').val(response.total_due);
                            $('#invoice_due').val(response.invoice_due);
                            $('#debit_noteamount').val(response.debit_note_amount);
                            $('#balance_due').val(response.balance_due);
                            $('#collected_amount').val(response.collected_amount);


                            // Clear previous product rows
                            $('#product_table').empty();

                            // Populate product rows
                            $.each(response.products, function(index, product) {
                                var productRow;

                    if (product.quantity === 'returned') {
                        // If the product is fully returned, display 'Returned'
                        productRow = `<tr>
                            <td width="20%">${product.product_name}</td>
                            <td width="20%">-</td>
                            <td width="20%">Returned</td>
                            <td width="20%">-</td>
                            <td width="20%"><input type="text" name="debit_note_amount[]" placeholder="Enter amount" class="form-control" readonly></td>

                            <!-- Hidden fields to store product data -->
                            <input type="hidden" name="product[]" value="${product.product_name}">
                            <input type="hidden" name="product_id[]" value="${product.product}">
                            <input type="hidden" name="sellingcost[]" value="${product.rate}">
                            <input type="hidden" name="quantity[]" value="0"> <!-- No quantity as it's returned -->
                            <input type="hidden" name="total[]" value="0"> <!-- No total as it's returned -->
                        </tr>`;
                    } else {
                                productRow = `<tr>
                                    <td  width="20%">${product.product_name}</td>
                                    <td  width="20%">${product.rate}</td>
                                    <td  width="20%">${product.quantity}</td>
                                    <td  width="20%">${product.price}</td>
                                    <td width="20%"><input type="text" name="debit_note_amount[]" placeholder="Enter amount" class="form-control"></td>

                                    <!-- Hidden fields to store product data -->
                                    <input type="hidden" name="product[]" value="${product.product_name}">
                                    <input type="hidden" name="product_id[]" value="${product.product}">
                                    <input type="hidden" name="sellingcost[]" value="${product.rate}">
                                    <input type="hidden" name="quantity[]" value="${product.quantity}">
                                    <input type="hidden" name="total[]" value="${product.price}">
                                </tr>`;
                            }
                                $('#product_table').append(productRow);
                            });
                        },
                        error: function(error) {
                            console.log(error);
                            alert('Error fetching details. Please try again.');
                        }
                    });
                } else {
                    // Clear fields if no bill is selected
                    $('#supplier').val('');
                    $('#total_due').val('');
                    $('#invoice_due').val('');
                    $('#product_table').empty();
                }
            });
        });
    </script>
<script>
    $(document).ready(function () {
        // Listen for changes in the relevant input fields
        $('#creditnote, #quantity').on('input', function (event) {
            // Parse input values or default to 0 if invalid
            var creditnote = parseFloat($('#creditnote').val()) || 0;
            var quantity = parseFloat($('#quantity').val()) || 0;
            var buycost = parseFloat($('#buycost').val()) || 0;
            var vat = parseFloat($('#vat').val()) || 0;
            var previous_rate = parseFloat($('#previous_rate').val()) || 0;
            var total_quantity = parseFloat($('#total_quantity').val()) || 0;

            // If quantity is changed, reset creditnote to null
            if (event.target.id === 'quantity') {
                $('#creditnote').val(null); // Set creditnote field to null
                creditnote = 0; // Treat creditnote as 0 for calculations
            }

            // Ensure quantity is not zero to avoid division errors
            if (quantity === 0) {
                $('#buycost').val("0.00");
                $('#rate').val("0.00");
                $('#withoutvat').val("0.00");
                $('#amount').val("0.00");
                return;
            }

            // Calculate adjusted Buy Cost
            var adjustedBuyCost = previous_rate - (creditnote / total_quantity);

            // Update the previous rate based on creditnote adjustment
            var adjustedRate = adjustedBuyCost;

            // Calculate without VAT
            var withoutVat = adjustedRate * quantity;

            // Calculate VAT amount
            var vatAmount = adjustedRate * (vat / 100);

            // Calculate final rate including VAT
            var rate = adjustedRate * (1 + (vat / 100));

            // Calculate total amount including VAT
            var amount = rate * quantity;

            // Update the relevant fields
            $('#buycost').val(adjustedBuyCost.toFixed(2));
            $('#rate').val(rate.toFixed(2));
            $('#withoutvat').val(withoutVat.toFixed(2));
            $('#amount').val(amount.toFixed(2));
        });
    });
</script>

<script>
    $(document).ready(function() {
    // Listen for changes in the creditnote field
    $('#creditnote').on('input', function() {
        var creditNoteValue = $('#creditnote').val(); // Get the value of creditnote field
        var qtyValue = $('#quantity').val(); // Get the value of qty field

        // Check if creditnote is filled but qty is empty
        if (creditNoteValue !== "" && qtyValue === "") {
            alert("Please fill in the quantity first."); // Alert message
            $('#creditnote').val(''); // Clear the creditnote field (optional)
        }
    });
});

</script>
