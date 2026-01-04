
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Stock</title>
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
/* Modal Styles */

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
        <div class="header-container">
            <x-admindetails_user :shopdatas="$shopdatas" />
            <div class="dropdown-quick-container">
                <div class="dropdown">
                    <button class="btn btn-info" style="background-color:#187f6a;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        ☰
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a href="/draft/purchasedraft" class="dropdown-item ">Drafts</a>
                <a style="display:none;" class="dropdown-item" data-toggle="modal" data-target="#addProductModal">Add New Product</a>
                    </div>
                </div>

                <div class="quick-layout">
                    @include('layouts.quick')
                </div>
                <button style="display:none;" id="switch" name="switch" type="button" class="btn btn-info">Switch to Service</button>
            </div>
        </div>
        @include('modal.product_modal.add_product_modal', [
              'categories' => $categories,
              'units' => $units,
            'page' => $page,
         ])

        @if (session('success'))
        <div class="alert alert-success" style="text-align: center;">
            {{ session('success') }}
        </div>
    @endif
        <form method="post" action="submitstock_table" enctype="multipart/form-data" id="purchase_form"
            name="purchase_form" onsubmit="return validateForm();">
            @csrf
            <input type="hidden" id="method" name="method" value="purchase">
            <h2 id="heading">Purchase Data</h2>
            <div class="form group row" >
                <div style="background-color: #f8f9fa; padding: 10px; border-radius: 12px; border: 1px solid #dee2e6; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);height:100px;">
                              <div class="row">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1" style="font-size: 10px; width: 120px;">Bill Number <span
                                    style="color: red;">*</span></span>
                            <!--<input type="text" id="reciept_no" name="reciept_no" style="width: 300px;"-->
                            <!--    class="form-control receiptno" placeholder="Bill No:" tabindex="1"-->
                            <!--    oninput="validateReceiptNo(this.value)" autofocus>-->

                            <input type="text" id="reciept_nos" name="reciept_no" list="reciept_no"
                                class="form-control receiptno" placeholder="Bill No:" aria-describedby="basic-addon2"
                                autocomplete="off" style="width: 100px; font-size: 12px;height:auto;" oninput="validateReceiptNo(this.value)"
                                tabindex="1" autofocus>
                            <datalist id="reciept_no">
                                @foreach ($receipt_nos as $receiptnos)
                                    <option value="{{ $receiptnos->reciept_no }}"></option>
                                @endforeach
                            </datalist>

                            <span id="reciept_error" style="color: red;"></span>
                        </div>

                        <div id="error-message" class="text-danger"></div>

                        <span style="color:red">
                            @error('reciept_no')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2" style="font-size: 10px; width: 120px;" >Supplier Name <span
                                    style="color: red;">*</span></span>
                                    <select name="supplier" id="supplierdata" class="form-control supplier" style="width: 140px; font-size: 12px; height: auto;" tabindex="3">
                                        <option value="" selected disabled>Select Supplier</option>
                                        @foreach ($suppliers as $row)
                                        <option value="{{ $row->name }}" data-id="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                        </div>
                        <span style="color:red">
                            @error('supplier')
                            {{ $message }}
                            @enderror
                        </span>
                        <input type="hidden" id="supp_id" name="supp_id">
                        <span style="color:red">
                            @error('supp_id')
                            {{ $message }}
                            @enderror
                        </span>
                    </div>

                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1" style="font-size: 10px; width: 120px;" >Comment</span>
                            <input type="text" id="comment" name="comment" style="width: 100px; font-size: 12px;height:auto;"
                                class="form-control comment" placeholder="Comment:" tabindex="2">
                        </div>
                        <span style="color:red">
                            @error('comment')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1" style="font-size: 10px; width: 120px;" >Invoice Date</span>
                            <input type="date" id="invoice_date" name="invoice_date" style="width: 140px; font-size: 12px;"
                                class="form-control invoice_date" tabindex="6">
                        </div>
                        <span style="color:red">
                            @error('invoice_date')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                </div>
                <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <span class="form-group-addon pr-2" id="payment_mode" for="payment_mode">Payment
                            Mode <span style="color: red;">*</span></span>
                        <label class="pr-2">
                            <input type="radio" class="mode cash" name="payment_mode" value="1"
                                tabindex="4">Cash
                        </label>&nbsp;&nbsp;&nbsp;
                        <label>
                            <input type="radio" class="mode credit" name="payment_mode" value="2"
                                tabindex="5">Credit
                            </label>&nbsp;&nbsp;&nbsp;&nbsp;
                      @foreach ($users as $user)

                            <?php if ($user->role_id == '24') { ?>
                                <label>
                                    <input type="radio" class="mode bank" name="payment_mode" value="3" tabindex="6" onclick="toggleDropdown()">Bank
                                </label>
                                <?php } ?>
                                @endforeach

                            <!-- Bank dropdown -->
                            <select id="bank-dropdown" class="form-control" name="bank_name" style="display: none; width: 100px; font-size: 12px;height:auto;">
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
                    <span style="color:red">
                        @error('payment_mode')
                            {{ $message }}
                        @enderror
                    </span>
                </div>
                </div>
            </div>
            </div>
            <br>
            <div id="prebuiltbilldiv" style="display:block;">

                <table id="mytable" class="responsive" style="border-radius: 8px;">
                    <thead>
                        <tr>
                            <th width="2%">SI. No.</th>
                            <th width="9%">Product</th>
                            <th width="9%">Buy Cost</th>
                            <th width="9%">{{$tax}}(%)</th>
                            <th width="9%">Rate</th>
                            <th width="9%">Selling Cost</th>
                            <th width="9%">Quantity Type</th>
                            <th width="9%">Quantity</th>
                            

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
                                <input type="hidden" id="box_count" name="box_count">
                                <input type="hidden" id="box_enabled" name="box_enabled">
                                <input type="hidden" id="quantity_enabled" name="quantity_enabled">

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
                <select name="quantity_type" id="quantity_type" class="form-control" tabindex="12">
                    <option value="quantity">Quantity</option>
                    <option value="box">Box</option>
                </select>
            </td>
            <td>
                <input type="number" name="quantity" id="quantity" class="form-control" tabindex="13" step="1">
                <input type="text" id="actual_quantity" name="actual_quantity" class="form-control" readonly>
            </td>
                         
                            <td>
                                <input type="text" name="unit" id="unit" class="form-control" readonly>
                            </td>
                            <td>
                                <input type="text" name="without_vat" id="without_vat" class="form-control"
                                    readonly>
                            </td>
                            <td>
                                <input type="text" name="total" id="total" class="form-control" readonly>
                            </td>

                            <td><a href="#" class="btn btn-info addRow" title="Add Row">+</a></td>
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
                <div class="col-sm-7">
                    <div class="discount_row">
                        <div class="checkbox-label">
                            <label  for="total_discount">Total Discount</label>&nbsp;&nbsp;
                            <select  name="total_discount" id="total_discount"
                                class="form-control custom-select-no-padding"
                                style="width: 80px;margin-top:-5px;">
                                <option value="0">No</option>
                                {{-- <option value="1">%</option> --}}
                                <option value="2">{{ $currency }}</option>
                            </select>
                        </div>
                        {{-- <div id="discount_field_percentage" class="hidden group_dis">
                            <div>
                                <label for="discount_percentage">Discount in %</label>
                                <input oninput="Barcodenotworkhere(this)" type="number" id="discount_percentage" name="discount_percentage"
                                    disabled>
                                <span style="margin-right: 3px;">%</span>
                            </div>
                        </div> --}}
                        <div  id="discount_field_amount" class="hidden group_dis">
                            <input oninput="Barcodenotworkhere(this)" type="number" id="discount_amount" name="discount_amount" disabled>
                            <span >{{ $currency }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4"></div>
                <div class="col-sm-3">
                    <div class="input-group">
                        <!--<span class="input-group-addon" id="basic-addon2">Bill Amount (without VAT)</span>-->
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
                    <div class="form-group">
                                <div class="checkbox-label">
                                    <label>
                                        <input type="radio" name="print_option" value="no_print" checked> Without Print
                                    </label>
                                    <label>
                                        <input type="radio" name="print_option" value="1"> Print Barcode
                                    </label>
                                </div>
                        </div>
                    <!--{{-- <button type="submit" class="btn btn-primary submitpurchase">Submit</button> --}}-->
                    <input class="btn btn-primary" type="submit" value="submit" id="submitBtn">
                    <button type="button" class="btn btn-warning" id="saveDraftBtn" onclick="saveToDraft()">Save to Draft</button>

                </div>
            </div>

        </form>
    </div>
    <button id="navigateBtn" style="display: none;">Navigate Away</button>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span style="color: red;font-weight:bold;font-size:40px;" class="close">&times;</span>
            <p>Are you sure you want to leave this page?</p>
            <button id="saveBtn" class="btn btn-success" >Save to draft</button>
            <button id="leaveBtn" class="btn btn-danger">Leave</button>
        </div>
    </div>
    <script src="{{ asset('javascript/purchase.js') }}"></script>
</body>

</html>
<script>
    function toggleDropdown() {
        var bankDropdown = document.getElementById("bank-dropdown");
        if (document.querySelector('input[name="payment_mode"]:checked').value == '3') {
            bankDropdown.style.display = "block";
        } else {
            bankDropdown.style.display = "none";
        }
    }
</script>
<script>
    var modal = document.getElementById("myModal");
    var leaveBtn = document.getElementById("leaveBtn");
    var closeModalBtn = document.getElementsByClassName("close")[0];
    var isModalConfirmed = false;
    var rowAdded = false;

    closeModalBtn.onclick = function() {
        modal.style.display = "none";
    }

    leaveBtn.onclick = function() {
        isModalConfirmed = true;
        modal.style.display = "none";
        window.removeEventListener('beforeunload', beforeUnloadHandler);
        // Optionally navigate or handle confirmation
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    var addRowBtn = document.querySelector(".addRow");
    addRowBtn.onclick = function() {
        rowAdded = true;
    }

    function beforeUnloadHandler(event) {
        if (rowAdded && !isModalConfirmed) {
            modal.style.display = "block"; // Show modal
            event.preventDefault();
            event.returnValue = ''; // Chrome requires returnValue to be set
            return ''; // Standard browser message for unsaved changes
        }
    }

    window.addEventListener('beforeunload', beforeUnloadHandler);

    function saveToDraft() {
        var form = document.getElementById("purchase_form");
        form.action = "{{ route('data.savepurchasedraft') }}";
        if (validateForm()) {
            isModalConfirmed = true; // Confirm modal action
            form.submit();
        }
    }

    // Submit Button Logic
    document.getElementById("submitBtn").addEventListener("click", function(event) {
        event.preventDefault(); // Prevent default submission
        var form = document.getElementById("purchase_form");
        form.action = "{{ route('data.submitstock_table') }}"; // Set action for submission

        if (validateForm()) {
            isModalConfirmed = true; // Confirm modal action
            form.submit(); // Submit form if valid
        }
    });
</script>
{{-- <script>
    function saveToDraft() {
        var form = document.getElementById("purchase_form");
        form.action = "{{ route('data.savepurchasedraft') }}";
        if (validateForm()) {
            form.submit();
        }
    }
    document.getElementById("submitBtn").addEventListener("click", function(event) {
        event.preventDefault(); // Prevent default form submission for better control

        var form = document.getElementById("purchase_form");
        form.action = "{{ route('data.submitstock_table') }}"; // Set the form action for normal submit

        if (validateForm()) {
            form.submit(); // Submit form if validation passes
        }
    });

</script> --}}

<script>
    $(document).ready(function() {
        // Trigger the calculation when the rate or vat input changes
        $('#buycost, #vat').on('input', function() {
            // Get the values of rate and vat
            var buyCost = parseFloat($('#buycost').val()) || 0;
            var vat = parseFloat($('#vat').val()) || 0;

            // Calculate the buy cost
            var rate = buyCost + (buyCost * vat / 100);

            $('#rate').val(rate);
        });
    });
</script>

<script type="text/javascript">
    function addExtraColumns(selectedMode) {
        var box_dozen_header = document.getElementById("box_dozen_header");
        var items_header = document.getElementById("items_header");

        var quantity_header = document.getElementById("quantity_header");

        var boxDozenNo = document.getElementById("boxDozenNo");
        var itemColumn = document.getElementById("itemColumn");

        var QuantityColumn = document.getElementById("QuantityColumn");


        if (selectedMode === "1") {

            box_dozen_header.style.display = "table-cell";
            items_header.style.display = "table-cell";
            quantity_header.style.display = "none";


            boxDozenNo.innerHTML =
                '<input type="number" id="boxselect" name="boxselect" min="0" onkeydown="moveToboxenterFields(event)" class="form-control boxselect" placeholder="No. of Box" tabindex="13">';
            itemColumn.innerHTML =
                '<input type="number" id="boxselectenter" name="boxselectenter" min="0" onkeydown="whichmovehappen(event)" class="form-control boxselectenter" placeholder="Items in Box" tabindex="14">';

            QuantityColumn.innerHTML = '';

            boxDozenNo.style.display = "table-cell";
            itemColumn.style.display = "table-cell";
            QuantityColumn.style.display = "none";

        } else if (selectedMode === "2") {

            box_dozen_header.style.display = "table-cell";
            items_header.style.display = "table-cell";

            quantity_header.style.display = "none";

            boxDozenNo.innerHTML =
                '<input type="number" id="dozenselect" name="dozenselect" min="0" onkeydown="moveTodozenenterFields(event)" oninput="getValue()" onChange = "getValue()" class="form-control dozenselect" placeholder="No. of Dozen" tabindex="13">';
            itemColumn.innerHTML =
                '<input type="number" id="dozenselectenter" name="dozenselectenter" min="0" oninput="getValue()" onkeydown="whichmovehappen(event)" class="form-control dozenselectenter" placeholder="Items" tabindex="14">';

            QuantityColumn.innerHTML = '';

            boxDozenNo.style.display = "table-cell";
            itemColumn.style.display = "table-cell";
            QuantityColumn.style.display = "none";

        } else if (selectedMode === "3") {

            box_dozen_header.style.display = "none";
            items_header.style.display = "none";

            boxDozenNo.innerHTML = '';
            itemColumn.innerHTML = '';

            boxDozenNo.style.display = "none";
            itemColumn.style.display = "none";

            quantity_header.style.display = "table-cell";

            // Display the input field for Quantity mode
            QuantityColumn.innerHTML =
                '<input type="number" id="boxselectenter" name="boxselectenter" min="0" onkeydown="whichmovehappen(event)" class="form-control boxselectenter" placeholder="Items in Box" tabindex="13">';

            QuantityColumn.style.display = "table-cell";


        } else {

            box_dozen_header.style.display = "none";
            items_header.style.display = "none";
            quantity_header.style.display = "none";

            boxDozenNo.innerHTML = '';
            itemColumn.innerHTML = '';
            QuantityColumn.innerHTML = '';

            boxDozenNo.style.display = "none";
            itemColumn.style.display = "none";
            QuantityColumn.style.display = "none";
        }
    }


    document.addEventListener('input', function(event) {
        // Check if the event is coming from the buycost input
        if (event.target && event.target.id === 'buycost') {
            getValue();
        }
    });

    function getValue() {

        var dozenselect = parseFloat(document.getElementById('dozenselect').value) || 0;
        // var buycos = $('#buycost').val();

        var dozenselectenter = dozenselect * 12;
        $('#dozenselectenter').val(dozenselectenter);

        var dozenselectenter = parseFloat(document.getElementById('dozenselectenter').value) || 0;

        // $("#dozenselect, #vat_amount, #vat, #buycost").keyup(function() {

        $("#dozenselect, #buycost, #vat, #rate").keyup(function() {

            var buycos = $('#buycost').val() || 0;

            // var buycos = parseFloat(buycos);

            var vaT_dozcase = $('#vat').val() || 0;

            var doz_vat = parseFloat(vaT_dozcase);

            var doz_ratE = $('#rate').val();

            /* second done */

            var total = dozenselectenter * doz_ratE;

            total = total.toFixed(2);

            total = parseFloat(total);

            document.getElementById('total').value = total;

            /* -------------- without VAT----------------*/

            var total_without_vat = dozenselectenter * buycos;

            total_without_vat = total_without_vat.toFixed(2);

            total_without_vat = parseFloat(total_without_vat);

            document.getElementById('without_vat').value = total_without_vat;

            /* -----------------------------------------*/

        });

    }

    function productlist(x) {

        var array = @json($products);

        function isSeries(elm) {
            return elm.id == x;
        }

        var foundProduct = array.find(isSeries); // new

        if (foundProduct) { // new

            var proname = array.find(isSeries).product_name;
            $('#product_name').val(proname);

            var box_count = array.find(isSeries).box_count;
            $('#box_count').val(box_count);
            var box_enabled = array.find(isSeries).box_enabled;
            $('#box_enabled').val(box_enabled);
            var quantity_enabled = array.find(isSeries).quantity_enabled;
            $('#quantity_enabled').val(quantity_enabled);

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

    $(document).ready(function() {
        $('.product-list').select2({
            theme: "classic"
        });
    });

    $(document).ready(function() {
        $('.quantity-list').select2({
            theme: "classic"
        });
    });
</script>

<script type="text/javascript">
    var addedProducts = [];

    function updateTotalAmount() {
        var total = 0;
        var totalwithoutvat = 0;

        $('input[name^="total"]').each(function() {
            // total += Number($(this).val());

            if (addedProducts.includes($(this).closest("tr").find('input[name^="product_id["]').val())) {
                total += Number($(this).val());
            }
        });

        total = total.toFixed(2);
        var discountAmount = 0;

        total = parseFloat(total);
        var discountValue = $('#discount_amount').val();
        if (discountValue > 0) {
            discountAmount = discountValue;
        }
        total -= discountAmount;
        total = total.toFixed(2);
        total = parseFloat(total);
        $('#price').val(total);

        /*-------------------*/

        $('input[name^="without_vat"]').each(function() {
            // totalwithoutvat += Number($(this).val());

            if (addedProducts.includes($(this).closest("tr").find('input[name^="product_id["]').val())) {
                totalwithoutvat += Number($(this).val());
            }
        });

        $('#price_without_vat').val(totalwithoutvat);
        /*------------------*/
    }


    $('.addRow').on('click', function() {
        addRow();
        $(' #discount_amount').on('input', function() {
            updateTotalAmount();
    });
        updateTotalAmount();
    });

    var serialNumber = 1;

    function addRow() {

        var selectedProductId = $("#product_id").val();
 if (!selectedProductId) {
            alert("This Product is not exist.");
            return;
        }
        // Check if the product is already in the addedProducts array
        if (addedProducts.includes(selectedProductId)) {
            // Product is already added, show an alert message
            alert("Product already added!");
            return;
        }
        var quantityType = $("#quantity_type").val();
    var quantity = $("#quantity").val();
    var boxCount = $("#box_count").val() || 1;
    var actualQuantity = $("#actual_quantity").val();

        var selectedMode = $("select[name='mode']").val();

        var boxSelect = $('#boxselect').val();
        var boxSelectEnter = $('#boxselectenter').val();
        var dozenSelect = $('#dozenselect').val();
        var dozenSelectEnter = $('#dozenselectenter').val();
        if (($("#product").val() == "") || !quantity || (quantityType === 'box' && !boxCount)) {
        alert("Please fill all required fields!");
        return;
    }

        var name = ($("#product_name").val());
        // console.log(name);

        var buycost = ($("#buycost").val());
        // console.log(buycost);

        var sellcost = ($("#sellingcost").val());

        var Is_box_dozen = $("select[name='mode']").val();
        // console.log("Mode:", Is_box_dozen);

        var unit = ($("#unit").val());
        // console.log(unit);

        var pid = Number($("#product_id").val());

        var tot = ($("#total").val());


        var tot_without_vat = ($("#without_vat").val());

        var rate = ($("#rate").val()) || 0;
        var vat = ($("#vat").val()) || 0;


        var tr = '<tr>' + '<td>' + serialNumber + '</td>' + '<td>' +
            '<input type="text" id="productnamevalue" value="' + name +
            '" name="productName[' + pid +
            ']" class="form-control" readonly> <input type="hidden"  value="' + pid + '" name="productId[' + pid +
            ']" class="form-control">' +
            '</td>' +
            '<td><input type="text" value=' + buycost + ' id="buy_cost" name="buy_cost[' + pid +
            ']" class="form-control" readonly> </td>' +
            '<td><input type="text" value=' + vat + ' id="vat_r" name="vat_r[' + pid +
            ']" class="form-control" readonly> </td>' +

            '<td><input type="text" value=' + rate + ' id="rate_r" name="rate_r[' + pid +
            ']" class="form-control" readonly> </td>' +
            // '<input type="hidden" value=' + vatop + ' name="vatdatas[' + pid + ']" class="form-control"></td>' +
            
            '<td><input type="text" value=' + sellcost + ' id="sell_cost" name="sell_cost[' + pid +
            ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + quantityType + '" name="quantity_type[' + pid +
            ']" class="form-control" readonly></td>' +
        '<td><input type="text" value="' + quantity + '" name="quantity[' + pid +
            ']" class="form-control" readonly></td>' +
        '<td style="display:none"><input type="text" value="' + actualQuantity + '" name="actual_quantity[' + pid +
            ']" class="form-control" readonly></td>' +
             '<td><input type="text" value="' + unit + '" name="unit[' + pid +
            ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + tot_without_vat + '" name="without_vat[' + pid +
            ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + tot + '" name="total[' + pid +
            ']" class="form-control" readonly></td>' +
            '<td><a href="#" class="btn btn-danger remove">-</a></td>' +
            '<input type="hidden" value=' + pid + ' name="product_id[' + pid + ']" class="form-control" >' +
            '</tr>';

        $('tbody').append(tr);

        addedProducts.push(selectedProductId);

        serialNumber++;

        var nu = "";

        $("#product_name").val(nu);
        $("#product_id").val(nu);
        $("#buycost").val(nu);
        $("#sellingcost").val(nu);
        $("#unit").val(nu);
        $("#total").val(nu);
        $("#product").val(nu).trigger('change');

        $('#boxselect').val(nu); // Clear input field
        $('#boxselectenter').val(nu); // Clear input field
        $('#dozenselect').val(nu); // Clear input field
        $('#dozenselectenter').val(nu); // Clear input field
        $("#actual_quantity").val(nu);
        $("#quantity").val(nu);
        $("#quantity_type").val("quantity"); // Reset to default

        $("#without_vat").val(nu);

        $("#rate").val(nu);
        $("#vat").val(nu);

        updateTotalAmount();
    }

    $('tbody').on('click', '.remove', function() {

        var removedProductId = $(this).parent().parent().find('input[name^="product_id["]').val();
        var index = addedProducts.indexOf(removedProductId);
        if (index !== -1) {
            addedProducts.splice(index, 1);
        }

        $(this).parent().parent().remove();
        updateTotalAmount();
    });
</script>


<script>
$('#supplierdata').on('change', function() {
    var name = $(this).val(); // Get selected supplier name
    var id = $(this).find(':selected').data('id'); // Get selected supplier ID from data attribute

    $('#supp_id').val(id); // Store supplier ID in hidden field
});

</script>

<script>
    $(document).ready(function() {

        $('select[name="mode"]').on('click change', function() {
            var selectedMode = $('select[name="mode"]').val();

            if (selectedMode == 1 || selectedMode == 3) {

                $("#boxselectenter, #buycost, #vat, #rate").keyup(function() {

                    if (selectedMode == 1) {
                        var box = $('#boxselect').val();
                    }


                    var boxEnter = $('#boxselectenter').val();
                    var buyco = $('#buycost').val() || 0;
                    var vaT = $('#vat').val() || 0;

                    var orivat = parseFloat(vaT);

                    var ratE = $('#rate').val();

                    /* second done */

                    var totalvalue = boxEnter * ratE;

                    totalvalue = totalvalue.toFixed(2);

                    totalvalue = parseFloat(totalvalue);

                    $('#total').val(totalvalue);

                    /* -------------- without VAT----------------*/

                    var total_without_vatbox = boxEnter * buyco;

                    total_without_vatbox = total_without_vatbox.toFixed(2);

                    total_without_vatbox = parseFloat(total_without_vatbox);

                    $('#without_vat').val(total_without_vatbox);

                    /* -----------------------------------------*/

                });

            }
        });

        $('select[name="mode"]').on('change', function() {

            $("#total").val("");
            $('#without_vat').val("");

        });

    });

    //when buycost changes

    $(document).ready(function() {
        $('select[name="mode"]').on('change', function() {
            $("#total").val("");
            $('#without_vat').val("");

        });

    });
</script>

<script type="text/javascript">
    $('form input:not([type="submit"])').keydown((e) => {
        if (e.keyCode === 13) {
            e.preventDefault();
            return false;
        }
        return true;
    });
</script>

<script type="text/javascript">
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
        // var commentd = $('input[name="comment"]').val();
        var su = $('select[name="supplier"]').val();
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

            if (forbiddenCharacters.some(char => rpt.includes(char))) {
                alert("Invoice No should not contain '/', '\\', '?' or '#' characters");

                // Re-enable the submit button after alert
                submitBtn.disabled = false;
                submitBtn.innerText = "Submit";

                return false;

            } else {
                var url = "{{ route('checkreceipt') }}";
                var data = {
                    reciept_no: rpt
                };

                $.getJSON(url, data, function(response) {
                    if (response.exists) {
                        alert('The receipt has already been taken');

                        submitBtn.disabled = false;
                        submitBtn.innerText = "submit";
                    } else {
                        var currentBalanceStr = document.getElementById('current_balance').value;
                        var billAmountStr = document.getElementById('price').value;

                        var currentBalance = parseFloat(currentBalanceStr);
                        var billAmount = parseFloat(billAmountStr);

                        if (payment_mode_val == '3') {
                            if (currentBalance < billAmount) {
                                alert('Insufficient balance!');

                                submitBtn.disabled = false;
                                submitBtn.innerText = "Submit";

                                return false;
                            }
                        }
                        isModalConfirmed = true; // Ensure the unload modal doesn't appear
                        form.submit();
                    }
                });

                return false;
            }
        }
    }
</script>

<script>
    function moveToboxenterFields(event) {
        if (event.keyCode === 13) { // Enter key
            event.preventDefault();
            $('#boxselectenter').focus();
        }
    }

    function moveTodozenenterFields(event) {
        if (event.keyCode === 13) { // Enter key
            event.preventDefault();
            $('#dozenselectenter').focus();
        }
    }

    function whichmovehappen(event) {

        if (event.keyCode === 13) { // Enter key
            event.preventDefault();
            addRow();
        }
    }
</script>

<script>
    function validateReceiptNo(receiptNo) {
        // Clear previous error messages
        $("#reciept_error").text("");

        // Check for forbidden characters in receipt number
        const forbiddenCharacters = ["/", "\\", "?", "#"];
        if (forbiddenCharacters.some(char => receiptNo.includes(char))) {
            $("#reciept_error").text("Invoice No should not contain '/', '\\', '?' or '#' characters");
            return;
        }

        // Make an AJAX call to check if the receipt number exists
        $.ajax({
            url: "{{ route('checkreceipt') }}",
            method: "GET",
            data: {
                reciept_no: receiptNo
            },
            success: function(response) {
                if (response.exists) {
                    $("#reciept_error").text("The receipt has already been taken");
                }
            }
        });
    }
</script>
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
    document.getElementById('bank-dropdown').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const currentBalance = selectedOption.getAttribute('data-current-balance');
        document.getElementById('current_balance').value = currentBalance;
    });
