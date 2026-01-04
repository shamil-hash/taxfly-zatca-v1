<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Sidebar</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Js CDN -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.js"></script>

    <style>
        body {
            font-size: 12px;
            background: white;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        #sidebar {
            min-width: 230px;
            max-width: 230px;
            background: #fff;
            color: #333;
            transition: margin-left 0.2s;
            border-right: 1px solid #ddd;
            height: 100vh;
            overflow-y: auto;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
        }

        #sidebar.active {
            margin-left: -230px;
        }

        #sidebar::-webkit-scrollbar {
            width: 4px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: #f5f5f5;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: #ccc;
        }

        .sidebar-header {
            padding: 20px;
            background: #187f6a;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        .sidebar-header h3 {
            color: white;
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
        }

        .sidebar-header strong {
            color: rgba(255, 255, 255, 0.8);
            font-size: 12px;
            display: block;
            margin-top: 5px;
        }

        .sidebar-header a {
            color: white;
            text-decoration: none;
        }

        .sidebar-header a:hover {
            text-decoration: none;
            color: white;
        }

        #sidebar ul.components {
            padding: 0;
            margin: 0;
        }

        #sidebar ul li {
            list-style: none;
            border-bottom: 1px solid #f0f0f0;
            position: relative;
        }

        #sidebar ul li:hover {
            background: #f8f8f8;
        }

        #sidebar ul li a {
            padding: 12px 15px;
            font-size: 13px;
            display: block;
            color: #333;
            text-decoration: none;
            transition: color 0.2s;
            border-left: 2px solid transparent;
        }

        #sidebar ul li a:hover {
            color: #187f6a;
            background: #f8f8f8;
            text-decoration: none;
            border-left: 2px solid #187f6a;
        }

        #sidebar ul li a i {
            margin-right: 8px;
            font-size: 14px;
            width: 16px;
            text-align: center;
            color: #666;
        }

        #sidebar ul li a:hover i {
            color: #187f6a;
        }

        /* Right-side dropdown styles */
        #sidebar ul li a[data-toggle="collapse"]:after {
            content: '>';
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: #999;
        }

        #sidebar ul li:hover a[data-toggle="collapse"]:after {
            color: #187f6a;
        }

        /* Submenu styles - positioned in content area */
        #sidebar ul ul {
            position: fixed;
            top: 0;
            left: 230px;
            min-width: 250px;
            max-width: 350px;
            background: #fff;
            border: 1px solid #ddd;
            z-index: 1001;
            opacity: 0;
            visibility: hidden;
            transform: translateX(-10px);
            transition: all 0.2s;
            margin: 0;
            padding: 5px 0;
            display: none; /* Changed from opacity/visibility to display */

        }

        #sidebar ul li.active ul {
            display: block; /* Show submenu when parent is active */
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }

        #sidebar ul ul li {
            border-bottom: 1px solid #f0f0f0;
        }

        #sidebar ul ul li:last-child {
            border-bottom: none;
        }

        #sidebar ul ul li a {
            padding: 10px 15px;
            font-size: 12px;
            color: #555;
            border-left: none;
            margin: 1px 5px;
        }

        #sidebar ul ul li a:hover {
            color: #187f6a;
            background: #f8f8f8;
            border-left: 2px solid #187f6a;
        }

        #sidebar ul ul li a i {
            font-size: 12px;
            margin-right: 8px;
        }

        /* Prevent submenu from going off-screen */
        #sidebar ul li.active ul {
            max-height: calc(100vh - 50px);
            overflow-y: auto;
        }

        /* Content area adjustment */
        #content {
            padding: 20px;
            min-height: 100vh;
            transition: all 0.2s;
            width: calc(100% - 230px);
            margin-left: 230px;
        }

        #content.active {
            width: 100%;
            margin-left: 0;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
 #sidebar {
            margin-left: -230px;
            transition: all 0.3s ease;
            /* Add these new properties */
            overflow-y: auto;
            pointer-events: none;
        }

        #sidebar.active {
            margin-left: 0;
            pointer-events: auto;
        }

        /* Prevent body scrolling when sidebar is open */
        body.sidebar-open {
            overflow: hidden;
            position: fixed;
            width: 100%;
        }


            #content {
                width: 100%;
                margin-left: 0;
            }

            #sidebar ul ul {
                left: 0 !important;
                min-width: calc(100vw - 40px);
                max-width: calc(100vw - 40px);
                margin: 0 20px;
            }
        }

        /* Toggle button */
        .sidebar-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1002;
            background: #187f6a;
            color: white;
            border: none;
            padding: 8px 10px;
            cursor: pointer;
        }

        .sidebar-toggle:hover {
            background: #156b58;
        }

        /* Help Button */
        .help-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 45px;
              border-radius: 50%;
            height: 45px;
            background: #187f6a;
            color: white;
            border: none;
            font-size: 18px;
            cursor: pointer;
            z-index: 9999;
        }

        .help-button:hover {
            background: #136a58;
        }

        /* Modal Backdrop */
        .help-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        /* Modal Content */
        .help-content {
            background: white;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 500px;
            position: relative;
        }

        /* Close Button */
        .close-help {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 24px;
            font-weight: bold;
            color: #555;
            cursor: pointer;
        }

        .close-help:hover {
            color: #000;
        }

        /* Help Sections */
        .help-section {
            margin: 15px 0;
            padding: 10px;
            background: #f9f9f9;
        }

        .help-section h4 {
            color: #187f6a;
            margin-top: 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .help-content {
                width: 90%;
                margin: 15% auto;
                padding: 15px;
            }
        }

        .whatsapp-support {
            text-align: center;
            padding: 15px;
        }

        .whatsapp-icon {
            font-size: 40px;
            color: #25D366;
            margin-bottom: 10px;
        }

        .support-numbers {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin: 15px 0;
        }

        .whatsapp-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #25D366;
            color: white;
            padding: 10px;
            text-decoration: none;
            font-weight: bold;
        }

        .whatsapp-btn:hover {
            background: #128C7E;
            color: white;
            text-decoration: none;
        }

        .whatsapp-btn i {
            margin-right: 8px;
        }

        .secondary-btn {
            background: #075E54;
        }

        .secondary-btn:hover {
            background: #128C7E;
        }

        .support-info {
            color: #666;
            font-size: 12px;
            margin-top: 8px;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .whatsapp-btn {
                font-size: 14px;
                padding: 8px;
            }
        }

        /* Demo content */
        .demo-content {
            background: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
        }

    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar Toggle Button -->
        <button type="button" id="sidebarCollapse" class="sidebar-toggle">
            <i class="glyphicon glyphicon-menu-hamburger"></i>
        </button>

        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <a href="/userdashboard">
                    <h3>Admin</h3>
                </a>
            </div>
         
            <ul class="list-unstyled components">
                <!-- Billing Desk -->
                @foreach ($users as $user)
                @if ($user->module_id == '1')
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-shopping-cart"></i>
                        Billing Desk
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="/listdesk"><i class="glyphicon glyphicon-list"></i> List Desk</a></li>
                    </ul>
                </li>
                @endif
                @endforeach



                <!-- Inventory -->
                @foreach ($users as $user)
                <?php if ($user->module_id == '3') { ?>
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-th-large"></i>
                        Inventory
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="/listinventory"><i class="glyphicon glyphicon-list"></i> List Inventory</a></li>
                    </ul>
                </li>
                <?php } ?>
                @endforeach

                <!-- Branches -->
                @foreach ($users as $user)
                <?php if ($user->module_id == '8') { ?>
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-home"></i>
                        Branches
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="/createbranch"><i class="glyphicon glyphicon-plus"></i> Create Branch</a></li>
                        <li><a href="/listbranch"><i class="glyphicon glyphicon-list"></i> List Branch</a></li>
                    </ul>
                </li>
                <?php } ?>
                @endforeach

                <!-- Accountant -->
                @foreach ($users as $user)
                <?php if ($user->module_id == '9') { ?>
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-usd"></i>
                        Accountant
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="/listaccountant"><i class="glyphicon glyphicon-list"></i> List Accountant</a></li>
                        <li><a href="/expensereport"><i class="glyphicon glyphicon-file"></i> Expense Report</a></li>
                        <li><a href="/adminemployeesalaryreport"><i class="glyphicon glyphicon-piggy-bank"></i> Salary Report</a></li>
                        <li><a href="/listaccountantreports"><i class="glyphicon glyphicon-book"></i> Final Reports</a></li>
                    </ul>
                </li>
                <?php } ?>
                @endforeach

                <!-- User Management -->
                @foreach ($users as $user)
                <?php if ($user->module_id == '12') { ?>
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-user"></i>
                        User
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="/createuser"><i class="glyphicon glyphicon-plus"></i> Create User</a></li>
                        <li><a href="/listuser"><i class="glyphicon glyphicon-list"></i> List User</a></li>
                    </ul>
                </li>
                <?php } ?>
                @endforeach

                <!-- Customer Management -->
                @foreach ($users as $user)
                <?php if ($user->module_id == '14') { ?>
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-phone"></i>
                        Customer
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="/createcredit"><i class="glyphicon glyphicon-plus"></i> Create Customer</a></li>
                        <li><a href="/listcredit"><i class="glyphicon glyphicon-stats"></i> Customer Summary</a></li>
                        <li><a href="/listcredituser"><i class="glyphicon glyphicon-list"></i> List Customer</a></li>
                    </ul>
                </li>
                <?php } ?>
                @endforeach

                <!-- Employee Management -->
                @foreach ($users as $user)
                <?php if ($user->module_id == '32') { ?>
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-briefcase"></i>
                        Employee
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="/employee"><i class="glyphicon glyphicon-plus"></i> Create Employee</a></li>
                        <li><a href="/listemployee"><i class="glyphicon glyphicon-list"></i> List Employee</a></li>
                    </ul>
                </li>
                <?php } ?>
                @endforeach

                <!-- Supplier Management -->
                @foreach ($users as $user)
                <?php if ($user->module_id == '16') { ?>
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-globe"></i>
                        Supplier
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="/createsupplier"><i class="glyphicon glyphicon-plus"></i> Create Supplier</a></li>
                        <li><a href="/listsupplier"><i class="glyphicon glyphicon-list"></i> List Supplier</a></li>
                    </ul>
                </li>
                <?php } ?>
                @endforeach

                <!-- Reports -->
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-file"></i>
                        Report
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="/stock"><i class="glyphicon glyphicon-th"></i> Stock Report</a></li>
                        <li><a href="/p_and_l_report"><i class="glyphicon glyphicon-stats"></i> P & L Report</a></li>
                        <li><a href="/branchwisesummary"><i class="glyphicon glyphicon-home"></i> Branchwise Summary</a></li>
                        <li><a href="/transactions"><i class="glyphicon glyphicon-transfer"></i> Transaction Report</a></li>
                        <li><a href="/purchasehistory"><i class="glyphicon glyphicon-shopping-cart"></i> Purchase Report</a></li>
                        <li><a href="/returnhistory"><i class="glyphicon glyphicon-retweet"></i> Return Report</a></li>
                    </ul>
                </li>

                <!-- Logout -->
                <li>
                    <a href="#" id="logout-link"
                        data-session="{{ Session::has('adminuser') ? 'admin' : (Session::has('softwareuser') ? 'software' : 'superuser') }}">
                        <i class="glyphicon glyphicon-log-out"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>
                           <button id="helpButton" class="help-button">
  <span class="glyphicon glyphicon-question-sign"></span>
