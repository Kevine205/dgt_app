<?php

namespace App\Services;

use App\Models\Dossier;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function genererContratVise(Dossier $dossier): string
    {
        // Charger le validateur avec sa signature
        $validateur = $dossier->validateur_id
            ? User::find($dossier->validateur_id)
            : auth()->user();

        $pdf = Pdf::loadView('pdf.contrat-vise', compact('dossier', 'validateur'))
            ->setPaper('A4', 'portrait');

        $chemin = "dossiers/{$dossier->id}/contrat_vise_{$dossier->numero_suivi}.pdf";
        Storage::disk('private')->put($chemin, $pdf->output());

        return $chemin;
    }
}
