<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Reciept</title>

    <!--  -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!--  -->
    @include('layouts/usersidebar')
</head>
<style>
    #footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 60px;
        /* Height of the footer */
    }
</style>
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #fafafa;
    }

    table.heading {
        border-collapse: collapse;
        width: 100%;
    }

    table.heading th,
    table.heading td {
        border: 1px solid white;
        text-align: left;
        padding: 8px;
    }

    table.heading th {
        background-color: white;
        color: white;
    }
</style>
<style>
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
        background-color: #20639B;
        color: white;
    }
</style>
<style>
    body {
        color: #2e323c;
        background: #f5f6fa;
        position: relative;
        height: 100%;
    }

    .invoice-container {
        padding: 1rem;
    }

    .invoice-container .invoice-header .invoice-logo {
        margin: 0.8rem 0 0 0;
        display: inline-block;
        font-size: 1.6rem;
        font-weight: 700;
        color: #2e323c;
    }

    .invoice-container .invoice-header .invoice-logo img {
        max-width: 130px;
    }

    .invoice-container .invoice-header address {
        font-size: 1.3 rem;
        color: #9fa8b9;
        margin: 0;
    }

    .invoice-container .invoice-details {
        margin: 1rem 0 0 0;
        padding: 1rem;
        line-height: 180%;
        background: #f5f6fa;
    }

    .invoice-container .invoice-details .invoice-num {
        text-align: right;
        font-size: 1.2rem;
    }

    .invoice-container .invoice-body {
        padding: 1rem 0 0 0;
    }

    .invoice-container .invoice-footer {
        text-align: center;
        font-size: 1.3rem;
        margin: 5px 0 0 0;
    }

    .invoice-status {
        text-align: center;
        padding: 1rem;
        background: #ffffff;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        margin-bottom: 1rem;
    }

    .invoice-status h2.status {
        margin: 0 0 0.8rem 0;
    }

    .invoice-status h5.status-title {
        margin: 0 0 0.8rem 0;
        color: #9fa8b9;
    }

    .invoice-status p.status-type {
        margin: 0.5rem 0 0 0;
        padding: 0;
        line-height: 150%;
    }

    .invoice-status i {
        font-size: 1.5rem;
        margin: 0 0 1rem 0;
        display: inline-block;
        padding: 1rem;
        background: #f5f6fa;
        -webkit-border-radius: 50px;
        -moz-border-radius: 50px;
        border-radius: 50px;
    }

    .invoice-status .badge {
        text-transform: uppercase;
    }

    @media (max-width: 767px) {
        .invoice-container {
            padding: 1rem;
        }
    }

    .custom-table {
        border: 1px solid #e0e3ec;
    }

    .custom-table thead {
        background: #007ae1;
    }

    .custom-table thead th {
        border: 0;
        color: #ffffff;
    }

    .custom-table>tbody tr:hover {
        background: #fafafa;
    }

    .custom-table>tbody tr:nth-of-type(even) {
        background-color: #ffffff;
    }

    .custom-table>tbody td {
        border: 1px solid #e6e9f0;
    }

    .card {
        background: #ffffff;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        border: 0;
        margin-bottom: 1rem;
    }

    .text-success {
        color: #20639B !important;
    }

    .text-muted {
        color: #9fa8b9 !important;
    }

    .custom-actions-btns {
        margin: auto;
        display: flex;
        justify-content: flex-end;
    }

    .custom-actions-btns .btn {
        margin: .3rem 0 .3rem .3rem;
    }
</style>
<style>
    @media print {
        body {
            visibility: hidden;
        }

        .print-container,
        .print-container * {
            visibility: visible;
        }
    }

    @page {
        size: A4;
        margin: 0;
    }

    .image {
        position: relative;
    }

    .image img {
        width: 400px;
        height: 109px;
        position: absolute;
        left: 50%;
    }
</style>