</button>
                   <div id="helpModal" class="help-modal">
  <div class="help-content">
    <span class="close-help">&times;</span>
    <h3>WhatsApp Support</h3>
    <div class="help-section">
      <div class="whatsapp-support">
        <div class="whatsapp-icon">
          <i class="fab fa-whatsapp"></i>
        </div>
        <p>Contact our support team via WhatsApp:</p>

        <div class="support-numbers">
          <a href="https://wa.me/9400550569" class="whatsapp-btn" target="_blank">
            <i class="fab fa-whatsapp"></i> Primary Support
          </a>

          <a href="https://wa.me/9947609015" class="whatsapp-btn secondary-btn" target="_blank">
            <i class="fab fa-whatsapp"></i> Secondary Support
          </a>
        </div>

      </div>
    </div>
  </div>
</div>
@include('modal.logout_modal', [
            'adminUser' => Session::has('adminuser'),
            'softwareUser' => Session::has('softwareuser'),
            'superUser' => Session::has('superuser'),
            'creditUser' => Session::has('credituser'),
        ])

    </div>

    <script>
        $(document).ready(function() {
            // Toggle sidebar
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('active');
                $('#content').toggleClass('active');
                $('body').toggleClass('sidebar-open');
            });

            // Mobile responsiveness
            if ($(window).width() <= 768) {
                $('#sidebar').removeClass('active');
                $('#content').addClass('active');
            }

            $(window).resize(function() {
                if ($(window).width() <= 768) {
                    $('#sidebar').removeClass('active');
                    $('#content').addClass('active');
                } else {
                    $('#sidebar').removeClass('active');
                    $('#content').removeClass('active');
                    $('body').removeClass('sidebar-open');
                }
            });

            // Click handler for dropdown menus
            $('#sidebar ul li a.dropdown-toggle').on('click', function(e) {
                e.preventDefault();
                
                // Close all other open dropdowns
                $('#sidebar ul li').not($(this).parent()).removeClass('active');
                
                // Toggle current dropdown
                $(this).parent().toggleClass('active');
                
                // Position the dropdown
                if ($(this).parent().hasClass('active')) {
                    var $submenu = $(this).next('ul');
                    var liOffset = $(this).parent().offset();
                    var submenuHeight = $submenu.outerHeight();
                    var windowHeight = $(window).height();
                    
                    $submenu.css('top', liOffset.top + 'px');
                    
                    if (liOffset.top + submenuHeight > windowHeight) {
                        $submenu.css('top', (windowHeight - submenuHeight - 20) + 'px');
                    }
                    
                    if ($('#sidebar').hasClass('active')) {
                        $submenu.css('left', '0px');
                    } else {
                        $submenu.css('left', '230px');
                    }
                }
            });

            // Close dropdowns when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#sidebar ul li').length) {
                    $('#sidebar ul li').removeClass('active');
                }
            });
        });
    </script>
