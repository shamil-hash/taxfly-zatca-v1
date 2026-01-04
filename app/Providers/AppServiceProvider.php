<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use App\Models\Branch;
use App\Models\Adminuser;
use App\Models\Credituser;
use App\Models\Softwareuser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        date_default_timezone_set('Asia/Muscat');

        // Existing view composer
        view()->composer('*', function ($view) {
            if (session('adminuser')) {
                $adminId = session('adminuser') ?? null;
            } elseif (session('softwareuser')) {
                $userid = session('softwareuser');
                $adminId = Softwareuser::where('id', $userid)
                    ->pluck('admin_id')
                    ->first();
            } elseif (session('credituser')) {
                $userid = session('credituser');
                $adminId = Credituser::where('id', $userid)
                    ->pluck('admin_id')
                    ->first();
            }

            if (session('softwareuser')) {
                $branch = Softwareuser::where('id', session('softwareuser'))
                    ->pluck('location')
                    ->first();
                $currency = Branch::where('id', $branch)->pluck('currency')->first();
                $view->with('currency', $currency);
            }
        });

        /**
         * EXTRA: Log controller → view mappings for the whole project
         */
        Event::listen('composing:*', function ($view) {
            // Laravel sometimes sends the view name as string
            if (is_string($view)) {
                return;
            }

            // Get controller/method from current route
            $routeAction = optional(Route::current())->getActionName();
            $viewName = $view->getName();

            $entry = [
                'controller' => $routeAction,
                'view'       => $viewName,
            ];

            $filePath = storage_path('framework/viewmap.json');
            $data = [];

            if (file_exists($filePath)) {
                $data = json_decode(file_get_contents($filePath), true) ?? [];
            }

            if (!in_array($entry, $data, true)) {
                $data[] = $entry;
                file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
        });
    }
}
