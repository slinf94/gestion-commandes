<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class NewOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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

        $statusInfo = $statusMap[$this->order->status] ?? ['text' => ucfirst($this->order->status), 'color' => '#757575', 'icon' => '📦'];

        return $this->subject('🛒 Nouvelle commande reçue - #' . $this->order->order_number)
            ->view('emails.new-order')
            ->with([
                'order' => $this->order,
                'statusInfo' => $statusInfo,
            ]);
    }
}

