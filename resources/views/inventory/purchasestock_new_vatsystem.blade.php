<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Stock</title>
    @include('layouts/usersidebar')
    <style>
        table {
            border: solid black 1px;
            border-collapse: separate;
            border-left: solid black 1px;
            border-radius: 10px;
            border-spacing: 0px;
        }

        th,
        td {
            border: 1px solid black;
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
            background-color: #187f6a;
            color: white;
        }

        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 1px solid #010101;
        }

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
        @include('navbar.invnavbar')
        @else
        <x-logout_nav_user />
        @endif

        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif
        <div align="center">
            @foreach ($shopdatas as $shopdata)
                {{ $shopdata['name'] }}
                <br>
                Phone No:{{ $shopdata['phone'] }}
                <br>
                Email:{{ $shopdata['email'] }}
                <br>
                <br>
            @endforeach
        </div>
        <div align="right">
            @include('modal.product_modal.add_product_modal', ['categories' => $categories, 'units' => $units])

            <!-- Add Product button -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">
                Add New Product
            </button>
        </div>
        <br />
        <br />

        <form method="post" action="submitstock_table" enctype="multipart/form-data" id="purchase_form"
            name="purchase_form" onsubmit="return validateForm();">
            @csrf
            <h2>Purchase Data</h2>
            <br />
            <div class="form-group row">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1" style="width: 126px;">Bill Number</span>
                            <!--<input type="text" id="reciept_no" name="reciept_no" style="width: 300px;"-->
                            <!--    class="form-control receiptno" placeholder="Bill No:" tabindex="1"-->
                            <!--    oninput="validateReceiptNo(this.value)" autofocus>-->

                            <input type="text" id="reciept_nos" name="reciept_no" list="reciept_no"
                                class="form-control receiptno" placeholder="Bill No:" aria-describedby="basic-addon2"
                                autocomplete="off" style="width: 300px;" oninput="validateReceiptNo(this.value)"
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
                        <br />
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">Comment</span>
                            <input type="text" id="comment" name="comment" style="width: 500px;"
                                class="form-control comment" placeholder="Comment:" tabindex="2">
                        </div>
                        <span style="color:red">
                            @error('comment')
                                {{ $message }}
                            @enderror
                        </span>
                        <br />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">Supplier Name</span>
                            <input type="text" list="supplier" name="supplier" id="supplierdata"
                                class="form-control supplier" placeholder="Supplier Name"
                                aria-describedby="basic-addon2" autocomplete="off" style="width: 300px;" tabindex="3">
                            <datalist id="supplier">
                                @foreach ($suppliers as $row)
                                    <!--<option value="< ?php echo $row->name ?>">< ?php echo $row->name ?></option>-->
                                    <option data-value="{{ $row->id }}" value="{{ $row->name }}"></option>
                                @endforeach
                            </datalist>
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

                    <div class="col-md-6" style="padding-top: 5px;">
                        <div class="form-group pl-1">
                            <span class="form-group-addon pr-2" id="payment_mode" for="payment_mode">Payment Mode</span>
                            <label class="pr-2">
                                <input type="radio" class="mode cash" name="payment_mode" value="1"
                                    tabindex="4">Cash
                            </label>
                            <label>
                                <input type="radio" class="mode credit" name="payment_mode" value="2"
                                    tabindex="5">Credit
                            </label>
                        </div>
                        <span style="color:red">
                            @error('payment_mode')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>

                </div>

                <!-- {{-- <div class="row">
                    <div class="col-md-6" style="padding-top: 5px;">
                        <div class="form-group pl-1">
                            <span class="form-group-addon pr-2" id="vat_mode" for="vat_mode">VAT Mode</span>
                            <label class="pr-2">
                                <input type="radio" class="mode percentage disable-after-select" name="vat_mode"
                                    value="1" onclick="addVatColumns(this)" tabindex="6">Vat In %
                            </label>
                            <label class="pr-2">
                                <input type="radio" class="mode amount disable-after-select" name="vat_mode"
                                    value="2" onclick="addVatColumns(this)" tabindex="7"> Vat in Amount
                            </label>
                            <label>
                                <input type="radio" class="mode none disable-after-select" name="vat_mode"
                                    value="0" tabindex="8"> No Vat
                            </label>

                            <input type="hidden" name="selectedOptionInput" id="selectedOptionInput"
                                value="0">
                        </div>
                        <span style="color:red">
                        </span>
                    </div> --}} -->

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">Invoice Date</span>
                        <input type="date" id="invoice_date" name="invoice_date" style="width: 300px;"
                            class="form-control invoice_date" tabindex="6">
                    </div>
                    <span style="color:red">
                        @error('invoice_date')
                            {{ $message }}
                        @enderror
                    </span>
                    <br />
                </div>
            </div>
            <br /><br />

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
                            <th width="9%" id="box_dozen_header" style="display: none;">Box / Dozen</th>
                            <th width="9%" id="items_header" style="display: none;">Items</th>

                            <!-- {{-- <th width="10%" id="percentage_first" style="display: none;">VAT (%)</th>
                            <th width="10%" id="percentage_second" style="display: none;">VAT Amount</th>

                            <th width="10%" id="amount_first" style="display: none;">VAT Amount</th>
                            <th width="10%" id="amount_second" style="display: none;">VAT (%)</th> --}} -->

                            <th width="5%">Unit</th>
                            <th width="10%">Total <br />( Without {{$tax}}) </th>
                            <th width="10%">Total <br />( With {{$tax}}) </th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr bgcolor="#187f6a">
                            <td></td>
                            <td id="one">
                                <div>
                                    <select id="product" name="product" class="product-list product"
                                        style="width: 300px;" onclick="productlist(this.value)"
                                        onkeydown="moveToRadioGroup(event)" tabindex="7">
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

                            <td style="color: #fff;">
                                <input type="radio" name="mode" value="1" onclick="addExtraColumns(this)"
                                    class="box" tabindex="12">
                                <span style="margin-right:15px;">Box</span>

                                <input type="radio" name="mode" value="2" onclick="addExtraColumns(this)"
                                    class="dozen" tabindex="13">
                                <span>Dozen</span>
                            </td>
                            <td id="boxDozenNo" style="display: none;"></td>
                            <td id="itemColumn" style="display: none;"></td>

                            <!-- {{-- <td id="percentage_first_column" style="display: none;"></td>
                            <td id="percentage_second_column" style="display: none;"></td>

                            <td id="amount_first_coumn" style="display: none;"></td>
                            <td id="amount_second_coumn" style="display: none;"></td> --}} -->

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
                    <!--{{-- <button type="submit" class="btn btn-primary submitpurchase">Submit</button> --}}-->
                    <input class="btn btn-primary" type="submit" value="submit" id="submitBtn">
                </div>
            </div>
    </div>
    </form>
    </div>
    <script src="{{ asset('javascript/purchase.js') }}"></script>
