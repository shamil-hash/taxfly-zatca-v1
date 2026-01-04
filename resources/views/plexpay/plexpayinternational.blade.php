<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="Plexpay International Recharge">
        <link rel="stylesheet" href="build/css/intlTelInput.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
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
    <li class="breadcrumb-item active" aria-current="page">International Recharge Countries</li>
  </ol>
</nav>


<div class="nav-dat" align="right">
Balance {{$wallet_amount}} &nbsp; Due {{$due_amount}}
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
    <li role="presentation"><a href="/plexpayreports">Reports</a></li>
    <li><a href="/plexpayregister">Plexpay Register</a></li>
    <li><a href="/plexpaypasswordchange">Change Password</a></li>
  </ul>
</div>
<br>
<ul class="nav nav-tabs nav-justified"> <li role="presentation"><a href="/plexpaybalance">LOCAL</a></li> <li role="presentation" class="active"><a href="/plexpayinternational">INTERNATIONAL</a></li></ul>
<br><br><br><br>

  <form action="plexpayrechargesearch" method="GET"> 
    @csrf
    <input type="hidden" id="dash" name="dash" type="hidden" value="1"  >
    <input type="hidden" id="method" name="method" type="hidden" value="1"  >
    <input type="hidden" id="amount" name="amount" type="hidden" value="0"   >
    <input id="phone" name="phone" type="tel" value="+"  style="height:56px; width:600px" class="form-control">
    <button  class="btn btn-primary"type="submit" style="height:56px; width:90px">Submit</button>
  </form>

  <script src="build/js/intlTelInput.js"></script>
  <script>
    // Vanilla Javascript
    var input = document.querySelector("#phone");
    window.intlTelInput(input,({
      // options here
    }));

    $(document).ready(function() {
        $('.iti__flag-container').click(function() { 
          var countryCode = $('.iti__selected-flag').attr('title');
          var countryCode = countryCode.replace(/[^0-9]/g,'')
          $('#phone').val("");
          $('#phone').val("+"+countryCode+" "+ $('#phone').val());
        });
    });
  </script>
  <br><br>
<h2>INTERNATIONAL</h2>
<br>
    <div class="row">
      <div class="col-md-1">
      </div>
    @foreach($countires as $country)
      <div class="col-md-5">
        <div class="row">
          <div class="col-sm-4">
            <div class="thumbnail">
              <div class="caption text-center" onclick="location.href='/internationalbycountry/{{$country['CountryIso']}}'">
                <h4 id="thumbnail-label"><a href="" target="_blank">{{$country['CountryName']}}</a></h4>
                </div>
                <div align="center" onclick="location.href='/internationalbycountry/{{$country['CountryIso']}}'">
                  <img src="{{$country['Cflag']}}" style="width:72px;height:72px;" alt="Country Flags" />
                </div>
              <div class="caption card-footer text-center" onclick="location.href='/internationalbycountry/{{$country['CountryIso']}}'">
                <ul class="list-inline">
                  <li></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-1">
      </div>
    @endforeach
    </div>
  </div>
  </div>
</body>
</html>
