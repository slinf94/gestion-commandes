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
        $query = Product::with(['category', 'attributeValues.productTypeAttribute', 'productImages', 'prices']);

        // Filtrage par statut (par défaut: active, mais peut être changé)
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        } else {
            // Par défaut, ne montrer que les produits actifs
            $query->where('status', 'active');
        }

        // Filtrage par catégorie
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrage par type de produit
        if ($request->has('product_type_id') && $request->product_type_id) {
            $query->where('product_type_id', $request->product_type_id);
        }

        // Filtrage par prix (min/max)
        if ($request->has('price_min') && $request->price_min) {
            $query->where('price', '>=', $request->price_min);
        } elseif ($request->has('min_price') && $request->min_price) {
            // Compatibilité avec l'ancien paramètre
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('price_max') && $request->price_max) {
            $query->where('price', '<=', $request->price_max);
        } elseif ($request->has('max_price') && $request->max_price) {
            // Compatibilité avec l'ancien paramètre
            $query->where('price', '<=', $request->max_price);
        }

        // Filtrage par disponibilité stock
        if ($request->has('stock_available')) {
            if ($request->stock_available == 'yes') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($request->stock_available == 'no') {
                $query->where('stock_quantity', '<=', 0);
            }
        } elseif ($request->has('in_stock')) {
            // Compatibilité avec l'ancien paramètre
            if ($request->in_stock) {
                $query->where('stock_quantity', '>', 0);
            }
        } else {
            // Par défaut, ne pas afficher les produits épuisés (sauf si status=draft)
            if (!$request->has('status') || $request->status !== 'draft') {
                $query->where('stock_quantity', '>', 0);
            }
        }

        // Filtres avancés pour téléphones
        if ($request->has('brand') && $request->brand) {
            $query->where('brand', $request->brand);
        }

        if ($request->has('range') && $request->range) {
            $query->where('range', $request->range);
        }

        if ($request->has('format') && $request->format) {
            $query->where('format', $request->format);
        }

        // Filtres avancés pour accessoires
        if ($request->has('type_accessory') && $request->type_accessory) {
            $query->where('type_accessory', $request->type_accessory);
        }

        if ($request->has('compatibility') && $request->compatibility) {
            $query->where('compatibility', $request->compatibility);
        }

        // Filtrage par produits en vedette
        if ($request->has('featured') && $request->featured) {
            $query->where('is_featured', true);
        }

        // Recherche par nom, description, SKU, marque, gamme
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%")
                  ->orWhere('brand', 'LIKE', "%{$search}%")
                  ->orWhere('range', 'LIKE', "%{$search}%");
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

        // Formater les données de manière optimisée
        $formattedProducts = collect($products->items())->map(function ($product) {
            $productData = $product->toArray();

            // Construire les URLs d'images depuis S3
            $disk = \Storage::disk('s3');

            // Formater les images avec URLs complètes (optimisé)
            if (isset($productData['images']) && is_array($productData['images'])) {
                $productData['images'] = array_values(array_filter(array_map(function ($image) use ($disk) {
                    if (empty($image)) return null;
                    if (str_starts_with($image, 'http')) return $image;
                    return $disk->url($image);
                }, $productData['images'])));
            }

            // Ajouter l'image principale (optimisé)
            $productData['main_image'] = $product->main_image ?: $disk->url('products/placeholder.jpg');

            // Ajouter les prix par quantité (style Alibaba)
            $productData['quantity_prices'] = $product->prices->map(function ($price) {
                return [
                    'min_quantity' => $price->min_quantity,
                    'max_quantity' => $price->max_quantity,
                    'price' => (float) $price->price,
                    'discount_percentage' => (float) $price->discount_percentage,
                    'discounted_price' => (float) $price->discounted_price,
                ];
            })->sortBy('min_quantity')->values();

            // Optimisation: ne pas charger all_images pour la liste
            unset($productData['all_images']);

            return $productData;
        });

        return response()->json([
            'success' => true,
            'data' => $formattedProducts,
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
        // Validation intelligente selon le statut (compatible Flutter)
        if ($request->status === 'draft') {
            // Validation assouplie pour les produits en brouillon
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|in:active,inactive,draft',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'stock_quantity' => 'nullable|integer|min:0',
                'cost_price' => 'nullable|numeric|min:0',
                'min_stock_alert' => 'nullable|integer|min:0',
                'product_type_id' => 'nullable|exists:product_types,id',
                'sku' => 'nullable|string|max:100|unique:products,sku',
                'barcode' => 'nullable|string|max:100|unique:products,barcode',
                'brand' => 'nullable|string|max:100',
                'range' => 'nullable|string|max:100',
                'format' => 'nullable|string|max:100',
                'type_accessory' => 'nullable|string|max:100',
                'compatibility' => 'nullable|string|max:100',
                'images' => 'nullable|array',
                'images.*' => 'string',
                'is_featured' => 'nullable|boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'tags' => 'nullable|array',
                'tags.*' => 'string',
            ]);
        } else {
            // Validation stricte pour les produits actifs/inactifs
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|in:active,inactive,draft',
                'description' => 'nullable|string',
                'cost_price' => 'nullable|numeric|min:0',
                'min_stock_alert' => 'nullable|integer|min:0',
                'product_type_id' => 'nullable|exists:product_types,id',
                'sku' => 'nullable|string|max:100|unique:products,sku',
                'barcode' => 'nullable|string|max:100|unique:products,barcode',
                'brand' => 'nullable|string|max:100',
                'range' => 'nullable|string|max:100',
                'format' => 'nullable|string|max:100',
                'type_accessory' => 'nullable|string|max:100',
                'compatibility' => 'nullable|string|max:100',
                'images' => 'nullable|array',
                'images.*' => 'string',
                'is_featured' => 'nullable|boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'tags' => 'nullable|array',
                'tags.*' => 'string',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Générer le SKU automatiquement si non fourni
        $validated['sku'] = $request->sku ?? Product::generateSku();
        
        // Générer le slug automatiquement
        $validated['slug'] = Product::generateSlug($validated['name']);

        // Créer le produit
        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'product' => $product->load(['category', 'attributeValues.productTypeAttribute']),
            'message' => 'Produit créé avec succès'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'attributeValues.productTypeAttribute', 'variants', 'prices'])
            ->findOrFail($id);

        $productData = $product->toArray();

        // Formater les images avec URLs complètes
        if (isset($productData['images']) && is_array($productData['images'])) {
            $productData['images'] = array_map(function ($image) {
                if (empty($image)) return null;
                if (str_starts_with($image, 'http')) return $image;
                return \Storage::disk('s3')->url($image);
            }, $productData['images']);
            $productData['images'] = array_filter($productData['images']);
        }

        // Ajouter l'image principale
        $productData['main_image'] = $product->main_image;
        $productData['all_images'] = $product->all_images;

        // Ajouter les prix par quantité (style Alibaba) avec formatage complet
        $productData['quantity_prices'] = $product->prices->map(function ($price) {
            $quantityRange = $price->min_quantity;
            if ($price->max_quantity) {
                $quantityRange .= ' - ' . $price->max_quantity;
            } else {
                $quantityRange .= '+';
            }
            
            return [
                'min_quantity' => $price->min_quantity,
                'max_quantity' => $price->max_quantity,
                'price' => (float) $price->price,
                'discount_percentage' => (float) $price->discount_percentage,
                'discounted_price' => (float) $price->discounted_price,
                'quantity_range' => $quantityRange,
            ];
        })->sortBy('min_quantity')->values();

        return response()->json([
            'success' => true,
            'data' => $productData,
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
            'q' => 'required|string|min:1',
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
            ->where('stock_quantity', '>', 0) // Masquer les produits épuisés
            ->where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%")
                      ->orWhere('sku', 'LIKE', "%{$search}%")
                      ->orWhere('barcode', 'LIKE', "%{$search}%");
            })
            ->limit(20)
            ->get();

        // Formater les données avec les URLs d'images complètes
        $formattedProducts = $products->map(function ($product) {
            $productData = $product->toArray();

            // Formater les images avec URLs complètes
            if (isset($productData['images']) && is_array($productData['images'])) {
                $productData['images'] = array_map(function ($image) {
                    if (empty($image)) return null;
                    if (str_starts_with($image, 'http')) return $image;
                    return \Storage::disk('s3')->url($image);
                }, $productData['images']);
                $productData['images'] = array_filter($productData['images']);
            }

            // Ajouter l'image principale
            $productData['main_image'] = $product->main_image;
            $productData['all_images'] = $product->all_images;

            return $productData;
        });

        return response()->json([
            'success' => true,
            'data' => $formattedProducts,
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
            ->where('stock_quantity', '>', 0) // Masquer les produits épuisés
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
        try {
            $user = auth()->user();

            // Version ultra-simplifiée pour éviter l'épuisement mémoire
            $favorites = $user->favorites()->get();

            // Extraire les produits des favoris et les formater sans relations
            $products = [];
            foreach ($favorites as $favorite) {
                try {
                    // Récupérer le produit directement par ID sans relations
                    $product = \App\Models\ProductSimple::find($favorite->product_id);
                    if ($product) {
                        $productData = [
                            'id' => $product->id,
                            'name' => $product->name,
                            'description' => $product->description,
                            'price' => $product->price,
                            'sku' => $product->sku,
                            'stock_quantity' => $product->stock_quantity,
                            'status' => $product->status,
                            'is_featured' => $product->is_featured,
                            'created_at' => $product->created_at,
                            'updated_at' => $product->updated_at,
                        ];

                        // Formater les images de manière robuste
                        $productData['main_image'] = $this->formatProductImage($product);
                        $productData['images'] = $this->formatProductImages($product);

                        $products[] = $productData;
                    }
                } catch (\Exception $e) {
                    \Log::error('Erreur lors du traitement du favori ID: ' . $favorite->id . ' - ' . $e->getMessage());
                    // Continuer avec les autres favoris même si un échoue
                }
            }

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Favoris récupérés avec succès'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur dans favorites(): ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des favoris',
                'data' => []
            ], 500);
        }
    }

    /**
     * Formater l'image principale d'un produit
     */
    private function formatProductImage($product)
    {
        try {
            if ($product->images && is_array($product->images) && count($product->images) > 0) {
                $firstImage = $product->images[0];
                if (is_string($firstImage) && !empty($firstImage)) {
                    // Si c'est une URL complète, l'utiliser directement
                    if (str_starts_with($firstImage, 'http')) {
                        return $firstImage;
                    }
                    // Sinon, générer l'URL S3
                    return \Storage::disk('s3')->url($firstImage);
                }
            }
            // Image par défaut
            return \Storage::disk('s3')->url('products/placeholder.svg');
        } catch (\Exception $e) {
            \Log::error('Erreur formatProductImage: ' . $e->getMessage());
            return url('storage/products/placeholder.svg');
        }
    }

    /**
     * Formater toutes les images d'un produit
     */
    private function formatProductImages($product)
    {
        try {
            if ($product->images && is_array($product->images) && count($product->images) > 0) {
                $formattedImages = [];
                foreach (array_slice($product->images, 0, 3) as $image) { // Limiter à 3 images
                    if (is_string($image) && !empty($image)) {
                        // Si c'est une URL complète, l'utiliser directement
                        if (str_starts_with($image, 'http')) {
                            $formattedImages[] = $image;
                        } else {
                            // Sinon, générer l'URL S3
                            $formattedImages[] = \Storage::disk('s3')->url($image);
                        }
                    }
                }
                return $formattedImages;
            }
            return [];
        } catch (\Exception $e) {
            \Log::error('Erreur formatProductImages: ' . $e->getMessage());
            return [];
        }
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
