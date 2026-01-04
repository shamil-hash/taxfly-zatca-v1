<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Our Custom CSS -->

 <style>
        * {
            margin-bottom: 0px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            white-space: nowrap;
            overflow-x: auto;
        }

        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 6px;
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

        table.custom-table {
            table-layout: fixed;
        }

        .custom-table thead {
            background: black;
        }

        .custom-table thead th {
            border: 0;
            color: #ffffff;
            font-size: 1.0rem;
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
          .invoice-container {
    padding: 20px;
    font-family: Arial, sans-serif;
    color: #333;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.invoice-header {
    margin-bottom: 20px;
}

.heading-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.heading-table td {
    padding: 10px;
    vertical-align: top;
}

.logo img {
    display: block;
}



.recipient-info, .statement-info {
    padding: 10px;
}

.account-summary h5 {
    margin-bottom: 10px;
}

.summary-table {
    width: 100%;
    border-collapse: collapse;
}

.summary-table td {
    padding: 8px 12px;
    border-bottom: 1px solid #ddd;
}



.mb-4 {
    margin-bottom: 1.5rem;
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
        <div class="row gutters">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 text-center">
      @if(Session('softwareuser'))
                                     @if ($logo!=null)
                                            <img src="{{ asset($logo) }}" alt="Branch Logo" class="imagecss" style="padding-bottom:15px;width:200px;height:60px;">
                                            @endif
                                            @endif
                                            </div>
        </div>


        <table class="heading">
            <tr>
                <td>
                    <div align="left">
                        <div style="display: inline-block; text-align: left;">
                            @if (Session('adminuser'))
                        <b>{{ strtoupper($adminname) }}</b><br>
                        <b>{!! nl2br(e(strtoupper($admin_address))) !!}</b><br>
                    @elseif(Session('softwareuser'))
                    <b>{{ strtoupper($company) }}</b><br>
                    <b>Address: </b> {{ucfirst( $Address) }}<br>
                    @endif
                    @if ($admintrno)
                    <b>TRN:</b> {{ $admintrno }}<br>
                    @endif
                    @if ($po_box)
                    <b>PO box:</b> {{ $po_box }}<br>
                    @endif
                    @if ($tel)
                    <b>Mob:</b> {{ $tel }}<br>
                    @endif
                        </div>
                    </div>
                </td>
                <td>
                    <div align="right">
                        <div style="display: inline-block; text-align: left;">
                            <b>To:</b>
                <b>{{ $credit_supplier_name }}</b><br>
                <b>Branch:</b> {{ $credit_branchname }}<br>
                <b>Mob:</b> {{ $credit_phone }}<br>
                <b>E-mail:</b> {{ $credit_email }}
                        </div>
                    </div>
                </td>
            <tr>
        </table>

        <!-- Statement Section (Centered) -->
        <div class="row gutters text-center mt-4">
            <div class="col-12">
                <p class="heading-para"><b>Statement of Accounts</b></p>
                <p class="date-para">
                    @if ($startDate != null && $endDate != null)
                        {{ date('d M Y', strtotime($startDate)) }}
                        To
                        {{ date('d M Y', strtotime($endDate)) }}
                    @elseif ($startDate == null && $endDate == null)
                        {{ date('d M Y', strtotime($cash_start_date)) }}
                        To
                        {{ date('d M Y', strtotime($cash_end_date)) }}
                    @endif
                </p>
            </div>
        </div>

        <!-- Account Summary Section (Centered) -->
        <div class="row gutters text-center mt-4">
            <div class="col-12">
                <p><b>Account Summary</b></p>
                <table class="table table-bordered d-inline-block">
                    <tr>
                        <td>Amount Received</td>
                        <td>{{ $currency }}
                            {{ number_format($updated_balance, 3) }}</td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
</div>
                            <div class="invoice-body">
                                <!-- Row start -->
                                <div class="row gutters">
                                    <div class="col-lg-10 col-md-10 col-sm-10">
                                        <div class="table-responsive">
                                            <table class="table custom-table m-0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>Sl</th>
                                                        <th>Date</th>
                                                        <th>Transaction <br /> Type</th>
                                                        <th>Details</th>
                                                        <th>Payments</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $number = 1; ?>
                                                    <?php $collectiontotal = 0; ?>
                                                    @foreach ($purchasedata as $purchasedat)
                                                        <tr>
                                                            <td>{{ $number }}</td>
                                                            <td>{{ date('d M Y', strtotime($purchasedat->created_at)) }}
                                                            </td>
                                                            <td>
                                                                @if ($purchasedat->comment == 'Payment Made')
                                                                    Payment <br /> Made
                                                                @elseif ($purchasedat->comment == 'Purchase Returned')
                                                                    Purchase <br /> Returned
                                                                @else
                                                                    {{ $purchasedat->comment }}
                                                                @endif
                                                            </td>
                                                            <td>

                                                                @if ($purchasedat->comment == 'Bill')
                                                                    <b>Invoice No.:</b> {{ $purchasedat->reciept_no }}
                                                                    <br />
                                                                @elseif ($purchasedat->comment == 'Payment Made')
                                                                    @if ($purchasedat->reciept_no != '')
                                                                        {{ $currency }}
                                                                        {{ $purchasedat->collected_amount }} <br />
                                                                        for Payment of <br />
                                                                        {{ $purchasedat->reciept_no }} <br /><br />
                                                                    @endif

                                                                    @if ($purchasedat->payment_type == 1)
                                                                        Payment - CASH <br />
                                                                    @elseif ($purchasedat->payment_type == 2)
                                                                        Payment - CHECK <br /> <br />

                                                                        @if ($purchasedat->check_number != '')
                                                                            Check Number -
                                                                            {{ $purchasedat->check_number }}
                                                                            <br /><br />
                                                                        @endif

                                                                        @if ($purchasedat->depositing_date != '')
                                                                            Depositing Date -
                                                                            {{ date('d M Y', strtotime($purchasedat->depositing_date)) }}
                                                                            <br />
                                                                        @endif

                                                                        @if ($purchasedat->bank_name != '')
                                                                            Bank - {{ $purchasedat->bank_name }} <br />
                                                                        @endif

                                                                        @if ($purchasedat->reference_number != '')
                                                                            Reference No. -
                                                                            {{ $purchasedat->reference_number }}
                                                                        @endif
                                                                    @endif
                                                                @elseif ($purchasedat->comment == 'Purchase Returned')
                                                                    @if ($purchasedat->reciept_no != '')
                                                                        <b>Invoice No.:</b>
                                                                        {{ $purchasedat->reciept_no }}
                                                                    @endif
                                                                @elseif ($purchasedat->comment == '')
                                                                    @if ($purchasedat->reciept_no != '')
                                                                        <b>Invoice No.:</b>
                                                                        {{ $purchasedat->reciept_no }} <br />
                                                                    @endif

                                                                    @if ($purchasedat->payment_type == 1)
                                                                        Payment - CASH <br />
                                                                    @elseif ($purchasedat->payment_type == 2)
                                                                        Payment - CHECK <br /> <br />

                                                                        @if ($purchasedat->check_number != '')
                                                                            Check Number -
                                                                            {{ $purchasedat->check_number }}
                                                                            <br /><br />
                                                                        @endif

                                                                        @if ($purchasedat->depositing_date != '')
                                                                            Depositing Date -
                                                                            {{ date('d M Y', strtotime($purchasedat->depositing_date)) }}
                                                                            <br />
                                                                        @endif

                                                                        @if ($purchasedat->bank_name != '')
                                                                            Bank - {{ $purchasedat->bank_name }} <br />
                                                                        @endif

                                                                        @if ($purchasedat->reference_number != '')
                                                                            Reference No. -
                                                                            {{ $purchasedat->reference_number }}
                                                                        @endif
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($purchasedat->collected_amount != null || $purchasedat->collected_amount != 0)
                                                                    <b>{{ $currency }}</b>
                                                                    {{ $purchasedat->collected_amount }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <?php $number++; ?>
                                                        <?php $collectiontotal = $collectiontotal + $purchasedat->collected_amount; ?>
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="5">&nbsp;</td>
                                                    </tr>
                                                    <tr>

                                                        <td colspan="3"></td>
                                                        <td>Total Invoice <br /> Amount</td>
                                                        <td><b>{{ $currency }}</b>
                                                            {{ $updated_balance }}</td>
                                                    </tr>
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
