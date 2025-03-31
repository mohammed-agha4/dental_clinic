<?php

namespace App\Providers;

use App\Models\Inventory;
use App\Models\Appointment;
use App\Observers\InventoryObserver;
use Illuminate\Pagination\Paginator;
use App\Observers\AppointmentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::UseBootstrapFive();
        Inventory::observe(InventoryObserver::class);
        Appointment::observe(AppointmentObserver::class);
    }
}
