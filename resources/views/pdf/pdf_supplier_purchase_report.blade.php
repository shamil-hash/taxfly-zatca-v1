<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Our Custom CSS -->

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            text-align: left;

        }

        tr:nth-child(even) {
            background-color: white
        }

        th {
            background-color: black;
            color: white;
        }

        table.heading th {
            background-color: white;
            color: white;
        }

        table.heading {
            border-collapse: collapse;

        }

        table.heading th,
        table.heading td {
            border: 1px solid white;
            text-align: left;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #2e323c;
            background: transparent;
            position: relative;
            height: 100%;
            font-size: 1.2rem;
        }

        .custom-table {
            border: 1px solid #e0e3ec;
            width: 100%;
        }

        .custom-table thead {
            background: black;
        }

        .custom-table thead th {
            border: 1px;
            color: #ffffff;
            font-size: 1.0rem;
            width: 100%;
        }

        .custom-table>tbody tr:hover {
            background: #fafafa;
        }

        .custom-table>tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }

        .custom-table>tbody td {
            border: 1px solid #e6e9f0;
            font-size: 1.0rem;
            width: 100%;
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

        .custom-actions-btns {
            margin: auto;
            display: flex;
            justify-content: flex-end;
        }

        .custom-actions-btns .btn {
            margin: .3rem 0 .3rem .3rem;
        }

        .heading_para {
            font-family: 'Poppins', sans-serif;
            font-size: 2em;
            text-decoration: underline;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .date_para {
            padding-top: 0;
            text-decoration: underline;
        }
    </style>

</head>


<!-- Page Content Holder -->

<body>
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

                                                    <b>{{ strtoupper($adminname) }}</b>
                                                    <br />

                                                    <!--<b>Shop No.: 06,Near Baldiya R/A, <br />New Industrial Area, Ajman --->
                                                    <!--    U.A.E</b> <br />-->
                                                    <b>{!! nl2br(e(strtoupper($admin_address))) !!} </b>
                                                    <br />
                                                    @if ($admintrno != '' || $admintrno != null)
                                                        <b>TR &nbsp;No&nbsp;&nbsp;: </b>
                                                        {{ $admintrno }}
                                                        <br>
                                                    @endif

                                                    @if ($po_box != '' || $po_box != null)
                                                        <b>PO BOX: </b>{{ $po_box }}
                                                        <br />
                                                    @endif
                                                    @if ($tel != '' || $tel != null)
                                                        <b>TEL: </b>{{ $tel }}
                                                        <br>
                                                    @endif
                                                    @if ($emailadmin != '' || $emailadmin != null)
                                                        <b>E-Mail: </b>{{ $emailadmin }}
                                                    @endif

                                                </div>
                                            </td>
                                        <tr>
                                    </table>

                                    <table class="heading">
                                        <tr>
                                            <td>
                                                <div align="left">
                                                    <b>To</b> <br />
                                                    <b>{{ $credit_supplier_name }}</b> <br />
                                                    {{ $credit_branchname }} <br />


                                                    @if ($credit_phone != '')
                                                        <b>TEL:</b>
                                                        {{ $credit_phone }} <br />
                                                    @endif

                                                    @if ($credit_email != '')
                                                        <b>E-Mail:</b>
                                                        {{ $credit_email }} <br />
                                                    @endif

                                                </div>
                                            </td>
                                            <td>
                                                <div align="right">

                                                    <p class="heading_para">Supplier Stock Purchase Report</p> <br />
                                                    <p class="date_para">
                                                        {{ date('d M Y', strtotime($credit_start_date)) }}
                                                        To
                                                        {{ date('d M Y', strtotime($credit_end_date)) }}
                                                    </p>
                                                    <br />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50%"></td>
                                            <td style="line-height: 4pt">
                                                <div align="left" style="margin-left:16rem;">
                                                    <p><b>Account Summary</b></p> <br />
                                                    <table>
                                                        <tr>
                                                            <td>Total Price -</td>
                                                            <td>{{ $currency }}
                                                                {{ number_format($totalPrice, 3) }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <br>
                                <hr style="border-top: dotted 3px;" />

                                <!-- Row end -->

                            </div>
                            <div class="invoice-body">
                                <!-- Row start -->
                                <div class="row gutters">
                                    <div class="col-lg-10 col-md-10 col-sm-10">
                                        <div class="table-responsive">
                                            <table class="table custom-table m-0">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Bill No.</th>
                                                        <th>Payment Mode</th>
                                                        <th>Price with {{$tax}}</th>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3"><b>Total</b></td>
                                                        <td>
                                                            <b>{{ $currency }}
                                                                {{ number_format($totalPrice, 3) }}</b>

                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @foreach ($purchase_pdf_data as $pdfpuchasedata)
                                                        <tr>
                                                            <td>
                                                                {{ date('d M Y | h:i:s A', strtotime($pdfpuchasedata->created_at)) }}

                                                            </td>
                                                            <td>{{ $pdfpuchasedata->reciept_no }}</td>
                                                            <td>
                                                                @if ($pdfpuchasedata->payment_mode == 1)
                                                                    <b>Cash</b>
                                                                @elseif($pdfpuchasedata->payment_mode == 2)
                                                                    <b>Credit</b>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <b>{{ $currency }}</b>
                                                                {{ number_format($pdfpuchasedata->price, 3) }}
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- Row end -->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