</body>
</html>
<script>
// Enhanced Modal Control
document.addEventListener('DOMContentLoaded', function() {
  const helpButton = document.getElementById('helpButton');
  const helpModal = document.getElementById('helpModal');
  const closeHelp = document.querySelector('.close-help');

  // Open modal
  helpButton.addEventListener('click', function(e) {
    e.stopPropagation();
    helpModal.style.display = 'block';
    document.body.style.overflow = 'hidden'; // Prevent scrolling
  });

  // Close modal
  closeHelp.addEventListener('click', function() {
    helpModal.style.display = 'none';
    document.body.style.overflow = 'auto';
  });

  // Close when clicking outside
  window.addEventListener('click', function(event) {
    if (event.target === helpModal) {
      helpModal.style.display = 'none';
      document.body.style.overflow = 'auto';
    }
  });

  // Close with Escape key
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && helpModal.style.display === 'block') {
      helpModal.style.display = 'none';
      document.body.style.overflow = 'auto';
    }
  });
});
</script>
<script>
   // Disable elements if license expired
if (document.querySelector('.alert-danger')) {
    // Add expired class to body
    document.body.classList.add('trial-expired');
    
    // Prevent all link clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('a') || e.target.closest('button')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);
    
    // Also prevent keyboard navigation
    document.addEventListener('keydown', function(e) {
        if ((e.key === 'Enter' || e.key === ' ') && 
            (e.target.closest('a') || e.target.closest('button'))) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);
}
        </script>