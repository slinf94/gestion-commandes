<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user' => function($query) {
                $query->withTrashed(); // Inclure les utilisateurs supprimés
            }, 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user' => function($query) {
                $query->withTrashed(); // Inclure les utilisateurs supprimés
            }, 'items.product', 'statusHistory']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'comment' => 'nullable|string|max:500',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Enregistrer l'historique du changement de statut
        $order->statusHistory()->create([
            'previous_status' => $oldStatus,
            'new_status' => $request->status,
            'comment' => $request->comment,
            'changed_by' => auth()->id(),
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Statut de la commande mis à jour avec succès');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')
            ->with('success', 'Commande supprimée avec succès');
    }
}
