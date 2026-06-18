<?php

namespace App\Notifications;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DossierSoumisNotification extends Notification
{
    use Queueable;

    public function __construct(public Dossier $dossier) {}

    public function via($notifiable): array { return ['mail', 'database']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("✅ Dossier {$this->dossier->numero_suivi} reçu — DGT Bénin")
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line("Votre dossier a été reçu avec succès par la Direction Générale du Travail.")
            ->line("**Numéro de suivi : {$this->dossier->numero_suivi}**")
            ->line("Vous serez notifié par e-mail à chaque étape du traitement.")
            ->action('Suivre mon dossier', route('usager.dossiers.show', $this->dossier))
            ->salutation("Cordialement, la Direction Générale du Travail — République du Bénin");
    }

    public function toDatabase($notifiable): array
    {
        return [
            'dossier_id'   => $this->dossier->id,
            'numero_suivi' => $this->dossier->numero_suivi,
            'titre'        => 'Dossier reçu',
            'message'      => "Votre dossier {$this->dossier->numero_suivi} a été reçu avec succès.",
        ];
    }
}
