<?php

namespace App\Http\Controllers\Validateur;
use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Models\Entretien;
use App\Models\JournalAudit;
use App\Notifications\CorrectionDemandeeNotification;
use App\Notifications\EntretienRequisNotification;
use App\Notifications\DossierViseNotification;
use App\Notifications\DossierRejeteNotification;
use App\Services\PdfService;
use Illuminate\Http\Request;

class ValidateurController extends Controller
{
    public function __construct(private PdfService $pdfService) {}

    public function dashboard()
    {
        $stats = [
            'soumis'       => Dossier::where('statut', 'soumis')->count(),
            'en_cours'     => Dossier::where('statut', 'en_cours')->count(),
            'entretiens'   => Dossier::where('statut', 'entretien_requis')->count(),
            'arbitrages'   => Dossier::where('statut', 'en_attente_arbitrage')->count(),
            'vises_mois'   => Dossier::where('statut', 'vise')->whereMonth('date_visa', now()->month)->count(),
            'rejetes_mois' => Dossier::where('statut', 'rejete')->whereMonth('updated_at', now()->month)->count(),
        ];

        $dossiersPrioritaires = Dossier::with('user')
            ->whereIn('statut', ['soumis', 'en_cours', 'entretien_requis', 'en_attente_arbitrage'])
            ->latest()->take(10)->get();

        return view('validateur.dashboard', compact('stats', 'dossiersPrioritaires'));
    }

    public function index(Request $request)
    {
        $query = Dossier::with('user')->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('recherche')) {
            $query->where(function ($q) use ($request) {
                $q->where('numero_suivi', 'like', "%{$request->recherche}%")
                  ->orWhere('nom_employe', 'like', "%{$request->recherche}%")
                  ->orWhere('nom_employeur', 'like', "%{$request->recherche}%");
            });
        }

