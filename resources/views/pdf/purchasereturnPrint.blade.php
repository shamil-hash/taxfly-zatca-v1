<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <style>
        * {
            margin-top: 0;
            margin-left: 2px;
            margin-right: 1.5rem;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #fafafa;
            color: #2e323c;
            position: relative;
            height: 100%;
            font-size: 1.1rem;
            margin: 0 5px 0 2px;

        }

        .card {
            background: #ffffff;
            border-radius: 5px;
            border: 0;
            margin-bottom: 1rem;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th {
            background-color: black;
            color: white;
        }

        tr:nth-child(even) {
            background-color: white;
        }

        .custom-table {
            border: 1px solid #e0e3ec;
            width: 100%;
        }

        .custom-table th,
        .custom-table td {
            border: 1px solid #e6e9f0;
            font-size: 0.9rem;
            /* padding: 4px; */
            text-align: left;
            word-wrap: break-word;
        }

        .custom-table th {
            background: black;
            color: #ffffff;
            font-size: 0.9rem;
        }

        .text-success {
            color: black;
        }

        .invoice_table {
            border-collapse: collapse;

        }

        .invoice_table th,
        .invoice_table td {
            border: 1px solid black;
            padding: 5px;
            overflow: hidden;
            word-wrap: break-word;
        }

        .datatable .invoice_table td.column_1 {
            width: 30%;
            padding: 7px;
        }

        .datatable .invoice_table td.column_2 {
            width: 70%;
            padding: 7px;
        }

        .datatable #supp_table {
            width: 90%;

        }

        .invoice_table td {
            padding: 3px;
            font-size: 1rem;
        }

        /* Custom styles for specific elements */
        .footer {
            padding: 10px;
            border: 1px solid black;
            position: fixed;
            bottom: 0;
            width: 92%;
            max-width: 95%;

        }

        .footer p#adminii {
            text-align: center;
            text-decoration: underline;
        }

        .footer .sign_div {

            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            padding-bottom: 10px;
        }

        .footer .sign_div span {
            padding: 4rem;
            padding-bottom: 0rem;
            display: inline-block;
        }

        .footer .sign_div .sign {
            position: relative;
            z-index: 1;
            margin-top: 10px;
        }

        .footer .sign_div .sign::before {
            content: "";
            position: absolute;
            top: 4rem;
            bottom: 5px;
            left: 0;
            width: 100%;
            border-top: 1px dotted black;
        }

        .head-tax {
            margin-bottom: 5px;
            margin-top: -2.3rem;
        }

        @media print {
            body {
                visibility: hidden;

            }

            .print-container,
            .print-container * {
                visibility: visible;
            }

            @page {
                size: A4;
            }

            .image {
                position: relative;
            }

            .image img {
                width: 400px;
                height: 109px;
                position: absolute;
                left: 0;
            }

            .custom-table td:nth-child(2) {
                word-wrap: break-word;
            }
        }
    </style>
</head>

