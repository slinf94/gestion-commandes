<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminActivityController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::orderBy('created_at', 'desc');

        // Filtrage par utilisateur
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id)
                  ->where('causer_type', User::class);
        }

        // Filtrage par type de sujet
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Filtrage par nom de log
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        // Filtrage par type d'activité
        if ($request->filled('activity_type')) {
            $query->where('description', 'like', '%' . $request->activity_type . '%');
        }

        // Filtrage par date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Recherche dans la description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%");
            });
        }

        $activities = $query->paginate(20);

        // Données pour les filtres
        $users = collect(); // Temporairement désactivé pour éviter les problèmes
        $subjectTypes = ActivityLog::select('subject_type')
            ->distinct()
            ->whereNotNull('subject_type')
            ->orderBy('subject_type')
            ->pluck('subject_type');

        $logNames = ActivityLog::select('log_name')
            ->distinct()
            ->whereNotNull('log_name')
            ->orderBy('log_name')
            ->pluck('log_name');

        $activityTypes = [
            'created' => 'Créé',
            'updated' => 'Modifié',
            'deleted' => 'Supprimé',
            'restored' => 'Restauré',
            'logged_in' => 'Connexion',
            'logged_out' => 'Déconnexion'
        ];

        return view('admin.activity_logs.index', compact(
            'activities',
            'users',
            'subjectTypes',
            'logNames',
            'activityTypes'
        ));
    }

    /**
     * Display the specified activity log.
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load(['causer', 'subject']);

        return view('admin.activity_logs.show', compact('activityLog'));
    }

    /**
     * Get activity logs for AJAX requests.
     */
    public function getLogs(Request $request)
    {
        $query = ActivityLog::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc');

        // Appliquer les mêmes filtres que dans index()
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id)
                  ->where('causer_type', User::class);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        if ($request->filled('activity_type')) {
            $query->where('description', 'like', '%' . $request->activity_type . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%")
                  ->orWhereHas('causer', function($subQuery) use ($search) {
                      $subQuery->where('nom', 'like', "%{$search}%")
                               ->orWhere('prenom', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $activities = $query->paginate(20);

        return response()->json([
            'html' => view('admin.activity_logs.partials.activities_table', compact('activities'))->render(),
            'pagination' => view('admin.activity_logs.partials.pagination', compact('activities'))->render()
        ]);
    }

    /**
     * Get statistics for activity logs.
     */
    public function statistics()
    {
        $stats = [
            'total_activities' => ActivityLog::count(),
            'today_activities' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week_activities' => ActivityLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month_activities' => ActivityLog::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Top utilisateurs par activité
        $topUsers = ActivityLog::select('causer_id')
            ->whereNotNull('causer_id')
            ->where('causer_type', User::class)
            ->selectRaw('COUNT(*) as activity_count')
            ->groupBy('causer_id')
            ->orderBy('activity_count', 'desc')
            ->limit(5)
            ->get()
            ->load('causer');

        // Activités par type
        $activitiesByType = ActivityLog::selectRaw('
                CASE
                    WHEN description LIKE "%créé%" THEN "created"
                    WHEN description LIKE "%modifié%" THEN "updated"
                    WHEN description LIKE "%supprimé%" THEN "deleted"
                    WHEN description LIKE "%restauré%" THEN "restored"
                    WHEN description LIKE "%connecté%" THEN "logged_in"
                    WHEN description LIKE "%déconnecté%" THEN "logged_out"
                    ELSE "other"
                END as activity_type,
                COUNT(*) as count
            ')
            ->groupBy('activity_type')
            ->orderBy('count', 'desc')
            ->get();

        // Activités par jour (derniers 7 jours)
        $activitiesByDay = ActivityLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'stats' => $stats,
            'topUsers' => $topUsers,
            'activitiesByType' => $activitiesByType,
            'activitiesByDay' => $activitiesByDay
        ]);
    }

    /**
     * Clean up old activity logs.
     */
    public function cleanup(Request $request)
    {
        $days = $request->input('days', 30);

        $deletedCount = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        return redirect()->back()->with('success',
            "Nettoyage terminé : {$deletedCount} activités supprimées (plus anciennes que {$days} jours)"
        );
    }

    /**
     * Export activity logs to CSV.
     */
    public function exportCsv(Request $request)
    {
        try {
            // Version simplifiée pour éviter les problèmes de mémoire
            $query = ActivityLog::query();

            // Appliquer les filtres de base
            if ($request->filled('user_id')) {
                $query->where('causer_id', $request->user_id)
                      ->where('causer_type', User::class);
            }

            if ($request->filled('subject_type')) {
                $query->where('subject_type', $request->subject_type);
            }

            if ($request->filled('log_name')) {
                $query->where('log_name', $request->log_name);
            }

            if ($request->filled('activity_type')) {
                $query->where('description', 'like', '%' . $request->activity_type . '%');
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('log_name', 'like', "%{$search}%");
                });
            }

            $filename = 'activity_logs_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public',
            ];

            $callback = function() use ($query) {
                $file = fopen('php://output', 'w');
                
                // Ajouter BOM pour UTF-8
                fputs($file, "\xEF\xBB\xBF");

                // En-têtes CSV
                fputcsv($file, [
                    'ID',
                    'Date',
                    'Utilisateur ID',
                    'Action',
                    'Modèle',
                    'Objet ID',
                    'Log Name',
                    'Propriétés'
                ]);

                // Traiter les données par chunks pour éviter les problèmes de mémoire
                $query->orderBy('created_at', 'desc')->chunk(1000, function($activities) use ($file) {
                    foreach ($activities as $activity) {
                        $properties = '';
                        try {
                            $properties = is_array($activity->properties) 
                                ? json_encode($activity->properties, JSON_UNESCAPED_UNICODE) 
                                : $activity->properties;
                        } catch (\Exception $e) {
                            $properties = 'Erreur encodage';
                        }

                        fputcsv($file, [
                            $activity->id,
                            $activity->created_at ? $activity->created_at->format('d/m/Y H:i:s') : 'N/A',
                            $activity->causer_id ?? 'Système',
                            $activity->description ?? 'N/A',
                            $activity->subject_type ? class_basename($activity->subject_type) : 'N/A',
                            $activity->subject_id ?? 'N/A',
                            $activity->log_name ?? 'default',
                            $properties
                        ]);
                    }
                });

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'export des journaux d\'activité: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 
                'Erreur lors de l\'export: ' . $e->getMessage()
            );
        }
    }
}
