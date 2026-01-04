<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Statement - {{ $credit_supplier_name }}</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #333;
            padding: 1cm; /* Standard PDF margin */
            margin: 0;
        }
        
        /* Main Container */
        .document {
            width: 100%;
            max-width: 21cm; /* A4 width */
            margin: 0 auto;
        }
        
        /* Header Section */
        .header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .logo img {
            max-height: 60px;
            max-width: 100%;
        }
        
        .info-columns {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
       
        
        .supplier-info {
            padding-left: 15px;
        }
        
        /* Content Styles */
        .statement-title {
            font-size: 14pt;
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .statement-period {
            text-align: center;
            margin-bottom: 15px;
            font-style: italic;
        }
        
        /* Transaction Table */
        .transaction-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        
        .transaction-table th {
             background-color: #187f6a;
            color:white;
            font-weight: bold;
            text-align: center;
            padding: 5px;
            border: 1px solid #ddd;
        }
        
        .transaction-table td {
            padding: 5px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Row Styles */
        .highlight-row {
            background-color: #f9f9f9;
        }
        
        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        /* Summary Section */
        .summary {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        
        .summary-table {
            width: 100%;
            max-width: 300px;
            margin-left: auto;
            border-collapse: collapse;
            font-size: 9pt;
        }
        
        .summary-table td {
            padding: 5px;
            border: 1px solid #ddd;
        }
        
        .summary-table td:last-child {
            text-align: right;
            font-weight: bold;
        }
        
        /* Footer */
        .footer {
            text-align: right;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-style: italic;
            font-size: 8pt;
        }
        
        /* Print Specific Styles */
        @media print {
            body {
                padding: 1.5cm !important;
                font-size: 9pt;
            }
            
            .footer {
                position: fixed;
                bottom: 0.5cm;
                right: 1.5cm;
            }
            
            .transaction-table {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="document">
        <!-- Header Section -->
       <div class="header" style="padding: 20px; font-family: Arial, sans-serif;">
    <!-- Company Information - Centered -->
    <div class="company-info" style="text-align: center; margin-bottom: 30px;">
        @if (Session('adminuser'))
            <div style="font-weight: bold; margin-bottom: 10px; font-size: 18px;">{{ strtoupper($adminname) }}</div>
        @elseif(Session('softwareuser'))
            <div style="font-weight: bold; margin-bottom: 10px; font-size: 18px;">{{ strtoupper($company) }}</div>
        @endif
        
        <!-- All details in a row - centered -->
        <div style="display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 10px; text-align: center;">
            @if (Session('adminuser'))
                <div>{!! nl2br(e(strtoupper($admin_address))) !!}</div>
            @elseif(Session('softwareuser'))
                <div><strong>Address:</strong> {{ ucfirst($Address) }}</div>
            @endif
            
            @if ($admintrno)
                <div><strong>TRN:</strong> {{ $admintrno }}</div>
            @endif
            
            @if ($po_box)
                <div><strong>PO Box:</strong> {{ $po_box }}</div>
            @endif
            
            @if ($tel)
                <div><strong>Phone:</strong> {{ $tel }}</div>
            @endif
        </div>
    </div>
    
    <!-- Customer Information -->
    <div class="customer-info" style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; background-color: #f9f9f9;">
        <div style="font-weight: bold; margin-bottom: 15px; font-size: 16px; text-align: center;">SUPPLIER ACCOUNT STATEMENT</div>
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div><strong>Name:</strong> {{ $credit_supplier_name }}</div>
            <div><strong>Branch:</strong> {{ $credit_branchname }}</div>
            <div><strong>Phone:</strong> {{ $credit_phone }}</div>
            <div><strong>Email:</strong> {{ $credit_email }}</div>
        </div>
    </div>
</div>
        
        <!-- Statement Title -->
    
        
        <!-- Transaction Table -->
        <table class="transaction-table">
            <thead>
                <tr>
                    <th width="4%">#</th>
                    <th width="8%">Date</th>
                    <th width="10%">Bill No.</th>
                    <th width="12%">Remark</th>
                    <th width="8%">Payment</th>
                    <th width="12%">Transaction Type</th>
                    <th width="10%" class="text-right">Purchase +</th>
                    <th width="10%" class="text-right">Payment -</th>
                    <th width="10%" class="text-right">Return -</th>
                    <th width="16%" class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sl = 1;
                    $totalPurchase = 0;
                    $totalPayment = 0;
                    $totalReturn = 0;
                    $currentBalance = 0;
                    $totalcancel=0;
                    
                    // Sort transactions by date
                    usort($allTransactions, function($a, $b) {
                        return strtotime($a['created_at']) - strtotime($b['created_at']);
                    });
                    
                    // Calculate opening balance (transactions before the statement period)
                    $openingBalance = $credit_first_due ?? 0;
                @endphp
                
                <!-- Opening Balance Row -->
                <tr class="highlight-row">
                    <td colspan="6"><strong>Opening Balance</strong></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right">{{ $currency }} {{ number_format($openingBalance, 2) }}</td>
                </tr>
                
                @foreach($allTransactions as $transaction)
                    @php
                        $isCredit = isset($transaction['credit_supplier_id']);
                        $rowClass = $sl % 2 === 0 ? 'highlight-row' : '';
                        
                        $purchaseAmount = 0;
                        $paymentAmount = 0;
                        $returnAmount = 0;
                        $cancelamount=0;
                        
                        if($isCredit) {
                            if ($transaction['comment'] == 'Bill') {
                                $purchaseAmount = $transaction['Invoice_due'] ?? 0;
                                $totalPurchase += $purchaseAmount;
                            }
                            if ($transaction['comment'] == 'Payment Made' || $transaction['comment'] == 'Bill') {
                                $paymentAmount = $transaction['collectedamount'] ?? 0;
                                $totalPayment += $paymentAmount;
                            }
                            if ($transaction['comment'] == 'Purchase Returned') {
                                $returnAmount = $transaction['collectedamount'] ?? 0;
                                $totalReturn += $returnAmount;
                            }
                             if ($transaction['comment'] == 'Payment Cancelled') {
                                $cancelamount = $transaction['collectedamount'] ?? 0;
                                $totalcancel += $cancelamount;
                            }
                            $currentBalance = $transaction['updated_balance'] ?? $currentBalance;
                        } else {
                            if ($transaction['comment'] == 'Bill') {
                                $purchaseAmount = $transaction['collected_amount'] ?? 0;
                                $totalPurchase += $purchaseAmount;
                                $paymentAmount = $transaction['collected_amount'] ?? 0;
                                $totalPayment += $paymentAmount;
                            }
                            if ($transaction['comment'] == 'Purchase Returned') {
                                $returnAmount = $transaction['collected_amount'] ?? 0;
                                $totalReturn += $returnAmount;
                            }
                        }
                    @endphp
                    
                    <tr class="{{ $rowClass }}">
                        <td class="text-center">{{ $sl++ }}</td>
                        <td>{{ date('d-m-Y', strtotime($transaction['created_at'])) }}</td>
                        <td>{{ $transaction['reciept_no'] }}</td>
                        <td>{{ $transaction['note'] ?? ($isCredit ? 'Credit Transaction' : 'Regular Transaction') }}</td>
                        <td>
                            @if($isCredit)
                                Credit
                            @else
                                @switch($transaction['payment_type'])
                                    @case(1) Cash @break
                                    @case(3) Bank @break
                                    @default N/A
                                @endswitch
                            @endif
                        </td>
                        <td>{{ $transaction['comment'] }} <br>
                         @if ($transaction['comment'] == 'Payment Made')
                             @if($transaction['payment_type'] == 1 || $transaction['payment_type'] == '')
                                Cash
                            @elseif($transaction['payment_type'] == 2)
                                Cheque
                                @if(isset($transaction['check_number'])) - {{ $transaction['check_number'] }} @endif
                                @if(isset($transaction['depositing_date'])) - {{ $transaction['depositing_date'] }} @endif
                            @elseif($transaction['payment_type'] == 3)
                                Bank - {{ $transaction['account_name'] ?? '' }}
                                @if(isset($transaction['reference_number'])) - {{ $transaction['reference_number'] }} @endif
                            @endif
                            @endif
                            
                             @if ($transaction['comment'] == 'Payment Cancelled')
                            {{$currency}} {{ $transaction['collectedamount'] }}
                            @endif
                            </td>
                        <td class="text-right">
                            @if($purchaseAmount > 0)
                                {{ $currency }} {{ number_format($purchaseAmount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if($paymentAmount > 0)
                                {{ $currency }} {{ number_format($paymentAmount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if($returnAmount > 0)
                                {{ $currency }} {{ number_format($returnAmount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if($isCredit)
                                {{ $currency }} {{ number_format($currentBalance, 2) }}
                            @else
                                {{ $currency }} {{ number_format($currentBalance, 2) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                
                <!-- Totals Row -->
                <tr class="total-row">
                    <td colspan="6" class="text-right"><strong>Totals</strong></td>
                    <td class="text-right"><strong>{{ $currency }} {{ number_format($totalPurchase, 2) }}</strong></td>
                    <td class="text-right"><strong>{{ $currency }} {{ number_format($totalPayment, 2) }}</strong></td>
                    <td class="text-right"><strong>{{ $currency }} {{ number_format($totalReturn, 2) }}</strong></td>
                    <td class="text-right"></td>
                </tr>
                
                <!-- Closing Balance Row -->
                <tr class="total-row">
                    <td colspan="9" class="text-right"><strong>Closing Balance</strong></td>
                    <td class="text-right"><strong>{{ $currency }} {{ number_format($currentBalance, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
        
        <!-- Summary Section -->
        <div class="summary">
            <table class="summary-table">
                <tr>
                    <td>Opening Balance</td>
                    <td>{{ $currency }} {{ number_format($credit_first_due, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Purchases</td>
                    <td>{{ $currency }} {{ number_format($totalPurchase, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Payments Made</td>
                    <td>{{ $currency }} {{ number_format($totalPayment, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Payments Cancelled</td>
                    <td>{{ $currency }} {{ number_format($totalcancel, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Returns</td>
                    <td>{{ $currency }} {{ number_format($totalReturn, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Closing Balance</strong></td>
                    <td><strong>{{ $currency }} {{ number_format($currentBalance, 2) }}</strong></td>
                </tr>
            </table>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            Generated on: {{ date('d M Y, h:i A') }}<br>
        </div>
    </div>
</body>
</html>