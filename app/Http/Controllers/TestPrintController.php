<?php

namespace App\Http\Controllers;

use App\Models\Adminuser;
use App\Models\Buyproduct;
use App\Models\RawilkPrint;
use App\Models\Softwareuser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Rawilk\Printing\Facades\Printing;
use Rawilk\Printing\Receipts\ReceiptPrinter;

use Rawilk\Printing\Contracts\Printer;


class TestPrintController extends Controller
{
   
    public function showPrinters()
    {

        $printers = Printing::printers();

        return response()->json([
            'printers' => $printers
        ]);
    }
    
    public function printReceipt(Request $request)
    {

        $userid = Session('softwareuser');

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $dataplan = DB::table('buyproducts')
            ->select(DB::raw("buyproducts.product_name as product_name,buyproducts.product_id as product_id,CAST(buyproducts.quantity as SIGNED) as quantity,buyproducts.mrp as mrp,buyproducts.price as price,buyproducts.fixed_vat as fixed_vat,buyproducts.vat_amount as vat_amount,buyproducts.total_amount as total_amount, buyproducts.unit as unit"),)
            ->where('buyproducts.transaction_id', $request->trans_id)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $adminname = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();

        $po_box = Adminuser::Where('id', $adminid)
            ->pluck('po_box')
            ->first();

        $tel = Adminuser::Where('id', $adminid)
            ->pluck('phone')
            ->first();

        $branchname = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();

        $custs = DB::table('buyproducts')
            ->where('transaction_id', $request->trans_id)
            ->pluck('customer_name')
            ->first();

        $trn_number = DB::table('buyproducts')
            ->where('transaction_id', $request->trans_id)
            ->pluck('trn_number')
            ->first();

        $supplieddate = Carbon::now()->format('d-m-Y');

        $date = DB::table('buyproducts')
            ->select(DB::raw("DATE(buyproducts.created_at) as date"),)
            ->where('transaction_id', $request->trans_id)
            ->pluck('date')
            ->first();

        $date = Carbon::parse($date)->format('d-m-Y');

        $payment_type = DB::table('buyproducts')
            ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
            ->select(DB::raw("payment.type as payment_type"))
            ->where('buyproducts.transaction_id', $request->trans_id)
            ->pluck('payment_type')
            ->first();

        $grand = Buyproduct::where('transaction_id', $request->trans_id)
            ->select(DB::raw("SUM(total_amount) as total_amount"),)
            ->pluck('total_amount')
            ->first();

        $grandinnumber = $grand;

        $vat = Buyproduct::select(
            DB::raw("SUM(vat_amount) as vat"),
        )
            ->where('transaction_id', $request->trans_id)
            ->pluck('vat')
            ->first();
        /*---------------------------------------------------*/


        $existingData = DB::table('rawilk_prints')
            ->where('user_id', $userid)
            ->where('branch', $branch)
            ->count();

        $printerdata = DB::table('rawilk_prints')
            ->where('user_id', $userid)
            ->where('branch', $branch)
            ->first();

        if (($payment_type) == "CREDIT") {

            $payment = $payment_type;
        } elseif (($payment_type) == "CASH") {
            $payment = $payment_type;
        } elseif (($payment_type) == "BANK") {
            $payment = $payment_type;
        } elseif (($payment_type) == "POS CARD") {
            $payment = $payment_type;
        }


        $dataArray = $dataplan->toArray();

        $tableData = [
            ['Item', 'Quantity', 'Rate ', 'Total'],
        ];

        foreach ($dataArray as $data) {
            $tableData[] = [
                $data->product_name,
                $data->quantity . $data->unit,
                $data->mrp,
                $data->price,
                // $data->unit,
            ];
        }

        $tableString = '';

        foreach ($tableData as $row) {
            $tableString .= sprintf(
                "\n%-15s  %8s  %5s  %8s\n",
                $row[0],
                $row[1],
                $row[2],
                $row[3]

            );
        }

        // Get the number of items
        $itemCount = count($dataArray);

        $currentDateTime = date('j F Y H:i:s');

        // First generate the receipt
        $text = (string) (new ReceiptPrinter)
            ->leftMargin(0)
            ->centerAlign()
            ->setEmphasis(true)
            ->setTextSize(1, 2)
            ->text(strtoupper($adminname))
            ->setEmphasis(false)
            ->setTextSize(1, 1)
            ->text('Branch: ' . $branchname)
            ->text('P O Box: ' . $po_box)
            ->text('Tel: ' . $tel)
            ->feed(2)
            ->setEmphasis(true)
            ->text('RECEIPT')
            ->setEmphasis(false)
            ->line()
            ->leftAlign()
            ->text('Invoice No.:' . $request->trans_id)
            ->text('Customer No.:' . $custs)
            ->centerAlign()
            ->line()
            ->leftAlign()
            ->text('Invoice Date:' . $date)
            ->text('Supplied Date:' . $supplieddate)
            ->text('Payment:' . $payment)
            ->centerAlign()
            ->line()
            ->leftAlign()
            ->text($tableString)
            ->centerAlign()
            ->line()
            ->leftAlign()
            ->setEmphasis(true)
            ->text(sprintf("\n%-15s  %8s  %5s  %5s\n", '', '', 'Subtotal:', $grandinnumber - $vat))
            ->setEmphasis(false)
            ->centerAlign()
            ->line()
            ->leftAlign()
            ->setEmphasis(true)
            ->text(sprintf("\n%-15s  %8s  %5s  %5s\n", '', '', 'VAT     :', $vat))
            ->setEmphasis(false)
            ->centerAlign()
            ->line()
            ->leftAlign()
            ->setEmphasis(true)
            ->text(sprintf("\n%-15s  %5s  %5s  %5s\n", 'No. of Items:', $itemCount, 'Grand Total:', $grandinnumber))
            ->setEmphasis(false)
            ->feed(2)
            ->centerAlign()
            ->setEmphasis(true)
            ->setTextSize(1, 2)
            ->text('*** THANK YOU VISIT AGAIN ***')
            ->setEmphasis(false)
            ->setTextSize(1, 1)
            ->feed(1)
            ->text($currentDateTime)
            ->cut();


        // Now send the string to your receipt printer
        Printing::newPrintTask()
            // ->printer("72446283")
            ->printer($request->printerdrop)
            ->content($text)
            ->send();

        $printerData = Printing::printer($request->printerdrop);
        $printerName = $printerData->name();
        $printerStatus = $printerData->status();


        if ($existingData == 0 || $printerdata->status != "online") {

            $data = new RawilkPrint();
            $data->user_id = $userid;
            $data->branch = $branch;
            $data->printername = $printerName;
            $data->printer_id = $request->printerdrop;
            $data->status  = $printerStatus;
            $data->save();

            $old = DB::table('rawilk_prints')
                ->where('user_id', $userid)
                ->where('branch', $branch)
                ->where('status', '!=', 'online')
                ->delete();
        }

        return redirect()->back();
    }
    
