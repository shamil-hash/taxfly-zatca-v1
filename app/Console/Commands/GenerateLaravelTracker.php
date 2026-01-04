<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateLaravelTracker extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tracker:generate';

    /**
     * The console command description.
     */
    protected $description = 'Generate Blade Registry from Laravel project into Excel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔍 Scanning views directory...");
        $bladeFiles = File::allFiles(resource_path('views'));

        if (empty($bladeFiles)) {
            $this->error("❌ No Blade files found in resources/views.");
            return 1;
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // Remove default
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Blade Registry');

        $headers = [
            'Blade ID', 'Path', 'Status', 'Routes',
            'Controller@method', 'Models Used', 'JS Files',
            'Priority', 'Owner', 'Last Updated',
            'Deprecation Path', 'Notes'
        ];

        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        $bladeIdNum = 101;

        foreach ($bladeFiles as $file) {
            $fullPath = $file->getPathname();
            $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $fullPath);
            $bladeId = 'V-' . $bladeIdNum++;

            $sheet->fromArray([
                $bladeId,
                $relativePath,
                'Active',        // Default status
                '',              // Route name
                '',              // Controller@method
                '',              // Models used
                '',              // JS file
                '',              // Priority
                '',              // Owner
                now()->toDateString(),  // Last updated
                '',              // Deprecation path
                '',              // Notes
            ], null, 'A' . $row++);

            $this->line("✅ $bladeId → $relativePath");
        }

        $outputPath = storage_path('app/Laravel_Tracker_Raw.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($outputPath);

        $this->info("\n🎉 Laravel Tracker Excel created at: $outputPath");

        return 0;
    }
}
