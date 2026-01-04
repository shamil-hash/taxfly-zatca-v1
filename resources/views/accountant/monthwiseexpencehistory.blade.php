<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Expense History</title>
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
    .dropdown {
        position: relative; /* Keep the dropdown in position relative to the button */
        float: right; /* Keep the ☰ button on the right side of the page */
    }

   /* Ensure the dropdown appears on the left side */
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
    <div style="margin-top: 19px;margin-left:15px;">

        @include('navbar.expnavbar')
    </div>
    @else
<x-logout_nav_user />
@endif
 <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Accountant</a></li>
        <li class="breadcrumb-item active" aria-current="page">Expense History</li>
      </ol>
    </nav>
    <div class="dropdown">
        <button class="btn btn-info" style="background-color:#187f6a;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            ☰
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a href="/income" class="dropdown-item ">Add Expense</a>
            <a href="/monthwiseexpensehistory" class="dropdown-item ">Expense History</a>
            <a href="/searchmonthwiseincome" class="dropdown-item ">Income History</a>
        </div>
    </div>
    <h2 ALIGN="CENTER">Expense History</h2>

    <form action="monthwiseexpencehistorydate" method="get" id="myexpense" onsubmit="return validateDateRange()">
        <div class="row">
          <div class="col-sm-8">
            <h4>SELECT DATE RANGE</h4>
            <div class="row">
              <div class="col-sm-4">
                <label for="start_date">From Date:</label>
                <input type="date" class="form-control" value="{{ $start_date ?? '' }}" name="start_date" id="start_date" required>
              </div>
              <div class="col-sm-4">
                <label for="end_date">To Date:</label>
                <input type="date" class="form-control" value="{{ $end_date ?? '' }}" name="end_date" id="end_date" required>
              </div>
              <div class="col-sm-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary form-control">Filter</button>
              </div>
            </div>
          </div>
         <div class="col-lg-1 col-md-2 col-sm-4 col-12" style="padding-top: 61px;">
            <a href="{{ url('/export-expense-history') }}"
               class="btn btn-success w-100" style="background-color:#187f6a;">
                Export
            </a>
        </div>
        </div>
        <br />
      </form>

    <table id="example" class="table table-striped table-bordered" style="width:100%">
      <thead>
        <tr>
          <th>Expense</th>
          <th>Expense Type</th>
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
        @foreach($expenses as $expense)
        <tr>
          <td>
            @if($expense->expense_type == 1)
            {{ $expense->direct_expense }}
            @else
            {{ $expense->indirect_expense }}
            @endif
          </td>
          <td>{{ $expense->expense_type == 1 ? 'Direct' : 'Indirect' }}</td>
          <td>{{ $expense->details }}</td>
          <td><b>{{ $currency }}</b> {{ $expense->amount }}</td>
          <td>{{ date('d M Y', strtotime($expense->date)) }}</td>
          <td>{{ date('d M Y | h:i:s A', strtotime($expense->created_at)) }}</td>
          <td>
          @if($expense->file!=null)
          <span style='background:lightgrey;'>
          <b><a href="{{ url('/expensedownload',$expense->file)}}">{{ $expense->file }}</a></b>
  </span>
            @else
            <b>No File</b>
            @endif
          </td>
          <td>
            <button class="btn btn-danger btn-sm edit-btn"
                    data-id="{{ $expense->id }}"
                    data-expense="{{ $expense->expense_type == 1 ? $expense->direct_expense : $expense->indirect_expense }}"
                    data-type="{{ $expense->expense_type }}"
                    data-details="{{ $expense->details }}"
                    data-amount="{{ $expense->amount }}"
                    data-date="{{ $expense->date }}"
                    data-file="{{ $expense->file }}">
                Edit
            </button>
        </td>
          @php
          $totamount += $expense->amount;
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


    <div class="modal fade" id="editExpenseModal" tabindex="-1" role="dialog" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editExpenseModalLabel">Edit Expense</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form action="{{ url('/update-expense') }}" method="POST">
              @csrf
              <input type="hidden" id="expense_id" name="id">
              <div class="modal-body">
                <div class="form-group">
                  <label>Expense Type</label>
                  <select class="form-control" name="expense_type" id="expense_type" required>
                    <option value="1">Direct</option>
                    <option value="2">Indirect</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Expense</label>
                  <input type="text" class="form-control" name="expense" id="expense" required>
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
        order: [
          [5, 'desc']
        ]
      });
    });
  </script>


</body>

</html>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const expenseType = this.dataset.type;
            const expense = this.dataset.expense;
            const details = this.dataset.details;
            const amount = this.dataset.amount;
            const date = this.dataset.date;

            document.getElementById('expense_id').value = id;
            document.getElementById('expense_type').value = expenseType;
            document.getElementById('expense').value = expense;
            document.getElementById('details').value = details;
            document.getElementById('amount').value = amount;
            document.getElementById('date').value = date;

            $('#editExpenseModal').modal('show');
        });
    });
});

    </script>

<script>
  function validateDateRange() {
    const start = new Date(document.getElementById('start_date').value);
    const end = new Date(document.getElementById('end_date').value);

    if (start > end) {
      alert('Start date cannot be greater than end date.');
      return false;
    }
    return true;
  }
</script>
