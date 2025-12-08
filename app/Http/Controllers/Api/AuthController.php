<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewUserRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        // Le middleware est géré dans les routes (api.php)
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        // Augmenter le timeout pour cette requête spécifique
        set_time_limit(300); // 5 minutes
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '512M');

        // Désactiver l'exécution du timeout pour cette requête
        ignore_user_abort(true);

        \Log::info('=== REGISTER REQUEST START ===', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'data_received' => $request->except(['password', 'password_confirmation']),
            'timestamp' => now()->toIso8601String(),
            'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'unknown'
        ]);

        try {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'numero_telephone' => 'required|string|max:20|unique:users',
            'numero_whatsapp' => 'nullable|string|max:20',
            'localisation' => 'nullable|string',
            'quartier' => 'required|string|max:100',
            'commercial_id' => 'nullable|exists:users,id',
            'commercial_phone' => 'nullable|string|max:20', // Numéro de téléphone du commercial
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
            'date_naissance' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            \Log::warning('Register validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        \Log::info('Register validation passed, creating user...');

        // Si un numéro de téléphone de commercial est fourni, rechercher le commercial
        $commercialId = $request->commercial_id;
        if (!$commercialId && $request->commercial_phone) {
            $commercial = User::where('numero_telephone', $request->commercial_phone)
                ->where(function($query) {
                    $query->where('role', 'vendeur')
                          ->orWhere('role', 'commercial')
                          ->orWhereHas('roles', function($q) {
                              $q->whereIn('slug', ['vendeur', 'commercial']);
                          });
                })
                ->where('status', 'active')
                ->first();

            if ($commercial) {
                $commercialId = $commercial->id;
                \Log::info('Commercial trouvé par numéro de téléphone', [
                    'commercial_id' => $commercialId,
                    'commercial_phone' => $request->commercial_phone
                ]);
            } else {
                \Log::warning('Commercial non trouvé avec le numéro fourni', [
                    'commercial_phone' => $request->commercial_phone
                ]);
            }
        }

        DB::beginTransaction();
        try {
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'numero_telephone' => $request->numero_telephone,
            'numero_whatsapp' => $request->numero_whatsapp,
            'localisation' => $request->localisation,
            'quartier' => $request->quartier,
            'commercial_id' => $commercialId,
            'password' => Hash::make($request->password),
            'date_naissance' => $request->date_naissance,
            'role' => 'client',
            'status' => 'pending', // En attente d'activation par l'admin
        ]);

        DB::commit();

        \Log::info('User created successfully', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        // Notifier les administrateurs via le système de notifications
        // Créer les notifications de manière synchrone pour garantir qu'elles sont créées
        try {
            \App\Helpers\NotificationHelper::notifyNewUser($user);
            \Log::info('Admin notification sent successfully (sync)');
        } catch (\Exception $e) {
            \Log::error('Erreur notification nouvel utilisateur: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            // Ne pas bloquer la création du compte si la notification échoue
        }

        \Log::info('=== REGISTER REQUEST SUCCESS ===', [
            'user_id' => $user->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Compte créé avec succès. En attente d\'activation par un administrateur.',
            'data' => [
                'user' => $user->makeHidden(['password', 'two_factor_secret'])
            ]
        ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('=== REGISTER REQUEST ERROR ===', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du compte. Veuillez réessayer.',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue'
            ], 500);
        }
        } catch (\Exception $e) {
            \Log::error('=== REGISTER REQUEST FATAL ERROR ===', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur. Veuillez réessayer plus tard.',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur serveur'
            ], 500);
        }
    }


    /**
     * Get a JWT via given credentials.
     */
    public function login(Request $request)
    {
        // Augmenter le timeout pour cette requête spécifique
        set_time_limit(60);

        \Log::info('Login attempt started', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            \Log::warning('Login validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Identifiants invalides'
                ], 401);
            }

            $user = Auth::user();

            // Vérifier si le compte est actif
            if (!$user->isActive()) {
                \Log::warning('Login attempt with inactive account', ['email' => $request->email]);
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte n\'est pas encore activé. Veuillez contacter un administrateur.'
                ], 403);
            }

            \Log::info('Login successful', ['user_id' => $user->id, 'email' => $user->email]);

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'data' => [
                    'user' => $user->makeHidden(['password', 'two_factor_secret']),
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60
                ]
            ]);

        } catch (JWTException $e) {
            \Log::error('JWT Exception during login', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Impossible de créer le token'
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Unexpected error during login', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la connexion'
            ], 500);
        }
    }

    /**
     * Get the authenticated User.
     */
    public function profile()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => auth()->user()->makeHidden(['password', 'two_factor_secret'])
            ]
        ]);
    }

    /**
     * Get the authenticated user.
     */
    public function me()
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->makeHidden(['password', 'two_factor_secret'])
            ]
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Refresh a token.
     */
    public function refresh()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'token' => auth()->refresh(),
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'numero_whatsapp' => 'nullable|string|max:20',
            'localisation' => 'nullable|string',
            'quartier' => 'nullable|string|max:100',
            'ville' => 'sometimes|string|max:100',
            'date_naissance' => 'nullable|date',
            'photo' => 'nullable|string|max:255', // URL de la photo
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only([
            'nom', 'prenom', 'numero_whatsapp',
            'localisation', 'quartier', 'ville', 'date_naissance', 'photo'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'data' => [
                'user' => $user->fresh()->makeHidden(['password', 'two_factor_secret'])
            ]
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mot de passe actuel incorrect'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe modifié avec succès'
        ]);
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Trouver l'utilisateur
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun utilisateur trouvé avec cette adresse email'
                ], 404);
            }

            // Générer un token de réinitialisation
            $token = Str::random(64);

            // Supprimer les anciens tokens pour cet email
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Insérer le nouveau token
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => now()
            ]);

            // Envoyer l'email de réinitialisation
            $user->notify(new \App\Notifications\PasswordResetNotification($token));

            return response()->json([
                'success' => true,
                'message' => 'Un lien de réinitialisation a été envoyé à votre adresse email'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi de l\'email de réinitialisation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Vérifier le token de réinitialisation
            $passwordReset = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('token', $request->token)
                ->first();

            if (!$passwordReset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token de réinitialisation invalide ou expiré'
                ], 400);
            }

            // Vérifier si le token n'est pas expiré (60 minutes)
            if (now()->diffInMinutes($passwordReset->created_at) > 60) {
                // Supprimer le token expiré
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();

                return response()->json([
                    'success' => false,
                    'message' => 'Le token de réinitialisation a expiré. Veuillez demander un nouveau lien.'
                ], 400);
            }

            // Mettre à jour le mot de passe
            $user = User::where('email', $request->email)->first();
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Supprimer le token utilisé
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe réinitialisé avec succès'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la réinitialisation du mot de passe: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réinitialisation du mot de passe. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Get user notifications
     */
    public function notifications()
    {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        auth()->user()->notifications()->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications marquées comme lues'
        ]);
    }

    /**
     * Get list of quartiers
     */
    public function getQuartiers()
    {
        $quartiers = \App\Models\Quartier::getQuartiers();

        return response()->json([
            'success' => true,
            'data' => $quartiers,
            'message' => 'Liste des quartiers récupérée avec succès'
        ]);
    }

    /**
     * Upload profile photo
     */
    public function uploadProfilePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = auth()->user();

            // Supprimer l'ancienne photo si elle existe
            if ($user->photo) {
                \Storage::disk('s3')->delete($user->photo);
            }

            // Sauvegarder la nouvelle photo
            $photo = $request->file('photo');
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('profiles', $filename, 'public');

            // Mettre à jour l'utilisateur
            $user->update(['photo' => $path]);

            // Recharger l'utilisateur pour obtenir toutes les données à jour
            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès',
                'data' => [
                    'photo_url' => \Storage::disk('s3')->url($path),
                    'photo' => $path, // Ajouter aussi le chemin relatif
                    'user' => [
                        'id' => $user->id,
                        'photo' => $path,
                        'photo_url' => \Storage::disk('s3')->url($path),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request)
    {
        try {
            $user = auth()->user();

            // Supprimer la photo de profil si elle existe
            if ($user->photo) {
                \Storage::disk('s3')->delete($user->photo);
            }

            // Supprimer l'utilisateur (soft delete)
            $user->delete();

            // Déconnexion
            auth()->logout();

            return response()->json([
                'success' => true,
                'message' => 'Votre compte a été supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du compte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechercher un commercial par numéro de téléphone
     * Utilisé lors de l'inscription pour lier un client à son commercial
     */
    public function searchCommercial(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'numero_telephone' => 'required|string|min:8|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Numéro de téléphone invalide',
                    'errors' => $validator->errors()
                ], 422);
            }

            $phoneNumber = $request->numero_telephone;

            // Rechercher les commerciaux (vendeurs) par numéro de téléphone
            $commercial = User::where('numero_telephone', $phoneNumber)
                ->where(function($query) {
                    $query->where('role', 'vendeur')
                          ->orWhere('role', 'commercial')
                          ->orWhereHas('roles', function($q) {
                              $q->whereIn('slug', ['vendeur', 'commercial']);
                          });
                })
                ->where('status', 'active')
                ->first();

            if (!$commercial) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun commercial trouvé avec ce numéro de téléphone',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Commercial trouvé',
                'data' => [
                    'id' => $commercial->id,
                    'nom' => $commercial->nom,
                    'prenom' => $commercial->prenom,
                    'full_name' => $commercial->full_name,
                    'numero_telephone' => $commercial->numero_telephone,
                    'email' => $commercial->email,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur recherche commercial: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche du commercial',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue'
            ], 500);
        }
    }

    /**
     * Obtenir la liste des quartiers
     */
    public function getQuartiers()
    {
        try {
            $quartiers = \App\Models\Quartier::getQuartiers();

            return response()->json([
                'success' => true,
                'data' => $quartiers
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur récupération quartiers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des quartiers',
                'data' => []
            ], 500);
        }
    }
}
