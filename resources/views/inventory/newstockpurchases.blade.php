<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>New Stock Purchases</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">
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
    <div style="margin-left:15px;margin-top:18px;">

        @include('navbar.invnavbar')
    </div>
    @else
<x-logout_nav_user />
@endif
<x-admindetails_user :shopdatas="$shopdatas" />

    <ul class="nav nav-tabs">
      <!--<li role="presentation"><a href="/inventorystock">Add Stocks </a></li>-->
      <li role="presentation" class="active"><a href="#">New Purchases</a></li>
      <li role="presentation"><a href="/liststocks">List Stocks </a></li>
    </ul>
    <br>
    <table id="example" class="table table-striped table-bordered" style="width:100%">
      <thead>
        <tr>
          <th>Reciept Number</th>
          <th>Comment</th>
          <th>Product Name</th>
          <th>Quantity</th>
          <th>Price</th>
          <th>Supplier Name</th>
          <th>Date and Time</th>
        </tr>
      </thead>
      <tbody>
        @if (count($newproducts) >= 1)
        @foreach($newproducts as $newproduct)
        <tr>
          <td>{{$newproduct->reciept_no}}</td>
          <td>{{$newproduct->comment}}</td>
          <td>{{$newproduct->product_name}}</td>
          <td>{{$newproduct->quantity}}</td>
          <td><b>{{ $currency }}</b> {{ $newproduct->price }}</td>
          <td>{{$newproduct->supplier}}</td>
          <td> {{ date('d M Y | h:i:s A', strtotime($newproduct->created_at)) }}</td>
        </tr>
        @endforeach
        @else
        @foreach($products as $product)
        <tr>
          <td>{{$product->reciept_no}}</td>
          <td>{{$product->comment}}</td>
          <td>{{$product->product_name}}</td>
          <td>{{$product->quantity}}</td>
          <td><b>{{ $currency }}</b> {{ $product->price }}</td>
          <td>{{$product->supplier}}</td>
          <td>{{ date('d M Y | h:i:s A', strtotime($product->created_at)) }}</td>
        </tr>
        @endforeach
        @endif
      </tbody>
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
        [6, 'desc']
      ]
    });
  });
</script>
