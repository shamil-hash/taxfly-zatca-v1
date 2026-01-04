<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Customer</title>

    @if (Session('adminuser'))
        @include('layouts/adminsidebar')
    @elseif(Session('softwareuser'))
        @include('layouts/usersidebar')
    @endif

    <style>
    .btn-primary {
    background-color: #187f6a;
}
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

        /* Flexbox layout for form container */
        .form-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            width: 1100px;
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



        .switch {
        display: inline-block;
        position: relative;
        width: 120px;
        height: 34px;
        margin-bottom: 25px;
        margin-top: -25px;

    }

    .switch-checkbox {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .switch-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #ccc;
        border-radius: 34px;
        cursor: pointer;
        padding: 5px;
        transition: background-color 0.3s;
        position: relative;
        width: 100%;
        height: 100%;
    }

    .switch-label .switch-button {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 26px;
        height: 26px;
        background-color: white;
        border-radius: 50%;
        transition: transform 0.3s;
    }

    .switch-checkbox:checked + .switch-label {
        background-color: #4CAF50;
    }

    .switch-checkbox:checked + .switch-label .switch-button {
        transform: translateX(86px);
    }

    .switch-text {
        margin-left: 30px;
        color: #fff;
    }

    .switch-checkbox:checked ~ .switch-text {
        content: 'Disable';
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
       

        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif

        @if (Session('softwareuser'))
        <x-admindetails_user :shopdatas="$shopdatas" />

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Customer</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Customer</li>
            </ol>
        </nav>
        @endif
        <h2 style="text-align: center;">Create Customer</h2>

        <form action="creditcreateform" method="POST" id="createcredituser" name="createcredituser"
        onsubmit="return(validateSearch());">
            @csrf

            <div class="form-container" style="margin-top: -35px;">
                <!-- Customer Details Section -->
                <div class="form-section">
                    <h4 class="text-center" style="margin-bottom: 20px;">Customer Details</h4>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">Name <span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="name" placeholder="Enter the name" autocomplete="name">
                            </div>
                        <div class="input-group">
                            <span class="input-group-addon">Business Name</span>
                            <input type="text" class="form-control" name="business_name" placeholder="Enter the business name">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">Trade lisence No</span>
                            <input type="text" class="form-control" name="trade_license_no" placeholder="Enter the trade/lisence No.">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">TRN Number</span>
                            <input type="text" class="form-control" name="trn_number" placeholder="Enter the TRN No.">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">Mobile Number</span>
                            <input type="text" class="form-control" name="phone" placeholder=" Enter the mobile No." autocomplete="tel">
                            </div>
                            <div class="switch">
                                <input id="toggleSwitchCredit" type="checkbox" class="switch-checkbox" onclick="toggleCreditLimit()">
                                <label for="toggleSwitchCredit" class="switch-label">
                                    <span class="switch-button"></span>
                                    <span class="switch-text" id="switchText">&nbsp;&nbsp;&nbsp;Enable</span>
                                </label>
                            </div>

                            <div class="input-group">
                                <span class="input-group-addon">Credit limit</span>
                                <input id="creditLimitInput" type="text" class="form-control" name="lamount" placeholder="Enter the limit amount" disabled>
                            </div>
                        <div class="input-group">
                            <span class="input-group-addon">Email</span>
                            <input type="text" class="form-control" name="email" placeholder="Enter the email" autocomplete="email">
                            </div>
                            @if(Session('adminuser'))
                            <div class="input-group">

                                    <span class="input-group-addon">Branch<span style="color: red;">*</span></span>
                                    <select id="branch" name="location" class="control">
                                        <option selected value="">Select Branch</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->location }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @elseif(Session('softwareuser'))
                                <input type="hidden" name="location" value="{{$branch}}">
                                @endif
                                <div class="switch" style=" display: none;position: relative;width: 120px;height: 40px;margin-bottom: 25px;margin-top: -25px;">
                                        <input id="toggleSwitchUsername" type="checkbox" class="switch-checkbox" onclick="toggleUsernamePassword()">
                                        <label for="toggleSwitchUsername" class="switch-label">
                                            <span class="switch-button" style="width:26px;height: 30px;"></span>
                                            <span class="switch-text" id="switchTextUserPass" style="font-size: 12px;">
                                                username<br>password</span>
                                                </label>
                                    </div>

                        <div id="usernamePasswordFields" style="display: none;">
                            <div class="input-group">
                                <span class="input-group-addon">Username <span style="color: red;">*</span></span>
                                <input type="text" class="form-control" name="username" placeholder="Enter the username" value="" autocomplete="username">
                                </div>
                            <div class="input-group">
                                <span class="input-group-addon">Password <span style="color: red;">*</span></span>
                                <input type="password" class="form-control" name="password" placeholder="Enter the password">
                            </div>
                        </div>
                        </div>
                                        </div>

                <!-- Address Section -->
                <div class="form-section">
    <h4 class="text-center" style="margin-bottom: 20px;">Address</h4>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">Billing Address</span>
            <input type="text" class="form-control" name="billing_address" placeholder="Enter the street address">
        </div>
        <div class="input-group">
            <span class="input-group-addon">City</span>
            <input type="text" class="form-control" name="billing_city" placeholder="Enter the city">
        </div>
        <div class="input-group">
            <span class="input-group-addon">State/Province</span>
            <input type="text" class="form-control" name="billing_state" placeholder="Enter the state/province">
        </div>
        <div class="input-group">
            <span class="input-group-addon">Postal/ZIP Code</span>
            <input type="text" class="form-control" name="billing_zip" placeholder="Enter the postal/ZIP code">
        </div>
        <div class="input-group">
            <span class="input-group-addon">landmark</span>
            <input type="text" class="form-control" name="billing_landmark" placeholder="Enter the landmark">
        </div>
        <div class="input-group">
            <span class="input-group-addon">Country</span>
            <input type="text" class="form-control" name="billing_country" placeholder="Enter the country">
        </div>
        <!-- Same as Billing Address Checkbox -->
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="sameAddress" onclick="fillDeliveryAddress()">
            <label class="form-check-label" for="sameAddress">Same as Billing Address</label>
        </div>
        <div class="input-group">
            <span class="input-group-addon">Delivery Address</span>
            <input type="text" class="form-control" name="delivery_address" placeholder="Enter the street address">
        </div>

        <div class="input-group">
            <span class="input-group-addon">City</span>
            <input type="text" class="form-control" name="delivery_city" placeholder="Enter the city">
        </div>
        <div class="input-group">
            <span class="input-group-addon">State/Province</span>
            <input type="text" class="form-control" name="delivery_state" placeholder="Enter the state/province">
        </div>
        <div class="input-group">
            <span class="input-group-addon">Postal/ZIP Code</span>
            <input type="text" class="form-control" name="delivery_zip" placeholder="Enter the postal/ZIP code">
        </div>
        <div class="input-group">
            <span class="input-group-addon">Landmark</span>
            <input type="text" class="form-control" name="delivery_landmark" placeholder="Enter the landmark">
        </div>
        <div class="input-group">
            <span class="input-group-addon">Country</span>
            <input type="text" class="form-control" name="delivery_country" placeholder="Enter the country">
        </div>
             <div class="form-check ml-auto">
        <input type="checkbox" class="form-check-input" id="showDeliveryAddress" name="showDeliveryAddress" value="1">
        <label class="form-check-label" for="showDeliveryAddress">Display Delivery Address in Invoice</label>
        </div>
    </div>
