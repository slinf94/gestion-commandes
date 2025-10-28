<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        $user = auth()->user();

        // Vérifier si l'utilisateur a l'un des rôles requis
        $hasRole = false;
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        // Si aucun rôle correspondant, vérifier les rôles par défaut (ancien système)
        if (!$hasRole) {
            if (in_array('super-admin', $roles) || in_array('admin', $roles)) {
                if (in_array($user->role, ['admin', 'super-admin'])) {
                    $hasRole = true;
                }
            }
            if (in_array('gestionnaire', $roles) && $user->role === 'gestionnaire') {
                $hasRole = true;
            }
            if (in_array('vendeur', $roles) && $user->role === 'vendeur') {
                $hasRole = true;
            }
        }

        if (!$hasRole) {
            abort(403, 'Accès non autorisé. Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource.');
        }

        return $next($request);
    }
}

