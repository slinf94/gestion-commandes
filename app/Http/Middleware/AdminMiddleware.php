<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        $user = auth()->user();
        
        if ($user->role !== 'admin' && $user->role !== 'super_admin') {
            abort(403, 'Accès non autorisé. Seuls les administrateurs peuvent accéder à cette page.');
        }

        if ($user->status !== 'active') {
            auth()->logout();
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Votre compte a été désactivé. Contactez l\'administrateur.',
            ]);
        }

        return $next($request);
    }
}