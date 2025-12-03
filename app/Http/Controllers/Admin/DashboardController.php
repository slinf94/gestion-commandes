<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderSimple as Order;
use App\Models\Category;
use App\Enums\OrderStatus;
use App\Helpers\ProductTypeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Optimisation mémoire : utiliser DB::table() au lieu d'Eloquent pour les statistiques
        // Statistiques générales
        $stats = [
            'total_users' => DB::table('users')->whereNull('deleted_at')->count(),
            'total_products' => DB::table('products')->whereNull('deleted_at')->count(),
            'total_orders' => DB::table('orders')->count(),
            'total_categories' => DB::table('categories')->where('is_active', true)->count(),
            'pending_orders' => DB::table('orders')->where('status', OrderStatus::PENDING->value)->count(),
            'active_products' => DB::table('products')->where('status', 'active')->whereNull('deleted_at')->count(),
            'draft_products' => DB::table('products')->where('status', 'draft')->whereNull('deleted_at')->count(),
            'out_of_stock' => DB::table('products')->where('stock_quantity', '<=', 0)->whereNull('deleted_at')->count(),
            'low_stock' => DB::table('products')
                ->where('stock_quantity', '>', 0)
                ->whereRaw('stock_quantity <= min_stock_alert')
                ->whereNull('deleted_at')
                ->count(),
            // Chiffre d'affaires : inclut les commandes pending, confirmed, processing, shipped, delivered
            // Exclut seulement les commandes cancelled
            'total_revenue' => DB::table('orders')
                ->whereNotIn('status', [OrderStatus::CANCELLED->value])
                ->sum('total_amount'),
        ];

        // Ventes journalières (7 derniers jours)
        $daily_sales = DB::table('orders')
            ->whereNotIn('status', [OrderStatus::CANCELLED->value])
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Ventes mensuelles (12 derniers mois)
        $monthlyFormat = '%Y-%m';
        $monthlyExpr = $this->getDateFormatExpression('created_at', $monthlyFormat);
        $monthly_sales = DB::table('orders')
            ->whereNotIn('status', [OrderStatus::CANCELLED->value])
            ->whereDate('created_at', '>=', now()->subMonths(12))
            ->selectRaw("{$monthlyExpr} as month, COUNT(*) as orders, SUM(total_amount) as revenue")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        // Commandes triées par statut
        $orders_by_status = DB::table('orders')
            ->selectRaw('status, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Commandes récentes - version optimisée avec DB::table pour éviter l'épuisement mémoire
        $recent_orders = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.id', 'orders.order_number', 'orders.status', 'orders.total_amount',
                     'orders.created_at',
                     DB::raw("COALESCE(users.nom, ''), ' ', COALESCE(users.prenom, '') as user_name"),
                     'users.email as user_email')
            ->whereNull('users.deleted_at')
            ->orderBy('orders.created_at', 'desc')
            ->limit(10)
            ->get();

        // Produits les plus commandés (tous temps)
        $top_products = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', [OrderStatus::CANCELLED->value])
            ->select('products.id', 'products.name', 'products.price', 'products.brand',
                     DB::raw('SUM(order_items.quantity) as total_sold'),
                     DB::raw('SUM(order_items.total_price) as total_revenue'))
            ->groupBy('products.id', 'products.name', 'products.price', 'products.brand')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // Produits les plus commandés ce mois
        $top_products_month = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', [OrderStatus::CANCELLED->value])
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->select('products.id', 'products.name', 'products.price', 'products.brand',
                     DB::raw('SUM(order_items.quantity) as total_sold'),
                     DB::raw('SUM(order_items.total_price) as total_revenue'))
            ->groupBy('products.id', 'products.name', 'products.price', 'products.brand')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // Produits en brouillon à compléter - version optimisée avec DB::table
        $draft_products = DB::table('products')
            ->where('status', 'draft')
            ->whereNull('deleted_at')
            ->select('id', 'name', 'slug', 'category_id', 'price', 'stock_quantity', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Produits en rupture de stock - version optimisée avec DB::table
        $out_of_stock_products = DB::table('products')
            ->where('stock_quantity', '<=', 0)
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name', 'slug', 'category_id', 'stock_quantity', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Produits à stock faible - version optimisée avec DB::table
        $low_stock_products = DB::table('products')
            ->where('stock_quantity', '>', 0)
            ->whereRaw('stock_quantity <= min_stock_alert')
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name', 'slug', 'category_id', 'stock_quantity', 'min_stock_alert')
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        // Statistiques par catégorie
        $category_stats = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.id', 'categories.name',
                     DB::raw('COUNT(products.id) as product_count'),
                     DB::raw('SUM(CASE WHEN products.status = "active" THEN 1 ELSE 0 END) as active_count'))
            ->whereNull('products.deleted_at')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('product_count', 'desc')
            ->get();

        // Statistiques par marque
        $brand_stats = DB::table('products')
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->whereNull('deleted_at')
            ->select('brand',
                     DB::raw('COUNT(*) as product_count'),
                     DB::raw('SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_count'))
            ->groupBy('brand')
            ->orderBy('product_count', 'desc')
            ->limit(10)
            ->get();

        // Utilisateurs récents - version optimisée avec DB::table
        $recent_users = DB::table('users')
            ->whereNull('deleted_at')
            ->select('id', 'nom', 'prenom', 'email', 'role', 'created_at',
                     DB::raw("CONCAT(COALESCE(nom, ''), ' ', COALESCE(prenom, '')) as name"))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Statistiques par type de produit (Téléphones vs Accessoires)
        $orders_by_product_type = [
            'telephones' => [
                'total' => DB::table('orders')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->whereNotIn('orders.status', [OrderStatus::CANCELLED->value])
                    ->where(function($q) {
                        $q->where(function($subQ) {
                            $subQ->whereNotNull('products.brand')->where('products.brand', '!=', '')
                                 ->orWhereNotNull('products.range')->where('products.range', '!=', '')
                                 ->orWhereNotNull('products.format')->where('products.format', '!=', '');
                        });
                    })
                    ->distinct('orders.id')
                    ->count('orders.id'),
                'revenue' => DB::table('orders')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->whereNotIn('orders.status', [OrderStatus::CANCELLED->value])
                    ->where(function($q) {
                        $q->where(function($subQ) {
                            $subQ->whereNotNull('products.brand')->where('products.brand', '!=', '')
                                 ->orWhereNotNull('products.range')->where('products.range', '!=', '')
                                 ->orWhereNotNull('products.format')->where('products.format', '!=', '');
                        });
                    })
                    ->sum('order_items.total_price'),
                'pending' => DB::table('orders')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->where('orders.status', OrderStatus::PENDING->value)
                    ->where(function($q) {
                        $q->where(function($subQ) {
                            $subQ->whereNotNull('products.brand')->where('products.brand', '!=', '')
                                 ->orWhereNotNull('products.range')->where('products.range', '!=', '')
                                 ->orWhereNotNull('products.format')->where('products.format', '!=', '');
                        });
                    })
                    ->distinct('orders.id')
                    ->count('orders.id'),
            ],
            'accessoires' => [
                'total' => DB::table('orders')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->whereNotIn('orders.status', [OrderStatus::CANCELLED->value])
                    ->where(function($q) {
                        $q->where(function($subQ) {
                            $subQ->whereNotNull('products.type_accessory')->where('products.type_accessory', '!=', '')
                                 ->orWhereNotNull('products.compatibility')->where('products.compatibility', '!=', '');
                        });
                    })
                    ->distinct('orders.id')
                    ->count('orders.id'),
                'revenue' => DB::table('orders')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->whereNotIn('orders.status', [OrderStatus::CANCELLED->value])
                    ->where(function($q) {
                        $q->where(function($subQ) {
                            $subQ->whereNotNull('products.type_accessory')->where('products.type_accessory', '!=', '')
                                 ->orWhereNotNull('products.compatibility')->where('products.compatibility', '!=', '');
                        });
                    })
                    ->sum('order_items.total_price'),
                'pending' => DB::table('orders')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->where('orders.status', OrderStatus::PENDING->value)
                    ->where(function($q) {
                        $q->where(function($subQ) {
                            $subQ->whereNotNull('products.type_accessory')->where('products.type_accessory', '!=', '')
                                 ->orWhereNotNull('products.compatibility')->where('products.compatibility', '!=', '');
                        });
                    })
                    ->distinct('orders.id')
                    ->count('orders.id'),
            ],
        ];

        return view('admin.dashboard', compact(
            'stats',
            'daily_sales',
            'monthly_sales',
            'orders_by_status',
            'recent_orders',
            'top_products',
            'top_products_month',
            'draft_products',
            'out_of_stock_products',
            'low_stock_products',
            'category_stats',
            'brand_stats',
            'recent_users',
            'orders_by_product_type'
        ));
    }

    private function getDateFormatExpression($column, $format)
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            return "strftime('{$format}', {$column})";
        }
        return "DATE_FORMAT({$column}, '{$format}')";
    }

    public function login()
    {
        return view('admin.auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials)) {
            $user = auth()->user();

            // Vérifier si l'utilisateur a un rôle admin (super-admin, admin, gestionnaire, ou vendeur)
            $allowedRoles = ['super-admin', 'admin', 'gestionnaire', 'vendeur'];
            $hasAllowedRole = false;

            foreach ($allowedRoles as $role) {
                if ($user->hasRole($role)) {
                    $hasAllowedRole = true;
                    break;
                }
            }

            // Fallback: vérifier le champ role (ancien système)
            if (!$hasAllowedRole && in_array($user->role, ['admin', 'gestionnaire', 'vendeur'])) {
                $hasAllowedRole = true;
            }

            if ($hasAllowedRole) {
                $request->session()->regenerate();
                return redirect()->intended('/admin');
            } else {
                auth()->logout();
                return back()->withErrors([
                    'email' => 'Accès non autorisé. Seuls les administrateurs peuvent accéder à cette interface.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ]);
    }

    public function logout(Request $request)
    {
        // Logger l'activité de déconnexion si possible
        try {
            if (auth()->check()) {
                activity()
                    ->causedBy(auth()->user())
                    ->log('Déconnexion');
            }
        } catch (\Exception $e) {
            // Ignorer les erreurs de logging
        }

        // Déconnexion propre
        auth()->logout();

        // Invalider la session
        $request->session()->invalidate();

        // Régénérer le token CSRF
        $request->session()->regenerateToken();

        return redirect('/admin/login')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
