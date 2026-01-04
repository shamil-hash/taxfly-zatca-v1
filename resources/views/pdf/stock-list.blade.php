<!DOCTYPE html>
<html>
<head>
    <title>Stock List</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 10mm;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 10mm;
        }
        th, td {
            padding: 4px 6px;
            text-align: left;
            border: 1px solid #ddd;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .product-col {
            width: 40%;
        }
        .stock-col {
            width: 15%;
            text-align: center;
        }
        .remaining-col {
            width: 15%;
            text-align: center;
        }
        .value-col {
            width: 30%;
            text-align: right;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
        .header {
            margin-bottom: 10mm;
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5mm;
        }
        .header h1 {
            font-size: 20px;
            margin: 2px 0;
        }
        .footer {
            margin-top: 5mm;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 3mm;
            font-size: 10px;
        }
        .nowrap {
            white-space: nowrap;
        }
        * {
            font-weight: 900;
        }
        thead {
            display: table-header-group;
        }
        tfoot {
            display: table-footer-group;
        }
        tr {
            page-break-inside: avoid;
        }
        @media print {
            body {
                font-size: 10pt;
            }
            .page {
                width: 210mm;
                min-height: 297mm;
                padding: 10mm;
                margin: 0 auto;
            }
            .header h1 {
                font-size: 16pt;
            }
            .footer {
                font-size: 8pt;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>Stock List</h1>
            <p class="nowrap">{{ $company }}</p>
            <p class="nowrap">As of: {{ date('d/m/Y H:i:s') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="product-col">Product Name</th>
                    <th class="stock-col">Total Stock</th>
                    <th class="remaining-col">Remaining Stock</th>
                    @if ($branch==0)
                    <th class="value-col">Stock Value</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td class="product-col">{{ $product->product_name }}</td>
                    <td class="stock-col">{{ $product->stock }}</td>
                    <td class="remaining-col">{{ number_format($product->remaining_stock, 3) }}</td>
                    @if ($branch==0)
                    <td class="value-col">{{ number_format(max(0, $product->remaining_stock) * $product->rate, 3) }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
            @if ($branch==0)
            <tfoot>
                <tr class="bold">
                    <td colspan="3">Total Stock Value</td>
                    <td class="text-right">{{ number_format($totalValue, 3) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>

        <div class="footer">
            <p class="nowrap">Printed at: {{ date('d/m/Y H:i:s') }}</p>
            <p>Page 1 of 1</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>