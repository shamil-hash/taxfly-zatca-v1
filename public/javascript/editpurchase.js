/* ------------------------------ rate calculation ----------------------------------------- */
$(document).ready(function () {
    // Trigger the calculation when the rate or vat input changes
    $("#buycost, #vat").on("input", function () {
        // Get the values of rate and vat
        var buyCost = parseFloat($("#buycost").val()) || 0;
        var vat = parseFloat($("#vat").val()) || 0;

        // Calculate the buy cost
        var rate = buyCost + (buyCost * vat) / 100;

        $("#rate").val(rate);
    });
});

/* ------------------------------ add extra columns ----------------------------------------- */

function addExtraColumns(selectedMode) {
    var box_dozen_header = document.getElementById("box_dozen_header");
    var items_header = document.getElementById("items_header");

    var quantity_header = document.getElementById("quantity_header");

    var boxDozenNo = document.getElementById("boxDozenNo");
    var itemColumn = document.getElementById("itemColumn");

    var QuantityColumn = document.getElementById("QuantityColumn");

    var empty_header = document.getElementById("select_qun");
    var emptyCol = document.getElementById("empty_Col");

    if (selectedMode === "1") {
        box_dozen_header.style.display = "table-cell";
        items_header.style.display = "table-cell";
        quantity_header.style.display = "none";
        empty_header.style.display = "none";

        boxDozenNo.innerHTML =
            '<input type="number" id="boxselect" name="boxselect" min="0" onkeydown="moveToboxenterFields(event)" class="form-control boxselect" placeholder="No. of Box" tabindex="9">';
        itemColumn.innerHTML =
            '<input type="number" id="boxselectenter" name="boxselectenter" min="0" onkeydown="whichmovehappen(event)" class="form-control boxselectenter" placeholder="Items in Box" tabindex="10">';

        QuantityColumn.innerHTML = "";

        boxDozenNo.style.display = "table-cell";
        itemColumn.style.display = "table-cell";
        QuantityColumn.style.display = "none";
        emptyCol.style.display = "none";
    } else if (selectedMode === "2") {
        box_dozen_header.style.display = "table-cell";
        items_header.style.display = "table-cell";

        quantity_header.style.display = "none";
        empty_header.style.display = "none";

        boxDozenNo.innerHTML =
            '<input type="number" id="dozenselect" name="dozenselect" min="0" onkeydown="moveTodozenenterFields(event)" oninput="getValue()" onChange = "getValue()" class="form-control dozenselect" placeholder="No. of Dozen" tabindex="9">';
        itemColumn.innerHTML =
            '<input type="number" id="dozenselectenter" name="dozenselectenter" min="0" oninput="getValue()" onkeydown="whichmovehappen(event)" class="form-control dozenselectenter" placeholder="Items" tabindex="10">';

        QuantityColumn.innerHTML = "";

        boxDozenNo.style.display = "table-cell";
        itemColumn.style.display = "table-cell";
        QuantityColumn.style.display = "none";
        emptyCol.style.display = "none";
    } else if (selectedMode === "3") {
        box_dozen_header.style.display = "none";
        items_header.style.display = "none";

        boxDozenNo.innerHTML = "";
        itemColumn.innerHTML = "";

        boxDozenNo.style.display = "none";
        itemColumn.style.display = "none";

        quantity_header.style.display = "table-cell";

        // Display the input field for Quantity mode
        QuantityColumn.innerHTML =
            '<input type="number" id="boxselectenter" name="boxselectenter" min="0" onkeydown="whichmovehappen(event)" class="form-control boxselectenter" placeholder="Items in Box" tabindex="9">';

        QuantityColumn.style.display = "table-cell";

        empty_header.style.display = "none";
        emptyCol.style.display = "none";
    } else {
        box_dozen_header.style.display = "none";
        items_header.style.display = "none";
        quantity_header.style.display = "none";

        empty_header.style.display = "table-cell";

        boxDozenNo.innerHTML = "";
        itemColumn.innerHTML = "";
        QuantityColumn.innerHTML = "";

        boxDozenNo.style.display = "none";
        itemColumn.style.display = "none";
        QuantityColumn.style.display = "none";

        emptyCol.style.display = "table-cell";
        
        // box_dozen_header.style.display = "none";
        // items_header.style.display = "none";
        // quantity_header.style.display = "none";

        // boxDozenNo.innerHTML = "";
        // itemColumn.innerHTML = "";
        // QuantityColumn.innerHTML = "";

        // boxDozenNo.style.display = "none";
        // itemColumn.style.display = "none";
        // QuantityColumn.style.display = "none";

        // empty_header.style.display = "table-cell";

        // // Display the input field for Quantity mode
        // emptyCol.innerHTML =
        //     '<input type="number" id="boxselectenter" name="boxselectenter" min="0" onkeydown="whichmovehappen(event)" class="form-control boxselectenter" placeholder="Items" tabindex="9">';

        // emptyCol.style.display = "table-cell";
    }
}

