<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Reciept</title>
    <script src="/javascript/printjs.js"></script>

    @include('layouts/usersidebar')
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
        }

        table.heading th {
            background-color: white;
            color: white;
        }
    </style>
    <style>
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
            background-color: #f2f2f2
        }

        th {
            background-color: #187f6a;
            color: white;
        }
    </style>
    <style>
        body {
            color: #2e323c;
            background: #f5f6fa;
            position: relative;
            height: 100%;
        }

        .invoice-container {
            padding: 1rem;
        }

        .invoice-container .invoice-header .invoice-logo {
            margin: 0.8rem 0 0 0;
            display: inline-block;
            font-size: 1.6rem;
            font-weight: 700;
            color: #2e323c;
        }

        .invoice-container .invoice-header .invoice-logo img {
            max-width: 130px;
        }

        .invoice-container .invoice-header address {
            font-size: 1.3 rem;
            color: #9fa8b9;
            margin: 0;
        }

        .invoice-container .invoice-details {
            margin: 1rem 0 0 0;
            padding: 1rem;
            line-height: 180%;
            background: #f5f6fa;
        }

        .invoice-container .invoice-details .invoice-num {
            text-align: right;
            font-size: 1.2rem;
        }

        .invoice-container .invoice-body {
            padding: 1rem 0 0 0;
        }

        .invoice-container .invoice-footer {
            text-align: center;
            font-size: 1.3rem;
            margin: 5px 0 0 0;
        }

        @media (max-width: 767px) {
            .invoice-container {
                padding: 1rem;
            }
        }

        .custom-table {
            border: 1px solid #e0e3ec;
        }

        .custom-table thead {
            background: #007ae1;
        }

        .custom-table thead th {
            border: 0;
            color: #ffffff;
        }

        .custom-table>tbody tr:hover {
            background: #fafafa;
        }

        .custom-table>tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }

        .custom-table>tbody td {
            border: 1px solid #e6e9f0;
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
            color: #187f6a !important;
        }
    </style>
    <style>
  .image-container {
    display: flex;
    justify-content: center; /* Center horizontally */
    align-items: center; /* Center vertically */
    height: 100%; /* Ensure the container has height for vertical alignment */
    text-align: center; /* Fallback for inline alignment */
}

.image-container img {
    width: 200px;
    height: 160px;
    margin-bottom: -110px;
}

    </style>
    <style>
        .div-1 {
            background-color: white;
        }

        .num {
            display: none;
        }

        #print .custom-table {
            border: 2px solid #e0e3ec;
            border-collapse: collapse;
            width: 100%;
            padding: 0px 2em;
        }

        #print .custom-table>tbody td {
            border: 1px solid #e6e9f0;
            padding: 5px;
        }

        .admindat {
            text-transform: uppercase;
        }

        .headadmin {
            margin-top: 15px;
        }

        .adjust {
            margin: 0;
            margin-top: 2px;
        }

        .welcome {
            margin-top: 1em;
        }
    </style>
    <style>
        @media print {
            #print {
                size: 80mm 297mm;
            }
        }
    </style>
    <!--<script>
        -- >
        <
        !-- function printPage(id) {
            -- >
            <
            !--
            var html = "<html>";
            -- >
            <
            !--html += document.getElementById(id).innerHTML;
            -- >
            <
            !--html += "</html>";
            -- >
            <
            !--
            var printWin = window.open('', '', 'left=0,top=0,width=600,height=600,toolbar=0,scrollbars=0,status =0');
            -- >
            <
            !--printWin.document.write(html);
            -- >
            <
            !--printWin.document.close();
            -- >
            <
            !--printWin.focus();
            -- >
            <
            !--printWin.print();
            -- >
            <
            !--printWin.close();
            -- >
            <
            !--
        }-- >
        <
        !--
    </script>-->

    <!-- <script>
        function printView() {
            window.print();
        }
    </script> -->

</head>

@php
    use App\Models\Softwareuser;
    use Illuminate\Support\Facades\DB;

    $userid = Session('softwareuser');
    $adminid = Softwareuser::Where('id', $userid)
        ->pluck('admin_id')
        ->first();
    $adminroles = DB::table('adminusers')
    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
    ->where('user_id', $adminid)
    ->get();
@endphp

<!-- Page Content Holder -->

