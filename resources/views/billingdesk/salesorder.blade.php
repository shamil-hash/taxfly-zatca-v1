<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Plexpay billing">
    @if ($page == 'sales_order')
        <title>Sales Order</title>
    @elseif ($page == 'quotation')
        <title>Quotation</title>
    @elseif ($page == 'performance_invoice')
        <title>Proforma Invoice</title>
    @endif
    @include('layouts/usersidebar')
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
.select2-container--default .select2-selection--single {
    height: 40px; /* Change this value */
    line-height: 40px; /* Should match height for vertical alignment */
}

/* Adjust the dropdown arrow position */
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px; /* Match the input height */
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
   <div class="dropdown">
        <button class="btn btn-info" style="background-color:#187f6a;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            ☰
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            @if ($page == 'sales_order')
                {{-- @include('layouts.quick') --}}
                <a class="dropdown-item" href="/draft/salesdraft">Drafts</a>
                <a class="dropdown-item" href="/history/sales_order">Sales Order History</a>
            @elseif ($page == 'quotation')
                {{-- @include('layouts.quick') --}}
                <a class="dropdown-item" href="/draft/quotationdraft">Drafts</a>
                <a class="dropdown-item" href="/history/quotation">Quotation History</a>
                <a class="dropdown-item" data-toggle="modal" data-target="#addProductModal">Add New Product</a>

            @elseif ($page == 'performance_invoice')
                {{-- @include('layouts.quick') --}}
                <a class="dropdown-item" href="/draft/performadraft">Drafts</a>
                <a class="dropdown-item" href="/history/performance_invoice">Proforma Invoice History</a>
            @endif
            {{-- <a class="dropdown-item" href="">Refresh</a> --}}
        </div>
    </div>
        @if ($page == 'quotation')
    @include('modal.product_modal.add_product_modal', [
    'categories' => $categories,
    'units' => $units,
    'page' => $page,
    ])
    @endif
        <x-admindetails_user :shopdatas="$shopdatas" />
        @if (session('success'))
        <div class="alert alert-success" style="text-align: center;">
            {{ session('success') }}
        </div>
    @endif
    <br><br>
        <form method="post" action="salesorder_submit" onsubmit="return validateForm();" id="billForm">
            @csrf
            <input type="hidden" name="page" id="page" value={{ $page }}>

           <div class="form group row" >
                <div style="background-color: #f8f9fa; padding: 10px; border-radius: 12px; border: 1px solid #dee2e6; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <!-- First Row: Customer ID, TRN Number, Phone No, Email -->
                    <div class="row">
                      <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1" style="font-size: 10px; width: 120px;">Customer ID</span>
                            <input
                              style="width: 100px; font-size: 12px;height:auto;"
                              id="cust_id"
                              name="customer_name"
                              class="form-control customer_id"
                              placeholder="Customer ID:"
                            />
                            </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                          <span class="input-group-addon" id="basic-addon1" style="font-size: 10px; width: 120px;">TRN Number</span>
                          <input
                            style="width: 100px; font-size: 12px;height:auto;"
                            id="trn_number"
                            name="trn_number"
                            class="form-control trn_no"
                             placeholder="TRN Number:"
                          >
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="input-group">
                          <span class="input-group-addon" id="basic-addon1" style="font-size: 10px; width: 120px;">Phone No.</span>
                          <input
                            style="width: 100px; font-size: 12px;height:auto;"
                            id="phone"
                            name="phone"
                            class="form-control phone"
                            placeholder="Phone:"
                          >
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="input-group">
                          <span class="input-group-addon" id="basic-addon1" style="font-size: 10px; width: 120px;">Email</span>
                          <input
                            style="width: 100px; font-size: 12px;height:auto;"
                            id="email"
                            name="email"
                            class="form-control email"
                            placeholder="Email:"
                          >
                        </div>
                      </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">

                        <div class="col-md-3">
                            <select class="sales_credit credituser" onclick="creditUser(this.value)"
                            onchange="getCreditId(this.value)" id="user_id" name="user_id" style="width: 222px; font-size: 10px;">
                            <option value="">SELECT CUSTOMER</option>
                            @foreach ($creditusers as $credituser)
                                <option value="{{ $credituser->id }}" data-id="{{ $credituser->id }}">
                                    {{ $credituser->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="credit_id" id="credit_id">
                    </div>

                    <div class="col-md-3">
                        <select class="sales_credit credituser" id="employee_id" name="employee_id" style="width: 222px; font-size: 10px;" onchange="updateEmployeeName()">
                            <option value="">SELECT EMPLOYEE</option>
                            @foreach ($listemployee as $employee)
                                <option value="{{ $employee->id }}" data-name="{{ $employee->first_name }}">{{ $employee->first_name }}</option>
                            @endforeach
                        </select>
                        <!-- Hidden input for employee_name -->
                        <input type="hidden" name="employee_name" id="employee_name">
                    </div>

                    <div class="col-md-3">
                        <div class="input-group">
                          <span class="input-group-addon" id="basic-addon1" style="font-size: 10px; width: 120px;">Barcode</span>
                          <input
                            type="text"
                            id="barcodenumber"
                            name="barcodenumber"
                            style="width: 100px; font-size: 12px;height:auto;"
                            class="form-control barcodenumber"
                            placeholder="Click Here"
                            autofocus
                          >
                        </div>
                      </div>

                      <div class="col-md-3">
                    </div>
                </div>
                </div>
                <div class="row">
                    <input type="hidden" name="vat_mode" id="vat_mode" value="{{$mode}}">
                    <br />
                    <div class="col-md-4" style="padding-top: 5px;">
                        <div class="form-group pl-1 hide">
                            <span class="form-group-addon pr-2" id="vat_type" for="vat_type">{{$tax}} Type <span
                                    style="color: red;">*</span></span>
                            <label class="pr-2">
                                <input type="radio" class="vattype_mode disable-after-select-vat"
                                    name="vat_type_mode" value="1" tabindex="4" id="inclusive_radio">Inclusive
                            </label>
                            <label>
                                <input type="radio" class="vattype_mode disable-after-select-vat"
                                    name="vat_type_mode" value="2" tabindex="5" id="exclusive_radio">Exclusive
                            </label>

                            <input type="hidden" name="vat_type_value" id="vat_type_value">
                        </div>
                        <div style="">
                            <button id="inclusive_button" type="button" class="vat-button">Inclusive</button>
                            <button id="exclusive_button" type="button" class="vat-button">Exclusive</button>
                            <div style="margin-top: 5px;" id="vat_message"></div> <!-- Placeholder for the VAT type message -->

                        </div>
                        <span style="color:red">
                            @error('payment_mode')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                </div>

                <div>
                    <div id="prebuiltbilldiv" style="display:block;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="2%"></th>
                                    <th width="10%">Product Name</th>
                                    <th width="8%">Quantity</th>
                                    <th width="6%">Unit</th>
                                    <th width="8%">Rate</th>
                                    <th width="8%" id="inclusive_heading" style="display:none">Inclusive Rate
                                    </th>
                                    <th width="8%" id="ratediscount_heading" style="display:none">Exclusive Rate
                                    </th>
                                    <th width="9%">
                                        <div class="form-inline">
                                            <label for="discount_type" class="control-label">Discount</label>
                                            <select name="discount_type" id="discount_type"
                                                class="form-control custom-select-no-padding">
                                                <option value="none">No</option>
                                                <option value="percentage">%</option>
                                                <option value="amount">{{ $currency }}</option>
                                            </select>
                                        </div>
                                    </th>
                                    <th width="8%" id="vat_perc">{{$tax}}(%)</th>
                                    <th width="8%" id="vat_ammi">Total {{$tax}} Amount</th>
                                    <th width="8%">Net Rate</th>
                                    <th width="10%">Total Amount</th>
                                    <th style="display: none;" width="10%">Total w/o <br /> Discount</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="background-color: #f8f9fa;">
                                    <td></td>
                                   <td data-label="Description">
                                        <div id="pselect" class="">
                                            <input
                                                type="text"
                                                id="product"
                                                class="form-control product-input"
                                                list="productList"
                                                placeholder="Search or type product name"
                                                autocomplete="off"
                                                style="width: 250px"
                                                oninput="handleProductInput(this.value)"
                                            >
                                            <datalist id="productList">
                                                @foreach ($items as $item)
                                                    <option value="{{ $item['product_name'] }}" data-id="{{ $item['id'] }}">
                                                        {{ $item['product_name'] }}
                                                    </option>
                                                @endforeach
                                            </datalist>
                                            <input type="hidden" id="product_id" name="product_id">
                                        </div>
                                        <div id="selectproduct" class="hide">
                                            <input
                                                type="text"
                                                id="barcodeproduct"
                                                class="form-control"
                                                style="width: 250px"
                                                list="barcodeProductList"
                                                placeholder="Search by barcode"
                                                autocomplete="off"
                                            >
                                            <datalist id="barcodeProductList"></datalist>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="1" id="qty" class="form-control qty"
                                            min="1" max="" tabindex="1">
                                    </td>
                                    <td><input type="text" name="prounit" id="prounit" class="form-control"
                                            readonly></td>
                                    <td>
                                        <input type="number" step="any" id="mrp" class="form-control"
                                            tabindex="2">
                                        <input type="hidden" step="any" id="buycost" class="form-control">
                                        <input type="hidden" step="any" id="buycost_rate" name="buycost_rate"
                                            class="form-control">
                                    </td>

                                    <td id="inclusive_rate_value" name="inclusive_rate_value" style="display: none;">
                                    </td>
                                    </td>
                                    <td id="rate_discount_value" name="rate_discount_value" style="display: none;">
                                    </td>
                                    <td>
                                        <input type="number" step="any" id="discount" class="form-control"
                                            min="0" tabindex="3">
                                    </td>
                                    <td>
                                        <input type="number" step="any" id="fixed_vat" class="form-control"
                                            tabindex="4">
                                    </td>
                                    <td>
                                        <input type="number" step="any" id="vat_amount" class="form-control"
                                            readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" id="net_rate" class="form-control"
                                            tabindex="5" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" id="price" class="form-control"
                                            readonly>
                                        <input type="hidden" id="pricex" class="form-control">
                                    </td>
                                    <td style="display: none;">
                                        <input type="number" step="any" id="price_wo_discount"
                                            class="form-control" readonly>
                                        <input type="hidden" step="any" id="total_wo_discount"
                                            class="form-control" readonly>
                                    </td>

                                    <td><a href="#" class="btn btn-info addRow" style="background-color:#187f6a;" title="Add Row">+</a></td>
                                    <input type="hidden" id="product_id" class="form-control">
                                    <input type="hidden" id="product_name" class="form-control">
                                    <input type="hidden" id="remain" name="remain" class="form-control">
                                </tr>
                                <tr>
                                    <td colspan="12"> <i class="glyphicon glyphicon-tags"></i> &nbsp BILL</td>
                                <tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <div class="row" style="margin: 1rem; background-color: #f8f9fa;padding:1rem;">

                        <div class="col-sm-7">
                            <div class="discount_row">
                                <div class="checkbox-label">
                                    <label for="total_discount">Add Total discount</label>&nbsp;
                                    <select name="total_discount" id="total_discount"
                                        class="form-control custom-select-no-padding"
                                        style="width: 80px;margin-top:-5px;">
                                        <option value="0">No</option>
                                        <option value="1">%</option>
                                        <option value="2">{{ $currency }}</option>
                                    </select>
                                </div>
                                <div id="discount_field_percentage" class="hidden group_dis">
                                    <div>
                                        <label for="discount_percentage">Discount in %</label>
                                        <input type="number" id="discount_percentage" name="discount_percentage"
                                            disabled>
                                        <span style="margin-right: 3px;">%</span>
                                    </div>
                                </div>
                                <div id="discount_field_amount" class="hidden group_dis">
                                    <label for="discount_amount">or</label>
                                    <input type="number" id="discount_amount" name="discount_amount" disabled>
                                    <span>{{ $currency }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3"
                            style="display: flex; flex-direction: column; align-items: flex-end;margin-bottom: 6px;">
                            <div class="input-group" style="display: flex; align-items: center; margin-bottom: 6px;">
                                <label for="sales_grand_total" class="mr-2" style="margin-right: 10px;">Grand
                                    Total:</label>
                                <input type="text" id="sales_grand_total" name="sales_grand_total"
                                    class="form-control-plaintext border-0"
                                    style="width: 70px; background: transparent; border: none;" readonly>
                            </div>
                            <div class="input-group" style="display: flex; align-items: center;">
                                <label for="sales_grand_total_wo_discount" class="mr-2"
                                    style="margin-right: 10px;">Grand
                                    Total without Discount:</label>
                                <input type="number" id="sales_grand_total_wo_discount"
                                    name="sales_grand_total_wo_discount" class="form-control-plaintext border-0"
                                    style="width: 70px; background: transparent; border: none;" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-7"></div>
                            <div class="col-sm-3"
                                style="display: flex; flex-direction: column; align-items: flex-end;">
                                <div class="input-group form-inline"
                                    style="display: flex; align-items: center; margin-bottom: 6px;">
                                    <div class="form-group" style="display: flex; align-items: center; ">
                                        <label for="payment_type" class="mr-2" style="margin-right: 12px;">Payment
                                            Type : </label>
                                        <br />
                                        @php
                                            $hasCreditRole = false;
                                        @endphp
                                        <select class="form-control" id="payment_type" name="payment_type"
                                            style="width: auto;border-radius:5px;" onchange="handlePaymentTypeChange(this.value)">
                                            <option value="1" id="cashoption">
                                                CASH</option>
                                    @foreach ($users as $user)
                                                <?php if ($user->role_id == '24') { ?>
                                            <option value="2">BANK</option>
                                            <?php } ?>
                                                <?php if ($user->role_id == '11') { ?>
                                                @php
                                                    $hasCreditRole = true;
                                                @endphp
                                                <option value="3" id="creditoption" style="display: none"
                                                    disabled>CREDIT</option>
                                                <?php } ?>
                                            @endforeach
                                            <option value="4">POS CARD</option>
                                        </select>
                                        &nbsp;
                                        &nbsp;
                                        &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;
                                        <select name="bank_name" id="bank_name" class="form-control"
                                        style="display: none;border-radius:5px;width:100%;">
                                        <option value="">SELECT BANK</option>
                                        @foreach ($listbank as $bank)
                                            @if ($bank->status == 1)
                                            <option value="{{ $bank->id }}">{{ $bank->bank_name }} ({{ $bank->account_name }})</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="account_name" id="account_name" value="">
                                    <input type="hidden" name="bank_id" id="bank_id" value="">

                                        <input type="hidden" name="user_credit_role" id="user_credit_role"
                                            value="{{ $hasCreditRole ? '11' : 'none' }}">
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row" align="right">
                        <div class="col-md-8">
                            <br />
                            <input class="btn btn-primary" type="submit" value="submit" id="submitBtn">
                            <button type="button" class="btn btn-warning" id="saveDraftBtn"
                                onclick="saveToDraft()">Save to Draft</button>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script src="{{ asset('javascript/billing.js') }}"></script>
</body>

</html>
<!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
// <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const paymentTypeSelect = document.getElementById('payment_type');
        const bankNameSelect = document.getElementById('bank_name');
        const accountNameInput = document.getElementById('account_name');
        const bankIdInput = document.getElementById('bank_id');

        paymentTypeSelect.addEventListener('change', function () {
            if (this.value == 2) {
                bankNameSelect.style.display = 'block';
            } else {
                bankNameSelect.style.display = 'none';
                bankIdInput.value = '';
                accountNameInput.value = '';
            }
        });

        bankNameSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];

            if (selectedOption) {
                const accountName = selectedOption.text.match(/\(([^)]+)\)/)[1];
                const bankId = selectedOption.value;

                accountNameInput.value = accountName;
                bankIdInput.value = bankId;

            }
        });
    });
    </script>
