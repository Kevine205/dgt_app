<?php
namespace App\Notifications;
use App\Models\{Dossier, Entretien};
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EntretienRequisNotification extends Notification {
    use Queueable;
    public function __construct(public Dossier $dossier, public Entretien $entretien) {}
    public function via($n): array { return ['mail']; }
    public function toMail($n): MailMessage {
        return (new MailMessage)
            ->subject("📋 Convocation à un entretien — Dossier {$this->dossier->numero_suivi}")
            ->greeting("Bonjour {$n->prenom},")
            ->line("Un entretien est requis pour votre dossier **{$this->dossier->numero_suivi}**.")
            ->line("**Motif :** {$this->entretien->motif_convocation}")
            ->line("Vous devez vous présenter à la DGT (Cotonou) avant le **{$this->entretien->date_limite->format('d/m/Y')}**.")
            ->line("Adresse : Direction Générale du Travail, Cotonou, Bénin.")
            ->action('Voir mon dossier', route('usager.dossiers.show', $this->dossier))
            ->salutation("Cordialement, DGT Bénin");
    }
}

class DossierViseNotification extends Notification {
    use Queueable;
    public function __construct(public Dossier $dossier) {}
    public function via($n): array { return ['mail']; }
    public function toMail($n): MailMessage {
        return (new MailMessage)
            ->subject("🎉 Contrat visé — Dossier {$this->dossier->numero_suivi}")
            ->greeting("Bonjour {$n->prenom},")
            ->line("Votre contrat a été **visé avec succès** !")
            ->line("Dossier : **{$this->dossier->numero_suivi}**")
            ->line("Vous pouvez maintenant télécharger votre contrat visé.")
            ->action('Télécharger mon contrat', route('usager.dossiers.telecharger', $this->dossier))
            ->salutation("Cordialement, DGT Bénin");
    }
}

class DossierRejeteNotification extends Notification {
    use Queueable;
    public function __construct(public Dossier $dossier) {}
    public function via($n): array { return ['mail']; }
    public function toMail($n): MailMessage {
        return (new MailMessage)
            ->subject("❌ Dossier rejeté — {$this->dossier->numero_suivi}")
            ->greeting("Bonjour {$n->prenom},")
            ->line("Votre dossier **{$this->dossier->numero_suivi}** a été rejeté.")
            ->line("**Motif :** {$this->dossier->motif_rejet}")
            ->line("Pour toute réclamation, contactez la DGT.")
            ->action('Voir mon dossier', route('usager.dossiers.show', $this->dossier))
            ->salutation("Cordialement, DGT Bénin");
    }
}
