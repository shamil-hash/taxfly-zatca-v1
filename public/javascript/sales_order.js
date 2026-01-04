$(document).ready(function () {
    $(".js-user").select2({
        theme: "classic",
    });

    $(".product-list").select2({
        theme: "classic",
    });
    $("#barcodenumber").focus();

    $("#qty,#fixed_vat,#mrp").keyup(function () {
        var mrp = Number($("#mrp").val());
        var fixed_vat = Number($("#fixed_vat").val());
        netrate = (fixed_vat * mrp) / 100 + mrp;
        var netrate = Math.round(netrate * 1000) / 1000;
        $("#net_rate").val(netrate);
    });

    $("#qty,#mrp").keyup(function () {
        var total = 0;
        var netrate = Number($("#net_rate").val());
        var quantity = Number($("#qty").val());
        var mrp = Number($("#mrp").val());
        var fixed_vat = Number($("#fixed_vat").val());
        total = mrp * quantity;
        grandtotal = netrate * quantity;
        grandvat = (fixed_vat * total) / 100;
        netrate = (fixed_vat * mrp) / 100 + mrp;
        var grandtotal = Math.round(grandtotal * 1000) / 1000;
        var grandvat = Math.round(grandvat * 1000) / 1000;
        var netrate = Math.round(netrate * 1000) / 1000;
        $("#pricex").val(total);
        $("#price").val(grandtotal);
        $("#vat_amount").val(grandvat);
        $("#net_rate").val(netrate);
    });

    $("#fixed_vat,#net_rate").keyup(function () {
        var rate = 0;
        var vat_amount = 0;
        var fixed_vat = Number($("#fixed_vat").val());
        var net_rate = Number($("#net_rate").val());
        a = fixed_vat / 100;
        b = a + 1;
        c = net_rate / b;
        rate = c;
        var rate = Math.round(rate * 1000) / 1000;
        $("#mrp").val(rate);
    });

    $("#fixed_vat,#net_rate").keyup(function () {
        var vat_amount = 0;
        var quantity = Number($("#qty").val());
        var mrp = Number($("#mrp").val());
        var fixed_vat = Number($("#fixed_vat").val());
        var netrate = Number($("#net_rate").val());
        total = quantity * mrp;
        grandtotal = netrate * quantity;
        grandvat = (fixed_vat * total) / 100;
        netrate = (fixed_vat * mrp) / 100 + mrp;
        var grandtotal = Math.round(grandtotal * 1000) / 1000;
        var grandvat = Math.round(grandvat * 1000) / 1000;
        var netrate = Math.round(netrate * 1000) / 1000;
        $("#pricex").val(total);
        $("#price").val(grandtotal);
        $("#vat_amount").val(grandvat);
        $("#net_rate").val(netrate);
    });
    generateCustomerID();
});

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

function generateCustomerID() {
    var daterandom = Date.now() % 1000000;
    $("#cust_id").val(daterandom);
}

