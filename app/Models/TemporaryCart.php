<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemporaryCart extends Model
{
    protected $table = 'temporary_cart';

    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
        'quantity',
        'selected_attributes',
        'expires_at',
    ];

    protected $casts = [
        'selected_attributes' => 'array',
        'expires_at' => 'datetime',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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
