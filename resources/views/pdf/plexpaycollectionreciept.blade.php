<!doctype html>
<html lang="en">
<title>{{$recharges['trans_id']}}</title>
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
    @page {
        margin: 0px;
    }
</style>
<style>
    @page {
        size: 320px 500px;
    }
</style>

<style type="text/css">
    * {
        font-family: Verdana, Arial, sans-serif;
        font-size: x-small;
    }

    tfoot tr td {
        font-weight: bold;
        font-size: x-small;
    }

    .gray {
        background-color: lightgray
    }
</style>

<body>
    <div align="center">
        <img src="{{public_path('/storage/logo/plexpay.jpg')}}" alt="logo" width="250" height="70">
    </div>
    <div align="center">
        <img src="{{public_path('/storage/logo/barcode.jpg')}}" alt="logo" width="250" height="70">
    </div>
    <div class="div-1">
        <div align="center">
            {{$recharges['entry_date']}}
        </div>
        <br>
        <table align="center">
            <tr>
                <td>
                    TRANSACTION ID
                </td>
                <td><b>:{{$recharges['trans_id']}}</b>
                <td></td>
            </tr>
            <tr>
                <td>
                    POINT NAME</td>
                <td><b>:{{$recharges['point_name']}}</b>
                </td>
            </tr>
            <tr>
                <td>
                    AGENT NAME</td>
                <td><b>:{{$recharges['agent_name']}}</b>
                </td>
            </tr>
            <tr>
                <td>
                    DUE WALLET</td>
                <td><b>:{{$recharges['due_wallet']}}</b>
                </td>
            </tr>
            <tr>
                <td>
                    COLLECTION AMOUNT</td>
                <td>:<b>{{$recharges['collected_amount']}}</b>
                </td>
            </tr>
            <tr>
                <td>
                    MODE OF PAYMENT </td>
                <td><b>:{{$recharges['mode_of_payment']}}</b>
                </td>
            </tr>
            <tr>
                <td>
                    DUE BALANCE </td>
                <td><b>:{{$recharges['due_balance']}}</b>
                </td>
            </tr>
            <tr>
                <td>
                    DATE AND TIME </td>
                <td><b>:{{$recharges['entry_date']}}</b>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>