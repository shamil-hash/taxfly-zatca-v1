<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Expenses and Income</title>
  @include('layouts/usersidebar')
  <style>
    /* Add styles for new category input */
    .category-input {
      display: flex;
      margin-top: 10px;
    }
     .btn-primary{
            background-color: #187f6a;
            color: white;
        }
    .category-input input[type="text"] {
      flex: 1;
      margin-right: 5px;
    }
    .category-input select {
      flex: 0 0 150px;
    }
    .modal-content {
      padding: 20px;
    }

    .modal-title {
      margin: 0;
    }
    .modal-footer {
      display: flex;
      justify-content: flex-end;
    }
    #content {
      padding: 30px;
    }


    .section-title {
      margin-bottom: 1rem;
      font-weight: 600;
      color: #333;
      border-bottom: 2px solid #187f6a;
      padding-bottom: 0.5rem;
      text-align: left;
    }

    .table {
      width: 100%;
      margin-bottom: 1.5rem;
      border-collapse: collapse;
    }

    .table th {
      background-color: #187f6a;
      color: white;
      padding: 10px;
      text-align: center;
      border: 1px solid #ddd;
    }

    .table td {
      padding: 10px;
      border: 1px solid #ddd;
      vertical-align: middle;
    }

    .table tbody tr:nth-child(even) {
      background-color: #f8f9fa;
    }

    .form-control {
      width: 100%;
      padding: 0.375rem 0.75rem;
      border: 1px solid #ced4da;
      border-radius: 0.25rem;
    }
    .btn-group {
      margin-bottom: 20px;
    }
    .custom-option {
        background-color: #e0f7fa;
        color: black;
        font-weight: bold;
    }
    h2, h3 {
      text-align: center;
    }
    h3{
        margin-top: -10px;
    }
    /* Add new styles */

.table-container {
  display: block;
}

.table-container .col-md-6 {
  width: 100%;
  margin-bottom: 20px; /* Adds space between sections */
}



table {
    width: 50%; /* Each table takes 50% width of the container */
    table-layout: fixed; /* Ensure fixed width for columns */
}



th:nth-child(1), td:nth-child(1) { width: 17%; } /* AccountType */
th:nth-child(2), td:nth-child(2) { width: 12%; } /* Amount */
th:nth-child(3), td:nth-child(3) { width: 12%; } /* Details */
th:nth-child(4), td:nth-child(4) { width: 20%; }  /* Date */
th:nth-child(5), td:nth-child(5) { width: 19%; } /* Payment */
th:nth-child(6), td:nth-child(6) { width: 15%; }  /* Empty Column */
th:nth-child(7), td:nth-child(7) { width: 9%; }  /* Action */

.dropdown {
        position: relative; /* Keep the dropdown in position relative to the button */
        float: right; /* Keep the ☰ button on the right side of the page */
    }

   /* Ensure the dropdown appears on the left side */
.dropdown-menu {
    display: none;
    position: absolute;
    right: 100%;  /* Open the dropdown to the left of the button */
    top: 70; /* Align it with the top of the button */
    background: white;
    border: 1px solid #ddd;
    padding: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-left: -100px;
}

.dropdown:hover .dropdown-menu {
    display: block; /* Show the dropdown when hovering */
}

.dropdown-menu a {
    display: block;
    padding: 8px 12px;
    text-decoration: none;
    color: black;
    border-bottom: 1px solid #ddd;

}

.dropdown-menu a:last-child {
    border-bottom: none;
}

