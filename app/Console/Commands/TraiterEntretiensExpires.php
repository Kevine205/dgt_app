<?php

namespace App\Console\Commands;

use App\Models\Entretien;
use App\Models\JournalAudit;
use App\Notifications\EntretienRelanceNotification;
use Illuminate\Console\Command;

class TraiterEntretiensExpires extends Command
{
    protected $signature   = 'dgt:traiter-entretiens';
    protected $description = 'Envoie les relances J+5 / J+10 et escalade les entretiens expirés';

    public function handle(): void
    {
        $entretiens = Entretien::with(['dossier.user'])
            ->where('statut', 'programme')
            ->get();

        foreach ($entretiens as $entretien) {
            $now = now();

            // Relance J+5
            if ($entretien->relance_j5 && $now->greaterThanOrEqualTo($entretien->relance_j5) && !$entretien->relance_j5_envoyee) {
                $entretien->dossier->user->notify(new EntretienRelanceNotification($entretien->dossier, $entretien, 5));
                $entretien->update(['relance_j5' => null]); // marquer comme envoyé
                $this->info("Relance J+5 envoyée : {$entretien->dossier->numero_suivi}");
            }

            // Relance J+10
            if ($entretien->relance_j10 && $now->greaterThanOrEqualTo($entretien->relance_j10) && !$entretien->relance_j10_envoyee) {
                $entretien->dossier->user->notify(new EntretienRelanceNotification($entretien->dossier, $entretien, 10));
                $entretien->update(['relance_j10' => null]);
                $this->info("Relance J+10 envoyée : {$entretien->dossier->numero_suivi}");
            }

            // Délai expiré → escalade
            if ($now->isAfter($entretien->date_limite)) {
                $entretien->update(['statut' => 'expire']);
                $entretien->dossier->update(['statut' => 'en_attente_arbitrage']);
                JournalAudit::create([
                    'user_id'     => null,
                    'action'      => 'ENTRETIEN_EXPIRE',
                    'modele'      => 'Dossier',
                    'modele_id'   => $entretien->dossier_id,
                    'description' => "Entretien expiré automatiquement — dossier {$entretien->dossier->numero_suivi} escaladé en arbitrage",
                    'adresse_ip'  => '127.0.0.1',
                ]);
                $this->warn("Entretien expiré — escalade : {$entretien->dossier->numero_suivi}");
            }
        }

        $this->info('✅ Traitement des entretiens terminé.');
    }
}
