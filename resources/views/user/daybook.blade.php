<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day Book - {{ $company_name }}</title>
    @include('layouts/usersidebar')
    <style>

        .container {
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
        }
          .btn-primary {
            background-color: #187f6a;
            color: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 5px;
        }
        h3 {
            padding: 8px 0;
            margin-top: 25px;
            border-bottom: 2px solid #f0f0f0;
        }
        .form-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            border: 1px solid #e0e0e0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
        }
        th, td {
            border: 1px solid #e0e0e0;
            padding: 10px 12px;
            text-align: left;
        }
        thead th {
            background-color: #187f6a;
            font-weight: 600;
            color: white;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        tfoot {
            font-weight: bold;
            background-color: #f1f1f1;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        .col-sm-3, .col-sm-4, .col-sm-10 {
            padding: 0 10px;
            box-sizing: border-box;
        }
        .col-sm-3 {
            flex: 0 0 25%;
            max-width: 25%;
        }
        .col-sm-4 {
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }
        .col-sm-10 {
            flex: 0 0 83.333%;
            max-width: 83.333%;
        }
        .d-flex {
            display: flex;
        }
        .align-items-end {
            align-items: flex-end;
        }
        .me-2 {
            margin-right: 10px;
        }
        @media (max-width: 768px) {
            .col-sm-3, .col-sm-4, .col-sm-10 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 15px;
            }
            .container {
                width: 95%;
                padding: 15px;
            }
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
    <div style="margin: 15px;">
        @include('navbar.reportnavbar')
    </div>
    @else
    <x-logout_nav_user />
    @endif

    <div class="container">
        <div class="header">
            <h2>{{ $company_name }} - Day Book</h2>
            <p>As of {{ date('F d, Y') }}</p>
        </div>

        <div class="form-section">
            <form class="formcss" id="daybookForm" action="{{ route('daybookfilter') }}" method="get" onsubmit="return validateDateFilter()">
                <div class="row">
                    <div class="col-sm-10">
                        <h4>SELECT DATES</h4>
                        <div class="row">
                            <div class="col-sm-3">
                                <label for="start_date">From</label>
                                <input type="date" class="form-control" name="start_date" id="start_date" value="{{ request('start_date', now()->toDateString()) }}">
                            </div>
                            <div class="col-sm-3">
                                <label for="end_date">To</label>
                                <input type="date" class="form-control" name="end_date" id="end_date" value="{{ request('end_date', now()->toDateString()) }}">
                            </div>
                            <div class="col-sm-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2" onclick="setAction('filter')">Filter</button>
                                <button type="submit" class="btn btn-primary" onclick="setAction('pdf')">Download PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <h3>Sales</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($sale->created_at)) }}</td>
                        <td>{{ $sale->id }}</td>
                        <td>{{ number_format($sale->total_amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total Sales</th>
                    <th></th>
                    <th>{{ $currency }} {{ number_format($total_sales, 3) }}</th>
                </tr>
            </tfoot>
        </table>

        <h3>Sales Return</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Invoice No</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales_return as $sale_return)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($sale_return->created_at)) }}</td>
                        <td>{{ $sale_return->id }}</td>
                        <td>{{ number_format($sale_return->total_amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total Sales Return</th>
                    <th></th>
                    <th>{{ $currency }} {{ number_format($total_return_sales, 3) }}</th>
                </tr>
            </tfoot>
        </table>

        <h3>Purchases</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchases as $purchase)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($purchase->created_at)) }}</td>
                        <td>{{ $purchase->id }}</td>
                        <td>{{ number_format($purchase->total_amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total Purchases</th>
                    <th></th>
                    <th>{{ $currency }} {{ number_format($total_purchases, 3) }}</th>
                </tr>
            </tfoot>
        </table>

        <h3>Purchases Return</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchases_return as $purchase_return)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($purchase_return->created_at)) }}</td>
                        <td>{{ $purchase_return->id }}</td>
                        <td>{{ number_format($purchase_return->total_amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total Purchases Return</th>
                    <th></th>
                    <th>{{ $currency }} {{ number_format($total_return_purchase, 3) }}</th>
                </tr>
            </tfoot>
        </table>

        <h3>Service</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Service Name</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($service as $services)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($services->created_at)) }}</td>
                        <td>{{ $services->id }}</td>
                        <td>{{ number_format($services->total_amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total Service</th>
                    <th></th>
                    <th>{{ $currency }} {{ number_format($total_service, 3) }}</th>
                </tr>
            </tfoot>
        </table>

        <h3>Receipt</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receiptcustomer as $receiptcustomers)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($receiptcustomers->created_at)) }}</td>
                        <td>{{ $receiptcustomers->id }}</td>
                        <td>{{ number_format($receiptcustomers->amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total Receipt</th>
                    <th></th>
                    <th>{{ $currency }} {{ number_format($total_receiptcustomer, 3) }}</th>
                </tr>
            </tfoot>
        </table>

        <h3>Payment</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paymentcustomer as $paymentcustomers)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($paymentcustomers->created_at)) }}</td>
                        <td>{{ $paymentcustomers->id }}</td>
                        <td>{{ number_format($paymentcustomers->amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total Payment</th>
                    <th></th>
                    <th>{{ $currency }} {{ number_format($total_paymentcustomer, 3) }}</th>
                </tr>
            </tfoot>
        </table>

        <h3>Expenses</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Expense</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $expense)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($expense->date)) }}</td>
                        <td>{{ $expense->expense_name }}</td>
                        <td>{{ number_format($expense->amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total Expenses</th>
                    <th></th>
                    <th>{{ $currency }} {{ number_format($total_expenses, 3) }}</th>
                </tr>
            </tfoot>
        </table>

        <h3>Incomes</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Income</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incomes as $income)
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($income->date)) }}</td>
                        <td>{{ $income->income_name }}</td>
                        <td>{{ number_format($income->amount, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total Incomes</th>
                    <th></th>
                    <th>{{ $currency }} {{ number_format($total_incomes, 3) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
    </div>

    <script>
        function validateDateFilter() {
            let startDate = document.getElementById("start_date").value;
            let endDate = document.getElementById("end_date").value;

            if (startDate && endDate && startDate > endDate) {
                alert("Start date cannot be greater than end date.");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }

        function setAction(actionType) {
            var form = document.getElementById('daybookForm');
            if (actionType === 'filter') {
                form.action = "{{ route('daybookfilter') }}";
            } else if (actionType === 'pdf') {
                form.action = "{{ route('daybook.pdf') }}";
            }
        }
    </script>
</body>
</html>
