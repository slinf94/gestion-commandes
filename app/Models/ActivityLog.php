<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity;

class ActivityLog extends Activity
{
    /**
     * Get the activity description with user context.
     */
    public function getDescriptionWithUserAttribute(): string
    {
        $userName = $this->causer ? $this->causer->full_name ?? $this->causer->name ?? 'Utilisateur inconnu' : 'Système';
        return "{$userName} a {$this->description}";
    }

    /**
     * Get the formatted properties for display.
     */
    public function getFormattedPropertiesAttribute(): array
    {
        $properties = $this->properties ?? [];

        // S'assurer que $properties est un tableau, pas une Collection
        if ($properties instanceof \Illuminate\Support\Collection) {
            $properties = $properties->toArray();
        } elseif (!is_array($properties)) {
            $properties = [];
        }

        if (isset($properties['attributes'])) {
            $attributes = $properties['attributes'];
            // S'assurer que attributes est un tableau
            if ($attributes instanceof \Illuminate\Support\Collection) {
                $attributes = $attributes->toArray();
            }
            $properties['attributes'] = $this->formatAttributes($attributes);
        }

        if (isset($properties['old'])) {
            $old = $properties['old'];
            // S'assurer que old est un tableau
            if ($old instanceof \Illuminate\Support\Collection) {
                $old = $old->toArray();
            }
            $properties['old'] = $this->formatAttributes($old);
        }

        return $properties;
    }

    /**
     * Format attributes for display (hide sensitive data).
     */
    private function formatAttributes($attributes): array
    {
        // S'assurer que $attributes est un tableau
        if ($attributes instanceof \Illuminate\Support\Collection) {
            $attributes = $attributes->toArray();
        } elseif (!is_array($attributes)) {
            return [];
        }

        $sensitiveFields = ['password', 'password_confirmation', 'remember_token', 'api_token'];

        $formatted = [];
        foreach ($attributes as $key => $value) {
            if (in_array($key, $sensitiveFields)) {
                $formatted[$key] = '***';
            } elseif (is_array($value)) {
                $formatted[$key] = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } elseif ($value instanceof \Illuminate\Support\Collection) {
                $formatted[$key] = json_encode($value->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                $formatted[$key] = $value;
            }
        }

        return $formatted;
    }

    /**
     * Get the activity type (created, updated, deleted, etc.).
     */
    public function getActivityTypeAttribute(): string
    {
        $description = strtolower($this->description);

        if (str_contains($description, 'created')) {
            return 'created';
        }

        if (str_contains($description, 'updated')) {
            return 'updated';
        }

        if (str_contains($description, 'deleted')) {
            return 'deleted';
        }

        if (str_contains($description, 'restored')) {
            return 'restored';
        }

        return 'other';
    }

    /**
     * Get the activity type badge class.
     */
    public function getActivityTypeBadgeClassAttribute(): string
    {
        return match ($this->activity_type) {
            'created' => 'bg-success',
            'updated' => 'bg-primary',
            'deleted' => 'bg-danger',
            'restored' => 'bg-warning',
            default => 'bg-secondary',
        };
    }
}
