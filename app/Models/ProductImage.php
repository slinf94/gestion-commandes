<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'url',
        'type',
        'order',
        'alt_text',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // Relation avec le produit
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scope pour récupérer l'image principale
    public function scopePrincipale($query)
    {
        return $query->where('type', 'principale');
    }

    // Scope pour récupérer les images secondaires
    public function scopeSecondaire($query)
    {
        return $query->where('type', 'secondaire');
    }

    // Scope pour récupérer les images de galerie
    public function scopeGalerie($query)
    {
        return $query->where('type', 'galerie');
    }
}
