<!doctype html>
<html lang="en">
<title>{{$recharges['TransactionID']}}</title>
<style>
    .div-2 {
    	background-color: #20639B;
        padding-top: 50px;
        padding-right: 75px;
        padding-bottom: 50px;
        padding-left: 75px;
        height: 340px;
    }
    .div-1 {
    	background-color: white;
        padding-top: 15px;
        padding-right: 5px;
        padding-bottom: 15px;
        padding-left: 5px;

        border-bottom-left-radius: 5%;
        border-bottom-right-radius: 5%;
        border-top-left-radius: 5%;
        border-top-right-radius: 5%;
    }
</style>
<style>
@page 
{ margin:0px; }
</style>
<style>
   @page { size: 320px 900px; }
  </style>

<style type="text/css">
    * {
        font-family: Verdana, Arial, sans-serif;
        font-size: x-small;
    }
    tfoot tr td{
        font-weight: bold;
        font-size: x-small;
    }
    .gray {
        background-color: lightgray
    }
</style>
<body>
<div align="center">
    <img src="{{public_path('/images/logoimage/plexpay.jpg')}}" alt="logo" width="250" height="70">
    </div>
    <div align="center">
    <img src="{{public_path('/images/logoimage/barcode.jpg')}}" alt="logo" width="250" height="70">
    </div>
    <div class="div-1">
        <div align="center">
{{$recharges['date_time']}}
</div>
<br>
<table align="center">
    <tr>
    <td>
    SHOP NAME
    </td>
    <td>
    :<b>{{$recharges['shop_name']}}</b>
    </td>
    </tr>
    <tr>
    <td>
    TRANSACTION ID
    </td>
<td><b>:{{$recharges['TransactionID']}}</b><td></td>
    </tr>
    <tr>
    <td>
OPERATOR</td><td><b>:{{$recharges['provider_name']}}</b>
</td>
    </tr>
    <tr>
    <td>
VOUCHERCODE</td>  <td>:<b>{{$recharges['voucher_code']}}</b>
</td>
    </tr>
    <tr>
    <td>
TYPE  </td>  <td><b>:{{$recharges['recharge_type']}}</b>
</td>
    </tr>
    <tr>
    <td>
AMOUNT  </td>  <td><b>:{{$recharges['recharge_amount']}}</b>
</td>
    </tr>
    <tr>
    <td>
RECEIVED VALUE  </td>  <td><b>:{{$recharges['coupen_title']}}</b>
</td>
    </tr>
    <tr>
    <td>
Instruction </td>  <td><b> :{{$recharges['instruction']}}</b>
</td>
    </tr>
    <tr>
    <td>
COUNTRY  </td>  <td><b>:{{$recharges['CountryName']}}</b>
</td>
    </tr>
    <tr>
    <td>
STATUS  </td>  <td><b>:SUCCESS</b>
</td>
    </tr>
</table>
<div>
    <p style="text-align : center">WhatsApp Support : {{$contact['mobile']}}</p>
    <p style="text-align : center">Thanks for using PLEXPAY</p>
    </div>
</div>

</body>
</html>