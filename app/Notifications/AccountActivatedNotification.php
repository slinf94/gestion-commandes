<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class AccountActivatedNotification extends Notification
{
    use Queueable;

    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        return (new MailMessage)
            ->subject('🎉 Bienvenue sur Allo Mobile - Votre compte est actif !')
            ->greeting('Bonjour ' . $this->user->prenom . ',')
            ->line('')
            ->line('✅ **Excellente nouvelle ! Votre compte Allo Mobile a été activé.**')
            ->line('')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('🎊 BIENVENUE SUR ALLO MOBILE')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('Vous pouvez maintenant profiter pleinement de nos services.')
            ->line('')
            ->line('📱 **Connectez-vous maintenant** pour découvrir :')
            ->line('   🛒 Nos produits et promotions')
            ->line('   📦 Le suivi de vos commandes')
            ->line('   💳 Des moyens de paiement sécurisés')
            ->line('   📞 Un support client réactif')
            ->line('')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('📋 VOS INFORMATIONS')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('👤 Nom : ' . $this->user->full_name)
            ->line('📧 Email : ' . $this->user->email)
            ->line('📱 Téléphone : ' . $this->user->numero_telephone)
            ->line('📍 Quartier : ' . ($this->user->quartier ?? 'Non défini'))
            ->line('')
            ->line('⚠️ **Sécurité :** Gardez vos identifiants confidentiels.')
            ->line('')
            ->action('🚀 Commencer mes achats', url('/'))
            ->line('')
            ->line('💬 Des questions ? Contactez notre équipe :')
            ->line('   📧 support@allomobile.com')
            ->line('   📱 Service client 24/7')
            ->line('')
            ->salutation('À très bientôt sur Allo Mobile !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->prenom . ' ' . $this->user->nom,
            'message' => 'Compte activé avec succès',
        ];
    }
}




