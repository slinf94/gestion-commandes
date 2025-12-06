<?php
// Script pour nettoyer définitivement ProductController.php

$filePath = __DIR__ . '/app/Http/Controllers/Admin/ProductController.php';

echo "Lecture du fichier...\n";
$lines = file($filePath, FILE_IGNORE_NEW_LINES);
$totalLines = count($lines);

echo "Total lignes actuelles : $totalLines\n";

// Trouver la ligne exacte de fermeture
$closingLine = null;
for ($i = 810; $i < 820; $i++) {
    if (isset($lines[$i]) && trim($lines[$i]) === '}') {
        $closingLine = $i;
        echo "Fermeture de classe trouvée à la ligne " . ($i + 1) . "\n";
        break;
    }
}

if ($closingLine === null) {
    die("❌ Impossible de trouver la fermeture de classe!\n");
}

// Garder seulement jusqu'à la ligne de fermeture (incluse)
$cleanLines = array_slice($lines, 0, $closingLine + 1);

// Écrire le fichier nettoyé
file_put_contents($filePath, implode("\n", $cleanLines) . "\n");

echo "✅ Fichier nettoyé avec succès !\n";
echo "Nouvelles lignes : " . ($closingLine + 1) . "\n";
