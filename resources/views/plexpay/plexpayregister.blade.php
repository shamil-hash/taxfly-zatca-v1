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
    <li class="breadcrumb-item active" aria-current="page">Register</li>
  </ol>
</nav>
<br>

<div align="right">
<a href="/plexpayunregister" class="btn btn-primary">UNREGISTER</a>
</div>
@if($register=='Registered')
<br>
                <div align="right">
                <a href="/plexpaybalance" class="btn btn-primary">PLEXPAY Balance</a>
                </div>
@endif

@if(Session::has('success'))
<br>
                <div class="alert alert-success">
                    {{Session::get('success')}}
                </div>
                <br>
@endif



<form action="/plexpayuserregisterpost" method="POST">
  @csrf
  {{$register}}
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-4">
        </div>
            <div class="col-sm-4">
        <div class="input-group">
        <input type="text" name="user_id" value="{{$user_id}}" class="form-control" placeholder="Userid">
        <span class="input-group-addon" style="padding: 5px 12px;" id="basic-addon2"><span class="glyphicon glyphicon-user"></span></span>
        </div>
        <br>
        <div class="input-group">
        <input type="text" name="password" class="form-control" placeholder="Password">
        <span class="input-group-addon" style="padding: 5px 12px;" id="basic-addon2">*****</span>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Register</button>
        <br>
        </div>
    </div>   
</div>
</form>
  </div>
  </div>
</body>
</html>