function creditUser(x) {
    var k = x;
    $("#cust_id").val(k);
    if (x != "") {
        $("#payment_type #creditoption").show();

        $.ajax({
            type: "get",
            url: "/gethistorysales/" + x,
            success: function (data) {
                var trn_number = data.trn_number;
                var phone = data.phone;
                var email = data.email;
                $("#trn_number").val(trn_number);
                $("#phone").val(phone);
                $("#email").val(email);
                $("#barcodenumber").focus();
            },
        });

        $("#payment_type").val(3).trigger("change");

        $("#payment_type #creditoption").prop("disabled", false);
    }
    if (x == "") {
        $("#payment_type #creditoption").hide();

        generateCustomerID();

        $("#trn_number").val("");
        $("#phone").val("");
        $("#email").val("");
        $("#payment_type").val(1).trigger("change");
        $("#barcodenumber").focus();

        // Disable the credit option in the payment dropdown
        $("#payment_type #creditoption").prop("disabled", true);
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

// Barcode add product code

$(document).ready(function () {
    $("#barcodenumber").on("keyup", function () {
        if ($(this).val() != "") {
            $("#selectproduct").removeClass("hide");
            $("#pselect").addClass("hide");
        }

        let bar = $(this).val();
        $("#barcodeproduct").empty();
        $.ajax({
            type: "GET",
            url: "getbarcodedata/" + bar,
            success: function (response) {
                var response = JSON.parse(response);
                console.log(response);
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

                    $("#qty").val('1');

                    $("#net_rate").val(element["netrate"]);

                    total = element["selling_cost"] * 1;
                    grandtotal = element["netrate"] * 1;
                    grandvat = (element["vat"] * total) / 100;

                    var grandtotal = Math.round(grandtotal * 1000) / 1000;
                    var grandvat = Math.round(grandvat * 1000) / 1000;

                    $("#pricex").val(total);
                    $("#price").val(grandtotal);
                    $("#vat_amount").val(grandvat);

                    // if (element['remaining_stock'] != 0) {
                    //     addRow();

                    // } else if(element['remaining_stock'] == 0) {
                    //     alert('Stock empty');
                    //     $('.addRow').off();

                    // }

                    // if(element['remaining_stock'] > 1 ) {
                    //     addRow();

                    // } else if ((element['remaining_stock'] == 0) || (element['remaining_stock'] < 1 && element['remaining_stock']> 0)) {
                    //     // alert('Stock empty');

                    //     alert("Remaining stock of left only :" + element['remaining_stock']);

                    //     $('.addRow').off();

                    // }

                    // if (element["remaining_stock"] > 1) {
                    //     addRow();
                    // } else if (
                    //     element["remaining_stock"] == 0 ||
                    //     (element["remaining_stock"] < 1 &&
                    //         element["remaining_stock"] > 0)
                    // ) {
                    //     alert(
                    //         "Remaining stock of left only :" +
                    //             element["remaining_stock"]
                    //     );

                    //     $(".addRow").off();
                    // }

                    var nu = "";
                    $("#mrp").val(nu);
                    $("#fixed_vat").val(nu);
                    $("#net_rate").val(nu);
                    $("#price").val(nu);
                    $("#pricex").val(nu);
                    $("#vat_amount").val(nu);
                    $("#prounit").val(nu);

                    /*--------------EDIT ADDED ROW'S QUANTITY AND BASED ON CHANGE VAT AMOUNT  & TOTAL AMOUNT----------------*/

                    $("#remain").val(element["remaining_stock"]);

                    var rema_stock = $("#remain").val();
                    var y = $("#product_name").val();
                    var u = Number($("#product_id").val());

                    var remaining_stockrow = parseFloat(rema_stock);

                    // $('input[name="quantity[' + u + ']"]').attr(
                    //     "max",
                    //     remaining_stockrow
                    // );

                    $('input[name="quantity[' + u + ']"]').on(
                        "input",
                        function () {
                            var product = $(
                                'input[name="quantity[' + u + ']"]'
                            ).val();
                            // if (product > remaining_stockrow) {
                            //     $('input[name="quantity[' + u + ']"]').val(
                            //         remaining_stockrow
                            //     );
                            // }

                            $('input[name="quantity[' + u + ']"]').keyup(
                                function () {
                                    var total = 0;
                                    var netrate = element["netrate"];

                                    var quantity = Number(
                                        $(
                                            'input[name="quantity[' + u + ']"]'
                                        ).val()
                                    );
                                    var mrp = element["selling_cost"];
                                    var fixed_vat = element["vat"];
                                    total = mrp * quantity;
                                    grandtotal = netrate * quantity;
                                    grandvat = (fixed_vat * total) / 100;

                                    var grandtotal =
                                        Math.round(grandtotal * 1000) / 1000;
                                    var grandvat =
                                        Math.round(grandvat * 1000) / 1000;

                                    $('input[name="price[' + u + ']"]').val(
                                        total
                                    );
                                    $("#pricex").val(total);
                                    $(
                                        'input[name="total_amount[' + u + ']"]'
                                    ).val(grandtotal);
                                    $(
                                        'input[name="vat_amount[' + u + ']"]'
                                    ).val(grandvat);
                                }
                            );
                        }
                    );

                    /*-------------------------------------------------------------------------------------------------*/
                });
            },
        });
    });
});

$(document).ready(function () {
    $('input[id="barcodenumber"]').change(function () {
        if ($(this).val() == "") {
            $("#pselect").removeClass("hide");
            $("#selectproduct").addClass("hide");
        } else {
            $("#pselect").addClass("hide");
            $("#selectproduct").removeClass("hide");
        }
    });

    $('input[id="barcodenumber"]').keyup(function () {
        if ($(this).val() == "") {
            $("#pselect").removeClass("hide");
            $("#selectproduct").addClass("hide");
        } else {
            $("#pselect").addClass("hide");
            $("#selectproduct").removeClass("hide");
        }
    });
});

var currentBoxNumber = 0;

$(".customer_id").keydown(function (event) {
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
$(".trn_no").keydown(function (event) {
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
$(".phone").keydown(function (event) {
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
$(".email").keydown(function (event) {
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
$(".barcodenumber").keydown(function (event) {
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
