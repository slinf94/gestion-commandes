<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    /**
     * Display a listing of attributes.
     */
    public function index(Request $request)
    {
        $query = Attribute::query();

        // Recherche d'abord
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filtres
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Tri
        $sortField = $request->get('sort', 'sort_order');
        $sortOrder = $request->get('order', 'asc');

        // Validation des champs de tri pour la sécurité
        $allowedSortFields = ['name', 'type', 'sort_order', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'sort_order';
        }

        $allowedSortOrders = ['asc', 'desc'];
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'asc';
        }

        $query->orderBy($sortField, $sortOrder);

        // Si on trie par autre chose que sort_order, ajouter sort_order comme tri secondaire
        if ($sortField !== 'sort_order') {
            $query->orderBy('sort_order', 'asc');
        }

        // Statistiques
        $stats = [
            'total' => Attribute::count(),
            'active' => Attribute::where('is_active', true)->count(),
            'inactive' => Attribute::where('is_active', false)->count(),
        ];

        // Pagination
        $perPage = $request->get('per_page', 15);
        $attributes = $query->paginate($perPage);

        return view('admin.attributes.index', compact('attributes', 'stats'));
    }

    /**
     * Show the form for creating a new attribute.
     */
    public function create()
    {
        return view('admin.attributes.create');
    }

    /**
     * Store a newly created attribute.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,number,select,multiselect,boolean,date,file',
            'options' => 'nullable|array',
            'options.*' => 'required|string',
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'is_variant' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'validation_rules' => 'nullable|array',
        ]);

        $data = $request->all();

        // Générer le slug
        $data['slug'] = Str::slug($data['name']);

        // Valeurs par défaut
        $data['is_required'] = $request->has('is_required');
        $data['is_filterable'] = $request->has('is_filterable');
        $data['is_variant'] = $request->has('is_variant');
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        Attribute::create($data);

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribut créé avec succès');
    }

    /**
     * Display the specified attribute.
     */
    public function show(Attribute $attribute)
    {
        $attribute->load(['productTypeAttributes.productType']);
        return view('admin.attributes.show', compact('attribute'));
    }

    /**
     * Show the form for editing the attribute.
     */
    public function edit(Attribute $attribute)
    {
        return view('admin.attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified attribute.
     */
    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,number,select,multiselect,boolean,date,file',
            'options' => 'nullable|array',
            'options.*' => 'required|string',
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'is_variant' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'validation_rules' => 'nullable|array',
        ]);

        $data = $request->all();

        // Générer le slug
        $data['slug'] = Str::slug($data['name']);

        // Valeurs par défaut
        $data['is_required'] = $request->has('is_required');
        $data['is_filterable'] = $request->has('is_filterable');
        $data['is_variant'] = $request->has('is_variant');
        $data['is_active'] = $request->has('is_active');

        $attribute->update($data);

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribut mis à jour avec succès');
    }

    /**
     * Remove the specified attribute.
     */
    public function destroy(Attribute $attribute)
    {
        // Vérifier s'il est utilisé par des produits
        if ($attribute->productAttributeValues()->count() > 0) {
            return redirect()->route('admin.attributes.index')
                ->with('error', 'Impossible de supprimer cet attribut car il est utilisé par des produits');
        }

        $attribute->delete();

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribut supprimé avec succès');
    }

    /**
     * Toggle attribute status.
     */
    public function toggleStatus(Attribute $attribute)
    {
        $attribute->update(['is_active' => !$attribute->is_active]);

        return redirect()->back()
            ->with('success', 'Statut de l\'attribut mis à jour');
    }

    /**
     * Get attribute options for AJAX requests.
     */
    public function getOptions(Attribute $attribute)
    {
        return response()->json([
            'options' => $attribute->options ?? []
        ]);
    }
}