<style>
    .modal {
        position: fixed;
        top: 30%;
    }

    .modal-header {
        padding: 15px;
        border-bottom: 1px solid #ffffff;
    }

    .modal-content {
        padding-left: 5%;
        padding-right: 5%;
        padding-bottom: 5%;
    }

    .modal-lg {
        width: 40%;
    }

    #parent {
        margin-left: 10%;
        margin-right: 10%;
    }

    .modal-top-left {
        position: fixed;
        top: 0;
        left: 10%;
        transform: translate(0, 0);
    }
</style>

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

<!-- Page Content Holder -->
<div id="content">
    @if ($adminroles->contains('module_id', '30'))
    @include('navbar.billingdesknavbar')
@else
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
                <li><a href="/dashboard">Back</a></li>
            </ul>
        </div>
    </div>
</nav>
    @endif

    <body>
        <div align="right" id="butndiv">

            <button id="printButton" class="btn btn-primary">Rawilk Print</button>

            <!--<a href="/print-receipt" class="btn btn-primary">PRINT SUNMI</a>-->

            <!--original and windowonload-->
            <a href="/generatetax-pdfsunmi/{{ $trans }}" class="btn btn-primary">PRINT SUNMI</a>

            <!--webclientprint-->
            <!-- <a href="/printers/{{ $trans }}" class="btn btn-primary">PRINT RECEIPT</a> -->

            <!-- webclientprint with modal -->
            <!-- <button class="btn btn-primary printsunmibtn btn-sm" onclick="javascript: return false;" value="{{ $trans }}" id="sunmi_print">PRINT MODAL</button> -->

            <!-- webclientprint modal -->
            <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#printModal" data-trans="{{ $trans }}" id="myButton">
                Open Modal
            </button> -->

            <!--charlieuik demo-->
            <!--<a href="/print-receipt/{{ $trans }}" target="_blank" class="btn btn-primary">Print Receipt</a>-->


            <a href="/generatetax-pdf/{{ $trans }}" class="btn btn-primary">PRINT</a>
            <a href="/billdeskfinalrecieptwithouttax/{{ $trans }}" class="btn btn-primary">WITHOUT HEADER</a>
        </div>
        <br>
        <div class="print-container">
            <div class="row gutters">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="invoice-container">
                                <div class="invoice-header">
                                    <!-- Row start -->
                                    <div class="row gutters">
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">

                                        </div>
                                    </div>
                                    <!-- Row end -->
                                    <!-- Row start -->
                                    <div class="row gutters">
                                        <div class="image col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                            <img class="imagecss" src="{{ asset('storage/logo/logo.jpg') }}" alt="logo" style="padding-bottom:15px;">
                                            <br>
                                        </div>
                                        <div align="right" class="col-lg-6 col-md-6 col-sm-6">

                                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('https://avolon.netplexsolution.com/generatepublic-pdf/' . $enctrans, 'QRCODE') }}"
                                                alt="barcode" />
                                        </div>
                                    </div>

                                    <!--<div style="display: inline-block; text-align: left;">-->
                                        <!--<b>CR &nbsp;No&nbsp;&nbsp;:</b>{{ $cr_num }}-->

                                    <!--    <b>TR &nbsp;No&nbsp;&nbsp;:</b>{{ $admintrno }}-->
                                    <!--    <br>-->
                                    <!--    <b>PO BOX:</b>{{ $po_box }}-->
                                    <!--    <br>-->
                                    <!--    {{ $branchname }}-->
                                    <!--    <br>-->
                                    <!--    <b>TEL:</b>{{ $tel }}-->
                                    <!--</div>-->

                                    <div class="row gutters">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                            <div style="display: inline-block; text-align: left;">
                                                <!--<b>CR &nbsp;No&nbsp;&nbsp;:</b>{{ $cr_num }}-->
                                                <b>TR &nbsp;No&nbsp;&nbsp;: </b>{{ $admintrno }}
                                                <br>
                                                <b>PO BOX: </b>{{ $po_box }}
                                                <br>
                                                {{ $branchname }}
                                                <br>
                                                <b>TEL: </b>{{ $tel }}
                                            </div>
                                        </div>
                                        <div align="right" class="col-lg-6 col-md-6 col-sm-6">
                                            <div style="display: inline-block; text-align: left;">
                                                <b> Invoice No : </b>{{ $trans }}
                                                <br>
                                                <b> Customer :</b> {{ $custs }}
                                                <br>
                                                <b> TRN No :</b> {{ $trn_number }}
                                                <br>
                                                <b>Phone:</b>{{ $billphone }}
                                                <br>
                                                <b>E-Mail:</b>{{ $billemail }}
                                                <br>
                                            </div>
                                        </div>
                                    </div>

                                    <hr style="border-top: dotted 3px;color: #dddddd;" />
                                    </hr>
                                    <div align="center">
                                        <p style="font-family:'Poppins', sans-serif;font-size: 1.4em;background-color:#20639B;color: white;">
                                            INVOICE</p>
                                    </div>
                                    <!-- Row end -->
                                    <!-- Row start -->
                                    <table class="heading">
                                        <tr>
                                            <td>
                                                <div align="left">
                                                    <b> Invoice No : </b>{{ $trans }}
                                                    <br>
                                                    <b> Customer :</b> {{ $custs }}
                                                    <br>
                                                    <b> TRN No :</b> {{ $trn_number }}
                                                    <br>
                                                    <b>Phone:</b>{{ $billphone }}
                                                    <br>
                                                    <b>E-Mail:</b>{{ $billemail }}
                                                    <br>
                                                </div>
                                            </td>
                                            <td>
                                                <div align="right">
                                                    <b>
                                                        Invoice &nbsp;&nbsp;&nbsp;&nbsp;Date:</b> {{ $date }}
                                                    <br>
                                                    <b>Supplied Date:</b> {{ $supplieddate }}
                                                    <br>
                                                     @if ($payment_type == 'CREDIT')
                                                        <b>Payment
                                                            Type:</b>{{ $payment_type }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    @elseif ($payment_type == 'POS CARD')
                                                        <b>Payment Type:</b>
                                                        {{ $payment_type }}&nbsp;&nbsp;&nbsp;
                                                    @else
                                                        <b>Payment Type:</b>
                                                        {{ $payment_type }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    @endif
                                                </div>
                                            </td>
                                        <tr>
                                    </table>
                                    <!-- Row end -->
                                </div>
                                <div class="invoice-body">
                                    <!-- Row start -->
                                    <div class="row gutters">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table custom-table m-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Sl</th>
                                                            <th>Description</th>
                                                            <th>Quantity</th>
                                                            <th>Unit</th>
                                                            <th>Rate</th>
                                                            <th>{{$tax}} (%)</th>
                                                            <th>{{$tax}} Amount</th>
                                                            <th>Net Rate</th>
                                                            <th>Total Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- product details -->
                                                        <?php $number = 1; ?>
                                                        @foreach ($details as $detail)
                                                        <tr>
                                                            <td>{{ $number }}</td>
                                                            <td>{{ $detail->product_name }}</td>
                                                            <td>{{ $detail->quantity }}</td>
                                                            <td>{{ $detail->unit }}</td>
                                                            <td>{{ $detail->mrp }}</td>
                                                            <td>{{ $detail->fixed_vat }}</td>
                                                            <td>{{ number_format(($detail->fixed_vat * $detail->price) / 100, 3) }}
                                                            </td>
                                                            <td>{{ number_format($detail->total_amount / $detail->quantity, 3) }}
                                                            </td>
                                                            <td>{{ $detail->total_amount }}</td>
                                                        </tr>
                                                        <?php $number++; ?>
                                                        @endforeach
                                                        <!-- total -->
                                                        <tr>
                                                            <td colspan="6">
                                                                <p>
                                                                    <br>
                                                                    <br>
                                                                </p>
                                                                <h5 class="text-success"><strong>Amount in Words :
                                                                        {{ $amountinwords }}</strong></h5>
                                                            </td>
                                                            <td>
                                                                <p>
                                                                    Subtotal<br>
                                                                    {{$tax}}<br>
                                                                </p>
                                                                <h5 class="text-success"><strong>Grand Total</strong>
                                                                </h5>
                                                            </td>
                                                            <td>
                                                                <p>
                                                                    {{ $grandinnumber - $vat }}<br>
                                                                    {{ $vat }}<br>
                                                                </p>
                                                                <h5 class="text-success"><strong>{{ $grandinnumber }}
                                                                        &nbsp{{ $currency }}</strong></h5>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Row end -->
                                </div>
                                <div class="invoice-footer">
                                    <br>
                                    <br>
                                    <br>
                                    The above mentioned goods are recieved in good condition.
                                    Goods once sold will not be taken back or exchanged in any condition.
                                    <BR>
                                    <BR>
                                    <BR>
                                    <BR>
                                    <div class="row gutters">
                                        <div class="col-xs-6">
                                            Reciever's Signature:
                                        </div>
                                        <div class="col-xs-6">
                                            Seller's Signature:
                                        </div>
                                        <br>
                                        <br>
                                        <br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </body>
    @include('modal.printers')

</html>
<script>
    var array = "{{ $trans }}";
    $("#trans_id").val(array);
</script>

<!-- -------------------------------------------------------------------------- -->

<!-- RAWILK PRINT WITH MODAL DISPLAY ONLY ONE TIME -->

<script>
    $(document).ready(function() {
        var userid = "<?php echo $user_id; ?>";
        var branch = "<?php echo $branch; ?>";
        var trans_id = "<?php echo $trans; ?>";

        $.ajax({
            type: 'get',
            url: '/rawilk_get_status_printer/' + userid + '/' + branch,
            success: function(data) {

                console.log(data.data);

                var printer_data = data.data;

                if (printer_data != null) {

                    if (printer_data.status == "online") {

                        $('#printButton').click(function() {

                            $('#PrinterM').modal("hide");

                            var data =
                                '<div id="raw_div" style="display:none;"><input type="text" id="printersecond" value="' +
                                printer_data.printer_id + '"></div>';
                            $('body #butndiv ').append(data);


                            var parameter1 = trans_id;
                            var parameter2 = printer_data.printer_id;

                            var url =
                                "{{ route('rawilk_second', [':param1', ':param2']) }}";
                            url = url.replace(':param1', parameter1);
                            url = url.replace(':param2', parameter2);
                            window.location.href = url;

                            console.log("hided");
                        });
                    }
                } else {

                    $('#printButton').click(function() {
                        $.ajax({
                            url: '/rawilk-get-printer',
                            type: 'GET',
                            success: function(response) {
                                console.log(response.printers);

                                var printers = response.printers;

                                var dropdown = $(
                                    '#printerdrop'
                                ); // Select the dropdown element

                                dropdown.empty(); // Clear existing options

                                var
                                    addedPrinters = {}; // Object to track added printers

                                // Iterate over the printers array and add options to the dropdown
                                for (var i = 0; i < printers.length; i++) {
                                    var printer = printers[i];
                                    if (printer.status === 'online' && !
                                        addedPrinters[printer.name]) {
                                        dropdown.append($('<option></option>')
                                            .attr('value', printer.id).text(
                                                printer.name));
                                        addedPrinters[printer.name] =
                                            true; // Mark the printer as added

                                    }
                                }

                                $('#PrinterM').modal('show');

                                $('#trans_id').val(trans_id);

                            },
                            error: function(xhr, status, error) {
                                console.log(xhr.responseText);
                            }
                        });
                    });
                }
            }
        });

    });
</script>


<!-- ---------------- -->


<!--The modal -->
<div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="printModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-top-left" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closebtn">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="defcode" class="">
                    <div id="msgInProgress">

                        <h3>Detecting WCPP utility at client side...</h3>
                        <h3>Please wait a few seconds...</h3>
                        <br />
                    </div>
                    <div id="msgInstallWCPP" style="display:none;">
                        <h3>#1 Install WebClientPrint Processor (WCPP)!</h3>
                        <p>
                            <strong>WCPP is a native app (without any dependencies!)</strong> that handles all print
                            jobs
                            generated by the <strong>WebClientPrint for PHP component</strong> at the server side. The
                            WCPP
                            is in charge of the whole printing process and can be
                            installed on <strong>Windows, Linux, Mac & Raspberry Pi!</strong>
                        </p>
                        <p>
                            <a href="//www.neodynamic.com/downloads/wcpp/" target="_blank">Download and Install WCPP
                                from Neodynamic website</a><br />
                        </p>
                        <h3>#2 After installing WCPP...</h3>
                        <p>
                            <a href="{{ route('printESCPOS', $trans) }}">You can go and test the printing page...</a>
                        </p>
                    </div>

                </div>

                <script type="text/javascript">
                    var wcppPingTimeout_ms = 60000; //60 sec
                    var wcppPingTimeoutStep_ms = 500; //0.5 sec

                    function wcppDetectOnSuccess() {
                        // WCPP utility is installed at the client side
                        // redirect to WebClientPrint sample page

                        // get WCPP version
                        var wcppVer = arguments[0];
                        if (wcppVer.substring(0, 1) == "6") {

                            var trans = "<?php echo $trans; ?>";

                            $.ajax({
                                url: '/getWcpScript/' + trans,
                                success: function(response) {
                                    var wcpScript = response.wcpScript;
                                    var modalBody = $('#printModal .modal-body').html(wcpScript);

                                    var content = `
										<div id="printer" >
											<label class="checkbox">
											<input type="checkbox" id="useDefaultPrinter" class="prevent-modal-open-checkbox" /> <strong>Use default printer</strong> or...
											</label>
											<div id="loadPrinters">
											<br />
											WebClientPrint can detect the installed printers in your machine.
											<br /> <br />
											<input type="button" onclick="javascript:jsWebClientPrint.getPrinters();" value="Load installed printers..." id="loadprinter" class="prevent-modal-open-button" />

											<br /><br />
											</div>
											<div id="installedPrinters" style="visibility:hidden">
											<br />
											<label for="installedPrinterName">Select an installed Printer:</label>
											<select name="installedPrinterName" id="installedPrinterName"></select>
											</div>

											<br /><br />

											<input type="button" style="font-size:18px" onclick="javascript:jsWebClientPrint.print('useDefaultPrinter=' + $('#useDefaultPrinter').attr('checked') + '&printerName=' + $('#installedPrinterName').val());" value="WebClient Print" class="btn btn-primary" id="printreceipt" />
										</div>`;

                                    modalBody.append(content);

                                    /*-----------------------------------------------------------------------*/

                                    $(document).ready(function() {
                                        $('#printreceipt').on('click', function() {
                                            $('#printModal').modal('hide');
                                            // Check if the checkbox or dropdown displaying button were clicked previously
                                            if (localStorage.getItem('modalState') === 'closed') {
                                                $('#printModal').modal('hide'); // Hide the modal
                                            }

                                            // Open Modal button click event
                                            $('#myButton').click(function() {
                                                // Check if the checkbox or dropdown displaying button were clicked previously
                                                if (localStorage.getItem('modalState') ===
                                                    'closed') {
                                                    return; // Don't open the modal
                                                }

                                                // Open the modal
                                                $('#printModal').modal('show');
                                            });

                                            // Close Modal button click event
                                            $('#closebtn').click(function() {
                                                // Close the modal
                                                $('#printModal').modal('hide');

                                                // Store the modal state as closed in local storage
                                                localStorage.setItem('modalState', 'closed');
                                            });

                                            // Event delegation for checkbox and display dropdown button
                                            $('#printModal').on('click', '#useDefaultPrinter, #loadprinter',
                                                function() {
                                                    // Store the modal state as closed in local storage
                                                    localStorage.setItem('modalState', 'closed');
                                                });
                                        });
                                    });

                                    /*-----------------------------------------------------------------------*/

                                    // Add the appropriate event listener for the modal
                                    $('#printModal').on('show.bs.modal', function(event) {
                                        var button = $(event.relatedTarget);
                                        var trans = button.data('trans');

                                        // var wcpScript = modalBody.html();
                                        if (wcpScript.trim() === '') {
                                            // Make AJAX request to get the WCP script
                                            $.ajax({
                                                url: '/getWcpScript/' + trans,
                                                success: function(response) {
                                                    var wcpScript = response.wcpScript;
                                                    modalBody.html(wcpScript);

                                                },
                                                error: function() {
                                                    // Handle error case
                                                    modalBody.html(
                                                        '<p>Error retrieving WCP script.</p>');
                                                }
                                            });
                                        }
                                    });
                                },
                                error: function() {
                                    // Handle error case
                                    $('#printModal .modal-body').html('<p>Error retrieving WCP script.</p>');
                                }
                            });

                        } else { //force to install WCPP v6.0
                            wcppDetectOnFailure();
                        }
                    }

                    function wcppDetectOnFailure() {
                        // It seems WCPP is not installed at the client side
                        // ask the user to install it
                        $('#msgInProgress').hide();
                        $('#msgInstallWCPP').show();
                    }
                </script>
            </div>
        </div>
    </div>
    {!! $wcppScript !!}

</div>

<script type="text/javascript">
    var wcppGetPrintersTimeout_ms = 60000; //60 sec
    var wcppGetPrintersTimeoutStep_ms = 500; //0.5 sec

    function wcpGetPrintersOnSuccess() {
        // Display client installed printers
        if (arguments[0].length > 0) {
            var p = arguments[0].split("|");
            var options = '';
            for (var i = 0; i < p.length; i++) {
                options += '<option>' + p[i] + '</option>';
            }
            $('#installedPrinters').css('visibility', 'visible');
            $('#installedPrinterName').html(options);
            $('#installedPrinterName').focus();
            $('#loadPrinters').hide();
        } else {
            alert("No printers are installed in your system.");
        }
    }

    function wcpGetPrintersOnFailure() {
        // Do something if printers cannot be got from the client
        alert("No printers are installed in your system.");
    }
</script>

<!--------------------------------------------------------------------------------->

<script>
    // Event delegation for the printreceipt button
    $(document).on('click', '#printModal .modal-body #printer #printreceipt', function() {
        console.log('printreceipt button clicked');
        $.ajax({
            type: 'post',
            url: '/savePrinterStatus',
            data: {
                _token: '{{ csrf_token() }}', // Include the CSRF token
            },
            success: function(data) {
                console.log(data);
            }
        });

    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        var userid = "<?php echo $user_id; ?>";
        var branch = "<?php echo $branch; ?>";

        $.ajax({
            type: 'get',
            url: '/getPrinterStatus/' + userid + '/' + branch,
            success: function(data) {

                var printState = data.printstatus;

                if (printState == "found") {

                    var data =
                        `<div id="secondprint" style="display:none"></div><input type="button" id="secondButton" onclick="javascript:jsWebClientPrint.print('useDefaultPrinter=' + $('#useDefaultPrinter').attr('checked') + '&printerName=' + $('#installedPrinterName').val());" value="WebClient Print" class="btn btn-primary" />`;
                    $('body #butndiv').append(data);

                    var t = "<?php echo $trans; ?>";
                    $.ajax({
                        type: 'get',
                        url: '/getWcpScript/' + t,
                        success: function(res) {
                            var wcpScript = res.wcpScript;
                            $('#secondprint').html(wcpScript);
                        }

                    });
                } else if (printState == null) {
                    var data =
                        `<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#printModal" data-trans="{{ $trans }}" id="myButton">WebClient Print</button>`;
                    $('body #butndiv').append(data);
                }
            }
        });
    });
</script>
