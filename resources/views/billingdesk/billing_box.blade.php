<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Plexpay billing">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Billing Desk</title>
    @include('layouts/usersidebar')

<style>
/* Simplified Base Styles */
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background-color: transparent;
    color: #2c3e50;
    margin: 0;
    padding: 0;
}

#content {
    padding: 30px;
}

.hidden {
    display: none;
}

/* Simplified Header */
.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px;
    background-color: white;
}

.quick-layout {
    position: relative;
    z-index: 1;
}

.dropdown-quick-container {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
}

.dropdown {
    position: relative;
    float: right;
}

.dropdown-menu {
    display: none;
    position: absolute;
    right: 100%;
    background: white;
    border: 1px solid #ddd;
    padding: 5px;
    margin-left: -100px;
}

.dropdown-menu a {
    display: block;
    padding: 6px 10px;
    text-decoration: none;
    color: black;
    border-bottom: 1px solid #ddd;
}

.dropdown-menu a:hover {
    background: #187f6a;
    color: white;
}

/* Simplified Form Styles */
#billForm {
    background-color: white;
    padding: 15px;
}

.input-group-addon {
    background-color: #f8f9fa;
    color: #495057;
    border: 1px solid #e0e6ed;
}

.form-control {
    border: 1px solid #e0e6ed;
    padding: 6px 10px;
    font-size: 13px;
    height: auto;
}

.form-control:focus {
    border-color: #187f6a;
}

/* Simplified Table */
table {
    border-collapse: separate;
    width: 100%;
    border: 1px solid #e5e7e9;
}

.table thead th {
    background-color: #f8f9fa;
    padding: 8px;
    border-bottom: 1px solid #e0e6ed;
}

.table tbody td {
    padding: 8px;
    vertical-align: middle;
    border-bottom: 1px solid #e0e6ed;
}

/* Simplified Buttons */
.btn {
    padding: 6px 12px;
    font-size: 13px;
}

.btn-info {
    background-color: #187f6a;
    color: white;
}

.btn-primary {
    background-color: #187f6a;
}

.btn-warning {
    background-color: #f39c12;
    color: white;
}

.addRow {
    padding: 4px 8px;
}

/* Simplified VAT Buttons */
.vat-button {
    background-color: white;
    border: 1px solid #e0e6ed;
    padding: 6px 12px;
    margin-right: 8px;
        border-radius: 6px;

}

.vat-button.active {
    background-color: #187f6a;
    color: white;
}

/* Simplified Discount Row */
.discount_row {
    display: flex;
    align-items: center;
    gap: 10px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 5px;
}

.custom-select-no-padding {
    padding: 6px;
    border: 1px solid #e0e6ed;
}

/* Simplified Totals Section */
.input-group {
    margin-bottom: 8px;
}

.form-control-plaintext {
    font-weight: 600;
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
        gap: 10px;
    }

    .dropdown-quick-container {
        width: 100%;
    }

    .table thead {
        display: none;
    }

    .table tbody tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #e0e6ed;
    }

    .table tbody td {
        display: flex;
        justify-content: space-between;
        padding: 8px 12px;
    }

    .discount_row {
        flex-direction: column;
    }
}

/* Simplified Select2 */
.select2-container--default .select2-selection--single {
    border: 1px solid #e0e6ed;
    padding: 6px;
}

/* Simplified Alert */
.alert {
    padding: 8px 15px;
    margin-bottom: 15px;
}

/* Hidden Elements */
.hide, .hidden {
    display: none;
}

/* Simplified Payment Type Section */
#payment_type {
    width: 100px !important;
}

#bank_name {
    width: 180px !important;
}

/* Simplified Advance Payment */
#advanceInput {
    width: 100px !important;
}

/* Simplified Submit Buttons */
#submitBtn, #saveDraftBtn {
    padding: 8px 20px;
    font-size: 14px;
}

