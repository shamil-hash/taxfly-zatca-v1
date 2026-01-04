<!-- add_product_form.blade.php -->
{{-- <form id="newProductForm" method="post" action="{{ route('submit_product') }}" enctype="multipart/form-data"> --}}

<form id="newProductForm" onsubmit="event.preventDefault(); submitProductModalForm(); ">
    @csrf
    <div class="container">
         <input type="hidden" name="page" id="page" value="{{ $page }}">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="product_name_new">Product Name</label>
                            <input type="text" class="form-control" id="product_name_new" name="product_name_new"
                                required>
                            <span id="chkproname"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="product_description">Product Description</label>
                            <textarea class="form-control" id="product_description" name="product_description" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="unit_new">Unit</label>
                            <select class="form-control" id="unit_new" name="unit_new" required>
                                <option value="">Select Unit</option>
                                <!-- Populate units from your database -->
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->unit }}">{{ $unit->unit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="buycost_new">Buy Cost</label>
                                <input type="text" class="form-control" id="buycost_new" name="buycost_new" required>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="purchase_vat_new">Purchase {{$tax}}</label>
                                <input type="text" class="form-control" id="purchase_vat_new"
                                    name="purchase_vat_new">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="rate_new">Rate</label>
                                <input type="text" class="form-control" id="rate_new" name="rate_new" required
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-10">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sellingcost_new">Sell Cost</label>
                                <input type="text" class="form-control" id="sellcost_new" name="sellcost_new"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vat_new">{{$tax}}</label>
                                <input type="text" class="form-control" id="vat_new" name="vat_new" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-10">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="rate_new">Inclusive Rate</label>
                                <input type="text" class="form-control" id="inlclusive_rate_new"
                                    name="inlclusive_rate_new" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="purchase_vat_new">Inclusive {{$tax}} Amount</label>
                                <input type="text" class="form-control" id="inlclusive_vat_new"
                                    name="inlclusive_vat_new" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="category_new">Category</label>
                            <select class="form-control" id="category_new" name="category_new" required>
                                <option value="">Select Category</option>
                                <!-- Populate categories from your database -->
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="barcode_newww">Barcode</label>
                            <input type="text" class="form-control" id="barcode_newww" name="barcode_newww">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        {{-- <button type="button" class="btn btn-primary" onclick="submitNewProductForm()">Submit</button> --}}
        {{-- <input class="btn btn-primary" type="submit" value="submit" id="addProductSingleBtn"> --}}

        <button type="submit" class="btn btn-primary" id="addProductSingleBtn">Submit</button>
    </div>
</form>


