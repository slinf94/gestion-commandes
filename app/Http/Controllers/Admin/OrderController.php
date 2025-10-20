<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderSimple as Order;
use App\Models\User;
use App\Models\OrderStatusHistory;
use App\Notifications\OrderStatusChangedNotification;
use App\Notifications\NewOrderNotification;
use App\Helpers\OrderStatusHelper;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        // Version ultra-simplifiée pour éviter l'épuisement mémoire
        $orders = Order::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Version ultra-simplifiée pour éviter l'épuisement mémoire
        $order->load(['user' => function($query) {
                $query->withTrashed(); // Inclure les utilisateurs supprimés
            }, 'items', 'statusHistory']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:' . implode(',', array_column(OrderStatus::cases(), 'value')),
            'comment' => 'nullable|string|max:500',
        ]);

        try {
            $newStatus = OrderStatus::from($request->status);
            $oldStatus = $order->status instanceof \App\Enums\OrderStatus ? $order->status : OrderStatus::from($order->status);

            // Vérifier si le changement de statut est autorisé
            if (!$order->canChangeStatusTo($newStatus)) {
                $oldStatusLabel = $oldStatus instanceof \App\Enums\OrderStatus ? $oldStatus->getLabel() : $order->getStatusLabel();
                $newStatusLabel = $newStatus instanceof \App\Enums\OrderStatus ? $newStatus->getLabel() : $order->getStatusLabel();
                $message = "Impossible de changer le statut de \"{$oldStatusLabel}\" vers \"{$newStatusLabel}\"";

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }

                return redirect()->back()->withErrors(['status' => $message]);
            }

            // Changer le statut en utilisant la méthode du modèle
            $success = $order->changeStatus($newStatus, $request->comment, auth()->id());

            if (!$success) {
                throw new \Exception('Erreur lors du changement de statut');
            }

            // Envoyer une notification au client
            try {
                $order->user->notify(new OrderStatusChangedNotification($order, $oldStatus, $newStatus));
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'envoi de la notification au client: ' . $e->getMessage());
            }

            // Message de succès
            $message = "Statut de la commande changé de \"{$oldStatus->getLabel()}\" vers \"{$newStatus->getLabel()}\" avec succès";

            // Vérifier si c'est une requête AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'order' => $order->fresh(['user', 'items.product', 'statusHistory.changedBy']),
                    'new_status' => [
                        'value' => $newStatus->value,
                        'label' => $newStatus->getLabel(),
                        'class' => $newStatus->getBootstrapClass(),
                        'color' => $newStatus->getColor(),
                        'icon' => $newStatus->getIcon(),
                    ]
                ]);
            }

            return redirect()->route('admin.orders.show', $order)
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour du statut de la commande: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'new_status' => $request->status,
                'user_id' => auth()->id()
            ]);

            $errorMessage = 'Erreur lors de la mise à jour du statut: ' . $e->getMessage();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()->withErrors(['status' => $errorMessage]);
        }
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
