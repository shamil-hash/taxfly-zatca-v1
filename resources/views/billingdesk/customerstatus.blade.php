<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Customer Summary</title>
    @include('layouts/usersidebar')
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
                        margin-bottom: 20px;

        }

        th,
        td {
            border: 1px solid black;
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

     .section-title {
            margin-top: 30px;
            margin-bottom: 15px;
            color: #187f6a;
            font-weight: bold;
        }
        .inner-table {
    border-collapse: collapse;
    width: 100%;
}

.inner-table tr:not(:last-child) {
    border-bottom: 1px solid #ddd;
}

.inner-table td {
    padding: 2px 8px;
    border: none;
}

                           div.dataTables_wrapper div.dataTables_paginate ul.pagination li a {
            color: #187f6a !important;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.active a,
        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.active a:focus,
        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.active a:hover {
            background-color: #187f6a !important;
            border-color: #187f6a !important;
            color: white !important;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination li a:hover {
            background-color: #187f6a !important;
            border-color: #187f6a !important;
            color: white !important;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.disabled a {
            color: #6c757d !important;
        }

    </style>
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
        <div style="margin-top: 15px;margin-left:15px;">
            @include('navbar.billingdesknavbar')
        </div>
        @else
            <x-logout_nav_user />
        @endif
        <br><br>
        <x-admindetails_user :shopdatas="$shopdatas" />

        <div class="content-wrapper">
            <h2>Customer Summary</h2>
        <div class="section-title">Customer Invoices</div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table" id="invoiceTable">
                        <thead>
                            <tr>
                                <th width="5%">Sl No</th>
                                <th>Customer Name</th>
                                <th width="10%">Invoice No</th>
                                <th>Invoice Due</th>
                                <th>Balance Due</th>
                                <th width="10%">Invoice Date</th>
                                <th width="10%">Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchases as $purchase)
                            <tr data-due-date="{{ \Carbon\Carbon::parse($purchase->due_date)->format('Y-m-d') }}" data-total-due="{{ $purchase->total_due }}">
                                <td>{{ $loop->iteration }}</td> <!-- Serial number -->
                                    <td><b>{{ $purchase->credit_username }}</b></td>
                                    <td>{{ $purchase->transaction_id }}</td>
                                    <td>{{ $purchase->total }}</td>
                                    <td>{{ $purchase->total_due }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($purchase->invoice_date)->format('Y-m-d') }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($purchase->due_date)->format('Y-m-d') }}
                                    </td>
                                    <td class="overdue-days">--</td>
                                </tr>
                            @endforeach
                        </tbody>





                    </table>
                </div>
                  </div>

            <!-- Customer Product Summary Table -->
           <!-- Customer Product Summary Table -->
<div class="section-title">Customer Product Summary</div>
<div class="row">
    <div class="col-md-12">
        <table class="table" id="productTable">
            <thead>
                <tr>
                    <th width="5%">Sl No</th>
                    <th>Customer Name</th>
                    <th>Products & Quantities</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $productCounter = 1;
                    // Group products by customer
                    $groupedProducts = [];
                    foreach($productDetails as $product) {
                        $customerName = $product->customer_name ?? 'N/A';
                        if (!isset($groupedProducts[$customerName])) {
                            $groupedProducts[$customerName] = [];
                        }
                        $groupedProducts[$customerName][] = [
                            'product_name' => $product->product_name,
                            'total_quantity' => $product->total_quantity
                        ];
                    }
                @endphp

                @foreach($groupedProducts as $customerName => $products)
                <tr>
                    <td>{{ $productCounter++ }}</td>
                    <td>{{ $customerName }}</td>
                    <td>
                        <table class="inner-table" style="width:100%;border:none;">
                            @foreach($products as $product)
                            <tr style="border:none;">
                                <td style="width:70%;border:none;padding:2px 8px;">{{ $product['product_name'] }}</td>
                                <td style="width:30%;border:none;padding:2px 8px;text-align:right;">{{ $product['total_quantity'] }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
            </div>
        </div>
    </div>
</body>
</html>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
 $(document).ready(function() {
        $('#invoiceTable').DataTable({
            order: [[0, 'asc']]
        });

        $('#productTable').DataTable({
            order: [[0, 'asc']]
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const today = new Date(); // Get today's date (current date)

        // Loop through all table rows
        document.querySelectorAll('tbody tr').forEach(row => {
            const dueDateStr = row.getAttribute('data-due-date'); // Get due date
            const totalDue = parseFloat(row.getAttribute('data-total-due')); // Get total_due amount

            if (dueDateStr) {
                const dueDate = new Date(dueDateStr); // Convert due date to Date object

                // Calculate the overdue days
                let overdueDays = Math.floor((today - dueDate) / (1000 * 60 * 60 * 24)); // Convert ms to days

                // Ensure overdue days are only counted when due date is in the past
                if (dueDate < today && totalDue > 0) {
                    // Apply light red background
                    row.style.backgroundColor = '#FFADAD'; // Light red
                    row.style.color = 'black'; // Keep text black

                    // Find the "Overdue Days" column and update it
                    const overdueDaysCell = row.querySelector('.overdue-days');
                    if (overdueDaysCell) {
                        overdueDaysCell.textContent = "Over due by " + overdueDays + " days";
                    }
                } else {
                    // Not overdue, show '--'
                    const overdueDaysCell = row.querySelector('.overdue-days');
                    if (overdueDaysCell) {
                        overdueDaysCell.textContent = "--";
                    }
                }
            }
        });
    });
</script>