<script>
    $("#product_description, #buycost_new, #purchase_vat_new, #sellcost_new, #vat_new").click(function() {
        var new_pro_name = $("#product_name_new").val();
        $.ajax({
            type: 'GET',
            url: '/check-productname',
            data: {
                new_pro_name: new_pro_name
            },
            success: function(resp) {
                if (resp.status == true) {
                    // alert(resp.new_product);

                    $("#chkproname").html("<font color='red'>" + resp.new_product +
                        " Product Name Already Exist !</font>");
                } else if (resp.status == false) {
                    $("#chkproname").html("");
                }

            },
            error: function() {
                alert("Error");
            }
        });
    });

    $("#unit_new, #category_new").click(function() {
        var new_pro_name = $("#product_name_new").val();
        $.ajax({
            type: 'GET',
            url: '/check-productname',
            data: {
                new_pro_name: new_pro_name
            },
            success: function(resp) {
                if (resp.status == true) {
                    // alert(resp.new_product);

                    $("#chkproname").html("<font color='red'>" + resp.new_product +
                        " Product Name Already Exist !</font>");
                } else if (resp.status == false) {
                    $("#chkproname").html("");
                }

            },
            error: function() {
                alert("Error");
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
       // Trigger the calculation when the rate or vat input changes
        $('#buycost_new, #purchase_vat_new').on('input', function() {
            // Get the values of rate and vat
            var buycost_new = parseFloat($('#buycost_new').val()) || 0;
            var purchase_vat_new = parseFloat($('#purchase_vat_new').val()) || 0;

            // Calculate the buy cost
            var rate_new = buycost_new + (buycost_new * purchase_vat_new / 100);

            $('#rate_new').val(rate_new);
        });
    });

    $('#sellcost_new, #vat_new').on('input', function() {
            // Get the values of rate and vat
            var sellcost_new = parseFloat($('#sellcost_new').val()) || 0;
            var vat_new = parseFloat($('#vat_new').val()) || 0;

            var InclusiveRateNew = sellcost_new / (1 + (vat_new / 100));

            var InclusiveVATAmountNew = sellcost_new - InclusiveRateNew;

            $('#inlclusive_rate_new').val(InclusiveRateNew.toFixed(3));
            $('#inlclusive_vat_new').val(InclusiveVATAmountNew.toFixed(3));
        });
</script>

<script>
    function submitProductModalForm() {
        // Collect form data
        var formData = $('#newProductForm').serialize();

        // Submit the form using AJAX
        $.ajax({
                type: 'POST',
                url: '/add-product-modal',
                data: formData + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',

            })
            .done(function(response) {
                // Handle success
                console.log(response);
                alert(response.message);
                $('#newProductForm')[0].reset(); // Reset the form
                $('#addProductModal').modal('hide'); // Hide the modal

                // Optionally, update the product dropdown in the purchase form with the new product
                // updateProductDropdown(response.product.id, response.product.product_name);

                // Append the new product to the dropdown
                $('#product').append('<option value="' + response.product.id + '">' + response.product
                    .product_name + '</option>');

                // $('#product_name').val(response.product.product_name);
                // $('#product_id').val(response.product.id);

                $('#product').on('change click', function() {

                    var newselectedProductId = $(this).val();

                    if (newselectedProductId == response.product.id) {
                        fetchProductDetails(response.product.id);
                    }
                });

            })
            .fail(function(error) {
                // Handle errors
                alert('An error occurred');
            });
    }

    // Add an event listener for the product dropdown change
    function fetchProductDetails(productId) {

        var page = $('#page').val();

        $.ajax({
            type: 'GET',
            url: '/get-product-details/' + productId,
            dataType: 'json',
            success: function(response) {

                console.log("new product details" + response);
                if (response.success) {

                    // console.log(response);
                    if (page == "purchase") {
                        // Update the fields in the purchase form
                        $('#rate').val(response.product.rate);
                        $('#vat').val(response.product.purchase_vat);
                        $('#buycost').val(response.product.buy_cost);
                        $('#sellingcost').val(response.product.selling_cost);
                        $('#unit').val(response.product.unit);

                        // Update the hidden fields
                        $('#product_name').val(response.product.product_name);
                        $('#product_id').val(response.product.id);

                        var nu = "";

                        $('#boxselect').val(nu); // Clear input field
                        $('#boxselectenter').val(nu); // Clear input field
                        $('#dozenselect').val(nu); // Clear input field
                        $('#dozenselectenter').val(nu); // Clear input field
                        $("#total").val(nu);
                        $("#without_vat").val(nu);

                    } else if (page == "quotation") {

			            $('#mrp').val(response.product.selling_cost);
                        $('#fixed_vat').val(response.product.vat);
                        $('#prounit').val(response.product.unit);
                        $('#buycost').val(response.product.buy_cost);
                        $('#buycost_rate').val(response.product.rate);

                        // Update the hidden fields
                        $('#product_name').val(response.product.product_name);
                        $('#product_id').val(response.product.id);

                        $("#inclusive_rate").val(response.product.inclusive_rate);

                        var nu = "";
                        $("#qty").val(nu);
                        $("#net_rate").val(nu);
                        $("#price").val(nu);
                        $("#pricex").val(nu);
                        $("#vat_amount").val(nu);

                    }

                } else {
                    console.log(response.message);
                    // Handle the case where the product details are not found
                    // You may want to display an error message or take appropriate action
                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    }
</script>

{{-- <script type="text/javascript">
    function NewProValidate() {
        // Prevent the form from submitting multiple times
        const formnw = document.getElementById("newProductForm");
        const submitBtnnw = document.getElementById("addProductSingleBtn");

        if (submitBtnnw.disabled) {
            return false; // Prevent form submission
        }

        // Disable the submit button
        submitBtnnw.disabled = true;
        submitBtnnw.innerText = "Submitting...";

        var pro_pt = $('input[name="product_name_new"]').val();

        if (pro_pt == "" || pro_pt == null) {
            alert("Enter your product name");

            // Re-enable the submit button after alert
            submitBtnnw.disabled = false;
            submitBtnnw.innerText = "submit";

            return false;

        }
        // else {
        //     // Check for forbidden characters in receipt number
        //     const forbiddenCharacters = ["/", "\\", "?", "#"];

        //     if (forbiddenCharacters.some(char => rpt.includes(char))) {
        //         alert("Receipt No should not contain '/', '\\', '?' or '#' characters");

        //         // Re-enable the submit button after alert
        //         submitBtn.disabled = false;
        //         submitBtn.innerText = "Submit";

        //         return false;

        //     }
        else {
            var url = "/check-productname";
            var data = {
                new_pro_name: new_pro_name
            };

            $.getJSON(url, data, function(response) {
                if (response.exists) {
                    alert('The product Already Exists!');

                    // Re-enable the submit button after alert
                    submitBtnnw.disabled = false;
                    submitBtnnw.innerText = "submit";
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
</script> --}}
