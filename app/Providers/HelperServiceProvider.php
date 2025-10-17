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
        // Enregistrer le helper globalement
        if (!function_exists('order_status')) {
            function order_status($status) {
                return OrderStatusHelper::getStatusInfo($status);
            }
        }

        if (!function_exists('order_status_text')) {
            function order_status_text($status) {
                return OrderStatusHelper::getStatusText($status);
            }
        }

        if (!function_exists('order_status_class')) {
            function order_status_class($status) {
                return OrderStatusHelper::getStatusClass($status);
            }
        }

        if (!function_exists('order_status_icon')) {
            function order_status_icon($status) {
                return OrderStatusHelper::getStatusIcon($status);
            }
        }
    }
}


