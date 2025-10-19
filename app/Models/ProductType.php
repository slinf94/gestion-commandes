<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'default_attributes',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'default_attributes' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the category that owns the product type.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the products for this product type.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the attributes associated with this product type.
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_type_attributes')
            ->withPivot(['is_required', 'is_filterable', 'is_variant', 'sort_order', 'default_value'])
            ->withTimestamps();
    }

    /**
     * Get the product type attributes pivot table.
     */
    public function productTypeAttributes(): HasMany
    {
        return $this->hasMany(ProductTypeAttribute::class);
    }

    /**
     * Scope for active product types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for a specific category.
     */
    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Get the formatted attributes list.
     */
    public function getFormattedAttributesAttribute()
    {
        return $this->attributes->map(function($attribute) {
            return [
                'id' => $attribute->id,
                'name' => $attribute->name,
                'type' => $attribute->type,
                'is_required' => $attribute->pivot->is_required ?? false,
                'is_filterable' => $attribute->pivot->is_filterable ?? true,
                'is_variant' => $attribute->pivot->is_variant ?? false,
            ];
        });
    }
}
