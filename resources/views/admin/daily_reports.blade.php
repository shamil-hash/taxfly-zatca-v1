<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin</title>
    @include('layouts/adminsidebar')

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            text-align: center;
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

        tr.footer_table>td {
            text-align: right;
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

        <div class="btn-group btn-group-justified" role="group" aria-label="...">
            <div class="btn-group" role="group">
                <a href="/userreport/{{ $uid }}" class="btn btn-primary">Sales</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchase/{{ $uid }}" class="btn btn-primary">Purchase</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/userpurchasereturn/{{ $uid }}" class="btn btn-primary">Purchase Return</a>
            </div>
            <!--<div class="btn-group" role="group">-->
            <!--    <a href="/userstock/{{ $uid }}" class="btn btn-primary">Stocks</a>-->
            <!--</div>-->
            <div class="btn-group" role="group">
                <a href="/userreturn/{{ $uid }}" class="btn btn-primary">Return</a>
            </div>
            <div class="btn-group" role="group">
                <a href="/daily_report/{{ $uid }}" class="btn btn-primary active">Daily Report</a>
            </div>
        </div>
        <br>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Reports</a></li>
                <li class="breadcrumb-item active" aria-current="page">Daily Report</li>
            </ol>
        </nav>
        <h2>Daily Report</h2>
        <form action="/filter_daily_report/{{ $uid }}" method="get">
            <div class="row">
                <div class="col-sm-6">
                    <h4>SELECT DATES </h4>
                    <div class="row">
                        <div class="col-sm-5">
                            From
                            <input type="date" class="form-control" value="{{ $start_date }}" name="start_date">
                        </div>
                        <div class="col-sm-5">
                            To
                            <input type="date" class="form-control" value="{{ $end_date }}" name="end_date">
                        </div>
                        <div class="col-sm-2">
                            <br>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <br>
        <!-- content -->

        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Date and Time</th>
                    <th>User Report ID</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($daily_datas as $daily_data)
                    <tr>
                        <td>
                            {{ date('d M Y | h:i:s A', strtotime($daily_data->created_at)) }}

                        </td>
                        <td>
                            {{ $daily_data->trans_id }}
                        </td>
                        <td>
                            <a href="/admin_daily_report_print/{{ $uid }}/{{ $daily_data->trans_id }}"
                                class="btn btn-primary">VIEW</a>
                        </td>
                @endforeach

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
            order: []
        });
    });
</script>
