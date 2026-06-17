<?php

namespace App\Http\Controllers\Usager;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Dossier;
use App\Models\PieceJustificative;
use App\Models\JournalAudit;
use App\Notifications\DossierSoumisNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DossierController extends Controller
{
    
    use AuthorizesRequests;
    public function create()
    {
        return view('usager.dossiers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_employeur'    => 'required|string|max:200',
            'secteur_activite' => 'nullable|string|max:200',
            'adresse_employeur'=> 'nullable|string|max:300',
            'nom_employe'      => 'required|string|max:100',
            'prenom_employe'   => 'required|string|max:100',
            'date_naissance_employe' => 'nullable|date|before:today',
            'nationalite_employe'    => 'nullable|string|max:100',
            'type_contrat'     => 'required|in:CDI,CDD,Apprentissage,Stage,Interim,Saisonnier',
            'date_signature'   => 'required|date',
            'date_debut'       => 'required|date',
            'date_fin'         => 'nullable|date|after:date_debut',
            'salaire'          => 'nullable|numeric|min:0',
            'poste'            => 'required|string|max:200',
            'pieces.*'         => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'types_pieces.*'   => 'required|string',
        ], [
            'pieces.*.max'     => 'Chaque pièce ne doit pas dépasser 5 Mo.',
            'pieces.*.mimes'   => 'Formats acceptés : PDF, JPG, PNG.',
        ]);

        $dossier = Dossier::create([
            'numero_suivi'           => Dossier::genererNumeroChrono(),
            'user_id'                => auth()->id(),
            'nom_employeur'          => $request->nom_employeur,
            'secteur_activite'       => $request->secteur_activite,
            'adresse_employeur'      => $request->adresse_employeur,
            'nom_employe'            => strtoupper($request->nom_employe),
            'prenom_employe'         => ucfirst($request->prenom_employe),
            'date_naissance_employe' => $request->date_naissance_employe,
            'nationalite_employe'    => $request->nationalite_employe,
            'type_contrat'           => $request->type_contrat,
            'date_signature'         => $request->date_signature,
            'date_debut'             => $request->date_debut,
            'date_fin'               => $request->date_fin,
            'salaire'                => $request->salaire,
            'poste'                  => $request->poste,
            'statut'                 => 'soumis',
        ]);

        // Stocker les pièces jointes
        if ($request->hasFile('pieces')) {
            foreach ($request->file('pieces') as $index => $fichier) {
                $nomStockage = $fichier->store("dossiers/{$dossier->id}/pieces", 'private');
                PieceJustificative::create([
                    'dossier_id'   => $dossier->id,
                    'nom_original' => $fichier->getClientOriginalName(),
                    'nom_stockage' => $nomStockage,
                    'type_piece'   => $request->types_pieces[$index] ?? 'document',
                    'mime_type'    => $fichier->getMimeType(),
                    'taille'       => $fichier->getSize(),
                ]);
            }
        }

        JournalAudit::enregistrer('DOSSIER_SOUMIS', "Dossier {$dossier->numero_suivi} soumis", $dossier);
        auth()->user()->notify(new DossierSoumisNotification($dossier));

        return redirect()->route('usager.dossiers.show', $dossier)
            ->with('success', "Votre dossier {$dossier->numero_suivi} a été soumis avec succès. Vous recevrez une notification à chaque étape.");
    }

    public function show(Dossier $dossier)
    {
        $this->authorize('view', $dossier);
        $dossier->load(['pieces', 'entretien', 'audits.user']);
        return view('usager.dossiers.show', compact('dossier'));
    }

    public function corriger(Dossier $dossier)
    {
        $this->authorize('view', $dossier);
        if ($dossier->statut !== 'correction_demandee') {
            return redirect()->route('usager.dossiers.show', $dossier)
                ->with('error', 'Ce dossier n\'est pas en attente de correction.');
        }
        $dossier->load('pieces');
        return view('usager.dossiers.corriger', compact('dossier'));
    }

    public function soumettreCorrection(Request $request, Dossier $dossier)
    {
        $this->authorize('view', $dossier);

        $request->validate([
            'nouvelles_pieces.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pieces_ids.*'       => 'nullable|exists:pieces_justificatives,id',
        ]);

        if ($request->hasFile('nouvelles_pieces')) {
            foreach ($request->file('nouvelles_pieces') as $index => $fichier) {
                $pieceId = $request->pieces_ids[$index] ?? null;
                if ($pieceId) {
                    $piece = PieceJustificative::find($pieceId);
                    Storage::disk('private')->delete($piece->nom_stockage);
                    $nomStockage = $fichier->store("dossiers/{$dossier->id}/pieces", 'private');
                    $piece->update([
                        'nom_original' => $fichier->getClientOriginalName(),
                        'nom_stockage' => $nomStockage,
                        'mime_type'    => $fichier->getMimeType(),
                        'taille'       => $fichier->getSize(),
                        'conforme'     => null,
                    ]);
                }
            }
        }

        $dossier->update(['statut' => 'en_cours', 'motif_correction' => null]);
        JournalAudit::enregistrer('CORRECTION_SOUMISE', "Corrections soumises pour le dossier {$dossier->numero_suivi}", $dossier);

        return redirect()->route('usager.dossiers.show', $dossier)
            ->with('success', 'Vos corrections ont été soumises. L\'agent va reprendre l\'examen de votre dossier.');
    }

    public function telecharger(Dossier $dossier)
    {
        $this->authorize('view', $dossier);

        if (!$dossier->peutEtreTelecharge()) {
            abort(403, 'Le téléchargement n\'est pas disponible pour ce dossier.');
        }

        JournalAudit::enregistrer('TELECHARGEMENT_CONTRAT', "Téléchargement du contrat visé {$dossier->numero_suivi}", $dossier);

        return Storage::disk('private')->download(
            $dossier->chemin_contrat_vise,
            "Contrat_Vise_{$dossier->numero_suivi}.pdf"
        );
    }
}
