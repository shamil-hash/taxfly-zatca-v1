<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Plexpay billing">
    @php
        switch ($page) {
            case 'edit_bill':
                $title = 'Edit Transaction';
                break;
            case 'sales_order':
            case 'quotation':
                $title = 'Convert To Billing';
                break;
            case 'bill_draft':
                $title = 'Draft To Billing';
                break;
            case 'editsalesorder':
                $title = 'Sales Order Edit';
                break;
            case 'salesorderdraft':
            case 'quot_to_salesorder':
                $title = 'To Sales Order';
                break;
            case 'quotationdraft':
            case 'clone_quotation':
                $title = 'To Quotation';
                break;
            case 'performadraft':
                $title = 'To Proforma Invoice';
                break;
            case 'deliverydraft':
            case 'to_delivery':
                $title = 'To Delivery Note';
                break;
            case 'clone_bill':
                $title = 'To Billing';
                break;
            default:
                $title = 'Plexpay Billing'; // Default title if no match is found
                break;
        }
    @endphp
    <title>{{ $title }}</title>

    @if (Session('adminuser'))
        @include('layouts/adminsidebar')
    @elseif(Session('softwareuser'))
        @include('layouts/usersidebar')
    @endif
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

/* Simplified Modal */


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
    <style>
         .btn-primary{
            background-color: #187f6a;
            color: white;
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
    @if (Session('softwareuser'))

        <!--    <div align="right">-->
        <!--    @include('layouts.quick')-->
        <!--    <a href="" class="btn btn-info">Refresh</a>-->
        <!--</div>-->
        <x-admindetails_user :shopdatas="$shopdatas" />
        @endif

        @php
            $url = '';
            $credit_id = '';
            $text = '';
            $modaltext = '';
            switch ($page) {
                case 'edit_bill':
                    $url = "/edittransactiondetails/{$page}/editsubmitdata";
                    $credit_id = 'credit_user_id';
                    $text = 'EDIT BILL';
                    break;
                case 'sales_order':
                case 'quotation':
                case 'bill_draft':
                case 'clone_bill':
                    $url = "/to_invoice/{$page}/submitdata";
                    $credit_id = 'credit_id';
                    $text = 'TO BILL';
                    $modaltext = 'Invoice';
                    break;
                case 'editsalesorder':
                    $url = "/edited/{$transaction_id}";
                    $credit_id = 'credit_user_id';
                    $text = 'EDIT SALES ORDER';
                    break;
                case 'salesorderdraft':
                case 'quot_to_salesorder':
                    $url = "/submitsalesdraft/{$transaction_id}";
                    $credit_id = 'credit_id';
                    $text = 'TO SALES ORDER';
                    $modaltext = 'Sales Order';
                    break;
                case 'quotationdraft':
                case 'clone_quotation':
                    $url = "/submitquotationdraft/{$transaction_id}";
                    $credit_id = 'credit_id';
                    $text = 'TO QUOTATION';
                    $modaltext = 'Quotation';
                    break;
                case 'performadraft':
                    $url = "/submitperformadraft/{$transaction_id}";
                    $credit_id = 'credit_id';
                    $text = 'TO PROFORMA INVOICE';
                    $modaltext = 'Proforma Invoice';
                    break;
                case 'deliverydraft':
                case 'to_delivery':
                    $url = "/submitdeliverydraft/{$transaction_id}";
                    $credit_id = 'credit_id';
                    $text = 'TO DELIVERY NOTE';
                    $modaltext = 'Delivery Note';
                    break;
            }
        @endphp

        <form method="post" action="{{ $url }}" onsubmit="return validateForm();" id="billForm">
            @csrf

                        @if ($page == 'edit_bill'||$page == 'clone_bill'||$page == 'bill_draft'||$page == 'sales_order'||$page == 'quotation')

            <input type="hidden" name="branchid" id="branchid" value="{{$branch}}">
            @endif

            <input type="hidden" name="edit_comment" id="edit_comment">
            <div class="form group row">
            <div style="background-color: #f8f9fa; padding: 8px; border: 1px solid #dee2e6;border-radius: 8px;">
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">Customer ID</span>
                            <x-Form.input type="text" style="width: 150px;" id="cust_id" name="customer_name"
                                class="customer_id" placeholder="Customer ID:" value="{{ $customer_name }}"
                                aria-label="Customer Name" />

                            <input type="hidden" name="page" id="page" value="{{ $page }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">TRN Number</span>
                            <x-Form.input type="text" style="width: 150px;" id="trn_number" name="trn_number"
                                class="trn_no" placeholder="TRN Number:" value="{{ $trn_number }}"
                                aria-label="TRN Number" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">Phone Number</span>
                            <x-Form.input type="text" style="width: 150px;" id="phone" name="phone"
                                class="phone" placeholder="Phone:" value="{{ $phone }}"
                                aria-label="Phone Number" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon" style="width: 90px;" id="EmailField">Email</span>
                            <x-Form.input type="text" style="width: 170px;" id="email" name="email"
                                class="email" value="{{ $email }}" aria-labelledby="EmailField" />
                        </div>
                    </div>
                </div>
                <br /> <br />
                <div class="row">
                    @if (!($page == 'clone_bill' || $page == 'clone_quotation'))
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-addon" id="TransactionID">Transaction ID</span>
                                <x-Form.input type="text" style="width: 138px;" id="transaction_id"
                                    value="{{ $transaction_id }}" name="transaction_id" aria-labelledby="TransactionID"
                                    readonly />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="input-group">

                                @php
                                    $type = '';
                                    if ($payment_type == 1) {
                                        $type = 'CASH';
                                    } elseif ($payment_type == 2) {
                                        $type = 'BANK';
                                    } elseif ($payment_type == 3) {
                                        $type = 'CREDIT';
                                    } elseif ($payment_type == 4) {
                                        $type = 'POS CARD';
                                    }
                                @endphp
                                @if (($payment_type == 3 || $payment_type == 2) || $page != 'edit_bill')
                                <span class="input-group-addon" id="PaymentMode" style="width: 90px;">Payment
                                    Mode</span>
                                    <x-Form.input type="text" style="width: 130px;" id="payment_mode" name="payment_mode"
                                    value="{{ $type }}" aria-labelledby="PaymentMode" readonly />
                                    @elseif(($payment_type == 1 || $payment_type == 4 ) && $page == 'edit_bill')
                                    <span class="input-group-addon" id="PaymentMode" style="width: 90px;">Payment Mode</span>
                                    <select class="form-control" style="width: 130px;" id="payment_mode" name="payment_mode" aria-labelledby="PaymentMode" onchange="updatePaymentType()">
                                        <option value="" disabled selected>Select Payment Mode</option>
                                        <option value="1" {{ $payment_type == 1 ? 'selected' : '' }}>CASH</option>
                                        @if ($cash_user_id != null)
                                        <option value="3" {{ $payment_type == 3 ? 'selected' : '' }}>CREDIT</option>
                                        @endif
                                        <option value="4" {{ $payment_type == 4 ? 'selected' : '' }}>POST CARD</option>
                                    </select>
                                    <input type="hidden" id="hidden_payment_mode" name="hidden_payment_mode" value="{{ $payment_type }}">

                                    @endif

                                   @if ($account_name!=null)
                                <x-Form.input type="text" style="width: 130px;" id="account_name" name="account_name"
                                value="{{ $account_name }}" readonly />
                                @endif

                                <x-Form.input type="hidden" style="width: 130px;" id="payment_type"
                                    name="payment_type" value="{{ $payment_type }}" readonly />

                                    <input type="hidden" name="bank_name" id="" value="{{$bank_id}}">
                                    <!--<input type="hidden" name="account_name" value="{{$account_name}}">-->
                                    @if (
                                        $page == 'edit_bill' ||
                                        $page == 'sales_order' ||
                                            $page == 'bill_draft' ||
                                            $page == 'quotation'
                                            )
                                    <input type="hidden" name="current_balance" id="current_balance" value="{{$current_balance}}">

                                    @endif

                                    @if ( $page == 'sales_order' || $page == 'bill_draft' ||$page == 'edit_bill'|| $page == 'quotation')
                                @if (!is_null($current_lamount))
                                    <!--<label for="">due balance</label>-->
                                    <input type="hidden" name="advance_balance" id="advance_balance" value="{{ $advance_balance ? $advance_balance->advance_balance : '' }}">
                                    <!--<label for="">credit limit</label>-->
                                    <input type="hidden" name="current_lamount" id="current_lamount" value="{{$current_lamount}}">

                                    <input type="hidden" name="advance_balance_flag" id="advance_balance_flag" value="0">

                                    @endif
                                    @if ($page == 'edit_bill')
                                    <input type="hidden" name="prev_grand_total" id="prev_grand_total" value="{{$prev_grand_total}}">
                                    @endif
                                @endif

                                @if ($payment_type == 3)
                                    <x-Form.input type="hidden" style="width: 130px;" id="{{ $credit_id }}"
                                        name="{{ $credit_id }}" value="{{ $credit_user_id }}" readonly />
                                        @elseif ($payment_type == 1 || $payment_type == 2 || $payment_type == 4 || (isset(request()->payment_mode) && request()->payment_mode == $payment_type))
                                    <x-Form.input type="hidden" style="width: 130px;" id="{{ $credit_id }}"
                                        name="{{ $credit_id }}" value="{{ $cash_user_id }}" readonly />
                                @endif

                            </div>
                        </div>
                        @if (
                            $page == 'edit_bill' ||
                                $page == 'sales_order' ||
                                $page == 'quotation' ||
                                $page == 'bill_draft' ||
                                $page == 'salesorderdraft' ||
                                $page == 'quotationdraft' ||
                                $page == 'performadraft' ||
                                $page == 'quot_to_salesorder')
                            <div class="col-md-3">
                                <div class="input-group">
                                    @if ($payment_type == 3)
                                        <span class="input-group-addon" id="CreditUserName"
                                            style="width: 90px;">Customer
                                            </span>
                                        <x-Form.input type="text" style="width: 175px;" id="user_id"
                                            name="user_id" value="{{ $credit_user_name }}"
                                            aria-labelledby="CreditUserName" readonly />
                                    @endif
                                </div>

                            </div>
                            @if ($employee_name!==null)
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon" id=""
                                    style="width: 90px;">Employee
                                </span>
                                <x-Form.input type="text" style="width: 175px;" id="employee_name"
                                name="employee_name" value="{{ $employee_name }}"
                                aria-labelledby="" readonly />
                                <input type="hidden" name="employee_id" id="employee_id" value="{{$employee_id}}">

                            </div>
                            @endif
                        </div>
                        @endif
                    @elseif ($page == 'clone_bill' || $page == 'clone_quotation')
                        <!-- CREDIT-->
                        <div class="col-md-3">
                            <div class="input-group">

                            <br />
                            <select class="js-user credituser" onclick="creditUser(this.value)"
                                onchange="getCreditId(this.value)" id="user_id" name="user_id"
                                style="width:260px;">
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
                            <!--<label for="">due_balance</label>-->
                            <input type="hidden" name="advance_balance" id="advance_balance"><br>
                            <input type="hidden" name="customer" id="customer">
                            <!--<label for="">credit limit</label>-->
                            <input type="hidden" name="current_lamount" id="current_lamount">
                            <input type="hidden" name="advance_balance_flag" id="advance_balance_flag" value="0">
                            <input type="hidden" name="credit_id" id="credit_id">
                            <br /><br />
                        </div></div>
                        <div class="input-group">

                        <div class="col-md-3">
                            <br>

                            <select class="js-user credituser" id="employee_id" name="employee_id" style="width:260px;" onchange="updateEmployeeName()">
                                <option value="">SELECT EMPLOYEE</option>
                                @foreach ($listemployee as $employee)
                                    <option value="{{ $employee->id }}" data-name="{{ $employee->first_name }}">{{ $employee->first_name }}</option>
                                @endforeach
                            </select>

                            <!-- Hidden input for employee_name -->
                            <input type="hidden" name="employee_name" id="employee_name">
                            <br /><br />
                        </div></div>
                        <!-- CREDIT-->
                    @endif

                    @if ($page == 'deliverydraft')
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-addon" id="Location">Location</span>
                                <x-Form.input type="text" style="width: 193px;" id="location"
                                    value="{{ $location_delivery }}" name="location" aria-label="Location" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-addon" id="FlatNo">Flat No</span>
                                <x-Form.input type="text" style="width: 190px;" id="flat_no"
                                    value="{{ $flat_no }}" name="flat_no" aria-label="Flat No" />
                            </div>
                        </div>
                        <div class="row" style="padding-left: 1.5rem;">
                            <div class="col-md-3">
                                <br><br>
                                <div class="input-group">
                                    <span class="input-group-addon" id="Area">Area</span>
                                    <x-Form.input type="text" style="width: 203px;" id="area"
                                        value="{{ $area }}" name="area" aria-label="Area" />
                                </div>

                            </div>
                            <div class="col-md-3">
                                <br><br>
                                <div class="input-group">
                                    <span class="input-group-addon" id="LandMark">Land Mark</span>
                                    <x-Form.input type="text" style="width: 160px;" id="land_mark"
                                        value="{{ $land_mark }}" name="land_mark" aria-label="Land Mrak" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <br><br>
                                <div class="input-group">
                                    <span class="input-group-addon" id="VillaNo">Villa No.</span>
                                    <x-Form.input type="text" style="width: 190px;" id="villa_no"
                                        value="{{ $villa_no }}" name="villa_no" aria-label="Villa Number" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <br><br>
                                <div class="input-group">
                                    <span class="input-group-addon" id="Delivery-Date">Delivery Date</span>
                                    <x-Form.input type="date" style="width: 135px;" id="delivery_date"
                                        value="{{ $delivery_date }}" name="delivery_date"
                                        aria-label="Delivery Date" />
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($page == 'to_delivery')
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-addon" id="Location">Location</span>
                                <x-Form.input type="text" style="width: 193px;" id="location"
                                     name="location" aria-label="Location" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-addon" id="FlatNo">Flat No</span>
                                <x-Form.input type="text" style="width: 190px;" id="flat_no"
                                     name="flat_no" aria-label="Flat No" />
                            </div>
                        </div>
                        <div class="row" style="padding-left: 1.5rem;">
                            <div class="col-md-3">
                                <br><br>
                                <div class="input-group">
                                    <span class="input-group-addon" id="Area">Area</span>
                                    <x-Form.input type="text" style="width: 203px;" id="area"
                                     name="area" aria-label="Area" />
                                </div>

                            </div>
                            <div class="col-md-3">
                                <br><br>
                                <div class="input-group">
                                    <span class="input-group-addon" id="LandMark">Land Mark</span>
                                    <x-Form.input type="text" style="width: 160px;" id="land_mark"
                                         name="land_mark" aria-label="Land Mrak" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <br><br>
                                <div class="input-group">
                                    <span class="input-group-addon" id="VillaNo">Villa No.</span>
                                    <x-Form.input type="text" style="width: 190px;" id="villa_no"
                                         name="villa_no" aria-label="Villa Number" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <br><br>
                                <div class="input-group">
                                    <span class="input-group-addon" id="Delivery-Date">Delivery Date</span>
                                    <x-Form.input type="date" style="width: 135px;" id="delivery_date"
                                         name="delivery_date"
                                        aria-label="Delivery Date" />
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-4" style="padding-top: 5px;">
                        <div class="form-group pl-1">
                            <span class="form-group-addon pr-2" id="vat_type" for="vat_type">{{$tax}} Type</span>
                            @if ($vattype == 1)
                                <label class="pr-2">
                                    <input type="radio" class="vattype_mode disable-after-select-vat"
                                        name="vat_type_mode" value="1" tabindex="4" checked disabled>Inclusive
                                </label>
                                <label>
                                    <input type="radio" class="vattype_mode disable-after-select-vat"
                                        name="vat_type_mode" value="2" tabindex="5" disabled>Exclusive
                                </label>
                            @elseif($vattype == 2)
                                <label class="pr-2">
                                    <input type="radio" class="vattype_mode disable-after-select-vat"
                                        name="vat_type_mode" value="1" tabindex="4" disabled>Inclusive
                                </label>
                                <label>
                                    <input type="radio" class="vattype_mode disable-after-select-vat"
                                        name="vat_type_mode" value="2" tabindex="5" checked disabled>Exclusive
                                </label>
                            @endif
                            <input type="hidden" name="vat_type_value" id="vat_type_value"
                                value="{{ $vattype }}" />
                        </div>
                        <span style="color:red">
                            @error('vat_type_mode')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                </div>

                <div>
                    <div id="prebuiltbilldiv" style="display:block;">
                        <table class="table" style="border-radius: 8px;">
                            <thead>
                                <tr>
                                    <th width="2%"></th>
                                    <th width="10%">Description</th>
                                    <th width="8%">Quantity</th>
                                    <th width="6%">Unit</th>
                                    <th width="8%">Rate</th>
                                    @if ($vattype == 1)
                                        <th width="8%" id="inclusive_heading">Inclusive Rate
                                        </th>
                                    @elseif ($vattype == 2)
                                        <th width="8%" id="ratediscount_heading" style="display:none">Exclusive
                                            Rate</th>
                                    @endif
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
                                    <th width="10%">Total Amount<br />w/o Discount</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>


                                <tr style="background-color: #f8f9fa;" @if ($page=='to_delivery') style="display: none;" @endif>
                                    <td></td>
                                    <td>
                                        <div id="pselect">
                                            <select onclick="doSomething(this.value)" id="product"
                                            class="product-list" style="width: 250px" @if ($page=='to_delivery') disabled @endif>
                                            <option value="">Select Product</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item['id'] }}">{{ $item['product_name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td id="Quantity">
                                        <input type="number" step="1" id="qty" class="form-control qty"
                                            min="1" max="" onkeyup="checkquantity();" tabindex="1"
                                            aria-label="Quantity">
                                    </td>
                                    <td>
                                        <input type="text" name="prounit" id="prounit" class="form-control"
                                            aria-label="Unit" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" id="mrp" class="form-control"
                                            aria-label="MRP" tabindex="2">
                                        <input type="hidden" step="any" id="buycost" class="form-control">
                                        <input type="hidden" step="any" id="buycost_rate" name="buycost_rate"
                                            class="form-control">
                                    </td>

                                    @if ($vattype == 1)
                                        <td id="inclusive_rate_value" name="inclusive_rate_value">
                                        </td>
                                    @elseif ($vattype == 2)
                                        <td id="rate_discount_value" name="rate_discount_value"
                                            style="display: none;">
                                        </td>
                                    @endif
                                    <td>
                                        <input type="number" step="any" id="discount" class="form-control"
                                            min="0" aria-label="Discount" tabindex="3">
                                    </td>
                                    <td>
                                        <input type="number" step="any" id="fixed_vat" class="form-control"
                                            aria-label="VAT" tabindex="4">
                                    </td>
                                    <td>
                                        <input type="number" step="any" id="vat_amount" class="form-control"
                                            aria-label="VAT Amount" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" id="net_rate" class="form-control"
                                            aria-label="Netrate" tabindex="5" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" id="price" class="form-control"
                                            aria-label="Price" readonly>
                                        <input type="hidden" id="pricex" class="form-control">
                                    </td>
                                    <td>
                                        <input type="number" step="any" id="price_wo_discount"
                                            aria-label="Price without Discount" class="form-control" readonly>
                                        <input type="hidden" step="any" id="total_wo_discount"
                                            class="form-control" readonly>
                                    </td>

                                    <td><a href="#" class="btn btn-info addRow" title="Add Row">+</a></td>
                                    <input type="hidden" id="product_id" class="form-control">
                                    <input type="hidden" id="product_name" class="form-control">
                                </tr>
                                <tr>
                                    <td colspan="12"><i class="glyphicon glyphicon-tags"></i> &nbsp
                                        {{ $text }}</td>
                                </tr>
                                @foreach ($details as $detail)
                                    @if ($detail->status === 0)
                                        @php
                                            $styling = 'border:5px solid red;';
                                        @endphp
                                    @elseif ($detail->status === 1)
                                        @php
                                            $styling = '';
                                        @endphp
                                    @endif
                                    <tr style="{{ $styling }}">
                                        <td></td>
                                        <td>
                                            <input type="text" name="productName[{{ $detail->product_id }}]"
                                                value="{{ $detail->product_name }}" class="form-control"
                                                aria-label="Product Name" readonly required />

                                            <input type="hidden" name="productId[{{ $detail->product_id }}]"
                                                value="{{ $detail->product_id }}" class="form-control" required />

                                            <input type="hidden" name="productStatus[{{ $detail->product_id }}]"
                                                value="{{ $detail->status }}" class="form-control" required />
                                        </td>
                                        <td>
                                            <input type="text" name="quantity[{{ $detail->product_id }}]"
                                                value="{{ $detail->quantity }}" class="form-control quantity-input"
                                                aria-label="Quantity"
                                                @if ($detail->status === 0 || $page=='to_delivery') readonly @endif required />
                                                @if ($branch!=3)
                                            @if ($page == 'sales_order' || $page == 'quotation' || $page == 'bill_draft' || $page == 'clone_bill')
                                                <span id="quantity_error_{{ $detail->product_id }}"
                                                    class="text-danger"></span>
                                            @endif
                                            @endif
                                        </td>
                                        <td>
                                            <input type="text" name="prounit[{{ $detail->product_id }}]"
                                                value="{{ $detail->unit }}" class="form-control" aria-label="Unit"
                                                readonly required />
                                        </td>
                                        <td>
                                            <input type="text" name="mrp[{{ $detail->product_id }}]"
                                                value="{{ $detail->mrp }}" class="form-control mrp-edit"
                                                aria-label="MRP" @if ($detail->status === 0 || $page=='to_delivery') readonly @endif
                                                required />

                                            <input type="hidden" step="any"
                                                name="buy_cost[{{ $detail->product_id }}]"
                                                value="{{ $detail->one_pro_buycost }}" class="form-control"
                                                readonly />

                                            <input type="hidden" step="any"
                                                name="buycost_rate[{{ $detail->product_id }}]"
                                                value="{{ $detail->one_pro_buycost_rate }}" class="form-control">
                                        </td>
                                        @if ($vattype == 1)
                                            <td>
                                                <input type="text"
                                                    name="inclusive_rate_r[{{ $detail->product_id }}]"
                                                    value="{{ $detail->inclusive_rate }}" class="form-control"
                                                    aria-label="Inclusive Rate" readonly />
                                            </td>
                                        @elseif ($vattype == 2)
                                            <td>
                                                <input type="text"
                                                    name="rate_discount_r[{{ $detail->product_id }}]"
                                                    value="{{ $detail->exclusive_rate }}" class="form-control"
                                                    aria-label="Exclusive Rate" readonly />
                                            </td>
                                        @endif
                                        <td>
                                        <input type="text" name="dis_count[{{ $detail->product_id }}]"
                                                id="discountInput" {{-- value="{{ $detail->discount_type === 'none' ? 0 : ($detail->discount_type === 'percentage' ? $detail->discount : $detail->discount_amount) }}" --}}
                                                value="{{ $detail->discount_type === 'percentage' ? $detail->discount : ($detail->discount_type === 'amount' ? $detail->discount_amount : 0) }}"
                                                class="form-control" aria-label="Discount"
                                                {{ $detail->discount_type === 'none' ? 'readonly' : '' }}

                                                {{-- @if($page=='to_delivery'){{ in_array($detail->discount_type, ['none', 'percentage', 'amount']) ? 'readonly' : '' }}@endif --}}
                                                @if ($detail->status === 0) readonly @endif />

                                            @if ($detail->status === 1)
                                            @if($page=='to_delivery')
                                            <select style="display: none;" name="dis_count_type[{{ $detail->product_id }}]"
                                                id="discount_type"
                                                class="form-control custom-select-no-padding discount_input">
                                                <option value="none"
                                                    {{ $detail->discount_type === 'none' ? 'selected' : '' }}>No
                                                </option>
                                                <option value="percentage"
                                                    {{ $detail->discount_type === 'percentage' ? 'selected' : '' }}>
                                                    %
                                                </option>
                                                <option value="amount"
                                                    {{ $detail->discount_type === 'amount' ? 'selected' : '' }}>
                                                    {{ $currency }}</option>
                                            </select>
                                            @else
                                                <select name="dis_count_type[{{ $detail->product_id }}]"
                                                    id="discount_type"
                                                    class="form-control custom-select-no-padding discount_input">
                                                    <option value="none"
                                                        {{ $detail->discount_type === 'none' ? 'selected' : '' }}>No
                                                    </option>
                                                    <option value="percentage"
                                                        {{ $detail->discount_type === 'percentage' ? 'selected' : '' }}>
                                                        %
                                                    </option>
                                                    <option value="amount"
                                                        {{ $detail->discount_type === 'amount' ? 'selected' : '' }}>
                                                        {{ $currency }}</option>
                                                </select>
                                                @endif

                                            @elseif ($detail->status === 0)
                                                <input type="text"
                                                    name="dis_count__typee[{{ $detail->product_id }}]"
                                                    {{-- value="{{ $detail->discount_type === 'none' ? 'none' : ($detail->discount_type === 'percentage' ? '%' : $currency) }}" --}}
                                                    value="{{ $detail->discount_type === 'percentage' ? '%' : ($detail->discount_type === 'amount' ? $currency : 'none') }}"
                                                    class="form-control"
                                                    @if ($detail->status === 0) readonly @endif />

                                                <input type="hidden"
                                                    name="dis_count__tp_ori[{{ $detail->product_id }}]"
                                                    {{-- value="{{ $detail->discount_type === 'none' ? 'none' : ($detail->discount_type === 'percentage' ? 'percentage' : 'amount') }}" --}}
                                                    value="{{ $detail->discount_type === 'percentage' ? 'percentage' : ($detail->discount_type === 'amount' ? 'amount' : 'none') }}"
                                                    class="form-control"
                                                    @if ($detail->status === 0) readonly @endif />
                                            @endif
                                        </td>
                                        <td>
                                            <input type="text" name="fixed_vat[{{ $detail->product_id }}]"
                                                value="{{ $detail->fixed_vat }}" class="form-control"
                                                aria-label="VAT"@if ($detail->status === 0 || $page=='to_delivery') readonly @endif
                                                required />
                                        </td>
                                        <td>
                                            <input type="text" name="vat_amount[{{ $detail->product_id }}]"
                                                value="{{ $detail->vat_amount }}" class="form-control"
                                                aria-label="VAT Amount" readonly />
                                        </td>
                                        <td>
                                            <input type="text" name="net_rate[{{ $detail->product_id }}]"
                                                value="{{ $detail->netrate }}" class="form-control"
                                                aria-label="netrate" readonly />
                                        </td>
                                        <td>
                                            <input type="text" name="total_amount[{{ $detail->product_id }}]"
                                                value="{{ $detail->total_amount }}" class="form-control total-amount"
                                                aria-label="Total" readonly />

                                            <input type="hidden" name="price[{{ $detail->product_id }}]"
                                                value="{{ $detail->price }}" class="form-control" />
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="total_amount_wo_discount[{{ $detail->product_id }}]"
                                                value="{{ $detail->totalamount_wo_discount != '' ? $detail->totalamount_wo_discount : $detail->total_amount }}"
                                                class="form-control total-discount-amount"
                                                aria-label="Total Without Discount" readonly />

                                            <input type="hidden"
                                                name="price_withoutvat_wo_discount[{{ $detail->product_id }}]"
                                                value="{{ $detail->price_wo_discount != '' ? $detail->price_wo_discount : $detail->price }}"
                                                class="form-control" />
                                        </td>
                                        <td>
                                            <input type="hidden" name="product_id[{{ $detail->product_id }}]"
                                                value="{{ $detail->product_id }}" class="form-control" />
                                                @if ($page=='bill_draft')
                                                <a href="javascript:void(0);" class="btn btn-danger"
                                                onclick="deleteRow(this);">-</a>
                                                @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <div class="row" style="margin: 1rem; background-color: #f2f2f2;padding:1rem;">
                        <div class="col-sm-7">
                            <div class="discount_row">
                                <div class="checkbox-label">
                                    <label for="total_discount">Add Total discount</label>&nbsp;
                                    <select @if ($page=='to_delivery') disabled @endif name="total_discount" id="total_discount"
                                        class="form-control custom-select-no-padding"
                                        style="width: 80px;margin-top:-5px;">
                                        <option value="0" @if ($total_discount_type == '0' || $total_discount_type == '') selected @endif>No
                                        </option>
                                        <option value="1" @if ($total_discount_type == '1') selected @endif>%
                                        </option>
                                        <option value="2" @if ($total_discount_type == '2') selected @endif>
                                            {{ $currency }}</option>
                                    </select>
                                </div>
                                <div id="discount_field_percentage"
                                    class="group_dis @if ($total_discount_type != '1') hidden @endif">
                                    <div>
                                        <label for="discount_percentage">Discount in %</label>
                                        <input type="number" id="discount_percentage" name="discount_percentage"
                                            value="{{ $total_discount_type != '1' ? '' : $total_discount_percent }}"
                                            @if ($total_discount_type != '1' || $page=='to_delivery') disabled @endif>
                                        <span style="margin-right: 3px;">%</span>
                                    </div>
                                </div>
                                <div id="discount_field_amount"
                                    class="group_dis @if ($total_discount_type != '2') hidden @endif">
                                    <label for="discount_amount">or</label>
                                    <input type="number" id="discount_amount" name="discount_amount"
                                        value="{{ $total_discount_type != '2' ? '' : $total_discount_amount }}"
                                        @if ($total_discount_type != '2'|| $page=='to_delivery') disabled @endif>
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
                        @if (($page == 'sales_order' || $page == 'quotation') && $payment_type == 3)
                            <div class="row" id="advance">
                                <div class="col-sm-7"></div>
                                <div class="col-sm-3"
                                    style="display: flex; flex-direction: column; align-items: flex-end;">
                                    <div class="input-group form-inline"
                                        style="display: flex; align-items: center; margin-bottom: 6px;">
                                        <div class="form-group" style="display: flex; align-items: center; ">
                                            <label for="advance" class="mr-2" style="margin-right: 12px;">Advance
                                                Payment : </label>
                                            <br />
                                            <input type="number" name="advance" id="advanceInput" class="form-control"
                                                style="width: 147px;margin-right: -7rem;border-radius:5px;" />
                                        </div>
                                        <br>
                                    </div>
                                </div>
                            </div>

                        @endif
                        @if ($page == 'bill_draft' && $payment_type == 3)
                        <div class="row" id="advance">
                            <div class="col-sm-7"></div>
                            <div class="col-sm-3"
                                style="display: flex; flex-direction: column; align-items: flex-end;">
                                <div class="input-group form-inline"
                                    style="display: flex; align-items: center; margin-bottom: 6px;">
                                    <div class="form-group" style="display: flex; align-items: center; ">
                                        <label for="advance" class="mr-2" style="margin-right: 12px;">Advance
                                            Payment : </label>
                                        <br />
                                        <input type="number" name="advance" id="advanceInput" class="form-control"
                                            style="width: 147px;margin-right: -7rem;border-radius:5px;" value="{{$advance}}"
                                            />
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>

                    @endif
                        @if ($page == 'edit_bill')
                        <div class="row" id="advance" style="display: none;">
                            <div class="col-sm-7"></div>
                            <div class="col-sm-3"
                                style="display: flex; flex-direction: column; align-items: flex-end;">
                                <div class="input-group form-inline"
                                    style="display: flex; align-items: center; margin-bottom: 6px;">
                                    <div class="form-group" style="display: flex; align-items: center; ">
                                        <label for="advance" class="mr-2" style="margin-right: 12px;">Advance
                                            Payment : </label>
                                        <br />
                                        <input type="number" name="advance" id="advanceInput" class="form-control"
                                            style="width: 147px;margin-right: -7rem;border-radius:5px;" />
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>

                    @endif


                        @if ($page == 'clone_bill' || $page == 'clone_quotation')
                            <div class="row">
                                <div class="col-sm-7"></div>
                                <div class="col-sm-3"
                                    style="display: flex; flex-direction: column; align-items: flex-end;">
                                    <div class="input-group form-inline"
                                        style="display: flex; align-items: center; margin-bottom: 6px;">
                                        <div class="form-group" style="display: flex; align-items: center; ">
                                            <label for="payment_type" class="mr-2"
                                                style="margin-right: 12px;">Payment
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

                            <div class="row" id="advance" style="display: none;">
                                <div class="col-sm-7"></div>
                                <div class="col-sm-3"
                                    style="display: flex; flex-direction: column; align-items: flex-end;">
                                    <div class="input-group form-inline"
                                        style="display: flex; align-items: center; margin-bottom: 6px;">
                                        <div class="form-group" style="display: flex; align-items: center; ">
                                            <label for="advance" class="mr-2" style="margin-right: 12px;">Advance
                                                Payment : </label>
                                            <br />
                                            <input type="number" name="advance" id="advanceInput" class="form-control"
                                                style="width: 147px;margin-right: -7rem;border-radius:5px;" />
                                        </div>
                                        <br>
                                    </div>
                                </div>
                            </div>

                            @endif


                        <div class="col-sm-8"></div>
                        <div class="col-sm-2">
                            <input class="btn btn-primary" type="submit" value="submit" id="submitBtn">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script src="{{ asset('javascript/billing.js') }}"></script>
