<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword', 'resetPassword']]);
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'numero_telephone' => 'required|string|max:20|unique:users',
            'numero_whatsapp' => 'nullable|string|max:20',
            'localisation' => 'nullable|string',
            'quartier' => 'nullable|string|max:100',
            'ville' => 'required|string|max:100',
            'password' => 'required|string|min:6',
            'date_naissance' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'numero_telephone' => $request->numero_telephone,
            'numero_whatsapp' => $request->numero_whatsapp,
            'localisation' => $request->localisation,
            'quartier' => $request->quartier,
            'ville' => $request->ville,
            'password' => Hash::make($request->password),
            'date_naissance' => $request->date_naissance,
            'role' => 'client',
            'status' => 'pending',
        ]);

        // TODO: Envoyer un code de vérification par SMS/email
        // TODO: Notifier les administrateurs

        return response()->json([
            'success' => true,
            'message' => 'Compte créé avec succès. En attente d\'activation par un administrateur.',
            'data' => [
                'user' => $user->makeHidden(['password', 'two_factor_secret'])
            ]
        ], 201);
    }

    /**
     * Get a JWT via given credentials.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
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
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte n\'est pas encore activé. Veuillez contacter un administrateur.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'data' => [
                    'user' => $user->makeHidden(['password', 'two_factor_secret']),
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60
                ]
            ]);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de créer le token'
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
                'expires_in' => auth()->factory()->getTTL() * 60
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
            'localisation', 'quartier', 'ville', 'date_naissance'
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

        // TODO: Envoyer un code de réinitialisation par email/SMS

        return response()->json([
            'success' => true,
            'message' => 'Code de réinitialisation envoyé à votre email'
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // TODO: Vérifier le code de réinitialisation

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe réinitialisé avec succès'
        ]);
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
}
