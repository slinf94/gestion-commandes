<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;
use App\Models\Order;

class NotificationHelper
{
    /**
     * Créer une notification pour tous les admins
     */
    public static function notifyAdmins($title, $message, $type = 'system', $data = [])
    {
        try {
            // Récupérer tous les admins (super-admin, admin, gestionnaire)
            $admins = User::whereIn('role', ['super-admin', 'admin', 'gestionnaire'])
                         ->where('status', 'active')
                         ->get();

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'is_read' => false,
                    'data' => $data,
                ]);
            }

            \Log::info('Notifications créées pour les admins', [
                'title' => $title,
                'type' => $type,
                'admins_count' => $admins->count()
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Erreur création notifications admins: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notifier les admins d'une nouvelle commande
     */
    public static function notifyNewOrder(Order $order)
    {
        $title = '🛒 Nouvelle commande';
        $message = "Nouvelle commande #{$order->order_number} de {$order->user->full_name} pour un montant de " . number_format($order->total_amount, 0, ',', ' ') . " FCFA";
        
        return self::notifyAdmins($title, $message, 'order', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'user_id' => $order->user_id,
            'total_amount' => $order->total_amount,
        ]);
    }

    /**
     * Notifier les admins d'une nouvelle inscription
     */
    public static function notifyNewUser(User $user)
    {
        $title = '👤 Nouvelle inscription';
        $message = "Nouvel utilisateur inscrit: {$user->full_name} ({$user->email})";
        
        return self::notifyAdmins($title, $message, 'client', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }

    /**
     * Notifier les admins d'un changement de statut de commande
     */
    public static function notifyOrderStatusChanged(Order $order, $oldStatus, $newStatus)
    {
        $title = '📦 Statut de commande modifié';
        $message = "Commande #{$order->order_number}: {$oldStatus} → {$newStatus}";
        
        return self::notifyAdmins($title, $message, 'order', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);
    }

    /**
     * Notifier les admins d'une action système
     */
    public static function notifySystem($title, $message, $data = [])
    {
        return self::notifyAdmins($title, $message, 'system', $data);
    }
}

