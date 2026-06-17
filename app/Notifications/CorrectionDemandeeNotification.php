<?php
namespace App\Notifications;
use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CorrectionDemandeeNotification extends Notification {
    use Queueable;
    public function __construct(public Dossier $dossier) {}
    public function via($n): array { return ['mail']; }
    public function toMail($n): MailMessage {
        return (new MailMessage)
            ->subject("⚠️ Corrections requises — Dossier {$this->dossier->numero_suivi}")
            ->greeting("Bonjour {$n->prenom},")
            ->line("Des corrections sont requises pour votre dossier **{$this->dossier->numero_suivi}**.")
            ->line("**Motif :** {$this->dossier->motif_correction}")
            ->action('Corriger mon dossier', route('usager.dossiers.corriger', $this->dossier))
            ->salutation("Cordialement, DGT Bénin");
    }
}