</script>
<script>
    function TotalBillDiscount() {
     // Trigger on discount type change
     $("#total_discount").change(function () {
         var total_discount = $(this).val();

         if (total_discount == 1) {
             $("#discount_field_percentage").removeClass("hidden");
             $("#discount_field_amount").addClass("hidden");

             $("#discount_percentage").prop("disabled", false);
             $("#discount_amount").prop("disabled", true).val("");
         } else if (total_discount == 2) {
             $("#discount_field_amount").removeClass("hidden");
             $("#discount_field_percentage").addClass("hidden");

             $("#discount_amount").prop("disabled", false);
             $("#discount_percentage").prop("disabled", true).val("");
         } else {
             $("#discount_field_percentage").addClass("hidden");
             $("#discount_field_amount").addClass("hidden");

             $("#discount_percentage, #discount_amount")
                 .val("")
                 .prop("disabled", true);
         }

         updateTotalAmount(); // Ensure this function is defined elsewhere
     });

     // Handle percentage discount input
     $("#discount_percentage").on("input", function () {
         if ($(this).val() !== "") {
             $("#discount_amount").val("");
         }
         updateTotalAmount(); // Ensure this function is defined elsewhere
     });

     // Handle amount discount input
     $("#discount_amount").on("input", function () {
         if ($(this).val() !== "") {
             $("#discount_percentage").val("");
         }
         updateTotalAmount(); // Ensure this function is defined elsewhere
     });
 }

 // Call the function after DOM is ready
 $(function () {
     TotalBillDiscount();
 });

 </script>

