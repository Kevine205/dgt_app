<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PieceJustificative extends Model
{
     protected $table = 'pieces_justificatives';
    protected $fillable = [
        'dossier_id', 'nom_original', 'nom_stockage',
        'type_piece', 'mime_type', 'taille', 'conforme', 'motif_non_conformite',
    ];

    protected $casts = [
        'conforme' => 'boolean',
        'taille' => 'integer',
    ];

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function getTailleFormateeAttribute(): string
    {
        $taille = $this->taille;
        if ($taille >= 1048576) return round($taille / 1048576, 2) . ' Mo';
        if ($taille >= 1024)    return round($taille / 1024, 2) . ' Ko';
        return $taille . ' o';
    }
}
