<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderStatus;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'previous_status',
        'new_status',
        'comment',
        'changed_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relation avec la commande
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relation avec l'utilisateur qui a fait le changement
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Obtenir le statut précédent sous forme d'enum
     */
    public function getPreviousStatusEnum(): ?OrderStatus
    {
        return $this->previous_status ? OrderStatus::from($this->previous_status) : null;
    }

    /**
     * Obtenir le nouveau statut sous forme d'enum
     */
    public function getNewStatusEnum(): OrderStatus
    {
        return OrderStatus::from($this->new_status);
    }

    /**
     * Obtenir le label du statut précédent
     */
    public function getPreviousStatusLabel(): string
    {
        return $this->getPreviousStatusEnum()?->getLabel() ?? 'N/A';
    }

    /**
     * Obtenir le label du nouveau statut
     */
    public function getNewStatusLabel(): string
    {
        return $this->getNewStatusEnum()->getLabel();
    }

    /**
     * Obtenir la classe CSS du statut précédent
     */
    public function getPreviousStatusClass(): string
    {
        return $this->getPreviousStatusEnum()?->getBootstrapClass() ?? 'secondary';
    }

    /**
     * Obtenir la classe CSS du nouveau statut
     */
    public function getNewStatusClass(): string
    {
        return $this->getNewStatusEnum()->getBootstrapClass();
    }

    /**
     * Obtenir l'icône du statut précédent
     */
    public function getPreviousStatusIcon(): string
    {
        return $this->getPreviousStatusEnum()?->getIcon() ?? '❓';
    }

    /**
     * Obtenir l'icône du nouveau statut
     */
    public function getNewStatusIcon(): string
    {
        return $this->getNewStatusEnum()->getIcon();
    }

    /**
     * Obtenir le nom de l'utilisateur qui a fait le changement
     */
    public function getChangedByName(): string
    {
        if (!$this->changedBy) {
            return 'Système';
        }

        return $this->changedBy->nom . ' ' . $this->changedBy->prenom;
    }

    /**
     * Obtenir une description du changement
     */
    public function getChangeDescription(): string
    {
        $from = $this->getPreviousStatusLabel();
        $to = $this->getNewStatusLabel();

        return "Changement de statut de \"{$from}\" vers \"{$to}\"";
    }
}
