<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day Book Report</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 20px;
            padding: 0;
            color: #333;
            line-height: 1.5;
            max-width: 1100px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .date-range {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #f0f0f0;
            padding: 8px 12px;
            font-size: 16px;
            font-weight: bold;
            border-left: 4px solid #555;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 14px;
        }
        th {
            background-color: #e6e6e6;
            padding: 10px 8px;
            text-align: left;
            border: 1px solid #d0d0d0;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border: 1px solid #d0d0d0;
        }
        tfoot tr {
            background-color: #f7f7f7;
            font-weight: bold;
        }
        .amount {
            text-align: right;
        }
        .total-row {
            background-color: #f0f0f0;
        }
        .page-number {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company_name }}</div>
        <div class="report-title">Day Book Report</div>
        <div class="date-range">Period: {{ date('d-M-Y', strtotime($start_date)) }} to {{ date('d-M-Y', strtotime($end_date)) }}</div>
    </div>

    <div class="section">
        <div class="section-title">Sales</div>
        <table>
            <thead>
                <tr>
                    <th width="25%">Date</th>
                    <th width="45%">Customer</th>
                    <th width="30%" class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($sale->created_at)) }}</td>
                        <td>{{ $sale->id }}</td>
                        <td class="amount">{{ number_format($sale->total_amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2"><strong>Total Sales</strong></td>
                    <td class="amount"><strong>{{ $currency }} {{ number_format($total_sales, 3) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Sales Return</div>
        <table>
            <thead>
                <tr>
                    <th width="25%">Date</th>
                    <th width="45%">Invoice No</th>
                    <th width="30%" class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales_return as $sale_return)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($sale_return->created_at)) }}</td>
                        <td>{{ $sale_return->id }}</td>
                        <td class="amount">{{ number_format($sale_return->total_amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2"><strong>Total Sales Return</strong></td>
                    <td class="amount"><strong>{{ $currency }} {{ number_format($total_return_sales, 3) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Purchases</div>
        <table>
            <thead>
                <tr>
                    <th width="25%">Date</th>
                    <th width="45%">Supplier</th>
                    <th width="30%" class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchases as $purchase)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($purchase->created_at)) }}</td>
                        <td>{{ $purchase->id }}</td>
                        <td class="amount">{{ number_format($purchase->total_amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2"><strong>Total Purchases</strong></td>
                    <td class="amount"><strong>{{ $currency }} {{ number_format($total_purchases, 3) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Purchases Return</div>
        <table>
            <thead>
                <tr>
                    <th width="25%">Date</th>
                    <th width="45%">Supplier</th>
                    <th width="30%" class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchases_return as $purchase_return)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($purchase_return->created_at)) }}</td>
                        <td>{{ $purchase_return->id }}</td>
                        <td class="amount">{{ number_format($purchase_return->total_amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2"><strong>Total Purchases Return</strong></td>
                    <td class="amount"><strong>{{ $currency }} {{ number_format($total_return_purchase, 3) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div class="section">
        <div class="section-title">Service</div>
        <table>
            <thead>
                <tr>
                    <th width="25%">Date</th>
                    <th width="45%">Service Name</th>
                    <th width="30%" class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($service as $services)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($services->created_at)) }}</td>
                        <td>{{ $services->id }}</td>
                        <td class="amount">{{ number_format($services->total_amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2"><strong>Total Service</strong></td>
                    <td class="amount"><strong>{{ $currency }} {{ number_format($total_service, 3) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Receipt</div>
        <table>
            <thead>
                <tr>
                    <th width="25%">Date</th>
                    <th width="45%">Customer</th>
                    <th width="30%" class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receiptcustomer as $receiptcustomers)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($receiptcustomers->created_at)) }}</td>
                        <td>{{ $receiptcustomers->id }}</td>
                        <td class="amount">{{ number_format($receiptcustomers->amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2"><strong>Total Receipt</strong></td>
                    <td class="amount"><strong>{{ $currency }} {{ number_format($total_receiptcustomer, 3) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>


    <div class="section">
        <div class="section-title">Payment</div>
        <table>
            <thead>
                <tr>
                    <th width="25%">Date</th>
                    <th width="45%">Supplier</th>
                    <th width="30%" class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paymentcustomer as $paymentcustomers)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($paymentcustomers->created_at)) }}</td>
                        <td>{{ $paymentcustomers->id }}</td>
                        <td class="amount">{{ number_format($paymentcustomers->amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2"><strong>Total Payment</strong></td>
                    <td class="amount"><strong>{{ $currency }} {{ number_format($total_paymentcustomer, 3) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Expenses</div>
        <table>
            <thead>
                <tr>
                    <th width="25%">Date</th>
                    <th width="45%">Expense</th>
                    <th width="30%" class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $expense)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($expense->date)) }}</td>
                        <td>{{ $expense->expense_name }}</td>
                        <td class="amount">{{ number_format($expense->amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2"><strong>Total Expenses</strong></td>
                    <td class="amount"><strong>{{ $currency }} {{ number_format($total_expenses, 3) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Incomes</div>
        <table>
            <thead>
                <tr>
                    <th width="25%">Date</th>
                    <th width="45%">Income</th>
                    <th width="30%" class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incomes as $income)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($income->date)) }}</td>
                        <td>{{ $income->income_name }}</td>
                        <td class="amount">{{ number_format($income->amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2"><strong>Total Incomes</strong></td>
                    <td class="amount"><strong>{{ $currency }} {{ number_format($total_incomes, 3) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

</body>
</html>