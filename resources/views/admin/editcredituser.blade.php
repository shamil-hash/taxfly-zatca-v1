<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Edit Customer</title>
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
            width: 1200px;
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
    <div id="content">
    

        @if (Session::has('failed'))
            <div class="alert alert-danger">
                {{ Session::get('failed') }}
            </div>
        @endif
        @if (Session('softwareuser'))
        <x-admindetails_user :shopdatas="$shopdatas" />

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Customer</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Customer</li>
            </ol>
        </nav>
        @endif
        <h2 style="text-align: center;">Edit Customer</h2>
        <form action="/credituseredit" method="POST" id="credituser_edit" name="credituser_edit"
        onsubmit="return(validateSearch());">
        @csrf
        <div class="form-container" style="margin-top: -35px;">
<div class="form-section" >
    <h4 class="text-center" style="margin-bottom: 20px;">Customer Details</h4>
    <div class="form-group">
        <input type="hidden" class="form-control" name="id" value="{{ $uid }}">

        <div class="input-group mb-3">
            <span class="input-group-addon">Name <span style="color: red;">*</span></span>
            <input type="text" class="form-control" name="name"  value="{{ $name }}" autocomplete="name">
            <span style="color:red">@error('name') {{ $message }} @enderror</span>
        </div>

        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">Business Name</span>
            <input type="text" class="form-control" name="business_name"  value="{{ $business_name }}" autocomplete="business_name">
            <span style="color:red">@error('business_name') {{ $message }} @enderror</span>
        </div>

        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">Trade License No</span>
            <input type="text" class="form-control" name="trade_license_no"  value="{{ $trade_license_no }}" autocomplete="trade_license_no">
            <span style="color:red">@error('trade_license_no') {{ $message }} @enderror</span>
        </div>

        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">Email</span>
            <input type="text" class="form-control" name="email"  value="{{ $email }}" autocomplete="email">
            <span style="color:red">@error('email') {{ $message }} @enderror</span>
        </div>

        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">Mobile Number</span>
            <input type="text" class="form-control" name="phone"  value="{{ $phone }}" autocomplete="tel">
            <span style="color:red">@error('phone') {{ $message }} @enderror</span>
        </div>
        @if ($current_lamount!==null)
        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">Add Amount</span>
            <input type="text" class="form-control" name="add_amount" placeholder="Add credit limit amount" pattern="^[0-9]+$" title="Only numbers are allowed">
        </div>
        <input type="hidden" class="form-control" name="current_lamount"  value="{{ $current_lamount }}">
        @endif

        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">TRN Number</span>
            <input type="text" class="form-control" name="trn_number"  value="{{ $trn_number }}">
            <span style="color:red">@error('trn_number') {{ $message }} @enderror</span>
        </div>
@if ($username!==null)

<div class="input-group mb-3" style="margin-top: 10px;">
    <span class="input-group-addon">Username </span>
    <input type="text" class="form-control" name="username"  value="{{ $username }}" autocomplete="username" readonly>
    <span style="color:red">@error('username') {{ $message }} @enderror</span>
</div>

{{-- <div class="input-group mb-3" style="margin-top: 10px;">
    <span class="input-group-addon">Password <span style="color: red;">*</span></span>
    <input type="password" class="form-control" name="password">
    <span style="color:red">@error('password') {{ $message }} @enderror</span>
</div> --}}
@endif
    </div>
</div>

