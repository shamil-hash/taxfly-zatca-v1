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
<br>

<div class="container-fluid" > 
  
      
      <form action="/plexpayrechargeinternationalnumber" method="POST" style="margin-left :500px ">
        @csrf
            <div class="col-sm-6">
            <div class="panel panel-info"  align="center">
            <input type="hidden" name="ReceiveValue" value="{{$ReceiveValue}}'">
            <input type="hidden" name="SkuCode" value="{{$SkuCode}}">
            <input type="hidden" name="ProviderCode" value="{{$ProviderCode}}">
            <input type="hidden" name="AccountNumber" value="{{$AccountNumber}}">
            <input type="hidden" name="ReceiveValue" value="{{$ReceiveValue}}">
            <input type="hidden" name="ProviderCode" value="{{$ProviderCode}}">
            <input type="hidden" name="ReceiveCurrencyIso" value="{{$ReceiveCurrencyIso}}" class="form-control" placeholder="ReceiveCurrencyIso">
        <input type="hidden" name="Our_SendValue" value="{{$Our_SendValue}}" class="form-control" placeholder="Our_SendValue">
        <input type="hidden" name="Country_Iso" value="{{$Country_Iso}} "class="form-control" placeholder="Country_Iso">
        <input type="hidden" name="SendValue" value="{{$SendValue}} "class="form-control" placeholder="SendValue">
        <input type="hidden" name="SendCurrencyIso" value="{{$SendCurrencyIso}}" class="form-control" placeholder="SendCurrencyIso">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="amount" value="{{$amount}}">
            <input type="hidden" name="method" value="{{$method}}">
            <br>
            <br>
            <div class="panel-body text-center" style="text-align :left">Consumer Number : {{$AccountNumber}}&nbsp;</div>
            
            <div class="panel-body text-center" style="text-align :left">Recharge Amount : {{$amount}} {{$ReceiveCurrencyIso}}&nbsp;</div>
          
            <div class="panel-body text-center" style="text-align :left">Received Amount : {{$ReceivedValue}} AED&nbsp;</div>
            
              <a class="btn btn-primary btn-close" href='/localcustom/0'>Cancel</a>&nbsp; &nbsp;
            <button type="submit" class="btn btn-primary" onclick="doSomething(this.value)" id="product"  value="">confirm</button>
           
            <div class="panel-body text-center">&nbsp
            </div>
            </div>
            </div>
            </div>
      </form>
        
</div>
  </div>
  </div>
</body>
</html>
