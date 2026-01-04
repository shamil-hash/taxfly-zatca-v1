<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service List PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }
        .customer-details {
            text-align: left;
            margin-bottom: 20px;
            padding: 0 20px;
        }
        .customer-details p {
            margin: 5px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color:black;
            color: white;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="image-container" style="text-align: center;">
        @if ($logo != null)
            <img src="{{ public_path($logo) }}" alt="Branch Logo" style="padding-bottom: 15px; width: 200px; height: 60px;">
        @endif
        <br>
    </div>
    <br>
    <div class="header">
        <h1>{{ $company }}</h1>
        <p>{{ $Address }}</p>
        <p>{{ $tel }}</p>
        <p>{{ $admintrno }}</p>

    </div>

    <!-- Customer Details Section -->
    <div class="customer-details">
        <p><strong>Customer Name:</strong> {{ $customer_name }}</p>
        <p><strong>Address:</strong> {{ $customer_address }}</p>
        <p><strong>Phone:</strong> {{ $customer_phone }}</p>
        <p><strong>Payment:</strong>
            @if($payment_mode == 1 || $payment_mode === null)
                Cash
            @elseif($payment_mode == 2)
                POS
            @endif
        </p>

    </div>

    <h3 style="text-align: center;">Service List</h3>
    <table>
        <thead>
            <tr>
                <th>Service ID</th>
                <th>Service Name</th>
                <th>Quantity</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach($services as $service)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $service->service_name }}</td>
                    <td>{{ number_format($service->quantity, 0) }}</td>
                    <td>{{ $currency }} {{ number_format($service->total_amount, 2) }}</td>
                </tr>
                @php
            $total += $service->total_amount;

                @endphp
            @endforeach
        </tbody>
        <tr style="font-weight: bold;font-size:16px;">
            <td colspan="3" class="total">Total</td>
            <td class="total" id="ttt"><b>{{ $currency }}</b> {{ number_format($total, 3) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
        <p>&copy; {{ date('Y') }} {{ $company }} All rights reserved.</p>
    </div>
</body>
</html>
