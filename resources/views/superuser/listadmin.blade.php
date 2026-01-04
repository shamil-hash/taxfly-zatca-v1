<!DOCTYPE html>
<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SuperUser</title>
    @include('layouts/superusersidebar')
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2
        }

        th {
            background-color: #187f6a;
            color: white;
        }

        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 1px solid #010101;
        }

        .btnstyle {
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 20px;
        }
    </style>
</head>

<body>
    <!-- Page Content Holder -->
    <div id="content">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" id="sidebarCollapse" class="btn navbar-btn">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                        <span></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="/superuserlogout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <h2>List Admin</h2>
        <table class="table">
            <thead>
                <tr>
                    <th width="10%">Name</th>
                    <th width="10%">Username</th>
                    <th width="10%">Created At</th>
                    <th width="5%">Assign Role</th>
                    <th width="5%">Edit Admin</th>
                    <!--<th width="5%">Delete Admin</th>-->
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->created_at }}</td>
                        <td>
                            <a>
                                <button type="button" value="{{ $user->id }}"
                                    class="btn btn-primary editbtn btn-sm">Add Roles</button>
                            </a>
                        </td>
                        <td>
                            <a href="adminedit/{{ $user->id }}" class="btn btn-primary btn-sm"> EDIT</a>
                        </td>
                       
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('modal.addmodules')
    @include('modal.deleteadmin')

    </div>
</body>

</html>
<script>
    $(document).ready(function() {
        $(document).on('click', '.editbtn', function() {
            var user = $(this).val();
            fetchRoles();

            function fetchRoles() {
                $.ajax({
                    type: 'get',
                    url: '/getmodules/' + user,
                    success: function(data) {
                        var roles = data.roles;
                        console.log(roles);

                        // Iterate through the roles array and check corresponding checkboxes
                        roles.forEach(function(roleId) {
                            $('#' + getCheckboxId(roleId)).prop('checked', true);
                        });

                        $('#Roles').modal('show');
                        $('#user_id').val(user);
                    }
                });
            }

            // Function to get the corresponding checkbox ID based on role ID
            function getCheckboxId(roleId) {
                // Add your logic to map role ID to checkbox ID
                // For example:
                switch (roleId) {
                    case 1:
                        return 'bill';
                    case 2:
                        return 'analytics';
                    case 3:
                        return 'inventory';
                    case 4:
                        return 'customersupport';
                    case 5:
                        return 'teamleader';
                    case 6:
                        return 'manager';
                    case 7:
                        return 'marketing';
                    case 8:
                        return 'branches';
                    case 9:
                        return 'warehouse';
                    case 10:
                        return 'hr';
                    case 11:
                        return 'accountant';
                    case 12:
                        return 'user';
                    case 13:
                        return 'billingtouch';
                    case 14:
                        return 'credit';
                    case 16:
                        return 'supplier';
                    case 17:
                        return 'salesorder';
                    case 18:
                        return 'deliverynote';
                    case 19:
                        return 'purchaseorder';
                    case 20:
                        return 'quotation';
                    case 21:
                        return 'performance_invoice';
                    case 22:
                        return 'salesorder_to_invoice';
                    case 23:
                        return 'sunmi_print';
                    case 24:
                        return 'pdf_prints';
                    case 25:
                        return 'all_pdfsunmi_prints';
                    case 26:
                        return 'quotation_to_invoice';
                    case 27:
                        return 'purchaseorder_to_purchase';
                    case 28:
                        return 'Quotation_to_sales_order';
                    case 29:
                        return 'bill_without_header';
                    case 30:
                        return 'new_layout';
                    case 31:
                        return 'to_delivery';
                    case 32:
                        return 'employee';

                    default:
                        return ''; // Handle default case if needed
                }
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $(document).on('click', '.deletebtn', function() {
            var user = $(this).val();
            $('#Delete').modal('show');
            $("#disable").attr("href", '/deleteadmin/' + user);
        });
    });
</script>
