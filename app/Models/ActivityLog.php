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

        if (isset($properties['attributes'])) {
            $properties['attributes'] = $this->formatAttributes($properties['attributes']);
        }

        if (isset($properties['old'])) {
            $properties['old'] = $this->formatAttributes($properties['old']);
        }

        return $properties;
    }

    /**
     * Format attributes for display (hide sensitive data).
     */
    private function formatAttributes(array $attributes): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'remember_token', 'api_token'];

        $formatted = [];
        foreach ($attributes as $key => $value) {
            if (in_array($key, $sensitiveFields)) {
                $formatted[$key] = '***';
            } elseif (is_array($value)) {
                $formatted[$key] = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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
