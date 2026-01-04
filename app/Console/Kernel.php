<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     */
    protected $commands = [
        \App\Console\Commands\BackupTables::class,
        \App\Console\Commands\GenerateLaravelTracker::class,
        \App\Console\Commands\BackfillPurchaseJournals::class,
        \App\Console\Commands\GenerateZatcaCsr::class,
        \App\Console\Commands\RequestZatcaCompliance::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // 🗓 Backup: Jan 1 & Jul 1 at midnight (with name to allow withoutOverlapping)
        $schedule->call(function () {
            $logFile = storage_path('app/backup_log.json');
            $today = now()->toDateString();

            // Check if already ran today
            $alreadyRan = false;
            $logData = [];

            if (File::exists($logFile)) {
                $logData = json_decode(File::get($logFile), true);
                if (!empty($logData['last_backup_date']) && $logData['last_backup_date'] === $today) {
                    $alreadyRan = true;
                }
            }

            if ($alreadyRan) {
                Log::info("📛 Backup skipped — already ran today.");
                return;
            }

            Log::info("🔁 Running scheduled backup...");
            Artisan::call('backup:tables');
            Log::info("✅ Backup completed.");

            // Update backup log
            $logData['last_backup_date'] = $today;
            File::put($logFile, json_encode($logData));

        })
        ->name('backup-task') // ✅ Required for withoutOverlapping
        ->withoutOverlapping()
        ->everyMinute() // Jan 1 & Jul 1 at midnight
        ->sendOutputTo(storage_path('logs/backup.log'))
        ->emailOutputTo('netplexdev@gmail.com');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
