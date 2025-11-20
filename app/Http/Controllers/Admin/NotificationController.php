<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Helpers\ProductTypeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            // Optimisation : ne sélectionner que les colonnes nécessaires pour éviter de charger trop de données
            $query = Notification::select('id', 'user_id', 'title', 'message', 'type', 'is_read', 'data', 'created_at', 'updated_at')
                ->where(function($q) use ($user) {
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
            
            // Formater les notifications et déterminer le type de produit
            // Optimisation : utiliser DB::table() pour éviter de charger tous les modèles
            $formattedNotifications = $notifications->map(function($notification) {
                $data = $notification->data ?? [];
                $productType = $data['product_type'] ?? null;
                $hasTelephones = $data['has_telephones'] ?? false;
                $hasAccessoires = $data['has_accessoires'] ?? false;
                
                // Si c'est une notification de commande et qu'on n'a pas le product_type, le déterminer avec DB::table()
                if ($notification->type === 'order' && !$productType && isset($data['order_id'])) {
                    try {
                        $orderId = $data['order_id'];
                        
                        // Vérifier si la commande contient des téléphones
                        $hasPhones = DB::table('orders')
                            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                            ->join('products', 'order_items.product_id', '=', 'products.id')
                            ->where('orders.id', $orderId)
                            ->whereNull('products.deleted_at')
                            ->where(function($q) {
                                $q->where(function($subQ) {
                                    $subQ->whereNotNull('products.brand')->where('products.brand', '!=', '')
                                         ->orWhereNotNull('products.range')->where('products.range', '!=', '')
                                         ->orWhereNotNull('products.format')->where('products.format', '!=', '');
                                });
                            })
                            ->exists();
                        
                        // Vérifier si la commande contient des accessoires
                        $hasAccessories = DB::table('orders')
                            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                            ->join('products', 'order_items.product_id', '=', 'products.id')
                            ->where('orders.id', $orderId)
                            ->whereNull('products.deleted_at')
                            ->where(function($q) {
                                $q->where(function($subQ) {
                                    $subQ->whereNotNull('products.type_accessory')->where('products.type_accessory', '!=', '')
                                         ->orWhereNotNull('products.compatibility')->where('products.compatibility', '!=', '');
                                });
                            })
                            ->exists();
                        
                        // Déterminer le type
                        if ($hasPhones && $hasAccessories) {
                            $productType = 'mixed';
                        } elseif ($hasPhones) {
                            $productType = 'telephone';
                        } elseif ($hasAccessories) {
                            $productType = 'accessoire';
                        } else {
                            $productType = 'other';
                        }
                        
                        $hasTelephones = $hasPhones;
                        $hasAccessoires = $hasAccessories;
                        
                        // Mettre à jour les données
                        $data['product_type'] = $productType;
                        $data['has_telephones'] = $hasTelephones;
                        $data['has_accessoires'] = $hasAccessoires;
                    } catch (\Exception $e) {
                        \Log::warning('Erreur détermination type produit notification: ' . $e->getMessage());
                        $productType = 'other';
                    }
                }
                
                return [
                    'id' => $notification->id,
                    'user_id' => $notification->user_id,
                    'title' => $notification->title ?? 'Notification',
                    'message' => $notification->message ?? '',
                    'type' => $notification->type ?? 'system',
                    'is_read' => (bool)($notification->is_read ?? false),
                    'data' => $data,
                    'product_type' => $productType, // 'telephone', 'accessoire', 'mixed', 'other', ou null
                    'has_telephones' => $hasTelephones,
                    'has_accessoires' => $hasAccessoires,
                    'created_at' => $notification->created_at ? $notification->created_at->toIso8601String() : now()->toIso8601String(),
                    'updated_at' => $notification->updated_at ? $notification->updated_at->toIso8601String() : now()->toIso8601String(),
                ];
            });
            
            // Compter les notifications par type (optimisé)
            $telephonesCount = 0;
            $accessoiresCount = 0;
            $mixedCount = 0;
            $otherCount = 0;
            $telephonesUnread = 0;
            $accessoiresUnread = 0;
            $mixedUnread = 0;
            
            foreach ($formattedNotifications as $notif) {
                $productType = $notif['product_type'] ?? null;
                $isRead = $notif['is_read'] ?? false;
                
                if ($productType === 'telephone') {
                    $telephonesCount++;
                    if (!$isRead) $telephonesUnread++;
                } elseif ($productType === 'accessoire') {
                    $accessoiresCount++;
                    if (!$isRead) $accessoiresUnread++;
                } elseif ($productType === 'mixed') {
                    $mixedCount++;
                    $telephonesCount++; // Les mixed comptent dans les deux
                    $accessoiresCount++;
                    if (!$isRead) {
                        $mixedUnread++;
                        $telephonesUnread++;
                        $accessoiresUnread++;
                    }
                } else {
                    $otherCount++;
                }
            }
            
            $countsByType = [
                'telephones' => $telephonesCount,
                'accessoires' => $accessoiresCount,
                'mixed' => $mixedCount,
                'other' => $otherCount,
            ];
            
            $unreadCountsByType = [
                'telephones' => $telephonesUnread,
                'accessoires' => $accessoiresUnread,
                'mixed' => $mixedUnread,
            ];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $formattedNotifications->toArray(),
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ],
                'unread_count' => $this->getUnreadCount(),
                'counts_by_type' => $countsByType,
                'unread_counts_by_type' => $unreadCountsByType,
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

