<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemporaryCart extends Model
{
    protected $table = 'temporary_carts';

    protected $fillable = [
        'session_id',
        'product_id',
        'quantity',
        'unit_price',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'unit_price' => 'decimal:2',
    ];

    // Relations
    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ProductSimple::class, 'product_id');
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    // Helper methods
    public function isExpired()
    {
        return $this->expires_at <= now();
    }

    public function isValid()
    {
        return !$this->isExpired() && $this->quantity > 0;
    }

    public function getTotalPriceAttribute()
    {
        return $this->quantity * ($this->product ? $this->product->price : 0);
    }
}
