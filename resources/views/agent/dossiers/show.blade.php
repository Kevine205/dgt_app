@extends('layouts.app')
@section('title', 'Instruire ' . $dossier->numero_suivi)
@section('sidebar-color', 'bg-emerald-800')
@section('page-title', 'Dossier ' . $dossier->numero_suivi)
@section('page-subtitle', 'Instruction et traitement du dossier')

@section('sidebar-nav')
    <a href="{{ route('agent.dashboard') }}" class="sidebar-link"><i class="fas fa-arrow-left w-4"></i> Tableau de bord</a>
    <a href="{{ route('agent.dossiers.index') }}" class="sidebar-link"><i class="fas fa-folder w-4"></i> Tous les dossiers</a>
@endsection

@section('content')
<div class="grid md:grid-cols-3 gap-6">

    <!-- COLONNE PRINCIPALE -->
    <div class="md:col-span-2 space-y-6">

        <!-- INFOS DOSSIER -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Informations du dossier</h3>
                <span class="px-3 py-1 rounded-full text-xs font-medium badge-{{ $dossier->statut }}">{{ $dossier->statut_info['label'] }}</span>
            </div>
            <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
                @foreach([
                    ['Employeur', $dossier->nom_employeur],
                    ['Secteur', $dossier->secteur_activite ?? '—'],
                    ['Employé', $dossier->prenom_employe . ' ' . $dossier->nom_employe],
                    ['Nationalité', $dossier->nationalite_employe ?? '—'],
                    ['Type contrat', $dossier->type_contrat],
                    ['Poste', $dossier->poste],
                    ['Date signature', $dossier->date_signature->format('d/m/Y')],
                    ['Date début', $dossier->date_debut->format('d/m/Y')],
                    ['Date fin', $dossier->date_fin?->format('d/m/Y') ?? '—'],
                    ['Salaire', $dossier->salaire ? number_format($dossier->salaire, 0, ',', ' ') . ' FCFA' : '—'],
                ] as [$label, $val])
                <div>
                    <dt class="text-gray-500 text-xs">{{ $label }}</dt>
                    <dd class="font-medium text-gray-900">{{ $val }}</dd>
                </div>
                @endforeach
            </div>
        </div>

        <!-- PIÈCES JUSTIFICATIVES -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Pièces justificatives ({{ $dossier->pieces->count() }})</h3>
            <div class="space-y-2">
                @foreach($dossier->pieces as $piece)
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas {{ str_contains($piece->mime_type, 'pdf') ? 'fa-file-pdf text-red-600' : 'fa-file-image text-blue-600' }} text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900 truncate">{{ $piece->nom_original }}</div>
                        <div class="text-xs text-gray-500">{{ ucfirst($piece->type_piece) }} — {{ $piece->taille_formatee }}</div>
                    </div>
                    <a href="{{ route('agent.fichier.piece', $piece) }}" target="_blank"
                        class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition">
                        <i class="fas fa-eye mr-1"></i>Consulter
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <!-- HISTORIQUE -->
        @if($dossier->audits->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Historique</h3>
            <div class="space-y-3">
                @foreach($dossier->audits as $audit)
                <div class="flex items-start gap-3 text-sm">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full mt-1.5 flex-shrink-0"></div>
                    <div>
                        <span class="text-gray-800">{{ $audit->description }}</span>
                        <span class="text-gray-400 ml-2 text-xs">— {{ $audit->created_at->format('d/m/Y H:i') }}</span>
                        @if($audit->user)
                            <span class="text-gray-400 text-xs ml-1">par {{ $audit->user->nom_complet }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- COLONNE ACTIONS -->
    <div class="space-y-4">

        <!-- PRENDRE EN CHARGE -->
        @if($dossier->statut === 'soumis')
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
            <h4 class="font-semibold text-blue-900 mb-3">Prendre en charge</h4>
            <p class="text-sm text-blue-700 mb-4">Ce dossier est en attente de traitement. Prenez-le en charge pour commencer l'instruction.</p>
            <form method="POST" action="{{ route('agent.dossiers.prendre', $dossier) }}">
                @csrf
                <button type="submit" class="w-full py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition">
                    <i class="fas fa-hand-paper mr-2"></i>Prendre en charge
                </button>
            </form>
        </div>
        @endif

        <!-- DEMANDER CORRECTION -->
        @if(in_array($dossier->statut, ['en_cours', 'soumis']))
        <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5">
            <h4 class="font-semibold text-orange-900 mb-3">Demander une correction</h4>
            <form method="POST" action="{{ route('agent.dossiers.correction', $dossier) }}">
                @csrf
                <textarea name="motif_correction" rows="4" required placeholder="Décrivez précisément les corrections requises (min. 20 caractères)..."
                    class="w-full px-3 py-2 border border-orange-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none mb-3 resize-none bg-white"></textarea>
                <button type="submit" class="w-full py-2.5 bg-orange-600 text-white rounded-xl text-sm font-semibold hover:bg-orange-700 transition">
                    <i class="fas fa-edit mr-2"></i>Envoyer la demande
                </button>
            </form>
        </div>
        @endif

        <!-- DÉCLENCHER ENTRETIEN -->
        @if(in_array($dossier->statut, ['en_cours']) && !$dossier->entretien)
        <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5">
            <h4 class="font-semibold text-purple-900 mb-3">Déclencher un entretien</h4>
            <form method="POST" action="{{ route('agent.dossiers.entretien', $dossier) }}">
                @csrf
                <textarea name="motif_convocation" rows="4" required placeholder="Motif de la convocation (min. 20 caractères)..."
                    class="w-full px-3 py-2 border border-purple-200 rounded-xl text-sm focus:ring-2 focus:ring-purple-400 focus:outline-none mb-3 resize-none bg-white"></textarea>
                <button type="submit" class="w-full py-2.5 bg-purple-700 text-white rounded-xl text-sm font-semibold hover:bg-purple-800 transition">
                    <i class="fas fa-calendar-plus mr-2"></i>Convoquer l'usager
                </button>
            </form>
        </div>
        @endif

        <!-- INFOS ENTRETIEN EN COURS -->
        @if($dossier->entretien && $dossier->statut === 'entretien_requis')
        <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5">
            <h4 class="font-semibold text-purple-900 mb-3"><i class="fas fa-calendar-check mr-2"></i>Entretien programmé</h4>
            <dl class="text-sm space-y-2">
                <div><dt class="text-purple-600 text-xs">Motif</dt><dd class="text-purple-900">{{ $dossier->entretien->motif_convocation }}</dd></div>
                <div><dt class="text-purple-600 text-xs">Date limite</dt><dd class="font-semibold text-purple-900">{{ $dossier->entretien->date_limite->format('d/m/Y') }}</dd></div>
                <div><dt class="text-purple-600 text-xs">Jours restants</dt><dd class="font-semibold text-purple-900">{{ $dossier->entretien->joursRestants() }} jour(s)</dd></div>
            </dl>
        </div>
        @endif

        <!-- INFOS USAGER -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h4 class="font-semibold text-gray-900 mb-3">Usager</h4>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center font-bold text-blue-700">
                    {{ strtoupper(substr($dossier->user->prenom, 0, 1)) }}
                </div>
                <div>
                    <div class="font-medium text-gray-900 text-sm">{{ $dossier->user->nom_complet }}</div>
                    <div class="text-xs text-gray-500">{{ $dossier->user->email }}</div>
                    @if($dossier->user->telephone)
                        <div class="text-xs text-gray-500">{{ $dossier->user->telephone }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
