<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get user notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $user->notifications();

        // Filtres
        if ($request->has('unread_only') && $request->unread_only) {
            $query->whereNull('read_at');
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'message' => 'Notifications récupérées avec succès'
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $count = $user->unreadNotifications()->count();

        return response()->json([
            'success' => true,
            'data' => ['count' => $count],
            'message' => 'Nombre de notifications non lues récupéré'
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été marquées comme lues'
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification supprimée'
        ]);
    }

    /**
     * Clear all notifications
     */
    public function clear()
    {
        $user = Auth::user();
        $user->notifications()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été supprimées'
        ]);
    }

    /**
     * Get notification details
     */
    public function show($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);

        // Marquer comme lue si elle ne l'est pas déjà
        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'data' => $notification,
            'message' => 'Détails de la notification récupérés'
        ]);
    }
}