/* Simplified Scrollbar */
::-webkit-scrollbar {
    width: 4px;
    height: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
}
</style>
<style>
    .message-container {
        position: relative;
    top: -30px;
        width: 100%;
        display: flex;
        flex-direction: row;
        gap: 10px;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: -25px;  /* Adjusted to move higher (more negative) */
        margin-bottom: 10px; /* Added for spacing below */
    }

    .message-container > div {
        padding: 6px 12px;
        border-radius: 4px;
        white-space: nowrap;
    }

    #selling-cost-display:not(:empty),
    #buycostmessage:not(:empty),
    #stockavailable:not(:empty) {
        background-color: #FFF9C4;
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

                <div class="header-container">
            <x-admindetails_user :shopdatas="$shopdatas" />
            <div class="dropdown-quick-container">
                <div class="dropdown">
                    <button style="background-color:#187f6a;" class="btn btn-info" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        ☰
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a href="/draft/bill_draft" class="dropdown-item ">Drafts</a>
                        <a class="dropdown-item" href="">Refresh</a>
                    </div>
                </div>
                <div class="quick-layout">
                    @include('layouts.quick')
                </div>
                <a style="border-radius: 5px;padding: 8px;" class="dropdown-item btn-info" data-toggle="modal" data-target="#addCustomerModal">Add New Customer</a>

                <button style="display:none;" id="switch" name="switch" type="button" class="btn btn-info">Switch to Service</button>
            </div>
        </div>
        @include('modal.add_customer_modal')
                @if (session('success'))
        <div class="alert alert-success" style="text-align: center;">
            {{ session('success') }}
        </div>
    @endif
        <form method="post" action="submitdata" onsubmit="return validateForm();" id="billForm">
            @csrf
            <div class="form group row" >
                <div style="background-color: #f8f9fa; padding: 8px; border: 1px solid #dee2e6;border-radius: 8px;">
                    <!-- First Row: Customer ID, TRN Number, Phone No, Email -->
                    <div class="row">
                      <div class="col-md-3">
                        <div class="input-group">
                          <span class="input-group-addon" style="font-size: 10px; width: 100px;">Customer ID</span>
                          <input
                            style="width: 90px; font-size: 12px;height:auto;"
                            id="cust_id"
                            name="customer_name"
                            class="form-control customer_id"
                            placeholder="Customer ID:"
                            autocomplete="off"
                        />
                            <input type="hidden" name="page" id="page" value="bill">
                            <input type="hidden" name="branchid" id="branchid" value="{{$branchid}}">
                            <div id="customerDropdown" class="dropdown-list" style="display: none; max-height: 150px; overflow-y: auto; border: 1px solid #ccc; position: absolute; background: white; z-index: 1000;">
                                <!-- Customer names will be populated here -->
                            </div>
                            <input type="hidden" id="creditNoteAmount" name="credit_note_amount" readonly>
                        </div>
                        <div id="creditNoteDisplay" style="margin-top: 8px; font-weight: bold;"></div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                          <span class="input-group-addon" style="font-size: 10px; width: 100px;">@if ($branchid==13)VAT Number @elseif ($branchid!=13)TRN Number @endif</span>
                          <input
                            style="width: 90px; font-size: 12px;height:auto;"
                            id="trn_number"
                            name="trn_number"
                            class="form-control trn_no" placeholder="TRN Number:">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                          <span class="input-group-addon" style="font-size: 10px; width: 100px;">Phone No.</span>
                          <input
                            style="width: 90px; font-size: 12px;height:auto;"
                            id="phone"
                            name="phone"
                            class="form-control phone"
                            placeholder="Phone:"
                          >
                        </div>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                      <span class="input-group-addon" style="font-size: 10px; width: 100px;">Email</span>
                      <input
                        style="width: 90px; font-size: 12px;height:auto;"
                        id="email"
                        name="email"
                        class="form-control email"
                        placeholder="Email:"
                      >
                    </div>
                  </div>
                </div>

                    <!-- Second Row: Barcode, Employee, Credit User -->
                    <div class="row" style="margin-top: 8px;">

                        <div class="col-md-3">
                          <select class="js-user credituser" onclick="creditUser(this.value)" onchange="getCreditId(this.value)" id="user_id" name="user_id" style="width: 200px; font-size: 10px;">
                            <option value="">SELECT CUSTOMER</option>
                            @foreach ($creditusers as $credituser)
                              <option value="{{ $credituser->id }}"
                                      data-id="{{ $credituser->id }}"
                                      data-current_lamount="{{ $credituser->current_lamount }}"
                                      data-balance="{{ $credituser->balance }}"
                                      data-name="{{ $credituser->name }}">
                                {{ $credituser->name }}
                              </option>
                            @endforeach
                          </select>
                          <input type="hidden" name="advance_balance" id="advance_balance">
                          <input type="hidden" name="customer" id="customer">
                          <input type="hidden" name="current_lamount" id="current_lamount">
                          <input type="hidden" name="credit_id" id="credit_id">
                          <input type="hidden" name="advance_balance_flag" id="advance_balance_flag" value="0">
                        </div>

                        <div class="col-md-3">
                          <select class="js-user" id="employee_id" name="employee_id" style="width: 200px; font-size: 10px;" onchange="updateEmployeeName()">
                            <option value="">SELECT EMPLOYEE</option>
                            @foreach ($listemployee as $employee)
                              <option value="{{ $employee->id }}" data-name="{{ $employee->first_name }}">{{ $employee->first_name }}</option>
                            @endforeach
                          </select>
                          <input type="hidden" name="employee_name" id="employee_name">
                        </div>

                        <div class="col-md-3">
                          <div class="input-group">
                            <span class="input-group-addon" style="font-size: 10px; width: 100px;">Barcode</span>
                            <input
                              type="text"
                              id="barcodenumber"
                              name="barcodenumber"
                              style="width: 90px; font-size: 12px;height:auto;"
                              class="form-control barcodenumber"
                              placeholder="Click Here"
                              autofocus
                            >
                          </div>
                        </div>

                        <div class="col-md-3">
                          <!-- Optional column -->
                        </div>
                      </div>
                    </div>
            <input type="hidden" name="vat_mode" id="vat_mode" value="{{$mode}}">
                <br>
                <div class="row">
                    <div class="col-md-4" style="padding-top: 5px;">
                        <div class="form-group pl-1 hide">
                            <span class="form-group-addon pr-2" id="vat_type" for="vat_type">{{$tax}} Type <span
                                    style="color: red;">*</span></span>
                                    <label class="pr-2">
                                        <input type="radio" class="vattype_mode disable-after-select-vat"
                                               name="vat_type_mode" value="1" id="inclusive_radio">Inclusive
                                    </label>
                                    <label>
                                        <input type="radio" class="vattype_mode disable-after-select-vat"
                                               name="vat_type_mode" value="2" id="exclusive_radio">Exclusive
                                    </label>

                            <input type="hidden" name="vat_type_value" id="vat_type_value">
                        </div>
                        <div style="margin-top: -10px;">
                            <button id="inclusive_button" type="button" class="vat-button">Inclusive</button>
                            <button id="exclusive_button" type="button" class="vat-button">Exclusive</button>
                            <div style="margin-top: 5px;" id="vat_message"></div>
                        </div>
                        <span style="color:red">
                            @error('payment_mode')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                   <div class="message-container">
                        <div id="selling-cost-display"></div>
                        <div id="stockavailable"></div>
                        <div id="buycostmessage"></div>
                    </div>
                    </div>
                <div style="margin-top: -50px;">
                    <div class="table-responsive">
                        <table class="table" style="border-radius: 8px;">
                            <thead>
                                <tr>
                                    <th width="2%"></th>
                                    <th width="10%">Product Name</th>
                                    <th width="8%" id="typeHeader" style="display:none;">Type</th>
                                    <th width="8%" id="boxHeader" style="display:none;">Boxes</th>
                                    <th width="8%">Quantity</th>
                                    <th width="8%">Rate</th>
                                    <th width="8%" style="display: none;" id="service">Service</th>
                                    <th width="8%" style="display: none;" id="servicecost">Service Cost</th>

                                    <th width="6%" id="unithide">Unit</th>
                                    <th width="8%" id="inclusive_heading" style="display:none">Inclusive Rate
                                    </th>
                                    <th width="8%" id="ratediscount_heading" style="display:none">Exclusive Rate
                                    </th>
                                    <th width="9%" id="discounthide">Discount</th>
                                    <th width="8%" id="vat_perc">{{$tax}}(%)</th>
                                    <th width="8%" id="vat_ammi">Total {{$tax}} Amount</th>
                                    <th width="8%">Net Rate</th>
                                    <th width="10%">Total Amount</th>
                                    <th style="display: none;" width="10%" id="totalhide">Total w/o <br /> Discount</th>
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
                                                style="width: 200px"
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
                                            <input type="hidden" id="box_count" name="box_count" class="form-control">
                                            <input type="hidden" id="box_enabled" name="box_enabled" class="form-control">
                                        </div>
                                        <div id="selectproduct" class="hide">
                                            <select onclick="doSomething(this.value)" id="barcodeproduct"
                                                class="form-control" style="width: 250px"></select>
                                        </div>
                                        
                                    </td>
                                    <td id="typeCell" style="display:none;">
                <select id="quantity_type" class="form-control">
                    <option value="quantity">Quantity</option>
                    <option value="box">Box</option>
                </select>
            </td>
            
            <!-- Box Input Cell -->
            <td id="boxCell" style="display:none;">
                <input type="number" id="box_input" class="form-control">
            </td>
                                 <td data-label="Quantity"><input type="number" step="1" id="qty" class="form-control qty"
                                            min="1" max="" onkeyup="checkquantity();"
                                            oninput="Barcodenotworkhere(this)" tabindex="1">
                                    </td>

                                    <td data-label="Rate">
                                        <input type="number" step="any" id="mrp" class="form-control"
                                            tabindex="2" oninput="Barcodenotworkhere(this)" >
                                        <input type="hidden" step="any" id="buycost" class="form-control">

                                        <input type="hidden" step="any" id="buycost_rate" name="buycost_rate"
                                            class="form-control">
                                    </td>
                                    <td data-label="Service" style="display: none"><input type="text" name="pservice" id="pservice" class="form-control"
                                        ></td>
                                    <td data-label="Servicecost" style="display: none"><input type="text" name="pservicecost" id="pservicecost" class="form-control"
                                            ></td>
                                            <td data-label="Unit"><input type="text" name="prounit" id="prounit" class="form-control"
                                                    readonly></td>
                                    <td id="inclusive_rate_value" name="inclusive_rate_value" style="display: none;">
                                    </td>
                                    <td id="rate_discount_value" name="rate_discount_value" style="display: none;">
                                    </td>
                                    <td data-label="Discount">
                                        <div class="d-flex flex-column">
                                            <select name="discount_type" id="discount_type" class="form-control custom-select-no-padding mb-2">
                                                <option value="none">No</option>
                                                <option value="percentage">%</option>
                                                <option value="amount">{{ $currency }}</option>
                                            </select>
                                            <input type="number" oninput="Barcodenotworkhere(this)"  step="any" id="discount" class="form-control" min="0" tabindex="3">
                                        </div>
                                    </td>
                                    <td data-label="{{$tax}}(%)">
                                        <input type="number" step="any" id="fixed_vat" class="form-control"
                                            tabindex="4" oninput="Barcodenotworkhere(this)">
                                    </td>
                                    <td data-label="Total {{$tax}} Amount">
                                        <input type="number" step="any" id="vat_amount" class="form-control"
                                            readonly>
                                    </td>
                                    <td data-label="Net Rate">
                                        <input type="number" step="any" id="net_rate" class="form-control"
                                            tabindex="5" readonly>
                                    </td>
                                    <td data-label="Total Amount">
                                        <input type="number" step="any" id="price" class="form-control"
                                            readonly>
                                        <input type="hidden" id="pricex" class="form-control" readonly>
                                    </td>
                                    <td style="display: none;" data-label="Total w/o Discount">
                                        <input type="number" step="any" id="price_wo_discount"
                                            class="form-control" readonly>
                                        <input type="hidden" step="any" id="total_wo_discount"
                                            class="form-control" readonly>
                                    </td>

                                    <td data-label=""><a href="#" class="btn btn-info addRow" style="background-color:#187f6a;" title="Add Row">+</a></td>
                                    <input type="hidden" id="product_id" class="form-control">
                                    <input type="hidden" id="product_name" class="form-control">
                                    <input type="hidden" id="remain" name="remain" class="form-control">
                                </tr>

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
                                        style="width: 70px;margin-top:-5px;">
                                        <option value="0">No</option>
                                        <option value="1">%</option>
                                        <option value="2">{{ $currency }}</option>
                                    </select>
                                </div>
                                <div id="discount_field_percentage" class="hidden group_dis">
                                    <div>
                                        <label for="discount_percentage">Discount in %</label>
                                        <input oninput="Barcodenotworkhere(this)" type="number" id="discount_percentage" name="discount_percentage"
                                            disabled>
                                        <span style="margin-right: 3px;">%</span>
                                    </div>
                                </div>
                                <div id="discount_field_amount" class="hidden group_dis">
                                    <label for="discount_amount">or</label>
                                    <input oninput="Barcodenotworkhere(this)" type="number" id="discount_amount" name="discount_amount" disabled>
                                    <span>{{ $currency }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3"
                            style="display: flex; flex-direction: column; align-items: flex-end;margin-bottom: 6px;">
                            <div class="input-group" style="display: flex; align-items: center; margin-bottom: 6px;">
                                <label for="bill_grand_total" class="mr-2" style="margin-right: 10px;">Grand
                                    Total:</label>
                                <input type="text" id="bill_grand_total" name="bill_grand_total"
                                    class="form-control-plaintext border-0"
                                    style="width: 70px; background: transparent; border: none;margin-top:-4px;"
                                    readonly>
                            </div>
                            <div class="input-group" style="display: flex; align-items: center;">
                                <label for="bill_grand_total_wo_discount" class="mr-2"
                                    style="margin-right: 10px;">Grand
                                    Total without Discount:</label>
                                <input type="number" id="bill_grand_total_wo_discount"
                                    name="bill_grand_total_wo_discount" class="form-control-plaintext border-0"
                                    style="width: 70px; background: transparent; border: none;margin-top:-4px;"
                                    readonly>
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

                       <div class="row" id="advance" style="display:none;">
                            <div class="col-sm-7"></div>
                            <div class="col-sm-5"
                                style="display: flex; flex-direction: column; align-items: flex-end;">
                                <div class="input-group form-inline"
                                    style="display: flex; align-items: center; margin-bottom: 6px;">
                                    <div class="form-group" style="display: flex; align-items: center; ">
                                        <label for="advance" class="mr-2" style="margin-right: 11px;">Advance
                                            Payment : </label>
                                        <br />
                                        <input oninput="Barcodenotworkhere(this)" type="number" name="advance" id="advanceInput" class="form-control"
                                            style="width: 100px;margin-right: 4.7rem;border-radius:5px;" />
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center; ">
                                        <label for="due_days" class="mr-2" style="margin-right: 12px;">Due Days:</label>&nbsp;
                                        <select class="form-control" name="due_days" id="due_days" style="width: 160px;">
                                            <option value="">Select Due Days</option>
                                            <option value="5">5 Days</option>
                                            <option value="10">10 Days</option>
                                            <option value="15">15 Days</option>
                                            <option value="20">20 Days</option>
                                            <option value="25">25 Days</option>
                                            <option value="30">30 Days</option>
                                            <option value="45">45 Days</option>
                                            <option value="60">60 Days</option>
                                            <option value="90">90 Days</option>
                                        </select>
                                                                    </div>
                            </div>
                        </div>
                        {{-- <input type="hidden" name="total_balance" id="total_balance"> --}}

                    </div>

                    <div class="row" align="right">
                        <div class="col-md-8">
                            <br/>
                            <input class="btn btn-primary" type="submit" value="submit" id="submitBtn">
                            <button type="button" class="btn btn-warning" id="saveDraftBtn" onclick="saveToDraft()">Save to Draft</button>

                        </div>

                    </div>
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
    <script src="{{ asset('javascript/billing.js') }}" defer></script>
</body>

</html>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> -->
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
        var form = document.getElementById("billForm");
        form.action = "{{ route('data.saveDraft') }}";
        if (validateForm()) {
            isModalConfirmed = true; // Confirm modal action
            form.submit();
        }
    }

    // Submit Button Logic
    document.getElementById("submitBtn").addEventListener("click", function(event) {
        event.preventDefault(); // Prevent default submission
        var form = document.getElementById("billForm");
        form.action = "{{ route('data.submitdata') }}"; // Set action for submission

        if (validateForm()) {
            isModalConfirmed = true; // Confirm modal action
            form.submit(); // Submit form if valid
        }
    });
