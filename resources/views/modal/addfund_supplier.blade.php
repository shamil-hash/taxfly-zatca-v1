<style>
    .modal {
        position: fixed;
        top: 30%;
    }

    .modal-header {
        padding: 15px;
        border-bottom: 1px solid #ffffff;
        background-color: #f8f8f8;
        border-radius: 10px 10px 0 0;
        text-align: center;
    }

    .modal-content {
        background-color: #f8f8f8;
        border-radius: 10px;
        padding-left: 5%;
        padding-right: 5%;
        padding-top: 5%;
        padding-bottom: 5%;
    }

    .modal-lg {
        width: 40%;
    }

    #parent {
        margin-top: 2%;
        margin-left: 10%;
        margin-right: 10%;
    }

    .row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .col-sm-4 {
        width: 48%;
    }
    .style{
        margin-top:-200px;
    }
</style>

<form action="/addsupplier_creditfund" method="POST" id="myFormSupp" onsubmit="return validateForm();">
    @csrf
    <div class="modal fade text-left style" id="AddSupplierfund" tabindex="-1">
        <!-- <div class="modal-dialog modal-lg" role="document"> -->
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Ledger Fund | Supplier | Payment</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="hidden" class="form-control" id="supplierid" name="supplierid" style="width:100%;">
                            <label>SUPPLIER NAME:</label>
                            <input type="text" class="form-control" id="fundsuppliername" name="fundsuppliername" placeholder="USER ID:" style="width:100%;" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label>TOTAL DUE:</label>
                            <input type="text" class="form-control" id="dueamount" name="dueamount" style="width:100%;" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <label>BILL NO:</label>
                            <select name="receipt_no" id="receipt_no" class="form-control" style="width:100%;">
                                <option value="">Select BILL No</option>
                            </select>
                        </div>
                        <!--<div class="col-sm-4" id="products_div" style="display: none;">-->
                        <!--    <label for="receipt_product">Products:</label>-->
                        <!--    <select name="receipt_product" id="receipt_product" class="form-control" style="width:100%;">-->
                        <!--        <option value="">Select Product</option>-->
                        <!--    </select>-->
                        <!--</div>-->
                        <div class="col-sm-4" style="display:none;">
                            <label>INVOICE DUE:</label>
                            <input type="text" id="invoice_due_display" class="form-control" name="invoice_due" style="width:100%;" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                        <label for="categoryname">AMOUNT:</label>
                                <input type="text" class="form-control" id="addedcollectfund" name="addedcollectfund"
                                    placeholder="AMOUNT" style="width:100%;">
                        </div>
                        <div class="col-sm-4">
                            <label>PAYMENT TYPE:</label>
                            <select name="payment_option" id="payment_option" class="form-control" style="width:100%;">
                                <option value="">Select Payment Option</option>
                                <option value="1">CASH</option>
                                <option value="2">CHEQUE</option>
                                <option value="3">BANK</option>
                            </select>
                        </div>
                    </div>
                    <div id="check_details" class="row" style="display:none;">
                        <div class="col-sm-4">
                            <label>CHEQUE NUMBER:</label>
                            <input type="text" name="check_number" id="check_number" class="form-control" style="width:100%;" placeholder="Cheque Number" />
                        </div>
                        <div class="col-sm-4">
                            <label>DEPOSITING DATE:</label>
                            <input type="date" name="check_date" id="check_date" class="form-control" style="width:100%;" />
                        </div>
                    </div>

                    <div id="bank_details" class="row" style="display:none;">
                        <div class="col-sm-4">
                            <label>BANK NAME:</label>
                            <select name="bank_name" id="bank_name" class="form-control" style="width:100%;">
                                <option value="">Select Bank</option>
                                @foreach ($listbank as $bank)
                                    @if ($bank->status == 1)
                                        <option value="{{ $bank->id }}">{{ $bank->bank_name }} ({{ $bank->account_name }})</option>
                                    @endif
                                @endforeach
                            </select>
                            <input type="hidden" name="account_name" id="account_names" value="">
                            <input type="hidden" name="bank_id" id="bank_ids" value="">
                            <input type="hidden" name="current_balance" id="current_balance" value="" readonly >

                        </div>
                        <br>
                        <div class="col-sm-4">
                            <x-Form.label for="bank_transfer_date">TRANSFER DATE:</x-Form.label>
                            <x-Form.input type="date" name="bank_transfer_date" id="bank_transfer_date" class="form-control" style="width:100%;" />
                        </div>
                        <br>
                        <div class="col-sm-4">
                            <x-Form.label for="bank_ref_no">REFERENCE NUMBER:</x-Form.label>
                            <x-Form.input type="text" name="bank_ref_no" id="bank_ref_no" class="form-control" style="width:100%;" placeholder="Reference Number" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <label>REMARKS</label>
                            <textarea id="remarks" name="note" placeholder="Enter Remarks" class="form-control" style="width:95%;"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary"
                        id="submitBtnSupp">SUBMIT</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("myFormSupp");
        const submitBtn = document.getElementById("submitBtnSupp");


    });

    $(document).ready(function() {
        // Function to enable or disable debit note input
        function toggleDebitNoteInput(enabled) {
            $('#debitnote').prop('disabled', !enabled);
            if (!enabled) {
                $('#debitnote').val('');
                $('#debitnote').removeAttr('max');
            }
        }

        // Function to update debit note max attribute based on conditions
        function updateDebitNoteMax(totalPAmount, productPrices, selectedProductId) {
            if (selectedProductId && productPrices.length > 0) {
                var productIndex = parseInt(selectedProductId) - 1;
                if (!isNaN(productIndex) && productPrices[productIndex]) {
                    var productPrice = parseFloat(productPrices[productIndex]);
                    $('#debitnote').attr('max', productPrice);
                }
            } else {
                $('#debitnote').attr('max', totalPAmount);
            }
        }

        // Disable debit note input initially
        toggleDebitNoteInput(false);

        // Handle receipt number change
        $('#receipt_no').change(function() {
    var selectedReceiptNo = $(this).val();

    if (selectedReceiptNo !== '') {
        // Fetch total amount for the selected receipt number
        $.ajax({
            type: 'get',
            url: '/getpurchasetotalamount/' + selectedReceiptNo,
            success: function(data) {
                var totalPAmount = parseFloat(data.totalPAmount);
                var productPrices = data.productPrices ? data.productPrices.split(',') : [];

                // Set maximum limit for debit note
                $('#debitnote').attr('max', totalPAmount);

                // Update debit note limit based on selected product's price
                $('#receipt_product').change(function() {
                    var selectedProductId = $(this).val();
                    updateDebitNoteMax(totalPAmount, productPrices, selectedProductId);
                });

                // Enable debit note input
                toggleDebitNoteInput(true);
            }
        });

        // Fetch products and invoice due for the selected receipt number
        $.ajax({
            type: 'get',
            url: '/getpurchaseproducts/' + selectedReceiptNo,
            success: function(data) {
                var re_products = data.re_products;
                var invoice_due = data.invoice_due;

                $('#receipt_product').empty();
                $('#invoice_due_display').val(''); // Clear previous invoice due

                if (re_products.length > 0) {
                    $('#receipt_product').append($('<option>', {
                        value: '',
                        text: 'Select Product'
                    }));
                    for (var i = 0; i < re_products.length; i++) {
                        $('#receipt_product').append($('<option>', {
                            value: re_products[i].product,
                            text: re_products[i].product_name
                        }));
                    }
                    $('#products_div').show();
                } else {
                    $('#receipt_product').append($('<option>', {
                        value: '',
                        text: 'No products found'
                    }));
                    $('#products_div').show();
                }

                // Display invoice due in the input field
                if (invoice_due !== null) {
                    $('#invoice_due_display').val( invoice_due);
                } else {
                    $('#invoice_due_display').val('Invoice Due: Not Available');
                }
            },
            error: function(xhr, status, error) {
                console.log("Error fetching products and invoice due: ", error);
            }
        });
    } else {
        // No receipt number selected, disable debit note input
        toggleDebitNoteInput(false);

        // Hide products div when no receipt number selected
        $('#products_div').hide();
    }
});
        // Handle input changes in debitnote to enforce max limit
        $('#debitnote').on('input', function() {
            var val = parseFloat($(this).val());
            var max = parseFloat($(this).attr('max'));

            if (isNaN(val)) {
                $(this).val('');
            } else {
                var limitedValue = Math.min(val, max);
                $(this).val(limitedValue);
            }
        });

        // Initialize Select2 for dropdowns
        $('.credit_supplier').select2({ theme: "classic" });
        $('.receiptno_credit').select2({ theme: "classic" });

        // Toggle visibility of payment option fields
        $('#payment_option').on('change', function() {
            var payment_op = $('#payment_option').val();

            if (payment_op == 2) {
                $('#check_details').show();
                $('#bank_details').hide();  // Hide bank details
            } else if (payment_op == 3) {
                $('#bank_details').show();  // Show bank details
                $('#check_details').hide(); // Hide cheque details
            } else {
                $('#check_details').hide();
                $('#bank_details').hide();  // Hide bank details if not cheque or bank
            }
        });
    });
