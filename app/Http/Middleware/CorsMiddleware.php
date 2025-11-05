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
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, Accept, Origin, X-Mobile-App',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => '86400',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
        ];

        // Gérer les requêtes OPTIONS (preflight) immédiatement
        if ($request->isMethod('OPTIONS')) {
            return response()->json(['status' => 'OK'], 200, $headers);
        }

        $response = $next($request);

        // Ajouter les headers CORS à toutes les réponses
        foreach ($headers as $key => $value) {
            try {
                // Vérifier si la réponse est une StreamedResponse
                if ($response instanceof \Symfony\Component\HttpFoundation\StreamedResponse) {
                    // Pour StreamedResponse, ajouter les headers avant de retourner
                    $response->headers->set($key, $value);
                } else {
                    // Pour les autres types de réponse, utiliser la méthode header()
                    $response->header($key, $value);
                }
            } catch (\Exception $e) {
                // Ignorer les erreurs de headers pour ne pas bloquer la réponse
                \Log::warning('Erreur ajout header CORS: ' . $e->getMessage());
            }
        }

        return $response;
    }
}
























