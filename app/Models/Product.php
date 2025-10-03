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
        'is_featured' => 'boolean'
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
        return $this->productImages()->principale()->first()?->url;
    }

    // Récupérer toutes les images
    public function getAllImagesAttribute()
    {
        return $this->productImages()->get()->pluck('url')->toArray();
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
