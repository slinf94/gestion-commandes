<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    // Récupérer l'image principale
    public function getMainImageAttribute()
    {
        if ($this->relationLoaded('productImages') && $this->productImages->isNotEmpty()) {
            // Chercher l'image principale
            $principale = $this->productImages->where('type', 'principale')->first();
            if ($principale) return $this->formatImageUrl($principale->url);

            // Sinon prendre la première image
            return $this->formatImageUrl($this->productImages->first()->url);
        }

        // Fallback sur l'ancien système d'images
        if ($this->images && is_array($this->images) && !empty($this->images)) {
            return $this->formatImageUrl($this->images[0]);
        }

        return null;
    }

    // Récupérer toutes les images
    public function getAllImagesAttribute()
    {
        if ($this->relationLoaded('productImages') && $this->productImages->isNotEmpty()) {
            return $this->productImages->map(function($image) {
                return $this->formatImageUrl($image->url);
            })->toArray();
        }

        // Fallback sur l'ancien système d'images
        if ($this->images && is_array($this->images)) {
            return array_map([$this, 'formatImageUrl'], $this->images);
        }

        return [];
    }

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
}
