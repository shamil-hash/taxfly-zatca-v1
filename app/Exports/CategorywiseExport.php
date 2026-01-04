<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategorywiseExport implements FromCollection, WithHeadings, WithStyles
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
                'Product Name' => $product->category_name,
                'Total Sold Quantity' => $product->remain_quantity,
                // 'Total Sold Amount' => number_format($product->total_amount, 3),
                // 'Profit' => number_format($product->profit, 3)
            ];
        }

        // Add total row
        $data[] = [
            'Category Name' => '',
            'Total Sold Quantity' => '',
            // 'Total Sold Amount' => number_format($this->totalSold, 3),
            // 'Profit' => number_format($this->totalProfit, 3)
        ];

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Category Name',
            'Total Sold Quantity',
            // 'Total Sold Amount',
            // 'Profit'
        ];
    }

public function styles(Worksheet $sheet)
{
    // Method 1: Using cell ranges (often more reliable)
    $sheet->getStyle('A1:D1')->getFont()->setBold(true);
    
    $totalRow = count($this->products) + 2;
    $sheet->getStyle("A{$totalRow}:D{$totalRow}")->getFont()->setBold(true);
    
    // Method 2: Or using the array return (both work)
    return [
        1 => ['font' => ['bold' => true]],
        $totalRow => ['font' => ['bold' => true]],
    ];
}
}