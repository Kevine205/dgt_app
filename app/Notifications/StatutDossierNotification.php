<?php

namespace App\Notifications;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StatutDossierNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Dossier $dossier,
        public string  $sujet,
        public string  $message,
        public ?string $action_url   = null,
        public ?string $action_label = null,
    ) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->sujet)
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line($this->message)
            ->line("**Numéro de dossier : {$this->dossier->numero_suivi}**");

        if ($this->action_url && $this->action_label) {
            $mail->action($this->action_label, $this->action_url);
        }

        return $mail->salutation("Cordialement, la Direction Générale du Travail — Bénin");
    }
}
