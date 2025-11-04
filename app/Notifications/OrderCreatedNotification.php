<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;
use App\Helpers\OrderStatusHelper;

class OrderCreatedNotification extends Notification
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
        // Convertir l'enum en string si nécessaire
        $statusString = $this->order->status instanceof \App\Enums\OrderStatus 
            ? $this->order->status->value 
            : (string)$this->order->status;
        $statusInfo = OrderStatusHelper::getStatusInfo($statusString);

        $mailMessage = (new MailMessage)
            ->subject('✅ Commande confirmée - #' . $this->order->order_number)
            ->greeting('Bonjour ' . $this->order->user->full_name . ',')
            ->line('Votre commande a été créée avec succès !')
            ->line('')
            ->line('**Détails de votre commande :**')
            ->line('• Numéro de commande : #' . $this->order->order_number)
            ->line('• Date de commande : ' . $this->order->created_at->format('d/m/Y à H:i'))
            ->line('• Statut actuel : ' . $statusInfo['text'])
            ->line('• Montant total : **' . number_format($this->order->total_amount, 0, ',', ' ') . ' FCFA**')
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
            $mailMessage->line('**Vos notes :**')
                ->line($this->order->notes);
        }

        $mailMessage
            ->line('')
            ->line('📞 **Prochaines étapes :**')
            ->line('• Notre équipe va traiter votre commande')
            ->line('• Vous recevrez une notification à chaque changement de statut')
            ->line('• La livraison est prévue pour le ' . $this->order->delivery_date->format('d/m/Y'))
            ->line('')
            ->line('Merci pour votre confiance !')
            ->line('')
            ->salutation('Cordialement,')
            ->line('L\'équipe Allo Mobile')
            ->action('Suivre ma commande', url('/orders/' . $this->order->id));

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
            'status' => $this->order->status,
            'total_amount' => $this->order->total_amount,
            'delivery_date' => $this->order->delivery_date,
            'created_at' => $this->order->created_at,
            'message' => 'Votre commande #' . $this->order->order_number . ' a été créée avec succès',
            'type' => 'order_created'
        ];
    }
}
