<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

echo "🖼️  Ajout d'images placeholder aux produits...\n\n";

try {
    // Récupérer tous les produits
    $products = Product::all();
    $added = 0;
    $skipped = 0;

    foreach ($products as $product) {
        // Vérifier si le produit a déjà des images
        $hasImages = ProductImage::where('product_id', $product->id)->exists();
        
        if ($hasImages) {
            echo "   ⏭️  Produit #{$product->id} ({$product->name}) - a déjà des images\n";
            $skipped++;
            continue;
        }

        // Créer une image placeholder
        $imagePath = 'products/placeholder-' . $product->id . '.png';
        
        // Créer une entrée dans la table product_images
        ProductImage::create([
            'product_id' => $product->id,
            'url' => $imagePath,
            'type' => 'principale',
            'order' => 1,
        ]);

        echo "   ✅ Produit #{$product->id} ({$product->name}) - image placeholder ajoutée\n";
        $added++;
    }

    echo "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 RÉSUMÉ :\n";
    echo "   ✅ Images ajoutées : $added\n";
    echo "   ⏭️  Produits ignorés (ont déjà des images) : $skipped\n";
    echo "   📦 Total produits : " . $products->count() . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n";
    echo "💡 PROCHAINE ÉTAPE :\n";
    echo "   1. Allez sur http://192.168.100.73:8000/admin/products\n";
    echo "   2. Cliquez sur 'Modifier' pour chaque produit\n";
    echo "   3. Uploadez une vraie image pour remplacer le placeholder\n";
    echo "\n";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo "\nTrace : " . $e->getTraceAsString() . "\n";
}

