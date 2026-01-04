<!DOCTYPE html>
<html>

<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js" integrity="sha512-Wt1bJGtlnMtGP0dqNFH1xlkLBNpEodaiQ8ZN5JLA5wpc1sUlk/O5uuOMNgvzddzkpvZ9GLyYNa8w2s7rqiTk5Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Due Summary</title>
    <style>
        #content {
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
            width: 100%;
        }
    </style>

    <head> <!-- Bootstrap CSS CDN -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- Our Custom CSS -->
        <link rel="stylesheet" href="/css/bootstrap.css">
    </head>
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
    <!-- Bootstrap Js CDN -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>
    <style>
        #content {
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
            width: 100%;
        }
    </style>
    <style>
        body {
            background: #ffffff;
        }
    </style>

</head>

<body>
    <div class="wrapper">
        <!-- Sidebar Holder -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <a href="/creditdashboard">
                    <h3>Credit User</h3>
                    <strong>CU</strong>
                </a>
            </div>
            <ul class="list-unstyled components">
                <span>
                </span>
                <li>
                    <a href="/creditduesummary"> <i class="glyphicon glyphicon-th-list"></i>Credit Duesummary</a>
                </li>
                <li>
                    <a href="/credittransactions"> <i class="glyphicon glyphicon-tags"></i>Credit Transactions</a>
                </li>
                <li>
                    <a href="/creditchangepassword"> <i class="glyphicon glyphicon-cog"></i>Change Password</a>
                </li>
                <li>
                    <a href="/creditlogout"> <i class="glyphicon glyphicon-log-out"></i>Logout</a>
                </li>
            </ul>
        </nav>


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
                </div>
            </nav>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Credit User</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
            <h1><b>Due Summary</b></h1>
            <form action="creditsummarydate" method="GET">
                <div class="row">
                    <div class="col-sm-6">
                        <h4>SELECT DATES</h4>
                        <div class="row">
                            <div class="col-sm-6">
                                From
                                <input type="date" class="form-control" name="start_date" value="{{$start_date}}">
                            </div>
                            <div class="col-sm-6">
                                To
                                <input type="date" class="form-control" id="datepicker" name="end_date" value="{{$end_date}}">
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <button type="submit" class="btn btn-primary">search</button>
            </form>
            <br>
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">Due Summary</div>
                <!-- Table -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Date</th>
                            <th>Transaction Type</th>
                            <th>Details</th>
                            <th>Due</th>
                            <th>Invoice</th>
                            <th>Payments</th>
                            <th>Credit Note</th>
                            <th>Balance <br /> (Closing Amount)</th>
                            <th>Desk User ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        <?php $collectiontotal = 0; ?>
                        @foreach ($salesdata as $salesdat)
                            <tr>
                                <td>{{ $number }}</td>
                                <td>{{ date('d M Y | h:i:s A', strtotime($salesdat->created_at)) }}</td>
                                <td>{{ $salesdat->comment }}</td>
                                <td>
                            @if ($salesdat->comment == 'Credit Note')
                                Credit Note
                                {{ $currency }} {{ $salesdat->credit_note }} <br /> for payment of
                                {{ $salesdat->transaction_id }}
                            @elseif ($salesdat->comment == 'Payment Received')
                                @if ($salesdat->transaction_id != '')
                                    {{ $currency }} {{ $salesdat->collected_amount }} <br /> for payment of
                                    {{ $salesdat->transaction_id }}
                                @else
                                    {{ $currency }} {{ $salesdat->collected_amount }} paid.
                                @endif
                            @elseif ($salesdat->comment == 'Payment & Credit Note')
                                @if ($salesdat->transaction_id != '')
                                    {{ $currency }} {{ $salesdat->collected_amount }} <br /> for payment of
                                    {{ $salesdat->transaction_id }} <br /> <br />

                                    Credit Note {{ $currency }} {{ $salesdat->credit_note }} <br /> for payment of
                                    {{ $salesdat->transaction_id }}
                                @else
                                    {{ $currency }} {{ $salesdat->collected_amount }} paid. <br /> <br />
                                    {{ $currency }} {{ $salesdat->credit_note }} added.
                                @endif
                            @elseif ($salesdat->comment == 'Invoice')
                                @if ($salesdat->transaction_id != '')
                                    {{ $currency }} {{ $salesdat->Invoice_due }} <br /> due on transaction
                                    {{ $salesdat->transaction_id }} <br /> <br />
                                @else
                                    {{ $currency }} {{ $salesdat->Invoice_due }} due. <br /> <br />
                                @endif
                            @endif
                                </td>
                                <td><b>{{ $currency }}</b> {{ $salesdat->due }}</td>
                                <td>
                                    @if ($salesdat->Invoice_due != 0)
                                        <b>{{ $currency }}</b> {{ $salesdat->Invoice_due }}
                                    @endif
                                </td>
                                <td>
                                    @if ($salesdat->collected_amount != null || $salesdat->collected_amount != 0)
                                        <b>{{ $currency }}</b> {{ $salesdat->collected_amount }}
                                    @endif
                                </td>
                                <td>
                                    @if ($salesdat->credit_note != null || $salesdat->credit_note != 0)
                                        <b>{{ $currency }}</b> {{ $salesdat->credit_note }}
                                    @endif

                                </td>

                                <td><b>{{ $currency }}</b> {{ $salesdat->updated_balance }}</td>

                                <td>UID{{ $salesdat->user_id }}</td>
                            </tr>
                            <?php $number++; ?>
                            <?php $collectiontotal = $collectiontotal + $salesdat->collected_amount; ?>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>