</script>


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
    $(document).ready(function() {
        $("#payment_type").change(function() {
            var payment_type = $(this).val();
            var role = $("#user_credit_role").val();

            if (payment_type == 3 && role == 11) {
                $('#advance').show();
            } else {
                $('#advance').hide();
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        handleDiscount("#discount_type", "#discount");
        setFocus("#barcodenumber");
        handleVatSelection();
        // handleVatTypeChange();

        $('input[name="vat_type_mode"]').on("change, input", function() {

            var vat_type = $('input[name="vat_type_mode"]:checked').val();
            var selectval = $("#vat_type_value").val();
            var tax = @json($tax); // Ensure tax is converted properly for JavaScript

            handleVatTypeChange(vat_type,tax);
        });
        var tax = @json($tax);
        var branchid = @json($branchid);
        removeProductRow();
        generateCustomerID();
        handleBarcodeNumberKeyup(tax, branchid); // Pass tax and branchid to the external function
        getPrevSellingCost();
        preventFormSubmitOnEnter();
        TotalBillDiscount();
    });

    var addedProducts = [];

    function updateGrandTotalAmount() {
        var grandtotal = 0;

        $('.total-amount').each(function() {
            grandtotal += Number($(this).val());
        });

        grandtotal = grandtotal.toFixed(2);
        grandtotal = parseFloat(grandtotal);

        $('#bill_grand_total_wo_discount').val(grandtotal);

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
        $('#bill_grand_total').val(grandtotal);
    }

    $('#discount_percentage, #discount_amount').on('input', function() {
        updateGrandTotalAmount();
    });

    // Initial update
    updateGrandTotalAmount();


    $('.addRow').off().on('click', addRow);

function addRow() {
$('#buycostmessage').html('');
$('#stockavailable').html('');
var productId = $('#product_id').val();
var data = $('#barcodenumber').val();
var array = @json($items);

function isSeries(elm) {
    return elm.id == u;
}
// Check if barcode input is empty
if (data == "") {
    if ($("#product").val() == "") {
        return;
    }

    // If product is already added, increase quantity
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
        var existingRow = $("input[name^='quantity']").filter(function () {
            return $(this).attr("name").includes(productId);
        });

        if (existingRow.length > 0) {
            var currentQty = parseInt(existingRow.val()) || 0;

            // ✅ Fetch the remaining stock from the product list
            var productData = array.find(el => el.id == productId);
            if (!productData) {
                alert("Product data not found.");
                return;
            }

            var remainingStock = parseFloat(productData.remaining_stock) || 0;
            console.log("Remaining Stock:", remainingStock); // Debugging log

            // ✅ Check if stock is available before adding
            if (currentQty + 1 > remainingStock) {
                alert("Stock is not available for this product. Only " + remainingStock + " left.");
                return;
            }
            existingRow.val(currentQty + 1); // Increase quantity by 1
            console.log("Updated Quantity:", currentQty + 1);

            // Recalculate VAT
            var vatInput = $("input[name='vat_amount[" + productId + "]']");
            var originalVatPerUnit = parseFloat(vatInput.attr("value")) || 0;

            // Calculate new VAT amount
            var updatedVatAmount = ((currentQty + 1) * originalVatPerUnit).toFixed(3);
            vatInput.val(updatedVatAmount);

            var totalinput = $("input[name='total_amount[" + productId + "]']");
            var totalvalue = parseFloat(totalinput.attr("value")) || 0;
            var updatedtotalAmount = ((currentQty + 1) * totalvalue).toFixed(3);
            totalinput.val(updatedtotalAmount);


            var totalwoinput = $("input[name='total_amount_wo_discount[" + productId + "]']");
            var totalwovalue = parseFloat(totalwoinput.attr("value")) || 0;
            var updatedtotalwoAmount = ((currentQty + 1) * totalwovalue).toFixed(3);
            totalwoinput.val(updatedtotalwoAmount);

        } else {
            alert('Product is already added, but row not found.');
        }
                $('#barcodenumber').focus();
        $("#barcodenumber").val("");
        $("#barcodeproduct").val("");
        $("#qty").val("");
        $("#selectproduct").addClass('hide');
        $("#pselect").removeClass('hide');
        $("#product").val(null).trigger('change');
                updateGrandTotalAmount();

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
var pservice = ($("#pservice").val());
var pservicecost = ($("#pservicecost").val());
var wv = Number($("#buycost").val());
var buycost_rate = Number($("#buycost_rate").val());
var discount = Number($("#discount").val());
var quantityType = $('#quantity_type').val();
    var quantityInput = quantityType === 'box' ? $('#box_input').val() : $('#qty').val();
    var boxCount = $('#box_count').val() || 1;
    var actualQuantity = quantityType === 'box' ? (parseFloat(quantityInput) * parseFloat(boxCount)) : quantityInput;


discount = (discount != null) ? discount : 0;

var discount_type = $("#discount_type").val();

discount_type = (discount_type != null) ? discount_type : 0;

var price_dis = Number($("#price_wo_discount").val());
var total_disc = Number($("#total_wo_discount").val());
if (w < buycost_rate) {
alert('Rate cannot be less than Buy Cost.');
return;
}

if (($("#qty").val()) == "") {
    return;
}

if (!validateDiscount()) {
    return;
}
let isServiceMode = document.getElementById("switch").textContent === "Switch to Service"; // Check if Service Mode is active
if(!isServiceMode){

    if (!pservicecost) {
            alert("Service field is required.");
            return;
        }
        if (!pservice) {
            alert("Service field is required.");
            return;
        }
}

var u = Number($("#product_id").val());
 if (!u) {
            alert("This Product is not exist.");
            return;
        }
var vat_type = $('input[name="vat_type_mode"]:checked').val();

if (vat_type == 1) {
    var inclu_rate = Number($("#inclusive_rate").val());
} else if (vat_type == 2) {
    var ratediscount = Number($("#rate_discount").val());
}

// var totalamount = Math.round(totalamount * 1000) / 1000;

var tr = '<tr style="background-color: #f2f2f2;">' + '<td></td>' + '<td>' +
    '<input type="text" id="productnamevalue" value="' + y + '" name="productName[' + u +
    ']" class="form-control" readonly> <input type="hidden"  value="' + u + '" name="productId[' + u +
    ']" class="form-control">' +
    '</td>';

    var boxEnabled = $('#box_enabled').val();
    if (boxEnabled == 1) {
        tr += '<td class="quantity-type-cell">' + 
                '<input type="text" value="' + quantityType + '" name="quantityType[' + u + ']" class="form-control" readonly>' +
              '</td>';
        
        // Add box input cell (only if quantity type is box)
        if (quantityType === 'box') {
            tr += '<td class="box-input-cell">' + 
                    '<input type="text" value="' + quantityInput + '" name="boxInput[' + u + ']" class="form-control" readonly>' +
                    '<input type="hidden" value="' + boxCount + '" name="boxCount[' + u + ']" class="form-control">' +
                  '</td>';
        } else {
            tr += '<td class="box-input-cell" style="display:none;"></td>';
        }
    } else {
        tr += '<td class="quantity-type-cell" style="display:none;"></td>';
        tr += '<td class="box-input-cell" style="display:none;"></td>';
    }
tr +=  '<td id="barquan"><input type="text" value=' + x + ' id="quantityrow" name="quantity[' + u +
    ']" min="1" max="" class="form-control" required oninput="Barcodenotworkhere(this)"></td>' +
    '<td><input type="number" value=' + w + ' name="mrp[' + u +
    ']" class="form-control" readonly><input type="hidden" step="any" name="buy_cost[' + u + ']" value=' + wv +
    ' class="form-control">' +
    '<input type="hidden" step="any" name="buycost_rate[' + u + ']" value=' + buycost_rate +
    ' class="form-control"></td>'+
    '<td class="proservice_td"><input type="text" value=' + pservice + ' name="servicename[' + u + ']" class="form-control" readonly></td>' +
    '<td class="proservicecost_td"><input type="text" value=' + pservicecost + ' name="serviceprice[' + u + ']" class="form-control" readonly></td>' +

    '<td class="prounit_td"><input type="text" value=' + punit + ' name="prounit[' + u + ']" class="form-control" readonly></td>';

if (vat_type == 1) {
    tr += '<td><input type="number" value=' + inclu_rate + ' name="inclusive_rate_r[' + u +
        ']" class="form-control" readonly></td>';
} else if (vat_type == 2) {
    tr += '<td><input type="number" value=' + ratediscount + ' name="rate_discount_r[' + u +
        ']" class="form-control" readonly></td>';
}

tr += '<td class="prodiscount_td"><input oninput="Barcodenotworkhere(this)" type="text" value="' + discount + '" name="dis_count[' + u +
    ']" id="discountInput" class="form-control" min="0" ' + (discount_type === 'none' ? 'readonly' : '') + ' >';
tr += '<select name="dis_count_type[' + u + ']" class="form-control custom-select-no-padding">';
tr += '<option value="none" ' + (discount_type === 'none' ? 'selected' : '') + '>No</option>';
tr += '<option value="percentage" ' + (discount_type === 'percentage' ? 'selected' : '') + '>%</option>';
tr += '<option value="amount" ' + (discount_type === 'amount' ? 'selected' : '') + '>' +
    '{{ $currency }}' + '</option>';
tr += '</select></td>';

tr += '<td class="profixedvat_td"><input type="number" value=' + q + ' name="fixed_vat[' + u +
    ']" class="form-control" readonly></td>' +
    '<td class="provatamount_td"><input type="number" value=' + vat + ' id="vat_amt" name="vat_amount[' + u +
    ']" class="form-control" readonly></td>' +
    '<td><input type="number" value=' + netrate + ' name="net_rate[' + u +
    ']" class="form-control" readonly></td>' +
    '<td><input type="number" value=' + z + ' id="total_amount" name="total_amount[' + u +
    ']" class="form-control total-amount" readonly>' +
    '<input type="hidden" value=' + p + ' id="rowprice" name="price[' + u +
    ']" class="form-control" readonly></td>' +
    '<td style="display: none;" class="prowithoutdis_td hide"><input type="number" value=' + price_dis +
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
$("#box_input").val(nu);

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
$("#pservice").val(nu);
$("#pservicecost").val(nu);


$("#switch").prop("disabled", true);

/*--------------EDIT ADDED ROW'S QUANTITY AND BASED ON CHANGE VAT AMOUNT  & TOTAL AMOUNT----------------*/

// Fetch the branch ID
var branchid = Number(document.getElementById("branchid").value);

// Only proceed if branchid is 1
if (branchid != 0) {
    var array = @json($items);

function isSeries(elm) {
    return elm.id == u;
}
var remstock = array.find(isSeries).remaining_stock;
var remstk = Number(remstock);

$('input[name="quantity[' + u + ']"]').attr("max", remstk);
}
var page = $('#page').val();

addRowDiscountCalculation(u, remstk, $('input[name="vat_type_mode"]:checked').val(), 0, page);

/*-------------------------------------------------------------------------------------------------*/
// After adding a new row
updateGrandTotalAmount();
}

    function doSomething(x) {

        var vat_type_selected = $('input[name="vat_type_mode"]:checked').val();
        var tax = @json($tax); // Ensure tax is converted properly for JavaScript
        var branchid = @json($branchid); // Ensure tax is converted properly for JavaScript

        if (vat_type_selected == null) {
            // $('#vatModeAlert').text('Please select VAT mode first.');
            alert('Please select {{$tax}} type first.');
            $("#product").val(null).trigger('change');
            return;
        }

        var array = @json($items);

        function isSeries(elm) {
            return elm.id == x;
        }

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
        var box_count = array.find(isSeries).box_count;
        $('#box_count').val(box_count);
        var box_enabled = array.find(isSeries).box_enabled;
        $('#box_enabled').val(box_enabled);

$('#buycostmessage').html(
    'Buy Cost Rate of ' + w + ' : MRP <span style="font-size: 1.1em; font-weight: 500;">' + buycost_rate_kv + '</span>'
);

$('#stockavailable').html(
    'Stock availability of ' + w + ' : <span style="font-size: 1.1em; font-weight: 500;">' + rem + '</span>'
);

if (vat_type_selected == 1) {
    // Ensure recalculation when pservicecost changes
    $("#pservicecost").on("input", function () {
        calculateInclusiveRate(); // Call function to update InclusiveRate dynamically
    });

    function calculateInclusiveRate() {
        var pservicecost = Number($("#pservicecost").val()) || 0; // Get latest service cost
        var k = Number($("#mrp").val()) || 0; // Get MRP
        var t = Number($("#fixed_vat").val()) || 0; // Get VAT
        var InclusiveRate = (k + pservicecost) / (1 + (t / 100));
        InclusiveRate = Math.round(InclusiveRate * 1000) / 1000;
        $("#inclusive_rate").val(InclusiveRate);
    }

    // Call the function initially to set the correct value
    calculateInclusiveRate();

}


         else if (vat_type_selected == 2) {
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
        // checkquantity();
        if (vat_type_selected == 1) {
            discount_calcu_inclu();

        } else if (vat_type_selected == 2) {
            discount_calcu_exclus();

        }
        checkquantity();
        // handleVatTypeChange(tax);
        handleBarcodeNumberKeyup(tax,branchid);

        /*-------------CHECK STOCK WHEN ENTERING QUANTITY-----------------------*/
        if(branchid!=0){

        var remaining_stock = parseFloat(rem);
        $("#qty").attr("max", remaining_stock);

        $(document).ready(function() {
            $('#qty').on('input', function() {

                var product = parseFloat($('#qty').val());

                if (product > remaining_stock) {
                    $('.addRow').off();
                } else {
                    $('.addRow').off().on('click', addRow);
                }
            });
        });
    }

        /*-------------------------------------------------------------------------*/
    }

    function checkquantity() {
        var branchid = parseInt(document.getElementById("branchid").value);
        if (branchid != 0) {
        p_id = $('#product_id').val();

        var array = @json($items);

        function isSeries(elm) {
            return elm.id == p_id;
        }
        var p_name = array.find(isSeries).product_name;
        var rem = array.find(isSeries).remaining_stock;
        var remaining_stock = parseFloat(rem);
        var myField = document.getElementById("qty");
        var inputQuantity = parseFloat(myField.value);

        if (inputQuantity > remaining_stock) {
            alert("Remaining stock of " + p_name + " left only :" + remaining_stock);
         // Disable the "Add Row" button if quantity exceeds stock
         $('.addRow').off();
    } else {
        // Enable the "Add Row" button
        $('.addRow').off().on('click', addRow);
    }
}
    }

    $(document).ready(function() {
        $('.js-user').select2({
            theme: "classic"
        });
        $('.product-list').select2({
            theme: "classic"
        });
    });


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

    // Validate Account Selection
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

    // Get and parse limit status balance

    var advanceInput = parseFloat(document.getElementById('advanceInput').value) || 0;
    var currentBalance = document.getElementById('current_lamount').value;
    var billAmount = parseFloat(document.getElementById('bill_grand_total').value) || 0;
        var totaladvBalance = parseFloat(document.getElementById('advance_balance').value) || 0;
        var totaladvBalanceflag = parseInt(document.getElementById('advance_balance_flag').value) || 0;
        var customernames = document.getElementById('customer').value;

        currentBalance = (currentBalance === '' || currentBalance === null) ? NaN : parseFloat(currentBalance);


        if (!isNaN(currentBalance)) {
    var totalBalance = currentBalance + advanceInput + totaladvBalance;
    console.log(totalBalance);
} else {
    console.log('currentBalance is null or empty');
}

        if (payment_type == 3 && totaladvBalanceflag === 1 && totalBalance < billAmount) {
            alert(customernames + ' has only ' + totaladvBalance + ' MRP remaining in due.');

        }
        // Now check if totalBalance is less than the bill amount
        if (payment_type == 3 && totalBalance < billAmount) {
                alert('Credit limit reached '+customernames + ' has only ' + currentBalance+ ' MRP remaining in credit limit.');

                // Re-enable the submit button after alert
                submitBtn.disabled = false;
                submitBtn.innerText = "Submit";

                return false; // Validation failed; prevent form submission
            }




    // Form is valid; continue with submission
    return true;
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
    var discountType = document.getElementById("discount_type");

    function addRowIfValid() {
        if (validateDiscount()) {
            $('.addRow').click();
        }
    }

    product_name_name.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            // $('.addRow').click();
            addRowIfValid();
        }
    });

    qty.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            // $('.addRow').click();
            addRowIfValid();
        }
    });

    mrp.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            // $('.addRow').click();
            addRowIfValid();
        }
    });

    fixed_vat.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            // $('.addRow').click();
            addRowIfValid();
        }
    });

    vat_amount.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            // $('.addRow').click();
            addRowIfValid();
        }
    });

    net_rate.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            // $('.addRow').click();
            addRowIfValid();
        }
    });

    price.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            // $('.addRow').click();
            addRowIfValid();
        }
    });
    discount.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            // $('.addRow').click();

            addRowIfValid();
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
    document.addEventListener('DOMContentLoaded', function() {
    const vatTypeRadios = document.querySelectorAll('input[name="vat_type_mode"]');
    const barcodeInput = document.getElementById('barcodenumber');

    vatTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                barcodeInput.focus();
            }
        });
    });
});



