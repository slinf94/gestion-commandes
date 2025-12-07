<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    /**
     * Lister tous les rôles avec leurs permissions
     */
    public function roles()
    {
        $roles = Role::with('permissions')->where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
     * Afficher un rôle avec ses permissions
     */
    public function showRole($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $role
        ]);
    }

    /**
     * Lister toutes les permissions
     */
    public function permissions()
    {
        $permissions = Permission::all()->groupBy('module');

        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }

    /**
     * Attacher/détacher des permissions à un rôle
     */
    public function updateRolePermissions(Request $request, $roleId)
    {
        $user = Auth::user();

        // Seuls les admins peuvent modifier les permissions des rôles
        if (!in_array($user->role, ['admin', 'super-admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas la permission de modifier les rôles.'
            ], 403);
        }

        $role = Role::findOrFail($roleId);

        // Empêcher la modification du rôle super-admin
        if ($role->slug === 'super-admin' && $user->role !== 'super-admin') {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas modifier le rôle Super Administrateur.'
            ], 403);
        }

        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->permissions()->sync($validated['permissions']);

        return response()->json([
            'success' => true,
            'message' => 'Permissions du rôle mises à jour avec succès',
            'data' => $role->load('permissions')
        ]);
    }

    /**
     * Attacher/détacher des rôles à un utilisateur
     */
    public function updateUserRoles(Request $request, $userId)
    {
        $currentUser = Auth::user();

        // Seuls les admins peuvent modifier les rôles des utilisateurs
        if (!in_array($currentUser->role, ['admin', 'super-admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas la permission de modifier les rôles des utilisateurs.'
            ], 403);
        }

        $user = User::findOrFail($userId);

        // Empêcher un admin de modifier un super-admin
        if ($user->role === 'super-admin' && $currentUser->role !== 'super-admin') {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas modifier un Super Administrateur.'
            ], 403);
        }

        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user->roles()->sync($validated['roles']);

        return response()->json([
            'success' => true,
            'message' => 'Rôles de l\'utilisateur mis à jour avec succès',
            'data' => $user->load('roles.permissions')
        ]);
    }

    /**
     * Obtenir les permissions d'un utilisateur
     */
    public function getUserPermissions($userId)
    {
        $user = User::with('roles.permissions')->findOrFail($userId);

        // Collecter toutes les permissions uniques de tous les rôles
        $permissions = $user->roles->flatMap(function ($role) {
            return $role->permissions;
        })->unique('id')->values();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->only(['id', 'nom', 'prenom', 'email', 'role']),
                'roles' => $user->roles,
                'permissions' => $permissions
            ]
        ]);
    }

    /**
     * Ajouter une permission spécifique à un utilisateur via un rôle
     */
    public function addPermissionToUser(Request $request, $userId)
    {
        $currentUser = Auth::user();

        if (!in_array($currentUser->role, ['admin', 'super-admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas la permission d\'effectuer cette action.'
            ], 403);
        }

        $validated = $request->validate([
            'permission_slug' => 'required|exists:permissions,slug'
        ]);

        $user = User::findOrFail($userId);
        $permission = Permission::where('slug', $validated['permission_slug'])->first();

        // Trouver un rôle qui a cette permission ou créer un rôle personnalisé
        $roleWithPermission = Role::whereHas('permissions', function ($query) use ($permission) {
            $query->where('permissions.id', $permission->id);
        })->first();

        if ($roleWithPermission && !$user->hasRole($roleWithPermission->slug)) {
            $user->attachRole($roleWithPermission);
        }

        return response()->json([
            'success' => true,
            'message' => 'Permission ajoutée à l\'utilisateur',
            'data' => $user->load('roles.permissions')
        ]);
    }

    /**
     * Retirer une permission spécifique d'un utilisateur
     */
    public function removePermissionFromUser(Request $request, $userId)
    {
        $currentUser = Auth::user();

        if (!in_array($currentUser->role, ['admin', 'super-admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas la permission d\'effectuer cette action.'
            ], 403);
        }

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $user = User::findOrFail($userId);
        $user->roles()->detach($validated['role_id']);

        return response()->json([
            'success' => true,
            'message' => 'Rôle retiré de l\'utilisateur',
            'data' => $user->load('roles.permissions')
        ]);
    }

    /**
     * Créer un nouveau rôle
     */
    public function createRole(Request $request)
    {
        $currentUser = Auth::user();

        if ($currentUser->role !== 'super-admin') {
            return response()->json([
                'success' => false,
                'message' => 'Seul le Super Administrateur peut créer des rôles.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->attach($validated['permissions']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Rôle créé avec succès',
            'data' => $role->load('permissions')
        ], 201);
    }

    /**
     * Supprimer un rôle
     */
    public function deleteRole($id)
    {
        $currentUser = Auth::user();

        if ($currentUser->role !== 'super-admin') {
            return response()->json([
                'success' => false,
                'message' => 'Seul le Super Administrateur peut supprimer des rôles.'
            ], 403);
        }

        $role = Role::findOrFail($id);

        // Empêcher la suppression des rôles système
        if (in_array($role->slug, ['super-admin', 'admin', 'gestionnaire', 'vendeur', 'client'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer un rôle système.'
            ], 403);
        }

        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rôle supprimé avec succès'
        ]);
    }
}
