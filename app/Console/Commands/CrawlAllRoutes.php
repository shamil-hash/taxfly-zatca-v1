<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

class CrawlAllRoutes extends Command
{
    protected $signature = 'crawl:routes';
    protected $description = 'Visit all GET routes in the app to trigger view rendering and logging';

    public function handle()
    {
        $this->info('Starting to crawl all GET routes...');

        $routes = collect(Route::getRoutes())->filter(function ($route) {
            // Only GET routes with a name and no parameters
            return in_array('GET', $route->methods())
                && !$route->parameterNames()
                && !str_contains($route->uri(), '{');
        });

        $baseUrl = config('app.url') ?? 'http://localhost';

        foreach ($routes as $route) {
            $url = $baseUrl . '/' . ltrim($route->uri(), '/');
            $this->info("Visiting: {$url}");

            try {
                $response = Http::timeout(10)->get($url);

                if ($response->successful()) {
                    $this->info("Success: {$url}");
                } else {
                    $this->warn("Failed (status {$response->status()}): {$url}");
                }
            } catch (\Exception $e) {
                $this->warn("Error visiting {$url}: {$e->getMessage()}");
            }
        }

        $this->info('Route crawling finished.');
        $this->info('Check storage/framework/viewmap.json for updated controller-view mappings.');
    }
}
