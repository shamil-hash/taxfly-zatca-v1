<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <title>Invoice</title>
    <style>
        @page {
            size: A5;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            font-size: 10px;
            line-height: 1.3;
        }
        
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        
        .header-table td {
            vertical-align: top;
            padding: 0;
        }
        
        .logo-cell {
            width: 100px;
        }
        
        .logo-placeholder img {
            max-width: 100px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
            color: #187f6a;
        }
        
        .contact-info {
            font-size: 10px;
            line-height: 1.3;
        }
        
        .divider {
            border-top: 1px solid #000;
            margin: 5px 0;
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .trn-number {
            font-size: 10px;
            color: #187f6a;
            font-weight: bold;
        }
        
        .invoice-title {
            font-size: 16px;
            font-weight: bold;
            color: #187f6a;
            text-align: center;
            margin-top: -15px;
                display: block;
        }
        
        .customer-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 1px solid #afafaf;
            font-size: 10px;
        }
        
        .customer-table td {
            padding: 3px 5px;
            border: none;
        }
        
        .field-label {
            font-weight: bold;
            white-space: nowrap;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
            border: 0.5px solid #000;
            font-size: 9px;
            table-layout: fixed;
        }
        
        .details-table th, 
        .details-table td {
            padding: 3px;
            border: 0.5px solid #000;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }
        
        .details-table th {
            background-color: #187f6a;
            color: white;
            font-weight: normal;
        }
        
        .details-table td:nth-child(2) {
            text-align: left;
        }
        
        .total-row td {
            border: 0.5px solid #000;
            padding: 3px;
            font-weight: bold;
        }
        
        .spacer-row1 td {
            height: 20px;
            border: none !important;
            background: transparent;
        }
        
        .bank-details {
            padding: 8px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            max-width: 280px;
            margin-top: 10px;
            font-size: 9px;
        }
        
        .bank-details div {
            margin: 4px 0;
        }
        
        .footer {
            font-size: 9px;
            color: #999;
            text-align: center;
            margin-top: 10px;
            border-top: 1px solid #e0e0e0;
            padding-top: 5px;
        }
        
        /* Column widths */
        .details-table th:nth-child(1),
        .details-table td:nth-child(1) { width: 5%; } /* Sr No */
        .details-table th:nth-child(2),
        .details-table td:nth-child(2) { width: 25%; } /* Description */
        .details-table th:nth-child(3),
        .details-table td:nth-child(3) { width: 8%; } /* Qty/Box */
        .details-table th:nth-child(4),
        .details-table td:nth-child(4) { width: 10%; } /* Rate */
        .details-table th:nth-child(5),
        .details-table td:nth-child(5) { width: 8%; } /* VAT % */
        .details-table th:nth-child(6),
        .details-table td:nth-child(6) { width: 10%; } /* VAT Amount */
        .details-table th:nth-child(7),
        .details-table td:nth-child(7) { width: 12%; } /* Netrate */
        .details-table th:nth-child(8),
        .details-table td:nth-child(8) { width: 10%; } /* Discount */
        .details-table th:nth-child(9),
        .details-table td:nth-child(9) { width: 12%; } /* Total */
    </style>
</head>
<body>
    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <div class="logo-placeholder">
                    <img src="{{$logo}}" style="max-width: 100px; height: auto;">
                </div>
            </td>
            <td style="padding-left: 10px;">
                <div class="company-name">{{$company}}</div>
                <div class="contact-info">
                    Tel: {{ $tel }}<br>
                    <span class="glyphicon glyphicon-envelope"></span> {{ $emailadmin }}<br>
                    <span class="glyphicon glyphicon-globe"></span> {{$Address}}
                </div>
            </td>
        </tr>
    </table>
    <div class="divider"></div>
    
    <!-- Invoice Title -->
    <div class="invoice-header">
        @if($branch!=3 && $branch!=4 && $branch!=5)
        <span class="trn-number">TRN # {{ $admintrno }}</span>
        @endif
        <span class="invoice-title">@if($branch!=3 && $branch!=4 && $branch!=5)TAX @endif INVOICE</span>
    </div>
    
    <!-- Customer Details -->
    <table class="customer-table">
        <tr>
            <td class="field-label">NAME:</td>
            <td colspan="4">{{ $custs }}</td>
            <td class="field-label">INVOICE No.:</td>
            <td>{{ $trans }}</td>
        </tr>
        <tr>
            <td class="field-label">ADDRESS:</td>
            <td colspan="4">{{ $billingAdd }}</td>
            <td class="field-label">Date:</td>
            <td>{{ $date }}</td>
        </tr>
        <tr>
            @if($branch!=3 && $branch!=4 && $branch!=5)
            <td class="field-label">TRN:</td>
            <td colspan="4">{{ $trn_number }}</td>
            @endif
            <td class="field-label">Payment Type:</td>
            <td>{{$payment_type}}</td>
        </tr>
    </table>
    
    <!-- Items Table -->
    <table class="details-table">
        <thead>
            <tr>
                <th>Sr No.</th>
                <th>Description</th>
                <th>Qty/Box</th>
                <th>Rate</th>
                <th>{{$tax}} (%)</th>
                <th>{{$tax}} Amount</th>
                <th>Netrate</th>
                <th>Discount</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($details as $detail)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    {{ $detail->product_name }}
                    @if($detail->record_type == 'return')
                    <span style="color: red;">(Returned)</span>
                    @endif
                </td>
                <td>@if($detail->box_count >0) {{$detail->box_count}} @else {{ $detail->quantity }} @endif</td>
                <td>{{ $detail->mrp }}</td>
                <td>{{ $detail->fixed_vat }}</td>
                <td>{{ $detail->vat_amount }}</td>
                <td>{{ $detail->netrate }}</td>
                <td>{{ $detail->discount_amount * $detail->quantity }}</td>
                <td>{{ $detail->total_amount }}</td>
            </tr>
            @endforeach
            
            @php
            $rowCount = is_countable($details ?? []) ? count($details ?? []) : 0;
            $maxHeight = 400;
            $deductionPerRow = 40;
            $calculatedHeight = max(0, $maxHeight - ($deductionPerRow * $rowCount));
            @endphp
            
            @if ($rowCount <= 10)
            <tr class="spacer-row1">
                @for ($i = 0; $i < 9; $i++)
                <td>&nbsp;</td>
                @endfor
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: left;">Amount in Words: <strong>{{ $amountinwords }}</strong></td>
                <td colspan="3" style="text-align: right;">SUB TOTAL (AED)</td>
                <td>
                    @if ($vat_type == 1)
                    {{ number_format($rate - $vat, 3) }}
                    @elseif ($vat_type == 2)
                    {{ number_format($rate, 3) }}
                    @endif
                </td>
            </tr>
            <tr class="total-row">
                <td colspan="5"></td>
                <td colspan="3" style="text-align: right;">{{$tax}} (AED)</td>
                <td>{{ $vat }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="5"></td>
                <td colspan="3" style="text-align: right;">Discount (AED)</td>
                <td>{{ $discount_amt == null && $Main_discount_amt == null ? 0 : $discount_amt + $Main_discount_amt }}</td>
            </tr>
            @if ($returntotal>0)
            <tr class="total-row">
                <td colspan="5"></td>
                <td colspan="3" style="text-align: right;">Return (AED)</td>
                <td>{{$returntotal}}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="5"></td>
                <td colspan="3" style="text-align: right; font-weight: bold;">Grand Total (AED)</td>
                <td style="font-weight: bold;">{{ $grandinnumber }}</td>
            </tr>
        </tfoot>
    </table>
    
    <!-- Bank Details -->
    @if ($bankDetails !== null)
    <div class="bank-details">
        <div style="font-weight: bold; border-bottom: 1px solid #f0f0f0; padding-bottom: 4px; margin-bottom: 4px;">
            Bank Details
        </div>
        <div>
            <span style="font-weight: bold;">Account Name:</span> {{ $bankDetails->account_name }}
        </div>
        <div>
            <span style="font-weight: bold;">Account number:</span> {{ $bankDetails->account_no }}
        </div>
        <div>
            <span style="font-weight: bold;">Bank Name:</span> {{ $bankDetails->bank_name }}
        </div>
        <div>
            <span style="font-weight: bold;">IBAN:</span> {{ $bankDetails->iban_code }}
        </div>
    </div>
    @endif
    
    <!-- Footer -->
    <div class="footer">
        <strong>Electronically Generated Invoice</strong><br>
        Invoice generated on: {{ $date }}
    </div>
</body>
</html>