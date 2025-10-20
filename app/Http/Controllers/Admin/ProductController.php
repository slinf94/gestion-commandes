<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductSimple as Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\Attribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Utiliser les modèles Eloquent avec les relations pour afficher les images
        $query = Product::with(['category', 'productType', 'productImages'])
            ->whereNull('deleted_at');

        // Filtres
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('product_type_id')) {
            $query->where('product_type_id', $request->product_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        $categories = Category::where('is_active', true)->get();
        $productTypes = ProductType::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories', 'productTypes'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'orderItems', 'productImages']);
        return view('admin.products.show', compact('product'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $productTypes = ProductType::where('is_active', true)->get();
        $attributes = Attribute::where('is_active', true)->get();

        return view('admin.products.create', compact('categories', 'productTypes', 'attributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0|regex:/^[1-9][0-9]*$/',
            'min_stock_alert' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'product_type_id' => 'nullable|exists:product_types,id',
            'sku' => 'required|string|unique:products,sku',
            'barcode' => 'nullable|string|unique:products,barcode',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
            'images' => 'nullable|array|max:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_id' => 'required|exists:attributes,id',
            'attributes.*.value' => 'required|string',
            'attributes.*.display_value' => 'nullable|string',
        ], [
            'stock_quantity.regex' => 'La quantité en stock doit être un nombre entier positif (ne peut pas commencer par 0).',
            'images.*.required' => 'Chaque image est requise.',
            'images.*.image' => 'Le fichier doit être une image.',
            'images.*.mimes' => 'L\'image doit être de type: jpeg, png, jpg, gif.',
            'images.*.max' => 'L\'image ne doit pas dépasser 2MB.',
        ]);

        $data = $request->all();

        // Traitement des images
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
            }
            $data['images'] = $imagePaths;
        }

        // Traitement des tags
        if ($request->has('tags') && !empty($request->tags)) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        }

        $product = Product::create($data);

        // Gérer les attributs du produit
        if ($request->has('attributes')) {
            foreach ($request->input('attributes') as $attributeData) {
                if (!empty($attributeData['attribute_id']) && !empty($attributeData['value'])) {
                    // Trouver le product_type_attribute_id correspondant
                    $productTypeAttribute = \DB::table('product_type_attributes')
                        ->where('product_type_id', $product->product_type_id)
                        ->where('attribute_id', $attributeData['attribute_id'])
                        ->first();

                    if ($productTypeAttribute) {
                        $product->attributeValues()->create([
                            'product_type_attribute_id' => $productTypeAttribute->id,
                            'attribute_value' => $attributeData['value'],
                            'numeric_value' => is_numeric($attributeData['value']) ? $attributeData['value'] : null,
                        ]);
                    }
                }
            }
        }

        // Créer les entrées dans product_images si des images ont été uploadées
        if ($request->hasFile('images')) {
            $uploadedImages = [];
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $uploadedImages[] = $path;

                $product->productImages()->create([
                    'url' => $path,
                    'type' => $index === 0 ? 'principale' : 'galerie',
                    'order' => $index + 1,
                    'alt_text' => "Image du produit {$product->name}"
                ]);
            }

            // Mettre à jour la colonne images du produit
            $product->update(['images' => $uploadedImages]);
        }

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Produit créé avec succès');
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'productType', 'productImages', 'attributeValues.attribute']);
        $categories = Category::where('is_active', true)->get();
        $productTypes = ProductType::where('is_active', true)->get();
        $attributes = Attribute::where('is_active', true)->get();

        return view('admin.products.edit', compact('product', 'categories', 'productTypes', 'attributes'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_alert' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'product_type_id' => 'nullable|exists:product_types,id',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'status' => 'required|in:active,inactive',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_id' => 'required|exists:attributes,id',
            'attributes.*.value' => 'required|string',
            'attributes.*.display_value' => 'nullable|string',
        ]);

        $data = $request->all();

        // Traitement des tags
        if ($request->has('tags') && !empty($request->tags)) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        }

        $product->update($data);

        // Mettre à jour les attributs du produit
        if ($request->has('attributes')) {
            // Supprimer les anciens attributs
            $product->attributeValues()->delete();

            // Créer les nouveaux attributs
            foreach ($request->input('attributes') as $attributeData) {
                if (!empty($attributeData['attribute_id']) && !empty($attributeData['value'])) {
                    $product->attributeValues()->create([
                        'attribute_id' => $attributeData['attribute_id'],
                        'value' => $attributeData['value'],
                        'display_value' => $attributeData['display_value'] ?? $attributeData['value'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Produit mis à jour avec succès');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Produit supprimé avec succès');
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();
        return redirect()->route('admin.products.index')
            ->with('success', 'Produit restauré avec succès');
    }

    public function uploadImages(Request $request, Product $product)
    {
        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $uploadedImages = [];
        $uploadedPaths = [];

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('products', 'public');
            $uploadedPaths[] = $path;
            $order = $product->productImages()->max('order') + $index + 1;

            $productImage = $product->productImages()->create([
                'url' => $path,
                'type' => 'galerie',
                'order' => $order,
                'alt_text' => "Image du produit {$product->name}"
            ]);

            $uploadedImages[] = $productImage;
        }

        // Mettre à jour la colonne images du produit
        $existingImages = $product->images ? (is_array($product->images) ? $product->images : json_decode($product->images, true)) : [];
        $allImages = array_merge($existingImages, $uploadedPaths);
        $product->update(['images' => $allImages]);

        return redirect()->route('admin.products.show', $product)
            ->with('success', count($uploadedImages) . ' image(s) ajoutée(s) avec succès');
    }

    public function setMainImage(Request $request, Product $product, $imageId)
    {
        // Récupérer l'image par ID
        $image = ProductImage::where('id', $imageId)
            ->where('product_id', $product->id)
            ->first();

        if (!$image) {
            return redirect()->route('admin.products.show', $product)
                ->with('error', 'Image non trouvée');
        }

        // Retirer le statut "principale" de toutes les autres images
        $product->productImages()->update(['type' => 'galerie']);

        // Définir cette image comme principale
        $image->update(['type' => 'principale']);

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Image principale mise à jour avec succès');
    }

    public function deleteImage(Request $request, Product $product, $imageId)
    {
        // Récupérer l'image par ID
        $image = ProductImage::where('id', $imageId)
            ->where('product_id', $product->id)
            ->first();

        if (!$image) {
            return redirect()->route('admin.products.show', $product)
                ->with('error', 'Image non trouvée');
        }

        // Supprimer le fichier physique
        if (Storage::disk('public')->exists($image->url)) {
            Storage::disk('public')->delete($image->url);
        }

        // Supprimer l'entrée de la base de données
        $image->delete();

        // Mettre à jour la colonne images du produit
        $existingImages = $product->images ? (is_array($product->images) ? $product->images : json_decode($product->images, true)) : [];
        $updatedImages = array_filter($existingImages, function($img) use ($image) {
            return $img !== $image->url;
        });
        $product->update(['images' => array_values($updatedImages)]);

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Image supprimée avec succès');
    }
}
