<!-- <style>
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
    /* padding: 20px;
    width: 100%;
    max-width: 500px;
    margin: 0 auto; */
}
.modal-content {
        padding-left: 5%;
        padding-right: 5%;
        padding-top: 5%;
        padding-bottom: 5%;
    }
.modal-lg {
    width: 40%;
}

/* #parent {
    margin: 2% auto;
    text-align: center;
} */
#parent {
        margin-top: 2%;
        margin-left: 10%;
        margin-right: 10%;
    }

/* input[type="text"], select {
    border-radius: 10px;
    border: 2px solid #d1d1d1;
    padding: 8px 12px;
    font-size: 16px;
    width: 100%;
    box-sizing: border-box;
    margin-bottom: 15px;
} */



/* /* label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
    text-align: left;
} */

.row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.col-sm-4 {
    width: 48%;
}

/* #remarks {
    width: 60%;
    height: 40px;
    border-radius: 5px;
} */

/* input[readonly] {
    background-color: #e0e0e0;
}  */

/* .select2-container .select2-choice {
    height: 40px;
    line-height: 26px;
} */

/* #submitBtn {
    font-weight: bold;
    text-transform: uppercase;
} */
</style> -->
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
<form action="/addfundcredit2" method="POST" id="myForm" onsubmit="return validateForms();">
    @csrf
    <div class="modal fade text-left style" id="Addfund" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Ledger Fund | Customer | Receipt</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="hidden" class="form-control" id="creditid" name="creditid" style="width:100%;"readonly>
                            <label>CUSTOMER NAME</label>
                            <input type="text" class="form-control" id="fundusername" name="fundusername" placeholder="USER ID:" style="width:100%;" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label>TOTAL DUE</label>
                            <input type="text" class="form-control" id="due" name="due" style="width:100%;" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <label>SELECT INVOICE NO</label>
                            <select name="transaction_id" id="transaction_id" class="form-control" style="width:100%;">
                                <option value="">Select Transaction ID</option>
                            </select>
                        </div>
                        <!--<div class="col-sm-4" id="product_div" style="display: none;">-->
                        <!--    <label for="product_dropdown">PRODUCTS:</label>-->
                        <!--    <select name="product_dropdown" id="product_dropdown" class="credittrans form-control" style="width:100%;">-->
                        <!--        <option value="">Select Product</option>-->
                        <!--    </select>-->
                        <!--</div>-->
                        <div class="col-sm-4" style="display:none;">
                            <label>INVOICE DUE</label>
                            <input type="text" id="invoice_due_input" class="form-control" style="width:100%;" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <label>AMOUNT</label>
                            <input type="text" class="form-control" id="addedfund" name="addedfund" placeholder="AMOUNT" style="width:100%;" disabled>
                        </div>

                      <!-- Updated Payment Type Field -->
                        <div class="col-sm-4">
                            <label for="credit_payment_type">PAYMENT TYPE:</label>
                            <select name="credit_payment_type" id="credit_payment_type" class="form-control" style="width:100%; ">
                                <option value="">Select Payment Option</option>
                                <option value="1">CASH</option>
                                <option value="2">CHEQUE</option>
                                <option value="3">BANK</option>
                            </select>
                        </div>
                    </div>

                    <!-- Cheque Fields (Hidden by Default) -->
                    <div id="credit_user_check" class="row" style="display:none;">
                        <div class="col-sm-6">
                            <x-Form.label for="cheque_no">CHEQUE NUMBER:</x-Form.label>
                            <x-Form.input type="text" name="cheque_no" id="cheque_no" class="form-control" style="width:100%;" placeholder="Cheque Number" />
                        </div>
                        <br>
                        <div class="col-sm-6">
                            <x-Form.label for="cheque_date">DEPOSITING DATE:</x-Form.label>
                            <x-Form.input type="date" name="cheque_date" id="cheque_date" class="form-control" style="width:100%;" />
                        </div>
                    </div>

                    <!-- Bank Transfer Fields (Hidden by Default) -->
                    <div id="credit_user_bank" style="display:none;">
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-sm-4" style="padding-right: 10px;">
                            <x-Form.label for="bank_name">BANK NAME:</x-Form.label>
                            <select name="bank_name" id="bank_name" class="form-control" style="width: 100%; border-radius:5px;">
                                <option value="">Select Bank</option>
                                @foreach ($listbank as $bank)
                                    @if ($bank->status == 1)
                                        <option value="{{ $bank->id }}">{{ $bank->bank_name }} ({{ $bank->account_name }})</option>
                                    @endif
                                @endforeach
                            </select>
                                                        <input type="hidden" name="account_name" id="account_name" value="">

                        </div>
<br>
                        <div class="col-sm-4" style="padding-right: 10px;">
                            <x-Form.label for="bank_transfer_date">TRANSFER DATE:</x-Form.label>
                            <x-Form.input type="date" name="bank_transfer_date" id="bank_transfer_date" class="form-control" style="width: 100%;" />
                        </div>
