<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entretien extends Model
{
    protected $fillable = [
        'dossier_id', 'declenche_par', 'motif_convocation',
        'date_convocation', 'date_limite', 'relance_j5', 'relance_j10',
        'statut', 'valide_par', 'date_validation', 'notes_validateur',
    ];

    protected $casts = [
        'date_convocation' => 'datetime',
        'date_limite'      => 'datetime',
        'relance_j5'       => 'datetime',
        'relance_j10'      => 'datetime',
        'date_validation'  => 'datetime',
    ];

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function declenchePar()
    {
        return $this->belongsTo(User::class, 'declenche_par');
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function estExpire(): bool
    {
        return now()->isAfter($this->date_limite) && $this->statut === 'programme';
    }

    public function joursRestants(): int
    {
        return max(0, (int) now()->diffInDays($this->date_limite, false));
    }
}