</script>

<script>
    let lastInputTime = 0;
    let alertShown = false; // Flag to prevent multiple alerts

    function Barcodenotworkhere(input) {
        const currentTime = new Date().getTime();
        const timeDifference = currentTime - lastInputTime;

        if (timeDifference < 50 && !alertShown) {
            alertShown = true; // Set the flag to true
            alert('Barcode not work here'); // Show alert
            input.value = ''; // Clear the input field
            alertShown = false; // Reset the flag after the alert is closed
        }

        lastInputTime = currentTime; // Update the time of the last input
    }
</script>

<script>
//     function getCreditId(selectedUserId) {
//     var selectedOption = document.querySelector(`#user_id option[value="${selectedUserId}"]`);
//     var lAmount = selectedOption.getAttribute('data-current_lamount');
//     var balance = selectedOption.getAttribute('data-balance');
//     var customername = selectedOption.getAttribute('data-name');



//     document.getElementById('credit_id').value = selectedUserId;
//     document.getElementById('current_lamount').value = lAmount;
//     document.getElementById('advance_balance').value = balance;
//     document.getElementById('customer').value = customername;



//     updateTotalBalance();
//     let bankDropdown = document.getElementById("bank_name");
//     bankDropdown.selectedIndex = 0; // Reset to the first option
//     bankDropdown.style.display = 'block'; // Show the dropdown
// }



