<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Our Custom CSS -->
</head>
<style>
    #footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 60px;
        /* Height of the footer */
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid black;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: white
    }

    th {
        background-color: black;
        color: white;
    }
</style>
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #fafafa;
    }

    table.heading {
        border-collapse: collapse;
        width: 100%;
    }

    table.heading th,
    table.heading td {
        border: 1px solid white;
        text-align: left;
        padding: 8px;

        padding-top: 0;
        /* add */
        margin-top: 0;
        /* add */
    }

    table.heading th {
        background-color: white;
        color: white;
    }
</style>
<style>
    body {
        color: #2e323c;
        background: transparent;
        position: relative;
        height: 100%;
        font-size: 1.2rem;
    }

    .custom-table {
        border: 1px solid #e0e3ec;
    }

    .custom-table thead {
        background: black;
        width: "100%";
    }

    .custom-table thead th {
        border: 0;
        color: #ffffff;
        font-size: 0.95rem;
    }

    .custom-table>tbody tr:hover {
        background: #fafafa;
    }

    .custom-table>tbody tr:nth-of-type(even) {
        background-color: #ffffff;
    }

    .custom-table>tbody td {
        border: 1px solid #e6e9f0;
        font-size: 0.95rem;
    }

    .card {
        background: #ffffff;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        border: 0;
        margin-bottom: 1rem;
    }

    .text-success {
        color: black !important;
    }

    .text-muted {
        color: #9fa8b9 !important;
    }

    .custom-actions-btns {
        margin: auto;
        display: flex;
        justify-content: flex-end;
    }

    .custom-actions-btns .btn {
        margin: .3rem 0 .3rem .3rem;
    }

    .custom-table>tbody td:nth-child(2) {
        word-wrap: break-word;
    }

    @media print {
        .custom-table>tbody td:nth-child(2) {
            word-wrap: break-word;
        }
    }
</style>

