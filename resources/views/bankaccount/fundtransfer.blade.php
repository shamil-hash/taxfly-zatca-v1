<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Amount Transfer</title>


    @include('layouts/usersidebar')
    <style>

#fundtransfersubmit {
        max-width: 1100px;
        width: 100%;
        padding: 40px 50px;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: auto; /* Center the form on the page */
    }
 .btn-primary{
            background-color: #187f6a;
            color: white;
        }
    /* Flexbox layout for form container */
    .form-container {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        justify-content: center;
    }

    /* Styling for individual sections within the form */
    .form-section {
        flex: 1 1 45%;
        background-color: #fff;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        width: 100%; /* Make the section take full width */
    }

    /* Input group styling */
    .input-group {
        display: flex;
        align-items: center;
        margin-bottom: 15px; /* Space between input groups */
    }

    /* Style for input group add-ons */
    .input-group-addon {
        flex-shrink: 0;
        width: 190px; /* Fixed width for labels */
        font-weight: 500;
        background-color: #e9ecef; /* Optional: Add background color */
        padding: 9.3px; /* Padding for better spacing */
    }



    /* Additional styling for labels */
    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #666;
        margin-bottom: 8px; /* Space below the label */
    }

    /* Employee details section styling */
    #employee-details {
        padding: 25px;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
    }



        /* Responsive design */
        @media (max-width: 768px) {
            .form-container {
                flex-direction: column;
            }

            .form-section {
                width: 100%;
            }

            form {
                padding: 20px 30px;
            }
        }

    .control {
      /* Inherit Bootstrap's base styles */
    display: block;
    width: 100%;
    padding: 0.375rem 0.75rem;  /* Adjust padding */
    font-size: 14px;             /* Adjust font size */
    line-height: 1.5;            /* Adjust line height */

    height: 34px;      /* Text color */

    border: 1px solid #ced4da;   /* Border color */
}




        /* Modal Style */
        .modal-dialog {
            max-width: 400px;
        }


        .modal-body {
            padding: 5px;
        }
        .modal-footer {
      display: flex;
      justify-content: flex-end;
    }
        .modal-footer button {
            width: auto;
            padding: 10px 15px;
        }

        /* Responsive Styles */
        @media screen and (max-width: 768px) {
            .form-group {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-group label {
                margin-bottom: 8px;
            }

            .transaction-dropdowns {
                flex-direction: column;
            }



            .text-center button {
                width: 100%;
                margin-bottom: 10px;
            }
        }



.custom-option{
    background-color: #e0f7fa;
        color: black;
        font-weight: bold;
}
.modal-content {
      padding: 20px;
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
        <div style="margin-top: 15px;margin-left:15px;">
            @include('navbar.banknav')
        </div>
        @else
            <x-logout_nav_user />
        @endif
        <x-admindetails_user :shopdatas="$shopdatas" />

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Bank</a></li>
                <li class="breadcrumb-item active" aria-current="page">Amount Transfer</li>
            </ol>
        </nav>
        <h2 style="text-align: center;">Amount Transfer</h2>

        <div class="transfer-balance-modal">
            <br>
             @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="form-container" style="margin-top: -85px;">
                <div id="employee-details" class="bank-details">
                    <form id="fundtransfersubmit" action="fundtransfersubmit" method="POST">
                @csrf
                <div class="form-section" style="width: 100%;">
                    <div class="input-group">

                        <span class="input-group-addon">Account Name<span style="color: red;">*</span></span>
                        <select id="account_name" name="account_name" class="control">
                        <option value="" selected disabled>Select account</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" data-balance="{{ $account->current_balance }}">
                                {{ $account->account_name }} {{ $account->current_balance }}
                            </option>
                        @endforeach
                    </select>

                </div>

                <!-- Debit/Credit Dropdowns with Modal Integration -->
                <div class="input-group">
                    <span class="input-group-addon">Transaction Type<span style="color: red;">*</span></span>
                    <select id="transaction_type_dropdown" class="control" name="transaction_type" onchange="toggleTransactionTypeDropdown()">
                        <option value="" selected disabled>Select Transaction Type</option>
                        <option value="debit">Debit</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>
                    <div id="debit_dropdown_group" style="display: none;">
                        <div class="input-group">
                            <span class="input-group-addon">Debit Option<span style="color: red;">*</span></span>
                                <select id="debit_dropdown" class="control" name="debit_type">
                                <option value="" selected disabled>Select Debit</option>
                                @foreach($debitTypes as $id => $debitType)
                                <option value="{{ $id }}">{{ $debitType }}</option>
                                @endforeach
                                <option value="add_new_debit" class="custom-option">Add New Debit Option...</option>
                            </select>
                        </div>
                    </div>
            <div id="credit_dropdown_group" style="display: none;">
                <div class="input-group">
                    <span class="input-group-addon">Credit Option<span style="color: red;">*</span></span>
                    <select id="credit_dropdown" class="control" name="credit_type">
                        <option value="" selected disabled>Select Credit</option>
                        @foreach($creditTypes as $id => $creditType)
                        <option value="{{ $id }}">{{ $creditType }}</option>
                        @endforeach
                        <option value="add_new_credit" class="custom-option">Add New Credit Option...</option>
                    </select>
                </div>
            </div>
                <!-- Checkbox for toggling -->
            <div class="input-group">
                <span class="input-group-addon">Send To<span style="color: red;">*</span></span>
                <select class="control" id="sendTo" name="sendTo" onchange="toggleDropdown()">
                <option value="" selected disabled>Select Type</option>
                    <option value="supplier">Supplier</option>
                    <option value="customer">Customer</option>
                </select>
            </div>

                <div id="supplierDropdown" style="display: none;">
                    <div class="input-group">
                        <span class="input-group-addon">Supplier<span style="color: red;">*</span></span>
                    <select id="supplier_drop" class="control" name="supplierAccountName">
                    <option value="" selected disabled>Select Supplier</option>
                    @foreach($supp as $supplier)
                        <option value="{{ $supplier->b_accountname }}">{{ $supplier->b_accountname }}</option>
                    @endforeach
                </select>
                </div>
            </div>

            <div id="customerDropdown" style="display: none;">
                <div class="input-group">
                    <span class="input-group-addon">Customer<span style="color: red;">*</span></span>
                    <select id="customer_drop" class="control" name="customerAccountName">
                        <option value="" selected disabled>Select Customer</option>
                        @foreach($customer as $cust)
                            <option value="{{ $cust->b_accountname }}">{{ $cust->b_accountname }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
                <div class="input-group">
                    <span class="input-group-addon">Date</span>
                    <input type="date" id="date" name="date" value="{{ date('Y-m-d') }}" class="form-control">
                </div>

                <div class="input-group">
                    <span class="input-group-addon">Amount<span style="color: red;">*</span></span>
                    <input type="text" id="amount" name="amount" class="form-control no-arrows" pattern="^[0-9]+$" title="Only numbers are allowed">
                </div>
                <div class="input-group">
                    <span class="input-group-addon">Reference Number</span>
                    <input type="text" id="ref_no" name="ref_no" class="form-control">
        </div>

        <!-- New Field: Image Upload -->
            <div class="input-group">
                <span class="input-group-addon">Receipt Image</span>
                <input type="file" id="receipt_image" name="receipt_image" class="form-control">
            </div>
                    <div class="input-group">
                        <span class="input-group-addon">Remarks</span>
                        <input type="text" id="remarks" name="remarks" class="form-control" >
                    </div>
                <div class="text-center">
                    <button type="submit"  id="submitBtn" class="btn btn-primary" style="font-size: 14px;">Send</button>
                </div>
            </div>
            </form>
    </div></div></div></div>

    <!-- Modal for Adding New Transfer Type -->
    <div class="modal fade" id="newOptionModal" tabindex="-1" role="dialog" aria-labelledby="newOptionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #187f6a;color: white;border-bottom: none;">
                    <h5 class="modal-title" id="newOptionModalLabel" >Add New Transfer Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="newOptionForm" action="/storetransfertype" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="newOptionName" class="text-center">Transfer Type Name</label>
                        <input type="text" placeholder="Enter transfer type name" class="form-control" id="newOptionNamePost" name="newOptionName" required>
                    </div>
                    <input type="hidden" id="dropdownType" name="dropdownType">
                    <button type="submit" class="btn btn-primary">save</button>
                </form>
                </div>
                </div>



    <script>

        // Update Current Balance display
        document.getElementById('account_name').addEventListener('change', function () {
            var balance = this.options[this.selectedIndex].getAttribute('data-balance');
            document.getElementById('current_balance').textContent = '{{$currency}}' + balance;
        });


    // Handle New Option Selection and Modal Handling
    function checkForNewOption(selectElement, type) {
        if (selectElement.value === 'add_new_' + type) {
            $('#newOptionModal').modal('show');
            $('#dropdownType').val(type);
        }
    }

    $('#debit_dropdown').on('change', function () {
        checkForNewOption(this, 'debit');
    });

    $('#credit_dropdown').on('change', function () {
        checkForNewOption(this, 'credit');
    });

    // Handle New Option Modal Form Submission
    $('#newOptionForm').on('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        var newOptionName = $('#newOptionNamePost').val().trim();
    var dropdownType = $('#dropdownType').val();

    if (newOptionName === '') {
        alert('Please enter a valid name.');
        return;
    }
        $.ajax({
            url: '/storetransfertype', // Your route to store the option
            method: 'POST',
            data: {
                _token: $('input[name="_token"]').val(),
                newOptionName: newOptionName,
                dropdownType: dropdownType
            },
            success: function (response) {
                  // Check if the response was successful
            if (response.success) {
                // Identify the correct dropdown to update
                var selectElement = dropdownType === 'debit' ? $('#debit_dropdown') : $('#credit_dropdown');

                // Add the new option to the dropdown
                selectElement.append(`<option value="${response.id}" selected>${response.name}</option>`);

                // Reset the dropdown's selected value to the newly added option
                selectElement.val(response.id).trigger('change'); // Select and trigger change

                // Hide the modal and reset form fields
                $('#newOptionModal').modal('hide');
                $('#newOptionNamePost').val(''); // Clear input field
            } else {
                alert('Failed to add the new transfer type. Please try again.');
            }
        },
        error: function () {
            alert('An error occurred while adding the new transfer type.');
        }
        });
    });

    // Reset the dropdown value when the modal is closed
$('#newOptionModal').on('hidden.bs.modal', function () {
    var dropdownType = $('#dropdownType').val();
    if (dropdownType) {
        var selectElement = dropdownType === 'debit' ? $('#debit_dropdown') : $('#credit_dropdown');

        // Reset the dropdown to the default option
        selectElement.val(selectElement.find('option:first').val());
        // Enable both dropdowns after closing the modal
        $('#debit_dropdown').prop('disabled', false);
        $('#credit_dropdown').prop('disabled', false);
    }
});
$(document).ready(function () {
    var currentBalance;
    // Update current balance when the account is selected
    $('#account_name').change(function () {
        currentBalance = parseFloat($(this).find('option:selected').data('balance'));
    });

// Function to handle form submission for adding a new transfer name
$('#addTransferNameForm').on('submit', function (e) {
    e.preventDefault(); // Prevent the default form submission

    // Get the value of the new transfer name
    var newTransferName = $('#newTransferNameInput').val();

    // Append the new transfer name to the dropdown
    $('#transferDropdown').append($('<option>', {
        value: newTransferName,
        text: newTransferName
    }));

    // Optionally, select the new transfer name
    $('#transferDropdown').val(newTransferName);

    // Clear the input field in the modal
    $('#newTransferNameInput').val('');

    // Close the modal
    $('#newOptionModal').modal('hide');
});
});
</script>
<script>
function toggleDropdown() {
    const selectedType = document.getElementById('sendTo').value;
    const supplierDropdown = document.getElementById('supplierDropdown');
    const customerDropdown = document.getElementById('customerDropdown');

    if (selectedType === 'supplier') {
        supplierDropdown.style.display = 'block';
        customerDropdown.style.display = 'none';
    } else if (selectedType === 'customer') {
        supplierDropdown.style.display = 'none';
        customerDropdown.style.display = 'block';
    } else {
        supplierDropdown.style.display = 'none';
        customerDropdown.style.display = 'none';
    }
}
function toggleTransactionTypeDropdown() {
    const selectedType = document.getElementById('transaction_type_dropdown').value;
    const debitDropdownGroup = document.getElementById('debit_dropdown_group');
    const creditDropdownGroup = document.getElementById('credit_dropdown_group');

    if (selectedType === 'debit') {
        debitDropdownGroup.style.display = 'block';
        creditDropdownGroup.style.display = 'none';
    } else if (selectedType === 'credit') {
        creditDropdownGroup.style.display = 'block';
        debitDropdownGroup.style.display = 'none';
    } else {
        debitDropdownGroup.style.display = 'none';
        creditDropdownGroup.style.display = 'none';
    }
}
</script>
<script>
   document.getElementById('fundtransfersubmit').addEventListener('submit', function (event) {
    event.preventDefault();
    // Get the values of the required fields
    const accountName = document.getElementById('account_name').value;
    const transactionType = document.getElementById('transaction_type_dropdown').value;
    const sendTo = document.getElementById('sendTo').value; // Get the transaction type

    const amount = document.getElementById('amount').value;

    // Get Debit and Credit dropdown values
    const debitType = document.getElementById('debit_dropdown').value;
    const creditType = document.getElementById('credit_dropdown').value;
    const supplier_drop = document.getElementById('supplier_drop').value;
    const customer_drop = document.getElementById('customer_drop').value;




     // Validate Account Name
     if (accountName === "") {
        alert("Please select an Account Name.");
        return; // Exit if validation fails
    }

    // Validate Transaction Type
    if (transactionType === "") {
        alert("Please select a Transaction Type.");
        return; // Exit if validation fails
    }

    // Validate Amount
    if (amount === "" || amount <= 0) {
        alert("Please enter a valid Amount.");
        return; // Exit if validation fails
    }

    // Additional Validation for Transaction Type
    if (transactionType === "debit" && debitType === "") {
        alert("Please select a Debit Option.");
        return; // Exit if validation fails
    }

    if (transactionType === "credit" && creditType === "") {
        alert("Please select a Credit Option.");
        return; // Exit if validation fails
    }
  // Validate Transaction Type
  if (sendTo === "") {
    alert("Please select who to send to.");
    return; // Exit if validation fails
  }

  if (sendTo === "supplier" && supplier_drop === "") {
        alert("Please select a supplier.");
        return; // Exit if validation fails
    }

    if (sendTo === "customer" && customer_drop === "") {
        alert("Please select a customer.");
        return; // Exit if validation fails
    }


    // Call the checkInsufficientBalance() function before any further validation
    if (transactionType === "debit") {
        // Call the balance check function and prevent form submission if balance is insufficient
        if (!checkInsufficientBalance()) {
            return; // Exit if balance check fails
        }
    }

    // If there are other errors, prevent form submission and show the alert

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true; // Disable submit button to prevent multiple submissions
    submitBtn.innerText = "Sending...";

    this.submit(); // Submit the form programmatically if all validations pass
});

// Function to check for insufficient balance
function checkInsufficientBalance() {
    const amount = parseFloat(document.getElementById('amount').value);
    const transactionType = document.getElementById('transaction_type_dropdown').value; // Get the transaction type
    const currentBalance = parseFloat(document.querySelector('#account_name option:checked').dataset.balance); // Get the selected account's current balance

    // Check if the amount is valid
    if (amount <= 0) {
        alert("Please enter a valid amount greater than zero.");
        return false; // Return false to prevent form submission
    }

    // Show alert if the transaction type is debit and the amount exceeds the current balance
    if (transactionType === "debit" && amount > currentBalance) {
        alert("Insufficient balance! The entered amount exceeds the current balance.");
        return false; // Return false to prevent form submission
    }

    return true; // All checks passed, allow form submission
}
</script>
<script>
$(document).ready(function() {
    // Function to handle the transaction type change
    $('#transaction_type_dropdown').on('change', function() {
        const selectedType = $(this).val();

        // Reset and hide both dropdown groups initially
        $('#debit_dropdown_group, #credit_dropdown_group').hide();
        $('#debit_dropdown, #credit_dropdown').val('').prop('disabled', true);

        if (selectedType === 'debit') {
            // Show and enable debit options only
            $('#debit_dropdown_group').show();
            $('#debit_dropdown').prop('disabled', false);
        } else if (selectedType === 'credit') {
            // Show and enable credit options only
            $('#credit_dropdown_group').show();
            $('#credit_dropdown').prop('disabled', false);
        }
    });

    // Before form submission, check the selected transaction type and clear the unused dropdown
    $('form').on('submit', function(e) {
        const selectedType = $('#transaction_type_dropdown').val();

        if (selectedType === 'debit') {
            // Clear credit fields to ensure only debit is submitted
            $('#credit_dropdown').val('');
        } else if (selectedType === 'credit') {
            // Clear debit fields to ensure only credit is submitted
            $('#debit_dropdown').val('');
        }
    });
});
</script>

</body>
</html>