function updateTotalBalance() {
    const currentAmount = parseFloat(document.getElementById('current_lamount').value) || 0;
    const advanceBalance = parseFloat(document.getElementById('advance_balance').value) || 0;
    const advanceAmount = parseFloat(document.getElementById('advanceInput').value) || 0;

    // advanceBalance += advanceAmount;
    // document.getElementById('advance_balance').value = advanceBalance;

    const advanceBalanceFlag = advanceBalance > 0 ? 1 : 0;
    document.getElementById('advance_balance_flag').value = advanceBalanceFlag;
}


    function checkGrandTotal() {
        var payment_type = $("#payment_type").val();
        var currentBalance = parseFloat(document.getElementById('current_lamount').value) || 0;
        var billAmount = parseFloat(document.getElementById('bill_grand_total').value) || 0;

        if (payment_type == 3) {
        if (totalBalance < billAmount) {
            return false;
        }
    }

        return true;
    }

    $(document).ready(function() {
        $("form").on('submit', function(e) {
            if (!checkGrandTotal()) {
                e.preventDefault();
            }
        });

        updateTotalBalance();
    });

    document.getElementById('current_lamount').addEventListener('input', updateTotalBalance);
    document.getElementById('advanceInput').addEventListener('input', updateTotalBalance);
    document.getElementById('advance_balance').addEventListener('input', updateTotalBalance);

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
    // Variable to store the previous advance input value
    let previousAdvanceValue = 0;

    function updateAdvanceBalance() {
        let advanceInputValue = parseFloat(document.getElementById('advanceInput').value) || 0;
        let advanceBalanceValue = parseFloat(document.getElementById('advance_balance').value) || 0;
        let newBalance = advanceBalanceValue + (advanceInputValue - previousAdvanceValue);
        document.getElementById('advance_balance').value = newBalance;
        previousAdvanceValue = advanceInputValue;
    }

    document.getElementById('advanceInput').addEventListener('input', updateAdvanceBalance);