<body>
    <!-- Page Content Holder -->
    <div id="content">
        <div class="container">
            <div class="row gutters">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="invoice-container">
                                <div class="invoice-header">
                                    <!-- Row start -->
                                    <div class="row gutters">
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                            <div class="custom-actions-btns mb-5">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Row end -->
                                    <!-- Row start -->
                                    <div class="row gutters">
                                        <div class="image-container" style="text-align: center;">
                                            {{-- ---------------------------------------------------------------------- --}}
                                        @if ($logo!=null)
                                            <img src="{{ asset($logo) }}" alt="Branch Logo" class="imagecss" style="padding-bottom:15px;">
                                            @endif                                            {{-- -------------------------------------------------------------------------------- --}}
                                            <br>
                                        </div>
                                        <div align="right" class="col-lg-6 col-md-6 col-sm-6">

                                            <!--<img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('https://avolon.netplexsolution.com/generatepublic-pdf/' . $enctrans, 'QRCODE') }}"-->
                                            <!--    alt="barcode" />-->

                                            <!-- {{-- {{ $enctrans }} --}} -->
                                        </div>
                                    </div>

                                    <table class="heading">
                                        <tr>
                                            <td>
                                                <div align="left">
                                                    <div style="display: inline-block; text-align: left;">
                                                        <h4><b>{{ strtoupper($company) }}</b></h4>
                                                        <b>TRN: </b>{{ $admintrno }}
                                                        <br>
                                                        <b>PO box: </b> {{ $po_box }}
                                                        <br>
                                                        <b>Branch:</b> {{ ucfirst($branchname) }}
                                                        <br>
                                                        <b>Tel: </b> {{ $tel }}
                                                        <br>
                                                        <b>Address: </b> {{ucfirst( $Address) }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div align="right">
                                                    <div style="display: inline-block; text-align: left;">
                                                        <b> Credit Note No : </b>{{ $creditnote }}
                                                        <br>
                                                        <b> Invoice No : </b>{{ $trans }}
                                                        <br>
                                                        <b> Customer :</b> {{ $custs }}
                                                        <br>
                                                        @if ($trn_number != '' || $trn_number != null)
                                                            <b> TRN No :</b> {{ $trn_number }}
                                                            <br>
                                                        @endif
                                                        @if($billingAdd)
                                                        <b> Billing Address:</b> {{ $billingAdd }}
                                                        <br>
                                                        @endif
                                                        @if($deliveryAdd)
                                                            <b>Delivery Address:</b> {{ $deliveryAdd }}
                                                            <br>
                                                        @endif
                                                        @if ($billphone != '' || $billphone != null)
                                                            <b>Phone:</b>{{ $billphone }}
                                                            <br>
                                                        @endif
                                                        @if ($billemail != '' || $billemail != null)
                                                            <b>E-Mail:</b>{{ $billemail }}
                                                            <br>
                                                        @endif
                                                        <b>Invoice &nbsp;Date&nbsp;&nbsp;:</b> {{ $date }}
                                                        <br>
                                                        <b>Supplied Date:</b> {{ $supplieddate }}
                                                        <br>
                                                        @if ($payment_type == 'CREDIT')
                                                            <b>Payment
                                                                Type:</b>{{ $payment_type }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        @elseif ($payment_type == 'CASH')
                                                            <b>Payment Type:</b>
                                                            {{ $payment_type }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        @elseif ($payment_type == 'BANK')
                                                            <b>Payment Type:</b>
                                                            {{ $payment_type }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        @elseif ($payment_type == 'POS CARD')
                                                            <b>Payment Type:</b>
                                                            {{ $payment_type }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        <tr>
                                    </table>
                                </div>
                                <br>
                                <hr style="border-top: dotted 3px;" />
                                </hr>
                                <div align="center">
                                    <p
                                        style="font-family:'Poppins', sans-serif;font-size: 1.4em;background-color:black;color: white;">
                                        CREDIT NOTE</p>
                                </div>

                                <br />
                                <!-- Row end -->
                            </div>
                            <div class="invoice-body">
                                <!-- Row start -->
                                <div class="row gutters">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table custom-table m-0">
                                                <thead>
                                                    <tr>
                                                        <th width="">Sl</th>
                                                        <th width="">Description</th>
                                                        <th width="">Quantity</th>
                                                        <th width="">Unit</th>
                                                        <th width="">Rate</th>
                                                        <!-- {{-- @if ($vat_type == 1)
                                                            <th width=""> Inclusive<br />Rate</th>
                                                        @endif --}} -->
                                                        <th width="">{{$tax}} (%)</th>
                                                        <th width="">{{$tax}} Amount</th>
                                                        <th width="">Net Rate</th>
                                                        <th width="">Discount Amount</th>
                                                        <th width="">Total Amount</th>
                                                        <th width="">Credit Note <br/>Amount</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $number = 1; ?>
                                                    @foreach ($details as $detail)
                                                        <tr>
                                                            <td>{{ $number }}</td>
                                                            <td class="product-name">
                                                                <!--{{ $detail->product_name }} -->

                                                                <!-- {{-- < ?php
                                                                $chunks = [];
                                                                // Split the product name into chunks
                                                                while (strlen($detail->product_name) > 0) {
                                                                    $chunk = mb_substr($detail->product_name, 0, 20);
                                                                    $detail->product_name = mb_substr($detail->product_name, 20);
                                                                    $chunks[] = $chunk;
                                                                }

                                                                // Display the chunks on separate lines
                                                                echo implode('<br>', $chunks);
                                                                ?> --}} -->

                                                                <?php
                                                                $chunks = [];
                                                                $currentWord = '';
                                                                $wordLength = 0;

                                                                for ($i = 0; $i < mb_strlen($detail->product_name); $i++) {
                                                                    $char = mb_substr($detail->product_name, $i, 1);

                                                                    if ((mb_strlen($currentWord) > 20 && mb_strpos($char, ' ') !== false) || (mb_strlen($currentWord) > 20 && $char === '_')) {
                                                                        $chunks[] = $currentWord;
                                                                        $currentWord = '';
                                                                        $wordLength = 0;
                                                                    } else {
                                                                        $currentWord .= $char;
                                                                        $wordLength++;
                                                                    }
                                                                }

                                                                if ($currentWord) {
                                                                    $chunks[] = $currentWord;
                                                                }

                                                                echo implode('<br>', $chunks);
                                                                ?>


                                                            </td>
                                                            <td>{{ $detail->quantity }}</td>
                                                            <td> {{ $detail->unit }}</td>
                                                            <td>{{ $detail->mrp }}</td>
                                                            <!-- {{-- @if ($detail->vat_type == 1)
                                                                <td>{{ $detail->inclusive_rate }}</td>
                                                            @endif --}} -->
                                                            <td>{{ $detail->fixed_vat }}</td>

                                                            <td>{{ $detail->vat_amount }}</td>
                                                            <td>{{ $detail->netrate }}</td>
                                                            <td>{{ $detail->discount }}</td>
                                                            <td>{{ $detail->total_amount }}</td>
                                                            <td>{{ $detail->credit_note_amount }}</td>

                                                        </tr>
                                                        <?php $number++; ?>
                                                    @endforeach
                                                    <!-- total -->
                                                    <tr>
                                                        @if ($vat_type == 1)
                                                            <td colspan="7" style="font-size:15px;">
                                                            @elseif ($vat_type == 2)
                                                            <td colspan="7" style="font-size:15px;">
                                                        @endif
                                                        <p>
                                                            <br>
                                                            <br>
                                                        </p>
                                                        <h6 style="" class="text-success">
                                                            <strong>Amount in Words :
                                                                <br />
                                                                {{ $amountinwords }}
                                                            </strong>
                                                        </h6>
                                                        </td>
                                                        <td colspan="2">
                                                            <br />
                                                            <p style="color: #999;">
                                                                Subtotal<br>
                                                                {{$tax}}<br>
                                                                Discount<br />
                                                            </p>
                                                            <h6 class="text-success"><strong>Grand Total</strong></h6>
                                                        </td>
                                                        <td colspan="2">
                                                            <br />
                                                            <p style="color: #999;">
                                                                @if ($vat_type == 1)
                                                                    {{ number_format($rate - $vat, 3) }}<br />
                                                                @elseif ($vat_type == 2)
                                                                    {{ number_format($rate, 3) }}<br />
                                                                @endif

                                                                {{ $vat }}<br>
                                                                {{ $discount_amt == null && $Main_discount_amt == null ? 0 : $discount_amt + $Main_discount_amt }}
                                                                <br />
                                                            </p>
                                                            <h5 class="text-success">
                                                                <strong>
                                                                    @if ($currency == '₹')
                                                                        INR
                                                                    @else
                                                                        {{ $currency }}
                                                                    @endif
                                                                    {{ $grandinnumber }}
                                                                </strong>
                                                            </h5>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- Row end -->
                            </div>



<br>
                            <div class="invoice-footer">
                                The above mentioned goods are recieved in good condition.
                                Goods once sold will not be taken back or exchanged in any condition.
                                <br>
                                <br>
                                <div id="footer">
                                    <table class="heading">
                                        <tr>
                                            <td></td>
                                            <td>
                                                Seller's Signature:
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                            </td>
                                            <td align="right">
                                                Reciever's Signature:
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
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
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