<script>
    function updateCalculationsForProduct(pid) {
        // Retrieve the mode for this row (expected values: "1", "2", or "3")
        var mode = $('input[name="boxdozen[' + pid + ']"]').val();
        var buycost = Number($('input[name="buy_cost[' + pid + ']"]').val()) || 0;
        var rate = Number($('input[name="rate_r[' + pid + ']"]').val()) || 0;
        var quantity = 0;

        if (mode === '2') {
            // For mode 2 (Dozen): compute quantity from dozenCount multiplied by 12
            var dozenCount = Number($('input[name="dozenCount[' + pid + ']"]').val()) || 0;
            var dozenItem = dozenCount * 12;  // Each dozen equals 12 items
            quantity = dozenItem;
            // Update the dozenItem field with the computed value
            $('input[name="dozenItem[' + pid + ']"]').val(dozenItem.toFixed(2));
        }
else {
    // For mode 1 (Box) and mode 3 (Quantity): use boxItem as the quantity
    quantity = Number($('input[name="boxItem[' + pid + ']"]').val()) || 0;
}

// Calculate amounts
var withoutVat = quantity * buycost;
var total = quantity * rate;

// Update the other fields
$('input[name="without_vat[' + pid + ']"]').val(withoutVat.toFixed(2));
$('input[name="total[' + pid + ']"]').val(total.toFixed(2));


        updateTotalAmount();
    }

    // Attach an event listener for any changes in quantity-related inputs:
    // - boxItem (for modes 1 and 3)
    // - dozenCount and dozenItem (for mode 2)
    $(document).on('input change', 'input[name^="boxItem["], input[name^="dozenCount["], input[name^="dozenItem["]', function() {
        // Extract the product id from the input name (e.g., "boxItem[3]", "dozenCount[3]", etc.)
        var name = $(this).attr('name');
        var match = name.match(/\[(\d+)\]/);
        if (match && match[1]) {
            var pid = match[1];
            updateCalculationsForProduct(pid);
        }
    });
