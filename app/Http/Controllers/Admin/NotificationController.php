<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Afficher la page de toutes les notifications
     */
    public function indexPage()
    {
        return view('admin.notifications.index');
    }

    /**
     * Récupérer les notifications pour l'admin connecté (API)
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            // Récupérer les notifications pour les admins
            // Les notifications peuvent être créées pour tous les admins ou pour un admin spécifique
            $query = Notification::where(function($q) use ($user) {
                // Notifications pour tous les admins (user_id = null) ou pour cet admin spécifique
                $q->whereNull('user_id')
                  ->orWhere('user_id', $user->id);
            })
            ->whereIn('type', ['order', 'account', 'system', 'client']); // Types de notifications pour admins
            
            // Filtrer par non lues uniquement si demandé
            if ($request->has('unread_only') && $request->unread_only) {
                $query->unread();
            }
            
            // Pagination
            $perPage = $request->get('per_page', 20);
            $notifications = $query->orderBy('created_at', 'desc')
                                  ->paginate($perPage);
            
            // Formater les notifications
            $formattedNotifications = $notifications->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'user_id' => $notification->user_id,
                    'title' => $notification->title ?? 'Notification',
                    'message' => $notification->message ?? '',
                    'type' => $notification->type ?? 'system',
                    'is_read' => (bool)($notification->is_read ?? false),
                    'data' => $notification->data ?? [],
                    'created_at' => $notification->created_at ? $notification->created_at->toIso8601String() : now()->toIso8601String(),
                    'updated_at' => $notification->updated_at ? $notification->updated_at->toIso8601String() : now()->toIso8601String(),
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $formattedNotifications->toArray(),
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ],
                'unread_count' => $this->getUnreadCount()
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur chargement notifications: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des notifications: ' . $e->getMessage(),
                'data' => [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 20,
                    'total' => 0,
                ],
                'unread_count' => 0
            ], 500);
        }
    }
    
    /**
     * Récupérer le nombre de notifications non lues
     */
    public function unreadCount()
    {
        $count = $this->getUnreadCount();
        
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
    
    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = Notification::where(function($q) use ($user) {
            $q->whereNull('user_id')->orWhere('user_id', $user->id);
        })->findOrFail($id);
        
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue',
            'unread_count' => $this->getUnreadCount()
        ]);
    }
    
    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead(Request $request)
    {
        try {
            \Log::info('🔔 Début markAllAsRead', [
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            $user = Auth::user();
            
            if (!$user) {
                \Log::warning('❌ Utilisateur non authentifié pour markAllAsRead');
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            \Log::info('👤 Utilisateur authentifié', ['user_id' => $user->id, 'email' => $user->email]);
            
            // Compter les notifications non lues avant la mise à jour
            $beforeCount = Notification::where(function($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->whereIn('type', ['order', 'account', 'system', 'client'])
            ->where(function($q) {
                $q->where('is_read', false)
                  ->orWhereNull('is_read')
                  ->orWhere('is_read', 0);
            })
            ->count();
            
            \Log::info('📊 Notifications non lues trouvées', ['count' => $beforeCount]);
            
            // Marquer toutes les notifications non lues comme lues
            $updated = Notification::where(function($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->whereIn('type', ['order', 'account', 'system', 'client'])
            ->where(function($q) {
                $q->where('is_read', false)
                  ->orWhereNull('is_read')
                  ->orWhere('is_read', 0);
            })
            ->update(['is_read' => true]);
            
            \Log::info('✅ Notifications marquées comme lues', [
                'user_id' => $user->id,
                'updated_count' => $updated,
                'before_count' => $beforeCount
            ]);
            
            $unreadCount = $this->getUnreadCount();
            
            return response()->json([
                'success' => true,
                'message' => 'Toutes les notifications ont été marquées comme lues',
                'count' => $updated,
                'before_count' => $beforeCount,
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Erreur lors du marquage de toutes les notifications: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage des notifications: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Supprimer une notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = Notification::where(function($q) use ($user) {
            $q->whereNull('user_id')->orWhere('user_id', $user->id);
        })->findOrFail($id);
        
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification supprimée',
            'unread_count' => $this->getUnreadCount()
        ]);
    }
    
    /**
     * Obtenir le nombre de notifications non lues
     */
    private function getUnreadCount()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return 0;
            }
            
            return Notification::where(function($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->whereIn('type', ['order', 'account', 'system', 'client'])
            ->unread()
            ->count();
        } catch (\Exception $e) {
            \Log::error('Erreur comptage notifications: ' . $e->getMessage());
            return 0;
        }
    }
}

