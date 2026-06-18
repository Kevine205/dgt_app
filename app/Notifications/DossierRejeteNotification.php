<?php

namespace App\Notifications;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DossierRejeteNotification extends Notification
{
    use Queueable;

    public function __construct(public Dossier $dossier) {}

    public function via($notifiable): array { return ['mail', 'database']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("❌ Dossier rejeté — {$this->dossier->numero_suivi}")
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line("Votre dossier **{$this->dossier->numero_suivi}** a été rejeté.")
            ->line("**Motif :** {$this->dossier->motif_rejet}")
            ->line("Pour toute réclamation, contactez la DGT à contact@dgt.bj ou au guichet de la Direction Générale du Travail.")
            ->action('Voir mon dossier', route('usager.dossiers.show', $this->dossier))
            ->salutation("Cordialement, la Direction Générale du Travail — République du Bénin");
    }

    public function toDatabase($notifiable): array
    {
        return [
            'dossier_id'   => $this->dossier->id,
            'numero_suivi' => $this->dossier->numero_suivi,
            'titre'        => 'Dossier rejeté',
            'message'      => "Votre dossier {$this->dossier->numero_suivi} a été rejeté. Motif : {$this->dossier->motif_rejet}",
        ];
    }
}
