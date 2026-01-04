<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TransactionsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $data;
    protected $tax;
    protected $users;
    protected $totals;

    public function __construct($data, $tax, $users)
    {
        $this->data = $data;
        $this->tax = $tax;
        $this->users = $users;

        // Calculate totals
        $this->totals = [
            'total_price' => $data->sum('grandtotal_withdiscount'),
            'discount_amount' => $data->sum('discount_amount'),
            'vat_amount' => $data->sum('vat'),
            'grand_total' => $data->sum('sum'),
        ];
    }

    public function collection()
    {
        return $this->data->map(function ($item) {
            return [
                'Transaction ID' => $item->transaction_id,
                'Date' => \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i:s'),
                'Total Price' => number_format($item->grandtotal_withdiscount, 3),
                'Discount Amount' => number_format($item->discount_amount, 3),
                'VAT Amount' => number_format($item->vat, 3),
                'Grand Total' => number_format($item->sum, 3),
                'Customer Name' => $item->customer_name,
                'Payment Type' => $this->getPaymentTypeText($item->payment_type),
            ];
        });
    }

    protected function getPaymentTypeText($typeId)
    {
        $types = [
            1 => 'Cash',
            2 => 'Bank',
            3 => 'Credit',
            4 => 'POS Card',
        ];

        return $types[$typeId] ?? $typeId;
    }

    public function headings(): array
    {
        return [
            'Transaction ID',
            'Date',
            'Total Price',
            'Discount Amount',
            'VAT Amount',
            'Grand Total',
            'Customer Name',
            'Payment Type'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Get the last row number
                $lastRow = $event->sheet->getHighestRow();

                // Add totals row
                $event->sheet->append([
                    'TOTAL',
                    '',
                    number_format($this->totals['total_price'], 3),
                    number_format($this->totals['discount_amount'], 3),
                    number_format($this->totals['vat_amount'], 3),
                    number_format($this->totals['grand_total'], 3),
                    '',
                    ''
                ]);

                // Style the totals row
                $event->sheet->getStyle('A'.($lastRow+1).':H'.($lastRow+1))->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFD3D3D3',
                        ]
                    ]
                ]);
            }
        ];
    }
}