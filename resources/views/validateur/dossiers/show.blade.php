@extends('layouts.app')
@section('title', 'Dossier ' . $dossier->numero_suivi)
@section('sidebar-color', 'bg-amber-800')
@section('page-title', 'Dossier ' . $dossier->numero_suivi)
@section('page-subtitle', 'Traitement complet du dossier')

@section('sidebar-nav')
    <a href="{{ route('validateur.dashboard') }}" class="sidebar-link"><i class="fas fa-arrow-left w-4"></i> Tableau de bord</a>
    <a href="{{ route('validateur.dossiers.index') }}" class="sidebar-link"><i class="fas fa-folder w-4"></i> Tous les dossiers</a>
    <a href="{{ route('validateur.profil') }}" class="sidebar-link"><i class="fas fa-user w-4"></i> Mon profil</a>
    <a href="{{ route('validateur.audit') }}" class="sidebar-link"><i class="fas fa-list w-4"></i> Journal d'audit</a>
@endsection

@section('content')
<div class="grid md:grid-cols-3 gap-6">

    <div class="md:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Informations du dossier</h3>
                <span class="px-3 py-1 rounded-full text-xs font-medium badge-{{ $dossier->statut }}">
                    {{ $dossier->statut_info['label'] }}
                </span>
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
                    <dt class="text-gray-400 text-xs">{{ $label }}</dt>
                    <dd class="font-medium text-gray-900">{{ $val }}</dd>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Pièces justificatives ({{ $dossier->pieces->count() }})</h3>
            @forelse($dossier->pieces as $piece)
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl mb-2">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas {{ str_contains($piece->mime_type, 'pdf') ? 'fa-file-pdf text-red-600' : 'fa-file-image text-blue-600' }} text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-gray-900 truncate">{{ $piece->nom_original }}</div>
                    <div class="text-xs text-gray-500">{{ ucfirst($piece->type_piece) }} — {{ $piece->taille_formatee }}</div>
                </div>
                <a href="{{ route('validateur.fichier.piece', $piece) }}" target="_blank"
                    class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition">
                    <i class="fas fa-eye mr-1"></i>Voir
                </a>
            </div>
            @empty
            <p class="text-gray-400 text-sm">Aucune pièce jointe.</p>
            @endforelse
        </div>

        @if($dossier->statut === 'vise' && $dossier->chemin_contrat_vise)
        <div class="bg-green-50 border border-green-200 rounded-2xl p-6">
            <h3 class="font-semibold text-green-900 mb-3 flex items-center gap-2">
                <i class="fas fa-check-circle"></i> Contrat visé
            </h3>
            <p class="text-sm text-green-700 mb-3">
                Ce dossier a été visé le {{ $dossier->date_visa->format('d/m/Y à H:i') }}
                @if($dossier->validateur) par {{ $dossier->validateur->nom_complet }} @endif
            </p>
        </div>
        @endif

        @if($dossier->audits->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Historique</h3>
            <div class="space-y-3">
                @foreach($dossier->audits as $audit)
                <div class="flex items-start gap-3 text-sm">
                    <div class="w-2 h-2 bg-amber-500 rounded-full mt-1.5 flex-shrink-0"></div>
                    <div>
                        <span class="text-gray-800">{{ $audit->description }}</span>
                        <span class="text-gray-400 ml-2 text-xs">— {{ $audit->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="space-y-4">

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

        @if($dossier->statut === 'soumis')
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
            <h4 class="font-semibold text-blue-900 mb-2">Prendre en charge</h4>
            <p class="text-sm text-blue-700 mb-4">Démarrer l'instruction de ce dossier.</p>
            <form method="POST" action="{{ route('validateur.dossiers.prendre', $dossier) }}">
                @csrf
                <button type="submit" class="w-full py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition">
                    <i class="fas fa-hand-paper mr-2"></i>Prendre en charge
                </button>
            </form>
        </div>
        @endif

        @if(in_array($dossier->statut, ['soumis', 'en_cours']))
        <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5">
            <h4 class="font-semibold text-orange-900 mb-3">Demander une correction</h4>
            <form method="POST" action="{{ route('validateur.dossiers.correction', $dossier) }}">
                @csrf
                <textarea name="motif_correction" rows="3" required
                    placeholder="Décrivez les corrections requises (min. 20 caractères)..."
                    class="w-full px-3 py-2 border border-orange-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none mb-3 resize-none bg-white"></textarea>
                <button type="submit" class="w-full py-2.5 bg-orange-600 text-white rounded-xl text-sm font-semibold hover:bg-orange-700 transition">
                    <i class="fas fa-edit mr-2"></i>Envoyer
                </button>
            </form>
        </div>
        @endif

        @if($dossier->statut === 'en_cours' && !$dossier->entretien)
        <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5">
            <h4 class="font-semibold text-purple-900 mb-3">Déclencher un entretien</h4>
            <form method="POST" action="{{ route('validateur.dossiers.entretien', $dossier) }}">
                @csrf
                <textarea name="motif_convocation" rows="3" required
                    placeholder="Motif de la convocation (min. 20 caractères)..."
                    class="w-full px-3 py-2 border border-purple-200 rounded-xl text-sm focus:ring-2 focus:ring-purple-400 focus:outline-none mb-3 resize-none bg-white"></textarea>
                <button type="submit" class="w-full py-2.5 bg-purple-700 text-white rounded-xl text-sm font-semibold hover:bg-purple-800 transition">
                    <i class="fas fa-calendar-plus mr-2"></i>Convoquer l'usager
                </button>
            </form>
        </div>
        @endif

        <!-- APPOSER VISA AVEC SIGNATURE -->
        @if($dossier->statut === 'en_cours')
        <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
            <h4 class="font-semibold text-green-900 mb-2">Apposer le visa</h4>

            @if(auth()->user()->signature_electronique)
                <div class="flex items-center gap-2 mb-3 p-2 bg-white rounded-lg border border-green-200">
                    <img src="{{ auth()->user()->signature_electronique }}" alt="Votre signature" class="h-10">
                    <span class="text-xs text-green-700"><i class="fas fa-check-circle mr-1"></i>Signature prête</span>
                </div>
                <p class="text-sm text-green-700 mb-4">Le dossier est conforme. Votre signature sera apposée automatiquement sur le contrat visé.</p>
                <form method="POST" action="{{ route('validateur.dossiers.visa', $dossier) }}"
                    onsubmit="return confirm('Confirmer le visa de ce dossier avec votre signature électronique ?')">
                    @csrf
                    <button type="submit" class="w-full py-2.5 bg-green-700 text-white rounded-xl text-sm font-semibold hover:bg-green-800 transition">
                        <i class="fas fa-stamp mr-2"></i>Apposer le visa
                    </button>
                </form>
            @else
                <div class="flex items-start gap-2 mb-3 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-orange-500 mt-0.5"></i>
                    <p class="text-xs text-orange-700">
                        Vous devez d'abord configurer votre signature électronique avant de pouvoir viser un dossier.
                    </p>
                </div>
                <a href="{{ route('validateur.profil') }}" class="block w-full py-2.5 bg-orange-600 text-white rounded-xl text-sm font-semibold hover:bg-orange-700 transition text-center">
                    <i class="fas fa-pen-nib mr-2"></i>Configurer ma signature
                </a>
            @endif
        </div>
        @endif

        @if($dossier->statut === 'entretien_requis' && $dossier->entretien?->statut === 'programme')
        <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
            <h4 class="font-semibold text-green-900 mb-2">Valider l'entretien tenu</h4>
            <form method="POST" action="{{ route('validateur.dossiers.valider-entretien', $dossier) }}">
                @csrf
                <textarea name="notes_validateur" rows="2" placeholder="Notes (optionnel)..."
                    class="w-full px-3 py-2 border border-green-200 rounded-xl text-sm focus:ring-2 focus:ring-green-400 focus:outline-none mb-3 resize-none bg-white"></textarea>
                <button type="submit" class="w-full py-2.5 bg-green-700 text-white rounded-xl text-sm font-semibold hover:bg-green-800 transition">
                    <i class="fas fa-check mr-2"></i>Valider l'entretien
                </button>
            </form>
        </div>
        @endif

        @if($dossier->statut === 'en_attente_arbitrage')
        <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
            <h4 class="font-semibold text-red-900 mb-3">Arbitrage requis</h4>
            <form method="POST" action="{{ route('validateur.dossiers.arbitrage', $dossier) }}">
                @csrf
                <div class="space-y-2 mb-3">
                    @foreach(['prolonger' => 'Prolonger le délai (+10j)', 'annuler_entretien' => "Annuler l'entretien", 'rejeter' => 'Rejeter le dossier'] as $val => $label)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="decision" value="{{ $val }}" class="text-red-600">
                        <span class="text-sm text-red-900">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                <textarea name="motif_rejet" rows="2" placeholder="Motif (obligatoire si rejet)..."
                    class="w-full px-3 py-2 border border-red-200 rounded-xl text-sm focus:outline-none mb-3 resize-none bg-white"></textarea>
                <button type="submit" class="w-full py-2.5 bg-red-700 text-white rounded-xl text-sm font-semibold hover:bg-red-800 transition">
                    Confirmer la décision
                </button>
            </form>
        </div>
        @endif

        @if(in_array($dossier->statut, ['soumis', 'en_cours', 'correction_demandee']))
        <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
            <h4 class="font-semibold text-red-900 mb-3">Rejeter le dossier</h4>
            <form method="POST" action="{{ route('validateur.dossiers.rejeter', $dossier) }}"
                onsubmit="return confirm('Confirmer le rejet ?')">
                @csrf
                <textarea name="motif_rejet" rows="3" required
                    placeholder="Motif du rejet (min. 20 caractères)..."
                    class="w-full px-3 py-2 border border-red-200 rounded-xl text-sm focus:ring-2 focus:ring-red-400 focus:outline-none mb-3 resize-none bg-white"></textarea>
                <button type="submit" class="w-full py-2.5 bg-red-700 text-white rounded-xl text-sm font-semibold hover:bg-red-800 transition">
                    <i class="fas fa-times mr-2"></i>Rejeter
                </button>
            </form>
        </div>
        @endif

        @if($dossier->entretien && $dossier->statut === 'entretien_requis')
        <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5">
            <h4 class="font-semibold text-purple-900 mb-3"><i class="fas fa-calendar-check mr-2"></i>Entretien programmé</h4>
            <dl class="text-sm space-y-2">
                <div><dt class="text-xs text-purple-600">Motif</dt><dd class="text-purple-900">{{ $dossier->entretien->motif_convocation }}</dd></div>
                <div><dt class="text-xs text-purple-600">Date limite</dt><dd class="font-semibold text-purple-900">{{ $dossier->entretien->date_limite->format('d/m/Y') }}</dd></div>
                <div><dt class="text-xs text-purple-600">Jours restants</dt><dd class="font-semibold text-purple-900">{{ $dossier->entretien->joursRestants() }} jour(s)</dd></div>
            </dl>
        </div>
        @endif

    </div>
</div>
@endsection
