<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Plexpay billing">
    @if ($page == 'edit_purchase')
        <title>Edit Purchase</title>
    @elseif ($page == 'purchase_order' || $page == 'edit_purchase_draft')
        <title>Convert To Purchase</title>
    @endif
   
@if (Session('adminuser'))
        @include('layouts/adminsidebar')
    @elseif(Session('softwareuser'))
        @include('layouts/usersidebar')
    @endif
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
    <style>
 
        .disabled-row {
            cursor: not-allowed !important;
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
        <div style="margin-left:15px;margin-top:18px;">

            @include('navbar.invnavbar')
        </div>
        @else
<x-logout_nav_user />
@endif
        @if (Session('softwareuser'))

        <!--<div align="right">-->
        <!--        @include('layouts.quick')-->

        <!--        <a href="" class="btn btn-info">Refresh</a>-->
        <!--    </div>-->
            <x-admindetails_user :shopdatas="$shopdatas" />
        @endif
        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif

        @php
            $url = '';
            $text = '';
            $modeltext = '';
            switch ($page) {
                case 'edit_purchase':
                    $url = "/edit_purchasedetails/{ $page }/submit_editpurchase";
                    $text = 'EDIT PURCHASE';
                    $modeltext = 'Why did You Edit Purchase:';
                    break;
                case 'purchase_order':
                    $url = "/to_purchase/{ $page}/submitstock_table";
                    $text = 'TO PURCHASE';
                    $modeltext = 'Do You Want To Make Purchase:';
                    break;
                case 'edit_purchase_draft':
                    $url = "/submitpurchasedraft/{$page}/{$receipt_no}/submitstock_table";
                    $text = 'TO PURCHASE';
                    $modeltext = 'Do You Want To Make Purchase:';
                    break;
            }
        @endphp

        <!-- {{-- <form method="post" action="/edit_purchasedetails/{{ $page }}/submit_editpurchase"
            enctype="multipart/form-data" id="edit_purchase_form" name="edit_purchase_form"
            onsubmit="return validateForm();"> --}} -->

        <form method="post" action="{{ $url }}" enctype="multipart/form-data" id="edit_purchase_form"
            name="edit_purchase_form" onsubmit="return validateForm();">
            @csrf

           @if ($page == 'edit_purchase')
            @if ($method == 2)
                <h2>Edit Service</h2>
            @else
                <h2>Edit Purchase</h2>
            @endif
        @elseif ($page == 'edit_purchase_draft')
            @if ($method == 2)
                <h2>To Service</h2>
            @else
                <h2>To Purchase</h2>
            @endif
        @elseif ($page == 'purchase_order')
            <h2>To Purchase</h2>
        @endif

        @if ($page == 'edit_purchase' || $page == 'edit_purchase_draft')
        @php
            $methodText = ($method == 2) ? 'Service' : 'Purchase';
        @endphp
            <input type="hidden" name="method" id="" value="{{ $methodText }}">

        @endif

            <br />

            <input type="hidden" name="edit_comment" id="edit_comment">

            <div class="form-group row" style="padding-left:2rem;padding-right:2rem;">
                <div style="background-color: #f8f9fa; padding: 10px; border-radius: 12px; border: 1px solid #dee2e6; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);height:150px;">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">Bill Number</span>

                            <x-Form.input type="text" id="reciept_nos" name="reciept_no" class="receiptno"
                                aria-describedby="basic-addon1" autocomplete="off" value="{{ $receipt_no }}"
                                readonly /> <br />
                        </div>
                        <input type="hidden" name="page" id="page" value="{{ $page }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group" style="">
                            <span class="input-group-addon" id="basic-addon2">Comment</span>

                            <x-Form.input type="text" id="comment" name="comment" class="comment"
                                aria-describedby="basic-addon2" value="{{ $comment }}" tabindex="1" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">

                            <span class="input-group-addon" id="basic-addon3">Supplier Name</span>

                            <x-Form.input type="text" name="supplier" id="supplierdata" class="supplier"
                                aria-describedby="basic-addon3" value="{{ $supplier }}" readonly />
                        </div>
                        <input type="hidden" id="supp_id" name="supp_id" value="{{ $supplier_id }}" readonly>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">

                            @php
                            $type = '';
                            if ($payment_type == 1) {
                                $type = 'CASH';
                            } elseif ($payment_type == 2) {
                                $type = 'CREDIT';
                            } elseif ($payment_type == 3) {
                                $type = 'BANK';
                            }
                        @endphp
                                                    @if ($page == 'edit_purchase')
                                <span class="input-group-addon" id="basic-addon4">Payment Mode</span>
                                <x-Form.input type="text" id="payment_mode" name="payment_mode"
                                    value="{{ $type }}" readonly />
                                <input type="hidden" id="payment_type" name="payment_type" value="{{ $payment_type }}"
                                    readonly />
                                    <input type="hidden" name="bank_name" id="" value="{{$bank_id}}">
                                    <input type="hidden" name="account_name" value="{{$account_name}}">
                                    <input type="hidden" name="current_balance" id="current_balance" value="{{$current_balance}}">
                            @elseif ($page == 'purchase_order' || $page == 'edit_purchase_draft')
                                <span class="input-group-addon" id="basic-addon4">Payment Mode</span>
                                <x-Form.input type="text" id="payment_type" name="payment_type"
                                    value="{{ $type }}" readonly />
                                <input type="hidden" id="payment_mode" name="payment_mode" value="{{ $payment_type }}"
                                    readonly />
                                    <input type="hidden" name="bank_name" id="" value="{{$bank_id}}">
                                    <input type="hidden" name="account_name" value="{{$account_name}}">
                                    <input type="hidden" name="current_balance" id="current_balance" value="{{$current_balance}}">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon5">Invoice Date</span>
                            <x-Form.input type="date" id="invoice_date" name="invoice_date" class="invoice_date"
                                value="{{ $invoice_date }}" tabindex="2" />
                        </div>
                    </div>
                    @if ($page == 'purchase_order')
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon5">Purchase Order ID</span>
                                <x-Form.input type="text" id="purchase_order_id" name="purchase_order_id"
                                    value="{{ $purchase_order_id }}" readonly />
                            </div>
                        </div>
                    @endif
                </div>
                @if ($page == 'edit_purchase')
                    <input type="hidden" name="prev_grand_total" id="prev_grand_total" value="{{$prev_grand_total}}">
                    @endif
            </div>
            </div>
            <br />
            <div id="prebuiltbilldiv" style="display:block;">
                <table id="mytable">
                    <thead>
                        <tr>
                            <th width="2%">SI. No.</th>
                            <th width="9%">Product</th>
                            <th width="9%">Buy Cost</th>
                            <th width="9%">{{$tax}}(%)</th>
                            <th width="9%">Rate</th>
                            <th width="9%">Selling Cost</th>
                            <th width="10%">Mode</th>

                            <th width="18%" id="select_qun" colspan="2">Quantity</th>
                            <th width="9%" id="box_dozen_header" style="display: none;">Box / Dozen</th>
                            <th width="9%" id="items_header" style="display: none;">Items</th>
                            <th width="18%" id="quantity_header" style="display: none;" colspan="2">Quantity
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
                                    <select id="product" name="product" class="product-list product"
                                        style="width: 300px;" onclick="productlist(this.value)"
                                        onkeydown="moveToRadioGroup(event)" tabindex="3">
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->product_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="buycost" id="buycost" class="form-control"
                                    tabindex="4">
                            </td>
                            <td>
                                <input type="text" name="vat" id="vat" class="form-control"
                                    tabindex="5">
                            </td>
                            <td>

                                <input type="text" name="rate" id="rate" class="form-control"
                                    tabindex="6" readonly>
                            </td>
                            <td>
                                <input type="text" name="sellingcost" id="sellingcost" class="form-control"
                                    tabindex="7">
                            </td>
                            <td>
                                <div class="input-group">
                                    <select name="mode" class="quantity-list"
                                        onchange="addExtraColumns(this.value)" style="width: 150px;" tabindex="8">
                                        <option value="">Select Mode </option>
                                        <option value="3">Quantity</option>
                                        <option value="1">Box</option>
                                        <option value="2">Dozen</option>

                                    </select>
                                </div>
                            </td>
                            <td id="empty_Col" colspan="2"></td>
                            <td id="boxDozenNo" style="display: none;"></td>
                            <td id="itemColumn" style="display: none;"></td>
                            <td colspan="2" id="QuantityColumn" style="display: none;"></td>

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
                            <td colspan="12"> <i class="glyphicon glyphicon-tags"></i> &nbsp {{ $text }}
                            </td>
                        </tr>
                        @foreach ($details as $detail)
                            @if ($detail->status === 0)
                                @php
                                    $stylll = 'border:5px solid red;';
                                @endphp
                            @elseif ($detail->status === 1)
                                @php
                                    $stylll = '';
                                @endphp
                            @endif

                            @if ($page == 'edit_purchase')
                                @if ($detail->quantity == $detail->sell_quantity)
                                    @php
                                        $read = '';
                                        $styling = '';
                                        $disabled = '';
                                        $disabledClass = '';
                                    @endphp
                                @else
                                    @php
                                        $read = 'readonly';
                                        $styling = 'border: 2px solid red !important;';
                                        $disabled = 'disabled';
                                        $disabledClass = 'disabled-row';
                                    @endphp
                                @endif
                                <tr class="{{ $disabledClass }}" style="{{ $styling }} {{ $stylll }}"
                                    {{ $disabled }}>
                                @elseif ($page == 'purchase_order' || $page == 'edit_purchase_draft')
                                <tr style="{{ $stylll }}">
                            @endif

                            <td></td>
                            <td>
                                <input type="text" name="productName[{{ $detail->product }}]"
                                    value="{{ $detail->product_name }}" class="form-control" readonly required
                                    @if ($page == 'edit_purchase') {{ $read }} @endif
                                    @if ($detail->status === 0) readonly @endif />

                                <input type="hidden" name="productId[{{ $detail->product }}]"
                                    value="{{ $detail->product }}" class="form-control" readonly required
                                    @if ($page == 'edit_purchase') {{ $read }} @endif
                                    @if ($detail->status === 0) readonly @endif />

                                <input type="hidden" name="productStatus[{{ $detail->product }}]"
                                    value="{{ $detail->status }}" class="form-control" required
                                    @if ($detail->status === 0) readonly @endif />
                            </td>
                            <td>
                                <input type="text" name="buy_cost[{{ $detail->product }}]"
                                    value="{{ $detail->buycost }}" class="form-control"
                                    @if ($page == 'edit_purchase') {{ $read }} @endif
                                    @if ($detail->status === 0) readonly @endif />
                            </td>
                            <td>
                                <input type="text" name="vat_r[{{ $detail->product }}]"
                                    value="{{ $detail->vat }}" class="form-control"
                                    @if ($page == 'edit_purchase') {{ $read }} @endif
                                    @if ($detail->status === 0) readonly @endif />
                            </td>
                            <td>
                                <input type="text" name="rate_r[{{ $detail->product }}]"
                                    value="{{ $detail->rate }}" class="form-control rate-cal" readonly
                                    @if ($detail->status === 0) readonly @endif />
                            </td>
                            <td>
                                <input type="text" name="sell_cost[{{ $detail->product }}]"
                                    value="{{ $detail->sellingcost }}" class="form-control"
                                    @if ($page == 'edit_purchase') {{ $read }} @endif
                                    @if ($detail->status === 0) readonly @endif />
                            </td>
                            <td>
                                @if ($detail->is_box_or_dozen == 1 || $detail->is_box_or_dozen == 2)
                                    <input type="text" class="form-control mode-input"
                                        value="{{ $detail->is_box_or_dozen == 1 ? 'Box' : 'Dozen' }}" readonly
                                        @if ($detail->status === 0) readonly @endif>

                                    <input type="hidden" name="boxdozen[{{ $detail->product }}]"
                                        value="{{ $detail->is_box_or_dozen }}" readonly
                                        @if ($detail->status === 0) readonly @endif />
                                @elseif ($detail->is_box_or_dozen == 3)
                                    <input type="text" class="form-control mode-input" value="Quantity" readonly
                                        @if ($detail->status === 0) readonly @endif />

                                    <input type="hidden"name="boxdozen[{{ $detail->product }}]"
                                        value="{{ $detail->is_box_or_dozen }}" readonly
                                        @if ($detail->status === 0) readonly @endif />
                                @endif
                            </td>

                            @php
                                $name1 = '';
                                $name2 = '';
                                $boxOrDozen = $detail->is_box_or_dozen;
                                $readin = '';

                            @endphp

                            @if ($boxOrDozen == 1)
                                @php
                                    $name1 = 'boxCount';
                                    $name2 = 'boxItem';
                                    $readin = '';
                                @endphp
                            @elseif ($boxOrDozen == 2)
                                @php
                                    $name1 = 'dozenCount';
                                    $name2 = 'dozenItem';
                                    $readin = 'readonly';
                                @endphp
                            @elseif ($boxOrDozen == 3)
                                @php
                                    $name2 = 'boxItem';
                                @endphp
                            @endif

                            @if ($boxOrDozen == 1 || $boxOrDozen == 2)
                                <td>
                                    <input type="text" name="{{ $name1 }}[{{ $detail->product }}]"
                                        value="{{ $detail->box_dozen_count }}" class="form-control"
                                        @if ($page == 'edit_purchase') {{ $read }} @endif
                                        @if ($detail->status === 0) readonly @endif />
                                </td>
                            @endif

                            <td @if ($boxOrDozen == 3) colspan="2" @endif>
                                <input type="text" name="{{ $name2 }}[{{ $detail->product }}]"
                                    value="{{ $detail->quantity }}" class="form-control" {{ $readin }}
                                    @if ($page == 'edit_purchase') {{ $read }} @endif
                                    @if ($detail->status === 0) readonly @endif>
                            </td>

                            <td>
                                <input type="text" name="unit[{{ $detail->product }}]"
                                    value="{{ $detail->unit }}" class="form-control" readonly
                                    @if ($detail->status === 0) readonly @endif />
                            </td>
                            <td>
                                <input type="text" name="without_vat[{{ $detail->product }}]"
                                    value="{{ $detail->price_without_vat }}"
                                    class="form-control total-cal total_without_vat " readonly
                                    @if ($detail->status === 0) readonly @endif />
                            </td>
                            <td>
                                <input type="text" name="total[{{ $detail->product }}]"
                                    value="{{ $detail->price }}" class="form-control total-cal total-amount" readonly
                                    @if ($detail->status === 0) readonly @endif />

                                <input type="hidden" name="product_id[{{ $detail->product }}]"
                                    value="{{ $detail->product }}" class="form-control" readonly
                                    @if ($detail->status === 0) readonly @endif />
                            </td>
                            <td>
                            </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> <br />
            <div class="row">
                <div class="col-sm-7">
                    <div class="discount_row">
                        <div class="checkbox-label">
                            <label  for="total_discount">Total discount</label>&nbsp;

                            <!-- Dropdown -->
                            <select  name="total_discount" id="total_discount" class="form-control custom-select-no-padding"
                                style="width: 80px; margin-top: -5px;" @if ($page == 'edit_purchase' && $returnstatus == 1) disabled @endif>
                                <option value="0" {{ empty($discount_amount) ? 'selected' : '' }}>No</option>
                                {{-- <option value="1">%</option> --}}
                                <option value="2" {{ !empty($discount_amount) ? 'selected' : '' }}>{{ $currency }}</option>
                            </select>
                        </div>

                        <!-- Percentage Discount Field -->
                        {{-- <div id="discount_field_percentage" class="group_dis {{ empty($discount_percentage) ? 'hidden' : '' }}"
                            style="margin-top: 10px;">
                            <input type="number" id="discount_percentage" name="discount_percentage"
                                value="{{ $discount_percentage ?? '' }}" {{ empty($discount_percentage) ? 'disabled' : '' }}>
                            <span>%</span>
                        </div> --}}

                        <!-- Fixed Amount Discount Field -->
                        <div id="discount_field_amount" class="group_dis {{ !empty($discount) ? '' : 'hidden' }}"
                            >
                            <input  type="text" id="discount_amount" name="discount_amount"
                                value="{{ $discount ?? '' }}" {{ !empty($discount) ? '' : 'disabled' }} @if ($page == 'edit_purchase' && $returnstatus == 1) readonly @endif>
                            <span >{{ $currency }}</span>
                        </div>
                    </div>
                </div>

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
        </form>
        <script src="{{ asset('javascript/editpurchase.js') }}"></script>
    </div>
</body>

</html>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inputModalLabel"> {{ $modeltext }} {{ $receipt_no }}</h5>
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
    $(document).ready(function() {

        $('.rate-cal').each(function() {
            var id = $(this).closest('tr').find('input[name^="productId["]').val();
            rateCalculation(id);
        });

        $('.total-cal').each(function() {
            var id = $(this).closest('tr').find('input[name^="productId["]').val();
            updateTotalRowAmount(id);
        });

        updateTotalAmount();
    });
</script>

<script type="text/javascript">
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

    $('#product').change(function() {
        var selectedValue = $(this).val();

        // Check if the selected option is the default one
        if (selectedValue === "") {
            // Reset other fieldsselectedValue
            var nu = "";
            $("#product_name").val(nu);
            $("#product_id").val(nu);
            $("#buycost").val(nu);
            $("#sellingcost").val(nu);
            $("#unit").val(nu);
            $("#total").val(nu);
            $('#boxselect').val(nu);
            $('#boxselectenter').val(nu);
            $('#dozenselect').val(nu);
            $('#dozenselectenter').val(nu);
            $("#without_vat").val(nu);
            $("#rate").val(nu);
            $("#vat").val(nu);
        }
    });
</script>

<script type="text/javascript">
    var addedProducts = [];

    function updateTotalAmount() {
        var total = 0;
        var totalwithoutvat = 0;

        $('.total-amount').each(function() {
            total += Number($(this).val());
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

        $('.total_without_vat').each(function() {
            totalwithoutvat += Number($(this).val());
        });

        $('#price_without_vat').val(totalwithoutvat);
        /*-------------------*/
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

        // Check if the product is already in the addedProducts array
        if (addedProducts.includes(selectedProductId) || alreadySoldProducts.includes(parseInt(selectedProductId))) {
            // Product is already added, show an alert message
            alert('Product is either already added or purchased in this transaction!');
            $("#product").val(null).trigger('change');
            return;
        }

        var selectedMode = $("select[name='mode']").val();

        var boxSelect = $('#boxselect').val();
        var boxSelectEnter = $('#boxselectenter').val();
        var dozenSelect = $('#dozenselect').val();
        var dozenSelectEnter = $('#dozenselectenter').val();

        if ((($("#product").val()) == "") || (!selectedMode) || (selectedMode === '1' && (!boxSelect || !
                boxSelectEnter)) || (selectedMode === '2' && (!dozenSelect || !
                dozenSelectEnter)) || (selectedMode === '3' && (!
                boxSelectEnter))) {
            return;
        }

        var name = ($("#product_name").val());
        var buycost = ($("#buycost").val());
        var sellcost = ($("#sellingcost").val());
        var Is_box_dozen = $("select[name='mode']").val();
        var unit = ($("#unit").val());
        var pid = Number($("#product_id").val());
        var tot = ($("#total").val());
        var tot_without_vat = ($("#without_vat").val());

        var rate = ($("#rate").val()) || 0;
        var vat = ($("#vat").val()) || 0;

        var tr = '<tr>' + '<td>' + serialNumber + '</td>' + '<td>' +
            '<input type="text" id="productnamevalue" value="' + name + '" name="productName[' + pid +
            ']" class="form-control" readonly> ' +
            '<input type="hidden"  value="' + pid + '" name="productId[' + pid + ']" class="form-control"></td>' +
            '<td><input type="text" value=' + buycost + ' id="buy_cost" name="buy_cost[' + pid +
            ']" class="form-control" readonly> </td>' +
            '<td><input type="text" value=' + vat + ' id="vat_r" name="vat_r[' + pid +
            ']" class="form-control" readonly> </td>' +
            '<td><input type="text" value=' + rate + ' id="rate_r" name="rate_r[' + pid +
            ']" class="form-control" readonly> </td>' +
            '<td><input type="text" value=' + sellcost + ' id="sell_cost" name="sell_cost[' + pid +
            ']" class="form-control" readonly></td>' +
            '<td>';

        if (selectedMode === '1' || selectedMode === '2') {

            tr += '<input type="text" class="form-control mode-input" value="' + (selectedMode === '1' ? 'Box' :
                'Dozen') + '" readonly>';
            tr += '<input type="hidden" name="boxdozen[' + pid + ']" value="' + selectedMode + '">';

        } else if (selectedMode === '3') {

            tr += '<input type="text" class="form-control mode-input" value="Quantity" readonly>';
            tr += '<input type="hidden" name="boxdozen[' + pid + ']" value="' + selectedMode + '">';
        }

        tr += '</td>';

        if (selectedMode === '1') {
            tr += '<td><input type="text" value="' + boxSelect + '" name="boxCount[' + pid +
                ']" class="form-control" readonly></td>';
            tr += '<td><input type="text" value="' + boxSelectEnter + '" name="boxItem[' + pid +
                ']" class="form-control" readonly></td>';
        } else if (selectedMode === '2') {
            tr += '<td><input type="text" value="' + dozenSelect + '" name="dozenCount[' + pid +
                ']" class="form-control" readonly></td>';
            tr += '<td><input type="text" value="' + dozenSelectEnter + '" name="dozenItem[' + pid +
                ']" class="form-control" readonly></td>';

        } else if (selectedMode === '3') {

            tr += '<td colspan="2"><input type="text" value="' + boxSelectEnter + '" name="boxItem[' + pid +
                ']" class="form-control" readonly></td>';
        }

        tr += '<td><input type="text" value="' + unit + '" name="unit[' + pid +
            ']" class="form-control" readonly></td>' +
            '<td><input type="text" value="' + tot_without_vat + '" name="without_vat[' + pid +
            ']" class="form-control total_without_vat" readonly></td>' +
            '<td><input type="text" value="' + tot + '" name="total[' + pid +
            ']" class="form-control total-amount" readonly></td>' +
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

<script type="text/javascript">
    $('form input:not([type="submit"])').keydown((e) => {
        if (e.keyCode === 13) {
            e.preventDefault();
            return false;
        }
        return true;
    });

    var alreadySoldProducts = [];

    // Populate alreadySoldProducts array with product IDs from existing sold products
    @foreach ($details as $detail)
        alreadySoldProducts.push({{ $detail->product }});
    @endforeach
</script>

<script>
    // Event listener for modal confirm button
    document.getElementById("confirmBtn").addEventListener("click", function() {
        // Check if the additional input field is not empty
        if (document.getElementById("additionalInput").value.trim() !== "") {
            // Get the additional input value
            const additionalData = document.getElementById("additionalInput").value;

            // Set the additional data to the hidden input field
            document.getElementById("edit_comment").value = additionalData;
            // Submit the form
            document.getElementById("edit_purchase_form").submit();
        } else {
            alert("Please enter additional data.");
        }
    });

    // Event listener for modal hidden event
    $("#myModal").on("hidden.bs.modal", function() {
        // Re-enable the submit button
        document.getElementById("submitBtn").disabled = false;
    });
</script>
<script>
function validateForm() {

    var payment_type = $("#payment_type").val();

    var totalBalance = 0;

    // Get the current balance and price, set default to 0 if invalid or empty
    var currentBalance = parseFloat(document.getElementById('current_balance').value) || 0;
    var price = parseFloat(document.getElementById('price').value) || 0;
    var page = $('#page').val();

    // Ensure the 'page' variable is defined or passed in properly
    if (page === 'edit_purchase') {
        var prev_grand_total = parseFloat(document.getElementById('prev_grand_total').value) || 0;
        totalBalance = currentBalance + prev_grand_total;
    } else {
        totalBalance = currentBalance;
    }

    // Check if totalBalance is less than the price
    if (payment_type == 3 && totalBalance < price) {
        alert('Insufficient balance!');
        return false;
    }

    return true;
}


</script>
<script>
    function EditTotalBillDiscount() {
       // Get initial values from the DOM
       var initialPercentage = $("#discount_percentage").val();
       var initialAmount = $("#discount_amount").val();

       // Handle dropdown change
       $("#total_discount").change(function () {
           var total_discount = $(this).val();

           // if (total_discount == 1) { // Percentage discount selected
           //     $("#discount_field_percentage").removeClass("hidden");
           //     $("#discount_field_amount").addClass("hidden");

           //     $("#discount_percentage").prop("disabled", false).val(initialPercentage);
           //     $("#discount_amount").prop("disabled", true).val("");
           // } else
            if (total_discount == 2) { // Fixed amount discount selected
               $("#discount_field_amount").removeClass("hidden");
               $("#discount_field_percentage").addClass("hidden");

               $("#discount_amount").prop("disabled", false).val(initialAmount);
               $("#discount_percentage").prop("disabled", true).val("");
           } else { // No discount
               $("#discount_field_percentage").addClass("hidden");
               $("#discount_field_amount").addClass("hidden");

               $("#discount_percentage, #discount_amount")
                   .val("")
                   .prop("disabled", true);
           }

           // Update the total amount (if implemented)
           updateTotalAmount();
       });

       // // Handle percentage input
       // $("#discount_percentage").on("input", function () {
       //     if ($(this).val() !== "") {
       //         $("#discount_amount").val("");
       //     }
       //     updateGrandTotalAmount();
       // });

       // Handle amount input
       $("#discount_amount").on("input", function () {
           if ($(this).val() !== "") {
               $("#discount_percentage").val("");
           }
           updateTotalAmount();
       });
   }

   // Call the function after the DOM is ready
   $(document).ready(function () {
       EditTotalBillDiscount();
   });

    </script>
