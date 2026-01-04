<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Employee</title>
    @if (Session('adminuser'))
        @include('layouts/adminsidebar')
    @elseif(Session('softwareuser'))
        @include('layouts/usersidebar')
    @endif

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
        <div style="margin-top: 15px;margin-left:15px;">
            @if ($adminroles->contains('module_id', '30'))
            @include('navbar.employeenav')
            </div>
        @else
            <x-logout_nav_user />
        @endif
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if (Session('softwareuser'))
<x-admindetails_user :shopdatas="$shopdatas" />
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Employee</a></li>
        <li class="breadcrumb-item active" aria-current="page">Create Employee</li>
    </ol>
</nav>
@endif

<div class="form-container" style="margin-top: -85px;">
    <div id="employee-details" class="bank-details">
<form action="{{ route('employee.store') }}" method="POST">
    @csrf

    <h2 class="text-center" >Create Employee</h2>
            <div class="form-section" >

        <div class="form-group">
                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">First Name<span style="color: red;">*</span></span>
                        <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Enter the first name">
                </div>

                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Last Name</span>
                    <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Enter the last name">
                </div>

                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Email<span style="color: red;">*</span></span>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter the email">
                </div>

                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Phone<span style="color: red;">*</span></span>
                    <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter the phone No.">
                </div>
                @if(Session('adminuser'))
                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Branch<span style="color: red;">*</span></span>
                    <select id="branch" name="branch" class="control" >
                        <option selected value="">Select Branch</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->location }}</option>
                                @endforeach
                            </select>

                    </div>
                    @elseif(Session('softwareuser'))
                    <input type="hidden" name="branch" value="{{$branch}}">
                    @endif

                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Employee ID</span>
                        <input type="text" id="Employeeid" name="Employeeid" class="form-control" placeholder="Enter the employee ID">
                    </div>

                    <!--<div class="input-group" style="margin-bottom: 15px;">-->
                    <!--    <span class="input-group-addon">Salary<span style="color: red;">*</span></span>-->
                    <!--    <input type="text" id="salary" name="salary" step="0.01" class="form-control" placeholder="Enter the salary">-->
                    <!--</div>-->

                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Date of Joining<span style="color: red;">*</span></span>
                        <input type="date" id="date_of_joining" name="date_of_joining" class="form-control" placeholder="Enter the date of joining">
                    </div>

                    <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">Department<span style="color: red;">*</span></span>
                        <select id="department" name="department" class="control">
                            <option selected value="">Select Department</option>
                            <option class="highlight-option" value="add_department">Add New Department</option> <!-- New department option -->
                            @foreach($departments as $department)
                            <option value="{{ $department->name }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Hidden input field for new department -->
                    <div class="input-group" id="new-department-group" style="margin-bottom: 15px; display: none;">
                        <span class="input-group-addon">New Department Name</span>
                        <input type="text" id="newDepartmentName" name="newDepartmentName" class="form-control" placeholder="Enter New Department Name">
                        <button type="button" id="saveDepartment" class="btn btn-primary">Save</button>
                    </div>

                    <div class="text-center">
                        <input class="btn btn-primary" type="submit" value="submit" id="submitBtn">

                    </div>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Bootstrap form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

    </script>

<script>
    // Form validation function
    function validateForm() {
        let firstName = document.getElementById('first_name').value.trim();
        let email = document.getElementById('email').value.trim();
        let phone = document.getElementById('phone').value.trim();
        let branch = document.getElementById('branch').value;
        let salary = document.getElementById('salary').value.trim();
        let dateOfJoining = document.getElementById('date_of_joining').value.trim();
        let departmentSelect = document.getElementById('department').value;
        let newDepartmentName = document.getElementById('newDepartmentName') ? document.getElementById('newDepartmentName').value.trim() : '';

        // Check if required fields are empty
        if (!firstName) {
            alert("First Name is required.");
            return false;
        }
        if (!email) {
            alert("Email is required.");
            return false;
        }
        if (!phone) {
            alert("Phone is required.");
            return false;
        }
        if (!branch) {
            alert("Branch is required.");
            return false;
        }
        if (!salary) {
            alert("Salary is required.");
            return false;
        }
        if (!dateOfJoining) {
            alert("Date of Joining is required.");
            return false;
        }
        if (departmentSelect === "") {
        alert("Select the department.");
        return false;
    }

    // Check if "Add New Department" is selected and if new department name is provided
    if (departmentSelect === "add_department" && !newDepartmentName) {
        alert("Please provide a name for the new department.");
        return false;
    }

        return true;
    }

    // Attach the validateForm function to the form's onsubmit event
    document.querySelector('form').addEventListener('submit', function (e) {
        if (!validateForm()) {
            e.preventDefault(); // Prevent form submission if validation fails
        } else {
            // Disable the submit button to prevent multiple submissions
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.value = 'Submitting...'; // Change button text to indicate processing
            submitBtn.disabled = true; // Disable the button
        }
            });
</script>
<script>
    $(document).ready(function () {
    // Show the 'Add New Department' input when 'Add New Department' is selected
    $('#department').change(function () {
        if ($(this).val() === 'add_department') {
            $('#new-department-group').show();
        } else {
            $('#new-department-group').hide();
        }
    });

    // Save the new department via AJAX
    $('#saveDepartment').click(function () {
        let newDepartmentName = $('#newDepartmentName').val().trim();

        if (newDepartmentName === '') {
            alert('Please enter a department name.');
            return;
        }

        $.ajax({
            url: '{{ route('departments.store') }}', // Your route to store the department
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                name: newDepartmentName
            },
            success: function (response) {
                if (response.success) {
                    // Add the new department to the dropdown
                    $('#department').append(`<option value="${response.department.id}" selected>${response.department.department}</option>`);

                    // Hide the new department input
                    $('#new-department-group').hide();
                    $('#newDepartmentName').val(''); // Clear input field
                } else {
                    alert('Failed to add department. Try again.');
                }
            },
            error: function () {
                alert('An error occurred while adding the department.');
            }
        });
    });
});

</script>

</body>

</html>
