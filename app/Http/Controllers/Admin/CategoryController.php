<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function __construct()
    {
        // Augmenter la limite de mémoire PHP pour les opérations potentiellement lourdes
        ini_set('memory_limit', '1G'); // 1GB
        ini_set('max_execution_time', 300); // 5 minutes max
    }

    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        try {
            // Construction de la requête avec filtres dynamiques
            $query = DB::table('categories')
                ->select('id', 'name', 'description', 'parent_id', 'sort_order', 'icon', 'color', 'is_active', 'is_featured', 'created_at')
                ->whereNull('deleted_at')
                ->whereNull('parent_id');

            // Filtres dynamiques
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            if ($request->filled('is_featured')) {
                $query->where('is_featured', $request->is_featured);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Tri dynamique
            $sortBy = $request->get('sort_by', 'sort_order');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination configurable
            $perPage = $request->get('per_page', 10);
            $categories = $query->paginate($perPage)->appends($request->query());

            // Charger les compteurs en une seule requête pour toutes les catégories
            $categoryIds = $categories->pluck('id')->toArray();

            if (!empty($categoryIds)) {
                // Compter les enfants pour toutes les catégories en une seule requête
                $childrenCounts = DB::table('categories')
                    ->select('parent_id', DB::raw('COUNT(*) as count'))
                    ->whereIn('parent_id', $categoryIds)
                    ->whereNull('deleted_at')
                    ->groupBy('parent_id')
                    ->pluck('count', 'parent_id');

                // Compter les produits pour toutes les catégories en une seule requête
                $productsCounts = DB::table('products')
                    ->select('category_id', DB::raw('COUNT(*) as count'))
                    ->whereIn('category_id', $categoryIds)
                    ->whereNull('deleted_at')
                    ->groupBy('category_id')
                    ->pluck('count', 'category_id');

                // Assigner les compteurs aux catégories
                foreach ($categories as $category) {
                    $category->children_count = $childrenCounts[$category->id] ?? 0;
                    $category->products_count = $productsCounts[$category->id] ?? 0;
                    $category->parent = null; // S'assurer que parent est null pour les catégories racines
                }
            }

            // Statistiques pour les filtres
            $stats = [
                'total' => DB::table('categories')->whereNull('deleted_at')->whereNull('parent_id')->count(),
                'active' => DB::table('categories')->where('is_active', true)->whereNull('deleted_at')->whereNull('parent_id')->count(),
                'inactive' => DB::table('categories')->where('is_active', false)->whereNull('deleted_at')->whereNull('parent_id')->count(),
                'featured' => DB::table('categories')->where('is_featured', true)->whereNull('deleted_at')->whereNull('parent_id')->count(),
            ];

            return view('admin.categories.index', compact('categories', 'stats'));

        } catch (\Exception $e) {
            \Log::error('Erreur dans CategoryController::index: ' . $e->getMessage());
            return view('admin.categories.index', [
                'categories' => (object)['data' => collect([]), 'total' => 0],
                'stats' => ['total' => 0, 'active' => 0, 'inactive' => 0, 'featured' => 0],
                'error' => 'Erreur lors du chargement des catégories. Veuillez réessayer.'
            ]);
        }
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        try {
            $parentCategories = DB::table('categories')
                ->select('id', 'name')
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->limit(20)
                ->get();

            return view('admin.categories.create', compact('parentCategories'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans CategoryController::create: ' . $e->getMessage());
            return redirect()->route('admin.categories.index')
                ->with('error', 'Erreur lors du chargement du formulaire de création.');
        }
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'slug' => Str::slug($request->name),
                'color' => $request->color,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Gérer l'upload d'image
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('categories', 'public');
            }

            DB::table('categories')->insert($data);

            DB::commit();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Catégorie créée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création de la catégorie: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la catégorie.');
        }
    }

    /**
     * Display the specified category.
     */
    public function show($id)
    {
        try {
            // Chargement minimal avec DB::table()
            $category = DB::table('categories')
                ->select('id', 'name', 'description', 'slug', 'parent_id', 'sort_order', 'image', 'icon', 'color', 'is_active', 'is_featured', 'created_at', 'updated_at')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$category) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Catégorie non trouvée.');
            }

            // Charger les enfants séparément
            $children = DB::table('categories')
                ->select('id', 'name', 'description', 'slug', 'sort_order', 'is_active')
                ->where('parent_id', $id)
                ->whereNull('deleted_at')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->limit(20)
                ->get();

            // Charger le comptage des produits pour chaque enfant
            if ($children->count() > 0) {
                $childrenIds = $children->pluck('id')->toArray();
                $productsCounts = DB::table('products')
                    ->select('category_id', DB::raw('COUNT(*) as count'))
                    ->whereIn('category_id', $childrenIds)
                    ->whereNull('deleted_at')
                    ->groupBy('category_id')
                    ->pluck('count', 'category_id');

                // Assigner le comptage à chaque enfant
                foreach ($children as $child) {
                    $child->products_count = $productsCounts[$child->id] ?? 0;
                }
            }

            // Charger les produits séparément
            $products = DB::table('products')
                ->select('id', 'name', 'price', 'status', 'stock_quantity', 'created_at')
                ->where('category_id', $id)
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            // Charger la catégorie parente si nécessaire
            $parent = null;
            if ($category->parent_id) {
                $parent = DB::table('categories')
                    ->select('id', 'name')
                    ->where('id', $category->parent_id)
                    ->whereNull('deleted_at')
                    ->first();
            }

            // Attacher les données à la catégorie
            $category = (object) $category;
            $category->children = $children;
            $category->products = $products;
            $category->parent = $parent;

            return view('admin.categories.show', compact('category'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans CategoryController::show: ' . $e->getMessage());
            return redirect()->route('admin.categories.index')
                ->with('error', 'Erreur lors du chargement de la catégorie.');
        }
    }

    /**
     * Show the form for editing the category.
     */
    public function edit($id)
    {
        try {
            $category = DB::table('categories')
                ->select('id', 'name', 'description', 'parent_id', 'sort_order', 'image', 'icon', 'color', 'meta_data', 'is_active', 'is_featured')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$category) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Catégorie non trouvée.');
            }

            $categories = DB::table('categories')
                ->select('id', 'name')
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->where('id', '!=', $id)
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->limit(20)
                ->get();

            return view('admin.categories.edit', compact('category', 'categories'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans CategoryController::edit: ' . $e->getMessage());
            return redirect()->route('admin.categories.index')
                ->with('error', 'Erreur lors du chargement du formulaire d\'édition.');
        }
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'slug' => Str::slug($request->name),
                'color' => $request->color,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
                'updated_at' => now(),
            ];

            // Gérer l'upload d'image
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image
                $oldCategory = DB::table('categories')->select('image')->where('id', $id)->first();
                if ($oldCategory && $oldCategory->image) {
                    \Storage::disk('s3')->delete($oldCategory->image);
                }
                $data['image'] = $request->file('image')->store('categories', 'public');
            }

            DB::table('categories')->where('id', $id)->update($data);

            DB::commit();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Catégorie mise à jour avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la mise à jour de la catégorie: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de la catégorie.');
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy($id)
    {
        try {
            // Vérifier s'il y a des produits ou sous-catégories
            $productsCount = DB::table('products')->where('category_id', $id)->whereNull('deleted_at')->count();
            $childrenCount = DB::table('categories')->where('parent_id', $id)->whereNull('deleted_at')->count();

            if ($productsCount > 0 || $childrenCount > 0) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Impossible de supprimer cette catégorie car elle contient des produits ou des sous-catégories');
            }

            // Supprimer l'image
            $category = DB::table('categories')->select('image')->where('id', $id)->first();
            if ($category && $category->image) {
                \Storage::disk('s3')->delete($category->image);
            }

            // Soft delete
            DB::table('categories')->where('id', $id)->update(['deleted_at' => now()]);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Catégorie supprimée avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression de la catégorie: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de la catégorie.');
        }
    }

    /**
     * Toggle category status.
     */
    public function toggleStatus($id)
    {
        try {
            $category = DB::table('categories')->select('is_active')->where('id', $id)->first();
            if ($category) {
                DB::table('categories')->where('id', $id)->update([
                    'is_active' => !$category->is_active,
                    'updated_at' => now()
                ]);
            }

            return redirect()->back()
                ->with('success', 'Statut de la catégorie mis à jour');
        } catch (\Exception $e) {
            \Log::error('Erreur lors du changement de statut: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors du changement de statut.');
        }
    }

    /**
     * Reorder categories.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.sort_order' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->categories as $categoryData) {
                DB::table('categories')
                    ->where('id', $categoryData['id'])
                    ->update([
                        'sort_order' => $categoryData['sort_order'],
                        'updated_at' => now()
                    ]);
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors du réordonnement: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Erreur lors du réordonnement'], 500);
        }
    }
}
