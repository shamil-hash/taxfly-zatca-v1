<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Invoice Receipt</title>
     <script src="/javascript/printjs.js"></script>
    @if (Session('adminuser'))
        @include('layouts/adminsidebar')
    @elseif(Session('softwareuser'))
        @include('layouts/usersidebar')
    @endif
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

        /* Action Buttons */
        .action-buttons {
            text-align: right;
            padding: 15px 20px;
            background: #f5f5f5;
            border-bottom: 1px solid #ddd;
        }

        .btn {
            display: inline-block;
            padding: 3px 6px;
            margin-left: 2px;
            background: #187f6a;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #146b58;
        }

        /* Header Section */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #187f6a;
            color: white;
        }


        .company-info {
            flex: 1;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .logo {
            max-width: 150px;
            height: auto;
        }

        /* Invoice Info Section */
        .invoice-info {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            background: #f5f5f5;
            border-bottom: 1px solid #eee;
        }

        .invoice-meta {
            flex: 1;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #187f6a;
            margin-bottom: 10px;
            text-align: center;
            text-transform: uppercase;
        }

        /* Customer Info Section */
        .customer-info {
            display: flex;
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .bill-to, .ship-to {
            flex: 1;
        }

        .section-title {
            font-weight: bold;
            color: #187f6a;
            margin-bottom: 10px;
            font-size: 16px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            background: #187f6a;
            color: white;
            padding: 12px;
            text-align: left;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .items-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* Totals Section */
        .totals {
            display: flex;
            justify-content: flex-end;
            padding: 20px;
        }

        .totals-table {
            width: 300px;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .totals-table tr:last-child td {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #187f6a;
        }

        /* Footer Section */
        .footer {
            padding: 20px;
            text-align: center;
            background: #f5f5f5;
            border-top: 1px solid #eee;
        }

        .terms {
            margin-bottom: 20px;
            font-style: italic;
            color: #666;
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

        /* Utility Classes */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .amount-in-words {
            padding: 15px;
            background: #f5f5f5;
            border-radius: 4px;
            margin: 15px 0;
            font-style: italic;
        }

        .barcode {
            text-align: right;
            margin-bottom: 20px;
        }

        .barcode img {
            width: 100px;
            height: 100px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .header, .invoice-info, .customer-info {
                flex-direction: column;
            }

            .bill-to, .ship-to, .invoice-meta {
                margin-bottom: 20px;
            }

            .items-table {
                font-size: 14px;
            }

            .items-table th, .items-table td {
                padding: 8px;
            }
        }
          .btn:hover {
            color: white !important;
        }
    </style>
</head>
<body>
    <div id="content">

    <div class="container">
         <div class="action-buttons">
            @php
                $adminroles = DB::table('adminusers')
                    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                    ->where('user_id', $adminid)
                    ->get();
            @endphp

            <a href="/generatetax-pdf/{{ $trans }}" class="btn">DOWNLOAD PDF</a>
            @foreach ($adminroles as $adminrole)
                @if ($adminrole->module_id == '24')
                    <a href="/generatetax-pdf_a4/{{ $trans }}" class="btn">A4 PRINT</a>
                    <a href="/generatetax-pdf_a5/{{ $trans }}" class="btn">A5 PRINT</a>
                
                        <a href="/generatetax-pdfsunmi/{{ $trans }}" class="btn">PRINT SUNMI</a>
                        <a href="/generatetax-newsunmi/{{ $trans }}" class="btn">PRINT SUNMI MINI</a>
                    @endif


                    @if ($adminrole->module_id == '29')
                        <a href="/billdeskfinalrecieptwithouttax/{{ $trans }}" class="btn">WITHOUT HEADER</a>
                    @endif
            @endforeach
        </div>
        <!-- Header with Company Info -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">{{ $company }}</div>
                <div>TRN: {{ $admintrno }}</div>
                <div>PO Box: {{ $po_box }}</div>
                <div>Tel: {{ $tel }}</div>
                <div>{{ $Address }}</div>
            </div>
            {{-- @if ($logo != null)
            <div class="logo-container">
                <img src="{{ asset($logo) }}" alt="Company Logo" class="logo">
            </div>
            @endif --}}
        </div>
         @php
         $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
         @endphp
        <!-- Invoice Title and Info -->
        <div class="invoice-title">@if($branch!=3 && $branch!=4 && $branch!=5)TAX @endif INVOICE</div>

        <div class="invoice-info">
            <div class="invoice-meta">
                <div><span class="text-bold">Invoice No:</span> {{ $trans }}</div>
                <div><span class="text-bold">Date:</span> {{ $date }}</div>
                <div><span class="text-bold">Supplied Date:</span> {{ $supplieddate }}</div>
                @if ($employeename != null)
                <div><span class="text-bold">Employee:</span> {{ $employeename }}</div>
                @endif
            </div>

            <div class="barcode">
                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('https://taxfly.netplexsolution.com/generatepublic-pdf/' . $enctrans, 'QRCODE') }}" alt="barcode">
            </div>
        </div>

        <!-- Customer Information -->
        <div class="customer-info">
            <div class="bill-to">
                <div class="section-title">Bill To:</div>
                <div>{{ $custs }}</div>
                @if($branch!=3 && $branch!=4 && $branch!=5)
                <div>TRN No: {{ $trn_number }}</div>
                @endif
                <div>Phone: {{ $billphone }}</div>
                <div>Email: {{ $billemail }}</div>
                @if($billingAdd !== null)
                <div>Address: {{ $billingAdd }}</div>
                @endif
            </div>

            <div class="ship-to">
                @if($deliveryAdd)
                <div class="section-title">Ship To:</div>
                <div>{{ $deliveryAdd }}</div>
                @endif
                <div class="section-title" style="margin-top: 20px;">Payment Info:</div>
                <div>
                    @if ($payment_type == 'CREDIT')
                        Payment Type: {{ $payment_type }}
                    @elseif ($payment_type == 'POS CARD')
                        Payment Type: {{ $payment_type }}
                    @else
                        Payment Type: {{ $payment_type }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Description</th>
                    <th>Qty/Box</th>
                    <th>Rate</th>

                    <th>Unit</th>
                    @if ($vat_type == 1)
                    <th>Incl. Rate</th>
                    @endif
                    <th>{{$tax}} %</th>
                    <th>{{$tax}} Amt</th>
                    <th>Net Rate</th>
                    <th>Discount</th>
                    <th>Total</th>
                  <th>Total w/o Disc.t</th>
                </tr>
            </thead>
            <tbody>
                <?php $number = 1; ?>
                @foreach ($details as $detail)
                <tr>
                    <td>{{ $number }}</td>
                    <td>{{ $detail->product_name }}</td>
                    <td>@if( $detail->box_count >0 ) {{$detail->box_count}}  @else {{ $detail->quantity }} @endif</td>
                    <td>{{ $detail->mrp }}</td>

                    <td>{{ $detail->unit }}</td>
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
        <div class="totals">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">
                        @if ($vat_type == 1)
                        {{ number_format(($rate) - $vat, 3) }}
                        @elseif ($vat_type == 2)
                        {{ number_format(($rate), 3) }}
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
                @if ($credit_note_amount != null)
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

        @if ($bankDetails !== null)
        <!-- Bank Details -->
        <div style="padding: 15px; background: #f5f5f5; margin: 15px; border-radius: 4px;">
            <div class="section-title">Bank Details</div>
            <div><strong>Beneficiary:</strong> {{ $bankDetails->account_name }}</div>
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

        <!-- Footer with Terms and Signatures -->
        <div class="footer">
            <div class="terms">
                The above mentioned goods are received in good condition.
                Goods once sold will not be taken back or exchanged in any condition.
            </div>

            <div class="signature-area">
                <div class="signature-box">
                    Seller's Signature<br><br>
                    <div style="height: 1px; background: #ccc; margin: 5px 0;"></div>
                </div>

                <div class="signature-box">
                    Receiver's Signature<br><br>
                    <div style="height: 1px; background: #ccc; margin: 5px 0;"></div>
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
