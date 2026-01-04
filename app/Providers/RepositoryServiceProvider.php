<?php

namespace App\Providers;

use App\Repositories\MainRepository\EditTransactionRepository;
use App\Repositories\Interfaces\EditTransactionRepositoryInterface;

use App\Repositories\Interfaces\EditPurchaseRepositoryInterface;
use App\Repositories\Interfaces\PurchaseOrderRepositoryInterface;
use App\Repositories\MainRepository\EditPurchaseRepository;

use App\Repositories\MainRepository\EloquentUserRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;

use App\Repositories\Interfaces\SalesOrderRepositoryInterface;
use App\Repositories\MainRepository\SalesOrderRepository;

use App\Repositories\Interfaces\QuotationRepositoryInterface;
use App\Repositories\MainRepository\PurchaseOrderRepository;
use App\Repositories\MainRepository\QuotationRepository;

use Illuminate\Support\ServiceProvider;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(EditTransactionRepositoryInterface::class, EditTransactionRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(EditPurchaseRepositoryInterface::class, EditPurchaseRepository::class);
        $this->app->bind(SalesOrderRepositoryInterface::class, SalesOrderRepository::class);
        $this->app->bind(QuotationRepositoryInterface::class, QuotationRepository::class);
        $this->app->bind(PurchaseOrderRepositoryInterface::class, PurchaseOrderRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
