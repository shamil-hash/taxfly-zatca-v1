<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class TrackBladeUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan track:blades
     */
    protected $signature = 'track:blades';

    /**
     * The console command description.
     */
    protected $description = 'Scan controllers and map Blade views with their exact file paths.';

    public function handle()
    {
        $controllersPath = app_path('Http/Controllers');
        $viewsBasePath = resource_path('views');

        $viewFiles = [];

        // Step 1: Scan all controller PHP files recursively
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($controllersPath)
        );

        // Collect all Blade files with their relative dot-notation names for quick lookup
        $bladeFiles = $this->getAllBladeFiles($viewsBasePath);

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());

                // Match view('something.something') or View::make('something.something')
                preg_match_all("/view\(['\"]([^'\"]+)['\"]\)/", $content, $matches1);
                preg_match_all("/View::make\(['\"]([^'\"]+)['\"]\)/", $content, $matches2);

                $matches = array_merge($matches1[1], $matches2[1]);

                if (!empty($matches)) {
                    $controllerName = $file->getFilename();
                    if (!isset($viewFiles[$controllerName])) {
                        $viewFiles[$controllerName] = [];
                    }

                    foreach (array_unique($matches) as $viewName) {
                        $path = $this->findBladePath($viewName, $bladeFiles);
                        $viewFiles[$controllerName][] = [
                            'view' => $viewName,
                            'path' => $path ?? 'Not found',
                        ];
                    }
                }
            }
        }

        // Output results
        if (empty($viewFiles)) {
            $this->info("No Blade views found in controllers.");
        } else {
            foreach ($viewFiles as $controller => $views) {
                $this->info("Controller: {$controller}");
                foreach ($views as $view) {
                    $this->line("  - {$view['view']}.blade.php");
                    $this->line("     Path: {$view['path']}");
                }
                $this->newLine();
            }
        }
    }

    /**
     * Get all Blade files under views directory, mapped by dot notation => full path
     *
     * @param string $basePath
     * @return array
     */
    protected function getAllBladeFiles(string $basePath): array
    {
        $bladeFiles = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'blade.php') {
                // Example: resources/views/invoices/show.blade.php
                // Convert to dot notation: invoices.show
                $relativePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $relativePath = str_replace(DIRECTORY_SEPARATOR, '.', $relativePath);
                $relativePath = preg_replace('/\.blade\.php$/', '', $relativePath);
                $bladeFiles[$relativePath] = $file->getPathname();
            }
        }

        return $bladeFiles;
    }

    /**
     * Find the full path of a Blade view from its dot notation name
     *
     * @param string $viewName
     * @param array $bladeFiles
     * @return string|null
     */
    protected function findBladePath(string $viewName, array $bladeFiles): ?string
    {
        return $bladeFiles[$viewName] ?? null;
    }
}
