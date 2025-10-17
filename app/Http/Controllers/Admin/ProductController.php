<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'productImages'])
            ->withTrashed()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'orderItems', 'productImages']);
        return view('admin.products.show', compact('product'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
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
            'sku' => 'required|string|unique:products,sku',
            'barcode' => 'nullable|string|unique:products,barcode',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
            'images' => 'nullable|array|max:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        // Créer les entrées dans product_images si des images ont été uploadées
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->productImages()->create([
                    'url' => $path,
                    'type' => $index === 0 ? 'principale' : 'galerie',
                    'order' => $index + 1,
                    'alt_text' => "Image du produit {$product->name}"
                ]);
            }
        }

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Produit créé avec succès');
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'productImages']);
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
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
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'status' => 'required|in:active,inactive',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
        ]);

        $product->update($request->all());

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
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $uploadedImages = [];
        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('products', 'public');
            $order = $product->productImages()->max('order') + $index + 1;

            $productImage = $product->productImages()->create([
                'url' => $path,
                'type' => 'galerie',
                'order' => $order,
                'alt_text' => "Image du produit {$product->name}"
            ]);

            $uploadedImages[] = $productImage;
        }

        return redirect()->route('admin.products.show', $product)
            ->with('success', count($uploadedImages) . ' image(s) ajoutée(s) avec succès');
    }

    public function setMainImage(Request $request, Product $product, ProductImage $image)
    {
        // Retirer le statut "principale" de toutes les autres images
        $product->productImages()->update(['type' => 'galerie']);

        // Définir cette image comme principale
        $image->update(['type' => 'principale']);

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Image principale mise à jour avec succès');
    }

    public function deleteImage(Product $product, ProductImage $image)
    {
        // Supprimer le fichier physique
        if (Storage::disk('public')->exists($image->url)) {
            Storage::disk('public')->delete($image->url);
        }

        // Supprimer l'entrée de la base de données
        $image->delete();

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Image supprimée avec succès');
    }
}