</div>

            </div>
            <div class="text-center">
                <button type="button" class="btn btn-secondary btn-add-bank"
                style="background-color: #6c757d; border-color: #adb5bd;color: #ffffff;  padding: 8px 16px; font-size: 12px; margin: 20px 0;">
                Add Bank
                </button>

            </div>

            <div id="bank-details" style="display: none;">
                <div class="form-section " style="width: 700px;">
            <h4 class="text-center" style="margin-bottom: 20px;">Bank Details</h4>
        <!-- Left Column -->
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon">Account Name</span>
                <input name="accountName" type="text" class="form-control" id="accountName" placeholder="Enter the account name" tabindex="1">
            </div>
            <div class="input-group">
                <span class="input-group-addon">Bank Name</span>
            <input name="bank_name" type="text" class="form-control" id="bank_name" placeholder="Enter the bank name">
            </div>
            <div class="input-group">
                <span class="input-group-addon">Branch</span>
                <input type="text" class="form-control" name="branch" placeholder="Enter the branch name">
            </div>

            <div class="input-group">
                <span class="input-group-addon">Code Type</span>
                <select name="codeType" class="control" id="codeType">
                    <option value="" selected>Select code type</option>
                    <option value="ifsc">IFSC</option>
                    <option value="iban">IBAN</option>
                </select>
            </div>

            <div class="input-group" id="ifscField" style="display:none;">
                <span class="input-group-addon">Enter IFSC Code</span>
                <input name="ifscCode" type="text" class="form-control" id="ifscCode" placeholder="Enter the IFSC code">
            </div>
            <div class="input-group" id="ibanField" style="display:none;">
                <span class="input-group-addon">Enter IBAN Number</span>
                <input name="ibanCode" type="text" class="form-control" id="ibanCode" placeholder="Enter the IBAN code">
            </div>
        </div>

        <!-- Right Column -->
            <div class="input-group">
                <span class="input-group-addon">Account Number</span>
               <input name="account_number" type="text" class="form-control" id="account_number" placeholder="Enter the account No.">
            </div>

            <div class="input-group">
                <span class="input-group-addon">Date</span>
                <input name="date" type="date" class="form-control" id="date">
            </div>
            <div class="input-group">
                <span class="input-group-addon">Account Type</span>
                <select name="accountType" class="control" id="accountType">
                    <option value="" selected>Select account type</option>
                    <option value="savings">Savings</option>
                    <option value="current">Current</option>
                    <option value="fixed">Fixed Deposit</option>
                </select>
            </div>
            <!--<div class="input-group">-->
            <!--    <span class="input-group-addon">UPI ID</span>-->
            <!--    <input name="upiid" type="text" class="form-control" id="upiid" placeholder="Enter the UPI ID">-->
            <!--</div>-->
            <div class="input-group">
                <span class="input-group-addon">Country</span>
                <input name="country" type="text" class="form-control" id="country" placeholder="Enter the country" autocomplete="country-name">
                </div>
    </div>
