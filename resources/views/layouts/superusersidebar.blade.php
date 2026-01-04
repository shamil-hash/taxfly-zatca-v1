<head> <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="/css/bootstrap.css">
</head>
<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
<!-- Bootstrap Js CDN -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
        });
    });
</script>
<style>
    #content {
        padding: 20px;
        min-height: 100vh;
        transition: all 0.3s;
        width: 100%;
    }
</style>
<style>
    body {
        background: #ffffff;
        font-size: 12px;
    }
</style>

<body>
    <div class="wrapper">
        <!-- Sidebar Holder -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <a href="/superuserdashboard">
                    <h3>Super User</h3>
                    <strong>SU</strong>
                </a>
            </div>
            <ul class="list-unstyled components">
                <li>
                    <a href="#pageSub1" data-toggle="collapse" aria-expanded="false">
                        <i class="glyphicon glyphicon-user"></i>
                        Admin
                    </a>
                    </a>
                    <ul class="collapse list-unstyled" id="pageSub1">
                        <li><a href="/createadmin">Create Admin</a></li>
                        <li><a href="/listadmin">List Admin</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#pageSub2" data-toggle="collapse" aria-expanded="false">
                        <i class="glyphicon glyphicon-align-left"></i>
                        Analytics
                    </a>
                    </a>
                    <ul class="collapse list-unstyled" id="pageSub2">
                        <li><a href="/listsuperuseranalytics">List Analytics</a></li>
                        <li><a href="/listactivities">Activities</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#pageSub3" data-toggle="collapse" aria-expanded="false">
                        <i class="glyphicon glyphicon-print"></i>
                        Billing Desk
                    </a>
                    </a>
                    <ul class="collapse list-unstyled" id="pageSub3">
                        <li><a href="/listsuperuserbilldesks">List Desk</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#pageSub4" data-toggle="collapse" aria-expanded="false">
                        <i class="glyphicon glyphicon-download-alt"></i>
                        Inventory
                    </a>
                    </a>
                    <ul class="collapse list-unstyled" id="pageSub4">
                        <li><a href="/listsuperuserinventory">List Inventory</a></li>
                    </ul>
                </li>
                <!--<li>-->
                <!--    <a href="#pageSub5" data-toggle="collapse" aria-expanded="false">-->
                <!--    <i class="glyphicon glyphicon-phone-alt"></i>-->
                <!--        Customer Support-->
                <!--    </a>-->
                <!--    </a>-->
                <!--    <ul class="collapse list-unstyled" id="pageSub5">-->
                <!--        <li><a href="/listsuperusercustomersupport">List Customer Support</a></li>-->
                <!--    </ul>-->
                <!--</li>-->


                <li>
                    <a href="#pageSub9" data-toggle="collapse" aria-expanded="false">
                        <i class="glyphicon glyphicon-align-center"></i>
                        Accountant
                    </a>
                    </a>
                    <ul class="collapse list-unstyled" id="pageSub9">
                        <li><a href="/listsuperuseraccountants">List Accountants</a></li>
                    </ul>
                </li>

                <span>
                </span>
                <li>
                    <a href="/superuserchangepassword"> <i class="glyphicon glyphicon-cog"></i>Change Password</a>
                </li>
                <li>
                    {{-- <a href="/superuserlogout"> <i class="glyphicon glyphicon-log-out"></i>Logout</a> --}}

                    <a href="#" id="logout-link"
                        data-session="{{ Session::has('adminuser') ? 'admin' : (Session::has('softwareuser') ? 'software' : 'superuser') }}">
                        <i class="glyphicon glyphicon-log-out"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        @include('modal.logout_modal', [
            'adminUser' => Session::has('adminuser'),
            'softwareUser' => Session::has('softwareuser'),
            'superUser' => Session::has('superuser'),
            'creditUser' => Session::has('credituser'),
        ])
