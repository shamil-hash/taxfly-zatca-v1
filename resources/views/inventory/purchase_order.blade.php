<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Order</title>
    @include('layouts/usersidebar')
    <script src="/javascript/purchaseorder.js"></script>
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
            border: 2px solid #e5e7e9;
            border-collapse: separate;
            border-left: solid black 1px;
            border-radius: 10px;
            border-spacing: 0px;
        }

        th,
        td {
             border: 2px solid #e5e7e9;
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
            background-color: #f8f9fa;
            color: black;
        }

        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 1px solid #010101;
             background-color: #f8f9fa;
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
.responsive {
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

    <div id="content">
        @if ($adminroles->contains('module_id', '30'))
        <div style="margin-left:-15px;margin-top:-18px;">

            @include('navbar.invnavbar')
        </div>
        @else
<x-logout_nav_user />
@endif
        <!--<div align="right">-->
        <!--    <a href="" class="btn btn-info">Refresh</a>-->
        <!--    <a href="/purchase_order_history/purchase_order" class="btn btn-info">Purchase Order History</a>-->
        <!--</div>-->
        <div class="dropdown">
            <button class="btn btn-info" style="background-color:#187f6a;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                ☰
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                <a href="/purchase_order_history/purchase_order" class="dropdown-item ">Purchase Order History</a>

                <a class="dropdown-item" href="">Refresh</a>
            </div>
        </div>
        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif

        <x-admindetails_user :shopdatas="$shopdatas" />

        <br />

        <form method="post" action="purchaseorder_submit" enctype="multipart/form-data" id="purchase_form"
            name="purchase_form" onsubmit="return validateForm();">
            @csrf
            <h2>Purchase Order</h2>
            <br />
            <div class="form-group row">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 12px; border: 1px solid #dee2e6; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <!-- First Row: 4 columns -->
        <div class="row" style="margin-bottom: 15px;">
            <!-- Bill Number -->
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon" style="font-size: 12px; width: 100px;">Bill Number <span style="color: red;">*</span></span>
                    <input type="text" id="reciept_nos" name="reciept_no" list="reciept_no"
                        class="form-control" placeholder="Bill No"
                        autocomplete="off" style="width: 100%; font-size: 14px;"
                        oninput="validateReceiptNo(this.value)" tabindex="1" autofocus>
                    <datalist id="reciept_no">
                        @foreach ($receipt_nos as $receiptnos)
                            <option value="{{ $receiptnos->reciept_no }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div id="reciept_error" style="color: red; font-size: 12px;"></div>
                <span style="color:red; font-size: 12px;">
                    @error('reciept_no') {{ $message }} @enderror
                </span>
            </div>

            <!-- Comment -->
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon" style="font-size: 12px; width: 100px;">Comment</span>
                    <input type="text" id="comment" name="comment"
                        class="form-control" placeholder="Comment"
                        style="width: 100%; font-size: 14px;" tabindex="2">
                </div>
                <span style="color:red; font-size: 12px;">
                    @error('comment') {{ $message }} @enderror
                </span>
            </div>

            <!-- Supplier Name -->
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon" style="font-size: 12px; width: 100px;">Supplier <span style="color: red;">*</span></span>
                    <input type="text" list="supplier" name="supplier" id="supplierdata"
                        class="form-control" placeholder="Supplier Name"
                        autocomplete="off" style="width: 100%; font-size: 14px;" tabindex="3">
                    <datalist id="supplier">
                        @foreach ($suppliers as $row)
                            <option data-value="{{ $row->id }}" value="{{ $row->name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <span style="color:red; font-size: 12px;">
                    @error('supplier') {{ $message }} @enderror
                </span>
                <input type="hidden" id="supp_id" name="supp_id">
            </div>

            <!-- Delivery Date -->
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon" style="font-size: 12px; width: 100px;">Delivery Date</span>
                    <input type="date" id="delivery_date" name="delivery_date"
                        class="form-control" style="width: 100%; font-size: 14px;" tabindex="4">
                </div>
                <span style="color:red; font-size: 12px;">
                    @error('delivery_date') {{ $message }} @enderror
                </span>
            </div>
        </div>

        <!-- Second Row: Payment Mode -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group" style="display: flex; align-items: center;">
                    <span class="form-group-addon" style="font-size: 12px; margin-right: 10px; min-width: 100px;">Payment Mode <span style="color: red;">*</span></span>

                    <div style="display: flex; align-items: center; gap: 15px;">
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="radio" class="mode cash" name="payment_mode" value="1" tabindex="5">
                            <span>Cash</span>
                        </label>

                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="radio" class="mode credit" name="payment_mode" value="2" tabindex="6">
                            <span>Credit</span>
                        </label>

                        @foreach ($users as $user)
                            <?php if ($user->role_id == '24') { ?>
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="radio" class="mode bank" name="payment_mode" value="3" tabindex="7" onclick="toggleDropdown()">
                                <span>Bank</span>
                            </label>
                            <?php } ?>
                        @endforeach

                        <!-- Bank dropdown (initially hidden) -->
                        <select id="bank-dropdown" class="form-control" name="bank_name"
                            style="display: none; width: 200px; margin-left: 10px; font-size: 14px;">
                            <option value="">SELECT BANK</option>
                            @foreach ($listbank as $bank)
                                @if ($bank->status == 1)
                                <option value="{{ $bank->id }}" data-current-balance="{{ $bank->current_balance }}">
                                    {{ $bank->bank_name }} ({{ $bank->account_name }})
                                </option>
                                @endif
                            @endforeach
                        </select>

                        <input type="hidden" name="current_balance" id="current_balance" value="">
                        <input type="hidden" name="account_name" id="account_name" value="">
                        <input type="hidden" name="bank_id" id="bank_id" value="">
                    </div>
                </div>
                <span style="color:red; font-size: 12px;">
                    @error('payment_mode') {{ $message }} @enderror
                </span>
            </div>
        </div>
    </div>
</div>
                <br />

                <div id="prebuiltbilldiv" style="display:block;">

                    <table id="mytable">
                        <thead>
                            <tr>
                            <tr>
                                <th width="2%">SI. No.</th>
                                <th width="9%">Product</th>
                                <th width="9%">Buy Cost</th>
                                <th width="9%">{{$tax}}(%)</th>
                                <th width="9%">Rate</th>
                                <th width="9%">Selling Cost</th>

                                <th width="10%">Mode</th>
                                <th width="9%" id="box_dozen_header" style="display: none;">Box / Dozen</th>
                                <th width="9%" id="items_header" style="display: none;">Items</th>
                                <th width="9%" id="quantity_header" style="display: none;" colspan="2">
                                    Quantity
                                </th>

                                <th width="5%">Unit</th>
                                <th width="10%">Total <br />( Without {{$tax}}) </th>
                                <th width="10%">Total <br />( With {{$tax}}) </th>
                                <th width="5%"></th>
                            </tr>

                        </thead>
                        <tbody>
                                <tr style="background-color: #f8f9fa;">
                                <td></td>
                                <td id="one">
                                    <div>
                                        <input
                                            type="text"
                                            id="product"
                                            name="product"
                                            list="productOptions"
                                            class="form-control product-list product"
                                            style="width: 300px;"
                                            onclick="productlist(this.value)"
                                            onkeydown="moveToRadioGroup(event)"
                                            placeholder="Type or select product"
                                            autocomplete="off"
                                            tabindex="7"
                                        >
                                        <datalist id="productOptions">
                                            @foreach ($products as $product)
                                                <option value="{{ $product->product_name }}" data-id="{{ $product->id }}">
                                                    {{ $product->product_name }}
                                                </option>
                                            @endforeach
                                        </datalist>
                                        <input type="hidden" id="product_id" name="product_id">
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="buycost" id="buycost" class="form-control"
                                        tabindex="8">
                                </td>
                                <td>
                                    <input type="text" name="vat" id="vat" class="form-control"
                                        tabindex="9">
                                </td>
                                <td>

                                    <input type="text" name="rate" id="rate" class="form-control"
                                        tabindex="10" readonly>
                                </td>
                                <td>
                                    <input type="text" name="sellingcost" id="sellingcost" class="form-control"
                                        tabindex="11">
                                </td>

                                <td>
                                    <div class="input-group">
                                        <select name="mode" class="form-control"
                                            onchange="addExtraColumns(this.value)" style="width: 150px;"
                                            tabindex="12">
                                            <option value="">Select Mode </option>
                                            <option value="3">Quantity</option>
                                            <option value="1">Box</option>
                                            <option value="2">Dozen</option>

                                        </select>
                                    </div>
                                </td>

                                <td id="boxDozenNo" style="display: none;"></td>
                                <td id="itemColumn" style="display: none;"></td>
                                <td colspan="2" id="QuantityColumn" style="display: none;"></td>

                                <td>
                                    <input type="text" name="unit" id="unit" class="form-control"
                                        readonly>
                                </td>
                                <td>
                                    <input type="text" name="without_vat" id="without_vat" class="form-control"
                                        readonly>
                                </td>
                                <td>
                                    <input type="text" name="total" id="total" class="form-control"
                                        readonly>
                                </td>

                                <td><a href="#" class="btn btn-info addRow" title="Add Row" style="background-color:#187f6a;">+</a></td>
                                <input type="hidden" id="product_name" class="form-control">
                                <input type="hidden" id="product_id" class="form-control">
                            </tr>

                            <tr>
                                <td colspan="12"> <i class="glyphicon glyphicon-tags"></i> &nbsp BILL</td>
                            <tr>
                        </tbody>
                    </table>

                </div> <br />

                <div class="row">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="hidden" name="price_without_vat" class="form-control price"
                                id="price_without_vat" placeholder="Bill Amount" aria-describedby="basic-addon2"
                                readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">Bill Amount</span>
                            <input type="text" name="price" class="form-control price" id="price"
                                placeholder="Bill Amount" aria-describedby="basic-addon2" readonly>
                        </div>
                        <span style="color:red">
                            @error('price')
                                {{ $message }}
                            @enderror
                        </span>
                        <br />
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">File</span>
                            <input type="file" class="form-control camera" name="camera" id="camera"
                                aria-describedby="basic-addon2">
                        </div>
                        <span style="color:red">
                            @error('camera')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <input class="btn btn-primary" type="submit" value="submit" id="submitBtn">
                    </div>
                </div>
            </div>
        </form>

        <script src="{{ asset('javascript/purchase.js') }}"></script>
        <script src="{{ asset('javascript/purchaseorder.js') }}"></script>
    </div>

</body>

</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function toggleDropdown() {
            var bankRadio = document.querySelector('input[name="payment_mode"][value="3"]');
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
    function productlist(x) {

        var array = @json($products);

        function isSeries(elm) {
            return elm.id == x;
        }

        var foundProduct = array.find(isSeries); // new

        if (foundProduct) { // new

            var proname = array.find(isSeries).product_name;
            $('#product_name').val(proname);

            var uni = array.find(isSeries).unit;
            $('#unit').val(uni);

            var buy = array.find(isSeries).buy_cost;
            $('#buycost').val(buy);

            var y = array.find(isSeries).id;
            $('#product_id').val(y);

            var sell = array.find(isSeries).selling_cost;
            $('#sellingcost').val(sell);


            var ratee = array.find(isSeries).rate;
            $('#rate').val(ratee);

            var vatttt = array.find(isSeries).purchase_vat;
            $('#vat').val(vatttt);


            var nu = "";

            $('#boxselect').val(nu); // Clear input field
            $('#boxselectenter').val(nu); // Clear input field
            $('#dozenselect').val(nu); // Clear input field
            $('#dozenselectenter').val(nu); // Clear input field
            $("#total").val(nu);
            $("#without_vat").val(nu);

        } else { //new

            console.error('Product not found for ID: ' + x);

        }
    }


    function validateForm() {
        // Prevent the form from submitting multiple times
        const form = document.getElementById("purchase_form");
        const submitBtn = document.getElementById("submitBtn");
        var bankRadio = document.querySelector('input[name="payment_mode"][value="3"]');

        if (submitBtn.disabled) {
            return false; // Prevent form submission
        }

        // Disable the submit button
        submitBtn.disabled = true;
        submitBtn.innerText = "Submitting...";

        // Validate Account Selection
        var accountSelect = document.getElementById('bank-dropdown');
        if (bankRadio.checked && accountSelect.value === "") {
            alert("Please select a Bank.");
            accountSelect.focus();

            // Re-enable the submit button after alert
            submitBtn.disabled = false;
            submitBtn.innerText = "Submit";

            return false; // Validation failed; prevent form submission
        }

        // Validate Title
        var product = $("#productnamevalue").val();
    
        if (product == "" || product == null) {
            alert("Press the add button");

            // Re-enable the submit button after alert
            submitBtn.disabled = false;
            submitBtn.innerText = "submit";

            return false;
        }



        // Your other validation conditions here
        var su = $('input[name="supplier"]').val();
        var rpt = $('input[name="reciept_no"]').val();
        var payment_mode_val = $('input[name="payment_mode"]:checked').val();

        if (payment_mode_val == "" || payment_mode_val == null) {
            alert("Select payment mode");

            // Re-enable the submit button after alert
            submitBtn.disabled = false;
            submitBtn.innerText = "submit";

            return false;
        }

        if (su == "" || su == null) {
            alert("Select supplier");

            // Re-enable the submit button after alert
            submitBtn.disabled = false;
            submitBtn.innerText = "submit";

            return false;
        }

        if (rpt == "" || rpt == null) {
            alert("Give your receipt number");

            // Re-enable the submit button after alert
            submitBtn.disabled = false;
            submitBtn.innerText = "submit";

            return false;
        } else {
            // Check for forbidden characters in receipt number
            const forbiddenCharacters = ["/", "\\", "?", "#"];

            if (forbiddenCharacters.some((char) => rpt.includes(char))) {
                alert(
                    "Invoice No should not contain '/', '\\', '?' or '#' characters"
                );

                // Re-enable the submit button after alert
                submitBtn.disabled = false;
                submitBtn.innerText = "Submit";

                return false;
            } else {
                var url = "{{ route('checkorderreceipt') }}";
                var data = {
                    reciept_no: rpt,
                };

                $.getJSON(url, data, function(response) {
                    if (response.exists) {
                        alert("The receipt has already been taken");

                        // Re-enable the submit button after alert
                        submitBtn.disabled = false;
                        submitBtn.innerText = "submit";
                    } else {
                        // Submit the form if the receipt number is unique
                        form.submit();
                    }
                });

                // Prevent the default form submission for now
                return false;
            }
        }
    }


    function validateReceiptNo(receiptNo) {
        // Clear previous error messages
        $("#reciept_error").text("");

        // Check for forbidden characters in receipt number
        const forbiddenCharacters = ["/", "\\", "?", "#"];
        if (forbiddenCharacters.some((char) => receiptNo.includes(char))) {
            $("#reciept_error").text(
                "Invoice No should not contain '/', '\\', '?' or '#' characters"
            );
            return;
        }

        // Make an AJAX call to check if the receipt number exists
        $.ajax({
            url: "{{ route('checkorderreceipt') }}",
            method: "GET",
            data: {
                reciept_no: receiptNo,
            },
            success: function(response) {
                if (response.exists) {
                    $("#reciept_error").text("The receipt has already been taken");
                }
            },
        });
    }
</script>
<script>
    document.getElementById('submitBtn').addEventListener('click', function(event) {
        // Retrieve the values of current balance and bill amount as strings
        var currentBalanceStr = document.getElementById('current_balance').value;
        var billAmountStr = document.getElementById('price').value;

        // Parse the strings to floats
        var currentBalance = parseFloat(currentBalanceStr);
        var billAmount = parseFloat(billAmountStr);

        // Debug: Log the retrieved and parsed values to the console
        console.log('Current Balance (string):', currentBalanceStr);
        console.log('Bill Amount (string):', billAmountStr);
        console.log('Current Balance (parsed):', currentBalance);
        console.log('Bill Amount (parsed):', billAmount);

        // Check if the payment mode is "Bank" (value 3)
        var paymentMode = document.querySelector('input[name="payment_mode"]:checked').value;

        if (paymentMode == '3') {
            // Check if the current balance is less than the bill amount
            if (currentBalance < billAmount) {
                // Prevent form submission
                event.preventDefault();
                // Show an alert
                alert('Insufficient balance!');
            }
        }
    });
    </script>
    <script>
        document.getElementById('bank-dropdown').addEventListener('change', function() {
            // Get the selected option
            const selectedOption = this.options[this.selectedIndex];

            // Get the current balance from the data attribute
            const currentBalance = selectedOption.getAttribute('data-current-balance');

            // Set the current balance input value
            document.getElementById('current_balance').value = currentBalance;
        });
</script>
<script>
    document.getElementById('product').addEventListener('input', function() {
    // Find the selected option
    const input = this;
    const datalist = document.getElementById('productOptions');
    const options = datalist.options;
    const hiddenInput = document.getElementById('product_id');

    // Reset hidden value
    hiddenInput.value = '';

    // Find matching option
    for (let i = 0; i < options.length; i++) {
        if (options[i].value === input.value) {
            // Set the hidden input with the product ID
            hiddenInput.value = options[i].getAttribute('data-id');
            // Trigger your existing function
            productlist(options[i].getAttribute('data-id'));
            break;
        }
    }
});

// Maintain your existing onkeydown functionality
document.getElementById('product').addEventListener('keydown', function(event) {
    moveToRadioGroup(event);
});
</script>
