<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Augmentation de la mémoire PHP et désactivation des fonctionnalités lourdes
     */
    public function __construct()
    {
        ini_set('memory_limit', '2G'); // Augmentation à 2GB
        ini_set('max_execution_time', 300); // 5 minutes max
    }

    public function index(Request $request)
    {
        try {
            // Nettoyer les messages d'erreur persistants si on arrive sur la page directement
            if (!$request->has('error_redirect')) {
                // Ne pas nettoyer automatiquement, laisser Laravel gérer
            }

            // Construction de la requête avec filtres dynamiques
            // Utiliser DB::table pour éviter l'épuisement de mémoire
            // Ne charger que les colonnes essentielles pour la liste (exclure description, images, tags qui sont volumineux)
            $query = DB::table('products')->select('id', 'name', 'slug', 'price', 'cost_price', 'stock_quantity', 'min_stock_alert', 'status', 'sku', 'barcode', 'category_id', 'product_type_id', 'brand', 'range', 'format', 'type_accessory', 'compatibility', 'created_at', 'updated_at', 'deleted_at')
                ->whereNull('deleted_at');

            // Filtres dynamiques - Recherche générale
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%")
                      ->orWhere('range', 'like', "%{$search}%");
                });
            }

            // Filtre par statut
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filtre par catégorie
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Filtre par type de produit
            if ($request->filled('product_type_id')) {
                $query->where('product_type_id', $request->product_type_id);
            }

            // Filtres avancés pour téléphones
            if ($request->filled('brand')) {
                $query->where('brand', $request->brand);
            }

            if ($request->filled('range')) {
                $query->where('range', $request->range);
            }

            if ($request->filled('format')) {
                $query->where('format', $request->format);
            }

            // Filtres avancés pour accessoires
            if ($request->filled('type_accessory')) {
                $query->where('type_accessory', $request->type_accessory);
            }

            if ($request->filled('compatibility')) {
                $query->where('compatibility', $request->compatibility);
            }

            // Filtre par prix (min/max)
            if ($request->filled('price_min')) {
                $query->where('price', '>=', $request->price_min);
            }

            if ($request->filled('price_max')) {
                $query->where('price', '<=', $request->price_max);
            }

            // Filtre par disponibilité (stock)
            if ($request->filled('stock_available')) {
                if ($request->stock_available == 'yes') {
                    $query->where('stock_quantity', '>', 0);
                } elseif ($request->stock_available == 'no') {
                    $query->where('stock_quantity', '<=', 0);
                }
            }

            // Filtre par date
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Tri dynamique - Par défaut par ID croissant
            $sortBy = $request->get('sort_by', 'id');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination configurable - Limiter strictement pour éviter l'épuisement mémoire
            $perPage = min($request->get('per_page', 15), 30); // Maximum 30 produits par page, 15 par défaut

            // Utiliser simplePaginate() qui ne compte pas le total (évite COUNT(*) lourd)
            // et retourne directement des objets stdClass sans relations
            $products = $query->simplePaginate($perPage)->appends($request->query());

            // Préparer les slugs et images pour chaque produit
            $productIds = collect($products->items())->pluck('id')->toArray();

            // Charger toutes les images principales en une seule requête
            $mainImages = DB::table('product_images')
                ->select('product_id', 'url')
                ->whereIn('product_id', $productIds)
                ->orderBy('order')
                ->get()
                ->groupBy('product_id')
                ->map(function($images) {
                    return $images->first()->url ?? null;
                });

            // Charger aussi les images depuis le champ JSON images
            $productsWithImages = DB::table('products')
                ->select('id', 'images')
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            foreach ($products->items() as $product) {
                // S'assurer que le slug est une chaîne valide
                $product->slug = trim($product->slug ?? '') ?: ('no-slug-' . ($product->id ?? 0));

                // Charger l'image principale depuis product_images
                $mainImageUrl = $mainImages->get($product->id);

                // Si pas d'image dans product_images, essayer depuis le champ images JSON
                if (!$mainImageUrl && isset($productsWithImages[$product->id])) {
                    $imagesJson = $productsWithImages[$product->id]->images;
                    if ($imagesJson) {
                        $imagesArray = json_decode($imagesJson, true);
                        if (is_array($imagesArray) && !empty($imagesArray)) {
                            $mainImageUrl = is_array($imagesArray[0]) ? ($imagesArray[0]['url'] ?? $imagesArray[0]) : $imagesArray[0];
                        }
                    }
                }

                $product->main_image = $mainImageUrl;
            }

            // Charger les catégories et types pour les filtres (LIMITÉ et OPTIMISÉ)
            // Utiliser des collections vides si erreur pour éviter les problèmes de mémoire
            try {
                $categories = DB::table('categories')
                    ->select('id', 'name')
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->limit(50) // Réduire à 50 catégories
                    ->get();
            } catch (\Exception $e) {
                \Log::warning('Erreur chargement categories: ' . $e->getMessage());
                $categories = collect();
            }

            try {
                $productTypes = DB::table('product_types')
                    ->select('id', 'name')
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->limit(30) // Réduire à 30 types
                    ->get();
            } catch (\Exception $e) {
                \Log::warning('Erreur chargement productTypes: ' . $e->getMessage());
                $productTypes = collect();
            }

            // Charger les valeurs distinctes pour les filtres (OPTIMISÉ)
            // Valeurs par défaut si la base est vide
            $defaultBrands = ['Tecno', 'Infinix', 'Itel', 'Samsung', 'iPhone', 'Xiaomi', 'Huawei', 'Oppo', 'Vivo', 'Nokia', 'Realme', 'OnePlus', 'Lenovo', 'Alcatel', 'Sony Xperia', 'LG', 'ZTE', 'Gionee', 'Wiko', 'Blackview', 'Doogee', 'Cubot', 'Ulefone', 'Honor', 'Google Pixel', 'Motorola', 'Umidigi', 'Asus', 'Lava', 'Turing', 'Redmi', 'Poco'];
            $defaultRanges = ['Spark', 'Camon', 'Phantom', 'Pop', 'Hot', 'Note', 'Zero', 'Smart', 'A-series', 'S-series', 'P-series', 'Galaxy A', 'Galaxy M', 'Galaxy S', 'Galaxy Note', 'Z Fold', 'iPhone 6', 'iPhone 7', 'iPhone 8', 'iPhone X', 'iPhone XR', 'iPhone 11', 'iPhone 12', 'iPhone 13', 'iPhone 14', 'iPhone 15', 'Redmi Note', 'Redmi A', 'Poco X', 'Poco F', 'Y-series', 'Nova', 'P-series', 'Mate', 'A-series', 'Reno', 'F-series', 'V-series', 'X-series', 'C-series', 'G-series', 'XR-series', 'Narzo', 'GT', 'Nord', '8', '9', '10', '11', 'K-series', 'Tab M', 'G-series', 'Velvet', 'K-series', 'Pixel 4', 'Pixel 5', 'Pixel 6', 'Pixel 7', 'Pixel 8'];
            $defaultFormats = ['tactile', 'à touches', 'tablette Android'];
            $defaultAccessoryTypes = ['Chargeur mural', 'Câble USB', 'Adaptateur secteur', 'Écouteurs filaires', 'Écouteurs Bluetooth', 'Casque audio', 'Batterie externe (Power Bank)', 'Coque de protection', 'Film protecteur (verre trempé)', 'Support téléphone voiture', 'Trépied photo / selfie stick', 'Haut-parleur Bluetooth', 'Clé USB OTG', 'Adaptateur Type-C / Micro USB', 'Station de charge sans fil', 'Smartwatch', 'Bracelet connecté', 'Anneau lumineux (Ring Light)', 'Carte mémoire (SD / microSD)', 'Hub USB', 'Dock de recharge multiple', 'Étui tablette', 'Câble HDMI mobile', 'Support bureau pliable', 'Mini ventilateur USB', 'Câble auxiliaire audio (jack 3.5 mm)', 'Batterie interne (remplaçable)', 'Chargeur allume-cigare', 'Connecteur magnétique', 'Adaptateur SIM / Ejecteur SIM'];
            $defaultCompatibilities = ['Android universel', 'iPhone (Lightning)', 'Type-C universel', 'Micro-USB universel', 'Infinix / Tecno / Itel', 'Samsung Galaxy', 'iPhone 11 à 15', 'Huawei Y & P series', 'Redmi / Poco', 'Oppo A & F series', 'Vivo Y series', 'Nokia C & G series', 'Lenovo Tab', 'LG G & K series', 'OnePlus Nord / 8 / 9', 'Realme C / Narzo', 'Honor Magic / X', 'Google Pixel (4 à 8)', 'Motorola Moto G / E', 'Sony Xperia', 'Ulefone Armor', 'Doogee S series', 'Blackview BV series', 'Wiko Sunny / Jerry / Y', 'iPad (toutes générations)', 'Tablettes Android 10"', 'Smartwatch universelle', 'Accessoires audio Bluetooth 5.0', 'Casques jack 3.5 mm', 'Appareils à touches (Itel, Nokia 105, Tecno T series)'];

            $brands = collect();
            $ranges = collect();
            $formats = collect();
            $accessoryTypes = collect();
            $compatibilities = collect();

            try {
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
                    ->values();

                // Si aucune marque trouvée, utiliser les valeurs par défaut
                if ($brands->isEmpty()) {
                    $brands = collect($defaultBrands);
                }
            } catch (\Exception $e) {
                \Log::warning('Erreur chargement brands: ' . $e->getMessage());
                $brands = collect($defaultBrands);
            }

            try {
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
                    ->values();

                // Si aucune gamme trouvée, utiliser les valeurs par défaut
                if ($ranges->isEmpty()) {
                    $ranges = collect($defaultRanges);
                }
            } catch (\Exception $e) {
                \Log::warning('Erreur chargement ranges: ' . $e->getMessage());
                $ranges = collect($defaultRanges);
            }

            try {
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
                    ->values();

                // Si aucun format trouvé, utiliser les valeurs par défaut
                if ($formats->isEmpty()) {
                    $formats = collect($defaultFormats);
                }
            } catch (\Exception $e) {
                \Log::warning('Erreur chargement formats: ' . $e->getMessage());
                $formats = collect($defaultFormats);
            }

            try {
                $accessoryTypes = DB::table('products')
                    ->select('type_accessory')
                    ->whereNotNull('type_accessory')
                    ->where('type_accessory', '!=', '')
                    ->whereNull('deleted_at')
                    ->groupBy('type_accessory')
                    ->orderBy('type_accessory')
                    ->limit(50)
                    ->pluck('type_accessory')
                    ->filter()
                    ->values();

                // Si aucun type d'accessoire trouvé, utiliser les valeurs par défaut
                if ($accessoryTypes->isEmpty()) {
                    $accessoryTypes = collect($defaultAccessoryTypes);
                }
            } catch (\Exception $e) {
                \Log::warning('Erreur chargement accessoryTypes: ' . $e->getMessage());
                $accessoryTypes = collect($defaultAccessoryTypes);
            }

            try {
                $compatibilities = DB::table('products')
                    ->select('compatibility')
                    ->whereNotNull('compatibility')
                    ->where('compatibility', '!=', '')
                    ->whereNull('deleted_at')
                    ->groupBy('compatibility')
                    ->orderBy('compatibility')
                    ->limit(50)
                    ->pluck('compatibility')
                    ->filter()
                    ->values();

                // Si aucune compatibilité trouvée, utiliser les valeurs par défaut
                if ($compatibilities->isEmpty()) {
                    $compatibilities = collect($defaultCompatibilities);
                }
            } catch (\Exception $e) {
                \Log::warning('Erreur chargement compatibilities: ' . $e->getMessage());
                $compatibilities = collect($defaultCompatibilities);
            }

            // Statistiques pour les filtres (OPTIMISÉ avec une seule requête)
            // Utiliser une seule requête avec des sous-requêtes pour éviter 6 requêtes séparées
            // DÉSACTIVÉ TEMPORAIREMENT pour isoler le problème de mémoire
            try {
                $statsQuery = DB::table('products')
                    ->whereNull('deleted_at')
                    ->selectRaw('
                        COUNT(*) as total,
                        SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active,
                        SUM(CASE WHEN status = "inactive" THEN 1 ELSE 0 END) as inactive,
                        SUM(CASE WHEN status = "draft" THEN 1 ELSE 0 END) as draft,
                        SUM(CASE WHEN stock_quantity > 0 THEN 1 ELSE 0 END) as in_stock,
                        SUM(CASE WHEN stock_quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock
                    ')
                    ->first();

                $stats = [
                    'total' => $statsQuery->total ?? 0,
                    'active' => $statsQuery->active ?? 0,
                    'inactive' => $statsQuery->inactive ?? 0,
                    'draft' => $statsQuery->draft ?? 0,
                    'in_stock' => $statsQuery->in_stock ?? 0,
                    'out_of_stock' => $statsQuery->out_of_stock ?? 0,
                ];
            } catch (\Exception $e) {
                \Log::error('Erreur calcul stats: ' . $e->getMessage());
                // Valeurs par défaut en cas d'erreur
                $stats = [
                    'total' => 0,
                    'active' => 0,
                    'inactive' => 0,
                    'draft' => 0,
                    'in_stock' => 0,
                    'out_of_stock' => 0,
                ];
            }

            return view('admin.products.index', compact(
                'products',
                'categories',
                'productTypes',
                'brands',
                'ranges',
                'formats',
                'accessoryTypes',
                'compatibilities',
                'stats'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur dans ProductController::index: ' . $e->getMessage());

            return view('admin.products.index', [
                'products' => (object)['data' => collect([]), 'total' => 0],
                'categories' => collect([]),
                'productTypes' => collect([]),
                'stats' => ['total' => 0, 'active' => 0, 'inactive' => 0, 'draft' => 0],
                'error' => 'Erreur lors du chargement des produits. Veuillez réessayer.'
            ]);
        }
    }

    public function show($slug)
    {
        try {
            // Augmenter la mémoire pour cette méthode
            ini_set('memory_limit', '2G');

            // Version ULTRA-SIMPLE - Pas de modèle Eloquent du tout, récupération directe par slug
            $productData = DB::table('products')
                ->select('id', 'name', 'slug', 'description', 'price', 'cost_price', 'wholesale_price', 'retail_price', 'min_wholesale_quantity', 'stock_quantity', 'min_stock_alert', 'status', 'sku', 'barcode', 'category_id', 'product_type_id', 'meta_title', 'meta_description', 'tags', 'images', 'created_at', 'updated_at')
                ->where('slug', $slug)
                ->whereNull('deleted_at')
                ->first();

            // Si on ne trouve pas le produit, rediriger
            if (!$productData) {
                return redirect()->route('admin.products.index')
                    ->with('error', 'Produit non trouvé.');
            }

            $productId = $productData->id;

            // Chargement séparé des relations si nécessaire (avec limite)
            $category = null;
            if ($productData->category_id) {
                $category = DB::table('categories')
                    ->select('id', 'name')
                    ->where('id', $productData->category_id)
                    ->first();
            }

            $productType = null;
            if ($productData->product_type_id) {
                $productType = DB::table('product_types')
                    ->select('id', 'name')
                    ->where('id', $productData->product_type_id)
                    ->first();
            }

            // Chargement des attributs du produit (LIMITÉ à 50)
            $attributeValues = DB::table('product_attribute_values')
                ->join('product_type_attributes', 'product_attribute_values.product_type_attribute_id', '=', 'product_type_attributes.id')
                ->select('product_attribute_values.id',
                         'product_attribute_values.attribute_value',
                         'product_type_attributes.attribute_name',
                         'product_type_attributes.attribute_type',
                         'product_type_attributes.options')
                ->where('product_attribute_values.product_id', $productId)
                ->orderBy('product_type_attributes.sort_order')
                ->limit(50)
                ->get();

            // Chargement des images du produit (LIMITÉ à 20)
            $productImages = DB::table('product_images')
                ->select('id', 'product_id', 'url', 'order')
                ->where('product_id', $productId)
                ->orderBy('order')
                ->limit(20)
                ->get();

            // Conversion en objet pour la compatibilité avec la vue
            $product = (object) $productData;
            $product->category = $category;
            $product->productType = $productType;
            $product->attributeValues = $attributeValues;
            $product->productImages = $productImages;

            // Libération mémoire immédiate
            unset($productData, $category, $productType);
            gc_collect_cycles();

            return view('admin.products.show', compact('product'));

        } catch (\Exception $e) {
            \Log::error('Erreur dans ProductController::show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.products.index')
                ->with('error', 'Erreur lors du chargement du produit: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            // Chargement minimal avec limites strictes
            $categories = DB::table('categories')
                ->select('id', 'name')
                ->where('is_active', true)
                ->limit(20)
                ->get();

            $productTypes = DB::table('product_types')
                ->select('id', 'name')
                ->where('is_active', true)
                ->limit(20)
                ->get();

            $attributes = DB::table('attributes')
                ->select('id', 'name', 'type', 'options')
                ->limit(50)
                ->get();

            // Charger les valeurs distinctes pour les champs e-commerce
            $brands = DB::table('products')
                ->select('brand')
                ->whereNotNull('brand')
                ->where('brand', '!=', '')
                ->distinct()
                ->orderBy('brand')
                ->pluck('brand')
                ->filter()
                ->values();

            $ranges = DB::table('products')
                ->select('range')
                ->whereNotNull('range')
                ->where('range', '!=', '')
                ->distinct()
                ->orderBy('range')
                ->pluck('range')
                ->filter()
                ->values();

            $formats = DB::table('products')
                ->select('format')
                ->whereNotNull('format')
                ->where('format', '!=', '')
                ->distinct()
                ->orderBy('format')
                ->pluck('format')
                ->filter()
                ->values();

            $accessoryTypes = DB::table('products')
                ->select('type_accessory')
                ->whereNotNull('type_accessory')
                ->where('type_accessory', '!=', '')
                ->distinct()
                ->orderBy('type_accessory')
                ->pluck('type_accessory')
                ->filter()
                ->values();

            $compatibilities = DB::table('products')
                ->select('compatibility')
                ->whereNotNull('compatibility')
                ->where('compatibility', '!=', '')
                ->distinct()
                ->orderBy('compatibility')
                ->pluck('compatibility')
                ->filter()
                ->values();

            return view('admin.products.create', compact(
                'categories',
                'productTypes',
                'attributes',
                'brands',
                'ranges',
                'formats',
                'accessoryTypes',
                'compatibilities'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur dans ProductController::create: ' . $e->getMessage());
            return redirect()->route('admin.products.index')
                ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    public function store(Request $request)
    {
        // Validation intelligente selon le statut
        if ($request->status === 'draft') {
            // Validation assouplie pour les produits en brouillon
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|in:active,inactive,draft',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'stock_quantity' => 'nullable|integer|min:0',
                'product_type_id' => 'nullable|exists:product_types,id',
                'sku' => 'nullable|string|max:255|unique:products,sku',
                'brand' => 'nullable|string|max:100',
                'range' => 'nullable|string|max:100',
                'format' => 'nullable|string|max:100',
                'type_accessory' => 'nullable|string|max:100',
                'compatibility' => 'nullable|string|max:100',
                'cost_price' => 'nullable|numeric|min:0',
                'min_stock_alert' => 'nullable|integer|min:0',
                'barcode' => 'nullable|string|max:100|unique:products,barcode',
                'tags' => 'nullable|string',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'images' => 'nullable',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:4096',
            ]);
        } else {
            // Validation stricte pour les produits actifs/inactifs
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|in:active,inactive,draft',
                'description' => 'nullable|string',
                'product_type_id' => 'nullable|exists:product_types,id',
                'sku' => 'nullable|string|max:255|unique:products,sku',
                'brand' => 'nullable|string|max:100',
                'range' => 'nullable|string|max:100',
                'format' => 'nullable|string|max:100',
                'type_accessory' => 'nullable|string|max:100',
                'compatibility' => 'nullable|string|max:100',
                'cost_price' => 'nullable|numeric|min:0',
                'min_stock_alert' => 'nullable|integer|min:0',
                'barcode' => 'nullable|string|max:100|unique:products,barcode',
                'tags' => 'nullable|string',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'images' => 'nullable',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:4096',
            ]);
        }

        try {
            DB::beginTransaction();

            // Générer le SKU automatiquement si non fourni
            $sku = $request->sku ?? Product::generateSku();

            // Générer le slug seulement si name n'est pas vide
            $slug = !empty($validated['name']) ? Product::generateSlug($validated['name']) : null;

            // Convertir tags en tableau JSON si c'est une chaîne
            $tagsJson = null;
            if (isset($validated['tags']) && !empty($validated['tags'])) {
                $tags = null;
                if (is_string($validated['tags'])) {
                    // Si c'est une chaîne, la convertir en tableau
                    // Si la chaîne contient des virgules, la diviser en tableau
                    if (strpos($validated['tags'], ',') !== false) {
                        $tags = array_map('trim', explode(',', $validated['tags']));
                    } else {
                        // Sinon, créer un tableau avec un seul élément
                        $tags = [trim($validated['tags'])];
                    }
                } elseif (is_array($validated['tags'])) {
                    $tags = $validated['tags'];
                }
                // Convertir en JSON pour la base de données
                if ($tags !== null) {
                    $tagsJson = json_encode($tags);
                }
            }

            // Créer le produit avec DB::table() pour éviter les événements Eloquent qui déclenchent des relations
            $productId = DB::table('products')->insertGetId([
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'] ?? null,
                'cost_price' => $validated['cost_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'] ?? 0,
                'min_stock_alert' => isset($validated['min_stock_alert']) ? (int)$validated['min_stock_alert'] : 5,
                'category_id' => $validated['category_id'],
                'product_type_id' => $validated['product_type_id'] ?? null,
                'status' => $validated['status'],
                'sku' => $sku,
                'barcode' => $validated['barcode'] ?? null,
                'brand' => $validated['brand'] ?? null,
                'range' => $validated['range'] ?? null,
                'format' => $validated['format'] ?? null,
                'type_accessory' => $validated['type_accessory'] ?? null,
                'compatibility' => $validated['compatibility'] ?? null,
                'tags' => $tagsJson,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Récupérer le produit créé (sans relations) pour les opérations suivantes
            $product = (object) [
                'id' => $productId,
                'product_type_id' => $validated['product_type_id'] ?? null,
                'slug' => $slug,
            ];

            // Gestion des attributs du produit si fournis
            if ($request->has('attributes') && is_array($request->attributes) && !empty($product->product_type_id)) {
                foreach ($request->attributes as $attrData) {
                    if (isset($attrData['attribute_id']) && isset($attrData['value']) && !empty($attrData['value'])) {
                        try {
                            // Trouver le product_type_attribute correspondant avec DB::table() pour éviter les relations
                            $productTypeAttribute = DB::table('product_type_attributes')
                                ->where('product_type_id', $product->product_type_id)
                                ->where('attribute_id', $attrData['attribute_id'])
                                ->first();

                            if ($productTypeAttribute) {
                                // Créer ou mettre à jour la valeur de l'attribut avec DB::table()
                                $existing = DB::table('product_attribute_values')
                                    ->where('product_id', $productId)
                                    ->where('product_type_attribute_id', $productTypeAttribute->id)
                                    ->first();

                                if ($existing) {
                                    // Mettre à jour
                                    DB::table('product_attribute_values')
                                        ->where('id', $existing->id)
                                        ->update([
                                            'attribute_value' => $attrData['value'],
                                            'numeric_value' => is_numeric($attrData['value']) ? $attrData['value'] : null,
                                            'updated_at' => now(),
                                        ]);
                                } else {
                                    // Créer
                                    DB::table('product_attribute_values')->insert([
                                        'product_id' => $productId,
                                        'product_type_attribute_id' => $productTypeAttribute->id,
                                        'attribute_value' => $attrData['value'],
                                        'numeric_value' => is_numeric($attrData['value']) ? $attrData['value'] : null,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::warning('Erreur lors de la sauvegarde de l\'attribut: ' . $e->getMessage());
                            // Continuer avec les autres attributs même en cas d'erreur
                        }
                    }
                }
            }

            // Upload des images si fournies
            if ($request->hasFile('images')) {
                $paths = [];
                foreach ($request->file('images') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('products', 's3');
                        $paths[] = $path;
                    }
                }

                if (!empty($paths)) {
                    // Mettre à jour avec DB::table() pour éviter les relations
                    DB::table('products')
                        ->where('id', $productId)
                        ->update(['images' => json_encode($paths)]);

                    // Enregistrer aussi dans product_images pour compatibilité
                    $this->handleImageUpload($request->file('images'), $productId);
                }
            }

            DB::commit();

            // Rediriger vers l'index au lieu de show pour éviter les problèmes de mémoire
            return redirect()->route('admin.products.index')
                ->with('success', 'Produit créé avec succès ! ID: ' . $productId . ($slug ? ' (Slug: ' . $slug . ')' : ''));

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Erreur de validation lors de la création du produit: ' . json_encode($e->errors()));

            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Veuillez corriger les erreurs de validation.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création du produit: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Request data: ' . json_encode($request->except(['images', '_token'])));

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du produit. Veuillez réessayer ou contacter l\'administrateur. Détails: ' . $e->getMessage());
        }
    }

    public function edit($slug)
    {
        try {
            // Chargement minimal avec limites
            $categories = DB::table('categories')
                ->select('id', 'name')
                ->where('is_active', true)
                ->limit(20)
                ->get();

            $productTypes = DB::table('product_types')
                ->select('id', 'name')
                ->where('is_active', true)
                ->limit(20)
                ->get();

            $attributes = DB::table('attributes')
                ->select('id', 'name', 'type', 'options')
                ->limit(50)
                ->get();

            // Charger les valeurs distinctes pour les champs e-commerce
            $brands = DB::table('products')
                ->select('brand')
                ->whereNotNull('brand')
                ->where('brand', '!=', '')
                ->distinct()
                ->orderBy('brand')
                ->pluck('brand')
                ->filter()
                ->values();

            $ranges = DB::table('products')
                ->select('range')
                ->whereNotNull('range')
                ->where('range', '!=', '')
                ->distinct()
                ->orderBy('range')
                ->pluck('range')
                ->filter()
                ->values();

            $formats = DB::table('products')
                ->select('format')
                ->whereNotNull('format')
                ->where('format', '!=', '')
                ->distinct()
                ->orderBy('format')
                ->pluck('format')
                ->filter()
                ->values();

            $accessoryTypes = DB::table('products')
                ->select('type_accessory')
                ->whereNotNull('type_accessory')
                ->where('type_accessory', '!=', '')
                ->distinct()
                ->orderBy('type_accessory')
                ->pluck('type_accessory')
                ->filter()
                ->values();

            $compatibilities = DB::table('products')
                ->select('compatibility')
                ->whereNotNull('compatibility')
                ->where('compatibility', '!=', '')
                ->distinct()
                ->orderBy('compatibility')
                ->pluck('compatibility')
                ->filter()
                ->values();

            // Chargement du produit par slug (sans route model binding pour éviter la mémoire)
            // Si le slug contient un ID à la fin (format: nom-produit-id), essayer de trouver par ID aussi
            $productData = DB::table('products')
                ->select('id', 'name', 'slug', 'description', 'price', 'cost_price', 'wholesale_price', 'retail_price', 'min_wholesale_quantity', 'stock_quantity', 'min_stock_alert', 'status', 'sku', 'barcode', 'category_id', 'product_type_id', 'brand', 'range', 'format', 'type_accessory', 'compatibility', 'meta_title', 'meta_description', 'tags', 'images')
                ->where('slug', $slug)
                ->whereNull('deleted_at')
                ->first();

            // Si pas trouvé par slug, essayer de trouver par ID si le slug contient un ID (format: nom-produit-id)
            if (!$productData && preg_match('/-(\d+)$/', $slug, $matches)) {
                $productId = (int)$matches[1];
                $productData = DB::table('products')
                    ->select('id', 'name', 'slug', 'description', 'price', 'cost_price', 'wholesale_price', 'retail_price', 'min_wholesale_quantity', 'stock_quantity', 'min_stock_alert', 'status', 'sku', 'barcode', 'category_id', 'product_type_id', 'brand', 'range', 'format', 'type_accessory', 'compatibility', 'meta_title', 'meta_description', 'tags', 'images')
                    ->where('id', $productId)
                    ->whereNull('deleted_at')
                    ->first();

                // Si trouvé par ID mais pas de slug, générer le slug et le mettre à jour
                if ($productData && (empty($productData->slug) || is_null($productData->slug))) {
                    $newSlug = Product::generateSlug($productData->name, $productData->id);
                    DB::table('products')->where('id', $productData->id)->update(['slug' => $newSlug]);
                    $productData->slug = $newSlug;
                }
            }

            // Si toujours pas trouvé, essayer de trouver par ID directement si le slug est juste un nombre
            if (!$productData && is_numeric($slug)) {
                $productData = DB::table('products')
                    ->select('id', 'name', 'slug', 'description', 'price', 'cost_price', 'wholesale_price', 'retail_price', 'min_wholesale_quantity', 'stock_quantity', 'min_stock_alert', 'status', 'sku', 'barcode', 'category_id', 'product_type_id', 'brand', 'range', 'format', 'type_accessory', 'compatibility', 'meta_title', 'meta_description', 'tags', 'images')
                    ->where('id', (int)$slug)
                    ->whereNull('deleted_at')
                    ->first();
            }

            if (!$productData) {
                \Log::error('Produit non trouvé dans edit()', [
                    'slug_recherche' => $slug,
                    'type_slug' => gettype($slug),
                    'produits_disponibles' => DB::table('products')
                        ->select('id', 'name', 'slug')
                        ->whereNull('deleted_at')
                        ->limit(10)
                        ->get()
                        ->toArray()
                ]);
                return redirect()->route('admin.products.index', ['error_redirect' => true])
                    ->with('error', 'Produit non trouvé. Slug: ' . htmlspecialchars($slug));
            }

            // Chargement des attributs existants du produit (LIMITÉ)
            $productAttributeValues = DB::table('product_attribute_values')
                ->where('product_id', $productData->id)
                ->limit(100)
                ->get()
                ->keyBy('attribute_id');

            // Chargement des images existantes (LIMITÉ)
            $productImages = DB::table('product_images')
                ->select('id', 'url', 'order')
                ->where('product_id', $productData->id)
                ->orderBy('order')
                ->limit(20)
                ->get();

            // Conversion pour compatibilité
            $product = (object) $productData;
            $product->productImages = $productImages;

            return view('admin.products.edit', compact(
                'product',
                'categories',
                'productTypes',
                'attributes',
                'productAttributeValues',
                'brands',
                'ranges',
                'formats',
                'accessoryTypes',
                'compatibilities'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur dans ProductController::edit: ' . $e->getMessage());
            return redirect()->route('admin.products.index')
                ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    public function update(Request $request, $slug)
    {
        // Essayer d'abord par slug exact
        $productData = DB::table('products')
            ->where('slug', $slug)
            ->whereNull('deleted_at')
            ->first();

        // Si pas trouvé par slug, essayer par ID si le slug contient un ID
        if (!$productData && preg_match('/-(\d+)$/', $slug, $matches)) {
            $productId = (int)$matches[1];
            $productData = DB::table('products')
                ->where('id', $productId)
                ->whereNull('deleted_at')
                ->first();
        }

        // Si toujours pas trouvé, essayer par ID directement si le slug est juste un nombre
        if (!$productData && is_numeric($slug)) {
            $productData = DB::table('products')
                ->where('id', (int)$slug)
                ->whereNull('deleted_at')
                ->first();
        }

        if (!$productData) {
            \Log::error('Produit non trouvé dans update()', [
                'slug_recherche' => $slug,
                'type_slug' => gettype($slug)
            ]);
            return redirect()->route('admin.products.index')
                ->with('error', 'Produit non trouvé. Slug: ' . htmlspecialchars($slug));
        }

        $productId = $productData->id;
        $currentName = $productData->name;

        // Log pour debug
        \Log::info('=== UPDATE PRODUCT ===', [
            'slug' => $slug,
            'id' => $productId,
            'all_data' => $request->all(),
            'name' => $request->name,
            'price' => $request->price
        ]);

                try {
            // Prétraiter les données avant validation
            $data = $request->all();

            // Convertir les prix (virgule en point)
            if (isset($data['price'])) {
                $data['price'] = str_replace(',', '.', $data['price']);
            }
            if (isset($data['cost_price'])) {
                $data['cost_price'] = str_replace(',', '.', $data['cost_price']);
            }

            // Validation
            $validated = validator($data, [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'product_type_id' => 'nullable|exists:product_types,id',
                'status' => 'required|in:active,inactive,draft',
                'sku' => 'nullable|string|max:255',
                'cost_price' => 'nullable|numeric|min:0',
                'min_stock_alert' => 'nullable|integer|min:0',
                'barcode' => 'nullable|string|max:255',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'tags' => 'nullable|string',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ], [
                'name.required' => 'Le nom est obligatoire.',
                'price.required' => 'Le prix est obligatoire.',
                'price.numeric' => 'Le prix doit être un nombre.',
                'stock_quantity.required' => 'La quantité en stock est obligatoire.',
                'category_id.required' => 'La catégorie est obligatoire.',
                'status.required' => 'Le statut est obligatoire.',
            ])->validate();

            \Log::info('Validation OK, données validées:', $validated);

            // Convertir tags en tableau JSON si c'est une chaîne
            $tags = null;
            if (isset($validated['tags']) && !empty($validated['tags'])) {
                if (is_string($validated['tags'])) {
                    // Si c'est une chaîne, la convertir en tableau
                    // Si la chaîne contient des virgules, la diviser en tableau
                    if (strpos($validated['tags'], ',') !== false) {
                        $tags = array_map('trim', explode(',', $validated['tags']));
                    } else {
                        // Sinon, créer un tableau avec un seul élément
                        $tags = [trim($validated['tags'])];
                    }
                } elseif (is_array($validated['tags'])) {
                    $tags = $validated['tags'];
                }
            }

            // Nettoyer les données pour la mise à jour (ne garder que les colonnes de la table)
            $updateData = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => floatval($validated['price']),
                'stock_quantity' => intval($validated['stock_quantity']),
                'category_id' => intval($validated['category_id']),
                'status' => $validated['status'],
                'sku' => $validated['sku'] ?? null,
                'cost_price' => isset($validated['cost_price']) && !empty($validated['cost_price']) ? floatval($validated['cost_price']) : null,
                'min_stock_alert' => isset($validated['min_stock_alert']) ? intval($validated['min_stock_alert']) : null,
                'barcode' => $validated['barcode'] ?? null,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'tags' => $tags !== null ? json_encode($tags) : null,
                'updated_at' => now(),
            ];

            // Si product_type_id existe dans les données validées, l'ajouter
            if (isset($validated['product_type_id']) && !empty($validated['product_type_id'])) {
                $updateData['product_type_id'] = intval($validated['product_type_id']);
            }

            \Log::info('Données à mettre à jour:', $updateData);

            // Générer le slug si le nom a changé
            if (isset($validated['name']) && $validated['name'] !== $currentName) {
                $updateData['slug'] = Product::generateSlug($validated['name'], $productId);
            }

            // Mise à jour avec DB::table
            $updated = DB::table('products')
                ->where('id', $productId)
                ->update($updateData);

            \Log::info('Produit mis à jour:', ['id' => $productId, 'name' => $validated['name'], 'rows_affected' => $updated]);

            // Récupérer le nouveau slug
            $newSlug = DB::table('products')->where('id', $productId)->value('slug');

            // Gestion des images
            if ($request->hasFile('images')) {
                $this->handleImageUpload($request->file('images'), $productId);

                // Mettre à jour aussi le champ images dans la table products pour compatibilité
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    if ($image->isValid()) {
                        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $imagePath = $image->storeAs('products', $imageName, 's3');
                        $imagePaths[] = $imagePath;
                    }
                }

                // Récupérer les images existantes depuis product_images
                $existingImages = DB::table('product_images')
                    ->where('product_id', $productId)
                    ->orderBy('order')
                    ->pluck('url')
                    ->toArray();

                // Fusionner avec les nouvelles images
                $allImages = array_merge($existingImages, $imagePaths);

                // Mettre à jour le champ images dans products
                DB::table('products')
                    ->where('id', $productId)
                    ->update(['images' => json_encode($allImages)]);
            }

            return redirect()->route('admin.products.show', $newSlug)
                ->with('success', 'Produit mis à jour avec succès !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation:', ['errors' => $e->errors()]);
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Veuillez corriger les erreurs de validation.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Gère l'upload des images pour un produit
     */
    private function handleImageUpload($images, $productId)
    {
        foreach ($images as $image) {
            // Générer un nom unique pour l'image
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Déplacer l'image vers le dossier storage/app/public/products
            $imagePath = $image->storeAs('products', $imageName, 's3');

            // Obtenir l'ordre suivant pour cette image
            $nextOrder = DB::table('product_images')
                ->where('product_id', $productId)
                ->max('order') + 1;

            // Insérer l'image dans la base de données
            DB::table('product_images')->insert([
                'product_id' => $productId,
                'url' => $imagePath,
                'order' => $nextOrder ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function destroy($slug)
    {
        try {
            // Essayer d'abord par slug exact
            $productData = DB::table('products')
                ->where('slug', $slug)
                ->whereNull('deleted_at')
                ->first();

            // Si pas trouvé par slug, essayer par ID si le slug contient un ID
            if (!$productData && preg_match('/-(\d+)$/', $slug, $matches)) {
                $productId = (int)$matches[1];
                $productData = DB::table('products')
                    ->where('id', $productId)
                    ->whereNull('deleted_at')
                    ->first();
            }

            // Si toujours pas trouvé, essayer par ID directement si le slug est juste un nombre
            if (!$productData && is_numeric($slug)) {
                $productData = DB::table('products')
                    ->where('id', (int)$slug)
                    ->whereNull('deleted_at')
                    ->first();
            }

            if (!$productData) {
                return redirect()->route('admin.products.index')
                    ->with('error', 'Produit non trouvé. Slug: ' . $slug);
            }

            $productId = $productData->id;

            // Supprimer les images associées
            DB::table('product_images')->where('product_id', $productId)->delete();

            // Supprimer les variantes associées
            DB::table('product_variants')->where('product_id', $productId)->delete();

            // Suppression du produit (soft delete)
            DB::table('products')
                ->where('id', $productId)
                ->update(['deleted_at' => now()]);

            return redirect()->route('admin.products.index')
                ->with('success', 'Produit supprimé avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du produit: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du produit.');
        }
    }

    /**
     * Activer/Désactiver rapidement un produit (AJAX)
     */
    public function toggleStatus(Request $request, $slug)
    {
        try {
            // Essayer d'abord par slug exact
            $productData = DB::table('products')
                ->select('id', 'status')
                ->where('slug', $slug)
                ->whereNull('deleted_at')
                ->first();

            // Si pas trouvé par slug, essayer par ID si le slug contient un ID
            if (!$productData && preg_match('/-(\d+)$/', $slug, $matches)) {
                $productId = (int)$matches[1];
                $productData = DB::table('products')
                    ->select('id', 'status')
                    ->where('id', $productId)
                    ->whereNull('deleted_at')
                    ->first();
            }

            // Si toujours pas trouvé, essayer par ID directement si le slug est juste un nombre
            if (!$productData && is_numeric($slug)) {
                $productData = DB::table('products')
                    ->select('id', 'status')
                    ->where('id', (int)$slug)
                    ->whereNull('deleted_at')
                    ->first();
            }

            if (!$productData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé. Slug: ' . $slug
                ], 404);
            }

            // Ne basculer qu'entre active/inactive
            if ($productData->status === 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Le produit est en brouillon. Veuillez le compléter avant activation.'
                ], 422);
            }

            $newStatus = $productData->status === 'active' ? 'inactive' : 'active';

            $updated = DB::table('products')
                ->where('id', $productData->id)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now()
                ]);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'La mise à jour a échoué'
                ], 500);
            }

            $label = $newStatus === 'active' ? 'Actif' : 'Inactif';
            $color = $newStatus === 'active' ? 'success' : 'secondary';

            return response()->json([
                'success' => true,
                'message' => 'Statut du produit mis à jour',
                'data' => [
                    'status' => $newStatus,
                    'label' => $label,
                    'color' => $color,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur toggleStatus produit: ' . $e->getMessage(), [
                'product_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut'
            ], 500);
        }
    }

    /**
     * Afficher la page de gestion des prix par quantité
     */
    public function quantityPrices($id)
    {
        $product = Product::with('prices')->findOrFail($id);

        return view('admin.products.quantity_prices', compact('product'));
    }

    /**
     * Ajouter un palier de prix par quantité
     */
    public function storeQuantityPrice(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'min_quantity' => 'required|integer|min:1',
            'max_quantity' => 'nullable|integer|min:1|gt:min_quantity',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        // Vérifier qu'il n'y a pas de chevauchement de plages
        $overlapping = $product->prices()
            ->where(function($query) use ($validated) {
                $query->where(function($q) use ($validated) {
                    // Cas 1: min_quantity dans une plage existante
                    $q->where('min_quantity', '<=', $validated['min_quantity'])
                      ->where(function($subQ) use ($validated) {
                          $subQ->whereNull('max_quantity')
                               ->orWhere('max_quantity', '>=', $validated['min_quantity']);
                      });
                })
                ->orWhere(function($q) use ($validated) {
                    // Cas 2: max_quantity dans une plage existante (si défini)
                    if (isset($validated['max_quantity'])) {
                        $q->where('min_quantity', '<=', $validated['max_quantity'])
                          ->where(function($subQ) use ($validated) {
                              $subQ->whereNull('max_quantity')
                                   ->orWhere('max_quantity', '>=', $validated['max_quantity']);
                          });
                    }
                });
            })
            ->exists();

        if ($overlapping) {
            return back()->with('error', 'Cette plage de quantité chevauche un palier existant.');
        }

        $product->prices()->create($validated);

        return back()->with('success', 'Palier de prix ajouté avec succès !');
    }

    /**
     * Activer/Désactiver un palier de prix
     */
    public function toggleQuantityPrice($productId, $priceId)
    {
        $product = Product::findOrFail($productId);
        $price = $product->prices()->findOrFail($priceId);

        $price->update(['is_active' => !$price->is_active]);

        $status = $price->is_active ? 'activé' : 'désactivé';
        return back()->with('success', "Palier de prix {$status} avec succès !");
    }

    /**
     * Supprimer un palier de prix
     */
    public function destroyQuantityPrice($productId, $priceId)
    {
        $product = Product::findOrFail($productId);
        $price = $product->prices()->findOrFail($priceId);

        $price->delete();

        return back()->with('success', 'Palier de prix supprimé avec succès !');
    }
}
