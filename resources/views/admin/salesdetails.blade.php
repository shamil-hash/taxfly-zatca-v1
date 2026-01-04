<!DOCTYPE html>
<html>

<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js">
  </script>
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
      DEMO VERSION
      <br>
      Phone No:{{$shopdata['phone']}} &nbsp&nbspEmail:{{$shopdata['email']}}
      <br>
      <br>
      @endforeach
    </div>
    <h2>List Credit Sales</h2>
    <div class="row">
      <div class="col-md-12">
        <table class="table">
          <thead>
            <tr>
              <th>Product Name</th>
              <th>DATE</th>
              <th>AMOUNT</th>
            </tr>
          </thead>
          <tbody>
            @foreach($salesdatas as $salesdata)
            <tr>
              <td>
                {{$salesdata->product_name}}
              </td>
              <td>
                                    {{ date('d M Y | h:i:s A', strtotime($salesdata->created_at)) }}

                                </td>
                                <td>
                                    <b>{{ $currency }}</b> {{ $salesdata->price }}
                                </td>
              @endforeach
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>

</body>

</html>
