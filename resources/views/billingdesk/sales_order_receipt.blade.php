<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Receipt</title>
    @include('layouts/usersidebar')
    <style>
        /* Base Styles */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
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

    if ($page == 'sales_order' || $page == 'salesorderdraft' || $page == 'quot_to_salesorder') {
        $text = 'SALES ORDER';
    } elseif ($page == 'quotation' || $page == 'quotationdraft' || $page == 'clone_quotation') {
        $text = 'QUOTATION';
    } elseif ($page == 'performance_invoice' || $page == 'performadraft') {
        $text = 'PROFORMA INVOICE';
    }
@endphp

<body>
    <div id="content">
    <div class="container">
        <!-- Print Buttons -->
        <div class="print-button">
            <a href="/salesorderreceipt_pdf/{{ $page }}/{{ $trans }}" class="btn">Download PDF</a>
            <a href="/salesorderreceipt_print/{{ $page }}/{{ $trans }}" class="btn">Print</a>
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
                <div>TRN: {{ $admintrno }} | PO Box: {{ $po_box }}</div>
                <div>Branch: {{ $branchname }} | Tel: {{ $tel }}</div>
                <div>Address: {{ $Address }}</div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="document-title">{{ $text }}</div>

        <!-- Document Info -->
        <div class="document-info">
            <div class="info-block">
                <div><span class="info-label">{{ $text }} No:</span> {{ $trans }}</div>
                <div><span class="info-label">Customer:</span> {{ $custs }}</div>
                <div><span class="info-label">TRN No:</span> {{ $trn_number }}</div>
                <div><span class="info-label">Phone:</span> {{ $billphone }}</div>
                <div><span class="info-label">Email:</span> {{ $billemail }}</div>
            </div>

            <div class="info-block">
                <div><span class="info-label">{{ $text }} Date:</span> {{ $date }}</div>
                <div><span class="info-label">Supplied Date:</span> {{ $supplieddate }}</div>
                @if ($page == 'sales_order' || $page == 'performance_invoice' ||
                     $page == 'salesorderdraft' || $page == 'performadraft' ||
                     $page == 'quot_to_salesorder')
                <div><span class="info-label">Payment Type:</span> {{ $payment_type }}</div>
                @endif
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
                    @if ($vat_type == 1)
                    <th>Incl. Rate</th>
                    @endif
                    <th>{{$tax}} (%)</th>
                    <th>{{$tax}} Amt</th>
                    <th>Net Rate</th>
                    <th>Discount</th>
                    <th>Total</th>
                    <th>Total w/o Disc.</th>
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
                    @if ($detail->vat_type == 1)
                    <td>{{ $detail->inclusive_rate }}</td>
                    @endif
                    <td>{{ $detail->fixed_vat }}</td>
                    <td>{{ $detail->vat_amount }}</td>
                    <td>{{ $detail->netrate }}</td>
                    <td>{{ $detail->discount_amount * $detail->quantity }}</td>
                    <td>{{ $detail->total_amount }}</td>
                    <td>{{ $detail->totalamount_wo_discount }}</td>
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
<script>
    var array = "{{ $trans }}";
    $("#trans_id").val(array);
</script>
