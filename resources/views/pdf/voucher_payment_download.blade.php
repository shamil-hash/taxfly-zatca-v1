<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $id }}_Payment_Voucher</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
 <style>


    .container {
        padding: 30px;
        max-width: 700px;
        background-color: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .header-section {
        display: flex;
        justify-content: space-between;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .header-left {
        text-align: left;
    }

    .header-right {
        text-align: right;
        align-self: flex-start;
    }

    .header-left p, .header-right p {
        margin: 0;
    }

    .receipt-title {
        background-color: black;
        color: white;
        text-align: center;
        padding: 8px 0;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .receipt-box {
        background-color: #f9f9f9;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        width: 100%;
        max-width: 600px; /* Set maximum width for the receipt box */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .receipt-box p {
        margin: 0;
        padding: 5px 0;
        font-size: 14px;
    }

    .receipt-amount {
        font-size: 20px;
        font-weight: bold;
        margin: 10px 0;
        padding: 10px;
        color: #333;
        background-color: #f0f0f0;
        border: 1px solid #ccc;
        border-radius: 5px;
        display: inline-block;
        width: auto; /* Adjust width based on content */
        float: right; /* Moves the amount to the right side */
    }

    .amount-in-words {
        margin: 10px 0;
        font-size: 13px;

    }

    .receipt-box p strong {
        font-size: 14px;
        color: black;
    }

    .payment-mode {
        margin-bottom: 15px; /* Add some space between this and the next section */
    }

    .bordered-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        margin-bottom: 30px;
    }

    .bordered-table th, .bordered-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
        font-size: 14px;
    }

    .bordered-table th {
        background-color: black;
        color: white;
    }

    .bottom-line {
        border-top: 2px solid black;
        margin: 10px 0 30px;
    }

    .total-section p {
        font-size: 14px;
        font-weight: bold;

    }

    .signature-line {
        text-align: right;
        border-top: 1px solid #333;
        width: 200px;
        margin-left: auto;
    }
   /* Styles for the table */
.bordered-table {
    width: auto; /* Adjust width based on content */
    border-collapse: collapse; /* Ensure borders are collapsed */
}

.bordered-table th,
.bordered-table td {
    text-align: left; /* Align text to the left */
    border-bottom: 1px solid #ddd; /* Optional: Add a bottom border to rows */
    font-size: 12px; /* Adjust font size */
}

</style>

</head>

<body>
    <div id="content">
        <div class="container">
            <!-- Company and Receipt Info -->
            <div class="header-section">
            <div class="image-container" style="flex: 1; text-align: center;">

                    <h4 style="margin-left: 20; font-size: 18px;"><b>{{$company}}</b></h4>
                    <p style="margin-left: 20;"><b>{{$address}}</b></p>
                    <p style="margin-left: 20;">Mob: <b>{{$tel}}</b>  &nbsp; Email: <b>{{$emailadmin}}</b>  &nbsp; TRN: <b>{{ $admintrno}}</b> &nbsp; </p>
                </div>
                <div class="header-right">
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::now()->format('Y-m-d') }}</p>
                    <p><strong>Payment Voucher No:</strong> {{ $id }}</p>
                </div>
            </div>

            <div class="receipt-title">PAYMENT VOUCHER</div>

            <!-- Payment Info -->
            <div class="receipt-box" style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
    <p><strong>Payment Given to:</strong> {{ $purchases[0]->credit_supplier_username ?? 'N/A' }}</p>

    <div class="receipt-amount" style="font-size: 16px; font-weight: bold; margin-top: 5px;">
        {{ $currency }} {{ $purchases[0]->collectedamount ?? 'N/A' }} /-
    </div>
    <p class="amount-in-words" style="margin-top: 5px;">
        <strong>Amount In Words:</strong> {{ $amountinwords}} /-
    </p>

    @if(!is_null($purchases[0]->payment_type))
    <p class="payment-mode" style="margin-top: 5px;">
        <strong>Payment Mode:</strong>
        @switch($purchases[0]->payment_type)
            @case(1)
                CASH
                @break
            @case(2)
                CHEQUE
                @break
            @case(3)
                BANK
                @break
        @endswitch
    </p>
    @endif


    <p style="margin-top: 5px;"><strong>For:</strong>Purchase </p>
    @if(!empty($purchases[0]->reciept_no))
    <p style="margin-top: 5px;"><strong>Reciept No:</strong> {{ $purchases[0]->reciept_no ?? 'N/A' }}</p>
@endif
@if(!empty($purchases[0]->invoice_date))
<p style="margin-top: 5px;"><strong>Reciept Date:</strong> {{ $purchases[0]->invoice_date ?? 'N/A' }}</p>
@endif
</div>
            <!--  -->
 <!-- Products and Amount Section -->
@if($products->isNotEmpty())
    <div class="product-section">
        <h4>SALE DETAILS</h4>
        <table class="bordered-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->final_quantity }}</td> <!-- Quantity from buyproducts -->
                    <td>{{ $currency }} {{ $product->rate }}</td> <!-- Rate from buyproducts -->
                    <td>{{ $currency }} {{ $product->price }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

@endif

<br>
@if($purchases[0]->comment == 'Purchase Returned')
@if($returnpurchases->isNotEmpty())
    <div class="product-section">
        <h4>RETURN DETAILS</h4>
        <table class="bordered-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($returnpurchases as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->final_quantity }}</td> <!-- Quantity from buyproducts -->
                    <td>{{ $currency }} {{ $product->rate }}</td> <!-- Rate from buyproducts -->
                    <td>{{ $currency }} {{ $product->amount }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

@endif
@endif
<br>


           <!-- Total Section -->
           @if(!empty($purchases[0]->reciept_no))
<div class="total-section" style="text-align: left;">
    <div style="text-align: right;">
        <p>Grand Total: {{ $currency }} {{ number_format($purchases[0]->total ?? '0.00',3) }}</p>
        <p>
            @if($purchases[0]->comment == 'Bill')
            Balance Due: {{ $currency }} {{ number_format(($purchases[0]->previous_invoice_due ?? 0), 3) }}
        @elseif($purchases[0]->comment == 'Payment Made')
            Balance Due: {{ $currency }} {{ number_format(($purchases[0]->total_due ?? 0) + ($purchases[0]->collectedamount ?? 0), 3) }}
        @elseif($purchases[0]->comment == 'Purchase Returned')
            Balance Due: {{ $currency }} {{ number_format(($purchases[0]->total_due ?? 0) + ($purchases[0]->collectedamount ?? 0), 3) }}
        @else
        Balance Due: {{ $currency }} {{ number_format(($purchases[0]->total ?? 0), 3) }}

            @endif
        </p>
        @if ($purchases[0]->comment == 'Purchase Returned')
        <p>Returned Amount: {{ $currency }} {{ number_format($purchases[0]->collectedamount ?? '0.00',3) }}</p>
        @else
        <p>Paid Amount: {{ $currency }} {{ number_format($purchases[0]->collectedamount ?? '0.00',3) }}</p>
        @endif
        @if ($purchases[0]->total_debit_note!==null)
        <p>Debit Note Amount: {{ $currency }} {{ number_format($purchases[0]->total_debit_note ?? '0.00',3) }}</p>
        @endif

        <p>Balance: {{ $currency }} {{ number_format(($purchases[0]->total_due)-($purchases[0]->total_debit_note) ?? '0.00',3) }}</p>
    </div>
</div>
@endif

            <div class="bottom-line"></div>

            <!-- Signature Section -->
            <div class="signature-line">
                Signature
            </div>
        </div>
    </div>
</body>
</html>
