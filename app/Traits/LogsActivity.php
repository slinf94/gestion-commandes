<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;
use Spatie\Activitylog\LogOptions;

trait LogsActivity
{
    use SpatieLogsActivity;

    /**
     * Get the options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->logAttributes ?? [])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => $this->getDescriptionForEvent($eventName));
    }

    /**
     * Get the description for the event.
     */
    protected function getDescriptionForEvent(string $eventName): string
    {
        $modelName = class_basename($this);

        // Convertir le nom du modèle en français
        $translations = [
            'User' => 'utilisateur',
            'Product' => 'produit',
            'Order' => 'commande',
            'Category' => 'catégorie',
            'ProductImage' => 'image de produit',
        ];

        $modelDisplayName = $translations[$modelName] ?? strtolower($modelName);

        return match ($eventName) {
            'created' => "créé un nouveau {$modelDisplayName}",
            'updated' => "modifié le {$modelDisplayName}",
            'deleted' => "supprimé le {$modelDisplayName}",
            'restored' => "restauré le {$modelDisplayName}",
            default => "effectué une action sur le {$modelDisplayName}",
        };
    }
}
