<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\TemporaryCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = $user->orders()->with(['items.product', 'statusHistory.changedBy']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 20);
        $orders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
            'message' => 'Commandes récupérées avec succès'
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_address' => 'required|array',
            'delivery_address.street' => 'required|string',
            'delivery_address.city' => 'required|string',
            'delivery_address.country' => 'required|string',
            'delivery_date' => 'nullable|date|after:today',
            'delivery_time_slot' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $sessionId = $request->header('X-Session-ID') ?? $request->get('session_id');

        // Debug: Log des informations
        \Log::info('Order creation attempt', [
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'headers' => $request->headers->all(),
            'request_data' => $request->all()
        ]);

        $cartItems = TemporaryCart::with('product')
            ->where('session_id', $sessionId)
            ->where('expires_at', '>', now())
            ->get();

        // Debug: Log des articles du panier
        \Log::info('Cart items found', [
            'session_id' => $sessionId,
            'cart_items_count' => $cartItems->count(),
            'cart_items' => $cartItems->toArray()
        ]);

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Votre panier est vide'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $subtotal = $cartItems->sum(function($item) {
                return $item->quantity * $item->product->price;
            });

            $taxAmount = 0;
            $shippingCost = 0;
            $discountAmount = 0;
            $totalAmount = $subtotal + $taxAmount + $shippingCost - $discountAmount;

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'delivery_address' => $request->delivery_address,
                'delivery_date' => $request->delivery_date,
                'delivery_time_slot' => $request->delivery_time_slot,
                'notes' => $request->notes,
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price ?? $cartItem->product->price,
                    'total_price' => $cartItem->quantity * ($cartItem->unit_price ?? $cartItem->product->price),
                    'product_details' => [
                        'name' => $cartItem->product->name,
                        'sku' => $cartItem->product->sku,
                        'description' => $cartItem->product->description,
                    ],
                ]);

                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'new_status' => 'pending',
                'comment' => 'Commande créée',
                'changed_by' => $user->id,
            ]);

            TemporaryCart::where('session_id', $sessionId)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $order->load(['items.product', 'statusHistory']),
                'message' => 'Commande créée avec succès'
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la commande: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $order = auth()->user()->orders()
            ->with(['items.product', 'statusHistory.changedBy'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Commande récupérée avec succès'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldStatus = $order->status;
        $order->update([
            'status' => $request->status,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'previous_status' => $oldStatus,
            'new_status' => $request->status,
            'comment' => $request->comment,
            'changed_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $order->fresh()->load(['items.product', 'statusHistory']),
            'message' => 'Statut de la commande mis à jour avec succès'
        ]);
    }

    public function history(Request $request)
    {
        $user = auth()->user();

        $query = $user->orders()
            ->with(['items.product'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'message' => 'Historique des commandes récupéré avec succès'
        ]);
    }

    public function statusHistory($id)
    {
        $order = auth()->user()->orders()->findOrFail($id);
        $history = $order->statusHistory()->with('changedBy')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $history,
            'message' => 'Historique des statuts récupéré avec succès'
        ]);
    }

    public function adminIndex(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'statusHistory.changedBy']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'message' => 'Commandes récupérées avec succès'
        ]);
    }

    public function cancelOrder(Request $request, $id)
    {
        $order = auth()->user()->orders()->findOrFail($id);

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'annuler cette commande'
            ], 400);
        }

        $order->update(['status' => 'cancelled']);

        // Enregistrer l'historique
        $order->statusHistory()->create([
            'previous_status' => $order->getOriginal('status'),
            'new_status' => 'cancelled',
            'comment' => 'Commande annulée par l\'utilisateur',
            'changed_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Commande annulée avec succès'
        ]);
    }

    public function deleteOrder(Request $request, $id)
    {
        $order = auth()->user()->orders()->findOrFail($id);

        // Seules les commandes annulées ou en attente peuvent être supprimées
        if (!in_array($order->status, ['pending', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer cette commande'
            ], 400);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commande supprimée avec succès'
        ]);
    }
}
