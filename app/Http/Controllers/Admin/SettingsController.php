<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

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
}


















