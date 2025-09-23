<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TemporaryCart;
use App\Models\Product;
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
        $userId = auth()->id();

        $cartItems = TemporaryCart::with(['product.category'])
            ->where(function($query) use ($sessionId, $userId) {
                $query->where('session_id', $sessionId);
                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculer les totaux
        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * ($item->product ? $item->product->price : 0);
        });

        $totalItems = $cartItems->sum('quantity');

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
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
            'selected_attributes' => 'nullable|array',
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
        $userId = auth()->id();

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
                'selected_attributes' => $request->selected_attributes,
                'user_id' => $userId,
                'expires_at' => now()->addDays(7) // Expire dans 7 jours
            ]);

            $cartItem = $existingItem;
        } else {
            // Créer un nouvel article
            $cartItem = TemporaryCart::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'selected_attributes' => $request->selected_attributes,
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

        $cartItem = TemporaryCart::with('product')->findOrFail($id);
        
        // Vérifier la disponibilité en stock
        if ($cartItem->product && $cartItem->product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuffisant. Quantité disponible: ' . $cartItem->product->stock_quantity
            ], 400);
        }

        $cartItem->update([
            'quantity' => $request->quantity,
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
    public function remove($id)
    {
        $cartItem = TemporaryCart::findOrFail($id);
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
        $userId = auth()->id();

        TemporaryCart::where(function($query) use ($sessionId, $userId) {
            $query->where('session_id', $sessionId);
            if ($userId) {
                $query->orWhere('user_id', $userId);
            }
        })->delete();

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
        $userId = auth()->id();

        $count = TemporaryCart::where(function($query) use ($sessionId, $userId) {
            $query->where('session_id', $sessionId);
            if ($userId) {
                $query->orWhere('user_id', $userId);
            }
        })
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
        $sessionId = $request->header('X-Session-ID') 
            ?? $request->get('session_id') 
            ?? session()->getId();

        // Si pas de session ID, en créer un nouveau
        if (!$sessionId) {
            $sessionId = Str::uuid();
        }

        return $sessionId;
    }
}