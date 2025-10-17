<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;
use App\Helpers\OrderStatusHelper;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusInfo = OrderStatusHelper::getStatusInfo($this->order->status);

        $mailMessage = (new MailMessage)
            ->subject('🛒 Nouvelle commande reçue - #' . $this->order->order_number)
            ->greeting('Nouvelle commande reçue !')
            ->line('Une nouvelle commande vient d\'être passée sur votre plateforme Allo Mobile.')
            ->line('')
            ->line('**Informations de la commande :**')
            ->line('• Numéro de commande : #' . $this->order->order_number)
            ->line('• Date de commande : ' . $this->order->created_at->format('d/m/Y à H:i'))
            ->line('• Statut actuel : ' . $statusInfo['text'])
            ->line('• Montant total : **' . number_format($this->order->total_amount, 0, ',', ' ') . ' FCFA**')
            ->line('')
            ->line('**Informations du client :**')
            ->line('• Nom complet : ' . $this->order->user->full_name)
            ->line('• Email : ' . $this->order->user->email)
            ->line('• Téléphone : ' . $this->order->user->numero_telephone)
            ->line('• Localisation : ' . $this->order->user->localisation . ', ' . $this->order->user->quartier)
            ->line('')
            ->line('**Articles commandés :**');

        foreach ($this->order->items as $item) {
            $mailMessage->line('• ' . $item->product->name . ' (x' . $item->quantity . ') - ' . number_format($item->total_price, 0, ',', ' ') . ' FCFA');
        }

        $mailMessage
            ->line('')
            ->line('**Adresse de livraison :**')
            ->line($this->order->delivery_address['street'] ?? 'Non spécifiée')
            ->line($this->order->delivery_address['city'] ?? '')
            ->line($this->order->delivery_address['country'] ?? '')
            ->line('');

        if ($this->order->notes) {
            $mailMessage->line('**Notes du client :**')
                ->line($this->order->notes);
        }

        $mailMessage
            ->line('')
            ->line('⚠️ **Action requise :** Veuillez traiter cette commande dans les plus brefs délais.')
            ->line('')
            ->salutation('Système de notification Allo Mobile')
            ->action('Gérer la commande', url('/admin/orders/' . $this->order->id));

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'customer_name' => $this->order->user->full_name,
            'customer_email' => $this->order->user->email,
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status,
            'created_at' => $this->order->created_at,
        ];
    }
}
