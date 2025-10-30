<?php

/**
 * Helper functions globales pour l'application
 */

if (!function_exists('admin_can_see')) {
    /**
     * Vérifier si l'utilisateur connecté peut voir un élément
     *
     * @param string ...$requiredRoles
     * @return bool
     */
    function admin_can_see(...$requiredRoles): bool
    {
        $user = auth()->user();
        return \App\Helpers\AdminMenuHelper::canSee($user, ...$requiredRoles);
    }
}

if (!function_exists('admin_has_role')) {
    /**
     * Vérifier si l'utilisateur connecté a au moins un des rôles
     *
     * @param string ...$roles
     * @return bool
     */
    function admin_has_role(...$roles): bool
    {
        $user = auth()->user();
        return \App\Helpers\AdminMenuHelper::hasAnyRole($user, ...$roles);
    }
}

if (!function_exists('admin_has_permission')) {
    /**
     * Vérifier si l'utilisateur connecté a une permission
     *
     * @param string $permission
     * @return bool
     */
    function admin_has_permission(string $permission): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermission($permission);
    }
}