<script>
    function saveToDraft() {
        var form = document.getElementById("billForm");

        @switch($page)
            @case('sales_order')
            form.action = "{{ route('data.savesalesDraft') }}";
            @break

            @case('quotation')
            form.action = "{{ route('data.savequotationDraft') }}";
            @break

            @case('performance_invoice')
            form.action = "{{ route('data.saveperformanceDraft') }}";
            @break

            @default
            console.error('Unknown page type: {{ $page }}');
            return; // exit the function if the page type is unknown
        @endswitch

        if (validateForm()) {
            form.submit();
        }
    }


</script>

<script>
 
     $(document).ready(function() {
      $('.sales_product,.sales_credit').select2({
        theme: "default" // Omit or use "default", "bootstrap", etc.
      });
    });


    $(document).ready(function() {
        handleDiscount("#discount_type", "#discount");
        setFocus("#barcodenumber");
        handleVatSelection();

        $('input[name="vat_type_mode"]').on("change, input", function() {

            var vat_type = $('input[name="vat_type_mode"]:checked').val();
            var selectval = $("#vat_type_value").val();
            var tax = @json($tax); // Ensure tax is converted properly for JavaScript

            handleVatTypeChange(vat_type,tax);
        });

        generateCustomerID();
        handleBarcodeNumberKeyup();
        preventFormSubmitOnEnter();
        TotalBillDiscount();
    });
