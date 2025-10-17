<?php

require_once 'vendor/autoload.php';

use App\Helpers\OrderStatusHelper;

echo "=== TEST DU WORKFLOW DE GESTION DES COMMANDES ===\n\n";

echo "1. Test de la configuration des statuts...\n";
$statuses = OrderStatusHelper::getValidStatuses();
echo "   Statuts valides: " . implode(', ', $statuses) . "\n";

foreach ($statuses as $status) {
    $info = OrderStatusHelper::getStatusInfo($status);
    echo "   - {$status}: {$info['text']} (classe: {$info['class']})\n";
}

echo "\n2. Test du workflow...\n";
$workflow = config('order_status.workflow');
foreach ($workflow as $from => $to) {
    echo "   De '{$from}': " . (empty($to) ? 'Aucune transition' : implode(', ', $to)) . "\n";
}

echo "\n3. Test des transitions spécifiques...\n";
$testTransitions = [
    ['pending', 'processing'],
    ['pending', 'confirmed'],
    ['processing', 'shipped'],
    ['shipped', 'delivered'],
    ['delivered', 'completed'],
    ['pending', 'cancelled'],
    ['processing', 'cancelled'],
];

foreach ($testTransitions as $transition) {
    [$from, $to] = $transition;
    $canTransition = OrderStatusHelper::canTransition($from, $to);
    echo "   {$from} → {$to}: " . ($canTransition ? '✅' : '❌') . "\n";
}

echo "\n4. Test de validation des statuts...\n";
$validationRule = OrderStatusHelper::getValidationRule();
echo "   Règle de validation: {$validationRule}\n";

echo "\n=== FIN DU TEST ===\n";
