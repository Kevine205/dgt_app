<?php

namespace App\Notifications;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DossierViseNotification extends Notification
{
    use Queueable;

    protected $dossier;

    /**
     * Crée une nouvelle instance de notification.
     */
    public function __construct(Dossier $dossier)
    {
        $this->dossier = $dossier;
    }

    /**
     * Détermine les canaux de transmission de la notification (Mail et Base de données).
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Représentation de la notification par Email.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject("DGT — Votre dossier de contrat a été visé (" . $this->dossier->numero_suivi . ")")
                    ->greeting("Bonjour " . $notifiable->prenom . ",")
                    ->line("Nous vous informons que votre dossier numéro **" . $this->dossier->numero_suivi . "** concernant le poste de **" . $this->dossier->poste . "** a été examiné avec succès et validé par la Direction Générale du Travail.")
                    ->line("Votre contrat de travail officiel visé est désormais disponible en téléchargement sécurisé sur votre espace usager.")
                    ->action('Accéder à mon espace', url('/'))
                    ->line("Merci d'utiliser nos services en ligne.");
    }

    /**
     * Contenu stocké en base de données pour les notifications en temps réel.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'dossier_id' => $this->dossier->id,
            'numero_suivi' => $this->dossier->numero_suivi,
            'titre' => 'Contrat visé avec succès',
            'message' => "Le visa a été apposé sur votre dossier " . $this->dossier->numero_suivi . ".",
        ];
    }
}