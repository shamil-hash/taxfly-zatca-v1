<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Purchase Return</title>
    @include('layouts/usersidebar')
    <style>
/* Base Styles */
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background-color: transparent;
    color: #2c3e50;
    margin: 0;
    padding: 0;
}

#content {
    padding: 40px;
}
    .hidden {
    display: none;
}
/* Header Container */
.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}
.quick-layout {
    position: relative;
    z-index: 1; /* Keep it below the dropdown */
}
.header-container {
    display: flex;
    justify-content: space-between; /* Push elements to left & right */
    align-items: center;
    flex-wrap: wrap; /* Ensures responsiveness */
    padding: 10px;
}


.dropdown-quick-container {
    display: flex;
    flex-direction: column; /* Stack dropdown and quick-layout vertically */
    align-items: flex-end; /* Align them to the right */
    gap: 10px; /* Space between dropdown and quick layout */
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
    background: white;
    border: 1px solid #ddd;
    padding: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-left: -100px;
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
.dropdown-container {
    display: flex;
    flex-direction: column; /* Stack elements vertically */
    align-items: flex-end; /* Align to the left */
    gap: 10px; /* Add some spacing between the dropdown and quick layout */
}
/* Form Styles */
#billForm {
    background-color: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.input-group-addon {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 500;
    border: 1px solid #e0e6ed;
}

.form-control {
    border: 1px solid #e0e6ed;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 14px;
    height: auto;
}

.form-control:focus {
    border-color: #187f6a;
    box-shadow: 0 0 0 0.2rem rgba(24, 127, 106, 0.25);
}

/* Table Styles */
table {
            /* border: solid #e5e7e9 1px; */
            border-collapse: separate;
            border-radius: 10px;
            border-spacing: 0px;
            width: 100%;
            border: 2px solid #e5e7e9;
        }

.table thead th {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
    padding: 12px 8px;
    border-bottom: 2px solid #e0e6ed;
}

.table tbody td {
    padding: 12px 8px;
    vertical-align: middle;
    border-bottom: 1px solid #e0e6ed;
}

/* Button Styles */
.btn {
    border-radius: 6px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-info {
    background-color: #187f6a;
    border-color: #187f6a;
    color: white;
}

.btn-info:hover {
    background-color: #15a085;
    border-color: #15a085;
}

.btn-primary {
    background-color: #187f6a;
    border-color: #187f6a;
}

.btn-primary:hover {
    background-color: #15a085;
    border-color: #15a085;
}

.btn-warning {
    background-color: #f39c12;
    border-color: #f39c12;
    color: white;
}

.btn-warning:hover {
    background-color: #e67e22;
    border-color: #e67e22;
}

.addRow {
    padding: 6px 12px;
    border-radius: 4px;
}

/* VAT Buttons */
.vat-button {
    background-color: white;
    border: 1px solid #e0e6ed;
    color: #495057;
    padding: 8px 16px;
    margin-right: 10px;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.vat-button:hover {
    background-color: #f8f9fa;
}

.vat-button.active {
    background-color: #187f6a;
    color: white;
    border-color: #187f6a;
}

/* Discount Row */
.discount_row {
    display: flex;
    align-items: center;
    gap: 15px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
}

.custom-select-no-padding {
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #e0e6ed;
}

/* Totals Section */
.input-group {
    margin-bottom: 10px;
}

.form-control-plaintext {
    font-weight: 600;
    color: #2c3e50;
    text-align: right;
}
.table-responsive {
            width: 100%;
            overflow-x: auto;
        }


/* Responsive Adjustments */
@media (max-width: 768px) {
    .header-container {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .dropdown-quick-container {
        width: 100%;
        justify-content: space-between;
    }

    .table thead {
        display: none;
    }

    .table tbody tr {
        display: block;
        margin-bottom: 20px;
        border: 1px solid #e0e6ed;
        border-radius: 6px;
    }

    .table tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        border-bottom: 1px solid #e0e6ed;
    }

    .table tbody td:before {
        content: attr(data-label);
        font-weight: 600;
        margin-right: 15px;
    }

    .discount_row {
        flex-direction: column;
        align-items: flex-start;
    }
}

/* Select2 Customization */
.select2-container--default .select2-selection--single {
    border: 1px solid #e0e6ed;
    border-radius: 6px;
    height: auto;
    padding: 8px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100%;
}

/* Alert Styles */
.alert {
    padding: 12px 20px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

/* Input Group Addon */
.input-group-addon {
    min-width: 120px;
    text-align: left;
    font-size: 12px;
    padding: 8px 12px;
}



/* Hidden Elements */
.hide, .hidden {
    display: none;
}

/* Payment Type Section */
#payment_type {
    width: 120px !important;
}

#bank_name {
    width: 200px !important;
}

/* Advance Payment Section */
#advanceInput {
    width: 120px !important;
}

/* Submit Buttons */
#submitBtn, #saveDraftBtn {
    padding: 10px 24px;
    font-size: 15px;
    margin-left: 10px;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
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
  <!--<div align="right">-->
  <!--          @include('layouts.quick')-->
  <!--          <a href="/purchasereturnhistory" class="btn btn-info ">Purchase Return History</a>-->
  <!--          <a href="" class="btn btn-info">Refresh</a>-->
  <!--      </div>-->
   <div class="header-container">
            <x-admindetails_user :shopdatas="$shopdatas" />
            <div class="dropdown-quick-container">
                <div class="dropdown">
            <button class="btn btn-info" style="background-color:#187f6a;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                ☰
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                <a href="/purchasereturnhistory" class="dropdown-item ">Purchase Return History</a>

                <a class="dropdown-item" href="">Refresh</a>
            </div>
        </div>
        </div></div>

        @if (session('success'))
        <div class="alert alert-success" style="text-align: center;">
            {{ session('success') }}
        </div>
        @endif
        <br>
        <form method="post" action="submitpurchasereturn" id="myform" onsubmit="return validateForm()" style="margin-top:-40px;">
            @csrf
            <h2 id="header">Purchase Return</h2>

            <div class="row">
                <div class="form group col-md-1">
                    <select id="reciept_no" class="form-control" style="width: 250px;">
                        <option selected value="">Select Bill No.</option>
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
            <br>
            <div class="row">
                <div class="col-md-4">
                    <select id="product" class="form-control" style="width: 250px">
                        <option selected value="">Select Product</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <div class="input-group gap hide" id="credit">
                        <span class="input-group-addon" id="basic-addon1" style="width: 90px;">CREDIT SUPPLIER</span>
                        <input style="width: 170px;" id="creditsupplier" name="creditsupplier" value=""
                            class="form-control" readonly>
                    </div>
                </div>
            </div>
            <br>
            @foreach ($users as $user)
            <?php if ($user->role_id == '29') { ?>
            <div class="form-group">
                <label class="pr-2">
                    <input type="radio" name="toggle_creditnote" id="show_creditnote" value="yes"> Debit Note
                </label>
            </div>
            <?php } ?>
            @endforeach
             <div class="form-group">
                <span class="form-group-addon" id="payment_mode" for="payment_mode">Payment Mode <span style="color: red;">*</span></span>
                <label class="pr-2">
                    <input type="radio" class="mode cash" name="payment_mode" value="1" tabindex="4"> Cash
                </label>&nbsp;&nbsp;&nbsp;
            @foreach ($users as $user)

            <?php if ($user->role_id == '24') { ?>
                    <label>
                <label>
                    <input type="radio" class="mode bank" name="payment_mode" value="2" tabindex="5" onclick="toggleDropdown()"> Bank
                </label>
                <?php } ?>
                @endforeach

                <!-- Bank dropdown -->
                <select id="bank-dropdown" class="form-control" name="bank_name" style="display: none; margin-left: 10px; width: 135px;">
                    <option value="">SELECT BANK</option>
                    @foreach ($listbank as $bank)
                    @if ($bank->status == 1)
                    <option value="{{ $bank->id}}" data-current-balance="{{ $bank->current_balance }}">{{ $bank->bank_name }} ({{ $bank->account_name }})</option>
                   @endif
                    @endforeach
                </select>
                <input type="hidden" name="current_balance" id="current_balance" value="" readonly style="margin-left: 10px; width: 135px;">
                <input type="hidden" name="account_name" id="account_name" value="">
                <input type="hidden" name="bank_id" id="bank_id" value="">
            </div>


            <table class="table">
                <thead>
                    <tr>
                        <th width="10%">Bill No</th>
                        <th width="6%">Total Quantity</th>
                        {{-- <th width="5%">Comment</th> --}}
                        <th width="8%">Product</th>
                        <th width="6%">Quantity</th>
                        <th width="5%">Unit</th>
                        <th width="8%">Buycost</th>
                        <th width="5%">{{$tax}}(%)</th>
                        <th width="8%">Rate</th>
                        <th width="8%">Discount</th>
                        <!--{{-- <th width="9%">VAT (%)</th>-->
                        <!--<th width="9%">VAT amount</th> --}}-->
                        <th width="8%">AMOUNT <br />(without {{$tax}})</th>
                        <th width="8%">AMOUNT <br />(with {{$tax}})</th>
                        <th width="10%">Supplier Name</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody>
                                <tr style="background-color: #f8f9fa;">
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
                        {{-- <td><input type="text" id="comment" class="form-control comment"></td> --}}
                        <td><input type="text" id="product_name" class="form-control product" readonly></td>
                        <td><input type="text" id="quantity" step="0.001" class="form-control quantity"
                                min="0" max="">
                        </td>
                        <td> <input type="text" id="unit" name="unit" class="form-control" readonly></td>
                        <td> <input type="text" id="buycost" name="buycost" class="form-control" readonly></td>
                        <td><input type="text" name="vat" id="vat" class="form-control" readonly></td>
                        <td><input type="text" name="rate" id="rate" class="form-control" readonly></td>
                        <!-- -->

                        <!--{{-- <td> <input type="text" id="vat_percen" name="vat_percen" class="form-control" readonly></td>-->
                        <!--<td> <input type="text" id="vat_amount" name="vat_amount" class="form-control" readonly>-->
                        <!--</td> --}}-->

                        <!-- -->
                        <td><input type="text" name="discount" id="discount" class="form-control" readonly>
                            <input type="hidden" name="discount_percent" id="discount_percent" class="form-control" readonly></td>
                        <td><input type="number" id="withoutvat" name="withoutvat" class="form-control amount"
                                readonly></td>
                        <td><input type="number" id="amount" class="form-control amount" readonly></td>
                        <td><input type="text" id="shopname" class="form-control shopname" readonly>
                            <input type="hidden" id="suppid" name="suppid" readonly>
                        </td>
                        <td><a href="#" class="btn btn-info addRow" style="background-color:#187f6a;">+</a></td>
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
                url: 'getremainstock_purchase/' + receipt_No + '/' + pro_id,
                success: function(response) {

                    response.purchase_Data.forEach(element => {

                        var pp_id = element['product'];
                        $('#p_id').val(pp_id);

                        var p_name = element['product_name'];
                        $('#product_name').val(p_name);

                        var discount = element['discount'];
                        $('#discount').val(discount);

                        var discount_percent = element['discount_percent'];
                        $('#discount_percent').val(discount_percent);

                        var k = element['supplier'];
                        $('#shopname').val(k);

                        var kid = element['supplier_id'];
                        $('#suppid').val(kid);

                        var paymode = element['payment_mode'];
                        $('#paymenttype').val(paymode);

                        var buycost = element['buycost'];
                        $('#buycost').val(buycost);

                        var unit = element['unit'];
                        $('#unit').val(unit);

                        //vat

                        // var vat_percen = element['vat_percentage'];
                        // $('#vat_percen').val(vat_percen);

                        var rate = element['rate'];
                        $('#rate').val(rate);

                        var vat = element['vat'];
                        $('#vat').val(vat);

                        //

                        var p_main_id = element['id'];
                        $('#purchase_main_id').val(p_main_id);

                        var p_unique_trans_ID = element['purchase_trans_id'];
                        $('#purchase_unique_trans_id').val(p_unique_trans_ID);

                        /*---------------------------------------------------------------*/

                        var quanty = element['quantity'];
                        // if (quanty !== undefined && quanty !== null) {
                        //     $('#total_quantity').val(Math.round(quanty)); // This will round to the nearest integer
                        // } else {
                        //     console.log('Quantity is not available');
                        // }
                        var purchaseamt = element['price'];

                        var price_1 = (purchaseamt / quanty);

                        $('#quantity').on('change', function() {

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
                        $('#total_quantity').val(Math.round(purchase_remain)); // This will round to the nearest integer

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
                    $('#quantity').focus();
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
        var tq = ($("#total_quantity").val());
        var discount = ($("#discount").val());
        var discount_percent = ($("#discount_percent").val());

        if (!addedProductsByReceipt[y]) {
            addedProductsByReceipt[y] = [];
        }

        // Check if the product is already added for this receipt number
        if (addedProductsByReceipt[y].includes(pid)) {
            // Product already added for this receipt number, show an alert message
            alert("Product is already added for this receipt number.");
            return;
        }


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
            // '<td><input type="text" value="' + z + '" name="comment[' + pid_main +
            // ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + p + '" name="product[' + pid_main +
            ']" id="productname" class="form-control" readonly><input type="hidden" value="' + pid +
            '" name="p_id[' + pid_main + ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + q + '"  name="quantity[' + pid_main +
            ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + unit + '" name="units[' + pid_main +
            ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + Buycost + '" name="buycosts[' + pid_main +
            ']" class="form-control" readonly></td>' +
            '<td><input type="text" value=' + vat_value + ' id="vat_r" name="vat_r[' + pid_main +
            ']" class="form-control" readonly> </td>' +
            '<td><input type="text" value=' + rate_value + ' id="rate_r" name="rate_r[' + pid_main +
            ']" class="form-control" readonly> </td>' +
            '<td><input type="text" value=' + discount + ' id="discount" name="discount[' + pid_main +
                ']" class="form-control" readonly>' +
                '<input type="hidden" value="' + discount_percent + '" id="discount_percent" name="discount_percent[' +
                pid_main + ']" class="form-control" readonly> </td>' +
            // '<td><input type="text" value="' + vatperc +
            // '" name="vatpercentages[' + pid_main + ']" class="form-control" readonly></td>' +
            // '<td><input type="text" value="' + vatamo + '" name="vatamounts[' + pid_main +
            // ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + withoutvat_amount +
            '" name="withoutvat[' + pid_main + ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + w + '" name="amount[' + pid_main +
            ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + t +
            '" name="shop_name[' + pid_main + ']" class="form-control" readonly><input type="hidden" value="' +
            suppli_id +
            '" name="supplid[' + pid_main + ']" class="form-control" readonly></td>' +
            '<td><a href="#" class="btn btn-danger remove">-</a></td>' +
            '</tr>';
        $('tbody').append(tr);
        addedProductsByReceipt[y].push(pid);
        if (!document.getElementById('show_creditnote').checked) {
            $('#show_creditnote').prop('disabled', true);
        }

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
        $("#total_quantity").val(''); // Clear the field after use
        $('#show_creditnote').prop('disabled', false);

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
        if ((payment_mode_val == "" || payment_mode_val == null) && document.getElementById("credit").classList.contains("hide")) {
            alert("Select payment mode");

            submitBtn.disabled = false;
            submitBtn.innerText = "submit";

            return false;
        }
        if (bankRadio.checked && accountSelect.value === "") {
            alert("Please select a Bank.");
            accountSelect.focus();

            // Re-enable the submit button after alert
            submitBtn.disabled = false;
            submitBtn.innerText = "Submit";

            return false; // Validation failed; prevent form submission
        }
        // Form is valid; continue with submission
        // You can also perform any additional logic here

        // No need to re-enable the submit button here since the form will submit

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
    document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('myform');
    const creditNoteRadio = document.getElementById('show_creditnote');
    const returnProductRadio = document.getElementById('show_returnproduct');

    // Function to set the form action based on the radio button selection
    function setFormAction() {
        if (creditNoteRadio.checked) {
            form.action = 'debitnotesubmit';  // Set action for credit note
        } else {
            form.action = 'submitpurchasereturn';  // Set action for return product
        }
    }

    // Initialize the form action based on the current radio button state
    setFormAction();

    // Add event listeners to both radio buttons to update the action when clicked
    creditNoteRadio.addEventListener('change', setFormAction);
    returnProductRadio.addEventListener('change', setFormAction);
});

</script>
<script>
    document.getElementById('show_creditnote').addEventListener('change', function () {
    const header = document.getElementById('header');
    if (this.checked) {
        header.textContent = 'Debit Note';
    } else {
        header.textContent = 'Purchase Return';
    }
});

</script>
