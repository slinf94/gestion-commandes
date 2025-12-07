<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Policies\InvoicePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Invoice::class => InvoicePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate pour vérifier si l'utilisateur peut modifier les factures
        Gate::define('edit-invoices', function ($user) {
            return $user->hasPermission('invoices.edit') ||
                   in_array($user->role, ['admin', 'super-admin']);
        });

        // Gate pour vérifier si l'utilisateur peut créer des factures
        Gate::define('create-invoices', function ($user) {
            return $user->hasPermission('invoices.create') ||
                   in_array($user->role, ['admin', 'super-admin', 'gestionnaire']);
        });

        // Gate pour vérifier si l'utilisateur peut gérer les rôles/permissions
        Gate::define('manage-permissions', function ($user) {
            return in_array($user->role, ['admin', 'super-admin']);
        });
    }
}
