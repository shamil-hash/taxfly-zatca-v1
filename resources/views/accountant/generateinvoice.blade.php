<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Generate Invoice</title>
    @include('layouts/usersidebar')
    <style>
        .dot {
            height: 15px;
            width: 15px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
        }
    </style>
    <style>
        .gdot {
            height: 15px;
            width: 15px;
            background-color: green;
            border-radius: 50%;
            display: inline-block;
        }
    </style>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #555;
            text-align: left;
            padding: 8px;
        }

        th {
            color: #555;
        }

        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 1px solid #010101;
        }

        form {
            margin-top: 70px;
            margin-right: 70px;
            margin-left: 70px;
            background-color: #ebe9e9;
            padding: 30px;
            border-color: #ddd;
            border-width: 1px;
            border-radius: 4px 4px 0 0;
            -webkit-box-shadow: none;
            box-shadow: none;
        }

        .select2-container .select2-choice {
            height: 35px;
            line-height: 35px;
        }

        ul.select2-results {
            max-height: 100px;
        }

        th {
            background-color: #555;
            color: white;
        }
    </style>
    <style>
        @media (min-width: 768px) {
            .col-sm-12 {
                width: 100%;
                padding-bottom: 12px;
            }
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
        @include('navbar.expnavbar')
        @else
        <x-logout_nav_user />
        @endif
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Accountant</a></li>
                <li class="breadcrumb-item active" aria-current="page">Generate Invoice</li>
            </ol>
        </nav>
        @if(Session::has('success'))
        <div class="alert alert-success">
            {{Session::get('success')}}
        </div>
        @endif
        <form action="generateinvoiceform" method="POST" id="generateinvoiceform" onsubmit="return doSomething2()">
            @csrf
            <h2>Generate Invoice</h2>
            <div class="row">
                <div class="col-sm-4">
                    <div class="radio">
                        <label><input type="radio" value="1" name="invoicetype">LPO</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="radio">
                        <label><input type="radio" value="2" name="invoicetype" checked>Invoice</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="radio">
                        <label><input type="radio" value="3" name="invoicetype">Quotation</label>
                    </div>
                </div>
                <span style="color:red">@error('invoicetype'){{$message}}@enderror</span>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <h4>Company</h4>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">Name</span>
                        <input type="text" id="from_name" name="from_name" class="form-control" placeholder="Name" aria-describedby="basic-addon1" value="{{$shopname}}">
                    </div>
                    <span style="color:red">@error('from_name'){{$message}}@enderror</span>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon2">Mobile Number</span>
                        <input type="text" id="from_number" name="from_number" class="form-control" placeholder="Mobile Number" aria-describedby="basic-addon2" value="{{$shopnumber}}">
                    </div>
                    <span style="color:red">@error('from_number'){{$message}}@enderror</span>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon5">TRN Number</span>
                        <input type="text" id="from_trnnumber" name="from_trnnumber" class="form-control" placeholder="TRN Number" aria-describedby="basic-addon5">
                    </div>
                    <span style="color:red">@error('from_trnnumber'){{$message}}@enderror</span>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon5">Email</span>
                        <input type="text" id="from_email" name="from_email" class="form-control" placeholder="Email" aria-describedby="basic-addon5">
                    </div>
                    <span style="color:red">@error('from_email'){{$message}}@enderror</span>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon5">Address</span>
                        <input type="text" id="from_address" name="from_address" class="form-control" placeholder="Address" aria-describedby="basic-addon5">
                    </div>
                    <span style="color:red">@error('from_address'){{$message}}@enderror</span>
                    <br>
                </div>
                <div class="col-sm-6">
                    <h4>Client</h4>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">Name</span>
                        <input type="text" id="to_name" name="to_name" class="form-control" placeholder="Name" aria-describedby="basic-addon1">
                    </div>
                    <span style="color:red">@error('to_name'){{$message}}@enderror</span>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon2">Mobile Number</span>
                        <input type="text" id="to_number" name="to_number" class="form-control" placeholder="Mobile Number" aria-describedby="basic-addon2">
                    </div>
                    <span style="color:red">@error('to_number'){{$message}}@enderror</span>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon5">TRN Number</span>
                        <input type="text" id="to_trnnumber" name="to_trnnumber" class="form-control" placeholder="TRN Number" aria-describedby="basic-addon5">
                    </div>
                    <span style="color:red">@error('to_trnnumber'){{$message}}@enderror</span>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon5">Email</span>
                        <input type="text" id="to_email" name="to_email" class="form-control" placeholder="Email" aria-describedby="basic-addon5">
                    </div>
                    <span style="color:red">@error('to_email'){{$message}}@enderror</span>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon5">Address</span>
                        <input type="text" id="to_address" name="to_address" class="form-control" placeholder="Address" aria-describedby="basic-addon5">
                    </div>
                    <span style="color:red">@error('to_address'){{$message}}@enderror</span>
                    <br>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon5">Payment Type</span>
                        <select class="form-control" id="payment_type" name="payment_type">
                            <option value="1">CASH</option>
                            <option value="2">BANK</option>
                            @foreach($users as $user)
                            <?php if ($user->role_id == '11') { ?>
                                <option value="3">CREDIT</option>
                            <?php } ?>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <br>
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon5">Due Date</span>
                <input type="date" id="due_date" name="due_date" class="form-control" aria-describedby="basic-addon5">
            </div>
            <br>
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Heading</span>
                <input type="text" id="heading" name="heading" class="form-control" placeholder="Heading" aria-describedby="basic-addon1">
            </div>
            <span style="color:red">@error('heading'){{$message}}@enderror</span>
            <br>
            <div align="right">
                <a class="btn btn-default addemptyRow">Empty Row</a>
            </div>
            <br>
            <table class="table" id="table">
                <thead>
                    <tr>
                        <th width="20%">Product</th>
                        <th width="20%">Quantity</th>
                        <th width="20%">MRP</th>
                        <th width="20%">{{$tax}}(%)</th>
                        <th width="20%">AMOUNT</th>
                        <th width="5%">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select onclick="doSomething(this.value)" id="product" class="js-user" style="width: 100%">
                                <option selected value="">Select</option>
                                @foreach($products as $product)
                                <option value="{{$product['id']}}">{{$product['product_name']}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" id="qty" class="form-control"></td>
                        <td><input type="text" id="mrp" class="form-control"></td>
                        <td><input type="text" id="fixed_vat" class="form-control"></td>
                        <td><input type="text" id="price" class="form-control" readonly></td>
                        <td><a class="btn btn-secondary addRow">+</a></td>
                        <input type="hidden" id="product_id" class="form-control">
                        <input type="hidden" id="product_name" class="form-control">
                    </tr>
                    <tr>
                        <td colspan="6"> <i class="glyphicon glyphicon-tags"></i> &nbsp PRODUCTS
                        </td>
                    <tr>
                </tbody>
            </table>
            <br>
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Footer</span>
                <input type="text" id="footer" name="footer" class="form-control" placeholder="Footer" aria-describedby="basic-addon1">
            </div>
            <span style="color:red">@error('footer'){{$message}}@enderror</span>
            <br>
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Terms and Conditions</span>
                <input type="text" id="termsandcondition" class="form-control" placeholder="Terms and Conditions" aria-describedby="basic-addon1">
                <span class="input-group-addon" id="basic-addon1"> <a class="btn btn-secondary addterms" style="display: contents;">+</a></span>
            </div>
            <span style="color:red">@error('footer'){{$message}}@enderror</span>
            <br>
            <div class="row" id="listterms">
            </div>
            <div ALIGN="RIGHT">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</body>

</html>
<script>
    $(document).ready(function() {
        $('.js-user').select2({
            theme: "classic"
        });
    });
</script>
<script type="text/javascript">
    $('.addRow').on('click', function() {
        addRow();
    });

    function addRow() {
        var y = ($("#product_name").val());
        if (($("#product_name").val()) == "") {
            return;
        }
        var w = Number($("#mrp").val());
        var z = Number($("#price").val());
        var q = Number($("#fixed_vat").val());
        var x = Number($("#qty").val());
        if (($("#qty").val()) == "") {
            return;
        }
        var u = Number($("#product_id").val());
        var tr = '<tr>' + '<td>' +
            '<input type="text" id="productname" value="' + y + '" name="productName[]" class="form-control" readonly>' +
            '</td>' +
            '<td><input type="text" value=' + x + ' name="quantity[]" class="form-control" readonly></td>' +
            '<td><input type="text" value=' + w + ' name="mrp[]" class="form-control" readonly></td>' +
            '<td><input type="text" value=' + q + ' name="fixed_vat[]" class="form-control" readonly></td>' +
            '<td><input type="text" value=' + z + ' name="price[]" class="form-control" readonly></td>' +
            '<td><a class="btn btn-danger remove">-</a></td>' +
            '<input type="hidden" value=' + u + ' name="product_id[]" class="form-control" >' +
            '</tr>';
        $('tbody').append(tr);
        var nu = "";
        $("#qty").val(nu);
        $("#price").val(nu);
        $("#mrp").val(nu);
        $("#product_name").val(nu);
        $("#fixed_vat").val(nu);
        $("#product").val(null).trigger('change');
    };
    $('tbody').on('click', '.remove', function() {
        $(this).parent().parent().remove();
    });
</script>
<script>
    $(document).ready(function() {
        $("#mrp,#qty").keyup(function() {
            var total = 0;
            var x = Number($("#qty").val());
            var z = Number($("#mrp").val());
            total = x * z;
            var total = Math.round(total * 100) / 100;
            $("#price").val(total);
        });
    });
</script>
<script>
    function doSomething(x) {
        var array = @json($products);

        function isSeries(elm) {
            return elm.id == x;
        }
        var k = array.find(isSeries).selling_cost;
        $('#mrp').val(k);
        var y = array.find(isSeries).id;
        $('#product_id').val(y);
        var t = array.find(isSeries).vat;
        $('#fixed_vat').val(t);
        var w = array.find(isSeries).product_name;
        $('#product_name').val(w);
        var nu = "";
        $("#qty").val(nu);
        $("#price").val(nu);
    }
</script>
<script type="text/javascript">
    $('.addemptyRow').on('click', function() {
        addemptyRow();
    });

    function addemptyRow() {
        var tr = '<tr class="form-control-calc">' + '<td>' +
            '<input type="text" id="productname" name="productName[]" class="form-control">' +
            '</td>' +
            '<td><input type="text" id="q_u_a_n_t_i_t_y" name="quantity[]" class="form-control quantity" ></td>' +
            '<td><input type="text" id="m_r_p" name="mrp[]" class="form-control mrp"></td>' +
            '<td><input type="text" id="f_i_x_e_d_v_a_t" name="fixed_vat[]" class="form-control"></td>' +
            '<td><input type="text" id="p_r_i_c_e" name="price[]" class="form-control price" ></td>' +
            '<td><a class="btn btn-danger remove">-</a></td>' +
            '<input type="hidden" name="product_id[]" class="form-control" >' +
            '</tr>';
        $('tbody').append(tr);
        $('#table').on("keyup", ".form-control-calc input", function() {
            // for each row:
            console.log($(this).closest('table').find("tr.form-control-calc").length);
            $(this).closest('tr.form-control-calc').each(function() {
                // get the values from this row:
                var $mrp = $('.form-control.mrp', this).val();
                var $quantity = $('.form-control.quantity', this).val();
                console.log($mrp);
                console.log($quantity);
                var $total = $mrp * $quantity;
                console.log($total);
                var $price = Number($total).toFixed(2);;
                console.log($price);
                // set total for the row
                $('.form-control.price', this).val($price);
            });
        });
    };
    $('tbody').on('click', '.remove', function() {
        $(this).parent().parent().remove();
    });
</script>
<script>
    function doSomething2() {
        var from_name = document.getElementById("from_name").value.trim().toUpperCase();
        var from_trnnumber = document.getElementById("from_trnnumber").value.trim().toUpperCase();
        var from_number = document.getElementById("from_number").value.trim().toUpperCase();
        var from_address = document.getElementById("from_address").value.trim().toUpperCase();
        var to_name = document.getElementById("to_name").value.trim().toUpperCase();
        var to_trnnumber = document.getElementById("to_trnnumber").value.trim().toUpperCase();
        var to_number = document.getElementById("to_number").value.trim().toUpperCase();
        var to_address = document.getElementById("to_address").value.trim().toUpperCase();
        var heading = document.getElementById("heading").value.trim().toUpperCase();
        if (from_name === '' || from_name === null) {
            alert("Enter From Name");
            return false;
        } else if (from_number === '' || from_number === null) {
            alert("Enter From Number");
            return false;
        } else if (from_trnnumber === '' || from_trnnumber === null) {
            alert("Enter From TRN Number");
            return false;
        } else if (from_address === '' || from_address === null) {
            alert("Enter From Address");
            return false;
        } else if (to_name === '' || to_name === null) {
            alert("Enter To Name");
            return false;
        } else if (to_number === '' || to_number === null) {
            alert("Enter To Number");
            return false;
        } else if (to_trnnumber === '' || to_trnnumber === null) {
            alert("Enter To TRN Number");
            return false;
        } else if (to_address === '' || to_address === null) {
            alert("Enter To Address");
            return false;
        } else if (heading === '' || heading === null) {
            alert("Enter the Heading");
            return false;
        } else if (!$("#productname").length) {
            alert('Add Products');
            return false;
        }
        return true;
    }
</script>
<script src="/javascript/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // validate the comment form when it is submitted
        $("#generateinvoiceform").validate({
            onclick: false, // <-- add this option
            onfocusout: false,
            ignore: [],
            rules: {
                "productName[]": "required",
                "mrp[]": "required",
                "fixed_vat[]": "required",
                "quantity[]": "required",
                "price[]": "required",
            },
            messages: {
                "productName[]": {
                    required: "The Product Name is required!"
                },
                "mrp[]": {
                    required: "The mrp is required!"
                },
                "fixed_vat[]": {
                    required: "The fixed_{{$tax}} is required!"
                },
                "quantity[]": {
                    required: "The quantity is required!"
                },
                "price[]": {
                    required: "The price is required!"
                }
            },
            errorPlacement: function(error, element) {
                alert(error.text());
            }
        });
    });
</script>
<script type="text/javascript">
    $('.addterms').on('click', function() {
        addterms();
    });
    $part = "";
    $number = 1;

    function addterms() {
        var y = ($("#termsandcondition").val());
        if (($("#termsandcondition").val()) == "") {
            return;
        }
        $part = $part + '<div class="col-sm-12">\
                    <div class="input-group">\
                    <span class="input-group-addon" id="basic-addon1">' + $number + '</span>\
                    <input type="text" id="termsandcondition" name="termsandcondition[]" value="' + y + '" class="form-control" placeholder="Terms and Conditions" aria-describedby="basic-addon1">\
                    </div>\
                    </div>\
                    <br>'
        $("#listterms").html($part);
        var nu = "";
        $("#termsandcondition").val(nu);
        $number++;
    };
</script>
