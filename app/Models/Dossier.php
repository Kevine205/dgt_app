<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dossier extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_suivi', 'user_id', 'agent_id', 'validateur_id',
        'nom_employeur', 'secteur_activite', 'adresse_employeur',
        'nom_employe', 'prenom_employe', 'date_naissance_employe',
        'nationalite_employe', 'type_contrat', 'date_signature',
        'date_debut', 'date_fin', 'salaire', 'poste',
        'statut', 'motif_correction', 'motif_rejet', 'notes_agent',
        'chemin_contrat_vise', 'date_soumission', 'date_visa',
    ];

    protected $casts = [
        'date_signature' => 'date',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_naissance_employe' => 'date',
        'date_soumission' => 'datetime',
        'date_visa' => 'datetime',
        'salaire' => 'decimal:2',
    ];

    const STATUTS = [
        'soumis'               => ['label' => 'Soumis',                  'couleur' => 'blue',   'etape' => 1],
        'en_cours'             => ['label' => 'En cours d\'examen',      'couleur' => 'yellow', 'etape' => 2],
        'correction_demandee'  => ['label' => 'Correction demandée',     'couleur' => 'orange', 'etape' => 2],
        'entretien_requis'     => ['label' => 'Entretien requis',        'couleur' => 'purple', 'etape' => 3],
        'en_attente_arbitrage' => ['label' => 'En attente d\'arbitrage', 'couleur' => 'red',    'etape' => 3],
        'vise'                 => ['label' => 'Visé',                    'couleur' => 'green',  'etape' => 4],
        'rejete'               => ['label' => 'Rejeté',                  'couleur' => 'red',    'etape' => 4],
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }

    public function pieces()
    {
        return $this->hasMany(PieceJustificative::class);
    }

    public function entretien()
    {
        return $this->hasOne(Entretien::class);
    }

    public function audits()
    {
        return $this->hasMany(JournalAudit::class, 'modele_id')
                    ->where('modele', 'Dossier')
                    ->orderByDesc('created_at');
    }

    public function getStatutInfoAttribute(): array
    {
        return self::STATUTS[$this->statut] ?? ['label' => $this->statut, 'couleur' => 'gray', 'etape' => 0];
    }

    public function peutEtreTelecharge(): bool
    {
        return $this->statut === 'vise' && $this->chemin_contrat_vise !== null;
    }

    public function estBloque(): bool
    {
        return in_array($this->statut, ['entretien_requis', 'en_attente_arbitrage', 'rejete']);
    }

    public static function genererNumeroChrono(): string
    {
        $annee = now()->format('Y');
        $mois  = now()->format('m');
        $count = self::whereYear('created_at', $annee)->whereMonth('created_at', $mois)->count() + 1;
        return sprintf('DGT-%s%s-%04d', $annee, $mois, $count);
    }
}
