// window.onload = function () {
//     $("#qty").val("");
//     $("#mrp").val("");
//     $("#fixed_vat").val("");
//     window.addEventListener(
//         "unload",
//         function (event) {
//             document.getElementById("billForm").reset();
//         },
//         false
//     );
// };

function getCreditId(selectedUsername) {
    // Get the selected option
    var selectedOption = document.querySelector(
        'option[value="' + selectedUsername + '"]'
    );

    // Get the user ID from the data-id attribute of the selected option
    var selectedUserId = selectedOption.getAttribute("data-id");

    // Update the credit_id input field with the selected user's ID
    document.getElementById("credit_id").value = selectedUserId;
}

function handleDiscount(discountTypeSelector, discountFieldSelector) {
    var discountty = $(discountTypeSelector).val();

    if (discountty == "none") {
        $(discountFieldSelector).attr("readonly", "readonly");
    } else if (discountty == "percentage" || discountty == "amount") {
        $(discountFieldSelector).prop("readonly", false);
    }

    $(discountTypeSelector).change(function () {
        var discounttype = $(discountTypeSelector).val();

        if (discounttype == "none") {
            $(discountFieldSelector).attr("readonly", "readonly");
        } else if (discounttype == "percentage" || discounttype == "amount") {
            $(discountFieldSelector).prop("readonly", false);
        }
    });
}

function setFocus(selector) {
    $(document).ready(function () {
        $(selector).focus();
    });
}
function handleVatSelection() {
    $(".disable-after-select-vat").on("change", function () {
        if ($(this).is(":checked")) {
            $("#vat_type_value").val($(this).val());
            $(".disable-after-select-vat").not(this).prop("disabled", true);
        } else {
            $("#vat_type_value").val("");
            $(".disable-after-select-vat").prop("disabled", false);
        }
    });
}

