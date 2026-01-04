// rate calculation

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

function addExtraColumns(selectedMode) {
    var box_dozen_header = document.getElementById("box_dozen_header");
    var items_header = document.getElementById("items_header");

    var quantity_header = document.getElementById("quantity_header");

    var boxDozenNo = document.getElementById("boxDozenNo");
    var itemColumn = document.getElementById("itemColumn");

    var QuantityColumn = document.getElementById("QuantityColumn");

    if (selectedMode === "1") {
        box_dozen_header.style.display = "table-cell";
        items_header.style.display = "table-cell";
        quantity_header.style.display = "none";

        boxDozenNo.innerHTML =
            '<input type="number" id="boxselect" name="boxselect" min="0" onkeydown="moveToboxenterFields(event)" class="form-control boxselect" placeholder="No. of Box" tabindex="13">';
        itemColumn.innerHTML =
            '<input type="number" id="boxselectenter" name="boxselectenter" min="0" onkeydown="whichmovehappen(event)" class="form-control boxselectenter" placeholder="Items in Box" tabindex="14">';

        QuantityColumn.innerHTML = "";

        boxDozenNo.style.display = "table-cell";
        itemColumn.style.display = "table-cell";
        QuantityColumn.style.display = "none";
    } else if (selectedMode === "2") {
        box_dozen_header.style.display = "table-cell";
        items_header.style.display = "table-cell";

        quantity_header.style.display = "none";

        boxDozenNo.innerHTML =
            '<input type="number" id="dozenselect" name="dozenselect" min="0" onkeydown="moveTodozenenterFields(event)" oninput="getValue()" onChange = "getValue()" class="form-control dozenselect" placeholder="No. of Dozen" tabindex="13">';
        itemColumn.innerHTML =
            '<input type="number" id="dozenselectenter" name="dozenselectenter" min="0" oninput="getValue()" onkeydown="whichmovehappen(event)" class="form-control dozenselectenter" placeholder="Items" tabindex="14">';

        QuantityColumn.innerHTML = "";

        boxDozenNo.style.display = "table-cell";
        itemColumn.style.display = "table-cell";
        QuantityColumn.style.display = "none";
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
            '<input type="number" id="boxselectenter" name="boxselectenter" min="0" onkeydown="whichmovehappen(event)" class="form-control boxselectenter" placeholder="Items in Box" tabindex="13">';

        QuantityColumn.style.display = "table-cell";
    } else {
        box_dozen_header.style.display = "none";
        items_header.style.display = "none";
        quantity_header.style.display = "none";

        boxDozenNo.innerHTML = "";
        itemColumn.innerHTML = "";
        QuantityColumn.innerHTML = "";

        boxDozenNo.style.display = "none";
        itemColumn.style.display = "none";
        QuantityColumn.style.display = "none";
    }
}

