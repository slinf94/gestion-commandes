<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Invoice::with(['order.user']);

        if ($user->role === 'commercial') {
            $query->whereHas('order.user', function($q) use ($user) {
                $q->where('commercial_id', $user->id);
            });
        } elseif ($user->role === 'client') {
            $query->where('user_id', $user->id);
        }

        return response()->json($query->get());
    }

    public function show($id)
    {
        $invoice = Invoice::with(['order.user', 'order.orderItems.product'])->findOrFail($id);
        $user = Auth::user();

        if ($user->role === 'commercial' && $invoice->order->user->commercial_id !== $user->id) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        if ($user->role === 'client' && $invoice->user_id !== $user->id) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        return response()->json($invoice);
    }

    /**
     * Mettre à jour une facture
     * Nécessite la permission 'invoices.edit' pour les vendeurs
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $user = Auth::user();

        // Vérification des permissions
        // 1. Admin et super-admin ont tous les droits
        // 2. Les utilisateurs avec la permission 'invoices.edit' (ex: vendeurs)
        // 3. Policy pour des règles spécifiques
        if (!$user->hasPermission('invoices.edit') && 
            !in_array($user->role, ['admin', 'super-admin']) &&
            $user->cannot('update', $invoice)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas la permission de modifier les factures. Contactez un administrateur.'
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:paid,unpaid,partially_paid',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'due_date' => 'nullable|date',
            'total_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        $invoice->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Facture modifiée avec succès',
            'data' => $invoice->load(['order.user', 'order.orderItems.product'])
        ]);
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $user = Auth::user();

        if ($user->cannot('delete', $invoice)) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $invoice->delete();

        return response()->json(['message' => 'Facture supprimée avec succès']);
    }
}