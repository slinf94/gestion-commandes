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

        // Si aucun rôle correspondant via le nouveau système, utiliser une vérification STRICTE sur l'ancien champ `role`.
        // IMPORTANT: pas d'équivalence hiérarchique (admin != gestionnaire, gestionnaire != vendeur, etc.)
        if (!$hasRole) {
            if (!empty($roles)) {
                $hasRole = in_array($user->role, $roles, true);
            }
        }

        if (!$hasRole) {
            abort(403, 'Accès non autorisé. Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource.');
        }

        return $next($request);
    }
}