$(document).ready(function () {
    document.addEventListener("input", function (event) {
        // Check if the event is coming from the buycost input
        if (event.target && event.target.id === "buycost") {
            getValue();
        }
    });
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

$(document).ready(function () {
    $(".product-purchase").select2({
        theme: "classic",
    });
});

$(document).ready(function () {
    $(".quantity-purchase").select2({
        theme: "classic",
    });
});

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

// add row

var addedProducts = [];

function updateTotalAmount() {
    var total = 0;
    var totalwithoutvat = 0;

    $('input[name^="total"]').each(function () {
        if (
            addedProducts.includes(
                $(this).closest("tr").find('input[name^="product_id["]').val()
            )
        ) {
            total += Number($(this).val());
        }
    });

    total = total.toFixed(2);

    total = parseFloat(total);

    $("#price").val(total);
    /*-------------------*/
    $('input[name^="without_vat"]').each(function () {
        // totalwithoutvat += Number($(this).val());

        if (
            addedProducts.includes(
                $(this).closest("tr").find('input[name^="product_id["]').val()
            )
        ) {
            totalwithoutvat += Number($(this).val());
        }
    });
    $("#price_without_vat").val(totalwithoutvat);
    /*------------------*/
}

$(".addRow").on("click", function () {
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

    var selectedMode = $("select[name='mode']").val();

    var boxSelect = $("#boxselect").val();
    var boxSelectEnter = $("#boxselectenter").val();
    var dozenSelect = $("#dozenselect").val();
    var dozenSelectEnter = $("#dozenselectenter").val();

    if (
        $("#product").val() == "" ||
        !selectedMode ||
        (selectedMode === "1" && (!boxSelect || !boxSelectEnter)) ||
        (selectedMode === "2" && (!dozenSelect || !dozenSelectEnter)) ||
        (selectedMode === "3" && !boxSelectEnter)
    ) {
        return;
    }

    var name = $("#product_name").val();
    // console.log(name);

    var buycost = $("#buycost").val();
    // console.log(buycost);

    var sellcost = $("#sellingcost").val();

    var Is_box_dozen = $("select[name='mode']").val();
    // console.log("Mode:", Is_box_dozen);

    var unit = $("#unit").val();
    // console.log(unit);

    var pid = Number($("#product_id").val());

    var tot = $("#total").val();

    var tot_without_vat = $("#without_vat").val();

    var rate = $("#rate").val() || 0;
    var vat = $("#vat").val() || 0;

    var tr =
        "<tr>" +
        "<td>" +
        serialNumber +
        "</td>" +
        "<td>" +
        '<input type="text" id="productnamevalue" value="' +
        name +
        '" name="productName[' +
        pid +
        ']" class="form-control" readonly> <input type="hidden"  value="' +
        pid +
        '" name="productId[' +
        pid +
        ']" class="form-control">' +
        "</td>" +
        '<td><input type="text" value=' +
        buycost +
        ' id="buy_cost" name="buy_cost[' +
        pid +
        ']" class="form-control" readonly> </td>' +
        '<td><input type="text" value=' +
        vat +
        ' id="vat_r" name="vat_r[' +
        pid +
        ']" class="form-control" readonly> </td>' +
        '<td><input type="text" value=' +
        rate +
        ' id="rate_r" name="rate_r[' +
        pid +
        ']" class="form-control" readonly> </td>' +
        // '<input type="hidden" value=' + vatop + ' name="vatdatas[' + pid + ']" class="form-control"></td>' +
        '<td><input type="text" value=' +
        sellcost +
        ' id="sell_cost" name="sell_cost[' +
        pid +
        ']" class="form-control" readonly></td>' +
        "<td>";

    if (selectedMode === "1" || selectedMode === "2") {
        tr +=
            '<input type="text" class="form-control mode-input" value="' +
            (selectedMode === "1" ? "Box" : "Dozen") +
            '" readonly>';
        tr +=
            '<input type="hidden" name="boxdozen[' +
            pid +
            ']" value="' +
            selectedMode +
            '">';
    } else if (selectedMode === "3") {
        tr +=
            '<input type="text" class="form-control mode-input" value="Quantity" readonly>';
        tr +=
            '<input type="hidden" name="boxdozen[' +
            pid +
            ']" value="' +
            selectedMode +
            '">';
    }

    tr += "</td>";

    if (selectedMode === "1") {
        tr +=
            '<td><input type="text" value="' +
            boxSelect +
            '" name="boxCount[' +
            pid +
            ']" class="form-control" readonly></td>';
        tr +=
            '<td><input type="text" value="' +
            boxSelectEnter +
            '" name="boxItem[' +
            pid +
            ']" class="form-control" readonly></td>';
    } else if (selectedMode === "2") {
        tr +=
            '<td><input type="text" value="' +
            dozenSelect +
            '" name="dozenCount[' +
            pid +
            ']" class="form-control" readonly></td>';
        tr +=
            '<td><input type="text" value="' +
            dozenSelectEnter +
            '" name="dozenItem[' +
            pid +
            ']" class="form-control" readonly></td>';
    } else if (selectedMode === "3") {
        tr +=
            '<td colspan="2"><input type="text" value="' +
            boxSelectEnter +
            '" name="boxItem[' +
            pid +
            ']" class="form-control" readonly></td>';
    }

    tr +=
        '<td><input type="text" value="' +
        unit +
        '" name="unit[' +
        pid +
        ']" class="form-control" readonly></td>' +
        '<td><input type="text" value="' +
        tot_without_vat +
        '" name="without_vat[' +
        pid +
        ']" class="form-control" readonly></td>' +
        '<td><input type="text" value="' +
        tot +
        '" name="total[' +
        pid +
        ']" class="form-control" readonly></td>' +
        '<td><a href="#" class="btn btn-danger remove">-</a></td>' +
        '<input type="hidden" value=' +
        pid +
        ' name="product_id[' +
        pid +
        ']" class="form-control" >' +
        "</tr>";

    $("tbody").append(tr);

    addedProducts.push(selectedProductId);

    serialNumber++;

    var nu = "";

    $("#product_name").val(nu);
    $("#product_id").val(nu);
    $("#buycost").val(nu);
    $("#sellingcost").val(nu);
    $("#unit").val(nu);
    $("#total").val(nu);
    $("#product").val(nu).trigger("change");

    $("#boxselect").val(nu); // Clear input field
    $("#boxselectenter").val(nu); // Clear input field
    $("#dozenselect").val(nu); // Clear input field
    $("#dozenselectenter").val(nu); // Clear input field

    $("#without_vat").val(nu); 

    $("#rate").val(nu);
    $("#vat").val(nu);

    updateTotalAmount();
}

$("tbody").on("click", ".remove", function () {
    var removedProductId = $(this)
        .parent()
        .parent()
        .find('input[name^="product_id["]')
        .val();
    var index = addedProducts.indexOf(removedProductId);
    if (index !== -1) {
        addedProducts.splice(index, 1);
    }

    $(this).parent().parent().remove();
    updateTotalAmount();
});

$(document).ready(function () {
    $("#supplierdata").on("input", function () {
        var value = $(this).val();
        var id = $('#supplier [value="' + value + '"]').data("value");
        $("#supp_id").val(id);
    });

    // vat

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

    $('input[name="mode"]').on("change", function () {
        $("#total").val("");
        $("#without_vat").val("");
    });
});

$('form input:not([type="submit"])').keydown((e) => {
    if (e.keyCode === 13) {
        e.preventDefault();
        return false;
    }
    return true;
});
