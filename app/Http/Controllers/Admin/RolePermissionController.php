<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\AdminMenuHelper;

class RolePermissionController extends Controller
{
    /**
     * Vérifier que l'utilisateur est super-admin
     */
    private function enforceSuperAdmin(): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        if (!AdminMenuHelper::canSee($user, 'super-admin')) {
            abort(403, 'Accès réservé au Super Administrateur');
        }
    }

    /**
     * Afficher la liste des utilisateurs avec leurs rôles et permissions
     */
    public function index(Request $request)
    {
        $this->enforceSuperAdmin();

        $query = User::with(['roles.permissions'])
            ->where(function($q) {
                $q->whereIn('role', ['super-admin', 'admin', 'gestionnaire', 'vendeur'])
                  ->orWhereHas('roles', function($rq) {
                      $rq->whereIn('slug', ['super-admin', 'admin', 'gestionnaire', 'vendeur']);
                  });
            });

        // Recherche
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Filtrage par rôle
        if ($request->has('role') && $request->role) {
            $query->where(function($q) use ($request) {
                $q->where('role', $request->role)
                  ->orWhereHas('roles', function($rq) use ($request) {
                      $rq->where('slug', $request->role);
                  });
            });
        }

        $users = $query->orderBy('nom')->orderBy('prenom')->paginate(20);

        // Récupérer tous les rôles et permissions disponibles
        $allRoles = Role::with('permissions')->orderBy('name')->get();
        $allPermissions = Permission::orderBy('name')->get();

        return view('admin.role-permissions.index', compact('users', 'allRoles', 'allPermissions'));
    }

    /**
     * Afficher les détails d'un utilisateur avec ses rôles et permissions
     */
    public function show(User $user)
    {
        $this->enforceSuperAdmin();

        $user->load(['roles.permissions']);
        $allRoles = Role::with('permissions')->orderBy('name')->get();
        $allPermissions = Permission::orderBy('name')->get();

        return view('admin.role-permissions.show', compact('user', 'allRoles', 'allPermissions'));
    }

    /**
     * Assigner un rôle à un utilisateur
     */
    public function assignRole(Request $request, User $user)
    {
        $this->enforceSuperAdmin();

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($request->role_id);

        // Vérifier si l'utilisateur n'a pas déjà ce rôle
        if (!$user->hasRole($role->slug)) {
            $user->attachRole($role->slug);

            return redirect()->back()->with('success', "Le rôle '{$role->name}' a été assigné avec succès.");
        }

        return redirect()->back()->with('warning', "L'utilisateur a déjà le rôle '{$role->name}'.");
    }

    /**
     * Retirer un rôle d'un utilisateur
     */
    public function removeRole(Request $request, User $user)
    {
        $this->enforceSuperAdmin();

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($request->role_id);

        // Ne pas permettre de retirer le dernier rôle super-admin
        if ($role->slug === 'super-admin' && $user->hasRole('super-admin')) {
            $superAdminCount = User::whereHas('roles', function($q) {
                $q->where('slug', 'super-admin');
            })->orWhere('role', 'super-admin')->count();
            if ($superAdminCount <= 1) {
                return redirect()->back()->with('error', 'Impossible de retirer le rôle Super Admin. Il doit y avoir au moins un Super Administrateur.');
            }
        }

        $user->detachRole($role->slug);

        return redirect()->back()->with('success', "Le rôle '{$role->name}' a été retiré avec succès.");
    }

    /**
     * Assigner une permission à un rôle
     */
    public function assignPermissionToRole(Request $request, Role $role)
    {
        $this->enforceSuperAdmin();

        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $permission = Permission::findOrFail($request->permission_id);

        // Vérifier si le rôle n'a pas déjà cette permission
        if (!$role->hasPermission($permission->slug)) {
            $role->attachPermission($permission);

            return redirect()->back()->with('success', "La permission '{$permission->name}' a été assignée au rôle '{$role->name}' avec succès.");
        }

        return redirect()->back()->with('warning', "Le rôle '{$role->name}' a déjà la permission '{$permission->name}'.");
    }

    /**
     * Retirer une permission d'un rôle
     */
    public function removePermissionFromRole(Request $request, Role $role)
    {
        $this->enforceSuperAdmin();

        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $permission = Permission::findOrFail($request->permission_id);

        $role->detachPermission($permission);

        return redirect()->back()->with('success', "La permission '{$permission->name}' a été retirée du rôle '{$role->name}' avec succès.");
    }

    /**
     * Assigner une permission directe à un utilisateur
     * Note: Cette fonctionnalité nécessite une table user_permission
     * Pour l'instant, les permissions sont gérées via les rôles uniquement
     */
    public function assignPermission(Request $request, User $user)
    {
        $this->enforceSuperAdmin();

        return redirect()->back()->with('info', 'Les permissions directes ne sont pas encore implémentées. Les permissions sont gérées via les rôles.');
    }

    /**
     * Retirer une permission directe d'un utilisateur
     * Note: Cette fonctionnalité nécessite une table user_permission
     * Pour l'instant, les permissions sont gérées via les rôles uniquement
     */
    public function removePermission(Request $request, User $user)
    {
        $this->enforceSuperAdmin();

        return redirect()->back()->with('info', 'Les permissions directes ne sont pas encore implémentées. Les permissions sont gérées via les rôles.');
    }

    /**
     * Mettre à jour le rôle legacy (champ role dans la table users)
     */
    public function updateLegacyRole(Request $request, User $user)
    {
        $this->enforceSuperAdmin();

        $request->validate([
            'role' => 'required|in:super-admin,admin,gestionnaire,vendeur,client',
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', "Le rôle legacy a été mis à jour avec succès.");
    }
}

