<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <title>Invoice</title>
    <style>
 body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 10px;

}
.spacer{
    margin-left: 20px;
}



@page {

margin: 20px 25px;

}


tbody tr{
    page-break-inside: always; /* 'avoid' instead of 'always' */
}

tfoot tr{
    break-before: avoid-page; /* modern syntax */
    page-break-before: avoid; /* fallback for older browsers */
    page-break-inside: avoid;

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

.details-cell {
    width: 80%;
    padding-left: 5px; /* Increased padding for better spacing */
    border-left: 1px solid #000;
    position: relative;
}
.company-name {
    font-size: 24px;
    font-weight:bold;
    margin-bottom: 2px;
    margin-left:20px;
    color: #187f6a;;
}
.company-subtitle {
    font-size: 20px;
    margin-bottom: 3px;
    margin-left:20px;
}
.contact-info {
    font-size: 13px;
    line-height: 1.3;
    margin-left:20px;
}
.divider {
    border-top: 1px solid #000;
    margin: 5px 0;
}
 .invoice-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    margin-top:20px;
}
.trn-number {
    font-size: 12px;
    color: #187f6a;
    font-weight: bold;
}
.invoice-title {
    font-size: 22px;
    font-weight: bold;
    color: #187f6a;
    display: block;
    margin-top: -25px;
    text-align: center;
}
        .customer-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      margin-bottom: 10px;
      margin-top: -10px;
      border: 1px solid #afafaf;
      padding: 2px;
    }

    .customer-table td {
      padding: 1px;
      border: none;
    }

    .customer-table .field-label {
      font-size: 12px;
      width: 16%;
      font-weight: bold;
    }

         .details-table {
            width: 100%;
            max-width: 100%;
            table-layout: fixed;
            /* Prevents table expansion */
            border-collapse: separate;
            border-spacing: 0;
            border: 0.5px solid #000;
            border-radius: 8px;
            overflow: hidden;
        }

        .details-table th,
        .details-table td {
            padding: 4px;
            text-align: center;
            border: 0.5px solid #000;
            vertical-align: middle;
            height: 40px;
            word-wrap: break-word;
            /* Ensures long text wraps */
            overflow: hidden;
            text-overflow: ellipsis;
            /* Adds ... for overflow */
        }

        .details-table td {
            border-top: none;
            border-bottom: none;
        }

        /* Set a fixed width for each column */
        .details-table th:nth-child(1),
        .details-table td:nth-child(1) {
            width: 6%;
            /* No. */
        }

        .details-table th:nth-child(2),
        .details-table td:nth-child(2) {
            width: 14%;
            /* Description */
            text-align: left;
        }

        .details-table th:nth-child(3),
        .details-table td:nth-child(3){
            width:8%;
        }



.details-table th:nth-child(4),
        .details-table td:nth-child(4) {
            width: 8%;
            /* Qty and Rate */
        }




 .details-table th:nth-child(5),
        .details-table td:nth-child(5) {
            width: 6%;
            /* Unit */
        }

        .details-table th:nth-child(6),
        .details-table td:nth-child(6) {
            width: 6%;
            /* VAT % and Amount */

        }

        .details-table th:nth-child(7),
        .details-table td:nth-child(7) {
            width: 8%;
            /* VAT % and Amount */
        }

        .details-table th:nth-child(8),
        .details-table td:nth-child(8) {
            width: 12%;
            /* Net Rate */
        }

        .details-table th:nth-child(9),
        .details-table td:nth-child(9) {
            width: 8%;
            /* Discount Amount */
        }

        .details-table th:nth-child(10),
        .details-table td:nth-child(10) {
            width: 12%;
            /* Total Amount */
            white-space: nowrap;
            /* Prevent wrapping */
            overflow: hidden;
            text-overflow: ellipsis;
            /* Shortens large numbers */
        }





        /* Left align the Description column */
        .details-table td:nth-child(3) {
            text-align: left;
            white-space: normal;
            /* Allow wrapping */
        }

        /* Set a fixed height for rows */
        .details-table tr {
            height: 50px;
            /* Adjust this based on your design */
        }

        /* New style for spacer row when fewer than 4 products */
        /* .spacer-row1 td {
            height: 300px !important;
            border-top: none !important;
    border-bottom: none !important;
    background: transparent;
        } */

        .spacer-row2 td {
            height: 110px !important; /* Adjust this value as needed */
            border-top: none !important;
    border-bottom: none !important;
    background: transparent;
        }
        .spacer-row1 td:not(:empty),
.spacer-row2 td:not(:empty) {
    border-left: 0.5px solid #000 !important;
    border-right: 0.5px solid #000 !important;
}
        /* Apply border-radius to table corners */
        .details-table th:first-child {
            border-top-left-radius: 8px;
        }

        .details-table th:last-child {
            border-top-right-radius: 8px;
        }

 .total-row td {
  border: 1px solid rgb(0, 0, 0);
  padding: 2px 4px; /* Small padding for readability */
  font-size: 12px;
  height: 24px;      /* Explicitly reduce row height */
  line-height: 1.2;  /* Control text spacing */
}

       .footer {
      margin-top: 10px;
      font-size: 12px;
    }

    .footer-table {
      width: 100%;
      font-size: 12px;
      border-collapse: collapse;
    }

    .footer-table td {
      padding: 2px 0;
    }

    </style>
</head>
<body>


<div class="divider"></div>



<div class="invoice-header">
    <!--<span class="trn-number">TRN # {{ $admintrno }}</span>-->
    <span class="invoice-title">TAX INVOICE</span>
