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
  <style>
    input[type=text] {
      border-radius: 5px;
      width: 30%;
    }

    input[type=password] {
      border-radius: 5px;
      width: 30%;
    }

    select {
      border-radius: 5px;
      width: 30%;
    }

    .form-control {
      display: block;
      width: 30%;
      height: 34px;
    }
  </style>
  <!-- navbar -->
  <style>
    .navbar {
      padding: 15px 10px;
      border: none;
      border-radius: 0;
      margin-bottom: 40px;
      box-shadow: 1px 1px 3px rgb(0 0 0 / 10%);
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
    <!--  @foreach($shopdatas as $shopdata)-->
    <!--  {{$shopdata['name']}}-->
    <!--  <br>-->
    <!--  Phone No:{{$shopdata['phone']}}-->
    <!--  <br>-->
    <!--  Email:{{$shopdata['email']}}-->
    <!--  <br>-->
    <!--  <br>-->
    <!--  @endforeach-->
    <!--</div>-->
    <h2>Final Reports</h2>

    <table>
      <tr>
        <th>User</th>
        <th>Date</th>
        <th>Created_at</th>
        <th>Download</th>
      </tr>
      @foreach($reports as $report)
      <tr>
        <td>{{$report->name}}</td>
        <td>{{$report->date}}</td>
        <td>{{$report->created_at}}</td>
        <td><b><a href="{{url('/downloadreport',$report->file)}}">Download</a></b></td>
      </tr>
      @endforeach
    </table>
  </div>
</body>

</html>
