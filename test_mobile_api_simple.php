<?php
// Test API Mobile
$baseUrl = "http://192.168.100.73:8000/api/v1";

// Headers pour l'application mobile
$headers = [
    "Accept: application/json",
    "Content-Type: application/json",
    "X-Mobile-App: true"
];

// Test des produits
echo "Test des produits:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . "/products");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Code HTTP: $httpCode\n";
if ($httpCode == 200) {
    $data = json_decode($response, true);
    if ($data["success"]) {
        echo "✅ API des produits fonctionne\n";
        echo "✅ Nombre de produits: " . count($data["data"]) . "\n";
        if (!empty($data["data"])) {
            $firstProduct = $data["data"][0];
            echo "✅ Premier produit: " . $firstProduct["name"] . "\n";
            echo "✅ Image principale: " . ($firstProduct["main_image"] ?? "Aucune") . "\n";
        }
    } else {
        echo "❌ Erreur API: " . ($data["message"] ?? "Inconnue") . "\n";
    }
} else {
    echo "❌ Erreur HTTP: $httpCode\n";
}

echo "\nTest terminé.\n";
?>