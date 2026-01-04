<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Balance Sheet</title>
    <?php echo $__env->make('layouts/usersidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <style>

        :root {
            --primary-color: #187f6a;
            --secondary-color: #e5edff;
            --border-color: #e2e8f0;
        }



        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .filter-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .filter-row {
            display: flex;
            gap: 20px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 300px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #4a5568;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 18px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-success {
            background-color: #059669;
            color: white;
        }

        .balance-sheet {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-top: 30px;
        }

        .sheet-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            font-weight: bold;
        }

        .section-content {
            padding: 20px;
        }

        .item-group {
            margin-bottom: 20px;
        }

        .item-group-title {
            font-weight: bold;
            color: #2d3748;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            font-weight: bold;
            border-bottom: 5px solid var(--border-color);
            margin-top: 15px;
        }

        .currency {
            color: #2d3748;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .balance-sheet {
                grid-template-columns: 1fr;
            }
        }

    </style>
</head>
<?php
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
?>
<body>
    <div id="content">
        <?php if($adminroles->contains('module_id', '30')): ?>
        <div style="margin-top: 15px;margin-left:15px;">
            <?php echo $__env->make('navbar.chartofaccounts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.logout_nav_user','data' => []]); ?>
<?php $component->withName('logout_nav_user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <?php endif; ?>



        


            <div class="container" style="width: 100%;">
                <div class="header">
                    <h1>Balance Sheet</h1>
                    <p>As of <?php echo e(date('F d, Y')); ?></p>
                </div>



                <form class="formcss" action="<?php echo e(route('balanceSheetfilter')); ?>" method="get" onsubmit="return validateDateFilter()">
                    <div class="row">
                        <div class="col-sm-10">
                            <h4>SELECT DATES</h4>
                            <div class="row">
                                <div class="col-sm-3">
                                    From
                                    <input type="date" class="form-control" name="start_date" id="start_date" value="<?php echo e(request('start_date', now()->toDateString())); ?>">
                                </div>
                                <div class="col-sm-3">
                                    To
                                    <input type="date" class="form-control" name="end_date" id="end_date" value="<?php echo e(request('end_date', now()->toDateString())); ?>">
                                </div>
                                <div class="col-sm-2">
                                    <br>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>


                <div class="balance-sheet">
                    <!-- Assets Section -->
                    <div class="sheet-section">
                        <div class="section-header">
                            Assets
                        </div>
                        <div class="section-content">
                            <!-- Current Assets -->
                            <div class="item-group">
                                <div class="item-group-title">Current Assets</div>
                                <?php $totalCurrentAssets = 0; ?>

                                <?php $__currentLoopData = $currentAssets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="item-row">
                                        <span><?php echo e($asset->type_name); ?></span>
                                        <span class="currency"><?php echo e(number_format($asset->type_amount, 3)); ?></span>
                                    </div>
                                    <?php $totalCurrentAssets += $asset->type_amount; ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <div class="total-row">
                                    <span>Total Current Assets</span>
                                    <span class="currency"><?php echo e($currency); ?> <?php echo e(number_format($totalCurrentAssets, 3)); ?></span>
                                </div>
                            </div>

                            <!-- Fixed Assets -->
                            <div class="item-group">
                                <div class="item-group-title">Fixed Assets</div>
                                <?php $totalFixedAssets = 0; ?>
                                <?php $__currentLoopData = $fixedAssets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="item-row">
                                        <span><?php echo e($asset->type_name); ?></span>
                                        <span class="currency"><?php echo e(number_format($asset->type_amount, 3)); ?></span>
                                    </div>
                                    <?php $totalFixedAssets += $asset->type_amount; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <div class="total-row">
                                    <span>Total Fixed Assets</span>
                                    <span class="currency"><?php echo e($currency); ?> <?php echo e(number_format($totalFixedAssets, 3)); ?></span>
                                </div>
                            </div>

                            <!-- Other Assets -->
                            <div class="item-group">
                                <div class="item-group-title">Other Assets</div>
                                <?php $totalOtherAssets = 0; ?>
                                <?php $__currentLoopData = $otherAssets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="item-row">
                                        <span><?php echo e($asset->type_name); ?></span>
                                        <span class="currency"><?php echo e(number_format($asset->type_amount, 3)); ?></span>
                                    </div>
                                    <?php $totalOtherAssets += $asset->type_amount; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <div class="total-row">
                                    <span>Total Other Assets</span>
                                    <span class="currency"><?php echo e($currency); ?> <?php echo e(number_format($totalOtherAssets, 3)); ?></span>
                                </div>
                            </div>

                            <div class="total-row" style="border-top: 3px double var(--border-color);">
                                <span style="font-size: 16px;">Total Assets</span>
                                <span class="currency"><b style="font-size: 16px;"><?php echo e($currency); ?> <?php echo e(number_format($totalFixedAssets + $totalCurrentAssets + $totalOtherAssets, 3)); ?></b></span>
                            </div>
                        </div>
                    </div>

                    <!-- Liabilities and Equity Section -->
                    <div class="sheet-section">
                        <div class="section-header">
                            Liabilities
                        </div>
                        <div class="section-content">
                            <!-- Current Liabilities -->
                            <div class="item-group">
                                <div class="item-group-title">Short-term Liabilities</div>

                            <?php $totalShortTermLiabilities = 0; ?>
                            <?php $__currentLoopData = $shortTermLiabilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $liability): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="item-row">
                                        <span><?php echo e($liability->type_category); ?></span>
                                        <span class="currency"><?php echo e(number_format($liability->type_amount, 3)); ?></span>
                                    </div>
                                    <?php $totalShortTermLiabilities += $liability->type_amount; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <div class="total-row">
                                    <span>Total Current Liabilities</span>
                                    <span class="currency"><?php echo e($currency); ?> <?php echo e(number_format($totalShortTermLiabilities, 3)); ?></span>
                                </div>
                            </div>

                            <!-- Long-term Liabilities -->
                            <div class="item-group">
                                <div class="item-group-title">Long-term Liabilities</div>
                                <?php $totalLongTermLiabilities = 0; ?>
                                <?php $__currentLoopData = $longTermLiabilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $liability): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="item-row">
                                        <span><?php echo e($liability->type_category); ?></span>
                                        <span class="currency"><?php echo e(number_format($liability->type_amount, 3)); ?></span>
                                    </div>
                                    <?php $totalLongTermLiabilities += $liability->type_amount; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <div class="total-row">
                                    <span>Total Long-term Liabilities</span>
                                    <span class="currency"><?php echo e($currency); ?> <?php echo e(number_format($totalLongTermLiabilities, 3)); ?></span>
                                </div>
                            </div>
                            <div class="item-group">
                                <div class="item-group-title">Capital</div>
                                <?php $totalcapital = 0; ?>
                                <?php $__currentLoopData = $capital; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $capi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="item-row">
                                        <span><?php echo e($capi->type_name); ?></span>
                                        <span class="currency"><?php echo e(number_format($capi->type_amount, 3)); ?></span>
                                    </div>
                                    <?php $totalcapital += $capi->type_amount; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <div class="total-row">
                                    <span>Total Capital</span>
                                    <span class="currency"><?php echo e($currency); ?> <?php echo e(number_format($totalcapital, 3)); ?></span>
                                </div>
                            </div>


                            <div class="total-row" style="border-top: 3px double var(--border-color);">
                                <span style="font-size: 16px;">Total Liabilities and Capital</span>
                                <span class="currency"><b style="font-size: 16px;"><?php echo e($currency); ?> <?php echo e(number_format($totalShortTermLiabilities + $totalLongTermLiabilities + $totalcapital, 3)); ?></b></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Balance Sheet Summary -->
                <div class="sheet-section" style="grid-column: span 2; margin-top: 30px;">
                    <div class="section-header">
                        Balance Sheet Summary
                    </div>
                    <div class="section-content">
                        <div class="item-group">
                            <div class="item-row">
                                <span><strong>Total Assets:</strong></span>
                                <span class="currency"><strong><?php echo e($currency); ?> <?php echo e(number_format($totalFixedAssets + $totalCurrentAssets + $totalOtherAssets, 3)); ?></strong></span>
                            </div>
                            <div class="item-row">
                                <span><strong>Total Liabilities:</strong></span>
                                <span class="currency"><strong><?php echo e($currency); ?> <?php echo e(number_format($totalShortTermLiabilities + $totalLongTermLiabilities, 3)); ?></strong></span>
                            </div>
                            <div class="item-row">
                                <span><strong>Total Capital:</strong></span>
                                <span class="currency"><strong><?php echo e($currency); ?> <?php echo e(number_format($totalcapital, 3)); ?></strong></span>
                            </div>
                            <div class="item-row">
                                <span><strong>Total Liabilities & Capital:</strong></span>
                                <span class="currency"><strong><?php echo e($currency); ?> <?php echo e(number_format($totalShortTermLiabilities + $totalLongTermLiabilities + $totalcapital, 3)); ?></strong></span>
                            </div>
                            <div class="item-row" style="border-top: 2px solid var(--border-color); padding-top: 15px;">
                                <?php
                                    $totalAssets = $totalFixedAssets + $totalCurrentAssets + $totalOtherAssets;
                                    $totalLiabilitiesCapital = $totalShortTermLiabilities + $totalLongTermLiabilities + $totalcapital;
                                    $balanceStatus = ($totalAssets == $totalLiabilitiesCapital) ? 'Balanced' : 'Unbalanced';
                                    $statusColor = ($balanceStatus == 'Balanced') ? 'color: #059669;' : 'color: #dc2626;';
                                ?>
                                <span><strong>Balance Status:</strong></span>
                                <span class="currency" style="<?php echo e($statusColor); ?>"><strong><?php echo e($balanceStatus); ?></strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


    </div>
    <script>
        function validateDates() {
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;

            if (startDate && !endDate) {
                alert("Please select the 'To' date.");
                return false;
            }
            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date cannot be after the end date.');
                return false;
            }
            return true;
        }
    </script>
    <script>
        function validateDateFilter() {
            let startDate = document.getElementById("start_date").value;
            let endDate = document.getElementById("end_date").value;

            if (startDate && endDate && startDate > endDate) {
                alert("Start date cannot be greater than end date.");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\netplex_26_7\resources\views//chartaccounts/balancesheet.blade.php ENDPATH**/ ?>