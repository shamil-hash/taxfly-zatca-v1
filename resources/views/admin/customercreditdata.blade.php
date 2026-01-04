<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin</title>
  @include('layouts/adminsidebar')
  <style>
    .gdot {
      height: 15px;
      width: 15px;
      background-color: #0adc0a;
      border-radius: 50%;
      display: inline-block;
      vertical-align: bottom;
    }
  </style>
  <style>
    .cdot {
      height: 15px;
      width: 15px;
      background-color: #cccccc;
      border-radius: 50%;
      display: inline-block;
      vertical-align: bottom;
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
            <li><a href="/adminlogout">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>
    <div align="center">
      @foreach($shopdatas as $shopdata)
      {{$shopdata['name']}}
      <br>
      Phone No:{{$shopdata['phone']}}
      <br>
      Email:{{$shopdata['email']}}
      <br>
      <br>
      @endforeach
    </div>
    <h2>Credit User Summary</h2>

    <table class="table">

      <thead>
                <tr>
                    <th>Sl</th>
                    <th>Date</th>
                    <th>Transaction Type</th>
                    <th>Details</th>
                    <th>Due</th>
                    <th>Invoice</th>
                    <th>Payments</th>
                    <th>Credit Note</th>
                    <th>Balance <br /> (Closing Amount)</th>
                    <th>Desk User ID</th>
                </tr>
            </thead>
            <tbody>
                <?php $number = 1; ?>
                <?php $collectiontotal = 0; ?>
                @foreach ($salesdata as $salesdat)
                    <tr>
                        <td>{{ $number }}</td>
                        <td>{{ date('d M Y | h:i:s A', strtotime($salesdat->created_at)) }}</td>
                        <td>{{ $salesdat->comment }}</td>
                        <td>
                            @if ($salesdat->comment == 'Credit Note')
                                Credit Note
                                {{ $currency }} {{ $salesdat->credit_note }} <br /> for payment of
                                {{ $salesdat->transaction_id }}
                            @elseif ($salesdat->comment == 'Payment Received')
                                @if ($salesdat->transaction_id != '')
                                    {{ $currency }} {{ $salesdat->collected_amount }} <br /> for payment of
                                    {{ $salesdat->transaction_id }}
                                @else
                                    {{ $currency }} {{ $salesdat->collected_amount }} paid.
                                @endif
                            @elseif ($salesdat->comment == 'Payment & Credit Note')
                                @if ($salesdat->transaction_id != '')
                                    {{ $currency }} {{ $salesdat->collected_amount }} <br /> for payment of
                                    {{ $salesdat->transaction_id }} <br /> <br />

                                    Credit Note {{ $currency }} {{ $salesdat->credit_note }} <br /> for payment of
                                    {{ $salesdat->transaction_id }}
                                @else
                                    {{ $currency }} {{ $salesdat->collected_amount }} paid. <br /> <br />
                                    {{ $currency }} {{ $salesdat->credit_note }} added.
                                @endif
                            @elseif ($salesdat->comment == 'Invoice')
                                @if ($salesdat->transaction_id != '')
                                    {{ $currency }} {{ $salesdat->Invoice_due }} <br /> due on transaction
                                    {{ $salesdat->transaction_id }} <br /> <br />
                                @else
                                    {{ $currency }} {{ $salesdat->Invoice_due }} due. <br /> <br />
                                @endif
                            @endif
                        </td>
                        <td><b>{{ $currency }}</b> {{ $salesdat->due }}</td>
                        <td>
                            @if ($salesdat->Invoice_due != 0)
                                <b>{{ $currency }}</b> {{ $salesdat->Invoice_due }}
                            @endif
                        </td>
                        <td>
                            @if ($salesdat->collected_amount != null || $salesdat->collected_amount != 0)
                                <b>{{ $currency }}</b> {{ $salesdat->collected_amount }}
                            @endif
                        </td>
                        <td>
                            @if ($salesdat->credit_note != null || $salesdat->credit_note != 0)
                                <b>{{ $currency }}</b> {{ $salesdat->credit_note }}
                            @endif

                        </td>

                        <td><b>{{ $currency }}</b> {{ $salesdat->updated_balance }}</td>

                        <td>UID{{ $salesdat->user_id }}</td>
                    </tr>
                    <?php $number++; ?>
                    <?php $collectiontotal = $collectiontotal + $salesdat->collected_amount; ?>
                @endforeach
                <tr>
                    <td colspan="10">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="8"></td>
                    <td>Last Due Closing Amount</td>
                    <td><b>{{ $currency }}</b> {{ $finaldue }}</td>
                </tr>
                <tr>
                    <td colspan="8"></td>
                    <td>Total Collection Amount</td>
                    <td><b>{{ $currency }}</b> {{ $collectiontotal }}</td>
                </tr>
            </tbody>
    </table>

    </form>
    {{ $salesdata->links() }}
  </div>

</body>

</html>
