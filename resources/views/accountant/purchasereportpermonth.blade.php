<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Accounts Report</title>
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

    .export {
      display: inline-block;
      margin-left: 10px;
    }
     .btn-primary{
            background-color: #187f6a;
            color: white;
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
        <li class="breadcrumb-item active" aria-current="page">Purchase Report</li>
      </ol>
    </nav>
<div style="display: flex; width: 100%; background-color: #f8f9fa; padding: 5px; border-radius: 6px;" role="group" aria-label="Report navigation">
  <div style="flex: 1; margin: 0 3px;">
    <a href="" style="display: block; padding: 10px 5px; background-color: #115c4c; color: white; text-align: center; text-decoration: none; border: 1px solid #0d4539; border-radius: 4px; font-weight: bold; box-shadow: inset 0 2px 4px rgba(0,0,0,0.15);">Purchase Report</a>
  </div>
  <div style="flex: 1; margin: 0 3px;">
    <a href="/accountpurchasereturn" style="display: block; padding: 10px 5px; background-color: #187f6a; color: white; text-align: center; text-decoration: none; border: 1px solid #146354; border-radius: 4px; transition: all 0.3s ease;">Purchase Return Report</a>
  </div>
  <div style="flex: 1; margin: 0 3px;">
    <a href="/accountreturnreport" style="display: block; padding: 10px 5px; background-color: #187f6a; color: white; text-align: center; text-decoration: none; border: 1px solid #146354; border-radius: 4px; transition: all 0.3s ease;">Sales Return Report</a>
  </div>
  <div style="flex: 1; margin: 0 3px;">
    <a href="/salesreport" style="display: block; padding: 10px 5px; background-color: #187f6a; color: white; text-align: center; text-decoration: none; border: 1px solid #146354; border-radius: 4px; transition: all 0.3s ease;">Sales Report</a>
  </div>
</div>
    <h2 ALIGN="CENTER">Purchase Report</h2>

    <div class="input-group" style="margin-bottom: 10px;">
      <a href="/export-purchase-monthsdata/{{$userid}}/{{$viewtype}}/{{$location}}/{{$fromdate}}/{{$todate}}" class="btn btn-primary">Export</a>
    </div>
    <table>
      <tr>
            <th>Date</th>
            <th>Total Purchases</th>
            <th>Total Price</th>
            <th>Total {{$tax}}</th>
            <th>Grand Total <br />(Including {{$tax}})</th>
            <th>Total Discount</th>
            <th>Grand Total <br />(with discount)</th>

            <th>Action</th>
        </tr>
      @foreach($purchases as $purchase)
      <tr>
        <td>{{$purchase->date}}</td>
        <td>{{$purchase->purchase}}</td>
        <td><b>{{ $currency }}</b> {{ $purchase->price_without_vat }}</td>
                    <td>
                        <!--@if ($purchase->vat_amount != null)-->
                        <!--    <b>{{ $currency }}</b> {{ $purchase->vat_amount }}-->
                        <!--@elseif ($purchase->vat_amount == null)-->
                        <!--    <span class='badge badge-default'>NA</span>-->
                        <!--@endif-->
                        <b>{{ $currency }}</b> {{ $purchase->price - $purchase->price_without_vat }}
                    </td>

                    <td>
                        <b>{{ $currency }}</b>
                        <!--{{ $purchase->vat_amount !== null ? number_format($purchase->price_without_vat + $purchase->vat_amount, 3) : $purchase->price_without_vat }}-->
                        {{ $purchase->price }}
                    </td>
                    <td><b>{{ $currency }}</b> {{ $purchase->discount }}</td>
                    <td><b>{{ $currency }}</b> {{ $purchase->price - $purchase->discount }}</td>

        <td>
          <a href="/monthpurchases/{{$purchase->month_id}}/{{$location}}" class="btn btn-primary">VIEW</a>

          <div class="input-group export">
            <a href="/export-purchase-month/{{$userid}}/{{$viewtype}}/{{$location}}/{{$purchase->month_id}}" class="btn btn-primary">Excel</a>
          </div>
        </td>
      </tr>
      @endforeach
    </table>
  </div>

</body>

</html>
