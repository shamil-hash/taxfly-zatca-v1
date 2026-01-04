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
        }
        .company-name {
            font-size: 7px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 0.5mm;
            line-height: 1;
            text-align: center;
        }
        .product-name {
        font-size: 18px; /* Increased from 13px */
        max-width: 100%;
        font-weight: bold;
        margin-bottom: 0.5mm;
        white-space: nowrap;
        overflow: hidden; /* Changed from visible to hidden */
        text-overflow: ellipsis; /* Changed from clip to ellipsis for better overflow handling */
        text-align: center;
        display: block;
        /* Adjust the clamp values to your preference */
        font-size: clamp(9px, 3vw, 18px); /* Increased minimum and maximum values */
        line-height: 1.2; /* Added for better text appearance */
    }
        .barcode-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.5mm;
        }
        .product-code {
            font-size: 8px;
            font-weight: bold;
            text-align: left;
            padding-left: 3mm;
        }
        .barcode {
            width: 90%;
            height: 12mm;
            margin: 0 auto;
            image-rendering: crisp-edges;
        }
        .barcode-text {
            font-size: 7px;
            font-family: 'Monospace', sans-serif;
            letter-spacing: 0.5px;
            line-height: 1;
            text-align: right;
            padding-right: 3mm;
            font-weight: bold;

        }
        .price {
            font-size: 18px;
            font-weight: bold;
            margin-top: 0.5mm;
            line-height: 1;
            color: #000;
            text-align: left;
            padding-left: 3mm;
        }
        .currency {
            font-size: 10px;
        }
        .barcode-info {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 0.5mm;
        }
    </style>
    <script>
    window.onload = function() {
        window.print();
    };
   
</script>
</head>
<body>
    @foreach ($barcodes as $barcode)
        @if (!empty($barcode))
            <div class="barcode-container">
                <div class="company-name">{{ $company }}</div>
                    <div class="product-name">{{ $product_name_o }}</div>
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcode, 'C128', 1.8, 40) }}" class="barcode" alt="{{ $barcode }}">
                    <div class="barcode-info">
                <div class="product-code">{{ $product_code }}</div>
                <div class="barcode-text">{{ $barcode }}</div>
            </div>
                <div class="price"><span class="currency">{{$currency}}</span> {{ $selling_cost }}</div>
            </div>
        @endif
    @endforeach
</body>
</html>