<br>
                        <div class="col-sm-4">
                            <x-Form.label for="bank_ref_no">REFERENCE NUMBER:</x-Form.label>
                            <x-Form.input type="text" name="bank_ref_no" id="bank_ref_no" class="form-control" style="width: 100%;" placeholder="Reference Number" />
                        </div>
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
                        <button type="submit" class="btn btn-primary" id="submitBtn">SUBMIT</button>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
    // Initialize Select2 for dropdowns
    $(' .payment_type').select2({
        theme: "classic"
    });


    // Enable/disable credit note input based on conditions
    function toggleDebitNoteInput(enabled) {
        $('#creditnote').prop('disabled', !enabled);
        if (!enabled) {
            $('#creditnote').val('').removeAttr('max');
        }
    }
    toggleDebitNoteInput(false); // Initially disable

    // Handle changes in payment type dropdown
    $('#credit_payment_type').on('change', function() {
        const paymentType = $(this).val();
        $('#credit_user_check').toggle(paymentType === '2');
        $('#credit_user_bank').toggle(paymentType === '3');
    }).trigger('change'); // Trigger on page load to set correct visibility

    // Handle change in transaction ID
    $('#transaction_id').change(function() {
        const transactionId = $(this).val();

        if (transactionId) {
            // Fetch total amount and products for selected transaction ID
            $.get(`/gettotalamount/${transactionId}`, function(data) {
                const totalPAmount = parseFloat(data.totalPAmount);
                const productPrices = data.productPrices ? data.productPrices.split(',') : [];
                $('#creditnote').attr('max', totalPAmount);

                $('#product_dropdown').change(function() {
                    const selectedProductId = $(this).val();
                    updateDebitNoteMax(totalPAmount, productPrices, selectedProductId);
                });

                toggleDebitNoteInput(true); // Enable credit note input
            });

            $.get(`/getproducts/${transactionId}`, function(data) {
                const products = data.products;
                $('#product_dropdown').empty().append(
                    products.length > 0
                        ? '<option value="">Select Product</option>'
                        : '<option value="">No products found</option>'
                );

                products.forEach(product => {
                    $('#product_dropdown').append(
                        `<option value="${product.product_id}">${product.product_name}</option>`
                    );
                });
                $('#product_div').show();

                $('#invoice_due_input').val(data.invoice_due || 'No due found');
            });
        } else {
            toggleDebitNoteInput(false);
            $('#product_div').hide();
        }
    });

    // Handle max limit for credit note input
    $('#creditnote').on('input', function() {
        const val = parseFloat($(this).val());
        const max = parseFloat($(this).attr('max'));
        $(this).val(isNaN(val) ? '' : Math.min(val, max));
    });

    // Set account name and bank ID based on selected bank
    $('#bank_name').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const accountNameMatch = selectedOption.text().match(/\(([^)]+)\)/);
        const accountName = accountNameMatch ? accountNameMatch[1] : '';
        $('#account_name').val(accountName);
        $('#bank_id').val(selectedOption.val());
    });

    // Initial bank selection setup
    const initialOption = $('#bank_name option:selected');
    $('#account_name').val(initialOption.text().match(/\(([^)]+)\)/)?.[1] || '');
    $('#bank_id').val(initialOption.val());
});

// Function to update max limit for credit note based on product price
function updateDebitNoteMax(totalPAmount, productPrices, selectedProductId) {
    const productIndex = parseInt(selectedProductId) - 1;
    const productPrice = !isNaN(productIndex) && productPrices[productIndex]
        ? parseFloat(productPrices[productIndex])
        : totalPAmount;
    $('#creditnote').attr('max', productPrice);
}

// Form validation to ensure bank is selected for bank transfers
function validateForms() {
    const paymentType = $('#credit_payment_type').val();
    const bankSelected = $('#bank_name').val();
    const addedFundValue = $('#addedfund').val().trim();

    if (paymentType === '3' && !bankSelected) {
        alert("Please select a Bank.");
        $('#bank_name').focus();
        return false;
    }

    if (addedFundValue === '') {
        alert("Please enter an Amount.");
        return false;
    }

    $('#submitBtn').prop('disabled', true).text('Submitting...');

    return true;
}

</script>
<script>
    // JavaScript to update account_name based on the selected bank
    document.getElementById('bank_name').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var accountName = selectedOption.getAttribute('data-account-name');
        document.getElementById('account_name').value = accountName;
    });
</script>
<script>
    $(document).ready(function() {
        $('#transaction_id').on('change', function() {
            const transactionId = $(this).val();
            if (transactionId && transactionId !== "") {
                $('#addedfund').prop('disabled', false);
            } else {
                $('#addedfund').prop('disabled', true);
            }
        });
    });
</script>
