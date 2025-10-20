<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValue extends Model
{
    protected $fillable = [
        'product_id',
        'product_type_attribute_id',
        'attribute_value',
        'numeric_value'
    ];

    protected $casts = [
        'attribute_value' => 'string',
        'numeric_value' => 'decimal:2'
    ];

    /**
     * Get the product that owns the attribute value.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attribute that owns the attribute value.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get the product type attribute that owns the attribute value.
     */
    public function productTypeAttribute(): BelongsTo
    {
        return $this->belongsTo(ProductTypeAttribute::class);
    }
}
