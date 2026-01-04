<!DOCTYPE html>
<html>
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js" integrity="sha512-Wt1bJGtlnMtGP0dqNFH1xlkLBNpEodaiQ8ZN5JLA5wpc1sUlk/O5uuOMNgvzddzkpvZ9GLyYNa8w2s7rqiTk5Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ERP Dashboard</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f2f4f7;
            color: #2c3e50;
            display: flex;
            flex-direction: column;
        }

        #content {
            flex: 1;
            padding: 0;
            background: #f2f4f7;
            overflow-y: auto;
        }

        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1000;
            background: #187f6a;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            font-size: 20px;
            cursor: pointer;
        }

        .dashboard-header {
            background: white;
            color: black;
            padding: 12px 20px;
            box-shadow: 0 4px 10px rgba(24, 127, 106, 0.15);
            border-radius: 8px;
            width: 96.5%;
            margin: 0 auto;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-left: 10px;
        }

        .notification-btn,
        .calculator-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 20px;
            color: #333;
            transition: color 0.2s ease;
            position: relative;
        }

        .notification-btn:hover,
        .calculator-btn:hover {
            color: #187f6a;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            display: none;
        }

        .time-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
            min-width: 150px;
        }

        .current-time {
            font-weight: 600;
            font-size: 15px;
        }

        .current-date {
            font-size: 13px;
            color: #666;
        }

        .user-section {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar i {
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            font-size: 18px;
            color: #187f6a;
            transition: color 0.2s ease;
        }

        .user-avatar i:hover {
            color: #145c50;
        }

        .user-details p {
            margin: 0;
            font-weight: 600;
        }

        .user-details a {
            font-size: 13px;
            color: #187f6a;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .user-details a:hover {
            color: #145c50;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-wrap: wrap;
            }

            .header-left,
            .time-container,
            .user-section {
                order: initial !important;
                width: auto !important;
            }
        }

        .dashboard-container {
            width: 100%;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .dashboard-main-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }
        
        @media (min-width: 1200px) {
            .dashboard-main-grid {
                grid-template-columns: 2fr 1fr;
            }
        }
        
        .left-column {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .right-column {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        * {
            max-width: 100%;
            box-sizing: border-box;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            width: 100%;
        }

        @media (max-width: 576px) {
            .stats-grid {
                gap: 10px;
            }
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 10px 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-top: 4px solid #187f6a;
            min-height: 100px;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            background: #f0f7f5;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #187f6a;
            font-size: 18px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin: 5px 0;
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stat-trend {
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 5px;
            text-align: center;
        }

        .trend-up {
            color: #27ae60;
        }

        .trend-down {
            color: #e74c3c;
        }

        .stat-subtext {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
            display: flex;
            gap: 10px;
        }

        .return-text {
            color: #e74c3c;
        }

        .net-text {
            color: #27ae60;
        }

        .quick-actions {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-title::before {
            content: '';
            width: 4px;
            height: 16px;
            background: #187f6a;
            border-radius: 2px;
        }

        .actions-grid {
            display: grid;
            gap: 15px;
            grid-template-columns: repeat(2, 1fr);
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 6px;
            background: white;
            color: #2c3e50;
            text-decoration: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #e0e6ed;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
            border-left: 3px solid #187f6a;
        }

        .action-icon {
            width: 30px;
            height: 30px;
            background: #f0f7f5;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #187f6a;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: #e9f5f2;
            transform: translateY(-2px);
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .action-btn:hover .action-icon {
            color: black;
        }

        .action-btn:focus,
        .action-btn:active {
            text-decoration: none;
            color: #187f6a;
            outline: none;
        }

        .charts-section {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .chart-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .chart-header {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 15px;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 600;
        }
        
        .chart-period {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            align-self: flex-start;
        }
        
        .chart-container {
            position: relative;
            height: 200px;
            width: 100%;
        }

        .low-stock-section, .product-performance {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .modal-content {
            border-radius: 10px;
            border: none;
        }

        .modal-header {
            background: #187f6a;
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 15px;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
        }

        .close {
            color: white;
            opacity: 0.8;
            font-size: 24px;
        }
        
        .stock-items-container {
            overflow-y: auto;
            max-height: 200px;
            min-height: 100px;
            border: 1px solid #f1f3f5;
            border-radius: 8px;
        }

        .stock-items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .stock-items-table th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            padding: 10px 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            z-index: 10;
        }

        .stock-items-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #f1f3f5;
        }

        .low-stock {
            color: #f39c12;
            font-weight: 600;
        }

        .critical-stock {
            color: #e74c3c;
            font-weight: 700;
        }

        .performance-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .performance-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .performance-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f5;
        }

        .performance-item:last-child {
            border-bottom: none;
        }
        
        .calculator-btn {
            background: transparent;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: #2c3e50;
            cursor: pointer;
            margin-right: 10px;
        }

        .calculator-btn:hover {
            background: #f1f3f5;
        }

        .result-item {
            margin: 10px 0;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        #vatAmountResult {
            color: #e74c3c;
            font-weight: bold;
        }

        #includedAmount {
            color: #187f6a;
            font-weight: bold;
        }

        #excludedAmount {
            color: #3498db;
            font-weight: bold;
        }
        
        .update-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .update-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .update-item:last-child {
            border-bottom: none;
        }

        .update-date {
            color: #777;
            font-size: 12px;
        }

        .update-title {
            font-weight: bold;
            font-size: 16px;
            margin: 5px 0;
            color: #20639B;
        }

        .update-description ul {
            padding-left: 20px;
        }

        .update-description li {
            margin-bottom: 5px;
        }

        .high-performer {
            color: #27ae60;
        }

        .low-performer {
            color: #e74c3c;
        }
        
        .compact-stat .stat-card {
            min-height: 90px !important;
            padding: 10px 12px !important;
        }
        
        .compact-stat .stat-value {
            font-size: 18px !important;
            margin: 3px 0 !important;
        }
        
        .compact-stat .stat-label {
            font-size: 11px !important;
        }
        
        .compact-stat .stat-trend {
            font-size: 10px !important;
            margin-top: 2px !important;
        }
        
        .compact-stat .stat-icon {
            width: 30px !important;
            height: 30px !important;
            font-size: 14px !important;
        }
        
        @media (max-width: 767px) {
            .compact-stat .stat-card {
                min-height: 80px !important;
                padding: 8px 10px !important;
            }
            
            .compact-stat .stat-value {
                font-size: 16px !important;
            }
        }
        
        .report-category { 
            margin-bottom: 20px; 
        }

        .report-category-title { 
            font-weight: 600; color: #187f6a; 
            margin-bottom: 10px; 
            padding-bottom: 5px; 
            border-bottom: 1px solid #e0e6ed; 
        }

        .report-options { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
            gap: 10px; 
        }

        .report-option { 
            display: block; 
            padding: 12px 15px; 
            background: #f8f9fa; 
            border-radius: 6px; color: #2c3e50; 
            text-decoration: none; 
            font-size: 14px; 
            transition: all 0.2s ease; 
            border-left: 3px solid #187f6a; 
        }

        .report-option:hover { 
            background: #e9f5f2; 
            transform: translateX(3px); 
            text-decoration: none; 
        
        }
        .report-option i { 
            margin-right: 8px; 
            color: #187f6a; 
        }

        @media (max-width: 768px) {
            .report-options {
                grid-template-columns: 1fr;
            }
        }

        @keyframes  pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .time-widget {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            padding: 5px 10px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            min-width: 100px;
        }

        .current-time {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
        }

        .current-date {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 2px;
        }

        .license-warning {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(243, 156, 18, 0.3);
            z-index: 1000;
            max-width: 90%;
        }

        @media (max-width: 576px) {
            .stat-card {
                padding: 8px;
            }
            .stat-value {
                font-size: 16px;
            }
            .stat-label {
                font-size: 10px;
            }
            .action-btn {
                padding: 12px;
                font-size: 13px;
            }
            .time-widget {
                margin-right: 10px;
                min-width: 80px;
            }
            .current-time {
                font-size: 14px;
            }
            .current-date {
                font-size: 10px;
            }
        }

        @media (max-width: 767px) {
            .stat-card {
                min-height: 90px !important;
                padding: 10px !important;
            }
            .stat-header {
                margin-bottom: 5px !important;
            }
            .stat-value {
                font-size: 18px !important;
                margin: 2px 0 !important;
            }
            .stat-icon {
                width: 30px !important;
                height: 30px !important;
                font-size: 14px !important;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 8px !important;
            }
        }

        @media (min-width: 576px) {
            .dashboard-header {
                padding: 15px 25px;
            }
            .dashboard-container {
                padding: 20px;
                gap: 20px;
            }
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 15px;
            }
            .chart-header {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
            .performance-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (min-width: 768px) {
            .mobile-menu-btn {
                display: none;
            }
            .charts-section {
                grid-template-columns: 1fr 1fr;
            }
            .stat-card {
                min-height: auto;
                height: 100%;
                padding: 12px 10px;
            }
            .quick-actions {
                padding: 20px;
            }
            .stat-header {
                margin-bottom: 8px;
            }
            .stat-value {
                font-size: 18px;
                margin: 3px 0;
            }
            .stat-label {
                font-size: 11px;
            }
            .stat-trend {
                font-size: 10px;
                margin-top: 3px;
            }
            .stat-icon {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }
            .stats-grid {
                grid-auto-rows: 1fr;
            }
        }

        @media (min-width: 992px) {
            .dashboard-container {
                max-width: 1400px;
                margin: 0 auto;
            }
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            }
            .stat-value {
                font-size: 26px;
            }
            .section-title {
                font-size: 18px;
            }
            .action-btn {
                padding: 15px;
                font-size: 15px;
            }
        }
        
        .customer-performance {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .customer-rank {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rank-badge {
            width: 24px;
            height: 24px;
            background: #187f6a;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .performance-item:nth-child(1) .rank-badge {
            background: #FFD700;
        }
        .performance-item:nth-child(2) .rank-badge {
            background: #C0C0C0;
        }
        .performance-item:nth-child(3) .rank-badge {
            background: #CD7F32;
        }
    </style>
</head>
<?php
    $userid = Session('softwareuser');
    $monthid = date('n');
    $currentYear = date('Y');
    $todayDate = date('Y-m-d');
    $location = DB::table('softwareusers')
        ->where('id', Session('softwareuser'))
        ->pluck('location')
        ->first();
?>
<body>
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <span class="glyphicon glyphicon-menu-hamburger"></span>
    </button>

    <?php echo $__env->make('layouts/usersidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div id="content">
        <?php
        use Carbon\Carbon;
        $joined_date = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('joined_date')
            ->first();
        $joinedDate = Carbon::parse($joined_date);
        $expirationDate = $joinedDate->addDays(365)->toDateString();
        ?>

        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-left">
                    <button class="notification-btn notification-button" data-toggle="modal" data-target="#updateModal" title="Software Updates">
                        <span class="glyphicon glyphicon-bell" style="padding-top: 5px;"></span>
                        <span class="notification-badge" id="updateBadge">1</span>
                    </button>

                    <button class="calculator-btn" data-toggle="modal" data-target="#calculatorModal" title="VAT calculator">
                        <span class="fa fa-calculator"></span>
                    </button>
                </div>

                <div class="time-container">
                    <div class="time-widget">
                        <div class="current-time" id="currentTime"></div>
                        <div class="current-date" id="currentDate"></div>
                    </div>
                </div>

                <div class="user-section">
                    <div class="user-avatar">
                        <i class="fas fa-user" style="padding-right: 4px;"></i>
                    </div>
                    <div class="user-details">
                        <?php $__currentLoopData = $userdatas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userdata): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p><?php echo e(ucfirst(strtolower($userdata['name']))); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <a href="/edituser/<?php echo e($userid); ?>">Change Password</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="calculatorModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                        <h4 class="modal-title">VAT Calculator</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" id="vatAmount" placeholder="Enter amount" autofocus>
                        </div>
                        <div class="form-group">
                            <label>VAT Rate (%)</label>
                            <input type="number" class="form-control" id="vatRate" placeholder="Enter VAT rate" value="5">
                        </div>
                        <div class="form-group">
                            <label>Calculation Type</label>
                            <select class="form-control" id="vatType">
                                <option value="inclusive">Inclusive of VAT</option>
                                <option value="exclusive">Exclusive of VAT</option>
                            </select>
                        </div>

                        <div class="results mt-3">
                            <hr>
                            <div class="result-item">
                                <strong>VAT Amount:</strong>
                                <span id="vatAmountResult">0.00</span>
                            </div>
                            <div class="result-item">
                                <strong>Included Amount:</strong>
                                <span id="includedAmount">0.00</span>
                            </div>
                            <div class="result-item">
                                <strong>Excluded Amount:</strong>
                                <span id="excludedAmount">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if($expirationDate <= Carbon::now()->toDateString()): ?>
            <div class="container mt-3">
                <div class="alert alert-danger mb-3">
                    <strong>Warning!</strong> Your access has expired. Please contact the administrator.
                </div>
            </div>
        <?php else: ?>
        <div id="remaining-days" class="license-warning hide"></div>
        <?php endif; ?>

        <div class="dashboard-container">
            <div class="dashboard-main-grid">
                <div class="left-column">
                    <div class="stats-grid compact-stat">
                        <div class="stat-card">
                            <div class="stat-header">
                                <div>
                                    <div class="stat-value"><?php echo e($currency); ?> <?php echo e(number_format($todaysale, 3)); ?></div>
                                    <div class="stat-label">Today's Sales (Gross)</div>
                                    <div class="stat-subtext">
                                        <span class="return-text">Returns: <?php echo e($currency); ?> <?php echo e(number_format($todayreturn, 3)); ?></span>
                                        <span class="net-text">Net: <?php echo e($currency); ?> <?php echo e(number_format($todaysale - $todayreturn, 3)); ?></span>
                                    </div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-sack-dollar"></i>
                                </div>
                            </div>
                            <div class="stat-trend trend-up">
                                <i class="fa-solid fa-arrow-trend-up"></i>
                                Daily sales performance
                            </div>
                        </div>

                        <?php if($servicerole == 1): ?>
                        <div class="stat-card">
                            <div class="stat-header">
                                <div>
                                    <div class="stat-value"><?php echo e($currency); ?> <?php echo e(number_format($todayservice, 3)); ?></div>
                                    <div class="stat-label">Today's Services</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-screwdriver-wrench"></i>
                                </div>
                            </div>
                            <div class="stat-trend trend-up">
                                <i class="fa-solid fa-arrow-trend-up"></i>
                                Service revenue
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div>
                                    <div class="stat-value"><?php echo e($transaction_count); ?></div>
                                    <div class="stat-label">Today's Total Invoices</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </div>
                            </div>
                            <div class="stat-trend trend-up">
                                <i class="fa-solid fa-arrow-trend-up"></i>
                                Transaction volume
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div>
                                    <div class="stat-value"><?php echo e($currency); ?> <?php echo e(number_format($todaypurchase, 3)); ?></div>
                                    <div class="stat-label">Today's Purchases</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </div>
                            </div>
                            <div class="stat-trend trend-down">
                                <i class="fa-solid fa-arrow-trend-down"></i>
                                Supplier transactions
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div>
                                    <div class="stat-value"><?php echo e($currency); ?> <?php echo e(number_format($todayexpenses, 3)); ?></div>
                                    <div class="stat-label">Today's Expenses</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                            </div>
                            <div class="stat-trend trend-down">
                                <i class="fa-solid fa-arrow-trend-down"></i>
                                Cash Outflow
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div>
                                    <div class="stat-value"><?php echo e($currency); ?> <?php echo e(number_format($credit, 3)); ?></div>
                                    <div class="stat-label">Accounts Receivable</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-hand-holding-usd"></i>
                                </div>
                            </div>
                            <div class="stat-trend trend-up">
                                <i class="fa-solid fa-arrow-trend-up"></i>
                                Customer Balance
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div>
                                    <div class="stat-value"><?php echo e($currency); ?> <?php echo e(number_format($supplier, 3)); ?></div>
                                    <div class="stat-label">Accounts Payable</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-money-check-dollar"></i>
                                </div>
                            </div>
                            <div class="stat-trend trend-down">
                                <i class="fa-solid fa-arrow-trend-down"></i>
                                Supplier Dues
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    <h4 class="modal-title">Invoice Options</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="report-category">
                                        <h4 class="report-category-title">Invoice Actions</h4>
                                        <div class="report-options">
                                            <a href="/dashboard" class="report-option"><i class="glyphicon glyphicon-plus"></i> New Invoice</a>
                                            <a href="/return" class="report-option"><i class="glyphicon glyphicon-retweet"></i> Return Invoice</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="purchaseModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    <h4 class="modal-title">Purchase Options</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="report-category">
                                        <h4 class="report-category-title">Purchase Actions</h4>
                                        <div class="report-options">
                                            <a href="/purchasestock" class="report-option"><i class="glyphicon glyphicon-shopping-cart"></i> New Purchase</a>
                                            <a href="/purchasereturn" class="report-option"><i class="glyphicon glyphicon-repeat"></i> Purchase Return</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="reportModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    <h4 class="modal-title">Reports Center</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="report-category">
                                        <h4 class="report-category-title">Sales Reports</h4>
                                        <div class="report-options">
                                            <a href="/export-sales-day/<?php echo e($userid); ?>/1/<?php echo e($location); ?>/<?php echo e($todayDate); ?>" class="report-option"><i class="fas fa-calendar-alt"></i> Today's Sales</a>
                                            <a href="/export-sales-month/<?php echo e($userid); ?>/2/<?php echo e($location); ?>/<?php echo e($monthid); ?>" class="report-option"><i class="fas fa-chart-bar"></i> Monthly Sales</a>
                                            <a href="/export-sales-year/<?php echo e($userid); ?>/3/<?php echo e($location); ?>/<?php echo e($currentYear); ?>" class="report-option"><i class="fas fa-chart-line"></i> Annual Sales</a>                                
                                        </div>
                                    </div>
                                    <div class="report-category">
                                        <h4 class="report-category-title">Purchase Reports</h4>
                                        <div class="report-options">
                                            <a href="/export-purchase-day/<?php echo e($userid); ?>/1/<?php echo e($location); ?>/<?php echo e($todayDate); ?>" class="report-option"><i class="fas fa-shopping-cart"></i> Today's Purchases</a>
                                            <a href="/export-purchase-month/<?php echo e($userid); ?>/2/<?php echo e($location); ?>/<?php echo e($monthid); ?>" class="report-option"><i class="fas fa-list-alt"></i> Monthly Purchases</a>
                                            <a href="/export-purchase-year/<?php echo e($userid); ?>/3/<?php echo e($location); ?>/<?php echo e($currentYear); ?>" class="report-option"><i class="fas fa-book"></i> Annual Purchases</a>
                                        </div>
                                    </div>
                                    <div class="report-category">
                                        <h4 class="report-category-title">Export Data</h4>
                                        <div class="report-options">
                                            <a href="<?php echo e(route('exportproductreport')); ?>" class="report-option"><i class="fas fa-file-export"></i> Export Product Report</a>
                                            <a href="<?php echo e(url('export-product')); ?>" class="report-option"><i class="fas fa-file-export"></i> Export Products</a>
                                            <a href="<?php echo e(route('printstocklist')); ?>" class="report-option"><i class="fas fa-boxes"></i> Export Stock</a>
                                            <a href="<?php echo e(url('/export-expense-history')); ?>" class="report-option"><i class="fas fa-file-export"></i> Export Expenses</a>
                                            <?php
                                            $start_date = $end_date = Carbon::now()->format('Y-m-d');
                                            ?>
                                            <a href="/exportpandl/<?php echo e($start_date); ?>/<?php echo e($end_date); ?>/<?php echo e($location); ?>" class="report-option"><i class="fas fa-file-invoice-dollar"></i> Export Profit & Loss</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="createEntityModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    <h4 class="modal-title">New Entity</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="report-category">
                                        <h4 class="report-category-title">Entity Type</h4>
                                        <div class="report-options">
                                            <a href="/createcredit" class="report-option">
                                                <i class="fa fa-user"></i> New Customer
                                            </a>
                                            <a href="/createsupplier" class="report-option">
                                                <i class="fa fa-dolly"></i> New Supplier
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="inventoryModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    <h4 class="modal-title">Inventory</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="report-category">
                                        <h4 class="report-category-title">Inventory</h4>
                                        <div class="report-options">
                                            <a href="/inventorydashboard" class="report-option">
                                                <i class="fa fa-plus-square"></i> Add New Product
                                            </a>
                                            <a href="/liststocks" class="report-option">
                                                <i class="fa fa-cubes"></i> Stock List
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="charts-section">
                        <div class="chart-card">
                            <div class="chart-header">
                                <div class="chart-title">Sales Overview</div>
                            </div>
                            <div class="chart-container">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>

                        <div class="chart-card">
                            <div class="chart-header">
                                <div class="chart-title">Purchase Analysis</div>
                            </div>
                            <div class="chart-container">
                                <canvas id="purchaseChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="product-performance">
                        <div class="section-title">Product Performance (in Qty)</div>
                        <div class="performance-grid">
                            <div style="padding-right: 10px; padding-left: 10px;">
                                <h5><i class="fas fa-arrow-trend-up high-performer" style="padding-right: 2px;"></i> Top 3 Products</h5>
                                <div class="performance-list" id="topProducts">
                                    <?php $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="performance-item">
                                        <span><?php echo e($product->product_name); ?></span>
                                        <span class="high-performer"><?php echo e(number_format($product->total_sales, 2)); ?></span>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            <div style="padding-left: 10px; padding-right: 10px;">
                                <h5><i class="fas fa-arrow-trend-down low-performer" style="padding-right: 2px;"></i> Bottom 3 Products</h5>
                                <div class="performance-list" id="bottomProducts">
                                    <?php $__currentLoopData = $bottomProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="performance-item">
                                        <span><?php echo e($product->product_name); ?></span>
                                        <span class="low-performer"><?php echo e(number_format($product->total_sales, 2)); ?></span>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="right-column">
                    <div class="quick-actions">
                        <div class="section-title">Quick Actions</div>

                        <div class="actions-grid">
                            <a href="#" class="action-btn" data-toggle="modal" data-target="#createEntityModal">
                                <div class="action-icon">
                                    <i class="fa-solid fa-user-plus"></i>
                                </div>
                                New Entity
                            </a>

                            <a href="#" class="action-btn" data-toggle="modal" data-target="#invoiceModal">
                                <div class="action-icon">
                                    <i class="fa-solid fa-print"></i>
                                </div>
                                Invoice
                            </a>

                            <a href="#" class="action-btn" data-toggle="modal" data-target="#purchaseModal">
                                <div class="action-icon">
                                    <i class="fa-solid fa-cart-plus"></i>
                                </div>
                                Purchase
                            </a>

                            <a href="#" class="action-btn" data-toggle="modal" data-target="#inventoryModal">
                                <div class="action-icon">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </div>
                                Inventory
                            </a>

                            <a href="#" class="action-btn" data-toggle="modal" data-target="#reportModal">
                                <div class="action-icon">
                                    <i class="fa-solid fa-chart-line"></i>
                                </div>
                                Reports Center
                            </a>

                            <a href="/calender" class="action-btn">
                                <div class="action-icon">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </div>
                                Calendar
                            </a>
                        </div>
                    </div>

                    <div class="customer-performance">
                        <div class="section-title">Top Customers</div>
                        <div class="performance-list">
                            <?php $__currentLoopData = $topCustomers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="performance-item">
                                <div class="customer-rank">
                                    <span class="rank-badge"><?php echo e($index + 1); ?></span>
                                    <span><?php echo e($customer->customer_name); ?></span>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                                    
                    <div class="low-stock-section">
                        <div class="section-title">Low Stock Items (Below 5)</div>
                        <div class="stock-items-container">
                            <table class="stock-items-table">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Current Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $lowStockItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($item->product_name); ?></td>
                                        <td>
                                            <?php echo e($item->remaining_stock < 0 ? 0.000 : $item->remaining_stock); ?>

                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" data-content-version="1">
                <div class="modal-dialog modal-lg" role="document" style="max-width: 800px;">
                    <div class="modal-content" style="max-height: 80vh; display: flex; flex-direction: column;">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                            <h4 class="modal-title">Software Updates</h4>
                        </div>
                        <div class="modal-body" style="overflow-y: auto; flex: 1;">
                            <div class="update-list">
                                <div class="update-item">
                                    <div class="update-date">July 16, 2025</div>
                                    <div class="update-description">
                                        <ul>
                                            <li>Added a streamlined option to create new customers directly from the billing page and new suppliers from the purchase page for faster workflow.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="update-item">
                                    <div class="update-date">July 14, 2025</div>
                                    <div class="update-description">
                                        <ul>
                                            <li>Enhanced the <strong>Calendar</strong> section with day-wise sales report downloads - simply click on any date to instantly export that day's sales data.</li>
                                            <li>Improved mobile responsiveness across all dashboard components for better usability on smartphones.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="update-item">
                                    <div class="update-date">July 13, 2025</div>
                                    <div class="update-description">
                                        <ul>
                                            <li>Added new <strong>Settings</strong> section in sidebar navigation for editing company details.</li>
                                            <li>Dashboard now displays <strong>Accounts Receivable</strong> (customer balances) and <strong>Accounts Payable</strong> (supplier balances) for real-time financial visibility.</li>
                                            <li>Added a <strong>Calendar</strong> feature in the sidebar to easily track daily sales counts.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="update-item">
                                    <div class="update-date">July 09, 2025</div>
                                    <div class="update-description">
                                        <ul>
                                            <li><strong>New Reports Center:</strong> We've implemented a comprehensive reporting dashboard for easy access to all your business data.</li>
                                            <li><strong>Sales Reports:</strong> Generate daily, monthly, and yearly sales reports with just one click.</li>
                                            <li><strong>Purchase Reports:</strong> Track purchases by day, month, or year for better inventory management.</li>
                                            <li><strong>Financial Reports:</strong> Export expense reports and profit & loss statements for accounting purposes.</li>
                                            <li><strong>Inventory Reports:</strong> Access stock levels, product performance reports, and export product catalogs.</li>
                                            <li><strong>Mobile Access:</strong> All reports are now optimized for mobile viewing. Access your dashboard anytime at:
                                                <div style="margin-top: 8px;">
                                                    <a href="https://taxfly.netplexsolution.com" target="_blank" style="color: #187f6a; font-weight: bold; text-decoration: underline;">
                                                        https://taxfly.netplexsolution.com
                                                    </a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="update-item">
                                    <div class="update-date">July 08, 2025</div>
                                    <div class="update-description">
                                        <ul>
                                            <li>Implemented <strong>Low Stock Alert</strong> system that highlights products with stock below 5 on both Dashboard and Stock List pages</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="update-item">
                                    <div class="update-date">July 04, 2025</div>
                                    <div class="update-description">
                                        <ul>
                                            <li>Added <strong>Chart of Accounts</strong> and <strong>Trial Balance</strong> feature. You can now access them from the sidebar.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="update-item">
                                    <div class="update-date">July 01, 2025</div>
                                    <div class="update-description">
                                        <ul>
                                            <li>Added new <strong>"All Histories"</strong> section to sidebar, consolidating all transaction records in one convenient location for quick access and reporting.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-open');
        });

        const initializeCharts = () => {
            const yearData = JSON.parse('<?php echo $year; ?>');
            const userData = JSON.parse('<?php echo $user; ?>');
            const returnedData = JSON.parse('<?php echo $returned; ?>');
            const purchaseData = JSON.parse('<?php echo $purchase; ?>');
            const returnPurchaseData = JSON.parse('<?php echo $returnpurchase; ?>');

            const dayNames = yearData.map(date => {
                const day = new Date(date);
                return day.toLocaleDateString('en-US', { weekday: 'short' });
            });

            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 10,
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f3f5'
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            };

            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: dayNames,
                    datasets: [{
                        label: 'Sales',
                        data: userData,
                        backgroundColor: '#187f6a',
                        borderColor: '#187f6a',
                        borderWidth: 1,
                        borderRadius: 4,
                    }, {
                        label: 'Returns',
                        data: returnedData,
                        backgroundColor: '#e74c3c',
                        borderColor: '#e74c3c',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: chartOptions
            });

            const purchaseCtx = document.getElementById('purchaseChart').getContext('2d');
            new Chart(purchaseCtx, {
                type: 'bar',
                data: {
                    labels: dayNames,
                    datasets: [{
                        label: 'Purchases',
                        data: purchaseData,
                        backgroundColor: '#3498db',
                        borderColor: '#3498db',
                        borderWidth: 1,
                        borderRadius: 4,
                    }, {
                        label: 'Purchase Returns',
                        data: returnPurchaseData,
                        backgroundColor: '#f39c12',
                        borderColor: '#f39c12',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: chartOptions
            });
        };

        const updateLicenseWarning = () => {
            const expirationDate = <?php echo json_encode($expirationDate, 15, 512) ?>;
            const currentDate = new Date().toISOString().split('T')[0];

            const calculateRemainingDays = (expDate, currDate) => {
                const diff = new Date(expDate) - new Date(currDate);
                return Math.ceil(diff / (1000 * 3600 * 24));
            };

            const remainingDays = calculateRemainingDays(expirationDate, currentDate);
            const warningElement = document.getElementById('remaining-days');

            if (warningElement) {
                warningElement.innerHTML = `License expires in: <strong>${remainingDays}</strong> days`;

                if (remainingDays < 31) {
                    warningElement.style.background = 'linear-gradient(135deg, #e67e22, #d35400)';
                }
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            updateLicenseWarning();

            if (document.querySelector('.alert-danger')) {
                document.querySelectorAll('a, button').forEach(el => {
                    el.style.pointerEvents = 'none';
                    el.style.opacity = '0.7';
                });
            }
        });

        window.addEventListener('resize', function() {
            if (typeof Chart !== 'undefined' && Chart.instances) {
                Chart.instances.forEach(instance => {
                    instance.resize();
                });
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            function calculateVAT() {
                const amount = parseFloat($('#vatAmount').val()) || 0;
                const rate = parseFloat($('#vatRate').val()) || 15;
                const type = $('#vatType').val();

                let vat, included, excluded;

                if(type === 'inclusive') {
                    vat = amount * (rate / (100 + rate));
                    included = amount;
                    excluded = amount - vat;
                } else {
                    vat = amount * (rate / 100);
                    excluded = amount;
                    included = amount + vat;
                }

                $('#vatAmountResult').text(vat.toFixed(2));
                $('#includedAmount').text(included.toFixed(2));
                $('#excludedAmount').text(excluded.toFixed(2));
            }

            $('#vatAmount, #vatRate, #vatType').on('input change', calculateVAT);

            $('#calculatorModal').on('shown.bs.modal', function() {
                $('#vatAmount').focus();
            });

            $('#calculatorModal').on('hidden.bs.modal', function() {
                $('#vatAmount').val('');
                calculateVAT();
            });

            calculateVAT();
        });
    </script>

    <script>
        $(document).ready(function() {
            const updatesSeen = localStorage.getItem('updatesSeen') === 'true';

            if (!updatesSeen) {
                checkForUpdates();
            }

            $('#updateModal').on('shown.bs.modal', function() {
                localStorage.setItem('updatesSeen', 'true');
                hideNotifications();
            });

            function checkForUpdates() {
                const serverHasUpdates = true;

                if (serverHasUpdates) {
                    showNotifications();
                } else {
                    hideNotifications();
                }
            }

            function showNotifications() {
                if (localStorage.getItem('updatesSeen') !== 'true') {
                    $('#updateBadge').show();
                    $('.notification-btn').addClass('has-notifications');
                }
            }

            function hideNotifications() {
                $('#updateBadge').hide();
                $('.notification-btn').removeClass('has-notifications');
            }

            $(document).on('click', '#mark-unread', function() {
                localStorage.setItem('updatesSeen', 'false');
                showNotifications();
                $('#updateModal').modal('hide');
            });
        });
    </script>

    <script>
        function updateClock() {
            const now = new Date();
            
            const timeString = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            
            const dateString = now.toLocaleDateString('en-US', {
                weekday: 'short',
                month: 'short',
                day: 'numeric'
            });
            
            document.getElementById('currentTime').textContent = timeString;
            document.getElementById('currentDate').textContent = dateString;
        }

        updateClock();
        setInterval(updateClock, 1000);

        document.addEventListener('DOMContentLoaded', function() {
            updateClock();
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.report-option').attr('target', '_blank');

            setInterval(function() {
                $('.report-btn').toggleClass('pulse');
            }, 2000);
        });
    </script>
</body>
</html><?php /**PATH C:\xampp\htdocs\netplex_26_7\resources\views//user/dashboard.blade.php ENDPATH**/ ?>