<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiSecurityMiddleware
{
    /**
     * Middleware de sécurité pour protéger les routes API
     * Bloque l'accès depuis les navigateurs web pour empêcher
     * l'exploration des endpoints par des attaquants
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si la requête provient d'un navigateur web
        $userAgent = $request->header('User-Agent', '');

        // Liste des user agents de navigateurs courants
        $browserAgents = [
            'Mozilla', 'Chrome', 'Safari', 'Firefox', 'Edge', 'Opera',
            'MSIE', 'Trident', 'Gecko'
        ];

        // Vérifier si c'est un navigateur
        $isBrowser = false;
        foreach ($browserAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                $isBrowser = true;
                break;
            }
        }

        // Autoriser uniquement si :
        // 1. Ce n'est pas un navigateur (application mobile/Postman)
        // 2. OU si un header d'application mobile est présent
        // 3. OU si c'est une requête avec token JWT valide
        $hasAppHeader = $request->hasHeader('X-Mobile-App') ||
                       $request->hasHeader('X-API-Key') ||
                       $request->hasHeader('Authorization');

        if ($isBrowser && !$hasAppHeader) {
            // Rediriger vers la page de connexion admin
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}

