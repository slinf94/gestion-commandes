<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MobileRedirectController extends Controller
{
    /**
     * Afficher la page de redirection mobile
     */
    public function index(Request $request)
    {
        // Récupérer les informations utilisateur si disponibles
        $user = null;

        // Si un utilisateur est connecté, utiliser ses informations
        if (auth()->check()) {
            $user = auth()->user();
        }

        // Si un email est passé en paramètre, essayer de trouver l'utilisateur
        if ($request->has('email')) {
            $user = \App\Models\User::where('email', $request->email)->first();
        }

        return view('mobile-redirect', compact('user'));
    }
}
