<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Headers CORS pour permettre les requêtes depuis l'application mobile
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, Accept, Origin',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => '86400',
        ];

        // Gérer les requêtes OPTIONS (preflight)
        if ($request->isMethod('OPTIONS')) {
            return response()->json('OK', 200, $headers);
        }

        $response = $next($request);

        // Ajouter les headers CORS à toutes les réponses
        foreach ($headers as $key => $value) {
            // Vérifier si la réponse est une StreamedResponse
            if ($response instanceof \Symfony\Component\HttpFoundation\StreamedResponse) {
                // Pour StreamedResponse, ajouter les headers avant de retourner
                $response->headers->set($key, $value);
            } else {
                // Pour les autres types de réponse, utiliser la méthode header()
                $response->header($key, $value);
            }
        }

        return $response;
    }
}
























