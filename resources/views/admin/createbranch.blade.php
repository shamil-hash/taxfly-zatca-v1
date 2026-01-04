<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin</title>
    @include('layouts/adminsidebar')
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

    /* ZATCA section styling */
    .zatca-section {
        display: none;
        margin-top: 20px;
        border-top: 2px solid #187f6a;
        padding-top: 20px;
    }

    .zatca-toggle {
        margin-bottom: 20px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
        border: 1px solid #dee2e6;
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
    .highlight-option {
        background-color: #e0f7fa;
        color: black;
        font-weight: bold;
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

<body>
    <!-- Page Content Holder -->
    <div id="content">
        <x-logout_nav_user />

       @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
       
        <div class="form-container" style="margin-top: -85px;">
            <div id="employee-details" class="bank-details">
        <form action="branchcreate" method="POST" id="createbranch" name="createbranch"
            onsubmit="return validateSearch();" enctype="multipart/form-data">
            @csrf
            <h2 ALIGN="CENTER">Create Branch </h2>
            <div class="form-section" >
                <div class="form-group">
                    <!-- ZATCA Toggle -->
                    <div class="zatca-toggle">
                        <div class="input-group">
                            <span class="input-group-addon">Enable ZATCA</span>
                            <select class="form-control" name="enable_zatca" id="enable_zatca" style="height:auto;" onchange="toggleZatcaFields()">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Company Name<span style="color: red;">*</span></span>
                        <input type="text" class="form-control" name="company" id="company" placeholder="Enter the company name">
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Location<span style="color: red;">*</span></span>
                        <input type="text" class="form-control location" name="location" id="location" placeholder="Enter the location"
                            value="{{ old('location') }}">
                        <span style="color:red">
                            @error('location')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Branch Name<span style="color: red;">*</span></span>
                        <input type="text" class="form-control branchname" name="branchname"  id="branchname"
                            placeholder="Enter the branch name" value="{{ old('branchname') }}">
                        <span style="color:red">
                            @error('branchname')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Mobile No.<span style="color: red;">*</span></span>
                    <input type="text" class="form-control mobile" name="mobile"  id="mobile" placeholder="Enter the mobile"
                            value="{{ old('mobile') }}">
                        <span style="color:red">
                            @error('mobile')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Address<span style="color: red;">*</span></span>
                        <input type="text" class="form-control" name="address" id="address" placeholder="Enter the address">
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Email</span>
                        <input type="text" class="form-control" name="email"  id="email" placeholder="Enter the email">
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">PO Box</span>
                        <input type="text" class="form-control" name="po_box"   id="po_box" placeholder="Enter the Po box">
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">TRN</span>
                        <input type="text" class="form-control" name="tr_no" id="tr_no" placeholder="Enter the TRN">
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Currency<span style="color: red;">*</span></span>
                        <input type="text" class="form-control" name="currency" id="currency" placeholder="Enter the Currency" required>
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Transaction ID<span style="color: red;">*</span></span>
                        <input type="text" class="form-control" name="transaction_id" id="transaction_id"
                            placeholder="Enter the Transaction id" value="{{ old('transaction_id') }}" required>
                        @error('transaction_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Logo</span>
                        <input type="file" class="form-control" name="logo" id="logo" accept="image/*">
                    </div>

                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">License PDF(Optional)</span>
                        <input type="file" class="form-control file" name="file" >
                    </div>
                    
                    <!-- ZATCA Fields Section -->
                    <div id="zatca_fields" class="zatca-section">
                        <h3 style="color: #187f6a; margin-bottom: 20px;">ZATCA E-Invoicing Details</h3>
                        
                        <!-- Company Information -->
                        <h4>Company Information</h4>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Commercial Registration (CR) Number<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="cr_number" id="cr_number" placeholder="Enter CR Number">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">VAT / TRN Number<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="vat_trn_number" id="vat_trn_number" placeholder="Enter VAT/TRN Number">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Business Type<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="business_type" id="business_type" placeholder="Enter Business Type">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Number of Branches<span style="color: red;">*</span></span>
                            <input type="number" class="form-control" name="branch_count" id="branch_count" placeholder="Enter Number of Branches">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">City<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="city" id="city" placeholder="Enter City">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Postal Code<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="postal_code" id="postal_code" placeholder="Enter Postal Code">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Country (ISO Code)<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="country_code" id="country_code" placeholder="Enter Country Code (e.g., SA)" value="SA">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Official Email<span style="color: red;">*</span></span>
                            <input type="email" class="form-control" name="official_email" id="official_email" placeholder="Enter Official Email">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Official Phone<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="official_phone" id="official_phone" placeholder="Enter Official Phone">
                        </div>
                        
                        <!-- Authorized Person Details -->
                        <h4>Authorized Person Details</h4>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Full Name<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="auth_person_name" id="auth_person_name" placeholder="Enter Full Name">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Designation / Role<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="auth_person_role" id="auth_person_role" placeholder="Enter Designation/Role">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">National ID / Iqama Number<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="auth_person_id" id="auth_person_id" placeholder="Enter National ID/Iqama">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Mobile Number<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="auth_person_mobile" id="auth_person_mobile" placeholder="Enter Mobile Number">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Email Address<span style="color: red;">*</span></span>
                            <input type="email" class="form-control" name="auth_person_email" id="auth_person_email" placeholder="Enter Email Address">
                        </div>
                        
                        <!-- ZATCA E-Invoicing Setup -->
                        <h4>ZATCA E-Invoicing Setup</h4>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">ZATCA Environment Type<span style="color: red;">*</span></span>
                            <select class="form-control" name="zatca_environment" id="zatca_environment" style="height:auto;">
                                <option value="sandbox">Sandbox (Testing)</option>
                                <option value="production">Production (Live)</option>
                            </select>
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">ZATCA OTP (Onboarding Code)<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="zatca_otp" id="zatca_otp" placeholder="Enter ZATCA OTP">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">CSR File (.csr/.pem)<span style="color: red;">*</span></span>
                            <input type="file" class="form-control" name="csr_file" id="csr_file" accept=".csr,.pem">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Private Key File (.key/.pem)<span style="color: red;">*</span></span>
                            <input type="file" class="form-control" name="private_key_file" id="private_key_file" accept=".key,.pem">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">ZATCA Certificate (.crt/.pem)<span style="color: red;">*</span></span>
                            <input type="file" class="form-control" name="zatca_certificate" id="zatca_certificate" accept=".crt,.pem">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Device / Solution UUID<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="device_uuid" id="device_uuid" placeholder="Enter Device UUID">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">CSR Common Name (CN)<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="csr_common_name" id="csr_common_name" placeholder="Enter CSR Common Name">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <span class="input-group-addon">Confirm QR/TLV Required<span style="color: red;">*</span></span>
                            <select class="form-control" name="qr_tlv_required" id="qr_tlv_required" style="height:auto;">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary branchsubmit" name="submit"
                        id="submitBranch">SUBMIT</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    </div>
</body>

</html>

<script type="text/javascript">
    // Function to toggle ZATCA fields visibility
    function toggleZatcaFields() {
        const zatcaToggle = document.getElementById('enable_zatca');
        const zatcaSection = document.getElementById('zatca_fields');
        
        if (zatcaToggle.value === '1') {
            zatcaSection.style.display = 'block';
        } else {
            zatcaSection.style.display = 'none';
        }
    }
    
    var currentBoxNumber = 0;

    $(".location").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.branchname");
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
    $(".branchname").keydown(function(event) {
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
            textboxes = $("input.file");
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
    $(".file").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("button.branchsubmit");
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
    function validateSearch() {
        // List of required field IDs
        var requiredFields = [
            'company',
            'location',
            'branchname',
            'mobile',
            'address',
        ];

        // Check if ZATCA is enabled
        const zatcaEnabled = document.getElementById('enable_zatca').value === '1';
        
        // If ZATCA is enabled, add ZATCA required fields
        if (zatcaEnabled) {
            const zatcaRequiredFields = [
                'cr_number',
                'vat_trn_number',
                'business_type',
                'branch_count',
                'city',
                'postal_code',
                'country_code',
                'official_email',
                'official_phone',
                'auth_person_name',
                'auth_person_role',
                'auth_person_id',
                'auth_person_mobile',
                'auth_person_email',
                'zatca_environment',
                'zatca_otp',
                'csr_file',
                'private_key_file',
                'zatca_certificate',
                'device_uuid',
                'csr_common_name',
                'qr_tlv_required'
            ];
            
            requiredFields = requiredFields.concat(zatcaRequiredFields);
        }

        // Iterate through each field and check if it's filled
        for (var i = 0; i < requiredFields.length; i++) {
            var field = document.getElementsByName(requiredFields[i])[0]; // Get field by name
            if (field && field.value.trim() === "") {
                alert("Please fill all required fields.");
                field.focus(); // Focus on the first empty field
                return false; // Stop form submission
            }
        }

        return true; // Allow form submission if all fields are filled
    }

    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("createbranch");
        const submitBtn = document.getElementById("submitBranch");

        // Remove existing onsubmit from the form tag
        form.onsubmit = null;

        // Handle form submission with validation
        form.addEventListener("submit", function(e) {
            if (!validateSearch()) {
                e.preventDefault(); // Prevent form submission if validation fails
                return false;
            }

            // Disable submit button after validation passes
            submitBtn.disabled = true;
            submitBtn.innerText = "Submitting...";
        });

        // Prevent form submission on pressing Enter key
        form.addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                e.preventDefault(); // Prevent default form submit on Enter key
            }
        });
    });
</script>