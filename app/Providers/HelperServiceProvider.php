<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\OrderStatusHelper;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Helper functions moved to OrderStatusHelper class
    }
}


