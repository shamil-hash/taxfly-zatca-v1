<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Sales Report</title>
  @include('layouts/usersidebar')
  <style>
    .dot {
      height: 15px;
      width: 15px;
      background-color: #bbb;
      border-radius: 50%;
      display: inline-block;
    }
     .btn-primary{
            background-color: #187f6a;
            color: white;
        }
  </style>
  <style>
    .gdot {
      height: 15px;
      width: 15px;
      background-color: green;
      border-radius: 50%;
      display: inline-block;
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
    <div style="margin-top: 19px;margin-left:15px;">

        @include('navbar.expnavbar')
    </div>
    @else
    <x-logout_nav_user />
    @endif
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Accountant</a></li>
        <li class="breadcrumb-item active" aria-current="page">Accounts Report</li>
        <li class="breadcrumb-item active" aria-current="page">Sales Report</li>
      </ol>
    </nav>

        <div style="display: flex; width: 100%; background-color: #f8f9fa; padding: 5px; border-radius: 6px;" role="group" aria-label="Report navigation">
  <div style="flex: 1; margin: 0 3px;">
    <a href="/accountreport" style="display: block; padding: 10px 5px; background-color: #187f6a; color: white; text-align: center; text-decoration: none; border: 1px solid #146354; border-radius: 4px; transition: all 0.3s ease;">Purchase Report</a>
  </div>
  <div style="flex: 1; margin: 0 3px;">
    <a href="/accountpurchasereturn" style="display: block; padding: 10px 5px; background-color: #187f6a; color: white; text-align: center; text-decoration: none; border: 1px solid #146354; border-radius: 4px; transition: all 0.3s ease;" >Purchase Return Report</a>
  </div>
  <div style="flex: 1; margin: 0 3px;">
    <a href="/accountreturnreport" style="display: block; padding: 10px 5px; background-color: #187f6a; color: white; text-align: center; text-decoration: none; border: 1px solid #146354; border-radius: 4px; transition: all 0.3s ease;">Sales Return Report</a>
  </div>
  <div style="flex: 1; margin: 0 3px;">
    <a href=""  style="display: block; padding: 10px 5px; background-color: #115c4c; color: white; text-align: center; text-decoration: none; border: 1px solid #0d4539; border-radius: 4px; font-weight: bold; box-shadow: inset 0 2px 4px rgba(0,0,0,0.15);" >Sales Report</a>
  </div>
</div>
    <h2 ALIGN="CENTER">Sales Report </h2>
    <h1>
      TOTAL SALES:{{$count}}
    </h1>
    <form action="accountantsalesview" method="POST" id="myform" onsubmit="return doSomething2()">
      @csrf
      <div class="row">
        <div class="col-sm-6">
          <h4>SELECT DATES</h4>
          <div class="row">
            <div class="col-sm-6">
              From
              <input type="date" class="form-control" name="start_date">
            </div>
            <div class="col-sm-6">
              To
              <input type="date" class="form-control" id="datepicker" name="end_date">
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div align="right">
            <label for="view_type">Choose View Type</label>
            <select class="form-control" id="view_type" name="view_type" style="width:25%">
              <option value="">-select-</option>
              <option value="1">Day</option>
              <option value="2">Month</option>
              <option value="3">Year</option>
            </select>
            <br>
            <label for="location_id">Choose Location</label>
            <select class="form-control" id="location_id" name="location_id" style="width:25%">
              <option value="">-select-</option>
              @foreach($locations as $location)
              <option value="{{$location->id}}">{{$location->location}}</option>
              @endforeach
            </select>
            <br>
            <button type="submit" class="btn btn-primary">VIEW</button>
          </div>
        </div>
      </div>
    </form>
  </div>

</body>

</html>
<script>
  function doSomething2() {
    var view_type = document.getElementById("view_type").value;
    var location_id = document.getElementById("location_id").value;
    if (view_type === "" || view_type === null) {
      alert("Choose View Type");
      return false;
    } else if (location_id === "" || location_id === null) {
      alert("Choose Location");
      return false;
    } else {
      return true;
    }
  }
</script>
