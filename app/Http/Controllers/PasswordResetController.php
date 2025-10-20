<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Afficher le formulaire de demande de réinitialisation
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Traiter la demande de réinitialisation
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'email.exists' => 'Cette adresse email n\'existe pas dans notre système.'
        ]);

        try {
            // Vérifier si l'utilisateur existe
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->withErrors(['email' => 'Cette adresse email n\'existe pas.']);
            }

            // Générer un token de réinitialisation
            $token = Str::random(64);

            // Supprimer les anciens tokens pour cet utilisateur
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Insérer le nouveau token
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => now()
            ]);

            // Construire l'URL de réinitialisation
            $resetUrl = route('password.reset') . '?token=' . $token . '&email=' . urlencode($request->email);

            // Ici vous pouvez envoyer un email avec le lien de réinitialisation
            // Pour l'instant, nous allons juste afficher l'URL dans un message de succès

            return back()->with('success',
                'Un lien de réinitialisation a été généré. ' .
                'URL de réinitialisation: ' . $resetUrl
            );

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Une erreur est survenue. Veuillez réessayer.']);
        }
    }

    /**
     * Afficher le formulaire de réinitialisation
     */
    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        // Vérifier si le token est valide
        if (!$token || !$this->isValidToken($token, $email)) {
            return redirect()->route('password.request')
                ->withErrors(['token' => 'Ce lien de réinitialisation est invalide ou a expiré.']);
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Traiter la réinitialisation du mot de passe
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.'
        ]);

        try {
            // Vérifier si le token est valide
            if (!$this->isValidToken($request->token, $request->email)) {
                return back()->withErrors(['token' => 'Ce lien de réinitialisation est invalide ou a expiré.']);
            }

            // Trouver l'utilisateur
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->withErrors(['email' => 'Cette adresse email n\'existe pas.']);
            }

            // Mettre à jour le mot de passe
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Supprimer le token utilisé
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('token', $request->token)
                ->delete();

            return redirect()->route('admin.login')
                ->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');

        } catch (\Exception $e) {
            return back()->withErrors(['password' => 'Une erreur est survenue. Veuillez réessayer.']);
        }
    }

    /**
     * Vérifier si le token est valide
     */
    private function isValidToken($token, $email)
    {
        if (!$token || !$email) {
            return false;
        }

        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$passwordReset) {
            return false;
        }

        // Vérifier si le token n'a pas expiré (24 heures)
        $createdAt = Carbon::parse($passwordReset->created_at);
        if ($createdAt->diffInHours(now()) > 24) {
            // Supprimer le token expiré
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->where('token', $token)
                ->delete();
            return false;
        }

        return true;
    }
}
