<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Dossier, JournalAudit};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_dossiers'    => Dossier::count(),
            'soumis'            => Dossier::where('statut', 'soumis')->count(),
            'en_cours'          => Dossier::where('statut', 'en_cours')->count(),
            'vises'             => Dossier::where('statut', 'vise')->count(),
            'rejetes'           => Dossier::where('statut', 'rejete')->count(),
            'entretiens'        => Dossier::where('statut', 'entretien_requis')->count(),
            'total_usagers'     => User::role('usager')->count(),
            'total_validateurs' => User::role('validateur')->count(),
        ];

        // Stats par mois (6 derniers mois)
        $statsMois = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $statsMois[] = [
                'mois'    => $date->locale('fr')->isoFormat('MMMM YYYY'),
                'soumis'  => Dossier::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
                'vises'   => Dossier::where('statut', 'vise')->whereYear('date_visa', $date->year)->whereMonth('date_visa', $date->month)->count(),
                'rejetes' => Dossier::where('statut', 'rejete')->whereYear('updated_at', $date->year)->whereMonth('updated_at', $date->month)->count(),
            ];
        }

        return view('admin.dashboard', compact('stats', 'statsMois'));
    }

    public function agents(Request $request)
    {
        $query = User::with('roles')->whereDoesntHave('roles', fn($q) => $q->where('name', 'usager'));

        if ($request->filled('role')) {
            $query->role($request->role);
        }
        if ($request->filled('recherche')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->recherche}%")
                  ->orWhere('prenom', 'like', "%{$request->recherche}%")
                  ->orWhere('email', 'like', "%{$request->recherche}%");
            });
        }

        $agents = $query->latest()->paginate(20)->withQueryString();
        $roles  = Role::whereNotIn('name', ['usager'])->get();
        return view('admin.agents.index', compact('agents', 'roles'));
    }

    public function creerAgent()
    {
        $roles = Role::whereNotIn('name', ['usager'])->get();
        return view('admin.agents.create', compact('roles'));
    }

    public function enregistrerAgent(Request $request)
    {
        $request->validate([
            'nom'      => 'required|string|max:100',
            'prenom'   => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'telephone'=> 'nullable|string|max:20',
            'role'     => 'required|in:validateur,admin',
            'password' => 'required|min:8|confirmed',
        ]);

        $agent = User::create([
            'nom'       => strtoupper($request->nom),
            'prenom'    => ucfirst($request->prenom),
            'email'     => $request->email,
            'telephone' => $request->telephone,
            'password'  => Hash::make($request->password),
            'actif'     => true,
        ]);

        $agent->assignRole($request->role);
        JournalAudit::enregistrer('AGENT_CREE', "Compte {$request->role} créé : {$agent->nom_complet}", $agent);

        return redirect()->route('admin.agents.index')
            ->with('success', "Compte de {$agent->nom_complet} créé avec succès.");
    }

    public function modifierAgent(User $user)
    {
        $roles = Role::whereNotIn('name', ['usager'])->get();
        $user->load('roles');
        return view('admin.agents.edit', compact('user', 'roles'));
    }

    public function mettreAJourAgent(Request $request, User $user)
    {
        $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'role'      => 'required|in:validateur,admin',
        ]);

        $user->update([
            'nom'       => strtoupper($request->nom),
            'prenom'    => ucfirst($request->prenom),
            'email'     => $request->email,
            'telephone' => $request->telephone,
        ]);

        $user->syncRoles([$request->role]);
        JournalAudit::enregistrer('AGENT_MODIFIE', "Compte modifié : {$user->nom_complet}", $user);

        return redirect()->route('admin.agents.index')->with('success', 'Compte mis à jour.');
    }

    public function suspendreAgent(User $user)
    {
        $user->update(['actif' => false]);
        JournalAudit::enregistrer('AGENT_SUSPENDU', "Compte suspendu : {$user->nom_complet}", $user);
        return back()->with('success', "Compte de {$user->nom_complet} suspendu.");
    }

    public function reactiverAgent(User $user)
    {
        $user->update(['actif' => true]);
        JournalAudit::enregistrer('AGENT_REACTIVE', "Compte réactivé : {$user->nom_complet}", $user);
        return back()->with('success', "Compte de {$user->nom_complet} réactivé.");
    }

    public function reset2fa(User $user)
    {
        $user->update(['google2fa_secret' => null, 'google2fa_enabled' => false]);
        JournalAudit::enregistrer('2FA_RESET', "2FA réinitialisé pour : {$user->nom_complet}", $user);
        return back()->with('success', '2FA réinitialisé. L\'agent devra le reconfigurer à sa prochaine connexion.');
    }

    public function journalAudit(Request $request)
    {
        $audits = JournalAudit::with('user')
            ->when($request->filled('action'), fn($q) => $q->where('action', $request->action))
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('date_debut'), fn($q) => $q->whereDate('created_at', '>=', $request->date_debut))
            ->when($request->filled('date_fin'), fn($q) => $q->whereDate('created_at', '<=', $request->date_fin))
            ->latest()->paginate(50)->withQueryString();

        $actions = JournalAudit::distinct()->pluck('action');
        $agents  = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'usager'))->get();

        return view('admin.audit', compact('audits', 'actions', 'agents'));
    }

    public function statistiques()
    {
        $statsMois = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $statsMois[] = [
                'mois'    => $date->locale('fr')->isoFormat('MMM YY'),
                'soumis'  => Dossier::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
                'vises'   => Dossier::where('statut', 'vise')->whereYear('date_visa', $date->year)->whereMonth('date_visa', $date->month)->count(),
                'rejetes' => Dossier::where('statut', 'rejete')->whereYear('updated_at', $date->year)->whereMonth('updated_at', $date->month)->count(),
            ];
        }

        $statsTypeContrat = Dossier::selectRaw('type_contrat, count(*) as total')
            ->groupBy('type_contrat')->get();

        return view('admin.statistiques', compact('statsMois', 'statsTypeContrat'));
    }

    public function exporterStats()
    {
        $dossiers = Dossier::with(['user', 'agent', 'validateur'])->latest()->get();
        // Export CSV simple
        $headers = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename=statistiques_dgt_' . now()->format('Ymd') . '.csv'];
        $callback = function () use ($dossiers) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM UTF-8
            fputcsv($file, ['N° Suivi', 'Statut', 'Employeur', 'Employé', 'Type Contrat', 'Date Soumission', 'Date Visa', 'Agent', 'Validateur'], ';');
            foreach ($dossiers as $d) {
                fputcsv($file, [
                    $d->numero_suivi,
                    Dossier::STATUTS[$d->statut]['label'] ?? $d->statut,
                    $d->nom_employeur,
                    "{$d->prenom_employe} {$d->nom_employe}",
                    $d->type_contrat,
                    $d->date_soumission?->format('d/m/Y H:i'),
                    $d->date_visa?->format('d/m/Y H:i'),
                    $d->agent?->nom_complet ?? '-',
                    $d->validateur?->nom_complet ?? '-',
                ], ';');
            }
            fclose($file);
        };

        JournalAudit::enregistrer('EXPORT_STATS', 'Export des statistiques CSV');
        return response()->stream($callback, 200, $headers);
    }
}
