<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Inventory</title>
    @include('layouts/usersidebar')
    <style>
        #id_confrmdiv {
            display: none;
            background-color: #FFF;
            border-radius: 5px;
            border: 1px solid #aaa;
            position: fixed;
            width: 200px;
            left: 50%;
            margin-left: -150px;
            padding: 6px 8px 8px;
            box-sizing: border-box;
            text-align: center;
        }

        #id_confrmdiv button {
            background-color: #ccc;
            display: inline-block;
            border-radius: 3px;
            border: 1px solid #aaa;
            padding: 2px;
            text-align: center;
            width: 80px;
            cursor: pointer;
        }

        #id_confrmdiv button:hover {
            background-color: #ddd;
        }

        #confirmBox .message {
            text-align: left;
            margin-bottom: 8px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2
        }

        th {
            background-color: #187f6a;
            color: white;
        }

        tr.x0 td {
            background-color: #d9534f;
        }

        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 1px solid #010101;
        }

        .pt-4 {
            padding-top: 3.4rem;
        }

        .customupload {
            padding: 3px;

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
        <div style="margin-left:15px;margin-top:18px;">

            @include('navbar.invnavbar')
        </div>
        @else
<x-logout_nav_user />
@endif        <div align="right">
            @include('layouts.quick')
            <a href="" class="btn btn-info">Refresh</a>
        </div>
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
            <br>
            <form method="post" action="/submitproductdraft/edit/{{ $draft_id }}" enctype="multipart/form-data"
                id="generateinvoiceform" onsubmit="return validateForm();">
                @csrf
                <table id="mytable" class="dataTables-example">
                    <thead>
                        <tr>
                            <th width="15%">Product</th>
                            <th width="15%">Product Details</th>
                            <th width="8%">Unit</th>
                            <th width="8%">Buy Cost</th>
                            <th width="8%">Purchase <br />{{$tax}}(%)</th>
                            <th width="8%">Rate</th>
                            <th width="8%">Inclusive Rate</th>
                            <th width="8%">Inclusive {{$tax}} Amount</th>
                            <th width="8%">Sell Cost</th>
                            <th width="8%">{{$tax}}</th>
                            <th width="8%">Category</th>
                            <th width="20%">Barcode</th>
                            <th width="10%"><a href="#" title="Add New Row">Action</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($details as $detail)
                            <tr class="x{{ $detail->status }}">
                                <input type="hidden" name="id[]" value="{{ $detail->id }}" class="form-control">
                                <td>
                                    <input type="text" name="productName[]" value="{{ $detail->product_name }}"
                                        class="form-control cheek_product_name">
                                    <div class="error-message" style="color: red;"></div>
                                </td>
                                <td>
                                    <textarea name="productdetails[]" cols="20" rows="1" class="form-control">{{ $detail->productdetails }}</textarea>
                                </td>
                                <td>
                                    <select name="unit[]" id="unit" class="form-control">
                                        <option value="">Select Unit</option>

                                        @foreach ($xunit as $xunits)
                                            <option value="{{ $xunits->unit }}"
                                                {{ $detail->unit == $xunits->unit ? 'selected' : '' }}>
                                                {{ $xunits->unit }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="buy_cost[]" value="{{ $detail->buy_cost }}"
                                        class="form-control">
                                </td>
                                <td>
                                    <input type="text" name="purchase_vat[]" value="{{ $detail->purchase_vat }}"
                                        class="form-control">
                                </td>
                                <td>
                                    <input type="text" name="rate[]" value="{{ $detail->rate }}"
                                        class="form-control" readonly>
                                </td>
                                <td>
                                    <input type="text" name="inclusive_rate[]" value="{{ $detail->inclusive_rate }}"
                                        class="form-control" readonly>
                                </td>
                                <td>
                                    <input type="text" name="inclusive_vat_amount[]"
                                        value="{{ $detail->inclusive_vat_amount }}" class="form-control" readonly>
                                </td>
                                <td>
                                    <input type="text" name="selling_cost[]" value="{{ $detail->selling_cost }}"
                                        class="form-control">
                                </td>
                                <td>
                                    <input type="text" name="vat[]" value="{{ $detail->vat }}"
                                        class="form-control">
                                </td>
                                <td>
                                    <select name="category_id[]" id="category" class="form-control"
                                        style="width: 200px">
                                        <option value="">Select Category</option>
                                        @foreach ($xdetails as $xdetail)
                                            @if ($xdetail->access == 1)
                                                <option value="{{ $xdetail->category_id }}"
                                                    {{ $detail->category_id == $xdetail->category_id ? ' selected' : '' }}>
                                                    {{ $xdetail->category_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input style="width: 150px;" id="exist_barcode" name="exist_barcode[]"
                                        class="form-control" placeholder="Barcode Number"><br>

                                    {{-- <!--@ if ($detail->barcode != null) -->
                                    <!--    <span>-->
                                    <!--        < ?php-->

                                    <!--        $color = [0, 0, 0];-->
                                    <!--        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();-->
                                    <!--        echo '<a href="data:image/png;base64,' . base64_encode($generator->getBarcode($detail->barcode, $generator::TYPE_CODE_128)) . '" download=' . $detail->barcode . ' ><img src="data:image/png;base64,' . base64_encode($generator->getBarcode($detail->barcode, $generator::TYPE_CODE_128)) . '"></a>';-->

                                    <!--        ?>-->
                                    <!--    </span>-->
                                    <!-- @ endif-->

                                    <!--@ if ($detail->barcode != null) -->
                                    <!--  <span>-->
                                    <!--     < ?php-->
                                    <!--         $generator = new Picqer\Barcode\BarcodeGeneratorPNG();-->
                                    <!--         $barcodeImage = $generator->getBarcode($detail->barcode, $generator::TYPE_CODE_128);-->

                                    <!--         if ($barcodeImage !== false) {-->
                                    <!--            $base64Image = base64_encode($barcodeImage);-->
                                    <!--            $downloadAttribute = 'download="' . $detail->barcode . '"';-->

                                    <!--            echo '<a href="data:image/png;base64,' . $base64Image . '" ' . $downloadAttribute . '><img src="data:image/png;base64,' . $base64Image . '"></a>';-->
                                    <!--            } else {-->
                                    <!--              echo 'Error generating barcode image.';-->
                                    <!--        }-->
                                    <!--     ?>-->
                                    <!--    </span>-->
                                    <!-- @ endif--> --}}
                                    <br><span>{{ $detail->barcode }}</span>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-danger"
                                        onclick="deleteRow(this);">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- <span>
                {{ $details->links() }}
            </span> --}}
                <br />
                <div align="right">
                    <input class="btn btn-primary" type="submit" value="submit" id="submitBtn">
                </div>
            </form>
        </div>
    </div>
</body>

</html>

<script>
    function deleteRow(button) {
        // Find the row to be deleted
        var row = button.parentNode.parentNode;
        // Remove the row from the table
        row.parentNode.removeChild(row);
    }
</script>
<script>
    /* -------------- Inclusive Rate Calculation ------------*/

    // Incluisve Rate and VAT Calculation
    function InclusiveRateCalculation(input) {
        // Get the current row
        var row = input.parentNode.parentNode;

        // Get the selling cost and VAT (%) input values
        var sell_cost = parseFloat(row.querySelector('[name="selling_cost[]"]').value) || 0;
        var vat_percent = parseFloat(row.querySelector('[name="vat[]"]').value) || 0;

        var InclusiveRate = sell_cost / (1 + (vat_percent / 100));

        var InclusiveVATAmount = sell_cost - InclusiveRate;

        // Update the inclusive rate and vat input field
        row.querySelector('[name="inclusive_rate[]"]').value = InclusiveRate.toFixed(3);
        row.querySelector('[name="inclusive_vat_amount[]"]').value = InclusiveVATAmount.toFixed(3);
    }

    function InclusiveRateCalculationForRow(row) {

        // Get the rate and purchase VAT input values
        var sell_cost = parseFloat(row.querySelector('[name^="selling_cost["]').value) || 0;
        var vat_percent = parseFloat(row.querySelector('[name^="vat["]').value) || 0;

        if ((sell_cost != '' || sell_cost != null) && (vat_percent != '' || vat_percent != null)) {

            var InclusiveRate = sell_cost / (1 + (vat_percent / 100));
            var InclusiveVATAmount = sell_cost - InclusiveRate;

            // Update the buy inclusive rate and vat input field
            row.querySelector('[name^="inclusive_rate["]').value = InclusiveRate.toFixed(3);
            row.querySelector('[name="inclusive_vat_amount[]"]').value = InclusiveVATAmount.toFixed(3);
        }
    }

    // Function to calculate Inclusive Rate for all existing rows on page load
    function InclusiveRateCalculationForAllRows() {
        var existingRows = document.querySelectorAll('#mytable tbody tr:not(.new-row)');

        existingRows.forEach(function(row) {
            InclusiveRateCalculationForRow(row);

            // Attach the function to oninput event for rate and purchase VAT fields
            var sell_costInput = row.querySelector('[name^="selling_cost["]');
            var vat_percentInput = row.querySelector('[name^="vat["]');

            sell_costInput.addEventListener('input', function() {
                InclusiveRateCalculationForRow(row);
            });

            vat_percentInput.addEventListener('input', function() {
                InclusiveRateCalculationForRow(row);
            });
        });
    }

    /* -------------------------------------------------------*/

    // Call the function when the page is loaded
    document.addEventListener('DOMContentLoaded', function() {
        InclusiveRateCalculationForAllRows();
    });
</script>

<script>
    function calculateRate(input) {
        // Get the current row
        var row = input.parentNode.parentNode;

        // Get the rate and purchase VAT input values
        var buyCost = parseFloat(row.querySelector('[name="buy_cost[]"]').value) || 0;
        var purchaseVat = parseFloat(row.querySelector('[name="purchase_vat[]"]').value) || 0;

        var rate = buyCost + (buyCost * purchaseVat / 100);

        // Update the buy cost input field
        row.querySelector('[name="rate[]"]').value = rate.toFixed(3);
    }

    // Function to calculate buy cost for a given row
    function calculateRateForRow(row) {
        // Get the rate and purchase VAT input values
        var buyCost = parseFloat(row.querySelector('[name^="buy_cost["]').value) || 0;
        var purchaseVat = parseFloat(row.querySelector('[name^="purchase_vat["]').value) || 0;
        var prateu = parseFloat(row.querySelector('[name^="rate["]').value);

        if ((buyCost == '' || buyCost == null) && (purchaseVat == '' || purchaseVat == null)) {
            row.querySelector('[name^="rate["]').value = prateu.toFixed(3);
        } else if ((buyCost != '' || buyCost != null) && (purchaseVat != '' || purchaseVat != null)) {
            // Calculate the buy cost
            var rate = buyCost + (buyCost * purchaseVat / 100);
            // Update the buy cost input field
            row.querySelector('[name^="rate["]').value = rate.toFixed(3);
        }
    }

    // Function to calculate buy cost for all existing rows on page load
    function calculateRateForAllRows() {
        var existingRows = document.querySelectorAll('#mytable tbody tr:not(.new-row)');

        existingRows.forEach(function(row) {
            calculateRateForRow(row);

            // Attach the function to oninput event for rate and purchase VAT fields
            var buycostInput = row.querySelector('[name^="buy_cost["]');
            var purchaseVatInput = row.querySelector('[name^="purchase_vat["]');

            buycostInput.addEventListener('input', function() {
                calculateRateForRow(row);
            });

            purchaseVatInput.addEventListener('input', function() {
                calculateRateForRow(row);
            });
        });
    }

    // Call the function when the page is loaded
    document.addEventListener('DOMContentLoaded', function() {
        calculateRateForAllRows();
    });
</script>

<script>
    function doSomething(id) {
        document.getElementById('id_confrmdiv').style.display = "block"; //this is the replace of this line
        $("#link").attr("href", "/deleteproduct/" + id);
    }
</script>
<script>
    function myFunction() {
        var x = $("#query").val();
        // ("href","/deleteproduct/"+x);
        console.log(x);
        window.location.replace('/search/' + x + '');
    }
</script>

<script>
    $(document).ready(function() {
        // var daterandom=Date.now();
        var number = rand(1000000, 9999999);
        $("#bardemo").val(number);
    });
</script>

<script>
    function validateForm() {

        // Prevent the form from submitting multiple times
        const form = document.getElementById("generateinvoiceform");
        const submitBtn = document.getElementById("submitBtn");

        if (submitBtn.disabled) {
            return false; // Prevent form submission
        }

        // Disable the submit button
        submitBtn.disabled = true;
        submitBtn.innerText = "Submitting...";

        // newly added rows

        var pro_name = document.getElementsByName("productName[]");
        var buycostv = document.getElementsByName("buy_cost[]");
        var sellingcostv = document.getElementsByName("selling_cost[]");
        var vatv = document.getElementsByName("vat[]");
        var categoryv = document.getElementsByName("category_id[]");
        var unitv = document.getElementsByName("unit[]");
        var categoryDropdowns = document.getElementsByName("category_id[]");
        var unitDropdowns = document.getElementsByName("unit[]");

        // already added rows

        var nameAlready = document.getElementsByName("productName[]");
        var buycostAlready = document.getElementsByName("buy_cost[]");
        var sellingcostAlready = document.getElementsByName("selling_cost[]");
        var vatAlready = document.getElementsByName("vat[]");
        var pppid = document.getElementsByName("id[]");

        var newpid = '';

        // Check product names using AJAX

        function checkProductNames(productNames, productId, rowType) {
            for (var i = 0; i < productNames.length; i++) {
                var productName = productNames[i].value;
                var initialProductName = productNames[i].getAttribute("data-initialValue");

                if (productName !== initialProductName) {
                    $.ajax({
                        url: '/newcheck_productname',
                        method: 'GET',
                        async: false,
                        data: {
                            new_pro_name: productName,
                            product_id: productId[i].value
                        },
                        success: function(response) {
                            if (response.status == true) {

                                event.preventDefault();
                                alert(response.new_product + ' Product name already exists in ' + rowType +
                                    ' row ' + (i + 1) + '!');
                                enableSubmitButton(submitBtn);
                                return false;
                            } else if (response.status == true) {
                                return true;
                            }
                        },
                        error: function() {
                            console.error('Error occurred while checking product name.');
                        }
                    });
                }
            }
            return true;
        }

        // Check product names in already added rows
        if (!checkProductNames(nameAlready, pppid, 'existing')) {
            event.preventDefault();
            enableSubmitButton(submitBtn);
            return false;
        }

        for (var i = 0; i < pro_name.length; i++) {
            if (pro_name[i].value === "") {
                event.preventDefault();
                alert("Please give a Product Name in new row " + (i + 1));
                enableSubmitButton(submitBtn);
                return false; // Exit the function to prevent further alerts
            }
        }

        for (var i = 0; i < unitv.length; i++) {
            if (unitv[i].value === "") {
                event.preventDefault();
                alert("Please select a Unit in new row " + (i + 1));
                enableSubmitButton(submitBtn);
                return false; // Exit the function to prevent further alerts
            }
        }

        for (var i = 0; i < buycostv.length; i++) {
            if (buycostv[i].value === "") {
                event.preventDefault();
                alert("Please give a Buycost in new row " + (i + 1));
                enableSubmitButton(submitBtn);
                return false; // Exit the function to prevent further alerts
            }
        }

        for (var i = 0; i < sellingcostv.length; i++) {
            if (sellingcostv[i].value === "") {
                event.preventDefault();
                alert("Please give a Selling Cost in new row " + (i + 1));
                enableSubmitButton(submitBtn);
                return false; // Exit the function to prevent further alerts
            }
        }

        for (var i = 0; i < vatv.length; i++) {
            if (vatv[i].value === "") {
                event.preventDefault();
                alert("Please give a {{$tax}} in new row " + (i + 1));
                enableSubmitButton(submitBtn);
                return false; // Exit the function to prevent further alerts
            }
        }

        for (var i = 0; i < categoryv.length; i++) {
            if (categoryv[i].value === "") {
                event.preventDefault();
                alert("Please select a Category in new row " + (i + 1));
                enableSubmitButton(submitBtn);
                return false; // Exit the function to prevent further alerts
            }
        }

        // already added rows

        for (var i = 0; i < nameAlready.length; i++) {
            if (nameAlready[i].value === "") {
                event.preventDefault();
                alert("Please give a Product Name in row " + (i + 1));
                enableSubmitButton(submitBtn);
                return false; // Exit the function to prevent further alerts
            }
        }


        for (var i = 0; i < unitDropdowns.length; i++) {
            if (unitDropdowns[i].value === "") {
                event.preventDefault();
                alert("Please select a unit in row " + (i + 1));
                enableSubmitButton(submitBtn);
                return false; // Exit the function to prevent further alerts
            }
        }

        for (var i = 0; i < buycostAlready.length; i++) {
            if (buycostAlready[i].value === "") {
                event.preventDefault();
                alert("Please give a Buycost in row " + (i + 1));
                enableSubmitButton(submitBtn);
                return false; // Exit the function to prevent further alerts
            }
        }

        for (var i = 0; i < sellingcostAlready.length; i++) {
            if (sellingcostAlready[i].value === "") {
                event.preventDefault();
                alert("Please give a SellingCost in row " + (i + 1));
                enableSubmitButton(submitBtn);
                return false; // Exit the function to prevent further alerts
            }
        }

        for (var i = 0; i < vatAlready.length; i++) {
            if (vatAlready[i].value === "") {
                event.preventDefault();
                alert("Please give a {{$tax}} in row " + (i + 1));
                enableSubmitButton(submitBtn);
                return false; // Exit the function to prevent further alerts
            }
        }

        for (var i = 0; i < categoryDropdowns.length; i++) {
            if (categoryDropdowns[i].value === "") {
                event.preventDefault();
                alert("Please select a category in row " + (i + 1));
                enableSubmitButton(submitBtn);
                return false; // Exit the function to prevent further alerts
            }
        }
        return true;
    }

    // Function to enable the submit button and reset its text
    function enableSubmitButton(submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerText = "submit";
    }
</script>


<script type="text/javascript">
    $(document).on('focusin', '.cheek_product_name', function() {
        // Store the initial value of the product name field when it gains focus
        $(this).data('initialValue', $(this).val());
    });

    function checkproname() {
        $(document).on('focusout', '.cheek_product_name', function() {
            var new_pro_name = $(this).val();
            var initial_pro_name = $(this).data('initialValue');
            var currentRow = $(this).closest('tr');
            var errorMessage = currentRow.find('.error-message');

            var product_id = $(this).closest('tr').find('input[name="id[]"]')
                .val() || ''; // Assuming you have an input for product ID

            // Check if the product name has changed
            if (new_pro_name !== initial_pro_name) {
                // Make an Ajax request to check if the product name already exists
                $.ajax({
                    url: '/newcheck_productname', // Update this URL to the actual route in your Laravel application
                    method: 'GET',
                    data: {
                        new_pro_name: new_pro_name,
                        product_id: product_id
                    },
                    success: function(response) {
                        if (response.status == true) {
                            // Product name already exists, display red text message
                            // errorMessage.text('Product name already exists!');
                            errorMessage.text(response.new_product +
                                ' Product name already exists!');
                        } else if (response.status == false) {
                            // Remove the error message if product name is unique
                            errorMessage.text('');
                        }
                    },
                    error: function() {
                        console.error('Error occurred while checking product name.');
                    }
                });
            } else {
                // Product name hasn't changed, clear the error message
                errorMessage.text('');
            }
        });
    }
    checkproname();
</script>
