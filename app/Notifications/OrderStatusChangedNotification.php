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
            ->subject('📦 Mise à jour - Commande #' . $this->order->order_number . ' - Allo Mobile')
            ->greeting('Bonjour ' . $this->order->user->prenom . ',')
            ->line('')
            ->line('Nous avons une mise à jour concernant votre commande Allo Mobile.')
            ->line('')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('📦 VOTRE COMMANDE')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('🔢 Numéro : #' . $this->order->order_number)
            ->line('📅 Date : ' . $this->order->created_at->format('d/m/Y à H:i'))
            ->line('💰 Montant : ' . number_format($this->order->total_amount, 0, ',', ' ') . ' FCFA')
            ->line('')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('🔄 CHANGEMENT DE STATUT')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('❌ Ancien : ' . $this->oldStatus->getLabel())
            ->line('✅ Nouveau : **' . $this->newStatus->getLabel() . '**')
            ->line('');

        // Ajouter des informations spécifiques selon le statut
        switch ($this->newStatus) {
            case OrderStatus::CONFIRMED:
                $mailMessage->line('✅ **Votre commande a été confirmée !**')
                    ->line('Nous avons bien reçu votre paiement et préparons votre commande.')
                    ->line('Vous serez informé dès que nous commencerons la préparation.');
                break;
            case OrderStatus::PROCESSING:
                $mailMessage->line('⚙️ **Votre commande est en préparation !**')
                    ->line('Notre équipe prépare activement votre commande.')
                    ->line('Vous recevrez un message dès qu\'elle sera prête.');
                break;
            case OrderStatus::SHIPPED:
                $mailMessage->line('🚚 **Votre commande a été expédiée !**')
                    ->line('Votre colis est en cours de livraison.')
                    ->line('Le livreur vous contactera avant la livraison.')
                    ->line('');
                $mailMessage->line('📞 **Numéro de suivi :** Consultez votre tableau de bord pour suivre l\'avancement.');
                break;
            case OrderStatus::DELIVERED:
                $mailMessage->line('🎉 **Votre commande a été livrée !**')
                    ->line('Nous espérons que vous êtes satisfait de votre commande.')
                    ->line('Merci pour votre confiance et à très bientôt sur Allo Mobile !')
                    ->line('');
                $mailMessage->line('💬 **Votre avis nous intéresse** : N\'hésitez pas à nous laisser un commentaire.')
                    ->action('Laisser un avis', url('/orders/' . $this->order->id . '/review'));
                break;
            case OrderStatus::CANCELLED:
                $mailMessage->line('❌ **Votre commande a été annulée.**')
                    ->line('Si c\'était un malentendu ou si vous avez des questions,')
                    ->line('n\'hésitez pas à nous contacter au service client.')
                    ->line('');
                $mailMessage->line('📞 **Besoin d\'aide ?** Contactez-nous :')
                    ->line('   📧 support@allomobile.com')
                    ->line('   📱 Service client')
                    ->action('Nous contacter', url('/contact'));
                break;
        }

        $mailMessage
            ->line('')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('🛍️ ARTICLES COMMANDÉS')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        foreach ($this->order->items as $item) {
            $productName = $item->product_name ?? ($item->product->name ?? 'Produit supprimé');
            $mailMessage->line('• ' . $productName . ' × ' . $item->quantity . ' → ' . number_format($item->total_price, 0, ',', ' ') . ' FCFA');
        }

        $mailMessage
            ->line('')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('🏠 ADRESSE DE LIVRAISON')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line($this->order->delivery_address['street'] ?? 'Non spécifiée')
            ->line($this->order->delivery_address['city'] ?? '')
            ->line($this->order->delivery_address['country'] ?? '')
            ->line('')
            ->line('💬 **Questions ?** Notre équipe est à votre disposition.')
            ->line('')
            ->action('📋 Voir ma commande', url('/orders/' . $this->order->id))
            ->line('')
            ->salutation('Cordialement,')
            ->line('📱 L\'équipe Allo Mobile');

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
