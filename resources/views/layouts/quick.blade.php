<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Quick Create</title>

    <style>
        .dropdown {
            position: relative;
            display: inline-block;
            z-index: 1000;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 170px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 5;
            margin-left: -120px;
            font-family: sans-serif;
        }

        .dropdown-content a {
            color: black;
            padding: 6px 14px;
            text-decoration: none;
            display: block;
            text-align: start;
            font-size: 15px;
            height: 40px;
            font-weight: 500;
        }

        .dropdown-content a:hover {
            background-color: #187f6a;
            color: white;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a i {
            color: #187f6a;
        }

        .dropdown-content a:hover .custom-icon {
            color: white !important;
        }
    </style>
</head>

<body>
    <div class="dropdown">
        <a href="#" style="background-color:#187f6a;" class="btn btn-info">
            <i  class="glyphicon glyphicon-plus-sign"></i>
        </a>

                <div class="dropdown-content">
            @foreach ($users as $user)
                @if ($user->role_id == '26')
                    <a href="/createcredit"><i class="glyphicon glyphicon-plus-sign custom-icon"></i>&nbsp;Customer</a>
                @endif
            @endforeach
            <a href="/dashboard"><i class="glyphicon glyphicon-plus-sign custom-icon"></i>&nbsp;Billing</a>
            <a href="/inventorydashboard"><i class="glyphicon glyphicon-plus-sign custom-icon"></i>&nbsp;Product</a>
            <a href="/purchasestock"><i class="glyphicon glyphicon-plus-sign custom-icon"></i>&nbsp;Purchase</a>
            <a href="/return"><i class="glyphicon glyphicon-plus-sign custom-icon"></i>&nbsp;Return</a>
            <a href="/listcategory"><i class="glyphicon glyphicon-plus-sign custom-icon"></i>&nbsp;Category</a>
            <a href="/listcategory"><i class="glyphicon glyphicon-plus-sign custom-icon"></i>&nbsp;Unit</a>
            @foreach ($users as $user)
                @if ($user->role_id == '18')
                    <a href="/delivery_note"><i class="glyphicon glyphicon-plus-sign custom-icon"></i>&nbsp;Delivery
                        Note</a>
                @endif
            @endforeach
            @foreach ($users as $user)
                @if ($user->role_id == '21')
                    <a href="/performance_invoice"><i
                            class="glyphicon glyphicon-plus-sign custom-icon"></i>&nbsp;Proforma
                        Invoice</a>
                @endif
            @endforeach
            @foreach ($users as $user)
                @if ($user->role_id == '20')
                    <a href="/quotation"><i class="glyphicon glyphicon-plus-sign custom-icon"></i>&nbsp;Quotation</a>
                @endif
            @endforeach
            @foreach ($users as $user)
                @if ($user->role_id == '17')
                    <a href="/sales_order"><i class="glyphicon glyphicon-plus-sign custom-icon"></i>&nbsp;Sales
                        Order</a>
                @endif
            @endforeach
            @foreach ($users as $user)
                @if ($user->role_id == '19')
                    <a href="/purchase_order"><i class="glyphicon glyphicon-plus-sign custom-icon"></i>&nbsp;Purchase
                        Order</a>
                @endif
            @endforeach
        </div>
    </div>
</body>

</html>