</div>



            <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary submitcredituser" id="submitBtn">SUBMIT</button>
            </div>
        </form>
    </div>

    <script type="text/javascript">
        document.getElementById("sameAddress").addEventListener("change", function () {
            if (this.checked) {
                document.querySelector("input[name='delivery_address']").value = document.querySelector("input[name='billing_address']").value;
            } else {
                document.querySelector("input[name='delivery_address']").value = "";
            }
        });

        var currentBoxNumber = 0;

        $(".name").keydown(function(event) {
            if (event.keyCode == 13) {
                textboxes = $("input.username");
                currentBoxNumber = textboxes.index(this);
                if (textboxes[currentBoxNumber + 1] != null) {
                    nextBox = textboxes[currentBoxNumber + 1];
                    nextBox.focus();
                    nextBox.select();
                }
                event.preventDefault();
                return false;
            }
        });

        // Additional keydown event handlers for other fields
        // (similar to the one above for 'name')

        $(".location").keydown(function(event) {
            if (event.keyCode == 13) {
                textboxes = $("button.submitcredituser");
                currentBoxNumber = textboxes.index(this);
                if (textboxes[currentBoxNumber + 1] != null) {
                    nextBox = textboxes[currentBoxNumber + 1];
                    nextBox.focus();
                    nextBox.select();
                }
                event.preventDefault();
                return false;
            }
        });

        $(function() {
            $('#createcredituser').on('submit', function(e) {
                var emptyInputs = $('input[name="billing_address"], input[name="delivery_address"]').filter(function() {
                    return $.trim($(this).val()) === '';
                });

                // if (emptyInputs.length) {
                //     alert('Please fill in all required fields.');
                //     e.preventDefault();
                // }
            });
        });

        function fillDeliveryAddress() {
    // Check if the checkbox is checked
    const isChecked = document.getElementById('sameAddress').checked;

    // Get the billing address values
    const billingAddress = document.querySelector('input[name="billing_address"]').value;
    const billingCity = document.querySelector('input[name="billing_city"]').value;
    const billingState = document.querySelector('input[name="billing_state"]').value;
    const billingZip = document.querySelector('input[name="billing_zip"]').value;
    const billinglandmark = document.querySelector('input[name="billing_landmark"]').value;
    const billingCountry = document.querySelector('input[name="billing_country"]').value;

    // Assign the values to the delivery address fields if checked, otherwise clear the fields
    document.querySelector('input[name="delivery_address"]').value = isChecked ? billingAddress : '';
    document.querySelector('input[name="delivery_city"]').value = isChecked ? billingCity : '';
    document.querySelector('input[name="delivery_state"]').value = isChecked ? billingState : '';
    document.querySelector('input[name="delivery_zip"]').value = isChecked ? billingZip : '';
    document.querySelector('input[name="delivery_landmark"]').value = isChecked ? billinglandmark : '';
    document.querySelector('input[name="delivery_country"]').value = isChecked ? billingCountry : '';
}



    </script>
     <script>
    document.querySelector('.btn-add-bank').addEventListener('click', function () {
        const bankDetailsSection = document.getElementById('bank-details');

        // Check if the bank details section is currently hidden or visible
        if (bankDetailsSection.style.display === 'none' || bankDetailsSection.style.display === '') {
            bankDetailsSection.style.display = 'block'; // Show the section
        } else {
            bankDetailsSection.style.display = 'none'; // Hide the section
        }
    });

    $(document).ready(function () {
    // Handle dropdown change event
    $('#codeType').on('change', function () {
        var selectedValue = $(this).val();

        if (selectedValue === 'ifsc') {
            // Show IFSC field and hide IBAN field
            $('#ifscField').show();
            $('#ibanField').hide();
            $('#ibanCode').val(''); // Clear IBAN field if previously selected
        } else if (selectedValue === 'iban') {
            // Show IBAN field and hide IFSC field
            $('#ibanField').show();
            $('#ifscField').hide();
            $('#ifscCode').val(''); // Clear IFSC field if previously selected
        } else {
            // Hide both fields if no selection is made
            $('#ifscField').hide();
            $('#ibanField').hide();
            $('#ifscCode').val('');
            $('#ibanCode').val('');
        }
    });
});
</script>
<script>
    function validateSearch() {
        // Get form fields
        let name = document.forms["createcredituser"]["name"].value.trim();
        let branch = document.forms["createcredituser"]["location"].value;
        let username = document.forms["createcredituser"]["username"].value.trim();
        let password = document.forms["createcredituser"]["password"].value.trim();

        // Get the submit button
        const submitBtn = document.getElementById('submitBtn'); // Make sure the button has this ID

        // Validate Name
        if (name === "") {
            alert("Please enter your name.");
            return false;
        }

        // Validate Branch
        if (branch === "") {
            alert("Please select a branch.");
            return false;
        }

        // Validate Username and Password separately
        if (username && !password) {
            alert("Please fill in the password.");
            return false;
        }
        if (!username && password) {
            alert("Please fill in the username.");
            return false;
        }

        // If validation passes, disable the submit button
        submitBtn.disabled = true; // Disable the button
        submitBtn.value = 'Submitting...'; // Change button text to indicate processing

        return true; // Return true if validation passes
    }

    // Attach the validateSearch function to the form's onsubmit event
    document.forms["createcredituser"].onsubmit = function(e) {
        if (!validateSearch()) {
            e.preventDefault(); // Prevent form submission if validation fails
        }
    };
    </script>

   <script>
   // Function to toggle the credit limit input field
   function toggleCreditLimit() {
        var inputField = document.getElementById('creditLimitInput');
        var switchText = document.getElementById('switchText');

        if (inputField.disabled) {
            inputField.disabled = false;
            switchText.textContent = 'Disable';
        } else {
            inputField.disabled = true;
            switchText.textContent = 'Enable';
        }
    }

   // Function to toggle the visibility of the username and password fields
   function toggleUsernamePassword() {
        var toggleSwitch = document.getElementById("toggleSwitchUsername");
        var usernamePasswordFields = document.getElementById("usernamePasswordFields");

        if (toggleSwitch.checked) {
            usernamePasswordFields.style.display = "block";  // Show the fields
        } else {
            usernamePasswordFields.style.display = "none";   // Hide the fields
        }
    }
</script>
</body>
</html>