/* --------------------------- Dozen Calculation --------------------------------------- */

document.addEventListener("input", function (event) {
    // Check if the event is coming from the buycost input
    if (event.target && event.target.id === "buycost") {
        getValue();
    }
});

function getValue() {
    var dozenselect =
        parseFloat(document.getElementById("dozenselect").value) || 0;

    var dozenselectenter = dozenselect * 12;
    $("#dozenselectenter").val(dozenselectenter);

    var dozenselectenter =
        parseFloat(document.getElementById("dozenselectenter").value) || 0;

    $("#dozenselect, #buycost, #vat, #rate").keyup(function () {
        var buycos = $("#buycost").val() || 0;

        var vaT_dozcase = $("#vat").val() || 0;

        var doz_vat = parseFloat(vaT_dozcase);

        var doz_ratE = $("#rate").val();

        /* second done */

        var total = dozenselectenter * doz_ratE;

        total = total.toFixed(2);

        total = parseFloat(total);

        document.getElementById("total").value = total;

        /* -------------- without VAT----------------*/

        var total_without_vat = dozenselectenter * buycos;

        total_without_vat = total_without_vat.toFixed(2);

        total_without_vat = parseFloat(total_without_vat);

        document.getElementById("without_vat").value = total_without_vat;

        /* -----------------------------------------*/
    });
}

/* ---------------------------------------------------------------- */
$(document).ready(function () {
    $(".product-list").select2({
        theme: "classic",
    });
});

$(document).ready(function () {
    $(".quantity-list").select2({
        theme: "classic",
    });
});
/* ------------------------ Total Calculation ---------------------------------------- */

$(document).ready(function () {
    $('select[name="mode"]').on("click change", function () {
        var selectedMode = $('select[name="mode"]').val();

        if (selectedMode == 1 || selectedMode == 3) {
            $("#boxselectenter, #buycost, #vat, #rate").keyup(function () {
                if (selectedMode == 1) {
                    var box = $("#boxselect").val();
                }

                var boxEnter = $("#boxselectenter").val();
                var buyco = $("#buycost").val() || 0;
                var vaT = $("#vat").val() || 0;

                var orivat = parseFloat(vaT);

                var ratE = $("#rate").val();

                /* second done */

                var totalvalue = boxEnter * ratE;

                totalvalue = totalvalue.toFixed(2);

                totalvalue = parseFloat(totalvalue);

                $("#total").val(totalvalue);

                /* -------------- without VAT----------------*/

                var total_without_vatbox = boxEnter * buyco;

                total_without_vatbox = total_without_vatbox.toFixed(2);

                total_without_vatbox = parseFloat(total_without_vatbox);

                $("#without_vat").val(total_without_vatbox);

                /* -----------------------------------------*/
            });
        }
    });

    $('select[name="mode"]').on("change", function () {
        $("#total").val("");
        $("#without_vat").val("");
    });
});

//when buycost changes
$(document).ready(function () {
    $('select[name="mode"]').on("change", function () {
        $("#total").val("");
        $("#without_vat").val("");
    });
});

