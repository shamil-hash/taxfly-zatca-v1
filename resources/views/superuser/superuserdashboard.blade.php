<!DOCTYPE html>
<html>

<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js" integrity="sha512-Wt1bJGtlnMtGP0dqNFH1xlkLBNpEodaiQ8ZN5JLA5wpc1sUlk/O5uuOMNgvzddzkpvZ9GLyYNa8w2s7rqiTk5Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Super User</title>
    <style>
        #content {
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
            width: 100%;
        }
    </style>
    @include('layouts/superusersidebar')
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
                        <li><a href="/superuserlogout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Super User</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </nav>
        <h1><b>NETPLEX</b></h1>
        <div style="width:75%;margin-right:5%;margin-left:8%">
            <canvas id="barChart"></canvas>
        </div>
    </div>
</body>

</html>
<script>
    var year = <?php echo $year; ?>;
    var user = <?php echo $user; ?>;
    var returned = <?php echo $returned; ?>;
    var barChartData = {
        labels: year,
        datasets: [{
                label: 'Products sold',
                backgroundColor: "#563d7c",
                data: user
            },
            {
                label: 'Products returned',
                backgroundColor: "red",
                data: returned
            }
        ]
    };
    window.onload = function() {
        var ctx = document.getElementById("barChart").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            //         options: {
            //     scales: {
            //         x: {
            //             stacked: true
            //         },
            //         y: {
            //             stacked: true
            //         }
            //     }
            // }
        });
    };
</script>
