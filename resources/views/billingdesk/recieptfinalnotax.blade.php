<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Invoice Receipt</title>
    @include('layouts/usersidebar')
    <style>
        /* Base Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: #fafafa;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        /* Header Section */
        .header {
            padding: 20px;
            background: #187f6a;
            color: white;
            text-align: center;
        }

        .company-name {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 14px;
            line-height: 1.5;
        }

        /* Invoice Title */
        .invoice-title {
            text-align: center;
            padding: 10px;
            background: #187f6a;
            color: white;
            font-size: 18px;
            font-weight: 500;
            margin: 0;
        }

        /* Invoice Info Section */
        .invoice-info {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .info-block {
            flex: 1;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .items-table th {
            background: #187f6a;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: 500;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Totals Section */
        .totals-section {
            padding: 15px;
            background: #f5f5f5;
            border-radius: 4px;
            margin: 15px;
        }

        .amount-in-words {
            font-style: italic;
            color: #555;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 4px;
            margin: 10px 15px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .totals-table tr:last-child td {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #187f6a;
        }

        /* Bank Details */
        .bank-details {
            padding: 15px;
            background: #f5f5f5;
            border-radius: 4px;
            margin: 15px;
            font-size: 14px;
        }

        /* Footer Section */
        .footer {
            padding: 20px;
            text-align: center;
            border-top: 1px solid #eee;
        }

        .terms {
            font-style: italic;
            color: #777;
            margin-bottom: 20px;
        }

        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-box {
            width: 45%;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            text-align: center;
        }

        /* Buttons */
        .action-buttons {
            text-align: right;
            padding: 15px;
        }

        .btn {
            display: inline-block;
            padding: 8px 15px;
            margin-left: 2px;
            background: #187f6a;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 10px;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #146b58;
        }

        /* Print Styles */
        @media print {
            body {
                visibility: hidden;
            }

            .container, .container * {
                visibility: visible;
            }

            .action-buttons {
                display: none;
            }
        }

        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>

@php
    use App\Models\Softwareuser;
    use Illuminate\Support\Facades\DB;

    $userid = Session('softwareuser');
    $adminid = Softwareuser::Where('id', $userid)
        ->pluck('admin_id')
        ->first();
    $adminroles = DB::table('adminusers')
    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
    ->where('user_id', $adminid)
    ->get();
@endphp

<body>
    <div  id="content">
    <div class="container">
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="/generate-pdf/{{ $trans }}" class="btn">Download PDF</a>
            <a href="/billdeskfinalreciept/{{ $trans }}" class="btn">WITH HEADER</a>
        </div>

        <!-- Header Section -->


        <!-- Invoice Title -->
        <div class="invoice-title">TAX INVOICE</div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="info-block">
                <div><span class="info-label">Invoice No:</span> {{ $trans }}</div>
                <div><span class="info-label">Customer:</span> {{ $custs }}</div>
                <div><span class="info-label">TRN No:</span> {{ $trn_number }}</div>
                <div><span class="info-label">Phone:</span> {{ $billphone }}</div>
                <div><span class="info-label">Email:</span> {{ $billemail }}</div>
                @if($billingAdd!==null)
                <div><span class="info-label">Billing Address:</span> {{ $billingAdd }}</div>
                @endif
                @if($deliveryAdd)
                <div><span class="info-label">Delivery Address:</span> {{ $deliveryAdd }}</div>
                @endif
            </div>

            <div class="info-block">
                <div><span class="info-label">Invoice Date:</span> {{ $date }}</div>
                <div><span class="info-label">Supplied Date:</span> {{ $supplieddate }}</div>
                <div><span class="info-label">Payment Type:</span> {{ $payment_type }}</div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Rate</th>
                    <th>{{$tax}} (%)</th>
                    <th>{{$tax}} Amt</th>
                    <th>Net Rate</th>
                    <th>Discount</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $number = 1; ?>
                @foreach ($details as $detail)
                <tr>
                    <td>{{ $number }}</td>
                    <td>{{ $detail->product_name }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ $detail->unit }}</td>
                    <td>{{ $detail->mrp }}</td>
                    <td>{{ $detail->fixed_vat }}</td>
                    <td>{{ $detail->vat_amount }}</td>
                    <td>{{ $detail->netrate }}</td>
                    <td>{{ $detail->discount_amount * $detail->quantity }}</td>
                    <td>{{ $detail->total_amount }}</td>
                </tr>
                <?php $number++; ?>
                @endforeach
            </tbody>
        </table>

        <!-- Amount in Words -->
        <div class="amount-in-words">
            <strong>Amount in Words:</strong> {{ $amountinwords }}
        </div>

        <!-- Totals Section -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">
                        @if ($vat_type == 1)
                        {{ number_format($rate - $vat, 3) }}
                        @elseif ($vat_type == 2)
                        {{ number_format($rate, 3) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>{{$tax}}:</td>
                    <td class="text-right">{{ $vat }}</td>
                </tr>
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">
                        {{ $discount_amt == null && $Main_discount_amt == null ? 0 : $discount_amt + $Main_discount_amt }}
                    </td>
                </tr>
                @if ($credit_note_amount!=null)
                <tr>
                    <td>Credit Note:</td>
                    <td class="text-right">{{$credit_note_amount}}</td>
                </tr>
                @endif
                <tr>
                    <td>TOTAL:</td>
                    <td class="text-right">{{ $grandinnumber }} {{ $currency }}</td>
                </tr>
            </table>
        </div>

        @if ($bankDetails!==null)
        <!-- Bank Details -->
        <div class="bank-details">
            <div><strong>Beneficiary Bank:</strong> {{ $bankDetails->account_name }}</div>
            <div><strong>Account No:</strong> {{ $bankDetails->account_no }}</div>
            <div><strong>Bank:</strong> {{ $bankDetails->bank_name }}</div>
            @if($bankDetails->branch_name)
            <div><strong>Branch:</strong> {{ $bankDetails->branch_name }}</div>
            @endif
            @if($bankDetails->iban_code)
            <div><strong>IBAN:</strong> {{ $bankDetails->iban_code }}</div>
            @elseif($bankDetails->ifsc_code)
            <div><strong>IFSC:</strong> {{ $bankDetails->ifsc_code }}</div>
            @endif
        </div>
        @endif

        <!-- Footer Section -->
        <div class="footer">
            <div class="terms">
                The above mentioned goods are received in good condition.
                Goods once sold will not be taken back or exchanged in any condition.
            </div>

            <div class="signature-area">
                <div class="signature-box">
                    Seller's Signature
                </div>

                <div class="signature-box">
                    Receiver's Signature
                </div>
            </div>
        </div>
    </div>
    </div>
</body>
</html>