</script>
<script>
    // Function to toggle the input field based on the selected discount type
function toggleDiscountInput() {
    var discountType = document.getElementById("discount_type").value;
    var discountInput = document.getElementById("discount");

    if (discountType === "none") {
        // Hide or disable the input if 'none' is selected
        discountInput.style.display = 'none';
        discountInput.disabled = true;
        discountInput.value = '';  // Clear the input value
    } else {
        // Show or enable the input if 'percentage' or 'amount' is selected
        discountInput.style.display = 'block';
        discountInput.disabled = false;
    }
}

// Attach the event listener to the discount_type dropdown
document.getElementById("discount_type").addEventListener("change", toggleDiscountInput);

// Initial call to hide the input if 'none' is pre-selected
toggleDiscountInput();

</script>
<script>
// customer fetching
// Fetch and display credit note amount when typing in the customer ID input
// Function to fetch and display customer details based on input
document.getElementById('cust_id').addEventListener('input', function() {
    const inputValue = this.value;

    // Clear the displayed credit note amount when the input changes
    const creditNoteDisplay = document.getElementById('creditNoteDisplay');
    creditNoteDisplay.textContent = ''; // Clear the previous credit note amount
    document.getElementById('creditNoteAmount').value = ''; // Clear the readonly input field

    // Fetch customer names from the server
    if (inputValue.length > 0) {
        fetch(`/fetch-customers?name=${inputValue}`)
            .then(response => response.json())
            .then(data => {
                const dropdown = document.getElementById('customerDropdown');
                dropdown.innerHTML = ''; // Clear previous results

                if (data.length > 0) {
                    dropdown.style.display = 'block'; // Show dropdown
                    data.forEach(customer => {
                        const div = document.createElement('div');
                        // Display customer name with amount in the dropdown
                        div.textContent = `${customer.name} (${customer.credit_note_amount})`;
                        div.className = 'dropdown-item';
                        div.onclick = function() {
                            // Set input value to only the customer name
                            document.getElementById('cust_id').value = customer.name;
                            dropdown.style.display = 'none'; // Hide dropdown
                            displayCreditNoteAmount(customer); // Display credit note amount
                        };
                        dropdown.appendChild(div);
                    });
                } else {
                    dropdown.style.display = 'none'; // Hide dropdown if no matches
                }
            });
    } else {
        document.getElementById('customerDropdown').style.display = 'none'; // Hide dropdown if input is empty
    }
});

