<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">

    <title>List Stock</title>
    @include('layouts/usersidebar')
    <style>
        #content {
            padding: 30px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }
  .btn-primary {
            background-color: #187f6a;
            color: #fff;
        }
        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2
        }

        th {
            background-color: #187f6a;
            color: white;
        }

        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 1px solid #010101;
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
        .low-stock {
    background-color: #ffcccc !important; /* Light red background */
    font-weight: bold;
}

.btn-warning {
    background-color: #ffc107;
    color: #000;
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
    <!-- Page Content Holder -->
    <div id="content">
        @if ($adminroles->contains('module_id', '30'))
        <div style="margin-left:-15px;margin-top:-18px;">

            @include('navbar.invnavbar')
        </div>
                @else
        <x-logout_nav_user />
        @endif
        <x-admindetails_user :shopdatas="$shopdatas" />


<br><br>
 <div class="form-group row" style="margin-bottom: 15px;">
            <div class="col-md-12 text-right">
                 <button id="lowStockBtn" class="btn btn-warning">
                    <i class="fa fa-exclamation-triangle"></i> Show Low Stock ( < 5)
                </button>
                 <button id="showAllBtn" class="btn btn-primary" style="display:none;">
                    <i class="fa fa-list"></i> Show All
                </button>
                <a href="{{ route('exportstocklist') }}" class="btn btn-primary">
                    <i class="fa fa-file-excel-o"></i> Export to Excel
                </a>
                <a href="{{ route('printstocklist') }}" class="btn btn-primary" target="_blank">
                    <i class="fa fa-print"></i> Print
                </a>
            </div>
        </div>
        <div class="form group row">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Total Stock</th>
                        <th>Remaining Stock</th>
                    </tr>
                </thead>
                <tbody id="liststocks">
                    @foreach($products as $product)
                    <tr>
                        <td>{{$product->product_name}}</td>
                        <td>{{$product->stock}}</td>
                        <td>{{ number_format($product->remaining_stock,3)}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#example').DataTable({
            order: [[0, 'asc']],
            "columnDefs": [
                {
                    "targets": 2, // Remaining Stock column (0-based index)
                    "render": function(data, type, row) {
                        if (type === 'sort' || type === 'filter') {
                            // Return raw number for sorting/filtering
                            return parseFloat(data.replace(/,/g, ''));
                        }
                        return data; // Return formatted display value
                    }
                }
            ]
        });

        // Highlight low stock items function
        function highlightLowStock() {
            $('#example tbody tr').removeClass('low-stock');

            table.rows().every(function() {
                var row = this.node();
                var stockData = this.data();
                var stockText = stockData[2]; // Get the displayed text
                var stockValue = parseFloat(stockText.replace(/,/g, '')); // Convert to number

                if (stockValue < 5) {
                    $(row).addClass('low-stock');
                }
            });
        }

        // Low stock button click handler
        $('#lowStockBtn').click(function() {
            // Custom filtering for low stock
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var stockText = data[2]; // Remaining Stock column
                    var stockValue = parseFloat(stockText.replace(/,/g, ''));
                    return stockValue < 5;
                }
            );

            table.draw();
            highlightLowStock();
            $(this).hide();
            $('#showAllBtn').show();

            // Remove the custom filter after applying
            $.fn.dataTable.ext.search.pop();
        });

        // Show all button click handler
        $('#showAllBtn').click(function() {
            table.search('').columns().search('').draw();
            highlightLowStock();
            $(this).hide();
            $('#lowStockBtn').show();
        });

        // Highlight low stock items on initial load
        highlightLowStock();
    });
</script>
