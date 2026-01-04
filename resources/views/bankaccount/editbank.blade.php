<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Bank</title>

    @include('layouts/usersidebar')
    <style>
          form {
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

        /* Form control styling for input fields */
        .form-control {
            width: 100%; /* Full width for inputs */
            padding: 10px; /* Padding for better spacing */
            border: 1px solid #ced4da; /* Border color */
            border-radius: 0 4px 4px 0; /* Rounded corners on the right */
            font-size: 14px; /* Font size */
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


    </style>

    <script>
        function validateSearch() {
            var accountName = document.getElementById("accountName").value.trim();
            var accountNo = document.getElementById("accountNo").value.trim();
            var bankName = document.getElementById("bankName").value.trim();
            var openingBalance = document.getElementById("openingBalance").value.trim();

            if (accountName === "") {
                alert("Account Name is required.");
                return false;
            }

            if (accountNo === "") {
                alert("Account Number is required.");
                return false;
            }

            if (bankName === "") {
                alert("Bank Name is required.");
                return false;
            }

            if (openingBalance === "") {
                alert("Opening Balance is required.");
                return false;
            }

            return true;
        }

        $(document).ready(function () {
            $('#codeType').on('change', function () {
                var selectedValue = $(this).val();

                if (selectedValue === 'ifsc') {
                    $('#ifscField').show();
                    $('#ibanField').hide();
                    $('#ibanCode').val('');
                } else if (selectedValue === 'iban') {
                    $('#ibanField').show();
                    $('#ifscField').hide();
                    $('#ifscCode').val('');
                } else {
                    $('#ifscField').hide();
                    $('#ibanField').hide();
                    $('#ifscCode').val('');
                    $('#ibanCode').val('');
                }
            });
        });
    </script>
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
<div style="text-align: center;">
    @foreach ($shopdatas as $shopdata)
        <div style="display: inline-block; margin-bottom: 10px;">
            <p style="margin: 0;">{{ $shopdata['company'] }}</p>
            <p style="margin: 0;">Branch: {{ $shopdata['location'] }}</p>
            <p style="margin: 0;">Phone No: {{ $shopdata['mobile'] }}</p>
            <p style="margin: 0;">Email: {{ $shopdata['email'] }}</p>
        </div>
    @endforeach
</div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Bank</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Bank</li>
            </ol>
        </nav>

        <div class="form-container" style="margin-top: -85px;">
        <div id="employee-details" class="bank-details" >
            <form action="{{ route('bank.update', $editbank->id) }}" method="post" onsubmit="return(validateSearch());">
                @csrf
                @method('PUT')
                <h2 class="text-center" >Edit Bank</h2>
                <div class="form-section">
                    <div class="form-group">
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Account Name</span>
                            <input name="accountName" type="text" class="form-control" id="accountName"
                            value="{{ $editbank->account_name }}" readonly>
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Bank Name</span>
                        <input name="bankName" type="text" class="form-control" id="bankName"
                           value="{{ $editbank->bank_name }}" readonly>
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Account No.</span>
                        <input name="accountNo" type="text" class="form-control" id="accountNo"
                           value="{{ $editbank->account_no }}">
                    </div>

                  <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Current Balance</span>
                        <input name="openingBalance" type="text" class="form-control" readonly id="openingBalance"
                              value="{{ $editbank->current_balance }}">
                        <input name="currentbalance" type="hidden" class="form-control" readonly id="openingBalance"
                            value="{{ $editbank->opening_balance }}">
                    </div>
                    <div class="input-group" style="margin-top: 15px;">
                        <span class="input-group-addon">Add Amount</span>
                        <input type="text" class="form-control" name="add_amount" placeholder="Add amount in your bank" pattern="^[0-9]+$" title="Only numbers are allowed">
                    </div>
                    <div class="input-group" style="margin-top: 15px;">
                        <span class="input-group-addon">Depositing Date</span>
                        <input type="date" class="form-control" name="depositing_date">
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Code Type</span>
                        <select name="codeType" class="control" id="codeType">
                            <option value="" selected>Select Code Type</option>
                            <option value="ifsc">IFSC</option>
                            <option value="iban">IBAN</option>
                        </select>
                    </div>

                    <div class="input-group" id="ifscField" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Enter IFSC Code</span>
                        <input name="ifscCode" type="text" class="form-control" id="ifscCode" >
                    </div>

                    <div class="input-group" id="ibanField" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Enter IBAN Number</span>
                        <input name="ibanCode" type="text" class="form-control" id="ibanCode" >
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Branch Name</span>
                        <input name="branchName" type="text" class="form-control" id="branchName"  value="{{ $editbank->branch_name }}">
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Country</span>
                        <input name="country" type="text" class="form-control" id="country"  value="{{ $editbank->country }}">
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Account Type</span>
                        <select name="accountType" class="control" id="accountType">
                            <option value="" selected>Select Account Type</option>
                            <option value="savings" {{ $editbank->account_type == 'savings' ? 'selected' : '' }}>Savings</option>
                            <option value="current" {{ $editbank->account_type == 'current' ? 'selected' : '' }}>Current</option>
                            <option value="fixed" {{ $editbank->account_type == 'fixed' ? 'selected' : '' }}>Fixed Deposit</option>
                        </select>
                    </div>
                    <!--<div class="input-group" style="margin-bottom: 15px;">-->
                    <!--    <span class="input-group-addon">UPI Id</span>-->
                    <!--    <input name="upIId" type="text" class="form-control" id="upIId"  value="{{ $editbank->upi_id }}">-->
                    <!--</div>-->

                <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>

            </form>
        </div>
    </div>
</div>
</body>
</html>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let today = new Date().toISOString().split('T')[0];
        document.querySelector('input[name="depositing_date"]').value = today;
    });
    </script>