<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Purchase Order Invoice</title>
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

        /* Print Button Styles */
        .print-button {
            text-align: right;
            padding: 15px;
        }

        .btn {
            display: inline-block;
            padding: 8px 15px;
            margin-left: 10px;
            background: #187f6a;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #146b58;
        }

        /* Header Section */
        .header {
            padding: 20px;
            text-align: center;
        }

        .logo-container {
            margin-bottom: 15px;
        }

        .logo-container img {
            max-width: 220px;
            max-height: 140px;
        }

        .company-info {
            margin-bottom: 20px;
            text-align: center;
        }

        .company-name {
            font-size: 24px;
            font-weight: 600;
            color: #187f6a;
            margin-bottom: 5px;
        }

        /* Document Title */
        .document-title {
            text-align: center;
            padding: 10px;
            background: #187f6a;
            color: white;
            font-size: 18px;
            font-weight: 500;
            margin: 0;
        }

        /* Document Info Section */
        .document-info {
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

        /* Print Styles */
        @media print {
            body {
                visibility: hidden;
                font-size: 9.9px;
                height: 100%;
            }

            .print-container, .print-container * {
                visibility: visible;
            }

            .print-button {
                display: none;
            }

            @page {
                size: A4;
                margin: 0;
            }
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
    <div id="content">
    <div class="container">
        <!-- Print Buttons -->
        <div class="print-button">
            <a href="/purhaseorderreceipt_pdf/{{ $trans }}" class="btn">Download PDF</a>
            <a href="/purhaseorderreceipt_print/{{ $trans }}" class="btn">Print</a>
        </div>

        <!-- Header Section -->
        <div class="header">
            @if ($logo != null)
            <div class="logo-container">
                <img src="{{ asset($logo) }}" alt="Company Logo">
            </div>
            @endif

            <div class="company-info">
                <div class="company-name">{{ $company }}</div>
                <div><b>TRN:</b> {{ $admintrno }} | <b>PO Box:</b> {{ $po_box }}</div>
                <div><b>Branch:</b> {{ $branchname }} | <b>Tel:</b> {{ $tel }}</div>
                <div><b>Address:</b> {{ $Address }}</div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="document-title">PURCHASE ORDER INVOICE</div>

        <!-- Document Info -->
        <div class="document-info">
            <div class="info-block">
                <div><span class="info-label">Invoice No:</span> {{ $trans }}</div>
                <div><span class="info-label">Bill No:</span> {{ $billno }}</div>
                <div><span class="info-label">Supplier:</span> {{ $supplier }}</div>
                @if ($trn_supp != '')
                <div><span class="info-label">TRN:</span> {{ $trn_supp }}</div>
                @endif
            </div>

            <div class="info-block">
                <div><span class="info-label">Invoice Date:</span> {{ $date }}</div>
                @if ($delivery_date != '')
                <div><span class="info-label">Delivery Date:</span> {{ $delivery_date }}</div>
                @endif
                <div><span class="info-label">Payment Type:</span>
                    @if ($payment_type == 1)
                        CASH
                    @elseif ($payment_type == 2)
                        CREDIT
                    @elseif ($payment_type == 3)
                        BANK
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Description</th>
                    <th>Box/Dozen</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Buy Cost</th>
                    <th>{{$tax}} Amt</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $number = 1; ?>
                @foreach ($details as $detail)
                <tr>
                    <td>{{ $number }}</td>
                    <td>{{ $detail->product_name }}</td>
                    <td>
                        @if ($detail->is_box_or_dozen == 1)
                            BOX
                        @elseif ($detail->is_box_or_dozen == 2)
                            Dozen
                        @elseif ($detail->is_box_or_dozen == 3)
                            Quantity
                        @endif
                    </td>
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ $detail->unit }}</td>
                    <td>{{ $detail->buycost }}</td>
                    <td>{{ $detail->price - $detail->price_without_vat }}</td>
                    <td>{{ $detail->price }}</td>
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
                    <td>TOTAL:</td>
                    <td class="text-right">{{ $grandinnumber }} {{ $currency }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <div class="terms">
                The above mentioned goods are received in good condition.
                Goods once purchased will not be returned or exchanged in any condition.
            </div>

            <div class="signature-area">
                <div class="signature-box">
                    Supplier's Signature
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
<script>
    var array = "{{ $trans }}";
    $("#trans_id").val(array);
</script>
