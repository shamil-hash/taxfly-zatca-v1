<!doctype html>
<html lang="en">

<head>
    <title>{{ $transaction_id }}</title>
    <style type="text/css">
        .div-1 {
            background-color: white;
            padding-top: 5px;
            padding-bottom: 5px;

            border-radius: 5px;
        }

        .heading {
            /*padding: 0px 1em;*/
            width: 100%
        }

        .num {
            display: none;
        }

        .custom-table {
            border: 2px solid #e0e3ec;
            /* padding-left: 2em;
            padding-right: 4em; */
            border-collapse: collapse;
            width: 100%;
            padding: 0px 2em;
        }

        .custom-table>tbody td {
            border: 1px solid #e6e9f0;
            padding: 5px;

        }

        @page {
            margin: 0px;
            /*size: 330px 500px;*/
            size: 320px 800px;
        }


        * {
            font-family: Verdana, Arial, sans-serif;
            /*font-size: xx-small;*/
            font-size: 12px;
        }

        tfoot tr td {
            font-weight: bold;
            /*font-size: x-small;*/
            font-size: 12px;
        }

        .gray {
            background-color: lightgray
        }

        .admindat {
            text-transform: uppercase;
        }

        .headadmin {
            /*margin-top: 15px;*/
        }

        .adjust {
            margin: 0;
            margin-top: 2px;
        }

        .welcome {
            margin-top: 1em;
        }

        /* .welcome>h1 {
            font-size: 14px;
        } */
        h1 {
            font-size: 15px;
        }
    </style>
</head>

<body>
    <!-- <div align="center">
        <img src="{{ public_path('/storage/logo/logo.jpg') }}" alt="logo" width="200">
    </div> -->
    <div class="div-1" align="center">
        <div align="center" class="headadmin">
            <h1 class="admindat">{{ $admin_name }}</h1>
            <h6 class="adjust">
                @if ($po_box != '')
                    <b>PO BOX:</b>&nbsp;&nbsp;{{ $po_box }}
                @endif
            </h6>
            <h6 class="adjust">
                {{ $branchname }}
            </h6>
            <h6 class="adjust">
                @if ($tel != '')
                    <b>TEL:</b>&nbsp;&nbsp;{{ $tel }}
                @endif
            </h6>
            <h6 class="adjust">
                @if ($emailadmin != '')
                    <b>E-Mail:</b>&nbsp;&nbsp;{{ $emailadmin }}
                @endif
            </h6>

        </div>
        <table class="heading">
            <tr>
                <td>
                    <div align="left">
                        <!--<b>CR No:</b>&nbsp;&nbsp;{{ $cr_num }}-->

                        <!--<b>TR &nbsp;No&nbsp;&nbsp;:</b>{{ $admintrno }}-->
                        <!--<br>-->
                        <!-- {{ $branchname }}
                        <br>
                        <b>TEL:</b>&nbsp;&nbsp;{{ $tel }} -->
                    </div>
                </td>
                <td>
                    <div align="right">

                    </div>
                </td>
            </tr>
        </table>
        <br>
        <table class="heading">
            <tr>
                <td>
                    <div align="left" style="font-size:10px;">
                        <b> Invoice No : </b>{{ $trans }}
                        <br />
                        <b> Customer:</b> {{ $custs }}
                        <br />
                        <b> TRN No :</b> {{ $trn_number }}
                        <br />
                        @if ($billphone != '')
                            <b> Phone :</b> {{ $billphone }}
                            <br />
                        @endif
                        @if ($billemail != '')
                            <b> E-Mail :</b> {{ $billemail }}
                            <br />
                        @endif
                    </div>
                </td>
                <td>
                    <div align="right" style="margin-left: -16px;font-size:10px; margin-right:0;">
                        <b>Invoice &nbsp;Date&nbsp;&nbsp;:</b> {{ $date }}
                        <br />
                        <b>Supplied Date:</b> {{ $supplieddate }}
                        <br />
                        @if ($payment_type == 'CREDIT')
                            <b>Payment Type:</b>{{ $payment_type }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        @elseif ($payment_type == 'CASH')
                            <b>Payment Type:</b> {{ $payment_type }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        @elseif ($payment_type == 'BANK')
                            <b>Payment Type:</b> {{ $payment_type }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        @elseif ($payment_type == 'POS CARD')
                            <b>Payment Type:</b>
                            {{ $payment_type }}&nbsp;&nbsp;&nbsp;
                        @endif
                    </div>
                </td>
            <tr>
        </table>
        <br /><br />

        <table class="table table-striped custom-table">
            <thead>
                <tr style="text-align:center">
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Rate</th>
                    <th>Total Amount <br>With {{$tax}}</th>
                </tr>
            </thead>
            <tbody>
                <?php $number = 1; ?>
                @foreach ($details as $detail)
                    <tr>
                        <td class="num">{{ $number }}</td>
                        <td>{{ $detail->product_name }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td> {{ $detail->unit }}</td>
                        <td>
                            @if ($detail->vat_type == 1)
                                {{ $detail->mrp }}
                            @elseif ($detail->vat_type == 2)
                                {{ $detail->mrp + $detail->vat_amount / $detail->quantity }}
                            @endif
                        </td>
                        <td>{{ $detail->total_amount }}</td>
                    </tr>
                    <?php
                    $noofitems = $number;
                    $number++;
                    ?>
                @endforeach
                <!-- total -->
                <tr>
                    <td colspan="2">
                        <br /><br /><br /><br />
                        <p>No. of Items : <?php echo $noofitems; ?><br></p>
                    </td>
                    <td>
                        <p>Subtotal</p>
                        <p>{{$tax}}</p>
                        <p>Discount</p>
                        <p class="text-success" style="font-size:8px;"><strong>Grand Total</strong></p>
                    </td>
                    <td>
                        <p>
                            @if ($vat_type == 1)
                            {{ number_format($rate - $vat, 3) }}<br />
                        @elseif ($vat_type == 2)
                            {{ number_format($rate, 3) }}<br />
                        @endif
                        </p>
                        <p>{{ $vat }}</p>
                        <p> {{ $discount_amt == null && $Main_discount_amt == null ? 0 : $discount_amt + $Main_discount_amt }}
                        </p>
                        <p class="text-success" style="font-size:8px;"><strong>{{ $grandinnumber }}
                                {{ $currency }}</strong></p>
                    </td>
                </tr>

            </tbody>
        </table>
        <div class="welcome">
            <h1>*** THANK YOU VISIT AGAIN ***</h1>
        </div>
    </div>
</body>

</html>
