<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt Voucher</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .voucher-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0;
            text-decoration: underline;
        }
        .voucher-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .voucher-details td, .voucher-details th {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .voucher-details th {
            background-color: #f2f2f2;
            text-align: left;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #000;
            padding-top: 10px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            text-align: center;
            margin-top: 50px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .payment-details {
            margin-top: 10px;
            padding: 8px;
            background-color: #f9f9f9;
            border: 1px dashed #ccc;
        }
        .amount-cell {
            font-weight: bold;
        }
        .balance-info {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0f8ff;
            border: 1px solid #d1e7ff;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company->company }}</div>
        <div>{{ $company->address }}</div>
        <div>Tel: {{ $company->tel }} | Email: {{ $company->emailadmin }}</div>
        <div>TRN: {{ $company->admintrno }}</div>
    </div>

    <div class="voucher-title">SUPPLIER PAYMENT VOUCHER</div>
    
    <table class="voucher-details">
        <tr>
            <th width="20%">Voucher No:</th>
            <td width="30%">{{ $voucherNumber }}</td>
            <th width="20%">Date:</th>
            <td width="30%">{{ $date }}</td>
        </tr>
        <tr>
            <th>Supplier Name:</th>
            <td>{{ $suppliers->name }}</td>
            <th>Time:</th>
            <td>{{ $time }}</td>
        </tr>
        <tr>
            <th>Supplier TRN:</th>
            <td>{{ $suppliers->trn_number ?? 'N/A' }}</td>
            <th>Payment Method:</th>
            <td><strong>{{ $paymentType }}</strong></td>
        </tr>
        <tr>
            <th>Supplier Address:</th>
            <td colspan="3">{{ $suppliers->billing_add }}</td>
        </tr>
    </table>

    <table class="voucher-details">
        <tr>
            <th width="70%">Payment Details</th>
            <th width="30%" class="text-right">Amount {{$currency}}</th>
        </tr>
        <tr>
            <td>
                Payment Received
                <div class="payment-details">
                   @if($paymentType == 'Cash' || $paymentType == null)
                        <strong>Cash Payment</strong><br>
                        Received in person
                    @elseif($paymentType == 'Cheque')
                        <strong>Cheque Details:</strong><br>
                        Cheque No: {{ $transaction->check_number ?? 'N/A' }}<br>
                        Deposit Date: {{ $transaction->depositing_date ? \Carbon\Carbon::parse($transaction->depositing_date)->format('d/m/Y') : 'N/A' }}<br>
                    @elseif($paymentType == 'Bank Transfer')
                        <strong>Bank Transfer Details:</strong><br>
                        Account Name: {{ $transaction->account_name ?? 'N/A' }}<br>
                        Bank: {{ $transaction->bank_name ?? 'N/A' }}<br>
                        Transfer Date: {{ $transaction->transfer_date ? \Carbon\Carbon::parse($transaction->transfer_date)->format('d/m/Y') : 'N/A' }}<br>
                        Reference No: {{ $transaction->reference_number ?? 'N/A' }}<br>
                    @endif
                </div>
            </td>
            <td class="text-right amount-cell">{{ number_format($transaction->collectedamount, 2) }}</td>
        </tr>
        <tr>
            <th class="text-right">Total Amount:</th>
            <th class="text-right amount-cell">{{ number_format($transaction->collectedamount, 2) }}</th>
        </tr>
    </table>
    <div class="balance-info">
        <strong>Supplier Account Balance:</strong> 
        {{ number_format($balance, 2) }} {{$currency}}
    </div>

    <div class="footer">
        <div>Prepared by: ___________________</div>
        <div>Supplier Acknowledgement: ___________________</div>
    </div>

    <div class="signature">
        <div>Authorized Signature & Company Stamp</div>
        <div style="margin-top: 30px;">_________________________</div>
    </div>
    
    <div class="text-center" style="margin-top: 20px; font-size: 10px;">
        This is a computer generated receipt and does not require a physical signature
    </div>
</body>
</html>