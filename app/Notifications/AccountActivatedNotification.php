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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Votre compte Allo Mobile a été activé !')
            ->greeting('Bonjour ' . $this->user->prenom . ' ' . $this->user->nom . ' !')
            ->line('Excellente nouvelle ! Votre compte Allo Mobile a été activé avec succès par notre équipe.')
            ->line('Vous pouvez maintenant vous connecter à l\'application mobile et profiter de tous nos services :')
            ->line('✅ Parcourir notre catalogue de produits')
            ->line('✅ Ajouter des produits à vos favoris')
            ->line('✅ Passer des commandes en toute simplicité')
            ->line('✅ Suivre l\'état de vos commandes')
            ->line('')
            ->line('📱 **Informations de connexion :**')
            ->line('Email : ' . $this->user->email)
            ->line('Téléphone : ' . $this->user->telephone)
            ->line('')
            ->action('🚀 Se connecter maintenant', url('/'))
            ->line('Merci de nous faire confiance et bienvenue dans la famille Allo Mobile !')
            ->salutation('Cordialement, L\'équipe Allo Mobile');
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