// Additional event for handling direct input case (when user types and doesn't select)
document.getElementById('cust_id').addEventListener('blur', function() {
    const inputValue = this.value;

    // Check if the typed name matches a customer without selecting from dropdown
    if (inputValue.length > 0) {
        fetch(`/fetch-customers?name=${inputValue}`)
            .then(response => response.json())
            .then(data => {
                const customer = data.find(c => c.name.toLowerCase() === inputValue.toLowerCase());
                if (customer) {
                    displayCreditNoteAmount(customer); // Display credit note amount
                }
            });
    }
});

// Function to display credit note amount
function displayCreditNoteAmount(customer) {
    const creditNoteDisplay = document.getElementById('creditNoteDisplay');
    const creditNoteAmountInput = document.getElementById('creditNoteAmount');

    // Display the message
    creditNoteDisplay.textContent = `${customer.name} has a credit note amount of MRP ${customer.credit_note_amount}`;

    // Set the value of the readonly input field
    creditNoteAmountInput.value = customer.credit_note_amount;
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.form-group')) {
        document.getElementById('customerDropdown').style.display = 'none';
    }
});

// Function to handle user selection from dropdown
function getCreditId(selectedUserId) {
    var selectedOption = document.querySelector(`#user_id option[value="${selectedUserId}"]`);
    var lAmount = selectedOption.getAttribute('data-current_lamount');
    var balance = selectedOption.getAttribute('data-balance');
    var customername = selectedOption.getAttribute('data-name');

    // Update hidden fields
    document.getElementById('credit_id').value = selectedUserId;
    document.getElementById('current_lamount').value = lAmount;
    document.getElementById('advance_balance').value = balance;
    document.getElementById('customer').value = customername;

    // Update credit note display based on selection
    fetch(`/fetch-customers?name=${customername}`)
        .then(response => response.json())
        .then(data => {
            const customer = data[0]; // Assuming data returns an array with the customer
            if (customer) {
                displayCreditNoteAmount(customer); // Display credit note amount
            } else {
                // Clear display if no customer found
                document.getElementById('creditNoteDisplay').textContent = '';
                document.getElementById('creditNoteAmount').value = '';
            }
        });

    updateTotalBalance();
    let bankDropdown = document.getElementById("bank_name");
    bankDropdown.selectedIndex = 0; // Reset to the first option
    bankDropdown.style.display = 'block'; // Show the dropdown
}


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
    document.addEventListener("DOMContentLoaded", function () {
       let isServiceMode = false; // Tracks whether Service Mode is active

       const observer = new MutationObserver(() => {
           if (isServiceMode) {
               document.querySelectorAll('td.prounit_td, td.profixedvat_td,td.prodiscount_td, td.provatamount_td, td.prowithoutdis_td').forEach(td => {
                   console.log("MutationObserver: Hiding Billing Fields", td);
                   td.style.display = "none";
                   td.querySelectorAll("input, select").forEach(input => input.style.display = "none");
               });
               document.querySelectorAll('td.proservice_td, td.proservicecost_td').forEach(td => {
                   console.log("MutationObserver: showing serv Fields", td);
                   td.style.display = "table-cell";
                   td.querySelectorAll("input, select").forEach(input => input.style.display = "block");
               });
           } else {
               document.querySelectorAll('td.prounit_td, td.profixedvat_td,prodiscount_td, td.provatamount_td, td.prowithoutdis_td').forEach(td => {
                   console.log("MutationObserver: Showing Billing Fields", td);
                   td.style.display = "table-cell";
                   td.querySelectorAll("input, select").forEach(input => input.style.display = "block");
               });
               document.querySelectorAll('td.proservice_td, td.proservicecost_td').forEach(td => {
                   console.log("MutationObserver: showing serv Fields", td);
                   td.style.display = "none";
                   td.querySelectorAll("input, select").forEach(input => input.style.display = "none");
               });
           }
       });

       observer.observe(document.body, { childList: true, subtree: true });

       document.getElementById("switch").addEventListener("click", function () {
           document.activeElement.blur(); // Remove focus before switching

           let button = this;
           let tax = @json($tax); // Convert Laravel variable to JavaScript safely
           isServiceMode = !isServiceMode; // Toggle state

           if (isServiceMode) {
               console.log("Switching to Service Mode");

               // Hide billing fields
               document.getElementById("unithide").style.display = "none";
               document.getElementById("vat_perc").style.display = "none";
               document.getElementById("vat_ammi").style.display = "none";
               document.getElementById("totalhide").style.display = "none";
               document.getElementById("discounthide").style.display = "none";

               document.querySelectorAll('td[data-label="Unit"],td[data-label="Discount"], td[data-label="Total w/o Discount"], td[data-label="' + tax + '(%)"], td[data-label="Total ' + tax + ' Amount"]').forEach(td => {
                   td.style.display = "none";
               });

               // Show service fields
               // document.querySelectorAll('td.proservice_td, td.proservicecost_td').forEach(td => {
               //     td.style.display = "table-cell";
               //     td.querySelectorAll("input, select").forEach(input => input.style.display = "block");
               // });

               document.getElementById("service").style.display = "table-cell";
               document.getElementById("servicecost").style.display = "table-cell";

               document.querySelectorAll('td[data-label="Service"], td[data-label="Servicecost"]').forEach(td => {
                   td.style.display = "table-cell";
               });
               document.getElementById("saveDraftBtn").style.display = "none";

               button.textContent = "Switch to Billing";
           } else {
               console.log("Switching to Billing Mode");

               // Show billing fields
               document.getElementById("unithide").style.display = "table-cell";
               document.getElementById("vat_perc").style.display = "table-cell";
               document.getElementById("vat_ammi").style.display = "table-cell";
               document.getElementById("totalhide").style.display = "table-cell";
               document.getElementById("discounthide").style.display = "table-cell";

               document.querySelectorAll('td[data-label="Unit"],td[data-label="Discount"], td[data-label="Total w/o Discount"], td[data-label="' + tax + '(%)"], td[data-label="Total ' + tax + ' Amount"]').forEach(td => {
                   td.style.display = "table-cell";
               });

               // Hide service fields
               // document.querySelectorAll('td.proservice_td, td.proservicecost_td').forEach(td => {
               //     td.style.display = "none";
               //     td.querySelectorAll("input, select").forEach(input => input.style.display = "none");
               // });

               document.getElementById("service").style.display = "none";
               document.getElementById("servicecost").style.display = "none";

               document.querySelectorAll('td[data-label="Service"], td[data-label="Servicecost"]').forEach(td => {
                   td.style.display = "none";
               });
               document.getElementById("saveDraftBtn").style.display = "inline-block";

               button.textContent = "Switch to Service";

               // Focus barcode input after switching
               setTimeout(() => {
                   document.getElementById("barcodenumber").focus();
               }, 100);
           }

           observer.takeRecords(); // Ensure observer updates immediately
       });
   });

   </script>
   <script>
   $(document).ready(function () {
       console.log("Script Loaded");

       function handleDiscountCalculation() {
           var vat_type_selected = $('input[name="vat_type_mode"]:checked').val();
           console.log("VAT Type Selected:", vat_type_selected);

           if (vat_type_selected == 1) {
               console.log("Calling discount_calcu_inclu()");
               discount_calcu_inclu();
           } else if (vat_type_selected == 2) {
               console.log("Calling discount_calcu_exclus()");
               discount_calcu_exclus();
           }
       }

       function callVatFunction() {
           var vat_type = $('input[name="vat_type_mode"]:checked').val();
           var tax = $("#vat_type_value").val();

           console.log("VAT Type Selected:", vat_type);
           console.log("Tax Value Before Processing:", tax);

           if (!tax || isNaN(tax)) {
               tax = 0;
           }

           console.log("Tax Value After Processing:", tax);

           if (typeof handleVatTypeChange === "function") {
               console.log("Calling handleVatTypeChange()");
               handleVatTypeChange(vat_type, tax);
           } else {
               console.error("handleVatTypeChange function NOT FOUND!");
               waitForVatFunction(); // Try to load it if it's not found
           }
       }

       function waitForVatFunction() {
           if (typeof handleVatTypeChange === "function") {
               console.log("handleVatTypeChange is now available.");
               handleVatTypeChange($('input[name="vat_type_mode"]:checked').val(), $("#vat_type_value").val());
           } else {
               console.log("Waiting for handleVatTypeChange...");
               setTimeout(waitForVatFunction, 500); // Check again in 500ms
           }
       }

       $(document).on("change input", 'input[name="vat_type_mode"]', function () {
           handleDiscountCalculation();
           callVatFunction();
       });

       $("#pservicecost, #qty, #mrp, #discount, #discount_type").on("input change", function () {
           handleDiscountCalculation();
       });

       // Initial check for handleVatTypeChange
       waitForVatFunction();
   });



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
<script>
// When product is selected
document.getElementById('product').addEventListener('input', function() {
    const boxCount = document.getElementById('box_enabled').value;
    const hasBoxCount = boxCount && boxCount !== "" && boxCount !== "0";
    
    // Show/hide fields based on box_count
    document.getElementById('typeHeader').style.display = hasBoxCount ? '' : 'none';
    document.getElementById('typeCell').style.display = hasBoxCount ? '' : 'none';
    document.getElementById('boxHeader').style.display = 'none';
    document.getElementById('boxCell').style.display = 'none';
    
    // Reset to quantity by default
    if (hasBoxCount) {
        document.getElementById('quantity_type').value = 'quantity';
    }
});

// When quantity type changes
document.getElementById('quantity_type').addEventListener('change', function() {
    const isBox = this.value === 'box';
    
    document.getElementById('boxHeader').style.display = isBox ? '' : 'none';
    document.getElementById('boxCell').style.display = isBox ? '' : 'none';
    
    if (isBox) {
        document.getElementById('box_input').focus();
    } else {
        document.getElementById('box_input').value = '';
    }
});

// When box input changes
document.getElementById('box_input').addEventListener('input', function() {
    const boxValue = parseFloat(this.value) || 0;
    const boxCount = parseFloat(document.getElementById('box_count').value) || 1;
    
    // Update quantity field
    const qtyInput = document.getElementById('qty');
    qtyInput.value = boxValue * boxCount;
    
    // Create and dispatch input event to trigger calculations
    const event = new Event('input', {
        bubbles: true,
        cancelable: true,
    });
    qtyInput.dispatchEvent(event);
});

// Existing quantity input handler
document.getElementById('qty').addEventListener('input', function() {
    if (typeof checkquantity === 'function') {
        checkquantity();
    }
});
</script>