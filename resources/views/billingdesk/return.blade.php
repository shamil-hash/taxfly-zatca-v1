<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Return</title>
    @include('layouts/usersidebar')
  <style>
/* Base Styles */
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background-color:transparent;
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
        @include('navbar.billingdesknavbar')
    @else
        <x-logout_nav_user />
    @endif
        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif
      
              <div class="header-container">
            <x-admindetails_user :shopdatas="$shopdatas" />
            <div class="dropdown-quick-container">
                <div class="dropdown">
            <button class="btn btn-info" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                ☰
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a href="/returnhistory" class="dropdown-item ">Sales Return History</a>

                <a class="dropdown-item" href="">Refresh</a>
            </div>
        </div>
            </div>
              </div>
              <br>

        <form method="post" action="returnproduct" id="myform" onsubmit="return validateForm()" style="margin-top:-40px;">
            @csrf
            <h2 id="header">Sales Return</h2>

            <div class="form group row">
                <select id="transaction_id" name="transaction_id" class="form-control"
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
                        <select onclick="doSomething(this.value)" id="product" class="form-control"
                            style="width: 250px">
                            <option selected value="">Select Product</option>
                        </select>
                        {{-- <p id="soldQuantityMessage" style="color: green;display: none;margin-top:5px;margin-bottom:-10px;"></p> --}}

                    </div>
                    <div class="col-md-1">
                        <div class="input-group gap hide" id="credit">
                            @foreach ($users as $user)
                                <?php if ($user->role_id == '11') { ?>
                                <span class="input-group-addon" id="basic-addon1" style="width: 90px;">CREDIT
                                    USER</span>
                                <input style="width: 170px;" id="credituser" name="credituser" value=""
                                    class="form-control" readonly>

                                <input type="hidden" style="width: 170px;" id="credituser__id" name="credituser__id"
                                    value="" class="form-control" readonly>
                                <?php } ?>
                            @endforeach

                        </div>
                    </div>
                </div>
                <br>
                @foreach ($users as $user)
                <?php if ($user->role_id == '28') { ?>
                <div class="form-group">
                    <label class="pr-2">
                        <input type="radio" name="toggle_creditnote" id="show_creditnote" value="yes"> Credit Note
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
                            <th width="2%"></th>
                            <th width="5%">Total Quantity</th>
                            <th width="5%">Transction ID</th>
                            <th width="12%">Description</th>
                            <th width="5%">Quantity</th>
                            <th width="5%">Unit</th>
                            <th width="6%">Rate</th>
                            <th width="8%" id="inclusive_heading" style="display:none">Inclusive Rate
                            </th>
                            <th width="8%" id="ratediscount_heading" style="display:none">Exclusive Rate
                            </th>
                            <th width="5%" id="vat_perc">{{$tax}}(%)</th>
                            <th width="6%" id="vat_ammi">Total {{$tax}} Amount</th>
                            <th width="8%">Net Rate</th>
                            <th width="6%">Discount(%)</th>
                            <th width="8%">Total Amount</th>
                            <th width="8%">Total Amount<br />w/o Discount</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                                <tr style="background-color: #f8f9fa;">
                            <td></td>
                            <td><input type="text" id="total_quantity" name="total_quantity" class="form-control" readonly>                            </td>
                            <td><input type="text" id="trans_id" name="trans_id" class="form-control" readonly></td>
                            <td>
                                <input type="text" id="product_name_name" class="form-control" tabindex="1"
                                    readonly>
                            </td>
                            <td>
                                <input type="number" id="qty" step="1" class="form-control" min="0"
                                    max="" tabindex="2">
                            </td>
                            <td><input type="text" id="units" class="form-control" tabindex="3" readonly></td>
                            <td>
                                <input type="number" id="mrp" class="form-control" tabindex="4" readonly>
                                <input type="hidden" step="any" id="buycost" class="form-control">
                                <input type="hidden" step="any" id="buycost_rate" name="buycost_rate"
                                    class="form-control">
                            </td>
                            <td id="inclusive_rate_value" name="inclusive_rate_value" style="display: none;">
                            </td>
                            <td id="rate_discount_value" name="rate_discount_value" style="display: none;">
                            </td>
                            <td>
                                <input type="number" id="fixed_vat" class="form-control" tabindex="5" readonly>
                                <input type="hidden" id="vat_type" name="vat_type" readonly>
                            </td>
                            <td><input type="number" id="vat_amount" class="form-control" readonly></td>
                            <td>
                                <input type="number" id="net_rate" class="form-control" tabindex="6" readonly>
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

                            <td><a href="#" class="btn btn-info addRow" style="background-color:#187f6a;" title="Add Row">+</a></td>
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
        var pt = ($("#paymenttype").val());
        var wv = Number($("#buycost").val());
        var buycost_rate = Number($("#buycost_rate").val());
        var unit = ($("#units").val());
        var discount = Number($("#discount").val());
        var tq = ($("#total_quantity").val());

        discount = (discount != null) ? discount : 0;

        var price_dis = Number($("#price_wo_discount").val());
        var total_withoutvat_disc = Number($("#total_wo_discount").val());

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
        var tr = '<tr>' + '<td>' + number + '</td>' +
            '<td>' +
            ' <input type="text" value="' + tq +
            '" name="total_quantity[]" class="form-control">' +
            '</td>' +
            '<td>' +
            '<input type="text" id="trans" value="' + ti +
            '" name="trans[]" class="form-control" readonly> <input type="hidden" value="' + pt +
            '" name="ptype[]" class="form-control">' +
            '</td>' +
            '<td><input type="text" id="productname" value="' + y +
            '" name="productName[]" class="form-control" readonly>' +
            '<input type="hidden" value=' + credituse + ' name="creditusers[]" class="form-control" >' +
            '<input type="hidden" value=' + credituse_id + ' name="creditusers_id[]" class="form-control" ></td>' +
            '<td><input type="text" value=' + x + ' name="quantity[]" class="form-control" readonly></td>' +
            '<td><input type="text" value=' + unit + ' name="unit[]" class="form-control" readonly></td>' +
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
            '<td><a href="#" class="btn btn-danger remove">-</a></td>' +
            '<input type="hidden" value=' + u + ' name="product_id[]" class="form-control" >' +
            '</tr>';
        $('tbody').append(tr);

        @if($users->contains('role_id', '28')) // Blade condition
        if (!document.getElementById('show_creditnote').checked) {
            $('#show_creditnote').prop('disabled', true);
        }
        @endif


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
    }
    // $('tbody').on('click', '.remove', function() {
    //     $(this).parent().parent().remove();
    // });

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
        $(this).closest('tr').remove();
        $('#show_creditnote').prop('disabled', false);
        $('#product').prop('disabled', false);


        updateGrandTotal();
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
                    $('#total_quantity').val(Math.round(soldquantity));
                    $("#qty").attr("max", soldquantity);

                    $('.addRow').prop('disabled', false);

                    var sellcost = response.sales_details;
                    $('#mrp').val(sellcost);

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
                            var discounts = Number($("#discount").val());
                            var discount_type = Number($("#discount_type").val());

                            var inclusive_rate = mrp / (1 + (fixed_vat / 100));
                            var discount_amount = mrp * (discounts / 100);
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
                            var discounts = Number($("#discount").val());
                            var discount_type = Number($("#discount_type").val());

                            var discount_amount = mrp * (discounts / 100);
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

    function validateForm() {
        // Prevent the form from submitting multiple times
        const form = document.getElementById("myform");
        const submitBtn = document.getElementById("submitBtn");
        var currentBalanceStr = document.getElementById('current_balance').value;
        var billAmountStr = document.getElementById('grand_total').value;
        var payment_mode_val = $('input[name="payment_mode"]:checked').val();
        var bankRadio = document.querySelector('input[name="payment_mode"][value="2"]');
        var accountSelect = document.getElementById('bank-dropdown');

        var currentBalance = parseFloat(currentBalanceStr);
        var billAmount = parseFloat(billAmountStr);

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
            submitBtn.innerText = "return";

            return false; // Validation failed; prevent form submission
        }

        if ((payment_mode_val == "" || payment_mode_val == null) && document.getElementById("credit").classList.contains("hide")) {
            alert("Select payment mode");

            submitBtn.disabled = false;
            submitBtn.innerText = "submit";

            return false;
        }

        if (payment_mode_val == '2') {
            if (currentBalance < billAmount) {
                alert('Insufficient balance!');

                submitBtn.disabled = false;
                submitBtn.innerText = "return";

                return false;
            }
        }
        if (bankRadio.checked && accountSelect.value === "") {
            alert("Please select a Bank.");
            accountSelect.focus();

            // Re-enable the submit button after alert
            submitBtn.disabled = false;
            submitBtn.innerText = "Submit";

            return false; // Validation failed; prevent form submission
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
            form.action = 'creditnotesubmit';  // Set action for credit note
        } else {
            form.action = 'returnproduct';  // Set action for return product
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
        header.textContent = 'Credit Note';
    } else {
        header.textContent = 'Sales Return';
    }
});

</script>