</body>

</html>


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



<!--{{-- <script>
    $(document).ready(function() {
        $('.disable-after-select').on('change', function() {
            if ($(this).is(':checked')) {
                $('#selectedOptionInput').val($(this).val());
                $('.disable-after-select').not(this).prop('disabled', true);
            } else {
                $('#selectedOptionInput').val('');
                $('.disable-after-select').prop('disabled', false);
            }
        });
    });
</script> --}} -->

<script type="text/javascript">
    function addExtraColumns(radio) {
        var box_dozen_header = document.getElementById("box_dozen_header");
        var items_header = document.getElementById("items_header");

        var boxDozenNo = document.getElementById("boxDozenNo");
        var itemColumn = document.getElementById("itemColumn");

        if (radio.value === "1") {

            box_dozen_header.style.display = "table-cell";
            items_header.style.display = "table-cell";

            boxDozenNo.innerHTML =
                '<input type="number" id="boxselect" name="boxselect" min="0" onkeydown="moveToboxenterFields(event)" class="form-control boxselect" placeholder="No. of Box" tabindex="14">';
            itemColumn.innerHTML =
                '<input type="number" id="boxselectenter" name="boxselectenter" min="0" onkeydown="whichmovehappen(event)" class="form-control boxselectenter" placeholder="Items in Box" tabindex="15">';

            boxDozenNo.style.display = "table-cell";
            itemColumn.style.display = "table-cell";

        } else if (radio.value === "2") {

            box_dozen_header.style.display = "table-cell";
            items_header.style.display = "table-cell";

            boxDozenNo.innerHTML =
                '<input type="number" id="dozenselect" name="dozenselect" min="0" onkeydown="moveTodozenenterFields(event)" oninput="getValue()" onChange = "getValue()" class="form-control dozenselect" placeholder="No. of Dozen" tabindex="14">';
            itemColumn.innerHTML =
                '<input type="number" id="dozenselectenter" name="dozenselectenter" min="0" oninput="getValue()" onkeydown="whichmovehappen(event)" class="form-control dozenselectenter" placeholder="Items" tabindex="15">';

            boxDozenNo.style.display = "table-cell";
            itemColumn.style.display = "table-cell";
        } else {

            box_dozen_header.style.display = "none";
            items_header.style.display = "none";

            boxDozenNo.innerHTML = '';
            itemColumn.innerHTML = '';

            boxDozenNo.style.display = "none";
            itemColumn.style.display = "none";
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

            // var vati_am_dozen = parseFloat($('#vat_amount').val());
            // var vatamdozen = (!isNaN(vati_am_dozen)) ? vati_am_dozen : 0;

            // var vat_mmdoz = $('input[name="vat_mode"]:checked').val();

            // if ((vat_mmdoz == "1" || vat_mmdoz == "2" || vat_mmdoz == "0" && !isNaN(vatamdozen))) {

            //     var total_1 = dozenselectenter * buycos;
            //     var total = total_1 + vatamdozen;

            // } else if (vat_mmdoz == null || (vat_mmdoz == "1" || vat_mmdoz == "2" || vat_mmdoz == "0" && isNaN(
            //         vatamdozen))) {

            //     var total = dozenselectenter * buycos;

            // }

            /* first done */

            // var total = dozenselectenter * buycos;

            // var doz_tot_vat = total + doz_vat;

            // doz_tot_vat = doz_tot_vat.toFixed(2);

            // doz_tot_vat = parseFloat(doz_tot_vat);


            // document.getElementById('total').value = doz_tot_vat;

            // /* -------------- without VAT----------------*/

            // var total_without_vat = dozenselectenter * buycos;

            // total_without_vat = total_without_vat.toFixed(2);

            // total_without_vat = parseFloat(total_without_vat);

            // document.getElementById('without_vat').value = total_without_vat;

            // /* -----------------------------------------*/

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

        // var vatModeSelected = $('input[name="vat_mode"]:checked').val();

        // if (vatModeSelected == null) {
        //     // $('#vatModeAlert').text('Please select VAT mode first.');
        //     alert('Please select VAT mode first.');
        //     $("#product").val(null).trigger('change');
        //     return;

        // }

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
        $('input[name="mode"]').prop('checked', false); // Clear selected radio button
        $('#boxselect').val(nu); // Clear input field
        $('#boxselectenter').val(nu); // Clear input field
        $('#dozenselect').val(nu); // Clear input field
        $('#dozenselectenter').val(nu); // Clear input field
        $("#total").val(nu);
        $("#without_vat").val(nu);

        } else {  //new

            console.error('Product not found for ID: ' + x);

        }
    }

    $(document).ready(function() {
        $('.product-list').select2({
            theme: "classic"
        });
    });
