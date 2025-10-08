<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quartier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuartierController extends Controller
{
    /**
     * Afficher la liste des quartiers avec statistiques
     */
    public function index()
    {
        $quartiers = Quartier::getQuartierStats();

        // Statistiques générales
        $stats = [
            'total_quartiers' => count($quartiers),
            'total_clients' => User::where('role', 'client')->count(),
            'clients_avec_quartier' => User::where('role', 'client')->whereNotNull('quartier')->count(),
            'clients_sans_quartier' => User::where('role', 'client')->whereNull('quartier')->count(),
        ];

        return view('admin.quartiers.index', compact('quartiers', 'stats'));
    }

    /**
     * Afficher les détails d'un quartier avec ses clients
     */
    public function show($quartier)
    {
        $clients = Quartier::getClientsByQuartier($quartier)->orderBy('created_at', 'desc')->get();
        $activeClients = Quartier::getActiveClientsByQuartier($quartier)->get();

        // Statistiques du quartier
        $stats = [
            'total_clients' => $clients->count(),
            'clients_actifs' => $activeClients->count(),
            'clients_en_attente' => $clients->where('status', 'pending')->count(),
            'clients_suspendus' => $clients->where('status', 'suspended')->count(),
            'derniere_inscription' => $clients->max('created_at'),
        ];

        // Clients récents (5 derniers)
        $recentClients = $clients->take(5);

        // Créer un objet quartier pour la vue
        $quartierData = (object) [
            'nom' => $quartier,
            'clients' => $clients,
            'active_clients_count' => $activeClients->count(),
            'clients_count' => $clients->count()
        ];

        return view('admin.quartiers.show', compact('quartierData', 'stats', 'recentClients'));
    }

    /**
     * Afficher le formulaire de création d'un quartier
     */
    public function create()
    {
        return view('admin.quartiers.create');
    }

    /**
     * Créer un nouveau quartier
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100|unique:quartiers,nom',
            'ville' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $quartier = Quartier::create($request->all());

        return redirect()->route('admin.quartiers.show', $quartier)
            ->with('success', 'Quartier créé avec succès');
    }

    /**
     * Afficher le formulaire d'édition d'un quartier
     */
    public function edit(Quartier $quartier)
    {
        return view('admin.quartiers.edit', compact('quartier'));
    }

    /**
     * Mettre à jour un quartier
     */
    public function update(Request $request, Quartier $quartier)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100|unique:quartiers,nom,' . $quartier->id,
            'ville' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $quartier->update($request->all());

        return redirect()->route('admin.quartiers.show', $quartier)
            ->with('success', 'Quartier mis à jour avec succès');
    }

    /**
     * Supprimer un quartier
     */
    public function destroy(Quartier $quartier)
    {
        // Vérifier s'il y a des clients dans ce quartier
        if ($quartier->clients()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer ce quartier car il contient des clients. Veuillez d\'abord réassigner les clients à d\'autres quartiers.');
        }

        $quartier->delete();

        return redirect()->route('admin.quartiers.index')
            ->with('success', 'Quartier supprimé avec succès');
    }

    /**
     * Lister les clients d'un quartier avec filtres
     */
    public function clients(Quartier $quartier, Request $request)
    {
        $query = $quartier->clients()->with(['orders', 'quartier']);

        // Filtrage par statut
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filtrage par date d'inscription
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
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

        $clients = $query->paginate(20);

        return view('admin.quartiers.clients', compact('quartier', 'clients'));
    }

    /**
     * Réassigner un client à un autre quartier
     */
    public function reassignClient(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'quartier_id' => 'required|exists:quartiers,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $oldQuartier = $user->quartier;
        $newQuartier = Quartier::find($request->quartier_id);

        $user->update(['quartier_id' => $request->quartier_id]);

        return back()->with('success',
            "Client {$user->full_name} réassigné de {$oldQuartier->nom} vers {$newQuartier->nom}");
    }

    /**
     * Statistiques par quartier
     */
    public function statistics()
    {
        $quartiers = Quartier::withCount(['clients', 'activeClients'])
            ->with(['clients' => function($query) {
                $query->select('quartier_id', 'created_at');
            }])
            ->active()
            ->ordered()
            ->get();

        // Statistiques par mois pour chaque quartier
        $monthlyStats = [];
        foreach ($quartiers as $quartier) {
            $monthlyStats[$quartier->id] = $quartier->clients()
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get();
        }

        return view('admin.quartiers.statistics', compact('quartiers', 'monthlyStats'));
    }

    /**
     * Export des clients par quartier
     */
    public function exportClients(Quartier $quartier)
    {
        $clients = $quartier->clients()
            ->with(['orders'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = "clients_quartier_{$quartier->nom}_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($clients) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'Nom', 'Prénom', 'Email', 'Téléphone', 'Ville', 'Quartier',
                'Statut', 'Date d\'inscription', 'Nombre de commandes', 'Dernière commande'
            ]);

            // Données
            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->nom,
                    $client->prenom,
                    $client->email,
                    $client->numero_telephone,
                    $client->ville,
                    $client->quartier->nom ?? 'Non défini',
                    $client->status,
                    $client->created_at->format('d/m/Y H:i'),
                    $client->orders->count(),
                    $client->orders->max('created_at') ? $client->orders->max('created_at')->format('d/m/Y') : 'Aucune'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
