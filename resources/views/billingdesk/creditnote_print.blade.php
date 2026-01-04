<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Note</title>
    @include('layouts/usersidebar')
    <style>


        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            border: 1px solid #d1d1d1; /* Adjusted border color */
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            color: black; /* Maintained the same color for header */
        }

        .info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .info div {
            width: 48%;
        }

        .info p {
            margin: 6px 0;
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table, th, td {
            border: 1px solid #d1d1d1; /* Adjusted border color */
        }

        th, td {
            padding: 10px 12px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #333;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .signature {
            margin-top: 30px;
            text-align: right;
        }

        .signature p {
            display: inline-block;
            margin-right: 50px;
            font-size: 14px;
        }

        .print-button {
            text-align: center;
            margin-top: 20px;
        }




        @media print {
            .print-btn {
                display: none;
            }

            .container {
                border: none;
                box-shadow: none;
            }
        }
    </style>
    <script>
        function printCreditNote() {
            window.print();
        }
    </script>
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
<body>
    <div id="content">
        @if ($adminroles->contains('module_id', '30'))
        <div style="margin-left:15px;margin-top:15px;">

            @include('navbar.billingdesknavbar')
            </div>
                @else
        <x-logout_nav_user />
    @endif
    <div align="right">
        <a href="/creditnote_history" class="btn btn-info ">Credit Note History</a>
        <a href="" class="btn btn-info">Refresh</a>
    </div>
        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif

        <x-admindetails_user :shopdatas="$shopdatas" />

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
                    <td>{{number_format($detail->sell_cost,3) }}</td>
                    <td>{{ number_format($detail->quantity,3) }}</td>
                    <td>{{ number_format($detail->total,3) }}</td>
                    <td>{{ $detail->credit_note }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <p>Signature:</p> ..........................................
    </div>

    <div class="print-button">
        <button type="button" class="btn btn-primary" onclick="window.location.href='/credit_pdf_print?transaction_id={{ $creditNote->transaction_id }}';">
            Print
        </button>
    </div>
</div>

</body>
</html>
