<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    // use SoftDeletes; // Pas de soft delete pour les order_items

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'product_details',
        'product_name',
        'product_image',
        'product_sku',
        'product_stock',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'product_details' => 'array',
    ];

    // Relations
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Boot method to calculate total price
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_price = $item->quantity * $item->unit_price;
        });
    }

    /**
     * Déterminer si cet article est un téléphone
     */
    public function isPhone(): bool
    {
        $productDetails = $this->product_details;
        $productName = strtolower($this->product_name ?? '');
        
        // Vérifier par product_details (category_name)
        if (is_array($productDetails) && isset($productDetails['category_name'])) {
            $catName = strtolower($productDetails['category_name']);
            if (str_contains($catName, 'téléphone') || 
                str_contains($catName, 'telephone') ||
                str_contains($catName, 'phone') ||
                str_contains($catName, 'smartphone')) {
                return true;
            }
        }
        
        // Vérifier par nom de produit
        $phoneKeywords = [
            'téléphone', 'telephone', 'phone', 'smartphone',
            'iphone', 'samsung', 'galaxy', 'xiaomi', 'redmi',
            'oppo', 'vivo', 'huawei', 'honor', 'oneplus', 'realme',
            'nokia', 'motorola', 'lg', 'sony', 'técno', 'infinix',
            'itel', 'wiko', 'umi', 'doogee'
        ];
        
        foreach ($phoneKeywords as $keyword) {
            if (str_contains($productName, $keyword)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Déterminer si cet article est un accessoire
     */
    public function isAccessory(): bool
    {
        // Ne pas être un téléphone d'abord
        if ($this->isPhone()) {
            return false;
        }
        
        $productDetails = $this->product_details;
        $productName = strtolower($this->product_name ?? '');
        
        // Vérifier par product_details (category_name)
        if (is_array($productDetails) && isset($productDetails['category_name'])) {
            $catName = strtolower($productDetails['category_name']);
            if (str_contains($catName, 'accessoire') || 
                str_contains($catName, 'accessory') ||
                str_contains($catName, 'étui') ||
                str_contains($catName, 'coque') ||
                str_contains($catName, 'chargeur') ||
                str_contains($catName, 'écouteur') ||
                str_contains($catName, 'casque') ||
                str_contains($catName, 'protection')) {
                return true;
            }
        }
        
        // Vérifier par nom de produit
        $accessoryKeywords = [
            'accessoire', 'accessory', 'étui', 'coque', 'chargeur', 'charge',
            'écouteur', 'ecouteur', 'casque', 'protection', 'film', 'housse',
            'pochette', 'support', 'câble', 'cable', 'adaptateur', 'batterie',
            'powerbank', 'power bank', 'carte mémoire', 'sd card', 'carte sim',
            'stylus', 'stylets', 'autocollant', 'sticker', 'bracelet', 'montre'
        ];
        
        foreach ($accessoryKeywords as $keyword) {
            if (str_contains($productName, $keyword)) {
                return true;
            }
        }
        
        return false;
    }
}
