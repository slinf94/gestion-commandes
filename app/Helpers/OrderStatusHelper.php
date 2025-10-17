<?php

namespace App\Helpers;

class OrderStatusHelper
{
    /**
     * Mappage des statuts de commande en français
     */
    public static function getStatusMap(): array
    {
        return config('order_status.statuses', []);
    }

    /**
     * Obtenir les informations d'un statut
     */
    public static function getStatusInfo(string $status): array
    {
        $statusMap = self::getStatusMap();
        return $statusMap[$status] ?? [
            'text' => ucfirst($status),
            'class' => 'secondary',
            'icon' => '📦',
            'color' => '#757575'
        ];
    }

    /**
     * Obtenir le texte d'un statut
     */
    public static function getStatusText(string $status): string
    {
        return self::getStatusInfo($status)['text'];
    }

    /**
     * Obtenir la classe CSS d'un statut
     */
    public static function getStatusClass(string $status): string
    {
        return self::getStatusInfo($status)['class'];
    }

    /**
     * Obtenir l'icône d'un statut
     */
    public static function getStatusIcon(string $status): string
    {
        return self::getStatusInfo($status)['icon'];
    }

    /**
     * Obtenir la couleur d'un statut
     */
    public static function getStatusColor(string $status): string
    {
        return self::getStatusInfo($status)['color'];
    }

    /**
     * Vérifier si un statut est valide
     */
    public static function isValidStatus(string $status): bool
    {
        return array_key_exists($status, self::getStatusMap());
    }

    /**
     * Obtenir tous les statuts valides
     */
    public static function getValidStatuses(): array
    {
        return array_keys(self::getStatusMap());
    }

    /**
     * Obtenir les statuts pour les formulaires de validation
     */
    public static function getValidationRule(): string
    {
        return 'in:' . implode(',', self::getValidStatuses());
    }

    /**
     * Obtenir les statuts d'un groupe
     */
    public static function getStatusesByGroup(string $group): array
    {
        return config("order_status.groups.{$group}", []);
    }

    /**
     * Vérifier si une transition de statut est valide
     */
    public static function canTransition(string $from, string $to): bool
    {
        $workflow = config('order_status.workflow', []);
        return in_array($to, $workflow[$from] ?? []);
    }

    /**
     * Obtenir les statuts suivants possibles
     */
    public static function getNextStatuses(string $currentStatus): array
    {
        $workflow = config('order_status.workflow', []);
        return $workflow[$currentStatus] ?? [];
    }

    /**
     * Obtenir la description d'un statut
     */
    public static function getStatusDescription(string $status): string
    {
        $statusInfo = self::getStatusInfo($status);
        return $statusInfo['description'] ?? '';
    }
}
