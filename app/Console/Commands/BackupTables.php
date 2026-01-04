<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Writer;

class BackupTables extends Command
{
    protected $signature = 'backup:tables';
    protected $description = 'Backup old data from multiple tables to a ZIP file';

    private $tables = ['buyproducts', 'bill_histories']; // Replace with your actual tables

    public function handle()
    {
        $cutoffDate = Carbon::now()->subMonths(1);
        $zipFileName = storage_path('app'.DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR.'backup_'.now()->format('Y_m_d_H_i_s').'.zip');
        $externalBackupPath = 'D:\\backups\\backup_'.now()->format('Y_m_d_H_i_s').'.zip'; // External path on D drive

        // Ensure the internal backup directory exists
        if (!file_exists(dirname($zipFileName))) {
            mkdir(dirname($zipFileName), 0755, true);
        }

        // Ensure the external backup directory exists
        if (!file_exists('D:\\backups')) {
            mkdir('D:\\backups', 0755, true);
        }

        $zip = new \ZipArchive();

        if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $this->error('Could not create ZIP file.');
            Log::error('Could not create ZIP file at '.$zipFileName);

            return;
        }

        $dataBackedUp = false; // Flag to check if any data was backed up

        foreach ($this->tables as $table) {
            $data = DB::table($table)->where('created_at', '<', $cutoffDate)->get();

            if ($data->isEmpty()) {
                $this->info("No old data to backup for table: $table.");
                continue;
            }

            $dataBackedUp = true; // Set flag to true as there is data to backup

            $csv = Writer::createFromFileObject(new \SplTempFileObject());
            $csv->insertOne(array_keys((array) $data->first())); // Header row

            foreach ($data as $row) {
                $csv->insertOne((array) $row);
            }

            $zip->addFromString("$table.csv", $csv->toString());
            DB::table($table)->where('created_at', '<', $cutoffDate)->delete();
            $this->info("Old data backed up and deleted for table: $table.");
        }

        $zip->close();

        // Only proceed with copying the file if data was backed up
        if ($dataBackedUp) {
            // Ensure the ZIP file was created
            if (!file_exists($zipFileName)) {
                $this->error('ZIP file was not created.');
                Log::error('ZIP file was not created at '.$zipFileName);

                return;
            }

            $this->info('Backup completed and saved to '.$zipFileName);
            Log::info('Backup completed and saved to '.$zipFileName);

            // Log the paths for debugging
            Log::info('Attempting to copy ZIP file to external location.');
            Log::info('Source path: '.$zipFileName);
            Log::info('Destination path: '.$externalBackupPath);

            // Check if the ZIP file exists right before copying
            if (file_exists($zipFileName)) {
                $this->info('ZIP file exists: '.$zipFileName);
                Log::info('ZIP file exists: '.$zipFileName);
            } else {
                $this->error('ZIP file does not exist at the expected path: '.$zipFileName);
                Log::error('ZIP file does not exist at the expected path: '.$zipFileName);

                return;
            }

            // Copy the ZIP file to the external location
            try {
                if (copy($zipFileName, $externalBackupPath)) {
                    $this->info('Backup also saved to '.$externalBackupPath);
                    Log::info('Backup also saved to '.$externalBackupPath);
                } else {
                    $this->error('Failed to copy backup to external location.');
                    Log::error('Failed to copy backup to external location.');
                }
            } catch (\Exception $e) {
                $this->error('Error during copy: '.$e->getMessage());
                Log::error('Error during copy: '.$e->getMessage());
            }
        } else {
            $this->info('No data was backed up, so no ZIP file was created or copied.');
            Log::info('No data was backed up, so no ZIP file was created or copied.');
        }
    }
}
