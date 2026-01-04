<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">

    <title>Service</title>
    @include('layouts/usersidebar')
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .card {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .card-header {
            background-color: #187f6a;
            color: #fff;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .card-header h2, .card-header h4 {
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .btn {
            padding: 4px 8px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-primary {
            background-color: #187f6a;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #187f6a;
        }

        .btn-success {
            background-color: #28a745;
            color: #fff;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .remove-row {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .remove-row:hover {
            background-color: #c82333;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th, .table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .table th {
            background-color: #187f6a;
            color: #fff;
        }

        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table tr:hover {
            background-color: #f1f1f1;
        }

        .text-muted {
            color: #6c757d;
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
<body>
    <div id="content">
        <div class="container">
            <br><br><br>
            <x-admindetails_user :shopdatas="$shopdatas" />

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            <div class="card mb-4">
                <div class="card-header">
                    <h2>Add Service</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('services.store') }}" method="POST">
                        @csrf
                        <div id="service-rows">
                            <div class="service-row">
                                <div class="mb-3">
                                    <label for="customer_name" class="form-label">Customer Name</label>
                                    <input type="text" name="customer_name" class="form-control" placeholder="Enter customer name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input name="address" class="form-control" placeholder="Enter address" required>
                                </div>
                                <div class="mb-3">
                                    <label for="mobile" class="form-label">Mobile</label>
                                    <input type="text" name="mobile" class="form-control" placeholder="Enter mobile number" required>
                                </div>
                                <div class="mb-3">
                                    <label for="payment" class="form-label">Payment</label>
                                    <select name="payment" class="form-control" required style="height: auto;">
                                        <option value="" selected disabled>Select Payment Mode</option>
                                        <option value="1">Cash</option>
                                        <option value="2">POS Card</option>
                                    </select>
                                </div>


                                <div class="mb-3">
                                    <label for="service_name" class="form-label">Service Name</label>
                                    <input type="text" name="service_name[]" class="form-control" placeholder="Enter service name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" name="quantity[]" class="form-control" placeholder="Enter quantity" min="1" required>
                                </div>
                                <div class="mb-3">
                                    <label for="total_amount" class="form-label">Total Amount</label>
                                    <input type="number" name="total_amount[]" class="form-control" placeholder="Enter total amount" step="0.01" required>
                                </div>
                                <button type="button" class="remove-row">Remove</button>
                            </div>
                        </div>
                        <br>
                        <button type="button" class="btn btn-primary" id="add-service-row">Add Another Service</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
            <br>

            <!-- Service List -->
            <div class="card">
                <div class="card-header">
                    <h4>Service List</h4>
                </div>
                <div class="card-body">
                    @if($services->isEmpty())
                        <p class="text-muted">No services added yet.</p>
                    @else
                        <table class="table table-bordered" id="table">
                            <thead>
                                <tr>
                                    <th>Service ID</th>
                                    <th>Date</th>
                                    <th>Customer Name</th>
                                    <th>Address</th>
                                    <th>Mobile</th>
                                    <th>Total Amount</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $service)
                                <tr>
                                    <td>{{ $service->service_id }}</td>
                                    <td>{{ $service->created_at }}</td>
                                    <td>{{ $service->customer }}</td>
                                    <td>{{ $service->address }}</td>
                                    <td>{{ $service->phone }}</td>
                                    <td>{{ number_format($service->total_amount, 3) }}</td>
                                    <td>
                                        @if($service->payment_mode == 1 || $service->payment_mode === null)
                                            Cash
                                        @elseif($service->payment_mode == 2)
                                            POS
                                        @endif
                                    </td>

                                    <td>
                                        @if($service->service_id)
                                            <a href="{{ route('services.downloadRow', $service->service_id) }}" class="btn btn-success btn-sm">Download</a>
                                        @else
                                            <span>Service ID is missing</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                    @endif
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#table').DataTable({
            order: [
                [0, 'asc']
            ]
        });
    });
</script>

    <script>
        document.getElementById('add-service-row').addEventListener('click', function () {
            const serviceRowHTML = `
                <div class="service-row">

                    <div class="mb-3">
                        <label for="service_name" class="form-label">Service Name</label>
                        <input type="text" name="service_name[]" class="form-control" placeholder="Enter service name" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" name="quantity[]" class="form-control" placeholder="Enter quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="total_amount" class="form-label">Total Amount</label>
                        <input type="number" name="total_amount[]" class="form-control" placeholder="Enter total amount" step="0.01" required>
                    </div>
                    <button type="button" class="remove-row">Remove</button>
                </div>
            `;
            document.getElementById('service-rows').insertAdjacentHTML('beforeend', serviceRowHTML);
        });

        document.body.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-row')) {
                e.target.closest('.service-row').remove();
            }
        });
    </script>
</body>
</html>
