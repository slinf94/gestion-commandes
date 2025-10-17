<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActivityLog;
use App\Helpers\ActivityLogger;

class CleanupActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity-log:cleanup {--days=30 : Number of days to keep logs} {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old activity logs based on the configured retention period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info("🧹 Nettoyage des logs d'activité...");
        $this->line("");

        // Vérifier si le nettoyage est activé
        if (!config('activitylog.cleanup.enabled', true)) {
            $this->warn("⚠️  Le nettoyage automatique est désactivé dans la configuration.");
            $this->line("Pour l'activer, définissez ACTIVITY_LOG_CLEANUP_ENABLED=true dans votre fichier .env");
            return 1;
        }

        // Calculer la date de coupure
        $cutoffDate = now()->subDays($days);
        
        $this->line("📅 Suppression des logs plus anciens que : {$cutoffDate->format('d/m/Y H:i:s')}");
        $this->line("📊 Période de rétention : {$days} jours");
        $this->line("");

        // Compter les logs à supprimer
        $logsToDelete = ActivityLog::where('created_at', '<', $cutoffDate);
        $count = $logsToDelete->count();

        if ($count === 0) {
            $this->info("✅ Aucun log à supprimer trouvé.");
            return 0;
        }

        $this->line("🔍 Logs trouvés à supprimer : {$count}");
        $this->line("");

        if ($dryRun) {
            $this->warn("🔍 MODE DRY-RUN - Aucune suppression réelle effectuée");
            $this->line("");
            
            // Afficher un échantillon des logs qui seraient supprimés
            $sampleLogs = $logsToDelete->limit(10)->get();
            
            if ($sampleLogs->count() > 0) {
                $this->line("📋 Échantillon des logs qui seraient supprimés :");
                $this->table(
                    ['ID', 'Description', 'Utilisateur', 'Date'],
                    $sampleLogs->map(function ($log) {
                        return [
                            $log->id,
                            $log->description,
                            $log->causer ? $log->causer->full_name : 'Système',
                            $log->created_at->format('d/m/Y H:i:s'),
                        ];
                    })
                );
            }
            
            return 0;
        }

        // Demander confirmation
        if (!$this->confirm("Êtes-vous sûr de vouloir supprimer {$count} logs d'activité ?")) {
            $this->info("❌ Opération annulée.");
            return 1;
        }

        $this->line("");
        $this->info("🗑️  Suppression en cours...");

        // Créer une barre de progression
        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        // Supprimer par lots pour éviter les problèmes de mémoire
        $batchSize = 1000;
        $deletedCount = 0;

        while ($logsToDelete->count() > 0) {
            $batch = $logsToDelete->limit($batchSize)->get();
            
            foreach ($batch as $log) {
                $log->delete();
                $deletedCount++;
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->line("");
        $this->line("");

        // Logger l'action de nettoyage
        ActivityLogger::logSystemAction(
            "Nettoyage automatique des logs d'activité effectué",
            [
                'deleted_count' => $deletedCount,
                'retention_days' => $days,
                'cutoff_date' => $cutoffDate->toISOString(),
                'command' => 'activity-log:cleanup'
            ]
        );

        // Afficher les statistiques finales
        $this->info("✅ Nettoyage terminé avec succès !");
        $this->line("📊 Statistiques :");
        $this->line("   • Logs supprimés : {$deletedCount}");
        $this->line("   • Période de rétention : {$days} jours");
        $this->line("   • Date de coupure : {$cutoffDate->format('d/m/Y H:i:s')}");
        $this->line("");

        // Afficher les statistiques actuelles
        $totalLogs = ActivityLog::count();
        $oldestLog = ActivityLog::oldest('created_at')->first();
        $newestLog = ActivityLog::latest('created_at')->first();

        $this->line("📈 Statistiques actuelles :");
        $this->line("   • Total des logs restants : {$totalLogs}");
        
        if ($oldestLog) {
            $this->line("   • Plus ancien log : {$oldestLog->created_at->format('d/m/Y H:i:s')}");
        }
        
        if ($newestLog) {
            $this->line("   • Plus récent log : {$newestLog->created_at->format('d/m/Y H:i:s')}");
        }

        $this->line("");
        $this->info("🎉 Opération terminée avec succès !");

        return 0;
    }
}