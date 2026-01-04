<?php

namespace App\Exports;

use App\Models\Activity;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class ActivityExportReport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithColumnFormatting, WithMapping
{

    protected $branch;

    function __construct($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return \Illuminate\Support\Collection
     */

    public function collection()
    {

        if ($this->branch != null || $this->branch != "") {
            $datas = Activity::select([
                'activities.created_at',
                DB::raw('CASE
                    WHEN activities.is_admin = 1 THEN "Admin"
                    WHEN activities.is_user = 1 THEN "User"
                    WHEN activities.is_credituser = 1 THEN "Credit User"
                    ELSE "Other"
                END AS role'),
                DB::raw('CASE
                    WHEN activities.is_admin = 1 THEN adminusers.username
                    WHEN activities.is_user = 1 THEN softwareusers.username
                    WHEN activities.is_credituser = 1 THEN creditusers.username
                    ELSE ""
                END AS name'),
                'branches.branchname',
                'activities.ipaddress',
                'activities.message',
                DB::raw('CONCAT_WS(", ", activities.cityName, activities.regionName, activities.countryName) AS location'),
            ])
                ->leftJoin('adminusers', 'activities.admin_id', '=', 'adminusers.id')
                ->leftJoin('softwareusers', 'activities.user_id', '=', 'softwareusers.id')
                ->leftJoin(
                    'creditusers',
                    'activities.credituser_id',
                    '=',
                    'creditusers.id'
                )
                ->leftJoin('branches', 'activities.branch_id', '=', 'branches.id')
                ->where('activities.branch_id', $this->branch)
                ->orderBy('activities.created_at', 'DESC')
                ->groupBy('activities.created_at')
                ->get();
        } else {

            $datas = Activity::select([
                'activities.created_at',
                DB::raw('CASE
                    WHEN activities.is_admin = 1 THEN "Admin"
                    WHEN activities.is_user = 1 THEN "User"
                    WHEN activities.is_credituser = 1 THEN "Credit User"
                    ELSE "Other"
                END AS role'),
                DB::raw('CASE
                    WHEN activities.is_admin = 1 THEN adminusers.username
                    WHEN activities.is_user = 1 THEN softwareusers.username
                    WHEN activities.is_credituser = 1 THEN creditusers.username
                    ELSE ""
                END AS name'),
                'branches.branchname',
                'activities.ipaddress',
                'activities.message',
                DB::raw('CONCAT_WS(", ", activities.cityName, activities.regionName, activities.countryName) AS location'),
            ])
                ->leftJoin('adminusers', 'activities.admin_id', '=', 'adminusers.id')
                ->leftJoin('softwareusers', 'activities.user_id', '=', 'softwareusers.id')
                ->leftJoin(
                    'creditusers',
                    'activities.credituser_id',
                    '=',
                    'creditusers.id'
                )
                ->leftJoin('branches', 'activities.branch_id', '=', 'branches.id')
                ->orderBy('activities.created_at', 'DESC')
                ->groupBy('activities.created_at')
                ->get();
        }

        return $datas;
    }
    public function headings(): array
    {
        return [
            'Date & Time',
            'Type',
            'Username',
            'Branch',
            'IP Address',
            'Activity',
            'Location',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:G1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true)->setSize(14);
            },
        ];
    }
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY . ' | ' . NumberFormat::FORMAT_DATE_TIME1,
        ];
    }
    public function map($datas): array
    {
        return [
            Date::dateTimeToExcel($datas->created_at),
            $datas->role,
            $datas->name,
            $datas->branchname,
            $datas->ipaddress,
            $datas->message,
            $datas->location,
        ];
    }
}
