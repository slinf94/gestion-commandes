<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $statusMap = [
            'pending' => ['text' => 'En attente', 'color' => '#FF9800', 'icon' => '⏳'],
            'confirmed' => ['text' => 'Confirmée', 'color' => '#2196F3', 'icon' => '✅'],
            'processing' => ['text' => 'En cours de traitement', 'color' => '#2196F3', 'icon' => '⚙️'],
            'shipped' => ['text' => 'Expédiée', 'color' => '#9C27B0', 'icon' => '🚚'],
            'delivered' => ['text' => 'Livrée', 'color' => '#4CAF50', 'icon' => '🎉'],
            'cancelled' => ['text' => 'Annulée', 'color' => '#F44336', 'icon' => '❌'],
        ];

        $newStatusInfo = $statusMap[$this->newStatus] ?? ['text' => ucfirst($this->newStatus), 'color' => '#757575', 'icon' => '📦'];
        $oldStatusInfo = $statusMap[$this->oldStatus] ?? ['text' => ucfirst($this->oldStatus), 'color' => '#757575', 'icon' => '📦'];

        return $this->subject('📦 Mise à jour de votre commande #' . $this->order->order_number)
            ->view('emails.order-status-update')
            ->with([
                'order' => $this->order,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
                'oldStatusInfo' => $oldStatusInfo,
                'newStatusInfo' => $newStatusInfo,
            ]);
    }
}

