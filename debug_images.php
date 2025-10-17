<?php

// Debug des images retournées par l'API
$baseUrl = 'http://192.168.100.73:8000/api/v1';

echo "=== DEBUG DES IMAGES ===\n\n";

$response = file_get_contents($baseUrl . '/products');
if ($response !== false) {
    $data = json_decode($response, true);
    if ($data && isset($data['data'])) {
        echo "Produits récupérés: " . count($data['data']) . "\n\n";

        foreach ($data['data'] as $index => $product) {
            echo "Produit " . ($index + 1) . ": " . $product['name'] . "\n";
            echo "  - ID: " . $product['id'] . "\n";
            echo "  - Stock: " . $product['stock_quantity'] . "\n";

            // Vérifier main_image
            if (!empty($product['main_image'])) {
                echo "  - Main Image: " . $product['main_image'] . "\n";
            } else {
                echo "  - Main Image: NULL\n";
            }

            // Vérifier images array
            if (!empty($product['images']) && is_array($product['images'])) {
                echo "  - Images array: " . count($product['images']) . " images\n";
                foreach ($product['images'] as $imgIndex => $img) {
                    echo "    [$imgIndex]: $img\n";
                }
            } else {
                echo "  - Images array: NULL ou vide\n";
            }

            // Vérifier product_images
            if (!empty($product['product_images']) && is_array($product['product_images'])) {
                echo "  - Product Images: " . count($product['product_images']) . " images\n";
                foreach ($product['product_images'] as $imgIndex => $img) {
                    if (isset($img['url'])) {
                        echo "    [$imgIndex]: " . $img['url'] . "\n";
                    }
                }
            } else {
                echo "  - Product Images: NULL ou vide\n";
            }

            echo "\n";

            // Limiter à 3 produits pour le debug
            if ($index >= 2) break;
        }
    }
}

echo "=== FIN DU DEBUG ===\n";