</body>

</html>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                @if ($page == 'edit_bill' || $page == 'editsalesorder')
                    <h5 class="modal-title" id="inputModalLabel">Why did You Edit {{ $transaction_id }}</h5>
                @elseif (
                    $page == 'sales_order' ||
                        $page == 'quotation' ||
                        $page == 'bill_draft' ||
                        $page == 'salesorderdraft' ||
                        $page == 'quotationdraft' ||
                        $page == 'performadraft' ||
                        $page == 'deliverydraft' ||
                        $page == 'to_delivery' ||
                        $page == 'quot_to_salesorder' ||
                        $page == 'clone_bill' ||
                        $page == 'clone_quotation')
                    <h5 class="modal-title" id="inputModalLabel">Do You Want To Make {{ $transaction_id }}
                        {{ $modaltext }}?
                    </h5>
                @endif
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="popupInput">Enter Value:</label>
                <input type="text" id="additionalInput" class="form-control"
                    placeholder="Enter Reason For Editing">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>
<script>
    var tax = @json($tax);
</script>
<script>
    $(document).ready(function() {

        $('.js-user').select2({
            theme: "classic"
        });

        handleDiscount("#discount_type", "#discount");

        var vat_type = $('#vat_type_value').val();
        var tax = @json($tax); // Ensure tax is converted properly for JavaScript

        handleVatTypeChange(vat_type,tax);

        // Set the initial data attributes for the fields
        $("#discount_percentage").data('initial', $("#discount_percentage").val());
        $("#discount_amount").data('initial', $("#discount_amount").val());

        EditTotalBillDiscount();
    });

    // total discount type change
    function EditTotalBillDiscount() {
        // Store the initial values from the database
        var initialPercentage = $("#discount_percentage").data('initial');
        var initialAmount = $("#discount_amount").data('initial');

        $("#total_discount").change(function() {
            var total_discount = $(this).val();

            if (total_discount == 1) {
                $("#discount_field_percentage").removeClass("hidden");
                $("#discount_field_amount").addClass("hidden");

                $("#discount_percentage").prop("disabled", false).val(initialPercentage);
                $("#discount_amount").prop("disabled", true).val("");
            } else if (total_discount == 2) {
                $("#discount_field_amount").removeClass("hidden");
                $("#discount_field_percentage").addClass("hidden");

                $("#discount_amount").prop("disabled", false).val(initialAmount);
                $("#discount_percentage").prop("disabled", true).val("");
            } else {
                $("#discount_field_percentage").addClass("hidden");
                $("#discount_field_amount").addClass("hidden");
                $("#discount_percentage, #discount_amount")
                    .val("")
                    .prop("disabled", true);
            }
            updateGrandTotalAmount();
        });

        $("#discount_percentage").on("input", function() {
            if ($(this).val() !== "") {
                $("#discount_amount").val("");
            }
            updateGrandTotalAmount();
        });

        $("#discount_amount").on("input", function() {
            if ($(this).val() !== "") {
                $("#discount_percentage").val("");
            }
            updateGrandTotalAmount();
        });
    }
