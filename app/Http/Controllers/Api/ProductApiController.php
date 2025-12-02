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
            
            // Construire la requête avec filtres dynamiques
            $whereConditions = ["deleted_at IS NULL"];
            $queryParams = [];
            
            // Filtre par statut (par défaut: active)
            $status = $request->get('status', 'active');
            $whereConditions[] = "status = ?";
            $queryParams[] = $status;
            
            // Filtre par catégorie
            if ($request->filled('category_id')) {
                $whereConditions[] = "category_id = ?";
                $queryParams[] = $request->category_id;
            }
            
            // Filtre par type de produit
            if ($request->filled('product_type_id')) {
                $whereConditions[] = "product_type_id = ?";
                $queryParams[] = $request->product_type_id;
            }
            
            // Filtres avancés pour téléphones
            if ($request->filled('brand') && trim($request->brand) !== '') {
                $whereConditions[] = "brand = ?";
                $queryParams[] = trim($request->brand);
            }
            
            if ($request->filled('range') && trim($request->range) !== '') {
                $whereConditions[] = "`range` = ?";
                $queryParams[] = trim($request->range);
            }
            
            if ($request->filled('format') && trim($request->format) !== '') {
                $whereConditions[] = "format = ?";
                $queryParams[] = trim($request->format);
            }
            
            // Filtres avancés pour accessoires
            if ($request->filled('type_accessory') && trim($request->type_accessory) !== '') {
                $whereConditions[] = "type_accessory = ?";
                $queryParams[] = trim($request->type_accessory);
            }
            
            if ($request->filled('compatibility') && trim($request->compatibility) !== '') {
                $whereConditions[] = "compatibility = ?";
                $queryParams[] = trim($request->compatibility);
            }
            
            // Filtre par prix
            if ($request->filled('price_min') || $request->filled('min_price')) {
                $whereConditions[] = "price >= ?";
                $queryParams[] = $request->price_min ?? $request->min_price;
            }
            
            if ($request->filled('price_max') || $request->filled('max_price')) {
                $whereConditions[] = "price <= ?";
                $queryParams[] = $request->price_max ?? $request->max_price;
            }
            
            // Filtre par disponibilité stock
            if ($request->filled('stock_available')) {
                if ($request->stock_available == 'yes') {
                    $whereConditions[] = "stock_quantity > 0";
                } elseif ($request->stock_available == 'no') {
                    $whereConditions[] = "stock_quantity <= 0";
                }
                // Si stock_available est fourni mais vide/null, on n'applique pas de filtre (afficher tous)
            } elseif ($request->filled('in_stock')) {
                if ($request->in_stock) {
                    $whereConditions[] = "stock_quantity > 0";
                }
            } else {
                // Par défaut, afficher uniquement les produits en stock pour les produits actifs
                // Permettre de voir tous les produits si explicitement demandé via show_all=true
                if (!$request->filled('show_all') && $status !== 'draft') {
                    $whereConditions[] = "stock_quantity > 0";
                }
            }
            
            // Recherche
            if ($request->filled('search')) {
                $search = $request->search;
                $whereConditions[] = "(name LIKE ? OR description LIKE ? OR sku LIKE ? OR brand LIKE ? OR `range` LIKE ?)";
                $searchPattern = "%{$search}%";
                $queryParams[] = $searchPattern;
                $queryParams[] = $searchPattern;
                $queryParams[] = $searchPattern;
                $queryParams[] = $searchPattern;
                $queryParams[] = $searchPattern;
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            // Requête SQL avec tous les nouveaux champs
            $productsQuery = "SELECT id, name, slug, description, price, cost_price, wholesale_price, 
                retail_price, min_wholesale_quantity, stock_quantity, min_stock_alert,
                category_id, product_type_id, sku, barcode, brand, `range`, format, 
                type_accessory, compatibility, status, is_featured,
                meta_title, meta_description, images, tags, created_at, updated_at
                FROM products 
                WHERE {$whereClause}
                ORDER BY updated_at DESC, created_at DESC 
                LIMIT ? OFFSET ?";
            
            $queryParams[] = $perPage;
            $queryParams[] = $offset;
            
            $products = DB::select($productsQuery, $queryParams);
            
            // Requête pour le total
            $totalQuery = "SELECT COUNT(*) as total FROM products WHERE {$whereClause}";
            $totalParams = array_slice($queryParams, 0, -2); // Enlever LIMIT et OFFSET
            $totalResult = DB::selectOne($totalQuery, $totalParams);
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
            $product = DB::selectOne("SELECT id, name, slug, description, price, cost_price, wholesale_price, 
                retail_price, min_wholesale_quantity, stock_quantity, min_stock_alert,
                category_id, product_type_id, sku, barcode, brand, `range`, format, 
                type_accessory, compatibility, status, is_featured,
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
                    SELECT pav.id, pav.attribute_value as value, a.id as attribute_id, a.name as attribute_name
                    FROM product_attribute_values pav
                    JOIN product_type_attributes pta ON pav.product_type_attribute_id = pta.id
                    JOIN attributes a ON pta.attribute_id = a.id
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
                if ($attributeValue->productTypeAttribute && $attributeValue->productTypeAttribute->attribute_id == $attributeId) {
                    $values[] = $attributeValue->attribute_value;
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
        // Augmenter la limite de mémoire pour cette requête
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', '300');
        
        try {
            $searchTerm = $request->get('q');

            if (empty($searchTerm)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terme de recherche requis'
                ], 400);
            }

            \Log::info('ProductApiController@search: Recherche pour: ' . $searchTerm);

            // Utiliser des requêtes SQL directes pour éviter les problèmes Eloquent
            $limit = min($request->get('per_page', 20), 50); // Limiter à 50 pour la recherche
            
            // Requête SQL directe pour les produits
            $productsQuery = "SELECT id, name, slug, description, price, cost_price, wholesale_price, 
                retail_price, min_wholesale_quantity, stock_quantity, min_stock_alert,
                category_id, product_type_id, sku, barcode, brand, `range`, format, 
                type_accessory, compatibility, status, is_featured,
                meta_title, meta_description, images, tags, created_at, updated_at
                FROM products 
                WHERE status = 'active' 
                AND stock_quantity > 0 
                AND deleted_at IS NULL 
                AND (
                    name LIKE ? 
                    OR description LIKE ? 
                    OR sku LIKE ? 
                    OR barcode LIKE ?
                )
                ORDER BY 
                    CASE 
                        WHEN name LIKE ? THEN 1
                        WHEN name LIKE ? THEN 2
                        WHEN sku LIKE ? THEN 3
                        ELSE 4
                    END,
                    created_at DESC
                LIMIT ?";
            
            $searchPattern = "%{$searchTerm}%";
            $products = DB::select($productsQuery, [
                $searchPattern, // name LIKE
                $searchPattern, // description LIKE
                $searchPattern, // sku LIKE
                $searchPattern, // barcode LIKE
                $searchTerm,    // ORDER BY name exact
                $searchPattern, // ORDER BY name starts with
                $searchPattern, // ORDER BY sku
                $limit
            ]);

            if (empty($products)) {
                \Log::info('ProductApiController@search: Aucun produit trouvé');
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Récupérer les IDs des produits
            $productIds = array_map(function($p) { return $p->id; }, $products);
            $productIdsPlaceholders = implode(',', array_fill(0, count($productIds), '?'));

            // Charger toutes les images en une seule requête
            $productImages = DB::select(
                "SELECT id, product_id, url, type FROM product_images WHERE product_id IN ($productIdsPlaceholders)",
                $productIds
            );
            
            // Organiser les images par product_id
            $productImagesData = [];
            foreach ($productImages as $img) {
                if (!isset($productImagesData[$img->product_id])) {
                    $productImagesData[$img->product_id] = [];
                }
                $productImagesData[$img->product_id][] = [
                    'id' => $img->id,
                    'url' => $img->url,
                    'type' => $img->type,
                ];
            }

            // Formater les produits
            $formattedProducts = [];
            foreach ($products as $product) {
                try {
                    $productData = $this->formatProductFromRaw($product, $productImagesData[$product->id] ?? []);
                    $formattedProducts[] = $productData;
                } catch (\Exception $e) {
                    \Log::warning('ProductApiController@search: Erreur formatage produit ' . $product->id . ': ' . $e->getMessage());
                    continue;
                }
            }

            \Log::info('ProductApiController@search: ' . count($formattedProducts) . ' produits trouvés');

            return response()->json([
                'success' => true,
                'data' => $formattedProducts
            ]);
        } catch (\Throwable $e) {
            \Log::error('Erreur dans ProductApiController@search', [
                'search_term' => $request->get('q'),
                'message' => $e->getMessage(),
                'type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => config('app.debug') 
                    ? 'Erreur de recherche: ' . $e->getMessage()
                    : 'Erreur du serveur: 500',
                'data' => []
            ], 500);
        }
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
                        $q->whereHas('productTypeAttribute.attribute', function($subQ) use ($attributeName) {
                            $subQ->where('name', $attributeName);
                        })->whereIn('attribute_value', $values);
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
                            : \Storage::disk('s3')->url(ltrim($imageUrl, '/'));
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
                                : \Storage::disk('s3')->url(ltrim($image, '/'));
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
                'slug' => isset($product->slug) ? (string)$product->slug : null,
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
                // Nouveaux champs e-commerce
                'brand' => isset($product->brand) ? (string)$product->brand : null,
                'range' => isset($product->range) ? (string)$product->range : null,
                'format' => isset($product->format) ? (string)$product->format : null,
                'type_accessory' => isset($product->type_accessory) ? (string)$product->type_accessory : null,
                'compatibility' => isset($product->compatibility) ? (string)$product->compatibility : null,
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
                            : \Storage::disk('s3')->url(ltrim($img['url'], '/')),
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
                                    $images[] = \Storage::disk('s3')->url(ltrim($imageUrl, '/'));
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
                                    $images[] = \Storage::disk('s3')->url(ltrim($image, '/'));
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
                                    : \Storage::disk('s3')->url(ltrim($imageUrl, '/'));
                                
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
                                $productData['images'][] = str_starts_with($img, 'http') ? $img : \Storage::disk('s3')->url(ltrim($img, '/'));
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
                        $images[] = \Storage::disk('s3')->url(ltrim($imageUrl, '/'));
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
                        $images[] = \Storage::disk('s3')->url(ltrim($image, '/'));
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
                if ($attrValue->productTypeAttribute && $attrValue->productTypeAttribute->attribute) {
                    $attribute = $attrValue->productTypeAttribute->attribute;
                    $attributes[] = [
                        'id' => $attribute->id,
                        'name' => $attribute->name,
                        'value' => $attrValue->attribute_value,
                        'type' => $attribute->type ?? 'text',
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
                                $variantImages[] = \Storage::disk('s3')->url(ltrim($img, '/'));
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

    /**
     * Get distinct filter values for mobile app
     */
    public function filterValues()
    {
        try {
            // Valeurs par défaut si la base est vide
            $defaultBrands = ['Tecno', 'Infinix', 'Itel', 'Samsung', 'iPhone', 'Xiaomi', 'Huawei', 'Oppo', 'Vivo', 'Nokia', 'Realme', 'OnePlus', 'Lenovo', 'Alcatel', 'Sony Xperia', 'LG', 'ZTE', 'Gionee', 'Wiko', 'Blackview', 'Doogee', 'Cubot', 'Ulefone', 'Honor', 'Google Pixel', 'Motorola', 'Umidigi', 'Asus', 'Lava', 'Turing', 'Redmi', 'Poco'];
            $defaultRanges = ['Spark', 'Camon', 'Phantom', 'Pop', 'Hot', 'Note', 'Zero', 'Smart', 'A-series', 'S-series', 'P-series', 'Galaxy A', 'Galaxy M', 'Galaxy S', 'Galaxy Note', 'Z Fold', 'iPhone 6', 'iPhone 7', 'iPhone 8', 'iPhone X', 'iPhone XR', 'iPhone 11', 'iPhone 12', 'iPhone 13', 'iPhone 14', 'iPhone 15', 'Redmi Note', 'Redmi A', 'Poco X', 'Poco F', 'Y-series', 'Nova', 'P-series', 'Mate', 'A-series', 'Reno', 'F-series', 'V-series', 'X-series', 'C-series', 'G-series', 'XR-series', 'Narzo', 'GT', 'Nord', '8', '9', '10', '11', 'K-series', 'Tab M', 'G-series', 'Velvet', 'K-series', 'Pixel 4', 'Pixel 5', 'Pixel 6', 'Pixel 7', 'Pixel 8'];
            $defaultFormats = ['tactile', 'à touches', 'tablette Android'];
            $defaultAccessoryTypes = ['Chargeur mural', 'Câble USB', 'Adaptateur secteur', 'Écouteurs filaires', 'Écouteurs Bluetooth', 'Casque audio', 'Batterie externe (Power Bank)', 'Coque de protection', 'Film protecteur (verre trempé)', 'Support téléphone voiture', 'Trépied photo / selfie stick', 'Haut-parleur Bluetooth', 'Clé USB OTG', 'Adaptateur Type-C / Micro USB', 'Station de charge sans fil', 'Smartwatch', 'Bracelet connecté', 'Anneau lumineux (Ring Light)', 'Carte mémoire (SD / microSD)', 'Hub USB', 'Dock de recharge multiple', 'Étui tablette', 'Câble HDMI mobile', 'Support bureau pliable', 'Mini ventilateur USB', 'Câble auxiliaire audio (jack 3.5 mm)', 'Batterie interne (remplaçable)', 'Chargeur allume-cigare', 'Connecteur magnétique', 'Adaptateur SIM / Ejecteur SIM'];
            $defaultCompatibilities = ['Android universel', 'iPhone (Lightning)', 'Type-C universel', 'Micro-USB universel', 'Infinix / Tecno / Itel', 'Samsung Galaxy', 'iPhone 11 à 15', 'Huawei Y & P series', 'Redmi / Poco', 'Oppo A & F series', 'Vivo Y series', 'Nokia C & G series', 'Lenovo Tab', 'LG G & K series', 'OnePlus Nord / 8 / 9', 'Realme C / Narzo', 'Honor Magic / X', 'Google Pixel (4 à 8)', 'Motorola Moto G / E', 'Sony Xperia', 'Ulefone Armor', 'Doogee S series', 'Blackview BV series', 'Wiko Sunny / Jerry / Y', 'iPad (toutes générations)', 'Tablettes Android 10"', 'Smartwatch universelle', 'Accessoires audio Bluetooth 5.0', 'Casques jack 3.5 mm', 'Appareils à touches (Itel, Nokia 105, Tecno T series)'];

            // Récupérer les valeurs distinctes depuis la base de données
            $brands = DB::table('products')
                ->select('brand')
                ->whereNotNull('brand')
                ->where('brand', '!=', '')
                ->whereNull('deleted_at')
                ->groupBy('brand')
                ->orderBy('brand')
                ->limit(100)
                ->pluck('brand')
                ->filter()
                ->values()
                ->toArray();
            
            if (empty($brands)) {
                $brands = $defaultBrands;
            } else {
                // Fusionner avec les valeurs par défaut et supprimer les doublons
                $brands = array_values(array_unique(array_merge($defaultBrands, $brands)));
            }

            $ranges = DB::table('products')
                ->select('range')
                ->whereNotNull('range')
                ->where('range', '!=', '')
                ->whereNull('deleted_at')
                ->groupBy('range')
                ->orderBy('range')
                ->limit(100)
                ->pluck('range')
                ->filter()
                ->values()
                ->toArray();
            
            if (empty($ranges)) {
                $ranges = $defaultRanges;
            } else {
                $ranges = array_values(array_unique(array_merge($defaultRanges, $ranges)));
            }

            $formats = DB::table('products')
                ->select('format')
                ->whereNotNull('format')
                ->where('format', '!=', '')
                ->whereNull('deleted_at')
                ->groupBy('format')
                ->orderBy('format')
                ->limit(50)
                ->pluck('format')
                ->filter()
                ->values()
                ->toArray();
            
            if (empty($formats)) {
                $formats = $defaultFormats;
            } else {
                $formats = array_values(array_unique(array_merge($defaultFormats, $formats)));
            }

            $accessoryTypes = DB::table('products')
                ->select('type_accessory')
                ->whereNotNull('type_accessory')
                ->where('type_accessory', '!=', '')
                ->whereNull('deleted_at')
                ->groupBy('type_accessory')
                ->orderBy('type_accessory')
                ->limit(100)
                ->pluck('type_accessory')
                ->filter()
                ->values()
                ->toArray();
            
            if (empty($accessoryTypes)) {
                $accessoryTypes = $defaultAccessoryTypes;
            } else {
                $accessoryTypes = array_values(array_unique(array_merge($defaultAccessoryTypes, $accessoryTypes)));
            }

            $compatibilities = DB::table('products')
                ->select('compatibility')
                ->whereNotNull('compatibility')
                ->where('compatibility', '!=', '')
                ->whereNull('deleted_at')
                ->groupBy('compatibility')
                ->orderBy('compatibility')
                ->limit(100)
                ->pluck('compatibility')
                ->filter()
                ->values()
                ->toArray();
            
            if (empty($compatibilities)) {
                $compatibilities = $defaultCompatibilities;
            } else {
                $compatibilities = array_values(array_unique(array_merge($defaultCompatibilities, $compatibilities)));
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'brands' => $brands,
                    'ranges' => $ranges,
                    'formats' => $formats,
                    'accessory_types' => $accessoryTypes,
                    'compatibilities' => $compatibilities,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur récupération filterValues: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des valeurs de filtres',
                'data' => [
                    'brands' => [],
                    'ranges' => [],
                    'formats' => [],
                    'accessory_types' => [],
                    'compatibilities' => [],
                ],
            ], 500);
        }
    }
}
