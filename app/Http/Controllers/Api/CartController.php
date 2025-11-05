<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TemporaryCart;
use App\Models\ProductSimple as Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sessionId = $this->getSessionId($request);
        $userId = auth()->id(); // Peut être null si pas connecté
        
        // Si l'utilisateur est connecté et qu'un session_id différent de user_ est fourni,
        // migrer les items du panier guest vers le panier user
        if ($userId && $sessionId && !str_starts_with($sessionId, 'user_')) {
            $userSessionId = 'user_' . $userId;
            // Migrer les items du panier guest vers le panier user
            TemporaryCart::where('session_id', $sessionId)
                ->where('expires_at', '>', now())
                ->update(['session_id' => $userSessionId]);
            $sessionId = $userSessionId;
        }

        $cartItems = TemporaryCart::with(['product.category'])
            ->where('session_id', $sessionId)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculer les totaux
        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * ($item->product ? $item->product->price : 0);
        });

        $totalItems = $cartItems->sum('quantity');

        // Formater les items pour la réponse JSON
        $formattedItems = $cartItems->map(function($item) {
            return [
                'id' => $item->id,
                'session_id' => $item->session_id,
                'user_id' => $item->user_id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'created_at' => $item->created_at ? $item->created_at->toIso8601String() : now()->toIso8601String(),
                'updated_at' => $item->updated_at ? $item->updated_at->toIso8601String() : now()->toIso8601String(),
                'expires_at' => $item->expires_at->toIso8601String(),
                'product' => $item->product ? [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'main_image' => $item->product->main_image,
                    'product_images' => $item->product->productImages ? $item->product->productImages->map(function($img) {
                        return [
                            'url' => $img->url,
                            'is_principale' => $img->is_principale ?? false,
                        ];
                    })->toArray() : [],
                ] : null,
            ];
        })->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $formattedItems,
                'subtotal' => $subtotal,
                'total_items' => $totalItems,
                'tax_amount' => 0, // TODO: Calculer les taxes
                'shipping_cost' => 0, // TODO: Calculer les frais de livraison
                'total' => $subtotal
            ],
            'message' => 'Panier récupéré avec succès'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::findOrFail($request->product_id);

        // Vérifier la disponibilité en stock
        if ($product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuffisant. Quantité disponible: ' . $product->stock_quantity
            ], 400);
        }

        $sessionId = $this->getSessionId($request);
        $userId = auth()->id(); // Peut être null si pas connecté
        
        // Si l'utilisateur est connecté et qu'un session_id différent de user_ est fourni,
        // migrer les items du panier guest vers le panier user
        if ($userId && $sessionId && !str_starts_with($sessionId, 'user_')) {
            $userSessionId = 'user_' . $userId;
            // Migrer les items du panier guest vers le panier user
            TemporaryCart::where('session_id', $sessionId)
                ->where('expires_at', '>', now())
                ->update(['session_id' => $userSessionId]);
            $sessionId = $userSessionId;
        }

        // Vérifier si l'article existe déjà dans le panier
        $existingItem = TemporaryCart::where('session_id', $sessionId)
            ->where('product_id', $request->product_id)
            ->where('expires_at', '>', now())
            ->first();

        if ($existingItem) {
            // Mettre à jour la quantité
            $newQuantity = $existingItem->quantity + $request->quantity;

            if ($product->stock_quantity < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuffisant. Quantité disponible: ' . $product->stock_quantity
                ], 400);
            }

            $existingItem->update([
                'quantity' => $newQuantity,
                'unit_price' => $product->price,
                'expires_at' => now()->addDays(7) // Expire dans 7 jours
            ]);

            $cartItem = $existingItem;
        } else {
            // Créer un nouvel article
            $cartItem = TemporaryCart::create([
                'session_id' => $sessionId,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'unit_price' => $product->price,
                'expires_at' => now()->addDays(7) // Expire dans 7 jours
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $cartItem->load('product.category'),
            'message' => 'Produit ajouté au panier avec succès'
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $sessionId = $this->getSessionId($request);

        // Utiliser l'ID du TemporaryCart (item du panier), pas le product_id
        $cartItem = TemporaryCart::with('product')
            ->where('id', $id)
            ->where('session_id', $sessionId)
            ->where('expires_at', '>', now())
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Article non trouvé dans le panier'
            ], 404);
        }

        // Vérifier la disponibilité en stock
        if ($cartItem->product && $cartItem->product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuffisant. Quantité disponible: ' . $cartItem->product->stock_quantity
            ], 400);
        }

        $cartItem->update([
            'quantity' => $request->quantity,
            'unit_price' => $cartItem->product ? $cartItem->product->price : $cartItem->unit_price,
            'expires_at' => now()->addDays(7) // Renouveler l'expiration
        ]);

        return response()->json([
            'success' => true,
            'data' => $cartItem->load('product.category'),
            'message' => 'Article mis à jour avec succès'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function remove(Request $request, $id)
    {
        $sessionId = $this->getSessionId($request);

        // Utiliser l'ID du TemporaryCart (item du panier), pas le product_id
        $cartItem = TemporaryCart::where('id', $id)
            ->where('session_id', $sessionId)
            ->where('expires_at', '>', now())
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Article non trouvé dans le panier'
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article supprimé du panier avec succès'
        ]);
    }

    /**
     * Clear all cart items
     */
    public function clear(Request $request)
    {
        $sessionId = $this->getSessionId($request);
        $userId = auth()->id(); // Peut être null si pas connecté

        TemporaryCart::where('session_id', $sessionId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Panier vidé avec succès'
        ]);
    }

    /**
     * Get cart count
     */
    public function count(Request $request)
    {
        $sessionId = $this->getSessionId($request);
        $userId = auth()->id(); // Peut être null si pas connecté

        $count = TemporaryCart::where('session_id', $sessionId)
        ->where('expires_at', '>', now())
        ->sum('quantity');

        return response()->json([
            'success' => true,
            'data' => ['count' => $count],
            'message' => 'Nombre d\'articles récupéré avec succès'
        ]);
    }

    /**
     * Get session ID from request or create new one
     */
    private function getSessionId(Request $request)
    {
        $userId = auth()->id();
        
        // PRIORITÉ: session_id depuis les données de la requête, puis header, puis query param
        $sessionId = $request->input('session_id')
                     ?? $request->header('X-Session-ID')
                     ?? $request->get('session_id');

        // Si l'utilisateur est connecté et qu'un session_id est fourni, l'utiliser
        // Sinon, utiliser user_ + userId pour les utilisateurs connectés
        if ($userId) {
            // Si un session_id est fourni, l'utiliser (pour migration panier guest vers user)
            if ($sessionId) {
                return $sessionId;
            }
            // Sinon, utiliser user_ + userId
            return 'user_' . $userId;
        }

        // Pour les utilisateurs non authentifiés, utiliser le header ou créer un nouveau
        // Si pas de session ID, en créer un nouveau basé sur l'IP et User-Agent
        if (!$sessionId) {
            $ip = $request->ip();
            $userAgent = $request->userAgent();
            $sessionId = 'guest_' . md5($ip . $userAgent . date('Y-m-d'));
        }

        return $sessionId;
    }
}
