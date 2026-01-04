<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Plex Bill User Report">
    <title>User Report</title>



    @include('layouts/usersidebar')
    <style>
        .form-group,
        .table {
            color: black;
            margin: 20px 0;
        }
  .btn-primary {
            background-color: #187f6a;
            color: #fff;
        }
        .table {
            background-color: #187f6a;
            border-color: #187f6a;
        }

        .table th,
        .table td {
            text-align: center;
            border-color: #187f6a;
        }

        .table thead {
            color: #ffffff;
            background-color: #187f6a;
        }

        .total-row {
            font-weight: bold;
        }



        .custom-option {
          background-color: #e0f7fa;
        color: black;
        font-weight: bold;

        }

        .calculation-item input {
            text-align: center;
            margin-top: 5px;
        }

        .con-data {
            margin-left: 5%;
            margin-right: 5%;
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
    <div style="margin-left:15px;margin-top:15px;">

        @include('navbar.reportnavbar')
    </div>
    @else
    <x-logout_nav_user />
    @endif
        <div class="con-data">
            <div align="center">

                @foreach ($userdatas as $userdata)
                <h1><b>{{ $userdata['name'] }}</b></h1>
                <span><b>Login Time :</b></span>
                {{ date('d-M-Y | h:i:s A', strtotime($userdata['last_login'])) }}
                @endforeach
            </div>
            <hr>
            <h4 class="text-center">Cash Drawer Form</h4>

            <form id="user_report_form" action="user-report-submit" method='post' onsubmit="return(validateSearch());">
                @csrf
                <div class="row">
                    <div class="col-md-3 calculation-item">
                        <label for="openingBalance">Opening Balance</label>
                        <input name="openingBalance" type="text" class="form-control" id="openingBalance" placeholder="Enter Opening Balance" tabindex="1">
                    </div>
                    <div class="col-md-3 calculation-item">
                        <label for="totalSales">Total Sales Amount</label>
                        <input type="text" class="form-control" id="totalSales" name="totalSales" value="{{ $totalsales - $totalretunsales }}" readonly>
                    </div>
                    <div class="col-md-3 calculation-item">
                        <label for="creditPayment">Credit Payment</label>
                        <input type="text" class="form-control" id="creditPayment" name="creditPayment" value="{{ $creditpayment }}" readonly>
                    </div>
                    <div class="col-md-3 calculation-item">
                        <label for="posBankSale">POS/Bank Sale Amount</label>
                        <input type="text" class="form-control" id="posBankSale" name="posBankSale" value="{{ (($pos + $servicepos) - $posreturn) }}" readonly>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3 calculation-item" style="margin-top: 10px;">
                        <label for="creditSale">Credit Sale Amount</label>
                        <input type="text" class="form-control" id="creditSale" name="creditSale" value="{{ $creditsalebuyproducts - $creditsalereturnfinal }}" readonly>
                    </div>
                    <div class="col-md-3 calculation-item" style="margin-top: 10px;">
                        <label for="service">Service</label>
                        <input type="text" class="form-control" id="service" name="service" value="{{ $service }}" readonly>
                    </div>
                    <div class="col-md-3 calculation-item" style="margin-top: 10px;">
                        <label for="income">Income</label>
                        <input type="text" class="form-control" id="income" name="income" value="{{ $totalIncomecash }}" readonly>
                    </div>
                    <div class="col-md-3 calculation-item" style="margin-top: 10px;">
                        <label for="expense">Expense</label>
                        <input type="text" class="form-control" id="expense" name="expense" value="{{ $totalexpensecash }}" readonly>
                    </div>
                    <div class="col-md-3 calculation-item" style="margin-top: 10px;">
                        <label for="totalCashInDraw">Total Cash in Draw</label>
                        <input type="number" class="form-control" id="totalCashInDraw" name="totalCashInDraw" readonly>
                    </div>
                </div>

                <!-- Total Expense Table -->


                <!-- Net Cash In Draw table -->
                <div class="form-group row hide">
                    <label class="col-sm-2 col-form-label">Net Cash In Draw</label>
                    <div class="col-sm-10">
                        <div class="card">
                            <div class="card-body">
                                <input type="text" id="netCashInDraw" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Cash Draw Details Table -->
                <h4 class="text-center">Cash Draw Details</h4>
                <table class="table table-bordered text-center" id="cashDrawTable">
                    <thead>
                        <tr>
                            <th>Note</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select class="form-control" name="note" id="note" class="product-list"
                                    style="width: 100%" tabindex="5">
                                    <option value="">select</option>
                                    <option value="1">1</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="200">200</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="2000">2000</option>
                                </select>
                            </td>
                            <td><input type="number" class="form-control" name="quantity" id="quantity"
                                    value="" min="0" tabindex="6"></td>
                            <td><input type="number" class="form-control" name="noteTotal" id="noteTotal" readonly>
                            </td>
                            <td><button type="button" class="btn btn-success addRow">+</button></td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Total Cash Draw</label>
                    <div class="col-sm-10">
                        <input type="number" name="cashDrawTotal" class="form-control" id="cashDrawTotal" readonly>
                    </div>
                </div>
                <div id="mismatchMessage" style="color: red; display: none;"></div>

                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button id='submitBtn' type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

</body>

</html>

<script>
    $(function() {
        $('#user_report_form').keypress(function(e) { //use form id
            if (e.which == 13) {
                validateSearch(); //-- to validate form
                $('#user_report_form').submit(); // use form id
                return false;
            }
        });
    });

    // to validate the form
    function validateSearch() {

        const form = document.getElementById("user_report_form");
        const submitBtn = document.getElementById("submitBtn");

        if (submitBtn.disabled) {
            return false; // Prevent form submission
        }

        // Disable the submit button
        submitBtn.disabled = true;
        submitBtn.innerText = "Submitting...";

        // var openv = $('#openingBalance').val();
        var totv = $('#cashDrawTotal').val();




        if (totv == "" || totv == null || totv == 0) {
            alert("Your cashDrawTotal is empty!");

            // Re-enable the submit button after alert
            submitBtn.disabled = false;
            submitBtn.innerText = "submit";

            return false;
        }
        if (exp == "" || exp == null) {
    if (confirm("Your total expense is empty. Do you want to submit the form anyway?")) {
        return true; // Allows the form to be submitted
    } else {
        submitBtn.disabled = false;
        submitBtn.innerText = "submit";
        return false; // Prevents the form from being submitted
    }
}
    }
</script>
<script>
    function preventFormSubmitOnEnter() {
        $('form input:not([type="submit"])').keydown((e) => {
            if (e.keyCode === 13) {
                e.preventDefault();
                return false;
            }
            return true;
        });
    }

    // Function to check for mismatch and prompt the user
    function checkForMismatch() {
        var netCashInDraw = parseFloat($('#netCashInDraw').val()) || 0;
        var cashDrawTotal = parseFloat($('#cashDrawTotal').val()) || 0;

        if (netCashInDraw !== cashDrawTotal) {
            $('#mismatchMessage').text('Mismatch detected between Net Cash in Draw and Cash Draw Details.').show();
        } else {
            $('#mismatchMessage').hide();
        }
    }

    function handleMismatchCheck() {
        clearTimeout(mismatchCheckTimeout);
        mismatchCheckTimeout = setTimeout(checkForMismatch, 300); // 300ms debounce time
    }

    $(document).ready(function() {
        $('.product-list').select2({
            theme: "classic",
        });
    });

    function calculateNetCashInDraw() {
        var totalCashInDraw = parseFloat($('#totalCashInDraw').val()) || 0;
        var totalExpense = parseFloat($('#totalExpense').val()) || 0;
        var netCashInDraw = totalCashInDraw - totalExpense;
        $('#netCashInDraw').val(netCashInDraw.toFixed(2));
        checkForMismatch();
    }

    $(document).ready(function() {
        var i = 0;

        function calculateTotalCashInDraw() {
            var openingBalance = parseFloat($('#openingBalance').val()) || 0;
            var totalSales = parseFloat($('#totalSales').val()) || 0;
            var creditPayment = parseFloat($('#creditPayment').val()) || 0;
            var posBankSale = parseFloat($('#posBankSale').val()) || 0;
            var creditSale = parseFloat($('#creditSale').val()) || 0;
            var expense = parseFloat($('#expense').val()) || 0;
            var income = parseFloat($('#income').val()) || 0;
            var service = parseFloat($('#service').val()) || 0;


            var totalCashInDraw = openingBalance + service + income  + totalSales + creditPayment - posBankSale - creditSale - expense;
            $('#totalCashInDraw').val(totalCashInDraw.toFixed(2));
            preventFormSubmitOnEnter();
            calculateNetCashInDraw();
        }
    $(document).ready(function() {
        $('#openingBalance, #totalSales, #creditPayment, #posBankSale, #creditSale').on('input', function() {
        calculateTotalCashInDraw();
    });

    // Initial calculation in case the fields have predefined values
        calculateTotalCashInDraw();
});

        function calculateCashDrawTotal() {
            var total = 0;
            $('#cashDrawTable tbody tr').each(function() {
                var quantity = parseFloat($(this).find('input[name="quantity"]').val()) || 0;
                var note = parseFloat($(this).find('select[name="note"]').val()) || 0;
                var rowTotal = quantity * note;
                $(this).find('input[name="noteTotal"]').val(rowTotal.toFixed(2));
                total += rowTotal;
                checkForMismatch(); // Accumulate row totals to get the total cash draw
            });
            preventFormSubmitOnEnter();

        }

        $('#cashDrawTable').on('input', 'input[name="quantity"], input[name="note"]', calculateCashDrawTotal);
        $(document).ready(function() {
            $('#cashDrawTable').on('click', '.addRow', function() {
                addRow();
                checkForMismatch();
                updateTotalAmount();
            });
        });

        function updateTotalAmount() {
            var total = 0;
            $('input[id^="total_cash_amt"]').each(function() {
                total += Number($(this).val());
            });
            $('#cashDrawTotal').val(total.toFixed(2));
            preventFormSubmitOnEnter();
            checkForMismatch();
            // calculateNetCashInDraw();
        }

        function addRow() {
            if ($("#note").val() === "" || $("#quantity").val() <= 0) {
                return;
            }
            var note = $("#note").val();
            var note_quant = Number($("#quantity").val());
            // Debugging: Check the value of note
            console.log("Note value before adding row:", note);
            i++;
            var total = note * note_quant;
            var tr = '<tr>' +

                '<td><select name="cashnote[' + i + ']" id="cashnote[' + i +
                ']" class="form-control" style="width: 100%">' +
                '<option value="">select</option>' +
                '<option value="1">1</option>' +
                '<option value="5">5</option>' +
                '<option value="10">10</option>' +
                '<option value="20">20</option>' +
                '<option value="50">50</option>' +
                '<option value="100">100</option>' +
                '<option value="200">200</option>' +
                '<option value="500">500</option>' +
                '<option value="1000">1000</option>' +
                '<option value="2000">2000</option>' +
                '</select></td>' +
                '<td><input type="number" value="' + note_quant + '" id="note_quantity[' + i +
                ']" name="note_quantity[' + i +
                ']" class="form-control" step="1" min="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" title="Numbers only"></td>' +
                '<td><input type="text" value="' + total + '" id="total_cash_amt[' + i +
                ']" name="total_cash_amt[' + i + ']" class="form-control" readonly></td>' +
                '<td><a href="#" class="btn btn-danger removeRow">-</a><input type="hidden" value="' + i +
                '" name="product_id[' + i + ']" class="form-control"></td>' +
                '' +
                '</tr>';
            $('#cashDrawTable tbody').append(tr);
            $("#note").val(null).trigger('change');
            $("#quantity").val("");

            $('select[name="cashnote[' + i + ']"]').val(note);
            checkForMismatch();
            calculateCashDrawTotal();
            updateTotalAmount();
            preventFormSubmitOnEnter();
        }

        $('#cashDrawTable').on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            checkForMismatch();
            preventFormSubmitOnEnter();
            calculateCashDrawTotal();
            updateTotalAmount();
        });

        $('#cashDrawTable').on('input', 'input[name^="note_quantity"]', function() {
            var quantity = parseFloat($(this).val()) || 0;
            var note = parseFloat($(this).closest('tr').find('select[name^="cashnote"]').val()) || 0;
            var total = quantity * note;
            $(this).closest('tr').find('input[name^="total_cash_amt"]').val(total.toFixed(2));
            checkForMismatch();
            preventFormSubmitOnEnter();
            calculateCashDrawTotal();
            updateTotalAmount();
        });

        $('#cashDrawTable').on('change', 'select[name^="cashnote"]', function() {
            var note = parseFloat($(this).val()) || 0;
            var quantity = parseFloat($(this).closest('tr').find('input[name^="note_quantity"]')
                .val()) || 0;
            var total = quantity * note;
            $(this).closest('tr').find('input[name^="total_cash_amt"]').val(total.toFixed(2));
            checkForMismatch();
            preventFormSubmitOnEnter();
            calculateCashDrawTotal();
            updateTotalAmount();
        });

        function calculateTotalExpense() {
            var total = 0;
            $('#expenseTable tbody tr').each(function() {
                var amount = parseFloat($(this).find('input[name^="amount"]').val()) || 0;
                total += amount;
            });
            $('#totalExpense').val(total.toFixed(2));
            preventFormSubmitOnEnter();
            calculateNetCashInDraw();
        }

        $('#expenseTable').on('click', '.addExpenseRow', function() {
            i++;
            var accountTypeValue = $('#accountType').val();
            var detailsValue = $('#details').val();
            var amountValue = $('#amount').val();
            var w = ($("#amount").val());
            if (($("#amount").val()) == "") {
                return;
            }
            var newRow = '<tr>' +
                '<td><input type="text" class="form-control" name="accountType[' + i +
                ']" id="accountType[' + i + ']" value="' + accountTypeValue + '"readonly></td>' +
                '<td><input type="text" class="form-control" name="details[' + i + ']" id="details[' +
                i + ']" value="' + detailsValue + '"></td>' +
                '<td><input type="number" class="form-control" name="amount[' + i + ']" id="amount[' +
                i + ']" value="' + amountValue + '" min="0" " readonly></td>' +
                '<td><button type="button" class="btn btn-danger removeExpenseRow">-</button></td>' +
                '<input type="hidden" value="' + i + '" name="expense_id[' + i +
                ']" class="form-control">' +
                '</tr>';

            $('#expenseTable tbody').append(newRow);

            $('#accountType').val('');
            $('#details').val('');
            $('#amount').val('');
            calculateNetCashInDraw();
            calculateTotalExpense();
            preventFormSubmitOnEnter();
        });

        $('#expenseTable').on('click', '.removeExpenseRow', function() {
            $(this).closest('tr').remove();
            calculateNetCashInDraw();
            calculateTotalExpense();
            preventFormSubmitOnEnter();

        });

        $('#openingBalance, #totalSales, #creditPayment, #posBankSale, #creditSale').on('input',
            calculateTotalCashInDraw);
    });

    // JavaScript for managing account type dropdown
    const accountTypeDropdown = document.getElementById('accountType');

    accountTypeDropdown.addEventListener('change', function() {
        const selectedOption = this.value;

        if (selectedOption === 'add_manage_category') {
            const newCategoryName = prompt('Enter the new category name:');

            if (newCategoryName && newCategoryName.trim() !== '') {
                const newOption = document.createElement('option');
                newOption.value = newCategoryName;
                newOption.textContent = newCategoryName;

                accountTypeDropdown.appendChild(newOption);
                accountTypeDropdown.value = newCategoryName;
            } else {
                // Reset to the default value or clear the selection
                accountTypeDropdown.value = '';
            }
        }
    });
</script>

<script>
    var notes = document.getElementById("note");
    var quantity = document.getElementById("quantity");
    var noteTotal = document.getElementById("noteTotal");

    notes.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });

    quantity.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });

    noteTotal.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
</script>

<script>
    var accountType = document.getElementById("accountType");
    var details = document.getElementById("details");
    var amount = document.getElementById("amount");

    accountType.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault(); // Prevent default form submission
            $('.addExpenseRow').click();
        }
    });

    details.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault(); // Prevent default form submission
            $('.addExpenseRow').click();
        }
    });

    amount.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault(); // Prevent default form submission
            $('.addExpenseRow').click();
        }
    });
</script>
