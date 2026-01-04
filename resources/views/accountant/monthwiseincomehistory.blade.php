<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Income History</title>
      <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">

  @include('layouts/usersidebar')
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
    }
 .btn-primary{
            background-color: #187f6a;
            color: white;
        }
    th,
    td {
      border: 1px solid black;
      text-align: left;
      padding: 8px;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
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

    .branch {
      float: left;
      margin: 2rem 0;
      width: 15%;
    }

    .dropdown {
        position: relative; /* Keep the dropdown in position relative to the button */
        float: right; /* Keep the ☰ button on the right side of the page */
    }
    .dropdown-menu {
    display: none;
    position: absolute;
    right: 100%;  /* Open the dropdown to the left of the button */
    top: 70; /* Align it with the top of the button */
    background: white;
    border: 1px solid #ddd;
    padding: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-left: -120px;
}

.dropdown:hover .dropdown-menu {
    display: block; /* Show the dropdown when hovering */
}

.dropdown-menu a {
    display: block;
    padding: 8px 12px;
    text-decoration: none;
    color: black;
    border-bottom: 1px solid #ddd;

}

.dropdown-menu a:last-child {
    border-bottom: none;
}

.dropdown-menu a:hover {
    background: #187f6a;
    color: white;
}
                    div.dataTables_wrapper div.dataTables_paginate ul.pagination li a {
            color: #187f6a !important;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.active a,
        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.active a:focus,
        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.active a:hover {
            background-color: #187f6a !important;
            border-color: #187f6a !important;
            color: white !important;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination li a:hover {
            background-color: #187f6a !important;
            border-color: #187f6a !important;
            color: white !important;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.disabled a {
            color: #6c757d !important;
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
    <div style="margin-top: 15px;margin-left:15px;">

        @include('navbar.expnavbar')
    </div>
    @else
<x-logout_nav_user />
@endif
 <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Accountant</a></li>
        <li class="breadcrumb-item active" aria-current="page">Income History</li>
      </ol>
    </nav>
    <div class="dropdown">
        <button class="btn btn-info" style="background-color:#187f6a;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            ☰
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a href="/income" class="dropdown-item ">Add Income</a>
            <a href="/monthwiseexpensehistory" class="dropdown-item ">Expense History</a>
            <a href="/searchmonthwiseincome" class="dropdown-item ">Income History</a>
        </div>
    </div>
    <h2 ALIGN="CENTER">Income History</h2>
{{-- <br>
    <div class="btn-group btn-group-justified" role="group" aria-label="...">
        <div class="btn-group" role="group">
          <a href="/income" class="btn btn-primary ">Income and Expense</a>
        </div>
        <div class="btn-group" role="group">
          <a href="/monthwiseexpensehistory" class="btn btn-primary">Expense History</a>
        </div>
        <div class="btn-group" role="group">
          <a href="/searchmonthwiseincome" class="btn btn-primary active">Income History</a>
        </div>
      </div>
    <!-- content -->
<br> --}}
    <form action="{{ url('monthwseincomehistory') }}" method="get" id="myexpense" onsubmit="return doSomething2()">
      <div class="row">
        <div class="col-sm-6">
          <h4>SELECT MONTHS</h4>
          <div class="row">
            <div class="col-sm-5">
              <input type="month" class="form-control" value="{{ $start_date }}" name="start_date">
            </div>
            <div class="col-sm-5">
              <select name="branch" id="branch" class="form-control">
                <option value="">SELECT BRANCH</option>
                @foreach($branch as $b)
                <option value="{{ $b->id }}">{{ $b->branchname }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-sm-2">
              <button type="submit" class="btn btn-primary">Filter</button>
            </div>
          </div>
        </div>
      </div>
      <br />
    </form>

    <table id="example" class="table table-striped table-bordered" style="width:100%">
      <thead>
        <tr>
          <th>Income</th>
          <th>Income Type</th>
          <th>Details</th>
          <th>Amount</th>
          <th>Date</th>
          <th>Created At</th>
          <th>Download</th>
          <th>Edit</th>
        </tr>
      </thead>
      <tbody>
        @php
        $totamount = 0;
        @endphp
        @foreach($incomes as $income)
        <tr>
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
          <td>
            <button class="btn btn-danger btn-sm edit-income"
                    data-id="{{ $income->id }}"
                    data-income="{{ $income->income_type == 1 ? $income->direct_income : $income->indirect_income }}"
                    data-type="{{ $income->income_type }}"
                    data-details="{{ $income->details }}"
                    data-amount="{{ $income->amount }}"
                    data-date="{{ $income->date }}"
                    data-toggle="modal"
                    data-target="#editIncomeModal">
                Edit
            </button>
        </td>

          @php
          $totamount += $income->amount;
          @endphp
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td class="total" colspan="3">Total Amount</td>
          <td class="total" colspan="4"><b>{{ $currency }}</b> {{ number_format($totamount, 2) }}</td>
        </tr>
      </tfoot>
    </table>


    <div class="modal fade" id="editIncomeModal" tabindex="-1" role="dialog" aria-labelledby="editIncomeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editIncomeModalLabel">Edit Income</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form action="{{ url('/update-income') }}" method="POST">
              @csrf
              <input type="hidden" id="income_id" name="id">
              <div class="modal-body">
                <div class="form-group">
                  <label>Income Type</label>
                  <select class="form-control" name="income_type" id="income_type" required>
                    <option value="1">Direct</option>
                    <option value="2">Indirect</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Income</label>
                  <input type="text" class="form-control" name="income" id="income" required>
                </div>
                <div class="form-group">
                  <label>Details</label>
                  <input type="text" class="form-control" name="details" id="details">
                </div>
                <div class="form-group">
                  <label>Amount</label>
                  <input type="number" class="form-control" name="amount" id="amount" required>
                </div>
                <div class="form-group">
                  <label>Date</label>
                  <input type="date" class="form-control" name="date" id="date" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>



  </div>

  <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#example').DataTable({
        order: [[0, 'asc']]
      });
    });
  </script>

  <script>
    function doSomething2() {
      var branch = document.getElementById("branch").value;
      if (branch === "" || branch === null) {
        alert("Choose Branch");
        return false;
      }
      return true;
    }
  </script>
</body>

</html>
<script>
    $(document).ready(function() {
      $(".edit-income").click(function() {
        let income_id = $(this).data("id");
        let income_type = $(this).data("type");
        let income = $(this).data("income");
        let details = $(this).data("details");
        let amount = $(this).data("amount");
        let date = $(this).data("date");

        $("#income_id").val(income_id);
        $("#income_type").val(income_type);
        $("#income").val(income);
        $("#details").val(details);
        $("#amount").val(amount);
        $("#date").val(date);
      });
    });
  </script>