</script>


<script>
 function getCreditId(selectedUsername) {
    // Get the selected option from the user dropdown
    const quotationDropdown = document.getElementById('user_id');
    const selectedOption = quotationDropdown.options[quotationDropdown.selectedIndex];

    // Get the selected user's ID
    var selectedUserId = selectedOption.getAttribute('data-id');

    // Update the hidden credit_id input with the selected user ID
    document.getElementById('credit_id').value = selectedUserId;

    // Refresh and show the bank dropdown
    let bankDropdown = document.getElementById("bank_name");
    bankDropdown.selectedIndex = 0;
    bankDropdown.style.display = 'block';


}

</script>

<script type="text/javascript">
    var addedProducts = [];

    function updateGrandTotalAmount() {
        var grandtotal = 0;

        $('.total-amount-sales').each(function() {
            grandtotal += Number($(this).val());
        });

        grandtotal = grandtotal.toFixed(2);
        grandtotal = parseFloat(grandtotal);
        $('#sales_grand_total_wo_discount').val(grandtotal);

        // Get the discount amount
        var discountAmount = 0;

        var discountPercentage = $('#discount_percentage').val();
        var discountValue = $('#discount_amount').val();

        if (discountPercentage > 0) {
            discountAmount = (grandtotal * discountPercentage) / 100;
        } else if (discountValue > 0) {
            discountAmount = discountValue;
        }
        // Subtract the discount amount from the grand total
        grandtotal -= discountAmount;

        grandtotal = grandtotal.toFixed(2);
        grandtotal = parseFloat(grandtotal);
        $('#sales_grand_total').val(grandtotal);
    }

    $('#discount_percentage, #discount_amount').on('input', function() {
        updateGrandTotalAmount();
    });

    // Initial update
    updateGrandTotalAmount();

    $('.addRow').off().on('click', addRow);

    function addRow() {

        var productId = $('#product_id').val();
         if (!productId) {
            alert("This Product is not exist.");
            return;
        }
        var data = $('#barcodenumber').val();

        if (data == "") {
            if (($("#product").val()) == "") {
                return;
            }
            // Check if the product is already added
            if (addedProducts.includes(productId)) {
                alert('Product is already added.');
                return;
            }
        } else {
            // if (($("#barcodeproduct").val()) == "") {
            //     return;
            // }

            var barselect = $('#barcodeproduct').val();
            if (barselect == "" || barselect == null) {
                return;
            }

            if (addedProducts.includes(productId)) {
                alert('Product is already added.');
                $("#barcodenumber").val("");
                $("#barcodeproduct").val("");
                $("#qty").val("");

                $("#selectproduct").addClass('hide');
                $("#pselect").removeClass('hide');
                $("#product").val(null).trigger('change');

                return;
            }
        }

        if (($("#price").val()) <= 0) {
            return;
        }

        var y = ($("#product_name").val());
        var w = Number($("#mrp").val());
        var z = Number($("#price").val());
        var q = Number($("#fixed_vat").val());
        var x = Number($("#qty").val());
        var p = Number($("#pricex").val());
        var vat = Number($("#vat_amount").val());
        var netrate = Number($("#net_rate").val());
        var punit = ($("#prounit").val());
        var wv = Number($("#buycost").val());
        var buycost_rate = Number($("#buycost_rate").val());
        var discount = Number($("#discount").val());

        discount = (discount != null) ? discount : 0;

        var discount_type = $("#discount_type").val();

        discount_type = (discount_type != null) ? discount_type : 0;

        var price_dis = Number($("#price_wo_discount").val());
        var total_disc = Number($("#total_wo_discount").val());

        if (($("#qty").val()) == "") {
            return;
        }

        if (!validateDiscount()) {
            return;
        }

        var u = Number($("#product_id").val());
        var vat_type = $('input[name="vat_type_mode"]:checked').val();

        if (vat_type == 1) {
            var inclu_rate = Number($("#inclusive_rate").val());
        } else if (vat_type == 2) {
            var ratediscount = Number($("#rate_discount").val());
        }

        // var totalamount = netrate * x;
        // var totalamount = Math.round(totalamount * 1000) / 1000;

        var tr = '<tr>' + '<td></td>' + '<td>' +
            '<input type="text" id="productnamevalue" value="' + y + '" name="productName[' + u +
            ']" class="form-control" readonly> <input type="hidden"  value="' + u + '" name="productId[' + u +
            ']" class="form-control">' +
            '</td>' +
            '<td id="barquan"><input type="text" value=' + x + ' id="quantityrow" name="quantity[' + u +
            ']" min="1" max="" class="form-control" required></td>' +
            '<td><input type="text" value=' + punit + ' name="prounit[' + u + ']" class="form-control" readonly></td>' +
            '<td><input type="number" value=' + w + ' name="mrp[' + u +
            ']" class="form-control" readonly><input type="hidden" step="any" name="buy_cost[' + u + ']" value=' + wv +
            ' class="form-control">' +
            '<input type="hidden" step="any" name="buycost_rate[' + u + ']" value=' + buycost_rate +
            ' class="form-control"></td>';

        if (vat_type == 1) {
            tr += '<td><input type="number" value=' + inclu_rate + ' name="inclusive_rate_r[' + u +
                ']" class="form-control" readonly></td>';
        } else if (vat_type == 2) {
            tr += '<td><input type="number" value=' + ratediscount + ' name="rate_discount_r[' + u +
                ']" class="form-control" readonly></td>';
        }

        tr += '<td><input type="text" value="' + discount + '" name="dis_count[' + u +
            ']" id="discountInput" class="form-control" min="0" ' + (discount_type === 'none' ? 'readonly' : '') + ' >';
        tr += '<select name="dis_count_type[' + u + ']" class="form-control custom-select-no-padding">';
        tr += '<option value="none" ' + (discount_type === 'none' ? 'selected' : '') + '>No</option>';
        tr += '<option value="percentage" ' + (discount_type === 'percentage' ? 'selected' : '') + '>%</option>';
        tr += '<option value="amount" ' + (discount_type === 'amount' ? 'selected' : '') + '>' +
            '{{ $currency }}' + '</option>';
        tr += '</select></td>';

        tr += '<td><input type="number" value=' + q + ' name="fixed_vat[' + u +
            ']" class="form-control" readonly></td>' +
            '<td><input type="number" value=' + vat + ' id="vat_amt" name="vat_amount[' + u +
            ']" class="form-control" readonly></td>' +
            '<td><input type="number" value=' + netrate + ' name="net_rate[' + u +
            ']" class="form-control" readonly></td>' +
            '<td><input type="number" value=' + z + ' id="total_amount" name="total_amount[' + u +
            ']" class="form-control total-amount-sales" readonly><input type="hidden" value=' + p +
            ' id="rowprice" name="price[' + u + ']" class="form-control"></td>' +
            '<td style="display: none;"><input type="number" value=' + price_dis +
            ' id="total_amount_wo_discount" name="total_amount_wo_discount[' + u +
            ']" class="form-control total_with_discount" readonly>' +
            '<input type="hidden" value=' + total_disc +
            ' id="price_withoutvat_wo_discount" name="price_withoutvat_wo_discount[' + u +
            ']" class="form-control" readonly></td>' +
            '<td><a href="#" class="btn btn-danger remove">-</a></td>' +
            '<input type="hidden" value=' + u + ' name="product_id[' + u + ']" class="form-control" >' +
            '</tr>';
        $('tbody').append(tr);

        addedProducts.push(productId);

        var nu = "";
        $("#qty").val(nu);
        $("#price").val(nu);
        $("#barcodenumber").val(nu);
        $("#product_name").val(nu);
        $("#product_id").val(nu);
        $("#mrp").val(nu);
        $("#buycost").val(nu);
        $("#fixed_vat").val(nu);
        $("#vat_amount").val(nu);
        $("#net_rate").val(nu);
        $("#pricex").val(nu);
        $("#prounit").val(nu);
        $("#product").val(null).trigger('change');
        $('#barcodenumber').focus();
        $("#barcodenumber").val(nu).trigger('change');

        if (vat_type == 1) {
            $("#inclusive_rate").val(nu);
        } else if (vat_type == 2) {
            $("#rate_discount").val(nu);
        }

        $("#buycost_rate").val(nu);
        $("#discount").val(nu);
        $("#price_wo_discount").val(nu);
        $("#total_wo_discount").val(nu);

        /*--------------EDIT ADDED ROW'S QUANTITY AND BASED ON CHANGE VAT AMOUNT  & TOTAL AMOUNT----------------*/

        var array = @json($items);

        function isSeries(elm) {
            return elm.id == u;
        }

        var page = $('#page').val();

        addRowDiscountCalculation(u, 0, $('input[name="vat_type_mode"]:checked').val(), 0, page);

        updateGrandTotalAmount();
    };

    $('tbody').on('click', '.remove', function() {
        // Remove the product ID from the addedProducts array
        var productId = $(this).parent().parent().find('input[name^="productId["]').val();
        var index = addedProducts.indexOf(productId);
        if (index !== -1) {
            addedProducts.splice(index, 1);
        }

        $(this).parent().parent().remove();
        updateGrandTotalAmount();
    });

    function doSomething(x) {

        var vat_type_selected = $('input[name="vat_type_mode"]:checked').val();
        if (vat_type_selected == null) {
            // $('#vatModeAlert').text('Please select VAT mode first.');
            alert('Please select {{$tax}} Type first.');
            $("#product").val(null).trigger('change');
            return;
        }

        var array = @json($items);

        function isSeries(elm) {
            return elm.id == x;
        }

        var page = $('#page').val();

        if (page == "sales_order" || page == "performance_invoice") {

            var w = array.find(isSeries).product_name;
            $('#product_name').val(w);
            var k = array.find(isSeries).selling_cost;
            $('#mrp').val(k);
            var y = array.find(isSeries).id;
            $('#product_id').val(y);
            var t = array.find(isSeries).vat;
            $('#fixed_vat').val(t);

            var rem = array.find(isSeries).remaining_stock;

            var prunit = array.find(isSeries).unit;
            $('#prounit').val(prunit);

            var kv = array.find(isSeries).buy_cost;
            $('#buycost').val(kv);

            var buycost_rate_kv = array.find(isSeries).rate;
            $('#buycost_rate').val(buycost_rate_kv);

            if (vat_type_selected == 1) {

                var InclusiveRate = k / (1 + (t / 100));
                var InclusiveRate = Math.round(InclusiveRate * 1000) / 1000;
                $("#inclusive_rate").val(InclusiveRate);
            } else if (vat_type_selected == 2) {
                var mrp_with_discount = k;
                $("#rate_discount").val(mrp_with_discount);
            }

            var nu = "";
            $("#qty").val(1);
            $("#price").val(nu);
            $("#vat_amount").val(nu);
            $("#discount").val(nu);
            $("#price_with_discount").val(nu);
            $("#total_with_discount").val(nu);
            $('#qty').focus();

            if (vat_type_selected == 1) {
            discount_calcu_inclu();

        } else if (vat_type_selected == 2) {
            discount_calcu_exclus();

        }
        handleVatTypeChange();
        handleBarcodeNumberKeyup();
        // checkquantity();

        } else if (page == "quotation") {

            var foundProduct = array.find(isSeries);

            if (foundProduct) {

                var w = array.find(isSeries).product_name;
                $('#product_name').val(w);
                var k = array.find(isSeries).selling_cost;
                $('#mrp').val(k);
                var y = array.find(isSeries).id;
                $('#product_id').val(y);
                var t = array.find(isSeries).vat;
                $('#fixed_vat').val(t);

                var rem = array.find(isSeries).remaining_stock;

                var prunit = array.find(isSeries).unit;
                $('#prounit').val(prunit);

                var kv = array.find(isSeries).buy_cost;
                $('#buycost').val(kv);

                var buycost_rate_kv = array.find(isSeries).rate;
                $('#buycost_rate').val(buycost_rate_kv);

                if (vat_type_selected == 1) {
                    var InclusiveRate = k / (1 + (t / 100));
                    var InclusiveRate = Math.round(InclusiveRate * 1000) / 1000;
                    $("#inclusive_rate").val(InclusiveRate);
                } else if (vat_type_selected == 2) {
                    var mrp_with_discount = k;
                    $("#rate_discount").val(mrp_with_discount);
                }

                var nu = "";
                $("#qty").val(1);
                $("#price").val(nu);
                $("#vat_amount").val(nu);
                $("#discount").val(nu);
                $("#price_with_discount").val(nu);
                $("#total_with_discount").val(nu);
                $('#qty').focus();

                if (vat_type_selected == 1) {
            discount_calcu_inclu();

        } else if (vat_type_selected == 2) {
            discount_calcu_exclus();

        }
        handleVatTypeChange();
        handleBarcodeNumberKeyup();
        // checkquantity();

            } else {
                console.error('Product not found for ID: ' + x);
            }
        }
    }

    function creditUser(x) {
        var k = x;
        // $('#cust_id').val(k);
        // $('#cust_id').val("");

        // $("#payment_type").val(3).trigger('change');
        if (x != "") {
            var role = $("#user_credit_role").val();
            // $("#payment_type #creditoption").show();

            if (role == 11) {
                $("#payment_type #creditoption").show();
                $("#payment_type").val(1).trigger("change");
                $("#payment_type #creditoption").prop("disabled", false);
            } else if (role == "none") {
                $("#payment_type #creditoption").hide();
                $("#payment_type #creditoption").prop("disabled", true);
                $("#payment_type").val(1).trigger("change");
            }

            $.ajax({
                type: 'get',
                url: '/gethistorysales/' + x,
                success: function(data) {
                    var trn_number = (data.trn_number);
                    var phone = (data.phone);
                    var email = (data.email);

                    $('#trn_number').val(trn_number);
                    $('#phone').val(phone);
                    $('#email').val(email);

                    var full_name = (data.full_name);
                    $('#cust_id').val(full_name);

                    $('#barcodenumber').focus();
                }
            });
        }

        if (x == "") {

            $('#payment_type #creditoption').hide();

            // var daterandom = Date.now();
            // $("#cust_id").val(daterandom);

            generateCustomerID();

            $("#trn_number").val("");
            $("#phone").val("");
            $("#email").val("");
            $("#payment_type").val(1).trigger('change');
            // document.getElementById('buttondiv').style.display = "none";
            $('#barcodenumber').focus();

            // Disable the credit option in the payment dropdown
            $('#payment_type #creditoption').prop('disabled', true);
        }
    }

    /* validate plus double submit removal */

    function validateForm() {
        // Prevent the form from submitting multiple times
        const form = document.getElementById("billForm");
        const submitBtn = document.getElementById("submitBtn");
        var payment_type = $("#payment_type").val();

        if (submitBtn.disabled) {
            return false; // Prevent form submission
        }

        // Disable the submit button
        submitBtn.disabled = true;
        submitBtn.innerText = "Submitting...";

        if (payment_type == 2) {
        var accountSelect = document.getElementById('bank_name');
    if (accountSelect.value === "") {
        alert("Please select a Bank.");
        accountSelect.focus();

        // Re-enable the submit button after alert
        submitBtn.disabled = false;
        submitBtn.innerText = "Submit";

        return false; // Validation failed; prevent form submission
    }
}

        // Validate Title
        var product = $("#productnamevalue").val();
        if (product == "" || product == null) {
            alert("Press the add button");

            // Re-enable the submit button after alert
            submitBtn.disabled = false;
            submitBtn.innerText = "Submit";

            return false; // Validation failed; prevent form submission
        }

        // Check if any added row's quantity is empty
        var rows = $("input[name^='quantity[']");
        var isEmptyQuantity = false;

        rows.each(function() {
            var quantityInput = $(this).val();
            if (quantityInput == "" || quantityInput == null) {
                isEmptyQuantity = true;
                return false; // Exit the loop
            }
        });

        if (isEmptyQuantity) {
            alert("Quantity cannot be empty.");
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
    var product_name_name = document.getElementById("product_name");
    var qty = document.getElementById("qty");
    var mrp = document.getElementById("mrp");
    var fixed_vat = document.getElementById("fixed_vat");
    var vat_amount = document.getElementById("vat_amount");
    var net_rate = document.getElementById("net_rate");
    var price = document.getElementById("price");
    var discount = document.getElementById("discount");


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
    discount.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {

        $('input[id="barcodenumber"]').change(function() {
            if ($(this).val() == "") {
                $("#pselect").removeClass('hide');
                $("#selectproduct").addClass('hide');
            } else {
                $("#pselect").addClass('hide');
                $("#selectproduct").removeClass('hide');
            }
        });

        $('input[id="barcodenumber"]').keyup(function() {
            if ($(this).val() == "") {
                $("#pselect").removeClass('hide');
                $("#selectproduct").addClass('hide');
            } else {
                $("#pselect").addClass('hide');
                $("#selectproduct").removeClass('hide');
            }
        });
    });
</script>

<script type="text/javascript">
    var currentBoxNumber = 0;

    $(".customer_id").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.trn_no");
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
    $(".trn_no").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.phone");
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
    $(".phone").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.email");
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
    $(".email").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.barcodenumber");
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
    $(".barcodenumber").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.qty");
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
    function handlePaymentTypeChange(paymentType) {
        const bankDropdown = document.getElementById('bank_name');

        if (paymentType == '2') {
            bankDropdown.style.display = 'block';
        } else {
            bankDropdown.style.display = 'none';
        }
    }

</script>
<script>
    function refreshBankDropdown(selectedCustomerId) {
        let bankDropdown = document.getElementById("bank_name");
        bankDropdown.selectedIndex = 0;
        bankDropdown.style.display = 'block';
    }
</script>
<script>
    function updateEmployeeName() {
        var selectElement = document.getElementById("employee_id");
        var selectedOption = selectElement.options[selectElement.selectedIndex];
        var employeeName = selectedOption.getAttribute("data-name");

        // Set the employee name in the input field
        document.getElementById("employee_name").value = employeeName || '';
    }
    </script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
     var tax = @json($tax); // Ensure tax is converted properly for JavaScript
     var vatMode = document.getElementById("vat_mode").value;  // Get the value of vat_mode
     var inclusiveRadio = document.getElementById("inclusive_radio");
     var exclusiveRadio = document.getElementById("exclusive_radio");

     // Set radio button selection based on vat_mode
     if (vatMode == "1") {
         inclusiveRadio.checked = true; // Select Inclusive
         exclusiveRadio.disabled = true; // Disable Exclusive
         document.getElementById("vat_type_value").value = "1"; // Set value to Inclusive
         handleVatTypeChange("1",tax); // Call handleVatTypeChange function for Inclusive
     } else if (vatMode == "2") {
         exclusiveRadio.checked = true; // Select Exclusive
         inclusiveRadio.disabled = true; // Disable Inclusive
         document.getElementById("vat_type_value").value = "2"; // Set value to Exclusive
         handleVatTypeChange("2",tax); // Call handleVatTypeChange function for Exclusive
     }

     // Event listener to handle radio button changes
     inclusiveRadio.addEventListener("change", function() {
         if (this.checked) {
             document.getElementById("vat_mode").value = "1"; // Update vat_mode to 1
             exclusiveRadio.disabled = true; // Disable Exclusive
             document.getElementById("vat_type_value").value = "1"; // Set value to Inclusive
             handleVatTypeChange("1",tax); // Call handleVatTypeChange function for Inclusive
         }
     });

     exclusiveRadio.addEventListener("change", function() {
         if (this.checked) {
             document.getElementById("vat_mode").value = "2"; // Update vat_mode to 2
             inclusiveRadio.disabled = true; // Disable Inclusive
             document.getElementById("vat_type_value").value = "2"; // Set value to Exclusive
             handleVatTypeChange("2",tax); // Call handleVatTypeChange function for Exclusive
         }
     });
 });
     </script>
 <script>
     document.addEventListener("DOMContentLoaded", function () {
         const inclusiveButton = document.getElementById("inclusive_button");
         const exclusiveButton = document.getElementById("exclusive_button");

         // Function to update VAT mode in the database and change button colors
         function updateVatMode(vatType) {
             console.log('Updating VAT mode:', vatType);

             // Update VAT mode in the database
             fetch('/update-vat-mode', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                 },
                 body: JSON.stringify({
                     vat_mode: vatType
                 })
             })
             .then(response => response.json())
             .then(data => {
                 console.log("VAT Mode Updated:", data);
                 // Change button colors based on the VAT mode
                 if (vatType === 1) {
                     inclusiveButton.classList.add("active");
                     exclusiveButton.classList.remove("active");
                 } else if (vatType === 2) {
                     exclusiveButton.classList.add("active");
                     inclusiveButton.classList.remove("active");
                 }
             })
             .catch(error => {
                 console.error("Error updating VAT mode:", error);
             });
         }

         // Event listener for Inclusive button
         inclusiveButton.addEventListener("click", function () {
             console.log('Inclusive button clicked');
             // Update VAT mode to 1 (Inclusive)
             updateVatMode(1);
         });

         // Event listener for Exclusive button
         exclusiveButton.addEventListener("click", function () {
             console.log('Exclusive button clicked');
             // Update VAT mode to 2 (Exclusive)
             updateVatMode(2);
         });

         // Check the current VAT mode (if stored in localStorage) and update button styles
         const storedVatType = localStorage.getItem("vat_type_mode");
         if (storedVatType === "1") {
             inclusiveButton.classList.add("active");
             exclusiveButton.classList.remove("active");
         } else if (storedVatType === "2") {
             exclusiveButton.classList.add("active");
             inclusiveButton.classList.remove("active");
         }
     });


         </script>
 <script>
   document.addEventListener("DOMContentLoaded", function () {
     // Get the value of vat_mode
     const vatMode = document.getElementById("vat_mode").value;

     // Reference to the message display element
     const vatMessageElement = document.getElementById("vat_message");

     // Get the button elements
     const inclusiveButton = document.getElementById("inclusive_button");
     const exclusiveButton = document.getElementById("exclusive_button");

     // Set the message and button styles based on the vat_mode value
     if (vatMode === "1") {
         vatMessageElement.textContent = "{{$tax}} type is Inclusive."; // Display inclusive message
         inclusiveButton.style.backgroundColor = "#187f6a"; // Green for inclusive
         inclusiveButton.style.color = "white"; // Text color for inclusive
         exclusiveButton.style.backgroundColor = "lightgray"; // Light gray for exclusive
         exclusiveButton.style.color = "black"; // Text color for exclusive
     } else if (vatMode === "2") {
         vatMessageElement.textContent = "{{$tax}} type is Exclusive."; // Display exclusive message
         exclusiveButton.style.backgroundColor = "#187f6a"; // Green for exclusive
         exclusiveButton.style.color = "white"; // Text color for exclusive
         inclusiveButton.style.backgroundColor = "lightgray"; // Light gray for inclusive
         inclusiveButton.style.color = "black"; // Text color for inclusive
     } else {
         vatMessageElement.textContent = "VAT mode is not set."; // If vat_mode is not 1 or 2
     }

     // Event listener for Inclusive button click
     inclusiveButton.addEventListener("click", function () {
         if (vatMode === "2") {
             const confirmation = confirm(
                 "Switching to Inclusive mode will remove your changes. Do you want to refresh the page?"
             );
             if (confirmation) {
                 location.reload(); // Refresh the page after confirmation
             }
         } else {
             document.getElementById("vat_mode").value = "1"; // Set to Inclusive
             vatMessageElement.textContent = "{{$tax}} type is Inclusive."; // Update message
             inclusiveButton.style.backgroundColor = "#187f6a"; // Green for inclusive
             inclusiveButton.style.color = "white"; // Text color
             exclusiveButton.style.backgroundColor = "lightgray"; // Light gray for exclusive
             exclusiveButton.style.color = "black"; // Text color
         }
     });

     // Event listener for Exclusive button click
     exclusiveButton.addEventListener("click", function () {
         if (vatMode === "1") {
             const confirmation = confirm(
                 "Switching to Exclusive mode will remove your changes. Do you want to refresh the page?"
             );
             if (confirmation) {
                 location.reload(); // Refresh the page after confirmation
             }
                 } else {
             document.getElementById("vat_mode").value = "2"; // Set to Exclusive
             vatMessageElement.textContent = "{{$tax}} type is Exclusive."; // Update message
             exclusiveButton.style.backgroundColor = "#187f6a"; // Green for exclusive
             exclusiveButton.style.color = "white"; // Text color
             inclusiveButton.style.backgroundColor = "lightgray"; // Light gray for inclusive
             inclusiveButton.style.color = "black"; // Text color
         }
     });
 });

 </script>
