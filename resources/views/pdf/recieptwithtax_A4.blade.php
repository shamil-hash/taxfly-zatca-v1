@php
    header('Content-Type: text/html; charset=utf-8');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ZATCA Compliant Invoice</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 20px; }
        .arabic-text { direction: rtl; unicode-bidi: embed; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #000; padding: 6px; font-size: 11px; }
        th { background: #187f6a; color: #fff; }
        .no-border td { border: none; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 25px; font-size: 10px; text-align: center; }
        .qr { margin-top: 15px; text-align: center; }
        .zatca-meta { font-size: 10px; margin-top: 15px; width: 100%; }
        .zatca-meta td { padding: 3px; vertical-align: top; }
        .warning-box { background-color: #fff8e1; border: 1px solid #ffc107; padding: 8px; margin: 10px 0; font-size: 10px; }
        .error-box { background-color: #ffebee; border: 1px solid #f44336; padding: 8px; margin: 10px 0; font-size: 10px; }
    </style>
</head>
<body>

{{-- Header --}}
<table class="no-border">
    <tr>
        <td style="width: 30%;">
            @if(!empty($logo))
                <img src="{{ $logo }}" style="max-width: 120px;">
            @endif
        </td>
        <td style="width: 70%; text-align:right;">
            <strong>{{ $company }}</strong><br>
            TRN: {{ $admintrno }}<br>
            Tel: {{ $tel }} | Email: {{ $emailadmin }}<br>
            {{ $Address }}
        </td>
    </tr>
</table>

<h3 style="text-align:center; color:#187f6a;">
    TAX INVOICE <span class="arabic-text">فاتورة ضريبية</span>
</h3>

{{-- Customer / Invoice Info --}}
<table>
    <tr>
        <td><strong>Customer Name</strong><br><span class="arabic-text">اسم العميل</span></td>
        <td>{{ $custs }}</td>
        <td><strong>Invoice No</strong><br><span class="arabic-text">رقم الفاتورة</span></td>
        <td>{{ $display_invoice_number }}</td>
    </tr>
    <tr>
        <td><strong>Customer TRN</strong><br><span class="arabic-text">الرقم الضريبي</span></td>
        <td>{{ $trn_number }}</td>
        <td><strong>Date/Time</strong><br><span class="arabic-text">التاريخ / الوقت</span></td>
        <td>{{ $issue_datetime_utc }}</td>
    </tr>
    <tr>
        <td><strong>Payment Type</strong><br><span class="arabic-text">طريقة الدفع</span></td>
        <td>{{ $payment_type }}</td>
        <td><strong>UUID</strong><br><span class="arabic-text">المعرف الفريد</span></td>
        <td>{{ $uuid }}</td>
    </tr>
</table>

{{-- ZATCA Metadata Table
<table class="zatca-meta">
    <tr>
        <td><strong>ZATCA Invoice Number:</strong></td>
        <td>{{ $display_invoice_number }}</td>
        <td><strong>Invoice Counter (ICV):</strong></td>
        <td>{{ $invoice_counter }}</td>
    </tr>
    <tr>
        <td><strong>Previous Invoice Hash (PIH):</strong></td>
        <td colspan="3">{{ $previous_invoice_hash ?? '-' }}</td>
    </tr>
    <tr>
        <td><strong>Invoice Type:</strong></td>
        <td>{{ $transactionType }}</td>
        <td><strong>Customization ID:</strong></td>
        <td>urn:cen.eu:en16931:2017#compliant#urn:sa.gov.zatca:invoice</td>
    </tr>
</table> --}}

{{-- Line Items --}}
<table>
    <thead>
        <tr>
            <th>Sr</th>
            <th>Description<br><span class="arabic-text">الوصف</span></th>
            <th>Qty</th>
            <th>Rate (Incl. VAT)<br><span class="arabic-text">السعر (شامل الضريبة)</span></th>
            <th>Net Price (Excl. VAT)<br><span class="arabic-text">السعر الصافي (بدون ضريبة)</span></th>
            <th>VAT %</th>
            <th>VAT Amt</th>
            <th>Net Total<br><span class="arabic-text">المجموع الصافي</span></th>
            <th>Discount</th>
            <th>Total (Incl. VAT)<br><span class="arabic-text">المجموع (شامل الضريبة)</span></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($details as $loopIndex => $item)
            <tr>
                <td>{{ $loopIndex + 1 }}</td>
                <td>{{ $item->product_name ?? '' }}</td>
                <td class="text-right">{{ number_format((float)$item->quantity, 2) }}</td>
                <td class="text-right">{{ number_format((float)$item->mrp, 2) }}</td>
                <td class="text-right">
                    @php
                        $netPrice = (float)$item->mrp / (1 + ((float)$item->fixed_vat / 100));
                        echo number_format(round($netPrice, 2), 2);
                    @endphp
                </td>
                <td class="text-right">{{ number_format((float)$item->fixed_vat, 2) }}</td>
                <td class="text-right">{{ number_format((float)$item->vat_amount, 2) }}</td>
                <td class="text-right">
                    @php
                        $netTotal = $netPrice * (float)$item->quantity;
                        echo number_format(round($netTotal, 2), 2);
                    @endphp
                </td>
                <td class="text-right">{{ number_format((float)($item->discount_amount * $item->quantity), 2) }}</td>
                <td class="text-right">{{ number_format((float)$item->total_amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <!-- Subtotal = Sum of Net Amounts -->
        <tr>
            <td colspan="8" class="text-right"><strong>Sub Total</strong></td>
            <td class="text-right">
                @php
                    $subTotalNet = 0;
                    foreach ($details as $item) {
                        $netPrice = (float)$item->mrp / (1 + ((float)$item->fixed_vat / 100));
                        $subTotalNet += round($netPrice * (float)$item->quantity, 2);
                    }
                    echo number_format($subTotalNet, 2);
                @endphp
            </td>
            <td class="text-right"></td>
        </tr>

        <!-- VAT -->
        <tr>
            <td colspan="8" class="text-right"><strong>VAT</strong></td>
            <td class="text-right">{{ number_format($vat, 2) }}</td>
            <td class="text-right"></td>
        </tr>

        <!-- Discount -->
        <tr>
            <td colspan="8" class="text-right"><strong>Discount</strong></td>
            <td class="text-right">
                @php
                    $totalDiscount = (float) ($discount_amt + $Main_discount_amt);
                    echo number_format($totalDiscount, 2);
                @endphp
            </td>
            <td class="text-right"></td>
        </tr>

        <!-- Return -->
        @if($returntotal > 0)
            <tr>
                <td colspan="8" class="text-right"><strong>Return</strong></td>
                <td class="text-right">{{ number_format($returntotal, 2) }}</td>
                <td class="text-right"></td>
            </tr>
        @endif

        <!-- Grand Total -->
        <tr>
            <td colspan="8" class="text-right"><strong>Grand Total</strong></td>
            <td class="text-right">{{ number_format($grandinnumber, 2) }}</td>
            <td class="text-right"></td>
        </tr>
    </tfoot>
</table>

{{-- Amount in words --}}
<p><strong>Amount in Words:</strong> {{ $amountinwords }}</p>

{{-- Bank Details --}}
@if (!empty($bankDetails))
    <table>
        <tr><td><strong>Bank</strong></td><td>{{ $bankDetails->bank_name }}</td></tr>
        <tr><td><strong>Account Name</strong></td><td>{{ $bankDetails->account_name }}</td></tr>
        <tr><td><strong>Account No</strong></td><td>{{ $bankDetails->account_no }}</td></tr>
        <tr><td><strong>IBAN</strong></td><td>{{ $bankDetails->iban_code }}</td></tr>
    </table>
@endif

{{-- QR Code --}}
@if(!empty($qrCodeBase64))
    <div class="qr">
        @if(!empty($qrCodeBase64))
            <div class="qr">
                <img src="data:image/png;base64,{{ $qrCodeBase64 }}" width="150" alt="ZATCA QR Code" style="border:1px solid #ccc;">
            </div>
        @endif
    </div>
@endif

{{-- Errors --}}
@if(isset($zatcaErrors) && count($zatcaErrors) > 0)
    <div class="error-box">
        <strong>ZATCA Validation Errors:</strong>
        @foreach($zatcaErrors as $err)
            <div>{{ $err }}</div>
        @endforeach
    </div>
@endif

{{-- Warnings --}}
@if(isset($zatcaWarnings) && count($zatcaWarnings) > 0)
    <div class="warning-box">
        <strong>ZATCA Warnings:</strong>
        @foreach($zatcaWarnings as $warn)
            <div>{{ $warn }}</div>
        @endforeach
    </div>
@endif

{{-- Audit Trail (Optional) --}}
@if(!empty($signedXml))
    <div style="font-size:9px; color:#666; margin-top:20px; border-top:1px dashed #ccc; padding-top:10px;">
        <strong>Internal Audit:</strong><br>
        Signed XML Path: {{ $signedXml }}<br>
        Final Invoice Hash: {{ $invoice_hash ?? 'Not Available' }}
    </div>
@endif

<div class="footer">
    <p>Thank you for your business! <span class="arabic-text">شكراً لتعاملكم معنا</span></p>
    <p>This is a system generated invoice, no signature required</p>
</div>

</body>
</html>