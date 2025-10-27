<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Afficher la liste des clients
     */
    public function index(Request $request)
    {
        $query = User::clients()->withCount('orders');

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('prenom', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('numero_telephone', 'like', "%{$search}%");
            });
        }

        // Filtre par quartier
        if ($request->filled('quartier_id')) {
            $query->where('quartier_id', $request->quartier_id);
        }

        // Filtre par date d'inscription
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortFields = ['prenom', 'nom', 'email', 'created_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 20);
        $clients = $query->with(['orders' => function($ordersQuery) {
                $ordersQuery->latest()->take(3); // Dernières 3 commandes
            }])
            ->paginate($perPage)->appends($request->query());

        // Statistiques
        $stats = [
            'total' => User::clients()->count(),
            'with_orders' => User::clients()->has('orders')->count(),
            'new_this_month' => User::clients()->whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.clients.index', compact('clients', 'stats'));
    }

    /**
     * Afficher les détails d'un client avec ses statistiques et commandes
     */
    public function show($id)
    {
        $client = User::clients()
            ->with(['orders' => function($query) {
                $query->with(['items.product.productImages'])->latest();
            }])
            ->findOrFail($id);

        // Appliquer les filtres si présents
        $ordersQuery = $client->orders();

        if (request('status')) {
            $ordersQuery->where('status', request('status'));
        }

        if (request('date_from')) {
            $ordersQuery->whereDate('created_at', '>=', request('date_from'));
        }

        if (request('date_to')) {
            $ordersQuery->whereDate('created_at', '<=', request('date_to'));
        }

        $orders = $ordersQuery->paginate(10);

        // Calculer les statistiques
        $stats = [
            'total' => $client->orders->count(),
            'livrees' => $client->orders->where('status', 'delivered')->count(),
            'encours' => $client->orders->whereIn('status', ['pending', 'confirmed', 'processing', 'shipped'])->count(),
            'annulees' => $client->orders->where('status', 'cancelled')->count(),
            'montant_total' => $client->orders->sum('total_amount'),
            'montant_moyen' => $client->orders->count() > 0 ? $client->orders->avg('total_amount') : 0,
        ];

        // Dernière commande
        $derniere_commande = $client->orders->first();

        return view('admin.clients.show', compact('client', 'stats', 'derniere_commande', 'orders'));
    }

    /**
     * Filtrer les commandes d'un client par statut
     */
    public function filterOrders(Request $request, $id)
    {
        $client = User::clients()->findOrFail($id);

        $query = $client->orders()->with(['items.product.productImages'])->latest();

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.clients.partials.orders_table', compact('orders'))->render()
            ]);
        }

        return view('admin.clients.show', compact('client', 'orders'));
    }

    /**
     * Rechercher des clients
     */
    public function search(Request $request)
    {
        $query = User::clients()->withCount('orders');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('numero_telephone', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.clients.index', compact('clients'));
    }
}
