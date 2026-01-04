<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Note</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px; /* Reduced max-width */
            margin: 30px auto;
            padding: 20px; /* Reduced padding */
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .header {
            text-align: center;
            margin-bottom: 20px; /* Reduced margin */
        }

        .header h1 {
            margin: 0;
            font-size: 24px; /* Reduced font size */
            font-weight: bold;
            color: black;
        }

        .info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px; /* Reduced margin */
            font-size: 14px;
        }

        .info div {
            width: 48%; /* Maintain left/right split */
        }

        .info p {
            margin: 4px 0; /* Reduced margin */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px; /* Reduced margin */
            font-size: 12px; /* Reduced font size for the table */
        }

        table, th, td {
            border: 1px solid #dee2e6;
        }

        th, td {
            padding: 8px; /* Reduced padding */
            text-align: left;
        }

        th {
            background-color: #333;
            color: white;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        .signature {
            margin-top: 20px; /* Reduced margin */
            text-align: right;
        }

        .signature p {
            display: inline-block;
            margin-right: 50px;
        }

        .print-btn {
            padding: 10px 25px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px; /* Added margin */
        }

        .print-btn:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function printCreditNote() {
            window.print();
        }
    </script>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>CREDIT NOTE</h1>
    </div>

    <div class="info">
        <div>
            <p><strong>Company name:</strong> {{ $creditNote->company_name ?? 'N/A' }}</p>
            <p><strong>Details:</strong> {{ $creditNote->company_details ?? 'N/A' }}</p>
            <p><strong>Credit note No:</strong> {{ $creditNote->id }}</p>
            <p><strong>Date:</strong> {{ $creditNote->created_at }}</p>
        </div>
        <div>
            <p><strong>Customer name:</strong> {{ $creditNote->customer_name }}</p>
            <p><strong>Details:</strong> {{ $creditNote->customer_details ?? 'N/A' }}</p>
            <p><strong>Invoice no:</strong> {{ $creditNote->transaction_id ?? 'N/A' }}</p>
            <p><strong>Invoice date:</strong> {{ $buyProduct->created_at ?? 'N/A' }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Sell Cost</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Credit Note Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($creditNoteDetails as $detail)
                <tr>
                    <td>{{ $detail->product_name }}</td>
                    <td>{{ $detail->sell_cost }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ $detail->total }}</td>
                    <td>{{ $detail->credit_note }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <p>Signature:</p> ..........................................
    </div>
    
   
</div>

</body>
</html>
