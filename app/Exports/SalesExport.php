<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $userid;
    protected $location;
    protected $date;
    protected $viewtype;
    protected $totals = [];


    function __construct($userid, $viewtype, $location, $date)
    {
        $this->userid = $userid;
        $this->location = $location;
        $this->date = $date;
        $this->viewtype = $viewtype;
    }

    // /**
    // * @return \Illuminate\Support\Collection
    // */


    public function collection()
    {
        $data = $this->getData();
        
        // Calculate totals for each numeric column
        $this->totals = [
            'Total Price' => $data->sum('grandtotal_withdiscount'),
            'VAT' => $data->sum('vat'),
            'Discount Amount' => $data->sum('discount_amount'),
            'Grand Total (w/. discount)' => $data->sum('sum'),
        ];
        
        return $data;
    }

    private function getData()
    {
        if ($this->viewtype == 1) {

            return DB::table('accountantlocs')
                ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                // ->select(DB::raw("buyproducts.transaction_id,SUM(buyproducts.price) as price,((SUM(buyproducts.total_amount)) - (SUM(buyproducts.price))) as vat,SUM(buyproducts.total_amount) as sum,buyproducts.created_at,buyproducts.customer_name"),)
                ->selectRaw("
                    buyproducts.transaction_id as transaction_id,
    CASE
                    WHEN SUM(buyproducts.discount_amount) != 0 THEN
                        SUM(buyproducts.totalamount_wo_discount)
                    ELSE
                        SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0))
                    END as grandtotal_withdiscount,
                    SUM(buyproducts.vat_amount) as vat,
                    SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
                    + SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount,
                    
                                          SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,
       


                    buyproducts.created_at as created_at,

                    buyproducts.customer_name as customer_name
                ")
                ->groupBy('buyproducts.transaction_id')
                ->orderBy('buyproducts.created_at', 'ASC')
                ->whereDate('buyproducts.created_at', $this->date)
                ->where('accountantlocs.user_id', $this->userid)
                ->where('buyproducts.branch', $this->location)
                ->get();

            
        }
        if ($this->viewtype == 2) {

            return DB::table('accountantlocs')
                ->Join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                // ->select(DB::raw("buyproducts.transaction_id,SUM(buyproducts.price) as price, ((SUM(buyproducts.total_amount)) - (SUM(buyproducts.price))) as vat,SUM(buyproducts.total_amount) as sum, buyproducts.created_at, buyproducts.customer_name"),)
                ->selectRaw("
                    buyproducts.transaction_id as transaction_id,
    CASE
                    WHEN SUM(buyproducts.discount_amount) != 0 THEN
                        SUM(buyproducts.totalamount_wo_discount)
                    ELSE
                        SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0))
                    END as grandtotal_withdiscount,

                    SUM(buyproducts.vat_amount) as vat,

                   SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
                    +  SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount,
                    
                                          SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,




                    buyproducts.created_at as created_at,
                    buyproducts.customer_name as customer_name
                ")
                ->groupBy('buyproducts.transaction_id')
                ->orderBy('buyproducts.created_at', 'ASC')
                ->whereMonth('buyproducts.created_at', $this->date)
                ->where('accountantlocs.user_id', $this->userid)
                ->where('buyproducts.branch', $this->location)
                ->get();

            
        }
        if ($this->viewtype == 3) {
           return DB::table('accountantlocs')
                ->join('buyproducts', 'accountantlocs.location_id', '=', 'buyproducts.branch')
                ->selectRaw("
                    buyproducts.transaction_id as transaction_id,
    CASE
                    WHEN SUM(buyproducts.discount_amount) != 0 THEN
                        SUM(buyproducts.totalamount_wo_discount)
                    ELSE
                        SUM(DISTINCT COALESCE(buyproducts.bill_grand_total, 0))
                    END as grandtotal_withdiscount,

                    SUM(buyproducts.vat_amount) as vat,
                    SUM(COALESCE(buyproducts.discount_amount * buyproducts.quantity, 0))
                    + SUM(DISTINCT COALESCE(total_discount_amount, 0)) as discount_amount,


                                          SUM(DISTINCT COALESCE(bill_grand_total, 0)) as sum,





                    buyproducts.created_at as created_at,
                    buyproducts.customer_name as customer_name
                ")
                ->groupBy('buyproducts.transaction_id')
                ->orderBy('buyproducts.created_at', 'ASC')
                ->whereYear('buyproducts.created_at', $this->date)
                ->where('accountantlocs.user_id', $this->userid)
                ->where('buyproducts.branch', $this->location)
                ->get();

           
        }
    }
    public function headings(): array
    {
        return [
            'Transaction ID',
            'Total Price',
            'VAT',
            'Discount Amount',
            'Grand Total (w/. discount)',
            // 'Credit Note Amount',
            // 'Total (Grand Total - Credit Note Amount)',
            'Date and Time',
            'Customer Name',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Header row style
                $event->sheet->getDelegate()->getStyle('A1:G1')
                    ->getFont()
                    ->setBold(true)
                    ->setSize(14);
                
                // Add totals row
                $lastRow = $event->sheet->getHighestRow();
                $newRow = $lastRow + 1;
                
                // Set totals values
                $event->sheet->setCellValue("A{$newRow}", 'TOTAL:');
                $event->sheet->setCellValue("B{$newRow}", $this->totals['Total Price']);
                $event->sheet->setCellValue("C{$newRow}", $this->totals['VAT']);
                $event->sheet->setCellValue("D{$newRow}", $this->totals['Discount Amount']);
                $event->sheet->setCellValue("E{$newRow}", $this->totals['Grand Total (w/. discount)']);
                
                // Totals row style with background color
                $totalsStyle = [
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'black'] // White text
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'D3D3D3' // Your brand green color
                        ]
                    ]
                ];
                
                $event->sheet->getDelegate()->getStyle("A{$newRow}:G{$newRow}")
                    ->applyFromArray($totalsStyle);
                
                // Number formatting
                $event->sheet->getDelegate()->getStyle("B2:E{$newRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');
            },
        ];
    }
}
