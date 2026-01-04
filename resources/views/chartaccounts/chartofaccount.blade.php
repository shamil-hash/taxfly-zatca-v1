<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Chart of Accountant</title>
  @include('layouts/usersidebar')
  <style>
    /* Add styles for new category input */

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
 .btn-primary{
            background-color: #187f6a;
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
    @include('navbar.chartofaccounts')
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
      <li class="breadcrumb-item"><a href="#">Chart of Accountant</a></li>
      <li class="breadcrumb-item active" aria-current="page">Asset and Capital</li>
    </ol>
  </nav>

<div class="dropdown">
    <button style="background-color: #187f6a;" class="btn btn-info" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        ☰
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a href="/assethistory" class="dropdown-item ">Asset History</a>
        <a href="/capitalhistory" class="dropdown-item ">Capital History</a>
        <a href="/liabilityhistory" class="dropdown-item ">Liability History</a>

    </div>
</div>
<h2>Chart of Accounts</h2>

<form action="{{ route('chartaccounts.submit') }}" method="POST" enctype="multipart/form-data" id="chartaccountsform">
    @csrf
    <div class="row">
        <div class="col-12 mb-4">
            <h3 class="section-title">Assets</h3>
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Account Type</th>
                        <th>Asset Category</th>
                        <th>Asset Name</th>
                        <th>Amount</th>
                        <th>Details</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="expenseTableBody">
                    <tr>
                        <td>
                            <select id="expense_comment" class="form-control comment" onchange="updateAssetCategory(this)">
                                <option value="" selected>Select</option>
                                <option value="fixed">Fixed</option>
                                <option value="current">Current</option>
                            </select>
                        </td>
                        <td>
                            <select id="asset_category" class="form-control asset-category" disabled onchange="toggleAssetNameInput()">
                                <option value="" selected>Select</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" id="asset_name" class="form-control asset-name" placeholder="Asset Name" disabled>
                        </td>
                        <td>
                            <input type="text" id="expense_amount" placeholder="Amount" class="form-control amount">
                        </td>
                        <td>
                            <input type="text" id="expense_details" placeholder="Details" class="form-control details">
                        </td>
                        <td>
                            <input type="date" class="form-control start_date" value="{{$start_date}}" name="start_date" id="expense_start_date">
                        </td>
                        <td style="text-align: center;">
                            <a href="#" id="addExpenseButton" onclick="onAddButtonClick()" class="btn btn-info addExpenseRow" title="Add Row" style="background-color: #187f6a;">+</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
            <br>

        </div>

        <div class="col-12 mb-4">
            <h3 class="section-title">Capital</h3>
            <div class="table-responsive">

            <table class="table">
                <thead>
                    <tr>
                        <th>Account Type</th>
                        <th>Capital Category</th>
                        <th>Partner</th>
                        <th>Amount</th>
                        <th>Details</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="incomeTableBody">
                    <tr>
                        <td>
                            <select id="income_comment" class="form-control comment" onchange="updateCapitalCategory(this)">
                                <option value="" selected>Select</option>
                                <option value="Owner’s & Partner’s Capital">Owner’s & Partner’s Capital</option>
                                <option value="Investment & Reserves">Investment & Reserves</option>
                            </select>
                        </td>
                        <td>
                            <select id="capital_category" class="form-control capital-category" disabled onchange="toggleCapitalNameInput()">
                                <option value="" selected>Select</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" id="capital_name" class="form-control capital-name" placeholder="Partner Name" disabled>
                        </td>
                        <td>
                            <input type="text" id="income_amount" placeholder="Amount" class="form-control amount">
                        </td>
                        <td>
                            <input type="text" id="income_details" placeholder="Details" class="form-control details">
                        </td>
                        <td>
                            <input type="date" class="form-control start_date" value="{{$start_date}}" name="start_date" id="income_start_date">
                        </td>
                        <td style="text-align: center;">
                            <a href="#" id="addIncomeButton" onclick="onAddButtonClickcapital()" class="btn btn-info addIncomeRow" title="Add Row" style="background-color: #187f6a;">+</a>
                        </td>
                    </tr>

                </tbody>
            </table>

            <br>
        </div>
        </div>

        <div class="col-12 mb-4">
            <h3 class="section-title">Liability</h3>
            <div class="table-responsive">

            <table class="table">
                <thead>
                    <tr>
                        <th>Account Type</th>
                        <th>Liability Category</th>
                        <th>Amount</th>
                        <th>Details</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="liabilityTableBody">
                    <tr>
                        <td>
                            <select id="liablity" class="form-control comment" onchange="updateLiabilityCategory(this)">
                                <option value="" selected>Select</option>
                                <option value="Short-term Liabilities">Short-term Liabilities</option>
                                <option value="Long-term Liabilities">Long-term Liabilities</option>
                            </select>
                        </td>
                        <td>
                            <select id="liability_category" class="form-control liability-category" disabled onchange="toggleLiabilityInput()">
                                <option value="" selected>Select</option>
                            </select>
                        </td>

                        <td>
                            <input type="text" id="liablity_amount" placeholder="Amount" class="form-control amount">
                        </td>
                        <td>
                            <input type="text" id="liablity_details" placeholder="Details" class="form-control details">
                        </td>
                        <td>
                            <input type="date" class="form-control start_date" value="{{$start_date}}" name="start_date" id="liablity_date">
                        </td>
                        <td style="text-align: center;">
                            <a href="#" id="addIncomeButton" onclick="onAddButtonClickliability()" class="btn btn-info addliabilityRow" title="Add Row" style="background-color: #187f6a;">+</a>
                        </td>
                    </tr>

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



  <script>
    function updateAssetCategory(selectElement) {
        let assetCategory = document.getElementById("asset_category");
        let assetName = document.getElementById("asset_name");

        assetCategory.innerHTML = '<option value="" selected>Select</option>'; // Reset options
        assetCategory.disabled = false; // Enable dropdown

        if (selectElement.value === "fixed") {
            var fixedOptions = ['Furniture & Fixtures', 'Office Equipment', 'Vehicles', 'Machinery & Tools', 'Buildings & Property', 'Land', 'Intangible Assets', 'Air Conditioners & HVAC Systems', 'Security Systems', 'Networking Equipment'];
            fixedOptions.forEach(option => {
                let opt = document.createElement("option");
                opt.value = option;
                opt.textContent = option;
                assetCategory.appendChild(opt);
            });
        } else if (selectElement.value === "current") {
            var currentOptions = ['Cash & Bank Balances', 'Account Receivable', 'Inventory / Stock', 'Prepaid Expenses', 'Short-term Investments'];
            currentOptions.forEach(option => {
                let opt = document.createElement("option");
                opt.value = option;
                opt.textContent = option;
                assetCategory.appendChild(opt);
            });
        } else {
            assetCategory.disabled = true;
            assetName.disabled = true;
            assetName.value = "";
        }
    }

    function toggleAssetNameInput() {
        let assetName = document.getElementById("asset_name");
        assetName.disabled = false;
    }

    function onAddButtonClick() {
        event.preventDefault();

        let expenseComment = document.getElementById("expense_comment").value;
        let assetCategory = document.getElementById("asset_category").value;
        let assetName = document.getElementById("asset_name").value;
        let expenseAmount = document.getElementById("expense_amount").value;
        let expenseDetails = document.getElementById("expense_details").value;
        let expenseDate = document.getElementById("expense_start_date").value;

        if (expenseComment === "" || assetCategory === "" || assetName === "" || expenseAmount === "" || expenseDate === "") {
            alert("Please fill in all fields before adding a row.");
            return;
        }

        let tableBody = document.getElementById("expenseTableBody");

// Create a new row
        let newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td><input type="text" name="asset_type[]" value="${expenseComment}" class="form-control" readonly></td>
            <td><input type="text" name="asset_category[]" value="${assetCategory}" class="form-control"></td>
            <td><input type="text" name="asset_name[]" value="${assetName}" class="form-control"></td>
            <td><input type="text" name="asset_amount[]" value="${expenseAmount}" class="form-control"></td>
            <td><input type="text" name="asset_details[]" value="${expenseDetails}" class="form-control"></td>
            <td><input type="date" name="asset_date[]" value="${expenseDate}" class="form-control"></td>
            <td><a href="#" class="btn btn-danger removeRow" onclick="removeRow(this)">-</a></td>
        `;
// Append the new row to the table body
    tableBody.appendChild(newRow);

        // Clear input fields after adding row
        document.getElementById("expense_comment").value = "";
        document.getElementById("asset_category").innerHTML = '<option value="" selected>Select</option>';
        document.getElementById("asset_category").disabled = true;
        document.getElementById("asset_name").value = "";
        document.getElementById("asset_name").disabled = true;
        document.getElementById("expense_amount").value = "";
        document.getElementById("expense_details").value = "";
    }

    function removeRow(button) {
        let row = button.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }
</script>
<script>
    function updateCapitalCategory(selectElement) {
        let capitalCategory = document.getElementById("capital_category");
        let capitalName = document.getElementById("capital_name");

        capitalCategory.innerHTML = '<option value="" selected>Select</option>'; // Reset options
        capitalCategory.disabled = false; // Enable dropdown

        if (selectElement.value === "Owner’s & Partner’s Capital") {
            var capitalOptions = ['Owner’s Equity', 'Partner’s Capital Account', 'Shareholder’s Equity', 'Retained Earnings', 'Additional Paid-in Capital', 'Drawings / Owner’s Withdrawal', 'Opening Capital Balance', 'Proprietor’s Fund', 'Partner’s Profit Share', 'Reserve & Surplus'];
            capitalOptions.forEach(option => {
                let opt = document.createElement("option");
                opt.value = option;
                opt.textContent = option;
                capitalCategory.appendChild(opt);
            });
        } else if (selectElement.value === "Investment & Reserves") {
            var investmentOptions = ['Initial Capital Investment', 'Reinvestment of Profits', 'General Reserve', 'Capital Reserve', 'Securities Premium Reserve', 'Dividend Payable', 'Share Capital – Common Stock', 'Share Capital – Preferred Stock', 'Bonus Shares Issued', 'Convertible Debentures'];
            investmentOptions.forEach(option => {
                let opt = document.createElement("option");
                opt.value = option;
                opt.textContent = option;
                capitalCategory.appendChild(opt);
            });
        } else {
            capitalCategory.disabled = true;
            capitalName.disabled = true;
            capitalName.value = "";
        }
    }

    function toggleCapitalNameInput() {
        let capitalName = document.getElementById("capital_name");
        capitalName.disabled = false;
    }

    function onAddButtonClickcapital() {
    let incomeTableBody = document.getElementById("incomeTableBody");

    // Get input values
    let incomeComment = document.getElementById("income_comment").value;
    let capitalCategory = document.getElementById("capital_category").value;
    let capitalName = document.getElementById("capital_name").value;
    let incomeAmount = document.getElementById("income_amount").value;
    let incomeDetails = document.getElementById("income_details").value;
    let incomeStartDate = document.getElementById("income_start_date").value;

    // Validation check (optional)
    if (incomeComment === "" || capitalCategory === "" || capitalName==="" || incomeAmount === "" || incomeStartDate === "") {
        alert("Please fill in all fields before adding a row.");
        return;
    }

    // Create new row
    let newRow = document.createElement("tr");


    newRow.innerHTML = `
            <td><input type="text" name="capital_type[]" value="${incomeComment}" class="form-control" readonly></td>
            <td><input type="text" name="capital_category[]" value="${capitalCategory}" class="form-control"></td>
            <td><input type="text" name="capital_name[]" value="${capitalName}" class="form-control"></td>
            <td><input type="text" name="capital_amount[]" value="${incomeAmount}" class="form-control"></td>
            <td><input type="text" name="capital_details[]" value="${incomeDetails}" class="form-control"></td>
            <td><input type="date" name="capital_date[]" value="${incomeStartDate}" class="form-control"></td>
            <td><a href="#" class="btn btn-danger removeRow" onclick="removeRow(this)">-</a></td>
        `;
    // Append new row before the last data row
    incomeTableBody.appendChild(newRow);


    // Clear the input fields after adding the row
    document.getElementById("income_comment").value = "";
    document.getElementById("capital_category").innerHTML = '<option value="" selected>Select</option>';
    document.getElementById("capital_category").disabled = true;
    document.getElementById("capital_name").value = "";
    document.getElementById("capital_name").disabled = true;
    document.getElementById("income_amount").value = "";
    document.getElementById("income_details").value = "";
}

// Function to remove row
function removeRow(btn) {
    btn.closest("tr").remove();
}

</script>
<script>
    function updateLiabilityCategory(selectElement) {
        let liabilityCategory = document.getElementById("liability_category");
        let liabilityName = document.getElementById("liability_name");

        liabilityCategory.innerHTML = '<option value="" selected>Select</option>'; // Reset options
        liabilityCategory.disabled = false; // Enable dropdown

        if (selectElement.value === "Short-term Liabilities") {
            var currentLiabilities = [
                "Accounts Payable", "Short-term Loans", "Bank Overdraft", "Taxes Payable",
                "Salaries & Wages Payable", "Interest Payable", "Rent Payable", "Utility Bills Payable",
                "Advance from Customers", "Unearned Revenue"
            ];
            currentLiabilities.forEach(option => {
                let opt = document.createElement("option");
                opt.value = option;
                opt.textContent = option;
                liabilityCategory.appendChild(opt);
            });
        } else if (selectElement.value === "Long-term Liabilities") {
            var longTermLiabilities = [
                "Long-term Bank Loan", "Mortgage Payable", "Bonds Payable", "Deferred Tax Liabilities",
                "Lease Liabilities", "Pension & Retirement Liabilities", "Debentures Payable",
                "Loan from Directors or Partners", "Security Deposits Received", "Deferred Revenue"
            ];
            longTermLiabilities.forEach(option => {
                let opt = document.createElement("option");
                opt.value = option;
                opt.textContent = option;
                liabilityCategory.appendChild(opt);
            });
        } else {
            liabilityCategory.disabled = true;
            liabilityName.disabled = true;
            liabilityName.value = "";
        }
    }

    function toggleLiabilityInput() {
        let liabilityName = document.getElementById("liability_name");
        liabilityName.disabled = false;
    }

    function onAddButtonClickliability() {
        let accountType = document.getElementById("liablity").value;
        let liabilityCategory = document.getElementById("liability_category").value;
        let incomeAmount = document.getElementById("liablity_amount").value;
        let incomeDetails = document.getElementById("liablity_details").value;
        let incomeDate = document.getElementById("liablity_date").value;

        if (accountType === "" || liabilityCategory === "" || incomeAmount === "" || incomeDate === "") {
            alert("Please fill in all fields before adding a row.");
            return;
        }

        let tableBody = document.getElementById("liabilityTableBody");

        let newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td><input type="text" name="liability_type[]" value="${accountType}" class="form-control" readonly></td>
            <td><input type="text" name="liability_category[]" value="${liabilityCategory}" class="form-control"></td>
            <td><input type="text" name="liability_amount[]" value="${incomeAmount}" class="form-control"></td>
            <td><input type="text" name="liability_details[]" value="${incomeDetails}" class="form-control"></td>
            <td><input type="date" name="liability_date[]" value="${incomeDate}" class="form-control"></td>
            <td><a href="#" class="btn btn-danger removeRow" onclick="removeRow(this)">-</a></td>
        `;

        tableBody.appendChild(newRow);

        // Clear input fields after adding the row
        document.getElementById("liablity").value = "";
        document.getElementById("liability_category").innerHTML = '<option value="" selected>Select</option>';
        document.getElementById("liability_category").disabled = true;
        document.getElementById("liablity_amount").value = "";
        document.getElementById("liablity_details").value = "";
    }

    function removeRow(element) {
        element.closest("tr").remove();
    }

</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("chartaccountsform");

    // Prevent form submission using the Enter key
    form.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
        }
    });

    // Form submission validation
    form.addEventListener("submit", function (event) {
        let isValid = true;
        let errorMessage = "";

        // If validation fails, prevent form submission
        if (!isValid) {
            event.preventDefault();
            alert(errorMessage);
        }
    });
});

</script>
</body>
</html>
