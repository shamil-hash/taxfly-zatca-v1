<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Bank</title>

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
        <div style="margin-top: 15px; margin-left: 15px;">
            @include('navbar.banknav')
        </div>
        @else
            <x-logout_nav_user />
        @endif
        @if (session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
        @endif
        <x-admindetails_user :shopdatas="$shopdatas" />

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Bank</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Bank</li>
            </ol>
        </nav>

        <div class="form-container" style="margin-top: -85px;">
            <div id="employee-details" class="bank-details" >
        <form id="banksubmit" action="banksubmit" method="post" onsubmit="return validateForm()">
    @csrf
    <h2 class="text-center">Add Bank</h2>

    <div class="form-section" >
        <div class="form-group">
            <div class="input-group" style="margin-bottom: 15px;">
                <span class="input-group-addon">Account Name<span style="color: red;">*</span></span>
                <input name="accountName" type="text" class="form-control" id="accountName" placeholder="Enter the account name" tabindex="1" >
            </div>

            <div class="input-group" style="margin-bottom: 15px;">
                <span class="input-group-addon">Bank Name<span style="color: red;">*</span></span>
                <input name="bankName" type="text" class="form-control" id="bankName" placeholder="Enter the bank name" tabindex="4" >
            </div>

            <div class="input-group" style="margin-bottom: 15px;">
                <span class="input-group-addon">Account Number<span style="color: red;">*</span></span>
                <input name="accountNo" type="text" class="form-control" id="accountNo" placeholder="Enter the account number" tabindex="2" >
            </div>

            <div class="input-group" style="margin-bottom: 15px;">
                <span class="input-group-addon">Date<span style="color: red;">*</span></span>
                <input name="date" type="date" class="form-control" id="date" >
            </div>

            <div class="input-group" style="margin-bottom: 15px;">
                <span class="input-group-addon">Opening Balance<span style="color: red;">*</span></span>
                <input name="openingBalance" type="text" class="form-control" id="openingBalance" placeholder="Enter the opening balance" tabindex="3" >
            </div>

           <div class="form-check ml-auto">
                <input class="form-check-input" type="checkbox" name="defaultBank" id="defaultBank" value="1">
                <label class="form-check-label" for="">Default Bank for Invoice</label>
                </div>


            <div class="text-center">
                <button type="button" class="btn btn-primary" id="showAdditionalDetails">Add Further Details</button>
            </div>
        </div>

        <!-- Additional Details Section -->
        <div id="additional-details" class="additional-details" style="display: none;">
            <div class="form-group">
                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Branch Name</span>
                    <input name="branchName" type="text" class="form-control" id="branchName" placeholder="Enter the branch name">
                </div>

                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Code Type</span>
                    <select name="codeType" class="control" id="codeType">
                        <option value="">Select Code Type</option>
                        <option value="ifsc">IFSC</option>
                        <option value="iban">IBAN</option>
                    </select>
                </div>

                <!-- IFSC Code Field -->
                <div class="input-group" id="ifscField" style="display:none; margin-bottom: 15px;">
                    <span class="input-group-addon">Enter IFSC Code</span>
                    <input name="ifscCode" type="text" class="form-control" id="ifscCode" placeholder="Enter the IFSC code">
                </div>

                <!-- IBAN Code Field -->
                <div class="input-group" id="ibanField" style="display:none; margin-bottom: 15px;">
                    <span class="input-group-addon">Enter IBAN Number</span>
                    <input name="ibanCode" type="text" class="form-control" id="ibanCode" placeholder="Enter the IBAN code">
                </div>

                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Account Type</span>
                    <select name="accountType" class="control" id="accountType">
                        <option value="">Select Account Type</option>
                        <option value="savings">Savings</option>
                        <option value="current">Current</option>
                        <option value="fixed">Fixed Deposit</option>
                    </select>
                </div>

                <!--<div class="input-group" style="margin-bottom: 15px;">-->
                <!--    <span class="input-group-addon">UPI ID</span>-->
                <!--    <input name="upiid" type="text" class="form-control" id="upiid" placeholder="Enter the UPI ID">-->
                <!--</div>-->

                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Country</span>
                    <input name="Country" type="text" class="form-control" id="Country" placeholder="Enter the country name">
                    </select>
                </div>
            </div>
        </div>

        <div class="text-center" style="margin-top: 20px;">
            <button type="submit" id="submitBtn" class="btn btn-primary" style="font-size: 14px;">Submit</button>
        </div>
    </div>
</form>
        </div></div>
    <script>
        $(document).ready(function () {
            // Toggle additional details
            $('#showAdditionalDetails').on('click', function () {
                var additionalDetails = $('#additional-details');
                if (additionalDetails.is(':visible')) {
                    additionalDetails.hide();
                    $(this).text('Add Further Details');
                } else {
                    additionalDetails.show();
                    $(this).text('Hide Further Details');
                }
            });

            // Handle dropdown change event
            $('#codeType').on('change', function () {
                var selectedValue = $(this).val();
                if (selectedValue === 'ifsc') {
                    $('#ifscField').show();
                    $('#ibanField').hide();
                } else if (selectedValue === 'iban') {
                    $('#ibanField').show();
                    $('#ifscField').hide();
                } else {
                    $('#ifscField').hide();
                    $('#ibanField').hide();
                }
            });

            // Form validation

        });
        $('#banksubmit').on('submit', function (e) {
    if ($('#defaultBank').is(':checked')) {
        if (!confirm('Are you sure you want to set this bank as the default for invoices?')) {
            e.preventDefault();
        }
    }
});

</script>
<script>
   function validateForm() {
    const requiredFields = [
        'accountName',
        'bankName',
        'accountNo',
        'date',
        'openingBalance'
    ];

    // Get the submit button
    const submitBtn = document.getElementById('submitBtn'); // Ensure the button has this ID

    for (const field of requiredFields) {
        const input = document.getElementById(field);
        if (!input.value) {
            alert(`Please fill ${input.previousElementSibling.innerText}`);
            input.focus();
            return false; // Prevent form submission
        }
    }

    // If all required fields are filled
    submitBtn.disabled = true; // Disable the button
    submitBtn.value = 'Submitting...'; // Change button text to indicate processing

    return true; // Allow form submission
}

// Attach the validateForm function to the form's onsubmit event
document.querySelector('form').onsubmit = function(e) {
    if (!validateForm()) {
        e.preventDefault(); // Prevent form submission if validation fails
    }
};

</script>
</body>
</html>
