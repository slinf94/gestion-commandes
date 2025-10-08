<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use App\Notifications\AccountActivatedNotification;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withTrashed();

        // Filtrage par quartier
        if ($request->has('quartier') && $request->quartier) {
            $query->where('quartier', $request->quartier);
        }

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

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['orders']);
        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'numero_telephone' => 'required|string|max:20|unique:users,numero_telephone',
            'numero_whatsapp' => 'nullable|string|max:20',
            'quartier' => 'nullable|string|max:100',
            'localisation' => 'nullable|string|max:255',
            'role' => 'required|in:client,admin,gestionnaire',
            'status' => 'required|in:pending,active,suspended,inactive',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'numero_telephone' => $request->numero_telephone,
            'numero_whatsapp' => $request->numero_whatsapp,
            'quartier' => $request->quartier,
            'localisation' => $request->localisation,
            'role' => $request->role,
            'status' => $request->status,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Utilisateur créé avec succès');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'numero_telephone' => 'required|string|max:20|unique:users,numero_telephone,' . $user->id,
            'numero_whatsapp' => 'nullable|string|max:20',
            'quartier' => 'nullable|string|max:100',
            'localisation' => 'nullable|string|max:255',
            'role' => 'required|in:client,admin,gestionnaire',
            'status' => 'required|in:pending,active,suspended,inactive',
        ]);

        // Vérifier si le statut passe de 'pending' à 'active'
        $wasActivated = ($user->status === 'pending' && $request->status === 'active');

        $user->update($request->only([
            'nom', 'prenom', 'email', 'numero_telephone', 'numero_whatsapp', 'quartier', 'localisation', 'role', 'status'
        ]));

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Envoyer l'email de notification si le compte vient d'être activé
        if ($wasActivated) {
            try {
                $user->notify(new AccountActivatedNotification($user));
                \Log::info('Email d\'activation envoyé à l\'utilisateur: ' . $user->email);
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'envoi de l\'email d\'activation: ' . $e->getMessage());
                // On continue quand même, l'activation est réussie même si l'email n'est pas envoyé
            }
        }

        $message = 'Utilisateur mis à jour avec succès';
        if ($wasActivated) {
            $message .= '. Un email de confirmation a été envoyé au client.';
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', $message);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur restauré avec succès');
    }

    public function byQuartier()
    {
        // Statistiques générales
        $totalClients = User::where('role', 'client')->count();
        $activeClients = User::where('role', 'client')->where('status', 'active')->count();
        $pendingClients = User::where('role', 'client')->where('status', 'pending')->count();
        $clientsSansQuartier = User::where('role', 'client')->whereNull('quartier')->count();

        // Statistiques par quartier
        $quartiersStats = [];
        $quartiers = \App\Models\Quartier::getQuartiers();

        foreach ($quartiers as $quartier) {
            $totalClientsQuartier = User::where('role', 'client')->where('quartier', $quartier)->count();
            $activeClientsQuartier = User::where('role', 'client')->where('quartier', $quartier)->where('status', 'active')->count();

            $quartiersStats[] = [
                'quartier' => $quartier,
                'total_clients' => $totalClientsQuartier,
                'active_clients' => $activeClientsQuartier,
            ];
        }

        // Trier par nombre de clients décroissant
        usort($quartiersStats, function($a, $b) {
            return $b['total_clients'] - $a['total_clients'];
        });

        $quartiersAvecClients = collect($quartiersStats)->where('total_clients', '>', 0)->count();

        return view('admin.users.by-quartier', compact(
            'totalClients',
            'activeClients',
            'pendingClients',
            'clientsSansQuartier',
            'quartiersStats',
            'quartiersAvecClients'
        ));
    }

    /**
     * Exporter la liste des clients en CSV
     */
    public function exportCsv(Request $request)
    {
        $query = User::where('role', 'client');

        // Appliquer les mêmes filtres que dans index()
        if ($request->has('quartier') && $request->quartier) {
            $query->where('quartier', $request->quartier);
        }

        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $clients = $query->orderBy('created_at', 'desc')->get();

        $filename = 'clients_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($clients) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Nom',
                'Prénom',
                'Email',
                'Téléphone',
                'WhatsApp',
                'Quartier',
                'Localisation',
                'Statut',
                'Date d\'inscription',
                'Dernière connexion'
            ]);

            // Données des clients
            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->id,
                    $client->nom,
                    $client->prenom,
                    $client->email,
                    $client->numero_telephone,
                    $client->numero_whatsapp ?? '',
                    $client->quartier ?? 'Non défini',
                    $client->localisation ?? '',
                    ucfirst($client->status),
                    $client->created_at ? $client->created_at->format('d/m/Y H:i') : '',
                    $client->updated_at ? $client->updated_at->format('d/m/Y H:i') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exporter la liste des clients par quartier en CSV
     */
    public function exportByQuartierCsv()
    {
        $quartiers = \App\Models\Quartier::getQuartiers();
        $filename = 'clients_par_quartier_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($quartiers) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'Quartier',
                'Total Clients',
                'Clients Actifs',
                'Clients En Attente',
                'Taux d\'Activité (%)',
                'Dernière Inscription'
            ]);

            // Données par quartier
            foreach ($quartiers as $quartier) {
                $totalClients = User::where('role', 'client')->where('quartier', $quartier)->count();
                $activeClients = User::where('role', 'client')->where('quartier', $quartier)->where('status', 'active')->count();
                $pendingClients = User::where('role', 'client')->where('quartier', $quartier)->where('status', 'pending')->count();
                $tauxActivite = $totalClients > 0 ? round(($activeClients / $totalClients) * 100, 1) : 0;

                $derniereInscription = User::where('role', 'client')
                    ->where('quartier', $quartier)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $dateDerniereInscription = $derniereInscription ? $derniereInscription->created_at->format('d/m/Y') : 'Aucune';

                fputcsv($file, [
                    $quartier,
                    $totalClients,
                    $activeClients,
                    $pendingClients,
                    $tauxActivite,
                    $dateDerniereInscription
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exporter les détails des clients d'un quartier spécifique
     */
    public function exportQuartierClientsCsv($quartier)
    {
        $clients = User::where('role', 'client')
            ->where('quartier', $quartier)
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'clients_' . str_replace(' ', '_', $quartier) . '_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($clients, $quartier) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Nom',
                'Prénom',
                'Email',
                'Téléphone',
                'WhatsApp',
                'Localisation',
                'Statut',
                'Date d\'inscription',
                'Dernière connexion'
            ]);

            // Données des clients
            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->id,
                    $client->nom,
                    $client->prenom,
                    $client->email,
                    $client->numero_telephone,
                    $client->numero_whatsapp ?? '',
                    $client->localisation ?? '',
                    ucfirst($client->status),
                    $client->created_at ? $client->created_at->format('d/m/Y H:i') : '',
                    $client->updated_at ? $client->updated_at->format('d/m/Y H:i') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
