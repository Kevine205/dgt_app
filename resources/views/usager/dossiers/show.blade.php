@extends('layouts.app')
@section('title', 'Dossier ' . $dossier->numero_suivi)
@section('sidebar-color', 'bg-blue-800')
@section('page-title', 'Dossier ' . $dossier->numero_suivi)
@section('page-subtitle', 'Détail et suivi de votre dossier')

@section('sidebar-nav')
    <a href="{{ route('usager.dashboard') }}" class="sidebar-link"><i class="fas fa-arrow-left w-4"></i> Tableau de bord</a>
    <a href="{{ route('usager.dossiers.create') }}" class="sidebar-link"><i class="fas fa-plus w-4"></i> Nouveau dossier</a>
@endsection

@section('content')
<!-- BARRE DE PROGRESSION -->
@php
    $etapes = [
        ['key' => 'soumis', 'label' => 'Soumis', 'icon' => 'fa-upload'],
        ['key' => 'en_cours', 'label' => 'En cours d\'examen', 'icon' => 'fa-search'],
        ['key' => 'vise', 'label' => 'Visé', 'icon' => 'fa-check-circle'],
    ];
    $etapeActuelle = $dossier->statut_info['etape'];
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold text-gray-900">Suivi de votre dossier</h3>
        <span class="px-3 py-1 rounded-full text-xs font-medium badge-{{ $dossier->statut }}">
            {{ $dossier->statut_info['label'] }}
        </span>
    </div>

    <!-- Barre -->
    <div class="relative mt-6 mb-2">
        <div class="absolute top-5 left-0 right-0 h-1 bg-gray-200 rounded"></div>
        <div class="absolute top-5 left-0 h-1 bg-green-500 rounded transition-all" style="width: {{ min(100, ($etapeActuelle / 4) * 100) }}%"></div>
        <div class="relative flex justify-between">
            @foreach($etapes as $i => $etape)
            <div class="flex flex-col items-center w-24">
                <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 {{ $i + 1 <= $etapeActuelle ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                    <i class="fas {{ $etape['icon'] }} text-sm"></i>
                </div>
                <span class="text-xs text-center mt-2 {{ $i + 1 <= $etapeActuelle ? 'text-green-700 font-medium' : 'text-gray-400' }}">{{ $etape['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    @if($dossier->statut === 'correction_demandee')
    <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-xl">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-orange-600 mt-0.5"></i>
            <div>
                <div class="font-semibold text-orange-900 text-sm">Corrections requises</div>
                <p class="text-sm text-orange-700 mt-1">{{ $dossier->motif_correction }}</p>
                <a href="{{ route('usager.dossiers.corriger', $dossier) }}" class="inline-block mt-3 px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700 transition">
                    Corriger mon dossier
                </a>
            </div>
        </div>
    </div>
    @endif

    @if($dossier->statut === 'entretien_requis' && $dossier->entretien)
    <div class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-xl">
        <div class="flex items-start gap-3">
            <i class="fas fa-calendar-alt text-purple-600 mt-0.5"></i>
            <div>
                <div class="font-semibold text-purple-900 text-sm">Entretien requis</div>
                <p class="text-sm text-purple-700 mt-1">{{ $dossier->entretien->motif_convocation }}</p>
                <p class="text-sm text-purple-700 mt-1">
                    <strong>Date limite :</strong> {{ $dossier->entretien->date_limite->format('d/m/Y') }}
                    ({{ $dossier->entretien->joursRestants() }} jour(s) restant(s))
                </p>
                <p class="text-xs text-purple-600 mt-2">Présentez-vous à la DGT, Cotonou avant cette date.</p>
            </div>
        </div>
    </div>
    @endif

    @if($dossier->peutEtreTelecharge())
    <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
            <div>
                <div class="font-semibold text-green-900">Contrat visé disponible !</div>
                <p class="text-sm text-green-700">Votre contrat a été visé le {{ $dossier->date_visa->format('d/m/Y à H:i') }}</p>
            </div>
        </div>
        <a href="{{ route('usager.dossiers.telecharger', $dossier) }}" class="px-6 py-3 bg-green-700 text-white rounded-xl font-semibold hover:bg-green-800 transition">
            <i class="fas fa-download mr-2"></i>Télécharger
        </a>
    </div>
    @endif

    @if($dossier->statut === 'rejete')
    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
        <div class="flex items-start gap-3">
            <i class="fas fa-times-circle text-red-600 mt-0.5"></i>
            <div>
                <div class="font-semibold text-red-900 text-sm">Dossier rejeté</div>
                <p class="text-sm text-red-700 mt-1">{{ $dossier->motif_rejet }}</p>
                <p class="text-xs text-red-600 mt-2">Pour toute réclamation, contactez la DGT à contact@dgt.bj</p>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- DÉTAILS DU DOSSIER -->
<div class="grid md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Informations du contrat</h3>
        <dl class="space-y-3 text-sm">
            @foreach([
                ['Employeur', $dossier->nom_employeur],
                ['Employé', $dossier->prenom_employe . ' ' . $dossier->nom_employe],
                ['Type de contrat', $dossier->type_contrat],
                ['Poste', $dossier->poste],
                ['Date de signature', $dossier->date_signature->format('d/m/Y')],
                ['Date de début', $dossier->date_debut->format('d/m/Y')],
                ['Date de fin', $dossier->date_fin ? $dossier->date_fin->format('d/m/Y') : '—'],
                ['Salaire', $dossier->salaire ? number_format($dossier->salaire, 0, ',', ' ') . ' FCFA' : '—'],
            ] as [$label, $val])
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ $label }}</dt>
                <dd class="font-medium text-gray-900 text-right">{{ $val }}</dd>
            </div>
            @endforeach
        </dl>
    </div>

    <!-- PIÈCES JOINTES -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Pièces jointes ({{ $dossier->pieces->count() }})</h3>
        <div class="space-y-2">
            @foreach($dossier->pieces as $piece)
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas {{ str_contains($piece->mime_type, 'pdf') ? 'fa-file-pdf text-red-600' : 'fa-file-image text-blue-600' }} text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-gray-900 truncate">{{ $piece->nom_original }}</div>
                    <div class="text-xs text-gray-500">{{ $piece->type_piece }} — {{ $piece->taille_formatee }}</div>
                </div>
                @if($piece->conforme === false)
                    <span class="text-xs text-red-600 font-medium">Non conforme</span>
                @elseif($piece->conforme === true)
                    <i class="fas fa-check-circle text-green-500"></i>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- HISTORIQUE -->
@if($dossier->audits->count() > 0)
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h3 class="font-semibold text-gray-900 mb-4">Historique du dossier</h3>
    <div class="space-y-3">
        @foreach($dossier->audits as $audit)
        <div class="flex items-start gap-3">
            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
            <div>
                <div class="text-sm text-gray-800">{{ $audit->description }}</div>
                <div class="text-xs text-gray-400 mt-0.5">{{ $audit->created_at->locale('fr')->diffForHumans() }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
