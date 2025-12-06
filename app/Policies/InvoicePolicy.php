<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Invoice $invoice)
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'commercial') {
            return $invoice->order->user->commercial_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Invoice $invoice)
    {
        return $user->role === 'admin';
    }
}