</script>
<script>
document.getElementById("switch").addEventListener("click", function() {
    let heading = document.getElementById("heading");
    let button = document.getElementById("switch");
    let modeInput = document.getElementById("method");

    if (modeInput.value === "purchase") {
        heading.innerText = "Service Data";
        button.innerText = "Switch to Purchase";
        modeInput.value = "service";
    } else {
        heading.innerText = "Purchase Data";
        button.innerText = "Switch to Service";
        modeInput.value = "purchase";
    }

    console.log("Switch clicked - method:", modeInput.value); // Debugging
});

// Ensure method is included before submitting
document.getElementById("purchase_form").addEventListener("submit", function(event) {
    let modeInput = document.getElementById("method");
    if (!modeInput.value) {
        modeInput.value = "purchase";  // Set default if empty
    }

    console.log("Submitting form - method:", modeInput.value); // Debugging
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
<script>
// Initialize event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get all relevant input fields
    const inputs = [
        'product', 'buycost', 'vat', 'sellingcost', 
        'quantity_type', 'quantity', 'box_count'
    ];
    
    // Add event listeners to all relevant fields
    inputs.forEach(function(id) {
        document.getElementById(id).addEventListener('input', calculateAll);
    });
    
    // Also calculate when quantity type changes
    document.getElementById('quantity_type').addEventListener('change', function() {
        updateQuantityField();
        calculateAll();
    });
    
    // Set initial state
    updateQuantityField();
});

