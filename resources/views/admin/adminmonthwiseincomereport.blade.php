<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin Month-wise Income Report</title>
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

    .total {
      font-weight: bold;
      background-color: white;
      font-size: large;
    }

    h2 {
      margin-bottom: 2rem;
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
            <li><a href="/userlogout">Logout</a></li>
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

    <div class="btn-group btn-group-justified" role="group" aria-label="...">
      <div class="btn-group" role="group">
        <a href="/expensereport" class="btn btn-primary">Month-wise Expense Report</a>
      </div>
      <div class="btn-group" role="group">
        <a href="" class="btn btn-primary active">Month-wise Income Report</a>
      </div>
    </div>
    <h2>Month-wise Expence Report</h2>
    <form action="/adminmonthwiseincomereportdate" method="get" id="myexpense" onsubmit="return doSomething2()">
      <div class="row">
        <div class="col-sm-6">
          <h4>SELECT MONTHS
          </h4>
          <div class="row">
            <div class="col-sm-5">
              <input type="month" class="form-control" value="{{$start_date}}" name="start_date">
            </div>
            <div class="col-sm-5">
              <select name="branch" id="branch" class="form-control">
                <option value="">SELECT BRANCH</option>
                @foreach($branch as $branch)
                <option value="{{$branch->id}}">{{$branch->branchname}}</option>
                @endforeach
              </select>
            </div>

            <div class="col-sm-2">
              <button type="submit" class="btn btn-primary">Filter</button>
            </div>
          </div>
        </div>
      </div>
    </form>
    <br>
    <!-- content -->

    <table id="example" class="table table-striped table-bordered" style="width:100%">
      <thead>
        <tr>
            <th>Branch</th>
            <th>Income</th>
            <th>Income Type</th>
            <th>Details</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Created At</th>
            <th>Download</th>
        </tr>
      </thead>
      <tbody>
        @php
        $totamount=0;
        @endphp
       @foreach($income as $income)
       <tr>
           <td>{{$income->branchname}}</td>

         <td>
           @if($income->income_type == 1)
           {{ $income->direct_income }}
           @else
           {{ $income->indirect_income }}
           @endif
         </td>
         <td>{{ $income->income_type == 1 ? 'Direct' : 'Indirect' }}</td>
         <td>{{ $income->details }}</td>
         <td><b>{{ $currency }}</b> {{ number_format($income->amount, 2) }}</td>
         <td>{{ date('d M Y', strtotime($income->date)) }}</td>
         <td>{{ date('d M Y | h:i:s A', strtotime($income->created_at)) }}</td>
         <td>
           @if($income->file)
           <span style='background:lightgrey;'>
           <a href="{{ url('/expensedownload', $income->file) }}">{{ $income->file }}</a>
           </span>
           @else
           <b>No File</b>
           @endif
         </td>
         @php
         $totamount += $income->amount;
         @endphp
       </tr>
       @endforeach
      </tbody>
      <tr>
                <td class="total" colspan="2">Total Price</td>
                <td colspan="4" class="total">


                        <b>{{ $currency }}</b> {{ number_format($totamount, 3) }}

                </td>
            </tr>
    </table>

  </div>

</body>

</html>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
  $(document).ready(function() {
    $('#example').DataTable({
      order: [
        [4, 'desc']
      ]
    });
  });
</script>
<script>
  function doSomething2() {
    var branch = document.getElementById("branch").value;
    if (branch === "" || branch === null) {
      alert("Choose Branch");
      return false;
    } else {
      return true;
    }
  }
</script>
