<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Payment History</title>

    @include('layouts/usersidebar')

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1.5px solid black;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        th {
            background-color: #187f6a;
            color: white;
        }

        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 1px solid #010101;
        }
        .content-wrapper {
            padding: 20px;
        }
        .voucher-btn {
            background-color: #187f6a;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            margin-right: 5px;
        }
        
        .voucher-btn:hover {
            background-color: #146b5a;
        }
        
        .cancel-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .cancel-btn:hover {
            background-color: #c82333;
        }
        
        .action-cell {
            white-space: nowrap;
        }
    </style>

</head>
@php
$branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();
@endphp
<body>
    <!-- Page Content Holder -->
    <div id="content">
        <div class="content-wrapper">
        <h2>Payment History for {{ $customer->name ?? 'Customer' }}</h2>
        @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @if(count($paymentTransactions) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount ({{ $currency }})</th>
                        <th>Payment Type</th>
                        <th>Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paymentTransactions as $transaction)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d-m-Y H:i') }}</td>
                        <td>{{ number_format($transaction->collected_amount, 2) }}</td>
                        <td>
                            @if($transaction->payment_type == 1 || $transaction->payment_type == '')
                                Cash
                            @elseif($transaction->payment_type == 2)
                                Cheque
                            @elseif($transaction->payment_type == 3)
                                Bank Transfer
                            @endif
                        </td>
                        <td>
                            @if($transaction->payment_type == 2)
                                Cheque No: {{ $transaction->cheque_number ?? 'N/A' }}<br>
                                Deposit Date: {{ $transaction->depositing_date ? \Carbon\Carbon::parse($transaction->depositing_date)->format('d-m-Y') : 'N/A' }}
                            @elseif($transaction->payment_type == 3)
                                Account: {{ $transaction->account_name ?? 'N/A' }}<br>
                                Reference: {{ $transaction->reference_number ?? 'N/A' }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <a href="{{ url('/receipt-voucher/' . $customer->id . '/' . $transaction->id) }}" 
                               class="voucher-btn" target="_blank">
                                View Voucher
                            </a>
                            @if($branch==2)
                            <form method="POST" action="{{ route('payment.cancel') }}" style="display: inline;">
                                @csrf
                                <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                <button type="submit" class="cancel-btn" 
                                    onclick="return confirm('Are you sure you want to cancel this payment of {{ number_format($transaction->collected_amount, 2) }}?')">
                                    Cancel
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p>No payment records found.</p>
            @endif
        </div>
    </div>
  
</body>

</html>