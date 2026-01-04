<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Sidebar</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Font Awesome CSS CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            display: none;
        }

        #sidebar ul li.active ul {
            display: block;
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

        #sidebar ul li.active ul {
            max-height: calc(100vh - 50px);
            overflow-y: auto;
        }

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

        @media (max-width: 768px) {
            #sidebar {
                margin-left: -230px;
                transition: all 0.3s ease;
                overflow-y: auto;
                pointer-events: none;
            }

            #sidebar.active {
                margin-left: 0;
                pointer-events: auto;
            }

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

        .help-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #187f6a;
            color: white;
            border: none;
            font-size: 18px;
            cursor: pointer;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .help-button:hover {
            background: #136a58;
        }

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

        .help-content {
            background: white;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 500px;
            position: relative;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

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

        .help-section {
            margin: 15px 0;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 4px;
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
            border-radius: 4px;
            transition: background 0.3s;
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
    </style>
</head>
<?php
$userid = auth()->id(); // Better way to get user ID
$branch = auth()->user()->branch_id ?? null; // Get branch ID properly
$today = date('Y-m-d');
$company = DB::table('branches')
    ->where('id', $branch)
    ->value('company'); // Simpler than pluck()->first()      
?>
<body>
    <div class="wrapper">
        <!-- Sidebar Toggle Button -->
        <button type="button" id="sidebarCollapse" class="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <a href="/userdashboard" class="dashboard-link">
                    <h3 class="dashboard-title" style="padding-top: 2px;">Dashboard</h3>
                </a>
            </div>

            <ul class="list-unstyled components">
                <!-- BILLING DESK -->
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($user->role_id == '1'): ?>
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-print"></i>
                        Billing Desk
                    </a>
                    <ul class="list-unstyled">
                        <?php if(isset($branch) && $branch != 3): ?>
                            <li><a href="/dashboard"><i class="fas fa-file-invoice"></i> Billing</a></li>
                        <?php else: ?>
                            <li><a href="/servicebilling"><i class="fas fa-tools"></i> Service Billing</a></li>
                        <?php endif; ?>
                        <?php if(isset($branch) && $branch == 2): ?>
                            <li><a href="/servicebilling"><i class="fas fa-tools"></i> Service Billing</a></li>
                        <?php endif; ?>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($user->role_id == '32'): ?>
                        <li><a href="/posbilling"><i class="fas fa-cash-register"></i> POS Billing</a></li>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <li><a href="/return"><i class="fas fa-exchange-alt"></i> Return</a></li>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->role_id == '20'): ?>
                                <li><a href="/quotation"><i class="fas fa-file-signature"></i> Quotation</a></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->role_id == '17'): ?>
                                <li><a href="/sales_order"><i class="fas fa-file-pen"></i> Sales Order</a></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->role_id == '18'): ?>
                                <li><a href="/delivery_note"><i class="fas fa-truck-moving"></i> Delivery Note</a></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->role_id == '21'): ?>
                                <li><a href="/performance_invoice"><i class="fas fa-file-contract"></i> Proforma Invoice</a></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <li><a href="/billingsidetransactions?start_date=<?php echo $today; ?>&end_date=<?php echo $today; ?>&credit_user_id="><i class="fas fa-history"></i> Transaction History</a></li>
                        <li><a href="/addfunds"><i class="fas fa-book"></i> Ledger Fund</a></li>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->role_id == '28'): ?>
                                <li><a href="/creditnote"><i class="fas fa-file-export"></i> Credit Note</a></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </li>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- INVENTORY -->
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($user->role_id == '2'): ?>
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-cubes"></i>
                            Inventory
                        </a>
                        <ul class="list-unstyled">
                            <li><a href="/inventorydashboard"><i class="fas fa-cube"></i> Add Product</a></li>
                            <li><a href="/listcategory"><i class="fas fa-tags"></i> Category & Unit</a></li>
                            <li><a href="/liststocks"><i class="fas fa-boxes"></i> Stock List</a></li>
                            <li><a href="/purchasestock"><i class="fas fa-shopping-cart"></i> Purchase</a></li>
                            <li><a href="/purchasereturn"><i class="fas fa-undo"></i> Purchase Return</a></li>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($user->role_id == '19'): ?>
                                    <li><a href="/purchase_order"><i class="fas fa-file-invoice-dollar"></i> Purchase Order</a></li>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <li><a href="/purchasehistory"><i class="fas fa-receipt"></i> Purchase History</a></li>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($user->role_id == '29'): ?>
                                    <li><a href="/debitnote"><i class="fas fa-file-import"></i> Debit Note</a></li>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- ACCOUNTANT -->
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($user->role_id == '9'): ?>
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-calculator"></i>
                            Accountant
                        </a>
                        <ul class="list-unstyled">
                            <li><a href="/income"><i class="fas fa-coins"></i> Income & Expense</a></li>
                            <li><a href="/accountreport"><i class="fas fa-chart-pie"></i> Accounts Report</a></li>
                            <li><a href="/finalreport"><i class="fas fa-chart-line"></i> Final Report</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- FINANCE -->
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($user->role_id == '30'): ?>
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-wallet"></i>
                            Finance
                        </a>
                        <ul class="list-unstyled">
                            <li><a href="/chartaccounts"><i class="fas fa-sitemap"></i> Chart of Accounts</a></li>
                            <li><a href="/trailbalance"><i class="fas fa-balance-scale"></i> Trial Balance</a></li>
                            <li><a href="/balancesheet"><i class="fas fa-table"></i> Balance Sheet</a></li>
                            <li><a href="/journalentry"><i class="fas fa-pen-nib"></i> Journal Entry</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- BANK -->
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($user->role_id == '24'): ?>
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-building-columns"></i>
                            Bank
                        </a>
                        <ul class="list-unstyled">
                            <li><a href="/bank"><i class="fas fa-plus-circle"></i> Add Bank</a></li>
                            <li><a href="/listbank"><i class="fas fa-list-ol"></i> List Bank</a></li>
                            <li><a href="/fundtransfer"><i class="fas fa-random"></i> Amount transfer</a></li>
                            <li><a href="/bankreport"><i class="fas fa-file-alt"></i> Bank report</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- CUSTOMER -->
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($user->role_id == '26'): ?>
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-user"></i>
                            Customer
                        </a>
                        <ul class="list-unstyled">
                            <li><a href="/createcredit"><i class="fas fa-user-plus"></i> Create Customer</a></li>
                            <li><a href="/listcredituser"><i class="fas fa-address-book"></i> List Customer</a></li>
                            <li><a href="/customerstatus"><i class="fas fa-chart-bar"></i> Customer Summary</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- SUPPLIER -->
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($user->role_id == '22'): ?>
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-dolly"></i>
                            Supplier
                        </a>
                        <ul class="list-unstyled">
                            <li><a href="/createsupplier"><i class="fa fa-dolly-flatbed"></i> Create Supplier</a></li>
                            <li><a href="/listsupplier"><i class="fas fa-clipboard-list"></i> List Supplier</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- EMPLOYEE -->
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($user->role_id == '25'): ?>
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-id-badge"></i>
                            Employee
                        </a>
                        <ul class="list-unstyled">
                            <li><a href="/employee"><i class="fas fa-user-edit"></i> Create Employee</a></li>
                            <li><a href="/listemployee"><i class="fas fa-users-cog"></i> List Employee</a></li>
                            <li><a href="/employeesalary"><i class="fas fa-money-check-alt"></i> Salary</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- REPORT -->
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($user->role_id == '27'): ?>
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-chart-simple"></i>
                            Report
                        </a>
                        <ul class="list-unstyled">
                            <li><a href="/user-report"><i class="fas fa-user-gear"></i> User Report</a></li>
                            <li><a href="/daybook"><i class="fas fa-book"></i> Day Book</a></li>
                            <li><a href="/stock"><i class="fas fa-box-open"></i> Stock Report</a></li>
                            <li><a href="/product-profit"><i class="fas fa-chart-bar"></i> Product Report</a></li>
                            <li><a href="/p_and_l_report"><i class="fas fa-chart-column"></i> P & L Report</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- ALL HISTORIES -->
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-history"></i>
                        All Histories
                    </a>
                    <ul class="list-unstyled" style="max-height: 250px; overflow-y: auto; min-height: 200px;">
                        <li><a href="/billingsidetransactions?start_date=<?php echo $today; ?>&end_date=<?php echo $today; ?>&credit_user_id="><i class="fas fa-clock"></i> Transaction History</a></li>
                        <li><a href="/returnhistory"><i class="fas fa-undo-alt"></i> Return History</a></li>
                        <li><a href="/purchasehistory"><i class="fas fa-shopping-basket"></i> Purchase History</a></li>
                        <li><a href="/purchasereturnhistory"><i class="fas fa-rotate-left"></i> Purchase Return History</a></li>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->role_id == '20'): ?>
                                <li><a href="/history/quotation"><i class="fas fa-file-alt"></i> Quotation History</a></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->role_id == '21'): ?>
                                <li><a href="/history/performance_invoice"><i class="fas fa-file-invoice"></i> Proforma History</a></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->role_id == '17'): ?>
                                <li><a href="/history/sales_order"><i class="fas fa-file-invoice-dollar"></i> Sales Order History</a></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->role_id == '18'): ?>
                                <li><a href="/history/deliverynote"><i class="fas fa-truck-loading"></i> Delivery Note History</a></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->role_id == '19'): ?>
                                <li><a href="/purchase_order_history/purchase_order"><i class="fas fa-file-circle-check"></i> Purchase Order History</a></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <li><a href="/searchmonthwiseincome"><i class="fas fa-money-bill-trend-up"></i> Income History</a></li>
                        <li><a href="/monthwiseexpensehistory"><i class="fas fa-money-bill-transfer"></i> Expense History</a></li>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->role_id == '30'): ?>
                                <li><a href="/assethistory"><i class="fas fa-building"></i> Asset History</a></li>
                                <li><a href="/capitalhistory"><i class="fas fa-coins"></i> Capital History</a></li>
                                <li><a href="/liabilityhistory"><i class="fas fa-hand-holding-usd"></i> Liability History</a></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </li>

                <!-- SERVICE -->
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($user->role_id == '31'): ?>
                    <li>
                        <a href="/service">
                            <i class="fas fa-headset"></i>
                            Service
                        </a>
                    </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- CALENDAR -->
                <li>
                    <a href="/calender">
                        <i class="fas fa-calendar-days"></i>
                        Calendar
                    </a>
                </li>

                <!-- SETTINGS -->
                <li>
                    <a href="/setting">
                        <i class="fas fa-cogs"></i>
                        Settings
                    </a>
                </li>

                <!-- LOGOUT -->
                <li>
                    <a href="#" id="logout-link"
                        data-session="<?php echo e(Session::has('adminuser') ? 'admin' : (Session::has('softwareuser') ? 'software' : 'superuser')); ?>">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
                <li style="position: fixed; bottom: 0; width: 230px; background: #f8f8f8; padding: 10px; text-align: center; border-top: 1px solid #ddd;border-right: 1px solid #ddd;">
                    <span style="color: #187f6a; font-weight: bold;font-size:10px;"><?php echo e($company); ?></span>
                </li>
            </ul>
        </nav>

        <!-- HELP BUTTON -->
        <button id="helpButton" class="help-button">
            <i class="fas fa-question-circle"></i>
        </button>

        <!-- HELP MODAL -->
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

        <?php echo $__env->make('modal.logout_modal', [
            'adminUser' => Session::has('adminuser'),
            'softwareUser' => Session::has('softwareuser'),
            'superUser' => Session::has('superuser'),
            'creditUser' => Session::has('credituser'),
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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

        // Enhanced Modal Control
        document.addEventListener('DOMContentLoaded', function() {
            const helpButton = document.getElementById('helpButton');
            const helpModal = document.getElementById('helpModal');
            const closeHelp = document.querySelector('.close-help');

            // Open modal
            helpButton.addEventListener('click', function(e) {
                e.stopPropagation();
                helpModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
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
</body>
</html><?php /**PATH C:\xampp\htdocs\netplex_26_7\resources\views/layouts/usersidebar.blade.php ENDPATH**/ ?>