<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Plexpay billing">
    <title>Convert To Billing</title>
    @include('layouts/usersidebar')

    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="/css/table_style.css">
    <link rel="stylesheet" href="{{ asset('css/bill.css') }}">
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
        <div align="right">
            @include('layouts.quick')

            <a href="" class="btn btn-info">Refresh</a>
        </div>

        <x-admindetails_user :shopdatas="$shopdatas" />

        <form method="post" action="" onsubmit="return validateForm();" id="billForm">
            @csrf

            <input type="hidden" name="edit_comment" id="edit_comment">

            <div class="form group row">
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">Customer ID</span>
                            <x-Form.input type="text" style="width: 150px;" id="cust_id" name="customer_name"
                                class="customer_id" placeholder="Customer ID:" value="{{ $customer_name }}" />

                            <input type="text" name="page" id="page" value="{{ $page }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">TRN Number</span>
                            <x-Form.input type="text" style="width: 150px;" id="trn_number" name="trn_number"
                                class="trn_no" placeholder="TRN Number:" value="{{ $trn_number }}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">Phone Number</span>
                            <x-Form.input type="text" style="width: 150px;" id="phone" name="phone"
                                class="phone" placeholder="Phone:" value="{{ $phone }}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1" style="width: 90px;">Email</span>
                            <x-Form.input type="text" style="width: 170px;" id="email" name="email"
                                class="email" value="{{ $email }}" />
                        </div>
                    </div>
                </div>
                <br /> <br />
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">Transaction ID</span>
                            <x-Form.input type="text" style="width: 150px;" id="transaction_id"
                                value="{{ $transaction_id }}" name="transaction_id" readonly />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="input-group">

                            @if ($payment_type == 1)
                                <?php $type = 'CASH'; ?>
                            @elseif ($payment_type == 2)
                                <?php $type = 'BANK'; ?>
                            @elseif ($payment_type == 3)
                                <?php $type = 'CREDIT'; ?>
                            @elseif ($payment_type == 4)
                                <?php $type = 'POST CARD'; ?>
                            @endif

                            <span class="input-group-addon" id="basic-addon1" style="width: 90px;">Payment Mode</span>
                            <x-Form.input type="text" style="width: 150px;" id="payment_mode" name="payment_mode"
                                value="{{ $type }}" readonly />
                            <x-Form.input type="hidden" style="width: 150px;" id="payment_type" name="payment_type"
                                value="{{ $payment_type }}" readonly />

                            @if ($payment_type == 3)
                                <x-Form.input type="text" style="width: 150px;" id="credit_id" name="credit_id"
                                    value="{{ $credit_user_id }}" readonly />
                            @elseif ($payment_type == 1 || $payment_type == 2 || $payment_type == 4)
                                <x-Form.input type="text" style="width: 150px;" id="credit_id" name="credit_id"
                                    value="{{ $cash_user_id }}" readonly />
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        @if ($payment_type == 3)
                            <x-Form.input type="text" style="width: 280px;" id="user_id" name="user_id"
                                value="{{ $credit_user_name }}" readonly />
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
                        <table class="table">
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
                                <tr bgcolor="#20639B">
                                    <td></td>
                                    <td>
                                        <div id="pselect">
                                            <select onclick="doSomething(this.value)" id="product"
                                                class="product-list" style="width: 250px">
                                                <option value="">Select Product</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item['id'] }}">{{ $item['product_name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="1" id="qty" class="form-control qty"
                                            min="1" max="" onkeyup="checkquantity();" tabindex="1">
                                    </td>
                                    <td>
                                        <input type="text" name="prounit" id="prounit" class="form-control"
                                            readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" id="mrp" class="form-control"
                                            tabindex="2">
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
                                        <input type="text" id="pricex" class="form-control">
                                    </td>
                                    <td>
                                        <input type="number" step="any" id="price_wo_discount"
                                            class="form-control" readonly>
                                        <input type="text" step="any" id="total_wo_discount"
                                            class="form-control" readonly>
                                    </td>

                                    <td><a href="#" class="btn btn-info addRow" title="Add Row">+</a></td>
                                    <input type="hidden" id="product_id" class="form-control">
                                    <input type="hidden" id="product_name" class="form-control">
                                </tr>
                                <tr>
                                    <td colspan="12"><i class="glyphicon glyphicon-tags"></i> &nbsp EDIT BILL</td>
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
                                                value="{{ $detail->product_name }}" class="form-control" readonly
                                                required />

                                            <input type="hidden" name="productId[{{ $detail->product_id }}]"
                                                value="{{ $detail->product_id }}" class="form-control" required />

                                            <input type="text" name="productStatus[{{ $detail->product_id }}]"
                                                value="{{ $detail->status }}" class="form-control" required />
                                        </td>
                                        <td>
                                            <input type="text" name="quantity[{{ $detail->product_id }}]"
                                                value="{{ $detail->quantity }}" class="form-control quantity-input"
                                                @if ($detail->status === 0) readonly @endif required />

                                            <span id="quantity_error_{{ $detail->product_id }}"
                                                class="text-danger"></span>
                                        </td>
                                        <td>
                                            <input type="text" name="prounit[{{ $detail->product_id }}]"
                                                value="{{ $detail->unit }}" class="form-control" readonly required />
                                        </td>
                                        <td>
                                            <input type="text" name="mrp[{{ $detail->product_id }}]"
                                                value="{{ $detail->mrp }}" class="form-control mrp-edit"
                                                @if ($detail->status === 0) readonly @endif required />

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
                                                    readonly />
                                            </td>
                                        @elseif ($vattype == 2)
                                            <td>
                                                <input type="text"
                                                    name="rate_discount_r[{{ $detail->product_id }}]"
                                                    value="{{ $detail->exclusive_rate }}" class="form-control"
                                                    readonly />
                                            </td>
                                        @endif
                                        <td>
                                            <input type="text" name="dis_count[{{ $detail->product_id }}]"
                                                id="discountInput"
                                                value="{{ $detail->discount_type === 'none' ? 0 : ($detail->discount_type === 'percentage' ? $detail->discount : $detail->discount_amount) }}"
                                                class="form-control"
                                                {{ $detail->discount_type === 'none' ? 'readonly' : '' }}
                                                @if ($detail->status === 0) readonly @endif />

                                            @if ($detail->status === 1)
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
                                            @elseif ($detail->status === 0)
                                                <input type="text"
                                                    name="dis_count__typee[{{ $detail->product_id }}]"
                                                    value="{{ $detail->discount_type === 'none' ? 'none' : ($detail->discount_type === 'percentage' ? '%' : $currency) }}"
                                                    class="form-control"
                                                    @if ($detail->status === 0) readonly @endif />

                                                <input type="hidden"
                                                    name="dis_count__tp_ori[{{ $detail->product_id }}]"
                                                    value="{{ $detail->discount_type === 'none' ? 'none' : ($detail->discount_type === 'percentage' ? 'percentage' : 'amount') }}"
                                                    class="form-control"
                                                    @if ($detail->status === 0) readonly @endif />
                                            @endif
                                        </td>
                                        <td>
                                            <input type="text" name="fixed_vat[{{ $detail->product_id }}]"
                                                value="{{ $detail->fixed_vat }}" class="form-control"
                                                @if ($detail->status === 0) readonly @endif required />
                                        </td>
                                        <td>
                                            <input type="text" name="vat_amount[{{ $detail->product_id }}]"
                                                value="{{ $detail->vat_amount }}" class="form-control" readonly />
                                        </td>
                                        <td>
                                            <input type="text" name="net_rate[{{ $detail->product_id }}]"
                                                value="{{ $detail->netrate }}" class="form-control" readonly />
                                        </td>
                                        <td>
                                            <input type="text" name="total_amount[{{ $detail->product_id }}]"
                                                value="{{ $detail->total_amount }}" class="form-control total-amount"
                                                readonly />

                                            <input type="text" name="price[{{ $detail->product_id }}]"
                                                value="{{ $detail->price }}" class="form-control" />
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="total_amount_wo_discount[{{ $detail->product_id }}]"
                                                value="{{ $detail->totalamount_wo_discount != '' ? $detail->totalamount_wo_discount : $detail->total_amount }}"
                                                class="form-control total-discount-amount" readonly />

                                            <input type="text"
                                                name="price_withoutvat_wo_discount[{{ $detail->product_id }}]"
                                                value="{{ $detail->price_wo_discount != '' ? $detail->price_wo_discount : $detail->price }}"
                                                class="form-control" />
                                        </td>
                                        <td>
                                            <input type="hidden" name="product_id[{{ $detail->product_id }}]"
                                                value="{{ $detail->product_id }}" class="form-control" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <div class="row">
                        <div class="col-sm-7">
                            <div class="discount_row">
                                <div class="checkbox-label">
                                    <label for="total_discount">Add Total discount</label>
                                    <select name="total_discount" id="total_discount"
                                        class="form-control custom-select-no-padding">
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
                                            @if ($total_discount_type != '1') disabled @endif>
                                        <span style="margin-right: 3px;">%</span>
                                    </div>
                                </div>
                                <div id="discount_field_amount"
                                    class="group_dis @if ($total_discount_type != '2') hidden @endif">
                                    <label for="discount_amount">or</label>
                                    <input type="number" id="discount_amount" name="discount_amount"
                                        value="{{ $total_discount_type != '2' ? '' : $total_discount_amount }}"
                                        @if ($total_discount_type != '2') disabled @endif>
                                    <span>{{ $currency }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group group-grand-total">
                                <span class="input-group-addon align-grand-total" id="basic-addon2"
                                    style="width: 50%;">Grand Total</span>
                                <input type="text" name="bill_grand_total" class="form-control"
                                    id="bill_grand_total" aria-describedby="basic-addon2" readonly>

                                <input type="text" name="bill_grand_total_wo_discount" class="form-control"
                                    id="bill_grand_total_wo_discount" aria-describedby="basic-addon2" readonly>
                            </div>
                            <br />
                        </div>
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
                <h5 class="modal-title" id="inputModalLabel">Do You Want To Make {{ $transaction_id }} Invoice ?</h5>
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
        handleDiscount("#discount_type", "#discount");

        var vat_type = $('#vat_type_value').val();
        handleVatTypeChange(vat_type);

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

            console.log(total_discount);
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
        // Apply quantity change handling to existing rows
        $('.quantity-input').each(function() {
            var u = $(this).closest('tr').find('input[name^="productId["]').val();
            handleQuantityChange(u);
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

{{-- <script>
    $(document).ready(function() {

        var vat_type = $('#vat_type_value').val();

        if (vat_type == 1) {

            /* extra column add */

            var inclusive_header = document.getElementById("inclusive_heading");
            var inclusive_rate_value = document.getElementById("inclusive_rate_value");

            inclusive_header.style.display = "table-cell";

            inclusive_rate_value.innerHTML =
                '<input type="number" step="any" id="inclusive_rate" name="inclusive_rate" class="form-control" readonly>';

            inclusive_rate_value.style.display = "table-cell";

            /*----------------------------------*/

            $('#vat_perc').text('VAT(%)-Inclus');
            $('#vat_ammi').text('Total VAT Amount-Inclus');

            $("#mrp, #fixed_vat").keyup(function() {

                var mrp = Number($("#mrp").val());
                var fixed_vat = Number($("#fixed_vat").val());

                var InclusiveRate = mrp / (1 + (fixed_vat / 100));
                var InclusiveRate = Math.round(InclusiveRate * 1000) / 1000;

                $("#inclusive_rate").val(InclusiveRate);

            });

            $("#qty,#mrp, #discount").keyup(function() {
                var total = 0;
                var total_discount = 0;
                var grandtotal_discount = 0;
                var netrate = Number($("#net_rate").val());
                var quantity = Number($("#qty").val());
                var mrp = Number($("#mrp").val());
                var fixed_vat = Number($("#fixed_vat").val());
                var inc_rate = Number($("#inclusive_rate").val());
                var discounts = Number($("#discount").val());

                if (quantity > 0) {
                    total = inc_rate * quantity;

                    var discount_amt = total * (discounts / 100);
                    total_discount = total - discount_amt;
                } else {
                    total = 0;
                    total_discount = 0;
                }

                inclusive_vate = mrp - inc_rate;
                grandvat = inclusive_vate * quantity;
                netrate = inc_rate + inclusive_vate;

                if (quantity > 0) {
                    grandtotal = netrate * quantity;
                    var discount_amount = grandtotal * (discounts / 100);
                    grandtotal_discount = grandtotal - discount_amount;

                } else {
                    grandtotal = 0;
                    grandtotal_discount = 0;
                }

                grandtotal_discount = Math.round(grandtotal_discount * 1000) / 1000;
                var grandtotal = Math.round(grandtotal * 1000) / 1000;
                var grandvat = Math.round(grandvat * 1000) / 1000;
                var netrate = Math.round(netrate * 1000) / 1000;

                $("#pricex").val(total_discount);
                $("#price").val(grandtotal_discount);
                $("#vat_amount").val(grandvat);
                $("#net_rate").val(netrate);
                $("#price_wo_discount").val(grandtotal);
                $("#total_wo_discount").val(total);
            });

            $("#fixed_vat,#net_rate, #discount").keyup(function() {
                var vat_amount = 0;
                var total = 0;
                var total_discount = 0;
                var grandtotal_discount = 0;

                var quantity = Number($("#qty").val());
                var mrp = Number($("#mrp").val());
                var fixed_vat = Number($("#fixed_vat").val());
                var netrate = Number($("#net_rate").val());
                var inc_rate = Number($("#inclusive_rate").val());
                var discounts = Number($("#discount").val());

                if (quantity > 0) {
                    total = inc_rate * quantity;

                    var discount_amt = total * (discounts / 100);
                    total_discount = total - discount_amt;
                } else {
                    total = 0;
                }

                inclusive_vate = mrp - inc_rate;
                grandvat = inclusive_vate * quantity;
                netrate = inc_rate + inclusive_vate;

                if (quantity > 0) {
                    grandtotal = netrate * quantity;
                    var discount_amount = grandtotal * (discounts / 100);
                    grandtotal_discount = grandtotal - discount_amount;
                } else {
                    grandtotal = 0;
                }

                grandtotal_discount = Math.round(grandtotal_discount * 1000) / 1000;
                var grandtotal = Math.round(grandtotal * 1000) / 1000;
                var grandvat = Math.round(grandvat * 1000) / 1000;
                var netrate = Math.round(netrate * 1000) / 1000;

                $("#pricex").val(total_discount);
                $("#price").val(grandtotal_discount);
                $("#vat_amount").val(grandvat);
                $("#net_rate").val(netrate);
                $("#price_wo_discount").val(grandtotal);
                $("#total_wo_discount").val(total);
            });
        } else if (vat_type == 2) {
            $('#vat_perc').text('VAT(%)-Exclus');
            $('#vat_ammi').text('Total VAT Amount-Exclus');

            $("#qty,#fixed_vat,#mrp").keyup(function() {
                var mrp = Number($("#mrp").val());
                var fixed_vat = Number($("#fixed_vat").val());
                netrate = ((fixed_vat * mrp) / 100) + mrp;
                var netrate = Math.round(netrate * 1000) / 1000;
                $("#net_rate").val(netrate);
            });

            $("#qty,#mrp, #discount").keyup(function() {
                var total = 0;
                var total_discount = 0;
                var grandtotal_discount = 0;
                var netrate = Number($("#net_rate").val());
                var quantity = Number($("#qty").val());
                var mrp = Number($("#mrp").val());
                var fixed_vat = Number($("#fixed_vat").val());
                var discounts = Number($("#discount").val());

                if (quantity > 0) {
                    total = mrp * quantity;

                    var discount_amt = total * (discounts / 100);
                    total_discount = total - discount_amt;
                } else {
                    total = 0;
                    total_discount = 0;
                }

                if (quantity > 0) {
                    grandtotal = netrate * quantity;
                    var discount_amount = grandtotal * (discounts / 100);
                    grandtotal_discount = grandtotal - discount_amount;
                } else {
                    grandtotal = 0;
                    grandtotal_discount = 0;
                }

                grandvat = (fixed_vat * total) / 100;
                netrate = ((fixed_vat * mrp) / 100) + mrp;

                grandtotal_discount = Math.round(grandtotal_discount * 1000) / 1000;
                var grandtotal = Math.round(grandtotal * 1000) / 1000;
                var grandvat = Math.round(grandvat * 1000) / 1000;
                var netrate = Math.round(netrate * 1000) / 1000;

                $("#pricex").val(total_discount);
                $("#price").val(grandtotal_discount);
                $("#vat_amount").val(grandvat);
                $("#net_rate").val(netrate);
                $("#price_wo_discount").val(grandtotal);
                $("#total_wo_discount").val(total);
            });

            $("#fixed_vat,#net_rate").keyup(function() {
                var rate = 0;
                var vat_amount = 0;
                var fixed_vat = Number($("#fixed_vat").val());
                var net_rate = Number($("#net_rate").val());
                a = (fixed_vat / 100);
                b = a + 1;
                c = net_rate / b;
                rate = c;
                var rate = Math.round(rate * 1000) / 1000;
                $("#mrp").val(rate);
            });

            $("#fixed_vat,#net_rate, #discount").keyup(function() {
                var vat_amount = 0;
                var total = 0;
                var total_discount = 0;
                var grandtotal_discount = 0;

                var quantity = Number($("#qty").val());
                var mrp = Number($("#mrp").val());
                var fixed_vat = Number($("#fixed_vat").val());
                var netrate = Number($("#net_rate").val());
                var discounts = Number($("#discount").val());

                if (quantity > 0) {
                    total = quantity * mrp;

                    var discount_amt = total * (discounts / 100);
                    total_discount = total - discount_amt;
                } else {
                    total = 0;
                    total_discount = 0;
                }

                if (quantity > 0) {
                    grandtotal = netrate * quantity;

                    var discount_amount = grandtotal * (discounts / 100);
                    grandtotal_discount = grandtotal - discount_amount;
                } else {
                    grandtotal = 0;
                    grandtotal_discount = 0;
                }

                grandvat = (fixed_vat * total) / 100;
                netrate = ((fixed_vat * mrp) / 100) + mrp;

                grandtotal_discount = Math.round(grandtotal_discount * 1000) / 1000;
                var grandtotal = Math.round(grandtotal * 1000) / 1000;
                var grandvat = Math.round(grandvat * 1000) / 1000;
                var netrate = Math.round(netrate * 1000) / 1000;

                $("#pricex").val(total_discount);
                $("#price").val(grandtotal_discount);
                $("#vat_amount").val(grandvat);
                $("#net_rate").val(netrate);
                $("#price_wo_discount").val(grandtotal);
                $("#total_wo_discount").val(total);
            });
        }

        $('#product').change(function() {
            var selectedValue = $(this).val();

            // Check if the selected option is the default one
            if (selectedValue === "") {
                // Reset other fieldsselectedValue
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
                if (vat_type == 1) {
                    $("#inclusive_rate").val(nu);
                }
                $("#buycost_rate").val(nu);
                $("#discount").val(nu);
                $("#price_wo_discount").val(nu);
                $("#total_wo_discount").val(nu);
            }
        });
    });
</script> --}}

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
    function handleQuantityChange(u) {

        var array = @json($items);
        var item = array.find(item => item.id == u);

        if (item) {
            var remstock = item.remaining_stock;
            var remstk = Number(remstock);
        } else {
            console.log("Item not found in the array.");
        }

        // var remstock = array.find(item => item.id == u).remaining_stock;
        // var remstk = Number(remstock);

        /*-----------------------------------------------*/

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

        /*-----------------------------------------------*/

        $('input[name="quantity[' + u + ']"], input[name="dis_count[' + u + ']"]').on('input', function() {

            var pro = $('input[name="quantity[' + u + ']"]').val();

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

            // $('input[name="quantity[' + u + ']"], input[name="dis_count[' + u + ']"]').keyup(function() {
            //     var vat_type = $('input[name="vat_type_value"]').val();

            //     var total = 0;
            //     var total_discount = 0;
            //     var grandtotal = 0;
            //     var grandtotal_discount = 0;
            //     var netrt = Number($('input[name="net_rate[' + u + ']"]').val());
            //     var quantty = Number($('input[name="quantity[' + u + ']"]').val());
            //     var mrp = Number($('input[name="mrp[' + u + ']"]').val());
            //     var fixed_vat = Number($('input[name="fixed_vat[' + u + ']"]').val());
            //     var discounts = Number($('input[name="dis_count[' + u + ']"]').val());

            //     if (vat_type == 1) {

            //         var inc_rate = Number($('input[name="inclusive_rate_r[' + u + ']"]').val());

            //         inclusive_vate = mrp - inc_rate;
            //         grandvat = inclusive_vate * quantty;
            //         var netrate = inc_rate + inclusive_vate;
            //         netrate = Math.round(netrate * 1000) / 1000;

            //         if (quantty > 0) {
            //             grandtotal = netrate * quantty;
            //             var discount_amount = grandtotal * (discounts / 100);
            //             grandtotal_discount = grandtotal - discount_amount;
            //         } else {
            //             grandtotal = 0;
            //             grandtotal_discount = 0;
            //         }

            //         $('input[name="net_rate[' + u + ']"]').val(netrate);

            //         if (quantty > 0) {
            //             total = inc_rate * quantty;
            //             var discount_amt = total * (discounts / 100);
            //             total_discount = total - discount_amt;
            //         } else {
            //             total = 0;
            //             total_discount = 0;
            //         }

            //         $('input[name="price[' + u + ']"]').val(total_discount);
            //         $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val(total);

            //     } else if (vat_type == 2) {

            //         if (quantty > 0) {
            //             total = mrp * quantty;
            //             var discount_amt = total * (discounts / 100);
            //             total_discount = total - discount_amt;
            //         } else {
            //             total = 0;
            //             total_discount = 0;
            //         }

            //         $('input[name="price[' + u + ']"]').val(total_discount);
            //         $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val(total);

            //         if (quantty > 0) {
            //             grandtotal = netrt * quantty;
            //             var discount_amount = grandtotal * (discounts / 100);
            //             grandtotal_discount = grandtotal - discount_amount;
            //         } else {
            //             grandtotal = 0;
            //             grandtotal_discount = 0;
            //         }

            //         grandvat = (fixed_vat * total) / 100;
            //     }

            //     var grandvat = Math.round(grandvat * 1000) / 1000;
            //     var grandtotal = Math.round(grandtotal * 1000) / 1000;
            //     grandtotal_discount = Math.round(grandtotal_discount * 1000) / 1000;

            //     $('input[name="vat_amount[' + u + ']"]').val(grandvat);
            //     $('input[name="total_amount[' + u + ']"]').val(grandtotal_discount);
            //     $('input[name="total_amount_wo_discount[' + u + ']"]').val(grandtotal);

            //     updateGrandTotalAmount();
            // });

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
        var item = array.find(item => item.id == u);
        if (item) {
            var remstock = item.remaining_stock;
            var remstk = Number(remstock);
        } else {
            console.log("Item not found in the array.");
        }

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

        addRowDiscountCalculation(u, remstk, vat_type, 0, page);
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

        // var totalamount = netrate * x;
        // var totalamount = Math.round(totalamount * 1000) / 1000;

        var tr = '<tr>' + '<td></td>' + '<td>' +
            '<input type="text" id="productnamevalue" value="' + y + '" name="productName[' + u +
            ']" class="form-control" readonly> <input type="hidden"  value="' + u + '" name="productId[' + u +
            ']" class="form-control">' +
            '</td>' +
            '<td id="barquan"><input type="text" value=' + x + ' id="quantityrow" name="quantity[' + u +
            ']" class="form-control"></td>' +
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
            ']" class="form-control total-amount" readonly><input type="text" value=' + p +
            ' id="rowprice" name="price[' + u + ']" class="form-control"></td>' +
            '<td><input type="number" value=' + price_dis +
            ' id="total_amount_wo_discount" name="total_amount_wo_discount[' + u +
            ']" class="form-control total-discount-amount" readonly>' +
            '<input type="text" value=' + total_disc +
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

        // $('input[name="quantity[' + u + ']"], input[name="dis_count[' + u + ']"]').on('input', function() {

        //     var pro = $('input[name="quantity[' + u + ']"]').val();

        //     if (pro > remstk) {

        //         $('input[name="quantity[' + u + ']"]').attr("max", remstk);
        //         $('input[name="quantity[' + u + ']"]').val(remstk);
        //     }

        //     $('input[name="quantity[' + u + ']"], input[name="dis_count[' + u + ']"]').keyup(function() {
        //         var vat_type = $('input[name="vat_type_value"]').val();

        //         var total = 0;
        //         var total_discount = 0;
        //         var grandtotal = 0;
        //         var grandtotal_discount = 0;
        //         var netrt = Number($('input[name="net_rate[' + u + ']"]').val());
        //         var quantty = Number($('input[name="quantity[' + u + ']"]').val());
        //         var mrp = Number($('input[name="mrp[' + u + ']"]').val());
        //         var fixed_vat = Number($('input[name="fixed_vat[' + u + ']"]').val());
        //         var discounts = Number($('input[name="dis_count[' + u + ']"]').val());

        //         if (vat_type == 1) {

        //             var inc_rate = Number($('input[name="inclusive_rate_r[' + u + ']"]').val());

        //             inclusive_vate = mrp - inc_rate;
        //             grandvat = inclusive_vate * quantty;
        //             var netrate = inc_rate + inclusive_vate;
        //             netrate = Math.round(netrate * 1000) / 1000;

        //             if (quantty > 0) {
        //                 grandtotal = netrate * quantty;
        //                 var discount_amount = grandtotal * (discounts / 100);
        //                 grandtotal_discount = grandtotal - discount_amount;
        //             } else {
        //                 grandtotal = 0;
        //                 grandtotal_discount = 0;
        //             }

        //             $('input[name="net_rate[' + u + ']"]').val(netrate);

        //             if (quantty > 0) {
        //                 total = inc_rate * quantty;
        //                 var discount_amt = total * (discounts / 100);
        //                 total_discount = total - discount_amt;
        //             } else {
        //                 total = 0;
        //                 total_discount = 0;
        //             }

        //             $('input[name="price[' + u + ']"]').val(total_discount);
        //             $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val(total);

        //         } else if (vat_type == 2) {

        //             if (quantty > 0) {
        //                 total = mrp * quantty;
        //                 var discount_amt = total * (discounts / 100);
        //                 total_discount = total - discount_amt;
        //             } else {
        //                 total = 0;
        //                 total_discount = 0;
        //             }

        //             $('input[name="price[' + u + ']"]').val(total_discount);
        //             $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val(total);

        //             if (quantty > 0) {
        //                 grandtotal = netrt * quantty;
        //                 var discount_amount = grandtotal * (discounts / 100);
        //                 grandtotal_discount = grandtotal - discount_amount;
        //             } else {
        //                 grandtotal = 0;
        //                 grandtotal_discount = 0;
        //             }

        //             grandvat = (fixed_vat * total) / 100;
        //         }

        //         var grandvat = Math.round(grandvat * 1000) / 1000;
        //         var grandtotal = Math.round(grandtotal * 1000) / 1000;
        //         grandtotal_discount = Math.round(grandtotal_discount * 1000) / 1000;

        //         $('input[name="vat_amount[' + u + ']"]').val(grandvat);
        //         $('input[name="total_amount[' + u + ']"]').val(grandtotal_discount);
        //         $('input[name="total_amount_wo_discount[' + u + ']"]').val(grandtotal);

        //         updateGrandTotalAmount();
        //     });
        // });

        var page = $('#page').val();

        addRowDiscountCalculation(u, remstk, $('input[name="vat_type_value"]').val(), 0, page);

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
        }

        var nu = "";
        $("#qty").val(nu);
        $("#price").val(nu);
        $("#vat_amount").val(nu);
        $('#qty').focus();

        $("#discount").val(nu);
        $("#price_wo_discount").val(nu);
        $("#total_wo_discount").val(nu);

        /*-------------CHECK STOCK WHEN ENTERING QUANTITY-----------------------*/

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
        /*-------------------------------------------------------------------------*/
    }

    function checkquantity() {

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
        }
    }

    /* validate plus double submit removal */
    function validateForm() {
        // Prevent the form from submitting multiple times
        const form = document.getElementById("billForm");
        const submitBtn = document.getElementById("submitBtn");

        if (submitBtn.disabled) {
            return false; // Prevent form submission
        }

        // Disable the submit button
        submitBtn.disabled = true;
        submitBtn.innerText = "Submitting...";

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

        // Show the modal
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
