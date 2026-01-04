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

    .total {
      font-weight: bold;
      background-color: white;
      font-size: large;
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
        <li class="breadcrumb-item active" aria-current="page">Expenses History</li>
      </ol>
    </nav>
    <div class="btn-group btn-group-justified" role="group" aria-label="...">
      <div class="btn-group" role="group">
        <a href="/companyexpenses" class="btn btn-primary">Monthly Expenses</a>
      </div>
      <div class="btn-group" role="group">
        <a href="/employeesalary" class="btn btn-primary">Salary</a>
      </div>
      <div class="btn-group" role="group">
        <a href="" class="btn btn-primary active">Expenses History</a>
      </div>
      <div class="btn-group" role="group">
        <a href="/monthwiseexpensehistory" class="btn btn-primary">Month-wise Expense History</a>
      </div>
      <div class="btn-group" role="group">
        <a href="/indirectincome" class="btn btn-primary">Indirect Income</a>
      </div>
      <div class="btn-group" role="group">
        <a href="/searchmonthwiseincome" class="btn btn-primary">Month-wise Income History</a>
      </div>
    </div>
    <h2 ALIGN="CENTER">Expenses History </h2>

    <form action="expenseshistorydate" method="get" id="myexpense" onsubmit="return doSomething2()">
      @csrf
      <div class="row">
        <div class="col-sm-2">
          <select name="branch" id="branch" class="form-control">
            <option value="">SELECT BRANCH</option>
            @foreach($branch as $branch)
            <option value="{{$branch->id}}">{{$branch->branchname}}</option>
            @endforeach
          </select>
        </div>
        <div class="col-sm-2">
          <button type="submit" class="btn btn-primary" title="Filter By Branch">VIEW</button>
        </div>
      </div>
      <br />

    </form>

    <table id="example" class="table table-striped table-bordered" style="width:100%">
      <thead>
        <tr>
          <th width="20%">Branch</th>
          <th width="20%">Comment</th>
          <th width="20%">Amount</th>
          <th width="20%">Date</th>
          <th width="20%">created at</th>
          <th width="5%">Download</th>
        </tr>
      </thead>
      <tbody>
        @php
        $totamount=0;
        @endphp
        @foreach($expenses as $expense)
        <tr>
          <td>{{$expense->branchname}}</td>
          <td>{{$expense->comment}}</td>
          <td><b>{{ $currency }}</b> {{ $expense->amount }}</td>
          <td>{{ date('d M Y', strtotime($expense->date)) }}</td>
          <td>{{ date('d M Y | h:i:s A', strtotime($expense->created_at)) }}</td>
          <td><b><a href="{{url('/expensedownload',$expense->file)}}">{{$expense->file}}</a></b></td>
          @php
          $totamount=$totamount+$expense->amount;
          @endphp
        </tr>
        @endforeach
      </tbody>
      <tr>
                <td class="total" colspan="2">Total Price</td>
                <td class="total" colspan="4"><b>{{ $currency }}</b> {{ number_format($totamount, 3) }}</td>
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
        [0, 'asc']
      ]
    });
  });
</script>
