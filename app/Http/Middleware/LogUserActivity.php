<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers\ActivityLogger;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est authentifié
        if (Auth::check()) {
            $user = Auth::user();
            $routeName = $request->route()?->getName();
            $action = $request->method();
            $path = $request->path();

            // Logger les actions importantes
            if ($this->shouldLogActivity($routeName, $action, $path)) {
                $this->logActivity($user, $routeName, $action, $path, $request);
            }
        }

        return $next($request);
    }

    /**
     * Détermine si l'activité doit être loggée
     */
    private function shouldLogActivity(string $routeName, string $action, string $path): bool
    {
        // Routes à logger
        $routesToLog = [
            'admin.users.store',      // Création d'utilisateur
            'admin.users.update',     // Modification d'utilisateur
            'admin.users.destroy',    // Suppression d'utilisateur
            'admin.products.store',   // Création de produit
            'admin.products.update',  // Modification de produit
            'admin.products.destroy', // Suppression de produit
            'admin.orders.status',    // Changement de statut de commande
            'admin.orders.destroy',   // Suppression de commande
            'admin.login',            // Connexion
            'admin.logout',           // Déconnexion
        ];

        // Actions à logger
        $actionsToLog = ['POST', 'PUT', 'PATCH', 'DELETE'];

        // Vérifier si la route doit être loggée
        if (in_array($routeName, $routesToLog)) {
            return true;
        }

        // Logger les actions importantes sur les ressources
        if (in_array($action, $actionsToLog) && str_contains($path, 'admin/')) {
            return true;
        }

        return false;
    }

    /**
     * Log l'activité de l'utilisateur
     */
    private function logActivity($user, string $routeName, string $action, string $path, Request $request): void
    {
        try {
            $description = $this->getActivityDescription($routeName, $action, $path);
            $logName = $this->getLogName($routeName, $path);

            // Utiliser le helper ActivityLogger pour les actions spéciales
            if (in_array($routeName, ['admin.login', 'admin.logout'])) {
                if ($routeName === 'admin.login') {
                    ActivityLogger::logLogin($user);
                } else {
                    ActivityLogger::logLogout($user);
                }
            } else {
                // Pour les autres actions, utiliser l'activity helper de Spatie
                activity($logName)
                    ->causedBy($user)
                    ->withProperties([
                        'route' => $routeName,
                        'method' => $action,
                        'path' => $path,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'timestamp' => now()->toISOString(),
                    ])
                    ->log($description);
            }
        } catch (\Exception $e) {
            // Logger l'erreur mais ne pas faire échouer la requête
            Log::error('Erreur lors du logging de l\'activité utilisateur: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'route' => $routeName,
                'action' => $action,
                'path' => $path,
            ]);
        }
    }

    /**
     * Génère la description de l'activité
     */
    private function getActivityDescription(string $routeName, string $action, string $path): string
    {
        // Descriptions spécifiques pour certaines routes
        $descriptions = [
            'admin.users.store' => 'créé un nouvel utilisateur',
            'admin.users.update' => 'modifié un utilisateur',
            'admin.users.destroy' => 'supprimé un utilisateur',
            'admin.products.store' => 'créé un nouveau produit',
            'admin.products.update' => 'modifié un produit',
            'admin.products.destroy' => 'supprimé un produit',
            'admin.orders.status' => 'modifié le statut d\'une commande',
            'admin.orders.destroy' => 'supprimé une commande',
            'admin.login' => 's\'est connecté',
            'admin.logout' => 's\'est déconnecté',
        ];

        if (isset($descriptions[$routeName])) {
            return $descriptions[$routeName];
        }

        // Descriptions génériques basées sur l'action
        switch ($action) {
            case 'POST':
                return 'a créé une ressource';
            case 'PUT':
            case 'PATCH':
                return 'a modifié une ressource';
            case 'DELETE':
                return 'a supprimé une ressource';
            default:
                return 'a effectué une action';
        }
    }

    /**
     * Détermine le nom du log
     */
    private function getLogName(string $routeName, string $path): string
    {
        // Logs spécifiques
        if (str_contains($routeName, 'users')) {
            return 'user';
        }

        if (str_contains($routeName, 'products')) {
            return 'product';
        }

        if (str_contains($routeName, 'orders')) {
            return 'order';
        }

        if (in_array($routeName, ['admin.login', 'admin.logout'])) {
            return 'auth';
        }

        // Log générique pour l'administration
        if (str_contains($path, 'admin/')) {
            return 'admin';
        }

        return 'default';
    }
}
