<?php

namespace App\Providers;

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
        // Configurer les textes de pagination en français
        \Illuminate\Pagination\Paginator::defaultView('pagination::bootstrap-4');

        // Personnaliser les textes de pagination
        \Illuminate\Pagination\Paginator::$defaultView = 'pagination::bootstrap-4';
    }
}
