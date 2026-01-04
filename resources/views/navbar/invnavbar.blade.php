
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    <style>
        .btnnav {
            border: none;
            margin-top: -20px;
            margin-left:-5px;
            height: 55px;
            z-index: 10000;
            width: 100%;
        }

        .navbar-nav {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-nav a {
            font-weight: bold;
        }



        .navbar-nav > li {
            flex-grow: 1;
            margin: 0 5px;
        }

        .navbar-nav > li > a.btn-custom {
            color: #187f6a;
            background-color: white;
            border: 1px solid #187f6a;
            border-radius: 8px;
            padding: 10px 20px;
            text-align: center;
            margin-top: -10px;
            width: 100%; /* Make the width responsive */
            box-sizing: border-box; /* Include padding and border in the element's total width */
        }

        .navbar-nav > li > a.btn-custom:hover,
        .navbar-nav > li.active > a.btn-custom {
            background-color: #187f6a;
            color: white;

        }




        @media (max-width: 768px) {
            .navbar-collapse {
                display: none;
            }

            .navbar-nav {
                flex-direction: column;
                align-items: stretch;
            }

            .navbar-nav > li {
                flex-grow: 0;
                margin: 5px 0;
            }
        }

        @media (min-width: 769px) and (max-width: 1070px) {
            .navbar-nav {
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .navbar-nav > li > a.btn-custom {
                padding: 10px 2px;
                width: auto; /* Adjust the width for medium-sized screens */
            }
        }

        @media (min-width: 600px) {
            .top-links {
                justify-content: flex-end;
            }

            .top-links a {
                margin-right: 10px;
            }

            .navigation {
                display: none;
            }

            .navigation.show {
                display: flex;
            }

            .grid-container {
                flex-direction: column;
            }
        }
        .btn {
            font-size: 12px;
        }
    </style>


<nav class="navbar navbar-default btnnav">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>

      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav link2">

          @foreach ($users as $user)
            @if ($user->role_id == '2')
              <li class="{{ Request::is('inventorydashboard')||
              Request::is('search/*')||
              Request::is('search')||
              Request::is('editproductdraft/*')||
              Request::is('draft/productdraft')||
              Request::is('toproduct/*')||

              Request::is('productdraft')
               ? 'active' : '' }}"><a href="/inventorydashboard" class="btn btn-custom">Add Product</a></li>
              <li class="{{ Request::is('listcategory') ? 'active' : '' }}"><a href="/listcategory" class="btn btn-custom">Category</a></li>
              <li class="{{ Request::is('liststocks')||Request::is('newstockpurchases')
 ? 'active' : '' }}"><a href="/liststocks" class="btn btn-custom">Stock</a></li>

              <li class="{{ Request::is('purchasestock')||
              Request::is('purchasedraft')||
              Request::is('draft/purchasedraft')||
              Request::is('editpurchasedraft/edit_purchase_draft/*')||


              Request::is('editpurchasedraft/*')
               ? 'active' : '' }}"><a href="/purchasestock" class="btn btn-custom">Purchase</a></li>

              <li class="{{ Request::is('purchasereturn') || Request::is('purchasereturnhistory') || Request::is('returnpurchasedetails/*') ? 'active' : '' }}"><a href="/purchasereturn" class="btn btn-custom">Purchase Return</a></li>


            @endif
          @endforeach

          @foreach ($users as $user)
            @if ($user->role_id == '19')
              <li class="{{ Request::is('purchase_order')||
              Request::is('purchase_order_history/purchase_order')||
              Request::is('purchase_order_details/purchase_order/*')||
              Request::is('purchaseorderreceipt/*')||
              Request::is('to_purchase/purchase_order/*')||

               Request::is('purchaseorderhistorydate/purchase_order') ? 'active' : '' }}"><a href="/purchase_order" class="btn btn-custom">Purchase Order</a></li>
            @endif
          @endforeach





          <li class="{{ Request::is('purchasehistory')||Request::is('purchasehistorydate')||
 Request::is('purchasedetails/*')|| Request::is('edit_purchasedetails/edit_purchase/*') ? 'active' : '' }}"><a href="/purchasehistory" class="btn btn-custom">Purchase History</a></li>
           @foreach ($users as $user)
           @if ($user->role_id == '29')
            <li class="{{ Request::is('debitnote')||Request::is('supplier_summary')||Request::is('debit_voucher/*')||Request::is('debitnote_history')||Request::is('debitnote/view/*') ? 'active' : '' }}">
                <a href="/debitnote" class="btn btn-custom">Debit Note</a></li>
                @endif
                @endforeach
        </ul>
      </div>
    </div>
  </nav>




</body>
</html>
