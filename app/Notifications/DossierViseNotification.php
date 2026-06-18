<?php

namespace App\Notifications;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DossierViseNotification extends Notification
{
    use Queueable;

    public function __construct(public Dossier $dossier) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("🎉 Contrat visé — Dossier {$this->dossier->numero_suivi}")
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line("Votre contrat de travail a été **visé avec succès** par la Direction Générale du Travail.")
            ->line("**Numéro de dossier : {$this->dossier->numero_suivi}**")
            ->line("Vous pouvez désormais télécharger votre contrat visé directement depuis votre espace personnel.")
            ->action('Télécharger mon contrat', route('usager.dossiers.telecharger', $this->dossier))
            ->line("Ce document officiel fait foi auprès de toute administration.")
            ->salutation("Cordialement, la Direction Générale du Travail — République du Bénin");
    }

    public function toDatabase($notifiable): array
    {
        return [
            'dossier_id'    => $this->dossier->id,
            'numero_suivi'  => $this->dossier->numero_suivi,
            'titre'         => 'Contrat visé avec succès',
            'message'       => "Le visa a été apposé sur votre dossier {$this->dossier->numero_suivi}.",
        ];
    }
}
