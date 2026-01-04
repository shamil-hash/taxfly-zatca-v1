<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="Plexpay Recharge">
        <title>Plexpay Reports</title>
        @include('layouts/usersidebar')
<style>
table {
  border-collapse: collapse;
  width: 100%;
}
th, td {
  border: 1px solid black;
  text-align: left;
  padding: 8px;
}
tr:nth-child(even){background-color: #f2f2f2}
th {
  background-color: #20639B;
  color: white;
}
.table>thead>tr>th {
    vertical-align: bottom;
    border-bottom: 1px solid #010101;
}
div.nav-dat {
  font-size: 20px;
  color: #20639B;
}
</style>
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
                                <li><a href="/userlogout">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
                <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="">Plexpay</a></li>
    <li class="breadcrumb-item active" aria-current="page">Reports</li>
  </ol>
</nav>

<div class="nav-dat" align="right">
Balance {{$wallet_amount}} &nbsp; Due {{$due_amount}}
</div>
<br>



<ul class="nav nav-pills">
<li role="presentation"><a href="/plexpaybalance">Balance</a></li>
<li role="presentation"><a href="/plexpayrecharge">Recharge</a></li>
<li role="presentation" class="active"><a href="/plexpayreports">Reports</a></li>
</ul>
<div class="dropdown" align="right">
  <button class="btn btn-default dropdown-toggle" style="padding:10px 15px;background-color:#20639B;color:white;" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    Profile
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
    <li><a href="/plexpayfunds">Funds</a></li>
    <li><a href="/plexpaytransactions">Transactions</a></li>
    <li><a href="/plexpaycollection">Collection</a></li>
    <li><a href="/plexpayduesummary">Due Summary</a></li>
    <li><a href="/plexpaysummary">Profit Summary</a></li>
    <li><a href="/plexpayregister">Plexpay Register</a></li>
    <li><a href="/plexpaypasswordchange">Change Password</a></li>
  </ul>
</div>
<br>
<h2>Reports</h2>
<br>
<form action="/plexpayreportssearch" method="get">
        <div class="row">
                        <div class="col-sm-6">
                            <h4>SELECT DATES  
                            </h4>
                                <div class="row">
                                        <div class="col-sm-5">
                                            From
                                                <input type="date" value="{{$start_date}}" class="form-control" value="" name="start_date">
                                        </div>     
                                        <div class="col-sm-5">
                                            To
                                                <input type="date" value="{{$end_date}}" class="form-control" value="" name="end_date">
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
<table>
<thead>
<tr>
    <th>Reciept No</th>
    <th>Point Name</th>
    <th>Duration</th>
    <th>Balance</th>
</tr>
</thead>
<tbody>
<tr>
  <td>{{$point_name}}</td>
  <td>{{$point_name}}</td>
  <td>{{$Duration}}</td>
  <td>{{$Balance}}</td>
</tr>
</tbody>
</table>
<br>
<table id="example" class="table table-striped table-bordered" style="width:100%">
<thead>
<tr>
    <th>Service</th>
    <th>RechargedTime</th>
    <th>Amount</th>
    <th>Commission</th>
    <th>Date</th>
</tr>
</thead>
<tbody>
@foreach($reports as $report)
<tr>
  <td>{{$report['Service']}}</td>
  <td>{{$report['RechargedTime']}}</td>
  <td>{{$report['Amount']}}</td>
  <td>{{$report['Commission']}}</td>
  <td>{{$report['Date']}}</td>
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
    $('#example').DataTable({
        order: [[ 0, 'asc' ]]
    } 
    );
} );
  </script>