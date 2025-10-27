<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Afficher le profil de l'utilisateur connecté
     */
    public function show()
    {
        $user = Auth::user();
        return view('admin.profile.show', compact('user'));
    }

    /**
     * Afficher le formulaire d'édition du profil
     */
    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Mettre à jour le profil de l'utilisateur connecté
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'numero_telephone' => 'nullable|string|max:20',
            'numero_whatsapp' => 'nullable|string|max:20',
            'localisation' => 'nullable|string|max:255',
            'quartier' => 'nullable|string|max:255',
        ]);

        $user->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'numero_telephone' => $request->numero_telephone,
            'numero_whatsapp' => $request->numero_whatsapp,
            'localisation' => $request->localisation,
            'quartier' => $request->quartier,
        ]);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profil mis à jour avec succès !');
    }

    /**
     * Afficher le formulaire de changement de mot de passe
     */
    public function editPassword()
    {
        return view('admin.profile.password');
    }

    /**
     * Mettre à jour le mot de passe de l'utilisateur connecté
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        // Vérifier le mot de passe actuel
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Le mot de passe actuel est incorrect.'
            ]);
        }

        // Mettre à jour le mot de passe
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Mot de passe modifié avec succès !');
    }
}























