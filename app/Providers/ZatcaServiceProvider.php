<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Saleh7\Zatca\GeneratorInvoice;

class ZatcaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for the ZATCA library error
        if (class_exists(GeneratorInvoice::class)) {
            GeneratorInvoice::$currencyID = 'SAR';
        }
    }
}