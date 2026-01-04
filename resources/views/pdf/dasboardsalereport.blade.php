<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - {{ $date }}</title>
    <style>
        @page {
            size: 58mm auto; /* Set the page width to 58mm and height to auto */
                        margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: 900;
            margin: 0;
        }

        .container {
            width: 58mm;
            margin: 0 auto;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h2 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12px;
            margin: 3px 0;
        }

        .sales-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 12px;
        }

        .sales-table th,
        .sales-table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
            word-wrap: break-word;
        }

        .sales-table th {
            background-color: #f2f2f2;
            font-weight: 900;
        }

        .sales-table td {
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .total-container {
            margin-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            font-weight: bold;
        }

        .total-container div {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }

        @media print {
            .container {
                width: 100%;
                padding: 0;
            }
        }

        .num {
            display: none;
        }

    </style>
</head>
<body onload="window.print();">
    <div class="container">
        <div class="header">
            <h2>{{ $company }}</h2>
            <p>{{ $Address }}</p>
            <p>{{ $tel }}</p>
            <p>Sales Report for: {{ $date }}</p>
        </div>

        <table class="sales-table">
            <thead>
                <tr>
                    <th><b>Product</b></th>
                    <th><b>Quantity</b></th>
                    <th><b>{{ $tax }}</b></th>
                    <th><b>Total Amount</b></th>
                </tr>
            </thead>
            <tbody>
                @php
                $totalAmount = 0;
                $vat = 0;
                $number = 1;
                @endphp
                @foreach ($salesData as $sale)
                    <tr>
                        <td class="num">{{ $number }}</td>
                        <td>{{ $sale->product_name }}</td>
                        <td>{{ number_format($sale->quantity, 3) }}</td>
                        <td>{{ number_format($sale->vat_amount, 3) }}</td>
                        <td>{{ number_format($sale->final_amount, 3) }}</td>
                    </tr>
                    @php
                    $totalAmount += $sale->final_amount;
                    $vat += $sale->vat_amount;
                    $number++;
                    @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: left;">
                        <p>No. of Items: {{ $number - 1 }}</p>
                    </td>
                    <td>
                        <p>Sub Total</p>
                        <p>Discount</p>
                        <p>{{ $tax }}</p>
                        <p>Grand Total</p>
                    </td>
                    <td>
                        <p>{{ $currency }} {{ number_format(($totalAmount-$vat+$totalDiscountAmount->total_discount_amount), 3) }}</p>
                        <p>{{ $currency }} {{ number_format(($totalDiscountAmount->total_discount_amount ?? 0), 3) }}</p>
                        <p>{{ $currency }} {{ number_format($vat, 3) }}</p>
                        <p>{{ $currency }} {{ number_format($totalAmount, 3) }}</p>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
