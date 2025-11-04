<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderStatusHistory;
use App\Notifications\OrderStatusChangedNotification;
use App\Notifications\NewOrderNotification;
use App\Helpers\OrderStatusHelper;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('prenom', 'like', "%{$search}%")
                                ->orWhere('nom', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('numero_telephone', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortFields = ['id', 'created_at', 'total', 'status'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'id';
        }

        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 20);
        $orders = $query->paginate($perPage)->appends($request->query());

        // Statistiques
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        // Version ultra-simplifiée pour éviter l'épuisement mémoire
        $order->load(['user' => function($query) {
                $query->withTrashed(); // Inclure les utilisateurs supprimés
            }, 'items', 'statusHistory']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Annuler une commande
     */
    public function cancel(Request $request, Order $order)
    {
        try {
            // Vérifier si la commande peut être annulée
            if ($order->status === 'cancelled') {
                return redirect()->back()
                    ->with('error', 'Cette commande est déjà annulée.');
            }

            if ($order->status === 'delivered') {
                return redirect()->back()
                    ->with('error', 'Impossible d\'annuler une commande déjà livrée.');
            }

            // Annuler la commande
            $oldStatus = $order->status;
            $success = $order->changeStatus(OrderStatus::CANCELLED, 'Commande annulée par l\'administrateur', auth()->id());

            if (!$success) {
                throw new \Exception('Erreur lors de l\'annulation de la commande');
            }

            // Envoyer une notification au client
            try {
                $order->user->notify(new OrderStatusChangedNotification($order, $oldStatus, OrderStatus::CANCELLED));
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'envoi de la notification d\'annulation: ' . $e->getMessage());
            }

            return redirect()->route('admin.orders.index')
                ->with('success', 'Commande annulée avec succès.');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'annulation de la commande: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Erreur lors de l\'annulation de la commande: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        try {
            $request->validate([
                'status' => 'required|string|in:' . implode(',', array_column(OrderStatus::cases(), 'value')),
                'comment' => 'nullable|string|max:500',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Statut invalide: ' . $request->status,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            $newStatus = OrderStatus::from($request->status);
            $oldStatus = $order->status;

            // Vérifier si le changement de statut est autorisé
            if (!$order->canChangeStatusTo($newStatus)) {
                $oldStatusLabel = $oldStatus instanceof \App\Enums\OrderStatus ? $oldStatus->getLabel() : $order->getStatusLabel();
                $newStatusLabel = $newStatus instanceof \App\Enums\OrderStatus ? $newStatus->getLabel() : $order->getStatusLabel();
                $message = "Impossible de changer le statut de \"{$oldStatusLabel}\" vers \"{$newStatusLabel}\"";

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
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
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'order' => $order->fresh(),
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

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
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
        try {
            // Vérifier que la commande est annulée avant de permettre la suppression
            $status = is_string($order->status) ? $order->status : ($order->status->value ?? $order->status);
            
            if ($status !== 'cancelled') {
                if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossible de supprimer une commande qui n\'est pas annulée. Veuillez d\'abord annuler la commande.'
                    ], 422);
                }
                
                return redirect()->back()
                    ->with('error', 'Impossible de supprimer une commande qui n\'est pas annulée. Veuillez d\'abord annuler la commande.');
            }

            $orderId = $order->id;
            $order->delete();
            
            if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Commande supprimée avec succès'
                ]);
            }
            
            return redirect()->route('admin.orders.index')
                ->with('success', 'Commande #' . $orderId . ' supprimée avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression de la commande: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'user_id' => auth()->id()
            ]);
            
            if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de la commande: ' . $e->getMessage());
        }
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
