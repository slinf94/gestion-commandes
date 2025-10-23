<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\TemporaryCart;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Notifications\OrderStatusChangedNotification;
use App\Helpers\OrderStatusHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Version simplifiée pour éviter l'épuisement mémoire
        $query = $user->orders()->with('items');

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 20);
        $orders = $query->paginate($perPage);

        // Formater les données de manière simplifiée
        $formattedOrders = collect($orders->items())->map(function ($order) {
            $orderData = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status instanceof \App\Enums\OrderStatus ? $order->status->value : $order->status,
                'subtotal' => $order->subtotal,
                'tax_amount' => $order->tax_amount,
                'discount_amount' => $order->discount_amount,
                'shipping_cost' => $order->shipping_cost,
                'total_amount' => $order->total_amount,
                'delivery_address' => $order->delivery_address,
                'delivery_date' => $order->delivery_date,
                'delivery_time_slot' => $order->delivery_time_slot,
                'notes' => $order->notes,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'items_count' => $order->items()->count(),
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'order_id' => $item->order_id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'product_image' => $item->product_image,
                        'product_sku' => $item->product_sku,
                        'product_stock' => $item->product_stock,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                        'product_details' => $item->product_details,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ];
                })->toArray(),
            ];

            return $orderData;
        });

        return response()->json([
            'success' => true,
            'data' => $formattedOrders,
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

        // Utiliser la même logique de session_id que le contrôleur du panier
        $userId = auth()->id();
        if ($userId) {
            $sessionId = 'user_' . $userId;
        } else {
            $sessionId = $request->header('X-Session-ID') ?? $request->get('session_id');
            if (!$sessionId) {
                $ip = $request->ip();
                $userAgent = $request->userAgent();
                $sessionId = 'guest_' . md5($ip . $userAgent . date('Y-m-d'));
            }
        }

        // Debug: Log des informations
        \Log::info('Order creation attempt', [
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'headers' => $request->headers->all(),
            'request_data' => $request->all()
        ]);

        $cartItems = TemporaryCart::with('product')
            ->where('session_id', $sessionId)
            ->where(function($query) {
                $query->where('expires_at', '>', now())
                      ->orWhereNull('expires_at');
            })
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
                $unitPrice = $cartItem->unit_price ?? $cartItem->product->price;
                $totalPrice = $cartItem->quantity * $unitPrice;

                // Récupérer l'image principale du produit
                $mainImage = 'products/placeholder.svg'; // Image par défaut
                if ($cartItem->product->images && is_array($cartItem->product->images)) {
                    $firstImage = $cartItem->product->images[0];
                    if (is_string($firstImage) && !empty($firstImage)) {
                        $mainImage = $firstImage;
                    }
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'product_name' => $cartItem->product->name,
                    'product_image' => $mainImage,
                    'product_sku' => $cartItem->product->sku,
                    'product_stock' => $cartItem->product->stock_quantity,
                    'product_details' => [
                        'name' => $cartItem->product->name,
                        'sku' => $cartItem->product->sku,
                        'description' => $cartItem->product->description,
                        'category_id' => $cartItem->product->category_id,
                        'category_name' => $cartItem->product->category->name ?? null,
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

            // Les notifications seront envoyées en arrière-plan pour éviter les timeouts
            // Ne pas bloquer la réponse pour les notifications

            DB::commit();

            // Retourner les données de base sans charger toutes les relations
            $orderData = $order->toArray();

            // Charger seulement les items avec les données déjà sauvegardées
            $orderData['items'] = $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'order_id' => $item->order_id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'product_image' => $item->product_image,
                    'product_sku' => $item->product_sku,
                    'product_stock' => $item->product_stock,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'product_details' => $item->product_details,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'data' => $orderData,
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
            ->with([
                'items.product.productImages',
                'items.product.category',
                'statusHistory.changedBy'
            ])
            ->findOrFail($id);

        // Formater les données pour inclure toutes les informations nécessaires
        $orderData = $order->toArray();

        // Formater les items avec les détails complets du produit
        $orderData['items'] = $order->items->map(function ($item) {
            $itemData = $item->toArray();

            if ($item->product) {
                $itemData['product_name'] = $item->product->name;
                $itemData['product_image'] = $item->product->main_image;
                $itemData['product_sku'] = $item->product->sku;
                $itemData['product_stock'] = $item->product->stock_quantity;

                // Ajouter les détails du produit complet
                $itemData['product'] = [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'price' => $item->product->price,
                    'stock_quantity' => $item->product->stock_quantity,
                    'main_image' => $item->product->main_image,
                    'all_images' => $item->product->all_images,
                    'category' => $item->product->category ? [
                        'id' => $item->product->category->id,
                        'name' => $item->product->category->name,
                    ] : null,
                ];
            }

            return $itemData;
        })->toArray();

        return response()->json([
            'success' => true,
            'data' => $orderData,
            'message' => 'Commande récupérée avec succès'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|' . OrderStatusHelper::getValidationRule(),
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

        // Envoyer une notification au client si le statut a changé
        if ($oldStatus !== $request->status) {
            try {
                $order->user->notify(new OrderStatusChangedNotification($order, $oldStatus, $request->status));
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'envoi de la notification au client: ' . $e->getMessage());
            }
        }

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
