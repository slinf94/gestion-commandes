<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use App\Traits\LogsActivity;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'description', 'price', 'cost_price', 'wholesale_price', 'retail_price',
        'min_wholesale_quantity', 'stock_quantity', 'min_stock_alert', 'category_id',
        'product_type_id', 'sku', 'barcode', 'images', 'status', 'is_featured',
        'meta_title', 'meta_description', 'tags'
    ];

    protected $casts = [
        'images' => 'array',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'retail_price' => 'decimal:2'
    ];

    /**
     * Attributes to log when the model changes
     */
    protected $logAttributes = [
        'name',
        'description',
        'price',
        'cost_price',
        'wholesale_price',
        'retail_price',
        'stock_quantity',
        'min_stock_alert',
        'category_id',
        'sku',
        'barcode',
        'status',
        'is_featured'
    ];

    /**
     * Attributes to ignore when logging
     */
    protected $logIgnoredAttributes = [
        'images', // Les images sont gérées séparément
        'meta_title',
        'meta_description',
        'tags'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relation avec les images
    public function productImages(): HasMany
    {
        // Ne pas utiliser orderBy('order') directement car la colonne peut ne pas exister
        // L'ordre sera géré dans les requêtes si nécessaire
        return $this->hasMany(ProductImage::class);
    }

    // Récupérer l'image principale (temporairement désactivé)
    /*
    public function getMainImageAttribute()
    {
        return 'placeholder.jpg';
    }
    */

    // Récupérer toutes les images (temporairement désactivé)
    /*
    public function getAllImagesAttribute()
    {
        return [];
    }
    */

    // Formater l'URL de l'image
    private function formatImageUrl($url)
    {
        if (empty($url)) return null;

        // Si l'URL commence par http, la retourner telle quelle
        if (str_starts_with($url, 'http')) {
            return $url;
        }

        // Sinon, ajouter l'URL de base
        return url('storage/' . ltrim($url, '/'));
    }

    // Méthode pour récupérer un attribut spécifique
    public function getProductAttributeValue(string $attributeSlug): ?string
    {
        $attributeValue = $this->attributeValues()
            ->whereHas('productTypeAttribute', function($query) use ($attributeSlug) {
                $query->where('attribute_slug', $attributeSlug);
            })
            ->first();

        return $attributeValue ? $attributeValue->attribute_value : null;
    }

    // Méthode pour récupérer tous les attributs formatés
    public function getFormattedAttributes(): array
    {
        return $this->attributeValues()
            ->with('productTypeAttribute')
            ->get()
            ->mapWithKeys(function($value) {
                return [$value->productTypeAttribute->attribute_slug => [
                    'name' => $value->productTypeAttribute->attribute_name,
                    'value' => $value->attribute_value,
                    'type' => $value->productTypeAttribute->attribute_type
                ]];
            })
            ->toArray();
    }

    // Accessor pour s'assurer que les tags sont toujours un tableau
    public function getTagsAttribute($value)
    {
        if (is_string($value)) {
            // Si c'est une chaîne, essayer de la décoder comme JSON
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($value) ? $value : [];
    }

    /**
     * Get the product's attribute values as a key-value array.
     */
    public function getAttributeValuesArray()
    {
        return $this->attributeValues->pluck('value', 'attribute.name')->toArray();
    }

    /**
     * Get a specific attribute value.
     */
    public function getAttributeValue($attributeName)
    {
        $attributeValue = $this->attributeValues()
            ->whereHas('attribute', function($query) use ($attributeName) {
                $query->where('name', $attributeName);
            })
            ->first();

        return $attributeValue ? $attributeValue->value : null;
    }

    /**
     * Set an attribute value.
     */
    public function setAttributeValue($attributeName, $value, $displayValue = null)
    {
        $attribute = Attribute::where('name', $attributeName)->first();

        if (!$attribute) {
            return false;
        }

        return $this->attributeValues()->updateOrCreate(
            ['attribute_id' => $attribute->id],
            [
                'value' => $value,
                'display_value' => $displayValue ?? $value,
            ]
        );
    }

    /**
     * Get available variants for this product.
     */
    public function getAvailableVariants()
    {
        return $this->variants()->where('is_active', true)->get();
    }

    /**
     * Get variant attributes (attributes that are marked as variants).
     */
    public function getVariantAttributes()
    {
        if (!$this->productType) {
            return collect();
        }

        return $this->productType->attributes()->wherePivot('is_variant', true)->get();
    }

    /**
     * Check if product has variants.
     */
    public function hasVariants()
    {
        return $this->variants()->count() > 0;
    }

    /**
     * Get the formatted price with currency.
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' €';
    }

    /**
     * Get the formatted cost price with currency.
     */
    public function getFormattedCostPriceAttribute()
    {
        return number_format($this->cost_price, 2) . ' €';
    }

    /**
     * Scope for products with specific attribute value.
     */
    public function scopeWithAttributeValue($query, $attributeName, $value)
    {
        return $query->whereHas('attributeValues', function($q) use ($attributeName, $value) {
            $q->whereHas('attribute', function($subQ) use ($attributeName) {
                $subQ->where('name', $attributeName);
            })->where('value', $value);
        });
    }

    /**
     * Scope for products in a specific category.
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for products of a specific type.
     */
    public function scopeOfType($query, $productTypeId)
    {
        return $query->where('product_type_id', $productTypeId);
    }

    /**
     * Scope for featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for products in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Scope for low stock products.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_alert');
    }
}
