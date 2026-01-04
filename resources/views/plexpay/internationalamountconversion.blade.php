<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
         <meta name="description" content="Plexpay International Recharge">
        <title>Plexpay Recharge</title>
        @include('layouts/usersidebar')
        <link rel="stylesheet" href=
"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src=
"https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js">
    </script>
    <script src=
"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js">
    </script>
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
    <li class="breadcrumb-item active" aria-current="page">International Custom Recharge</li>
  </ol>
</nav>

<div class="nav-dat" align="right">
Balance 00 &nbsp; Due 00
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
<li role="presentation" class="active"><a href="/plexpaybalance">Balance</a></li>
<li role="presentation"><a href="/plexpayrecharge">Recharge</a></li>
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
  </ul>
</div>
<br>
<h2>Custom Recharge</h2>
<br>
<br>
<br>
<div class="container-fluid">
    <div class="row">
<form action="/plexpayinternationalcustom/0/1" method="GET">
  @csrf
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-4">
        </div>
            <div class="col-sm-4">
        <div class="input-group">
        <input type="text" name="AccountNumber" class="form-control" placeholder="Phone Number" value="{{$phonenumber}}"> 
        <span class="input-group-addon" style="padding: 5px 12px;" id="basic-addon2"><span class="glyphicon glyphicon-phone"></span></span>
        </div>
        <br><input type="hidden" name="minValue" value="{{$minValue}}"><input type="hidden" name="maxValue" value="{{$maxValue}}">
        between AED {{$minValue}} - AED {{$maxValue}}
        <br>
        <div class="input-group">
          
        @if(!empty($Amount))
        <input type="text" name="Amount" class="form-control" placeholder="Recharge" style="width: 250px;" value={{$Amount}}>
        @else
        <input type="text" name="Amount" class="form-control" placeholder="Recharge" style="width: 250px;" >
        @endif
        <button class="btn btn-primary" type="submit"><span class="glyphicon 
                glyphicon-search"></span></button>
        </div>
    </div>   
</div>
</form>
@if(!empty($DenominationInfo))
<form action="/localcustomrechargepost" method="POST">
  @csrf
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-4">
        </div>
            <div class="col-sm-4">
        <input type="hidden" name="AccountNumber" class="form-control" placeholder="Phone Number">      
        <input type="hidden" name="Amount" class="form-control" placeholder="Recharge" style="width: 250px;">
        <div class="input-group">
        <input type="text" name="Amount" class="form-control" placeholder="Recharge" style="width: 250px;" value=" Received amount is {{$DenominationInfo['ReceivableAmount']}}">
        <button type="submit" class="btn btn-primary">CONFIRM</button></div>
        </div>
    </div>   
</div>
</form>
@endif
  </div>
  </div>
  </div>
  </div>
</body>
</html>
