<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Supplier</title>

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

.bank{
    margin-bottom: 15px;
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
      

        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif

        @if (Session::has('error'))
            <div class="alert alert-danger">
                {{ Session::get('error') }}
            </div>
        @endif
        @if (Session('softwareuser'))
        <x-admindetails_user :shopdatas="$shopdatas" />

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Supplier</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Supplier</li>
            </ol>
        </nav>
        @endif
        <h2 style="text-align: center;">Create Supplier</h2>
        <form action="suppliercreateform" method="POST" id="createsupplier" name="createsupplier"
            onsubmit="return(validateSearch());">
            @csrf

<div class="form-container" style="margin-top: -35px;">
    <!-- Supplier Details Section -->
    <div class="form-section">
        <h4 class="text-center" style="margin-bottom: 20px;">Supplier Details</h4>
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon">Name <span style="color: red;">*</span></span>
                <input type="text" class="form-control" name="name" placeholder="Enter the name" value="{{ old('name') }}" autocomplete="name">
            </div>
            <span style="color:red">
                @error('name')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon">Business Name</span>
                <input type="text" class="form-control" name="business_name" placeholder="Enter the business name" value="{{ old('business_name') }}">
            </div>
            <span style="color:red">
                @error('business_name')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon addon-fixed-width">Trade License No</span>
                <input type="text" class="form-control uniform-input" name="trade_license_no" placeholder="Enter the trade license No." value="{{ old('trade_license_no') }}">
            </div>
            <span style="color:red">
                @error('trade_license_no')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon addon-fixed-width">TRN Number<span style="color: red;">*</span></span>
                <input type="text" class="form-control trn uniform-input" name="trn_number" placeholder="Enter the TRN No." value="{{ old('trn_number') }}">
            </div>
            <span style="color:red">
                @error('trn_number')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon addon-fixed-width">Mobile Number<span style="color: red;">*</span></span>
                <input type="text" class="form-control mobile uniform-input" name="mobile" placeholder="Enter the Mobile No." value="{{ old('mobile') }}" autocomplete="tel">
            </div>
            <span style="color:red">
                @error('mobile')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon addon-fixed-width">Email</span>
                <input type="email" class="form-control email uniform-input" name="email" placeholder="Enter the email" value="{{ old('email') }}" autocomplete="email">
            </div>
            <span style="color:red">
                @error('email')
                    {{ $message }}
                @enderror
            </span>
            @if(Session('adminuser'))
            <div class="input-group">
                <span class="input-group-addon addon-fixed-width">Branch <span style="color: red;">*</span></span>
                <select name="location" class="control location uniform-input">
                    <option selected value="">Select branch</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->location }}</option>
                    @endforeach
                </select>
            </div>
            <span style="color:red">
                @error('location')
                    {{ $message }}
                @enderror
            </span>
            @elseif(Session('softwareuser'))
            <input type="hidden" name="location" value="{{$branch}}">
            @endif
            <!-- <div class="input-group">
                <span class="input-group-addon">Username <span style="color: red;">*</span></span>
                <input type="text" class="form-control" name="username" placeholder="Username" value="{{ old('username') }}" autocomplete="username">
            </div>
            <span style="color:red">
                @error('username')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon">Password <span style="color: red;">*</span></span>
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <span style="color:red">
                @error('password')
                    {{ $message }}
                @enderror
            </span> -->
        </div>
    </div>


    <!-- Address Section -->
    <div class="form-section">
        <h4 class="text-center" style="margin-bottom: 20px;">Address</h4>
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon">Billing Address</span>
                <input type="text" class="form-control address" name="billing_address" placeholder="Enter the street address" value="{{ old('billing_address') }}">
            </div>
            <span style="color:red">
                @error('billing_address')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon">City</span>
                <input type="text" class="form-control" name="billing_city" placeholder="Enter the city" value="{{ old('billing_city') }}">
            </div>
            <span style="color:red">
                @error('billing_city')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon">State/Province</span>
                <input type="text" class="form-control" name="billing_state" placeholder="Enter the state/province" value="{{ old('billing_state') }}">
            </div>
            <span style="color:red">
                @error('billing_state')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon">Postal/ZIP Code</span>
                <input type="text" class="form-control" name="billing_zip" placeholder="Enter the postal/ZIP code" value="{{ old('billing_zip') }}">
            </div>
            <span style="color:red">
                @error('billing_zip')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon">Landmark</span>
                <input type="text" class="form-control" name="billing_landmark" placeholder="Enter the landmark" value="{{ old('billing_landmark') }}">
            </div>
            <span style="color:red">
                @error('billing_landmark')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon">Country</span>
                <input type="text" class="form-control" name="billing_country" placeholder="Enter the country" value="{{ old('billing_country') }}">
            </div>
            <span style="color:red">
                @error('billing_country')
                    {{ $message }}
                @enderror
            </span>

            <!-- Same as Billing Address Checkbox -->
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="sameAddress" onclick="fillDeliveryAddress()">
                <label class="form-check-label" for="sameAddress">Same as Billing Address</label>
            </div>

            <div class="input-group">
                <span class="input-group-addon">Delivery Address</span>
                <input type="text" class="form-control" name="delivery_address" placeholder="Enter the street address" value="{{ old('delivery_address') }}">
            </div>
            <span style="color:red">
                @error('delivery_address')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon">City</span>
                <input type="text" class="form-control" name="delivery_city" placeholder="Enter the city" value="{{ old('delivery_city') }}">
            </div>
            <span style="color:red">
                @error('delivery_city')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon">State/Province</span>
                <input type="text" class="form-control" name="delivery_state" placeholder="Enter the state/province" value="{{ old('delivery_state') }}">
            </div>
            <span style="color:red">
                @error('delivery_state')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon">Postal/ZIP Code</span>
                <input type="text" class="form-control" name="delivery_zip" placeholder="Enter the postal/ZIP code" value="{{ old('delivery_zip') }}">
            </div>
            <span style="color:red">
                @error('delivery_zip')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon">Landmark</span>
                <input type="text" class="form-control" name="delivery_landmark" placeholder="Enter the landmark" value="{{ old('delivery_landmark') }}">
            </div>
            <span style="color:red">
                @error('delivery_landmark')
                    {{ $message }}
                @enderror
            </span>
            <div class="input-group">
                <span class="input-group-addon">Country</span>
                <input type="text" class="form-control" name="delivery_country" placeholder="Enter the country" value="{{ old('delivery_country') }}">
            </div>
            <span style="color:red">
                @error('delivery_country')
                    {{ $message }}
                @enderror
            </span>
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
            <div class="input-group bank">
                <span class="input-group-addon">Account Name</span>
                <input name="accountName" type="text" class="form-control" id="accountName" placeholder="Enter the account name" tabindex="1">
            </div>
            <div class="input-group bank">
                <span class="input-group-addon">Bank Name</span>
            <input name="bank_name" type="text" class="form-control" id="bank_name" placeholder="Enter the bank name">
            </div>
            <div class="input-group bank">
                <span class="input-group-addon">Branch</span>
                <input type="text" class="form-control" name="branch" placeholder="Enter the branch name">
            </div>

            <div class="input-group bank">
                <span class="input-group-addon">Code Type</span>
                <select name="codeType" class="control" id="codeType">
                    <option value="" selected>Select Code Type</option>
                    <option value="ifsc">IFSC</option>
                    <option value="iban">IBAN</option>
                </select>
            </div>
            <div class="input-group bank" id="ifscField" style="display:none;">
                <span class="input-group-addon">Enter IFSC Code</span>
                <input name="ifscCode" type="text" class="form-control" id="ifscCode" placeholder="Enter IFSC code">
            </div>
            <div class="input-group bank" id="ibanField" style="display:none;">
                <span class="input-group-addon">Enter IBAN Number</span>
                <input name="ibanCode" type="text" class="form-control" id="ibanCode" placeholder="Enter the IBAN code">
            </div>
        </div>

        <!-- Right Column -->
        <div class="input-group bank">
            <span class="input-group-addon">Account Number</span>
               <input name="account_number" type="text" class="form-control" id="account_number" placeholder="Enter the account No.">
            </div>

            <div class="input-group bank">
                <span class="input-group-addon">Date</span>
                <input name="date" type="date" class="form-control" id="date">
            </div>
            <div class="input-group bank">
                <span class="input-group-addon">Account Type</span>
                <select name="accountType" class="control" id="accountType">
                    <option value="" selected>Select Account Type</option>
                    <option value="savings">Savings</option>
                    <option value="current">Current</option>
                    <option value="fixed">Fixed Deposit</option>
                </select>
            </div>
            <!--<div class="input-group bank">-->
            <!--    <span class="input-group-addon">UPI ID</span>-->
            <!--    <input name="upiid" type="text" class="form-control" id="upiid" placeholder="Enter the UPI ID">-->
            <!--</div>-->
            <div class="input-group bank">
                <span class="input-group-addon">Country</span>
                <input name="country" type="text" class="form-control" id="country" placeholder="Enter the country" autocomplete="country-name">
                </div>
        </div>
    </div>



<div class="text-center mt-4">
    <button type="submit" class="btn btn-primary submitsupplier" id="submitBtnSupp">SUBMIT</button>
            </div>
        </form>

    </div>

</body>

</html>

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
            textboxes = $("input.mobile");
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
    $(".mobile").keydown(function(event) {
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
            textboxes = $("textarea.address");
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
    $(".address").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("select.location");
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
    $(".location").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("button.submitsupplier");
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

<script>
    $(function() {
        $('#createsupplier').keypress(function(e) { //use form id
            if (e.which == 13) {
                validateSearch(); //-- to validate form
                $('#createsupplier').submit(); // use form id
                return false;
            }
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("createsupplier");
        const submitBtn = document.getElementById("submitBtnSupp");

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
