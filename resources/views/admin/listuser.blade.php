<!DOCTYPE html>
<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin</title>
    @include('layouts/adminsidebar')
    <style>
        .gdot {
            height: 15px;
            width: 15px;
            background-color: #0adc0a;
            border-radius: 50%;
            display: inline-block;
            vertical-align: bottom;
        }
    </style>
    <style>
        .cdot {
            height: 15px;
            width: 15px;
            background-color: #cccccc;
            border-radius: 50%;
            display: inline-block;
            vertical-align: bottom;
        }
    </style>
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
            div.dataTables_wrapper div.dataTables_paginate ul.pagination li a {
            color: #187f6a !important;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.active a,
        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.active a:focus,
        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.active a:hover {
            background-color: #187f6a !important;
            border-color: #187f6a !important;
            color: white !important;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination li a:hover {
            background-color: #187f6a !important;
            border-color: #187f6a !important;
            color: white !important;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.disabled a {
            color: #6c757d !important;
        }
        .btn-primary{
            background-color: #187f6a;
            color: white;
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
                        <li><a href="/adminlogout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
       
        <h2>List User</h2>

        <table class="table" id="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Last Login</th>
                    <th>Last Logout</th>
                    <th>Location</th>
                    <th>IP Address</th>
                    <th>Assign Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($softusers as $softuser)
                    <tr>
                        <td>
                            {{ $softuser->name }}
                        </td>
                        <td>
                            {{ $softuser->username }}
                        </td>
                        <td>
                            <div align="center">
                                <?php if ($softuser->status == '1') { ?>
                                <span class="gdot"></span>
                                <?php } else { ?>
                                <span class="cdot"></span>
                                <?php } ?>
                            </div>
                        </td>
                        <td>{{ date('d M Y | h:i:s A', strtotime($softuser->created_at)) }}</td>

                        <td>

                            <?php echo $softuser->last_login != '' ? date('d M Y | h:i:s A', strtotime($softuser->last_login)) : "<span class='badge badge-default'>NA</span>"; ?>

                        </td>
                        <td>
                            <?php echo $softuser->last_logout != '' ? date('d M Y | h:i:s A', strtotime($softuser->last_logout)) : "<span class='badge badge-default'>NA</span>"; ?>
                        </td>
                        <td>
                            {{ $softuser->location }}
                        </td>
                        <td>{{ $softuser->login_ipaddress }}</td>
                        <td>
                            <a>
                                <button type="button" value="{{ $softuser->id }}"
                                    class="btn btn-primary editbtn btn-sm" title="Add Modules to User">Add
                                    Roles</button>
                            </a>
                            <a>
                                <button type="button" value="{{ $softuser->id }},{{ $softuser->access }}"
                                    class="btn btn-primary settingsbtn btn-sm" title="Settings">
                                    <i class="glyphicon glyphicon-cog"></i>
                                </button>
                            </a>
                        </td>
                @endforeach
                </tr>
            </tbody>
        </table>


    </div>

    @include('modal.addroles')
    @include('modal.edituser')
</body>

</html>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#table').DataTable({
            order: [
                [0, 'asc']
            ]
        });
    });
</script>

<script>
    $(document).ready(function() {
        $(document).on('click', '.editbtn', function() {
            var user = $(this).val();
            fetchRoles();

            function fetchRoles() {

                // Uncheck all checkboxes when opening the modal
                $('input[type="checkbox"]').prop('checked', false);

                $.ajax({
                    type: 'get',
                    url: '/getroles/' + user,
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
                        return 'inventory';
                    case 3:
                        return 'analytics';
                    case 4:
                        return 'customersupport';
                    case 5:
                        return 'manager';
                    case 6:
                        return 'marketing';
                    case 7:
                        return 'teamleader';
                    case 8:
                        return 'hr';
                    case 9:
                        return 'accountant';
                    case 10:
                        return 'billingtouch';
                    case 11:
                        return 'credit';
                    case 1:
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
                        return 'supplier';
                        // case 23:
                        //     return 'sunmi_print';
                        // case 24:
                        //     return 'pdf_prints';

                    case 26:
                        return 'credit_user';
                    case 24:
                        return 'bank';
                    case 25:
                        return 'employee';
                        case 27:
                        return 'reports';
                    case 28:
                        return 'creditnote';
                    case 29:
                        return 'debitnote';
                    case 30:
                        return 'chartofaccounts';
                    case 31:
                        return 'service';
                    case 32:
                        return 'posbilling';
                    default:
                        return ''; // Handle default case if needed
                }
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $(document).on('click', '.settingsbtn', function() {
            var data = $(this).val().split(',');
            console.log(data);
            var user = data[0];
            console.log(user);
            var access = data[1];
            $('#Edituser').modal('show');
            $('#user__id').val(user);
            var uid = $("#user__id").val();
            $("#disable").attr("href", '/changeuseraccess/' + uid);
            $("#enable").attr("href", '/changeuseraccess/' + uid);
            $("#edit").attr("href", '/edituser/' + uid);
            $("#userreport").attr("href", '/userreport/' + uid);
            var disablediv = document.getElementById("disablediv");
            if (access === "1") {
                disablediv.style.display = "block";
            } else {
                disablediv.style.display = "none";
            }
            var enablediv = document.getElementById("enablediv");
            if (access === "0") {
                enablediv.style.display = "block";
            } else {
                enablediv.style.display = "none";
            }
        });
    });
</script>
