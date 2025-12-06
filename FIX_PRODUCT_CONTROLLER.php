<?php
// Script pour nettoyer le ProductController.php

$filePath = __DIR__ . '/app/Http/Controllers/Admin/ProductController.php';

// Lire le fichier
$content = file_get_contents($filePath);

// Trouver la position de "return back()->with('success', 'Palier de prix supprimé avec succès !');"
$searchString = "return back()->with('success', 'Palier de prix supprimé avec succès !');";
$pos = strpos($content, $searchString);

if ($pos === false) {
    die("String not found!\n");
}

// Trouver la position de la fin de la méthode destroyQuantityPrice
$closeMethodPos = strpos($content, "}", $pos);

if ($closeMethodPos === false) {
    die("Close method not found!\n");
}

// Garder tout jusqu'à la fermeture de destroyQuantityPrice + fermeture de classe
$newContent = substr($content, 0, $closeMethodPos + 1) . "\n}\n";

// Écrire le nouveau contenu
file_put_contents($filePath, $newContent);

echo "ProductController nettoyé avec succès !\n";
echo "Lignes conservées : " . substr_count($newContent, "\n") . "\n";
