<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Trial Balance</title>
    <?php echo $__env->make('layouts/usersidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <style>
        .content-wrapper {
            margin: 0 auto;
            max-width: 1600px;
            padding: 20px;
        }

        .table-container {
            margin-top: 20px;
            overflow-x: auto;
        }

        .financial-table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }

        .financial-table th {
            background-color: #187f6a;
            color: white;
            font-weight: bold;
            padding: 12px 15px;
            font-size:14px;
            text-align: center;
        }

        .financial-table td {
            padding: 10px 15px;
            font-size:13px;
            border-bottom: 1px solid #ddd;
        }

        .financial-table tr:hover {
            background-color: #f5f5f5;
        }

        .section-header {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .divider {
            background-color: #ddd;
            height: 1px;
            border: none;
            margin: 5px 0;
        }

        .totals-row {
            font-weight: bold;
            background-color: #f2f2f2;
            border-top: 2px solid #ddd;
            border-bottom: 2px solid #ddd;
        }

        .report-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .report-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .report-header p {
            font-size: 14px;
            color: #666;
        }

        .debit-col {
            text-align: right;
            width: 25%;
        }

        .credit-col {
            text-align: right;
            width: 25%;
        }

        .account-col {
            width: 50%;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 10px;
            }
            .financial-table th,
            .financial-table td {
                padding: 8px 10px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div id="content">
        <div class="content-wrapper">
            <div class="report-header">
                <h1>Trial Balance</h1>
                <p>As of <?php echo e(date('F j, Y')); ?></p>
            </div>

            <div class="table-container">
                <table class="financial-table">
                    <thead>
                        <tr>
                            <th class="account-col">Account Name</th>
                            <th class="debit-col">Debit (<?php echo e($currency); ?>)</th>
                            <th class="credit-col">Credit (<?php echo e($currency); ?>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ASSETS SECTION -->
                        <tr class="section-header">
                            <td colspan="3">ASSETS</td>
                        </tr>
                        <tr>
                            <td class="account-col">Cash in hand</td>
                            <td class="debit-col"><?php echo e(number_format($cashinhanddebit, 2)); ?></td>
                            <td class="credit-col"><?php echo e(number_format($cashinhandcredit, 2)); ?></td>
                        </tr>
                        <tr>
                            <td class="account-col">Bank</td>
                            <td class="debit-col"><?php echo e(number_format($bankdebit, 2)); ?></td>
                            <td class="credit-col"><?php echo e(number_format($bankcredit, 2)); ?></td>
                        </tr>
                        <tr>
                            <td class="account-col">Account Receivable</td>
                            <td class="debit-col"><?php echo e(number_format($credit, 2)); ?></td>
                            <td class="credit-col"></td>
                        </tr>
                        <tr>
                            <td class="account-col">Current Asset</td>
                            <td class="debit-col"><?php echo e(number_format($currentassetSum, 2)); ?></td>
                            <td class="credit-col"></td>
                        </tr>
                        <tr>
                            <td class="account-col">Fixed Asset</td>
                            <td class="debit-col"><?php echo e(number_format($fixedassetSum, 2)); ?></td>
                            <td class="credit-col"></td>
                        </tr>
                        <tr>
                            <td class="account-col">Purchase VAT</td>
                            <td class="debit-col"><?php echo e(number_format($purchasevat, 2)); ?></td>
                            <td class="credit-col"></td>
                        </tr>
                        <tr>
                            <td class="account-col">Purchase Return VAT</td>
                            <td class="debit-col"></td>
                            <td class="credit-col"><?php echo e(number_format($purchasereturnvat, 2)); ?></td>
                        </tr>
                         <tr>
                            <td class="account-col">Discount Allowed</td>
                            <td class="debit-col"><?php echo e(number_format($totaldiscount, 2)); ?></td>
                            <td class="credit-col"></td>
                        </tr>
                        <tr>
                            <td class="account-col">Discount Received</td>
                            <td class="debit-col"></td>
                            <td class="credit-col"><?php echo e(number_format($totaldiscountpurchase, 2)); ?></td>
                        </tr>
                        <!-- LIABILITIES SECTION -->
                        <tr class="section-header">
                            <td colspan="3">LIABILITIES</td>
                        </tr>
                        <tr>
                            <td class="account-col">Account Payable</td>
                            <td class="debit-col"></td>
                            <td class="credit-col"><?php echo e(number_format($supplier, 2)); ?></td>
                        </tr>
                        <tr>
                            <td class="account-col">Sales VAT</td>
                            <td class="debit-col"></td>
                            <td class="credit-col"><?php echo e(number_format($salevat, 2)); ?></td>
                        </tr>
                        <tr>
                            <td class="account-col">Sales Return VAT</td>
                            <td class="debit-col"><?php echo e(number_format($salereturnvat, 2)); ?></td>
                            <td class="credit-col"></td>
                        </tr>
                        <tr>
                            <td class="account-col">Long Term Liabilities</td>
                            <td class="debit-col"></td>
                            <td class="credit-col"><?php echo e(number_format($longliabilitySum, 2)); ?></td>
                        </tr>
                        <tr>
                            <td class="account-col">Short Term Liabilities</td>
                            <td class="debit-col"></td>
                            <td class="credit-col"><?php echo e(number_format($shortliabilitySum, 2)); ?></td>
                        </tr>

                        <!-- CAPITAL & EQUITY SECTION -->
                        <tr class="section-header">
                            <td colspan="3">CAPITAL & EQUITY</td>
                        </tr>
                         <tr>
                            <td class="account-col">Capital A/C</td>
                            <td class="debit-col"></td>
                            <td class="credit-col"><?php echo e(number_format($bankbalance, 2)); ?></td>
                        </tr>
                        <tr>
                            <td class="account-col">Owner's Capital</td>
                            <td class="debit-col"></td>
                            <td class="credit-col"><?php echo e(number_format($ownerscapitalSum, 2)); ?></td>
                        </tr>
                        <tr>
                            <td class="account-col">Investment Reserves</td>
                            <td class="debit-col"></td>
                            <td class="credit-col"><?php echo e(number_format($investmentcapitalSum, 2)); ?></td>
                        </tr>

                        <!-- INCOME SECTION -->
                        <tr class="section-header">
                            <td colspan="3">INCOME</td>
                        </tr>
                        <tr>
                            <td class="account-col">Sales</td>
                            <td class="debit-col"></td>
                            <td class="credit-col"><?php echo e(number_format($salefullTotal, 2)); ?></td>

                        </tr>
                        <tr>

                            <td class="account-col">Sales Return</td>
                            <td class="debit-col"><?php echo e(number_format($salesreturnwithoutvat, 2)); ?></td>
                            <td class="credit-col"></td>
                        </tr>
                        <tr>
                            <td class="account-col">Other Incomes</td>
                            <td class="debit-col"></td>
                            <td class="credit-col"><?php echo e(number_format($totalIncome, 2)); ?></td>
                        </tr>

                        <!-- EXPENSES SECTION -->
                        <tr class="section-header">
                            <td colspan="3">EXPENSES</td>
                        </tr>
                        <tr>
                            <td class="account-col">Purchases</td>
                            <td class="debit-col"><?php echo e(number_format($purchasefullTotal, 2)); ?></td>
                            <td class="credit-col"></td>
                        </tr>
                         <tr>
                            <td class="account-col">Purchase Return</td>
                            <td class="debit-col"></td>
                            <td class="credit-col"><?php echo e(number_format($purchasesreturnwithoutvat, 2)); ?></td>
                        </tr>
                        <tr>
                            <td class="account-col">Other Expenses</td>
                            <td class="debit-col"><?php echo e(number_format($totalexpenses, 2)); ?></td>
                            <td class="credit-col"></td>
                        </tr>

                        <!-- TOTALS ROW -->
                        <?php
                            $totalDebit =  $totaldiscount + $salereturnvat + $salesreturnwithoutvat + $cashinhanddebit + $bankdebit + $credit + $currentassetSum + $fixedassetSum + $purchasevat + $purchasefullTotal + $totalexpenses;
                            $totalCredit = $bankbalance + $totaldiscountpurchase + $purchasereturnvat + $purchasesreturnwithoutvat + $cashinhandcredit + $bankcredit + $supplier + $salevat + $longliabilitySum + $shortliabilitySum + $ownerscapitalSum + $investmentcapitalSum + $salefullTotal + $totalIncome;
                        ?>
                        <tr class="totals-row">
                            <td class="account-col">TOTAL</td>
                            <td class="debit-col"><?php echo e(number_format($totalDebit, 2)); ?></td>
                            <td class="credit-col"><?php echo e(number_format($totalCredit, 2)); ?></td>
                        </tr>
                    </tbody>
                </table>
                <br><br>
            </div>
        </div>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\netplex_26_7\resources\views//chartaccounts/trailbalance.blade.php ENDPATH**/ ?>