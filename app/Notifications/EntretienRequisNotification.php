<?php

namespace App\Notifications;

use App\Models\Dossier;
use App\Models\Entretien;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EntretienRequisNotification extends Notification
{
    use Queueable;

    public function __construct(public Dossier $dossier, public Entretien $entretien) {}

    public function via($notifiable): array { return ['mail', 'database']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("📋 Convocation à un entretien — Dossier {$this->dossier->numero_suivi}")
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line("Un entretien physique est requis pour votre dossier **{$this->dossier->numero_suivi}**.")
            ->line("**Motif :** {$this->entretien->motif_convocation}")
            ->line("**Date limite de présentation :** {$this->entretien->date_limite->format('d/m/Y')}")
            ->line("Veuillez vous présenter à la Direction Générale du Travail, Cotonou, avant cette date.")
            ->action('Voir mon dossier', route('usager.dossiers.show', $this->dossier))
            ->salutation("Cordialement, la Direction Générale du Travail — République du Bénin");
    }

    public function toDatabase($notifiable): array
    {
        return [
            'dossier_id'   => $this->dossier->id,
            'numero_suivi' => $this->dossier->numero_suivi,
            'titre'        => 'Entretien requis',
            'message'      => "Vous êtes convoqué à un entretien pour le dossier {$this->dossier->numero_suivi} avant le {$this->entretien->date_limite->format('d/m/Y')}.",
        ];
    }
}
