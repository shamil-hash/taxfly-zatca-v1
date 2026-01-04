<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>User Report Reciept</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .total-row,
        .totalexpenses {
            font-weight: bold;
            border-top: 2px solid #000;
        }

        .section-title {
            font-weight: bold;
            border-bottom: 2px solid #000;
            margin-top: 20px;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
        }

        .footer p {
            margin: 5px 0;
        }

        .print-button {
            text-align: center;
            margin-top: 20px;
        }

        .print-button a {
            color: white;
            text-decoration: none;
        }
    </style>
    <style>
        @media print {
            body {
                visibility: hidden;
                font-size: 9.9px;
                height: 100%;
            }

            .print-container,
            .print-container * {
                visibility: visible;
            }

            .print-button {
                display: none;
            }

            .print-container {
                width: 80mm;
                margin: 0;
                padding: 0;
            }

            .card {
                border: 0;
                margin: 0;
                width: 350px;
            }

            .align {
                font-size: 2rem;
                padding-bottom: 1rem;
                margin-top: 0;
            }

            .cashdiv {
                padding-top: 1rem;
            }

            .cashdiv h4 {
                font-weight: 600;
                font-size: 14px;
            }

            .cashdiv h3 {
                padding-bottom: 10px;
                font-size: 2rem;
            }

            .comment {
                margin: 0;
                padding-top: 0;
            }

            @page {
                size: 320px 800px;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Page Content Holder -->
    <div id="content">
        <div class="print-container">
            <div class="header">
                <h2>User Report</h2>
            </div>

            <div>
                @foreach ($userdatas as $userdata)
                    <span><b>NAME :</b> {{ $userdata['name'] }}</span> <br /><br />
                    <span><b>Login Info :</b> {{ date('d-M-Y : h:i:s A', strtotime($userdata['last_login'])) }}</span>
                @endforeach
            </div>

            <div class="section-title">TOTAL CASH BALANCE</div>
            <table class="table">
                <tbody>
                    <tr>
                        <td>OPENING CASH</td>
                        <td>{{ $open_balance }}</td>
                    </tr>
                    <tr>
                        <td>TOTAL SALES AMOUNT</td>
                        <td>{{ $total_sales_amount }}</td>
                    </tr>
                    <tr>
                        <td>CREDIT PAYMENT</td>
                        <td>{{ $creditPayment }}</td>
                    </tr>
                    <tr>
                        <td>POS/BANK SALE AMOUNT</td>
                        <td>{{ $posBankSale }}</td>
                    </tr>
                    <tr>
                        <td>CREDIT SALE AMOUNT</td>
                        <td>{{ $creditSale }}</td>
                    </tr>
                    <tr>
                        <td>SERVICE</td>
                        <td>{{ $service }}</td>
                    </tr>
                    <tr>
                        <td>INCOME</td>
                        <td>{{ $income }}</td>
                    </tr>
                    <tr>
                        <td>EXPENSE</td>
                        <td>{{ $expense }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>TOTAL CASH IN DRAW</td>
                        <td>{{ $total_amount }}</td>
                    </tr>
                </tbody>
            </table>




            <div class="section-title">TOTAL CASH IN DRAW</div>
            <table class="table">
                <tbody>
                    <tr>
                        <th>Note</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                    <?php
                    $total = 0;
                    ?>
                    @foreach ($cash_details as $detail)
                        <tr>
                            <td>{{ $detail->notes }}</td>
                            <td>{{ $detail->quantity }}</td>
                            <td>{{ $detail->note_type_total }}</td>
                        </tr>
                        <?php
                        $total += $detail->note_type_total;
                        ?>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2">TOTAL CASH IN DRAW</td>
                        <td>{{ $total }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="footer">
                @if ($total != $total_amount)
                    <h5 style="color: red;" align="left" class="comment">Not Equal</h5>
                @endif
                <p>____________________________________</p>
                <p>Report generated: {{ $Reportgenerated }}</p>
            </div>
            <div class="print-button">
                <button type="submit" name="userrepprint" id="userrepprint" class="btn btn-primary">
                    <a href="/userreport_pdf_print/{{ $trans_id }}">Print</a>
                </button>
            </div>
        </div>

    </div>
</body>

</html>