<script>
    function handleCreditVisibility(x) {
       const paymentTypeDropdown = document.getElementById('payment_type');
       const creditOption = document.getElementById('creditoption');

       if (x) {
           // Show and enable CREDIT if a customer is selected
           creditOption.style.display = "block";
           creditOption.disabled = false;

           // Automatically set Payment Type to CASH
           paymentTypeDropdown.value = "1"; // Set to CASH (value="1")
           handlePaymentTypeChange("1"); // Trigger any onchange logic for CASH
       } else {
           // Hide and disable CREDIT if no customer is selected
           creditOption.style.display = "none";
           creditOption.disabled = true;

           // Reset Payment Type to default
           paymentTypeDropdown.value = "1"; // Clear the selection
           handlePaymentTypeChange("1"); // Trigger onchange logic for clearing
       }
   }



   </script>
<script>
    function handleProductInput(value) {
    // Find the selected product in the datalist options
    const productList = document.getElementById('productList');
    const options = productList.getElementsByTagName('option');
    let selectedProductId = null;

    for (let option of options) {
        if (option.value === value) {
            selectedProductId = option.getAttribute('data-id');
            break;
        }
    }

    // Set the hidden product_id field
    document.getElementById('product_id').value = selectedProductId;

    // Call your existing function with the product ID
    if (selectedProductId) {
        doSomething(selectedProductId);
    }
}




</script>
