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
            $query = DB::table('products')->select('id', 'name', 'slug', 'description', 'price', 'cost_price', 'wholesale_price', 'retail_price', 'min_wholesale_quantity', 'stock_quantity', 'min_stock_alert', 'status', 'sku', 'barcode', 'category_id', 'product_type_id', 'meta_title', 'meta_description', 'tags', 'images', 'created_at', 'updated_at', 'deleted_at');

            // Filtres dynamiques
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('product_type_id')) {
                $query->where('product_type_id', $request->product_type_id);
            }

            if ($request->filled('price_min')) {
                $query->where('price', '>=', $request->price_min);
            }

            if ($request->filled('price_max')) {
                $query->where('price', '<=', $request->price_max);
            }

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

            // Pagination configurable
            $perPage = $request->get('per_page', 10);
            $products = $query->paginate($perPage)->appends($request->query());

            // Précharger toutes les images des produits en une seule requête
            if ($products->count() > 0) {
                $productIds = $products->pluck('id')->toArray();
                $allImages = DB::table('product_images')
                    ->whereIn('product_id', $productIds)
                    ->get()
                    ->groupBy('product_id');

                // Ajouter les images à chaque produit et s'assurer que le slug existe
                foreach ($products as $product) {
                    $product->images = $allImages->get($product->id) ?? collect();
                    // S'assurer que le slug existe, sinon le générer
                    if (empty($product->slug) || is_null($product->slug) || trim($product->slug) === '') {
                        $product->slug = Product::generateSlug($product->name, $product->id);
                        // Mettre à jour en base de données
                        DB::table('products')->where('id', $product->id)->update(['slug' => $product->slug]);
                    }
                    // S'assurer que le slug est une chaîne valide
                    $product->slug = trim($product->slug ?? '');
                }
            }

            // Charger les catégories et types pour les filtres
            $categories = DB::table('categories')
                ->select('id', 'name')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            $productTypes = DB::table('product_types')
                ->select('id', 'name')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            // Statistiques pour les filtres
            $stats = [
                'total' => DB::table('products')->count(),
                'active' => DB::table('products')->where('status', 'active')->count(),
                'inactive' => DB::table('products')->where('status', 'inactive')->count(),
                'draft' => DB::table('products')->where('status', 'draft')->count(),
            ];

            return view('admin.products.index', compact('products', 'categories', 'productTypes', 'stats'));

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

            return view('admin.products.create', compact('categories', 'productTypes', 'attributes'));

        } catch (\Exception $e) {
            \Log::error('Erreur dans ProductController::create: ' . $e->getMessage());
            return redirect()->route('admin.products.index')
                ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'product_type_id' => 'nullable|exists:product_types,id',
            'status' => 'required|in:active,inactive,draft',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'images' => 'nullable',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:4096',
        ]);

        try {
            DB::beginTransaction();

            // Générer le slug
            $slug = Product::generateSlug($request->name);
            
            // Création ultra-simple
            $productId = DB::table('products')->insertGetId([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'product_type_id' => $request->product_type_id,
                'status' => $request->status,
                'sku' => $request->sku,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Upload des images si fournies
            if ($request->hasFile('images')) {
                $this->handleImageUpload($request->file('images'), $productId);
            }

            DB::commit();

            // Récupérer le slug du produit créé
            $productSlug = DB::table('products')->where('id', $productId)->value('slug');
            
            return redirect()->route('admin.products.show', $productSlug)
                ->with('success', 'Produit créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création du produit: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du produit.');
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

            // Chargement du produit par slug (sans route model binding pour éviter la mémoire)
            // Si le slug contient un ID à la fin (format: nom-produit-id), essayer de trouver par ID aussi
            $productData = DB::table('products')
                ->select('id', 'name', 'slug', 'description', 'price', 'cost_price', 'wholesale_price', 'retail_price', 'min_wholesale_quantity', 'stock_quantity', 'min_stock_alert', 'status', 'sku', 'barcode', 'category_id', 'product_type_id', 'meta_title', 'meta_description', 'tags', 'images')
                ->where('slug', $slug)
                ->whereNull('deleted_at')
                ->first();
            
            // Si pas trouvé par slug, essayer de trouver par ID si le slug contient un ID (format: nom-produit-id)
            if (!$productData && preg_match('/-(\d+)$/', $slug, $matches)) {
                $productId = (int)$matches[1];
                $productData = DB::table('products')
                    ->select('id', 'name', 'slug', 'description', 'price', 'cost_price', 'wholesale_price', 'retail_price', 'min_wholesale_quantity', 'stock_quantity', 'min_stock_alert', 'status', 'sku', 'barcode', 'category_id', 'product_type_id', 'meta_title', 'meta_description', 'tags', 'images')
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
                    ->select('id', 'name', 'slug', 'description', 'price', 'cost_price', 'wholesale_price', 'retail_price', 'min_wholesale_quantity', 'stock_quantity', 'min_stock_alert', 'status', 'sku', 'barcode', 'category_id', 'product_type_id', 'meta_title', 'meta_description', 'tags', 'images')
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

            return view('admin.products.edit', compact('product', 'categories', 'productTypes', 'attributes', 'productAttributeValues'));

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
                'tags' => $validated['tags'] ?? null,
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
            $imagePath = $image->storeAs('products', $imageName, 'public');

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
}
