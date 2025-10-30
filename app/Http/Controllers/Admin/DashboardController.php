<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderSimple as Order;
use App\Models\Category;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_categories' => Category::count(),
            'pending_orders' => Order::where('status', OrderStatus::PENDING)->count(),
            'active_products' => Product::where('status', 'active')->count(),
            // Chiffre d'affaires : inclut les commandes pending, confirmed, processing, shipped, delivered
            // Exclut seulement les commandes cancelled
            'total_revenue' => Order::whereNotIn('status', [OrderStatus::CANCELLED])->sum('total_amount'),
        ];

        // Commandes récentes - version simplifiée pour éviter l'épuisement mémoire
        $recent_orders = Order::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Produits les plus vendus
        $top_products = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', 'products.price', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Utilisateurs récents
        $recent_users = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_orders',
            'top_products',
            'recent_users'
        ));
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
