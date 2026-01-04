
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

<nav class="navbar navbar-default btnnav" >
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
          <li class="{{
           Request::is('dashboard')
           ||Request::is('billdeskfinalreciept/*')
        ||Request::is('billdeskfinalrecieptwithouttax/*')
        || Request::is('draft/bill_draft')
          ||Request::is('editdraft/*')
          ||Request::is('draft')
           ? 'active' : '' }}"><a href="/dashboard" class="btn btn-custom">Billing</a></li>
          <li class="{{
          Request::is('transactions')||
        Request::is('edittransactiondetails/clone_bill/*')||
          Request::is('billingsidetransactions')||
          Request::is('transactiondetails/*')||
          Request::is('edittransactiondetails/edit_bill/*')
           ? 'active' : '' }}"><a href="/transactions" class="btn btn-custom">Transaction History</a></li>
          <li class="{{
          Request::is('return')||Request::is('returnhistory')||Request::is('return_transaction/*')||Request::is('returnfinalreciept/*/*') ? 'active' : '' }}"><a href="/return" class="btn btn-custom">Sales Return</a></li>
          @foreach ($users as $user)
            @if ($user->role_id == '20')
              <li class="{{
              Request::is('quotation')||
              Request::is('draft/quotationdraft')||
              Request::is('receipt/quotation/*')||
              Request::is('to_quotation/clone_quotation/*')||
              Request::is('to_salesorder/quot_to_salesorder/*')||

              Request::is('editquotationdraft/*')||
              Request::is('quotationdraft')||
              Request::is('historyfilter_sales_delivery/quotation')||
              Request::is('history/quotation')||
              Request::is('to_invoice/quotation/*')||
            Request::is('receipt/clone_quotation/*')||
            Request::is('receipt/quot_to_salesorder/*')||


              Request::is('historydetails/quotation/*')
              ? 'active' : '' }}"><a href="/quotation" class="btn btn-custom">Quotation</a></li>
            @endif
          @endforeach

          @foreach ($users as $user)
            @if ($user->role_id == '17')
              <li class="{{
               Request::is('sales_order')||
               Request::is('receipt/sales_order/*')||
               Request::is('draft/salesdraft')||
               Request::is('sales_order_draft/salesorderdraft/*')||

                Request::is('historyfilter_sales_delivery/sales_order')||
                Request::is('history/sales_order')||
                Request::is('historydetails/sales_order/*')||
                Request::is('editsalesorder/*')||
                Request::is('salesorder_edit/editsalesorder/*')||
                Request::is('to_invoice/sales_order/*')

                 ? 'active' : '' }}"><a href="/sales_order" class="btn btn-custom">Sales Order</a></li>
            @endif
          @endforeach

          @foreach ($users as $user)
            @if ($user->role_id == '18')
              <li class="{{
              Request::is('delivery_note')
              ||Request::is('historyfilter_sales_delivery/deliverynote')
              ||Request::is('history/deliverynote')
              || Request::is('historydetails/deliverynote/*')
                ||Request::is('draft/deliverydraft')
                ||Request::is('editdeliverydraft/deliverydraft/*')
                ||Request::is('deliverynotereceipt/*')
                ||Request::is('edittransactiondetails/to_delivery/*')




               ? 'active' : '' }}"><a href="/delivery_note" class="btn btn-custom">Delivery Note</a></li>
            @endif
          @endforeach

          @foreach ($users as $user)
            @if ($user->role_id == '21')
              <li class="{{
              Request::is('performance_invoice')
              ||Request::is('historyfilter_sales_delivery/performance_invoice')
              ||Request::is('history/performance_invoice')
              || Request::is('historydetails/performance_invoice/*')
              ||Request::is('draft/performadraft')
              ||Request::is('receipt/performance_invoice/*')
              ||Request::is('editperformadraft/performadraft/*')



               ? 'active' : '' }}"><a href="/performance_invoice" class="btn btn-custom">Proforma Invoice</a></li>
            @endif
          @endforeach
          <li class="{{
          Request::is('addfunds')
          ||Request::is('customerstatus')
          ||Request::is('chequesummary')
          ||Request::is('payment_voucher/*')
          ||Request::is('supplier_creditbillshistory/*')
          || Request::is('suppliercredit_trans_history/*')
          || Request::is('creditbillshistory/*')
          || Request::is('credittransactionshistory/*')
          ||Request::is('cash_statement_transactions/*')
          ||Request::is('cash_statement_transactions/*')
          ||Request::is('reciept_voucher/*')
           || Request::is('suppliercash_trans_history/*')

           ? 'active' : '' }}"><a href="/addfunds" class="btn btn-custom">Ledger Fund</a></li>
            @foreach ($users as $user)
            @if ($user->role_id == '28')
            <li class="{{ Request::is('creditnote')||Request::is('customer_summary')||Request::is('creditnotefinalreciept/*')||Request::is('creditnoteviewdetails/*')||Request::is('creditnote_history')||Request::is('creditnote/view/*')
            ? 'active' : ''}}">
            <a href="/creditnote" class="btn btn-custom">Credit Note</a>
           </li>
           @endif
           @endforeach
        </ul>
      </div>
    </div>
  </nav>
</body>
</html>