<body>
    <div id="content">
        @if ($adminroles->contains('module_id', '30'))
        <div style="margin-left:20px;margin-top:15px;">

            @include('navbar.billingdesknavbar')
        </div>
        @else
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" id="sidebarCollapse" class="btn navbar-btn">
                    <i class="glyphicon glyphicon-chevron-left"></i>
                    <span></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/dashboard">Back</a></li>
                </ul>
            </div>
        </div>
    </nav>
        @endif

        <div align="right">

            @php
                $adminroles = DB::table('adminusers')
                    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
                    ->where('user_id', $adminid)
                    ->get();
            @endphp

            @foreach ($adminroles as $adminrole)
                @if ($adminrole->module_id == '23')
                    <!-- original -->
                    {{-- <a href="/generatetax-pdfsunmi/{{ $trans }}" class="btn btn-primary">PRINT SUNMI</a> --}}
                @endif

                @if ($adminrole->module_id == '24')
                    <!-- original -->
                    <a href="/creditnote-pdf/{{ $trans }}/{{$creditnote}}" class="btn btn-primary">Download</a>

                    {{-- <a href="/generatetax-pdf_a4/{{ $trans }}" class="btn btn-primary">A4 Print</a> --}}

                    {{-- <a href="/generatetax-pdf_a5/{{ $trans }}" class="btn btn-primary">A5 Print</a> --}}
                @endif

                @if ($adminrole->module_id == '25')
                    {{-- <a href="/sunmi_PDFPrint/{{ $trans }}" class="btn btn-primary">SUNMI PRINT PDF</a> --}}
                @endif

                {{-- @if ($adminrole->module_id == '29')
                    <a href="/billdeskfinalrecieptwithouttax/{{ $trans }}" class="btn btn-primary">WITHOUT
                        HEADER</a>
                @endif --}}
            @endforeach

            <!-- webclientprint -->
            <!-- <a href="/printers/{{ $trans }}" class="btn btn-primary">PRINT SUNMI</a> -->

            <!--<input type="button" class="btn btn-primary" value="Receipt Print" onclick="printPage('print');">-->


        </div>
        <br>
        <div class="print-container">
            <div class="row gutters">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="invoice-container">
                                <div class="invoice-header">
                                    <!-- Row start -->
                                    <div class="row gutters">
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">

                                        </div>
                                    </div>
                                    <!-- Row end -->

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

                                    {{-- <div class="row gutters">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                            <div style="display: inline-block; text-align: left;">

                                                <b>TR &nbsp;No&nbsp;&nbsp;: </b>{{ $admintrno }}
                                                <br>
                                                <b>PO BOX: </b>{{ $po_box }}
                                                <br>
                                                {{ $branchname }}
                                                <br>
                                                <b>TEL: </b>{{ $tel }}
                                            </div>
                                        </div>

                                    </div> --}}
                                    {{-- -------------------------------------------------------- --}}
                                    <table class="heading">
                                        <tr>
                                            <td>
                                                                                        <div align="left">
                                            <div style="display: inline-block; text-align: left;">
                                                <h4><b>{{ $company }}</b></h4>
                                                <b>TRN: </b>{{ $admintrno }}
                                                <br>
                                                <b>PO box: </b> {{ $po_box }}
                                                <br>
                                                <b>branch:</b> {{ $branchname }}
                                                <br>
                                                <b>Tel: </b> {{ $tel }}
                                                <br>
                                                <b>Address: </b> {{ $Address }}
                                            </div>
                                        </div>
                                            </td>
                                        </tr></table>
                                </div>
                                        {{-- ----------------------------------------------------------- --}}

                                    <hr style="border-top: dotted 3px;color: #dddddd;" />
                                    </hr>
                                    <div align="center">
                                        <p
                                            style="font-family:'Poppins', sans-serif;font-size: 1.4em;background-color:#187f6a;color: white;">
                                            CREDIT NOTE</p>
                                    </div>
                                    <!-- Row end -->
                                    <!-- Row start -->
                                    <table class="heading">
                                        <tr>
                                            <td>
                                                <div align="left">
                                                    <b> Credit Note No : </b>{{ $creditnote }}
                                                    <br>
                                                    <b> Invoice No : </b>{{ $trans }}
                                                    <br>
                                                    <b> Customer :</b> {{ $custs }}
                                                    <br>
                                                    <b> TRN No :</b> {{ $trn_number }}
                                                    <br>
                                                    <b>Phone:</b>{{ $billphone }}
                                                    <br>
                                                    <b>E-Mail:</b>{{ $billemail }}
                                                    <br>
                                                    @if($billingAdd!==null)
                                                    <b> Billing Address:</b> {{ $billingAdd }}
                                                    @endif
                                                    <br>
                                                    @if($deliveryAdd)
                                                        <b>Delivery Address:</b> {{ $deliveryAdd }}
                                                        @endif
                                                        <br>
                                                </div>
                                            </td>
                                            <td>
                                                <div align="right">
                                                    <div style="display: inline-block; text-align: left;">

                                                    <b>
                                                        Invoice Date:</b> {{ $date }}
                                                    <br>
                                                    <b>Supplied Date:</b> {{ $supplieddate }}
                                                    <br>
                                                    {{-- <b>Employee:</b> {{ $employeename }} --}}
                                                    @if ($payment_type == 'CREDIT')
                                                        <b>Payment
                                                            Type:</b>{{ $payment_type }}
                                                    @elseif ($payment_type == 'POS CARD')
                                                        <b>Payment Type:</b>
                                                        {{ $payment_type }}
                                                    @else
                                                        <b>Payment Type:</b>
                                                        {{ $payment_type }}
                                                    @endif
                                                    </div>
                                                </div>
                                            </td>
                                        <tr>
                                    </table>
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
                                                            <th>Sl</th>
                                                            <th>Description</th>
                                                            <th>Quantity</th>
                                                            <th>Unit</th>
                                                            <th>Rate</th>
                                                            @if ($vat_type == 1)
                                                                <th> Inclusive Rate</th>
                                                            @endif
                                                            <th>{{$tax}} (%)</th>
                                                            <th>{{$tax}} Amount</th>
                                                            <th>Net Rate</th>
                                                            <th>Discount Amount</th>
                                                            <th>Total Amount</th>
                                                            <th>Total Amount w/o <br /> Discount</th>
                                                            <th>Credit Note <br/>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- product details -->
                                                        <?php $number = 1; ?>
                                                        @foreach ($details as $detail)
                                                            <tr>
                                                                <td>{{ $number }}</td>
                                                                <td>{{ $detail->product_name }}</td>
                                                                <td>{{ $detail->quantity }}</td>
                                                                <td>{{ $detail->unit }}</td>
                                                                <td>{{ $detail->mrp }}</td>
                                                                @if ($detail->vat_type == 1)
                                                                    <td>{{ $detail->inclusive_rate }}</td>
                                                                @endif
                                                                <td>{{ $detail->fixed_vat }}</td>
                                                                <td>{{ $detail->vat_amount }}</td>
                                                                <td>{{ $detail->netrate }}</td>
                                                                <td>{{ $detail->discount }}
                                                                </td>
                                                                <td>{{ $detail->total_amount }}</td>
                                                                <td>{{ $detail->totalamount_wo_discount }}</td>
                                                                <td>{{ $detail->credit_note_amount }}</td>

                                                            </tr>
                                                            <?php $number++; ?>
                                                        @endforeach
                                                        <!-- total -->
                                                        <tr>
                                                            @if ($vat_type == 1)
                                                                <td colspan="10">
                                                                @elseif ($vat_type == 2)
                                                                <td colspan="9">
                                                            @endif
                                                            <p>
                                                                <br>
                                                                <br>
                                                            </p>
                                                            <h5 class="text-success"><strong>Amount in Words :
                                                                    {{ $amountinwords }}</strong></h5>
                                                            </td>
                                                            <td>
                                                                <p>
                                                                    Subtotal<br>
                                                                    {{$tax}}<br>
                                                                    Discount<br />
                                                                </p>
                                                                <h5 class="text-success"><strong>Grand Total</strong>
                                                                </h5>
                                                            </td>
                                                            <td>
                                                                <p>
                                                                    @if ($vat_type == 1)
                                                                        {{ number_format($rate - $vat, 3) }}<br />
                                                                    @elseif ($vat_type == 2)
                                                                        {{ number_format($rate, 3) }}<br />
                                                                    @endif
                                                                    {{ $vat }}<br>

                                                                    {{ $discount_amt == null && $Main_discount_amt == null ? 0 : $discount_amt + $Main_discount_amt }}
                                                                    <br />

                                                                <h5 class="text-success">
                                                                    <strong>{{ $grandinnumber }}
                                                                        &nbsp{{ $currency }}</strong>
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


                                <div class="invoice-footer">
                                    <br>
                                    <br>
                                    <br>
                                    The above mentioned goods are recieved in good condition.
                                    Goods once sold will not be taken back or exchanged in any condition.
                                    <BR>
                                    <BR>
                                    <BR>
                                    <BR>
                                    <div class="row gutters">
                                        <div class="col-xs-6">
                                            Seller's Signature:
                                        </div>
                                        <div class="col-xs-6">
                                            Reciever's Signature:
                                        </div>
                                        <br>
                                        <br>
                                        <br>
                                    </div>
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
