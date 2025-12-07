<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Invoice::with(['order.user', 'order.items.product']);

        // Filtrer selon le rôle
        if ($user->hasRole('commercial') || $user->role === 'commercial' || $user->role === 'vendeur') {
            $query->whereHas('order.user', function($q) use ($user) {
                $q->where('commercial_id', $user->id);
            });
        } elseif ($user->role === 'client') {
            $query->where('user_id', $user->id);
        }
        // Admin et gestionnaire voient toutes les factures

        $invoices = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $invoices
        ]);
    }

    /**
     * Créer une facture pour une commande
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Vérifier les permissions
        if (!$user->hasPermission('invoices.create') &&
            !in_array($user->role, ['admin', 'super-admin', 'gestionnaire'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas la permission de créer des factures.'
            ], 403);
        }

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'payment_method' => 'nullable|string|max:50',
        ]);

        $order = Order::with('user')->findOrFail($validated['order_id']);

        // Vérifier si une facture existe déjà pour cette commande
        if ($order->invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Une facture existe déjà pour cette commande.'
            ], 422);
        }

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'total_amount' => $order->total_amount,
            'due_date' => $validated['due_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'payment_method' => $validated['payment_method'] ?? null,
            'created_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Facture créée avec succès',
            'data' => $invoice->load(['order.user', 'order.items.product'])
        ], 201);
    }

    public function show($id)
    {
        $invoice = Invoice::with(['order.user', 'order.items.product'])->findOrFail($id);
        $user = Auth::user();

        // Vérifications d'accès selon le rôle
        if (($user->hasRole('commercial') || $user->role === 'commercial' || $user->role === 'vendeur')
            && $invoice->order->user->commercial_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        if ($user->role === 'client' && $invoice->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $invoice
        ]);
    }

    /**
     * Ajouter un paiement à une facture
     */
    public function addPayment(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $user = Auth::user();

        // Vérifier les permissions
        if (!$user->hasPermission('invoices.edit') &&
            !in_array($user->role, ['admin', 'super-admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas la permission de modifier les paiements.'
            ], 403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:50',
        ]);

        if ($validated['payment_method']) {
            $invoice->payment_method = $validated['payment_method'];
        }

        $invoice->addPayment($validated['amount']);
        $invoice->updated_by = $user->id;
        $invoice->save();

        return response()->json([
            'success' => true,
            'message' => 'Paiement enregistré avec succès',
            'data' => $invoice->fresh()->load(['order.user', 'order.items.product'])
        ]);
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