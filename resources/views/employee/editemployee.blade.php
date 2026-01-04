<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Edit Employee</title>
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


        <!-- Display Success Message -->
        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif
        @if (Session('softwareuser'))
        <x-admindetails_user :shopdatas="$shopdatas" />
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Employee</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Employee</li>
            </ol>
        </nav>
        @endif
        <div class="form-container" style="margin-top: -85px;">
            <!-- Employee Edit Form -->
        <div id="employee-details" class="bank-details" >

            <form action="{{ route('employee.update', $employee->id) }}" method="POST">
                @csrf
                @method('PUT')
                <h2 class="text-center" >Edit Employee</h2>

            <div class="form-section" >
                    <div class="form-group">
                        <div class="input-group" style="margin-bottom: 15px;">
                        <span class="input-group-addon">First Name</span>
                    <input type="text" name="first_name" id="first_name" class="form-control" value="{{ $employee->first_name }}" readonly>
                </div>

                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Last Name</span>
                    <input type="text" name="last_name" id="last_name" class="form-control" value="{{ $employee->last_name }}">
            </div>

                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Email</span>
                    <input type="email" name="email" id="email" class="form-control" value="{{ $employee->email }}">
                </div>

                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Phone</span>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ $employee->phone }}">
                </div>

                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Branch</span>
                    <input type="text" name="branch" id="branch" class="form-control" value="{{ optional($locations->firstWhere('id', $employee->branch))->location ?? 'N/A' }}" readonly>
                </div>

                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Department</span>
                    <input type="text" name="department" id="department" class="form-control" value="{{ $employee->department ?? 'N/A' }}" readonly>
            </div>

                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Employee ID</span>
                    <input type="text" name="Employeeid" id="Employeeid" class="form-control" value="{{ $employee->employee_id }}">
                </div>

            <!--    <div class="input-group" style="margin-bottom: 15px;">-->
            <!--        <span class="input-group-addon">Salary</span>-->
            <!--        <input type="number" name="salary" id="salary" class="form-control" step="0.01" value="{{ $employee->salary }}">-->
            <!--</div>-->

                <div class="input-group" style="margin-bottom: 15px;">
                    <span class="input-group-addon">Date of Joining</span>
                    <input type="date" name="date_of_joining" id="date_of_joining" class="form-control" value="{{ \Carbon\Carbon::parse($employee->date_of_joining)->format('Y-m-d') }}">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
    </div>
    </div>
</body>
</html>
