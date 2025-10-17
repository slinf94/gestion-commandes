<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;
use App\Enums\OrderStatus;

class OrderStatusChangedNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, OrderStatus $oldStatus, OrderStatus $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
        $mailMessage = (new MailMessage)
            ->subject('📦 Mise à jour de votre commande #' . $this->order->order_number)
            ->greeting('Bonjour ' . $this->order->user->nom . ' ' . $this->order->user->prenom . ',')
            ->line('Nous vous informons que le statut de votre commande a été mis à jour.')
            ->line('')
            ->line('**Détails de la commande :**')
            ->line('• Numéro de commande : #' . $this->order->order_number)
            ->line('• Date de commande : ' . $this->order->created_at->format('d/m/Y à H:i'))
            ->line('• Montant total : ' . number_format($this->order->total_amount, 0, ',', ' ') . ' FCFA')
            ->line('')
            ->line('**Changement de statut :**')
            ->line('• Ancien statut : ' . $this->oldStatus->getLabel())
            ->line('• Nouveau statut : **' . $this->newStatus->getLabel() . '**')
            ->line('');

        // Ajouter des informations spécifiques selon le statut
        switch ($this->newStatus) {
            case OrderStatus::CONFIRMED:
                $mailMessage->line('✅ Votre commande a été confirmée et sera traitée sous peu.');
                break;
            case OrderStatus::PROCESSING:
                $mailMessage->line('⚙️ Votre commande est en cours de préparation.');
                break;
            case OrderStatus::SHIPPED:
                $mailMessage->line('🚚 Votre commande a été expédiée et sera livrée prochainement.');
                break;
            case OrderStatus::DELIVERED:
                $mailMessage->line('🎉 Votre commande a été livrée avec succès !');
                $mailMessage->line('Merci pour votre confiance et à bientôt !');
                break;
            case OrderStatus::CANCELLED:
                $mailMessage->line('❌ Votre commande a été annulée.');
                $mailMessage->line('Si vous avez des questions, n\'hésitez pas à nous contacter.');
                break;
        }

        $mailMessage
            ->line('')
            ->line('**Articles commandés :**');

        foreach ($this->order->items as $item) {
            $productName = $item->product_name ?? ($item->product->name ?? 'Produit supprimé');
            $mailMessage->line('• ' . $productName . ' (x' . $item->quantity . ') - ' . number_format($item->total_price, 0, ',', ' ') . ' FCFA');
        }

        $mailMessage
            ->line('')
            ->line('**Adresse de livraison :**')
            ->line($this->order->delivery_address['street'] ?? 'Non spécifiée')
            ->line($this->order->delivery_address['city'] ?? '')
            ->line($this->order->delivery_address['country'] ?? '')
            ->line('')
            ->line('Si vous avez des questions concernant votre commande, n\'hésitez pas à nous contacter.')
            ->line('')
            ->salutation('Cordialement,')
            ->line('L\'équipe Allo Mobile')
            ->action('Voir ma commande', url('/orders/' . $this->order->id));

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
            'old_status' => $this->oldStatus->value,
            'new_status' => $this->newStatus->value,
            'customer_name' => $this->order->user->nom . ' ' . $this->order->user->prenom,
            'total_amount' => $this->order->total_amount,
        ];
    }
}
