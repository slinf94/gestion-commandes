<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of product types.
     */
    public function index(Request $request)
    {
        $query = ProductType::with(['category', 'attributes']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filtre par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Tri
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');

        $allowedSortFields = ['name', 'sort_order', 'created_at', 'is_active'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'sort_order';
        }

        $query->orderBy($sortBy, $sortOrder);

        // Si on trie par autre chose que sort_order, ajouter sort_order comme tri secondaire
        if ($sortBy !== 'sort_order') {
            $query->orderBy('sort_order', 'asc');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $productTypes = $query->paginate($perPage)->appends($request->query());

        // Données supplémentaires
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $stats = [
            'total' => ProductType::count(),
            'active' => ProductType::where('is_active', true)->count(),
            'inactive' => ProductType::where('is_active', false)->count(),
        ];

        return view('admin.product-types.index', compact('productTypes', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new product type.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $attributes = Attribute::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.product-types.create', compact('categories', 'attributes'));
    }

    /**
     * Store a newly created product type.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'default_attributes' => 'nullable|array',
            'default_attributes.*' => 'exists:attributes,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();

        // Générer le slug
        $data['slug'] = Str::slug($data['name']);

        // Valeurs par défaut
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $productType = ProductType::create($data);

        // Associer les attributs sélectionnés
        if ($request->has('selected_attributes')) {
            $attributes = $request->input('selected_attributes', []);
            $productType->attributes()->sync($attributes);
        }

        return redirect()->route('admin.product-types.index')
            ->with('success', 'Type de produit créé avec succès');
    }

    /**
     * Display the specified product type.
     */
    public function show(ProductType $productType)
    {
        $productType->load(['category', 'attributes', 'products']);
        return view('admin.product-types.show', compact('productType'));
    }

    /**
     * Show the form for editing the product type.
     */
    public function edit(ProductType $productType)
    {
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $attributes = Attribute::where('is_active', true)
            ->orderBy('name')
            ->get();

        $productType->load('attributes');

        return view('admin.product-types.edit', compact('productType', 'categories', 'attributes'));
    }

    /**
     * Update the specified product type.
     */
    public function update(Request $request, ProductType $productType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'default_attributes' => 'nullable|array',
            'default_attributes.*' => 'exists:attributes,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();

        // Générer le slug
        $data['slug'] = Str::slug($data['name']);

        // Valeurs par défaut
        $data['is_active'] = $request->has('is_active');

        $productType->update($data);

        // Mettre à jour les attributs associés
        if ($request->has('selected_attributes')) {
            $attributes = $request->input('selected_attributes', []);
            $productType->attributes()->sync($attributes);
        }

        return redirect()->route('admin.product-types.index')
            ->with('success', 'Type de produit mis à jour avec succès');
    }

    /**
     * Remove the specified product type.
     */
    public function destroy(ProductType $productType)
    {
        // Vérifier s'il y a des produits de ce type
        if ($productType->products()->count() > 0) {
            return redirect()->route('admin.product-types.index')
                ->with('error', 'Impossible de supprimer ce type de produit car il est utilisé par des produits');
        }

        $productType->delete();

        return redirect()->route('admin.product-types.index')
            ->with('success', 'Type de produit supprimé avec succès');
    }

    /**
     * Toggle product type status.
     */
    public function toggleStatus(ProductType $productType)
    {
        $productType->update(['is_active' => !$productType->is_active]);

        return redirect()->back()
            ->with('success', 'Statut du type de produit mis à jour');
    }

    /**
     * Get attributes for a specific category (AJAX).
     */
    public function getAttributesForCategory(Category $category)
    {
        $attributes = Attribute::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'attributes' => $attributes->map(function($attribute) {
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'type' => $attribute->type,
                    'options' => $attribute->options,
                ];
            })
        ]);
    }
}
