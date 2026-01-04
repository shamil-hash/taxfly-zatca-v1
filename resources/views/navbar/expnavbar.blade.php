
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
            @if ($user->role_id == '9')

            <li class="{{Request::is('income')||Request::is('monthwseincomehistory')||Request::is('monthwiseexpencehistorydate')|| Request::is('searchmonthwiseincome')||Request::is('expenseshistory')||Request::is('monthwiseexpensehistory')||Request::is('indirectincome')  ||Request::is('companyexpenses') ? 'active' : '' }}"> <a href="/income" class="btn btn-custom"> </i>Income & Expense</a></li>
            <li class="{{Request::is('salesreportperday/*/*/*/*')||Request::is('datesales/*/*')||Request::is('datepurchases/*/*')||Request::is('datereturn/*/*')||Request::is('datereturn/*/*')||Request::is('returnreportperday/*/*/*/*')||Request::is('purchasereturnperyear/*/*/*/*')||Request::is('purchasereturnpermonth/*/*/*/*')||Request::is('purchasereturnperday/*/*/*/*')||Request::is('accountreport')||Request::is('purchaseperyear/*/*/*/*')||Request::is('purchasepermonth/*/*/*/*')|| Request::is('accountreturnreport')||Request::is('purchaseperday/*/*/*/*')|| Request::is('salesreport')||Request::is('accountpurchasereturn')
||Request::is('monthpurchases/*/*')
||Request::is('yearpurchases/*/*')
||Request::is('purchasedetailsproduct/*')
||Request::is('returnreportpermonth/*/*/*/*')
||Request::is('monthreturn/*/*')
||Request::is('returnreportperyear/*/*/*/*')
||Request::is('yearreturn/*/*')
||Request::is('salesreportfinal/*')
||Request::is('monthsales/*/*')
||Request::is('salesreportpermonth/*/*/*/*')
||Request::is('yearsales/*/*')
||Request::is('datepurchasesreturn/*/*')
||Request::is('monthpurchasesreturn/*/*')
 ||Request::is('yearpurchasesreturn/*/*')

||Request::is('salesreportperyear/*/*/*/*')? 'active' : '' }}"><a href="/accountreport" class="btn btn-custom">Accounts Report</a></li>
            <li class="{{ Request::is('finalreport')||Request::is('finalreporthistory') ? 'active' : '' }}"><a href="/finalreport" class="btn btn-custom">Final Report</a></li>

                @endif
            @endforeach
        </ul>
      </div>
    </div>
  </nav>
</body>
</html>
