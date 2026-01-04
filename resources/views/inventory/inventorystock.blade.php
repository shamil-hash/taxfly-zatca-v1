<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Stock</title>
    @include('layouts/usersidebar')
    <style>
        #content {
            padding: 30px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2
        }

        th {
            background-color: #187f6a;
            color: white;
        }

        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 1px solid #010101;
        }

        .select2-container .select2-choice {
            height: 35px;
            line-height: 35px;
        }

        ul.select2-results {
            max-height: 100px;
        }
    </style>

</head>

@php
    use App\Models\Softwareuser;
    use Illuminate\Support\Facades\DB;

    $userid = Session('softwareuser');
    $adminid = Softwareuser::Where('id', $userid)
        ->pluck('admin_id')
        ->first();
    $adminroles = DB::table('adminusers')
    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
    ->where('user_id', $adminid)
    ->get();
@endphp

<body>
    <!-- Page Content Holder -->
    <div id="content">
        @if ($adminroles->contains('module_id', '30'))
        @include('navbar.invnavbar')
        @else
        <x-logout_nav_user />
        @endif
        <div align="center">
            @foreach($shopdatas as $shopdata)
            {{$shopdata['name']}}
            <br>
            Phone No:{{$shopdata['phone']}}
            <br>
            Email:{{$shopdata['email']}}
            <br>
            <br>
            @endforeach
        </div>
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#">Add Stocks </a></li>
            <li role="presentation"><a href="/newstockpurchases">New Purchases <span class="badge">{{$count}}</span></a></li>
            <li role="presentation"><a href="/liststocks">List Stocks </a></li>
        </ul>
        <br>
        <form method="post" action="submitstock">
            @csrf
            <div class="form group row">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Add Stock</th>
                            <th>Remaining Stock</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr bgcolor="#187f6a">
                            <td width="30%">
                                <select onclick="doSomething(this.value)" id="product" class="js-user" style="width: 100%">
                                    <option selected value="">Select</option>
                                    @foreach($stocks as $stock)
                                    <option value="{{$stock->id}}">{{$stock->product_name}}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="productname" class="form-control">
                                <input type="hidden" name="uid" id="uid" value="{{$userid}}">
                            </td>
                            <td width="20%"><input type="text" id="stock" class="form-control"></td>
                            <td width="20%"><input type="text" id="remain" class="form-control"></td>
                            <td width="20%"><input type="text" id="total" class="form-control"></td>
                            <td width="10%"><a href="#" class="btn btn-info addRow">+</a></td>
                            <input type="hidden" id="id" class="form-control">
                        </tr>
                        <tr>
                            <td colspan="5"> <i class="glyphicon glyphicon-tags"></i> &nbsp ADDED
                            </td>
                        <tr>
                    </tbody>
                </table>
            </div>
            <input class="btn btn-primary" type="submit" value="submit">
        </form>
    </div>

</body>

</html>
<script type="text/javascript">
    $('.addRow').on('click', function() {
        addRow();
    });

    function addRow() {
        var y = ($("#productname").val());
        if (($("#product").val()) == "") {
            return;
        }
        var x = Number($("#stock").val());
        if (($("#stock").val()) == "") {
            return;
        }

        var w = Number($("#remain").val());

        var z = Number($("#total").val());

        var t = Number($("#id").val());
        var tr = '<tr>' + '<td>' +
            '<input type="text" id="product" value="' + y + '" name="productName[]" class="form-control" readonly>' +
            '</td>' +
            '<td><input type="text" value=' + x + ' name="stock[]" class="form-control" readonly></td>' +
            '<td><input type="text" value=' + w + ' name="remain[]" class="form-control" readonly></td>' +
            '<td><input type="text" value=' + z + ' name="total[]" class="form-control" readonly></td>' +
            '<td><a href="#" class="btn btn-danger remove">-</a></td>' +
            '<input type="hidden" value=' + t + ' name="id[]" class="form-control" >' +
            '</tr>';
        $('tbody').append(tr);
        var nu = "";
        $("#stock").val(nu);
        $("#remain").val(nu);
        $("#total").val(nu);
        $("#product").val(null).trigger('change');
    };
    $('tbody').on('click', '.remove', function() {
        $(this).parent().parent().remove();
    });
</script>
<script>
    $(document).ready(function() {
        $("#remain,#stock").keyup(function() {
            var total = 0;
            var x = Number($("#stock").val());
            var z = Number($("#remain").val());
            total = x + z;
            var total = Math.round(total * 100) / 100;
            $("#total").val(total);
        });

    });
</script>
<script>
    function doSomething(x) {
        var array = @json($stocks);

        function isSeries(elm) {
            return elm.id == x;
        }
        var k = array.find(isSeries).stock;
        var l = array.find(isSeries).stock_num;
        var productname = array.find(isSeries).product_name;
        var remaining_stock = Number(array.find(isSeries).remaining_stock);
        $('#remain').val(remaining_stock);
        // $('#remain').val(k-l);
        var h = array.find(isSeries).id;
        $('#id').val(h);
        $('#productname').val(productname);
    }
</script>
<script>
    $(document).ready(function() {
        $('.js-user').select2({
            theme: "classic"
        });
    });
    $('form input:not([type="submit"])').keydown((e) => {
        if (e.keyCode === 13) {
            e.preventDefault();
            return false;
        }
        return true;
    });
</script>
<script>
    var product_name_name = document.getElementById("productname");
    var stock = document.getElementById("stock");
    var remain = document.getElementById("remain");
    var total = document.getElementById("total");

    product_name_name.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    stock.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    remain.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
    total.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.addRow').click();
        }
    });
</script>