</script>

<script>

    $(document).ready(function () {
    // Event listener for the amount input field
    $('input[name="addedcollectfund"]').on('input', function () {
        // Get the values of amount, payment type, and current balance
        var amount = parseFloat($(this).val());
        var paymentType = $('#payment_option').val();
        var currentBalance = parseFloat($('#current_balance').val());

        // Check if payment type is 3 and amount exceeds current balance
        if (paymentType == '3' && amount > currentBalance) {
            alert('Insufficient bank balance');
            // Clear the input field or set it to a valid amount
            $(this).val(''); // Optionally, you can set it to a valid amount like currentBalance
            // Optionally, you can also disable the submit button
            $('#submitBtn').prop('disabled', true);
        } else {
            // Enable the submit button if the amount is valid
            $('#submitBtn').prop('disabled', false);
        }
    });

    // Event listener for the payment type dropdown
    $('#payment_option').on('change', function () {
        // Trigger the amount input event to recheck the condition
        $('input[name="addedcollectfund"]').trigger('input');
    });

    // Event listener for form submission
    $('form').on('submit', function (e) {
        var amount = parseFloat($('input[name="addedcollectfund"]').val());
        var paymentType = $('#payment_option').val();
        var currentBalance = parseFloat($('#current_balance').val());

        // Prevent submission if conditions are not met
        if (paymentType == '3' && amount > currentBalance) {
            e.preventDefault(); // Prevent form submission
            alert('Insufficient bank balance'); // Show alert
        }
    });
});

