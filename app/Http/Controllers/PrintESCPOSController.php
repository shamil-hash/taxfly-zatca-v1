<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;


use Illuminate\Support\Facades\Log;

//*********************************
// IMPORTANT NOTE 
// ==============
// If your website requires user authentication, then
// THIS FILE MUST be set to ALLOW ANONYMOUS access!!!
//
//*********************************

//Includes WebClientPrint classes
// include_once(app_path() . '\WebClientPrint\WebClientPrint.php');

include_once(app_path() . '/WebClientPrint/WebClientPrint.php');

use Neodynamic\SDK\Web\WebClientPrint;
use Neodynamic\SDK\Web\Utils;
use Neodynamic\SDK\Web\DefaultPrinter;
use Neodynamic\SDK\Web\InstalledPrinter;
use Neodynamic\SDK\Web\PrintFile;
use Neodynamic\SDK\Web\ClientPrintJob;

use Illuminate\Support\Facades\Session;


class PrintESCPOSController extends Controller
{
    public function printCommands(Request $request, $trans, $adminname, $po_box, $branchname, $tel, $grandinnumber, $vat, $trn_number, $custs, $date, $supplieddate, $payment_type)
    {

        // Retrieve the dataplan parameter from the query string
        $dataplan = json_decode($request->query('dataplan'));

        if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {

            $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
            $printerName = urldecode($request->input('printerName'));

            //Create ESC/POS commands for sample receipt
            $esc = '0x1B'; //ESC byte in hex notation
            $newLine = '0x0A'; //LF byte in hex notation

            // -------------------------HEADER PART --------------------------------
            $cmds = '';
            $cmds = $esc . "@"; //Initializes the printer (ESC @)

            $cmds .= $esc . '!' . '0x38'; //Emphasized + Double-height + Double-width mode selected (ESC ! (8 + 16 + 32)) 56 dec => 38 hex

            $cmds .= $esc . 'a' . '1';
            $cmds .= $esc . 'k' . strtoupper($adminname); // Print $adminname in uppercase
            $cmds .= $esc . '!' . '0x06'; // Character font A selected (ESC ! 0)
            $cmds .= $newLine;

            // Set font, font size, and line height for $po_box
            $cmds .= $esc . '!' . '0x10'; // Turn on emphasized mode (bold)
            $cmds .= $esc . '!' . '0x05'; // Character font B selected (ESC ! 1)
            $cmds .= 'Branch: ' . $branchname;
            $cmds .= $newLine;

            $cmds .= 'P O BOX:' . $po_box; //text to print
            $cmds .= $newLine;
            $cmds .= 'TEL:' . $tel; //text to print

            $cmds .= $newLine;
            if ($trn_number != '0') {
                $cmds .= 'TRN:' . $trn_number; //text to print
            }

            $cmds .= $newLine . $newLine;

            $cmds .= $esc . '!' . '0x13'; // Turn on emphasized mode (bold)
            $cmds .= 'RECEIPT';

            $cmds .= $newLine;
            $cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)
            // Add a dotted line using repetitive characters
            $dottedLineLength = 45; // Define the length of the dotted line
            $dottedLine = str_repeat('-', $dottedLineLength);
            $cmds .= $dottedLine;

            $cmds .= $newLine . $newLine;

            $cmds .= $esc . 'a' . '0';

            // ------------------------END HEADER PART ----------------------------------

            $leftMargin = 2;

            $cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)

            $cmds .= $esc . '!' . '0x08';
            $cmds .= str_repeat(' ', $leftMargin) . 'Invoice No. : ' . $trans;
            $cmds .= $newLine;

            $cmds .= str_repeat(' ', $leftMargin) . 'Customer No. : ' . $custs;
            $cmds .= $newLine;

