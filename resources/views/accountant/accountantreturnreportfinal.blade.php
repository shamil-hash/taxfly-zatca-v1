<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Return Report</title>
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
    @include('navbar.expnavbar')
    @else
    <x-logout_nav_user />
    @endif
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Accountant</a></li>
        <li class="breadcrumb-item active" aria-current="page">Accounts Report</li>
        <li class="breadcrumb-item active" aria-current="page">Return Report</li>
      </ol>
    </nav>
    <div class="btn-group btn-group-justified" role="group" aria-label="...">
      <div class="btn-group" role="group">
        <a href="/accountreport" class="btn btn-primary">Purchase Report</a>
      </div>
      <div class="btn-group" role="group">
        <a href="/accountpurchasereturn" class="btn btn-primary">Purchase Return Report</a>
      </div>
      <div class="btn-group" role="group">
        <a href="/accountreturnreport" class="btn btn-primary active">Sales Return Report</a>
      </div>
      <div class="btn-group" role="group">
        <a href="/salesreport" class="btn btn-primary">Sales Report</a>
      </div>
    </div>
    <form action="" method="POST">
      @csrf
      <h2 ALIGN="CENTER">Return Report</h2>
      <table class="table">
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Date and Time</th>
            <th>Price</th>
            <th>Value Added Tax</th>
            <th>Grand Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach($returns as $return)
          <tr>
            <td>
              {{$return['product_name']}}
            </td>
            <td>
              {{$return['quantity']}}
            </td>
            <td>
              {{$return['created_at']}}
            </td>
            <td>
              {{$return['price']}}
            </td>
            <td>
              {{$return['total_amount']-$return['price']}}
            </td>
            <td>
              {{$return['total_amount']}}
            </td>
            @endforeach
          </tr>
        </tbody>
      </table>
  </div>
</body>

</html>
