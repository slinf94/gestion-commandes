<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Afficher le dashboard admin
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'revenue' => Order::where('status', 'delivered')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'active_users' => User::where('status', 'active')->count(),
        ];

        $recent_orders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recent_users = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'recent_users'));
    }

    /**
     * Afficher la gestion des utilisateurs
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Filtres
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Afficher la gestion des produits
     */
    public function products(Request $request)
    {
        $query = Product::with('category');

        // Filtres
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.products', compact('products'));
    }

    /**
     * Afficher la gestion des commandes
     */
    public function orders(Request $request)
    {
        $query = Order::with(['user', 'items']);

        // Filtres
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('nom', 'like', '%' . $request->search . '%')
                               ->orWhere('prenom', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders', compact('orders'));
    }

    /**
     * Afficher les statistiques
     */
    public function statistics()
    {
        $stats = [
            'users_by_month' => User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get(),
            
            'orders_by_status' => Order::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get(),
            
            'revenue_by_month' => Order::selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
                ->whereYear('created_at', date('Y'))
                ->where('status', 'delivered')
                ->groupBy('month')
                ->get(),
        ];

        return view('admin.statistics', compact('stats'));
    }

    /**
     * Afficher les notifications
     */
    public function notifications()
    {
        $notifications = collect([
            (object) [
                'id' => 1,
                'title' => 'Nouvelle commande',
                'message' => 'Commande #CMD-001 reçue',
                'type' => 'order',
                'created_at' => now()->subMinutes(5),
                'is_read' => false,
            ],
            (object) [
                'id' => 2,
                'title' => 'Stock faible',
                'message' => 'iPhone 15 Pro - Stock: 3 unités',
                'type' => 'stock',
                'created_at' => now()->subHours(2),
                'is_read' => true,
            ],
            (object) [
                'id' => 3,
                'title' => 'Nouvel utilisateur',
                'message' => 'Jean Dupont s\'est inscrit',
                'type' => 'user',
                'created_at' => now()->subHours(5),
                'is_read' => true,
            ],
        ]);

        return view('admin.notifications', compact('notifications'));
    }

    /**
     * Afficher les paramètres
     */
    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Afficher la page de connexion admin
     */
    public function login()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Traiter la connexion admin
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if ($user->role === 'admin' || $user->role === 'super_admin') {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Accès non autorisé. Seuls les administrateurs peuvent se connecter.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ]);
    }

    /**
     * Déconnexion admin
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}