    public function rawilk_get_status($user_id, $branch)
    {
        $data = DB::table('rawilk_prints')
            ->where('user_id', $user_id)
            ->where('branch', $branch)
            ->where('status', 'online')
            ->first();

        return response()->json([
            'data' => $data,
            'branch' => $branch
        ]);
    }

    public function printReceiptSecond($trans_id, $printerId)
    {
        $userid = Session('softwareuser');

        $branch = DB::table('softwareusers')
            ->where('id', Session('softwareuser'))
            ->pluck('location')
            ->first();

        $dataplan = DB::table('buyproducts')
            ->select(DB::raw("buyproducts.product_name as product_name,buyproducts.product_id as product_id,CAST(buyproducts.quantity as SIGNED) as quantity,buyproducts.mrp as mrp,buyproducts.price as price,buyproducts.fixed_vat as fixed_vat,buyproducts.vat_amount as vat_amount,buyproducts.total_amount as total_amount, buyproducts.unit as unit"))
            ->where('buyproducts.transaction_id', $trans_id)
            ->get();

        $adminid = Softwareuser::Where('id', $userid)
            ->pluck('admin_id')
            ->first();
        $adminname = Adminuser::Where('id', $adminid)
            ->pluck('name')
            ->first();

        $po_box = Adminuser::Where('id', $adminid)
            ->pluck('po_box')
            ->first();

        $tel = Adminuser::Where('id', $adminid)
            ->pluck('phone')
            ->first();

        $branchname = DB::table('branches')
            ->where('id', $branch)
            ->pluck('location')
            ->first();

        $custs = DB::table('buyproducts')
            ->where('transaction_id', $trans_id)
            ->pluck('customer_name')
            ->first();

        $trn_number = DB::table('buyproducts')
            ->where('transaction_id', $trans_id)
            ->pluck('trn_number')
            ->first();

        $supplieddate = Carbon::now()->format('d-m-Y');

        $date = DB::table('buyproducts')
            ->select(DB::raw("DATE(buyproducts.created_at) as date"))
            ->where('transaction_id', $trans_id)
            ->pluck('date')
            ->first();

        $date = Carbon::parse($date)->format('d-m-Y');

        $payment_type = DB::table('buyproducts')
            ->leftJoin('payment', 'buyproducts.payment_type', '=', 'payment.id')
            ->select(DB::raw("payment.type as payment_type"))
            ->where('buyproducts.transaction_id', $trans_id)
            ->pluck('payment_type')
            ->first();

        $grand = Buyproduct::where('transaction_id', $trans_id)
            ->select(DB::raw("SUM(total_amount) as total_amount"))
            ->pluck('total_amount')
            ->first();

        $grandinnumber = $grand;

        $vat = Buyproduct::select(
            DB::raw("SUM(vat_amount) as vat"))
            ->where('transaction_id', $trans_id)
            ->pluck('vat')
            ->first();
        /*---------------------------------------------------*/

        $existingData = DB::table('rawilk_prints')
            ->where('user_id', $userid)
            ->where('branch', $branch)
            ->count();

        $printerdata = DB::table('rawilk_prints')
            ->where('user_id', $userid)
            ->where('branch', $branch)
            ->first();

        if (($payment_type) == "CREDIT") {

            $payment = $payment_type;
        } elseif (($payment_type) == "CASH") {
            $payment = $payment_type;
        } elseif (($payment_type) == "BANK") {
            $payment = $payment_type;
        } elseif (($payment_type) == "POS CARD") {
            $payment = $payment_type;
        }


        $dataArray = $dataplan->toArray();

        $tableData = [
            ['Item', 'Quantity', 'Rate ', 'Total'],
        ];

        foreach ($dataArray as $data) {
            $tableData[] = [
                $data->product_name,
                $data->quantity.$data->unit,
                $data->mrp,
                $data->price,
                // $data->unit,
            ];
        }

        $tableString = '';

        foreach ($tableData as $row) {
            $tableString .= sprintf(
                "\n%-15s  %8s  %5s  %8s\n",
                $row[0],
                $row[1],
                $row[2],
                $row[3]

            );
        }

        // Get the number of items
        $itemCount = count($dataArray);

        $currentDateTime = date('j F Y H:i:s');

       $text = (string) (new ReceiptPrinter)
            ->leftMargin(0)
            ->centerAlign()
            ->setEmphasis(true)
            ->setTextSize(1, 2)
            ->text(strtoupper($adminname))
            ->setEmphasis(false)
            ->setTextSize(1, 1)
            ->text('Branch: ' . $branchname)
            ->text('P O Box: ' . $po_box)
            ->text('Tel: ' . $tel)
            ->feed(2)
            ->setEmphasis(true)
            ->text('RECEIPT')
            ->setEmphasis(false)
            ->line()
            ->leftAlign()
            ->text('Invoice No.:' . $trans_id)
            ->text('Customer No.:' . $custs)
            ->centerAlign()
            ->line()
            ->leftAlign()
            ->text('Invoice Date:' . $date)
            ->text('Supplied Date:' . $supplieddate)
            ->text('Payment:' . $payment)
            ->centerAlign()
            ->line()
            ->leftAlign()
            ->text($tableString)
            ->centerAlign()
            ->line()
            ->leftAlign()
            ->setEmphasis(true)
            ->text(sprintf("\n%-15s  %8s  %5s  %5s\n", '', '', 'Subtotal:', $grandinnumber - $vat))
            ->setEmphasis(false)
            ->centerAlign()
            ->line()
            ->leftAlign()
            ->setEmphasis(true)
            ->text(sprintf("\n%-15s  %8s  %5s  %5s\n", '', '', 'VAT     :', $vat))
            ->setEmphasis(false)
            ->centerAlign()
            ->line()
            ->leftAlign()
            ->setEmphasis(true)
            ->text(sprintf("\n%-15s  %5s  %5s  %5s\n", 'No. of Items:', $itemCount, 'Grand Total:', $grandinnumber))
            ->setEmphasis(false)
            ->feed(2)
            ->centerAlign()
            ->setEmphasis(true)
            ->setTextSize(1, 2)
            ->text('*** THANK YOU VISIT AGAIN ***')
            ->setEmphasis(false)
            ->setTextSize(1, 1)
            ->feed(1)
            ->text($currentDateTime)
            ->cut();


        // Now send the string to your receipt printer
        Printing::newPrintTask()
            // ->printer("72446283")
            ->printer($printerId)
            ->content($text)
            ->send();

        $printerData = Printing::printer($printerId);
        $printerName = $printerData->name();
        $printerStatus = $printerData->status();


        if ($existingData == 0 || $printerdata->status != "online") {

            $data = new RawilkPrint();
            $data->user_id = $userid;
            $data->branch = $branch;
            $data->printername = $printerName;
            $data->printer_id = $printerId;
            $data->status  = $printerStatus;
            $data->save();

            $old = DB::table('rawilk_prints')
                ->where('user_id', $userid)
                ->where('branch', $branch)
                ->where('status', '!=', 'online')
                ->delete();
        }

        return redirect()->back();
    }
}
