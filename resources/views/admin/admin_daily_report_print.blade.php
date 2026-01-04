<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Report</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            max-width: 600px;
            margin: 0 auto;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        h2 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
        }

        .table {
            font-size: 14px;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f0f0f0;
            /* Neutral gray background */
            color: #333;
            /* Dark gray text color */
        }

        .table-bordered td,
        .table-bordered th {
            border: 1px solid #ddd;
        }

        .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
            /* Light gray background for total rows */
        }

        .footer {
            text-align: left;
            margin-top: 20px;
        }

        .comment {
            color: red;
            /* Red color for the comment text */
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>User Report</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="2">TOTAL CASH BALANCE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>OPENING CASH</td>
                    <td>{{ $opening_balance }}</td>
                </tr>
                <tr>
                    <td>TOTAL SALES AMOUNT</td>
                    <td>{{ $tot_sales }}</td>
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


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="4">TOTAL CASH IN DRAW</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>#</th>
                    <th>Note</th>
                    <th>Quantity</th>
                    <th>TOTAL</th>
                </tr>
                <?php
                $number = 1;
                $total = 0;
                ?>
                @foreach ($cash_details as $detail)
                    <tr>
                        <td>{{ $number }}</td>
                        <td>{{ $detail->notes }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ $detail->note_type_total }}</td>
                    </tr>
                    <?php
                    $number++;
                    $total += $detail->note_type_total;
                    ?>
                @endforeach
                <tr class="total-row">
                    <td colspan="3">TOTAL CASH IN DRAW</td>
                    <td>{{ $total }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            @if ($total != $total_amount)
                <h5 class="comment">Not Equal</h5>
            @endif
        </div>
    </div>
</body>

</html>