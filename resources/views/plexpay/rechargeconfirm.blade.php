<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="Plexpay Recharge">
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
  </ul>
</div>
<br>

<br>
<!-- <img src="{{$ProviderLogo}}">
<br>
<p>{{$ProviderName}}</p> -->
<br>

<div class="container-fluid" > 
  
      
      <form action="/plexpayrechargeinternationalnumber" method="POST" style="margin-left :500px ">
        @csrf
            <div class="col-sm-5">
            <div class="panel panel-info"  align="center">
            <input type="hidden" name="Our_SendValue" value="{{$Our_SendValue}}">
            <input type="hidden" name="AccountNumber" value="{{$phonenumber}}">
            <input type="hidden" name="SkuCode" value="{{$SkuCode}}">
            <input type="hidden" name="SendValue" value="{{$SendValue}}">
            <input type="hidden" name="ReceiveValue" value="{{$ReceiveValue}}">
            <input type="hidden" name="Country_Iso" value="{{$Country_Iso}}">
            <input type="hidden" name="ProviderCode" value="{{$ProviderCode}}">
            <input type="hidden" name="CoupenTitle" value="{{$CoupenTitle}}">
            <input type="hidden" name="dash" value="{{$dash}}">
            <input type="hidden" name="method" value="{{$method}}">
            <input type="hidden" name="amount" value="{{$amount}}">
            <br>
            <br>
            
            @if($method==1)
            <div class="panel-body text-center" style="text-align :left">Number : {{$phonenumber}}&nbsp;</div>
            @if($Country_Iso=='AE')
            <div class="panel-body text-center" style="text-align :left">Recharge Amount : {{$ReceiveValue}}&nbsp; {{$SendCurrencyIso}} </div>
            @else
            <div class="panel-body text-center" style="text-align :left">Recharge Amount : {{$Our_SendValue}}&nbsp; {{$SendCurrencyIso}} </div>
            @endif
            @elseif($method==0)
            <div class="panel-body text-center" style="text-align :left">Number : {{$phonenumber}}&nbsp;</div>
              <div class="panel-body text-center" style="text-align :left">Recharge Amount :{{$amount}} &nbsp; AED</div>
              
              @elseif($method==2)
              
              <div class="panel-body text-center" style="text-align :left">Card Name : {{$ProviderName}}&nbsp;</div>
              @if($Country_Iso=='AE')
            <div class="panel-body text-center" style="text-align :left">Recharge Amount : {{$ReceiveValue}}&nbsp; {{$SendCurrencyIso}} </div>
            @else
            <div class="panel-body text-center" style="text-align :left">Recharge Amount : {{$Our_SendValue}}&nbsp; {{$SendCurrencyIso}} </div>
            @endif
            
            @endif
            @if($Expiry_Date=="")
           
            @if($method==2)
            <div class="panel-body text-center" style="text-align :left">Received value : {{$CoupenTitle}}  &nbsp;</div>
            @endif
            @else
            @if($method=="0")
            <div class="panel-body text-center" style="text-align :left">Received value : {{$amount}} AED &nbsp;</div>
            @else
            <div class="panel-body text-center" style="text-align :left">Received vaule  : {{$CoupenTitle}}/{{$Expiry_Date}}&nbsp;</div>
            @endif 
            @endif
            <div class="row">
            @if($method==1)
            @if($dash==0)
            <a class="btn btn-primary btn-close" href='/plexpaybalance'>Cancel</a>&nbsp; &nbsp;
            
            @else
            <a class="btn btn-primary btn-close" href='/plexpayinternational'>Cancel</a>&nbsp; &nbsp;
            @endif
            @elseif($method==0)
              <a class="btn btn-primary btn-close" href='/localcustom/0'>Cancel</a>&nbsp; &nbsp;
              @else
              <a class="btn btn-primary btn-close" href='/plexpaybalance'>Cancel</a>&nbsp; &nbsp;
              @endif
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
