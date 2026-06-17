<?php

namespace App\Services;

use App\Models\Dossier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function genererContratVise(Dossier $dossier): string
    {
        $pdf = Pdf::loadView('pdf.contrat-vise', compact('dossier'))
            ->setPaper('A4', 'portrait');

        $chemin = "dossiers/{$dossier->id}/contrat_vise_{$dossier->numero_suivi}.pdf";
        Storage::disk('private')->put($chemin, $pdf->output());

        return $chemin;
    }

    public function genererAccuseReception(Dossier $dossier): string
    {
        $pdf  = Pdf::loadView('pdf.accuse-reception', compact('dossier'))->setPaper('A4', 'portrait');
        $path = "dossiers/{$dossier->id}/accuse_reception_{$dossier->numero_suivi}.pdf";
        Storage::disk('private')->put($path, $pdf->output());
        return $path;
    }
}
