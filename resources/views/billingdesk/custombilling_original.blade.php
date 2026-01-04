<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Plexpay billing">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/css/table_style.css">
    <title>Billing Desk</title>
    @include('layouts/usersidebar')
    <style>
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

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .pl-1 {
            padding-left: 1rem !important;
        }

        .pr-2 {
            padding-right: 2rem !important;
        }

        .custom-select-no-padding {
            padding: 0;
            height: 3rem;
            line-height: 3rem;
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
        <div align="right">
            @include('layouts.quick')

            <a href="" class="btn btn-info">Refresh</a>
        </div>

        <x-admindetails_user :shopdatas="$shopdatas" />

        <form method="post" action="submitdata" onsubmit="return validateForm();" id="billForm">
            @csrf
            <div class="form group row">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">Customer ID</span>
                            <input style="width: 150px;" id="cust_id" name="customer_name"
                                class="form-control customer_id" placeholder="Customer ID:">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">TRN Number</span>
                            <input style="width: 150px;" id="trn_number" name="trn_number" class="form-control trn_no"
                                placeholder="TRN Number:">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">Phone Number</span>
                            <input style="width: 150px;" id="phone" name="phone" class="form-control phone"
                                placeholder="Phone:">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1" style="width: 90px;">Email</span>
                            <input style="width: 170px;" id="email" name="email" class="form-control email">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <br>
                        <!-- CREDIT-->
                        @foreach ($users as $user)
                            <?php if ($user->role_id == '11') { ?>
                            <select class="js-user credituser" onclick="creditUser(this.value)"
                                onchange="getCreditId(this.value)" id="user_id" name="user_id" style="width:260px;">
                                <option value="">SELECT CREDIT CUSTOMER</option>
                                @foreach ($creditusers as $credituser)
                                    {{-- <option value="{{ $credituser->username }}" data-id="{{ $credituser->id }}">
                                        {{ $credituser->username }}</option> --}}

                                    <option value="{{ $credituser->id }}" data-id="{{ $credituser->id }}">
                                        {{ $credituser->name }}</option>
                                @endforeach
                            </select>

                            <input type="hidden" name="credit_id" id="credit_id">
                            <br /><br />

                            <?php } ?>
                        @endforeach
                        <!--  -->
                    </div>
                    <div class="col-md-4">
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1" style="width: 115px;"> Barcode</span>
                            <input type="text" id="barcodenumber" name="barcodenumber" style="width: 165px;"
                                class="form-control barcodenumber" placeholder="Click Here" autofocus>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <br />
                    <div class="col-md-4" style="padding-top: 5px;">
                        <div class="form-group pl-1">
                            <span class="form-group-addon pr-2" id="vat_type" for="vat_type">{{$tax}} Type</span>
                            <label class="pr-2">
                                <input type="radio" class="vattype_mode disable-after-select-vat" name="vat_type_mode"
                                    value="1">Inclusive
                            </label>
                            <label>
                                <input type="radio" class="vattype_mode disable-after-select-vat" name="vat_type_mode"
                                    value="2">Exclusive
                            </label>

                            <input type="hidden" name="vat_type_value" id="vat_type_value">
                        </div>
                        <span style="color:red">
                            @error('payment_mode')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                    <span id="selling-cost-display"></span>
                </div>

                <div>
                    <div id="prebuiltbilldiv" style="display:block;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="2%"></th>
                                    <th width="10%">Description</th>
                                    <th width="10%">Quantity</th>
                                    <th width="8%">Unit</th>
                                    <th width="10%">Rate</th>
                                    <th width="10%" id="inclusive_heading" style="display:none">Inclusive Rate
                                    </th>
                                    <th width="10%">
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
                                    <th width="10%">Total w/o <br /> Discount</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr bgcolor="#20639B">
                                    <td></td>
                                    <td>
                                        <div id="pselect" class="">
                                            <!-- <select onclick="doSomething(this.value)" id="product" class="form-control" style="width: 250px;color: black;"> -->
                                            <select onclick="doSomething(this.value)" id="product"
                                                class="product-list" style="width: 250px">
                                                <option value="">Select Product</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item['id'] }}">{{ $item['product_name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div id="selectproduct" class="hide">
                                            <select onclick="doSomething(this.value)" id="barcodeproduct"
                                                class="form-control" style="width: 250px"></select>
                                        </div>
                                    </td>
                                    <td><input type="number" step="1" id="qty" class="form-control qty"
                                            min="1" max="" onkeyup="checkquantity();" tabindex="1">
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

                                        <input type="text" id="pricex" class="form-control" readonly>
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
                    <div class="row">
                        <div class="col-sm-7"></div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon2"
                                    style="width: 50%;">GrandTotal</span>
                                <input type="text" name="bill_grand_total" class="form-control"
                                    id="bill_grand_total" aria-describedby="basic-addon2" readonly>
                            </div>
                            <br />
                        </div>


                        <div class="col-sm-8"></div>
                        <div class="col-sm-2">
                            <select class="form-control" id="payment_type" name="payment_type" style="width:80%">
                                <option value="1">CASH</option>
                                <option value="2">BANK</option>
                                @foreach ($users as $user)
                                    <?php if ($user->role_id == '11') { ?>
                                    <option value="3" id="creditoption" style="display: none" disabled>CREDIT
                                    </option>
                                    <?php } ?>
                                @endforeach
                                <option value="4">POS CARD</option>
                            </select>
                            <br>
                            <input class="btn btn-primary" type="submit" value="submit" id="submitBtn">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- {{-- <script src="{{ asset('javascript/billing.js') }}"></script> --}} -->
