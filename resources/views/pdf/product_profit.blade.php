<!DOCTYPE html>
<html>
<head>
    <title>Product Sales Report</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            width: 56mm;
            margin: 0;
            padding: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            padding: 2px;
            border-bottom: 1px solid #ddd;
            word-break: break-word;
        }
        .product-col {
            width: 30%;
        }
        .number-col {
            width: 17.5%;
            text-align: right;
            font-size: 8px;
        }
        .text-right {
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
        .header {
            margin-bottom: 5px;
            text-align: center;
        }
        .header h3 {
            font-size: 11px;
            margin: 2px 0;
        }
        .footer {
            margin-top: 5px;
            text-align: center;
            font-size: 8px;
        }
        .nowrap {
            white-space: nowrap;
        }
           * {
            font-weight: 900; /* Apply bold to all elements */
        }
    </style>
</head>
<body onload="window.print();">
    <div class="header">
        <h3>Product Sales Report</h3>
        <p>{{$company}}</p>
        <p class="nowrap">{{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="product-col">Product</th>
                <th style="display:none;" class="number-col">Buycost</th>
                <th class="number-col">Sold <br>Qty</th>
                <th style="display:none;" class="number-col">Sold <br>Amount</th>
                <th style="display:none;" class="number-col">Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td class="product-col">{{ $product->product_name }}</td>
                <td style="display:none;" class="number-col">{{ $product->rate }}</td>
                <td class="number-col">{{ $product->remain_quantity }}</td>
                <td style="display:none;" class="number-col">{{ number_format($product->total_amount, 3) }}</td>
                <td style="display:none;" class="number-col">{{ number_format($product->profit, 3) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot style="display:none;">
            <tr class="bold">
                <td colspan="3">Total</td>
                <td class="number-col">{{ number_format($totalSold, 3) }}</td>
                <td class="number-col">{{ number_format($totalProfit, 3) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p class="nowrap">Printed at: {{ date('d/m/Y H:i:s') }}</p>
    </div>


</body>
</html>