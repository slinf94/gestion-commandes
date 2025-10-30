<?php

namespace App\Helpers;

use App\Models\User;

class AdminMenuHelper
{
    /**
     * Détermine si un utilisateur peut voir un élément du menu
     *
     * @param User|null $user L'utilisateur à vérifier
     * @param string ...$requiredRoles Les rôles requis (au moins un)
     * @return bool
     */
    public static function canSee(?User $user, ...$requiredRoles): bool
    {
        if (!$user) {
            return false;
        }

        // Vérifier via le nouveau système RBAC
        foreach ($requiredRoles as $role) {
            if ($user->hasRole($role)) {
                return true;
            }
        }

        // Fallback sur l'ancien système
        if (isset($user->role) && in_array($user->role, $requiredRoles, true)) {
            return true;
        }

        return false;
    }

    /**
     * Récupérer tous les rôles d'un utilisateur (nouveau et ancien système)
     *
     * @param User|null $user
     * @return array
     */
    public static function getAllRoles(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $roles = [];

        // Rôles RBAC (nouveau système)
        foreach ($user->roles as $role) {
            $roles[] = $role->slug;
        }

        // Rôle legacy (ancien système - fallback)
        if (isset($user->role) && !in_array($user->role, $roles)) {
            $roles[] = $user->role;
        }

        return array_unique($roles);
    }

    /**
     * Vérifier si l'utilisateur a au moins un des rôles requis
     *
     * @param User|null $user
     * @param string ...$roles
     * @return bool
     */
    public static function hasAnyRole(?User $user, ...$roles): bool
    {
        if (!$user) {
            return false;
        }

        // Vérifier via le nouveau système
        if ($user->hasAnyRole($roles)) {
            return true;
        }

        // Vérifier via l'ancien système
        if (isset($user->role) && in_array($user->role, $roles, true)) {
            return true;
        }

        return false;
    }

    /**
     * Vérifier si l'utilisateur a toutes les permissions données
     *
     * @param User|null $user
     * @param string ...$permissions
     * @return bool
     */
    public static function hasAllPermissions(?User $user, ...$permissions): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasAllPermissions($permissions);
    }

    /**
     * Vérifier si l'utilisateur a au moins une des permissions données
     *
     * @param User|null $user
     * @param string ...$permissions
     * @return bool
     */
    public static function hasAnyPermission(?User $user, ...$permissions): bool
    {
        if (!$user) {
            return false;
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtenir une description lisible des rôles d'un utilisateur
     *
     * @param User|null $user
     * @return string
     */
    public static function getRolesDescription(?User $user): string
    {
        if (!$user) {
            return 'Aucun';
        }

        $roles = self::getAllRoles($user);

        if (empty($roles)) {
            return 'Aucun rôle';
        }

        $translations = [
            'super-admin' => 'Super Administrateur',
            'admin' => 'Administrateur',
            'gestionnaire' => 'Gestionnaire',
            'vendeur' => 'Vendeur',
            'client' => 'Client',
        ];

        $translatedRoles = array_map(function($role) use ($translations) {
            return $translations[$role] ?? $role;
        }, $roles);

        return implode(', ', $translatedRoles);
    }
}

