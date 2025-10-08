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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle inscription - Allo Mobile')
            ->greeting('Bonjour Admin,')
            ->line('Un nouvel utilisateur vient de s\'inscrire sur l\'application Allo Mobile.')
            ->line('**Informations du nouvel utilisateur :**')
            ->line('• Nom : ' . $this->user->nom . ' ' . $this->user->prenom)
            ->line('• Email : ' . $this->user->email)
            ->line('• Téléphone : ' . $this->user->numero_telephone)
            ->line('• Quartier : ' . ($this->user->quartier ?? 'Non défini'))
            ->line('• Date d\'inscription : ' . $this->user->created_at->format('d/m/Y H:i'))
            ->action('Voir le profil utilisateur', url('/admin/users/' . $this->user->id))
            ->line('Veuillez activer ce compte pour permettre à l\'utilisateur de se connecter.')
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
            //
        ];
    }
}
