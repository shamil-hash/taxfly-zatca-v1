<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="Plexpay International Recharge">
        <title>Plexpay Recharge</title>
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

@if(Session::has('success'))
<br>
                <div class="alert alert-success">
                    {{Session::get('success')}}
                </div>
                <br>
@endif


<ul class="nav nav-pills">
<li role="presentation"><a href="/plexpaybalance">Balance</a></li>
<li role="presentation" class="active"><a href="/plexpayrecharge">Recharge</a></li>
<li role="presentation"><a href="/plexpayreports">Reports</a></li>
</ul>
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
<br>
<br>
<img src="{{$provider_info['ProviderLogo']}}" alt="Provider Logo">
<br>
<p>{{$provider_info['ProviderName']}}</p>
<br>

<div class="container-fluid">
    <div class="row">
      @foreach($plans as $plan)
      <form action="/plexpayregisterpost" method="POST">
        @csrf
            <div class="col-sm-3">
            <div class="panel panel-info">
            <input type="hidden" name="dash" value="{{$dash}}"> 
            <input type="hidden" name="ProviderName" value="{{$provider_info['ProviderLogo']}}">
            <input type="hidden" name="ProviderName" value="{{$provider_info['ProviderName']}}">
            @if(!empty($plan['Our_SendValue']))
            <input type="hidden" name="Our_SendValue" value="{{$plan['Our_SendValue']}}">
            @else
            <input type="hidden" name="Our_SendValue" value="0">
            @endif
            @if(!empty($plan['SendValue']))
            <input type="hidden" name="SendValue" value="{{$plan['SendValue']}}">
            @else
            <input type="hidden" name="SendValue" value="0">
            @endif
            @if(!empty($plan['ReceiveValue']))
            <input type="hidden" name="ReceiveValue" value="{{$plan['ReceiveValue']}}">
            @else
            <input type="hidden" name="ReceiveValue" value="0">
            @endif
            @if(!empty($plan['minValue']))
            <input type="hidden" name="minValue" value="{{$plan['minValue']}}">
            @endif
            @if(!empty($plan['maxValue']))
            <input type="hidden" name="maxValue" value="{{$plan['maxValue']}}">
            @endif
            <input type="hidden" name="phone" value="{{$phonenumber}}">
            <input type="hidden" name="SkuCode" value="{{$plan['SkuCode']}}">
            <input type="hidden" name="Country_Iso" value="{{$provider_info['Country_Iso']}}">
            <input type="hidden" name="ProviderCode" value="{{$provider_info['ProviderCode']}}">
            <input type="hidden" name="CoupenTitle" value="{{$plan['CoupenTitle']}}">
            <input type="hidden" name="Expiry_Date" value="{{$plan['Expiry_Date']}}">
            <input type="hidden" name="SendCurrencyIso" value="{{$plan['SendCurrencyIso']}}">
            <input type="hidden" name="method" value="{{$method}}">
            <input type="hidden" name="amount" value="{{$amount}}">
            <div class="panel-heading text-center">{{$plan['CoupenTitle']}}
            </div>
            @if(!empty($plan['Our_SendValue']))
            <div class="panel-body text-center">{{$plan['Our_SendValue']}}&nbsp;{{$plan['SendCurrencyIso']}}
            </div>
            @endif
            <div class="panel-body text-center">Validity : &nbsp;{{$plan['Expiry_Date']}}
            </div>
            <div align="center">
            <button type="submit" class="btn btn-primary" onclick="doSomething(this.value)" id="product" value="">buy</button>
            <div class="panel-body text-center">&nbsp
            </div>
            </div>
            </div>
            </div>
            </form>
      @endforeach
    </div>      
</div>
  </div>
  </div>
</body>
</html>
