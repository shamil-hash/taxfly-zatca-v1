<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Barcode Print</title>
    <style>
        @page {
            size: 38.1mm 25.4mm;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 0;
            margin: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .barcode-container {
            width: 38.1mm;
            height: 25.4mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1mm;
            box-sizing: border-box;
            page-break-after: always;
            overflow: hidden; /* Prevent content bleeding */
        }
        .company-name {
            font-size: 7px;
            font-weight: bold;
            text-decoration: underline;
            line-height: 1;
            text-align: center;
            padding-left: 1mm;
            margin: 0;
            flex-shrink: 0;
        }
          .product-name {
            font-size: 18px; /* Reduced from 13px */
            font-weight: bold;
            text-align: center;
            display: -webkit-box;
            -webkit-line-clamp: 2; /* Limit to 2 lines */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.1;
            margin: 0.5mm 0;
            padding: 0 1mm;
            flex-shrink: 0;
            max-height: 15px; /* Fixed height for consistency */
            font-size: clamp(9px, 3vw, 18px);
        }
        .barcode {
            width: 90%;
            height: 10mm; /* Reduced from 12mm */
            margin: 0 auto;
            image-rendering: crisp-edges;
            flex-grow: 1;
        }
        .barcode-info {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 0.5mm;
            flex-shrink: 0;
        }
        .product-code {
            font-size: 8px;
            font-weight: bold;
            text-align: left;
            padding-left: 1mm;
        }
        .barcode-text {
            font-size: 7px;
            font-family: 'Monospace', sans-serif;
            letter-spacing: 0.5px;
            line-height: 1;
            text-align: right;
            padding-right: 1mm;
            font-weight: bold;
        }
        .price {
            font-size: 16px; /* Reduced from 18px */
            font-weight: bold;
            line-height: 1;
            color: #000;
            text-align: left;
            padding-left: 1mm;
            flex-shrink: 0;
        }
        .currency {
            font-size: 9px; /* Reduced from 10px */
        }
    </style>
     <script>
        window.onload = function() {
            window.print();
        };
        

    </script>
</head>

<body >
    @foreach ($barcodeData as $item)
        @if (!empty($item['barcode']))
            @for ($i = 0; $i < $item['quantity']; $i++)
                <div class="barcode-container">
                    <div class="company-name">{{ $company }}</div>
                    <div class="product-name" title="{{ $item['product_name'] }}">
                        {{ $item['product_name'] ?: 'No Name' }}
                    </div>
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($item['barcode'], 'C128', 1.8, 40) }}" class="barcode" alt="{{ $item['barcode'] }}">
                    <div class="barcode-info">
                        <div class="product-code">{{ $item['product_code'] }}</div>
                        <div class="barcode-text">{{ $item['barcode'] }}</div>
                    </div>
                    <div class="price"><span class="currency">{{$currency}}</span> {{ number_format($item['selling_cost'], 2) }}</div>
                </div>
            @endfor
        @endif
    @endforeach
</body>
</html>
