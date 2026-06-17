<?php
// DossierSoumisNotification.php
namespace App\Notifications;
use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DossierSoumisNotification extends Notification {
    use Queueable;
    public function __construct(public Dossier $dossier) {}
    public function via($notifiable): array { return ['mail']; }
    public function toMail($notifiable): MailMessage {
        return (new MailMessage)
            ->subject("✅ Dossier {$this->dossier->numero_suivi} reçu — DGT Bénin")
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line("Votre dossier a été reçu avec succès.")
            ->line("**Numéro de suivi : {$this->dossier->numero_suivi}**")
            ->line("Vous serez notifié à chaque étape du traitement.")
            ->action('Suivre mon dossier', route('usager.dossiers.show', $this->dossier))
            ->salutation("Cordialement, la Direction Générale du Travail — Bénin");
    }
}
