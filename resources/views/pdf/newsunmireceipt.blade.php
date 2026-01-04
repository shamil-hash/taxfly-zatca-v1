<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice - {{ $trans }}</title>
    <style>
        @page {
            size: 58mm auto; /* Set the page width to 58mm and height to auto */
            margin: 0; /* Remove default margins for a clean fit */
        }

        body {
            font-family: 'Courier New', monospace;
            margin: 0; /* Remove body margins to match the print page */
            font-size: 14px; /* Base font size for the body */
        }

        .receipt {
            width: 58mm; /* Set the width of the receipt */
            margin: 0 auto; /* Center the content for the set page size */
            padding: 0;
        }

        .heading {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .subheading h6 {
            font-size: 12px;
            margin: 3px 0;
            text-align: center;
            font-weight: normal;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .custom-table th, .custom-table td {
            padding: 5px;
            font-size: 12px;
            text-align: left;
        }

        .total-section {
            border-top: 1px dashed #000;
            margin-top: 10px;
            padding-top: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .total-section span {
            display: inline-block;
            width: 60%; /* Adjust the width of the labels */
            text-align: left;
        }

        .total-section .value {
            display: inline-block;
            width: 35%; /* Adjust the width of the values */
            text-align: right;
        }

        .welcome h1 {
            font-size: 14px;
            font-weight: normal;
            text-align: center;
            margin-top: 15px;
        }

        @media print {
            .receipt {
                width: 100%;
            }
        }

        .spacer td {
            padding: 5px;
            border: none;
        }
        .dotted-line {
            border-bottom: 1px dashed black;
                    margin: 10px 0;
    }
    * {
    font-weight: 900; /* Apply bold to all elements */
}
    </style>
</head>
<body onload="window.print();">
    <div class="receipt">
        <div class="heading">{{ strtoupper($admin_name) }}</div>
        <div class="subheading">
            <h6>TRN: {{ $trn_number }}</h6>
            <h6>@if ($po_box != '') PO BOX: {{ $po_box }} @endif</h6>
            <h6>Branch: {{ $branchname }}</h6>
            <h6>@if ($billphone != '') Mob: {{ $billphone }} @endif</h6>
                <h6>Customer: {{ $custs }}</h6>
            <h6>Invoice Date: {{ $date }}</h6>
            <h6>Payment Type: {{ $payment_type }}</h6>
            <h6><b>Invoice No: {{ $trans }}</b></h6>
        </div>

        <h1 style="text-align: center;">Tax Invoice</h1>
        <div class="dotted-line"></div> <!-- Dotted line added -->

        <table class="custom-table">
            <thead >
                <tr class="spacer"><td colspan="2"></td></tr>
                <tr>
                    <th>Product</th>
                    <th>Sale</th>
                </tr>
                <tr style="border-bottom: 1px dashed black;" class="spacer"><td colspan="2"></td></tr>
            </thead>
            <tbody>
                <tr class="spacer"><td colspan="2"></td></tr>
                @foreach ($details as $detail)
                <tr>
                    <td>{{ $detail->product_name }} x {{ number_format($detail->quantity, 3) }} {{$detail->unit}}</td>
                    <td style="white-space: nowrap;">{{number_format( $detail->total_amount,3) }}</td>
                </tr>
                @endforeach
                <tr class="spacer"><td colspan="2"></td></tr>
            </tbody>
        </table>

        <div class="total-section" style="border-bottom: 1px dashed black;">
            <br>
                        <p><span>Sub Total:</span>
                <span class="value">
                    @if ($vat_type == 1)
                        {{ number_format($rate - $vat, 3) }}
                    @elseif ($vat_type == 2)
                        {{ number_format($rate, 3) }}
                    @endif
                </span>
            </p>
            <p><span>VAT:</span> <span class="value">{{number_format( $vat,3) }}</span></p>
            @if ($discount_amt > 0 || $Main_discount_amt > 0)
            <p><span>Discount:</span> <span class="value">{{ number_format($discount_amt + $Main_discount_amt, 3) }}</span></p>
            @endif
            <p><span>Total:</span> <strong class="value">{{ $currency }} {{ number_format($grandinnumber, 3) }}</strong></p>
            <br>
        </div>

        <div class="welcome">
            <h1>*** THANK YOU VISIT AGAIN ***</h1>
        </div>
    </div>
</body>
</html>
