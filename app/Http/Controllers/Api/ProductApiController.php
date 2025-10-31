<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductApiController extends Controller
{
    /**
     * Get all products with filters.
     */
    public function index(Request $request)
    {
        // Augmenter la limite de mémoire pour cette requête
        ini_set('memory_limit', '2G'); // Augmenter à 2GB directement
        ini_set('max_execution_time', '300');
        
        try {
            \Log::info('ProductApiController@index: Début de la requête');
            
            // VERSION SIMPLIFIÉE - Utiliser DB::table directement pour éviter tout problème Eloquent
            // Augmenter les limites pour afficher plus de produits
            // AUGMENTER SIGNIFICATIVEMENT pour charger TOUS les produits en une seule requête
            $perPage = min($request->get('per_page', 200), 500); // 200 par défaut, max 500 pour charger TOUS les produits
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $perPage;
            
            // Requête SQL directe pour les produits
            $productsQuery = "SELECT id, name, description, price, cost_price, wholesale_price, 
                retail_price, min_wholesale_quantity, stock_quantity, min_stock_alert,
                category_id, product_type_id, sku, barcode, status, is_featured,
                meta_title, meta_description, images, tags, created_at, updated_at
                FROM products 
                WHERE status = 'active' 
                AND stock_quantity > 0 
                AND deleted_at IS NULL 
                ORDER BY updated_at DESC, created_at DESC 
                LIMIT ? OFFSET ?";
            
            $products = DB::select($productsQuery, [$perPage, $offset]);
            $totalQuery = "SELECT COUNT(*) as total FROM products 
                WHERE status = 'active' AND stock_quantity > 0 AND deleted_at IS NULL";
            $totalResult = DB::selectOne($totalQuery);
            $total = $totalResult->total ?? 0;
            
            \Log::info('ProductApiController@index: Produits récupérés', [
                'count' => count($products),
                'total' => $total
            ]);

            // Charger les images pour tous les produits en une seule requête
            $productIds = array_column($products, 'id');
            $productImagesData = [];
            
            if (!empty($productIds)) {
                try {
                    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                    $images = DB::select("SELECT id, product_id, url, type FROM product_images WHERE product_id IN ($placeholders)", $productIds);
                    
                    foreach ($images as $img) {
                        $productImagesData[$img->product_id][] = [
                            'id' => $img->id,
                            'url' => $img->url,
                            'type' => $img->type,
                        ];
                    }
                    unset($images);
                } catch (\Exception $e) {
                    \Log::warning('Erreur chargement productImages: ' . $e->getMessage());
                }
            }

            // Formater les produits - version simplifiée sans Eloquent
            $formattedProducts = [];
            $maxItems = count($products); // Formater TOUS les produits récupérés, pas de limite artificielle
            
            \Log::info('ProductApiController@index: Formatage de ' . $maxItems . ' produits');
            
            // Formater tous les produits avec gestion de la mémoire
            for ($i = 0; $i < $maxItems; $i++) {
                $product = (object)$products[$i];
                $productId = $product->id;
                
                try {
                    // Formater directement depuis les données brutes
                    $productData = $this->formatProductFromRaw($product, $productImagesData[$productId] ?? []);
                    $formattedProducts[] = $productData;
                    
                    // Garbage collection tous les 50 produits pour éviter l'épuisement de mémoire
                    if ($i > 0 && $i % 50 == 0 && function_exists('gc_collect_cycles')) {
                        gc_collect_cycles();
                        \Log::debug('ProductApiController@index: GC après produit ' . $i);
                    }
                } catch (\Throwable $e) {
                    \Log::error('Erreur formatage produit ' . $productId . ': ' . $e->getMessage());
                    // Ignorer ce produit en cas d'erreur
                }
            }
            
            // Libérer la mémoire après formatage
            unset($products, $productImagesData);
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
            
            // Calculer la pagination
            $lastPage = ceil($total / $perPage);
            $from = $offset + 1;
            $to = min($offset + $perPage, $total);
            
            \Log::info('ProductApiController@index: Terminé avec succès', [
                'formatted_count' => count($formattedProducts),
                'total' => $total
            ]);

            return response()->json([
                'success' => true,
                'data' => array_values($formattedProducts), // Convertir explicitement en tableau indexé
                'pagination' => [
                    'current_page' => (int)$page,
                    'last_page' => (int)$lastPage,
                    'per_page' => (int)$perPage,
                    'total' => (int)$total,
                    'from' => $from > 0 ? (int)$from : null,
                    'to' => $to > 0 ? (int)$to : null,
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('Erreur dans ProductApiController@index', [
                'message' => $e->getMessage(),
                'type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'memory' => memory_get_usage(true) / 1024 / 1024 . ' MB',
                'memory_limit' => ini_get('memory_limit'),
            ]);
            
            // Si erreur de mémoire, augmenter encore plus
            if (strpos(strtolower($e->getMessage()), 'memory') !== false || 
                strpos(strtolower($e->getMessage()), 'allocated') !== false ||
                strpos(strtolower($e->getMessage()), 'exhausted') !== false) {
                ini_set('memory_limit', '2G');
                \Log::warning('Limite de mémoire augmentée à 2G suite à une erreur mémoire');
            }
            
            // Retourner une réponse plus détaillée en mode debug
            $response = [
                'success' => false,
                'message' => config('app.debug') 
                    ? 'Erreur: ' . $e->getMessage() . ' dans ' . basename($e->getFile()) . ':' . $e->getLine()
                    : 'Erreur du serveur: 500',
                'data' => [],
            ];
            
            if (config('app.debug')) {
                $response['debug'] = [
                    'error' => $e->getMessage(),
                    'type' => get_class($e),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine(),
                    'memory' => memory_get_usage(true) / 1024 / 1024 . ' MB',
                ];
            }
            
            return response()->json($response, 500);
        }
    }

    /**
     * Get a specific product.
     */
    public function show($id)
    {
        // Augmenter la limite de mémoire pour cette requête
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', '300');
        
        try {
            \Log::info('ProductApiController@show: Début pour produit ' . $id);
            
            // VERSION SIMPLIFIÉE - Utiliser DB::select directement
            $product = DB::selectOne("SELECT id, name, description, price, cost_price, wholesale_price, 
                retail_price, min_wholesale_quantity, stock_quantity, min_stock_alert,
                category_id, product_type_id, sku, barcode, status, is_featured,
                meta_title, meta_description, images, tags, created_at, updated_at
                FROM products 
                WHERE id = ? 
                AND status = 'active' 
                AND stock_quantity > 0 
                AND deleted_at IS NULL", [$id]);

            if (!$product) {
                \Log::warning('ProductApiController@show: Produit non trouvé: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé ou en rupture de stock'
                ], 404);
            }

            // Charger les images
            $productImages = DB::select("SELECT id, product_id, url, type FROM product_images WHERE product_id = ?", [$id]);
            $productImagesData = [];
            foreach ($productImages as $img) {
                $productImagesData[] = [
                    'id' => $img->id,
                    'url' => $img->url,
                    'type' => $img->type,
                ];
            }

            // Formater le produit depuis les données brutes
            $productData = $this->formatProductFromRaw($product, $productImagesData);
            
            // Ajouter les attributs (optionnel, peut être chargé plus tard si nécessaire)
            try {
                $attributes = DB::select("
                    SELECT pav.id, pav.value, a.id as attribute_id, a.name as attribute_name
                    FROM product_attribute_values pav
                    JOIN attributes a ON pav.attribute_id = a.id
                    WHERE pav.product_id = ?
                ", [$id]);
                
                $productData['attributes'] = array_map(function($attr) {
                    return [
                        'id' => $attr->id,
                        'value' => $attr->value,
                        'attribute' => [
                            'id' => $attr->attribute_id,
                            'name' => $attr->attribute_name,
                        ]
                    ];
                }, $attributes);
            } catch (\Exception $e) {
                \Log::warning('Erreur chargement attributs: ' . $e->getMessage());
                $productData['attributes'] = [];
            }

            // Ajouter les variants (optionnel)
            try {
                $variants = DB::select("
                    SELECT id, variant_name, sku, price, stock_quantity, images, attributes, is_active
                    FROM product_variants
                    WHERE product_id = ? AND is_active = 1
                ", [$id]);
                
                $productData['variants'] = array_map(function($v) {
                    return [
                        'id' => $v->id,
                        'variant_name' => $v->variant_name,
                        'sku' => $v->sku,
                        'price' => (float)$v->price,
                        'stock_quantity' => (int)$v->stock_quantity,
                        'images' => is_string($v->images) ? json_decode($v->images, true) : ($v->images ?? []),
                        'attributes' => is_string($v->attributes) ? json_decode($v->attributes, true) : ($v->attributes ?? []),
                        'is_active' => (bool)$v->is_active,
                    ];
                }, $variants);
            } catch (\Exception $e) {
                \Log::warning('Erreur chargement variants: ' . $e->getMessage());
                $productData['variants'] = [];
            }

            \Log::info('ProductApiController@show: Terminé avec succès pour produit ' . $id);

            return response()->json([
                'success' => true,
                'data' => $productData
            ]);
        } catch (\Throwable $e) {
            \Log::error('Erreur dans ProductApiController@show', [
                'product_id' => $id,
                'message' => $e->getMessage(),
                'type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => config('app.debug') 
                    ? 'Erreur: ' . $e->getMessage()
                    : 'Erreur du serveur: 500',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get categories.
     */
    public function categories()
    {
        $categories = Category::where('is_active', true)
            ->with(['children' => function($query) {
                $query->where('is_active', true);
            }])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get product types.
     */
    public function productTypes()
    {
        $productTypes = ProductType::where('is_active', true)
            ->with('category')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $productTypes
        ]);
    }

    /**
     * Get filterable attributes.
     */
    public function attributes(Request $request)
    {
        $query = Attribute::where('is_active', true)
            ->where('is_filterable', true);

        // Filtrer par catégorie si spécifiée
        if ($request->filled('category_id')) {
            $query->whereHas('productTypes', function($q) use ($request) {
                $q->where('category_id', $request->get('category_id'));
            });
        }

        // Filtrer par type de produit si spécifié
        if ($request->filled('product_type_id')) {
            $query->whereHas('productTypes', function($q) use ($request) {
                $q->where('product_type_id', $request->get('product_type_id'));
            });
        }

        $attributes = $query->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => $attributes
        ]);
    }

    /**
     * Get attribute values for filtering.
     */
    public function attributeValues(Request $request)
    {
        $attributeId = $request->get('attribute_id');
        $categoryId = $request->get('category_id');
        $productTypeId = $request->get('product_type_id');

        $query = Product::with('attributeValues')
            ->where('status', 'active');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($productTypeId) {
            $query->where('product_type_id', $productTypeId);
        }

        $products = $query->get();

        $values = [];
        foreach ($products as $product) {
            foreach ($product->attributeValues as $attributeValue) {
                if ($attributeValue->attribute_id == $attributeId) {
                    $values[] = $attributeValue->value;
                }
            }
        }

        $uniqueValues = array_unique(array_filter($values));
        sort($uniqueValues);

        return response()->json([
            'success' => true,
            'data' => $uniqueValues
        ]);
    }

    /**
     * Search products.
     */
    public function search(Request $request)
    {
        $searchTerm = $request->get('q');

        if (empty($searchTerm)) {
            return response()->json([
                'success' => false,
                'message' => 'Terme de recherche requis'
            ], 400);
        }

        $query = Product::with(['category', 'productType', 'productImages', 'attributeValues.attribute'])
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0) // Ne retourner que les produits en stock
            ->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%");
            });

        $perPage = $request->get('per_page', 20);
        $products = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]
        ]);
    }

    /**
     * Get featured products.
     */
    public function featured(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $products = \App\Models\ProductSimple::where('status', 'active')
            ->where('is_featured', true)
            ->where('stock_quantity', '>', 0) // Ne retourner que les produits en stock
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Get products by category.
     */
    public function byCategory(Category $category, Request $request)
    {
        $query = Product::with(['category', 'productType', 'productImages', 'attributeValues.attribute'])
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0) // Ne retourner que les produits en stock
            ->where('category_id', $category->id);

        $this->applyFilters($query, $request);

        $perPage = $request->get('per_page', 20);
        $products = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]
        ]);
    }

    /**
     * Get products by type.
     */
    public function byType(ProductType $productType, Request $request)
    {
        $query = Product::with(['category', 'productType', 'productImages', 'attributeValues.attribute'])
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0) // Ne retourner que les produits en stock
            ->where('product_type_id', $productType->id);

        $this->applyFilters($query, $request);

        $perPage = $request->get('per_page', 20);
        $products = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'product_type' => $productType,
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]
        ]);
    }

    /**
     * Apply filters to the query.
     */
    private function applyFilters($query, Request $request)
    {
        // Filtre par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        // Filtre par type de produit
        if ($request->filled('product_type_id')) {
            $query->where('product_type_id', $request->get('product_type_id'));
        }

        // Filtre par prix
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->get('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->get('max_price'));
        }

        // Filtre par disponibilité (si in_stock=false, on peut afficher les produits épuisés, sinon filtrer)
        // Note: Par défaut, on filtre déjà les produits épuisés dans index(), mais on permet de les voir si demandé explicitement
        if ($request->filled('include_out_of_stock') && $request->get('include_out_of_stock') == true) {
            // Si explicitement demandé, on peut afficher les produits épuisés
        } else {
            // Sinon, ne montrer que les produits en stock
            $query->where('stock_quantity', '>', 0);
        }

        // Filtres par attributs
        $attributeFilters = $request->get('attributes', []);
        if (!empty($attributeFilters)) {
            foreach ($attributeFilters as $attributeName => $values) {
                if (!empty($values)) {
                    $query->whereHas('attributeValues', function($q) use ($attributeName, $values) {
                        $q->whereHas('attribute', function($subQ) use ($attributeName) {
                            $subQ->where('name', $attributeName);
                        })->whereIn('value', $values);
                    });
                }
            }
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'stock':
                $query->orderBy('stock_quantity', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
    }

    /**
     * Formater un produit depuis des données brutes (objet stdClass) - VERSION SIMPLIFIÉE
     */
    private function formatProductFromRaw($product, $productImagesRaw = [])
    {
        try {
            // Extraire les images
            $images = [];
            if (!empty($productImagesRaw)) {
                foreach ($productImagesRaw as $imgData) {
                    $imageUrl = $imgData['url'] ?? null;
                    if ($imageUrl && !empty(trim($imageUrl))) {
                        $images[] = str_starts_with($imageUrl, 'http') 
                            ? $imageUrl 
                            : url('storage/' . ltrim($imageUrl, '/'));
                    }
                }
            }
            
            // Images depuis le champ JSON
            if (empty($images) && !empty($product->images)) {
                $productImages = is_string($product->images) 
                    ? json_decode($product->images, true) 
                    : $product->images;
                if (is_array($productImages)) {
                    foreach ($productImages as $image) {
                        if (is_string($image) && !empty($image)) {
                            $images[] = str_starts_with($image, 'http') 
                                ? $image 
                                : url('storage/' . ltrim($image, '/'));
                        }
                    }
                }
            }
            
            // Tags
            $tags = [];
            if (!empty($product->tags)) {
                $tags = is_string($product->tags) 
                    ? json_decode($product->tags, true) 
                    : $product->tags;
                $tags = is_array($tags) ? $tags : [];
            }
            
            // Category et ProductType via requêtes simples
            $category = null;
            if (!empty($product->category_id)) {
                try {
                    $cat = DB::table('categories')->where('id', $product->category_id)->first(['id', 'name']);
                    if ($cat) {
                        $category = ['id' => $cat->id, 'name' => $cat->name];
                    }
                } catch (\Exception $e) {
                    // Ignorer
                }
            }
            
            $productType = null;
            if (!empty($product->product_type_id)) {
                try {
                    $pt = DB::table('product_types')->where('id', $product->product_type_id)->first(['id', 'name']);
                    if ($pt) {
                        $productType = ['id' => $pt->id, 'name' => $pt->name];
                    }
                } catch (\Exception $e) {
                    // Ignorer
                }
            }
            
            // Dates
            $createdAt = $product->created_at ?? null;
            $updatedAt = $product->updated_at ?? null;
            
            return [
                'id' => (int)$product->id,
                'name' => (string)($product->name ?? ''),
                'description' => $product->description ?? null,
                'price' => (float)($product->price ?? 0),
                'cost_price' => isset($product->cost_price) ? (float)$product->cost_price : null,
                'wholesale_price' => isset($product->wholesale_price) ? (float)$product->wholesale_price : null,
                'retail_price' => isset($product->retail_price) ? (float)$product->retail_price : null,
                'min_wholesale_quantity' => (int)($product->min_wholesale_quantity ?? 10),
                'stock_quantity' => (int)($product->stock_quantity ?? 0),
                'min_stock_alert' => (int)($product->min_stock_alert ?? 5),
                'category_id' => isset($product->category_id) ? (int)$product->category_id : null,
                'product_type_id' => isset($product->product_type_id) ? (int)$product->product_type_id : null,
                'sku' => (string)($product->sku ?? ''),
                'barcode' => isset($product->barcode) ? (string)$product->barcode : null,
                'status' => (string)($product->status ?? 'active'),
                'is_featured' => (bool)($product->is_featured ?? false),
                'meta_title' => isset($product->meta_title) ? (string)$product->meta_title : null,
                'meta_description' => isset($product->meta_description) ? (string)$product->meta_description : null,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
                'deleted_at' => null,
                'images' => array_values(array_unique($images)),
                'tags' => $tags,
                'category' => $category,
                'product_type' => $productType,
                'main_image' => !empty($images) ? $images[0] : null,
                'all_images' => array_values(array_unique($images)),
                'product_images' => array_map(function($img) {
                    return [
                        'id' => $img['id'] ?? null,
                        'url' => str_starts_with($img['url'], 'http') 
                            ? $img['url'] 
                            : url('storage/' . ltrim($img['url'], '/')),
                        'is_principale' => ($img['type'] ?? null) === 'principale',
                    ];
                }, $productImagesRaw),
            ];
        } catch (\Throwable $e) {
            \Log::error('Erreur formatProductFromRaw: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Formater un produit pour l'API avec les URLs complètes des images.
     */
    private function formatProductForApi($product, $productImagesRaw = [])
    {
        try {
            // Utiliser getAttributes() pour éviter de charger les relations et économiser la mémoire
            $attrs = $product->getAttributes();
            
            // Construire manuellement le tableau avec seulement les données essentielles
            $productData = [
                'id' => (int)($attrs['id'] ?? 0),
                'name' => (string)($attrs['name'] ?? ''),
                'description' => isset($attrs['description']) ? (string)$attrs['description'] : null,
                'price' => isset($attrs['price']) ? (float)$attrs['price'] : 0.0,
                'cost_price' => isset($attrs['cost_price']) ? (float)$attrs['cost_price'] : null,
                'wholesale_price' => isset($attrs['wholesale_price']) ? (float)$attrs['wholesale_price'] : null,
                'retail_price' => isset($attrs['retail_price']) ? (float)$attrs['retail_price'] : null,
                'min_wholesale_quantity' => (int)($attrs['min_wholesale_quantity'] ?? 10),
                'stock_quantity' => (int)($attrs['stock_quantity'] ?? 0),
                'min_stock_alert' => (int)($attrs['min_stock_alert'] ?? 5),
                'category_id' => isset($attrs['category_id']) ? (int)$attrs['category_id'] : null,
                'product_type_id' => isset($attrs['product_type_id']) ? (int)$attrs['product_type_id'] : null,
                'sku' => (string)($attrs['sku'] ?? ''),
                'barcode' => isset($attrs['barcode']) ? (string)$attrs['barcode'] : null,
                'status' => (string)($attrs['status'] ?? 'active'),
                'is_featured' => (bool)($attrs['is_featured'] ?? false),
                'meta_title' => isset($attrs['meta_title']) ? (string)$attrs['meta_title'] : null,
                'meta_description' => isset($attrs['meta_description']) ? (string)$attrs['meta_description'] : null,
            ];
            
            // Formater les dates - utiliser les attributs bruts si possible
            if (isset($attrs['created_at']) && $attrs['created_at']) {
                $productData['created_at'] = is_string($attrs['created_at']) 
                    ? $attrs['created_at'] 
                    : ($product->created_at ? $product->created_at->format('Y-m-d\TH:i:s\Z') : null);
            } else {
                $productData['created_at'] = $product->created_at ? $product->created_at->format('Y-m-d\TH:i:s\Z') : null;
            }
            
            if (isset($attrs['updated_at']) && $attrs['updated_at']) {
                $productData['updated_at'] = is_string($attrs['updated_at']) 
                    ? $attrs['updated_at'] 
                    : ($product->updated_at ? $product->updated_at->format('Y-m-d\TH:i:s\Z') : null);
            } else {
                $productData['updated_at'] = $product->updated_at ? $product->updated_at->format('Y-m-d\TH:i:s\Z') : null;
            }
            
            $productData['deleted_at'] = null; // Ne pas inclure
            
            // Gérer les images (JSON) - décoder une seule fois
            $imagesRaw = $attrs['images'] ?? null;
            if ($imagesRaw) {
                $productData['images'] = is_array($imagesRaw) ? array_values($imagesRaw) : (json_decode($imagesRaw, true) ?: []);
            } else {
                $productData['images'] = [];
            }
            
            // Gérer les tags - décoder une seule fois
            $tagsRaw = $attrs['tags'] ?? null;
            if ($tagsRaw) {
                $productData['tags'] = is_array($tagsRaw) ? array_values($tagsRaw) : (json_decode($tagsRaw, true) ?: []);
            } else {
                $productData['tags'] = [];
            }
            
            // Libérer la mémoire
            unset($attrs, $imagesRaw, $tagsRaw);
            
            // Charger category et productType via requêtes SQL brutes pour éviter les relations Eloquent
            if (!empty($productData['category_id'])) {
                try {
                    $category = DB::table('categories')->where('id', $productData['category_id'])->first(['id', 'name']);
                    if ($category) {
                        $productData['category'] = [
                            'id' => $category->id,
                            'name' => $category->name,
                        ];
                    }
                    unset($category);
                } catch (\Exception $e) {
                    \Log::warning('Erreur chargement category: ' . $e->getMessage());
                }
            }
            if (!empty($productData['product_type_id'])) {
                try {
                    $productType = DB::table('product_types')->where('id', $productData['product_type_id'])->first(['id', 'name']);
                    if ($productType) {
                        $productData['product_type'] = [
                            'id' => $productType->id,
                            'name' => $productType->name,
                        ];
                    }
                    unset($productType);
                } catch (\Exception $e) {
                    \Log::warning('Erreur chargement productType: ' . $e->getMessage());
                }
            }

            // PRIORITÉ 1: Utiliser les images passées en paramètre (depuis requête brute)
            $images = [];
            try {
                if (!empty($productImagesRaw)) {
                    foreach ($productImagesRaw as $imgData) {
                        try {
                            $imageUrl = $imgData['url'] ?? null;
                            if ($imageUrl && !empty(trim($imageUrl))) {
                                if (str_starts_with($imageUrl, 'http')) {
                                    $images[] = $imageUrl;
                                } else {
                                    $images[] = url('storage/' . ltrim($imageUrl, '/'));
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::warning('Erreur formatage image: ' . $e->getMessage());
                            continue;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Erreur formatage productImagesRaw: ' . $e->getMessage());
            }
            
            // PRIORITÉ 2: Si pas d'images depuis productImages, utiliser le champ images (JSON)
            if (empty($images) && isset($productData['images'])) {
                try {
                    $productImages = is_array($productData['images']) 
                        ? $productData['images'] 
                        : json_decode($productData['images'], true);
                    
                    if ($productImages && count($productImages) > 0) {
                        foreach ($productImages as $image) {
                            if (is_string($image) && !empty($image)) {
                                if (str_starts_with($image, 'http')) {
                                    $images[] = $image;
                                } else {
                                    $images[] = url('storage/' . ltrim($image, '/'));
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Erreur formatage images JSON pour produit ' . $product->id . ': ' . $e->getMessage());
                }
            }

            // Supprimer les doublons
            $images = array_values(array_unique($images));

            $productData['images'] = $images;
            // Ne pas utiliser de placeholder d'image (retourner null si pas d'image)
            // Le widget Flutter gérera l'affichage du placeholder localement
            $productData['main_image'] = !empty($images) ? $images[0] : null;
            $productData['all_images'] = $images;
            
            // Formater product_images depuis les données brutes passées en paramètre
            try {
                $productImagesArray = [];
                if (!empty($productImagesRaw)) {
                    foreach ($productImagesRaw as $imgData) {
                        try {
                            $imageUrl = $imgData['url'] ?? null;
                            if ($imageUrl && !empty(trim($imageUrl))) {
                                $fullUrl = str_starts_with($imageUrl, 'http') 
                                    ? $imageUrl 
                                    : url('storage/' . ltrim($imageUrl, '/'));
                                
                                $productImagesArray[] = [
                                    'id' => $imgData['id'] ?? null,
                                    'url' => $fullUrl,
                                    'is_principale' => ($imgData['type'] ?? null) === 'principale',
                                ];
                            }
                        } catch (\Exception $e) {
                            \Log::warning('Erreur formatage product_image: ' . $e->getMessage());
                            continue;
                        }
                    }
                }
                $productData['product_images'] = $productImagesArray;
                unset($productImagesArray);
            } catch (\Exception $e) {
                \Log::warning('Erreur formatage product_images: ' . $e->getMessage());
                $productData['product_images'] = [];
            }

            // Formater les tags - utiliser les attributs déjà décodés
            // Les tags sont déjà dans $productData['tags'] depuis la ligne 547
            // Pas besoin de les traiter à nouveau

            return $productData;
        } catch (\Exception $e) {
            \Log::error('Erreur formatProductForApi pour produit ' . ($product->id ?? 'unknown') . ': ' . $e->getMessage());
            // Retourner un format minimal
            return $this->formatProductForApiMinimal($product);
        }
    }
    
    /**
     * Format minimal en cas d'erreur dans formatProductForApi.
     */
    private function formatProductForApiMinimal($product)
    {
        try {
            $productData = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock_quantity' => $product->stock_quantity,
                'status' => $product->status,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'images' => [],
                'main_image' => null, // Pas de placeholder côté serveur
                'all_images' => [],
                'product_images' => [],
                'tags' => [],
            ];
            
            // Essayer de charger les images basiques
            if (isset($product->images)) {
                try {
                    $images = is_array($product->images) ? $product->images : json_decode($product->images, true);
                    if ($images && is_array($images)) {
                        foreach ($images as $img) {
                            if (is_string($img) && !empty($img)) {
                                $productData['images'][] = str_starts_with($img, 'http') ? $img : url('storage/' . ltrim($img, '/'));
                            }
                        }
                        if (!empty($productData['images'])) {
                            $productData['main_image'] = $productData['images'][0];
                            $productData['all_images'] = $productData['images'];
                        }
                    }
                } catch (\Exception $e) {
                    // Ignorer l'erreur
                }
            }
            
            return $productData;
        } catch (\Exception $e) {
            \Log::error('Erreur formatProductForApiMinimal: ' . $e->getMessage());
            // Dernier recours : retourner les données basiques
            return [
                'id' => $product->id ?? 0,
                'name' => $product->name ?? 'Produit sans nom',
                'description' => $product->description ?? null,
                'price' => $product->price ?? 0,
                'stock_quantity' => $product->stock_quantity ?? 0,
                'status' => $product->status ?? 'active',
                'sku' => $product->sku ?? '',
                'barcode' => $product->barcode ?? null,
                'images' => [],
                'main_image' => null, // Pas de placeholder côté serveur
                'all_images' => [],
                'product_images' => [],
                'tags' => [],
            ];
        }
    }

    /**
     * Formater un produit pour les détails avec toutes les informations.
     */
    private function formatProductDetailForApi($product)
    {
        $productData = $product->toArray();

        // Calculer la marge si le prix de revient est disponible
        if ($product->cost_price && $product->cost_price > 0 && $product->price > 0) {
            $marge = (($product->price - $product->cost_price) / $product->price) * 100;
            $productData['margin_percentage'] = round($marge, 2);
            $productData['margin_amount'] = $product->price - $product->cost_price;
        } else {
            $productData['margin_percentage'] = null;
            $productData['margin_amount'] = null;
        }

        // Formater les images avec URLs complètes
        $images = [];
        if ($product->productImages && $product->productImages->count() > 0) {
            foreach ($product->productImages as $image) {
                $imageUrl = $image->image_path ?? $image->url ?? null;
                if ($imageUrl) {
                    if (str_starts_with($imageUrl, 'http')) {
                        $images[] = $imageUrl;
                    } else {
                        $images[] = url('storage/' . ltrim($imageUrl, '/'));
                    }
                }
            }
        } elseif ($product->images) {
            $productImages = is_array($product->images) ? $product->images : json_decode($product->images, true);
            if ($productImages && count($productImages) > 0) {
                foreach ($productImages as $image) {
                    if (is_string($image) && str_starts_with($image, 'http')) {
                        $images[] = $image;
                    } elseif (!empty($image)) {
                        $images[] = url('storage/' . ltrim($image, '/'));
                    }
                }
            }
        }

        $productData['images'] = $images;
        $productData['main_image'] = !empty($images) ? $images[0] : null;
        $productData['all_images'] = $images;

        // Formater les attributs
        $attributes = [];
        if ($product->attributeValues && $product->attributeValues->count() > 0) {
            foreach ($product->attributeValues as $attrValue) {
                if ($attrValue->attribute) {
                    $attributes[] = [
                        'id' => $attrValue->attribute->id,
                        'name' => $attrValue->attribute->name,
                        'value' => $attrValue->value,
                        'type' => $attrValue->attribute->type ?? 'text',
                    ];
                }
            }
        }
        $productData['attributes'] = $attributes;

        // Formater les variantes
        $variants = [];
        if ($product->variants && $product->variants->count() > 0) {
            foreach ($product->variants as $variant) {
                $variantImages = [];
                if ($variant->images) {
                    $variantImgs = is_array($variant->images) ? $variant->images : json_decode($variant->images, true);
                    if ($variantImgs) {
                        foreach ($variantImgs as $img) {
                            if (str_starts_with($img, 'http')) {
                                $variantImages[] = $img;
                            } else {
                                $variantImages[] = url('storage/' . ltrim($img, '/'));
                            }
                        }
                    }
                }

                $variants[] = [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'stock_quantity' => $variant->stock_quantity,
                    'images' => $variantImages,
                    'is_active' => $variant->is_active,
                ];
            }
        }
        $productData['variants'] = $variants;

        // Formater les tags
        if ($product->tags) {
            $tags = is_array($product->tags) ? $product->tags : json_decode($product->tags, true);
            $productData['tags'] = $tags ?: [];
        } else {
            $productData['tags'] = [];
        }

        // Ajouter les informations de catégorie et type de produit
        if ($product->category) {
            $productData['category'] = [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug ?? null,
            ];
        }

        if ($product->productType) {
            $productData['product_type'] = [
                'id' => $product->productType->id,
                'name' => $product->productType->name,
                'description' => $product->productType->description ?? null,
            ];
        }

        return $productData;
    }
}
