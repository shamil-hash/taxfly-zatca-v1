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
      Phone No:{{$shopdata['phone']}}
      <br>
      Email:{{$shopdata['email']}}
      <br>
      <br>
      @endforeach
    </div>
    <h2>List Credit Sales</h2>


    <div align="right">
      <select class="form-control" style="width:25%" onclick="doDisplay(this.value)">
        @foreach($locations as $location)
        <option value="{{$location->id}}">{{$location->location}}</option>
        @endforeach
      </select>
      <br>
    </div>
    <table class="table">
      <thead>
        <tr>
          <th>Username</th>
          <th>Branch</th>
          <th>VIEW</th>
        </tr>
      </thead>
      <tbody id="listcredit">
        @foreach($creditdatas as $creditdata)
        <tr>
          <td>
            {{ $creditdata->username }}

          </td>
          <td>
            {{$creditdata->location}}
          </td>
          <td>
            <a href="customersalesdata/{{$creditdata->id}}" class="btn btn-primary" title="View Credit Sales Details">view</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</body>

</html>
<script>
    function doDisplay(x) {
        var location_id = x;
        console.log(location_id)
        $.ajax({
            type: "GET",
            url: "/locationcredit/" + location_id,
            dataType: "json",
            success: function(response) {
                console.log(response.categories);
                $part = "";
                $.each(response.categories, function(key, item) {
                    $part = $part + '<tr><td>' + item.username + '</td>\
                                    <td>\
                                    ' + item.location + '</td><td>\
                                    <a href="customersalesdata/' + item.id + '"class="btn btn-primary">view</a>\ \
                                    </td></tr>'
                });
                $("#listcredit").html($part);
            }
        });
    }
</script>
