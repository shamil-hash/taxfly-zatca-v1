<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sales Report</title>
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
                <li class="breadcrumb-item"><a href="#">Accountant</a></li>
                <li class="breadcrumb-item active" aria-current="page">Accounts Report</li>
                <li class="breadcrumb-item active" aria-current="page">Sales Report</li>
            </ol>
        </nav>
        <div style="display: flex; width: 100%; background-color: #f8f9fa; padding: 5px; border-radius: 6px;" role="group" aria-label="Report navigation">
  <div style="flex: 1; margin: 0 3px;">
    <a href="/accountreport" style="display: block; padding: 10px 5px; background-color: #187f6a; color: white; text-align: center; text-decoration: none; border: 1px solid #146354; border-radius: 4px; transition: all 0.3s ease;">Purchase Report</a>
  </div>
  <div style="flex: 1; margin: 0 3px;">
    <a href="/accountpurchasereturn" style="display: block; padding: 10px 5px; background-color: #187f6a; color: white; text-align: center; text-decoration: none; border: 1px solid #146354; border-radius: 4px; transition: all 0.3s ease;" >Purchase Return Report</a>
  </div>
  <div style="flex: 1; margin: 0 3px;">
    <a href="/accountreturnreport" style="display: block; padding: 10px 5px; background-color: #187f6a; color: white; text-align: center; text-decoration: none; border: 1px solid #146354; border-radius: 4px; transition: all 0.3s ease;">Sales Return Report</a>
  </div>
  <div style="flex: 1; margin: 0 3px;">
    <a href=""  style="display: block; padding: 10px 5px; background-color: #115c4c; color: white; text-align: center; text-decoration: none; border: 1px solid #0d4539; border-radius: 4px; font-weight: bold; box-shadow: inset 0 2px 4px rgba(0,0,0,0.15);" >Sales Report</a>
  </div>
</div>
        <h2 ALIGN="CENTER">Sales Report </h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Total Price</th>
                    <th>Discount <br /> amount</th>
                    <th>{{$tax}}</th>
                    <th>Grand Total <br /> (w/. discount)</th>
                    @if (Session('softwareuser'))
                     @foreach ($users as $user)
                    <?php if ($user->role_id == '28') { ?>
                    <th>Credit Note</th>
                    <th>Total </br>(Grand Total -
                        Credit Note
                        )</th>
                        <?php } ?>
                        @endforeach
                        @endif
                    <th>Date and Time</th>
                    <th>Customer Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>
                            {{ $product->transaction_id }}
                        </td>

                        <td>
                            @if ($product->grandtotal_without_discount != '')
                                <b>{{ $currency }}</b> {{ $product->grandtotal_without_discount }}
                            @elseif ($product->grandtotal_without_discount == '')
                                <b>{{ $currency }}</b> {{ $product->sum }}
                            @endif
                        </td>
                        <td>
                            @if ($product->discount_amount != '')
                                <b>{{ $currency }}</b> {{ number_format($product->discount_amount, 3) }}
                            @endif
                        </td>
                        <td>
                            <b>{{ $currency }}</b> {{ $product->vat }}
                        </td>
                        <td>

                            @if($product->vat_type==1)
                                  @if (!is_null($product->grandtotal_without_discount))
                                 <b>{{ $currency }}</b> {{ number_format($product->grandtotal_without_discount - $product->discount_amount, 3) }}
                                 @else
                                 <b>{{ $currency }}</b> {{ number_format($product->sum * ($product->quantity - $product->discount_amount), 3) }}
                                 @endif
                            @else
                                  <b>{{ $currency }}</b> {{ $product->sum }}
                            @endif

                            </td>
                            @if (Session('softwareuser'))
                            @foreach ($users as $user)
                            @if ($user->role_id == '28')
                            <td>
                                {{ $product->total_credit_note }}
                            </td>
                            <td>

                                @if($product->vat_type==1)
                                @if (!is_null($product->grandtotal_without_discount))
                                <b>{{ $currency }}</b> {{ number_format($product->grandtotal_without_discount - $product->discount_amount - $product->total_credit_note, 3) }}
                                @else
                                <b>{{ $currency }}</b> {{ number_format($product->sum * ($product->quantity - $product->discount_amount) - $product->total_credit_note, 3) }}
                                @endif
                                @else
                                <b>{{ $currency }}</b> {{ $product->sum - $product->total_credit_note }}
                                @endif

                            </td>
                            @endif
                            @endforeach
                            @endif

                        <td>
                            {{ date('d M Y | h:i:s A', strtotime($product->created_at)) }}

                        </td>
                        <td>
                            {{ $product->customer_name }}
                        </td>
                        <td>
                            <a href="/salesreportfinal/{{ $product->transaction_id }}">VIEW</a>
                        </td>
                @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