</div>

        <table class="customer-table">
      <tr>
        <td class="field-label">NAME&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
        <!-- <td>:</td> -->
        <td colspan="4">{{ $custs }}</td>
        <td class="field-label">INVOICE No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
        <!-- <td>:</td> -->
        <td>{{ $trans }}</td>
      </tr>
      <tr>

        <td class="field-label">Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
        <!-- <td>:</td> -->
        <td>{{ $date }}</td>
      </tr>
      <tr>
     
        <td class="field-label">Payment Type.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
        <!-- <td>:</td> -->
        <td>{{$payment_type}}</td>
      </tr>
    </table>
<table class="details-table">
    <thead style="background-color:#187f6a; color:white">
        <th style="width: 7%;font-size:12px;">Sr No.</th>
<th style="width: 23%;font-size:12px;">Description</th>
<th style="width: 10%;font-size:12px;">Qty</th>
<th style="width: 11%;font-size:12px;">Rate</th>
<th style="width: 8%;font-size:12px;">{{$tax}} (%)</th>
<th style="width: 11%;font-size:12px;">{{$tax}} Amount</th>
<th style="width: 12%;font-size:12px;">Netrate</th>
<th style="width: 11%;font-size:12px;">Discount Amount</th>
<th style="width: 11%;font-size:12px;">Total Amount</th>
</thead>
<tbody>

@foreach ($details as $detail)
    <tr>
        <td style="height: 22px; border-bottom: 0.5px solid #000;font-size:10px;">{{ $loop->iteration }}</td>
        <td style="height: 22px; border-bottom: 0.5px solid #000;font-size:10px;">
            {{ $detail->product_name }}
            @if($detail->record_type == 'return')
                <span style="color: red;">(Returned)</span>
            @endif
        </td>
        <td style="height: 22px; border-bottom: 0.5px solid #000;font-size:10px;">{{ $detail->quantity }}</td>
        <td style="height: 22px; border-bottom: 0.5px solid #000;font-size:10px;">{{ $detail->mrp }}</td>
        <td style="height: 22px; border-bottom: 0.5px solid #000;font-size:10px;">{{ $detail->fixed_vat }}</td>
        <td style="height: 22px; border-bottom: 0.5px solid #000;font-size:10px;">{{ $detail->vat_amount }}</td>
        <td style="height: 22px; border-bottom: 0.5px solid #000;font-size:10px;">{{ $detail->netrate }}</td>
        <td style="height: 22px; border-bottom: 0.5px solid #000;font-size:10px;">{{ $detail->discount_amount * $detail->quantity }}</td>
        <td style="height: 22px; border-bottom: 0.5px solid #000;font-size:10px;">{{ $detail->total_amount }}</td>
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
    @for ($i = 0; $i < 8; $i++)
        <td style="height: {{ $calculatedHeight }}px; border-top: none; border-bottom: none; background: transparent;">&nbsp;</td>
    @endfor
</tr>
@endif




    </tbody>
    <tfoot>
   <tr class="total-row">
   <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td colspan="3" class="text-right" style="text-align: right; padding-right: 15px;">SUB TOTAL. (AED)</td>
          <td style="text-align: center;"><strong>
 @if ($vat_type == 1)
    {{ number_format($rate - $vat, 3) }}<br />
@elseif ($vat_type == 2)
    {{ number_format($rate, 3) }}<br />
@endif
        </strong></td>
        </tr>
        <tr class="total-row">
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td colspan="3" class="text-right" style="text-align: right; padding-right: 15px;">{{$tax}} (AED)</td>
          <td style="text-align: center;"><strong>{{ $vat }}</strong></td>
        </tr>
        <tr class="total-row">
        <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td colspan="3" class="text-right" style="text-align: right; padding-right: 15px;">Discount (AED)</td>
          <td style="text-align: center;"><strong>{{ $discount_amt == null && $Main_discount_amt == null ? 0 : $discount_amt + $Main_discount_amt }}</strong></td>
        </tr>
         @if ($returntotal>0)
        <tr class="total-row">
        <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td   style="border:none; border-right:solid #000 0.5;"></td>
          <td colspan="3" class="text-right" style="text-align: right; padding-right: 15px;">Return (AED)</td>
          <td style="text-align: center;"><strong> {{$returntotal}}</strong></td>
        </tr>
        @endif
        <tr class="total-row">
            <td colspan="5" class="text-left bold" style="text-align: left;">Amount in Words :<span style="font-weight:bold;">{{ $amountinwords }}</span></td>
            <td colspan="3" class="text-right" style="text-align: right; padding-right: 15px;font-weight:bold;">Grand Total (AED)</td>
            <td style="text-align: center;"><strong style="font-weight:bold;">{{ $grandinnumber }}</strong></td>
          </tr>
          </tfoot>
</table>



    <div class="footer">
  <table class="footer-table">
    <tfoot>
    <tr>
      <td colspan="3" style="padding-bottom: 8px; text-align: justify;
">
        The above mentioned goods are received in good condition. Goods once sold will not be<br />
        taken back or exchanged in any condition.
      </td>
      <td style="text-align: right; vertical-align: top; white-space: nowrap; ">E. &nbsp;O. &nbsp;E.</td>
    </tr>
    <tr>
      <td style="width: 30%; white-space: nowrap;">Signature _______________________________
      </td>
      <td style="width: 40%; white-space: nowrap; padding-left:10px">Name / Number: _______________________________</td>
      <td style="width: 10%; white-space: nowrap; padding-left:10px">For</td>
      <td style="width: 20%;"></td>
    </tr>
    <tr>
      <td colspan="9" style="white-space: nowrap; font-weight: bold; padding-top: 10px; text-align: center; padding-left:200px; color: #187f6a;">
        {{$company}}
      </td>
    </tr>
</tfoot>
  </table>
</div>



</body>
</html>
