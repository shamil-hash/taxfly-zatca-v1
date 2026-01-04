<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Our Custom CSS -->
</head>
<style>
    body {
        font-size: 2mm;
        /* 5px is too small */
    }

    #content {
        width: 80mm;
    }

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
        background-color: #20639B;
        color: white;
    }

    table.heading {
        border-collapse: collapse;
        width: 100%;
        padding-bottom: 1rem;
    }

    table.heading th,
    table.heading td {
        border: 1px solid white;
        text-align: left;

    }

    table.heading th {
        background-color: white;
        color: white;
    }

    div.headingsunmi {
        border-collapse: collapse;
        width: 100%;
    }

    .custom-table {
        border: 1px solid #e0e3ec;
    }

    /* .custom-table thead {
  background: #007ae1;
        } */
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
        color: #20639B !important;
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

    @page {
        margin: 0px;
        /* size: 219.2126px 302.3622px; */
        /* size: 302.3622px 302.3622px; */
        size: 80mm 80mm;
    }
</style>
<div id="content">

    <body>
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
                                    <div align="center">
                                        <img src="{{ public_path('/storage/logo/logo.jpg') }}" alt="logo"
                                            width="90px">
                                    </div>
                                    <table class="heading">
                                        <tr>
                                            <td>
                                                <div align="left" class="headingsunmi">
                                                    <b>CR No:</b>{{ $cr_num }}
                                                    <br>
                                                    <b>PO BOX:</b>{{ $po_box }}
                                                    <br>
                                                    {{ $branchname }}
                                                    <br>
                                                    <b>TEL:</b>{{ $tel }}
                                                </div>
                                            </td>
                                            <td>
                                                <div align="right">
                                                    <img
                                                        src="data:image/png;base64, {{ base64_encode(QrCode::size(40)->generate('https://plexpay.plexbill.com/generatepublic-pdf/' . $enctrans)) }} ">
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- Row end -->
                                    <!-- Row start -->
                                    <table class="heading">
                                        <tr>
                                            <td>
                                                <div align="left">
                                                    <b> Invoice No: </b>{{ $trans }}
                                                    <br>
                                                    <b> Customer&ensp;&thinsp;&thinsp;:</b> {{ $custs }}
                                                    <br>
                                                    <b> TRN No &nbsp;&nbsp;&nbsp;&nbsp;:</b> {{ $trn_number }}
                                                    <br>
                                                </div>
                                            </td>
                                            <td>
                                                <div align="right">
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
                                                    @endif
                                                </div>
                                            </td>
                                        <tr>
                                    </table>
                                    <!-- Row end -->
                                </div>
                                <div class="invoice-body">
                                    <!-- Row start -->
                                    <div class="row gutters">
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <div class="table-responsive">
                                                <table class="table custom-table">
                                                    <thead>
                                                        <tr>

                                                            <th>Item</th>
                                                            <th>Quantity</th>
                                                            <th>Rate</th>
                                                            <th>Total Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $number = 1; ?>
                                                        @foreach ($details as $detail)
                                                            <tr>
                                                                <td hidden>{{ $number }}</td>
                                                                <td>{{ $detail->product_name }}</td>
                                                                <td>{{ $detail->quantity }}</td>
                                                                <td>{{ $detail->mrp }}</td>
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
                                                                <br>
                                                                <br><br>
                                                                <p style="color: #999;">
                                                                    No. of Items : <?php echo $noofitems; ?><br>

                                                                </p>
                                                            </td>
                                                            <!-- <td colspan="2">
                                                                            <p>
                                                                                    <br>
                                                                                    <br>
                                                                            </p>
                                                                            <p class="text-success">
                                                                                <strong>Amount in Words :
                                                                                <br>
                                                                                {{ $amountinwords }}
                                                                                </strong>
                                                                            </p>
                                                                        </td> -->
                                                            <td>
                                                                <p style="color: #999;">
                                                                    Subtotal<br>
                                                                    {{$tax}}<br>
                                                                </p>
                                                                <p class="text-success" style="font-size:8px;">
                                                                    <strong>Grand Total</strong></p>
                                                            </td>
                                                            <td>
                                                                <p style="color: #999;">
                                                                    {{ $grandinnumber - $vat }}<br>
                                                                    {{ $vat }}<br>
                                                                </p>
                                                                <p class="text-success" style="font-size:8px;">
                                                                    <strong>{{ $grandinnumber }}
                                                                        {{ $currency }}</strong></p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Row end -->
                                </div>
                                <!-- <div class="invoice-footer">
                                                The above mentioned goods are recieved in good condition.
                                                Goods once sold will not be taken back or exchanged in any condition.
                                                <div>
                                                    <table class="heading">
                                                        <tr>
                                                            <td>Reciever's Signature:</td>
                                                            <td></td><td></td><td></td><td></td><td></td><td></td>
                                                            <td align="right">Seller's Signature:</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</div>

</html>
<script>
    var array = "{{ $trans }}";
    $("#trans_id").val(array);
</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
