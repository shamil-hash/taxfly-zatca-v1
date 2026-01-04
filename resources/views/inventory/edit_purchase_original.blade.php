<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Plexpay billing">
    <title>Edit Purchase</title>
    <link rel="stylesheet" href="/css/table_style.css">

    @include('layouts/usersidebar')
    <style>
        th,
        td {
            padding: 8px;
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

        #content {
            padding: 1rem 4rem;
        }

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
        @include('navbar.invnavbar')
        @else
        <x-logout_nav_user />
        @endif        <div align="right">
            @include('layouts.quick')

            <a href="" class="btn btn-info">Refresh</a>
        </div>
        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif
        <x-admindetails_user :shopdatas="$shopdatas" />

        <form method="post" action="/edit_purchasedetails/{{ $page }}/submit_editpurchase"
            enctype="multipart/form-data" id="edit_purchase_form" name="edit_purchase_form"
            onsubmit="return validateForm();">
            @csrf
            <h2>Edit Purchase</h2>
            <br />

            <input type="hidden" name="edit_comment" id="edit_comment">

            <div class="form-group row" style="padding-left:2rem;padding-right:2rem;">
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

                            @if ($payment_type == 1)
                                <?php $type = 'CASH'; ?>
                            @elseif ($payment_type == 2)
                                <?php $type = 'CREDIT'; ?>
                            @endif

                            <span class="input-group-addon" id="basic-addon4">Payment Mode</span>
                            <x-Form.input type="text" id="payment_mode" name="payment_mode"
                                value="{{ $type }}" readonly />
                            <input type="hidden" id="payment_type" name="payment_type" value="{{ $payment_type }}"
                                readonly />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon5">Invoice Date</span>
                            <x-Form.input type="date" id="invoice_date" name="invoice_date" class="invoice_date"
                                value="{{ $invoice_date }}" tabindex="2" />
                        </div>
                    </div>
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
                        <tr bgcolor="#20639B">
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
                            <td colspan="12"> <i class="glyphicon glyphicon-tags"></i> &nbsp EDIT PURCHASE</td>
                        </tr>
                        @foreach ($details as $detail)
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
                            <tr class="{{ $disabledClass }}" style="{{ $styling }}" {{ $disabled }}>
                                <td></td>
                                <td>
                                    <input type="text" name="productName[{{ $detail->product }}]"
                                        value="{{ $detail->product_name }}" class="form-control" readonly required
                                        {{ $read }} />

                                    <input type="hidden" name="productId[{{ $detail->product }}]"
                                        value="{{ $detail->product }}" class="form-control" readonly required
                                        {{ $read }} />
                                </td>
                                <td>
                                    <input type="text" name="buy_cost[{{ $detail->product }}]"
                                        value="{{ $detail->buycost }}" class="form-control" {{ $read }} />
                                </td>
                                <td>
                                    <input type="text" name="vat_r[{{ $detail->product }}]"
                                        value="{{ $detail->vat }}" class="form-control" {{ $read }} />
                                </td>
                                <td>
                                    <input type="text" name="rate_r[{{ $detail->product }}]"
                                        value="{{ $detail->rate }}" class="form-control rate-cal" readonly />
                                </td>
                                <td>
                                    <input type="text" name="sell_cost[{{ $detail->product }}]"
                                        value="{{ $detail->sellingcost }}" class="form-control"
                                        {{ $read }} />
                                </td>
                                <td>

                                    @if ($detail->is_box_or_dozen == 1 || $detail->is_box_or_dozen == 2)

                                        <input type="text" class="form-control mode-input"
                                            value="{{ $detail->is_box_or_dozen == 1 ? 'Box' : 'Dozen' }}" readonly>

                                        <input type="hidden" name="boxdozen[{{ $detail->product }}]"
                                            value="{{ $detail->is_box_or_dozen }}" readonly />
                                    @elseif ($detail->is_box_or_dozen == 3)
                                        <input type="text" class="form-control mode-input" value="Quantity"
                                            readonly />

                                        <input type="hidden"name="boxdozen[{{ $detail->product }}]"
                                            value="{{ $detail->is_box_or_dozen }}" readonly />
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
                                            {{ $read }} />
                                    </td>
                                @endif

                                <td @if ($boxOrDozen == 3) colspan="2" @endif>
                                    <input type="text" name="{{ $name2 }}[{{ $detail->product }}]"
                                        value="{{ $detail->quantity }}" class="form-control" {{ $readin }}
                                        {{ $read }} />
                                </td>

                                <td>
                                    <input type="text" name="unit[{{ $detail->product }}]"
                                        value="{{ $detail->unit }}" class="form-control" readonly />
                                </td>
                                <td>
                                    <input type="text" name="without_vat[{{ $detail->product }}]"
                                        value="{{ $detail->price_without_vat }}"
                                        class="form-control total-cal total_without_vat " readonly />
                                </td>
                                <td>
                                    <input type="text" name="total[{{ $detail->product }}]"
                                        value="{{ $detail->price }}" class="form-control total-cal total-amount"
                                        readonly />

                                    <input type="hidden" name="product_id[{{ $detail->product }}]"
                                        value="{{ $detail->product }}" class="form-control" readonly />
                                </td>
                            </tr>
                        @endforeach
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
                <h5 class="modal-title" id="inputModalLabel">Why did You Edit Purcahse: {{ $receipt_no }}</h5>
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
        total = parseFloat(total);
        $('#price').val(total);

        console.log(total);

        /-------------------/

        $('.total_without_vat').each(function() {
            totalwithoutvat += Number($(this).val());
        });

        $('#price_without_vat').val(totalwithoutvat);
        /------------------/
    }

    $('.addRow').on('click', function() {
        addRow();
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
