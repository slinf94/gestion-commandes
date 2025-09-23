<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'attributeValues.productTypeAttribute'])
            ->where('status', 'active');

        // Filtrage par catégorie
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrage par prix
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filtrage par statut de stock
        if ($request->has('in_stock') && $request->in_stock) {
            $query->where('stock_quantity', '>', 0);
        }

        // Filtrage par produits en vedette
        if ($request->has('featured') && $request->featured) {
            $query->where('is_featured', true);
        }

        // Recherche par nom ou description
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortFields = ['name', 'price', 'created_at', 'stock_quantity'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
            'message' => 'Produits récupérés avec succès'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_alert' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:100|unique:products',
            'barcode' => 'nullable|string|max:100|unique:products',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'status' => 'nullable|in:active,inactive,out_of_stock,discontinued',
            'is_featured' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $product->load(['category', 'attributeValues.productTypeAttribute']),
            'message' => 'Produit créé avec succès'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'attributeValues.productTypeAttribute', 'variants'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Produit récupéré avec succès'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:200',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
            'min_stock_alert' => 'nullable|integer|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'sku' => 'sometimes|string|max:100|unique:products,sku,' . $id,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $id,
            'images' => 'nullable|array',
            'images.*' => 'string',
            'status' => 'sometimes|in:active,inactive,out_of_stock,discontinued',
            'is_featured' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $product->fresh()->load(['category', 'attributeValues.productTypeAttribute']),
            'message' => 'Produit mis à jour avec succès'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Vérifier s'il y a des commandes en cours
        if ($product->orderItems()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer ce produit car il est associé à des commandes'
            ], 400);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produit supprimé avec succès'
        ]);
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $search = $request->q;
        $products = Product::with(['category'])
            ->where('status', 'active')
            ->where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%")
                      ->orWhere('sku', 'LIKE', "%{$search}%")
                      ->orWhere('barcode', 'LIKE', "%{$search}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Recherche effectuée avec succès'
        ]);
    }

    /**
     * Get featured products
     */
    public function featured()
    {
        $products = Product::with(['category'])
            ->where('status', 'active')
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Produits en vedette récupérés avec succès'
        ]);
    }

    /**
     * Get user favorites
     */
    public function favorites()
    {
        $user = auth()->user();
        $favorites = $user->favorites()->with(['product.category'])->get();

        return response()->json([
            'success' => true,
            'data' => $favorites,
            'message' => 'Favoris récupérés avec succès'
        ]);
    }

    /**
     * Add to favorites
     */
    public function addToFavorites(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $productId = $request->product_id;

        // Vérifier si déjà en favori
        if ($user->favorites()->where('product_id', $productId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce produit est déjà dans vos favoris'
            ], 400);
        }

        $user->favorites()->create(['product_id' => $productId]);

        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté aux favoris'
        ]);
    }

    /**
     * Remove from favorites
     */
    public function removeFromFavorites($id)
    {
        $user = auth()->user();
        $favorite = $user->favorites()->where('product_id', $id)->first();

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé dans vos favoris'
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produit retiré des favoris'
        ]);
    }

    /**
     * Check if product is favorite
     */
    public function checkFavorite($id)
    {
        $user = auth()->user();
        $isFavorite = $user->favorites()->where('product_id', $id)->exists();

        return response()->json([
            'success' => true,
            'data' => ['is_favorite' => $isFavorite]
        ]);
    }

    /**
     * Upload product images
     */
    public function uploadImages(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'images' => 'required|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $imagePaths = [];
        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $imagePaths[] = $path;
        }

        $currentImages = $product->images ?? [];
        $product->update(['images' => array_merge($currentImages, $imagePaths)]);

        return response()->json([
            'success' => true,
            'data' => $product->images,
            'message' => 'Images uploadées avec succès'
        ]);
    }

}