</script>

<script>
    $(document).ready(function() {

        var page = $('#page').val();
        // Apply quantity change handling to existing rows
        $('.quantity-input').each(function() {
            var u = $(this).closest('tr').find('input[name^="productId["]').val();
            handleQuantityChange(u, page);
        });

        $('.mrp-edit').each(function() {
            var u = $(this).closest('tr').find('input[name^="productId["]').val();
            handleMRPandFixedVATChange(u);
        });

        $('.discount_input').each(function() {
            var u = $(this).closest('tr').find('input[name^="productId["]').val();
            handleDiscountChange(u);
        });

        updateGrandTotalAmount();
    });
</script>

<script type="text/javascript">
    var alreadySoldProducts = [];
    // Populate alreadySoldProducts array with product IDs from existing sold products
    @foreach ($details as $detail)
        alreadySoldProducts.push({{ $detail->product_id }});
    @endforeach
</script>

<script type="text/javascript">
    var addedProducts = [];

    function updateGrandTotalAmount() {
        var grandtotal = 0;
        var grandtotal_w_discount = 0;

        $('.total-amount').each(function() {
            grandtotal += Number($(this).val());
        });

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

    // Function to handle quantity changes
    function handleQuantityChange(u, page) {

        var array = @json($items);
        var item = array.find(item => item.id == u);
        var branchid = @json($branch);

        if (item) {
            var remstock = item.remaining_stock;
            var remstk = Number(remstock);
        } else {
            console.log("Item not found in the array.");
        }

        if (page == 'edit_bill' && branchid!=3) {
            var old_billq = @json($details);
            var billquan = old_billq.find(bq => bq.product_id == u).remain_quantity;
            var billq = Number(billquan);
        } else if (page == 'sales_order' || page == 'quotation' || page == 'bill_draft' || page == 'clone_bill') {

            var quantityInput = $('input[name="quantity[' + u + ']"]');
            var propp = Number(quantityInput.val());
            var quantityErrorSpan = $('#quantity_error_' + u);

            if (propp > remstk) {
                quantityInput.addClass('is-invalid');
                quantityErrorSpan.html('Error: Remaining Stock Left: ' + remstk +
                    ' <br/>Quantity exceeds by ' + (propp - remstk));
            } else {
                quantityInput.removeClass('is-invalid'); // Remove validation class
                quantityErrorSpan.html(''); // Clear error message
            }
        } else if (branchid==3 || page == 'editsalesorder' || page == 'salesorderdraft' || page == 'quotationdraft' || page ==
        'performadraft' || page == 'deliverydraft' || page == 'quot_to_salesorder' || page == 'clone_quotation'||page == 'to_delivery') {

        }

        /*-----------------------------------------------*/

        $('input[name="quantity[' + u + ']"], input[name="dis_count[' + u + ']"]').on('input', function() {

            var pro = $('input[name="quantity[' + u + ']"]').val();
            var branchid = @json($branch);

            if (page == 'edit_bill' && branchid!=3) {
                var newlimit = remstk + billq;

                if (pro > newlimit) {

                    $('input[name="quantity[' + u + ']"]').attr("max", newlimit);
                    $('input[name="quantity[' + u + ']"]').val(newlimit);
                }
            } else if (page == 'sales_order' || page == 'quotation' || page == 'bill_draft' || page ==
                'clone_bill') {
                var quantityInput = $('input[name="quantity[' + u + ']"]');
                var propp = Number(quantityInput.val());

                var quantityErrorSpan = $('#quantity_error_' + u);

                if (propp > remstk) {

                    $('input[name="quantity[' + u + ']"]').attr("max", remstk);

                    quantityInput.addClass('is-invalid');
                    quantityErrorSpan.html('Error: Remaining Stock Left: ' + remstk +
                        ' <br/>Quantity exceeds by ' +
                        (propp - remstk));

                } else if (propp == 0 && remstk == 0) {
                    quantityInput.addClass('is-invalid');
                    quantityErrorSpan.html('Error: Remaining Stock Left: ' + remstk +
                        ' <br/>No Quantity Left ');
                } else {
                    quantityInput.removeClass('is-invalid'); // Remove validation class
                    quantityErrorSpan.html(''); // Clear error message
                }
            } else if (page == 'editsalesorder' || page == 'salesorderdraft' || page == 'quotationdraft' ||
                page == 'performadraft' || page == 'deliverydraft' || page == 'quot_to_salesorder' || page ==
                'clone_quotation'||page == 'to_delivery' || branchid==3) {

            }

            var vat_type = $('input[name="vat_type_value"]').val();

            QuantityChangeCalculation(u, vat_type);
        });
    }

    // mrp and vat edit for alraedy sold products
    function handleMRPandFixedVATChange(u) {

        var vat_type = $('input[name="vat_type_value"]').val();

        if (vat_type == 1) {

            $('input[name="mrp[' + u + ']"], input[name="fixed_vat[' + u + ']"], input[name="dis_count[' + u + ']"]')
                .keyup(function() {

                    var mrp = Number($('input[name="mrp[' + u + ']"]').val());
                    var fixed_vat = Number($('input[name="fixed_vat[' + u + ']"]').val());
                    var discounts = Number($('input[name="dis_count[' + u + ']"]').val());
                    var discount_type = $('select[name="dis_count_type[' + u + ']"]').val();

                    var subInclusiveRate = mrp / (1 + fixed_vat / 100);

                    if (discount_type == "percentage") {
                        var inclus_disocunt_amt = mrp * (discounts / 100);
                        var InclusiveRate = mrp - inclus_disocunt_amt;
                    } else if (discount_type == "amount") {
                        inclus_disocunt_amt = discounts;
                        var InclusiveRate = mrp - inclus_disocunt_amt;
                    } else if (discount_type == "none") {
                        var inclus_disocunt_amt = subInclusiveRate * (discounts / 100);
                        var InclusiveRate = subInclusiveRate - inclus_disocunt_amt;
                    }

                    var InclusiveRate = Math.round(InclusiveRate * 1000) / 1000;

                    $('input[name="inclusive_rate_r[' + u + ']"]').val(InclusiveRate);

                    updateGrandTotalAmount();
                });

            $('input[name="qty[' + u + ']"],input[name="mrp[' + u + ']"],input[name="dis_count[' + u + ']"]').keyup(
                function() {

                    var total = 0;
                    var total_wo_discount = 0;
                    var grandtotal_wo_disc = 0;

                    var netrate = Number($('input[name="net_rate[' + u + ']"]').val());
                    var quantity = Number($('input[name="quantity[' + u + ']"]').val());
                    var mrp = Number($('input[name="mrp[' + u + ']"]').val());
                    var fixed_vat = Number($('input[name="fixed_vat[' + u + ']"]').val());
                    var inc_rate = Number($('input[name="inclusive_rate_r[' + u + ']"]').val());
                    var discounts = Number($('input[name="dis_count[' + u + ']"]').val());
                    var discount_type = $('select[name="dis_count_type[' + u + ']"]').val();

                    if ((discount_type == "percentage" || discount_type == "amount") && discounts != 0) {
                        var inc_withoutvat = mrp / (1 + fixed_vat / 100);

                        if (discount_type == "percentage") {
                            var discnt_amount = mrp * (discounts / 100);
                        } else if (discount_type == "amount") {
                            var discnt_amount = discounts;
                        }

                        var without_disc_amnt = mrp - discnt_amount;

                        var withoutvat_wdisc = without_disc_amnt / (1 + fixed_vat / 100);

                        var wo_disc_wo_vat = without_disc_amnt / (1 + fixed_vat / 100);
                        var vat_am = without_disc_amnt - wo_disc_wo_vat;

                        netrate = without_disc_amnt;

                        var grandtotal = netrate * quantity;
                        var grandtotal_wo_disc = mrp * quantity;
                        var grandvat = vat_am * quantity;
                        total_wo_discount = inc_withoutvat * quantity;
                        total = withoutvat_wdisc * quantity;

                    } else if (discount_type == "none" || ((discount_type == "percentage" || discount_type ==
                            "amount") && discounts == 0)) {
                        netrate = mrp;
                        var grandtotal = netrate * quantity;
                        var grandtotal_wo_disc = grandtotal;

                        var vat_am = grandtotal - grandtotal / (1 + fixed_vat / 100);

                        var grandvat = vat_am;
                        total_wo_discount = inc_rate * quantity;
                        total = total_wo_discount;
                    }

                    grandtotal = Math.round(grandtotal * 1000) / 1000;
                    grandtotal_wo_disc = Math.round(grandtotal_wo_disc * 1000) / 1000;
                    grandvat = Math.round(grandvat * 1000) / 1000;
                    netrate = Math.round(netrate * 1000) / 1000;

                    $('input[name="price[' + u + ']"]').val(total);
                    $('input[name="total_amount[' + u + ']"]').val(grandtotal);
                    $('input[name="vat_amount[' + u + ']"]').val(grandvat);
                    $('input[name="net_rate[' + u + ']"]').val(netrate);
                    $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val(total_wo_discount);
                    $('input[name="total_amount_wo_discount[' + u + ']"]').val(grandtotal_wo_disc);

                    updateGrandTotalAmount();
                });

            $('input[name="fixed_vat[' + u + ']"],input[name="net_rate[' + u + ']"],input[name="dis_count[' + u + ']"]')
                .keyup(function() {

                    var total = 0;
                    var total_wo_discount = 0;
                    var grandtotal_wo_disc = 0;

                    var netrate = Number($('input[name="net_rate[' + u + ']"]').val());
                    var quantity = Number($('input[name="quantity[' + u + ']"]').val());
                    var mrp = Number($('input[name="mrp[' + u + ']"]').val());
                    var fixed_vat = Number($('input[name="fixed_vat[' + u + ']"]').val());
                    var inc_rate = Number($('input[name="inclusive_rate_r[' + u + ']"]').val());
                    var discounts = Number($('input[name="dis_count[' + u + ']"]').val());
                    var discount_type = $('select[name="dis_count_type[' + u + ']"]').val();

                    if ((discount_type == "percentage" || discount_type == "amount") && discounts != 0) {
                        var inc_withoutvat = mrp / (1 + fixed_vat / 100);

                        if (discount_type == "percentage") {
                            var discnt_amount = mrp * (discounts / 100);
                        } else if (discount_type == "amount") {
                            var discnt_amount = discounts;
                        }

                        var without_disc_amnt = mrp - discnt_amount;

                        var wo_disc_wo_vat = without_disc_amnt / (1 + fixed_vat / 100);
                        var vat_am = without_disc_amnt - wo_disc_wo_vat;

                        netrate = without_disc_amnt;

                        var grandtotal = netrate * quantity;
                        var grandtotal_wo_disc = mrp * quantity;
                        var grandvat = vat_am * quantity;
                        total_wo_discount = inc_withoutvat * quantity;
                        total = wo_disc_wo_vat * quantity;
                    } else if (discount_type == "none" || ((discount_type == "percentage" || discount_type ==
                            "amount") && discounts == 0)) {
                        netrate = mrp;
                        var grandtotal = netrate * quantity;
                        var grandtotal_wo_disc = grandtotal;

                        var vat_am = grandtotal - grandtotal / (1 + fixed_vat / 100);

                        var grandvat = vat_am;
                        total_wo_discount = inc_rate * quantity;
                        total = total_wo_discount;
                    }

                    grandtotal = Math.round(grandtotal * 1000) / 1000;
                    grandtotal_wo_disc = Math.round(grandtotal_wo_disc * 1000) / 1000;
                    grandvat = Math.round(grandvat * 1000) / 1000;
                    netrate = Math.round(netrate * 1000) / 1000;

                    $('input[name="price[' + u + ']"]').val(total);
                    $('input[name="total_amount[' + u + ']"]').val(grandtotal);
                    $('input[name="vat_amount[' + u + ']"]').val(grandvat);
                    $('input[name="net_rate[' + u + ']"]').val(netrate);
                    $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val(total_wo_discount);
                    $('input[name="total_amount_wo_discount[' + u + ']"]').val(grandtotal_wo_disc);

                    updateGrandTotalAmount();
                });

        } else if (vat_type == 2) {

            $('input[name="mrp[' + u + ']"],input[name="fixed_vat[' + u + ']"],input[name="dis_count[' + u + ']"]')
                .keyup(function() {
                    var mrp = Number($('input[name="mrp[' + u + ']"]').val());
                    var fixed_vat = Number($('input[name="fixed_vat[' + u + ']"]').val());
                    var discounts = Number($('input[name="dis_count[' + u + ']"]').val());
                    var discount_type = $('select[name="dis_count_type[' + u + ']"]').val();

                    if (discount_type == "percentage" || discount_type == "amount") {
                        if (discount_type == "percentage") {
                            var disc_mrp = mrp - mrp * (discounts / 100);
                        } else if (discount_type == "amount") {
                            var disc_mrp = mrp - discounts;
                        }

                        var mrp_disc_vat = disc_mrp * (fixed_vat / 100);
                        var netrate = mrp_disc_vat + disc_mrp;
                        var mrp_with_discount = disc_mrp;
                    } else if (discount_type == "none") {
                        var netrate = mrp * (fixed_vat / 100) + mrp;
                        var mrp_with_discount = mrp;
                    }

                    var netrate = Math.round(netrate * 1000) / 1000;

                    $('input[name="net_rate[' + u + ']"]').val(netrate);
                    $('input[name="rate_discount_r[' + u + ']"]').val(mrp_with_discount);

                    updateGrandTotalAmount();
                });


            $('input[name="qty[' + u + ']"],input[name="mrp[' + u + ']"],input[name="dis_count[' + u + ']"]').keyup(
                function() {

                    var total = 0;
                    var total_wo_discount = 0;
                    var grandtotal_wo_disc = 0;

                    var netrate = Number($('input[name="net_rate[' + u + ']"]').val());
                    var quantity = Number($('input[name="quantity[' + u + ']"]').val());
                    var mrp = Number($('input[name="mrp[' + u + ']"]').val());
                    var fixed_vat = Number($('input[name="fixed_vat[' + u + ']"]').val());
                    var discounts = Number($('input[name="dis_count[' + u + ']"]').val());
                    var discount_type = $('select[name="dis_count_type[' + u + ']"]').val();

                    var mrp_vat = mrp * (fixed_vat / 100);

                    var grandvat = 0;
                    var grandtotal = 0;

                    if (discount_type === "percentage" || discount_type === "amount") {
                        var disc_mrp =
                            discount_type === "percentage" ?
                            mrp - mrp * (discounts / 100) :
                            mrp - discounts;

                        var mrp_disc_vat = disc_mrp * (fixed_vat / 100);
                        netrate = mrp_disc_vat + disc_mrp;

                        // vat
                        grandvat = mrp_disc_vat * quantity;
                        total = disc_mrp * quantity;
                        var mrp_with_discount = disc_mrp;
                    } else if (discount_type === "none") {
                        netrate = mrp_vat + mrp;
                        grandvat = mrp_vat * quantity;
                        total = mrp * quantity;
                        var mrp_with_discount = mrp;
                    }

                    grandtotal = netrate * quantity;
                    grandtotal_wo_disc = (mrp + mrp_vat) * quantity;
                    total_wo_discount = mrp * quantity;

                    grandtotal_wo_disc = Math.round(grandtotal_wo_disc * 1000) / 1000;
                    var grandtotal = Math.round(grandtotal * 1000) / 1000;
                    var grandvat = Math.round(grandvat * 1000) / 1000;
                    var netrate = Math.round(netrate * 1000) / 1000;

                    $('input[name="price[' + u + ']"]').val(total);
                    $('input[name="total_amount[' + u + ']"]').val(grandtotal);
                    $('input[name="vat_amount[' + u + ']"]').val(grandvat);
                    $('input[name="net_rate[' + u + ']"]').val(netrate);
                    $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val(total_wo_discount);
                    $('input[name="total_amount_wo_discount[' + u + ']"]').val(grandtotal_wo_disc);

                    updateGrandTotalAmount();
                });

            $('input[name="fixed_vat[' + u + ']"],input[name="net_rate[' + u + ']"],input[name="dis_count[' + u + ']"]')
                .keyup(function() {

                    var total = 0;
                    var total_wo_discount = 0;
                    var grandtotal_wo_disc = 0;

                    var netrate = Number($('input[name="net_rate[' + u + ']"]').val());
                    var quantity = Number($('input[name="quantity[' + u + ']"]').val());
                    var mrp = Number($('input[name="mrp[' + u + ']"]').val());
                    var fixed_vat = Number($('input[name="fixed_vat[' + u + ']"]').val());
                    var discounts = Number($('input[name="dis_count[' + u + ']"]').val());
                    var discount_type = $('select[name="dis_count_type[' + u + ']"]').val();

                    var mrp_vat = mrp * (fixed_vat / 100);

                    var grandvat = 0;
                    var grandtotal = 0;

                    if (discount_type === "percentage" || discount_type === "amount") {
                        var disc_mrp =
                            discount_type === "percentage" ?
                            mrp - mrp * (discounts / 100) :
                            mrp - discounts;

                        var mrp_disc_vat = disc_mrp * (fixed_vat / 100);
                        netrate = mrp_disc_vat + disc_mrp;

                        // vat
                        grandvat = mrp_disc_vat * quantity;
                        total = disc_mrp * quantity;
                        var mrp_with_discount = disc_mrp;
                    } else if (discount_type === "none") {
                        netrate = mrp_vat + mrp;
                        grandvat = mrp_vat * quantity;
                        total = mrp * quantity;
                        var mrp_with_discount = mrp;
                    }

                    grandtotal = netrate * quantity;
                    grandtotal_wo_disc = (mrp + mrp_vat) * quantity;
                    total_wo_discount = mrp * quantity;

                    grandtotal_wo_disc = Math.round(grandtotal_wo_disc * 1000) / 1000;
                    var grandtotal = Math.round(grandtotal * 1000) / 1000;
                    var grandvat = Math.round(grandvat * 1000) / 1000;
                    var netrate = Math.round(netrate * 1000) / 1000;

                    $('input[name="price[' + u + ']"]').val(total);
                    $('input[name="total_amount[' + u + ']"]').val(grandtotal);
                    $('input[name="vat_amount[' + u + ']"]').val(grandvat);
                    $('input[name="net_rate[' + u + ']"]').val(netrate);
                    $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val(total_wo_discount);
                    $('input[name="total_amount_wo_discount[' + u + ']"]').val(grandtotal_wo_disc);

                    updateGrandTotalAmount();
                });
        }
    }

    function handleDiscountChange(u) {
        var vat_type = $('input[name="vat_type_value"]').val();

        var page = $('#page').val();

        var array = @json($items);
        var remstock = array.find(item => item.id == u).remaining_stock;
        var remstk = Number(remstock);
        var branchid = @json($branch);

        if (page == 'edit_bill' && branchid!=3) {
            var old_billq = @json($details);
            var billquan = old_billq.find(bq => bq.product_id == u).remain_quantity;
            var billq = Number(billquan);

            addRowDiscountCalculation(u, remstk, vat_type, billq, page,branchid);

        } else if (page == 'sales_order' || page == 'quotation' || page == 'bill_draft' || page == 'clone_bill') {
            var quantityInput = $('input[name="quantity[' + u + ']"]');
            var propp = Number(quantityInput.val());
            var quantityErrorSpan = $('#quantity_error_' + u);

            if (propp > remstk) {
                quantityInput.addClass('is-invalid');
                quantityErrorSpan.html('Error: Remaining Stock Left: ' + remstk +
                    ' <br/>Quantity exceeds by ' + (propp - remstk));
            } else {
                quantityInput.removeClass('is-invalid'); // Remove validation class
                quantityErrorSpan.html(''); // Clear error message
            }

            addRowDiscountCalculation(u, remstk, vat_type, 0, page,branchid);
        } else if (branchid==3 || page == 'editsalesorder' || page == 'salesorderdraft' || page == 'quotationdraft' || page ==
        'performadraft' || page == 'deliverydraft' || page == 'quot_to_salesorder' || page == 'clone_quotation'||page == 'to_delivery') {

            addRowDiscountCalculation(u, remstk, vat_type, 0, page,branchid);
        }
    }

    $('.addRow').off().on('click', addRow);

    function addRow() {

        var productId = $('#product_id').val();

        // Check if the product is already added or sold in the transaction
        if (addedProducts.includes(productId) || alreadySoldProducts.includes(parseInt(productId))) {
            alert('Product is either already added or sold in this transaction.');
            $("#qty").val("");
            $("#product").val(null).trigger('change');
            return;
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
        var discount_type = $("#discount_type").val();

        discount = (discount != null) ? discount : 0;
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
        var vat_type = $('#vat_type_value').val();

        if (vat_type == 1) {
            var inclu_rate = Number($("#inclusive_rate").val());
        } else if (vat_type == 2) {
            var ratediscount = Number($("#rate_discount").val());
        }

        var tr = '<tr>' + '<td></td>' + '<td>' +
            '<input type="text" id="productnamevalue" value="' + y + '" name="productName[' + u +
            ']" class="form-control" readonly> <input type="hidden"  value="' + u + '" name="productId[' + u +
            ']" class="form-control">' +
            '</td>' +
            '<td id="barquan"><input type="text" value=' + x + ' id="quantityrow" name="quantity[' + u +
            ']" class="form-control" required><span id=quantity_error_' + u + ' class="text-danger"></span></td >' +
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

        tr += '<td><input type="number" value="' + discount + '" name="dis_count[' + u +
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
            ']" class="form-control total-amount" readonly><input type="hidden" value=' + p +
            ' id="rowprice" name="price[' + u + ']" class="form-control"></td>' +
            '<td><input type="number" value=' + price_dis +
            ' id="total_amount_wo_discount" name="total_amount_wo_discount[' + u +
            ']" class="form-control total-discount-amount" readonly>' +
            '<input type="hidden" value=' + total_disc +
            ' id="price_withoutvat_wo_discount" name="price_withoutvat_wo_discount[' +
            u + ']" class="form-control" readonly></td>' +
            '<td><a href="#" class="btn btn-danger remove">-</a></td>' +
            '<input type="hidden" value=' + u + ' name="product_id[' + u + ']" class="form-control" >' +
            '</tr>';
        $('tbody').append(tr);

        addedProducts.push(productId);

        var nu = "";
        $("#qty").val(nu);
        $("#price").val(nu);
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
        var remstock = array.find(item => item.id == u).remaining_stock;
        var remstk = Number(remstock);

        var page = $('#page').val();
        var branchid = @json($branch);

        addRowDiscountCalculation(u, remstk, $('input[name="vat_type_value"]').val(), 0, page,branchid);

        /*-------------------------------------------------------------------------------------------------*/
        // After adding a new row
        updateGrandTotalAmount();
    }

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

        var vat_type_selected = $('#vat_type_value').val();

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
        $("#qty").val(nu);
        $("#price").val(nu);
        $("#vat_amount").val(nu);
        $('#qty').focus();

        $("#discount").val(nu);
        $("#price_wo_discount").val(nu);
        $("#total_wo_discount").val(nu);

        var page = $('#page').val();

        /*-------------CHECK STOCK WHEN ENTERING QUANTITY-----------------------*/

       var remaining_stock = parseFloat(rem);
        $("#qty").attr("max", remaining_stock);
        var branchid = @json($branch);

        $(document).ready(function() {
            $('#qty').on('input', function() {
                var product = parseFloat($('#qty').val());
                if (!(page == 'editsalesorder' || page == 'salesorderdraft' || page ==
                        'quotationdraft' || page == 'performadraft' || page == 'deliverydraft' ||
                        page == 'quot_to_salesorder' || page == 'clone_quotation'||page == 'to_delivery') && (
                        product >
                        remaining_stock) && branchid!=3) {
                    $('.addRow').off();
                } else {
                    $('.addRow').off().on('click', addRow);
                }
            });
        });
        /*-------------------------------------------------------------------------*/
    }

    function checkquantity() {
        var branchid = parseInt(document.getElementById("branchid").value);
        if (branchid != 3) {
        p_id = $('#product_id').val();
        var page = $('#page').val();

        var array = @json($items);

        function isSeries(elm) {
            return elm.id == p_id;
        }
        var p_name = array.find(isSeries).product_name;
        var rem = array.find(isSeries).remaining_stock;
        var remaining_stock = parseFloat(rem);
        var myField = document.getElementById("qty");
        var inputQuantity = parseFloat(myField.value);

        if (!(page == 'editsalesorder' || page == 'salesorderdraft' || page == 'quotationdraft' || page ==
        'performadraft' || page == 'deliverydraft' || page == 'quot_to_salesorder' || page == 'clone_quotation' ||page == 'to_delivery'
            ) && (inputQuantity >
                remaining_stock)) {
            if (!(page == "editsalesorder" || page == "salesorderdraft" || page == "quotationdraft" || page ==
                    "performadraft" || page == "deliverydraft" || page == "quot_to_salesorder" || page ==
                    "clone_quotation" ||page == "to_delivery")) {
                        alert("Remaining stock of " + p_name + " left only :" + remaining_stock);
                        $('.addRow').off();
            } else if (page == "editsalesorder" || page == "salesorderdraft" || page == "quotationdraft" || page ==
            "performadraft" || page == "deliverydraft" || page == "quot_to_salesorder" || page == "clone_quotation" ||page == "to_delivery"
            ) {
                // Do not display an alert
            }
        }
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

        var page = $('#page').val();
        var branchid = parseInt(document.getElementById("branchid").value);
        if(branchid!=3){

        if (page == 'sales_order' || page == 'quotation' || page == 'bill_draft' || page == 'clone_bill') {
            var hasErrors = false;
            var array = @json($items);

            array.forEach(function(item) {
                var productId = item.id;
                var quantityInput = $('input[name="quantity[' + productId + ']"]');
                var remstock = item.remaining_stock;
                var remstk = Number(remstock);
                var propp = Number(quantityInput.val());

                if (propp > remstk) {
                    // Set hasErrors to true if there is an error
                    hasErrors = true;
                }

                if (propp == 0 && remstk == 0) {
                    hasErrors = true;
                }
            });

            // If there are errors, show an alert and prevent form submission
            if (hasErrors) {
                alert('There are errors in the quantity. Please purchase enough stock.');
                // Re-enable the submit button after alert
                submitBtn.disabled = false;
                submitBtn.innerText = "Submit";

                return false; // Validation failed; prevent form submission
            }
        }
    }

        $('#myModal').modal('show');

        // Form is valid; continue with submission
        // You can also perform any additional logic here

        // No need to re-enable the submit button here since the form will submit

        return false; // Validation passed; allow the form to submit
        // return true;// success
    }

    // Event listener for modal confirm button
    document.getElementById('confirmBtn').addEventListener('click', function() {
        // Check if the additional input field is not empty
        if (document.getElementById('additionalInput').value.trim() !== '') {
            // Get the additional input value
            const additionalData = document.getElementById('additionalInput').value;

            // Set the additional data to the hidden input field
            document.getElementById('edit_comment').value = additionalData;
            // Submit the form
            document.getElementById('billForm').submit();
        } else {
            alert('Please enter additional data.');
        }
    });

    // Event listener for modal hidden event
    $('#myModal').on('hidden.bs.modal', function() {
        // Re-enable the submit button
        document.getElementById('submitBtn').disabled = false;
    });

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
    var mrp = document.getElementById("mrp");
    var fixed_vat = document.getElementById("fixed_vat");
    var vat_amount = document.getElementById("vat_amount");
    var net_rate = document.getElementById("net_rate");
    var price = document.getElementById("price");
    var discount_s = document.getElementById("discount");

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
    discount_s.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
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
    function getCreditId(selectedUserId) {
            var selectedOption = document.querySelector(`#user_id option[value="${selectedUserId}"]`);
            var lAmount = selectedOption.getAttribute('data-current_lamount');
            var balance = selectedOption.getAttribute('data-balance');
            var customername = selectedOption.getAttribute('data-name');

            document.getElementById('credit_id').value = selectedUserId;
            document.getElementById('current_lamount').value = lAmount;
            document.getElementById('advance_balance').value = balance;
            document.getElementById('customer').value = customername;
            updateTotalBalance();
            let bankDropdown = document.getElementById("bank_name");
            bankDropdown.selectedIndex = 0;
            bankDropdown.style.display = 'block';
        }

        function updateTotalBalance() {
            const currentAmount = parseFloat(document.getElementById('current_lamount').value) || 0;
            const advanceAmount = parseFloat(document.getElementById('advanceInput').value) || 0;
            const advanceBalance = parseFloat(document.getElementById('advance_balance').value) || 0;

            const advanceBalanceFlag = advanceBalance > 0 ? 1 : 0;
    document.getElementById('advance_balance_flag').value = advanceBalanceFlag;

    console.log("Updated total balance:", currentAmount + advanceAmount + advanceBalance);
}


        $(document).ready(function() {

            updateTotalBalance();
        });

        document.getElementById('current_lamount').addEventListener('input', updateTotalBalance);
        document.getElementById('advanceInput').addEventListener('input', updateTotalBalance);
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
  function handleSubmitButtonClick(event) {
    var payment_type = parseInt($("#payment_type").val()); // Convert to number
    var payment_mode = parseInt($("#payment_mode").val()); // Convert to number
    var hidden_payment_mode = parseInt($("#hidden_payment_mode").val()); // Convert to number

    console.log("hidden_payment_mode:", hidden_payment_mode);
        var advanceInput = parseFloat(document.getElementById('advanceInput').value) || 0;
    var currentBalance = parseFloat(document.getElementById('current_lamount').value);
    var billAmount = parseFloat(document.getElementById('bill_grand_total').value) || 0;
    var totaladvBalance = parseFloat(document.getElementById('advance_balance').value) || 0;

    var customernames = document.getElementById('cust_id').value;
    var page = $('#page').val();

    console.log("Page:", page);
    console.log("Current balance:", currentBalance);
    console.log("Advance input:", advanceInput);
    console.log("Bill amount:", billAmount);
    console.log("Total advance balance:", totaladvBalance);

    var totalBalance = 0;

    // Check if current_lamount is not null or undefined
    if (currentBalance !== null && !isNaN(currentBalance)) {

        // Check if the page is not clone_quotation
        if (page !== 'clone_quotation') {
            // Calculate total balance based on the page type
            if (page === 'edit_bill') {
                var prev_grand_total = parseFloat(document.getElementById('prev_grand_total').value) || 0;
                if (hidden_payment_mode!=3) {
                totalBalance = currentBalance + advanceInput + totaladvBalance + prev_grand_total;
                }else{
                    totalBalance = currentBalance + advanceInput + totaladvBalance;

                }
                        } else if (page === 'sales_order' || page === 'bill_draft' || page === 'quotation' || page === 'clone_bill') {
                totalBalance = currentBalance + advanceInput + totaladvBalance;
            }

            console.log("Total balance:", totalBalance);

            // If the payment type is 3 (credit), and total balance is less than the bill amount
            if (payment_type === 3  && totalBalance < billAmount) {
                event.preventDefault(); // Stop form submission

                if (totaladvBalance > 0) {
                    alert(customernames + ' has only ' + currentBalance + ' MRP remaining in credit limit and ' + totaladvBalance + ' MRP remaining in due.');
                } else {
                    alert(customernames + ' has only ' + currentBalance + ' MRP remaining in credit limit.');
                }
            }
        }
    }
}

// Attach event listener to the submit button
document.getElementById('submitBtn').addEventListener('click', handleSubmitButtonClick);

    </script>


<script>
    let previousAdvanceValue = 0;

    function updateAdvanceBalance() {
        let advanceInputValue = parseFloat(document.getElementById('advanceInput').value) || 0;

        let advanceBalanceValue = parseFloat(document.getElementById('advance_balance').value) || 0;

        let newBalance = advanceBalanceValue + (advanceInputValue - previousAdvanceValue);

        document.getElementById('advance_balance').value = newBalance.toFixed(2); // Format to 2 decimal places

        previousAdvanceValue = advanceInputValue;
    }

    function initializeAdvanceBalance() {
        previousAdvanceValue = parseFloat(document.getElementById('advanceInput').value) || 0;

        document.getElementById('advance_balance').value = previousAdvanceValue.toFixed(2); // Format to 2 decimal places
    }

    window.onload = initializeAdvanceBalance;

    document.getElementById('advanceInput').addEventListener('input', updateAdvanceBalance);
</script>
<script>
    function handledit(event) {
    var payment_type = $("#payment_type").val();
    var billAmount = parseFloat(document.getElementById('bill_grand_total').value) || 0;
    var currentBankBalance = parseFloat(document.getElementById('current_balance').value) || 0;
    var prev_grand_total = parseFloat(document.getElementById('prev_grand_total').value) || 0;
    var accountname = document.getElementById('account_name').value;

    var page = $('#page').val();




    // Check if current_lamount is not null or undefined

        // Check if the page is not clone_quotation
            // Calculate total balance based on the page type
            if (payment_type == 2 && page === 'edit_bill' && currentBankBalance === 0 && prev_grand_total > billAmount) {
                event.preventDefault(); // Stop form submission
                alert('Bank balance: ' + currentBankBalance + ' for account holder: ' + accountname + ', so you cannot deduct the bill amount.');
            }

}

// Attach event listener to the submit button
document.getElementById('submitBtn').addEventListener('click', handledit);

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
        paymentTypeDropdown.value = ""; // Clear the selection
        handlePaymentTypeChange(""); // Trigger onchange logic for clearing
    }
}

</script>
<script>
    function deleteRow(button) {
        // Find the row to be deleted
        var row = button.parentNode.parentNode;
        // Remove the row from the table
        row.parentNode.removeChild(row);
    }
</script>
<script>
  function updatePaymentType() {
    var selected = document.getElementById("payment_mode").value;
    var paymentType = document.getElementById("payment_type");

    // Update hidden field value only if Cash or Post Card is selected
    if (selected == 1 || selected == 3 || selected == 4) {
        paymentType.value = selected;
    }

    var paymentMode = document.getElementById("payment_mode").value;
    document.getElementById("hidden_payment_mode").value = paymentMode;
}

</script>
