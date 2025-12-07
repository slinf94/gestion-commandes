<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    /**
     * Voir une facture
     */
    public function view(User $user, Invoice $invoice)
    {
        // Admin et super-admin peuvent tout voir
        if (in_array($user->role, ['admin', 'super-admin', 'gestionnaire'])) {
            return true;
        }

        // Commercial/Vendeur peut voir les factures de ses clients
        if ($user->role === 'commercial' || $user->role === 'vendeur' || $user->hasRole('commercial') || $user->hasRole('vendeur')) {
            return $invoice->order->user->commercial_id === $user->id;
        }

        // Client peut voir ses propres factures
        if ($user->role === 'client') {
            return $invoice->user_id === $user->id;
        }

        return false;
    }

    /**
     * Modifier une facture
     * Les vendeurs avec la permission 'invoices.edit' peuvent modifier
     */
    public function update(User $user, Invoice $invoice)
    {
        // Admin et super-admin peuvent tout modifier
        if (in_array($user->role, ['admin', 'super-admin'])) {
            return true;
        }

        // Vérifier la permission RBAC 'invoices.edit'
        if ($user->hasPermission('invoices.edit')) {
            // Pour les commerciaux/vendeurs, vérifier qu'ils sont liés au client
            if ($user->role === 'commercial' || $user->role === 'vendeur' || $user->hasRole('commercial') || $user->hasRole('vendeur')) {
                return $invoice->order->user->commercial_id === $user->id;
            }
            return true;
        }

        return false;
    }

    /**
     * Créer une facture
     */
    public function create(User $user)
    {
        return in_array($user->role, ['admin', 'super-admin', 'gestionnaire']) ||
               $user->hasPermission('invoices.create');
    }

    /**
     * Supprimer une facture
     */
    public function delete(User $user, Invoice $invoice)
    {
        return in_array($user->role, ['admin', 'super-admin']);
    }
}