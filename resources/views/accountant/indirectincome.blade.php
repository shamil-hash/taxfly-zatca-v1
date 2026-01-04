<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Indirect Income</title>
  @include('layouts/usersidebar')
  <style>
    #content {
      padding: 30px;
    }

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
    @include('navbar.expnavbar')
    @else
    <x-logout_nav_user />
    @endif
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Accounts</a></li>
        <li class="breadcrumb-item active" aria-current="page">Indirect Income</li>
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
        <a href="/expenseshistory" class="btn btn-primary">Expenses History</a>
      </div>
      <div class="btn-group" role="group">
        <a href="/monthwiseexpensehistory" class="btn btn-primary">Month-wise Expense History</a>
      </div>
      <div class="btn-group" role="group">
        <a href="" class="btn btn-primary active">Indirect Income</a>
      </div>
      <div class="btn-group" role="group">
        <a href="/searchmonthwiseincome" class="btn btn-primary">Month-wise Income History</a>
      </div>
    </div>
    <form action="indirectincomesubmit" method="POST" enctype="multipart/form-data" id="indirectincomeform" onsubmit="return validateForm();">
      @csrf
      <h2 ALIGN="CENTER">Indirect Income </h2>
      @csrf
      <div class="row">
        <div class="col-sm-2" id="branchdiv">
          <select name="branch" id="branch" class="form-control">
            <option value="">SELECT BRANCH</option>
            @foreach($branch as $branch)
            <option value="{{$branch->id}}">{{$branch->branchname}}</option>
            @endforeach
          </select>
          <span style="color:red">@error('branch'){{$message}}@enderror</span>
        </div>
      </div>
      <div class="form group row">

        <table class="table">
          <thead>
            <tr>
              <th width="25%">Comment</th>
              <th width="25%">Amount</th>
              <th width="25%">Date</th>
              <th width="25%"></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr bgcolor="#187f6a">
              <td>
                <input type="text" id="comment" placeholder="Comment" class="form-control comment" autofocus>
                <span style="color:red">@error('comment'){{$message}}@enderror</span>
              </td>
              </td>
              <td>
                <input type="text" id="amount" placeholder="Amount" class="form-control amount">
                <span style="color:red">@error('amount'){{$message}}@enderror</span>
              </td>
              <td>
                <input type="date" class="form-control start_date" value={{$start_date}} name="start_date" id="start_date">
                <span style="color:red">@error('start_date'){{$message}}@enderror</span>
              </td>
              <td></td>
              <td><a href="#" class="btn btn-info addRow" title="Add Row">+</a>
              </td>
            </tr>
            <tr>
              <td colspan="3"> <i class="glyphicon glyphicon-tags"></i> &nbsp DATA
              <td>FILE</td>
              <td>
              </td>
              </td>
            <tr>
          </tbody>
        </table>
      </div>
      <input class="btn btn-primary" type="submit" value="submit" id="submitBtn">
    </form>
  </div>

</body>

</html>

<!--  -->

<script>
  $(document).ready(function() {

    $('#branch').change(function() {
      $('#branchdiv').hide();

    });
  });
</script>
<!--  -->

<script type="text/javascript">
  $('.addRow').on('click', function() {
    addRow();
  });

  function addRow() {
    var b = ($("#branch").val());
    if (($("#branch").val()) == "") {
      return;
    }

    var y = ($("#comment").val());
    // if (($("#comment").val()) == "") {
    //   return;
    // }
    var w = ($("#amount").val());
    if (($("#amount").val()) == "") {
      return;
    }
    var z = ($("#start_date").val());
    var f = ($("#file").val());
    var tr = '<tr>' + '<td>' +
      // '</td>'+'<td>'+
      '<input type="text" placeholder="Comment" value="' + y + '" name="comment[]" class="form-control">' +
      '</td>' +
      '<td><input type="text" placeholder="Amount" value="' + w + '" name="amount[]" id="amountfield" class="form-control"></td>' +
      '<td><input type="text" value="' + z + '" name="start_date[]" class="form-control"></td>' +
      '<td><input type="file" name="image[]" multiple class="form-control" accept="image/*"></td>' +
      '<td><a href="#" class="btn btn-danger remove">-</a></td>' +
      '</tr>';
    $('tbody').append(tr);
    var nu = "";
    $("#comment").val(nu);
    $("#amount").val(nu);
  };
  $('tbody').on('click', '.remove', function() {
    $(this).parent().parent().remove();
  });
  $('form input:not([type="submit"])').keydown((e) => {
    if (e.keyCode === 13) {
      e.preventDefault();
      return false;
    }
    return true;
  });
</script>
<script type="text/javascript">
  var currentBoxNumber = 0;

  $(".comment").keydown(function(event) {
    if (event.keyCode == 13) {
      textboxes = $("input.amount");
      currentBoxNumber = textboxes.index(this);
      if (textboxes[currentBoxNumber + 1] != null) {
        nextBox = textboxes[currentBoxNumber + 1];
        nextBox.focus();
        nextBox.select();
      }
      event.preventDefault();
      return false;
    }
  });
  $(".amount").keydown(function(event) {
    if (event.keyCode == 13) {
      textboxes = $("input.start_date");
      currentBoxNumber = textboxes.index(this);
      if (textboxes[currentBoxNumber + 1] != null) {
        nextBox = textboxes[currentBoxNumber + 1];
        nextBox.focus();
        nextBox.select();
      }
      event.preventDefault();
      return false;
    }
  });
</script>

<script>
  var comment = document.getElementById("comment");
  var amount = document.getElementById("amount");
  var start_date = document.getElementById("start_date");

  comment.addEventListener("keyup", function(event) {
    if (event.keyCode === 13) {
      event.preventDefault();
      $('.addRow').click();
    }
  });
  amount.addEventListener("keyup", function(event) {
    if (event.keyCode === 13) {
      event.preventDefault();
      $('.addRow').click();
    }
  });
  start_date.addEventListener("keyup", function(event) {
    if (event.keyCode === 13) {
      event.preventDefault();
      $('.addRow').click();
    }
  });
</script>

<script>
    // document.addEventListener("DOMContentLoaded", function() {
    //     const form = document.getElementById("indirectincomeform");
    //     const submitBtn = document.getElementById("submitBtn");

    //     form.addEventListener("submit", function(e) {
    //         // Prevent the form from submitting multiple times
    //         submitBtn.disabled = true;
    //         submitBtn.innerText = "Submitting...";

    //         // Allow the form to submit normally
    //         return true;
    //     });
    // });

    function validateForm() {
        // Prevent the form from submitting multiple times
        const form = document.getElementById("indirectincomeform");
        const submitBtn = document.getElementById("submitBtn");

        if (submitBtn.disabled) {
            return false; // Prevent form submission
        }

        // Disable the submit button
        submitBtn.disabled = true;
        submitBtn.innerText = "Submitting...";

        // Validate Title
        var amt = $("#amountfield").val();
        if (amt == "" || amt == null) {
            alert("Press the add button");

            // Re-enable the submit button after alert
            submitBtn.disabled = false;
            submitBtn.innerText = "Submit";

            return false; // Validation failed; prevent form submission
        }

        // Form is valid; continue with submission
        // You can also perform any additional logic here

        // No need to re-enable the submit button here since the form will submit

        return true; // Validation passed; allow the form to submit
    }
</script>