</script>

<script>
    function validateForm() {
     var accountSelect = document.getElementById('bank-name');
     const paymentTypeSelect = document.getElementById('payment_option');
     const addedFundValue = $('#addedcollectfund').val().trim();

     const paymentTypeValue = paymentTypeSelect.value;
     if (paymentTypeValue === '3' && accountSelect.value === "") {
         alert("Please select a Bank.");
         accountSelect.focus();
         return false;
     }
     if (addedFundValue === '') {
        alert("Please enter an Amount.");
        return false;
    }

    $('#submitBtnSupp').prop('disabled', true).text('Submitting...');

     return true;
 }

 </script>

<script>
 document.addEventListener('DOMContentLoaded', function () {
    const paymentTypeSelect = document.getElementById('payment_option');
    const bankNameSelect = document.getElementById('bank-name');
    const accountNameInput = document.getElementById('account_names');
    const bankIdInput = document.getElementById('bank_ids');
    const currentBalanceInput = document.getElementById('current_balance');

    paymentTypeSelect.addEventListener('change', function () {
        if (this.value == 2) {
            bankNameSelect.style.display = 'block';
        } else {
            bankNameSelect.style.display = 'none';
            bankIdInput.value = '';
            accountNameInput.value = '';
            currentBalanceInput.value = ''; // Clear current balance
        }
    });

    bankNameSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];

        if (selectedOption) {
            const accountName = selectedOption.text.match(/\(([^)]+)\)/)[1]; // Extract account name
            const bankId = selectedOption.value; // Get bank ID
            const currentBalance = selectedOption.getAttribute('data-current-balance'); // Get current balance

            // Set the values in the respective fields
            accountNameInput.value = accountName || ''; // Set account name or empty string
            bankIdInput.value = bankId || ''; // Set bank ID or empty string
            currentBalanceInput.value = currentBalance || ''; // Set current balance or empty string
        }
    });
});
</script>
<script>
    function validateNumericInput(input) {
        input.value = input.value.replace(/[^0-9]/g, '');
    }
</script>
<script>
    $(document).ready(function() {
        $('#receipt_no').on('change', function() {
            const selectedReceiptNo = $(this).val();
            if (selectedReceiptNo && selectedReceiptNo !== "") {
                $('#addedcollectfund').prop('disabled', false);
            } else {
                $('#addedcollectfund').prop('disabled', true);
            }
        });
    });
</script>