function discount_calcu_inclu() {
    var total = 0;
    var total_wo_discount = 0;
    var grandtotal_wo_disc = 0;

    var netrate = Number($("#net_rate").val());
    var quantity = Number($("#qty").val());
    var mrp = Number($("#mrp").val());
    var fixed_vat = Number($("#fixed_vat").val());
    var inc_rate = Number($("#inclusive_rate").val());
    var discounts = Number($("#discount").val());
    var discount_type = $("#discount_type").val();
            // Calculate total service cost and update MRP
            var totalServiceCost = 0;
            $("input[name='pservicecost']").each(function () {
                var serviceCost = Number($(this).val()) || 0;
                totalServiceCost += serviceCost;
            });

            var updatedMRP = mrp + totalServiceCost; // Add service cost to MRP

            // Use the updated MRP for further calculations
            mrp = updatedMRP;

    if (
        (discount_type == "percentage" || discount_type == "amount") &&
        discounts != 0
    ) {
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
    } else if (
        discount_type == "none" ||
        ((discount_type == "percentage" || discount_type == "amount") &&
            discounts == 0)
    ) {
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

    $("#pricex").val(total);
    $("#price").val(grandtotal);
    $("#vat_amount").val(grandvat);
    $("#net_rate").val(netrate);
    $("#price_wo_discount").val(grandtotal_wo_disc);
    $("#total_wo_discount").val(total_wo_discount);
}
function discount_calcu_exclus() {
    var total = 0;
    var total_wo_discount = 0;
    var grandtotal_wo_disc = 0;

    var netrate = Number($("#net_rate").val());
    var quantity = Number($("#qty").val());
    var mrp = Number($("#mrp").val());
    var fixed_vat = Number($("#fixed_vat").val());
    var discounts = Number($("#discount").val());
    var discount_type = $("#discount_type").val();
    var totalServiceCost = 0;
    $("input[name='pservicecost']").each(function () {
        var serviceCost = Number($(this).val()) || 0;
        totalServiceCost += serviceCost;
    });

    var updatedMRP = mrp + totalServiceCost; // Add service cost to MRP

    // Use the updated MRP for further calculations
    mrp = updatedMRP;

    var mrp_vat = mrp * (fixed_vat / 100);

    var grandvat = 0;
    var grandtotal = 0;

    if (discount_type === "percentage" || discount_type === "amount") {
        var disc_mrp =
            discount_type === "percentage"
                ? mrp - mrp * (discounts / 100)
                : mrp - discounts;

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
    grandtotal = Math.round(grandtotal * 1000) / 1000;
    grandvat = Math.round(grandvat * 1000) / 1000;
    netrate = Math.round(netrate * 1000) / 1000;

    $("#pricex").val(total);
    $("#price").val(grandtotal);
    $("#vat_amount").val(grandvat);
    $("#net_rate").val(netrate);
    $("#price_wo_discount").val(grandtotal_wo_disc);
    $("#total_wo_discount").val(total_wo_discount);
    $("#rate_discount").val(mrp_with_discount);
}

function handleVatTypeChange(vat_type, tax) {
    // $('input[name="vat_type_mode"]').on("change, input", function () {

    //     var vat_type = $('input[name="vat_type_mode"]:checked').val();
    //     var selectval = $("#vat_type_value").val();

    if (vat_type == 1) {
        /* extra column add */

        var inclusive_header = document.getElementById("inclusive_heading");
        var inclusive_rate_value = document.getElementById(
            "inclusive_rate_value"
        );

        inclusive_header.style.display = "table-cell";
        inclusive_rate_value.innerHTML =
            '<input type="number" step="any" id="inclusive_rate" name="inclusive_rate" class="form-control" readonly>';
        inclusive_rate_value.style.display = "table-cell";

        $("#vat_perc").text("VAT(%)-Inclus");
        $("#vat_ammi").text("Total VAT Amount-Inclus");

        /*----------------------------------*/

        $("#discount_type").change(function () {
            $("#discount").val("");
            var discount_type = $("#discount_type").val();
            var mrp = Number($("#mrp").val());
            var fixed_vat = Number($("#fixed_vat").val());
            var subInclusiveRate = mrp / (1 + fixed_vat / 100);

            if (discount_type == "none") {
                var InclusiveRate = subInclusiveRate;
            } else if (discount_type == "percentage") {
                var discounts = Number($("#discount").val());
                var inclus_disocunt_amt = mrp * (discounts / 100);
                var InclusiveRate = mrp - inclus_disocunt_amt;
            } else if (discount_type == "amount") {
                var discounts = Number($("#discount").val());

                var inclus_disocunt_amt = discounts;
                var InclusiveRate = mrp - inclus_disocunt_amt;
            }

            var InclusiveRate = Math.round(InclusiveRate * 1000) / 1000;
            $("#inclusive_rate").val(InclusiveRate);
        });

        $("#mrp, #fixed_vat, #discount, #discount_type").keyup(function () {
            var mrp = Number($("#mrp").val());
            var pservicecost = Number($("#pservicecost").val()) || 0;
                mrp+=pservicecost;
            var fixed_vat = Number($("#fixed_vat").val());
            var discounts = Number($("#discount").val());
            var discount_type = $("#discount_type").val();

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
            $("#inclusive_rate").val(InclusiveRate);
        });

        $("#qty, #mrp, #discount, #discount_type").keyup(function () {
            discount_calcu_inclu();
        });

        $("#fixed_vat,#net_rate, #discount, #discount_type").keyup(function () {
            discount_calcu_inclu();
        });

        $("#qty, #discount, #discount_type").keyup(function () {
            $("#discount_type").change(function () {
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
                    netrate = mrp;

                    grandtotal = netrate * quantity;
                    grandtotal_discount = grandtotal;

                    total = inc_rate * quantity;
                    total_discount = total;

                    var subvat_tot = grandtotal / (1 + fixed_vat / 100);
                    grandvat = grandtotal - subvat_tot;

                    grandtotal_discount =
                        Math.round(grandtotal_discount * 1000) / 1000;
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
        var ratediscount_header = document.getElementById(
            "ratediscount_heading"
        );
        var ratediscount_value = document.getElementById("rate_discount_value");

        ratediscount_header.style.display = "table-cell";
        ratediscount_value.innerHTML =
            '<input type="number" step="any" id="rate_discount" name="rate_discount" class="form-control" aria-label="Exclusive Rate" readonly>';
        ratediscount_value.style.display = "table-cell";

        $("#vat_perc").text("VAT(%)-Exclus");
        $("#vat_ammi").text("Total VAT Amount-Exclus");

        $("#mrp,#fixed_vat,#discount,#discount_type").keyup(function () {
            var mrp = Number($("#mrp").val());
            var fixed_vat = Number($("#fixed_vat").val());
            var discounts = Number($("#discount").val());
            var discount_type = $("#discount_type").val();

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

            netrate = Math.round(netrate * 1000) / 1000;
            $("#net_rate").val(netrate);
            $("#rate_discount").val(mrp_with_discount);
        });

        $("#qty,#mrp, #discount, #discount_type").keyup(function () {
            discount_calcu_exclus();
        });

        $("#fixed_vat,#net_rate, #discount , #discount_type").keyup(
            function () {
                discount_calcu_exclus();
            }
        );

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
    }

    $("#product").change(function () {
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

    $("#discount_type").change(function () {
        var nu = "";

        $("#price").val(nu);
        $("#vat_amount").val(nu);
        $("#pricex").val(nu);
        $("#discount").val(nu);
        $("#price_wo_discount").val(nu);
        $("#total_wo_discount").val(nu);

        if (vat_type == 2) {
            discount_calcu_exclus();
        }
    });
    // });
}

// Remove product row on click
function removeProductRow() {
    $("tbody").on("click", ".remove", function () {
        // Remove the product ID from the addedProducts array
        var productId = $(this)
            .closest("tr")
            .find('input[name^="productId["]')
            .val();
        var index = addedProducts.indexOf(productId);
        if (index !== -1) {
            addedProducts.splice(index, 1);
        }
        $("#switch").prop("disabled", false);

        $(this).closest("tr").remove();
        updateGrandTotalAmount();
    });
}

function generateCustomerID() {
    var daterandom = Date.now() % 1000000;
    $("#cust_id").val(daterandom);
}

function creditUser(x) {
    var k = x;
    // $('#cust_id').val(k);

    // $('#cust_id').val("");
    // $("#payment_type").val(3).trigger('change');

    var page = $("#page").val();

    if (x != "") {
        var role = $("#user_credit_role").val();
        // $("#payment_type #creditoption").show();

        if (role == 11) {
            $("#payment_type #creditoption").show();
            $("#payment_type").val(3).trigger("change");
            $("#payment_type #creditoption").prop("disabled", false);

            // if (page == "bill" || page == 'clone_bill') {
            //     $("#advance").show();
            // }
        } else if (role == "none") {
            $("#payment_type #creditoption").hide();
            $("#payment_type #creditoption").prop("disabled", true);
            $("#payment_type").val(1).trigger("change");

            if (page == "bill" || page == 'clone_bill') {
                $("#advance").hide();
            }
        }

        $.ajax({
            type: "get",
            url: "/gethistory/" + x,
            success: function (data) {
                var trn_number = data.trn_number;
                var phone = data.phone;
                var email = data.email;

                $("#trn_number").val(trn_number);
                $("#phone").val(phone);
                $("#email").val(email);

                var full_name = data.full_name;
                $("#cust_id").val(full_name);

                $("#barcodenumber").focus();
            },
        });
    }

    if (x == "") {
        $("#payment_type #creditoption").hide();
        if (page == "bill" || page == 'clone_bill') {
            $("#advance").hide();
        }
        // var daterandom = Date.now();
        // $("#cust_id").val(daterandom);

        generateCustomerID();

        $("#trn_number").val("");
        $("#phone").val("");
        $("#email").val("");
        $("#payment_type").val(1).trigger("change");
        $("#barcodenumber").focus();

        // Disable the credit option in the payment dropdown
        $("#payment_type #creditoption").prop("disabled", true);
    }

    handleCreditVisibility(x);

}

function handleBarcodeNumberKeyup(tax, branchid) {
    var ajaxRequest;

    $("#barcodenumber").on("keyup", function () {
        if ($(this).val() != "") {
            $("#selectproduct").removeClass("hide");
            $("#pselect").addClass("hide");
        }

        // Abort the previous AJAX request if it's still in progress
        if (ajaxRequest) {
            ajaxRequest.abort();
        }

        let bar = $(this).val();

        $("#barcodeproduct").empty();
        ajaxRequest = $.ajax({
            type: "GET",
            url: "getbarcodedata/" + bar,
            success: function (response) {
                var response = JSON.parse(response);

                // Check if VAT Type mode is selected
                var vat_type_selected = $(
                    'input[name="vat_type_mode"]:checked'
                ).val();

                if (vat_type_selected == null) {
                    alert("Please select "+tax+" Type first.");
                    $("#barcodenumber").val("");
                    $("#selectproduct").addClass("hide");
                    $("#pselect").removeClass("hide");
                    return;
                }

                $("#barcodeproduct").empty();
                response.forEach((element) => {
                    $("#barcodeproduct").append(
                        `<option selected value="${element["id"]}">${element["product_name"]}</option>`
                    );

                    $("#product_id").val(element["id"]);
                    $("#product_name").val(element["product_name"]);
                    $("#mrp").val(element["selling_cost"]);
                    $("#buycost").val(element["buy_cost"]);
                    $("#fixed_vat").val(element["vat"]);
                    $("#prounit").val(element["unit"]);

                    $("#qty").val("1");
                    $("#discount").val("0");
                    $("#discount_type").val("none");
                    $("#buycost_rate").val(element["rate"]);

                    var discounts = $("#discount").val();
                    var discount__type = $("#discount_type").val();
                    var vat_typeba = $(
                        'input[name="vat_type_mode"]:checked'
                    ).val();
                    var selectvalba = $("#vat_type_value").val();
                    var u = Number($("#product_id").val());

                    if (discount__type == "none") {
                        var total = 0,
                            total_wo_discount = 0,
                            grandtotal = 0,
                            grandtotal_wo_disc = 0;

                        if (selectvalba == 1) {
                            $("#inclusive_rate").val(element["inclusive_rate"]);
                            var inc_rate = parseFloat(
                                element["inclusive_rate"]
                            );

                            var inclusive_vate = parseFloat(
                                element["selling_cost"] - inc_rate
                            );

                            var netrate = element["selling_cost"];
                            netrate = Math.round(netrate * 1000) / 1000;

                            grandtotal = netrate * 1;
                            grandtotal_wo_disc = grandtotal;

                            total_wo_discount = inc_rate * 1;
                            total = total_wo_discount;

                            var grandvat =
                                grandtotal -
                                grandtotal / (1 + element["vat"] / 100);
                        } else if (selectvalba == 2) {
                            $("#rate_discount").val(element["selling_cost"]);

                            var mrp_vat =
                                parseFloat(element["selling_cost"]) *
                                (element["vat"] / 100);

                            var netrate =
                                mrp_vat + parseFloat(element["selling_cost"]);

                            var grandvat = mrp_vat * 1;
                            total = element["selling_cost"] * 1;

                            grandtotal = netrate * 1;
                            grandtotal_wo_disc =
                                (parseFloat(element["selling_cost"]) +
                                    mrp_vat) *
                                1;
                            total_wo_discount = element["selling_cost"] * 1;
                        }

                        grandtotal = Math.round(grandtotal * 1000) / 1000;
                        grandvat = Math.round(grandvat * 1000) / 1000;

                        $("#pricex").val(total);
                        $("#price").val(grandtotal);
                        $("#vat_amount").val(grandvat);
                        $("#net_rate").val(netrate);
                        $("#price_wo_discount").val(grandtotal_wo_disc);
                        $("#total_wo_discount").val(total_wo_discount);
                    }

                    if (
                        element["remaining_stock"] > 1 ||
                        element["remaining_stock"] == 1 ||branchid==3
                    ) {
                        addRow();
                    } else if (
                        element["remaining_stock"] == 0 ||
                        (element["remaining_stock"] < 1 &&
                            element["remaining_stock"] > 0)
                    ) {
                        alert(
                            "Remaining stock of left only :" +
                            element["remaining_stock"]
                        );

                        $("#selectproduct").addClass("hide");
                        $("#pselect").removeClass("hide");

                        $("#barcodenumber").val("");
                        $("#barcodeproduct").val("");
                        $("#qty").val("");
                        $("#inclusive_rate").val("");
                        $("#discount").val("");

                        $(".addRow").off();
                    }


                    var nu = "";
                    $("#mrp").val(nu);
                    $("#fixed_vat").val(nu);
                    $("#net_rate").val(nu);
                    $("#price").val(nu);
                    $("#pricex").val(nu);
                    $("#vat_amount").val(nu);
                    $("#prounit").val(nu);
                });
            },
        });
    });
}

function getPrevSellingCost() {
    // Event listener for phone number and product selection
    $("#cust_id, #product, #barcodeproduct").on("change", function () {
        // Get phone number and selected products
        var cust_id = $("#cust_id").val();
        var selectedProducts = $("#product_id").val(); // Changed from #product.val()
        var barcodeproducts = $("#barcodeproduct").val();

        // Get the CSRF token value from the meta tag
        var csrfToken = $('meta[name="csrf-token"]').attr("content");

        // Make an AJAX request to the Laravel controller
        $.ajax({
            url: "/get-previous-selling-cost",
            type: "POST",
            data: {
                cust_id: cust_id,
                selectedProducts: selectedProducts,
                barcodeProducts: barcodeproducts,
            },
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": csrfToken, // Include CSRF token in the headers
            },
            success: function (response) {
                $("#selling-cost-display").text("");

                $.each(response, function (productId, data) {
                  $("#selling-cost-display").html(
                    data.product_name +
                    " Previously sold with MRP " +
                    '<span style="font-size: 1.1em; font-weight: 500;">' + data.mrp + '</span>'
                );
                });
            },
            error: function (error) { },
        });
    });
}


function preventFormSubmitOnEnter() {
    $('form input:not([type="submit"])').keydown((e) => {
        if (e.keyCode === 13) {
            e.preventDefault();
            return false;
        }
        return true;
    });
}

function validateDiscount() {
    var discountType = document.getElementById("discount_type").value;
    var discountValue = document.getElementById("discount").value.trim();

    if (
        (discountType === "percentage" || discountType === "amount") &&
        discountValue === ""
    ) {
        alert("Please enter a discount value.");
        return false;
    }
    return true;
}

function TotalBillDiscount() {
    $("#total_discount").change(function () {
        var total_discount = $(this).val();

        if (total_discount == 1) {
            $("#discount_field_percentage").removeClass("hidden");
            $("#discount_field_amount").addClass("hidden");

            $("#discount_percentage").prop("disabled", false);
            $("#discount_amount").prop("disabled", true).val("");
        } else if (total_discount == 2) {
            $("#discount_field_amount").removeClass("hidden");
            $("#discount_field_percentage").addClass("hidden");

            $("#discount_amount").prop("disabled", false);
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

    $("#discount_percentage").on("input", function () {
        if ($(this).val() !== "") {
            $("#discount_amount").val("");
        }
        updateGrandTotalAmount();
    });

    $("#discount_amount").on("input", function () {
        if ($(this).val() !== "") {
            $("#discount_percentage").val("");
        }
        updateGrandTotalAmount();
    });
}

function GetCommonDataquantityEdit(u) {
    var total = 0;
    var total_wo_discount = 0;
    var grandtotal_wo_disc = 0;
    var grandtotal = 0;
    var netrate = Number($('input[name="net_rate[' + u + ']"]').val());
    var quantity = Number($('input[name="quantity[' + u + ']"]').val());
    var mrp = Number($('input[name="mrp[' + u + ']"]').val());
    var serviceprice = Number($('input[name="serviceprice[' + u + ']"]').val()) || 0;
    var fixed_vat = Number($('input[name="fixed_vat[' + u + ']"]').val());
    var discounts = Number($('input[name="dis_count[' + u + ']"]').val());
    var discount_type = $('select[name="dis_count_type[' + u + ']"]').val();
    //  var vat_type = $('input[name="vat_type_mode"]:checked').val();

    return {
        total,
        total_wo_discount,
        grandtotal,
        grandtotal_wo_disc,
        netrate,
        quantity,
        mrp,
        fixed_vat,
        discounts,
        discount_type,
        serviceprice,
        // vat_type,
    };
}

function QuantityChangeCalculation(u, vat_type) {
    $(
        'input[name="quantity[' +
        u +
        ']"], input[name="dis_count[' +
        u +
        ']"], select[name="dis_count_type[' +
        u +
        ']"]'
    ).keyup(function () {
        var {
            total,
            total_wo_discount,
            grandtotal,
            grandtotal_wo_disc,
            netrate,
            quantity,
            mrp,
            fixed_vat,
            discounts,
            discount_type,
            serviceprice,
            // vat_type,
        } = GetCommonDataquantityEdit(u);

        if (vat_type == 1) {
            var inc_rate = Number(
                $('input[name="inclusive_rate_r[' + u + ']"]').val()
            );
            mrp +=serviceprice;
            if (
                (discount_type == "percentage" || discount_type == "amount") &&
                discounts != 0
            ) {
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
            } else if (
                discount_type == "none" ||
                ((discount_type == "percentage" || discount_type == "amount") &&
                    discounts == 0)
            ) {
                netrate = mrp;
                var grandtotal = netrate * quantity;
                var grandtotal_wo_disc = grandtotal;

                var grandvat = grandtotal - grandtotal / (1 + fixed_vat / 100);
                total_wo_discount = inc_rate * quantity;
                total = total_wo_discount;
            }

            var netrate = Math.round(netrate * 1000) / 1000;

            $('input[name="net_rate[' + u + ']"]').val(netrate);
            $('input[name="price[' + u + ']"]').val(total);
            $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val(
                total_wo_discount
            );
        } else if (vat_type == 2) {
            var mrp_vat = mrp * (fixed_vat / 100);

            if (
                (discount_type == "percentage" || discount_type == "amount") &&
                discounts != 0
            ) {
                var disc_mrp =
                    discount_type === "percentage"
                        ? mrp - mrp * (discounts / 100)
                        : mrp - discounts;

                var mrp_disc_vat = disc_mrp * (fixed_vat / 100);
                netrate = mrp_disc_vat + disc_mrp;

                // vat
                var grandvat = mrp_disc_vat * quantity;
                total = disc_mrp * quantity;
                var mrp_with_discount = disc_mrp;
            } else if (
                discount_type == "none" ||
                ((discount_type == "percentage" || discount_type == "amount") &&
                    discounts == 0)
            ) {
                netrate = mrp_vat + mrp;
                var grandvat = mrp_vat * quantity;
                total = mrp * quantity;
                var mrp_with_discount = mrp;
            }

            grandtotal = netrate * quantity;
            grandtotal_wo_disc = (mrp + mrp_vat) * quantity;
            total_wo_discount = mrp * quantity;

            var netrate = Math.round(netrate * 1000) / 1000;

            $('input[name="net_rate[' + u + ']"]').val(netrate);
            $('input[name="price[' + u + ']"]').val(total);
            $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val(
                total_wo_discount
            );
            $('input[name="rate_discount_r[' + u + ']"]').val(
                mrp_with_discount
            );
        }

        var grandvat = Math.round(grandvat * 1000) / 1000;
        grandtotal = Math.round(grandtotal * 1000) / 1000;
        grandtotal_wo_disc = Math.round(grandtotal_wo_disc * 1000) / 1000;

        $('input[name="vat_amount[' + u + ']"]').val(grandvat);
        $('input[name="total_amount[' + u + ']"]').val(grandtotal);
        $('input[name="total_amount_wo_discount[' + u + ']"]').val(
            grandtotal_wo_disc
        );

        updateGrandTotalAmount();
    });
}

function addRowDiscountCalculation(u, remstk, vat_type, billq, page,branchid) {
    $(
        'input[name="quantity[' +
        u +
        ']"], input[name="dis_count[' +
        u +
        ']"], select[name="dis_count_type[' +
        u +
        ']"]'
    ).on("input", function () {
        var pro = $('input[name="quantity[' + u + ']"]').val();

        if (page == "bill") {
            if (pro > remstk) {
                $('input[name="quantity[' + u + ']"]').val(remstk);
            }
        } else if (page == "edit_bill" && branchid!=3) {
            var newlimit = remstk + billq;
            // if (pro > remstk) {
            if (pro > newlimit) {
                // $('input[name="quantity[' + u + ']"]').val(remstk);
                $('input[name="quantity[' + u + ']"]').attr("max", newlimit);
                $('input[name="quantity[' + u + ']"]').val(newlimit);
            }
        } else if (page == 'sales_order' || page == 'quotation' || page == 'bill_draft' || page == 'clone_bill') {
            var quantityInput = $('input[name="quantity[' + u + ']"]');
            var propp = Number(quantityInput.val());

            var quantityErrorSpan = $("#quantity_error_" + u);

            if (propp > remstk && branchid!=3) {

                $('input[name="quantity[' + u + ']"]').attr("max", remstk);

                quantityInput.addClass("is-invalid");
                quantityErrorSpan.html(
                    "Error: Remaining Stock Left: " +
                    remstk +
                    " <br/>Quantity exceeds by " +
                    (propp - remstk)
                );
            } else if (propp == 0 && remstk == 0 && branchid!=3) {
                quantityInput.addClass("is-invalid");
                quantityErrorSpan.html(
                    "Error: Remaining Stock Left: " +
                    remstk +
                    " <br/>No Quantity Left "
                );
            } else {
                quantityInput.removeClass("is-invalid"); // Remove validation class
                quantityErrorSpan.html(""); // Clear error message
            }
        } else if (branchid==3 || page == 'editsalesorder' || page == 'salesorderdraft' || page == 'quotationdraft' || page == 'performadraft' || page == 'deliverydraft' || page == "quot_to_salesorder"|| page == "clone_quotation") {

        }

        $('select[name="dis_count_type[' + u + ']"]').change(function () {
            var discount__type = $(
                'select[name="dis_count_type[' + u + ']"]'
            ).val();

            $('input[name="vat_amount[' + u + ']"]').val("");
            $('input[name="total_amount[' + u + ']"]').val("");
            $('input[name="price[' + u + ']"]').val("");
            $('input[name="total_amount_wo_discount[' + u + ']"]').val("");
            $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val("");
            $('input[name="dis_count[' + u + ']"]').val("");

            var discount = $('input[name="dis_count[' + u + ']"]').val();

            var {
                total,
                total_wo_discount,
                grandtotal,
                grandtotal_wo_disc,
                netrate,
                quantity,
                mrp,
                fixed_vat,
                discounts,
                discount_type,
                // vat_type,
            } = GetCommonDataquantityEdit(u);

            if (vat_type == 1) {
                if (
                    discount__type == "none" ||
                    ((discount__type == "percentage" ||
                        discount__type == "amount") &&
                        discount == "")
                ) {
                    var InclusiveRate = mrp / (1 + fixed_vat / 100);

                    var netrate = mrp;
                    netrate = Math.round(netrate * 1000) / 1000;

                    var grandtotal = netrate * quantity;
                    grandtotal_wo_disc = grandtotal;

                    total_wo_discount = InclusiveRate * quantity;
                    total = total_wo_discount;

                    var vat_am =
                        grandtotal - grandtotal / (1 + fixed_vat / 100);
                    grandvat = vat_am;

                    var InclusiveRate = Math.round(InclusiveRate * 1000) / 1000;

                    // $('input[name="vat_amount[' + u + ']"]').val(grandvat);
                    $('input[name="inclusive_rate_r[' + u + ']"]').val(
                        InclusiveRate
                    );
                }
            } else if (vat_type == 2) {
                if (
                    discount__type == "none" ||
                    ((discount__type == "percentage" ||
                        discount__type == "amount") &&
                        discount == "")
                ) {
                    var mrp_vat = mrp * (fixed_vat / 100);
                    netrate = mrp_vat + mrp;
                    var grandvat = mrp_vat * quantity;
                    total = mrp * quantity;

                    grandtotal = netrate * quantity;
                    grandtotal_wo_disc = (mrp + mrp_vat) * quantity;
                    total_wo_discount = mrp * quantity;
                    var mrp_with_discount = mrp;

                    var netrate = Math.round(netrate * 1000) / 1000;

                    $('input[name="rate_discount_r[' + u + ']"]').val(
                        mrp_with_discount
                    );
                }
            }

            var grandvat = Math.round(grandvat * 1000) / 1000;
            grandtotal = Math.round(grandtotal * 1000) / 1000;
            grandtotal_wo_disc = Math.round(grandtotal_wo_disc * 1000) / 1000;

            $('input[name="net_rate[' + u + ']"]').val(netrate);
            $('input[name="vat_amount[' + u + ']"]').val(grandvat);
            $('input[name="total_amount[' + u + ']"]').val(grandtotal);
            $('input[name="price[' + u + ']"]').val(total);
            $('input[name="total_amount_wo_discount[' + u + ']"]').val(
                grandtotal_wo_disc
            );
            $('input[name="price_withoutvat_wo_discount[' + u + ']"]').val(
                total_wo_discount
            );

            updateGrandTotalAmount();
        });

        QuantityChangeCalculation(u, vat_type);

        $('select[name="dis_count_type[' + u + ']"]').change(function () {
            var total = 0;
            var total_wo_discount = 0;

            $('input[name="dis_count[' + u + ']"]').keyup(function () {
                if (vat_type == 1) {
                    var discounts = Number(
                        $('input[name="dis_count[' + u + ']"]').val()
                    );
                    var discount__type = $(
                        'select[name="dis_count_type[' + u + ']"]'
                    ).val();
                    var mrp = Number($('input[name="mrp[' + u + ']"]').val());
                    var fixed_vat = Number(
                        $('input[name="fixed_vat[' + u + ']"]').val()
                    );
                    var quantity = Number(
                        $('input[name="quantity[' + u + ']"]').val()
                    );

                    if (
                        discount__type == "percentage" ||
                        discount__type == "amount"
                    ) {
                        var subInclusiveRate = mrp / (1 + fixed_vat / 100);
                        var subInclusiveRate =
                            Math.round(subInclusiveRate * 1000) / 1000;

                        if (discount__type == "percentage") {
                            var inclus_disocunt_amt = mrp * (discounts / 100);
                            var inclus_disocunt_amt =
                                Math.round(inclus_disocunt_amt * 1000) / 1000;
                        } else if (discount__type == "amount") {
                            var inclus_disocunt_amt = discounts;
                        }

                        var InclusiveRate = mrp - inclus_disocunt_amt;

                        var InclusiveRate =
                            Math.round(InclusiveRate * 1000) / 1000;
                        $('input[name="inclusive_rate_r[' + u + ']"]').val(
                            InclusiveRate
                        );

                        if (discount__type == "percentage") {
                            var discnt_amount = mrp * (discounts / 100);
                        } else if (discount__type == "amount") {
                            var discnt_amount = discounts;
                        }

                        var without_disc_amnt = mrp - discnt_amount;

                        var wo_disc_wo_vat =
                            without_disc_amnt / (1 + fixed_vat / 100);
                        var vat_am = without_disc_amnt - wo_disc_wo_vat;

                        netrate = without_disc_amnt;
                        var grandtotal = netrate * quantity;
                        var grandtotal_wo_disc = mrp * quantity;
                        var grandvat = vat_am * quantity;
                        total_wo_discount = subInclusiveRate * quantity;
                        total = wo_disc_wo_vat * quantity;

                        var grandvat = Math.round(grandvat * 1000) / 1000;
                        var netrate = Math.round(netrate * 1000) / 1000;
                        var grandtotal = Math.round(grandtotal * 1000) / 1000;
                        var total_wo_discount =
                            Math.round(total_wo_discount * 1000) / 1000;

                        $('input[name="vat_amount[' + u + ']"]').val(grandvat);
                        $('input[name="total_amount[' + u + ']"]').val(
                            grandtotal
                        );
                        $(
                            'input[name="total_amount_wo_discount[' + u + ']"]'
                        ).val(grandtotal_wo_disc);
                        $('input[name="net_rate[' + u + ']"]').val(netrate);
                        $('input[name="price[' + u + ']"]').val(total);
                        $(
                            'input[name="price_withoutvat_wo_discount[' +
                            u +
                            ']"]'
                        ).val(total_wo_discount);

                        updateGrandTotalAmount();
                    }
                }
            });
        });
    });

    $('input[name="dis_count[' + u + ']"]').keyup(function () {
        if (vat_type == 1) {
            var discounts = Number(
                $('input[name="dis_count[' + u + ']"]').val()
            );
            var discount_type = $(
                'select[name="dis_count_type[' + u + ']"]'
            ).val();
            var mrp = Number($('input[name="mrp[' + u + ']"]').val());
            var fixed_vat = Number(
                $('input[name="fixed_vat[' + u + ']"]').val()
            );

            if (discount_type == "percentage" || discount_type == "amount") {
                var subInclusiveRate = mrp / (1 + fixed_vat / 100);

                if (discount_type == "percentage") {
                    var inclus_disocunt_amt = mrp * (discounts / 100);
                } else if (discount_type == "amount") {
                    var inclus_disocunt_amt = discounts;
                }

                var InclusiveRate = mrp - inclus_disocunt_amt;

                var InclusiveRate = Math.round(InclusiveRate * 1000) / 1000;
                $('input[name="inclusive_rate_r[' + u + ']"]').val(
                    InclusiveRate
                );
            }
        }
    });

    // Add an event listener to the discount type select element
    $('select[name="dis_count_type[' + u + ']"]').on("change", function () {
        var discounttype = $('select[name="dis_count_type[' + u + ']"]').val();

        if (discounttype == "none") {
            var readonly = "readonly";
            $('input[name="dis_count[' + u + ']"]').attr("readonly", readonly);
        } else if (discounttype == "percentage" || discounttype == "amount") {
            $('input[name="dis_count[' + u + ']"]').prop("readonly", false);
        }
    });
}
