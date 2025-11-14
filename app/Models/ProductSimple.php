<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSimple extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name', 'description', 'price', 'cost_price', 'wholesale_price', 'retail_price',
        'min_wholesale_quantity', 'stock_quantity', 'min_stock_alert', 'category_id',
        'product_type_id', 'sku', 'barcode', 'brand', 'range', 'format', 'type_accessory',
        'compatibility', 'images', 'status', 'is_featured', 'meta_title', 'meta_description', 'tags'
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

    // Relations de base uniquement
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(\App\Models\OrderItem::class, 'product_id');
    }

    // Accessor simple pour les tags
    public function getTagsAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }

    // Accessor simple pour le statut
    public function getFormattedStatusAttribute()
    {
        return ucfirst($this->status ?? 'inactive');
    }

    // Relations manquantes pour compatibilité
    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_id');
    }

    /**
     * Check if product has variants.
     */
    public function hasVariants()
    {
        return $this->variants()->count() > 0;
    }

    /**
     * Get available variants for this product.
     */
    public function getAvailableVariants()
    {
        return $this->variants()->where('is_active', true)->get();
    }

    // Méthode pour obtenir les attributs de variantes
    public function getVariantAttributes()
    {
        if (!$this->productType) {
            return collect();
        }

        // Récupérer les attributs du type de produit qui sont marqués comme variants
        return $this->productType->attributes()
            ->wherePivot('is_variant', true)
            ->orderByPivot('sort_order')
            ->get();
    }

    // Accessor pour les images
    public function getMainImageAttribute()
    {
        if ($this->images && is_array($this->images) && count($this->images) > 0) {
            return $this->images[0];
        }
        return 'placeholder.jpg';
    }

    // Accessor pour toutes les images
    public function getAllImagesAttribute()
    {
        if ($this->images && is_array($this->images)) {
            return $this->images;
        }
        return [];
    }
}
