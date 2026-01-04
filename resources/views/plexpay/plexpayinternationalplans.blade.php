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
    <li class="breadcrumb-item active" aria-current="page">International Recharge</li>
  </ol>
</nav>

<div class="nav-dat" align="right">
Balance 00 &nbsp; Due 00
</div>
<br>


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
    <li><a href="/plexpayregister">Plexpay Register</a></li>
    <li><a href="/plexpaypasswordchange">Change Password</a></li>
  </ul>
</div>
<br>
<h2>Select Plans</h2>
<br>
<ul class="nav nav-tabs nav-justified"> <li role="presentation" class="active"><a href="/plexpayinternationalrecharge/1/1">OFFER</a></li> <li role="presentation"><a href="/plexpayinternationalcustom/1/1">CUSTOM</a></li></ul>
<br>
<br>
<input type="text" class="form-control" placeholder="Country Code + Phone Number" style="width: 250px;">
<input type="hidden" value="{{$countrycode}}">
<br>
<div class="container-fluid">
    <div class="row">
            <div class="col-sm-3">
            <div class="panel panel-info">
            <div class="panel-heading text-center">Product Name
            </div>
            <div class="panel-body text-center">COST
            </div>
            <div align="center">
            <button type="button" class="btn btn-primary" onclick="doSomething(this.value)" id="product"  value="">buy</button>
            <div class="panel-body text-center">&nbsp
            </div>
            </div>
            </div>
            </div>

            <div class="col-sm-3">
            <div class="panel panel-info">
            <div class="panel-heading text-center">Product Name
            </div>
            <div class="panel-body text-center">COST
            </div>
            <div align="center">
            <button type="button" class="btn btn-primary" onclick="doSomething(this.value)" id="product"  value="">buy</button>
            <div class="panel-body text-center">&nbsp
            </div>
            </div>
            </div>
            </div>

            <div class="col-sm-3">
            <div class="panel panel-info">
            <div class="panel-heading text-center">Product Name
            </div>
            <div class="panel-body text-center">COST
            </div>
            <div align="center">
            <button type="button" class="btn btn-primary" onclick="doSomething(this.value)" id="product"  value="">buy</button>
            <div class="panel-body text-center">&nbsp
            </div>
            </div>
            </div>
            </div>
            

            <div class="col-sm-3">
            <div class="panel panel-info">
            <div class="panel-heading text-center">Product Name
            </div>
            <div class="panel-body text-center">COST
            </div>
            <div align="center">
            <button type="button" class="btn btn-primary" onclick="doSomething(this.value)" id="product"  value="">buy</button>
            <div class="panel-body text-center">&nbsp
            </div>
            </div>
            </div>
            </div>
    </div>      
</div>
  </div>
  </div>
</body>
</html>