/* ------------------------------ Validation --------------------------------------- */

function validateForm() {
    // Prevent the form from submitting multiple times
    const form = document.getElementById("edit_purchase_form");
    const submitBtn = document.getElementById("submitBtn");

    if (submitBtn.disabled) {
        return false; // Prevent form submission
    }

    // Disable the submit button
    submitBtn.disabled = true;
    submitBtn.innerText = "Submitting...";

    // Show the modal
    $("#myModal").modal("show");

    return false;
}

/* --------------------------------------------------------------------- */

function moveToboxenterFields(event) {
    if (event.keyCode === 13) {
        // Enter key
        event.preventDefault();
        $("#boxselectenter").focus();
    }
}

function moveTodozenenterFields(event) {
    if (event.keyCode === 13) {
        // Enter key
        event.preventDefault();
        $("#dozenselectenter").focus();
    }
}

function whichmovehappen(event) {
    if (event.keyCode === 13) {
        // Enter key
        event.preventDefault();
        addRow();
    }
}

/* ------------------------- Rate Calculation ------------------------------ */

function rateCalculation(id) {
    $('input[name="buy_cost[' + id + ']"], input[name="vat_r[' + id + ']"]').on(
        "input",
        function () {
            // Get the values of rate and vat
            var buyCost_already =
                parseFloat($('input[name="buy_cost[' + id + ']"]').val()) || 0;
            var vat_already =
                parseFloat($('input[name="vat_r[' + id + ']"]').val()) || 0;

            // Calculate the buy cost
            var rate_already =
                buyCost_already + (buyCost_already * vat_already) / 100;

            $('input[name="rate_r[' + id + ']"]').val(rate_already);

            updateTotalAmount();
        }
    );
}
/* --------------------------------------------------------------------- */

/* ---------------------- Total Row Calculation ------------------------ */

function updateTotalRowAmount(id) {
    var selectedRowMode = $('input[name="boxdozen[' + id + ']"]').val();

    var itemInputName, buyCostInputName, vatInputName, rateInputName;

    if (selectedRowMode == 1 || selectedRowMode == 3) {
        itemInputName = "boxItem[" + id + "]";
    } else if (selectedRowMode == 2) {
        itemInputName = "dozenCount[" + id + "]";
    }

    buyCostInputName = "buy_cost[" + id + "]";
    vatInputName = "vat_r[" + id + "]";
    rateInputName = "rate_r[" + id + "]";

    $(
        'input[name="' +
            itemInputName +
            '"], input[name="' +
            buyCostInputName +
            '"], input[name="' +
            vatInputName +
            '"], input[name="' +
            rateInputName +
            '"]'
    ).on("input", function () {
        if (selectedRowMode == 1 || selectedRowMode == 3) {
            var itemEnter = $('input[name="' + itemInputName + '"]').val();
        } else if (selectedRowMode == 2) {
            var dozenselect =
                parseFloat($('input[name="dozenCount[' + id + ']"]').val()) ||
                0;

            var dozenselectenter = dozenselect * 12;
            $('input[name="dozenItem[' + id + ']"]').val(dozenselectenter);

            var itemEnter = dozenselectenter;
        }

        var buyCost = $('input[name="' + buyCostInputName + '"]').val() || 0;
        var vat = $('input[name="' + vatInputName + '"]').val() || 0;
        var rate = $('input[name="' + rateInputName + '"]').val() || 0;

        var totalValue = itemEnter * rate;
        totalValue = parseFloat(totalValue.toFixed(2));

        $('input[name="total[' + id + ']"]').val(totalValue);

        /* -------------- without VAT----------------*/
        var totalWithoutVat = itemEnter * buyCost;
        totalWithoutVat = parseFloat(totalWithoutVat.toFixed(2));

        $('input[name="without_vat[' + id + ']"]').val(totalWithoutVat);
        /* -----------------------------------------*/

        updateTotalAmount();
    });
}
/* --------------------------------------------------------------------- */
