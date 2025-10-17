<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

    /**
     * Obtenir la traduction française du statut
     */
    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::CONFIRMED => 'Confirmée',
            self::PROCESSING => 'En cours de traitement',
            self::SHIPPED => 'Expédiée',
            self::DELIVERED => 'Livrée',
            self::CANCELLED => 'Annulée',
            self::COMPLETED => 'Terminée',
        };
    }

    /**
     * Obtenir la classe CSS Bootstrap
     */
    public function getBootstrapClass(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'info',
            self::PROCESSING => 'info',
            self::SHIPPED => 'primary',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
            self::COMPLETED => 'success',
        };
    }

    /**
     * Obtenir la couleur hexadécimale
     */
    public function getColor(): string
    {
        return match($this) {
            self::PENDING => '#FF9800',
            self::CONFIRMED => '#2196F3',
            self::PROCESSING => '#2196F3',
            self::SHIPPED => '#9C27B0',
            self::DELIVERED => '#4CAF50',
            self::CANCELLED => '#F44336',
            self::COMPLETED => '#4CAF50',
        };
    }

    /**
     * Obtenir l'icône
     */
    public function getIcon(): string
    {
        return match($this) {
            self::PENDING => '⏳',
            self::CONFIRMED => '✅',
            self::PROCESSING => '⚙️',
            self::SHIPPED => '🚚',
            self::DELIVERED => '🎉',
            self::CANCELLED => '❌',
            self::COMPLETED => '✅',
        };
    }

    /**
     * Obtenir la description
     */
    public function getDescription(): string
    {
        return match($this) {
            self::PENDING => 'Commande en attente de confirmation',
            self::CONFIRMED => 'Commande confirmée et en cours de préparation',
            self::PROCESSING => 'Commande en cours de préparation',
            self::SHIPPED => 'Commande expédiée et en cours de livraison',
            self::DELIVERED => 'Commande livrée avec succès',
            self::CANCELLED => 'Commande annulée',
            self::COMPLETED => 'Commande terminée',
        };
    }

    /**
     * Obtenir les statuts suivants possibles
     */
    public function getNextStatuses(): array
    {
        return match($this) {
            self::PENDING => [self::CONFIRMED, self::CANCELLED],
            self::CONFIRMED => [self::PROCESSING, self::CANCELLED],
            self::PROCESSING => [self::SHIPPED, self::CANCELLED],
            self::SHIPPED => [self::DELIVERED, self::CANCELLED],
            self::DELIVERED => [self::COMPLETED],
            self::CANCELLED => [],
            self::COMPLETED => [],
        };
    }

    /**
     * Vérifier si un statut peut être changé vers un autre
     */
    public function canTransitionTo(OrderStatus $status): bool
    {
        return in_array($status, $this->getNextStatuses());
    }

    /**
     * Obtenir tous les statuts
     */
    public static function all(): array
    {
        return [
            self::PENDING,
            self::CONFIRMED,
            self::PROCESSING,
            self::SHIPPED,
            self::DELIVERED,
            self::CANCELLED,
            self::COMPLETED,
        ];
    }

    /**
     * Obtenir les statuts actifs (non terminés)
     */
    public static function active(): array
    {
        return [
            self::PENDING,
            self::CONFIRMED,
            self::PROCESSING,
            self::SHIPPED,
        ];
    }

    /**
     * Obtenir les statuts terminés
     */
    public static function completed(): array
    {
        return [
            self::DELIVERED,
            self::COMPLETED,
        ];
    }

    /**
     * Obtenir les statuts annulés
     */
    public static function cancelled(): array
    {
        return [
            self::CANCELLED,
        ];
    }

    /**
     * Vérifier si le statut est actif
     */
    public function isActive(): bool
    {
        return in_array($this, self::active());
    }

    /**
     * Vérifier si le statut est terminé
     */
    public function isCompleted(): bool
    {
        return in_array($this, self::completed());
    }

    /**
     * Vérifier si le statut est annulé
     */
    public function isCancelled(): bool
    {
        return in_array($this, self::cancelled());
    }

    /**
     * Obtenir les options pour un select
     */
    public static function getSelectOptions(): array
    {
        $options = [];
        foreach (self::all() as $status) {
            $options[$status->value] = $status->getLabel();
        }
        return $options;
    }
}
