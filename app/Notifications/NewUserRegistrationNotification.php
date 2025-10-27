<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class NewUserRegistrationNotification extends Notification
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
            ->subject('🔔 Nouvelle inscription - Allo Mobile')
            ->greeting('Bonjour ' . $notifiable->prenom . ',')
            ->line('')
            ->line('📢 **Nouvelle inscription détectée sur Allo Mobile.**')
            ->line('')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('👤 INFORMATIONS DU NOUVEL UTILISATEUR')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('👨‍💼 Nom complet : ' . $this->user->full_name)
            ->line('📧 Email : ' . $this->user->email)
            ->line('📱 Téléphone : ' . $this->user->numero_telephone)
            ->line('📍 Quartier : ' . ($this->user->quartier ?? 'Non défini'))
            ->line('📍 Localisation : ' . ($this->user->localisation ?? 'Non définie'))
            ->line('📅 Date d\'inscription : ' . $this->user->created_at->format('d/m/Y à H:i'))
            ->line('')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('⚙️ ACTION REQUISE')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('Veuillez activer ce compte pour permettre à l\'utilisateur de se connecter.')
            ->line('')
            ->line('⚠️ **Important :** Vérifiez les informations avant activation.')
            ->line('')
            ->action('✅ Gérer le compte utilisateur', url('/admin/users/' . $this->user->id))
            ->line('')
            ->salutation('L\'équipe Allo Mobile - Administration');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
