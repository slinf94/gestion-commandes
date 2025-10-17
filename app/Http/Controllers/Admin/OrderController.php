<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusChangedNotification;
use App\Notifications\NewOrderNotification;
use App\Helpers\OrderStatusHelper;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user' => function($query) {
                $query->withTrashed(); // Inclure les utilisateurs supprimés
            }, 'items.product.productImages'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user' => function($query) {
                $query->withTrashed(); // Inclure les utilisateurs supprimés
            }, 'items.product.productImages', 'statusHistory']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|' . OrderStatusHelper::getValidationRule(),
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

        // Envoyer une notification au client si le statut a changé
        if ($oldStatus !== $request->status) {
            try {
                // Notifier le client du changement de statut
                $order->user->notify(new OrderStatusChangedNotification($order, $oldStatus, $request->status));
            } catch (\Exception $e) {
                // Log l'erreur mais ne pas bloquer le processus
                \Log::error('Erreur lors de l\'envoi de la notification au client: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Statut de la commande mis à jour avec succès');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')
            ->with('success', 'Commande supprimée avec succès');
    }

    /**
     * Notifier les administrateurs d'une nouvelle commande
     * Cette méthode peut être appelée depuis l'API ou d'autres contrôleurs
     */
    public static function notifyAdminsNewOrder(Order $order)
    {
        try {
            // Récupérer tous les administrateurs
            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(new NewOrderNotification($order));
            }

            \Log::info('Notifications de nouvelle commande envoyées aux administrateurs', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'admins_count' => $admins->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi des notifications aux administrateurs: ' . $e->getMessage());
        }
    }
}
