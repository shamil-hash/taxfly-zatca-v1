<!doctype html>
<html lang="en">

<head>
    <title>{{ $transaction_id }}</title>
    <style>
        /* Set the page size for 80mm x 210mm */
        @page {
            size: 80mm 210mm;
            margin: 0;
        }

* {
    font-weight: 900; /* Apply bold to all elements */
}
        body {
            margin: 0;
            padding: 5px;
            font-family: 'Courier New', monospace;
             font-size: 10px;
            width: 80mm;
            font-weight: bold;
        }
        .arabic-text {
    direction: rtl;
    text-align: right;
    font-family: Arial, sans-serif;
}


        .div-1 {
            text-align: center;
        }

        .image-container img {
            max-width: 50mm;
            height: auto;
            padding-bottom: 5px;
        }

        .headadmin h2 {
            font-size: 12px;
            margin: 3px 0;
        }

        .adjust {
            margin: 1px 0;
            font-size: 10px;
        }

        .heading {
            width: 100%;
            margin-top: 5px;
            font-size: 10px;
        }

        .heading td {
            vertical-align: top;
            padding: 2px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .table th {
            text-align: center;
            padding: 2px;
            font-size: 10px;
            border-bottom: 1px dotted #000;
        }

        .table td {
            text-align: center;
            padding: 2px;
            font-size: 10px;
            word-wrap: break-word;
            white-space: normal;
            max-width: 18mm;
        }


        .dotted-line {
            border-bottom: 1px dotted #000;
            margin: 5px 0;
        }

        .num {
            display: none;
        }

        .welcome h1 {
            font-size: 11px;
            margin: 8px 0;
            text-align: center;
        }

        td div {
            text-align: center;
            margin: 1px 0;
        }

        .totals-section {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
        }

        .totals-section p {
            margin: 2px 0;
        }
        .footer {
            text-align: center;
            margin-top: 3mm;
            font-size: 9px;
        }
    </style>
</head>
@php
    $user=Session('softwareuser');
    $name=DB::table('softwareusers')
          ->where('id',$user)
          ->pluck('name')
          ->first();
    $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();  
@endphp
<body>
<div class="div-1" @if ($logo!=null)style="margin-top:-11px;"@endif>
        <div class="image-container">
            @if ($logo!=null)
            <img src="{{ asset($logo) }}" alt="Branch Logo" style="filter: brightness(150%);{{$sunmilogo}}">
            @endif
        </div>
        <div class="headadmin">
            <h2>{{ $company }} <br>@if($arabic_name!=''){{$arabic_name}}@endif</h2>
            @if ($admintrno != '')
            <h6 class="adjust"><b>TRN:</b> {{ $admintrno }}</h6>
            @endif

            @if ($po_box != '')
            <h6 class="adjust"><b>PO BOX:</b> {{ $po_box }}</h6>
            @endif

            <h6 class="adjust">{{ $branchname }}</h6>

            @if ($tel != '')
            <h6 class="adjust"><b>Mob:</b> {{ $tel }}</h6>
            @endif
        </div>

        <h2 style="margin: 5px 0;">TAX INVOICE فاتورة ضريبية</h2>

        <table class="heading">
            <tr>
                <td style="text-align: left; width: 50%;">
                    <b>Invoice No:</b> {{ $trans }}<br>
                    <b>Customer:</b> {{ $custs }}<br>
                    <b>TRN:</b> {{ $trn_number }}
                    @if ($billphone != '')
                    <br><b>Phone:</b> {{ $billphone }}
                    @endif
                </td>
                <td style="text-align: right; width: 50%;">
                    <b>Invoice Date:</b> {{ $date }}<br>
                    <b>Payment Type:</b> {{ $payment_type }}
                </td>
            </tr>
        </table>
        <div class="dotted-line"></div>

        <table class="table">
            <thead>
                <tr>
                <th>Item <br>الصنف</th>
                <th>Qty <br>الكمية</th>
                <th>Rate <br>السعر</th>
                <th>{{ $tax }} <br>ض.ق.م</th>
                <th>Total <br>الإجمالي</th>

                </tr>
            </thead>
            <tbody>
                <?php $number = 1; ?>
                @foreach ($details as $detail)
                <tr class="item-row">
                    <td class="num">{{ $number }}</td>
                    <td>{{ $detail->product_name }}</td>
                    <td>{{ number_format($detail->quantity, 2) }}</td>
                    <td>
                        @if ($detail->vat_type == 1 || $detail->vat_type == 2)
                        {{ number_format($detail->mrp, 2) }}
                        @endif
                    </td>
                    <td>
                        <div>{{ $detail->fixed_vat }}%</div>
                        <div>{{ number_format($detail->vat_amount, 2) }}</div>
                    </td>
                    <td>{{ number_format($detail->total_amount, 2) }}</td>
                </tr>
                <?php $number++; ?>
                @endforeach
            </tbody>
        </table>
        <div class="dotted-line"></div>


     <div style="display: flex; justify-content: space-between; font-family: Arial, sans-serif;">
    <div style="text-align: left; width: 39%;">
        <p style="margin: 2px 0;">No. of Items: {{ $number - 1 }}</p>
    </div>
    <div style="text-align: right; width: 61%;">
        <!-- Subtotal Row -->
        <div style="display: flex; justify-content: space-between; margin: 2px 0;">
            <div style="width: 50%; text-align: left;">
                Subtotal <span class="arabic-text" style="margin-left: 5px;">المجموع الفرعي :</span>
            </div>
            <div style="width: 50%; text-align: right;">
                @if ($vat_type == 1)
                    {{ $currency }} {{ number_format($rate - $vat, 2) }}
                @elseif ($vat_type == 2)
                    {{ $currency }} {{ number_format($rate, 2) }}
                @endif
            </div>
        </div>
        
        <!-- Discount Row -->
        <div style="display: flex; justify-content: space-between; margin: 2px 0;">
            <div style="width: 50%; text-align: left;">
                Discount <span class="arabic-text">الخصم :</span>
            </div>
            <div style="width: 50%; text-align: right;">
                {{ $currency }} {{ number_format(($discount_amt + ($Main_discount_amt ?? 0)), 2) }}
            </div>
        </div>
        
        <!-- Tax Row -->
        <div style="display: flex; justify-content: space-between; margin: 2px 0;">
            <div style="width: 50%; text-align: left;">
                {{$tax}} <span class="arabic-text">ض.ق.م :</span>
            </div>
            <div style="width: 50%; text-align: right;">
                {{ $currency }} {{ number_format($vat, 2) }}
            </div>
        </div>
        
        <!-- Grand Total Row -->
        <div style="display: flex; justify-content: space-between; margin: 2px 0; font-weight: 900;">
            <div style="width: 50%; text-align: left;">
                Grand Total <span class="arabic-text">الإجمالي الكلي :</span>
            </div>
            <div style="width: 50%; text-align: right;">
                {{ $currency }} {{ number_format($grandinnumber, 2) }}
            </div>
        </div>
    </div>
</div>
        @if($payment_type=='CREDIT')
        <div class="dotted-line"></div>
        <div style="display: flex; justify-content: space-between; margin-top: 5px;">
    <div style="text-align: right; width: 100%;">
        <div style="display: flex; justify-content: space-between; margin: 2px 0;">
        <span>Previous Balance <span class="arabic-text">الرصيد السابق:</span></span>

            <span>
                {{ $currency }}

                    {{ number_format($Due, 2) }}
            </span>
        </div>
        <div style="display: flex; justify-content: space-between; margin: 2px 0;">
        <span>This Bill Amount <span class="arabic-text">مبلغ هذه الفاتورة :</span></span>

            <span>
                {{ $currency }}

                    {{ number_format($invoiceDue, 2) }}
            </span>
        </div>
        <div style="display: flex; justify-content: space-between; margin: 2px 0;">
            <span>
                @if($comment == 'Invoice')
                    Advance<span class="arabic-text">المبلغ المقدّم :</span>
                @elseif($comment == 'Product Returned')
                    Refund Amount<span class="arabic-text">المبلغ المسترد :</span>
                @else
                    {{ $comment }}:
                @endif
            </span>
            <span>
                {{ $currency }}

                    {{ number_format($collectedAmount, 2) }}
            </span>
        </div>
        <div style="display: flex; justify-content: space-between; margin: 2px 0; font-weight: 900;">
        <span>Balance <span class="arabic-text">الرصيد :</span></span>

            <span>
                {{ $currency }}

                    {{ number_format($updatedBalance, 2) }}
            </span>
        </div>
    </div>
</div>
@endif
<div class="dotted-line"></div>
        <div class="welcome">
            <h1>*** THANK YOU VISIT AGAIN ***</h1>
        </div>
    </div>
     <div class="footer">
        {{ date('d-M-Y H:i:s') }}<br>
        Printed by: {{$name}}
    </div>
</body>

</html>

@foreach ($adminroles as $adminrole)
    @if ($adminrole->module_id == '23')
        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    @endif
@endforeach