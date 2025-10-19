<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Attribute extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'options',
        'is_required',
        'is_filterable',
        'is_variant',
        'is_active',
        'sort_order',
        'validation_rules',
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'is_variant' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the product attribute values for this attribute (via product_type_attributes).
     */
    public function productAttributeValues(): HasManyThrough
    {
        return $this->hasManyThrough(
            ProductAttributeValue::class,
            ProductTypeAttribute::class,
            'attribute_id', // Foreign key on product_type_attributes table
            'product_type_attribute_id', // Foreign key on product_attribute_values table
            'id', // Local key on attributes table
            'id' // Local key on product_type_attributes table
        );
    }

    /**
     * Get the product type attributes for this attribute.
     */
    public function productTypeAttributes(): HasMany
    {
        return $this->hasMany(ProductTypeAttribute::class);
    }

    /**
     * Scope for active attributes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for filterable attributes.
     */
    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    /**
     * Scope for variant attributes.
     */
    public function scopeVariant($query)
    {
        return $query->where('is_variant', true);
    }
}
