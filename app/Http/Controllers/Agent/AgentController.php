<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Models\Entretien;
use App\Models\JournalAudit;
use App\Notifications\CorrectionDemandeeNotification;
use App\Notifications\EntretienRequisNotification;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'soumis'      => Dossier::where('statut', 'soumis')->count(),
            'en_cours'    => Dossier::where('statut', 'en_cours')->where('agent_id', auth()->id())->count(),
            'corrections' => Dossier::where('statut', 'correction_demandee')->count(),
            'entretiens'  => Dossier::where('statut', 'entretien_requis')->count(),
        ];
        $derniersDossiers = Dossier::with('user')
            ->whereIn('statut', ['soumis', 'en_cours', 'correction_demandee'])
            ->latest()->take(10)->get();

        return view('agent.dashboard', compact('stats', 'derniersDossiers'));
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
        return view('agent.dossiers.index', compact('dossiers'));
    }

    public function show(Dossier $dossier)
    {
        $dossier->load(['user', 'pieces', 'entretien.declenchePar', 'audits.user', 'agent']);
        return view('agent.dossiers.show', compact('dossier'));
    }

    public function prendreEnCharge(Dossier $dossier)
    {
        if (!in_array($dossier->statut, ['soumis'])) {
            return back()->with('error', 'Ce dossier ne peut pas être pris en charge.');
        }

        $dossier->update(['statut' => 'en_cours', 'agent_id' => auth()->id()]);
        JournalAudit::enregistrer('DOSSIER_PRIS_EN_CHARGE', "Dossier {$dossier->numero_suivi} pris en charge", $dossier);

        return back()->with('success', 'Dossier pris en charge avec succès.');
    }

    public function demanderCorrection(Request $request, Dossier $dossier)
    {
        $request->validate([
            'motif_correction' => 'required|string|min:20|max:2000',
        ], ['motif_correction.required' => 'Veuillez préciser les corrections demandées.']);

        $dossier->update([
            'statut'           => 'correction_demandee',
            'motif_correction' => $request->motif_correction,
        ]);

        JournalAudit::enregistrer('CORRECTION_DEMANDEE', "Correction demandée pour {$dossier->numero_suivi}", $dossier);
        $dossier->user->notify(new CorrectionDemandeeNotification($dossier));

        return redirect()->route('agent.dossiers.show', $dossier)
            ->with('success', 'Demande de correction envoyée à l\'usager.');
    }

    public function declencherEntretien(Request $request, Dossier $dossier)
    {
        $request->validate([
            'motif_convocation' => 'required|string|min:20|max:2000',
        ]);

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

        return redirect()->route('agent.dossiers.show', $dossier)
            ->with('success', "Entretien programmé. L'usager a été convoqué. Délai : {$dateLimite->format('d/m/Y')}.");
    }
}