</script>

<!-- {{-- <script type="text/javascript">
    function addVatColumns(radio) {
        var percentage_first = document.getElementById("percentage_first");
        var percentage_second = document.getElementById("percentage_second");

        var amount_first = document.getElementById("amount_first");
        var amount_second = document.getElementById("amount_second");

        var percentage_first_column = document.getElementById("percentage_first_column");
        var percentage_second_column = document.getElementById("percentage_second_column");

        var amount_first_coumn = document.getElementById("amount_first_coumn");
        var amount_second_coumn = document.getElementById("amount_second_coumn");

        if (radio.value === "1") {

            percentage_first.style.display = "table-cell";
            percentage_second.style.display = "table-cell";

            amount_first.style.display = "none";
            amount_second.style.display = "none";

            percentage_first_column.innerHTML =
                '<input type="text" name="vat" id="vat" class="form-control" onkeydown="addrowFields(event)" placeholder="vat in %" tabindex="15">';
            percentage_second_column.innerHTML =
                '<input type="text" name="vat_amount" id="vat_amount" class="form-control" placeholder="vat in amount" tabindex="16" readonly>';

            percentage_first_column.style.display = "table-cell";
            percentage_second_column.style.display = "table-cell";

            amount_first_coumn.style.display = "none";
            amount_second_coumn.style.display = "none";

        } else if (radio.value === "2") {

            amount_first.style.display = "table-cell";
            amount_second.style.display = "table-cell";

            percentage_first.style.display = "none";
            percentage_second.style.display = "none";

            amount_first_coumn.innerHTML =
                '<input type="text" name="vat_amount" id="vat_amount" class="form-control" onkeydown="addrowFields(event)" placeholder="vat in amount" tabindex="15">';
            amount_second_coumn.innerHTML =
                '<input type="text" name="vat" id="vat" class="form-control" placeholder="vat in %" tabindex="16" readonly>';

            amount_first_coumn.style.display = "table-cell";
            amount_second_coumn.style.display = "table-cell";

            percentage_first_column.style.display = "none";
            percentage_second_column.style.display = "none";
        }
    }
</script> --}} -->


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
        updateTotalAmount();
    });

    var serialNumber = 1;

    function addRow() {

        var selectedProductId = $("#product_id").val();

        // Check if the product is already in the addedProducts array
        if (addedProducts.includes(selectedProductId)) {
            // Product is already added, show an alert message
            alert("Product already added!");
            return;
        }

        var selectedMode = $('input[name="mode"]:checked').val();
        var boxSelect = $('#boxselect').val();
        var boxSelectEnter = $('#boxselectenter').val();
        var dozenSelect = $('#dozenselect').val();
        var dozenSelectEnter = $('#dozenselectenter').val();

        if ((($("#product").val()) == "") || (!selectedMode) || (selectedMode === '1' && (!boxSelect || !
                boxSelectEnter)) || (selectedMode === '2' && (!dozenSelect || !
                dozenSelectEnter))) {
            return;
        }

        var name = ($("#product_name").val());
        // console.log(name);

        var buycost = ($("#buycost").val());
        // console.log(buycost);

        var sellcost = ($("#sellingcost").val());

        var Is_box_dozen = $('input[name="mode"]:checked').val();
        // console.log("Mode:", Is_box_dozen);

        var unit = ($("#unit").val());
        // console.log(unit);

        var pid = Number($("#product_id").val());

        var tot = ($("#total").val());


        // var vatDataMode = $('input[name="vat_mode"]:checked').val();

        // if (vatDataMode == null) {
        //     vatDataMode = 0;
        // }

        // var vat_amount_data = $('#vat_amount').val();
        // var vatpercen = $('#vat').val();

        // var vatop = $('#selectedOptionInput').val();

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
            '<td>';

        if (selectedMode === '1') {

            tr += '<input type="radio" name="boxdozen[' + pid + ']" value="1" checked disabled > Box' +

                '<input type="hidden" name="boxdozen[' + pid + ']" value=' + selectedMode + ' >';

            tr += '<input type="radio" name="boxdozen[' + pid + ']" value="2" disabled> Dozen';

        } else if (selectedMode === '2') {
            tr += '<input type="radio" name="boxdozen[' + pid + ']" value="1" disabled> Box';

            tr += '<input type="radio" name="boxdozen[' + pid + ']" value="2" checked disabled > Dozen' +

                '<input type="hidden" name="boxdozen[' + pid + ']" value=' + selectedMode + ' >';

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
        }

        // if (vatDataMode === '1') { //percenatge given
        //     tr += '<td><input type="text" value="' + vatpercen + '" name="vat_percentage[' + pid +
        //         ']" class="form-control" readonly></td>';

        //     tr += '<td><input type="text" value="' + vat_amount_data + '" name="vatamountdata[' + pid +
        //         ']" class="form-control" readonly></td>';

        // } else if (vatDataMode === '2') { // amount given
        //     tr += '<td><input type="text" value="' + vat_amount_data + '" name="vatamountdata[' + pid +
        //         ']" class="form-control" readonly></td>';

        //     tr += '<td><input type="text" value="' + vatpercen + '" name="vat_percentage[' + pid +
        //         ']" class="form-control" readonly></td>';
        // }

        tr += '<td><input type="text" value="' + unit + '" name="unit[' + pid +
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

        $('input[name="mode"]').prop('checked', false); // Clear selected radio button
        $('#boxselect').val(nu); // Clear input field
        $('#boxselectenter').val(nu); // Clear input field
        $('#dozenselect').val(nu); // Clear input field
        $('#dozenselectenter').val(nu); // Clear input field

        // vat

        // $('#vat').val(nu); // Clear input field
        // $('#vat_amount').val(nu); // Clear input field
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
    $('#supplierdata').on('input', function() {
        var value = $(this).val();
        var id = $('#supplier [value="' + value + '"]').data('value');

        $('#supp_id').val(id);

    });
</script>

<!-- ---------------------------- VAT CONVERTION CALCULATION BASED ON QUANTITY  --------------------- -->
<!-- {{-- <script>
    $(document).ready(function() {

        $('input[name="mode"]').on('change', function() {

            var quantMode = $('input[name="mode"]:checked').val();

            function vatupdates() {

                if (quantMode == 1) { //box

                    let vatMode = $('input[name="vat_mode"]:checked').val();
                    // console.log("vat mode" + vatMode);

                    if (vatMode == 1) { //percentage to amount

                        $("#vat, #boxselectenter, #buycost").keyup(function() {

                            let vatpercentage = $("#vat").val();
                            let buycost = $("#buycost").val();
                            let quantity = $("#boxselectenter").val();

                            // console.log("vat percentage" + vatpercentage);
                            // console.log("box quantity inside" + quantity);

                            let vat_amount = (vatpercentage * buycost *
                                quantity) / 100;

                            // console.log("vat amount converted" + vat_amount);

                            $('#vat_amount').val(vat_amount);

                        });
                    } else if (vatMode == 2) { // amount to perecentage

                        $("#vat_amount, #boxselectenter, #buycost").keyup(function() {

                            let vatamount = $("#vat_amount").val();
                            let buycost = $("#buycost").val();
                            let quantity = $("#boxselectenter").val();

                            console.log("vat amount" + vatamount);
                            console.log("box quantity inside" + quantity);

                            var vatpercenatge1 = (vatamount * 100) / (buycost *
                                quantity);

                            var final_percen = (!isNaN(vatpercenatge1)) ? vatpercenatge1 : 0;

                            console.log("vat percentage converted" + final_percen);

                            $('#vat').val(final_percen.toFixed(5));

                        });
                    }

                } else if (quantMode == 2) { //dozen

                    let vatMode = $('input[name="vat_mode"]:checked').val();
                    // console.log("vat mode" + vatMode);

                    if (vatMode == 1) {

                        $("#vat, #dozenselect, #buycost").keyup(function() {

                            let vatpercentage = $("#vat").val();
                            let buycost = $("#buycost").val();
                            let quantity = $("#dozenselectenter").val();

                            // console.log("vat percentage" + vatpercentage);
                            // console.log("dozen quantity inside" + quantity);

                            let vat_amount = (vatpercentage * buycost *
                                quantity) / 100;

                            // console.log("vat amount converted" + vat_amount);

                            $('#vat_amount').val(vat_amount);

                        });
                    } else if (vatMode == 2) { //amount to perecentage

                        $("#vat_amount, #dozenselect, #buycost").keyup(function() {

                            let vatamount = $("#vat_amount").val();
                            let buycost = $("#buycost").val();
                            let quantity = $("#dozenselectenter").val();

                            // console.log("vat amount" + vatamount);
                            // console.log("dozen quantity inside" + quantity);

                            var vatpercenatge1 = (vatamount * 100) / (buycost *
                                quantity);

                            var final_percen_dozen = (!isNaN(vatpercenatge1)) ? vatpercenatge1 :
                                0;

                            // console.log("vat percentage converted" + vatpercenatge1);

                            $('#vat').val(final_percen_dozen.toFixed(5));

                        });
                    }
                }
            }

            vatupdates();
        });
    });
</script> --}} -->

<!-- --------------------------------------------------------------------------------------------- -->

<script>
    $(document).ready(function() {

        $('input[name="mode"]').on('click change', function() {
            var selectedMode = $('input[name="mode"]:checked').val();

            if (selectedMode == 1) {

                // $("#boxselectenter, #vat_amount, #vat, #buycost").keyup(function() {

                $("#boxselectenter, #buycost, #vat, #rate").keyup(function() {

                    var box = $('#boxselect').val();
                    var boxEnter = $('#boxselectenter').val();
                    var buyco = $('#buycost').val() || 0;
                    var vaT = $('#vat').val() || 0;

                    var orivat = parseFloat(vaT);

                    var ratE = $('#rate').val();

                    // var vatam = (!isNaN(parseFloat($('#vat_amount').val()))) ? parseFloat($(
                    //     '#vat_amount').val()) : 0;

                    // var vat_mm = $('input[name="vat_mode"]:checked').val();

                    // if ((vat_mm == "1" || vat_mm == "2" || vat_mm == "0" && !isNaN(vatam))) {

                    //     console.log("vatam" + vatam);

                    //     var totalvalue_1 = boxEnter * buyco;
                    //     var totalvalue = totalvalue_1 + vatam;

                    // } else if (vat_mm == null || (vat_mm == "1" || vat_mm == "2" || vat_mm ==
                    //         "0" && isNaN(
                    //             vatam))) {

                    //     var totalvalue = boxEnter * buyco;

                    // }

                    /* first done */

                    // var totalvalue = boxEnter * buyco;

                    // var totwithvat = totalvalue + orivat;

                    // totwithvat = totwithvat.toFixed(2);

                    // totwithvat = parseFloat(totwithvat);

                    // $('#total').val(totwithvat);

                    // /* -------------- without VAT----------------*/

                    // var total_without_vatbox = boxEnter * buyco;

                    // total_without_vatbox = total_without_vatbox.toFixed(2);

                    // total_without_vatbox = parseFloat(total_without_vatbox);

                    // $('#without_vat').val(total_without_vatbox);

                    // /* -----------------------------------------*/

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

        $('input[name="mode"]').on('change', function() {

            $("#total").val("");
            $('#without_vat').val("");

        });

    });

    //when buycost changes

    $(document).ready(function() {
        $('input[name="mode"]').on('change', function() {
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

    // function validateForm() {
    //     // Validate Title
    //     var product = $("#productnamevalue").val();
    //     if (product == "" || product == null) {
    //         alert("Press the add button");
    //         return false;
    //     }
    //     console.log(product);
    //     return true;
    // }
</script>

<script type="text/javascript">
    function validateForm() {
        // Prevent the form from submitting multiple times
        const form = document.getElementById("purchase_form");
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
            submitBtn.innerText = "submit";

            return false;
        }

        // Your other validation conditions here
        // var commentd = $('input[name="comment"]').val();
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

    // function addrowFields(event) {
    //     if (event.keyCode === 13) { // Enter key
    //         event.preventDefault();
    //         addRow();

    //     }
    // }

    function whichmovehappen(event) {

        // var vatselect = $('input[name="vat_mode"]:checked').val();

        // if (vatselect == null) {

        if (event.keyCode === 13) { // Enter key
            event.preventDefault();
            addRow();
        }
        // } else if (vatselect == 1) {
        //     if (event.keyCode === 13) { // Enter key
        //         event.preventDefault();
        //         $('#vat').focus();
        //     }
        // } else if (vatselect == 2) {
        //     if (event.keyCode === 13) { // Enter key
        //         event.preventDefault();
        //         $('#vat_amount').focus();
        //     }
        // } else if (vatselect == 0) {
        //     // console.log("vat select" + vatselect);
        //     if (event.keyCode === 13) { // Enter key
        //         event.preventDefault();
        //         addRow();
        //     }
        // }
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
