<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Plexpay Recharge">
    <title>Plexpay Balance</title>
      @include('layouts/usersidebar')
    <style>
      table {
        border-collapse: collapse;
        width: 100%;
      }
      th, td {
        border: 1px solid black;
        text-align: left;
        padding: 8px;
      }
      tr:nth-child(even){background-color: #f2f2f2}
      th {
        background-color: #20639B;
        color: white;
      }
      .table>thead>tr>th {
          vertical-align: bottom;
          border-bottom: 1px solid #010101;
      }
      div.nav-dat {
        font-size: 20px;
        color: #20639B;
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
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="">Plexpay</a></li>
          <li class="breadcrumb-item active" aria-current="page">Recharge</li>
        </ol>
      </nav>
      <div class="nav-dat" align="right">
        Balance {{$wallet_amount}} &nbsp; Due {{$due_amount}}
      </div>
      <br>

      <div class="dropdown" align="right">
        <button class="btn btn-default dropdown-toggle" style="padding:10px 15px;background-color:#20639B;color:white;" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
          Profile
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
          <li><a href="/plexpayfunds">Funds</a></li>
          <li><a href="/plexpaytransactions">Transactions</a></li>
          <li><a href="/plexpaycollection">Collection</a></li>
          <li><a href="/plexpayduesummary">Due Summary</a></li>
          <li><a href="/plexpaysummary">Profit Summary</a></li>
          <li role="presentation"><a href="/plexpayreports">Reports</a></li>
          <li><a href="/plexpayregister">Plexpay Register</a></li>
          <li><a href="/plexpaypasswordchange">Change Password</a></li>
        </ul>
      </div>
      <br><br>
      @if($id==1)

        @if($Our_SendValue!=0 || $SendValue!=0)
          <form action="/customrechargepost" method="GET">
          @csrf
            <div class="container-fluid">
              <div class="row">
                <div class="col-sm-4"></div>
                <div class="col-sm-4">
                  <div class="input-group">
                    <input type="hidden" name="ReceiveValue" value="{{$ReceiveValue}}" class="form-control" placeholder="ReceiveValue">
                    <input type="hidden" name="SkuCode" value="{{$SkuCode}}" class="form-control" placeholder="SkuCode">
                    <input type="hidden" name="ProviderCode" value="{{$ProviderCode}}" class="form-control" placeholder="ProviderCode">
                    <input type="hidden" name="ReceiveCurrencyIso" value="{{$ReceiveCurrencyIso}}" class="form-control" placeholder="ReceiveCurrencyIso">
                    <input type="hidden" name="Our_SendValue" value="{{$Our_SendValue}}" class="form-control" placeholder="Our_SendValue">
                    <input type="hidden" name="Country_Iso" value="{{$Country_Iso}}" class="form-control" placeholder="Country_Iso">
                    <input type="hidden" name="SendValue" value="{{$SendValue}}" class="form-control" placeholder="SendValue">
                    <input type="hidden" name="SendCurrencyIso" value="{{$SendCurrencyIso}}" class="form-control" placeholder="SendCurrencyIso">
                    <input type="hidden" name="method" value="3" class="form-control" name="method">
                    <input type="hidden" name="id" value={{$id}} class="form-control" placeholder="id">
                
                    <input type="text" name="AccountNumber" class="form-control" placeholder="Enter the number" value="<?php echo $Account?>">
                    <span class="input-group-addon" style="padding: 5px 12px;" id="basic-addon2"><span class="glyphicon glyphicon-phone"></span></span>
                  </div>
                  <br>
                  <label for="complaint">Amount in {{$ReceiveCurrencyIso}} </label>
                  <input type="text" name="Amount" class="form-control" placeholder="" style="width: 250px;" value=<?php echo $amount?>>
                  <br>
                  <label for="complaint">Amount in {{$SendCurrencyIso}} </label>
                  <input type="text" name="Our_SendValue" class="form-control" placeholder="Recharge" style="width: 250px;" value=<?php echo $Our_SendValue?>>
                  <br>
                  <button type="submit" class="btn btn-primary">Recharge Now</button>
                  <br>
                </div>
              </div>   
            </div>
          </form>
        @else
          <form action="/giftcardrecharge/{{$id}}" method="GET">
            <div class="container-fluid">
              <div class="row">
                <div class="col-sm-4"></div>
                <div class="col-sm-4">
                  <div class="input-group">
                    <input type="hidden" name="ReceiveValue" value="{{$ReceiveValue}}" class="form-control" placeholder="ReceiveValue">
                    <input type="hidden" name="SkuCode" value="{{$SkuCode}}" class="form-control" placeholder="SkuCode">
                    <input type="hidden" name="ProviderCode" value="{{$ProviderCode}}" class="form-control" placeholder="ProviderCode">
                    <input type="hidden" name="ReceiveCurrencyIso" value="{{$ReceiveCurrencyIso}}" class="form-control" placeholder="ReceiveCurrencyIso">
                    <input type="hidden" name="Our_SendValue" value="{{$Our_SendValue}}" class="form-control" placeholder="Our_SendValue">
                    <input type="hidden" name="Country_Iso" value="{{$Country_Iso}}" class="form-control" placeholder="Country_Iso">
                    <input type="hidden" name="SendValue" value="{{$SendValue}}" class="form-control" placeholder="SendValue">
                    <input type="hidden" name="SendCurrencyIso" value="{{$SendCurrencyIso}}" class="form-control" placeholder="SendCurrencyIso">
                    <input type="hidden" name="method" value="3" class="form-control" name="method">
                    <input type="hidden" name="id" value={{$id}} class="form-control" placeholder="id">
                    <input type="text" name="AccountNumber" class="form-control" placeholder="Enter the number">
                    <span class="input-group-addon" style="padding: 5px 12px;" id="basic-addon2"><span class="glyphicon glyphicon-phone"></span></span>
                  </div>
                  <br>
                  <input type="text" name="Amount" class="form-control" placeholder="Enter the amount {{$ReceiveCurrencyIso}}" style="width: 250px;" value=<?php echo $amount?>>
                  <br>
                  <button type="submit" class="btn btn-primary">Recharge Now</button>
                  <br>
                </div>
              </div>   
            </div>
          </form>
        @endif

      @elseif($id==2)
        <form action="/ElectricityGETpayment" method="GET">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-4"></div>
                <div class="col-sm-4">
                  <input type="hidden" name="ProviderCode" value="{{$ProviderCode}}" class="form-control" placeholder="ProviderCode">
                  <input type="hidden" name="Country_Iso" value="{{$Country_Iso}}" class="form-control" placeholder="Country_Iso">
                  <input type="hidden" name="ReceiveValue" value={{$ReceiveValue}} class="form-control" placeholder="ReceiveValue">
                  <input type="hidden" name="SkuCode" value={{$SkuCode}} class="form-control" placeholder="SkuCode">
                  <input type="hidden" name="ProviderCode" value={{$ProviderCode}} class="form-control" placeholder="ProviderCode">
                  <input type="hidden" name="method" value="4" class="form-control" name="method">
                  <input type="hidden" name="id" value={{$id}} class="form-control" placeholder="id">
                  <div class="input-group">
                    <input type="text" name="AccountNumber" class="form-control" placeholder="Enter the Consumer Number" style="width: 250px;" value="{{$AccountNumber}}" >
                    <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                  </div>
                </div>
              </div>
            </div>   
        </form>
      @if(!empty($plan_info))
        <form action="/customrechargepost" method="GET">
          @csrf
          <br>
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-4"></div>
              <div class="col-sm-4">
                @if($plan_info=="No Pending Bills")
                  {{$plan_info}}
                @else
                  CUSTOMER NAME :{{$plan_info["customername"]}}, AMOUNT :{{$plan_info["BillAmount"]}}, DUE DATE:{{$plan_info["dueDate"]}}
                  <div class="input-group">
                    <input type="hidden" name="Country_Iso" value="{{$Country_Iso}}" class="form-control" placeholder="Country_Iso">
                    <input type="hidden" name="ReceiveValue" value={{$ReceiveValue}} class="form-control" placeholder="ReceiveValue">
                    <input type="hidden" name="SkuCode" value={{$SkuCode}} class="form-control" placeholder="SkuCode">
                    <input type="hidden" name="ProviderCode" value={{$ProviderCode}} class="form-control" placeholder="ProviderCode">
                    <input type="hidden" name="ReceiveCurrencyIso" value={{$ReceiveCurrencyIso}} class="form-control" placeholder="ReceiveCurrencyIso">
                    <input type="hidden" name="method" value="4" class="form-control" name="method">
                    <input type="hidden" name="id" value={{$id}} class="form-control" placeholder="id">
                    <input type="hidden" name="AccountNumber" class="form-control" placeholder="phone Number" value="{{$AccountNumber}}">
                  </div>
                  <br>
                  <input type="text" name="Amount" class="form-control" style="width: 250px;"  placeholder="Enter amount in INR">
                  <br>
                  <button type="submit" class="btn btn-primary">Recharge Now</button>
                  <br>
                @endif
              </div>
            </div>   
          </div>
        </form>
      @endif
@endif
  <div class="col-md-1">
  </div>
</div>
<br>
  </div>
  </div>
</body>
</html>