<div class="form-section">
    <h4 class="text-center" style="margin-bottom: 20px;">Address</h4>
    <div class="form-group">
        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">Billing Address</span>
            <input type="text" class="form-control" name="billing_address"  value="{{ $billing_address }}" autocomplete="billing_address">
            <span style="color:red">@error('billing_address') {{ $message }} @enderror</span>
        </div>
        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">City</span>
            <input type="text" class="form-control" name="billing_city"  value="{{ $billing_city }}" autocomplete="billing_city">
            <span style="color:red">@error('billing_city') {{ $message }} @enderror</span>
        </div>
        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">State/Province</span>
            <input type="text" class="form-control" name="billing_state"  value="{{ $billing_state }}" autocomplete="billing_state">
            <span style="color:red">@error('billing_state') {{ $message }} @enderror</span>
        </div>

        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">Postal/ZIP Code</span>
            <input type="text" class="form-control" name="billing_zip"  value="{{ $billing_zip }}" autocomplete="billing_zip">
            <span style="color:red">@error('billing_zip') {{ $message }} @enderror</span>
        </div>

        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">Landmark</span>
            <input type="text" class="form-control" name="billing_landmark"  value="{{ $billing_landmark }}" autocomplete="billing_landmark">
            <span style="color:red">@error('billing_landmark') {{ $message }} @enderror</span>
        </div>

        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">Country</span>
            <input type="text" class="form-control" name="billing_country"  value="{{ $billing_country }}" autocomplete="billing_country">
            <span style="color:red">@error('billing_country') {{ $message }} @enderror</span>
        </div>

        <div class="form-check mb-3" style="margin-top: 10px;">
            <input type="checkbox" class="form-check-input" id="sameAddress" onclick="fillDeliveryAddress()">
            <label class="form-check-label" for="sameAddress">Same as Billing Address</label>
        </div>

        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">Delivery Address</span>
            <input type="text" class="form-control" name="delivery_address"  value="{{ $delivery_address }}" autocomplete="delivery_address">
            <span style="color:red">@error('delivery_address') {{ $message }} @enderror</span>
        </div>


        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">City</span>
            <input type="text" class="form-control" name="delivery_city"  value="{{ $delivery_city }}" autocomplete="delivery_city">
            <span style="color:red">@error('delivery_city') {{ $message }} @enderror</span>
        </div>

        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">State/Province</span>
            <input type="text" class="form-control" name="delivery_state"  value="{{ $delivery_state }}" autocomplete="delivery_state">
            <span style="color:red">@error('delivery_state') {{ $message }} @enderror</span>
        </div>

        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">Postal/ZIP Code</span>
            <input type="text" class="form-control" name="delivery_zip"  value="{{ $delivery_zip }}" autocomplete="delivery_zip">
            <span style="color:red">@error('delivery_zip') {{ $message }} @enderror</span>
        </div>

        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">Landmark</span>
            <input type="text" class="form-control" name="delivery_landmark"  value="{{ $delivery_landmark }}" autocomplete="delivery_landmark">
            <span style="color:red">@error('delivery_landmark') {{ $message }} @enderror</span>
        </div>

        <div class="input-group mb-3" style="margin-top: 10px;">
            <span class="input-group-addon">Country</span>
            <input type="text" class="form-control" name="delivery_country"  value="{{ $delivery_country }}" autocomplete="delivery_country">
            <span style="color:red">@error('delivery_country') {{ $message }} @enderror</span>
        </div>
            <div class="form-check" style="margin-top: 10px;">
    <input type="checkbox" class="form-check-input" id="showDeliveryAddress" name="delivery_default" value="1"
        {{ $delivery_default == 1 ? 'checked' : '' }} autocomplete="showDeliveryAddress">
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

        <div id="bank-details" class="bank-details"  style="display: none;">
            <div class="form-section " style="width: 700px;">
    <h4 class="text-center" style="margin-bottom: 20px;">Bank Details</h4>
    <!-- Left Column -->
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">Account Name</span>
                    <input name="accountName" type="text" class="form-control" id="accountName"  tabindex="1" value="{{ $accountName }}" autocomplete="accountName">
            <span style="color:red">
                        @error('accountName')
                            {{ $message }}
                        @enderror
                    </span>
        </div>
        <div class="input-group">
            <span class="input-group-addon">Bank Name</span>
            <input name="bank_name" type="text" class="form-control" id="bank_name"  value="{{ $bank_name }}" autocomplete="bank_name">
            <span style="color:red">
                        @error('bank_name')
                            {{ $message }}
                        @enderror
                    </span>
        </div>
        <div class="input-group">
            <span class="input-group-addon">Branch</span>
            <input type="text" class="form-control" name="branch" value="{{ $branch }}" autocomplete="branch">
            <span style="color:red">
                        @error('branch')
                            {{ $message }}
                        @enderror
                    </span>
        </div>

        <div class="input-group">
            <span class="input-group-addon">Code Type</span>
            <select name="codeType" class="control" id="codeType">
                <option value="" selected>Select Code Type</option>
                <option value="ifsc">IFSC</option>
                <option value="iban">IBAN</option>
            </select>
        </div>
        <div class="input-group" id="ifscField" style="display:none;">
            <span class="input-group-addon">Enter IFSC Code</span>
            <input name="ifscCode" type="text" class="form-control" id="ifscCode" value="{{ $ifscCode }}" autocomplete="ifscCode">
            <span style="color:red">
                        @error('ifscCode')
                            {{ $message }}
                        @enderror
                    </span>
        </div>
        <div class="input-group" id="ibanField" style="display:none;">
            <span class="input-group-addon">Enter IBAN Number</span>
            <input name="ibanCode" type="text" class="form-control" id="ibanCode" value="{{ $ibanCode }}" autocomplete="ibanCode">
            <span style="color:red">
                        @error('ibanCode')
                            {{ $message }}
                        @enderror
                    </span>
        </div>
    </div>

    <!-- Right Column -->
    <div class="input-group">
        <span class="input-group-addon">Account Number</span>
        <input name="account_number" type="text" class="form-control" id="account_number" value="{{ $account_number }}" autocomplete="account_number">
            <span style="color:red">
                        @error('account_number')
                            {{ $message }}
                        @enderror
                    </span>
        </div>
        <div class="input-group">
            <span class="input-group-addon">Date</span>
            <input name="date" type="date" class="form-control" id="date" value="{{ $date }}" autocomplete="date">
            <span style="color:red">
                        @error('date')
                            {{ $message }}
                        @enderror
                    </span>
        </div>
        <div class="input-group">
            <span class="input-group-addon">Account Type</span>
            <select name="accountType" class="control" id="accountType" value="{{ $accountType }}" autocomplete="accountType">
                <option value="" selected>Select Account Type</option>
                <option value="savings">Savings</option>
                <option value="current">Current</option>
                <option value="fixed">Fixed Deposit</option>
            </select>
        </div>
        <!--<div class="input-group">-->
        <!--    <span class="input-group-addon">UPI ID</span>-->
        <!--    <input name="upiid" type="text" class="form-control" id="upiid" value="{{ $upiid }}" autocomplete="upiid">-->
        <!--    <span style="color:red">-->
        <!--                @error('upiid')-->
        <!--                    {{ $message }}-->
        <!--                @enderror-->
        <!--            </span>-->
        <!--</div>-->
        <div class="input-group">
            <span class="input-group-addon">Country</span>
            <input name="country" type="text" class="form-control" id="country"  autocomplete="country-name" value="{{ $country }}" autocomplete="country">
            <span style="color:red">
                        @error('country')
                            {{ $message }}
                        @enderror
                    </span>
        </div>
    </div>
