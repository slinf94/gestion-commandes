<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\LogsActivity;
use App\Enums\OrderStatus;

class Order extends Model
{
    use SoftDeletes, LogsActivity;

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

    /**
     * Attributes to log when the model changes
     */
    protected $logAttributes = [
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
        'admin_notes'
    ];

    /**
     * Attributes to ignore when logging
     */
    protected $logIgnoredAttributes = [
        'order_number', // Généré automatiquement
        'user_id', // Géré séparément
        'processed_by',
        'processed_at'
    ];

    // Relations
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
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
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

    // Accessors
    public function getStatusLabelAttribute()
    {
        if ($this->status instanceof \App\Enums\OrderStatus) {
            return $this->status->value;
        }
        return $this->status;
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

    public function isShipped()
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;
        return $status === 'shipped';
    }

    public function isDelivered()
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;
        return $status === 'delivered';
    }

    public function isCancelled()
    {
        $status = $this->status instanceof \App\Enums\OrderStatus ? $this->status->value : $this->status;
        return $status === 'cancelled';
    }

    /**
     * Obtenir le statut sous forme d'enum
     */
    public function getStatusEnum(): OrderStatus
    {
        return $this->status;
    }

    /**
     * Obtenir le label du statut
     */
    public function getStatusLabel(): string
    {
        return $this->status->getLabel();
    }

    /**
     * Obtenir la classe CSS du statut
     */
    public function getStatusClass(): string
    {
        return $this->status->getBootstrapClass();
    }

    /**
     * Obtenir la couleur du statut
     */
    public function getStatusColor(): string
    {
        return $this->status->getColor();
    }

    /**
     * Obtenir l'icône du statut
     */
    public function getStatusIcon(): string
    {
        return $this->status->getIcon();
    }

    /**
     * Obtenir la description du statut
     */
    public function getStatusDescription(): string
    {
        return $this->status->getDescription();
    }

    /**
     * Vérifier si le statut peut être changé vers un autre
     */
    public function canChangeStatusTo(OrderStatus $newStatus): bool
    {
        return $this->status->canTransitionTo($newStatus);
    }

    /**
     * Obtenir les statuts suivants possibles
     */
    public function getNextPossibleStatuses(): array
    {
        return $this->status->getNextStatuses();
    }

    /**
     * Changer le statut de la commande
     */
    public function changeStatus(OrderStatus $newStatus, ?string $comment = null, ?int $changedBy = null): bool
    {
        if (!$this->canChangeStatusTo($newStatus)) {
            return false;
        }

        $oldStatus = $this->status;
        $oldStatusValue = $oldStatus instanceof \App\Enums\OrderStatus ? $oldStatus->value : $oldStatus;

        // Mettre à jour le statut
        $this->update(['status' => $newStatus]);

        // Enregistrer l'historique
        try {
            $this->statusHistory()->create([
                'previous_status' => $oldStatusValue,
                'new_status' => $newStatus->value,
                'comment' => $comment,
                'changed_by' => $changedBy ?? auth()->id(),
            ]);
        } catch (\Exception $e) {
            \Log::warning('Impossible d\'enregistrer l\'historique des statuts: ' . $e->getMessage());
        }

        return true;
    }

    /**
     * Vérifier si la commande est active
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     * Vérifier si la commande est terminée
     */
    public function isCompleted(): bool
    {
        return $this->status->isCompleted();
    }

    public function getItemCountAttribute()
    {
        return $this->items->sum('quantity');
    }

    // Boot method to generate order number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'CMD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
