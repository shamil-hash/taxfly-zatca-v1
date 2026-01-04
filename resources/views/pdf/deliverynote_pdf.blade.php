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
    font-size: 18px;
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
        padding: 4px;
        border: none;
        vertical-align: top;
    }

    .customer-table .field-label {
        font-size: 12px;
        width: 30%; /* Adjusted width to accommodate labels */
        font-weight: bold;
        white-space: nowrap; /* Prevent wrapping */
        padding-right: 5px;
    }

    .customer-table .field-value {
        width: 20%; /* Adjusted width for values */
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

       <table class="header-table">
    <tr>
        <td class="logo-cell">
            <div class="logo-placeholder">
        <img src="{{$logo}}"
            style="max-width: 150px; height: auto; display: block; margin: 0 auto;margin-top:10px;">
</div>
        </td>
        @if($logo!='')
        <td class="details-cell">
            <div class="company-name">{{$company}}</div>
            <div class="contact-info">
                Tel: {{ $tel }} <br>
<span class="glyphicon glyphicon-envelope" style="display: inline;"></span>
<span style="display: inline;">{{$email}}</span>
<span class="glyphicon glyphicon-globe" style="display: inline;"></span>
<span style="display: inline;">{{$Address}}</span>            </div>
        </td>
        @else
        <td  style="text-align: center;">
    <div  style="text-align: center; margin-bottom: 5px; font-weight: bold;color: #187f6a;font-size:24px;">{{$company}}</div>
    <div  style="text-align: center; line-height: 1.4;">
        <span style="display: inline-block;">Tel: {{ $tel }}</span><br>
        <span class="glyphicon glyphicon-envelope" style="display: inline;"></span>
        <span style="display: inline;">{{ $email }}</span><br>
        <span class="glyphicon glyphicon-globe" style="display: inline;"></span>
        <span style="display: inline;">{{$Address}}</span>
    </div>
</td>
@endif
    </tr>
</table>
<div class="divider"></div>



<div class="invoice-header">
    <span class="trn-number">TRN # {{ $admintrno }}</span>
    <span class="invoice-title">Delivery Note</span>
</div>

      <table class="customer-table">
    <tr>
        <td class="field-label">Customer:</td>
        <td class="field-value" colspan="3">
            @if ($custs !== null)
                {{ $custs }}
                @if ($location != ''){{ $location }},@endif
                @if ($area != ''){{ $area }}@endif
            @endif
        </td>
        <td class="field-label">Delivery Note No:</td>
        <td class="field-value">{{ $trans }}</td>
    </tr>
    <tr>
        <td class="field-label">Address:</td>
        <td class="field-value" colspan="3">
            @if ($villa_no != '')<b>Villa No:</b> {{ $villa_no }}@endif
            @if ($flat_no != '')<b>Flat No:</b> {{ $flat_no }}@endif
            @if ($land_mark != ''){{ $land_mark }}@endif
        </td>
        <td class="field-label">TRN No:</td>
        <td class="field-value">
            @if ($trn_number != ''){{ $trn_number }}@endif
        </td>
    </tr>
    <tr>
        <td class="field-label">Mob:</td>
        <td class="field-value" colspan="3">
            @if ($billphone != '') {{ $billphone }}@endif
        </td>
        <td class="field-label">Delivery Date:</td>
        <td class="field-value">{{ $delivery_date }}</td>
    </tr>
    <tr>
        <td class="field-label">Delivery Note Date:</td>
        <td class="field-value" colspan="3">
             {{ $date }}<br>
            
        </td>
        <td class="field-label">Payment Type:</td>
        <td class="field-value">
            @if ($payment_type == 'CREDIT') CREDIT
            @elseif ($payment_type == 'CASH') CASH
            @elseif ($payment_type == 'BANK') BANK
            @elseif ($payment_type == 'POS CARD') POS CARD
            @endif
        </td>
    </tr>
</table>


<table class="details-table">
    <thead style="background-color:#187f6a; color:white">
        <th style="width: 7%;font-size:12px;">Sr No.</th>
<th style="width: 23%;font-size:12px;">Description</th>
<th style="width: 11%;font-size:12px;">Qty</th>
<th style="width: 8%;font-size:12px;">Unit</th>
</thead>
<tbody>

@foreach ($details as $detail)
    <tr>
        <td style="height: 22px; border-bottom: 0.5px solid #000;font-size:10px;">{{ $loop->iteration }}</td>
        <td style="height: 22px; border-bottom: 0.5px solid #000;font-size:10px;">
            {{ $detail->product_name }}

        </td>

        <td style="height: 22px; border-bottom: 0.5px solid #000;font-size:10px;">{{ $detail->quantity }}</td>
        <td style="height: 22px; border-bottom: 0.5px solid #000;font-size:10px;">{{ $detail->unit }}</td>
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
    @for ($i = 0; $i < 4; $i++) <!-- Changed from 8 to 4 to match actual columns -->
        <td style="height: {{ $calculatedHeight }}px; border-top: none; border-bottom: none; background: transparent;">&nbsp;</td>
    @endfor
</tr>
@endif



    </tbody>

</table>



    <div class="footer">
  <table class="footer-table">
    <tfoot>

    <tr>
      <td style="width: 30%; white-space: nowrap;">Signature _______________________________
      </td>
      <td style="width: 40%; white-space: nowrap; padding-left:10px">Name / Number: _______________________________</td>
      <td style="width: 10%; white-space: nowrap; padding-left:10px">For</td>
      <td style="width: 20%;"></td>
    </tr>
    <tr>
      <td colspan="4" style="white-space: nowrap; font-weight: bold; padding-top: 10px; text-align: center; padding-left:200px; color: #187f6a;">
        {{$company}}
      </td>
    </tr>
</tfoot>
  </table>
</div>



</body>
</html>
