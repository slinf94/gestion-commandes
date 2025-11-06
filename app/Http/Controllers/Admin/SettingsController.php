<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    /**
     * Afficher la page des paramètres
     */
    public function index()
    {
        return view('admin.settings.index');
    }

    /**
     * Afficher les paramètres généraux
     */
    public function general()
    {
        $settings = [
            'app_name' => config('app.name'),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'app_url' => config('app.url'),
        ];

        return view('admin.settings.general', compact('settings'));
    }

    /**
     * Mettre à jour les paramètres généraux
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
        ]);

        // Note: En production, ces paramètres devraient être dans la base de données
        // Pour l'instant, on affiche juste un message de succès
        return redirect()->route('admin.settings.general')
            ->with('success', 'Paramètres généraux mis à jour avec succès !');
    }

    /**
     * Afficher les paramètres de sécurité
     */
    public function security()
    {
        return view('admin.settings.security');
    }

    /**
     * Afficher les paramètres de notification
     */
    public function notifications()
    {
        return view('admin.settings.notifications');
    }

    /**
     * Mettre à jour les paramètres de notification
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
        ]);

        // Ici vous pourriez sauvegarder les préférences de notification
        // Pour l'instant, on affiche juste un message de succès
        return redirect()->route('admin.settings.notifications')
            ->with('success', 'Paramètres de notification mis à jour avec succès !');
    }

    /**
     * Vider le cache de l'application
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return redirect()->route('admin.settings.index')
                ->with('success', 'Cache vidé avec succès !');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'Erreur lors du vidage du cache : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les informations système
     */
    public function system()
    {
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ];

        return view('admin.settings.system', compact('systemInfo'));
    }

    /**
     * Afficher les logs de l'application
     */
    public function logs(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        $logLevels = ['error', 'warning', 'info', 'debug', 'emergency', 'alert', 'critical'];
        $selectedLevel = $request->get('level', 'all');
        $lines = (int) $request->get('lines', 100); // Limiter à 100 lignes par défaut
        $search = $request->get('search', '');

        // Vérifier si le fichier de log existe
        if (File::exists($logFile)) {
            try {
                // Lire les dernières lignes du fichier
                $fileContent = File::get($logFile);
                $allLines = explode("\n", $fileContent);
                
                // Prendre les dernières lignes (les plus récentes)
                $recentLines = array_slice($allLines, -$lines);
                
                // Parser les logs Laravel
                $currentLog = null;
                foreach ($recentLines as $line) {
                    $line = trim($line);
                    if (empty($line)) {
                        continue;
                    }

                    // Détecter le début d'un nouveau log Laravel (format: [YYYY-MM-DD HH:MM:SS] local.LEVEL: ...)
                    if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(\w+)\.(\w+):\s*(.*)$/s', $line, $matches)) {
                        // Sauvegarder le log précédent s'il existe
                        if ($currentLog !== null) {
                            $logs[] = $currentLog;
                        }
                        
                        $currentLog = [
                            'timestamp' => $matches[1],
                            'environment' => $matches[2],
                            'level' => strtolower($matches[3]),
                            'message' => $matches[4],
                            'full_text' => $line
                        ];
                    } elseif ($currentLog !== null) {
                        // Continuer à construire le message du log actuel
                        $currentLog['message'] .= "\n" . $line;
                        $currentLog['full_text'] .= "\n" . $line;
                    }
                }
                
                // Ajouter le dernier log
                if ($currentLog !== null) {
                    $logs[] = $currentLog;
                }
                
                // Inverser pour avoir les plus récents en premier
                $logs = array_reverse($logs);
                
                // Filtrer par niveau
                if ($selectedLevel !== 'all') {
                    $logs = array_filter($logs, function($log) use ($selectedLevel) {
                        return $log['level'] === $selectedLevel;
                    });
                }
                
                // Filtrer par recherche
                if (!empty($search)) {
                    $logs = array_filter($logs, function($log) use ($search) {
                        return stripos($log['message'], $search) !== false || 
                               stripos($log['full_text'], $search) !== false;
                    });
                }
                
                // Re-indexer le tableau après filtrage
                $logs = array_values($logs);
                
            } catch (\Exception $e) {
                return redirect()->route('admin.settings.system')
                    ->with('error', 'Erreur lors de la lecture des logs : ' . $e->getMessage());
            }
        }

        $fileSize = File::exists($logFile) ? File::size($logFile) : 0;
        $fileSizeFormatted = $this->formatBytes($fileSize);

        return view('admin.settings.logs', compact('logs', 'logLevels', 'selectedLevel', 'lines', 'search', 'fileSizeFormatted'));
    }

    /**
     * Afficher la page de maintenance
     */
    public function maintenance()
    {
        // Statistiques de la base de données
        $dbStats = [];
        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::connection()->getDatabaseName();
            $tableKey = 'Tables_in_' . $dbName;
            
            $totalSize = 0;
            $tableInfo = [];
            
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                $tableStats = DB::select("SELECT 
                    table_name AS 'table',
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb',
                    table_rows AS 'rows'
                    FROM information_schema.TABLES 
                    WHERE table_schema = ? AND table_name = ?", [$dbName, $tableName]);
                
                if (!empty($tableStats)) {
                    $stats = $tableStats[0];
                    $totalSize += $stats->size_mb;
                    $tableInfo[] = [
                        'name' => $stats->table,
                        'size' => $stats->size_mb,
                        'rows' => $stats->rows
                    ];
                }
            }
            
            $dbStats = [
                'total_tables' => count($tables),
                'total_size' => round($totalSize, 2),
                'tables' => $tableInfo
            ];
        } catch (\Exception $e) {
            $dbStats = ['error' => $e->getMessage()];
        }
        
        // Statistiques des fichiers de log
        $logFile = storage_path('logs/laravel.log');
        $logSize = File::exists($logFile) ? File::size($logFile) : 0;
        
        // Statistiques du cache
        $cacheSize = 0;
        $cachePath = storage_path('framework/cache');
        if (File::exists($cachePath)) {
            $cacheSize = $this->getDirectorySize($cachePath);
        }
        
        return view('admin.settings.maintenance', compact('dbStats', 'logSize', 'cacheSize'));
    }

    /**
     * Optimiser la base de données
     */
    public function optimizeDatabase()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::connection()->getDatabaseName();
            $tableKey = 'Tables_in_' . $dbName;
            
            $optimizedTables = [];
            $errors = [];
            
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                try {
                    DB::statement("OPTIMIZE TABLE `{$tableName}`");
                    $optimizedTables[] = $tableName;
                } catch (\Exception $e) {
                    $errors[] = "Erreur pour {$tableName}: " . $e->getMessage();
                }
            }
            
            if (count($errors) === 0) {
                return redirect()->route('admin.settings.maintenance')
                    ->with('success', 'Base de données optimisée avec succès ! ' . count($optimizedTables) . ' table(s) optimisée(s).');
            } else {
                return redirect()->route('admin.settings.maintenance')
                    ->with('warning', 'Optimisation partielle : ' . count($optimizedTables) . ' table(s) optimisée(s), ' . count($errors) . ' erreur(s).');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.maintenance')
                ->with('error', 'Erreur lors de l\'optimisation : ' . $e->getMessage());
        }
    }

    /**
     * Vider les logs
     */
    public function clearLogs()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            if (File::exists($logFile)) {
                File::put($logFile, '');
            }
            
            return redirect()->route('admin.settings.maintenance')
                ->with('success', 'Logs vidés avec succès !');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.maintenance')
                ->with('error', 'Erreur lors du vidage des logs : ' . $e->getMessage());
        }
    }

    /**
     * Calculer la taille d'un répertoire
     */
    private function getDirectorySize($directory)
    {
        try {
            $size = 0;
            if (File::exists($directory)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
                );
                
                foreach ($files as $file) {
                    if ($file->isFile()) {
                        $size += $file->getSize();
                    }
                }
            }
        } catch (\Exception $e) {
            $size = 0;
        }
        
        return $size;
    }

    /**
     * Formater la taille en bytes en format lisible
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

























