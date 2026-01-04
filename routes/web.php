<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\PrintESCPOSController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SuperuserController;
use App\Http\Controllers\TestPrintController;
use App\Http\Controllers\UserAuth;
use App\Http\Controllers\CreditDebitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserControllerNew;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\WebClientPrintController;
use App\Http\Controllers\ZatcaController;
use Illuminate\Support\Facades\Route;

use App\Services\ZatcaService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'disable_back_btn'], function () {
    Route::get('/', function () {
        if (session()->has('superuser')) {
            return redirect('superuserdashboard');
        } elseif (session()->has('softwareuser')) {
            return redirect('userdashboard');
        } elseif (session()->has('adminuser')) {
            return redirect('admindashboard');
        } elseif (session()->has('credituser')) {
            return redirect('creditdashboard');
        }

        return view('/superuser/login');
    });
    // Route::get('/creditlogout', function () {
    //     if(session()->has('credituser'))
    //     {
    //         session()->pull('credituser');
    //     }
    //     return redirect('/');
    // });
    Route::get('/userlogin', function () {
        if (session()->has('softwareuser')) {
            return redirect('userdashboard');
        }

        return redirect('/');
    });
    Route::get('/superuserlogout', function () {
        if (session()->has('superuser')) {
            session()->pull('superuser');
        }

        return redirect('/');
    });
    // Route::get('/adminlogout', function () {
    //     if(session()->has('adminuser'))
    //     {
    //         session()->pull('adminuser');
    //     }
    //     return redirect('/');
    // });
    Route::get('/superuserlogin', function () {
        if (session()->has('superuser')) {
            return redirect('superuserdashboard');
        }

        return redirect('/');
    });
    Route::get('/adminlogin', function () {
        if (session()->has('adminuser')) {
            return redirect('admindashboard');
        }

        return redirect('/');
    });
    Route::get('/adminlogout', [UserAuth::class, 'adminLogout']);
    Route::get('/creditlogout', [UserAuth::class, 'creditLogout']);
    Route::get('/print-sunmi-mini', [PDFController::class, 'generateSalesPdfmini'])->name('print-sunmi-mini');
    Route::get('/print-sunmi', [PDFController::class, 'generateSalesPdf'])->name('print-sunmi');
    Route::post('/update-vat-mode', [UserController::class, 'updateVatMode']);

    Route::get('/dashboard', [UserController::class, 'custombillingdashboard']);
    Route::get('/createcredit', [AdminController::class, 'createCredit']);
    Route::get('/listcredit', [AdminController::class, 'listCredit']);
    Route::get('/customersummary/{id}', [AdminController::class, 'listCustomersummary']);
    Route::get('/customersalesdata/{transactionid}', [AdminController::class, 'listCustomersalesdat']);
    Route::get('/locationcredit/{locationid}', [AdminController::class, 'listLocationbasedcredit']);
    Route::get('/listcreditajax/{locationid}', [AdminController::class, 'listCreditajax']);
    Route::get('/gettransactionid', [UserController::class, 'getTransactionajax']);
    Route::get('/salesdetails/{transactionid}', [AdminController::class, 'listCustomersalesdetails']);
    Route::get('/customersales', [AdminController::class, 'listCustomersales']);
    Route::get('/billdeskreciept', [UserController::class, 'recieptSecond']);

    Route::get('/billdeskfinalreciept/{transaction}', [UserController::class, 'recieptFinal']);

    Route::get('/billdeskfinalrecieptwithouttax/{transaction}', [UserController::class, 'recieptwithouttaxFinal']);
    Route::get('/transactiondetails/{name}', [UserController::class, 'viewTrans']);
    Route::get('/return', [UserController::class, 'returnProduct']);
    Route::get('/transactions', [UserController::class, 'listTransaction']);
    Route::get('/edittransactions', [UserController::class, 'editTransaction']);
    Route::get('/returnhistory', [UserController::class, 'listReturn']);

    Route::get('/return_transaction/{transaction_id}/{date}/{branch_id?}', [UserController::class, 'viewReturns']);
    Route::get('/inventorydashboard', [UserController::class, 'inventorydashBoard']);
    Route::get('/search/{search_text}', [UserController::class, 'searchProduct']);
    Route::get('/search/', [UserController::class, 'inventorydashBoard']);
    Route::get('/listcategory', [UserController::class, 'listCategory']);
    Route::get('/changeproductstatus/{id}', [UserController::class, 'changeStatus']);
    Route::get('/deleteproduct/{id}', [UserController::class, 'deleteProduct']);
    Route::get('/deletecategory/{id}', [UserController::class, 'deleteCategory']);
    Route::get('/disablecredituser/{id}', [AdminController::class, 'disableCredit']);
    Route::get('/inventorystock', [UserController::class, 'stockData']);
    Route::get('/newstockpurchases', [UserController::class, 'newstockPurchases']);
    Route::get('/purchasestock', [UserController::class, 'purchaseData']);
    Route::get('/purchasehistory', [UserController::class, 'purchaseHistory']);
    Route::get('/datesales/{date}/{location}', [UserController::class, 'dateSales']);
    Route::get('/monthsales/{date}/{location}', [UserController::class, 'monthSales']);
    Route::get('/yearsales/{date}/{location}', [UserController::class, 'yearSales']);
    Route::get('/salesreport', [UserController::class, 'salesReport']);
    Route::get('/salesreportfinal/{transaction}', [UserController::class, 'salesReportfinal']);
    Route::get('/salesreportperyear/{viewtype}/{location_id}/{fromdate}/{todate}', [UserController::class, 'accountantperyearview']);
    Route::get('/salesreportpermonth/{viewtype}/{location_id}/{fromdate}/{todate}', [UserController::class, 'accountantpermonthview']);
    Route::get('/salesreportperday/{viewtype}/{location_id}/{fromdate}/{todate}', [UserController::class, 'accountantperdayview']);
    Route::get('/datereturn/{date}/{location}', [UserController::class, 'dateReturn']);
    Route::get('/monthreturn/{date}/{location}', [UserController::class, 'monthReturn']);
    Route::get('/yearreturn/{date}/{location}', [UserController::class, 'yearReturn']);
    Route::get('/accountreturnreport', [UserController::class, 'accountreturnReport']);
    Route::get('/returnreportfinal/{transaction}', [UserController::class, 'returnReportfinal']);

    Route::get('/returnreportperyear/{viewtype}/{location_id}/{fromdate}/{todate}', [UserController::class, 'accountantreturnperyearview']);
    Route::get('/returnreportpermonth/{viewtype}/{location_id}/{fromdate}/{todate}', [UserController::class, 'accountantreturnpermonthview']);
    Route::get('/returnreportperday/{viewtype}/{location_id}/{fromdate}/{todate}', [UserController::class, 'accountantreturnperdayview']);

    Route::get('/purchaseperyear/{viewtype}/{location_id}/{fromdate}/{todate}', [UserController::class, 'accountantpurchaseperyearview']);
    Route::get('/purchasepermonth/{viewtype}/{location_id}/{fromdate}/{todate}', [UserController::class, 'accountantpurchasepermonthview']);
    Route::get('/purchaseperday/{viewtype}/{location_id}/{fromdate}/{todate}', [UserController::class, 'accountantpurchaseperdayview']);

    Route::get('/datepurchases/{date}/{location}', [UserController::class, 'datePurchase']);
    Route::get('/monthpurchases/{date}/{location}', [UserController::class, 'monthPurchase']);
    Route::get('/yearpurchases/{date}/{location}', [UserController::class, 'yearPurchase']);
    Route::get('/createuser', [AdminController::class, 'createUser']);
    Route::get('/admindashboard', [AdminController::class, 'dashBoard']);
    Route::get('/listuser', [AdminController::class, 'listUser']);
    Route::get('getroles/{id}', [AdminController::class, 'getRoles']);
    Route::get('getlocs/{id}', [AdminController::class, 'getLocs']);
    Route::get('gethistory/{userid}', [UserController::class, 'getHistory']);
    Route::get('branchdat/{id}', [AdminController::class, 'branchMaindat']);
    Route::get('branchdatpurchase/{id}', [AdminController::class, 'branchPurchasedat']);
    Route::get('branchdatpurchasereturn/{id}', [AdminController::class, 'branchPurchasereturndat']);
    Route::get('branchdatstock/{id}', [AdminController::class, 'branchStockdat']);
    Route::get('branchdatreturn/{id}', [AdminController::class, 'branchReturndat']);
    Route::get('branchdatemployee/{id}', [AdminController::class, 'branchEmployeedat']);
    Route::get('branchdatdetails/{id}', [AdminController::class, 'branchdatDetails']);
    Route::get('purchasereportdetails/{id}', [AdminController::class, 'purchaseReportdat']);
    Route::get('returnreportdetails/{transaction_id}/{date}', [AdminController::class, 'returnReportdat']);
    Route::get('getmodules/{id}', [SuperuserController::class, 'getModules']);
    Route::get('/listdesk', [AdminController::class, 'listDesk']);
    Route::get('/listanalytics', [AdminController::class, 'listAnalytics']);
    Route::get('/listaccountant', [AdminController::class, 'listAccountant']);
    Route::get('inventorydetails/{id}', [AdminController::class, 'getInventorydat']);
    Route::get('purchasereport', [AdminController::class, 'purchaseReport']);
    Route::get('returnreport', [AdminController::class, 'returnReport']);
    Route::get('inventorydetails/{id}', [AdminController::class, 'getInventorydat']);
    Route::get('/listinventory', [AdminController::class, 'listInventory']);
    Route::get('/listcustomersupport', [AdminController::class, 'listCustomersupport']);
    Route::get('/listteamleader', [AdminController::class, 'listTeamleader']);
    Route::get('/listmanager', [AdminController::class, 'listManager']);
    Route::get('/listmarketing', [AdminController::class, 'listMarketing']);
    Route::get('/superuserdashboard', [SuperuserController::class, 'dashBoard']);
    Route::get('/listadmin', [SuperuserController::class, 'listAdmin']);
    Route::get('/listsuperuseranalytics', [SuperuserController::class, 'listAnalytics']);
    Route::get('/listsuperuserbilldesks', [SuperuserController::class, 'listBilldesks']);
    Route::get('/listsuperuserinventory', [SuperuserController::class, 'listInventory']);

    Route::get('/listsuperuseraccountants', [SuperuserController::class, 'listAccountants']);
    Route::get('/createadmin', [SuperuserController::class, 'createAdmin']);
    Route::get('/userlogout', [UserAuth::class, 'userLogout']);
    Route::get('/userdashboard', [UserController::class, 'dashBoard']);
    Route::get('/createbranch', [AdminController::class, 'createBranch']);
    Route::get('/listbranch', [AdminController::class, 'listBranch']);
    Route::get('/branchwisesummary', [AdminController::class, 'branchwiseSummary']);
    Route::get('/companyexpenses', [UserController::class, 'companyExpenses']);
    Route::get('/expenseshistory', [UserController::class, 'companyExpenseshistory']);
    Route::get('/accountreport', [UserController::class, 'accountReport']);
    Route::get('/finalreport', [UserController::class, 'FinalReport']);
    Route::get('/employeesalary', [UserController::class, 'EmployeeSalary']);
    Route::get('/employeesalarydat/{user_id}', [UserController::class, 'EmployeeSalarydat']);
    Route::get('/expensedownload/{file}', [UserController::class, 'expensedownload']);
    Route::get('/download/{file}', [UserController::class, 'download']);
    Route::get('generate-pdf/{transaction_id}', [PDFController::class, 'generatePDF']);
    Route::get('generatetax-pdf/{transaction_id}', [PDFController::class, 'generatetaxPDF']);

    Route::get('generatetax-pdfsunmi/{transaction_id}', [PDFController::class, 'generatetaxPDFsunmi']);

    Route::get('admingeneratetax-pdf/{transaction_id}', [PDFController::class, 'admingeneratetaxPDF']);
    Route::get('/edituser/{id}', [AdminController::class, 'editUsers']);
    Route::get('/changeuseraccess/{id}', [AdminController::class, 'changeAccess']);
    Route::get('/adminchangepassword', [AdminController::class, 'adminchangePassword']);
    Route::get('/superuserchangepassword', [SuperuserController::class, 'superuserchangePassword']);
    Route::get('/purchasereturn', [UserController::class, 'purchaseReturn']);
    Route::get('/purchasereturnhistory', [UserController::class, 'purchaseReturnhistory']);
    Route::get('/accountpurchasereturn', [UserController::class, 'accountPurchasereturn']);

    Route::get('/purchasereturnperyear/{viewtype}/{location_id}/{fromdate}/{todate}', [UserController::class, 'accountantpurchasereturnperyearview']);
    Route::get('/purchasereturnpermonth/{viewtype}/{location_id}/{fromdate}/{todate}', [UserController::class, 'accountantpurchasereturnpermonthview']);
    Route::get('/purchasereturnperday/{viewtype}/{location_id}/{fromdate}/{todate}', [UserController::class, 'accountantpurchasereturnperdayview']);

    Route::get('/datepurchasesreturn/{date}/{location}', [UserController::class, 'datePurchasereturn']);
    Route::get('/monthpurchasesreturn/{date}/{location}', [UserController::class, 'monthPurchasereturn']);
    Route::get('/yearpurchasesreturn/{date}/{location}', [UserController::class, 'yearPurchasereturn']);
    Route::get('generatepublic-pdf/{transaction_id}', [PublicController::class, 'generatePDF']);
    Route::get('/finalreporthistory', [UserController::class, 'finalReporthistory']);
    Route::get('/downloadreport/{file}', [UserController::class, 'downloadReport']);
    Route::get('/listaccountantreports', [AdminController::class, 'accountantReports']);
    Route::get('/usercreationrequests', [AdminController::class, 'hrusercreationRequests']);
    Route::get('/hrusercreation/{id}', [AdminController::class, 'hrusercreation']);
    Route::get('/adminedit/{id}', [SuperuserController::class, 'adminEdit']);
    Route::get('/generateinvoice', [UserController::class, 'generateInvoice']);
    Route::get('/invoicegenerated/{invoiceid}', [UserController::class, 'invoiceGenerated']);
    Route::get('printinvoice/{invoice_id}', [PDFController::class, 'invoicePDF']);
    Route::get('/creditdashboard', [CreditController::class, 'dashBoard']);
    Route::get('/creditchangepassword', [CreditController::class, 'changePassword']);
    Route::get('/credittransactions', [CreditController::class, 'creditTransactions']);
    Route::get('/creditduesummary', [CreditController::class, 'creditDuesummary']);
    Route::get('/credittransactiondate', [CreditController::class, 'creditTransactiondate']);
    Route::get('/creditsummarydate', [CreditController::class, 'creditDuesummarydate']);
    Route::get('generatepdf-pdf/{transaction_id}', [PDFController::class, 'generatecreditPDF']);
    Route::get('/billingsidetransactions', [UserController::class, 'billingsideTransactions']);
    Route::get('customercreditdata/{id}', [AdminController::class, 'customerCreditdata']);
    Route::get('/edittransactionsdate', [UserController::class, 'edittransactionsDate']);
    Route::get('/listcredituser', [AdminController::class, 'listCredituser']);
    Route::get('editcredituser/{id}', [AdminController::class, 'editCredituser']);
    Route::get('/stockaddhistory/{locationid}/{id}', [AdminController::class, 'stockAddHistory']);
    Route::get('/stocktransactionhistory/{locationid}/{id}', [AdminController::class, 'stockTransactionHistory']);
    Route::get('/addfunds', [UserController::class, 'addFunds']);
    Route::get('/liststocks', [UserController::class, 'inventoryStocklist']);
    Route::get('/purchasehistorydate', [UserController::class, 'purchaseHistorydate']);
    Route::get('credittransactionshistory/{id}', [UserController::class, 'creditTransactionsHistory']);
    Route::get('creditbillshistory/{id}', [UserController::class, 'creditBillsHistory']);

    Route::get('/editpurchase/{id}', [UserController::class, 'PurchaseEdit'])->name('purchase.edit');

    Route::get('adminsalesdate', [AdminController::class, 'dateSales']);
    Route::get('adminpurchasedate', [AdminController::class, 'datePurchases']);
    Route::get('adminpurchasereturndate', [AdminController::class, 'datePurchasesreturn']);
    Route::get('adminreturndate', [AdminController::class, 'dateAdminreturndate']);
    Route::get('adminstockdate', [AdminController::class, 'dateAdminstockdate']);
    Route::get('adminstocktransactiondate', [AdminController::class, 'dateStocktransactiondate']);
    Route::get('adminstockadddate', [AdminController::class, 'dateStockadddate']);
    Route::get('/deleteadmin/{id}', [SuperuserController::class, 'adminDelete']);
    Route::get('/plexpayrecharge', [UserController::class, 'Plexpayrecharge']);
    Route::get('/plexpayinternational', [UserController::class, 'Plexpayrechargeinternational']);
    Route::get('/plexpaybalance', [UserController::class, 'Plexpaybalance']);
    Route::get('/plexpayreports', [UserController::class, 'Plexpayreports']);
    Route::get('/plexpayreportssearch', [UserController::class, 'Plexpayreportsearch']);
    Route::get('/plexpayfunds', [UserController::class, 'Plexpayfunds']);
    Route::get('/plexpaytransactions', [UserController::class, 'Plexpaytransactions']);
    Route::get('/plexpaycollection', [UserController::class, 'Plexpaycollection']);
    Route::get('/plexpayduesummary', [UserController::class, 'Plexpayduesummary']);
    Route::get('/plexpaysummary', [UserController::class, 'Plexpaysummary']);
    Route::get('/voucherrechargeconfirm/{id}', [UserController::class, 'PlexpayVoucherrechargeconfirm']);
    Route::get('/customrecharge/{id}', [UserController::class, 'Plexpaycustomrecharge']);
    Route::get('/localvoucherrecharge/{id}', [UserController::class, 'Plexpaylocalvoucherrecharge']);
    Route::get('/giftcardrecharge/{id}', [UserController::class, 'Plexpaygiftcardrecharge']);
    Route::get('/plexpayfundssearch', [UserController::class, 'plexpayFundssearch']);
    Route::get('/plexpaytransactionsearch', [UserController::class, 'plexpayTransactionsearch']);
    Route::get('/plexpaycollectionsearch', [UserController::class, 'plexpayCollectionsearch']);
    Route::get('/plexpayduesummarysearch', [UserController::class, 'plexpayDuesummarysearch']);
    Route::get('/plexpaysummarysearch', [UserController::class, 'plexpaySummarysearch']);
    Route::get('/plexpayrechargesearch', [UserController::class, 'Plexpayrechargesearch']);
    Route::get('/plexpayrechargeinternational/{id}/{cid}/{pcode}', [UserController::class, 'PlexpayrechargeInternationalProvider']);
    Route::get('/plexpayinternationalrecharge/{country}/{provider}', [UserController::class, 'PlexpayrechargeInternationalPlans']);
    Route::get('/plexpayinternationalcustom/{country}/{provider}', [UserController::class, 'PlexpayrechargeInternationalCustom']);
    Route::get('/plexpayregister', [UserController::class, 'Plexpayregister']);
    Route::get('/plexpayunregister', [UserController::class, 'Plexpayunregister']);
    Route::get('/internationalbycountry/{id}', [UserController::class, 'Internationalbycountry']);
    Route::get('/internationalprovider/{id}', [UserController::class, 'Internationalbyprovider']);
    Route::get('/plexpaydownloadpdf/{id}', [PDFController::class, 'plexpaydownloadpdf']);
    Route::get('/plexpaycollectiondownloadpdf/{id}', [PDFController::class, 'plexpaycollectiondownloadpdf']);
    Route::get('/rechargefailed', [UserController::class, 'plexpayrechargefailed']);
    Route::get('/rechargesuccessful/{transaction_id}', [UserController::class, 'plexpayrechargesuccessful']);
    Route::get('/localprovider/{id}', [UserController::class, 'localprovider']);
    Route::get('/localcustom/{id}', [UserController::class, 'localCustom']);
    Route::get('/plexpaypasswordchange/', [UserController::class, 'plexpaychangepassword']);
    // post
    Route::post('plexpaypasswordchangepost', [UserController::class, 'plexpaypasswordchangepost']);
    Route::post('localvoucherconfirm', [UserController::class, 'localvoucherconfirm']);
    Route::post('localcustomrechargepost', [UserController::class, 'localCustomrechargepost']);
    Route::post('plexpaylocalnumber', [UserController::class, 'plexpaylocalnumber']);
    Route::post('plexpayinternationalnumber', [UserController::class, 'Plexpayinternationalnumber']);
    Route::get('customrechargepost', [UserController::class, 'Customrechargepost']);
    Route::post('plexpayrechargeinternationalnumber', [UserController::class, 'Plexpayrechargeinternationalnumber']);
    Route::post('plexpayrechargelocalnumber', [UserController::class, 'plexpayrechargelocalnumber']);
    Route::post('plexpayregisterpost', [UserController::class, 'Plexpayregisterpost']);
    Route::post('plexpayuserregisterpost', [UserController::class, 'Plexpayuserregisterpost']);
    Route::post('creditcreateform', [AdminController::class, 'createCreditform']);
    Route::post('addfundcredit', [UserController::class, 'addFundCredit']);
    Route::post('addfundcredit2', [UserController::class, 'addFundCredit2']);
    Route::post('/submitdata', [UserController::class, 'submitData'])->name('data.submitdata');
    Route::post('/editsubmitdata', [UserController::class, 'editsubmitData']);
    Route::post('/updatedata', [UserController::class, 'updateData']);
    Route::post('/returnproduct', [UserController::class, 'returnAction']);
    Route::get('/returnfinalreciept/{transaction}/{return_id}', [UserController::class, 'returnfinalreciept']);
    Route::get('/return-pdf/{transaction_id}/{return_id}', [PDFController::class, 'returnPDF']);
    Route::post('/productdata', [UserController::class, 'productData'])->name('data.productdata');;
    Route::post('/search/productdata', [UserController::class, 'productData']);
    Route::post('/createcategory', [UserController::class, 'createCategory']);
    Route::post('/submitstock', [UserController::class, 'stockAdd']);
    Route::post('/submitstockdata', [UserController::class, 'stockDetails']);
    Route::post('/accountantsalesview', [UserController::class, 'accountantSalesview']);
    Route::post('/accountantreturnview', [UserController::class, 'accountantReturnview']);
    Route::post('/accountantpurchaseview', [UserController::class, 'accountantPurchaseview']);
    Route::post('usercreate', [AdminController::class, 'createUserform']);
    Route::post('adminuser', [UserAuth::class, 'adminLogin']);
    Route::post('addroles', [AdminController::class, 'addRoles']);
    Route::post('addmodules', [SuperuserController::class, 'addModules']);
    Route::post('addlocationaccountant', [AdminController::class, 'locationofAccountant']);
    Route::post('superuseruser', [UserAuth::class, 'superuserLogin']);
    Route::post('admincreate', [SuperuserController::class, 'createAdminform']);
    Route::post('user', [UserAuth::class, 'userLogin']);
    Route::post('branchcreate', [AdminController::class, 'createBranchform']);
    Route::post('/companyexpensessubmit', [UserController::class, 'companyExpensessubmit']);
    Route::post('/addsalaryemployee', [UserController::class, 'addsalaryemployeeSubmit']);
    Route::post('/submitadminpassword', [AdminController::class, 'submitadminPassword']);
    Route::post('useredit', [AdminController::class, 'userEdit']);
    Route::post('/submitsuperuserpassword', [SuperuserController::class, 'submitsuperuserPassword']);
    Route::post('submitpurchasereturn', [UserController::class, 'returnsubmitData']);
    Route::post('/accountantpurchasereturnview', [UserController::class, 'accountantPurchasereturnview']);
    Route::post('/accountantfinalreport', [UserController::class, 'accountantFinalreport']);
    Route::post('hrusercreate', [AdminController::class, 'hrcreateUserform']);
    Route::post('admineditform', [SuperuserController::class, 'adminEditform']);
    Route::post('generateinvoiceform', [UserController::class, 'generateInvoiceform']);
    Route::post('/submitcredituserpassword', [CreditController::class, 'submitcredituserPassword']);
    Route::post('credituseredit', [AdminController::class, 'credituserEdit']);
    Route::post('editpurchasedata', [UserController::class, 'purchaseEditform']);
    Route::get('/changecategoryaccess/{id}', [UserController::class, 'changecategoryAccess']);
    Route::get('/giftcardrecharge1/{id}', [UserController::class, 'giftcardrecharge1']);
    Route::get('/ElectricityGETpayment', [UserController::class, 'ElectricityGETpayment']);

    Route::get('/userreport/{id}', [AdminController::class, 'salesreport']);
    Route::get('userdatdetails/{uid}/{transaction_id}', [AdminController::class, 'userdatDetails']);
    Route::get('/filtersalesreport/{uid}', [AdminController::class, 'filtersales']);
    Route::get('/userstock/{id}', [AdminController::class, 'userstockreport']);
    Route::get('/userstockaddhistory/{uid}/{product_id}', [AdminController::class, 'userstockAddHistory']);
    Route::get('userstockfilter', [AdminController::class, 'userstockfilter']);
    Route::get('/userstocktransactionhistory/{uid}/{id}', [AdminController::class, 'userstockTransactionHistory']);
    Route::get('/listunit', [UserController::class, 'listCategory']);
    Route::post('/createunit', [UserController::class, 'createUnit']);
    Route::get('/deleteunit/{id}', [UserController::class, 'deleteUnit']);
    Route::get('/getunit/{pro_id}', [UserController::class, 'getUnit']);
    Route::get('/createsupplier', [AdminController::class, 'createSupplier']);
    Route::post('suppliercreateform', [AdminController::class, 'suppliercreateform']);
    Route::get('/listsupplier', [AdminController::class, 'listSupplier']);
    Route::get('editsupplier/{id}', [AdminController::class, 'editSupplier']);
    Route::post('supplieredit', [AdminController::class, 'supplieredit']);
    Route::get('suppliersales/{supplier_name}', [AdminController::class, 'supplier_salesreport']);

    Route::get('/userstocktransactionhistory/{uid}/{product_id}', [AdminController::class, 'userstockTransaction']);
    Route::get('userstocktransactiondate/{uid}/{product_id}', [AdminController::class, 'userdateStocktransactiondate']);
    Route::get('userpurchase/{uid}', [AdminController::class, 'userpurchase']);
    Route::get('userpurchasedate/{uid}', [AdminController::class, 'userpurchasedata']);
    Route::get('userpurchasereturn/{uid}', [AdminController::class, 'userpurchasereturn']);
    Route::get('userpurchasereturndate/{uid}', [AdminController::class, 'userPurchasesreturnfilter']);
    Route::get('userreturn/{uid}', [AdminController::class, 'userReturndat']);
    Route::get('userreturndate/{uid}', [AdminController::class, 'dateUserreturndate']);

    Route::get('/getbarcodedata/{barcode}', [UserController::class, 'getbarcodedata']);

    Route::get('export-product', [UserController::class, 'exportExcel']);

    Route::get('stock', [AdminController::class, 'Stock']);
    Route::get('/stockhistory/{id}', [AdminController::class, 'stockHistory']);
    Route::get('adminstockdateall', [AdminController::class, 'dateStockdate']);
    Route::get('/stocktranshistory/{id}', [AdminController::class, 'stockTransHistory']);
    Route::get('adminstocktransdate', [AdminController::class, 'dateStocktransdate']);

    Route::get('/monthwiseexpensehistory', [UserController::class, 'monthwiseExpenseHistory']);
    Route::get('/monthwiseexpencehistorydate', [UserController::class, 'monthwiseExpenseHistorydate']);
    Route::get('/expensereport', [AdminController::class, 'adminmonthwiseExpenseReport']);
    Route::get('/adminmonthwiseexpencereportdate', [AdminController::class, 'adminmonthwiseExpenseReportdate']);
    Route::get('/adminemployeesalaryreport', [AdminController::class, 'AdminEmployeeSalary']);
    Route::get('/adminemployeesalaryreportdat/{user_id}', [AdminController::class, 'AdminEmployeeSalarydat']);

    Route::get('/getproductdata/{trans_id}', [UserController::class, 'getproductdata']);
    Route::get('/getsoldquantity/{trans_id}/{pro_id}', [UserController::class, 'getsoldquantity']);

    Route::get('/getremainstock_purchase/{receiptno}/{pro_id}', [UserController::class, 'getremainstock_purchase']);

    Route::get('export-sales-year/{userid}/{viewtype}/{location}/{date}', [UserController::class, 'exportExcelSalesReport']);
    Route::get('export-sales-month/{userid}/{viewtype}/{location}/{month}', [UserController::class, 'exportExcelSalesReport']);
    Route::get('export-sales-day/{userid}/{viewtype}/{location}/{day}', [UserController::class, 'exportExcelSalesReport']);
    Route::get('export-sales-daysdata/{userid}/{viewtype}/{location}/{fromdate}/{todate}', [UserController::class, 'completeSalesExport']);
    Route::get('export-sales-monthsdata/{userid}/{viewtype}/{location}/{fromdate}/{todate}', [UserController::class, 'completeSalesExport']);
    Route::get('export-sales-yearsdata/{userid}/{viewtype}/{location}/{fromdate}/{todate}', [UserController::class, 'completeSalesExport']);

    // new p and l

    Route::get('p_and_l_report', [AdminController::class, 'new_my_p_and_l_report']);

    Route::get('getfilterpayment/{supplier}', [AdminController::class, 'filterpayment_mode']);

    Route::get('getfundhistory/{userid}', [UserController::class, 'getFundHistory']);
    Route::post('addsupplier_creditfund', [UserController::class, 'addSupplierCreditFund']);
    Route::get('suppliercredit_trans_history/{id}', [UserController::class, 'supplierCreditTransactionHistory']);
    Route::get('supplier_creditbillshistory/{id}', [UserController::class, 'supplierCreditBillsHistory']);

    Route::get('admin_supplier_credittrans/{id}', [AdminController::class, 'admin_supplierCreditTransactionHistory']);

    Route::get('export-purchase-year/{userid}/{viewtype}/{location}/{date}', [UserController::class, 'exportExcelPurchaseReport']);
    Route::get('export-purchase-month/{userid}/{viewtype}/{location}/{month}', [UserController::class, 'exportExcelPurchaseReport']);
    Route::get('export-purchase-day/{userid}/{viewtype}/{location}/{day}', [UserController::class, 'exportExcelPurchaseReport']);
    Route::get('export-purchase-daysdata/{userid}/{viewtype}/{location}/{fromdate}/{todate}', [UserController::class, 'completePurchaseExport']);
    Route::get('export-purchase-monthsdata/{userid}/{viewtype}/{location}/{fromdate}/{todate}', [UserController::class, 'completePurchaseExport']);
    Route::get('export-purchase-yearsdata/{userid}/{viewtype}/{location}/{fromdate}/{todate}', [UserController::class, 'completePurchaseExport']);

    Route::get('export-salesreturn-day/{userid}/{viewtype}/{location}/{day}', [UserController::class, 'exportSalesReturnReport']);
    Route::get('export-salesreturn-month/{userid}/{viewtype}/{location}/{month}', [UserController::class, 'exportSalesReturnReport']);
    Route::get('export-salesreturn-year/{userid}/{viewtype}/{location}/{date}', [UserController::class, 'exportSalesReturnReport']);
    Route::get('export-salesreturn-daysdata/{userid}/{viewtype}/{location}/{fromdate}/{todate}', [UserController::class, 'completeSalesReturnExport']);
    Route::get('export-salesreturn-monthsdata/{userid}/{viewtype}/{location}/{fromdate}/{todate}', [UserController::class, 'completeSalesReturnExport']);
    Route::get('export-salesreturn-yearsdata/{userid}/{viewtype}/{location}/{fromdate}/{todate}', [UserController::class, 'completeSalesReturnExport']);

    Route::get('export-purchasereturn-day/{userid}/{viewtype}/{location}/{day}', [UserController::class, 'exportPurchaseReturnReport']);
    Route::get('export-purchasereturn-month/{userid}/{viewtype}/{location}/{month}', [UserController::class, 'exportPurchaseReturnReport']);
    Route::get('export-purchasereturn-year/{userid}/{viewtype}/{location}/{date}', [UserController::class, 'exportPurchaseReturnReport']);
    Route::get('export-purchasereturn-daysdata/{userid}/{viewtype}/{location}/{fromdate}/{todate}', [UserController::class, 'completePurchaseReturnExport']);
    Route::get('export-purchasereturn-monthsdata/{userid}/{viewtype}/{location}/{fromdate}/{todate}', [UserController::class, 'completePurchaseReturnExport']);
    Route::get('export-purchasereturn-yearsdata/{userid}/{viewtype}/{location}/{fromdate}/{todate}', [UserController::class, 'completePurchaseReturnExport']);

    Route::get('/export_supplier_stock/{supplier}/{payment_mode?}', [AdminController::class, 'export_supplier_stock_purchase']);

    Route::post('/upload-products', [UserController::class, 'upload_products'])->name('upload-products');

    Route::get('/indirectincome', [UserController::class, 'IndirectIncome']);
    Route::post('/indirectincomesubmit', [UserController::class, 'indirectincomesubmit']);
    Route::get('/searchmonthwiseincome', [UserController::class, 'searchMonthwiseIncome']);
    Route::get('/monthwseincomehistory', [UserController::class, 'monthwiseIncomeHistory']);
    Route::get('/expenseshistorydate', [UserController::class, 'expensesHistorydate']);
    Route::get('/incomereport', [AdminController::class, 'adminmonthwiseIncomeReport']);
    Route::get('/adminmonthwiseincomereportdate', [AdminController::class, 'adminmonthwiseIncomeReportdate']);

    // new filter p and l

    Route::get('filterpandl', [AdminController::class, 'optimized_new_filter_pandl']);

    Route::get('/getcreditsupplier/{receiptno}', [UserController::class, 'getcreditsupplier']);

    Route::get('/exportpandl/{startdate}/{enddate}/{branch}', [AdminController::class, 'export_p_and_l']);

    Route::get('/disableadmin/{id}', [SuperuserController::class, 'disableAdmin']);

    Route::get('/listactivities', [SuperuserController::class, 'listActivities']);

    Route::get('/enablecredituser/{id}', [AdminController::class, 'enableCredit']);

    Route::get('/updatestockdata/{close_stock}', [AdminController::class, 'store_old_stock']);

    // user report

    Route::get('/user-report', [UserController::class, 'userReport']);
    Route::post('/user-report-submit', [UserController::class, 'submitUserReport']);
    Route::get('/userreportreciept/{transaction}', [UserController::class, 'userReportReceipt']);

    // printer

    Route::get('/printers/{trans}', [PrintController::class, 'printers'])->name('printers');
    Route::get('/printESCPOS/{trans}', [PrintController::class, 'printESCPOS'])->name('printESCPOS');
    Route::any('process-request', [WebClientPrintController::class, 'processRequest'])->name('processRequest');
    Route::any('print-command/{trans}/{adminname}/{pobox}/{branchname}/{tel}/{grandinnumber}/{vat}/{trn_number}/{cust}/{date}/{supplieddate}/{payment_type}', [PrintESCPOSController::class, 'printCommands'])->name('printCommands');
    Route::get('/print-receipt/{trans}', [PrinterController::class, 'printsunmi']);
    Route::get('/print-receipt', [TestPrintController::class, 'printReceipt']);
    Route::get('/getWcpScript/{trans}', [PrintController::class, 'getWcpScript'])->name('getWcpScript');
    Route::post('savePrinterStatus', [PrintController::class, 'savePrinterStatus']);
    Route::get('getPrinterStatus/{userid}/{branch}', [PrintController::class, 'getPrinterStatus']);

    Route::get('/rawilk-get-printer', [TestPrintController::class, 'showPrinters']);
    Route::post('/rawilk_print_receipt', [TestPrintController::class, 'printReceipt']);
    Route::get('rawilk_get_status_printer/{userid}/{branch}', [TestPrintController::class, 'rawilk_get_status']);
    Route::get('rawilk_second/{transid}/{printerid}', [TestPrintController::class, 'printReceiptSecond'])->name('rawilk_second');

    // purchase table wise format
    Route::post('/submitstock_table', [UserController::class, 'purchaseTable'])->name('data.submitstock_table');

    // purchase history details view
    Route::get('/purchasedetails/{purchase_id}', [UserController::class, 'viewPurchasedetails']);

    // purchase return
    Route::get('/get_purchase_product/{receiptno}', [UserController::class, 'getPurchaseProduct']);
    // Purchase Return History
    Route::get('/returnpurchasedetails/{receiptno}/{date}', [UserController::class, 'returnPurchaseDetails']);

    // admin daily report
    Route::get('/daily_report/{userid}', [AdminController::class, 'admin_daily_report']);
    // admin daily report print
    Route::get('/admin_daily_report_print/{userid}/{user_report_id}', [AdminController::class, 'admin_daily_report_print']);
    // filter admin daily report
    Route::get('/filter_daily_report/{uid}', [AdminController::class, 'filterDailyReport']);

    // check receiptno
    Route::get('/checkreceipt', [UserController::class, 'receiptno_check'])->name('checkreceipt');

    // list stock category filter
    Route::get('/categoryfilter/{categoryid}', [UserController::class, 'categoryFilter']);

    Route::get('/purchasedetailsproduct/{receiptno}', [UserController::class, 'purchasedetailsproduct']);

    Route::get('/branch_purchase_products/{receiptno}', [AdminController::class, 'branchPurchaseProducts']);

    Route::get('/user_purchase_products/{uid}/{receiptno}', [AdminController::class, 'userPurchaseProducts']);

    // purchase wise bills
    Route::get('/branch_purchasewise_bills/{purchase_id}/{product_id}', [AdminController::class, 'branchPurchaseWiseBills']);
    Route::get('/purchasewise_bills/{purchase_id}/{product_id}', [AdminController::class, 'purchaseWiseBills']);

    Route::get('/exportactivities/{branch?}', [SuperuserController::class, 'export_activities']);
    Route::get('/filter_activities', [SuperuserController::class, 'filter_activities']);

    // sales order
    Route::post('/salesorder_submit', [UserController::class, 'salesorderSubmit']);
    Route::get('gethistorysales/{userid}', [UserController::class, 'gethistorySales']);

    // purchase order
    Route::get('/purchase_order', [UserController::class, 'purchaseOrder']);
    Route::post('/purchaseorder_submit', [UserController::class, 'purchaseOrderSubmit']);
    Route::get('/purchaseorderreceipt/{transaction}', [UserController::class, 'purchaseorderReceipt']);
    Route::get('/purhaseorderreceipt_pdf/{transaction}', [PDFController::class, 'purchaseorderPDF']);

    // delivery_note
    Route::get('/delivery_note', [UserController::class, 'deliveryNote']);
    Route::post('/deliverynote_submit', [UserController::class, 'deliveryNoteSubmit']);
    Route::get('/deliverynotereceipt/{transaction}', [UserController::class, 'deliveryNoteReceipt']);
    Route::get('/deliverynote_pdf/{transaction}', [PDFController::class, 'deliveryNotePDF']);

    // credit note
    Route::get('gettotalamount/{transaction_id}', [UserController::class, 'getTotalAmount']);

    Route::get('/credit_statement/{credit_id}/{start_date?}/{end_date?}', [PDFController::class, 'credit_statement_pdf'])->name('credit.statement.pdf');

    // credit supplier transaction pdf
    Route::get('/supplier_credit_statement/{supplier_credit_id}/{start_date?}/{end_date?}', [PDFController::class, 'credit_supplier_statement_pdf'])->name('supplier_credit.statement.pdf');

    Route::get('generatetax-pdf_a4/{transaction_id}', [PDFController::class, 'generatetaxPDFA4']);
    Route::get('/deliverynote_print/{transaction}', [PDFController::class, 'deliverynotePrint']);
    Route::get('/purhaseorderreceipt_print/{transaction}', [PDFController::class, 'purchaseOrderPrint']);
    Route::get('generatetax-pdf_a5/{transaction_id}', [PDFController::class, 'generatetaxPDFA5']);
    Route::get('generatetax-newsunmi/{transaction_id}', [PDFController::class, 'generatetaxNEWsunmi']);

    // history pages
    Route::get('/history/{page}', [UserController::class, 'salesOrder_DeliveryNoteHistory']);
    Route::get('historydetails/{page}/{transaction_id}', [UserController::class, 'viewSalesDelivery']);
    Route::get('/historyfilter_sales_delivery/{page}', [UserController::class, 'historyFilterSalesDelivery']);

    // purchase order history

    Route::get('/purchase_order_history/{page}', [UserController::class, 'purchaseOrderHistory']);
    Route::get('/purchase_order_details/{page}/{purchase_id}', [UserController::class, 'viewPurchaseOrderdetails']);
    Route::get('/download_purchase_order/{file}', [UserController::class, 'downloadPurchaseOrder']);
    Route::get('/purchaseorderhistorydate/{page}', [UserController::class, 'purchaseOrderHistorydate']);

    // add product modal
    Route::post('/add-product-modal', [UserController::class, 'addProductModal'])->name('add_product_modal');
    Route::get('/get-product-details/{id}', [UserController::class, 'getProductDetails'])->name('get_product_details');
    Route::get('/check-productname', [UserController::class, 'checkProductName']);
    Route::get('/newcheck_productname', [UserController::class, 'AddcheckProductName']);

    // admin supplier stock purchase report view products
    Route::get('/admin_purchase_products/{receipt_no}', [AdminController::class, 'viewProductsInSupplierPurchase']);
    // admin supplier purchase report pdf
    Route::get('/pdf_supplier_purchase_report/{supplier}/{payment_mode?}', [PDFController::class, 'PDFSupplierPurchaseReport']);

    // payment voucher
    Route::get('payment_voucher/{id}', [UserController::class, 'paymentVoucher']);
    Route::get('voucher_download/{supplier_id}/{id}', [PDFController::class, 'VoucherPaymentDownload']);

    // user-report pdf
    Route::get('/userreport_pdf_print/{reportid}', [PDFController::class, 'userReportPrintPDF']);

    // purchase order receipt check
    Route::get('/checkorderreceipt', [UserController::class, 'receiptno_purchaserder'])->name('checkorderreceipt');

    // edit transaction
    Route::get('/edittransactiondetails/{page}/{transaction_id}', [UserController::class, 'editviewTrans']);

    // purchase return print
    Route::get('purchasereturn-pdf/{receipt_no}/{created_at}', [PDFController::class, 'generatePurchaseReturnPrint']);

    // edit transaction page
    Route::get('/edittransactiondetails/{page}/{transaction_id}', [UserController::class, 'editviewTrans']);
    Route::post('/edittransactiondetails/{page}/editsubmitdata', [UserController::class, 'editsubmitData']);

    // sales order and quotation page

    Route::get('{page}', [UserController::class, 'sales_order_quot'])->where('page', 'sales_order|quotation|performance_invoice');
    Route::get('/receipt/{page}/{transaction}', [UserController::class, 'salesOrderReceipt']);
    Route::get('/salesorderreceipt_pdf/{page}/{transaction}', [PDFController::class, 'salesorderPDF']);
    Route::get('/salesorderreceipt_print/{page}/{transaction}', [PDFController::class, 'salesorderPrint']);

    // edit purchase page
    Route::get('/edit_purchasedetails/{page}/{receipt_no}', [UserController::class, 'editPurchase']);
    // edit puchase submit
    Route::post('/edit_purchasedetails/{page}/submit_editpurchase', [UserController::class, 'submitEditPurchase']);
    Route::post('/get-previous-selling-cost', [UserController::class, 'getPreviousSellingCost']);

    Route::get('/return_user_dat/{transaction_id}/{date}/{user_id}', [AdminController::class, 'viewUserReturns']);

    // to invoice
    Route::get('/to_invoice/{page}/{transaction_id}', [UserController::class, 'toInvoiceBillPage']);
    Route::post('/to_invoice/{page}/submitdata', [UserController::class, 'submitData']);

    // / pdf format print sunmi
    Route::get('sunmi_PDFPrint/{transaction_id}', [PDFController::class, 'generatetaxPDFsunmi']);

    // cash statement
    Route::get('cash_statement_transactions/{id}', [UserController::class, 'cashStatementTransactions']);
    Route::get('/cash_statement/{customer_id}/{start_date?}/{end_date?}', [PDFController::class, 'cash_statement_pdf'])->name('cash.statement.pdf');

    // edit sales order
    Route::get('/salesorder_edit/{page}/{transaction_id}', [UserController::class, 'editedsales']);
    Route::post('/edited/{transaction_id}', [UserController::class, 'editedsalesorder']);

    // billing draft................................................
    Route::get('/draft/{page}', [UserController::class, 'draft']);
    Route::post('/data/save-draft', [UserController::class, 'savedraft'])->name('data.saveDraft');
    Route::get('/editdraft/{page}/{transaction_id}', [UserController::class, 'editdraft']);

    // product draft.............................
    Route::post('/saveToDraft', [UserController::class, 'draftproductsubmit'])->name('data.saveproductdraft');
    Route::get('/toproduct/{draft_id}', [UserController::class, 'DraftToProduct']);
    Route::post('/submitproductdraft/edit/{draft_id}', [UserController::class, 'submitproductDataDraft']);
    Route::get('/delete/{id}', [UserController::class, 'deleteid']);

    // purchase draft..............................
    Route::post('/savepurchaseDraft', [UserController::class, 'draftpurchasesubmit'])->name('data.savepurchasedraft');
    Route::get('/editpurchasedraft/{page}/{reciept_no}', [UserController::class, 'editpurchasedraft']);
    Route::post('/submitpurchasedraft/{page}/{receipt_no}/submitstock_table', [UserController::class, 'purchaseTable']);

    // sales_performa_quotation_drafts......................................
    Route::post('/saveDraft', [UserController::class, 'draftsalessubmit'])->name('data.savesalesDraft');
    Route::get('/sales_order_draft/{page}/{transaction_id}', [UserController::class, 'salesorderdraft']); // salesorderdraft route
    Route::post('/submitsalesdraft/{transaction_id}', [UserController::class, 'salesorderSubmit']);

    // quatation draft...................................\
    Route::post('/savequotationDraft', [UserController::class, 'draftquotationsubmit'])->name('data.savequotationDraft');
    Route::get('/editquotationdraft/{page}/{transaction_id}', [UserController::class, 'salesorderdraft']);
    Route::post('/submitquotationdraft/{transaction_id}', [UserController::class, 'salesorderSubmit']);

    // performa draft...................................\
    Route::post('/saveperformaDraft', [UserController::class, 'draftperformasubmit'])->name('data.saveperformanceDraft');
    Route::get('/editperformadraft/{page}/{transaction_id}', [UserController::class, 'salesorderdraft']);
    Route::post('/submitperformadraft/{transaction_id}', [UserController::class, 'salesorderSubmit']);

    // delivery draft...................................\
    Route::post('/savedeliveryDraft', [UserController::class, 'draftdeliverysubmit'])->name('data.savedeliveryDraft');
    Route::get('/editdeliverydraft/{page}/{transaction_id}', [UserController::class, 'salesorderdraft']);
    Route::post('/submitdeliverydraft/{transaction_id}', [UserController::class, 'deliveryNoteSubmit']);

    // to purchase
    Route::get('/to_purchase/{page}/{receiptno}', [UserController::class, 'toPurchasePage']);
    Route::post('/to_purchase/{page}/submitstock_table', [UserController::class, 'purchaseTable']);

    // quotation to sales order............./
    Route::get('/to_salesorder/{page}/{transaction_id}', [UserController::class, 'salesorderdraft']);

    // credit user get products by transaction in ledger modal
    Route::get('/getproducts/{transactionId}', [UserController::class, 'getProductsByTransactionId']);

    // supplier Fund in Ledger fund............./
    // get supplier total amount
    Route::get('getpurchasetotalamount/{receipt_no}', [UserController::class, 'getPurchaseTotalAmount']);
    // Supplier get products by transaction in ledger modal
    Route::get('/getpurchaseproducts/{receipt_no}', [UserController::class, 'getProductsByReceiptNo']);
    Route::get('suppliercash_trans_history/{id}', [UserController::class, 'supplierCashTransactionHistory']);

    // cash stemenet pdf supplier
    Route::get('/supplier_cash_statement/{supplier_cash_id}/{start_date?}/{end_date?}', [PDFController::class, 'cash_supplier_statement_pdf'])->name('supplier_cash.statement.pdf');

    // user-report pdf
    Route::get('/userreport_pdf_print/{reportid}', [PDFController::class, 'userReportPrintPDF']);

    // restore
    Route::post('/restore-data', [SuperuserController::class, 'restoreData'])->name('restore-data');

    // quotation clone
    Route::get('/to_quotation/{page}/{transaction_id}', [UserController::class, 'toInvoiceBillPage'])->name('quotation-clone');


    // income and expense.....
    Route::get('/income', [UserController::class, 'incomeReport']);
    Route::post('/expensesubmit', [UserController::class, 'expensesubmit']);
    Route::post('/submittype', [UserController::class, 'submittype']);
    Route::post('/update-expense', [UserController::class, 'updateexpense'])->name('expense.update');
    Route::post('/update-income', [UserController::class, 'updateincome'])->name('income.update');




    //    bank
    Route::get('/bank', [UserController::class, 'bank']);
    Route::post('/banksubmit', [UserController::class, 'banksubmit']);
    Route::get('/listbank', [UserController::class, 'list'])->name('listbank');
    Route::get('/editbank/{id}', [UserController::class, 'editbank'])->name('bank.edit');
    Route::put('/updatebank/{id}', [UserController::class, 'updatebank'])->name('bank.update');
    Route::post('/toggle-bank-status/{id}', [UserController::class, 'toggleBankStatus'])->name('bank.toggleStatus');

    // fundtransfer
    Route::get('/fundtransfer', [UserController::class, 'fundtransfer']);
    Route::post('/fundtransfersubmit', [UserController::class, 'fundTransferSubmit'])->name('transfer.save');
    Route::post('/storetransfertype', [UserController::class, 'storeTransferType'])->name('storetransfertype');


    // bankreport
    Route::get('/bankreport', [UserController::class, 'bankreport']);
    Route::get('/bankreport-submit', [PDFController::class, 'bankReportSubmit'])->name('bankreport.submit');


    //employee
    Route::get('/employee', [UserController::class, 'employee']);
    Route::post('/employeesubmit', [UserController::class, 'employeesubmit'])->name('employee.store');;
    Route::get('/listemployee', [UserController::class, 'listemployee']);
    Route::post('/departments/store', [UserController::class, 'store'])->name('departments.store');
    Route::get('/employee/edit/{id}', [UserController::class, 'editemployee'])->name('employee.edit');
    Route::put('/employee/update/{id}', [UserController::class, 'updateemployee'])->name('employee.update');


    //reciept voucher
    Route::get('reciept_voucher/{id}', [UserController::class, 'recieptVoucher']);
    Route::get('recieptvoucher_download/{credit_id}/{id}', [PDFController::class, 'VoucherrecieptDownload']);
    
    
    Route::post('/approve-editbill', [UserController::class, 'editbillapprove']);
    Route::post('/approve-editpurchase', [UserController::class, 'editpurchaseapprove']);
    Route::get('customerstatus', [UserControllerNew::class, 'customerstatus']);
    Route::get('chequesummary', [UserControllerNew::class, 'chequesummary']);
    
    
    Route::get('/service', [UserController::class, 'service']);
    Route::post('/services', [UserController::class, 'stores'])->name('services.store');
    Route::get('/services/download/{id}', [PDFController::class, 'downloadRow'])->name('services.downloadRow');
    
    Route::get('/chartaccounts', [UserControllerNew::class, 'chartaccounts']);
    Route::post('/chartaccountssubmit', [UserControllerNew::class, 'storechartofaccounts'])->name('chartaccounts.submit');
    Route::get('/assethistory', [UserControllerNew::class, 'assethistory']);
    Route::get('/capitalhistory', [UserControllerNew::class, 'capitalhistory']);
    Route::get('/liabilityhistory', [UserControllerNew::class, 'liabilityhistory']);
    Route::get('/trailbalance', [UserControllerNew::class, 'trailbalance']);
    Route::get('balancesheet', [UserControllerNew::class, 'balancesheet']);
    Route::get('/balanceSheetfilter', [UserControllerNew::class, 'balanceSheetfilter'])->name('balanceSheetfilter');
    Route::get('journalentry', [UserControllerNew::class, 'journalentry']);
    Route::post('/journalentry/save', [UserControllerNew::class, 'saveJournalEntry'])->name('journalentry.save');
    
    Route::get('/daybook', [UserController::class, 'daybook']);
    Route::get('/daybookfilter', [UserController::class, 'daybookfilter'])->name('daybookfilter');
    Route::get('/daybook/pdf', [PDFController::class, 'downloadPDF'])->name('daybook.pdf');
    
    
    Route::get('/barcode/{purchase_id}', [UserControllerNew::class, 'barcode_print']);
    Route::get('/print-barcode', [UserControllerNew::class, 'printBarcode']);
    
    
    Route::get('productprofit', [UserControllerNew::class, 'productprofit']);
    Route::get('/product-profit', [UserControllerNew::class, 'productprofitfilter'])->name('product.profitfilter');
    Route::get('/exportproductreport', [UserControllerNew::class, 'exportproductreport'])->name('exportproductreport');
    Route::get('/printproductreport', [UserControllerNew::class, 'printproductreport'])->name('printproductreport');
    Route::get('/exportcategoryreport', [UserControllerNew::class, 'exportcategoryreport'])->name('exportcategoryreport');
    Route::get('/transactions/export', [UserControllerNew::class, 'exportTransactions']);
    Route::get('/exportstocklist', [UserControllerNew::class, 'exportStockListExcel'])->name('exportstocklist');
    Route::get('/printstocklist', [UserControllerNew::class, 'printStockList'])->name('printstocklist');
    
    
    Route::post('/customers/store', [UserControllerNew::class, 'customersstore'])->name('add.customer');
    Route::post('/supplier/store', [UserControllerNew::class, 'supplierstore'])->name('add.supplier');
    
    
    
    Route::get('/posbilling', [UserControllerNew::class, 'custombillingdashboardapk']);
    Route::post('/submitdatanew', [UserControllerNew::class, 'submitdatanew'])->name('data.submitdatanew');
    
    
    Route::get('/export-expense-history', [PDFController::class, 'exportExpenseHistory'])->name('export.expense.history');
    
    Route::get('/setting', [UserControllerNew::class, 'setting']);
    Route::post('/branch/update', [UserControllerNew::class, 'updateBranch'])->name('branch.update');
    
    Route::get('/calender', [UserControllerNew::class, 'calender']);
    
    Route::get('/servicebilling', [UserControllerNew::class, 'custombillingservicebilling']);
    Route::post('/submitservicedata', [UserControllerNew::class, 'submitservicedata'])->name('data.submitservicedata');
    
    Route::get('/payment_history_customer/{id}', [UserControllerNew::class, 'payment_history_customer']);
    Route::get('/payment_history_supplier/{id}', [UserControllerNew::class, 'payment_history_supplier']);
    
    Route::get('/receipt-voucher/{customer_id}/{transaction_id}', [PDFController::class, 'generateVoucher_customer'])->name('receipt.voucher');
    Route::get('/payment-voucher/{supplier_id}/{transaction_id}', [PDFController::class, 'generateVoucher_supplier'])->name('payment.voucher');

    Route::post('/cancel-c_payment', [UserControllerNew::class, 'cancelcustomerPayment'])->name('payment.cancel');
    Route::post('/cancel-s_payment', [UserControllerNew::class, 'cancelsupplierPayment'])->name('supplierpayment.cancel');
    
 //credit note
    Route::get('/creditnote', [CreditDebitController::class, 'creditnote']);
    Route::post('/get-invoice-details', [CreditDebitController::class, 'getInvoiceDetails'])->name('getInvoiceDetails');
    Route::post('/creditnotesubmit', [CreditDebitController::class, 'creditnotesubmit']);
    // Route::get('/getsoldquantity/{trans_id}/{pro_id}', [CreditDebitController::class, 'getsoldcreditquantity']);
    Route::get('/creditnotefinalreciept/{transaction}/{credit_note_id}', [CreditDebitController::class, 'creditnotereciept']);
    Route::get('/creditnote-pdf/{transaction_id}/{credit_note_id}', [CreditDebitController::class, 'creditnotePDF']);
    Route::get('/fetch-customers', [CreditDebitController::class, 'fetchCustomers']);
    Route::get('/creditnote_history', [CreditDebitController::class, 'creditNoteHistory']);
    Route::get('/creditnoteviewdetails/{credit_note_id}/{branch_id?}', [CreditDebitController::class, 'viewcreditnote']);
    Route::get('/customer_summary', [CreditDebitController::class, 'customerSummary']);




    
    Route::get('/debitnote', [CreditDebitController::class, 'debitnote']);
    Route::post('/getDebitInvoiceDetails', [CreditDebitController::class, 'getDebitInvoiceDetails'])->name('getDebitInvoiceDetails');
    // Route::get('/getremainstock_purchase/{receiptno}/{pro_id}', [CreditDebitController::class, 'getremainstock_purchase']);
    Route::post('debitnotesubmit', [CreditDebitController::class, 'debitnotesubmit']);
    Route::get('/debitnote_history', [CreditDebitController::class, 'DebitNoteHistory']);
    Route::get('/supplier_summary', [CreditDebitController::class, 'supplierSummary']);

    // api...................................
    Route::post('/loginsubmit', [UserController::class, 'loginsubmit']);
    Route::get('api/listcreditusers', [ApiController::class, 'listCreditusers']);
    Route::post('api/billing', [ApiController::class, 'billing']);
    Route::post('api/purchase', [ApiController::class, 'purchase']);
    Route::post('api/product', [ApiController::class, 'product']);
    Route::post('api/submitproduct', [ApiController::class, 'submitproduct']);
    Route::post('api/submitpurchase', [ApiController::class, 'submitpurchase']);
    Route::post('api/purchaseHistory', [ApiController::class, 'purchaseHistoryApi']);
    Route::post('api/submitunit', [ApiController::class, 'submitunit']);
    Route::post('api/submitcategory', [ApiController::class, 'submitcategory']);
    Route::post('api/submitsupplier', [ApiController::class, 'submitsupplier']);
    Route::post('api/submitbill', [ApiController::class, 'submitbill']);
    Route::post('api/submitcustomer', [ApiController::class, 'submitcustomer']);
    Route::post('api/listCategory', [ApiController::class, 'listCategory']);
    Route::post('api/dailysalesreport', [ApiController::class, 'dailysalesreport']);
    Route::post('api/dailysalesreportpayment', [ApiController::class, 'dailysalesreportpayment']);
    Route::post('api/billhistory', [ApiController::class, 'billhistory']);
    Route::post('api/billhistorydate', [ApiController::class, 'billhistorydate']); 
    Route::post('api/purchaseHistorydate', [ApiController::class, 'purchaseHistorydate']);
    Route::post('api/billhistoryview', [ApiController::class, 'billhistoryview']);
    Route::post('api/purchaseHistoryview', [ApiController::class, 'purchaseHistoryview']);

    Route::post('/zatca/generate-csr', [ZatcaController::class, 'generateCsr']);
    Route::post('/zatca/request-compliance/{otp}', [ZatcaController::class, 'requestCompliance']);



});
