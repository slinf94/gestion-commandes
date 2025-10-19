<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\Attribute;
use Illuminate\Http\Request;

class ProductFilterController extends Controller
{
    /**
     * Display products with filters.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'productType', 'productImages', 'attributeValues.attribute'])
            ->where('status', 'active');

        // Filtres
        $filters = $this->applyFilters($query, $request);

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        // Données pour les filtres
        $categories = Category::where('is_active', true)->get();
        $productTypes = ProductType::where('is_active', true)->get();
        $attributes = Attribute::where('is_active', true)
            ->where('is_filterable', true)
            ->with('attributeValues')
            ->get();

        return view('frontend.products.index', compact('products', 'categories', 'productTypes', 'attributes', 'filters'));
    }

    /**
     * Get filtered products via AJAX.
     */
    public function getFilteredProducts(Request $request)
    {
        $query = Product::with(['category', 'productType', 'productImages', 'attributeValues.attribute'])
            ->where('status', 'active');

        // Appliquer les filtres
        $this->applyFilters($query, $request);

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'products' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Get filter options for AJAX requests.
     */
    public function getFilterOptions(Request $request)
    {
        $categoryId = $request->get('category_id');
        $productTypeId = $request->get('product_type_id');

        $query = Product::where('status', 'active');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($productTypeId) {
            $query->where('product_type_id', $productTypeId);
        }

        $products = $query->with('attributeValues.attribute')->get();

        // Extraire les valeurs d'attributs disponibles
        $attributeValues = [];
        foreach ($products as $product) {
            foreach ($product->attributeValues as $attributeValue) {
                $attributeName = $attributeValue->attribute->name;
                if (!isset($attributeValues[$attributeName])) {
                    $attributeValues[$attributeName] = [];
                }
                $attributeValues[$attributeName][] = $attributeValue->value;
            }
        }

        // Nettoyer et dédupliquer
        foreach ($attributeValues as $attributeName => $values) {
            $attributeValues[$attributeName] = array_unique(array_filter($values));
            sort($attributeValues[$attributeName]);
        }

        return response()->json([
            'attribute_values' => $attributeValues
        ]);
    }

    /**
     * Apply filters to the query.
     */
    private function applyFilters($query, Request $request)
    {
        $filters = [];

        // Filtre par catégorie
        if ($request->filled('category_id')) {
            $categoryId = $request->get('category_id');
            $query->where('category_id', $categoryId);
            $filters['category_id'] = $categoryId;
        }

        // Filtre par type de produit
        if ($request->filled('product_type_id')) {
            $productTypeId = $request->get('product_type_id');
            $query->where('product_type_id', $productTypeId);
            $filters['product_type_id'] = $productTypeId;
        }

        // Filtre par prix
        if ($request->filled('min_price')) {
            $minPrice = $request->get('min_price');
            $query->where('price', '>=', $minPrice);
            $filters['min_price'] = $minPrice;
        }

        if ($request->filled('max_price')) {
            $maxPrice = $request->get('max_price');
            $query->where('price', '<=', $maxPrice);
            $filters['max_price'] = $maxPrice;
        }

        // Filtre par disponibilité
        if ($request->filled('in_stock')) {
            $query->where('stock_quantity', '>', 0);
            $filters['in_stock'] = true;
        }

        // Filtre par produits vedettes
        if ($request->filled('featured')) {
            $query->where('is_featured', true);
            $filters['featured'] = true;
        }

        // Filtre par recherche
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
            $filters['search'] = $search;
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
                    $filters['attributes'][$attributeName] = $values;
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

        $filters['sort_by'] = $sortBy;
        $filters['sort_order'] = $sortOrder;

        return $filters;
    }

    /**
     * Get products by category.
     */
    public function category(Category $category, Request $request)
    {
        $query = Product::with(['category', 'productType', 'productImages', 'attributeValues.attribute'])
            ->where('status', 'active')
            ->where('category_id', $category->id);

        // Appliquer les filtres supplémentaires
        $this->applyFilters($query, $request);

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        // Données pour les filtres
        $categories = Category::where('is_active', true)->get();
        $productTypes = ProductType::where('is_active', true)->get();
        $attributes = Attribute::where('is_active', true)
            ->where('is_filterable', true)
            ->with('attributeValues')
            ->get();

        return view('frontend.products.category', compact('products', 'category', 'categories', 'productTypes', 'attributes'));
    }

    /**
     * Get products by type.
     */
    public function type(ProductType $productType, Request $request)
    {
        $query = Product::with(['category', 'productType', 'productImages', 'attributeValues.attribute'])
            ->where('status', 'active')
            ->where('product_type_id', $productType->id);

        // Appliquer les filtres supplémentaires
        $this->applyFilters($query, $request);

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        // Données pour les filtres
        $categories = Category::where('is_active', true)->get();
        $productTypes = ProductType::where('is_active', true)->get();
        $attributes = Attribute::where('is_active', true)
            ->where('is_filterable', true)
            ->with('attributeValues')
            ->get();

        return view('frontend.products.type', compact('products', 'productType', 'categories', 'productTypes', 'attributes'));
    }
}