.dropdown-menu a:hover {
    background: #187f6a;
    color: white;
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
  <div class="container-fluid">

  <div id="content">
    @if ($adminroles->contains('module_id', '30'))
    @include('navbar.expnavbar')
    @else
    <x-logout_nav_user />
    @endif
    @if (session('success'))
    <div class="alert alert-success" style="text-align: center;">
        {{ session('success') }}
    </div>
@endif
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Accountant</a></li>
      <li class="breadcrumb-item active" aria-current="page">Income and Expense</li>
    </ol>
  </nav>
  {{-- <div class="btn-group btn-group-justified" role="group" aria-label="...">
      <div class="btn-group" role="group">
          <a href="" class="btn btn-primary active">Income and Expense</a>
        </div>
        <div class="btn-group" role="group">
          <a href="/monthwiseexpensehistory" class="btn btn-primary">Expense History</a>
        </div>
        <div class="btn-group" role="group">
          <a href="/searchmonthwiseincome" class="btn btn-primary">Income History</a>
        </div>
    </div>
<br><br> --}}

<div class="dropdown">
    <button class="btn btn-info" style="background-color:#187f6a;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        ☰
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a href="/monthwiseexpensehistory" class="dropdown-item ">Expense History</a>
        <a href="/searchmonthwiseincome" class="dropdown-item ">Income History</a>
    </div>
</div>
<h2>Financial Overview</h2>


<form action="expensesubmit" method="POST" enctype="multipart/form-data" id="companyexpenseform" onsubmit="return validateForm();">
    @csrf
    {{-- <div class="row">
        <div class="col-sm-2" id="branchdiv">
            <select name="branch" id="branch" class="form-control" title="Select Your Branch">
                <option value="">SELECT BRANCH</option>
                @foreach($branches as $branch)
                <option value="{{$branch->id}}">{{$branch->branchname}}</option>
                @endforeach
            </select>
        </div>
    </div> --}}
    <br>

    <div class="row">
        <div class="col-12 mb-4">
            <h3 class="section-title">Expense</h3>
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>AccountType</th>
                        <th>Amount</th>
                        <th>Details</th>
                        <th>Date</th>
                        <th>Payment</th>
                        <th></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="expenseTableBody">
                    <tr>
                        <td>
                            <select id="expense_comment" class="form-control comment">
                                <option value="" selected>Select</option>
                                <option value="add_manage_category" class="custom-option">+ Add Category&nbsp;&nbsp;&nbsp;</option>
                                @foreach($expense_category as $categoryItem)
                                <option value="{{ $categoryItem->category }}">{{ $categoryItem->category }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" id="expense_amount" placeholder="Amount" class="form-control amount"></td>
                        <td><input type="text" id="expense_details" placeholder="Details" class="form-control details"></td>
                        <td><input type="date" class="form-control start_date" value={{$start_date}} name="start_date" id="expense_start_date"></td>
                        <td data-label="Payment">
                            <div class="d-flex flex-column">
                                <select name="expense_payment_type" id="expense_payment_type" class="form-control custom-select-no-padding mb-2" onchange="toggleBankDropdown('expense_payment_type', 'expense_bank_dropdown')">
                                    <option value="cash">Cash</option>
                                    @foreach ($users as $user)

                                    <?php if ($user->role_id == '24') { ?>
                                    <option value="bank">Bank</option>
                                    <?php } ?>
                                    @endforeach                                </select>
                                <!-- Dropdown for Bank Selection (hidden by default) -->
                                <select name="bank_name" id="expense_bank_dropdown" class="form-control mt-2" style="display:none;">
                                    <option value="" selected>Select Bank</option>
                                    @foreach ($listbank as $bank)
                                    @if ($bank->status == 1)
                                    <option value="{{ $bank->id}}" data-current-balance="{{ $bank->current_balance }}" data-account-name="{{ $bank->account_name }}" data-bank-name="{{$bank->bank_name}}">{{ $bank->bank_name }} ({{ $bank->account_name }})</option>
                                   @endif
                                    @endforeach
                                </select>
                            </div>
                        </td>

                        <td></td>
                        <td style="text-align: center;"><a href="#" id="addExpenseButton" onclick="onAddButtonClick()" class="btn btn-info addExpenseRow" style="background-color:#187f6a;" title="Add Row">+</a></td>
                    </tr>
                    <tr>
                        <td colspan="5"> <i class="glyphicon glyphicon-tags"></i> &nbsp DATA
                        <td width="15%">FILE</td>
                        <td>
                        </td>
                        </td>
                      <tr>
                </tbody>
            </table>
        </div>
            <br>
        </div>
        <div class="col-12 mb-4">
            <h3 class="section-title">Income</h3>
            <div class="table-responsive">

            <table class="table">
                <thead>
                    <tr>
                        <th>AccountType</th>
                        <th>Amount</th>
                        <th>Details</th>
                        <th>Date</th>
                        <th>Payment</th>
                        <th></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="incomeTableBody">
                    <tr>
                        <td>
                            <select id="income_comment" class="form-control comment">
                                <option value="" selected>Select</option>
                                <option value="add_manage_category" class="custom-option">+ Add Category&nbsp;&nbsp;&nbsp;</option>
                                @foreach($income_category as $incomeItem)
                                <option value="{{ $incomeItem->category }}">{{ $incomeItem->category }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" id="income_amount" placeholder="Amount" class="form-control amount"></td>
                        <td><input type="text" id="income_details" placeholder="Details" class="form-control details"></td>
                        <td><input type="date" class="form-control start_date" value={{$start_date}} name="start_date" id="income_start_date"></td>
                        <td data-label="Payment">
                            <div class="d-flex flex-column">
                                <select name="income_payment_type" id="income_payment_type" class="form-control custom-select-no-padding mb-2" onchange="toggleBankDropdown('income_payment_type', 'income_bank_dropdown')">
                                    <option value="cash">Cash</option>
                                        @foreach ($users as $user)

                                    <?php if ($user->role_id == '24') { ?>
                                    <option value="bank">Bank</option>
                                    <?php } ?>
                                    @endforeach                                </select>
                                <!-- Dropdown for Bank Selection (hidden by default) -->
                                <select name="income_bank_name" id="income_bank_dropdown" class="form-control mt-2" style="display:none;">
                                    <option value="" selected>Select Bank</option>
                                    @foreach ($listbank as $bank)
                                        @if ($bank->status == 1)
                                            <option value="{{ $bank->id }}" data-current-balance="{{ $bank->current_balance }}" data-bank-name="{{$bank->bank_name}}" data-account-name="{{ $bank->account_name }}">
                                                {{ $bank->bank_name }} ({{ $bank->account_name }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>


                            </div>
                        </td>
                             <td></td>
                        <td style="text-align: center;"><a href="#" id="addIncomeButton" onclick="onAddButtonClick()" class="btn btn-info addIncomeRow" style="background-color:#187f6a;" title="Add Row">+</a></td>
                    </tr>
                    <tr>
                        <td colspan="5"> <i class="glyphicon glyphicon-tags"></i> &nbsp DATA
                        <td width="15%">FILE</td>
                        <td>
                        </td>
                        </td>
                      <tr>
                </tbody>
            </table>
        </div>
    </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2">
            <input type="submit" id="submitBtn" value="Submit" class="btn btn-primary">
        </div>
    </div>
</form>
  </div>

  </div>

  <!-- Add/Manage Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #187f6a;color: white;">
        <h4 class="modal-title" id="categoryModalLabel">Add/Manage Category</h4>
      </div>
      <div class="modal-body">
        <form id="categoryForm" action="/submittype" method="POST">
          @csrf
          <div class="form-group">
            <label for="categoryTypeSelect">Category Type</label>
            <select id="categoryTypeSelect" class="form-control" name="type">
              <option value="" selected>Select Type</option>
              <option value="direct">DIRECT</option>
              <option value="indirect">INDIRECT</option>
            </select>
          </div>
          <div class="form-group">
            <label for="newCategoryName">New Category Name</label>
            <input type="text" class="form-control" id="newCategoryName" name="newCategoryName">
          </div>
          <input type="hidden" id="categoryType" name="categoryType" >

          <div class="form-group">
          <button type="submit" class="btn btn-primary" id="addCategoryBtn">Add Category</button>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function () {
    // Prevent form submission on Enter keypress and add row instead
    function preventFormSubmitOnEnter() {
        document.querySelectorAll('form input:not([type="submit"])').forEach(input => {
            input.addEventListener('keydown', function (e) {
                if (e.keyCode === 13) {
                    e.preventDefault();
                    if (this.closest('tr').parentElement.id === 'expenseTableBody') {
                        addExpenseRow();
                    } else if (this.closest('tr').parentElement.id === 'incomeTableBody') {
                        addIncomeRow();
                    }
                    enterKeyPressed = true; // Set flag when Enter key is pressed
                    return false;
                }
            });
        });
    }

    preventFormSubmitOnEnter();

    // Attach event listener to category dropdowns
    function attachCategoryHandler() {
    document.querySelectorAll('.comment').forEach(select => {
        select.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.classList.contains('custom-option')) {
                const categoryType = this.id.includes('expense') ? 'expense' : 'income';
                document.getElementById('categoryType').value = categoryType;
                $('#categoryModal').modal('show');
            }
        });
    });
}

attachCategoryHandler();

// Add event listener to Add Category button in modal
document.getElementById('addCategoryBtn').addEventListener('click', function (e) {
    e.preventDefault();
    const newCategoryName = document.getElementById('newCategoryName').value.trim();
    const categoryType = document.getElementById('categoryType').value;
    const categoryTypeSelect = document.getElementById('categoryTypeSelect').value;

    if (!newCategoryName || !categoryTypeSelect) {
        alert('Please fill in the category name and select a type.');
        return;
    }

    // Send AJAX request to submit the new category
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/submittype', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            type: categoryTypeSelect,
            newCategoryName: newCategoryName,
            categoryType: categoryType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const newCategory = data.category;
            const newOption = new Option(newCategory.category, newCategory.category);

            if (categoryType === 'expense') {
                document.querySelectorAll('#expenseTableBody .comment').forEach(select => {
                    const selectedValue = select.value;
                    if (!Array.from(select.options).some(option => option.value === newCategory.category)) {
                        select.add(newOption.cloneNode(true));
                    }
                    select.value = selectedValue; // Preserve the selected value
                });
                const expenseComment = document.querySelector('#expense_comment');
                if (!Array.from(expenseComment.options).some(option => option.value === newCategory.category)) {
                    expenseComment.add(newOption.cloneNode(true));
                }
                expenseComment.value = newCategory.category; // Select the new category
            } else if (categoryType === 'income') {
                document.querySelectorAll('#incomeTableBody .comment').forEach(select => {
                    const selectedValue = select.value;
                    if (!Array.from(select.options).some(option => option.value === newCategory.category)) {
                        select.add(newOption.cloneNode(true));
                    }
                    select.value = selectedValue; // Preserve the selected value
                });
                const incomeComment = document.querySelector('#income_comment');
                if (!Array.from(incomeComment.options).some(option => option.value === newCategory.category)) {
                    incomeComment.add(newOption.cloneNode(true));
                }
                incomeComment.value = newCategory.category; // Select the new category
            }

            // Clear the input fields and reset the modal state
            document.getElementById('newCategoryName').value = '';
            document.getElementById('categoryType').value = '';
            document.getElementById('categoryTypeSelect').value = '';
            $('#categoryModal').modal('hide');
        } else {
            alert('Failed to add category. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add category. Please try again.');
    });
});

// Handle modal close event to reset state
$('#categoryModal').on('hidden.bs.modal', function () {
    document.getElementById('newCategoryName').value = '';
    document.getElementById('categoryType').value = '';
    document.getElementById('categoryTypeSelect').value = '';

    // Reset the dropdowns when the modal is closed
    document.querySelectorAll('.comment').forEach(select => {
        if (select.value === 'add_manage_category') {
            select.value = '';
        }
    });
});


// Function to handle removing income row and reverting bank balance
var bankBalances = {}; // Store bank balances based on the bank ID

function addExpenseRow() {
    var comment = $("#expense_comment").val();
    var amount = parseFloat($("#expense_amount").val()); // Parse amount as a float
    var details = $("#expense_details").val();
    var date = $("#expense_start_date").val();
    var commentHtml = $('#expense_comment').html();
    var expense_comment = $('#expense_comment').val();
    var paymentType = $("#expense_payment_type").val(); // Get payment type (cash or bank)

// Get the selected bank from the dropdown
var bankSelectedOption = $("#expense_bank_dropdown").find("option:selected");
var bankValue = $("#expense_bank_dropdown").val(); // Get the selected bank value
var bankAccountName = bankSelectedOption.data("account-name"); // Get account name
    var initialBalance = parseFloat(bankSelectedOption.data("current-balance"));
    var currentBalance = bankBalances[bankValue] !== undefined ? bankBalances[bankValue] : initialBalance;
    // Get current balance or initial balance
// Validate account type and amount
    if (!expense_comment) {
        alert("Account type is required");
        return;
    } else if (!amount) {
        alert("Amount is required");
        return;
    }
        // Show an alert if bank is not selected when payment type is bank
        if (paymentType === 'bank' && bankValue === '') {
        alert("Please select a bank for the payment.");
        return;
    }
       // Check if the amount is greater than the current balance
       if (paymentType === 'bank' && amount > currentBalance) {
        alert("Insufficient balance. The current balance is " + currentBalance);
        return; // Stop execution if balance is insufficient
    }


    if (paymentType === 'bank') {
        bankBalances[bankValue] = currentBalance - amount; // Update the bank balance
    }
    // Set payment text to selected bank name or Cash
    var paymentText = paymentType === 'bank' ? bankSelectedOption.data("bank-name") : 'Cash'; // Only use account name if bank is selected

    var tr = `<tr>
                <td>
                    <select name="expense_comment[]" class="form-control comment">
                        ${commentHtml}
                    </select>
                </td>
                <td><input type="text" name="expense_amount[]" value="${amount}" class="form-control" readonly></td>
                <td><input type="text" name="expense_details[]" value="${details}" class="form-control"></td>
                <td><input type="date" name="expense_start_date[]" value="${date}" class="form-control"></td>
                <td>
                    <span style=" display: flex;justify-content: center;align-items: center;margin-top:10px;">${paymentText}</span> <!-- Display selected bank name or Cash -->

                    <input type="hidden" name="expense_bank_id[]" value="${bankValue}"> <!-- Store bank ID -->
                    <input type="hidden" name="expense_account_name[]" value="${bankAccountName}"> <!-- Store account name -->
                    <input type="hidden" name="expense_current_balance[]" value="${currentBalance}"> <!-- Store current balance -->
                </td>
                <td><input type="file" name="expense_image[]" multiple class="form-control" accept="image/*"></td>
                <td><a href="#" class="btn btn-danger remove-expense-btn" title="Remove Row">-</a></td>
            </tr>`;

    $('#expenseTableBody').append(tr);
    var newRow = $('#expenseTableBody tr:last-child');

    // Set the selected value to the previously selected comment
    newRow.find('select[name="expense_comment[]"]').val(comment);

    // Clear the input fields only after adding the row
    $("#expense_comment").val('');
    $("#expense_amount").val('');
    $('#expense_details').val('');
    $("#expense_payment_type").val('cash');
    $("#expense_bank_dropdown").val('').hide();
    attachCategoryHandler();
}
 // Functions to add income and expense rows
 function addIncomeRow() {
    var comment = $("#income_comment").val();
    var amount = parseFloat($("#income_amount").val()); // Parse amount as a float
    var details = $('#income_details').val();
    var date = $("#income_start_date").val();
    var commentHtml = $('#income_comment').html();
    var income_comment = $('#income_comment').val();
    var paymentType = $("#income_payment_type").val(); // Get payment type (cash or bank)
    var bankSelectedOption = $("#income_bank_dropdown").find("option:selected"); // Get the selected bank text
    var bankValue = $("#income_bank_dropdown").val(); // Get the selected bank value
    var bankAccountName = bankSelectedOption.data("account-name"); // Get account name
    var initialBalance = parseFloat(bankSelectedOption.data("current-balance")); // Bank's initial balance
    var currentBalance = bankBalances[bankValue] !== undefined ? bankBalances[bankValue] : initialBalance; // Get current balance or initial balance

    // var bankAccountName = $("#income_bank_dropdown").find("option:selected").data("account-name"); // Get account name
    var f = ($("#file").val());
    if (!income_comment) {
        alert("Account type is required");
        return;
    } else if (!amount) {
        alert("Amount is required");
        return;
    }
        // Validate bank selection if payment type is bank
        if (paymentType === 'bank' && bankValue === '') {
        alert("Please select a Bank.");
        return;
    }
    if (paymentType === 'bank') {
        bankBalances[bankValue] = currentBalance + amount; // Increase the balance by the income amount
    }
    var paymentText = paymentType === 'bank' ? bankSelectedOption.data("bank-name") : 'Cash'; // Only show bank name if bank is selected

    var tr = `<tr>
                <td>
                    <select name="income_comment[]" class="form-control comment">
                        ${commentHtml}
                    </select>
                </td>
                <td><input type="text" name="income_amount[]" value="${amount}" class="form-control" readonly></td>
                <td><input type="text" name="income_details[]" value="${details}" class="form-control"></td>
                <td><input type="date" name="income_start_date[]" value="${date}" class="form-control"></td>
                <td>
                    <span style=" display: flex;justify-content: center;align-items: center;margin-top:10px;">${paymentText}</span> <!-- Display selected bank name or Cash -->
                    <input type="hidden" name="income_bank_id[]" value="${bankValue}">
                    <input type="hidden" name="income_account_name[]" value="${bankAccountName}"> <!-- Hidden input for bank account name -->
                </td>
                <td><input type="file" name="income_image[]" multiple class="form-control" accept="image/*"></td>
                <td><a href="#" class="btn btn-danger remove-income-btn" title="Remove Row">-</a></td>
            </tr>`;

    $('#incomeTableBody').append(tr);
    var newRow = $('#incomeTableBody tr:last-child');

    // Set the selected value to the previously selected comment
    newRow.find('select[name="income_comment[]"]').val(comment);

    // Clear the input fields only after adding the row
    $("#income_comment").val('');
    $("#income_amount").val('');
    $('#income_details').val('');
    $("#income_payment_type").val('cash');
    $("#income_bank_dropdown").val('').hide();
    attachCategoryHandler();
}

// Function to handle removing an income row and updating the balance
// Function to handle removing an income row and updating the balance
$(document).on('click', '.remove-income-btn', function (e) {
    e.preventDefault();
    var row = $(this).closest('tr');
    var bankValue = row.find('input[name="income_bank_id[]"]').val();
    var amount = parseFloat(row.find('input[name="income_amount[]"]').val());

    // Check if the bank is selected and the balance exists
    if (bankValue && bankBalances[bankValue] !== undefined) {
        var initialBalance = parseFloat($("#income_bank_dropdown").find(`option[value='${bankValue}']`).data("current-balance"));

        // Reset the bank balance to its initial starting balance
        bankBalances[bankValue] = initialBalance;
    }

    // Check for any remaining expenses
    checkExpensesAgainstBalance();

    // Remove the income row
    row.remove();
});

// Function to handle removing expense row and updating bank balance
$(document).on('click', '.remove-expense-btn', function (e) {
    e.preventDefault();
    var row = $(this).closest('tr');
    var bankValue = row.find('input[name="expense_bank_id[]"]').val();
    var amount = parseFloat(row.find('input[name="expense_amount[]"]').val());

    // Re-add the expense amount to the bank balance when removing the row
    if (bankValue && bankBalances[bankValue] !== undefined) {
        bankBalances[bankValue] += amount; // Add back the removed expense amount
    }

    row.remove();
});

// Function to check balance before submitting expense
function checkExpensesAgainstBalance() {
    $('#expenseTableBody tr').each(function() {
        var row = $(this);
        var bankValue = row.find('input[name="expense_bank_id[]"]').val();
        var amount = parseFloat(row.find('input[name="expense_amount[]"]').val());

        // Check if there is a bank selected
        if (bankValue && bankBalances[bankValue] !== undefined && amount > bankBalances[bankValue]) {
            alert("An expense exceeds the current balance for the selected bank. Please adjust your expenses.");
            row.remove(); // Remove the invalid row
        }
    });
}



    // Event listeners for adding rows
    document.querySelector('.addExpenseRow').addEventListener('click', function (e) {
        e.preventDefault();
        addExpenseRow();
        addButtonClicked = true; // Set flag when "+" button is clicked
    });

    document.querySelector('.addIncomeRow').addEventListener('click', function (e) {
        e.preventDefault();
        addIncomeRow();
        addButtonClicked = true; // Set flag when "+" button is clicked
    });

    // Event listeners for removing rows
    document.querySelector('#expenseTableBody').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row-btn')) {
            e.preventDefault();
            if (confirm('Are you sure you want to remove this row?')) {
                e.target.closest('tr').remove();
            }
        }
    });

    document.querySelector('#incomeTableBody').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row-btn')) {
            e.preventDefault();
            if (confirm('Are you sure you want to remove this row?')) {
                e.target.closest('tr').remove();
            }
        }
    });

    // Handle modal close event to reset state
    $('#categoryModal').on('hidden.bs.modal', function () {
        document.getElementById('newCategoryName').value = '';
        document.getElementById('categoryType').value = '';
        document.getElementById('categoryTypeSelect').value = '';
    });

    // Validation function
    let addButtonClicked = false; // Flag for "+" button
    let enterKeyPressed = false; // Flag for Enter key

    function validateForm(event) {
        event.preventDefault(); // Prevent form from submitting

        // const branch = document.getElementById('branch').value;
        // if (branch === '') {
        //     alert('Please select a branch.');
        //     return false;
        // }

        // Prevent the form from submitting multiple times
        const submitBtn = document.getElementById("submitBtn");

        if (submitBtn.disabled) {
            return false; // Prevent form submission
        }

        // Disable the submit button
        submitBtn.disabled = true;
        submitBtn.innerText = "Submitting...";

        if (!addButtonClicked && !enterKeyPressed) {
            alert("Press the add button.");
            // Re-enable the submit button after alert
            submitBtn.disabled = false;
            submitBtn.innerText = "Submit";
            return false; // Validation failed; prevent form submission
        }

        // Reset flags for next submission
        addButtonClicked = false;
        enterKeyPressed = false;

        // Form is valid; continue with submission
        event.target.submit(); // Submit the form
        return true; // Validation passed; allow the form to submit
    }

    // Function to be called when "+" button is clicked
    function onAddButtonClick() {
        addButtonClicked = true;
    }

    // Function to handle Enter key press
    function onEnterKeyPress(event) {
        if (event.key === 'Enter') {
            enterKeyPressed = true;
        }
    }

    // Attach the Enter key handler to the form
    document.getElementById("companyexpenseform").addEventListener('keydown', onEnterKeyPress);

    // Ensure validateForm is called on form submission
    document.getElementById("companyexpenseform").addEventListener('submit', validateForm);

    // Set flag when "+" button is clicked
    document.querySelectorAll('.addExpenseRow, .addIncomeRow').forEach(button => {
        button.addEventListener('click', onAddButtonClick);
    });
});

</script>


<script>
    function toggleBankDropdown(paymentTypeId, bankDropdownId) {
    const paymentType = document.getElementById(paymentTypeId).value;
    const bankDropdown = document.getElementById(bankDropdownId);
    const paymentAmount = document.getElementById('payment_amount'); // Assuming there's a common ID for payment amount input

    if (paymentType === 'bank') {
        bankDropdown.style.display = 'block'; // Show bank dropdown
        paymentAmount.style.display = 'none';  // Hide payment amount input (if needed)
    } else {
        bankDropdown.style.display = 'none';  // Hide bank dropdown
        paymentAmount.style.display = 'block'; // Show payment amount input (if needed)
    }
}

</script>


</body>
</html>