</div>


    <div class="text-center">
        <button type="submit" class="btn btn-primary submitcredit" id="editcredit">UPDATE</button>
    </div>
</div>
</form>
    </div>

</body>

</html>

<script type="text/javascript">
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
    $(".username").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.email");
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
    $(".email").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.phone");
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
    $(".phone").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.trn_number");
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
    $(".trn_number").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.password");
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
    $(".password").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.confirmpassword");
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
    $(".confirmpassword").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("button.submitcredit");
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
</script>
<!-- Submit form data when clicking ENTER BUTTON -->
<script>
    $(function() {
        $('#credituser_edit').keypress(function(e) { //use form id
            if (e.which == 13) {
                validateSearch(); //-- to validate form
                $('#credituser_edit').submit(); // use form id
                return false;
            }
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("credituser_edit");
        const submitBtn = document.getElementById("editcredit");

        form.addEventListener("submit", function(e) {
            // Prevent the form from submitting multiple times
            submitBtn.disabled = true;
            submitBtn.innerText = "Submitting...";

            // Allow the form to submit normally
            return true;
        });
    });
</script>
<script>

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
    const isChecked = document.getElementById('sameAddress').checked;

    if (isChecked) {
        // Get billing address values
        const billingAddress = document.querySelector('input[name="billing_address"]').value;
        const billingCity = document.querySelector('input[name="billing_city"]').value;
        const billingState = document.querySelector('input[name="billing_state"]').value;
        const billingZip = document.querySelector('input[name="billing_zip"]').value;
        const billingCountry = document.querySelector('input[name="billing_country"]').value
        const landmark = document.querySelector('input[name="billing_landmark"]').value;

        // Set delivery address values
        document.querySelector('input[name="delivery_address"]').value = billingAddress;
        document.querySelector('input[name="delivery_city"]').value = billingCity;
        document.querySelector('input[name="delivery_state"]').value = billingState;
        document.querySelector('input[name="delivery_zip"]').value = billingZip;
        document.querySelector('input[name="delivery_country"]').value = billingCountry;
        document.querySelector('input[name="delivery_landmark"]').value = landmark;
    } else {
        // Clear delivery address fields if checkbox is unchecked
        document.querySelector('input[name="delivery_address"]').value = '';
        document.querySelector('input[name="delivery_city"]').value = '';
        document.querySelector('input[name="delivery_state"]').value = '';
        document.querySelector('input[name="delivery_zip"]').value = '';
        document.querySelector('input[name="delivery_country"]').value = '';
        document.querySelector('input[name="delivery_landmark"]').value = '';
    }
}

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
