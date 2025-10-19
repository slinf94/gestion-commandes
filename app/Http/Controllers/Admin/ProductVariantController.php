<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductSimple as Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of variants for a specific product.
     */
    public function index(Product $product)
    {
        $variants = $product->variants()->with('product')->get();
        return view('admin.product-variants.index', compact('product', 'variants'));
    }

    /**
     * Show the form for creating a new variant.
     */
    public function create(Product $product)
    {
        $variantAttributes = $product->getVariantAttributes();
        $attributes = Attribute::where('is_active', true)->get();

        return view('admin.product-variants.create', compact('product', 'variantAttributes', 'attributes'));
    }

    /**
     * Store a newly created variant.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'variant_name' => 'required|string|max:255',
            'sku' => 'required|string|unique:product_variants,sku',
            'price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'attributes' => 'required|array|min:1',
            'attributes.*.attribute_id' => 'required|exists:attributes,id',
            'attributes.*.value' => 'required|string',
            'images' => 'nullable|array|max:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();

        // Utiliser le prix du produit parent si non spécifié
        if (empty($data['price'])) {
            $data['price'] = $product->price;
        }

        // Traitement des attributs
        $attributesData = [];
        foreach ($request->input('attributes') as $attributeData) {
            if (!empty($attributeData['attribute_id']) && !empty($attributeData['value'])) {
                $attributesData[$attributeData['attribute_id']] = $attributeData['value'];
            }
        }
        $data['attributes'] = $attributesData;

        // Traitement des images
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('product-variants', 'public');
                $imagePaths[] = $path;
            }
            $data['images'] = $imagePaths;
        }

        $data['product_id'] = $product->id;
        $data['is_active'] = $request->has('is_active');

        $variant = ProductVariant::create($data);

        return redirect()->route('admin.products.variants.index', $product)
            ->with('success', 'Variante créée avec succès');
    }

    /**
     * Display the specified variant.
     */
    public function show(Product $product, ProductVariant $variant)
    {
        $variant->load('product');
        return view('admin.product-variants.show', compact('product', 'variant'));
    }

    /**
     * Show the form for editing the variant.
     */
    public function edit(Product $product, ProductVariant $variant)
    {
        $variantAttributes = $product->getVariantAttributes();
        $attributes = Attribute::where('is_active', true)->get();

        return view('admin.product-variants.edit', compact('product', 'variant', 'variantAttributes', 'attributes'));
    }

    /**
     * Update the specified variant.
     */
    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        $request->validate([
            'variant_name' => 'required|string|max:255',
            'sku' => 'required|string|unique:product_variants,sku,' . $variant->id,
            'price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'attributes' => 'required|array|min:1',
            'attributes.*.attribute_id' => 'required|exists:attributes,id',
            'attributes.*.value' => 'required|string',
            'images' => 'nullable|array|max:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();

        // Utiliser le prix du produit parent si non spécifié
        if (empty($data['price'])) {
            $data['price'] = $product->price;
        }

        // Traitement des attributs
        $attributesData = [];
        foreach ($request->input('attributes') as $attributeData) {
            if (!empty($attributeData['attribute_id']) && !empty($attributeData['value'])) {
                $attributesData[$attributeData['attribute_id']] = $attributeData['value'];
            }
        }
        $data['attributes'] = $attributesData;

        // Traitement des nouvelles images
        if ($request->hasFile('images')) {
            // Supprimer les anciennes images
            if ($variant->images) {
                foreach ($variant->images as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('product-variants', 'public');
                $imagePaths[] = $path;
            }
            $data['images'] = $imagePaths;
        }

        $data['is_active'] = $request->has('is_active');

        $variant->update($data);

        return redirect()->route('admin.products.variants.index', $product)
            ->with('success', 'Variante mise à jour avec succès');
    }

    /**
     * Remove the specified variant.
     */
    public function destroy(Product $product, ProductVariant $variant)
    {
        // Supprimer les images
        if ($variant->images) {
            foreach ($variant->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $variant->delete();

        return redirect()->route('admin.products.variants.index', $product)
            ->with('success', 'Variante supprimée avec succès');
    }

    /**
     * Toggle variant status.
     */
    public function toggleStatus(Product $product, ProductVariant $variant)
    {
        $variant->update(['is_active' => !$variant->is_active]);

        return redirect()->back()
            ->with('success', 'Statut de la variante mis à jour');
    }

    /**
     * Generate variants automatically based on product type attributes.
     */
    public function generateVariants(Request $request, Product $product)
    {
        $request->validate([
            'variant_attributes' => 'required|array|min:1',
            'variant_attributes.*' => 'required|exists:attributes,id',
        ]);

        $variantAttributes = Attribute::whereIn('id', $request->input('variant_attributes'))
            ->where('is_active', true)
            ->get();

        if ($variantAttributes->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Aucun attribut de variante valide sélectionné');
        }

        // Générer toutes les combinaisons possibles
        $combinations = $this->generateAttributeCombinations($variantAttributes);

        $generatedCount = 0;
        foreach ($combinations as $combination) {
            $variantName = implode(' - ', array_values($combination));
            $sku = $product->sku . '-' . strtoupper(substr(md5($variantName), 0, 6));

            // Vérifier si la variante existe déjà
            if (!ProductVariant::where('product_id', $product->id)
                ->where('sku', $sku)
                ->exists()) {

                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $variantName,
                    'sku' => $sku,
                    'price' => $product->price,
                    'stock_quantity' => 0,
                    'attributes' => $combination,
                    'is_active' => true,
                ]);

                $generatedCount++;
            }
        }

        return redirect()->route('admin.products.variants.index', $product)
            ->with('success', "{$generatedCount} variantes générées automatiquement");
    }

    /**
     * Generate all possible combinations of attribute values.
     */
    private function generateAttributeCombinations($attributes)
    {
        $combinations = [[]];

        foreach ($attributes as $attribute) {
            $newCombinations = [];
            $options = $attribute->options ?? [$attribute->name];

            foreach ($combinations as $combination) {
                foreach ($options as $option) {
                    $newCombination = $combination;
                    $newCombination[$attribute->id] = $option;
                    $newCombinations[] = $newCombination;
                }
            }

            $combinations = $newCombinations;
        }

        return $combinations;
    }
}