            $cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)
            // Add a dotted line using repetitive characters
            $dottedLineLength = 45; // Define the length of the dotted line
            $dottedLine = str_repeat('-', $dottedLineLength);
            $cmds .= $dottedLine;

            $cmds .= $newLine;
            $cmds .= $esc . '!' . '0x08';
            $cmds .= str_repeat(' ', $leftMargin) . 'Invoice Date : ' . $date;
            $cmds .= $newLine;
            $cmds .= str_repeat(' ', $leftMargin) . 'Supplied Date : ' . $supplieddate;
            $cmds .= $newLine;
            if ($payment_type == "CREDIT") {
                $payment = $payment_type;
            } elseif ($payment_type == "CASH") {
                $payment = $payment_type;
            } elseif ($payment_type == "BANK") {
                $payment = $payment_type;
            }
            $cmds .= str_repeat(' ', $leftMargin) . 'Payment : ' . $payment;
            $cmds .= $newLine;

            $cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)
            // Add a dotted line using repetitive characters
            $dottedLineLength = 45; // Define the length of the dotted line
            $dottedLine = str_repeat('-', $dottedLineLength);
            $cmds .= $dottedLine;
            // --------------------------BODY PART ------------------------------------

            // $cmds .= 'Transaction ID           ' . $trans;
            $cmds .= $newLine . $newLine;

            $cmds .= $esc . 'a' . '1';

            $cmds .= $esc . '!' . '0x01'; //Character font A selected (ESC ! 0)
            $cmds .= $esc . '!' . '0x08';
            $cmds .= 'Item         Quantity     Rate       Total';
            $cmds .=  $newLine;

            $cmds .= '0x20 0x20 0x20 0x20 0x20 0x20 0x20 0x20';

            $cmds .= str_pad('(With VAT)', 32, ' ', STR_PAD_LEFT); // Add padding for alignment
            $cmds .= $newLine . $newLine;

            $cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)

            $number = 1;
            foreach ($dataplan as $details) {
                $cmds .= $details->product_name . '      ' . (int)$details->quantity . $details->unit . '         ' . $details->mrp . '     ' . $details->total_amount;
                $cmds .= $newLine;

                $noofitems = $number;
                $number++;
            }

            $cmds .= $newLine;
            // $cmds .= '--------------------------------------------';

            // Add a dotted line using repetitive characters
            $dottedLineLength = 45; // Define the length of the dotted line
            $dottedLine = str_repeat('-', $dottedLineLength);
            $cmds .= $dottedLine;

            $cmds .= $newLine;


            $cmds .= $esc . '!' . '0x08';
            $cmds .= '0x20 0x20 0x20';
            $cmds .= str_pad('Sub Total', 29, ' ', STR_PAD_LEFT) . '   ' . $grandinnumber - $vat;

            $cmds .= $newLine;

            // Add a dotted line using repetitive characters
            $dottedLineLength = 45; // Define the length of the dotted line
            $dottedLine = str_repeat('-', $dottedLineLength);
            $cmds .= $dottedLine;
            $cmds .= $newLine;

            $cmds .= str_pad('VAT', 28, ' ', STR_PAD_LEFT) . '         ' . $vat;

            $cmds .= $newLine . $newLine;
            // Add a dotted line using repetitive characters
            $dottedLineLength = 45; // Define the length of the dotted line
            $dottedLine = str_repeat('-', $dottedLineLength);
            $cmds .= $dottedLine;
            $cmds .= $newLine;

            $cmds .= $esc . '!' . '0x00';
            $cmds .= 'No. of Items:' . $noofitems;
            $cmds .= $esc . '!' . '0x17';
            $cmds .= str_pad('Grand Total', 25, ' ', STR_PAD_LEFT) . '   ' . $grandinnumber;
            $cmds .= $newLine . $newLine . $newLine . $newLine;


            $cmds .= $esc . 'a' . '1';
            $cmds .= $esc . '!' . '0x18';
            $cmds .= '*** THANK YOU VISIT AGAIN ***';

            $cmds .= $newLine . $newLine;
            $cmds .= $esc . 'a' . '1';
            $cmds .= $esc . '!' . '0x00';
            $cmds .= $esc . '!' . '0x08';
            $cmds .= date('j F Y H:i:s');


            // -------------- END BODY PART ---------------------------


            $cmds .= $newLine . $newLine;
            // $cmds .= $esc . '!' . '0x18'; //Emphasized + Double-height mode selected (ESC ! (16 + 8)) 24 dec => 18 hex
            // $cmds .= '# ITEMS SOLD 2';
            // $cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)
            $cmds .= $newLine . $newLine;


            $cmds .= $newLine;
            $cmds .= '0x1D0x560x00'; //paper cut

            // $cmds .= '0x1D0x560x410xXX';//paper cut


            //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
            $cpj = new ClientPrintJob();
            //set ESCPOS commands to print...
            $cpj->printerCommands = $cmds;
            $cpj->formatHexValues = true;;

            if ($useDefaultPrinter || $printerName === 'null') {
                $cpj->clientPrinter = new DefaultPrinter();
            } else {
                $cpj->clientPrinter = new InstalledPrinter($printerName);
            }


            //Send ClientPrintJob back to the client
            return response($cpj->sendToClient())
                ->header('Content-Type', 'application/octet-stream');
        }
    }
}
