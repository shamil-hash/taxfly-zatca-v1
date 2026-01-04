<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Final Report</title>
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
        <li class="breadcrumb-item"><a href="#">Accounts</a></li>
        <li class="breadcrumb-item active" aria-current="page">Final Report</li>
      </ol>
    </nav>
    @if(Session::has('success'))
    <div class="alert alert-success">
      {{Session::get('success')}}
    </div>
    @endif
    <form action="accountantfinalreport" method="POST" enctype="multipart/form-data" id="finalreportform">
      @csrf
      <div align="right">
        <a href="/finalreporthistory" class="btn btn-primary">History</a>
      </div>
      <h2 ALIGN="CENTER">Final Report </h2>
      <p align="center" style="font-size:14px;">Upload Final accounts report</p>
      <div>
        <div align="center">
          <div class="form-group">
            <br>
            <div class="container-fluid">
              <div class="row">
                <div class=col-md-6>
                  <input type="text" class="form-control" name="date" placeholder="Date" value="{{$date}}" readonly>
                  <br>
                </div>
                <div class=col-md-6>
                  <input type="file" name="finalreportfile" multiple class="form-control">
                  <br>
                </div>
              </div>
            </div>
            <br>
            <button type="submit" class="btn btn-primary" id="submitBtn">SUBMIT</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</body>

</html>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("finalreportform");
        const submitBtn = document.getElementById("submitBtn");

        form.addEventListener("submit", function(e) {
            // Prevent the form from submitting multiple times
            submitBtn.disabled = true;
            submitBtn.innerText = "Submitting...";

            // Allow the form to submit normally
            return true;
        });
    });
</script>