<body>
    <div id="content" class="container">
        <div class="row gutters">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="invoice-container">
                            <div class="invoice-header">
                                <div class="row gutters">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                        <div class="custom-actions-btns mb-5"></div>
                                    </div>
                                </div>
                                <table class="heading">
                                    <tr>
                                        <td>
                                            <div align="left">
                                                @if ($shopdatas->isNotEmpty())
                                                    @foreach ($shopdatas as $shop)
                                                        @if ($shop->logo)
                                                            <img class="imagecss"
                                                                src="{{ public_path('/storage/logo/' . $shop->logo) }}"
                                                                alt="logo" style="padding-bottom:15px;"
                                                                width="300px">
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div align="right">
                                                <div align="center">
                                                    <b>{{ strtoupper($adminname) }}</b><br />
                                                    <b>{!! nl2br(e(strtoupper($admin_address))) !!} </b>
                                                    <br />

                                                    @if ($admintrno)
                                                        <b>TR No: </b>{{ $admintrno }}<br />
                                                    @endif
                                                    @if ($po_box)
                                                        <b>PO BOX: </b>{{ $po_box }}<br />
                                                    @endif
                                                    @if ($tel)
                                                        <b>TEL: </b>{{ $tel }},
                                                    @endif
                                                    @if ($emailadmin)
                                                        <b>E-Mail: </b>{{ $emailadmin }}
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <div align="center" class="head-tax">
                                    <span
                                        style="font-size: 2rem; font-weight:bold; text-decoration:underline;padding-top:0;">
                                        TaxInvoice
                                    </span> <br />
                                    @if ($admintrno != '')
                                        <span style="font-size: 10px; font-weight:bold;" class="trn_style">TRN
                                            {{ $admintrno }}
                                        </span>
                                    @endif
                                </div>
                                <table class="heading datatable">
                                    <tr>
                                        <td>
                                            <div align="left">
                                                <table class="invoice_table" id="supp_table">
                                                    <tr>
                                                        <th>Requested From</th>
                                                    </tr>
                                                    <tr>
                                                        <td style="vertical-align: top;">
                                                            @if ($supplier != '')
                                                                <b>{{ strtoupper($supplier) }}</b> <br />
                                                                @if ($address_supp != '')
                                                                    <b>{{ strtoupper($address_supp) }}</b> <br />
                                                                @endif
                                                                @if ($mobile_supp != '')
                                                                    <b>Tel: {{ $mobile_supp }}</b> <br />
                                                                @endif
                                                                @if ($trn_supp != '')
                                                                    <b>TRN: {{ $trn_supp }}</b> <br />
                                                                @endif
                                                                {{-- @if ($email_supp != '')
                                                                    <b>Email: {{ $email_supp }}</b> <br />
                                                                @endif --}}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>

                                            </div>
                                        </td>
                                        <td>
                                            <div align="center">
                                                <span
                                                    style="font-size: 1.5rem; font-weight:bold;color:rgba(41, 39, 39, 0.836)">
                                                    GRV
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div align="right" style="margin: 0;">
                                                <table class="invoice_table">
                                                    <tr>
                                                        <td class="column_1" style="vertical-align: top;">Invoice No
                                                        </td>
                                                        <td class="column_2" style="vertical-align: top;">
                                                            {{ $trans }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="column_1" style="vertical-align: top;">Invoice
                                                            Date</td>
                                                        <td class="column_2" style="vertical-align: top;">
                                                            {{ $date }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="column_1" style="vertical-align: top;">Supplier
                                                        </td>
                                                        <td class="column_2" style="vertical-align: top;">
                                                            {{ $supplier }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <div class="invoice-body">
                                <div class="row gutters">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table custom-table m-0">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">Sl</th>
                                                        <th width="10%">Description</th>
                                                        <th width="8%">Quantity</th>
                                                        <th width="8%">Unit</th>
                                                        <th width="8%">Buycost</th>
                                                        <th width="8%">{{$tax}} Amount</th>
                                                        <th width="8%">Total Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $number = 1; ?>
                                                    @foreach ($details as $detail)
                                                        <tr>
                                                            <td>{{ $number }}</td>
                                                            <td>
                                                                <?php
                                                                $chunks = [];
                                                                $currentWord = '';
                                                                $wordLength = 0;

                                                                for ($i = 0; $i < mb_strlen($detail->product_name); $i++) {
                                                                    $char = mb_substr($detail->product_name, $i, 1);

                                                                    if (mb_strlen($currentWord) > 20 && mb_strpos($char, ' ') !== false) {
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
                                                            <td>{{ $detail->quantity }} </td>
                                                            <td>{{ $detail->unit }}</td>
                                                            <td>{{ $detail->buycost }}</td>
                                                            <td>{{ $detail->amount - $detail->amount_without_vat }}
                                                            </td>
                                                            <td>{{ $detail->amount }}</td>
                                                        </tr>
                                                        <?php $number++; ?>
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="3" style="font-size:15px;">
                                                            <p></p>
                                                            <h5 class="text-success">
                                                                <strong>Amount in Words : <br />
                                                                    {{ $amountinwords }}</strong>
                                                            </h5>
                                                        </td>
                                                        <td colspan="2">
                                                            <br />
                                                            <h5 class="text-success"><strong>Grand Total</strong></h5>
                                                        </td>
                                                        <td colspan="2">
                                                            <br />
                                                            <h5 class="text-success">
                                                                <strong>{{ $grandinnumber }}
                                                                    {{ $currency }}</strong>
                                                            </h5>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <footer class="footer">
                                <p id="adminii">{{ $adminname }}</p>

                                <div class="sign_div">
                                    <span class="sign">Prepared By</span>
                                    <span class="sign">Checked By</span>
                                    <span class="sign">StoreKeeper</span>
                                    <span class="sign">Delivered By</span>
                                </div>
                            </footer>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
