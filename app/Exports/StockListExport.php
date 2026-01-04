<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockListExport implements FromCollection, WithHeadings, WithStyles
{
    protected $products;
    protected $totalValue;
    protected $branch;

    public function __construct($products, $totalValue, $branch)
    {
        $this->products = $products;
        $this->totalValue = $totalValue;
        $this->branch = $branch;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->products as $product) {
            $data[] = [
                'Product Name' => $product->product_name,
                'Stock' => $product->stock,
                'Remaining Stock' => number_format($product->remaining_stock, 3),
                'Value' => ($this->branch == 0) ? number_format(max(0, $product->remaining_stock) * $product->rate, 3) : ''
            ];
        }

        if ($this->branch == 0) {
            $data[] = [
                'Product Name' => '',
                'Stock' => '',
                'Remaining Stock' => 'Total',
                'Value' => number_format($this->totalValue, 3)
            ];
        }

        return collect($data);
    }

public function headings(): array
{
    $headings = [
        'Product Name',
        'Total Stock',
        'Remaining Stock',
    ];
    
    if ($this->branch == 0) {
        $headings[] = 'Remaining Stock Value';
    }
    
    return $headings;
}

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            count($this->products) + 2 => ['font' => ['bold' => true]],
        ];
    }
}