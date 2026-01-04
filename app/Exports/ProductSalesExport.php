<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductSalesExport implements FromCollection, WithHeadings, WithStyles
{
    protected $products;
    protected $totalProfit;
    protected $totalSold;

    public function __construct($products, $totalProfit, $totalSold)
    {
        $this->products = $products;
        $this->totalProfit = $totalProfit;
        $this->totalSold = $totalSold;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->products as $product) {
            $data[] = [
                'Product Name' => $product->product_name,
                // 'Buycost' => $product->rate,
                'Total Sold Quantity' => $product->remain_quantity,
                // 'Total Sold Amount' => number_format($product->total_amount, 3),
                // 'Profit' => number_format($product->profit, 3)
            ];
        }

        // Add total row
        $data[] = [
            'Product Name' => '',
            // 'Buycost' => '',
            'Total Sold Quantity' => '',
            // 'Total Sold Amount' => number_format($this->totalSold, 3),
            // 'Profit' => number_format($this->totalProfit, 3)
        ];

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Product Name',
            // 'Buycost',
            'Total Sold Quantity',
            // 'Total Sold Amount',
            // 'Profit'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            count($this->products) + 2 => ['font' => ['bold' => true]],
        ];
    }
}