<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'variant_name', 'sku', 'price', 
        'stock_quantity', 'images', 'attributes', 'is_active'
    ];

    protected $casts = [
        'images' => 'array',
        'attributes' => 'array',
        'is_active' => 'boolean'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
