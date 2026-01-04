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

    .list-group-item.success,
    .list-group-item.success:focus,
    .list-group-item.success:hover {
      z-index: 2;
      color: #fff;
      background-color: #28a745;
      border-color: #28a745;
    }

    .list-group-item.success .list-group-item-text,
    .list-group-item.success:focus .list-group-item-text,
    .list-group-item.success:hover .list-group-item-text {
      color: #c7ddef;
    }

    .list-group-item.success .list-group-item-heading,
    .list-group-item.success .list-group-item-heading>.small,
    .list-group-item.success .list-group-item-heading>small,
    .list-group-item.success:focus .list-group-item-heading,
    .list-group-item.success:focus .list-group-item-heading>.small,
    .list-group-item.success:focus .list-group-item-heading>small,
    .list-group-item.success:hover .list-group-item-heading,
    .list-group-item.success:hover .list-group-item-heading>.small,
    .list-group-item.success:hover .list-group-item-heading>small {
      color: inherit;
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
    <div align="center">
      @foreach($shopdatas as $shopdata)
      {{$shopdata['name']}}
      <br>
      Phone No:{{$shopdata['phone']}}
      <br>
      Email:{{$shopdata['email']}}
      <br>
      <br>
      @endforeach
    </div>
    <ul class="nav nav-tabs">
      <li role="presentation" class="active"><a href="#">User Creation Requests <span class="badge">{{$count}}</span></a></li>
      <li role="presentation"><a href="#">User Create</a></li>
    </ul>
    <br>
    <div class="list-group">
      @foreach($userrequests as $userrequest)
      <?php if ($userrequest->status == '0') { ?>
        <a href="/hrusercreation/{{$userrequest->id}}" class="list-group-item active">
          <h4 class="list-group-item-heading">{{$userrequest->name}}</h4>
          <p class="list-group-item-text">{{$userrequest->joining_date}}</p>
          <p class="list-group-item-text">{{$userrequest->username}}</p>
          <p class="list-group-item-text">{{$userrequest->location}}</p>
          <p class="list-group-item-text">{{$userrequest->hr}}</p>
        </a>
      <?php } ?>
      <?php if ($userrequest->status == '1') { ?>
        <a href="/hrusercreation/{{$userrequest->id}}" class="list-group-item">
          <h4 class="list-group-item-heading">{{$userrequest->name}}</h4>
          <p class="list-group-item-text">{{$userrequest->joining_date}}</p>
          <p class="list-group-item-text">{{$userrequest->username}}</p>
          <p class="list-group-item-text">{{$userrequest->location}}</p>
          <p class="list-group-item-text">{{$userrequest->hr}}</p>
        </a>
      <?php } ?>
      <?php if ($userrequest->status == '2') { ?>
        <a href="#" class="list-group-item success">
          <h4 class="list-group-item-heading">{{$userrequest->name}}</h4>
          <p class="list-group-item-text">{{$userrequest->joining_date}}</p>
          <p class="list-group-item-text">{{$userrequest->username}}</p>
          <p class="list-group-item-text">{{$userrequest->location}}</p>
          <p class="list-group-item-text">{{$userrequest->hr}}</p>
        </a>
      <?php } ?>
      @endforeach
    </div>
    <span>
      {{$userrequests->links()}}
    </span>
  </div>

</body>

</html>
