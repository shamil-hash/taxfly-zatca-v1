<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Expenses History</title>
  @include('layouts/usersidebar')
  <style>
    .dot {
      height: 15px;
      width: 15px;
      background-color: #bbb;
      border-radius: 50%;
      display: inline-block;
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

        @include('navbar.employeenav')
    </div>
    @else
<x-logout_nav_user />
@endif

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Employee</a></li>
                <li class="breadcrumb-item active" aria-current="page">Salary</li>
      </ol>
    </nav>

    <h2 ALIGN="CENTER">Employee Salary </h2>

    <table class="table">
      <thead>
        <tr>
          <th width="20%">Name</th>
          <th width="20%">Joining Date</th>
          <th width="20%">View</th>
        </tr>
      </thead>
      <tbody>
        @foreach($userdatas as $userdata)
        <tr>
          <td>
            {{$userdata->first_name}}
          </td>
          <td>
            {{$userdata->date}}
          </td>
          <td>
            <a href="/employeesalarydat/{{$userdata->id}}" class="btn btn-primary">VIEW</a>
          </td>
          @endforeach
        </tr>
      </tbody>
    </table>
  </div>
</body>

</html>
