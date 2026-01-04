<!DOCTYPE html>
<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin</title>
    @include('layouts/adminsidebar')

    <style>
        .gdot {
            height: 15px;
            width: 15px;
            background-color: #0adc0a;
            border-radius: 50%;
            display: inline-block;
            vertical-align: bottom;
        }
        .btn-primary{
            background-color: #187f6a;
            color: white;
        }
    </style>
    <style>
        .cdot {
            height: 15px;
            width: 15px;
            background-color: #cccccc;
            border-radius: 50%;
            display: inline-block;
            vertical-align: bottom;
        }
    </style>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
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
    </style>

</head>

<body>
    <!-- Page Content Holder -->
    <div id="content">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" id="sidebarCollapse" class="btn navbar-btn">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                        <span></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="/adminlogout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <!--<div align="center">-->
        <!--    @foreach ($shopdatas as $shopdata)-->
        <!--        {{ $shopdata['name'] }}-->
        <!--        <br>-->
        <!--        Phone No:{{ $shopdata['phone'] }}-->
        <!--        <br>-->
        <!--        Email:{{ $shopdata['email'] }}-->
        <!--        <br>-->
        <!--        <br>-->
        <!--    @endforeach-->
        <!--</div>-->
        <h2>Customer Summary</h2>
        <div class="row">
            <div class="col-sm-4">
            </div>
            <div class="col-sm-4">
            </div>
            <div class="col-sm-4" align="right">
                <div class="filter">
                </div>
            </div>
        </div>

        <table class="table" id="example">
            <thead>
                <tr>
                    <th width="20%">Name</th>
                    <!--<th width="15%">Username</th>-->
                    <th width="15%">Date</th>
                    <th width="10%">Gross Sale</th>
                    <th width="10%">Amount Received</th>
                    <th width="10%">Sales Returns</th>
                    <th width="10%">Due</th>
                    <th width="5%">VIEW</th>
                    <th width="8%">Status</th>
                    <th width="10%">Branch</th>
                </tr>
            </thead>
            <tbody id="listcredit">
                @foreach ($softusers as $credit)
                    <tr>
                        <td>
                            {{ $credit->name }}
                        </td>
                      
                        <td>
                            {{ date('d M Y | h:i:s A', strtotime($credit->created_at)) }}

                        </td>
                        <td>
                           <b>{{ $currency }}</b> {{ number_format($credit->total_invoiced_amount + $credit->credit_trans_invoice_amount, 3) }}
                        </td>
                        <td>
                           <b>{{ $currency }}</b> {{ number_format($credit->credit_trans_collected + $credit->total_invoiced_amount, 3) }}
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ number_format($credit->credit_trans_returned + $credit->total_product_returned, 3) }}
                        </td>
                        <td>

                        <b>{{ $currency }}</b> {{ number_format($credit->due_amount - $credit->collected_amount, 3) }}
                    </td>
                        <td>
                            <a>
                                {{-- <a href="customercreditdata/{{ $credit->id }}" class="btn btn-primary"
                                    title="View Credit User Summary">view</a> --}}

                                <a href="credittransactionshistory/{{ $credit->id }}" class="btn btn-primary"
                                    title="View Credit User Summary">view</a>
                            </a>
                        </td>
                        <td>
                            @if ($credit->status == 1)
                                <a href="disablecredituser/{{ $credit->id }}" class="btn btn-danger"
                                    title="Disable Credit User">disable</a>
                            @else
                                <a href="enablecredituser/{{ $credit->id }}" class="btn btn-success"
                                    title="Enable Credit User">Enable</a>
                            @endif
                        </td>
                        <td>
                            {{ $credit->branchname }}
                        </td>
                @endforeach
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            sDom: 'lrtip',
            initComplete: function() {
                this.api().columns(8).every(function() {
                    var column = this;
                    var select = $(
                            '<select class="form-control form-control-md selectalign"><option value="">Branch</option></select>'
                        )
                        .appendTo(".filter")
                        .on('change', function() {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                        });
                    column.data().unique().sort().each(function(d, j) {
                        select.append('<option value="' + d + '">' + d +
                            '</option>')
                    });
                });
            },
            "order": [
                [1, "asc"]
            ]
        });
    });
</script>