</body>

</html>

<script>
    function getCreditId(selectedUsername) {
        // Get the selected option
        var selectedOption = document.querySelector('option[value="' + selectedUsername + '"]');

        // Get the user ID from the data-id attribute of the selected option
        var selectedUserId = selectedOption.getAttribute('data-id');

        // Update the credit_id input field with the selected user's ID
        document.getElementById('credit_id').value = selectedUserId;
    }

    $(document).ready(function() {

        var discountty = $('#discount_type').val();

        if (discountty == "none") {
            var readon = "readonly";
            $('#discount').attr('readonly', readon);
        }

        $('#discount_type').change(function() {
            var discounttype = $('#discount_type').val();

            if (discounttype == "none") {
                var readonly = "readonly";
                $('#discount').attr('readonly', readonly);
            } else if ((discounttype == "percentage") || (discounttype == "amount")) {

                $('#discount').prop('readonly', false);
            }
        });
    });
</script>


<script type="text/javascript">
    $(document).ready(function() {
        $('#barcodenumber').focus();
    });
</script>

<script>
    $(document).ready(function() {

        function common_calcu() {
            var total = 0;
            var total_discount = 0;
            var grandtotal_discount = 0;
            var netrate = Number($("#net_rate").val());
            var quantity = Number($("#qty").val());
            var mrp = Number($("#mrp").val());
            var fixed_vat = Number($("#fixed_vat").val());
            var inc_rate = Number($("#inclusive_rate").val());
            var discounts = Number($("#discount").val());
            var discount_type = $("#discount_type").val();

            if (quantity > 0) {

                total = inc_rate * quantity;

                if (discount_type == "percentage") {
                    var discount_amt = total * (discounts / 100);
                } else if (discount_type == "amount") {
                    var discount_amt = discounts;
                }

                if (discount_type == "percentage" || discount_type == "amount") {
                    total_discount = total - discount_amt;
                } else if (discount_type == "none") {
                    total_discount = total;
                }

            } else {
                total = 0;
                total_discount = 0;
            }

            var inclusive_vate = mrp - inc_rate;
            netrate = inc_rate + inclusive_vate;

            if (quantity > 0) {
                grandtotal = netrate * quantity;

                if (discount_type == "percentage") {
                    var discount_amount = grandtotal * (discounts / 100);
                } else if (discount_type == "amount") {
                    var discount_amount = discounts;

                }

                if (discount_type == "percentage" || discount_type == "amount") {
                    grandtotal_discount = grandtotal - discount_amount;
                } else if (discount_type == "none") {
                    grandtotal_discount = grandtotal;
                }

            } else {
                grandtotal = 0;
                grandtotal_discount = 0;
            }

            var subvat_tot = grandtotal_discount / (1 + (fixed_vat / 100));
            grandvat = grandtotal_discount - subvat_tot;

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
        }

        $('.disable-after-select-vat').on('change', function() {
            if ($(this).is(':checked')) {
                $('#vat_type_value').val($(this).val());
                $('.disable-after-select-vat').not(this).prop('disabled', true);
            } else {
                $('#vat_type_value').val('');
                $('.disable-after-select-vat').prop('disabled', false);
            }
        });

        $('input[name="vat_type_mode"]').on('change', function() {

            var vat_type = $('input[name="vat_type_mode"]:checked').val();

            var selectval = $('#vat_type_value').val();

            if (vat_type == 1) {

                /* extra column add */

                var inclusive_header = document.getElementById("inclusive_heading");
                var inclusive_rate_value = document.getElementById("inclusive_rate_value");

                inclusive_header.style.display = "table-cell";

                inclusive_rate_value.innerHTML =
                    '<input type="number" step="any" id="inclusive_rate" name="inclusive_rate" class="form-control" readonly>';

                inclusive_rate_value.style.display = "table-cell";

                /*----------------------------------*/

                $('#vat_perc').text('{{$tax}}(%)-Inclus');
                $('#vat_ammi').text('Total {{$tax}} Amount-Inclus');

                $("#mrp, #fixed_vat").keyup(function() {

                    var mrp = Number($("#mrp").val());
                    var fixed_vat = Number($("#fixed_vat").val());

                    var InclusiveRate = mrp / (1 + (fixed_vat / 100));

                    var InclusiveRate = Math.round(InclusiveRate * 1000) / 1000;

                    $("#inclusive_rate").val(InclusiveRate);

                });

                $("#qty, #mrp, #discount, #discount_type").keyup(function() {
                    common_calcu();
                });

                // $("#fixed_vat,#net_rate, #discount, #discount_type").keyup(function() {

                //     var total = 0;
                //     var total_discount = 0;
                //     var grandtotal_discount = 0;
                //     var quantity = Number($("#qty").val());
                //     var mrp = Number($("#mrp").val());
                //     var fixed_vat = Number($("#fixed_vat").val());
                //     var netrate = Number($("#net_rate").val());
                //     var inc_rate = Number($("#inclusive_rate").val());
                //     var discounts = Number($("#discount").val());

                //     var discount_type = $("#discount_type").val();

                //     if (quantity > 0) {

                //         total = inc_rate * quantity;

                //         if (discount_type == "percentage") {
                //             var discount_amt = total * (discounts / 100);
                //         } else if (discount_type == "amount") {
                //             var discount_amt = discounts;
                //         }

                //         if (discount_type == "percentage" || discount_type == "amount") {
                //             total_discount = total - discount_amt;
                //         } else if (discount_type == "none") {
                //             total_discount = total;
                //         }

                //     } else {
                //         total = 0;
                //         total_discount = 0;
                //     }

                //     inclusive_vate = mrp - inc_rate;
                //     netrate = inc_rate + inclusive_vate;

                //     if (quantity > 0) {
                //         grandtotal = netrate * quantity;

                //         if (discount_type == "percentage") {
                //             var discount_amount = grandtotal * (discounts / 100);
                //         } else if (discount_type == "amount") {
                //             var discount_amount = discounts;

                //         }

                //         if (discount_type == "percentage" || discount_type == "amount") {
                //             grandtotal_discount = grandtotal - discount_amount;
                //         } else if (discount_type == "none") {
                //             grandtotal_discount = grandtotal;
                //         }

                //     } else {
                //         grandtotal = 0;
                //         grandtotal_discount = 0;
                //     }

                //     var subvat_tot = grandtotal_discount / (1 + (fixed_vat / 100));
                //     grandvat = grandtotal_discount - subvat_tot;

                //     grandtotal_discount = Math.round(grandtotal_discount * 1000) / 1000;
                //     var grandtotal = Math.round(grandtotal * 1000) / 1000;
                //     var grandvat = Math.round(grandvat * 1000) / 1000;
                //     var netrate = Math.round(netrate * 1000) / 1000;

                //     $("#pricex").val(total_discount);
                //     $("#price").val(grandtotal_discount);
                //     $("#vat_amount").val(grandvat);
                //     $("#net_rate").val(netrate);
                //     $("#price_wo_discount").val(grandtotal);
                //     $("#total_wo_discount").val(total);
                // });

                $("#fixed_vat,#net_rate, #discount, #discount_type").keyup(function() {
                    common_calcu();
                });

                $("#qty, #discount, #discount_type").keyup(function() {

                    $('#discount_type').change(function() {

                        var total = 0,
                            total_discount = 0,
                            grandtotal = 0,
                            grandtotal_discount = 0;

                        var discount_type = $("#discount_type").val();
                        var inc_rate = Number($("#inclusive_rate").val());
                        var mrp = Number($("#mrp").val());
                        var quantity = Number($("#qty").val());
                        var fixed_vat = Number($("#fixed_vat").val());

                        if (discount_type == "none") {

                            var inclusive_vate = mrp - inc_rate;
                            netrate = inc_rate + inclusive_vate;

                            grandtotal = netrate * quantity;
                            grandtotal_discount = grandtotal;

                            total = inc_rate * quantity;
                            total_discount = total;

                            var subvat_tot = grandtotal / (1 + (fixed_vat / 100));
                            grandvat = grandtotal - subvat_tot;

                            grandtotal_discount = Math.round(grandtotal_discount *
                                1000) / 1000;
                            var grandtotal = Math.round(grandtotal * 1000) / 1000;
                            var grandvat = Math.round(grandvat * 1000) / 1000;
                            var netrate = Math.round(netrate * 1000) / 1000;

                            $("#pricex").val(total_discount);
                            $("#price").val(grandtotal_discount);
                            $("#vat_amount").val(grandvat);
                            $("#net_rate").val(netrate);
                            $("#price_wo_discount").val(grandtotal);
                            $("#total_wo_discount").val(total);
                        }
                    });
                });

            } else if (vat_type == 2) {

                $('#vat_perc').text('VAT(%)-Exclus');
                $('#vat_ammi').text('Total {{$tax}} Amount-Exclus');

                $("#qty,#fixed_vat,#mrp").keyup(function() {
                    var mrp = Number($("#mrp").val());
                    var fixed_vat = Number($("#fixed_vat").val());
                    var discounts = Number($("#discount").val());

                    var disc_mrp = mrp - (mrp * (discounts / 100));
                    var mrp_disc_vat = disc_mrp * (fixed_vat / 100);
                    var netrate = mrp_disc_vat + disc_mrp;

                    // netrate = ((fixed_vat * mrp) / 100) + mrp;

                    netrate = Math.round(netrate * 1000) / 1000;

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

                    var disc_mrp = mrp - (mrp * (discounts / 100));
                    var mrp_disc_vat = disc_mrp * (fixed_vat / 100);
                    var netrate = mrp_disc_vat + disc_mrp;

                    if (quantity > 0) {
                        total = mrp * quantity;

                        var discount_amt = total * (discounts / 100);
                        total_discount = total - discount_amt;
                    } else {
                        total = 0;
                        total_discount = 0;
                    }

                    if (discounts != 0 || discounts != "") {
                        var grandvat = total_discount * (fixed_vat / 100);

                    } else if (discounts == 0 || discounts == "") {
                        var grandvat = total * (fixed_vat / 100);
                    }

                    grandtotal_discount = total_discount + grandvat;
                    grandtotal = total + (total * (fixed_vat / 100));

                    // grandvat = (fixed_vat * total) / 100;
                    // netrate = ((fixed_vat * mrp) / 100) + mrp;

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

                // $("#fixed_vat,#net_rate").keyup(function() {
                //     var rate = 0;
                //     var vat_amount = 0;
                //     var fixed_vat = Number($("#fixed_vat").val());
                //     var net_rate = Number($("#net_rate").val());
                //     a = (fixed_vat / 100);
                //     b = a + 1;
                //     c = net_rate / b;
                //     rate = c;
                //     var rate = Math.round(rate * 1000) / 1000;
                //     $("#mrp").val(rate);

                // });

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

                    var disc_mrp = mrp - (mrp * (discounts / 100));
                    var mrp_disc_vat = disc_mrp * (fixed_vat / 100);
                    var netrate = mrp_disc_vat + disc_mrp;

                    if (quantity > 0) {
                        total = quantity * mrp;

                        var discount_amt = total * (discounts / 100);
                        total_discount = total - discount_amt;
                    } else {
                        total = 0;
                        total_discount = 0;
                    }

                    if (discounts != 0 || discounts != "") {
                        var grandvat = total_discount * (fixed_vat / 100);

                    } else if (discounts == 0 || discounts == "") {
                        var grandvat = total * (fixed_vat / 100);
                    }

                    grandtotal_discount = total_discount + grandvat;
                    grandtotal = total + (total * (fixed_vat / 100));

                    // grandvat = (fixed_vat * total) / 100;
                    // netrate = ((fixed_vat * mrp) / 100) + mrp;

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
                if (selectedValue === "") {
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

                    if (vat_type == 1) {
                        $("#inclusive_rate").val(nu);
                    }
                    $("#buycost_rate").val(nu);
                    $("#discount").val(nu);
                    $("#price_wo_discount").val(nu);
                    $("#total_wo_discount").val(nu);
                }
            });

            $('#discount_type').change(function() {
                var nu = "";

                $("#price").val(nu);
                $("#vat_amount").val(nu);
                $("#pricex").val(nu);
                $("#discount").val(nu);
                $("#price_wo_discount").val(nu);
                $("#total_wo_discount").val(nu);

            });

        });
    });
</script>

<script type="text/javascript">
    var addedProducts = [];

    function updateGrandTotalAmount() {
        var grandtotal = 0;

        $('.total-amount').each(function() {
            grandtotal += Number($(this).val());
        });

        grandtotal = grandtotal.toFixed(2);
        grandtotal = parseFloat(grandtotal);
        $('#bill_grand_total').val(grandtotal);
    }

    $('.addRow').off().on('click', addRow);

    function addRow() {

        var productId = $('#product_id').val();
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

        var u = Number($("#product_id").val());

        var vat_type = $('input[name="vat_type_mode"]:checked').val();

        if (vat_type == 1) {
            var inclu_rate = Number($("#inclusive_rate").val());
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
            ']" class="form-control total-amount" readonly>' +
            '<input type="number" value=' + p + ' id="rowprice" name="price[' + u +
            ']" class="form-control" readonly></td>' +
            '<td><input type="number" value=' + price_dis +
            ' id="total_amount_wo_discount" name="total_amount_wo_discount[' + u +
            ']" class="form-control total_with_discount" readonly>' +
            '<input type="number" value=' + total_disc +
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
        // $( "#product" ).focus();
        // $('#product').select2('focus');
        $('#barcodenumber').focus();
        $("#barcodenumber").val(nu).trigger('change');

        if (vat_type == 1) {
            $("#inclusive_rate").val(nu);
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
        var remstock = array.find(isSeries).remaining_stock;
        var remstk = Number(remstock);

        $('input[name="quantity[' + u + ']"]').attr("max", remstk);

        function GetCommonDataquantityEdit(u) {
            var total = 0;
            var total_discount = 0;
            var grandtotal = 0;
            var grandtotal_discount = 0;

            var netrt = Number($('input[name="net_rate[' + u + ']"]').val());
            var quantty = Number($('input[name="quantity[' + u + ']"]').val());
            var mrp = Number($('input[name="mrp[' + u + ']"]').val());
            var fixed_vat = Number($('input[name="fixed_vat[' + u + ']"]').val());
            var discounts = Number($('input[name="dis_count[' + u + ']"]').val());
            var discount_type = $('select[name="dis_count_type[' + u + ']"]').val();
            var vat_type = $('input[name="vat_type_mode"]:checked').val();

            return {
                total,
                total_discount,
                grandtotal,
                grandtotal_discount,
                netrt,
                quantty,
                mrp,
                fixed_vat,
                discounts,
                discount_type,
                vat_type
            };
        }

        $('input[name="quantity[' + u + ']"], input[name="dis_count[' + u + ']"], select[name="dis_count_type[' + u +
            ']"]').on('input', function() {

            var pro = $('input[name="quantity[' + u + ']"]').val();
            if (pro > remstk) {
                $('input[name="quantity[' + u + ']"]').val(remstk);
            }

            $('select[name="dis_count_type[' + u + ']"]').change(function() {

                var discount__type = $('select[name="dis_count_type[' + u + ']"]').val();

                $('input[name="vat_amount[' + u + ']"]').val("");
                $('input[name="total_amount[' + u + ']"]').val("");
                $('input[name="price[' + u + ']"]').val("");
                $('input[name="total_amount_wo_discount[' + u + ']"]').val("");
                $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val("");
                $('input[name="dis_count[' + u + ']"]').val("");

                if (discount__type == "none") {
                    var {
                        total,
                        total_discount,
                        grandtotal,
                        grandtotal_discount,
                        netrt,
                        quantty,
                        mrp,
                        fixed_vat,
                        discounts,
                        discount_type,
                        vat_type
                    } = GetCommonDataquantityEdit(u);

                    if (vat_type == 1) {

                        var inc_rate = Number($('input[name="inclusive_rate_r[' + u + ']"]').val());

                        var inclusive_vate = mrp - inc_rate;
                        var netrate = inc_rate + inclusive_vate;
                        netrate = Math.round(netrate * 1000) / 1000;

                        var grandtotal = netrate * quantty;
                        grandtotal_discount = grandtotal;

                        total = inc_rate * quantty;
                        total_discount = total;

                        var subvat_tot = grandtotal_discount / (1 + (fixed_vat / 100));
                        grandvat = grandtotal_discount - subvat_tot;

                    } else if (vat_type == 2) {

                    }

                    var grandvat = Math.round(grandvat * 1000) / 1000;
                    grandtotal = Math.round(grandtotal * 1000) / 1000;
                    grandtotal_discount = Math.round(grandtotal_discount * 1000) / 1000;

                    $('input[name="vat_amount[' + u + ']"]').val(grandvat);
                    $('input[name="total_amount[' + u + ']"]').val(grandtotal_discount);
                    $('input[name="price[' + u + ']"]').val(total_discount);
                    $('input[name="total_amount_wo_discount[' + u + ']"]').val(grandtotal);
                    $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val(total);

                    updateGrandTotalAmount();
                }

            });

            $('input[name="quantity[' + u + ']"], input[name="dis_count[' + u +
                ']"], select[name="dis_count_type[' +
                u + ']"]').keyup(function() {

                var {
                    total,
                    total_discount,
                    grandtotal,
                    grandtotal_discount,
                    netrt,
                    quantty,
                    mrp,
                    fixed_vat,
                    discounts,
                    discount_type,
                    vat_type
                } = GetCommonDataquantityEdit(u);

                if (vat_type == 1) {

                    var inc_rate = Number($('input[name="inclusive_rate_r[' + u + ']"]').val());

                    inclusive_vate = mrp - inc_rate;
                    var netrate = inc_rate + inclusive_vate;
                    netrate = Math.round(netrate * 1000) / 1000;

                    if (quantty > 0) {
                        grandtotal = netrate * quantty;

                        if (discount_type == "percentage") {
                            var discount_amount = grandtotal * (discounts / 100);
                        } else if (discount_type == "amount") {
                            var discount_amount = discounts;
                        }

                        if (discount_type == "percentage" || discount_type == "amount") {
                            grandtotal_discount = grandtotal - discount_amount;
                        } else if (discount_type == "none") {
                            grandtotal_discount = grandtotal;
                        }

                    } else {
                        grandtotal = 0;
                        grandtotal_discount = 0;
                    }

                    $('input[name="net_rate[' + u + ']"]').val(netrate);

                    if (quantty > 0) {
                        total = inc_rate * quantty;

                        if (discount_type == "percentage") {
                            var discount_amt = total * (discounts / 100);
                        } else if (discount_type == "amount") {
                            var discount_amt = total * (discounts / 100);
                        }

                        if (discount_type == "percentage" || discount_type == "amount") {
                            total_discount = total - discount_amt;
                        } else if (discount_type == "none") {
                            total_discount = total;
                        }

                    } else {
                        total = 0;
                        total_discount = 0;
                    }

                    var subvat_tot = grandtotal_discount / (1 + (fixed_vat / 100));
                    grandvat = grandtotal_discount - subvat_tot;

                    $('input[name="price[' + u + ']"]').val(total_discount);
                    $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val(total);
                } else if (vat_type == 2) {

                    if (quantty > 0) {
                        total = mrp * quantty;
                        var discount_amt = total * (discounts / 100);
                        total_discount = total - discount_amt;
                    } else {
                        total = 0;
                        total_discount = 0;
                    }

                    $('input[name="price[' + u + ']"]').val(total_discount);
                    $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val(total);

                    var disc_mrp = mrp - (mrp * (discounts / 100));
                    var mrp_disc_vat = disc_mrp * (fixed_vat / 100);
                    netrt = mrp_disc_vat + disc_mrp;

                    $('input[name="net_rate[' + u + ']"]').val(netrt);

                    if (discounts != 0 || discounts != "") {
                        var grandvat = total_discount * (fixed_vat / 100);

                    } else if (discounts == 0 || discounts == "") {
                        var grandvat = total * (fixed_vat / 100);
                    }

                    grandtotal_discount = total_discount + grandvat;
                    grandtotal = total + (total * (fixed_vat / 100));

                    // grandvat = (fixed_vat * total) / 100;
                }

                var grandvat = Math.round(grandvat * 1000) / 1000;
                grandtotal = Math.round(grandtotal * 1000) / 1000;
                grandtotal_discount = Math.round(grandtotal_discount * 1000) / 1000;

                $('input[name="vat_amount[' + u + ']"]').val(grandvat);
                $('input[name="total_amount[' + u + ']"]').val(grandtotal_discount);
                $('input[name="total_amount_wo_discount[' + u + ']"]').val(grandtotal);

                updateGrandTotalAmount();
            });
        });

        // Add an event listener to the discount type select element
        $('select[name="dis_count_type[' + u + ']"]').on('change', function() {

            var discounttype = $('select[name="dis_count_type[' + u + ']"]').val();

            if (discounttype == "none") {

                var readonly = "readonly";
                $('input[name="dis_count[' + u + ']"]').attr('readonly', readonly);

            } else if ((discounttype == "percentage") || (discounttype == "amount")) {

                $('input[name="dis_count[' + u + ']"]').prop('readonly', false);
            }
        });

        /*-------------------------------------------------------------------------------------------------*/

        // After adding a new row
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

    $(document).ready(function() {
        // var daterandom = Date.now();

        // var daterandom = Date.now() % 1000000;
        // $("#cust_id").val(daterandom);

        generateCustomerID();
    });

    function generateCustomerID() {
        var daterandom = Date.now() % 1000000;
        $("#cust_id").val(daterandom);
    }

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
        // $("#net_rate").val(nu);
        $("#vat_amount").val(nu);
        $("#discount").val(nu);
        $("#price_with_discount").val(nu);
        $("#total_with_discount").val(nu);

        $('#qty').focus();

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

    function creditUser(x) {
        var k = x;
        // $('#cust_id').val(k);

        // $('#cust_id').val("");
        // $("#payment_type").val(3).trigger('change');
        if (x != "") {
            $('#payment_type #creditoption').show();

            $.ajax({
                type: 'get',
                url: '/gethistory/' + x,
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

            $("#payment_type").val(3).trigger('change');

            $('#payment_type #creditoption').prop('disabled', false);
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

    $(document).ready(function() {
        $('.js-user').select2({
            theme: "classic"
        });
    });

    // function validateForm() {
    //     // Validate Title
    //     var product = $("#productnamevalue").val();
    //     if (product == "" || product == null) {
    //         alert("Press the add button");
    //         return false;
    //     }
    //     return true;
    // }

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

        // Validate Title
        var product = $("#productnamevalue").val();
        if (product == "" || product == null) {
            alert("Press the add button");

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

<script>
    $(document).ready(function() {

        var ajaxRequest;

        $('#barcodenumber').on('keyup', function() {

            if ($(this).val() != "") {
                $("#selectproduct").removeClass('hide');
                $("#pselect").addClass('hide');
            }

            // Abort the previous AJAX request if it's still in progress
            if (ajaxRequest) {
                ajaxRequest.abort();
            }

            let bar = $(this).val();

            $('#barcodeproduct').empty();
            ajaxRequest = $.ajax({
                type: 'GET',
                url: 'getbarcodedata/' + bar,
                success: function(response) {
                    var response = JSON.parse(response);

                    // Check if VAT Type mode is selected
                    var vat_type_selected = $('input[name="vat_type_mode"]:checked').val();

                    if (vat_type_selected == null) {
                        alert('Please select VAT Type first.');
                        $('#barcodenumber').val('');
                        $("#selectproduct").addClass('hide');
                        $("#pselect").removeClass('hide');
                        return;
                    }

                    $('#barcodeproduct').empty();
                    response.forEach(element => {

                        $('#barcodeproduct').append(
                            `<option selected value="${element['id']}">${element['product_name']}</option>`
                        );

                        $('#product_id').val(element['id']);
                        $('#product_name').val(element['product_name']);
                        $('#mrp').val(element['selling_cost']);
                        $('#buycost').val(element['buy_cost']);
                        $('#fixed_vat').val(element['vat']);
                        $('#prounit').val(element['unit']);

                        $("#qty").val('1');
                        $("#discount").val('0');
                        $("#discount_type").val("none");
                        $('#buycost_rate').val(element['rate']);

                        var discounts = $("#discount").val();
                        var discount__type = $("#discount_type").val();
                        var vat_typeba = $('input[name="vat_type_mode"]:checked')
                            .val();
                        var selectvalba = $('#vat_type_value').val();
                        var u = Number($("#product_id").val());

                        if (discount__type == "none") {

                            var total = 0,
                                total_discount = 0,
                                grandtotal = 0,
                                grandtotal_discount = 0;

                            if (selectvalba == 1) {

                                $('#inclusive_rate').val(element['inclusive_rate']);
                                var inc_rate = parseFloat(element[
                                    'inclusive_rate']);

                                var inclusive_vate = parseFloat(element[
                                    'selling_cost'] - inc_rate);

                                var netrate = inc_rate + inclusive_vate;
                                netrate = Math.round(netrate * 1000) / 1000;

                                grandtotal = netrate * 1;
                                grandtotal_discount = grandtotal;

                                total = inc_rate * 1;
                                total_discount = total;

                                var subvat_tot = grandtotal / (1 + (element[
                                        'vat'] /
                                    100));
                                grandvat = grandtotal - subvat_tot;

                            } else if (selectvalba == 2) {

                            }

                            grandtotal = Math.round(grandtotal *
                                1000) / 1000;
                            grandvat = Math.round(grandvat * 1000) / 1000;

                            $("#pricex").val(total_discount);
                            $("#price").val(grandtotal_discount);
                            $("#vat_amount").val(grandvat);
                            $("#net_rate").val(netrate);
                            $("#price_wo_discount").val(grandtotal);
                            $("#total_wo_discount").val(total);
                        }

                        if (selectvalba == 2) {

                            var total = 0;
                            var total_discount = 0;
                            var grandtotal = 0;
                            var grandtotal_discount = 0;

                            var disc_mrp = element['selling_cost'] - (element[
                                'selling_cost'] * (discounts / 100));
                            var mrp_disc_vat = disc_mrp * (element['vat'] / 100);
                            var netrate = mrp_disc_vat + disc_mrp;

                            $("#net_rate").val(netrate);

                            var total = element['selling_cost'] * 1;
                            var discount_amt = total * (discounts / 100);
                            var total_discount = total - discount_amt;

                            // grandvat = (element['vat'] * total) / 100;

                            var grandvat = total * (element['vat'] / 100);

                            var grandtotal_discount = total_discount + grandvat;
                            grandtotal = total + (total * (element['vat'] / 100));

                            grandvat = Math.round(grandvat * 1000) /
                                1000;
                            grandtotal = Math.round(grandtotal *
                                1000) / 1000;
                            grandtotal_discount = Math.round(grandtotal_discount *
                                1000) / 1000;

                            $('#pricex').val(total_discount);
                            $("#price").val(grandtotal_discount);
                            $("#vat_amount").val(grandvat);
                            $("#total_wo_discount").val(total);
                            $("#price_wo_discount").val(grandtotal);
                        }

                        if (element['remaining_stock'] > 1 || element[
                                'remaining_stock'] == 1) {
                            addRow();

                        } else if ((element['remaining_stock'] == 0) || (element[
                                'remaining_stock'] < 1 && element[
                                'remaining_stock'] > 0)) {
                            // alert('Stock empty');

                            alert("Remaining stock of left only :" + element[
                                'remaining_stock']);

                            $("#selectproduct").addClass('hide');
                            $("#pselect").removeClass('hide');

                            $('#barcodenumber').val('');
                            $("#barcodeproduct").val("");
                            $("#qty").val("");
                            $("#inclusive_rate").val("");
                            $("#discount").val("");

                            $('.addRow').off();
                        }

                        var nu = "";
                        $("#mrp").val(nu);
                        $("#fixed_vat").val(nu);
                        $("#net_rate").val(nu);
                        $("#price").val(nu);
                        $('#pricex').val(nu);
                        $("#vat_amount").val(nu);
                        $("#prounit").val(nu);

                        /*--------------EDIT ADDED ROW'S QUANTITY AND BASED ON CHANGE VAT AMOUNT  & TOTAL AMOUNT----------------*/
                        /*-----------------------------------------------------------------------------------------------------*/
                    });


                }
            });
        });
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
    // Add this script to your Blade view or a separate JavaScript file

    // Event listener for phone number and product selection
    $('#phone, #product, #barcodeproduct').on('change', function() {
        // Get phone number and selected products
        var phone = $('#phone').val();
        var selectedProducts = $('#product').val();
        var barcodeproducts = $('#barcodeproduct').val();

        // Get the CSRF token value from the meta tag
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Make an AJAX request to the Laravel controller
        $.ajax({
            url: '/get-previous-selling-cost',
            type: 'POST',
            data: {
                phone: phone,
                selectedProducts: selectedProducts,
                barcodeProducts: barcodeproducts,
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': csrfToken // Include CSRF token in the headers
            },
            success: function(response) {

                $('#selling-cost-display').text('');

                $.each(response, function(productId, data) {
                    $('#selling-cost-display').text(data.product_name +
                        " Previously sold with MRP " + data.mrp);
                });
            },
            error: function(error) {}
        });
    });
</script>
