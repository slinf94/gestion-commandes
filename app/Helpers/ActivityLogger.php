<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log a user login activity.
     */
    public static function logLogin(User $user): void
    {
        activity('auth')
            ->causedBy($user)
            ->withProperties([
                'event' => 'login',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now(),
            ])
            ->log("{$user->full_name} s'est connecté");
    }

    /**
     * Log a user logout activity.
     */
    public static function logLogout(User $user): void
    {
        activity('auth')
            ->causedBy($user)
            ->withProperties([
                'event' => 'logout',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now(),
            ])
            ->log("{$user->full_name} s'est déconnecté");
    }

    /**
     * Log a system activity.
     */
    public static function logSystemActivity(string $description, array $properties = []): void
    {
        activity('system')
            ->withProperties(array_merge([
                'event' => 'system',
                'timestamp' => now(),
            ], $properties))
            ->log($description);
    }

    /**
     * Log a security activity.
     */
    public static function logSecurityActivity(string $description, array $properties = []): void
    {
        $user = Auth::user();

        activity('security')
            ->causedBy($user)
            ->withProperties(array_merge([
                'event' => 'security',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now(),
            ], $properties))
            ->log($description);
    }

    /**
     * Log a payment activity.
     */
    public static function logPaymentActivity(string $description, array $properties = []): void
    {
        $user = Auth::user();

        activity('payment')
            ->causedBy($user)
            ->withProperties(array_merge([
                'event' => 'payment',
                'timestamp' => now(),
            ], $properties))
            ->log($description);
    }

    /**
     * Log a bulk operation activity.
     */
    public static function logBulkOperation(string $description, array $properties = []): void
    {
        $user = Auth::user();

        activity('bulk_operation')
            ->causedBy($user)
            ->withProperties(array_merge([
                'event' => 'bulk_operation',
                'timestamp' => now(),
            ], $properties))
            ->log($description);
    }
}
