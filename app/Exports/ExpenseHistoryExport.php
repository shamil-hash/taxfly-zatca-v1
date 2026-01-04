<?php

namespace App\Exports;

use App\Models\Accountexpense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpenseHistoryExport implements FromCollection, WithHeadings, WithStyles
{
    protected $branchId;
    protected $startDate;
    protected $totalAmount;

    public function __construct($branchId, $startDate)
    {
        $this->branchId = $branchId;
        $this->startDate = $startDate;
    }

    public function collection()
    {
        $query = Accountexpense::where('branch', $this->branchId)
            ->select(
                'date',
                DB::raw("CASE WHEN expense_type = 1 THEN 'Direct' ELSE 'Indirect' END as expense_type"),
                DB::raw("CASE WHEN expense_type = 1 THEN direct_expense ELSE indirect_expense END as expense"),
                'details',
                'amount'
            );

        if ($this->startDate) {
            $query->where('date', '>=', $this->startDate);
        }

        $data = $query->get();
        
        // Calculate total amount
        $this->totalAmount = $data->sum('amount');
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Expense Type',
            'Expense',
            'Details',
            'Amount'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Get the last row number
        $lastRow = $sheet->getHighestRow();
        
        // Add total row
        $sheet->setCellValue('A'.($lastRow+1), 'Total');
        $sheet->setCellValue('E'.($lastRow+1), $this->totalAmount);
        
        // Merge cells for the total label
        $sheet->mergeCells('A'.($lastRow+1).':D'.($lastRow+1));
        
        // Style for total row
        return [
            // Style the header row
            1 => ['font' => ['bold' => true]],
            
            // Style the total row
            ($lastRow+1) => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD9D9D9']
                ]
            ],
            
            // Right align the amount column
            'E' => ['alignment' => ['horizontal' => 'right']],
            'E'.($lastRow+1) => ['alignment' => ['horizontal' => 'right']]
        ];
    }
}