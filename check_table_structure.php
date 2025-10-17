<?php

require_once 'vendor/autoload.php';

// Configuration de base de données simple
$host = '127.0.0.1';
$dbname = 'gestion-commandedb';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== STRUCTURE DE LA TABLE order_items ===\n\n";

    $stmt = $pdo->query("DESCRIBE order_items");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($columns as $column) {
        echo "Colonne: " . $column['Field'] . "\n";
        echo "  Type: " . $column['Type'] . "\n";
        echo "  Null: " . $column['Null'] . "\n";
        echo "  Key: " . $column['Key'] . "\n";
        echo "  Default: " . ($column['Default'] ?? 'NULL') . "\n";
        echo "  Extra: " . $column['Extra'] . "\n\n";
    }

    echo "=== FIN ===\n";

} catch (PDOException $e) {
    echo "Erreur de connexion: " . $e->getMessage() . "\n";
}
