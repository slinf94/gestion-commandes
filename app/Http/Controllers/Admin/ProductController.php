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
        ini_set('memory_limit', '1G'); // Augmentation à 1GB
        ini_set('max_execution_time', 300); // 5 minutes max
    }

    public function index(Request $request)
    {
        try {
            // Construction de la requête avec filtres dynamiques
            $query = DB::table('products')
                ->select('id', 'name', 'price', 'status', 'created_at', 'category_id', 'product_type_id');

            // Temporairement: afficher TOUS les produits pour vérifier (commenté)
            // ->whereNull('deleted_at');

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

            // Tri dynamique - Par défaut par ID croissant (du plus ancien au plus récent)
            $sortBy = $request->get('sort_by', 'id');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination configurable
            $perPage = $request->get('per_page', 10);
            $products = $query->paginate($perPage)->appends($request->query());

            // Données pour les filtres
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

    public function show($id)
    {
        try {
            // Version ULTRA-SIMPLE - Pas de modèle Eloquent du tout
            $product = DB::table('products')
                ->select('id', 'name', 'description', 'price', 'cost_price', 'wholesale_price', 'retail_price', 'min_wholesale_quantity', 'stock_quantity', 'min_stock_alert', 'status', 'sku', 'barcode', 'category_id', 'product_type_id', 'meta_title', 'meta_description', 'tags', 'images', 'created_at', 'updated_at')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$product) {
                return redirect()->route('admin.products.index')
                    ->with('error', 'Produit non trouvé.');
            }

            // Chargement séparé des relations si nécessaire (avec limite)
            $category = null;
            if ($product->category_id) {
                $category = DB::table('categories')
                    ->select('id', 'name')
                    ->where('id', $product->category_id)
                    ->first();
            }

            $productType = null;
            if ($product->product_type_id) {
                $productType = DB::table('product_types')
                    ->select('id', 'name')
                    ->where('id', $product->product_type_id)
                    ->first();
            }

            // Chargement des attributs du produit
            $attributeValues = DB::table('product_attribute_values')
                ->join('product_type_attributes', 'product_attribute_values.product_type_attribute_id', '=', 'product_type_attributes.id')
                ->select('product_attribute_values.*',
                         'product_type_attributes.attribute_name',
                         'product_type_attributes.attribute_type',
                         'product_type_attributes.options')
                ->where('product_attribute_values.product_id', $id)
                ->orderBy('product_type_attributes.sort_order')
                ->get();

            // Chargement des images du produit
            $productImages = DB::table('product_images')
                ->select('id', 'product_id', 'url', 'order')
                ->where('product_id', $id)
                ->orderBy('order')
                ->get();

            // Conversion en objet pour la compatibilité avec la vue
            $product = (object) $product;
            $product->category = $category;
            $product->productType = $productType;
            $product->attributeValues = $attributeValues;
            $product->productImages = $productImages;

            // Libération mémoire
            unset($category, $productType, $attributeValues, $productImages);

            return view('admin.products.show', compact('product'));

        } catch (\Exception $e) {
            \Log::error('Erreur dans ProductController::show: ' . $e->getMessage());
            return redirect()->route('admin.products.index')
                ->with('error', 'Erreur lors du chargement du produit.');
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
        ]);

        try {
            DB::beginTransaction();

            // Création ultra-simple
            $productId = DB::table('products')->insertGetId([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'product_type_id' => $request->product_type_id,
                'status' => $request->status,
                'sku' => $request->sku,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.products.show', $productId)
                ->with('success', 'Produit créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création du produit: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du produit.');
        }
    }

    public function edit($id)
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

            // Chargement du produit
            $product = DB::table('products')
                ->select('id', 'name', 'description', 'price', 'cost_price', 'wholesale_price', 'retail_price', 'min_wholesale_quantity', 'stock_quantity', 'min_stock_alert', 'status', 'sku', 'barcode', 'category_id', 'product_type_id', 'meta_title', 'meta_description', 'tags', 'images')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$product) {
                return redirect()->route('admin.products.index')
                    ->with('error', 'Produit non trouvé.');
            }

            // Chargement des attributs existants du produit (LIMITÉ)
            $productAttributeValues = DB::table('product_attribute_values')
                ->where('product_id', $id)
                ->limit(100)
                ->get()
                ->keyBy('attribute_id');

            // Chargement des images existantes (LIMITÉ)
            $productImages = DB::table('product_images')
                ->select('id', 'url', 'order')
                ->where('product_id', $id)
                ->orderBy('order')
                ->limit(20)
                ->get();

            // Conversion pour compatibilité
            $product = (object) $product;
            $product->productImages = $productImages;

            return view('admin.products.edit', compact('product', 'categories', 'productTypes', 'attributes', 'productAttributeValues'));

        } catch (\Exception $e) {
            \Log::error('Erreur dans ProductController::edit: ' . $e->getMessage());
            return redirect()->route('admin.products.index')
                ->with('error', 'Erreur lors du chargement du formulaire.');
        }
    }

    public function update(Request $request, $id)
    {
        // Log pour debug
        \Log::info('=== UPDATE PRODUCT ===', [
            'id' => $id,
            'all_data' => $request->all(),
            'name' => $request->name,
            'price' => $request->price
        ]);

                try {
            // Validation
            $validated = $request->validate([
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
            ]);

            \Log::info('Validation OK, données validées:', $validated);

            // Conversion des virgules en points pour les prix
            $validated['price'] = str_replace(',', '.', $validated['price']);
            if (!empty($validated['cost_price'])) {
                $validated['cost_price'] = str_replace(',', '.', $validated['cost_price']);
            }

            // Mise à jour avec DB::table pour éviter les problèmes de mémoire
            DB::table('products')
                ->where('id', $id)
                ->update(array_merge($validated, ['updated_at' => now()]));

            \Log::info('Produit mis à jour:', ['id' => $id, 'name' => $validated['name']]);

            // Gestion des images
            if ($request->hasFile('images')) {
                $this->handleImageUpload($request->file('images'), $id);
            }

            return redirect()->route('admin.products.index')
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

    public function destroy($id)
    {
        try {
            // Suppression ultra-simple (soft delete)
            DB::table('products')
                ->where('id', $id)
                ->update(['deleted_at' => now()]);

            return redirect()->route('admin.products.index')
                ->with('success', 'Produit supprimé avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du produit: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du produit.');
        }
    }
}