        $dossiers = $query->paginate(20)->withQueryString();
        return view('validateur.dossiers.index', compact('dossiers'));
    }

    public function show(Dossier $dossier)
    {
        $dossier->load(['user', 'pieces', 'entretien.declenchePar', 'audits.user', 'validateur']);
        return view('validateur.dossiers.show', compact('dossier'));
    }

    // ── Actions ex-agent ──────────────────────────────────────────

    public function prendreEnCharge(Dossier $dossier)
    {
        if ($dossier->statut !== 'soumis') {
            return back()->with('error', 'Ce dossier ne peut pas être pris en charge.');
        }
        $dossier->update(['statut' => 'en_cours', 'validateur_id' => auth()->id()]);
        JournalAudit::enregistrer('DOSSIER_PRIS_EN_CHARGE', "Dossier {$dossier->numero_suivi} pris en charge", $dossier);
        return back()->with('success', 'Dossier pris en charge.');
    }

    public function demanderCorrection(Request $request, Dossier $dossier)
    {
        $request->validate([
            'motif_correction' => 'required|string|min:20|max:2000',
        ], ['motif_correction.required' => 'Veuillez préciser les corrections demandées.']);

        $dossier->update(['statut' => 'correction_demandee', 'motif_correction' => $request->motif_correction]);
        JournalAudit::enregistrer('CORRECTION_DEMANDEE', "Correction demandée pour {$dossier->numero_suivi}", $dossier);
        $dossier->user->notify(new CorrectionDemandeeNotification($dossier));

        return redirect()->route('validateur.dossiers.show', $dossier)
            ->with('success', 'Demande de correction envoyée à l\'usager.');
    }

    public function declencherEntretien(Request $request, Dossier $dossier)
    {
        $request->validate(['motif_convocation' => 'required|string|min:20|max:2000']);

        $dateConvocation = now();
        $dateLimite      = now()->addWeekdays(15);

        $entretien = Entretien::create([
            'dossier_id'        => $dossier->id,
            'declenche_par'     => auth()->id(),
            'motif_convocation' => $request->motif_convocation,
            'date_convocation'  => $dateConvocation,
            'date_limite'       => $dateLimite,
            'relance_j5'        => $dateConvocation->copy()->addWeekdays(5),
            'relance_j10'       => $dateConvocation->copy()->addWeekdays(10),
            'statut'            => 'programme',
        ]);

        $dossier->update(['statut' => 'entretien_requis']);
        JournalAudit::enregistrer('ENTRETIEN_DECLENCHE', "Entretien déclenché pour {$dossier->numero_suivi}", $dossier);
        $dossier->user->notify(new EntretienRequisNotification($dossier, $entretien));

        return redirect()->route('validateur.dossiers.show', $dossier)
            ->with('success', "Entretien programmé. Délai limite : {$dateLimite->format('d/m/Y')}.");
    }

    // ── Actions visa ──────────────────────────────────────────────

    public function apposerVisa(Dossier $dossier)
    {
        if (!in_array($dossier->statut, ['en_cours', 'entretien_requis'])) {
            return back()->with('error', 'Ce dossier ne peut pas être visé dans son état actuel.');
        }

        $cheminPdf = $this->pdfService->genererContratVise($dossier);

        $dossier->update([
            'statut'              => 'vise',
            'validateur_id'       => auth()->id(),
            'date_visa'           => now(),
            'chemin_contrat_vise' => $cheminPdf,
        ]);

        JournalAudit::enregistrer('VISA_APPOSE', "Visa apposé sur {$dossier->numero_suivi}", $dossier);
        $dossier->user->notify(new DossierViseNotification($dossier));

        return redirect()->route('validateur.dossiers.show', $dossier)
            ->with('success', "Visa apposé. L'usager peut télécharger son contrat.");
    }

    public function rejeter(Request $request, Dossier $dossier)
    {
        $request->validate([
            'motif_rejet' => 'required|string|min:20|max:2000',
        ], ['motif_rejet.required' => 'Un motif de rejet est obligatoire.']);

        $dossier->update([
            'statut'        => 'rejete',
            'validateur_id' => auth()->id(),
            'motif_rejet'   => $request->motif_rejet,
        ]);

        JournalAudit::enregistrer('DOSSIER_REJETE', "Dossier {$dossier->numero_suivi} rejeté", $dossier);
        $dossier->user->notify(new DossierRejeteNotification($dossier));

        return redirect()->route('validateur.dossiers.show', $dossier)
            ->with('success', 'Dossier rejeté. L\'usager a été notifié.');
    }

    public function validerEntretien(Request $request, Dossier $dossier)
    {
        $request->validate(['notes_validateur' => 'nullable|string|max:2000']);

        if ($dossier->entretien) {
            $dossier->entretien->update([
                'statut'           => 'tenu',
                'valide_par'       => auth()->id(),
                'date_validation'  => now(),
                'notes_validateur' => $request->notes_validateur,
            ]);
        }

        $dossier->update(['statut' => 'en_cours']);
        JournalAudit::enregistrer('ENTRETIEN_VALIDE', "Entretien validé pour {$dossier->numero_suivi}", $dossier);

        return redirect()->route('validateur.dossiers.show', $dossier)
            ->with('success', 'Entretien validé. Vous pouvez apposer le visa.');
    }

    public function arbitrage(Request $request, Dossier $dossier)
    {
        $request->validate([
            'decision'    => 'required|in:prolonger,annuler_entretien,rejeter',
            'motif_rejet' => 'required_if:decision,rejeter|nullable|string|min:20',
        ]);

        switch ($request->decision) {
            case 'prolonger':
                $dossier->entretien?->update(['date_limite' => now()->addWeekdays(10), 'statut' => 'programme']);
                $dossier->update(['statut' => 'entretien_requis']);
                JournalAudit::enregistrer('ARBITRAGE_PROLONGATION', "Délai prolongé pour {$dossier->numero_suivi}", $dossier);
                return back()->with('success', 'Délai prolongé de 10 jours ouvrés.');

            case 'annuler_entretien':
                $dossier->entretien?->update(['statut' => 'annule']);
                $dossier->update(['statut' => 'en_cours']);
                JournalAudit::enregistrer('ARBITRAGE_ANNULATION', "Entretien annulé pour {$dossier->numero_suivi}", $dossier);
                return back()->with('success', 'Entretien annulé. Dossier remis en cours d\'examen.');

            case 'rejeter':
                $dossier->entretien?->update(['statut' => 'expire']);
                $dossier->update(['statut' => 'rejete', 'motif_rejet' => $request->motif_rejet]);
                $dossier->user->notify(new DossierRejeteNotification($dossier));
                JournalAudit::enregistrer('ARBITRAGE_REJET', "Dossier {$dossier->numero_suivi} rejeté après arbitrage", $dossier);
                return back()->with('success', 'Dossier rejeté définitivement.');
        }
    }

    public function journalAudit(Request $request)
    {
        $audits = \App\Models\JournalAudit::with('user')
            ->when($request->filled('action'), fn($q) => $q->where('action', $request->action))
            ->when($request->filled('date'), fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()->paginate(50)->withQueryString();

        return view('validateur.audit', compact('audits'));
    }
}
