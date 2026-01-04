<!DOCTYPE html>
<html>
<head>
    <title>Bank Report PDF</title>
    <style>
      body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        h2, h3 {
            text-align: center;
            color: black;
        }
        .header, .footer {
            text-align: center;
        }
        .header {
            border-bottom: 1px solid #ccc;
        }
        .footer {
            margin-top: -4px;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: bold;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ccc;
            text-align: left;
            padding: 10px;
        }
        th {
            background-color: black;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .balance-summary {
            text-align: right;
            font-weight: bold;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Bank Report</h2>
        <h4 style="font-weight: normal;">
            {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M, Y') }}
        </h4>
                <p><b>Branch:</b> {{ $branchname }} | <b>TRN:</b> {{ $cr_num }} | <b>PO Box:</b> {{ $po_box }} | <b>Mob:</b> {{ $tel }} | <b>Address:</b> {{ $admin_address }} </p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Account Name:</td>
            <td>{{ $accountName }}</td>
            <td class="label">Account Number:</td>
            <td>{{ $accountNo }}</td>
        </tr>
        <tr>
            <td class="label">Bank Name:</td>
            <td>{{ $bank_name }}</td>
            <td class="label">Branch Name:</td>
            <td>{{ $branch_name }}</td>
        </tr>
        <tr>
            <td class="label">Code (IFSC/IBAN):</td>
            <td colspan="3">{{ $codeToUse }}</td>
        </tr>
    </table>

    <p class="balance-summary">Opening Balance: {{$currency}} {{ number_format($opening_balance, 3) }}</p>

    <table>
        <thead>
            <tr>
                <th>Transaction Date</th>
                <th>Value Date</th>
                <th>Type</th>
                <th>Party</th>
                <th>Description</th>
                <th>Ref No</th>
                <th>Amount</th>
                <th>Dr/Cr</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->tnx_date }}</td>
                    <td>{{ $transaction->value_date }}</td>
                    <td>{{ $transaction->type }}</td>
                    <td>{{ $transaction->party }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ $transaction->ref_no }}</td>
                    <td>{{$currency}} {{ number_format(abs($transaction->amount), 3) }}</td>
                    <td>{{ $transaction->dr_cr }}</td>
                    <td>{{$currency}} {{ number_format($transaction->balance, 3) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9" class="balance-summary">Closing Balance: {{$currency}} {{ number_format($current_balance, 3) }}</td>
            </tr>
            @if($transactionType != 'credit')
            <tr>
                <td class="balance-summary" colspan="9">Total Debit: {{ number_format($totalDebit, 3) }}</td>
            </tr>
            @endif
            @if($transactionType != 'debit')
            <tr>
                <td class="balance-summary" colspan="9">Total Credit: {{ number_format($totalCredit, 3) }}</td>
            </tr>
            @endif
        </tfoot>

    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i A') }}</p>
    </div>
</body>
</html>
