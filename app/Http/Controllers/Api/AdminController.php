<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Notification;
use App\Notifications\AccountActivatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Get all users with pagination and filters
     */
    public function users(Request $request)
    {
        $query = User::with(['orders' => function($q) {
            $q->select('id', 'user_id', 'status', 'total_amount', 'created_at');
        }]);

        // Filtrage par rôle
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        // Filtrage par statut
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Recherche par nom ou email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 20);
        $users = $query->paginate($perPage);

        // Ajouter des statistiques pour chaque utilisateur
        $users->getCollection()->transform(function ($user) {
            $user->total_orders = $user->orders->count();
            $user->total_spent = $user->orders->sum('total_amount');
            $user->last_order = $user->orders->max('created_at');
            return $user;
        });

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
            'message' => 'Utilisateurs récupérés avec succès'
        ]);
    }

    /**
     * Get specific user details
     */
    public function showUser($id)
    {
        $user = User::with(['orders.items.product', 'notifications'])
            ->findOrFail($id);

        // Statistiques de l'utilisateur
        $userStats = [
            'total_orders' => $user->orders->count(),
            'total_spent' => $user->orders->sum('total_amount'),
            'average_order_value' => $user->orders->avg('total_amount'),
            'last_order_date' => $user->orders->max('created_at'),
            'unread_notifications' => $user->notifications->where('is_read', false)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'statistics' => $userStats
            ],
            'message' => 'Détails utilisateur récupérés avec succès'
        ]);
    }

    /**
     * Update user status
     */
    public function updateUserStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,pending,suspended,inactive',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail($id);
        $oldStatus = $user->status;

        $user->update([
            'status' => $request->status,
        ]);

        // Envoyer un email si le compte est activé
        if ($request->status === 'active' && $oldStatus !== 'active') {
            try {
                $user->notify(new AccountActivatedNotification($user));
            } catch (\Exception $e) {
                // Log l'erreur mais ne pas faire échouer la requête
                \Log::error('Erreur envoi email activation: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'data' => $user->fresh(),
            'message' => 'Statut utilisateur mis à jour avec succès'
        ]);
    }

    /**
     * Delete user (soft delete)
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Vérifier s'il y a des commandes en cours
        if ($user->orders()->whereIn('status', ['pending', 'confirmed', 'processing'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer cet utilisateur car il a des commandes en cours'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès'
        ]);
    }

    /**
     * Get admin notifications
     */
    public function notifications(Request $request)
    {
        $query = Notification::with('user')
            ->whereHas('user', function($q) {
                $q->whereIn('role', ['admin', 'gestionnaire']);
            });

        // Filtrage par type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filtrage par statut de lecture
        if ($request->has('is_read') && $request->is_read !== null) {
            $query->where('is_read', $request->is_read);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $notifications->items(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
            'message' => 'Notifications récupérées avec succès'
        ]);
    }

    /**
     * Send notification to users
     */
    public function sendNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|string|max:50',
            'target_users' => 'required|array',
            'target_users.*' => 'in:all,clients,admins',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $targetUsers = $request->target_users;
        $userIds = $request->user_ids ?? [];

        // Déterminer les utilisateurs cibles
        $query = User::query();

        if (in_array('all', $targetUsers)) {
            // Tous les utilisateurs
        } elseif (in_array('clients', $targetUsers) && in_array('admins', $targetUsers)) {
            // Clients et admins
        } elseif (in_array('clients', $targetUsers)) {
            $query->where('role', 'client');
        } elseif (in_array('admins', $targetUsers)) {
            $query->whereIn('role', ['admin', 'gestionnaire']);
        }

        if (!empty($userIds)) {
            $query->whereIn('id', $userIds);
        }

        $users = $query->get();

        // Créer les notifications
        $notifications = [];
        foreach ($users as $user) {
            $notifications[] = [
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => $user->id,
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'data' => [
                    'sent_by' => auth()->user()->full_name,
                    'sent_at' => now()->toISOString(),
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Notification::insert($notifications);

        return response()->json([
            'success' => true,
            'data' => [
                'notifications_sent' => count($notifications),
                'target_users' => $users->pluck('email')->toArray()
            ],
            'message' => 'Notifications envoyées avec succès'
        ]);
    }

    /**
     * Get general statistics
     */
    public function statistics()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('status', 'active')->count(),
                'pending' => User::where('status', 'pending')->count(),
                'new_today' => User::whereDate('created_at', $today)->count(),
                'new_this_week' => User::where('created_at', '>=', $thisWeek)->count(),
                'new_this_month' => User::where('created_at', '>=', $thisMonth)->count(),
            ],
            'orders' => [
                'total' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'confirmed' => Order::where('status', 'confirmed')->count(),
                'processing' => Order::where('status', 'processing')->count(),
                'shipped' => Order::where('status', 'shipped')->count(),
                'delivered' => Order::where('status', 'delivered')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
                'today' => Order::whereDate('created_at', $today)->count(),
                'this_week' => Order::where('created_at', '>=', $thisWeek)->count(),
                'this_month' => Order::where('created_at', '>=', $thisMonth)->count(),
            ],
            'products' => [
                'total' => Product::count(),
                'active' => Product::where('status', 'active')->count(),
                'out_of_stock' => Product::where('stock_quantity', 0)->count(),
                'low_stock' => Product::where('stock_quantity', '>', 0)
                    ->whereColumn('stock_quantity', '<=', 'min_stock_alert')->count(),
                'featured' => Product::where('is_featured', true)->count(),
            ],
            'revenue' => [
                'total' => Order::where('status', '!=', 'cancelled')->sum('total_amount'),
                'today' => Order::where('status', '!=', 'cancelled')
                    ->whereDate('created_at', $today)->sum('total_amount'),
                'this_week' => Order::where('status', '!=', 'cancelled')
                    ->where('created_at', '>=', $thisWeek)->sum('total_amount'),
                'this_month' => Order::where('status', '!=', 'cancelled')
                    ->where('created_at', '>=', $thisMonth)->sum('total_amount'),
            ],
            'notifications' => [
                'unread' => Notification::where('is_read', false)->count(),
                'today' => Notification::whereDate('created_at', $today)->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Statistiques récupérées avec succès'
        ]);
    }

    /**
     * Get order statistics
     */
    public function orderStatistics(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        $startDate = $this->getStartDate($period);

        $query = Order::where('created_at', '>=', $startDate);

        // Statistiques par statut
        $statusStats = $query->selectRaw('status, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Statistiques par jour/semaine/mois
        $dateFormat = $this->getDateFormat($period);
        $dailyStats = $query->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period, COUNT(*) as orders, SUM(total_amount) as revenue")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // Top clients
        $topClients = Order::where('created_at', '>=', $startDate)
            ->selectRaw('user_id, COUNT(*) as order_count, SUM(total_amount) as total_spent')
            ->with('user:id,nom,prenom,email')
            ->groupBy('user_id')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'start_date' => $startDate,
                'status_breakdown' => $statusStats,
                'daily_stats' => $dailyStats,
                'top_clients' => $topClients,
            ],
            'message' => 'Statistiques des commandes récupérées avec succès'
        ]);
    }

    /**
     * Get product statistics
     */
    public function productStatistics(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);

        // Produits les plus vendus
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.created_at', '>=', $startDate)
            ->selectRaw('products.id, products.name, SUM(order_items.quantity) as total_sold, SUM(order_items.total_price) as total_revenue')
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // Statistiques par catégorie
        $categoryStats = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.created_at', '>=', $startDate)
            ->selectRaw('categories.id, categories.name, SUM(order_items.quantity) as total_sold, SUM(order_items.total_price) as total_revenue')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Alertes de stock
        $stockAlerts = Product::where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '<=', 'min_stock_alert')
            ->with('category:id,name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'start_date' => $startDate,
                'top_products' => $topProducts,
                'category_stats' => $categoryStats,
                'stock_alerts' => $stockAlerts,
            ],
            'message' => 'Statistiques des produits récupérées avec succès'
        ]);
    }

    /**
     * Get user statistics
     */
    public function userStatistics(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);

        // Inscriptions par période
        $dateFormat = $this->getDateFormat($period);
        $registrations = User::where('created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period, COUNT(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // Répartition par rôle
        $roleStats = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->get()
            ->keyBy('role');

        // Répartition par statut
        $statusStats = User::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Utilisateurs les plus actifs (par nombre de commandes)
        $activeUsers = User::withCount(['orders' => function($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }])
            ->withSum(['orders' => function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }], 'total_amount')
            ->having('orders_count', '>', 0)
            ->orderBy('orders_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'start_date' => $startDate,
                'registrations' => $registrations,
                'role_breakdown' => $roleStats,
                'status_breakdown' => $statusStats,
                'most_active_users' => $activeUsers,
            ],
            'message' => 'Statistiques des utilisateurs récupérées avec succès'
        ]);
    }

    /**
     * Get start date based on period
     */
    private function getStartDate($period)
    {
        switch ($period) {
            case 'day':
                return Carbon::today();
            case 'week':
                return Carbon::now()->startOfWeek();
            case 'month':
                return Carbon::now()->startOfMonth();
            case 'year':
                return Carbon::now()->startOfYear();
            default:
                return Carbon::now()->startOfMonth();
        }
    }

    /**
     * Get date format for grouping
     */
    private function getDateFormat($period)
    {
        switch ($period) {
            case 'day':
                return '%Y-%m-%d %H:00:00';
            case 'week':
                return '%Y-%u';
            case 'month':
                return '%Y-%m';
            case 'year':
                return '%Y';
            default:
                return '%Y-%m';
        }
    }
}