function updateQuantityField() {
    const quantityType = document.getElementById('quantity_type').value;
    const quantityInput = document.getElementById('quantity');
    
    if (quantityType === 'box') {
        quantityInput.placeholder = 'Enter number of boxes';
    } else {
        quantityInput.placeholder = 'Enter quantity';
    }
}

function calculateAll() {
    // Get all input values
    const quantityType = document.getElementById('quantity_type').value;
    const boxCount = parseFloat(document.getElementById('box_count').value) || 1;
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const buyCost = parseFloat(document.getElementById('buycost').value) || 0;
    const vatPercent = parseFloat(document.getElementById('vat').value) || 0;
    const sellingCost = parseFloat(document.getElementById('sellingcost').value) || 0;
    
    // Calculate actual quantity
    let actualQuantity = quantity;
    if (quantityType === 'box') {
        actualQuantity = quantity * boxCount;
    }
    document.getElementById('actual_quantity').value = actualQuantity;
    
    // Calculate rate (selling cost + vat)
    const vatAmount = buyCost * (vatPercent / 100);
    const rate = buyCost + vatAmount;
    document.getElementById('rate').value = rate.toFixed(2);
    
    // Calculate totals
    const withoutVat = buyCost * actualQuantity;
    const withVat = rate * actualQuantity;
    
    document.getElementById('without_vat').value = withoutVat.toFixed(2);
    document.getElementById('total').value = withVat.toFixed(2);
}
</script>