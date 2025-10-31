<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\Attribute;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * Get all products with filters.
     */
    public function index(Request $request)
    {
        // Utiliser une requête simple pour éviter les problèmes de mémoire
        $query = \App\Models\ProductSimple::where('status', 'active');

        // Appliquer les filtres
        $this->applyFilters($query, $request);

        // Pagination
        $perPage = $request->get('per_page', 20);
        $products = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Formater les produits avec les URLs complètes des images
        $formattedProducts = collect($products->items())->map(function($product) {
            return $this->formatProductForApi($product);
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
            ]
        ]);
    }

    /**
     * Get a specific product.
     */
    public function show($id)
    {
        $product = Product::with([
            'category',
            'productType',
            'productImages',
            'attributeValues.attribute',
            'variants' => function($query) {
                $query->where('is_active', true);
            }
        ])->where('status', 'active')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
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

        // Filtre par disponibilité
        if ($request->filled('in_stock')) {
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
     * Formater un produit pour l'API avec les URLs complètes des images.
     */
    private function formatProductForApi($product)
    {
        $productData = $product->toArray();

        // Formater les images avec URLs complètes
        if ($product->images) {
            $images = is_array($product->images) ? $product->images : json_decode($product->images, true);
            if ($images && count($images) > 0) {
                $productData['images'] = array_map(function($image) {
                    // Si c'est une URL complète, l'utiliser directement
                    if (is_string($image) && str_starts_with($image, 'http')) {
                        return $image;
                    }
                    // Si c'est un array vide, l'ignorer
                    if (is_array($image) && empty($image)) {
                        return null;
                    }
                    // Sinon, traiter comme un chemin relatif
                    return url('storage/' . ltrim($image, '/'));
                }, $images);

                // Filtrer les images null et récupérer la première image valide
                $validImages = array_filter($productData['images'], function($img) { return $img !== null; });
                $productData['main_image'] = !empty($validImages) ? reset($validImages) : url('images/placeholder.jpg');
            } else {
                $productData['images'] = [];
                $productData['main_image'] = url('images/placeholder.jpg');
            }
        } else {
            $productData['images'] = [];
            $productData['main_image'] = url('images/placeholder.jpg');
        }

        // Formater les tags
        if ($product->tags) {
            $tags = is_array($product->tags) ? $product->tags : json_decode($product->tags, true);
            $productData['tags'] = $tags ?: [];
        } else {
            $productData['tags'] = [];
        }

        return $productData;
    }
}
