<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\OrderStatus;

class OrderSimple extends Model
{
    use SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'shipping_cost',
        'total_amount',
        'delivery_address',
        'delivery_date',
        'delivery_time_slot',
        'notes',
        'admin_notes',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'delivery_address' => 'array',
        'delivery_date' => 'date:Y-m-d',
        'processed_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'status' => OrderStatus::class,
    ];

    // Relations simplifiées
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed()->withDefault([
            'full_name' => 'Utilisateur supprimé',
            'email' => 'N/A',
            'numero_telephone' => 'N/A',
            'localisation' => 'N/A',
        ]);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Accessors simplifiés
    public function getStatusLabelAttribute()
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;

        return match($status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'processing' => 'En cours de traitement',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default => ucfirst($status)
        };
    }

    public function getStatusClassAttribute()
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;

        return match($status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'success',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    // Méthodes pour compatibilité avec les vues
    public function getStatusClass()
    {
        return $this->getStatusClassAttribute();
    }

    public function getStatusLabel()
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;

        return match($status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'processing' => 'En cours de traitement',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default => ucfirst($status)
        };
    }

    public function getStatusIcon()
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;

        return match($status) {
            'pending' => 'fas fa-clock',
            'confirmed' => 'fas fa-check-circle',
            'processing' => 'fas fa-cog fa-spin',
            'shipped' => 'fas fa-shipping-fast',
            'delivered' => 'fas fa-check-double',
            'cancelled' => 'fas fa-times-circle',
            default => 'fas fa-question-circle'
        };
    }

    public function getStatusDescription()
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;

        return match($status) {
            'pending' => 'Commande en attente de confirmation',
            'confirmed' => 'Commande confirmée et en cours de préparation',
            'processing' => 'Commande en cours de préparation',
            'shipped' => 'Commande expédiée et en cours de livraison',
            'delivered' => 'Commande livrée avec succès',
            'cancelled' => 'Commande annulée',
            default => 'Statut inconnu'
        };
    }

    // Helper methods
    public function isPending()
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;
        return $status === 'pending';
    }

    public function isConfirmed()
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;
        return $status === 'confirmed';
    }

    public function isProcessing()
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;
        return $status === 'processing';
    }

    public function isActive()
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;
        return in_array($status, ['pending', 'confirmed', 'processing', 'shipped']);
    }

    public function isCompleted()
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;
        return in_array($status, ['delivered', 'completed']);
    }

    public function getNextPossibleStatuses()
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;

        $statusStrings = match($status) {
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered'],
            'delivered' => [],
            'cancelled' => [],
            default => []
        };

        // Convertir les chaînes en objets enum
        return array_map(function($statusString) {
            try {
                return \App\Enums\OrderStatus::from($statusString);
            } catch (\ValueError $e) {
                return null;
            }
        }, array_filter($statusStrings));
    }

    public function canChangeStatusTo($newStatus)
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;
        $nextStatuses = $this->getNextPossibleStatuses();

        if (is_object($newStatus) && method_exists($newStatus, 'value')) {
            $newStatus = $newStatus->value;
        }

        return in_array($newStatus, array_map(function($s) { return $s->value; }, $nextStatuses));
    }

    public function changeStatus($newStatus, $comment = null, $changedBy = null)
    {
        if (!$this->canChangeStatusTo($newStatus)) {
            return false;
        }

        $oldStatus = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;

        if (is_object($newStatus) && method_exists($newStatus, 'value')) {
            $newStatusValue = $newStatus->value;
        } else {
            $newStatusValue = $newStatus;
        }

        // Mettre à jour le statut
        $this->update(['status' => $newStatusValue]);

        // Enregistrer l'historique si la table existe
        try {
            $this->statusHistory()->create([
                'previous_status' => $oldStatus,
                'new_status' => $newStatusValue,
                'comment' => $comment,
                'changed_by' => $changedBy ?? auth()->id(),
            ]);
        } catch (\Exception $e) {
            // Si la table n'existe pas ou s'il y a une erreur, continuer sans l'historique
            \Log::warning('Impossible d\'enregistrer l\'historique des statuts: ' . $e->getMessage());
        }

        return true;
    }
}
